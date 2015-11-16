<?php

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class wProduct extends Controller {

    /**
     * 权限检查
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('Session');
        $this->Session->start();
        if ($this->Session->get('loginKey') === false) {
            $this->redirect('?/Wdmin/logOut');
        }
    }

    /**
     * 获取产品库存列表Json
     */
    public function ajax_product_list_stock() {
        $page = $this->pPost('page', 0);
        $page_size = $this->pPost('page_size', 15);
        $offset = $page * $page_size;
        $product_cat = $this->pPost('product_cat', 0);
        $order = $this->pPost('order', 'po.product_id DESC');
        $key = $this->pPost('key', '');
        if ($product_cat > 0) {
            $pds = $this->Dao->select("po.product_id,po.product_name,po.product_code")
                    ->from(TABLE_PRODUCTS)->alias('po')
                    ->where('po.`is_delete` <> 1')
                    ->aw(!empty($key) ? "`product_name` LIKE '%%$key%%'" : '')
                    ->aw("`product_cat` = $product_cat")
                    ->orderBy($order)
                    ->limit("$offset, $page_size")
                    ->exec();
            foreach ($pds as &$pd) {
                // 图片转换
                $pd['catimg'] = $this->productImageConv($pd['catimg']);
                $pd['specs'] = $this->Dao->select()->from(TABLE_PRODUCT_SPEC)->where('product_id=' . $pd['product_id'])->exec();
                $pd['product_stock'] = 0;
                foreach ($pd['specs'] as $spec) {
                    $pd['spec_count'] ++;
                    $pd['product_stock'] += $spec['instock'];
                }
            }
            $this->echoMsg(0, array(
                'data' => $pds,
                'count' => sizeof($pds)
            ));
        } else {
            $this->echoMsg(-1);
        }
    }

    /**
     * 获取产品列表Json
     */
    public function ajax_product_list() {
        $page = $this->pPost('page', 0);
        $page_size = $this->pPost('page_size', 15);
        $offset = $page * $page_size;
        $product_cat = $this->pPost('product_cat', 0);
        $order = $this->pPost('order', 'po.product_id DESC');
        $key = $this->pPost('key', '');
        if ($product_cat > 0) {
            $pds = $this->Dao->select("po.catimg,po.product_id,po.product_name,po.product_online,po.product_code,po.product_unit,po.product_readi,ps.sale_prices,psl.serial_name,pca.cat_parent")
                    ->from(TABLE_PRODUCTS)->alias('po')
                    ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('ps')->on('ps.product_id=po.product_id')
                    ->leftJoin(TABLE_PRODUCT_SERIALS)->alias('psl')->on('psl.id = po.product_serial')
                    ->leftJoin(TABLE_PRODUCT_CATEGORY)->alias('pca')->on('pca.cat_id = po.product_cat')
                    ->where('`is_delete` <> 1')
                    ->aw(!empty($key) ? "`product_name` LIKE '%%$key%%'" : '')
                    ->aw("`product_cat` = $product_cat")
                    ->orderBy($order)
                    ->limit("$offset, $page_size")
                    ->exec();
            // 算总数
            $tcount = $this->Dao->select("")->count()
                    ->from(TABLE_PRODUCTS)->alias('po')
                    ->where('`is_delete` <> 1')
                    ->aw(!empty($key) ? "`product_name` LIKE '%%$key%%'" : '')
                    ->aw("`product_cat` = $product_cat")
                    ->getOne();
            foreach ($pds as &$pd) {
                $pd['catimg'] = $this->productImageConv($pd['catimg']);
            }
            $this->echoMsg(0, array(
                'data' => $pds,
                'count' => intval($tcount)
            ));
        } else {
            $this->echoMsg(-1);
        }
    }

    /**
     * 商品图片转换
     * @param type $catimg
     * @param type $x
     * @param type $y
     */
    private function productImageConv($catimg, $x = 50, $y = 50) {
        global $config;
        if (empty($catimg)) {
            $catimg = 'static/images/icon/iconfont-pic.png';
        } else {
            $catimg = "static/Thumbnail/?w=$x&h=$y&p=" . $config->productPicLink . $catimg;
        }
        return $catimg;
    }

    /**
     * 商品上下架
     */
    public function switchOnline() {
        $productId = $this->pPost('productId');
        $isOnline = $this->pPost('isOnline');
        echo $this->Dao->update(TABLE_PRODUCTS)->set(array('product_online' => $isOnline))->where('product_id=' . $productId)->exec();
    }

    /**
     * ajax获取商品分类列表树
     * @param type $Query
     */
    public function ajaxGetCategroys() {
        $this->loadModel('Product');
        $cats = $this->Product->getAllCats();
        $cats[] = array('name' => '未分类', 'dataId' => 0, 'hasChildren' => false, 'children' => array(), 'open' => 'true');
        echo $this->toJson($cats);
    }

    /**
     * ajax编辑分类信息
     */
    public function ajaxAlterCategroy() {
        $id = intval($this->post('id'));
        if ($id > 0) {
            $this->loadModel('Product');
            $catinfo = $this->Product->getCatInfo($id);
            if ($id > 0) {
                $set = array();
                $data = $this->post('data');
                foreach ($data as &$d) {
                    if ($d['name'] == 'cat_image' && is_file(dirname(__FILE__) . '/../../uploads/banner_tmp/' . $d['value'])) {
                        if (is_file(dirname(__FILE__) . '/../../' . $catinfo['cat_image'])) {
                            @unlink(dirname(__FILE__) . '/../../' . $catinfo['cat_image']);
                        }
                        @rename(dirname(__FILE__) . '/../../uploads/banner_tmp/' . $d['value'], dirname(__FILE__) . '/../../uploads/banner/' . $d['value']);
                        $d['value'] = 'uploads/banner/' . $d['value'];
                    }
                    $set[] = "`$d[name]` = '$d[value]'";
                }
                $set = implode(',', $set);
                $sql = "UPDATE `product_category` SET $set WHERE `cat_id` = $id";
                echo $this->Db->query($sql);
            }
        }
    }

    /**
     * 删除分类
     * @param type $catname
     * @param type $pid
     */
    public function ajaxDelCategroy() {
        $id = $this->post('id');
        $this->loadModel('Product');
        echo $this->delCategroy($id);
    }

    /**
     * 递归删除分类
     * @param type $id
     * @return boolean
     */
    private function delCategroy($id) {
        $catinfo = $this->Product->getCatInfo($id);
        if ($catinfo['cat_id'] > 0) {
            if (is_file(dirname(__FILE__) . '/../../' . $catinfo['cat_image'])) {
                @unlink(dirname(__FILE__) . '/../../' . $catinfo['cat_image']);
            }
            // 下移分类商品
            $this->Dao->update(TABLE_PRODUCTS)->set(array('product_cat' => 0))->where("product_cat = $id")->exec();
            // 下级分类
            $subcat = $this->Dao->select('cat_id')->from(TABLE_PRODUCT_CATEGORY)->where("cat_parent = $id")->exec();
            // 删除分类
            $this->Db->query("DELETE FROM `product_category` WHERE cat_id = $id;");
            foreach ($subcat as $cat) {
                $this->delCategroy($cat['cat_id']);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 重新生成商品静态描述
     */
    public function generateStaticDesc() {
        $dir = dirname(__FILE__) . '/../../html/products/';
        if (is_writable($dir)) {
            $products = $this->Dao->select('product_id,product_desc')->from(TABLE_PRODUCTS)->exec();
            foreach ($products as $product) {
                file_put_contents(dirname(__FILE__) . '/../../html/products/' . $product['product_id'] . '.html', $product['product_desc']);
            }
            $this->echoMsg(0);
        } else {
            $this->echoMsg(-1);
        }
    }

    /**
     * 还原已删除商品
     */
    public function productReverse() {
        if (is_numeric($this->post('pid'))) {
            $pid = intval($this->pPost('pid'));
            if ($pid > 0) {
                echo $this->Db->query("UPDATE `products_info` SET `is_delete` = 0 WHERE product_id = $pid;");
            } else if ($pid == 0) {
                // 全部还原
                echo $this->Db->query("UPDATE `products_info` SET `is_delete` = 0 WHERE `is_delete` = 1;");
            }
        } else {
            echo 0;
        }
    }

    /**
     * 清空已删除商品
     */
    public function removeProduct() {
        if (is_numeric($this->post('pid'))) {
            $pid = intval($this->pPost('pid'));
            $this->loadModel('Product');
            if ($pid == 0) {
                $products = $this->Dao->select('product_id')->from(TABLE_PRODUCTS)->where('`is_delete` = 1')->exec();
                foreach ($products as $pd) {
                    $this->Product->removeProduct($pd['product_id']);
                }
                echo count($products);
            } else {
                echo $this->Product->removeProduct($pid);
            }
        } else {
            echo 0;
        }
    }

}
