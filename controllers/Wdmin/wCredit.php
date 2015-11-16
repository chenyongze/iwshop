<?php

/**
 * 积分相关控制器
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class wCredit extends Controller {

    /**
     * 权限检查
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('Session');
        $this->loadModel('CreditExchange');
        $this->Session->start();
        $loginKey = $this->Session->get('loginKey');
        if (!$loginKey || empty($loginKey)) {
            $this->redirect('?/Wdmin/logOut');
        }
    }

    public function modify() {
        $id = $this->post('id');
        $amount = $this->post('amount');
        if ($id > 0 && $amount > 0) {
            if ($this->CreditExchange->modi($id, $amount)) {
                $this->echoMsg(0);
            } else {
                $this->echoMsg(-1);
            }
        } else {
            $this->echoMsg(-1);
        }
    }

    public function delete() {
        $id = $this->post('id');
        if (!empty($id) && is_numeric($id)) {
            if ($this->CreditExchange->del($id)) {
                $this->echoMsg(0);
            } else {
                $this->echoMsg(-1);
            }
        } else {
            $this->echoMsg(-1);
        }
    }

    public function add() {
        $ids = $this->post('ids');
        if (!empty($ids)) {
            $ids = explode(',', $ids);
            foreach ($ids as $id) {
                if (is_numeric($id) && $id > 0) {
                    $this->CreditExchange->add(trim($id), 0);
                }
            }
            $this->echoMsg(0);
        } else {
            $this->echoMsg(-1);
        }
    }

}
