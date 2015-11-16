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
header('X-Powered-By: iWshop');

define('APP_PATH', __DIR__ . '/');

if (is_dir(dirname(__FILE__) . '/install/') && !is_file(dirname(__FILE__) . '/install/install.lock')) {

    header('Location:./install/');
    
} else {

    ini_set('display_errors', 'On');
    ini_set('magic_quotes_gpc', 'Off');

    date_default_timezone_set('PRC');

    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

    header("Content-type:text/html;charset=utf-8");

    // Config
    require_once 'config/config.php';

    // ClassLoader
    require_once 'lib/ClassLoader.php';

    // App
    require_once 'system/App.php';

    // Contoller
    require_once 'system/Controller.php';

    // Model
    require_once 'system/Model.php';

    // Smarty
    require_once 'lib/Smarty/Smarty.class.php';

    $App = App::getInstance();

    // @see URL /?/Controller/Action/@queryString
    // @see https://www.processon.com/diagraming/54d9e613e4b0105cbf1b0ab0

    $App->parseRequest();
}
