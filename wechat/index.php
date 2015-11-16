<?php

define('APP_PATH', __DIR__ . '/../');

/**
 * 微信入口
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

include dirname(__FILE__) . '/../config/config.php';
include dirname(__FILE__) . '/../system/Model.php';
include dirname(__FILE__) . '/../models/Db.php';
include dirname(__FILE__) . '/../models/Dao.php';
include dirname(__FILE__) . '/../models/Curl.php';
include dirname(__FILE__) . '/../models/WechatSdk.php';
include dirname(__FILE__) . '/../models/Wechat.php';

$Wechat = new Wechat();
$Wechat->handle();