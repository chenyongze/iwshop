<?php

/**
 * 微信公众号消息推送处理
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
// 会员红包表
define('TABLE_USER_ENVL', 'client_envelopes');

/**
 * @property Wechat $wc Wechat
 */
class TextHandler extends WechatHandler {

    /**
     * 客服消息接口转发已经内置到 <models/Wechat.php> 中
     * @param type $Content
     */
    public function run($Content) {

        $Content = trim($Content);

        //抢红包
        $Robs = $this->wc->Db->query("SELECT * FROM `envs_robblist` WHERE `key` = '$Content';", FALSE);
        
        if (sizeof($Robs) > 0) {
    		$Robs = $Robs[0];
            $RobId = $Robs['id'];
            $envsRobId = $Robs['envsid'];
            $envsRobOpen = $Robs['on'];
            if ($envsRobOpen > 0) {
                // 是否已经抢过
                $ex = $this->wc->Db->query("SELECT * FROM `envs_robrecord` WHERE `openid` = '$this->openID' AND `envsid` = $envsRobId AND `eid` = $RobId;", FALSE);
                if (count($ex) == 0) {
                    // update限量 悲观锁
                    $remains = $Robs['remains'];
                    if ($remains >= 1) {
                        $remains--;
                        // 默认30天过期
                        $exp = date('Y-m-d H:i:s', strtotime('+30 day'));
                        // 获取uid
                        $uid = $this->wc->Db->getOne("SELECT `client_id` FROM `clients` WHERE `client_wechat_openid` = '$this->openID';");
                        $uid = $uid > 0 ? $uid : NULL;
                        $ex = $this->wc->Db->getOne("SELECT * FROM `client_envelopes` WHERE openid = '$this->openID' AND envid = $envsRobId and `exp` = '$exp';");
                        if ($ex && isset($ex['openid'])) {
                            $this->wc->Db->query("UPDATE `client_envelopes` set `count` = `count` + 1 WHERE openid = '$this->openID' AND `envid` = $envsRobId AND `exp` = '$exp'");
                        } else {
                            $this->wc->Db->query("INSERT INTO `client_envelopes` (openid,uid,envid,count,exp) VALUES('$this->openID',$uid,$envsRobId,1,'$exp');");
                        }
                        $this->wc->Db->query("INSERT INTO `envs_robrecord` (openid,envsid,eid) VALUES('$this->openID',$envsRobId,$RobId);");
                        $count = $this->wc->Db->getOne("SELECT COUNT(*) FROM `envs_robrecord` WHERE `eid` = $RobId;");
                        $envsName = $this->wc->Db->getOne("SELECT `name` FROM `client_envelopes_type` WHERE `id` = $envsRobId;");
                        $this->wc->Db->query("UPDATE `envs_robblist` SET `remains` = `remains` - 1 WHERE `id` = '$RobId';");
                        $this->wc->responseText("恭喜你获得'$envsName'红包一个，<a href='" . $this->serverRoot . "?/Uc/home/'>点击查看</a>，您是第{$count}位抢到红包的朋友，红包还剩{$remains}个。");
                    } else {
                        $this->wc->responseText("红包已抢完了哦");
                    }
                } else {
                    $this->wc->responseText("您已参加过此次活动");
                }
            }
        }

        // 系统定义自动回复
        $rep = $this->wc->Db->getOneRow("SELECT * FROM `wechat_autoresponse` WHERE `key` = '$Content';");
        
        /*
         * Added By Lei
         * http://www.jiloc.com
         * jerry.jee@live.com
         * 没有设定关键词的默认回复，名为 default
         */
        if(!$rep){
            $rep = $this->wc->Db->getOneRow("SELECT * FROM `wechat_autoresponse` WHERE `key` = 'default';");
        }

        if ($rep) {
            // 自动回复已匹配
            $this->wc->Db->query("INSERT INTO `client_messages` (`openid`,`msgcont`,`autoreped`,`send_time`) VALUES ('$this->openID','$Content',1,NOW());");
            if ($rep['rel'] != 0 && $rep['reltype'] == 1) {
                $this->wc->echoGmess($rep['rel']);
            } else {
                $this->wc->responseText($rep['message']);
            }
        } else {
            @$this->wc->Db->query("INSERT INTO `client_messages` (`openid`,`msgcont`,`autoreped`,`send_time`) VALUES ('$this->openID','$Content',0,NOW());");
        }
        @$this->wc->Db->query("REPLACE INTO `client_message_session` (`openid`,`undesc`,`unread`,`lasttime`) VALUES ('$this->openID','$Content',(SELECT COUNT(*) FROM `client_messages` WHERE `openid` = '$this->openID' AND `msgtype` = 0 AND `sreaded` = 0),NOW());");
    }

}