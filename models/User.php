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
class User extends Model {

    const MANT_BALANCE_ADD = '+';
    const MANT_BALANCE_DIS = '-';

    /**
     * 删除用户
     * @param type $userId
     */
    public function deleteUser($userId) {
        
    }

    /**
     * 修改用户信息
     * @param type $userId
     * @param type $modifyData
     */
    public function modifyUser($userId, $modifyData = array()) {
        
    }

    /**
     * 创建用户
     * @param type $userData
     */
    public function createUser($userData = array()) {
        
    }

    /**
     * 微信性别转换
     * @param type $sexInt
     * @return string
     */
    private function wechatSexConv($sexInt) {
        $sex_arr = array('NULL', "m", "f");
        return $sex_arr[($sexInt ? $sexInt : 0)];
    }

    /**
     * 获取用户信息
     * @param type $openId
     * @return type
     */
    public function getUserInfoByOpenId($openId) {
        return $this->Db->getOneRow("SELECT `client_wechat_openid` AS openid,`client_name`,`client_head`,`client_groupid` FROM `clients` WHERE `client_wechat_openid` = '$openId';");
    }

    /**
     * 
     * @param type $uid
     * @return <object>
     */
    public function getUserInfoRaw($uid = false) {
        if (!$uid) {
            $uid = $this->pCookie("uid");
        }
        if (!is_numeric($uid)) {
            $userInfosq = $this->Db->getOneRow("SELECT * from clients WHERE client_wechat_openid = '$uid';");
        } else {
            $userInfosq = $this->Db->getOneRow("SELECT * from clients WHERE client_id = $uid;");
        }
        return $userInfosq;
    }

    /**
     * 
     * @param type $uid
     * @return <object>
     */
    public function getUserInfoFull($uid) {
        $SQL = "SELECT
                cl.*, cl.client_id AS cid,
                cus.`name` AS `company_name`,
                (
                        SELECT
                                count(*)
                        FROM
                                `orders`
                        WHERE
                                client_id = cl.client_id
                ) AS `order_count`
        FROM
                `clients` cl
        LEFT JOIN `companys` cus ON cus.id = cl.client_comid
        WHERE
                cl.client_id = $uid;";
        $userInfosq = $this->Db->getOneRow($SQL);
        return $userInfosq;
    }

    /**
     * 获取用户积分
     * @param type $uid
     * @return boolean
     */
    public function getCredit($uid, $cache = false) {
        if ($uid > 0) {
            return $this->Dao->select('client_credit')->from(TABLE_USER)->where("client_id = $uid")->getOne($cache);
        } else {
            return false;
        }
    }

    /**
     * 设置用户积分
     * @param int $uid 用户UID
     * @param int $credit 设置的积分数额
     * @return boolean
     */
    public function setCredit($uid, $credit) {
        if ($uid > 0 && $credit > 0) {
            return $this->Dao->update(TABLE_USER)->set(array('client_credit' => $credit))->where("client_id = $uid")->exec();
        } else {
            return false;
        }
    }

