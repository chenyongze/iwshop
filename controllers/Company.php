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
class Company extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('mCompany');
    }

    /**
     * 代理申请页面
     * 选择代理级别
     */
    public function companyRequest() {
        $uid = $this->getUid();
        if ($uid > 0) {
            // 查找有无购买记录
            $buyed = $this->Dao->select('')->count()->from(TABLE_ORDERS)->where("client_id=$uid AND `status` = 'received'")->getOne() > 0;
            // 查找购买金额是否达到晋级要求
            $sum = $this->Dao->select('SUM(order_amount)')->from(TABLE_ORDERS)->where("client_id=$uid AND `status` = 'received'")->getOne();
            $this->Smarty->assign('user_buyed', $buyed);
            $this->Smarty->assign('user_ordersum', floatval($sum));
            $this->show();
        }
    }

    /**
     * 直接成为代理
     */
    public function companyDirectReg($Q) {
        $this->loadModel('User');
        $user = $this->User->getUserInfoRaw();
        $uid = $this->pCookie('uid');
        $type = intval($Q->type);
        global $config;
        if ($uid) {
            $this->loadModel('WechatSdk');
            $refundRate = array(0.02, 0.035, 0.05);
            $refundRate = $refundRate[$type];
            $companyType = array('初级代理', '晋级代理', '专业代理');
            $verify = $type === 2 ? 0 : 1;

            $SQL = sprintf("INSERT INTO `companys` "
                    . "(uid,name,phone,email,person_id,openid,join_date,verifed,return_percent,utype) "
                    . "VALUES ('%s','%s','%s','%s','%s','%s',NOW(),'$verify','$refundRate',$type);", $uid, $user['client_name'], $user['client_phone'], $user['client_email'], '', $user['client_wechat_openid']);
            $ret1 = $this->Db->query($SQL);

            if ($ret1) {
                $ret2 = $this->Db->query("UPDATE `clients` SET `is_com` = 1 WHERE client_id = $uid;");
                if ($ret2) {
                    Messager::sendTemplateMessage($config->messageTpl['company_reg_notify'], $user['client_wechat_openid'], array(
                        'first' => '尊敬的' . $user['client_name'] . '，您的 ' . $companyType[$type] . ' 申请已通过，欢迎加入瑞马优品大家庭！共同成长，传播分享好品质生活！',
                        'keyword1' => $user['client_name'],
                        'keyword2' => $user['client_phone'],
                        'keyword3' => '已通过',
                        'remark' => '点击详情 查看代理制度'
                            ), $this->getBaseURI() . "?/Gmess/view/id=122");
                }
            }

            $this->redirect('?/Uc/home/');
        }
    }

    /**
     * 添加一个com推广记录
     */
    public function addComSpread() {
        // 这个代码写的烂
        $productId = intval($this->post('productId'));
        $comId = $this->post('comId');
        $Uin = "SELECT COUNT(`rid`) AS `count` FROM " . COMPANY_SPREAD . "WHERE `product_id` = $productId AND `com_id` = '$comId';";
        $Uin = $this->Db->query($Uin);
        // 生成记录
        if ($Uin[0]['count'] == 0) {
            $SQL = "REPLACE INTO " . COMPANY_SPREAD . " (`product_id`,`com_id`) VALUES ($productId,'$comId');";
            echo $this->Db->query($SQL);
        } else {
            // 已经有记录了
            echo 0;
        }
    }

    /**
     * 添加微代理
     */
    public function addCompany() {
        $SQL = sprintf("INSERT INTO `companys` "
                . "(uid,name,phone,email,person_id,openid,join_date,return_percent,utype) "
                . "VALUES ('%s','%s','%s','%s','%s','%s',NOW(),'0.08',2);", $this->pCookie('uid'), $this->pPost('name'), $this->pPost('phone'), $this->pPost('email'), $this->pPost('ids'), $this->pPost('openid'));
        $ret = $this->Db->query($SQL);
        echo $ret ? 1 : 0;
    }

    /**
     * 判断是否微代理
     */
    private function isCompany($openid) {
        return $this->Db->query("SELECT `uid` FROM `companys` WHERE `uid` = '$openid';");
    }

    /**
     * 代理二维码页面
     */
    public function companyQrcode() {
        $this->loadModel('WechatSdk');
        $this->loadModel('mCompany');
        $openid = $this->getOpenId();
        $comId = $this->mCompany->getCompanyIdByOpenId($openid);
        if ($comId > 0) {
            $stoken = WechatSdk::getServiceAccessToken();
            $qrcodeImage = WechatSdk::getCQrcodeImage(WechatSdk::getCQrcodeTicket($stoken, $comId));
            echo $qrcodeImage;
        }
    }

    /**
     * 获取代理二维码
     * @param type $Query
     */
    public function ajaxGetCompanyQrcode($Query) {
        if (is_numeric($Query->id)) {
            $this->loadModel('WechatSdk');
            $this->Smarty->caching = false;
            $id = intval($Query->id);
            $stoken = WechatSdk::getServiceAccessToken();
            $qrcodeImage = WechatSdk::getCQrcodeImage(WechatSdk::getCQrcodeTicket($stoken, $id));
            $this->Smarty->assign('id', $id);
            $this->Smarty->assign('qrcode', $qrcodeImage);
            $this->show("wdminpage/company/ajax_qrcode.tpl");
        }
    }

    /**
     * 代理申请审核通过
     * @global type $config
     * @param type $Query
     */
    public function companyReqPass($Query) {
        global $config;
        if (isset($Query->id)) {
            $id = $Query->id;
            if ($id > 0) {
                $this->loadModel('mCompany');
                $pwd = $this->make_password();
                $pass = $this->mCompany->generateCompanyPwd($pwd);
                $r1 = $this->Db->query("UPDATE `companys` SET `verifed` = 1,`password` = '$pass' WHERE `id` = $id AND `verifed` = 0;");
                if ($r1) {
                    $info = $this->Db->getOneRow("SELECT * FROM `companys` WHERE id = $id;");
                    // 更新会员状态
                    $clientId = $this->Dao->select('uid')->from('companys')->where("id=$id")->getOne();
                    $this->Db->query("UPDATE `clients` SET `is_com` = 1 WHERE client_id = $clientId;");
                    // 更新会员状态 >
                    if ($info['uid'] > 0) {
                        $this->loadModel('WechatSdk');

//                        $stoken = WechatSdk::getServiceAccessToken();
//                        $qrcodeImage = WechatSdk::getCQrcodeImage(WechatSdk::getCQrcodeTicket($stoken, $id));
//                        $this->Smarty->assign('qrcode', $qrcodeImage);
//                        $this->loadModel('Email');
//                        // 邮件通知
//                        $this->Smarty->assign('toName', $info['name']);
//                        $this->Smarty->assign('fromName', $this->settings['shopname']);
//                        $this->Smarty->assign('fromAddress', $config->mail['account']);
//                        $this->Smarty->assign('expcom', 'http://' . $config->domain . '/admin/');
//                        $this->Smarty->assign('account', 'pa' . $info['phone']);
//                        $this->Smarty->assign('password', $pwd);
//                        $content = $this->Smarty->fetch("email/company_reg_notify.html");
//                        $subject = $this->settings['shopname'] . ' - 代理审核通知 编号#' . $id;
//                        #$this->Email->send($info['email'], $this->settings['shopname'], $subject, $content);

                        $type = $info['utype'];

                        $companyType = array('普通代理', '晋级代理', '专业代理');

                        Messager::sendTemplateMessage($config->messageTpl['company_reg_notify'], $info['openid'], array(
                            'first' => '尊敬的' . $info['name'] . '，您的 ' . $companyType[$type] . ' 申请已通过，欢迎加入瑞马优品大家庭！共同成长，传播分享好品质生活！',
                            'keyword1' => $info['name'],
                            'keyword2' => $info['phone'],
                            'keyword3' => '已通过',
                            'remark' => '点击详情 查看代理制度'
                                ), $this->getBaseURI() . "?/Gmess/view/id=122");

                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            } else {
                $id = abs($id);
                echo $this->Db->query("UPDATE `companys` SET `deleted` = 1 WHERE `id` = '$id' AND `verifed` = 0;");
            }
        } else {
            echo 0;
        }
    }

    /**
     * 生成代理密码
     * @param type $length
     * @return string
     */
    public function make_password($length = 8) {
        // 密码字符集，可任意添加你需要的字符
        $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x', 'y', 'z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        // 在 $chars 中随机取 $length 个数组元素键名
        $keys = array_rand($chars, $length);

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            // 将 $length 个数组元素连接成字符串
            $password .= $chars[$keys[$i]];
        }

        return $password;
    }

    /**
     * 增改代理信息
     * @global type $config
     */
    public function ajaxAlterCompanyInfo() {
        if ($this->post('id') == '0') {
            // add
            $field = array();
            $values = array();
            $data = $this->post('data');
            $pwd = "";
            foreach ($data as &$d) {
                if ($d['name'] == 'password') {
                    if ($d['value'] != '') {
                        $field[] = "`$d[name]`";
                        $pwd = $d['value'];
                        $pass = $this->mCompany->generateCompanyPwd($d['value']);
                        $values[] = "'$pass'";
                    } else {
                        continue;
                    }
                } else {
                    $field[] = "`$d[name]`";
                    $values[] = "'$d[value]'";
                }
            }
            $SQL = sprintf("INSERT INTO `companys` (%s) VALUES (%s);", implode(',', $field), implode(',', $values));
            $ret = $this->Db->query($SQL);
            if ($ret !== false) {
                // 代理通过通知，todo整合
                global $config;
                $this->loadModel('WechatSdk');
                $info = $this->Db->getOneRow("SELECT * FROM `companys` WHERE id = $ret;");
                $stoken = WechatSdk::getServiceAccessToken();
                $qrcodeImage = WechatSdk::getCQrcodeImage(WechatSdk::getCQrcodeTicket($stoken, $ret));
                $this->Smarty->assign('qrcode', $qrcodeImage);
                $this->loadModel('Email');
                // 邮件通知
                $this->Smarty->assign('toName', $info['name']);
                $this->Smarty->assign('fromName', $this->settings['shopname']);
                $this->Smarty->assign('fromAddress', $config->mail['account']);
                $this->Smarty->assign('expcom', 'http://' . $config->domain . '/admin/');
                $this->Smarty->assign('account', 'pa' . $info['phone']);
                $this->Smarty->assign('password', $pwd);
                $content = $this->Smarty->fetch("email/company_reg_notify.html");
                $subject = $this->settings['shopname'] . ' - 代理审核通知 编号#' . $id;
                $this->Email->send($info['email'], $this->settings['shopname'], $subject, $content);
                echo 1;
            } else {
                echo 0;
            }
        } else {
            $id = intval($this->post('id'));
            if ($id > 0) {
                $set = array();
                $data = $this->post('data');
                foreach ($data as &$d) {
                    if ($d['name'] == 'password') {
                        if ($d['value'] != '') {
                            $d['value'] = $this->mCompany->generateCompanyPwd($d['value']);
                        } else {
                            continue;
                        }
                    }
                    $set[] = "`$d[name]` = '$d[value]'";
                }
                $set = implode(',', $set);
                $sql = "UPDATE `companys` SET $set WHERE `id` = $id";
                echo $this->Db->query($sql);
            }
        }
    }

    /**
     * 代理结算
     */
    public function payCompanyBills() {
        if (intval($this->pPost('id')) > 0) {
            echo $this->mCompany->payCompanyBills($this->pPost('id'));
        } else {
            echo 0;
        }
    }

    /**
     * 删除代理
     */
    public function AjaxDeleteCompany() {
        if ($this->post('id') && is_numeric($this->post('id'))) {
            $id = intval($this->post('id'));
            $clientId = $this->Dao->select('uid')->from('companys')->where("id=$id")->getOne();
            $this->Db->query("UPDATE `clients` SET `is_com` = 0 WHERE client_id = $clientId;");
            echo $this->Db->query("DELETE FROM `companys` WHERE `id` = '$id'");
        }
    }

}
