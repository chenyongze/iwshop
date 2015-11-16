<?php

include_once 'Curl.php';
include_once 'DigCrypt.php';

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class Util extends Model {

    private $DigCrypt;

    public function __construct() {
        parent::__construct();
        $this->DigCrypt = new DigCrypt();
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
     * xssFilter
     * @todo function
     * @param type $str
     * @return type
     */
    public function xssFilter($str) {
        return $str;
    }

    public function getServerIP() {
        return gethostbyname($_SERVER["SERVER_NAME"]);
    }

    /**
     * 
     * @param type $timestamp
     * @return string
     */
    public function dateTimeFormat($timestamp) {
        $timestamp = strtotime($timestamp);
        $curTime = time();
        $space = $curTime - $timestamp;
        //1分钟
        if ($space < 60) {
            $string = "刚刚";
            return $string;
        } elseif ($space < 3600) { //一小时前
            $string = floor($space / 60) . "分钟前";
            return $string;
        }
        $curtimeArray = getdate($curTime);
        $timeArray = getDate($timestamp);
        if ($curtimeArray['year'] == $timeArray['year']) {
            if ($curtimeArray['yday'] == $timeArray['yday']) {
                $format = "%H:%M";
                $string = strftime($format, $timestamp);
                return "今天 {$string}";
            } elseif (($curtimeArray['yday'] - 1) == $timeArray['yday']) {
                $format = "%H:%M";
                $string = strftime($format, $timestamp);
                return "昨天 {$string}";
            } else {
                $string = sprintf("%d月%d日 %02d:%02d", $timeArray['mon'], $timeArray['mday'], $timeArray['hours'], $timeArray['minutes']);
                return $string;
            }
        }
        $string = sprintf("%d-%d-%d", $timeArray['year'], $timeArray['mon'], $timeArray['mday'], $timeArray['hours'], $timeArray['minutes']);
        return $string;
    }

    /**
     * 
     * @param string $ip
     * @return type
     */
    public function ipConvAddress($ip) {
        $json = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);
        $arr = json_decode($json);
        return $arr->data;
    }

    public function digEncrypt($nums) {
        return $this->DigCrypt->en($nums);
    }

    public function digDecrypt($code) {
        return $this->DigCrypt->de($code);
    }

    /**
     * 性别eng转换
     * @param type $sex
     * @return string
     */
    public function sexConv($sex) {
        $s = array('f' => '女', 'm' => '男');
        if (array_key_exists($sex, $s)) {
            return $s[$sex];
        } else {
            return '未知';
        }
    }

    /**
     * 删除目录文件
     * @param type $dir
     */
    public function delDirFiles($dir) {
        $dirs = dir($dir);
        if ($dirs && is_readable($dirs)) {
            try {
                while ($file = $dirs->read()) {
                    $file = $dir . $file;
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                return true;
            } catch (Exception $ex) {
                return false;
            }
        }
        return false;
    }

    /**
     * 数组转换XML
     * @param type $arr
     * @return string
     */
    public function toXML($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml.="<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml.="<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($arr) {
        $buff = "";
        foreach ($arr as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 生成签名
     * @param type $pack
     * @return type
     */
    public function paySign($pack) {
        ksort($pack);
        $string = $this->ToUrlParams($pack);
        $string = $string . "&key=" . PARTNERKEY;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 生成随机字符串
     * @param type $length
     * @return type
     */
    public function createNoncestr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str.= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            //$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
        }
        return $str;
    }

    /**
     * 检查是否登陆
     * @todo sessioncheck
     * @return boolean
     */
    public function isLogin() {
        if (isset($_COOKIE['uopenid']) && isset($_COOKIE['uid'])) {
            return $this->User->checkUserExt($_COOKIE['uopenid']);
        } else {
            return false;
        }
    }

}
