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
class Enterprise extends Controller {

    public function ajaxAdd() {
        $name = $this->pPost('name');
        $phone = $this->pPost('phone');
        if ($name !== '') {
            echo $this->Dao->insert('enterprise', 'ename,ephone')->values(array($name, $phone))->exec();
        }
    }

    public function ajaxUpdate() {
        $name = $this->pPost('name');
        $phone = $this->pPost('phone');
        $id = $this->pPost('id');
        if ($name !== '') {
            $r = $this->Dao->update('enterprise')->set(array('ename' => $name, 'ephone' => $phone))->where("id=$id")->exec();
            echo $r ? 1 : 0;
        }
    }

}
