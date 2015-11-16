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
class vProduct extends Controller {

    /**
     * 商品列表显示数量
     */
    const LIST_LIMIT = 10;

    /**
     * 构造函数
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('Product');
    }

    /**
     * 查看商品详情
     * @param type $Query
     */
    public function view($Query) {

        $this->loadModel('mCompany');
        $this->loadModel('User');

        $openid = $this->getOpenId();
        $isSubscribed = $this->User->isSubscribed();
        // 推荐com，90分钟
        if (!$this->pCookie('com') && isset($Query->com)) {
            $this->sCookie('com', $Query->com, 7200);
        }
        $Query->id = intval($Query->id);
        // 缓存关联
        $this->cacheId = $Query->id . ($isSubscribed ? 1 : 0) . 'x';
        $this->Smarty->cache_lifetime = 300;

        if (!$this->isCached($Query->id)) {

            $this->loadModel('Envs');
            $this->loadModel('User');
            $this->loadModel('UserLevel');
            $this->loadModel('Supplier');
            $this->loadModel('JsSdk');

            // 产品图片
            $productInfo = $this->Product->getProductInfo($Query->id);
            // 加入产品首图到图片列表
            array_unshift($productInfo['images'], array('image_path' => $productInfo['catimg']));
            // 随机product推荐
            $sList = $this->Product->randomGetProducts($productInfo['product_cat'], $Query->id, 6);

            // 促销判断
            if (strtotime($productInfo['product_prom_limitdate']) < $this->now) {
                $productInfo['product_prom'] = 0;
            }

            // 红包信息
            $promInfo = $this->Envs->getPdEnvs($Query->id, 1);

            // 获取价格表
            $specs = $this->Product->getProductSpecs($Query->id);
            // 获取价格表
            $specsDistinct = $this->Product->getProductSpecsDistinct($Query->id);

            // 获取会员折扣
            $discount = $this->User->getDiscount($this->getUid());

            // 获取会员等级
            $level = $this->UserLevel->get($level['client_level']);

            // 获取商户信息
            $supplier = $this->Supplier->get($productInfo['product_supplier']);

            if (Controller::inWechat()) {
                $signPackage = $this->JsSdk->GetSignPackage();
                $this->assign('signPackage', $signPackage);
            }

            $this->assign('supplier', $supplier);
            $this->assign('level', $level);
            $this->assign('discount', $discount);
            $this->assign('root', $this->root);
            $this->assign('specs', $specs);
            $this->assign('specsDistinct', $specsDistinct);
            $this->assign('slist', $sList);
            $this->assign('images', $productInfo['images']);
            $this->assign('images_count', count($productInfo['images']));
            $this->assign('productInfo', $productInfo);
            $this->assign('productid', $Query->id);
            $this->assign('title', $productInfo['product_name']);
            $this->assign('prominfo', $promInfo[0]);
            $this->assign('isSubscribed', $isSubscribed);
        }

        // 判断代理信息
        if (isset($Query->com)) {
            $this->mCompany->updateCompanySpread($Query->id, $Query->com);
        }
        // 代理处理
        if ($openid && $openid != '' && !$this->pCookie('comid')) {
            $comid = $this->mCompany->getCompanyIdByOpenId($openid);
            if ($comid > 0) {
                $this->sCookie('comid', $comid, 72000);
            }
        }

        // 增加点击数
        $this->Product->upReadi($Query->id);

        $this->show();
    }

    /**
     * 收藏商品
     */
    public function ajaxAlterProductLike() {
        $this->loadModel('User');
        $openid = $this->getOpenId();
        $id = $this->post('id');
        if ($id > 0 && $openid != '') {
            // add
            echo $this->User->addUserLike($openid, $id);
        } else if ($id < 0 && $openid != '') {
            // delete
            $id = abs($id);
            echo $this->User->deleteUserLike($openid, $id);
        } else {
            echo 0;
        }
    }

