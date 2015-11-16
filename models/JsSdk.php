<?php

class JsSdk extends Model {

    private $appId;
    private $appSecret;

    public function __construct() {
        parent::__construct();
    }

    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => APPID,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        $file = dirname(__FILE__) . "/json_cache/jsapi_ticket.json";
        if (!is_dir(dirname(__FILE__) . "/json_cache/")) {
            mkdir(dirname(__FILE__) . "/json_cache/", 0777);
        }
        if (!is_file($file)) {
            file_put_contents($file, '{"jsapi_ticket":"","expire_time":0}');
        }
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents($file));
        if ($data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$accessToken&type=jsapi";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                file_put_contents($file, json_encode($data));
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }

    private function getAccessToken() {
        $file = dirname(__FILE__) . "/json_cache/access_token.json";
        if (!is_dir(dirname(__FILE__) . "/json_cache/")) {
            mkdir(dirname(__FILE__) . "/json_cache/", 0777);
        }
        if (!is_file($file)) {
            file_put_contents($file, '{"expire_time":0,"access_token":""}');
        }
        // access_token 应该全局存储与更新，以下代码以写入到文件中
        $data = json_decode(file_get_contents($file));
        if ($data->expire_time < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . APPID . "&secret=" . APPSECRET;
            $res = json_decode(Curl::get($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                file_put_contents($file, json_encode($data));
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97'); // 模拟用户使用的浏览器 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转 
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer 
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回 
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

}
