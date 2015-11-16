<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// 用户积分
class UserCredit extends Model {

    /**
     * 加减积分
     * @param type $uid
     * @param type $point
     * @return boolean
     */
    public function add($uid, $point) {
        if ($uid > 0 && is_numeric($point)) {
            $command = $point > 0 ? '+' : '-';
            $point = abs($point);
            $ret = $this->Db->query("UPDATE clients SET `client_credit` = client_credit $command $point wHerE client_id = $uid;");
            return $ret;
        }
        return false;
    }

    /**
     * 记录积分情况
     * @param type $uid
     * @param type $amount
     * @param type $reltype
     * @param type $relid
     */
    public function record($uid, $amount, $reltype, $relid) {
        return $this->Dao->insert(TABLE_CREDIT_RECORD, '`uid`,`amount`,`reltype`,`relid`, `dt`')
                        ->values(array($uid, $amount, $reltype, $relid, 'NOW()'))->exec();
    }

}
