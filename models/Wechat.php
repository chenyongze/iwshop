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
$_TIME = time();

class Wechat {

    public $serverRoot;

    /**
     * request user openid
     * @var <string>
     */
    public $openID = null;

    /**
     * wechat origin id
     * @var <string>
     */
    private $serverID = null;

    /**
     * current time
     * @var <UNIX TIMESTAMP>
     */
    private $time;

    /**
     * mysql database
     * @var <PDO>
     */
    public $Db;
    
    
    public $Dao;

    /**
     * Wechat Class Construction method
     * @access public
     */
    public function __construct() {
        global $config;
        if (isset($_GET["echostr"]) && isset($_GET["signature"]) && isset($_GET["timestamp"]) && isset($_GET["nonce"])) {
            echo $_GET["echostr"];
            exit();
        }
        $this->Db = new Db();
        $this->Dao = new Dao();
        $this->time = time();
        $this->serverRoot = "http://" . $_SERVER['HTTP_HOST'] . $config->shoproot;
    }

    public function valid() {
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()) {
            die($echoStr);
        }
    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function responseImageText($data = array()) {
        $tpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>%s</ArticleCount>
        <Articles>%s</Articles>
        </xml>";

        $items = "";
        foreach ($data as $item) {
            $items .= "<item>";
            // cont
            $items .= "<Title><![CDATA[" . $item['title'] . "]]></Title>";
            $items .= "<Description><![CDATA[" . $item['desc'] . "]]></Description>";
            if ($item['url']) {
                $items .= "<Url><![CDATA[" . $item['url'] . "]]></Url>";
            }
            if ($item['picurl']) {
                $items .= "<PicUrl><![CDATA[" . $item['picurl'] . "]]></PicUrl>";
            }
            // cont
            $items .= "</item>";
        }

        echo sprintf($tpl, $this->openID, $this->serverID, $this->time, count($data), $items);
        exit(0);
    }

    // 向客户端发送文本
    public function responseText($contentStr) {
        $textTpl = "<xml> 
                    <ToUserName><![CDATA[%s]]></ToUserName> 
                    <FromUserName><![CDATA[%s]]></FromUserName> 
                    <CreateTime>%s</CreateTime> 
                    <MsgType><![CDATA[%s]]></MsgType> 
                    <Content><![CDATA[%s]]></Content> 
                    <FuncFlag>0</FuncFlag> 
                    </xml>";
        echo sprintf($textTpl, $this->openID, $this->serverID, $this->time, "text", $contentStr);
        exit(0);
    }

    // 事件处理入口
    public function EventRequest($postObj) {

        /*
         * unsubscribe
         */
        if ($postObj->Event == "unsubscribe") {
            $this->Db->query("REPLACE INTO wechat_subscribe_record (openid,date,dv) VALUES ('$this->openID',NOW(),-1);");
        }

        /**
         * subscribe
         */
        if ($postObj->Event == "subscribe") {
            $this->Db->query("REPLACE INTO wechat_subscribe_record (openid,date,dv) VALUES ('$this->openID',NOW(),1);");
        }

        include dirname(__FILE__) . '/../wechat/EventHandler.php';
        $EventHandler = new EventHandler();
        $EventHandler->wc = $this;
        $EventHandler->openID = $this->openID;
        $EventHandler->serverRoot = $this->serverRoot;
        $EventHandler->run($postObj);
    }

// 语音处理入口
    public function VoiceRequest($postObj) {
        $this->responseText($postObj->Recognition);
    }

// 普通文本处理入口
    public function TextRequest($Content) {
        include dirname(__FILE__) . '/../wechat/TextHandler.php';
        $TextHandler = new TextHandler();
        $TextHandler->wc = $this;
        $TextHandler->openID = $this->openID;
        $TextHandler->serverRoot = $this->serverRoot;
        $TextHandler->run($Content);
// 客服接口转发
        echo "<xml>
<ToUserName><![CDATA[$this->openID]]></ToUserName>
<FromUserName><![CDATA[$this->serverID]]></FromUserName>
<CreateTime>$this->time</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
    }

// 主入口
    public function handle() {
//get post data, May be due to the different environments 
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

// 记录openid
        $this->openID = $postObj->FromUserName;
// 记录服务号ID
        $this->serverID = $postObj->ToUserName;

//extract post data 
        if (!empty($postStr)) {
            switch ($postObj->MsgType) {
                case "text" :
                    $this->TextRequest($postObj->Content);
                    break;
                case "event" :
                    $this->EventRequest($postObj);
                    break;
                case 'voice' :
                    break;
            }
        }
    }

    public function loginQrcodeScaned($code, $openid) {
        $Db = new Db();
        $code = substr($code, 4);
        $res = $Db->query(sprintf("UPDATE `admin_login_code_token` SET `used` = 1,`bind` = '%s' WHERE `tid` = %s;", $openid, $code));
        if ($res > 0) {
            Messager::sendText(WechatSdk::getServiceAccessToken(), $openid, '后台扫描登录成功！');
        } else {
            Messager::sendText(WechatSdk::getServiceAccessToken(), $openid, '后台扫描登录失败！');
        }
    }

    /**
     * getIPaddress
     * @return type
     */
    public function getIp() {
        $cIP = getenv('REMOTE_ADDR');
        $cIP1 = getenv('HTTP_X_FORWARDED_FOR');
        $cIP2 = getenv('HTTP_CLIENT_IP');
        $cIP1 ? $cIP = $cIP1 : null;
        $cIP2 ? $cIP = $cIP2 : null;
        return $cIP;
    }

    /**
     * 回复图文内容
     * @param type $msgid
     */
    public function echoGmess($msgid) {
        $data = $this->Db->query("SELECT * FROM `gmess_page` WHERE `id` = $msgid;");
        $data = $data[0];
        $this->responseImageText(array(array(
                'title' => $data['title'],
                'url' => "$this->serverRoot?/Gmess/view/id=$msgid",
                'picurl' => $this->serverRoot . "uploads/gmess/" . $data['catimg'],
                'desc' => $data['desc']
        )));
    }

    /**
     * 自动红包
     * @param type $envid
     */
    public function autoEnvs() {
        $envid = $this->Db->getOne("SELECT `value` FROM `wshop_settings` WHERE `key` = 'auto_envs';");
        if ($envid > 0) {
            $exp = date('Y-m-d H:i:s', strtotime('+30 day'));
            $uid = $this->Db->getOne("SELECT `client_id` FROM `clients` WHERE `client_wechat_openid` = '$this->openID';");
            $ext = $this->Db->getOne("SELECT openid FROM `client_autoenvs` WHERE `openid` = '$this->openID';");
            if (!$ext) {
                $uid = $uid > 0 ? $uid : 'NULL';
                $this->Db->query("INSERT INTO `client_envelopes` (openid,uid,envid,count,exp) VALUES('$this->openID',$uid,$envid,1,'$exp');");
                $this->Db->query("INSERT INTO `client_autoenvs` (openid,envid) VALUES('$this->openID',$envid);");
                Messager::sendText(WechatSdk::getServiceAccessToken(), $this->openID, "恭喜你获得红包一个，<a href='" . $this->serverRoot . "?/Uc/envslist/'>点击查看</a>");
            }
        }
    }
    
    /**
     * 获取系统设置
     * @param type $key
     * @return type
     */
    public function getSetting($key){
        return $this->Db->getOne("SELECT `value` FROM `wshop_settings` WHERE `key` = '$key' LIMIT 1;");
    }

}

class WechatHandler {

    public $wc;
    public $openID;
    public $serverRoot;

}