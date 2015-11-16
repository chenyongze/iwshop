<?php

/**
 * Conifg Object
 */
$config = new stdClass();

$config->orderStatus = array(
    'unpay' => '未支付',
    'payed' => '已支付',
    'canceled' => '已取消',
    'received' => '已完成',
    'delivering' => '快递在途',
    'closed' => '已关闭',
    'refunded' => '已退款',
    'reqing' => '代付'
);

/**
 * 系统初始化自动加载模块
 */
$config->preload = array(
      'Smarty' // Smarty模板引擎
    , 'Db'     // 数据库连接
    , 'Util'
    , 'Dao'
    , 'Banners'
    , 'Load'
    , 'Auth'
);

/**
 * 控制器默认方法，最终默认为index
 */
$config->defaultAction = array(
    'ViewProduct' => 'view_list',
    'Uc' => 'home'
);

/**
 * 默认视图文件目录
 */
$config->tpl_dir = "views";

/**
 * 默认Controller
 */
$config->default_controller = "Index";

/**
 * 模块加载自动查找路径
 */
$config->classRoot = array(
    dirname(__FILE__) . "/../controllers/",
    dirname(__FILE__) . "/../models/",
    dirname(__FILE__) . "/../system/",
    dirname(__FILE__) . '/../lib/',
    dirname(__FILE__) . '/../lib/Smarty/',
    dirname(__FILE__) . '/../lib/Smarty/plugins/',
    dirname(__FILE__) . '/../lib/Smarty/sysplugins/',
    dirname(__FILE__) . '/../lib/barcodegen/',
    dirname(__FILE__) . '/../lib/phpqrcode/',
    dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel/',
    dirname(__FILE__) . "/../controllers/Wdmin/",
    dirname(__FILE__) . "/../models/Interface/",
    dirname(__FILE__) . "/../controllers/Interface/"
);

/**
 * config -> shoproot
 * 微信支付发起路径
 */
$config->wxpayroot = 'wxpay.php';

/**
 * config -> admin_salt
 * 管理后台加密盐
 */
$config->admin_salt = '1akjx99k';

/**
 * config -> admin_salt
 * 微店加密盐
 */
$config->wshop_salt = 'a_asd(x';