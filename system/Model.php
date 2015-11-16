<?php

if (!defined('APP_PATH')) {
    exit(0);
}

/**
 * System Super Class Model
 */

/**
 * @property Dao $Dao Data access Object
 * @property User $User User Management
 * @property Product $Product
 * @property Banners $Banners
 * @property mOrder $mOrder
 * @property mCompany $Company
 * @property Email $Email
 * @property ImageUploader $ImageUploader
 * @property Db $Db
 * @property DigCrypt $DigCrypt
 * @property mGmess $mGmess
 * @property mMemcache $Memcache
 * @property config $config Description
 * @property Smarty $Smarty Smarty
 * @property Helper $Helper Helper
 * @property UserCredit $UserCredit UserCredit
 * @property UserLevel $UserLevel UserLevel
 * @property Auth $Auth Auth
 * @property Envs $Envs Envs
 * @property Util $Util Util
 * @property Controller $Controller Controller
 * @property Load $Load Load
 * @property SqlCached $SqlCached SqlCached
 * @property GroupBuying $GroupBuying GroupBuying
 */
class Model {

    protected $Controller;

    /**
     * 数据源表
     * @var string 
     */
    private $source;

    // 构造方法
    public function __construct() {
        // nothing
    }

    /**
     * magic get
     * @param type $name
     * @return type
     */
    function __get($name) {
        $class = $this->Controller;
        if (property_exists($class, $name)) {
            return $this->Controller->$name;
        }
    }

    /**
     * magic call
     * @param type $name
     * @param type $arguments
     */
    function __call($name, $arguments) {
        // 对Controller进行动态反射，跨对象调用
        $class = new ReflectionClass('Controller');
        try {
            $ec = $class->getMethod($name);
            return $ec->invokeArgs($this->Controller, $arguments);
        } catch (ReflectionException $re) {
            die('Fatal Error : ' . $re->getMessage());
        }
        return false;
    }

    /**
     * hook
     * 手动挂载model到另外一个model
     * @todo 自动挂载
     */
    public final function hook($classA = array()) {
        foreach ($classA AS $class) {
            $className = get_class($class);
            $this->$className = $class;
            unset($className);
        }
    }

    /**
     * 设置数据源
     * @param type $source
     */
    protected final function setSource($source) {
        $this->source = $source;
    }

    /**
     * 查找数据
     * @param var $conditions
     */
    protected final function find($conditions) {
        if (is_array($conditions)) {
            
        } else {
            
        }
    }

    protected final function findFirst() {
        
    }

    /**
     * 链接控制器句柄
     * @param type $obj
     */
    public final function linkController(&$obj) {
        $this->Controller = $obj;
    }

}
