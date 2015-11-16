<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 读写分离测试中
 */

return array(
    'read' => array(
        // 支持多个读实例，随机分配
        array(
            'host' => '127.0.0.1',
            'user' => 'root',
            'pass' => 'root',
            'dbname' => 'asdasddsd'
        )
    ),
    'write' => array(
        'host' => '127.0.0.1',
        'user' => 'root',
        'pass' => 'root',
        'dbname' => 'asdasddsd'
    )
);