    /**
     * 获取用户信息
     * @param type $uid
     * @return <object>
     */
    public function getUserInfo($uid = false) {
        if (!$uid) {
            $uid = $this->pCookie("uid");
        }
        $userInfosq = $this->Db->query("SELECT cs.client_address,cs.client_type,cs.client_id,cs.client_head,cs.client_wechat_openid,cs.client_name,cs.client_money from clients cs WHERE cs.client_id = $uid;");
        if ($userInfosq) {
            $info = new stdClass();
            $info->uid = (int) $userInfosq[0]['client_id'];
            $info->uhead = $userInfosq[0]['client_head'] == '' ? $this->root . 'static/images/login/profle_1.png' : $userInfosq[0]['client_head'] . '/132';
            $info->nickname = $userInfosq[0]['client_name'];
            $info->address = $userInfosq[0]['client_address'];
            $info->balance = (float) $userInfosq[0]['client_money'];
            $info->type = (int) $userInfosq[0]['client_type'];
            unset($userInfosq);
            return $info;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $uid
     * @return boolean
     */
    public function getUserEmail($uid) {
        if (is_numeric($uid)) {
            return $this->Db->getOne("SELECT `client_email` FROM `clients` WHERE client_id = $uid;");
        }
        return false;
    }

    /**
     * 用户余额操作
     * @param type $amount
     * @param type $uid
     * @param type $type
     */
    public function mantUserBalance($amount, $uid, $type = self::MANT_BALANCE_ADD) {
        $mSql = sprintf("UPDATE `clients` SET `client_money` = `client_money` $type $amount WHERE `client_id` = $uid;");
        if ($this->Db->query($mSql) !== false) {
            if ($type === self::MANT_BALANCE_DIS) {
                $amount = -$amount;
            }
            return $this->Dao->insert('client_money_record', `client_id`, `amount`, `time`)->values(array($uid, $amount, 'NOW()'))->exec();
        }
    }

    /**
     * 检查用户是否已经注册
     * @param type $openid
     */
    public function checkUserExt($openid) {
        $openid = addslashes(trim($openid));
        $ret = $this->Db->query("SELECT COUNT(*) AS count FROM `clients` WHERE `client_wechat_openid` = '$openid';");
        return $ret[0]['count'] > 0;
    }

    /**
     * 
     * @global type $config
     * @param type $uid
     * @return type
     */
    public function genUcToken($uid) {
        global $config;
        $this->loadModel('Secure');
        return hash('sha1', $this->getIp() . hash('md4', $uid) . date("Y-m") . $config->wshop_salt);
    }

    /**
     * 
     * @param type $account
     * @param type $password
     * @return boolean
     */
    public function userLogin($account, $password) {
        $password = $this->genUserPassword($password);
        $ret = $this->Db->getOneRow("SELECT `client_id` FROM `clients` WHERE (`client_email`= '$account' OR `client_phone` = '$account') AND `client_password` = '$password';");
        if ($ret !== false && isset($ret['client_id']) && is_numeric($ret['client_id'])) {
            return intval($ret['client_id']);
        } else {
            return false;
        }
    }

    /**
     * 生成用户密码
     * @global type $config
     * @param type $password
     * @return type
     */
    public function genUserPassword($password) {
        global $config;
        return hash('sha256', hash('md4', $password) . $config->wshop_salt . 'pwxd');
    }

    /**
     * 检查用户存在
     * @param type $field
     * @param type $val
     */
    public function userCheckExt($field, $val) {
        $ret = $this->Db->getOneRow("SELECT COUNT(*) AS count FROM `clients` WHERE `$field` = '$val' AND `client_wechat_openid` <> '';");
        if ($ret['count'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * 检查用户是否存在
     * @param type $openid
     * @return boolean
     */
    public function userCheckReg($openid) {
        if (empty($openid)) {
            return false;
        } else {
            $c = $this->Dao->select('')->count('*')->from(TABLE_USER)->where("client_wechat_openid = '$openid'")->getOne();
            return $c > 0;
        }
    }

    /**
     * 获取用户收藏信息
     * @param type $openid
     * @param type $limit
     * @return boolean
     */
    public function getUserLikes($openid, $limit) {
        if ($openid != '') {
            return $this->Db->query("SELECT po.*,pos.sale_prices FROM `client_product_likes` cpl LEFT JOIN `products_info` po ON po.product_id = cpl.product_id LEFT JOIN `product_onsale` pos ON pos.product_id = cpl.product_id WHERE cpl.openid = '$openid' LIMIT $limit;");
        } else {
            return false;
        }
    }

    /**
     * 添加用户收藏
     * @param type $openid
     * @param type $productId
     * @return type
     */
    public function addUserLike($openid, $productId) {
        return $this->Db->query("INSERT INTO `client_product_likes` (`openid`,`product_id`) VALUES ('$openid','$productId');");
    }

    /**
     * 删除用户收藏
     * @param type $openid
     * @param type $productId
     * @return type
     */
    public function deleteUserLike($openid, $productId) {
        return $this->Db->query("DELETE FROM `client_product_likes` WHERE `openid` = '$openid' AND `product_id` = $productId;");
    }

    /**
     * 获取用户 uid by openid
     * @param type $openid
     * @return type
     */
    public function getUidByOpenId($openid) {
        return intval($this->Db->getOne("SELECT `client_id` FROM `clients` WHERE `client_wechat_openid` = '$openid';"));
    }

    /**
     * 获取用户 openid by uid
     * @param type $uid
     * @return type
     */
    public function getOpenIdByUid($uid) {
        return $this->Db->getOne("SELECT `client_wechat_openid` FROM `clients` WHERE `client_id` = '$uid';");
    }

    /**
     * 获取用户列表
     * @param type $gid
     * @param type $limit
     * @return type
     */
    public function getUserList($gid = '', $limit = 1000) {
        if ($gid != '') {
            $Ext = " AND `client_level` = $gid";
        } else {
            $Ext = '';
        }
        if ($this->pCookie('comid')) {
            $comid = $this->Util->digDecrypt($this->pCookie('comid'));
            $SQL = "SELECT
                    cl.*,cl.client_id AS cid,
                    (
                            SELECT
                                    count(*)
                            FROM
                                    `orders`
                            WHERE
                                    client_id = cl.client_id AND `orders`.status <> 'unpay'
                    ) AS `order_count`
                    FROM
                            company_users cu
                    LEFT JOIN clients cl ON cl.client_id = cu.uid
                    WHERE
                            cu.comid = $comid AND cl.deleted = 0 LIMIT $limit;";
        } else {
            $SQL = "SELECT
                            cl.*, cl.client_id AS cid,
                            cus.`name` AS `company_name`,
                            (
                                    SELECT
                                            count(*)
                                    FROM
                                            `orders`
                                    WHERE
                                            client_id = cl.client_id AND `orders`.status <> 'unpay'
                            ) AS `order_count`,cvs.level_name as `levelname`
                    FROM
                            `clients` cl
                    LEFT JOIN `companys` cus ON cus.id = cl.client_comid
                    LEFT JOIN `client_level` cvs ON cl.client_level = cvs.id
                    WHERE
                            cl.deleted = 0$Ext
                    ORDER BY
                            cl.client_id DESC
                    LIMIT $limit;";
        }
        $list = $this->Db->query($SQL);
        foreach ($list AS &$l) {
            $l['client_sex'] = $this->Util->sexConv($l['client_sex']);
        }
        return $list;
    }

    /**
     * 获取用户头像
     * @param type $openid
     * @return type
     */
    public function getUserHeadByOpenId($openid, $size = 0) {
        $head = $this->Db->getOne("SELECT client_head FROM `clients` WHERE `client_wechat_openid` = '$openid';");
        return $head ? $head . "/$size" : 'static/images/login/profle_1.png';
    }

    /**
     * 微信进入自动注册
     */
    public function wechatAutoReg($openid = '') {
        global $config;
        // 检查用户是否注册
        if ($config->wechatVerifyed && Controller::inWechat() && !$this->userCheckReg($openid)) {
            $redirect_uri = !$redirect_uri ? $this->uri : $redirect_uri;
            // 微信用户资料 UNIONID机制
            $WechatUserInfo = WechatSdk::getUserInfo(WechatSdk::getServiceAccessToken(), $openid, true);

            if ($WechatUserInfo->subscribe > 0) {
                // 如果已经关注，那么已经获取到信息
            } else {
                // 未关注，网页授权方式获取信息
                $AccessCode = WechatSdk::getAccessCode($this->uri, "snsapi_userinfo");
                if ($AccessCode !== FALSE) {
                    // 获取到accesstoken和openid
                    $Result = WechatSdk::getAccessToken($AccessCode);
                    // 微信用户资料
                    $WechatUserInfo = WechatSdk::getUserInfo($Result->access_token, $Result->openid);
                }
            }

            // 写入用户信息
            $uid = $this->Dao->insert(TABLE_USER, 'client_nickname,client_name,client_sex,client_head,client_head_lastmod,client_wechat_openid,client_joindate,client_province,client_city,client_address')
                            ->values(array(
                                $WechatUserInfo->nickname,
                                $WechatUserInfo->nickname,
                                $this->wechatSexConv($WechatUserInfo->sex),
                                substr($WechatUserInfo->headimgurl, 0, strlen($WechatUserInfo->headimgurl) - 2),
                                'NOW()',
                                $openid,
                                'NOW()',
                                $WechatUserInfo->province,
                                $WechatUserInfo->city,
                                $WechatUserInfo->province . $WechatUserInfo->city
                            ))->exec();

            // 红包绑定uid
            $this->Dao->update(TABLE_USER_ENVL)->set(array('uid' => $uid))->where("openid = '$openid'")->aw("uid IS NULL")->exec();
            // 后续代理绑定
            if ($uid !== false) {
                // 查找 代理-会员对应关系
                $ret = $this->Dao->update(TABLE_COMPANY_USERS)->set(array('uid', $uid))->where("openid='$openid'");
                if ($ret) {
                    // 如果确实有代理推荐
                    $comid = $this->Dao->select('comid')->from(TABLE_COMPANY_USERS)->where("openid='$openid'")->limit(1)->getOne();
                    // 更新代理对应
                    $ret = $this->Dao->update(TABLE_USER)->set(array('client_comid', $comid > 0 ? $comid : 0))->where("client_id=$uid");
                }
                // set cookie
                $this->sCookie('uid', $uid, Uc::COOKIEXP);
                $this->sCookie('uctoken', $this->User->genUcToken($uid), Uc::COOKIEXP);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 从订单收货地址中提取个人信息
     * @param type $orderId
     */
    public function importFromOrderAddress($orderId) {
        $this->loadModel('mOrder');
        $orderAddr = $this->mOrder->getOrderAddr($orderId);
        if ($orderAddr) {
            return $this->Dao->update(TABLE_USER)
                            ->set(array(
                                'client_name' => $orderAddr['user_name'],
                                'client_address' => $orderAddr['address'],
                                'client_phone' => $orderAddr['tel_number'],
                                'client_province' => $orderAddr['province'],
                                'client_city' => $orderAddr['city']
                            ))->where("client_id=" . $orderAddr['client_id'])->exec();
        } else {
            return false;
        }
    }

    /**
     * 判断微信用户是否已经关注
     * @return type
     */
    public function isSubscribed() {
        if (Controller::inWechat()) {
            $openid = $this->getOpenId();
            $this->loadModel('WechatSdk');
            $WechatUserInfo = WechatSdk::getUserInfo(WechatSdk::getServiceAccessToken(), $openid, true);
            return $WechatUserInfo->subscribe == 1;
        }
        return false;
    }

    /**
     * 获取用户折扣
     * @param type $uid
     * @return type
     */
    public function getDiscount($uid) {
        if ($uid > 0) {
            return $this->Dao->select('level_discount')->from(TABLE_USER)->alias('us')
                            ->leftJoin(TABLE_USER_LEVEL)->alias('ul')
                            ->on("ul.id = us.client_level")
                            ->where("us.client_id = $uid")
                            ->limit(1)
                            ->getOne() / 100;
        } else {
            return 1;
        }
    }

    /**
     * 获取所有产品分类列表，递归
     * @param type $catParent
     * @return type
     */
    public function getAllGroup() {
        $group = WechatSdk::getUserGroup();
        $g = array();
        $g[] = array(
            'dataId' => 0,
            'name' => '全部用户'
        );
        foreach ($group as &$l) {
            $a = array();
            $a['name'] = $l['name'];
            $a['dataId'] = intval($l['id']);
            $a['open'] = 'false';
            $a['hasChildren'] = false;
            $g[] = $a;
        }
        return $g;
    }

    /**
     * 获取用户集团ID
     * @param string $openid
     * @return int
     */
    public function getEntsId($openid) {
        $entS = $this->Dao->select('eid')->from('enterprise_users')->where("openid='$openid'")->getOne();
        return $entS > 0 ? $entS : 0;
    }
    
    /**
     * 获取用户代理编号
     * @param int $openid
     * @return boolean
     */
    public function getCompanyId($openid) {
        $companyCom = intval($this->Db->getOne("SELECT `comid` FROM `company_users` WHERE `openid` = '$openid';"));
        return $companyCom > 0 ? $companyCom : 0;
    }

}
