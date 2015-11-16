<?php

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class wOrder extends Controller {

    /**
     * 权限检查
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        if (!$this->Auth->checkAuth()) {
            $this->redirect('?/Wdmin/logOut');
        } else {
            $this->loadModel('mOrder');
        }
    }

    /**
     * 订单导出
     * @param type $Q
     */
    public function order_exports($Q) {
        $stime = $Q->stime;
        $etime = $Q->etime;
        $otype = $Q->otype;
        if (!empty($stime) && !empty($etime)) {
            global $config;
            $express = include dirname(__FILE__) . '/../../config/express_code.php';
            if (strtotime($stime) > strtotime($etime)) {
                $tmp = $stime;
                $stime = $etime;
                $etime = $tmp;
            }
            $where = "order_time >= '$stime' AND order_time <= '$etime'";
            if ($otype != '') {
                $where .= " AND status = '$otype'";
            }
            $orderList = $this->Dao->select('od.order_id,od.express_code,od.express_com,wepay_serial,od.serial_number,od.order_time,pd.product_id,pd.product_name,ods.product_count,ods.product_discount_price as product_price,od.order_yunfei')
                            ->from(TABLE_ORDERS_DETAILS)->alias('ods')
                            ->leftJoin(TABLE_ORDERS)->alias('od')
                            ->on("od.order_id = ods.order_id")
                            ->leftJoin(TABLE_PRODUCTS)->alias('pd')
                            ->on("pd.product_id = ods.product_id")
                            ->where($where)->orderby('od.order_id')->desc()->exec();
            /**
             * 加工
             */
            foreach ($orderList as $index => $order) {
                // address
                $address = $this->Db->query("SELECT * FROM `orders_address` WHERE order_id = $order[order_id];");
                $orderList[$index]['address'] = $address[0];
                $orderList[$index]['expname'] = $express[$orderList[$index]['express_com']];
            }

            include dirname(__FILE__) . '/../../lib/PHPExcel/Classes/PHPExcel.php';

            include dirname(__FILE__) . '/../../lib/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

            include dirname(__FILE__) . '/../../lib/PHPExcel/Classes/PHPExcel/Reader/Excel2007.php';

            $templateName = dirname(__FILE__) . '/../../exports/orders_export/order_exp_sample/sample_1.xlsx';

            $PHPReader = new PHPExcel_Reader_Excel2007();

            if (!$PHPReader->canRead($templateName)) {
                $PHPReader = new PHPExcel_Reader_Excel5();
                if (!$PHPReader->canRead($templateName)) {
                    echo '无法识别的Excel文件！';
                    return false;
                }
            }

            $PHPExcel = $PHPReader->load($templateName);

            echo $this->genXlsxFileType1($orderList, $PHPExcel, $PHPExcel->getActiveSheet(), 2);

            header('Location: ' . $this->genXlsxFileType1($orderList, $PHPExcel, $PHPExcel->getActiveSheet(), 2));
        }
    }

    /**
     * @global type $config
     * @param type $data
     * @param type $PHPExcel
     * @param type $Sheet
     * @param type $offset
     * @param type $expType
     * @return type
     */
    private function genXlsxFileType1($data, $PHPExcel, $Sheet, $offset, $expType = 1) {
        global $config;

        $Sheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);

        foreach ($data as $index => $da) {

            $Sheet->setCellValueExplicit("A$offset", $da['wepay_serial'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("B$offset", $da['serial_number'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("C$offset", $da['order_time'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("D$offset", $da['address']['user_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("E$offset", $da['address']['address'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("F$offset", $da['address']['tel_number'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("G$offset", $da['product_id'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("H$offset", $da['product_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("I$offset", $da['product_count'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("J$offset", $da['product_price'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("K$offset", $da['product_price'] * $da['product_count'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("L$offset", $da['order_yunfei'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("M$offset", $da['address']['postal_code'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("N$offset", $da['expname'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("O$offset", $da['express_code'], PHPExcel_Cell_DataType::TYPE_STRING);

            $offset++;
        }
        // 写入文件
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        $fileName = date('Y-md') . '-' . $this->convName[$expType] . '-' . uniqid() . '.xlsx';
        $objWriter->save(dirname(__FILE__) . '/../../exports/orders_export/export_files/' . $fileName);
        return "http://" . $_SERVER['HTTP_HOST'] . $config->shoproot . 'exports/orders_export/export_files/' . $fileName;
    }

    /**
     * 获取订单详情信息
     * @param int $id 订单编号
     */
    public function getOrderInfo() {
        // 页码
        $id = $this->pGet('id');
        if ($id > 0) {
            global $config;
            $info = $this->mOrder->GetOrderDetail($id, FALSE);
            $info['statusX'] = $config->orderStatus[$info['status']];
            // 快递公司名称
            if (!empty($info['express_com'])) {
                $express = include APP_PATH . '/config/express_code.php';
                $info['expressName'] = $express[$info['express_com']];
            } else {
                $info['expressName'] = '';
            }
            $this->echoMsg(0, $info);
        } else {
            $this->echoMsg(-1);
        }
    }

    /**
     * 获取订单列表
     * @param int $page
     * @param int $page_no
     */
    public function getOrderList() {

        global $config;

        $this->Db->cache = false;

        // 页码
        $page = $this->pGet('page');
        // 订单状态
        $status = $this->pGet('status', 'all');
        // 页数
        $page_size = $this->pGet('page_size', 30);
        // 用户编号
        $uid = $this->pGet('uid', 0);
        // 搜索字段
        $serial_number = $this->pGet('serial_number', false);

        $express = include dirname(__FILE__) . '/../config/express_code.php';

        $WHERE = ' WHERE order_id > 0 ';

        // where
        if ($status != 'all') {
            if ($status == 'canceled') {
                // 退货而且已经支付才需要审核，否则直接关闭订单
                $WHERE .= " AND status = '$status' AND wepay_serial <> '' ";
            } else {
                $WHERE .= " AND status = '$status' ";
            }
        }

        if ($uid > 0) {
            $WHERE .= " AND client_id = $uid ";
        }

//        if (isset($Query->month) && !empty($Query->month) && $status != 'canceled') {
//            if ($status == 'all') {
//                $WHERE .= " WHERE DATE_FORMAT(order_time,'%Y-%c') = '$Query->month' ";
//            } if ($status == 'delivering') {
//                $WHERE .= "AND DATE_FORMAT(send_time,'%Y-%c') = '$Query->month' ";
//            } else {
//                $WHERE .= "AND DATE_FORMAT(order_time,'%Y-%c') = '$Query->month' ";
//            }
//        }

        if ($serial_number) {
            $WHERE .= "AND `serial_number` LIKE '%$serial_number%' ";
        }

        $Limit = $page * $page_size . "," . $page_size;
        // 计算总数
        $count = $this->Db->getOne("SELECT COUNT(order_id) FROM `orders` $WHERE;");
        // 订单列表
        $orderList = $this->Db->query("SELECT * FROM `orders` $WHERE ORDER BY `order_id` DESC LIMIT $Limit;");

        if ($status == 'canceled') {
            foreach ($orderList as &$od) {
                if ($od['order_amount'] < 1) {
                    $od['refundable'] = $od['order_amount'];
                } else {
                    $od['refundable'] = $this->mOrder->getUnRefunded($od['order_id']);
                }
            }
        }

        /**
         * 加工
         */
        foreach ($orderList as $index => $order) {
            // company
            if ($order['company_com'] > 0) {
                $orderList[$index]['company'] = $this->Db->getOneRow("SELECT `id`,`name` FROM `companys` WHERE `id` = $order[company_com];");
            }
            // address
            $address = $this->Db->query("SELECT * FROM `orders_address` WHERE order_id = $order[order_id];");
            $orderList[$index]['address'] = $address[0];
            $orderList[$index]['order_time'] = $this->Util->dateTimeFormat($orderList[$index]['order_time']);
            $orderList[$index]['statusX'] = $config->orderStatus[$orderList[$index]['status']];
            $orderList[$index]['expressName'] = $express[$orderList[$index]['express_com']];
            // product info
            $orderList[$index]['data'] = $this->Db->query("SELECT catimg,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price FROM `orders_detail` sd LEFT JOIN `vproductinfo` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $order['order_id']);
        }

        $this->echoMsg(0, array(
            'list' => $orderList,
            'count' => intval($count)
        ));
    }

    /**
     * 删除订单
     * @param int $order_id 订单编号
     */
    public function deleteOrder() {
        $orderId = $this->pPost('order_id', false);
        if ($orderId) {
            $this->loadModel('mOrder');
            if ($this->mOrder->deleteOrder($orderId)) {
                $this->echoMsg(0);
            } else {
                $this->echoMsg(-1, 'delete error');
            }
        } else {
            $this->echoMsg(-1, 'params error');
        }
    }

    /**
     * 获取订单分类统计数据
     */
    public function ajaxGetOrderStatnums() {
        $this->loadModel('SqlCached');
        // file cached
        $cacheKey = 'ajaxGetOrderStatnums';
        $fileCache = new SqlCached();
        $ret = $fileCache->get($cacheKey);
        if (-1 === $ret) {
            $status = array('payed' => 0, 'canceled' => 0, 'delivering' => 0, 'all' => 0, 'unpay' => 0, 'refunded' => 0, 'received' => 0, 'closed' => 0);
            foreach ($status as $key => &$value) {
                if ($key == 'all') {
                    $WHERE = ';';
                } else {
                    if ($key == 'canceled') {
                        // 退货而且已经支付才需要审核，否则直接关闭订单
                        $WHERE = " WHERE status = '$key' AND `wepay_serial` <> '';";
                    } else {
                        $WHERE = " WHERE status = '$key';";
                    }
                }
                $sql = "select count(*) from `orders`$WHERE;";
                $ret = $this->Db->getOne($sql);
                $value = intval($ret);
            }
            $fileCache->set($cacheKey, $status);
            $this->echoJson($status);
        } else {
            $this->echoJson($ret);
        }
    }

    /**
     * 订单发货
     * @param int $orderId
     * @param string $expressCode
     * @param string $expressCompany
     * @param string $expressStaff
     */
    public function expressSend() {
        if ($this->Auth->checkAuth()) {
            global $config;
            $tplconfig = include APP_PATH . 'config/config_msg_template.php';
            $tpl = $tplconfig['exp_notify'];
            $this->loadModel('mOrder');
            $this->loadModel('WechatSdk');
            $orderId = intval($this->pPost('orderId'));
            $expressCode = $this->pPost('expressCode');
            $expressCompany = $this->pPost('expressCompany');
            $expressStaff = $this->post('expressStaff');

            if ($this->mOrder->despacthGood($orderId, $expressCode, $expressCompany)) {
                global $config;
                if (!empty($tpl['tpl_id'])) {
                    // 快递公司列表
                    $expressList = include APP_PATH . 'config/express_code.php';
                    // 订单信息
                    $orderData = $this->Dao->select("wepay_openid, serial_number")->from(TABLE_ORDERS)->where("order_id = $orderId")->getOneRow();
                    // 微信模板消息提示
                    if ($expressCompany == 'none' && !empty($expressStaff)) {
                        // 获取配送人员信息
                        $expStaff = $this->Dao->select('client_name, client_phone')->from(TABLE_USER)->where("client_wechat_openid = '$expressStaff'")->getOneRow();
                        // 更新订单信息
                        $this->Dao->update(TABLE_ORDERS)->set(array('express_openid' => $expStaff))->where("order_id = $orderId")->exec();
                        $expName = $expStaff['client_name'];
                        $expCode = $expStaff['client_phone'];
                    } else {
                        $expName = $expressList[$expressCompany];
                        $expCode = $expressCode;
                    }
                    Messager::sendTemplateMessage($tpl['tpl_id'], $orderData["wepay_openid"], array(
                        $tpl['first_key'] => '您有一笔订单已发货',
                        $tpl['serial_key'] => $orderData['serial_number'],
                        $tpl['expname'] => $expName,
                        $tpl['expcode'] => $expCode,
                        $tpl['remark_key'] => '点击详情 随时查看订单状态'), $config->domain . "?/Order/expressDetail/order_id=$orderId");
                    // 提示配送员
                    if (!empty($expressStaff)) {
                        Messager::sendTemplateMessage($tpl['tpl_id'], $expressStaff, array(
                            $tpl['first_key'] => '您有一笔订单需要进行发货处理',
                            $tpl['serial_key'] => $orderData['serial_number'],
                            $tpl['expname'] => $expressList[$expressCompany],
                            $tpl['expcode'] => $expressCode,
                            $tpl['remark_key'] => '点击详情 随时查看订单详情'), $config->domain . "?/Order/expressDetail/order_id=$orderId");
                    }
                }
                $this->echoMsg(0);
            } else {
                $this->echoMsg(-1, '发货失败，参数错误');
            }
        } else {
            $this->echoMsg(-2, 'Access Denied');
        }
    }

    /**
     * 获取快递公司列表
     */
    public function getExpressCompanys() {
        $express = include APP_PATH . 'config/express_code_prefix.php';
        $expressFormated = [];
        $expressEs = $this->Dao->select("value")->from('wshop_settings')->where("`key` = 'expcompany'")->getOne();
        $expressEs = explode(',', $expressEs);
        foreach ($express as $k => &$od) {
            if (!in_array($k, $expressEs)) {
                unset($express[$k]);
            } else {
                $expressFormated[] = ['code' => $k, 'name' => $od];
            }
        }
        $this->echoMsg(0, $expressFormated);
    }
    
    /**
     * 获取快递人员列表
     */
    public function getExpressStaff() {
        $openid = $this->getSetting('order_express_openid');
        $openids = explode(',', $openid);
        $exps = $this->Dao->select("client_wechat_openid AS openid, client_name AS name")
                        ->from(TABLE_USER)->where("client_wechat_openid in ('" . implode("','", $openids) . "')")->exec();
        $this->echoMsg(0, $exps);
    }

}