    /**
     * 收藏商品检查
     * @param type $Query
     */
    public function checkLike($Query) {
        $id = $Query->id;
        if ($id > 0) {
            $openid = $this->getOpenId();
            $isLiked = $this->Dao->select()->from('client_product_likes')
                            ->where(['openid' => $openid, 'product_id' => $id])->limit(1)->getOne(false);
            if (!empty($openid) && $isLiked > 0) {
                $this->echoMsg(0, 1);
            } else {
                $this->echoMsg(-1, 0);
            }
        } else {
            $this->echoMsg(-1);
        }
    }

    /**
     * 查看产品分类介绍
     * @param type $Query
     */
    public final function view_category($Query) {

        !isset($Query->cat) && $Query->cat = 0;

        $countSubcat = intval($this->Dao->select('')->count('*')->from('product_category')->where("cat_parent=$Query->cat")->getOne());

        if ($countSubcat === 0 && $Query->cat > 0) {
            $this->redirect("?/vProduct/view_list/cat=$Query->cat");
        }

        $this->cacheId = $Query->cat;

        // 缓存
        if (!$this->isCached()) {

            $topCats = $this->Product->getCatList(0);
            $secCats = array();
            foreach ($topCats as $tc) {
                $secCats = array_merge($secCats, $this->Product->getCatList($tc['cat_id']));
            }

            $catInfo = $this->Product->getCatInfo($Query->cat);
            $subCatInfo = $this->Product->getCatList($catInfo['cat_id']);
            $this->assign('topcat', $topCats);
            $this->assign('subcat', $subCatInfo);
            $this->assign('title', '产品搜索');
            $this->assign('cat', $Query->cat);
            $this->assign('serial_id', $Query->serial_id);
            $this->assign('cat_descs', $catInfo['cat_descs']);
        }

        $this->show();
    }

    /**
     * ajax获取分类列表视图
     * @param type $Query
     */
    public final function ajaxCatList($Query) {
        if (isset($Query->id)) {
            $this->cacheId = intval($Query->id);
            if ($this->cacheId == -1) {
                // 一周新品
                $products = $this->Product->getNewEst();
                $this->assign('products', $products);
                $this->show('./vproduct/ajax_newproduct.tpl');
            } else if ($this->cacheId == -2) {
                // 一周热搜
                $products = $this->Product->getHotEst();
                $this->assign('products', $products);
                $this->show('./vproduct/ajax_hotproduct.tpl');
            } else if ($this->cacheId == -3) {
                $this->loadModel('Brand');
                // 品牌列表
                $brands = $this->Brand->getList();
                $this->assign('brands', $brands);
                $this->show('./vproduct/ajax_brandslist.tpl');
            } else if ($this->cacheId >= 0) {
                $this->loadModel('Brand');
                $subCatInfo = $this->Product->getCatList($this->cacheId);
                if (sizeof($subCatInfo) > 0) {
                    // 如果分类下面有子分类
                    $brands = $this->Brand->getCatBrand($this->cacheId);
                    foreach ($subCatInfo as &$s) {
                        $childrens = $this->Product->getCatList($s['cat_id']);
                        if (count($childrens) > 0) {
                            $s['child'] = $childrens;
                        } else {
                            $s['child'] = false;
                        }
                    }
                    $this->assign('subcat', $subCatInfo);
                    $this->assign('brands', $brands);
                    $this->show();
                } else {
                    $this->assign('products', $this->Product->getNewEst($this->cacheId));
                    $this->show('./vproduct/ajax_hotproduct.tpl');
                    // 分类下面无子分类
                }
            }
        }
    }

