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
class Index extends Controller {

    /**
     * 
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

    /**
     * 店铺首页
     * @param type $Q
     */
    public function index($Q) {

        $this->loadModel('User');
        $this->loadModel('WechatSdk');

        $openId = $this->getOpenId();

        // 微信注册
        $this->User->wechatAutoReg($openId);

        $this->Smarty->cache_lifetime = 60;

        if (!$this->isCached()) {

            $this->loadModel('Product');
            $this->loadModel('HomeSection');

            // hotProduct
            $productHot = $this->Product->getHotEst(false, 4);

            // newProduct
            $productNew = $this->Product->getList(false, 0, 4);

            // topBanners
            $this->assign('topBanners', $this->Banners->getBanners(0));
            // bottomBanners
            $this->assign('bottomBanners', $this->Banners->getBanners(1));
            // section
            $sec = $this->HomeSection->gets();
            foreach ($sec as &$s) {
                $s['products'] = $this->Product->getIn($s['pid']);
            }
            $this->assign('Section', $sec);
            $this->assign('productHot', $productHot);
            $this->assign('productNew', $productNew);
        }
        $this->show();
    }

}
