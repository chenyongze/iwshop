<?php

class mRedis extends CI_Model {

    private static $_redis;
    
    private $on = false;

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * redis 单例模式
     * @return Redis Sigle Instance
     */
    public function get_instance() {
        if ($this->on && extension_loaded('redis')) {
            if (!(self::$_redis instanceof self)) {
                try{
                    self::$_redis = new Redis();
                    self::$_redis->pconnect('127.0.0.1', 6379);
                } catch (Exception $ex) {
                    return false;
                }
            }
            return self::$_redis;
        } else {
            return false;
        }
    }

}