    /**
     * 商品列表
     * @param type $Query
     */
    public function view_list($Query) {
        $this->getOpenId();

        !isset($Query->brand) && $Query->brand = 0;
        !isset($Query->page) && $Query->page = 0;
        !isset($Query->searchkey) && $Query->searchkey = '';
        !isset($Query->serial) && $Query->serial = false;
        !isset($Query->cat) && $Query->cat = false;
        !isset($Query->orderby) && $Query->orderby = "";
        !isset($Query->level) && $Query->orderby = false;
        $Query->searchkey = urldecode($Query->searchkey);

        $this->cacheId = md5(serialize($Query));

        // 推荐com，90分钟
        if (!isset($this->pGet['com']) && isset($Query->com)) {
            setcookie("com", $Query->com, time() + 5400);
        }

        if (!$this->isCached()) {
            // params
            if ($Query->searchkey != '') {
                $catInfo = array(
                    'cat_id' => $Query->cat,
                    'cat_name' => $Query->searchkey . ' 的搜索结果'
                );
            } else if ($Query->serial) {
                $serialInfo = $this->Product->getSerialInfo($Query->serial);
                $catInfo = array(
                    'cat_name' => $serialInfo['serial_name']
                );
            } else if ($Query->brand) {
                $catInfo = array(
                    'cat_name' => $this->Db->getOne("SELECT `brand_name` FROM `product_brand` WHERE `id` = $Query->brand;")
                );
            } else if ($Query->cat) {
                $catInfo = $this->Product->getCatInfo($Query->cat);
            } else {
                $catInfo = array(
                    'cat_name' => '商品列表'
                );
            }

            $this->assign('brand', $Query->brand);
            $this->assign('serial', $Query->serial);
            $this->assign('query', (array) $Query);
            $this->assign('searchkey', $Query->searchkey);
            $this->assign('cat', $Query->cat);
            $this->assign('level', $Query->level);
            $this->assign('catInfo', $catInfo);
            $this->assign('orderby', $Query->orderby);
            $this->assign('title', $catInfo['cat_name']);
        }

        $this->show();
    }

