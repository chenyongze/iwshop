<?php

/**
 * 微信公众号事件推送处理
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

/**
 * @property Wechat $wc Wechat
 */
class EventHandler extends WechatHandler {

    public function run($postObj) {

        /**
         * 处理二维码扫描事件
         */
        if ($postObj->Event == "subscribe" || $postObj->Event == "SCAN") {

            if ($postObj->EventKey && preg_match("/qrscene\_/is", $postObj->EventKey)) {
                // 情景：二维码扫描并且关注公众号
                $qrscene = preg_replace("/qrscene\_/is", "", $postObj->EventKey);
                if ($qrscene >= 98000) {
                    // 企业用户加入
                    $this->wc->Db->query(sprintf("INSERT INTO `enterprise_users` (openid,eid) VALUES ('%s','%s');", $this->openID, $qrscene));
                } else {
                    // 代理用户加入
                    $ret = $this->wc->Db->query(sprintf("INSERT INTO `company_users` (openid,comid) VALUES ('%s','%s');", $this->openID, $qrscene));
                    // 对应代理关系
                    $ret && $this->wc->Db->query("UPDATE `clients` SET client_comid = $qrscene WHERE `client_wechat_openid` = '$this->openID' AND `client_comid` = 0;");
                    if ($ret) {
                        // 提醒代理
                        $openid = $this->wc->Db->query("SELECT `openid` FROM `companys` WHERE `id` = $qrscene;");
                        Messager::sendText(WechatSdk::getServiceAccessToken(), $openid[0]['openid'], date('Y-m-d') . ' 有一名会员加入了');
                    }
                }
            } else if ($postObj->Event == "SCAN") {
                // 情景：二维码扫描但是<已关注>公众号
                $qrscene = intval($postObj->EventKey);
                if ($qrscene >= 98000) {
                    // 企业用户加入
                    $this->wc->Db->query(sprintf("INSERT INTO `enterprise_users` (openid,eid) VALUES ('%s','%s');", $this->openID, $qrscene));
                } else {
                    // 代理用户加入
                    // 二维码扫描，已关注
                    $ret = $this->wc->Db->query(sprintf("INSERT INTO `company_users` (openid,comid) VALUES ('%s','%s');", $this->openID, $qrscene));
                    // 设置会员ID
                    $ret && $this->wc->Db->query("UPDATE `company_users` SET `uid` = (SELECT client_id FROM `clients` WHERE `client_wechat_openid` = '$this->openID') WHERE `comid` = '$qrscene' AND `openid` = '$this->openID';");
                    // 对应代理关系
                    $ret && $this->wc->Db->query("UPDATE `clients` SET client_comid = $qrscene WHERE `client_wechat_openid` = '$this->openID' AND `client_comid` = 0;");
                    if ($ret) {
                        $openid = $this->wc->Db->query("SELECT `openid` FROM `companys` WHERE `id` = $qrscene;");
                        Messager::sendText(WechatSdk::getServiceAccessToken(), $openid[0]['openid'], date('Y-m-d') . '有一名会员加入了');
                    }
                }
            }

            // 清除用户信息缓存
            @unlink(dirname(__FILE__) . '/../tmp/wechat_userinfo/userinfo_' . $this->openID . '.json');

            // 关注消息
            $welcomeId = $this->wc->Db->getOne("SELECT `value` FROM `wshop_settings` WHERE `key` = 'welcomegmess';");
            if ($welcomeId > 0) {
                $this->wc->echoGmess($welcomeId);
            }

            $this->wc->autoEnvs();
        }

        /**
         * 处理按钮点击事件
         */
        if ($postObj->EventKey != '') {
            
            // 自定义按钮点击
            if (strpos('K_', $postObj->EventKey)) {
                $keyId = intval(str_replace('K_', '', $postObj->EventKey));
                if ($keyId > 0) {
                    $r = $this->wc->Db->getOneRow("SELECT * FROM `wshop_menu` WHERE `id` = $keyId;");
                    switch ($r['reltype']) {
                        case 0:
                            // 纯文字
                            $this->wc->responseText($r['relcontent']);
                            break;
                        case 1:
                            // 图文
                            $this->wc->echoGmess($r['relid']);
                            break;
                        case 2:
                        // 商品推荐
                    }
                }
            }
            
            // 签到按钮点击
            if ($postObj->EventKey == 'SIGN') {
                // uid
                $uid = $this->wc->Db->getOne("SELECT `client_id` FROM `clients` WHERE `client_wechat_openid` = '$this->openID';");
                // 返回积分数额
                $credit = $this->wc->Db->getOne("SELECT `value` FROM `wshop_settings` WHERE `key` = 'sign_credit';");
                // 签到限制天数
                $limitDay = $this->wc->Db->getOne("SELECT `value` FROM `wshop_settings` WHERE `key` = 'sign_daylim';");
                // 查找上一次签到记录
                $lastSign = $this->wc->Db->getOneRow("SELECT DATEDIFF(current_date(), `dt`) AS `d`,`dt` FROM `client_sign_record` WHERE `openid` = '$this->openID' order by `dt` DESC LIMIT 1;", false);
                // 比较
                if (isset($lastSign['dt']) && $lastSign['d'] >= $limitDay) {
                    // 尝试插入 签到记录 唯一索引控制
                    $r1 = $this->wc->Db->query("INSERT INTO `client_sign_record` (`dt`,`credit`,`openid`) VALUES (NOW(),$credit,'$this->openID');");
                    // 签到记录
                    if ($r1 !== false) {
                        // 增加积分
                        $this->wc->Db->query("UPDATE `clients` SET client_credit = client_credit + $credit WHERE `client_wechat_openid` = '$this->openID';");
                        // 积分记录
                        $r1 = $this->wc->Db->query("INSERT INTO `client_credit_record` (`dt`,`amount`,`reltype`,`uid`,`relid`) VALUES (NOW(),$credit,1,'$uid',0);");
                        $this->wc->responseText("签到成功，您获得{$credit}积分。");
                    }
                } else {
                    $this->wc->responseText("你最近已经签到过了。");
                }
            }
        }
    }

}
