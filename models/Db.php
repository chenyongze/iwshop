<?php

require_once 'SqlCached.php';

/**
 * Db模块，采用PDO
 * @property Redis $_redis Description
 */
class Db extends Model {

    private static $_redis = false;
    // dsn
    private $dsn = "mysql:host=localhost;dbname=test";
    // db
    private $db;
    // 单例 todo
    protected static $_instance = NULL;
    // memcached
    public $memcached = false;
    // single instance
    private static $instance = false;
    // db swcache
    public $prevDb = null;
    // file cache
    public $fileCache = true;
    // file cached
    public $fileCached = false;
    // global cache
    public $cache = true;
    // debug
    public $debug = false;

    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new Db();
        }
        return self::$instance;
    }

    /**
     * 
     * @global type $config
     * @param type $dbname
     */
    public function __construct($dbname = false) {
        global $config;
        parent::__construct();
        $this->initRedis($config);
        if ($dbname) {
            $config->db['db'] = $dbname;
        }
        $this->dsn = sprintf("mysql:host=%s;dbname=%s", $config->db['host'], $config->db['db']);
        $this->db = new PDO($this->dsn, $config->db['user'], $config->db['pass']);
        $this->db->exec("SET NAMES utf8mb4;");
    }
    
    /**
     * 使用事务
     * @return type
     */
    public function transtart() {
        $this->db->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        return $this->db->beginTransaction();
    }

    /**
     * 提交事务
     * @return type
     */
    public function transcommit() {
        return $this->db->commit();
    }

    /**
     * 回滚事务
     * @return type
     */
    public function transrollback() {
        return $this->db->rollBack();
    }

    /**
     * @param type $statement
     * @param type $mcache
     * @param type $fetchStyle
     * @return type
     */
    public function query($statement, $mcache = true, $fetchStyle = PDO::FETCH_ASSOC) {
        global $config;
        if ($this->debug) {
            echo $statement . '\r\n';
        }
        if (preg_match("/INSERT/is", $statement)) {
            // INSERT
            $this->db->exec($statement);
            $result = $this->db->lastInsertId();
        } else if (preg_match("/UPDATE|DELETE|REPLACE/s", $statement)) {
            // UPDATE|DELETE=
            $result = $this->db->exec($statement);
        } else {
            // 使用redis而且连接成功
            if ($config->redis_on && $mcache && $this->cache) {
                /**
                 * allow memcached <default>
                 */
                $sHash = $this->getSHash($statement) . '.sqlcache';
                $mca = self::$_redis->get($sHash);
                if ($mca) {
                    return unserialize($mca);
                } else {
                    // SELECT
                    $result = $this->rawQuery($statement, $fetchStyle);
                    // cache sql query resultSet
                    self::$_redis->set($sHash, serialize($result));
                    self::$_redis->expireAt($sHash, time() + $config->redis_exps);
                }
            } else {
                if ($this->fileCache && $mcache && $this->cache) {
                    // 无memcache则使用文件cache
                    if (!$this->fileCached) {
                        $this->fileCached = new SqlCached();
                    }
                    $result = $this->fileCached->get($statement);
                    if (-1 === $result) {
                        $result = $this->rawQuery($statement, $fetchStyle);
                        $this->fileCached->set($statement, $result);
                    }
                } else {
                    /**
                     * not allow cache <sf>
                     */
                    $result = $this->rawQuery($statement, $fetchStyle);
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param type $statement
     * @param type $fetchStyle
     * @return type
     */
    private function rawQuery($statement, $fetchStyle) {
        $query = $this->db->prepare($statement);
        $query->execute();
        return $query->fetchAll($fetchStyle);
    }

    /**
     * 查询一个数据
     * @param type $SQL
     * @return type
     */
    public function getOne($SQL, $cache = true) {
        $ret = $this->query($SQL, $cache);
        if (!$ret[0]) {
            return false;
        }
        return current($ret[0]);
    }

    /**
     * 查询一行数据
     * @param type $SQL
     * @return type
     */
    public function getOneRow($SQL, $cache = true) {
        $ret = $this->query($SQL, $cache);
        return $ret[0];
    }

    /**
     * 检查某字段有某值
     * @param type $table
     * @param type $field
     * @param type $value
     * @return type
     */
    public function isExist($table, $field, $value) {
        $rst = $this->query("SELECT * FROM `$table` WHERE `$field` = '$value' LIMIT 1");
        return count($rst) > 0;
    }

    /**
     * 
     * @param type $statement
     * @return type
     */
    public function exec($statement) {
        return $this->db->exec($statement);
    }

    /**
     * Redis
     * @param type $config
     * @return boolean
     */
    private function initRedis($config) {
        if ($config->redis_on && extension_loaded('redis')) {
            if (!(self::$_redis instanceof self)) {
                try {
                    self::$_redis = new Redis();
                    self::$_redis->pconnect($config->redis_host, $this->redis_port);
                } catch (Exception $ex) {
                    return false;
                }
            }
            return self::$_redis;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $statement
     * @return type
     */
    private final function getSHash($statement) {
        return md5($statement . APPID);
    }

}
