<?php

if (!defined('APP_PATH')) {
    exit(0);
}

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http=>//www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http=>//www.iwshop.cn
 */
class Test extends Controller {

    public function buildCatSearch() {
        $this->loadModel('Product');
        $pds = $this->Dao->select('product_id,product_cat')->from(TABLE_PRODUCTS)->exec();
        foreach ($pds as $pd) {
            echo $this->Product->buildCatSearch($pd['product_id'], $pd['product_cat']);
        }
    }

    /**
     * 反射方法
     */
    public function index() {
        $class = new ReflectionClass('Test');
        $instance = $class->newInstanceArgs();
        $methods = $class->getMethods();
        foreach ($methods as $m) {
            echo '<a href="?/Test/' . $m->getName() . '">' . $m->getName() . '</m>' . "<br />";
        }
    }

    /**
     * 测试订单通知
     */
    public function notify() {
        # $this->loadModel('mOrder');
        # var_dump($this->mOrder->comNewOrderNotify(218));
        # var_dump($this->mOrder->userNewOrderNotify(218, 'o_JvCuFQoYqbwIWOSPnrDkRP6Wrg'));
    }

    /**
     * 生成订单数据
     */
//    public function genOrderData() {
//
//        $this->Db->query('TRUNCATE TABLE orders;');
//
//        $this->Db->query('TRUNCATE TABLE orders_detail;');
//
//        $this->Db->query('TRUNCATE TABLE orders_address;');
//
//        ini_set("max_execution_time", 7200);
//
//        $this->loadModel('mOrder');
//
//        $expFee = rand(6, 15);
//
//        $openids = array('oau7MtyyFJq8Gp0t0_-zSBWUGHrA',
//            'oau7Mt699Y3vWp_iQ5WMXTNCh4bs',
//            'oau7Mt_ODbN8dpIuQzl0e3aPrSSg',
//            'oau7MtyPfsEi35ETYSW7z_z3MKh8',
//            'oau7Mt6wK0o75gvFFcvgwSClP0f4',
//            'oau7Mt2EOgm3gVnemfdVlt4UbNX0',
//            'oau7Mt92UnZUjn7eaRC5SW1A7pRg',
//            'oau7Mt11b9WCLKX5X4XXh9AaLBN4',
//            'oau7Mt6aeDyj7idm5EdvMzi5fdW4',
//            'oau7MtxRlyW7d-n0fZkvaHkZWKaE',
//            'oau7Mt27-PQlvJldvsDSRbo2eu_c',
//            'oau7Mt0HIVhy-zVy2BpfOAti5zUY');
//
//        $addrs = array(
//            array(
//                'proviceFirstStageName' => '新疆维吾尔自治区',
//                'addressCitySecondStageName' => '广州市',
//                'addressCountiesThirdStageName' => '天河区',
//                'addressDetailInfo' => '新燕花园三期1201 新燕花园三期1201 新燕花园三期1201 新燕花园三期1201',
//                'addressPostalCode' => 510006,
//                'telNumber' => 18565518404,
//                'userName' => '陈永才'
//            ),
//            array(
//                'proviceFirstStageName' => '新疆维吾尔自治区',
//                'addressCitySecondStageName' => '广州市',
//                'addressCountiesThirdStageName' => '天河区',
//                'addressDetailInfo' => '新燕花园三期1201 新燕花园三期1201 新燕花园三期1201 新燕花园三期1201',
//                'addressPostalCode' => 510006,
//                'telNumber' => 18565518404,
//                'userName' => '陈永才2'
//            )
//        );
////        $this->runStart();
//        for ($i = 0; $i < 10000; $i++) {
//
//            $pid = $this->Dao->select('product_id')->from(TABLE_PRODUCTS)->where('product_cat < 50')->orderby('RAND()')->limit(1)->getOne(false);
//
//            $this->mOrder->create($openids[ceil(rand(0, count($openids) - 1))], 'asdasdasd', array("p{$pid}m0" => rand(1, 5)), $addrs[ceil(rand(0, count($addrs) - 1))], false, $expFee, '');
//        }
////        $this->runEnd();
//    }
}
