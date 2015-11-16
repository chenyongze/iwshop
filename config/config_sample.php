<?php

// 系统配置
include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sys_config.php';

// 数据库表
include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tables.php';

// 微信公众号AppId
define("APPID", "__APPID__");

// 微信公众号AppSecret
define("APPSECRET", "__APPSECRET__");

// 微信公众号通讯AESKey
define('EncodingAESKey', '');

// 微信公众号验证TOKEN
define("TOKEN", "__TOKEN__");

// <微信支付> 商户ID(partnerId)
define("PARTNER", "__PARTNER__");

// <微信支付> 商户通加密串(partnerKey)
define("PARTNERKEY", "__PARTNERKEY__");

// <微信支付> CA证书 .pem文件
define('CERT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . "apiclient_cert.pem");

// <微信支付> CA证书 .pem文件
define('CERT_KEY_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . "apiclient_key.pem");

// <微信支付> CA证书 .pem文件
define('CERT_ROOTCA', dirname(__FILE__) . DIRECTORY_SEPARATOR . "rootca.pem");

$config->debug = true;

$config->db['host'] = '__DBHOST__';

$config->db['db'] = '__DBNAME__';

$config->db['user'] = '__DBUSER__';

$config->db['pass'] = '__DBPASS__';

// 是否已经通过微信认证
$config->wechatVerifyed = true;

// 废弃，不删除
$config->useWechatAddr = true;

// 是否打开memcached缓存,需要php扩展memcached
$config->memcached['on'] = false;

// memcached Host
$config->memcached['host'] = '127.0.0.1';

// memcached Host
$config->memcached['port'] = 3306;

// memcached 过期时间 秒
$config->memcached['exps'] = 2;

// 是否打开Smarty缓存
$config->Smarty['cached'] = true;

// Smarty缓存时间间隔
$config->Smarty['cache_lifetime'] = 2;

// 系统根目录
$config->shoproot = '__DOCROOT__';

// 系统根域名 /结尾
$config->domain = '__DOMAIN__';

// 不需要微信支付直接下单 测试用
$config->order_nopayment = FALSE;

// 微信支付回调
$config->order_wxpay_notify = $config->domain . "wxpay_notify.php";
// 微信支付回调 代付
$config->order_wxpay_notify_req = $config->domain . "wxpay_notify_req.php";

// sendCloud邮件群发 废弃
$config->mail['API_KEY'] = '0Qz3I5NPMsh6hGMF';

$config->mail['server'] = "smtp.exmail.qq.com";

$config->mail['account'] = "noreply@iwshop.cn";

$config->mail['formAddress'] = "noreply@noreply.iwshop.cn";

$config->mail['password'] = "A919161a";

$config->mail['port'] = 25;

// 订单收到提示模板消息
$config->messageTpl['new_order_notify'] = 'rmtx_Ld1b-0NMZmGGac64vBIklUZg9Jz_6k8dzj5MU4';

// 代理审核通过模板消息
$config->messageTpl['company_reg_notify'] = '0EeaE15LDIg-LJc0JKFDYgOVfFZgK4hPwGfN9S13-qM';

// 订单发货模板消息
$config->messageTpl['order_exp_notify'] = 'ds5sXHkdNYW7dDqjuDiL5b47wmWGmK6KopXPuyjEt3U';

$config->imagesSuffix50 = '_x50';

$config->imagesSuffix100 = '_x100';

$config->imagesSuffix500 = '_x500';

$config->cssversion = '1.5.1';

$config->shopName = '__SHOPNAME__';

// email使用sendcloud
$config->sendCloudOn = true;

// upyun 目录
$config->upyunDir = '=';

// upyun bucket
$config->upyunBucket = '';

// upyun operator
$config->upyunOperator = '';

// upyun password
$config->upyunPassword = '';

// upyun 商品图片前缀
$config->imagesPrefix = 'http://iwshop.b0.upaiyun.com/' . $config->upyunDir;

// 商品图片目录
$config->productPicRoot = dirname(__FILE__) . "/../uploads/product_hpic/";

// 商品图片临时目录
$config->productPicRootTmp = dirname(__FILE__) . "/../uploads/product_hpic_tmp/";

// 商品外链目录
$config->productPicLink = $config->shoproot . "uploads/product_hpic/";

// 商品外链预览
$config->productPicLinkTmp = $config->shoproot . "uploads/product_hpic_tmp/";

// 图片云 商品图片目录
$config->cdnProductPicRoot = '/' . $config->upyunDir . '/product_hpic/';

// 是否启用upyun CDN
$config->usecdn = false;

// 商品Id前缀
$config->out_trade_no_prefix = 'ord-';

// redis缓存开关
$config->redis_on = false;
// redis host
$config->redis_host = '127.0.0.1';
// redis 端口
$config->redis_port = 6379;
// redis 过期
$config->redis_exps = 15;