    /**
     * Ajax返回商品列表 分页
     * @todo cat
     * @param type $Query
     */
    public function ajaxProductList($Query) {
        // 商品系列
        !isset($Query->serial) && $Query->serial = false;
        // 分页号
        !isset($Query->page) && $Query->page = 0;
        // 商品分类
        !isset($Query->cat) && $Query->cat = 1;
        // 列表宫格样式标记
        $this->assign('stype', $Query->stype);
        // 特殊分页标记
        if ($Query->page != 0) {
            $pdlists1 = $this->pCookie('pdlist-serial');
            $pdlists2 = $this->pCookie('pdlist-start');
        } else {
            $pdlists1 = false;
            $pdlists2 = 0;
        }
        // 排序
        if (!isset($Query->orderby) || $Query->orderby == "") {
            $Query->orderby = '`product_cat` ASC';
        } else {
            $Query->orderby = trim(urldecode($Query->orderby));
        }
        // 缓存id
        $this->cacheId = md5(serialize($Query)) . $pdlists1 . '-' . $pdlists2;
        // 缓存文件判断
        if ($Query->serial) {
            // 系列产品列表
            $tpl = 'vproduct/ajaxproductlist_serials.tpl';
        } else {
            $tpl = 'vproduct/ajaxproductlist.tpl';
        }
        // 数据处理
        if (!$this->isCached()) {
            if ($Query->serial) {
                // 系列展示列表
                if (is_numeric($Query->serial)) {

                    $_categorys = $this->Product->getCategoryByLevel($Query->level, $Query->cat);

                    $categorys = array();
                    foreach ($_categorys as $ca) {
                        $categorys[$ca['cat_id']] = array(
                            'cat_image' => $ca['cat_image'],
                            'cat_name' => $ca['cat_name'],
                            'cat_id' => $ca['cat_id'],
                            'pd' => array()
                        );
                    }

                    $pds = $this->Dao->select("po.*,ps.sale_prices,psl.serial_name,pca.cat_parent,(SELECT SUM(product_count) FROM `orders_detail` WHERE `orders_detail`.product_id = `po`.product_id) AS sale_count")
                            ->from(TABLE_PRODUCTS)->alias('po')
                            ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('ps')->on('ps.product_id=po.product_id')
                            ->leftJoin(TABLE_PRODUCT_SERIALS)->alias('psl')->on('psl.id = po.product_serial')
                            ->leftJoin(TABLE_PRODUCT_CATEGORY)->alias('pca')->on('pca.cat_id = po.product_cat')
                            ->where('`is_delete` <> 1')
                            ->aw('`product_online` = 1')
                            ->aw('`product_serial` = ' . $Query->serial)
                            ->aw(isset($Query->searchKey) && $Query->searchKey != '' ? "`product_name` LIKE '%%$Query->searchKey%%'" : '')
                            ->orderBy($Query->orderby)
                            ->limit("$pdlists2,1000")
                            ->exec();

                    // 已加载商品列表数量
                    $pdLoaded = count($pds);

                    foreach ($pds as $pd) {
                        if (!array_key_exists($pd['product_cat'], $categorys)) {
                            // level catid 转换
                            $catId = $this->Product->getCatIdUtilLevel($pd['product_cat'], $Query->level);
                        } else {
                            $catId = $pd['product_cat'];
                        }
                        $categorys[$catId]['pd'][] = $pd;
                    }

                    $this->sCookie('pdlist-start', $pdlists2 + $pdLoaded);
                    $this->assign('pdloaded', $pdLoaded);
                    $this->assign('categorys', $categorys);
                    unset($pds);
                    unset($_categorys);
                }
            } else {
                // 搜索展示列表
                $pdLoaded = 0;
                $limit = 10;
                // 获取所有系列
                $serials = $this->Product->getSerials($pdlists1);
                $serialsCount = count($serials) - 1;
                if (isset($Query->searchKey) && $Query->searchKey != '') {
                    $Query->searchKey = urldecode($Query->searchKey);
                }
                foreach ($serials as $index => &$seri) {
                    $seri['s'] = $index == 0 && $Query->page != 0;
                    // 商品列表
                    $seri['pd'] = $this->Dao->select('po.*,ps.sale_prices,psl.serial_name')
                            ->from(TABLE_PRODUCTS)->alias('po')
                            ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('ps')->on('ps.product_id=po.product_id')
                            ->leftJoin(TABLE_PRODUCT_SERIALS)->alias('psl')->on('psl.id = po.product_serial')
                            ->where('`is_delete` <> 1')
                            ->aw('`product_online` = 1')
                            ->aw(intval($Query->cat) > 0 ? "`product_cat` = $Query->cat" : '')
                            ->aw('`product_serial` =' . $seri['id'])
                            ->aw(isset($Query->searchKey) && $Query->searchKey != '' ? "`product_name` LIKE '%%$Query->searchKey%%'" : '')
                            ->aw(isset($Query->in) && $Query->in != '' ? "po.`product_id` IN ($Query->in)" : '')
                            ->orderBy($Query->orderby)
                            ->limit("$pdlists2,1000")
                            ->exec();
                    // 商品计数
                    $seri['pdCount'] = count($seri['pd']);
                    $pdLoaded += $seri['pdCount'];
                    $limit -= $seri['pdCount'];
                    if ($limit <= 0 || $index == $serialsCount) {
                        $this->sCookie('pdlist-serial', $seri['sort']);
                        if ($seri['sort'] == $pdlists1) {
                            $this->sCookie('pdlist-start', $pdlists2 + $seri['pdCount']);
                        } else {
                            $this->sCookie('pdlist-start', $seri['pdCount']);
                        }
                        $serials = array_slice($serials, 0, $index + 1);
                        break;
                    }
                    $pdlists2 = 0;
                }
                // echo $pdLoaded;
                $this->assign('pdloaded', $pdLoaded);
            }
            $this->assign('serials', $serials);
        }

        // final show
        $this->show($tpl);
    }

    /**
     * 购物车数据
     * @return HTML STRING
     * @todo json
     */
    public function cartData() {
        $this->loadModel('User');
        $this->loadModel('Envs');
        $this->Smarty->caching = false;
        // $openid = $this->getOpenId();
        $data = json_decode($_POST['data'], true);
        $discount = $this->User->getDiscount($this->getUid());
        $pdList = array();
        foreach ($data as $key => $count) {
            preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
            // product id
            $pid = intval($matchs[1]);
            // price hash id
            $spid = intval($matchs[2]);
            // product info
            if ($pid > 0) {
                $pinfo = $this->Product->getProductInfoWithSpec($pid, $spid);
                if ($pinfo === NULL) {
                    continue;
                } else {
                    if ($pinfo['product_prom'] == 1 && time() < strtotime($pinfo['product_prom_limitdate'])) {
                        $pinfo['sale_prices'] *= ($pinfo['product_prom_discount'] / 100);
                    } else {
                        if ($discount < 1) {
                            // 获取会员折扣
                            $pinfo['sale_prices'] *= $discount;
                        }
                    }
                    // product count
                    $pinfo['count'] = intval($count);
                    $pinfo['envs'] = $this->Envs->getPdEnvsJoinStr($pid);
                    $pinfo['spid'] = $spid;
                    $pdList[] = $pinfo;
                }
            } else {
                continue;
            }
        }
        $this->assign('product_list', $pdList);
        $this->show();
    }

