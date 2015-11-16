<?php

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
interface infaceProduct {

    /**
     * 获取最新商品列表
     * @param type $cat
     * @param type $limit
     * @return type
     */
    public function getNewEst($cat = 0, $limit = 10);
    
    /**
     * 获取最热商品列表
     * @param type $cat
     * @param type $limit
     * @return type
     */
    public function getHotEst($cat = 0, $limit = 10);
}
