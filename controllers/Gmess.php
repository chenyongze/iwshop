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
class Gmess extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('mGmess');
    }

    /**
     * 群发页面
     * @param type $Query
     */
    public function view($Query) {
        $id = (int) $Query->id;
        $this->initSettings(true);//读admin_setting
        if ($id > 0) {
            $this->cacheId = $id;
            $this->Smarty->cache_lifetime = 7200;
            if (!$this->isCached()) {
                $this->Db->query("UPDATE `gmess_send_stat` set read_count = read_count + 1 WHERE `msg_id` = $id;");
                $gmess = $this->mGmess->getGmess($id);
                $gmess['createtime'] = date("Y-n-d", strtotime($gmess['createtime']));
                $this->Smarty->assign('page', $gmess);
            }
            $this->show();
        }
    }

    /**
     * ajax记录分享数量
     * @param type $Query
     */
    public function ajaxUpShare($Query) {
        $id = (int) $Query->id;
        $this->Db->query("UPDATE `gmess_send_stat` set share_count = share_count + 1 WHERE `msg_id` = $id;");
        $this->log("UPDATE `gmess_send_stat` set share_count = share_count + 1 WHERE `msg_id` = $id;");
    }

    /**
     * ajax删除素材
     */
    public function ajaxDelByMsgId() {
        $this->loadModel('mGmess');
        $id = intval($this->post('msgid'));
        $this->echoJson(array('status' => $this->mGmess->deleteGmess($id) === false ? 0 : 1));
    }

    /**
     * 获取素材
     * @param type $Query
     */
    public function ajaxGetGmess($Query) {
        $id = (int) $Query->id;
        $res = $this->Db->query("SELECT * FROM `gmess_page` WHERE `id` = $id;");
        $this->echoJson($res[0]);
    }

    public function getGmessCategory() {
        echo $this->toJson($this->mGmess->getGmessCategory(0));
    }

    /**
     * 高级群发
     * @param type $gmessId
     * @param type $method
     * @param type $isGroup
     * @param type $GroupId
     * @param type $openIds
     */
    public function sendGmessNWay() {
        $this->loadModel('WechatSdk');

        $gmessId = intval($this->post('id'));
        $method = $this->post('method');
        $groupId = $this->post('groupid');
        $toUser = $this->post('openid');
        $total = $this->post('total');

        if ($gmessId > 0) {
            $gmess = $this->mGmess->getGmess($gmessId);
            if ($gmess['catimg'] != '' && $gmess['thumb_media_id'] == '') {
                $thumbMediaId = WechatSdk::upLoadMedia($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'wshop' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'images_gmess' . DIRECTORY_SEPARATOR . $gmess['catimg'], 'image');
                $thumbMediaId = $thumbMediaId['media_id'];
                $this->Db->query("UPDATE `gmess_page` SET `thumb_media_id` = '$thumbMediaId' WHERE `id` = $gmessId;");
            } else {
                $thumbMediaId = $gmess['thumb_media_id'];
            }
            $mediaId = WechatSdk::upLoadGmess($thumbMediaId, $gmess['title'], $gmess['content'], $gmess['desc']);
            if (isset($mediaId['media_id'])) {
                $mediaId = $mediaId['media_id'];
                $this->Db->query("UPDATE `gmess_page` SET `media_id` = '$mediaId' WHERE `id` = $gmessId;");
            }
        }

        if ($method == 'openid') {
            // openid列表群发
            $ret = WechatSdk::sendGmessOpenId($mediaId, $toUser);
            // 总数要换一下
            $total = count($toUser);
        } else if ($method == 'all') {
            $ret = WechatSdk::sendGmessAll($mediaId, true);
        } else if ($method == 'group') {
            $ret = WechatSdk::sendGmessAll($mediaId, false, $groupId);
        } else {
            $ret['errcode'] = 1;
        }
        if (isset($ret) && $ret['errcode'] == 0) {
            $SQL = sprintf("INSERT INTO `gmess_send_stat` (msg_id,send_date,send_count,receive_count,msg_type,send_type) "
                    . " VALUES (%s,NOW(),%s,%s,'images',1);", $gmessId, $total, $total);
            $this->Db->query($SQL);
        }
        echo $ret['errcode'];
    }

    /**
     * 客服消息群发
     * @param type $gmessId
     * @param type $method
     * @param type $isGroup
     * @param type $GroupId
     * @param type $openIds
     */
    public function sendGmessSWay() {
        $gmessId = intval($this->post('id'));
        $openIds = $this->post('openid');
        if (is_array($openIds) && count($openIds) > 0) {
            // openid列表群发
        } else {
            echo 0;
        }
    }

}