    /**
     * 上传产品图片
     * ImageUpload
     */
    public function ImageUpload() {
        global $config;
        $this->loadModel('ImageUploader');
        $this->ImageUploader->dir = $config->productPicRootTmp;
        $targetFileName = $this->ImageUploader->upload();
        $arr = array(
            "s" => $targetFileName !== false,
            "pic" => $config->productPicRootTmp . $targetFileName,
            "imgn" => $targetFileName,
            "link" => $config->productPicLinkTmp . $targetFileName
        );
        $this->echoJson($arr);
    }

    /**
     * 获取分类列表
     * @param type $Query
     */
    public function ajaxGetCategroys() {
        $this->loadModel('SqlCached');
        // file cached
        $cacheKey = 'ajaxGetCategroys1';
        $fileCache = new SqlCached();
        $ret = $fileCache->get($cacheKey);
        if (-1 === $ret) {
            $cats = $this->Product->getAllCats();
            array_unshift($cats, array('name' => '未分类', 'dataId' => 0, 'hasChildren' => false, 'children' => array(), 'open' => 'true'));
            $catJson = $this->toJson($cats);
            $fileCache->set($cacheKey, $catJson);
            echo $catJson;
        } else {
            echo $ret;
        }
    }

    /**
     * 添加分类
     * @param type $catname
     * @param type $pid
     */
    public function ajaxAddCategroy() {
        $catname = trim($this->post('catname'));
        $pid = intval($this->post('pid'));
        echo $this->Db->query("INSERT INTO `product_category` (cat_name,cat_parent) VALUES ('$catname',$pid);");
    }

    /**
     * 删除分类
     * @param type $catname
     * @param type $pid
     */
    public function ajaxDelCategroy() {
        $id = $this->post('id');
        echo $this->Db->query("DELETE FROM `product_category` WHERE cat_id = $id;");
    }

    public function ajaxAlterSerial() {
        $id = intval($this->post('id'));
        if ($id > 0) {
            $set = array();
            $data = $this->post('data');
            foreach ($data as &$d) {
                $set[] = "`$d[name]` = '$d[value]'";
            }
            $set = implode(',', $set);
            $sql = "UPDATE `product_serials` SET $set WHERE `id` = $id";
            echo $this->Db->query($sql);
        } else {
            // add
            $name = $this->pPost('name');
            $ret = $this->Db->query("INSERT INTO `product_serials` (`serial_name`) VALUES ('$name');");
            if ($ret !== false) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }

    public function ajaxDeleteSerial() {
        $id = intval($this->post('id'));
        if ($id > 0) {
            echo $this->Db->query("UPDATE `product_serials` SET `deleted` = 1 WHERE `id` = $id");
        }
    }

    /**
     * ajax递增商品分享数量
     * @param type $Query
     */
    public function ajaxUpProductShare($Query) {
        if ($Query->id > 0) {
            $this->Db->query("UPDATE `products_info` SET `product_sharei` = `product_sharei` + 1 WHERE `product_id` = $Query->id;");
        }
    }

    /**
     * 同级分类推荐
     * @param type $Q
     */
    public function categorySugg($Q) {
        if (isset($Q->id)) {
            $cat = intval($Q->id);
            $parentCat = $this->Dao->select('cat_parent')->from(TABLE_PRODUCT_CATEGORY)->where('cat_id=' . $cat)->getOne();
            $catS = $this->Dao->select()->from(TABLE_PRODUCT_CATEGORY)->where("cat_parent=$parentCat")->aw("cat_id <> $cat")->limit(6)->exec();
            $this->assign('cats', $catS);
            $this->show();
        }
    }

}
