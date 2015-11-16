<?php

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class WdminAdmin extends Model {

    /**
     * 生成admin加密密文
     * @global type $config
     * @param type $pwd
     * @return type
     */
    public function encryptPassword($pwd) {
        global $config;
        return hash('sha384', $pwd . $config->admin_salt);
    }

    /**
     * 校验登陆提交密码
     * @param type $pwd_db
     * @param type $name_submit
     * @param type $pwd_submit
     * @return type
     */
    public function pwdCheck($pwd_db, $pwd_submit) {
        return $pwd_db == $this->encryptPassword($pwd_submit);
    }

    /**
     * 生成登陆token
     * @global type $config
     * @param type $ip
     * @param type $id
     * @param type $pwd
     * @return type
     */
    public function encryptToken($ip, $id, $pwd) {
        global $config;
        return hash('sha384', $pwd . $config->admin_salt . hash('md4', $id . $ip));
    }

    /**
     * 管理员登陆记录
     * @param type $account
     * @param type $ip
     * @param type $id
     */
    public function updateAdminState($account, $ip, $id) {
        // 更新登陆时间
        $this->Db->query("UPDATE `admin` SET `admin_last_login` = NOW(),`admin_ip_address` = '$ip' WHERE id = $id;");
        // 写入登陆记录
        @$this->Db->query("INSERT INTO `admin_login_records` (`account`, `ip`, `ldate`) VALUE ('$account', '$ip', NOW())");
    }

}
