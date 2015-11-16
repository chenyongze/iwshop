<?php

/**
 * 管理后台页面控制器
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class WdminPage extends Controller {

    const TPL = './views/wdminpage/';

    /**
     * 
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        if (!$this->Auth->checkAuth()) {
            $this->redirect('?/Wdmin/logOut');
        }
    }

    /**
     * 报表首页
     */
    public function home() {
        $this->show();
    }

    /**
     * 订单导出
     */
    public function orderListExport() {
        $this->show(self::TPL . 'orders/order_list_export.tpl');
    }

    /**
     * 代发货订单
     */
    public function orders_toexpress() {
        $this->show(self::TPL . 'orders/orders_toexpress.tpl');
    }

    /**
     * 代发货订单
     * @param type $Query
     */
    public function orders_received($Query) {
        $year = date('Y');
        for ($i = 1; $i <= 12; $i++) {
            $months[] = array('index' => $year . '-' . $i, 'name' => $year . '年 ' . $i . '月');
        }
        $month = isset($Query->month) ? $Query->month : date('Y-m');
        $this->assign('month', $month);
        $this->assign('months', $months);
        $this->show(self::TPL . 'orders/orders_received.tpl');
    }

    /**
     * 快递中订单
     * @param type $Query
     */
    public function orders_expressing($Query) {
        $year = date('Y');
        for ($i = 1; $i <= 12; $i++) {
            $months[] = array('index' => $year . '-' . $i, 'name' => $year . '年 ' . $i . '月');
        }
        $month = isset($Query->month) ? $Query->month : date('Y-m');
        $this->assign('month', $month);
        $this->assign('months', $months);
        $this->show(self::TPL . 'orders/orders_expressing.tpl');
    }

    /**
     * 所有订单
     * @param type $Query
     */
    public function orders_all($Query) {
        $year = date('Y');
        $months = array();
        for ($i = 1; $i <= 12; $i++) {
            $months[] = array('index' => $year . '-' . $i, 'name' => $year . '年 ' . $i . '月');
        }
        $month = isset($Query->month) ? $Query->month : date('Y-m');
        $this->assign('month', $month);
        $this->assign('months', $months);
        $this->show(self::TPL . 'orders/orders_all.tpl');
    }

    /**
     * 订单管理
     */
    public function orders_manage() {
        $this->show(self::TPL . 'orders/manage.tpl');
    }

    /**
     * 退货申请
     * @param type $Query
     */
    public function orders_toreturn($Query) {
        $year = date('Y');
        $months = array();
        for ($i = 1; $i <= 12; $i++) {
            $months[] = array('index' => $year . '-' . $i, 'name' => $year . '年 ' . $i . '月');
        }
        $month = isset($Query->month) ? $Query->month : date('Y-m');
        $this->assign('month', $month);
        $this->assign('months', $months);
        $this->show(self::TPL . 'orders/orders_toreturn.tpl');
    }

    /**
     * 退款申请
     * @param type $Query
     */
    public function orders_refunded($Query) {
        $year = date('Y');
        for ($i = 1; $i <= 12; $i++) {
            $months[] = array('index' => $year . '-' . $i, 'name' => $year . '年 ' . $i . '月');
        }
        $month = isset($Query->month) ? $Query->month : date('Y-m');
        $this->assign('month', $month);
        $this->assign('months', $months);
        $this->show(self::TPL . 'orders/orders_refunded.tpl');
    }

    /**
     * 未支付订单
     */
    public function orders_unpay($Query) {
        $year = date('Y');
        $months = array();
        for ($i = 1; $i <= 12; $i++) {
            $months[] = array('index' => $year . '-' . $i, 'name' => $year . '年 ' . $i . '月');
        }
        $month = isset($Query->month) ? $Query->month : date('Y-m');
        $this->assign('month', $month);
        $this->assign('months', $months);
        $this->show(self::TPL . 'orders/orders_unpay.tpl');
    }

    /**
     * 所有产品
     */
    public function list_products() {
        $this->show(self::TPL . 'products/list_products.tpl');
    }

    /**
     * 库存管理
     */
    public function list_product_instock() {
        $this->show(self::TPL . 'products/list_product_instock.tpl');
    }

    /**
     * 库存管理
     */
    public function list_product_instock_in($Query) {
        $cat = isset($Query->cat) ? intval($Query->cat) : false;
        $this->Smarty->cache_id = $cat;
        $this->Smarty->caching = true;

        if (!$this->isCached()) {
            $iscom = $this->pCookie('comid') ? 1 : '';
            $this->assign('cat', $cat);
            $this->assign('iscom', $iscom);
        }

        $this->show(self::TPL . 'products/list_product_instock_in.tpl');
    }

    /**
     * 编辑商品信息
     * @param type $Query
     */
    public function iframe_alter_product($Query) {

        $this->loadModel('Product');
        $this->loadModel('mProductSpec');
        $this->loadModel('Supplier');
        $mod = $Query->mod;

        // 获取系列
        $serials = $this->Product->getSerials();

        // 获取分类
        $categorys = $this->Product->getAllCats();

        // 获取品牌
        $brands = $this->Product->getAllBrands();

        // 编辑模式
        if ($mod == 'edit') {
            $pid = $this->pGet('id');
            $productInfo = $this->Product->getProductInfo($pid, false);
            $this->assign('ed', true);
            $this->assign('pd', $productInfo);
            $this->assign('cat', $productInfo['product_cat']);
        } else {
            $cat = $Query->catid;
            $this->assign('ed', false);
            $this->assign('cat', $cat);
            $pid = false;
        }

        // 集团列表
        // $ents = $this->Dao->select()->from('enterprise')->exec();
        // 集团折扣
        //        foreach ($ents as &$ent) {
        //            if (!$pid) {
        //                $ent['discount'] = 1.0;
        //            } else {
        //                $ent['discount'] = $this->Dao->select('discount')->from('product_enterprise_discount')->where("productId=$pid AND entId = $ent[id]")->getOne();
        //                $ent['discount'] = $ent['discount'] ? $ent['discount'] : 1.0;
        //            }
        //        }
        // 获取规格列表
        $speclist = $this->mProductSpec->getSpecList();

        // 获取商家列表
        $suppliers = $this->Supplier->getList();

        $this->assign('suppliers', $suppliers);
        $this->assign('speclist', $speclist);
        // $this->assign('ents', $ents);
        $this->assign('brands', $brands);
        $this->assign('categorys', $categorys);
        $this->assign('serials', $serials);
        $this->assign('mod', $mod);
        $this->show(self::TPL . 'products/iframe_alter_product.tpl');
    }

    /**
     * iframe商品列表
     * @param type $Query
     */
    public function iframe_list_product($Query) {

        $cat = isset($Query->cat) ? intval($Query->cat) : false;
        $this->Smarty->cache_id = $cat;
        $this->Smarty->caching = true;

        if (!$this->isCached()) {
            $iscom = $this->pCookie('comid') ? 1 : '';
            $this->assign('cat', $cat);
            $this->assign('iscom', $iscom);
        }

        $this->show(self::TPL . 'products/iframe_list_products.tpl');
    }

    /**
     * ajax 添加商品分类
     */
    public function ajax_add_category() {
        $this->loadModel('Product');
        $categorys = $this->Product->getAllCats();
        $this->assign('categorys', $categorys);
        $this->show(self::TPL . 'products/ajax_add_category.tpl');
    }

    /**
     * ajax 拉取数据
     * 测试京东
     */
    public function ajax_fetch_data() {

        include './lib/simple_html_dom.php';

        $ret = Curl::get('http://item.jd.com/1690984.html');
        // http://p.3.cn/prices/mgets?callback=&type=1&area=1&skuIds=J_981250%2CJ_1208740%2CJ_1033729%2CJ_1103407%2CJ_763586%2CJ_875760%2CJ_1690975%2CJ_405074%2CJ_565232%2CJ_692918%2CJ_1208740%2CJ_1195848%2CJ_261513%2CJ_852431%2CJ_992362%2CJ_405074%2CJ_565232%2CJ_392541%2CJ_692918%2CJ_1107763%2CJ_981250&_=1444833422133
        $ret = iconv('GBK', 'UTF-8', $ret);
//        var_dump($ret);exit();
        $product_info = array();
        $html = new simple_html_dom();
        $html->load($ret);
        //京东
        $product_info['product_name'] = $html->find('#name h1')->innertext; //商品名称
        $product_info['product_desc'] = $html->find('#p-ad')->innertext; //商品描述
        $product_info['product_price'] = $html->find('#jd-price')->innertext; //商品价格
        $this->assign('product_info', $product_info);
//        var_dump($product_info);exit();
        $this->show(self::TPL . 'products/ajax_fetch_data.tpl');
        $html->clear(); //清除simple_html_dom对象
    }

    /**
     * ajax 添加品牌
     */
    public function ajax_add_brand() {
        $this->loadModel('Product');
        $categorys = $this->Product->getAllCats();
        $this->assign('categorys', $categorys);
        $this->show(self::TPL . 'products/ajax_add_brand.tpl');
    }

    /**
     * 商品分类列表
     */
    public function alter_products_category() {
        $this->show(self::TPL . 'products/alter_products_category.tpl');
    }

    /**
     * 编辑商品分类
     * @param type $Query
     */
    public function alter_category($Query) {
        $id = intval($Query->id);
        // ch
        if (is_numeric($id)) {

            $this->loadModel('Product');
            $cati = $this->Product->getCatInfo($id, false);
            $categorys = $this->Product->getAllCats();
            $this->assign('id', $id);
            $this->assign('cat', $cati);
            $this->assign('categorys', $categorys);
            $this->show(self::TPL . 'products/alter_category.tpl');
        }
    }

    /**
     * 素材列表
     */
    public function gmess_list() {
        $this->loadModel('mGmess');
        $list = $this->mGmess->getGmessList();
        $this->assign('list', $list);
        $this->show(self::TPL . 'gmess/gmess_list.tpl');
    }

    /**
     * 微信消息群发 已发列表
     */
    public function gmess_sent() {
        global $config;
        $sendType = array('客服消息接口', '高级群发接口');
        $list = $this->Db->query(sprintf("SELECT * FROM `gmess_page` RIGHT JOIN `gmess_send_stat` on gmess_page.id = gmess_send_stat.msg_id ORDER BY `gmess_send_stat`.`id` DESC;"));
        foreach ($list as &$l) {
            $l['send_type'] = $sendType[$l['send_type']];
            $l['reach_rate'] = $l['receive_count'] == 0 ? 0 : sprintf('%.2f', ($l['receive_count'] / $l['send_count']) * 100);
            $l['send_date'] = date("n月d日", strtotime($l['send_date']));
            $l['href'] = "http://" . $this->server('HTTP_HOST') . "$config->shoproot?/Gmess/view/id=" . $l['msg_id'];
        }
        $this->assign('list', $list);
        $this->show(self::TPL . 'gmess/gmess_sent.tpl');
    }

    /**
     * 微信消息群发
     */
    public function gmess_send() {
        $this->loadModel('WechatSdk');
        $group = WechatSdk::getUserGroup();
        $this->assign('stoken', $stoken = WechatSdk::getServiceAccessToken());
        $this->assign('userGroup', $group);
        $this->show(self::TPL . 'gmess/gmess_send.tpl');
    }

    /**
     * ajax获取素材列表
     */
    public function ajax_gmess_list() {
        $this->loadModel('mGmess');
        $list = $this->mGmess->getGmessList();
        $this->assign('gmess', $list);
        $this->show(self::TPL . 'gmess/ajax_gmess_list.tpl');
    }

    /**
     * ajax获取已关注会员列表
     * @param type $Query
     */
    public function ajax_gmess_user_list($Query) {
        $Query->way = isset($Query->way) ? $Query->way : 0;
        $this->cacheId = $Query->way;
        if (!$this->isCached()) {
            $this->loadModel('User');
            $this->loadModel('WechatSdk');
            $access_token = WechatSdk::getServiceAccessToken();
            $openIds = WechatSdk::getWechatSubscriberList($access_token);
            $this->assign('count', $openIds['count']);
            $openIds = $openIds['data']['openid'];
            $us = array();
            foreach ($openIds as $openId) {
                $uinfo = $this->User->getUserInfoByOpenId($openId);
                if (!$uinfo) {
                    $us[] = array(
                        'openid' => $openId,
                        'client_name' => '未注册'
                    );
                } else {
                    $us[] = $uinfo;
                }
            }
            $this->assign('list', $us);
        }
        $this->show(self::TPL . 'gmess/ajax_gmess_user_list.tpl');
    }

    /**
     * 编辑素材
     * @param type $Query
     */
    public function gmess_edit($Query) {
        if (isset($Query->id) && is_numeric($Query->id) && $Query->id > 0) {
            $id = intval($Query->id);
            $this->loadModel('mGmess');
            $gmess = $this->mGmess->getGmess($id);
            $this->assign('g', $gmess);
        } else {
            $id = 0;
        }
        $this->assign('ed', $id > 0);
        $this->show(self::TPL . 'gmess/gmess_edit.tpl');
    }

    /**
     * 用户消息列表
     */
    public function customer_messages() {
        $this->loadModel('WechatMessage');
        $msgs = $this->WechatMessage->getSessions();
        $this->assign('msgs', $msgs);
        $this->show(self::TPL . 'customers/customer_messages.tpl');
    }

    /**
     * 消息会话详情
     * @param type $Q
     */
    public function message_session($Q) {
        $openid = $Q->openid;
        $this->cacheId = $openid;
        $this->loadModel('WechatMessage');
        $this->loadModel('User');
        $msgs = $this->WechatMessage->getSession($openid);
        $head = $this->User->getUserHeadByOpenId($openid);
        $this->assign('msgs', $msgs);
        $this->assign('head', $head);
        $this->show(self::TPL . 'customers/message_session.tpl');
    }

    /**
     * iframe 用户列表
     * @param type $Query
     */
    public function iframe_list_customer($Query) {
        $this->assign('gid', $Query->gid);
        $this->show(self::TPL . 'customers/iframe_list_customer.tpl');
    }

    /**
     * 会员列表json数据源
     * @param type $Query
     */
    public function ajax_list_customerData($Query) {
        $this->loadModel('User');
        !isset($Query->gid) && $Query->gid = '';
        $page = !isset($Query->page) ? 0 : intval($Query->page);

        $pageSize = 50;
        $offset = $page * $pageSize;
        // 获取用户列表
        $list = $this->User->getUserList($Query->gid, "$offset, $pageSize");
        $this->echoJson($list);
    }

    /**
     * 会员列表
     */
    public function list_customers() {
        $this->loadModel('UserLevel');
        $group = $this->UserLevel->getList();
        foreach ($group as &$g) {
            // 用户组计数
            $g['count'] = $this->Db->getOne("SELECT COUNT(*) FROM `clients` WHERE `deleted` = 0 AND `client_level` = $g[id];");
        }
        array_unshift($group, array(
            'id' => '',
            'level_name' => '全部用户',
            'count' => $this->Db->getOne("SELECT COUNT(*) FROM `clients` WHERE `deleted` = 0;")
        ));
        $this->assign('group', $group);
        $this->assign('iscom', $this->pCookie('comid') ? 1 : '');
        $this->show(self::TPL . 'customers/list_customers.tpl');
    }

    /**
     * 商品回收站
     */
    public function deleted_products() {
        $this->loadModel('Product');
        $productList = $this->Product->getDeletedProducts(1000, false);
        $this->assign('list', $productList);
        $this->show(self::TPL . 'products/deleted_products.tpl');
    }

    /**
     * 编辑商品规格
     * @param type $Query
     */
    public function alter_product_specs() {
        $this->loadModel('Product');
        $specs = $this->Product->getSpecs(false);
        $this->assign('specs', $specs);
        $this->show(self::TPL . 'products/alter_product_specs.tpl');
    }

    /**
     * 客户订单列表
     * @param type $Query
     */
    public function list_customer_orders($Query) {
        $cid = $Query->id;
        $this->assign('cid', $cid);
        $this->show(self::TPL . 'customers/list_customer_orders.tpl');
    }

    /**
     * 代理列表
     */
    public function list_companys() {
        $this->loadModel('mCompany');
        $companys = $this->mCompany->getCompanys();
        $companyLevel = array('初级', '晋级', '专业');
        foreach ($companys as &$c) {
            $c['fellow_count'] = $this->mCompany->getCompanyFellowsCount($c['id']);
            $c['income_total'] = $this->mCompany->getCompanyIncomeCount($c['id'], false);
            $c['income_month'] = $this->mCompany->getCompanyIncomeCount($c['id'], false, true);
            $c['income_unset'] = $this->mCompany->getCompanyIncomeCount($c['id'], 0, false);
            $c['orderscount'] = $this->Db->getOne("SELECT COUNT(*) FROM `orders` WHERE `company_com` = $c[id];");
            $c['level'] = $companyLevel[intval($c['utype'])];
        }
        $this->assign('companys', $companys);
        $this->show(self::TPL . 'company/list_companys.tpl');
    }

    /**
     * 代理申请列表
     */
    public function company_requests() {
        $this->loadModel('mCompany');
        $companys = $this->mCompany->getCompanys(0);
        $this->assign('companys', $companys);
        $this->show(self::TPL . 'company/company_requests.tpl');
    }

    /**
     * 代理提现
     */
    public function company_withdrawal() {
        $this->loadModel('mCompany');
        $wdList = $this->mCompany->getCompanyCashs();
        $this->assign('wdlist', $wdList);
        $this->show(self::TPL . 'company/company_withdrawal.tpl');
    }

    /**
     * 代理会员列表
     * @param type $Query
     */
    public function list_company_users($Query) {
        $this->cacheId = $Query->id;
        $this->loadModel('mCompany');
        $users = $this->mCompany->getCompanyFellows($Query->id);
        $this->assign('list', $users);
        $this->show(self::TPL . 'company/list_company_users.tpl');
    }

    /**
     * 自动回复设置
     */
    public function settings_autoresponse() {

        $r = $this->Db->query("SELECT * FROM `wechat_autoresponse` ORDER BY `id` DESC;", false);
        $this->assign('rs', $r);
        $this->show(self::TPL . 'settings/autoresponse_edit.tpl');
    }

    /**
     * 编辑自动回复
     * @param type $Q
     */
    public function iframe_alter_autoresponse($Q) {
        $Q->id = intval($Q->id);

        $a = $this->Db->getOneRow("SELECT * FROM `wechat_autoresponse` WHERE `id` = $Q->id;", false);
        if ($a['rel'] > 0) {
            $this->loadModel('mGmess');
            $gmess = $this->mGmess->getGmess($a['rel']);
            $this->assign('g', $gmess);
        }
        $this->assign('a', $a);
        $this->show(self::TPL . 'settings/autoresponse_list.tpl');
    }

    /**
     * 基础设置
     */
    public function settings_base() {
        $this->initSettings(true);
        $this->loadModel('mGmess');
        $this->loadModel('Envs');
        if ($this->settings['welcomegmess'] > 0) {
            $gm = $this->mGmess->getGmess($this->settings['welcomegmess']);
            $this->assign('gm', $gm);
        }
        $envs = $this->Envs->gets();
        $this->assign('envs', $envs);
        $this->show(self::TPL . 'settings/base.tpl');
    }

    /**
     * 自定义菜单设置
     */
    public function settings_menu() {
        $this->loadModel('Product');
        $categorys = $this->Product->getAllCats();
        $this->assign('categorys', $categorys);
        $this->show(self::TPL . 'settings/menu.tpl');
    }

    /**
     * ajax编辑商品规格
     * @param type $Query
     */
    public function ajax_alter_product_spec($Query) {
        $this->loadModel('mProductSpec');

        if (isset($Query->id)) {
            $spec = $this->mProductSpec->getSpecData($Query->id);
            $this->assign('spec', $spec);
            $this->assign('add', false);
        } else {
            $this->assign('add', true);
        }
        $this->show(self::TPL . 'products/ajax_alter_product_spec.tpl');
    }

    public function ajax_alter_product_spec_detail() {
        $this->loadModel('Product');
        $specs = $this->Product->getSpecs();
        $this->assign('specs', $specs);
        $this->show(self::TPL . 'products/ajax_alter_product_spec_detail.tpl');
    }

    public function ajax_alter_product_spec_rep() {
        $this->show(self::TPL . 'products/ajax_alter_product_spec_rep.tpl');
    }

    public function list_company_income($Query) {
        $this->loadModel('mCompany');
        if ($this->pCookie('comid')) {
            $this->assign('iscom', true);
            $comid = $this->Util->digDecrypt($this->pCookie('comid'));
        } else if (isset($Query->id)) {
            $this->assign('iscom', false);
            $comid = intval($Query->id);
        } else {
            return false;
        }
        $companyIncome = $this->mCompany->getCompanyIncome($comid);
        $this->assign('companyIncome', $companyIncome);
        $this->show(self::TPL . 'company/list_company_income.tpl');
    }

    public function alter_company($Query) {
        if ($Query->mod == 'edit') {
            $this->loadModel('mCompany');
            $com = $this->mCompany->getCompanyInfo($Query->id);

            $this->assign('com', $com);
        } else {
            $this->assign('date', date('Y-m-d'));
        }

        $this->assign('mod', $Query->mod);
        $this->show(self::TPL . 'company/alter_company.tpl');
    }

    public function company_bills() {
        $this->loadModel('mCompany');
        $comid = $this->pCookie('comid') ? $this->Util->digDecrypt($this->pCookie('comid')) : false;
        $bills = $this->mCompany->getCompanyBills($comid);
        $this->assign('bills', $bills);
        $this->assign('iscom', $comid ? 1 : '');
        $this->show(self::TPL . 'company/company_bills.tpl');
    }

    public function list_product_toshare() {

        $this->show(self::TPL . 'company_home/list_product_toshare.tpl');
    }

    /**
     * 代理分销记录
     */
    public function list_product_myshare() {
        $comid = $this->pCookie('comid') ? $this->Util->digDecrypt($this->pCookie('comid')) : false;
        if ($comid) {
            $this->cacheId = $comid;
            if (!$this->isCached()) {
                $this->loadModel('mCompany');
                $list = $this->mCompany->getCompanySpreadRecords($comid);
                $this->assign('list', $list);
            }
            $this->show(self::TPL . 'company_home/list_product_myshare.tpl');
        }
    }

    /**
     * 商品推荐二维码
     * @param type $Query
     */
    public function product_share_qrcode($Query) {
        if (is_numeric($Query->id)) {
            $this->cacheId = $Query->id;
            if (!$this->isCached()) {
                $this->loadModel('Product');
                $this->assign('qrcode', 'http://api.ycchen.cc/qrcode/generate/?url=' . urlencode($this->Product->getURI($Query->id, $this->pCookie('comid') ? $this->Util->digDecrypt($this->pCookie('comid')) : false)));
            }
            $this->show(self::TPL . 'company_home/product_share_qrcode.tpl');
        }
    }

    public function company_my_info() {
        $comid = $this->pCookie('comid') ? $this->Util->digDecrypt($this->pCookie('comid')) : false;
        if ($comid) {
            $this->loadModel('WechatSdk');
            $stoken = WechatSdk::getServiceAccessToken();
            $qrcodeImage = WechatSdk::getCQrcodeImage(WechatSdk::getCQrcodeTicket($stoken, $comid));
            $this->assign('comid', $comid);
            $this->assign('qrcode', $qrcodeImage);
            $this->show(self::TPL . 'company_home/company_my_info.tpl');
        }
    }

    public function alter_product_serials() {
        $this->loadModel('Product');
        // product serials
        $serials = $this->Product->getSerials();
        $this->assign('list', $serials);
        $this->show(self::TPL . 'products/alter_product_serials.tpl');
    }

    /**
     * 编辑系列
     * @param type $Query
     */
    public function iframe_alter_serial($Query) {
        $this->loadModel('Product');
        if (isset($Query->id) && is_numeric($Query->id)) {

            $id = intval($Query->id);
            $this->loadModel('Product');
            $serial = $this->Product->getSerialInfo($id);
            $this->assign('serial', $serial);
            $categorys = $this->Product->getAllCats();
            $this->assign('categorys', $categorys);
            $this->show(self::TPL . 'products/iframe_alter_serial.tpl');
        }
    }

    public function list_customer_address($Query) {
        $id = intval($Query->id);
        $this->cacheId = $id;
        if (!$this->isCached()) {
            if ($id > 0) {
                $address = $this->Db->query("select * from orders_address where address IS NOT NULL AND address <> '' AND `client_id` = '$id' GROUP BY address");
                $this->assign('address', $address);
            }
        }
        $this->show(self::TPL . 'customers/list_customer_address.tpl');
    }

    public function orders_history_address() {
        $address = $this->Db->query("select * from orders_address where address IS NOT NULL AND address <> '' GROUP BY address");
        $this->assign('address', $address);
        $this->show(self::TPL . 'orders/orders_history_address.tpl');
    }

    public function iframe_alter_customer($Q) {
        $this->loadModel('mCompany');
        $this->loadModel('WechatSdk');
        $group = WechatSdk::getUserGroup();

        // 获取代理列表
        $coms = $this->mCompany->getCompanysPairs();
        if (isset($Q->id)) {
            $id = intval($Q->id);
            if ($id > 0) {
                $c = $this->Db->getOneRow("SELECT * FROM `clients` WHERE client_id = $id;");
                $this->assign('c', $c);
            }
        } else {
            $id = 0;
        }
        $this->assign('group', $group);
        $this->assign('id', $id);
        $this->assign('lev', $lev = $this->Dao->select()->from(TABLE_USER_LEVEL)->exec());
        $this->assign('coms', $coms);
        $this->show(self::TPL . 'customers/iframe_alter_customer.tpl');
    }

    public function sale_trend() {
        $this->show(self::TPL . 'stat/sale_trend.tpl');
    }

    public function area_ans() {
        $this->show(self::TPL . 'stat/area_ans.tpl');
    }

    public function user_ans() {
        $this->show(self::TPL . 'stat/user_ans.tpl');
    }

    /**
     * 报表中心 - 代理分析页
     */
    public function com_sale() {
        $this->show(self::TPL . 'stat/com_sale.tpl');
    }

    /**
     * 微店总览
     */
    public function overview() {
        $this->Smarty->cache_id = 'stat-overview';
        $this->loadModel('StatOverView');
        // 新增粉丝
        $newFans = $this->Dao->select()->from(TABLE_USER_SUMMARY)->orderby('ref_date')->desc()->limit(1)->getOneRow();
        $this->assign('newFans', $newFans);
        // 粉丝总数
        $totalFans = $this->Dao->select()->from(TABLE_USER_CUMULATE)->orderby('ref_date')->desc()->limit(1)->getOneRow();
        $this->assign('totalFans', $totalFans);
        $this->assign('Datas', $this->StatOverView->getOverViewDatas());
        $this->show(self::TPL . 'stat/overview.tpl');
    }

    public function customer_profile($Q) {
        $this->loadModel('User');
        $id = intval($Q->id);
        if ($id > 0) {
            $c = $this->User->getUserInfoFull($id);
            $this->assign('c', $c);
        }
        $this->show(self::TPL . 'customers/customer_profile.tpl');
    }

    public function alter_brand($Query) {
        $id = intval($Query->id);
        // ch
        if (is_numeric($id)) {
            $this->loadModel('Product');
            $this->loadModel('Brand');
            $cati = $this->Brand->get($id, false);
            $categorys = $this->Product->getAllCats();
            $this->cacheId = $id;
            $this->assign('id', $id);
            $this->assign('cat', $cati);
            $this->assign('categorys', $categorys);
            $this->show(self::TPL . 'products/alter_brand.tpl');
        }
    }

    public function alter_product_brand() {
        $this->show(self::TPL . 'products/alter_product_brand.tpl');
    }

    public function corporation() {
        $comp = $this->Dao->select()->from('enterprise')->exec();
        foreach ($comp as &$com) {
            $com['scount'] = $this->Dao->select('')->count()->from('enterprise_users')->where("eid=" . $com['id'])->getOne();
            $com['scode'] = $this->Util->digEncrypt($com['id']);
            $com['sodcount'] = $this->Dao->select('')->count()->from('orders')->where("enterprise_id=" . $com['id'])->getOne();
            $com['samount'] = $this->Dao->select('')->sum('order_amount')->from('orders')->where("enterprise_id=" . $com['id'])->getOne();
        }
        $this->assign('comp', $comp);
        $this->show(self::TPL . 'corporation/corporation.tpl');
    }

    /**
     * 添加集团
     * @param type $Q
     */
    public function addEnterprise($Q) {
        if (isset($Q->mod)) {
            $ent = $this->Dao->select()->from('enterprise')->where('id=' . $Q->id)->getOneRow();
            $this->assign('ent', $ent);
        }
        $this->show(self::TPL . 'corporation/add.tpl');
    }

    /**
     * Banner 列表
     */
    public function settings_banners() {
        $this->loadModel('Banners');
        $arrPos = array('首页顶部', '首页尾部', '个人中心', '搜索板块', '全站顶部');
        $arrType = array('产品分类', '产品列表', '图文消息', '超链接');
        $banner = $this->Banners->getBanners();
        foreach ($banner as &$ba) {
            $ba['pos'] = $arrPos[$ba['banner_position']];
            $ba['type'] = $arrType[$ba['reltype']];
        }
        $this->assign('banners', $banner);
        $this->show(self::TPL . 'settings/banners.tpl');
    }

    /**
     * 编辑banner
     * @param type $Q
     */
    public function settings_banner_edit($Q) {
        $this->loadModel('Product');
        $this->loadModel('mGmess');

        if (isset($Q->id) && $Q->id > 0) {
            $this->loadModel('Banners');
            $this->Db->cache = false;
            $banner = $this->Banners->getOne($Q->id);

            switch ($banner['reltype']) {
                case 0:
                    // 分类
                    break;
                case 1:
                    // 商品池
                    $this->assign('products', $this->Product->getIn($banner['relid']));
                    break;
                case 2:
                    // 图文消息
                    $this->assign('gm', $this->mGmess->getGmess($banner['relid']));
                    break;
            }

            $this->assign('banner', $banner);
        }

        $categorys = $this->Product->getAllCats();
        $this->assign('gmess', $this->mGmess->getGmessList());
        $this->assign('categorys', $categorys);
        $this->show(self::TPL . 'settings/banner_edit.tpl');
    }

    public function settings_ads() {
        $this->show(self::TPL . 'corporation/add.tpl');
    }

    public function settings_expfee() {

        $datas = $this->Dao->select()->from('wshop_settings_expfee')->exec();
        $this->assign('datas', $datas);
        $this->show(self::TPL . 'settings/expfee.tpl');
    }

    public function user_level() {
        $this->loadModel('UserLevel');
        $this->assign('levels', $this->UserLevel->getList());
        $this->show(self::TPL . 'customers/customer_level.tpl');
    }

    public function ajaxmodlevel($Q) {
        $id = $Q->id;

        if ($id >= 0) {
            $lev = $this->Dao->select()->from(TABLE_USER_LEVEL)->where("id = $id")->getOneRow();
            $this->assign('com', $lev);
        }
        $this->show(self::TPL . 'customers/customer_level_alter.tpl');
    }

    public function settings_alter_envs($Q) {
        $this->loadModel('Envs');
        $id = $Q->id;
        if ($id > 0) {
            $env = $this->Envs->get($id);
            $this->assign('env', $env);
            if ($env['pid'] != '') {
                $this->loadModel('Product');
                $productList = $this->Product->getListByIds($env['pid']);
                $this->assign('products', $productList);
            }
        }
        $this->show(self::TPL . 'settings/alter_envs.tpl');
    }

    public function settings_envs() {
        $this->loadModel('Envs');
        $envs = $this->Envs->gets();
        foreach ($envs as &$en) {
            if ($en['remark'] != '') {
                $en['name'] .= '<span style="color:red">(' . $en['remark'] . ')</span>';
            }
            if ($en['pid'] == '') {
                $en['pid'] = '全品';
            } else {
                $P = explode(',', $en['pid']);
                $en['pid'] = count($P) . '件商品';
            }
        }
        $this->assign('envs', $envs);
        $this->show(self::TPL . 'settings/envs.tpl');
    }

    public function settings_auth() {
        $this->loadModel('Auth');
        $auths = $this->Auth->gets();
        $this->assign('auths', $auths);
        $this->show(self::TPL . 'settings/auth_list.tpl');
    }

    public function auth_edit($Q) {
        if ($Q->id > 0) {
            $this->loadModel('Auth');
            $auth = $this->Auth->get($Q->id);
            $authArr = array();
            foreach (explode(',', $auth['admin_auth']) as $a) {
                $authArr[$a] = 1;
            }
            $auth['arr'] = $authArr;
            $this->assign('auth', $auth);
        }
        $this->show(self::TPL . 'settings/auth_edit.tpl');
    }

    public function user_envsend() {
        $this->loadModel('Envs');
        $this->loadModel('UserLevel');
        $this->loadModel('WechatSdk');
        $group = $this->UserLevel->getList();
        foreach ($group as &$g) {
            // 用户组计数
            $g['count'] = $this->Db->getOne("SELECT COUNT(*) FROM `clients` WHERE `deleted` = 0 AND `client_level` = $g[id];");
        }
        $this->assign('group', $group);
        $envs = $this->Envs->gets();
        $this->assign('envs', $envs);
        $this->show(self::TPL . 'customers/customer_envsend.tpl');
    }

    /**
     * 积分规则设置
     */
    public function settings_sign() {
        $this->initSettings(true);

        $this->show(self::TPL . 'settings/sign.tpl');
    }

    /**
     * 快递公司设置
     */
    public function settings_expcompany() {
        $this->initSettings(true);
        $expressCode = include dirname(__FILE__) . '/../config/express_code.php';
        $this->assign('exps', $expressCode);
        $exps_openid = explode(',', $this->settings['order_express_openid']);
        $noti_openid = explode(',', $this->settings['order_notify_openid']);
        $exps = $this->Dao->select()->from(TABLE_USER)->where("client_wechat_openid in ('" . implode("','", $exps_openid) . "')")->exec();
        $noti = $this->Dao->select()->from(TABLE_USER)->where("client_wechat_openid in ('" . implode("','", $noti_openid) . "')")->exec();
        $this->assign('exps_user', $exps);
        $this->assign('noti_user', $noti);
        $this->show(self::TPL . 'settings/expcompany.tpl');
    }

    /**
     * 编辑首页板块
     * @param type $Q
     */
    public function alter_section($Q) {
        $this->loadModel('Product');

        if ($Q->id > 0) {
            $sec = $this->Dao->select()->from(TABLE_HOME_SECTION)->where("id = $Q->id")->getOneRow(false);
            $this->assign('products', $this->Product->getIn($sec['pid']));
            $this->assign('sec', $sec);
        }
        $this->assign('categorys', $this->Product->getAllCats());
        $this->show(self::TPL . 'settings/alter_section.tpl');
    }

    /**
     * 编辑首页板块列表
     */
    public function settings_section() {

        $this->loadModel('HomeSection');
        $this->assign('section', $this->HomeSection->getAll(false));
        $this->show(self::TPL . 'settings/section.tpl');
    }

    /**
     * 发票设置
     */
    public function settings_reci() {
        $this->initSettings(true);
        $this->show(self::TPL . 'settings/reci.tpl');
    }

    /**
     * 抢红包设置
     * @param type $Q
     */
    public function settings_envs_robb($Q) {
        $this->cacheId = $Q->id;
        $this->loadModel('Envs');
        if (isset($Q->id)) {
            $id = $Q->id;
            $envs = $this->Envs->getRob($id);
            $this->assign('env', $envs);
        } else {
            
        }
        $this->loadModel('Envs');
        $this->assign('envs', $this->Envs->gets());
        $this->show(self::TPL . 'settings/envs_robb.tpl');
    }

    /**
     * 红包抢购
     */
    public function envsRobList() {

        $this->loadModel('Envs');
        $envs = $this->Envs->getRobList();
        foreach ($envs as &$env) {
            $env['env'] = $this->Envs->get($env['envsid']);
            $env['invo'] = $this->Db->getOne("SELECT COUNT(*) FROM `envs_robrecord` WHERE `eid` = $env[id];");
        }
        $this->assign('envs', $envs);
        $this->show(self::TPL . 'settings/envs_robb_list.tpl');
    }

    /**
     * 团购活动
     */
    public function settings_group() {

        if (!$this->isCached()) {
            $this->Load->model('GroupBuying');
            $this->assign('list', $this->GroupBuying->getList());
        }

        $this->show(self::TPL . 'settings/group_buy.tpl');
    }

    /**
     * 素材分类
     */
    public function gmess_category() {
        $this->show(self::TPL . 'gmess/gmess_category.tpl');
    }

    /**
     * 商户列表
     */
    public function suppliers_list() {
        $this->loadModel('Supplier');

        $suppilers = $this->Supplier->getList();
        foreach ($suppilers as &$supp) {
            $supp['pdcount'] = $this->Supplier->getSuppProductCount($supp['id']);
        }
        $this->assign('suppilers', $suppilers);
        $this->show(self::TPL . 'suppliers/suppliers_list.tpl');
    }

    /**
     * 积分兑换设置
     */
    public function credit_exchange() {
        $this->loadModel('CreditExchange');
        $list = $this->CreditExchange->getList(false);
        $this->assign('list', $list);
        $this->show(self::TPL . 'credit/credit_exchange.tpl');
    }

}
