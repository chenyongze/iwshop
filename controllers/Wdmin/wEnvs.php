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
class wEnvs extends Controller {

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
        $loginKey = $this->Session->get('loginKey');
        if (!$loginKey || empty($loginKey)) {
            $this->redirect('?/Wdmin/logOut');
        }
    }

    public function send() {

        $this->loadModel('Envs');

        $target = $this->post('envsTarget');
        $group = $this->post('envsGroup');
        $ids = $this->post('envsIds');
        $id = $this->post('envsId');
        $count = $this->post('envsCount');
        $envsDt = $this->post('envsDt');
        if ($envsDt == '') {
            $envsDt = date('Y-m-d', strtotime('+300 DAY'));
        }
        switch ($target) {
            case 0 :
                $ids = $this->Dao->select('client_id')->from(TABLE_USER)->exec();
                break;
            case 1 :
                $ids = $this->Dao->select('client_id')->from(TABLE_USER)->where("client_level = $group")->exec();
                break;
            case 2 :
                $ids = explode(',', $ids);
        }

        if ($target == 2) {
            foreach ($ids as $uid) {
                $this->Envs->send($uid, $id, $count, $envsDt);
            }
        } else {
            foreach ($ids as $uid) {
                $this->Envs->send($uid['client_id'], $id, $count, $envsDt);
            }
        }

        echo 1;
    }

}
