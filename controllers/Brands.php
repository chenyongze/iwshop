<?php

/**
 * 品牌控制器
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class Brands extends Controller {

    private $table = '`product_brand`';

    public function vBrand($Q) {
        $id = intval($Q->id);
        $this->loadModel('Brand');
        $this->cacheId = $id;
        $brand = $this->Brand->get($id);
        $pds = $this->Dao->select("po.*,ps.sale_prices,psl.serial_name,pca.cat_parent,(SELECT SUM(product_count) FROM `orders_detail` WHERE `orders_detail`.product_id = `po`.product_id) AS sale_count")
                ->from(TABLE_PRODUCTS)->alias('po')
                ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('ps')->on('ps.product_id=po.product_id')
                ->leftJoin(TABLE_PRODUCT_SERIALS)->alias('psl')->on('psl.id = po.product_serial')
                ->leftJoin(TABLE_PRODUCT_CATEGORY)->alias('pca')->on('pca.cat_id = po.product_cat')
                ->where('`delete` <> 1')
                ->aw('`product_brand` = ' . $id)
                ->orderBy('`product_id`')->desc()
                ->exec();

        // 已加载商品列表数量
        $this->Smarty->assign('pds', $pds);
        $this->Smarty->assign('title', $brand['brand_name']);
        $this->show();
    }

    public function create() {
        $name = $this->pPost('brand_name');
        $cat = $this->pPost('brand_cat');
        echo $this->Db->query("INSERT INTO $this->table (`brand_name`,`brand_cat`) VALUE ('$name','$cat');");
    }

    /**
     * 更新品牌数据
     */
    public function set() {
        global $config;
        // update
        $id = intval($this->post('id'));
        $imgPath = false;
        $imgName = false;
        if ($id > 0) {
            $set = array();
            $gid = false;
            $data = $this->post('data');
            foreach ($data as &$d) {
                if ($d['name'] == 'brand_img2') {
                    $imgName = $d['value'];
                    $imgPath = dirname(__FILE__) . '/../uploads/brands/' . $imgName;
                }
                $set[] = "`$d[name]` = '$d[value]'";
            }
            $set = implode(',', $set);
            $sql = "UPDATE $this->table SET $set WHERE `id` = $id";
            if ($this->Db->query($sql)) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    /**
     * 删除品牌
     */
    public function del() {
        $id = intval($this->pPost('id'));
        if (is_numeric($id)) {
            echo $this->Db->query("UPDATE $this->table SET `deleted` = 1 WHERE `id` = $id;");
        } else {
            echo 0;
        }
    }

    public function gets() {
        $this->loadModel('SqlCached');
        // file cached
        $cacheKey = 'ajaxGetBrands';
        $fileCache = new SqlCached();
        $ret = $fileCache->get($cacheKey);
        if (-1 === $ret) {
            $this->loadModel('Product');
            $cats = $this->toJson($this->Product->getAllBrands());
            $fileCache->set($cacheKey, $cats);
            echo $cats;
        } else {
            echo $ret;
        }
    }

    /**
     * banner 图片上传
     * @global type $config
     */
    public function uploadImage() {
        $this->loadModel('ImageUploader');
        $this->ImageUploader->dir = dirname(__FILE__) . '/../uploads/brands/';
        $ret = $this->ImageUploader->upload();
        if ($ret !== false) {
            $this->echoJson(array('s' => 1, 'img' => $ret));
        } else {
            $this->echoJson(array('s' => 0));
        }
    }

}
