<?php

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class wSettings extends Controller {

    /**
     * 权限检查
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('Session');
        $this->Session->start();
        if ($this->Session->get('loginKey') === false) {
            $this->redirect('?/Wdmin/logOut');
        }
    }

    /**
     * 更新系统设置项
     * 直接replace
     */
    public function updateSettings() {
        $data = $this->post('data');
        if (is_array($data) && count($data) > 0) {
            foreach ($data as &$d) {
                $d['value'] = trim(str_replace("'", '"', $d['value']));
                $set[] = "('$d[name]', '$d[value]', NOW())";
            }
            $set = implode(',', $set);
            $sql = "REPLACE INTO `wshop_settings` (`key`,`value`,`last_mod`) VALUES $set;";
            echo $this->Db->query($sql);
        } else {
            echo 0;
        }
    }

    public function ajaxGetSettings() {
        $jsonA = array();
        $datas = $this->Dao->select()->from('wshop_settings')->exec();
        foreach ($datas as $da) {
            $jsonA[$da['key']] = $da['value'];
        }
        $this->echoJson($jsonA);
    }

    public function ajaxGetExpTemplate() {
        $datas = $this->Dao->select()->from('wshop_settings_expfee')->exec();
        $this->echoJson($datas);
    }

    public function updateExpTemplate() {
        $data = $this->post('data');
        $this->Db->query('TRUNCATE TABLE `wshop_settings_expfee`;');
        foreach ($data as $a) {
            $this->Dao->insert('wshop_settings_expfee', '`province`,`ffee`,`ffeeadd`')
                    ->values($a)->exec();
        }
    }

    public function addEnvs() {
        $this->loadModel('Envs');
        $id = $this->post('id') != '' ? $this->post('id') : false;
        echo $this->Envs->add($id, $this->post('name'), $this->post('req'), $this->post('dis'), $this->post('pid'), $this->post('remark'));
    }

    public function delteEnvs() {
        $this->loadModel('Envs');
        echo $this->Envs->delete($this->post('id'));
    }

    public function deleteAuth() {
        $id = $this->post('id');
        if ($id > 0) {
            echo $this->Dao->delete()->from(TABLE_AUTH)->where("id = $id")->exec();
        }
    }

    /**
     * 添加权限账号
     */
    public function addAuth() {
        $id = $this->post('id');
        $acc = $this->post('acc');
        $auth = $this->post('auth');
        $pwd = $this->post('pwd');
        $this->loadModel('WdminAdmin');
        if ($pwd != '') {
            $pwd = $this->WdminAdmin->encryptPassword($pwd);
            if ($id > 0) {
                echo $this->Dao->update(TABLE_AUTH)->set(array(
                    'admin_account' => $acc,
                    'admin_auth' => $auth,
                    'admin_password' => $pwd
                ))->where("id = $id")->exec();
            } else {
                echo $this->Dao->insert(TABLE_AUTH, 'admin_account, admin_auth, admin_password')
                        ->values(array($acc, $auth, $pwd))->exec();
            }
        } else {
            if ($id > 0) {
                echo $this->Dao->update(TABLE_AUTH)->set(array(
                    'admin_account' => $acc,
                    'admin_auth' => $auth,
                ))->where("id = $id")->exec();
            } else {
                echo $this->Dao->insert(TABLE_AUTH, 'admin_account, admin_auth')
                        ->values(array($acc, $auth))->exec();
            }
        }
    }

    /**
     * 编辑首页板块
     */
    public function alterSection() {
        $id = $this->post('id');
        $name = $this->post('name');
        $pid = $this->post('pid');
        $banner = $this->post('banner');
        $relId = $this->post('relId');
        $bsort = $this->post('bsort');
        $ftime = $this->post('ftime');
        $ttime = $this->post('ttime');
        if (!$bsort || !is_numeric($bsort)) {
            $bsort = 0;
        }
        if ($ftime == '') {
            $ftime = 'NULL';
        }
        if ($ttime == '') {
            $ttime = 'NULL';
        }
        if ($id > 0) {
            echo $this->Dao->update(TABLE_HOME_SECTION)->set(array(
                'name' => $name,
                'pid' => $pid,
                'banner' => $banner,
                'relid' => $relId,
                'ftime' => $ftime,
                'ttime' => $ttime,
                'bsort' => $bsort
            ))->where("id = $id")->exec();
        } else {
            echo $this->Dao->insert(TABLE_HOME_SECTION, '`name`,`pid`,`banner`,`relid`,`ftime`,`ttime`,`bsort`')
                    ->values(array($name, $pid, $banner, $relId, $ftime, $ttime, $bsort))->exec();
        }
    }

    /**
     * 清空抢红包记录
     */
    public function clearEnvsRobRecord() {
        $eid = $this->post('eid');
        echo $this->Db->query("DELETE FROM `envs_robrecord` WHERE `eid` = $eid;") ? 1 : 0;
    }

    /**
     * ajax编辑用户 | 添加用户
     */
    public function ajaxAlterEnvs() {
        if ($this->post('id') == '0') {
            // add
            $field = array();
            $values = array();
            $data = $this->post('data');
            foreach ($data as &$d) {
                $field[] = "`$d[name]`";
                $values[] = "'$d[value]'";
            }
            $SQL = sprintf("INSERT INTO `envs_robblist` (%s) VALUES (%s);", implode(',', $field), implode(',', $values));
            $ret = $this->Db->query($SQL);
            if ($ret !== false) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            // update
            $id = intval($this->post('id'));
            if ($id > 0) {
                $set = array();
                $gid = false;
                $data = $this->post('data');
                foreach ($data as &$d) {
                    $set[] = "`$d[name]` = '$d[value]'";
                }
                $set = implode(',', $set);
                $sql = "UPDATE `envs_robblist` SET $set WHERE `id` = $id";
                echo $this->Db->query($sql);
            }
        }
        #echo $SQL;
    }

    public function deleteEnvsRob() {
        $id = $this->post('id');
        echo $this->Dao->delete()->from(TABLE_ENVS_ROBLIST)->where("id = $id")->exec();
    }

}
