<?php

#error_reporting(0);

/*
 * Copyright (C) 2014 koodo@qq.com.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

switch ($_POST['a']) {
    // 数据库连接检查
    case 'db_valid' : {
            if (mysql_connect($_POST['f-dbaddress'], $_POST['f-dbusername'], $_POST['f-dbpassword'])) {
                echo 1;
            } else {
                echo 0;
            }
        }
        break;
    // 数据库安装
    case 'db_install': {
            $db = mysql_connect($_POST['f-dbaddress'], $_POST['f-dbusername'], $_POST['f-dbpassword']);
            mysql_query("drop database if exists " . $_POST['f-dbname'] . ";");
            $db_found = mysql_query("CREATE DATABASE IF NOT EXISTS " . $_POST['f-dbname'] . " DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;") !== false;
            if ($db_found) {


                $sql_file11 = dirname(__FILE__)."/iwshop.sql";

                $db11 = array();
                $db11['host'] = $_POST['f-dbaddress'];
                $db11['dbname'] = $_POST['f-dbname'];
                $db11['user'] = $_POST['f-dbusername'];
                $db11['pwd'] = $_POST['f-dbpassword'];

                echo  run_sql_file( $sql_file11 , $db11 );



            } else {
                echo -1;
            }
        }
        break;
    case 'config_install': {

            $configCont = file_get_contents(dirname(__FILE__) . '/../config/config_sample.php');

            $configCont = str_replace('__APPID__', $_POST['f-appid'], $configCont);
            $configCont = str_replace('__APPSECRET__', $_POST['f-appsecret'], $configCont);
            $configCont = str_replace('__TOKEN__', $_POST['f-token'], $configCont);
            $configCont = str_replace('__PARTNER__', $_POST['f-partner'], $configCont);
            $configCont = str_replace('__PARTNERKEY__', $_POST['f-partnerkey'], $configCont);
            $configCont = str_replace('__DBNAME__', $_POST['f-dbname'], $configCont);
            $configCont = str_replace('__DBHOST__', $_POST['f-dbaddress'], $configCont);
            $configCont = str_replace('__DBUSER__', $_POST['f-dbusername'], $configCont);
            $configCont = str_replace('__DBPASS__', $_POST['f-dbpassword'], $configCont);
            $configCont = str_replace('__DOCROOT__', $_POST['f-docroot'], $configCont);
            $configCont = str_replace('__DOMAIN__', urldecode($_POST['f-domain']), $configCont);
            $configCont = str_replace('__SHOPNAME__', urldecode($_POST['f-shopname']), $configCont);

            touch(dirname(__FILE__) . '/install.lock');

            file_put_contents('../config/config.php', $configCont);

            // 创建admin账户
            include '../config/sys_config.php';
            $db = mysql_connect($_POST['f-dbaddress'], $_POST['f-dbusername'], $_POST['f-dbpassword']);
            mysql_query("SET NAMES 'utf8mb4';");
            mysql_select_db($_POST['f-dbname']);
            $pwd = hash('sha384', $_POST['f-adminpassword'] . $config->admin_salt . hash('md4', $config->admin_salt2[0]));
            mysql_query("INSERT INTO `admin` (admin_account,admin_password,admin_permission,admin_auth) VALUES ('" . $_POST['f-adminname'] . "','$pwd',0,'stat,orde,prod,gmes,user,comp,sett');");

            if (file_exists(dirname(__FILE__) . '/install.lock')){
                echo 1;
            }else{
                echo '请检查/install/目录权限是否可写';
            }

        }
}

function run_sql_file( $sql_file , $dbconfig ) {

    define('DB_CHARSET','utf8');
    $host = $dbconfig['host'] ;
    $dbname = $dbconfig['dbname'] ;
    $user = $dbconfig['user'] ;
    $pwd = $dbconfig['pwd'] ;

    // 连接mysql数据库
    $conn = mysql_connect($host,$user,$pwd) or die( '连接mysql错误：'.mysql_error() );

    // 删除旧的数据库
    mysql_query( "DROP database IF EXISTS {$dbname} ;" ) or die ( "重新建立新的数据库 操作失败，无法删除【旧】数据库, 请检查mysql操作权限。错误信息: \n".mysql_error() );

    // 重新建立新数据库
    mysql_query( "CREATE DATABASE {$dbname} CHARACTER SET ".DB_CHARSET." ;" ) or die ( "无法创建数据库, 请检查mysql操作权限。错误信息: \n".mysql_error() );

    // 选择数据库
    mysql_select_db($dbname,$conn) or die( "连接数据库名 {$dbname} 错误：\n".mysql_error() );


    /* ############ 数据文件分段执行 ######### */
    $sql_str = file_get_contents( $sql_file );
    $piece = array(  ); // 数据段
    preg_match_all( "@([\s\S]+?;)\h*[\n\r]@" , $sql_str , $piece ); // 数据以分号;\n\r换行  为分段标记
    !empty( $piece[1] ) && $piece = $piece[1];
    $count = count($piece);
    if ( $count <= 0 ) {
        exit( 'mysql数据文件: '. $sql_file .' , 不是正确的数据文件. 请检查安装包.' );
    }

    $tb_list = array(  ); // 表名列表
    preg_match_all( '@CREATE\h+TABLE\h+[`]?([^`]+)[`]?@' , $sql_str , $tb_list );
    !empty( $tb_list[1] ) && $tb_list = $tb_list[1];
    $tb_count = count( $tb_list );

    // 开始循环执行
    for($i=0;$i<$count ;$i++){

        $sql = $piece[$i] ;
        mysql_query("SET character_set_connection='".DB_CHARSET."', character_set_results='".DB_CHARSET."', character_set_client='binary'");
        $result = mysql_query($sql);

        // 建表数量
        if ( $i < $tb_count ) {

        }
        // 执行其它语句
        else {
            if(!$result){
                echo "\n<br /> sql语句执行<font color='red'>失败</font> , 原因:".mysql_error();
                exit;
            }
        }

    }
    echo 1;
}
