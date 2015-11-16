<?php

/**
 * 订单模型
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class mOrder extends Model {

    // 商品售价数组
    private $productSalePrices;
    // 订单商品数量
    private $order_product_count = 0;
    // 发货通知接口
    private $deliver_notify_url = "https://api.weixin.qq.com/pay/delivernotify?access_token=";
    // openid
    private $openid = false;
    // uid
    private $uid;

    /**
     * Create New Order <wechat payment success>
     * @param string $openid 微信OPENID
     * @param array $orderlist 订单列表
     * @param array $address 地址信息
     * @param float $params [expfee, companyid, banlancepay, envsid] 其他附加参数
     * @return <boolean>
     */
    public function create($openid, $orderlist, $address, $params = []) {
        if (empty($openid)) {
            throw new Exception('openid cannot be empty');
        }
        if (!is_array($orderlist)) {
            throw new Exception('orderlist cannot be empty');
        }
        $this->loadModel('User');
        $this->Db->transtart();
        try {
            // 运费
            if (!isset($params['expfee'])) {
                $params['expfee'] = 0.00;
            } else {
                $params['expfee'] = $params['expfee'] > 0 ? floatval($params['expfee']) : 0.00;
            }
            // order infos
            $this->openid = $openid;
            // 获取用户uid
            $this->uid = $this->User->getUidByOpenId($this->openid);
            // 获取集团ID
            $entS = $this->User->getEntsId($this->openid);
            // 打包订单列表
            $orderList = $this->orderListRepack($orderlist);
            // 计算订单总额
            $orderAmount = $this->sumOrderAmount($orderList) + $params['expfee'];
            // 代理编号处理
            if (isset($params['companyid']) && $params['companyid'] > 0) {
                $companyCom = intval($params['companyid']);
            } else {
                $companyCom = $this->User->getCompanyId($openid);
            }
            // 如果使用余额支付
            if (isset($params['balancePay']) && $params['balancePay']) {
                $uinfo = $this->User->getUserInfo();
                // 计算将扣减的余额
                if ($uinfo->balance >= $orderAmount) {
                    $orderBalance = $orderAmount;
                } else {
                    $orderBalance = $uinfo->balance;
                }
                // 减余额
                $this->User->mantUserBalance($orderBalance, $uinfo->uid, $type = User::MANT_BALANCE_DIS);
            } else {
                $orderBalance = 0.00;
            }
            // 如果使用红包抵扣
            if (isset($params['envsid']) && $params['envsid'] > 0) {
                $this->loadModel('Envs');
                $envs = $this->Envs->get($params['envsid']);
                $discountAmount = floatval($envs['dis_amount']);
                if ($discountAmount > 0) {
                    $orderAmount -= $discountAmount;
                    // 核销红包
                    $this->Envs->distoryEnvs($this->uid, $params['envsid'], 1);
                }
            } else {
                $params['envsid'] = 0;
            }
            // 订单备注
            if (isset($params['remark'])) {
                $params['remark'] = addslashes($params['remark']);
            } else {
                $params['remark'] = '';
            }
            // 订单状态
            $orderStatus = $orderAmount == 0 ? 'payed' : 'unpay';
            // 生成序列号
            $serial_number = date("Ymdhis") . mt_rand(10, 99);
            // 写入订单数据
            $orderId = $this->Dao->insert(TABLE_ORDERS, ['status', 'company_com', 'client_id', 'product_count',
                                'order_balance', 'order_amount', 'order_yunfei', 'order_time', 'wepay_serial', 'envs_id',
                                'wepay_openid', 'leword', 'serial_number', 'exptime'])
                            ->values([$orderStatus, $companyCom, $this->uid, $this->order_product_count,
                                $orderBalance, $orderAmount, $params['expfee'], 'NOW()', '', $params['envsid'],
                                $openid, $params['remark'], $serial_number, $address['exptime']])->exec();
            // 生成订单详情信息
            $SQL_orderDetails = $this->genOrderDetailSQL($orderId, $orderList);
            // 执行SQL
            $this->Db->query($SQL_orderDetails);
            // 写入订单地址
            $addrHash = $this->writeAddressData($orderId, $address);
            // 回写订单哈希到订单表
            $addrHash && $this->Dao->update(TABLE_ORDERS)->set(array('address_hash' => $addrHash))->where("order_id = $orderId")->exec();
            $this->Db->transcommit();
            // 返回订单Id
            return $orderId;
        } catch (Exception $ex) {
            $this->Dao->echoSql();
            $this->Db->transrollback();
            throw $ex;
        }
    }

    /**
     * 
     * @param type $orderList
     * @return type
     */
    private function orderListRepack($orderList) {
        $matchs = array();
        $ret = array();
        foreach ($orderList as $key => $count) {
            preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
            $ret[] = array('pid' => intval($matchs[1]), 'spid' => intval($matchs[2]), 'count' => intval($count));
        }
        return $ret;
    }

    /**
     * 计算订单总金额
     * @param type $orderList
     * @return <Int> orderAmount
     */
    private function sumOrderAmount($orderList) {
        $return = 0;
        $this->loadModel('User');
        foreach ($orderList as $ord) {
            $pid = $ord['pid'];
            $discount = $this->User->getDiscount($this->uid);
            $pinfo = $this->Dao->select()->from(TABLE_PRODUCTS)->where("product_id = $pid")->getOneRow();
            if ($pinfo['product_prom'] == 1 && time() < strtotime($pinfo['product_prom_limitdate'])) {
                $discount = $pinfo['product_prom_discount'] / 100;
            }
            if ($ord['spid'] > 0) {
                $salePrice = $this->Db->getOne("SELECT sale_price FROM `product_spec` WHERE `product_id` = $pid AND `id` = $ord[spid];");
            } else {
                $salePrice = $this->Db->getOne("SELECT sale_prices FROM `product_onsale` WHERE `product_id` = $pid;");
            }
            if ($salePrice > 1) {
                $this->productSalePrices[$pid] = $salePrice * $discount;
            } else {
                $this->productSalePrices[$pid] = $salePrice;
            }
            $this->order_product_count += $ord['count'];
            $return += $this->productSalePrices[$pid] * $ord['count'];
        }
        return $return;
    }

    /**
     * 订单积分结算
     * @param type $orderId
     * @return boolean
     */
    public function creditFinalEstimate($orderId) {
        $this->loadModel('UserCredit');
        // uid
        $uid = $this->Dao->select('client_id')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne();
        // amount
        $amount = $this->Dao->select('order_amount')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne();
        // 获取等级信息
        $lev = $this->UserLevel->getLevByUid($uid);
        // 积分赠送
        $creditTotal = $amount * $lev['level_credit_feed'] / 100;
        if ($creditTotal > 0) {
            $ret = $this->UserCredit->add($uid, $creditTotal);
            // 检查升级条件
            $this->UserLevel->checkUpdate($orderId);
            if ($ret) {
                $this->UserCredit->record($uid, $creditTotal, 0, $orderId);
            }
            return $ret;
        }
        return false;
    }

    /**
     * 
     * @param type $orderList
     * @return SQLstatment <string>
     */
    private function genOrderDetailSQL($orderId, $orderList) {
        // original sql statment
        $SQL = sprintf("INSERT INTO orders_detail 
            (`order_id`,`product_id`,`product_count`,`product_discount_price`,`is_returned`,`product_price_hash_id`)
            VALUES ");
        $_tmp = array();
        foreach ($orderList as $ord) {
            // pack params
            array_push($_tmp, sprintf("(%s, %s, %s, %s, 0, %s)", $orderId, $ord['pid'], $ord['count'], $this->productSalePrices[$ord['pid']], $ord['spid']));
        }
        return $SQL . implode(',', $_tmp) . ';';
    }

    /**
     * 写入订单地址
     * @todo 自动归集
     * @param type $orderid
     * @param type $addrData
     * @return string $hash
     */
    public function writeAddressData($orderid, $addrData) {
        $client_id = intval($this->pCookie('uid'));
        $hash = hash('md4', $this->toJson($addrData));
        $SQL = sprintf("INSERT IGNORE INTO `orders_address` (`order_id`,`client_id`,`user_name`,`tel_number`,`postal_code`,`address`,`province`,`city`,`hash`) "
                . "VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');", $orderid, $client_id, $addrData['userName'], $addrData['telNumber'], $addrData['addressPostalCode'], $addrData['Address'], $addrData['proviceFirstStageName'], $addrData['addressCitySecondStageName'], $hash);
        return $this->Db->query($SQL) ? $hash : false;
    }

    /**
     * despacthGood
     * 发货 - 通知
     */
    public function despacthGood($orderId, $expressCode, $expressCompany) {
        if ($orderId > 0 && !empty($expressCode) && !empty($expressCompany)) {
            $SQL = sprintf("UPDATE `orders` SET `send_time` = NOW(),`status` = 'delivering',`express_code` = '%s',`express_com`='%s' WHERE order_id = $orderId;", $expressCode, $expressCompany);
            $AffectRow = $this->Db->query($SQL);
            if ($AffectRow != false) {
                if (APPID != '' && APPSECRET != '') {
                    $this->wechat_deliverNotify($orderId);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * 微信后台发货通知接口
     */
    public function wechat_deliverNotify($orderId) {

        include_once(dirname(__FILE__) . "/../lib/Tools.php");
        include_once(dirname(__FILE__) . "/../lib/wepaySdk/WxPayHelper.php");
        include_once(dirname(__FILE__) . "/WechatSdk.php");

        $SignTool = new SignTool();

        $dtime = (string) time();

        $data = $this->Db->getOneRow("SELECT `wepay_serial`,`wepay_openid` FROM `orders` WHERE `order_id` = $orderId;");

        if ($data['wepay_serial'] != '') {
            $Stoken = WechatSdk::getServiceAccessToken();

            // app_signature：appid、appkey、openid、transid、out_trade_no、deliver_timestamp、deliver_status、deliver_msg

            $SignTool->setParameter('appid', APPID);
            $SignTool->setParameter('appkey', APPSECRET);
            $SignTool->setParameter('deliver_timestamp', $dtime);
            $SignTool->setParameter('deliver_status', "1");
            $SignTool->setParameter('deliver_msg', "ok");
            $SignTool->setParameter('openid', $data['wepay_openid']);
            $SignTool->setParameter('out_trade_no', $orderId);
            $SignTool->setParameter('transid', $data['wepay_serial']);

            $WxPayHelper = new WxPayHelper();
            $app_signature = $WxPayHelper->get_biz_sign($SignTool->parameters);

            $postData = array(
                "appid" => APPID,
                "openid" => $data['wepay_openid'],
                "transid" => $data['wepay_serial'],
                "out_trade_no" => $orderId,
                "deliver_timestamp" => $dtime,
                "deliver_status" => "1",
                "deliver_msg" => "ok",
                "app_signature" => $app_signature,
                "sign_method" => SIGNTYPE
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->deliver_notify_url . $Stoken);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // exec
            curl_exec($curl);
            // close
            curl_close($curl);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取订单详情
     * @param <int> $id 订单id
     */
    public function GetOrderDetail($id, $cache = true) {
        if ($id > 0) {
            $this->loadModel('Product');
            $this->Db->cache = $cache;
            $orderData = $this->Db->getOneRow("SELECT * FROM `orders` WHERE `order_id` = $id");
            $orderData['address'] = $this->Db->getOneRow("SELECT * FROM `orders_address` WHERE order_id = $id;");
            $orderData['products'] = $this->Db->query("SELECT product_id, product_price_hash_id, product_count, product_discount_price FROM `orders_detail` where order_id = " . $orderData['order_id']);
            foreach ($orderData['products'] as &$pds) {
                $pinfo = $this->Product->getProductInfoWithSpec($pds['product_id'], $pds['product_price_hash_id']);
                $pinfo = array_merge(array('product_count' => $pds['product_count'], 'product_discount_price' => $pds['product_discount_price']), $pinfo);
                $pds = $pinfo;
            }
            return $orderData;
        } else {
            return false;
        }
    }

    /**
     * 获取订单列表
     * @param <int> $id 订单id
     */
    public function GetOrderDetailList($id) {
        if ($id > 0) {
            $orderData = $this->Dao->select()->from(TABLE_ORDERS_DETAILS)->alias('od')
                            ->leftJoin(TABLE_PRODUCTS)->alias('po')
                            ->on('po.product_id = od.product_id')
                            ->where("od.order_id = $id")->exec();
            return $orderData;
        } else {
            return false;
        }
    }

    /**
     * 获取订单列表，单表
     */
    public function GetOrderDetails($id) {
        if ($id > 0) {
            return $this->Dao->select()->from(TABLE_ORDERS_DETAILS)->alias('od')
                            ->where("od.order_id = $id")->exec();
        } else {
            return false;
        }
    }

    /**
     * 商户订单付款通知 微信模板信息
     * @global type $config
     * @param type $orderId
     * @param type $openid
     */
    public function comNewOrderNotify($orderId) {
        $tplconfig = include APP_PATH . 'config/config_msg_template.php';
        $tpl = $tplconfig['pay_success'];
        if (!empty($tpl['tpl_id'])) {
            // 查找通知openid列表
            $openIds = explode(',', $this->getSetting('order_notify_openid'));
            if (!is_array($openIds) && count($openIds) <= 0) {
                return false;
            }
            $this->loadModel('WechatSdk');
            // 获取订单商品列表
            $orderProducts = $this->Db->query("select pi.product_name as `name`,product_count as `count` from orders_detail od 
                left JOIN products_info pi on pi.product_id = od.product_id
                where od.order_id = $orderId;");
            $orderInfos = array();
            // 获取订单信息
            $orderInfo = $this->getOrderInfo($orderId);
            foreach ($orderProducts as $oi) {
                $orderInfos[] = $oi['name'] . '(' . $oi['count'] . ')';
            }
            foreach ($openIds as $openid) {
                // 批量通知商户
                Messager::sendTemplateMessage($tpl['tpl_id'], $openid, array(
                    $tpl['first_key'] => '有一位顾客下单了，请尽快发货',
                    $tpl['serial_key'] => $orderInfo['serial_number'],
                    $tpl['product_name_key'] => implode('、', $orderInfos),
                    $tpl['product_count_key'] => $orderInfo['product_count'] . '件',
                    $tpl['order_amount_key'] => '¥' . sprintf('%.2f', $orderInfo['order_amount']),
                    $tpl['remark_key'] => '点击详情 随时查看订单状态'
                        ), $this->getBaseURI() . "?/Order/expressDetail/order_id=$orderId");
            }
        }
    }

    /**
     * 用户订单付款通知 微信模板信息
     * @global type $config
     * @param type $orderId
     * @param type $openid
     */
    public function userNewOrderNotify($orderId, $openid) {
        global $config;
        $tplconfig = include APP_PATH . 'config/config_msg_template.php';
        $tpl = $tplconfig['pay_success'];
        if (!empty($tpl['tpl_id'])) {
            $this->loadModel('WechatSdk');
            $orderProducts = $this->Db->query("select pi.product_name as `name`,product_count as `count` from orders_detail od 
                left JOIN products_info pi on pi.product_id = od.product_id
                where od.order_id = $orderId;");
            $orderInfos = array();
            $orderInfo = $this->getOrderInfo($orderId);
            foreach ($orderProducts as $oi) {
                $orderInfos[] = $oi['name'] . '(' . $oi['count'] . ')';
            }
            return Messager::sendTemplateMessage($tpl['tpl_id'], $openid, array(
                        $tpl['first_key'] => '感谢您在' . $config->shopName . '购物',
                        $tpl['serial_key'] => $orderInfo['serial_number'],
                        $tpl['product_name_key'] => implode('、', $orderInfos),
                        $tpl['product_count_key'] => $orderInfo['product_count'] . '件',
                        $tpl['order_amount_key'] => '¥' . sprintf('%.2f', $orderInfo['order_amount']),
                        $tpl['remark_key'] => '点击详情 随时查看订单状态'
                            ), $this->getBaseURI() . "?/Order/expressDetail/order_id=$orderId");
        }
    }

    /**
     * 微信支付退货处理
     * 退货前提是必须支付成功
     * @param type $orderId
     * @return boolean
     */
    public function orderRefund($orderId, $refund_fee = false) {
        global $config;
        $orderId = intval($orderId);
        $orderInfo = $this->getOrderInfo();
        if ($orderId > 0) {

            $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

            $totalFee = floatval($this->Db->getOne("SELECT `order_amount` FROM `orders` WHERE `order_id` = $orderId;")) * 100;

            $idReq = $this->Dao->select()->from(TABLE_ORDER_REQS)->where("order_id = $orderId AND `wepay_serial` <> ''")->getOne() > 0;


            if ($idReq !== false && count($idReq) > 0) {
                // 众筹退款
                foreach ($idReq as $req) {
                    // req-
                    $postData = array(
                        "appid" => APPID,
                        "mch_id" => PARTNER,
                        "transaction_id" => $req['wepay_serial'],
                        "out_trade_no" => 'req' - $req['id'],
                        "out_refund_no" => 'req' - $req['id'],
                        "total_fee" => $req['amount'],
                        "refund_fee" => $req['amount'],
                        "op_user_id" => PARTNER,
                        "nonce_str" => $this->createNoncestr()
                    );

                    $sign = $this->createSign($postData);

                    $postData["sign"] = $sign;

                    $reqPar = $this->toXML($postData);

                    $r = $this->curlPost($url, $reqPar, 50);
                }
            } else {

                if (!$refund_fee) {
                    $refund_fee = $totalFee;
                } else {
                    $refund_fee *= 100;
                }

                if ($refund_fee > $totalFee) {
                    // 支持部分退款，但是不允许大于总金额
                    return false;
                } else {

                    $postData = array(
                        "appid" => APPID,
                        "mch_id" => PARTNER,
                        "transaction_id" => $orderInfo['wepay_serial'],
                        "out_trade_no" => $config->out_trade_no_prefix . $orderId,
                        "out_refund_no" => $orderId . $refund_fee,
                        "total_fee" => $totalFee,
                        "refund_fee" => $refund_fee,
                        "op_user_id" => PARTNER,
                        "nonce_str" => $this->createNoncestr()
                    );

                    $sign = $this->createSign($postData);

                    $postData["sign"] = $sign;

                    $reqPar = $this->toXML($postData);

                    $r = $this->curlPost($url, $reqPar, 50);

                    return $r;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * 获取订单信息
     * @param type $orderId
     * @return type
     */
    public function getOrderInfo($orderId) {
        return $this->Dao->select()->from(TABLE_ORDERS)->where("order_id = $orderId")->getOneRow();
    }

    /**
     * 生成随机字符串
     * @param type $length
     * @return type
     */
    protected function createNoncestr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str.= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            //$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
        }
        return $str;
    }

    /**
     * 数组转换XML
     * @param type $arr
     * @return string
     */
    public function toXML($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml.="<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml.="<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 
     * @param type $postData
     * @return type
     */
    private function createReqStr($postData) {
        $reqPar = "";

        ksort($postData);

        foreach ($postData as $k => $v) {
            if ("spbill_create_ip" != $k) {
                $reqPar .= $k . "=" . urlencode($v) . "&";
            } else {
                $reqPar .= $k . "=" . str_replace(".", "%2E", $v) . "&";
            }
        }

        $reqPar = substr($reqPar, 0, strlen($reqPar) - 1);

        return $reqPar;
    }

    /**
     * 
     * @param type $postData
     * @return type
     */
    function createSign($postData) {
        ksort($postData);

        $signPars = "";

        foreach ($postData as $k => $v) {
            if ("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }

        $signPars .= "key=" . PARTNERKEY;

        $sign = strtoupper(md5($signPars));

        return $sign;
    }

    /**
     * curl POST 
     * 
     * @param   string  url 
     * @param   array   数据 
     * @param   int     请求超时时间 
     * @param   bool    HTTPS时是否进行严格认证 
     * @return  string 
     */
    function curlPost($url, $data = array(), $timeout = 30) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 财付通caKey路径
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, CERT_PATH);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PARTNER);

        //默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, CERT_KEY_PATH);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //data with URLEncode  
        $ret = curl_exec($ch);
        curl_close($ch);
        $retObj = simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $retObj;
    }

    /**
     * 更新订单状态
     * @param type $orderId
     * @param type $status
     * @return type
     */
    public function updateOrderStatus($orderId, $status, $refundAmount = false) {
        if ($status == 'refunded' && $refundAmount) {
            return $this->Dao->update(TABLE_ORDERS)->set(array('status' => $status, 'order_refund_amount' => $refundAmount))->where("`order_id` = $orderId")->exec();
        } else {
            return $this->Dao->update(TABLE_ORDERS)->set(array('status' => $status))->where("`order_id` = $orderId")->exec();
        }
    }

    /**
     * 获取订单收货地址信息
     * @param type $orderId
     */
    public function getOrderAddr($orderId) {
        return $this->Dao->select()->from(TABLE_ORDER_ADDRESS)->where("order_id=$orderId")->getOneRow();
    }

    /**
     * 获取订单未退款金额
     * @param type $orderId
     * @return type
     */
    public function getUnRefunded($orderId) {
        return $this->Dao->select('order_amount - order_refund_amount')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne();
    }

    /**
     * 订单退款
     * @param type $orderId
     * @return type
     */
    public function getRefunded($orderId) {
        return $this->Dao->select('order_refund_amount')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne();
    }

    /**
     * 获取订单代付总额
     * @param type $orderId
     */
    public function getOrderReqAmount($orderId) {
        $amount = 0.00;
        $ret = $this->Dao->select()->from('order_reqpay')->where("order_id = $orderId AND `wepay_serial` <> '' ")->exec();
        foreach ($ret as $r) {
            $amount += round($r['amount'], 2);
        }
        return $amount > 0 ? $amount : 0;
    }

    /**
     * 获取代付列表
     * @param type $orderId
     * @return type
     */
    public function getOrderReqList($orderId) {
        $ret = $this->Dao->select()->from('order_reqpay')->where("order_id = $orderId AND `wepay_serial` <> '' ")->exec();
        foreach ($ret as &$f) {
            $f['dt'] = $this->Util->dateTimeFormat($f['dt']);
            $f['user'] = $this->User->getUserInfoRaw($f['openid']);
        }
        return $ret;
    }

    /**
     * 获取订单代付参与数量
     * @param type $orderId
     */
    public function getOrderReqCount($orderId) {
        $ret = $this->Dao->select("COUNT(`id`)")->from('order_reqpay')->where("order_id = $orderId AND `wepay_serial` <> '' ")->getOne();
        return $ret > 0 ? $ret : 0;
    }

    /**
     * 过期订单回收
     * @param type $Uid
     * @return boolean
     */
    public function orderReclycle($Uid = false) {
        if (!$Uid) {
            return false;
        }
        $expDay = $this->getSetting('order_cancel_day');
        $expDate = date('Y-m-d', strtotime('-' . $expDay . ' DAY'));
        $orderIds = $this->Dao->select("GROUP_CONCAT(order_id)")->from(TABLE_ORDERS)->where("order_time <= '$expDate'")
                ->aw("client_id = $Uid")
                ->aw("`status` = 'unpay'")
                ->getOne();
        if ($orderIds != '') {
            // 删除订单
            $this->Dao->delete()->from(TABLE_ORDERS)->where("order_id IN ($orderIds)")->exec();
            $this->Dao->delete()->from(TABLE_ORDERS_DETAILS)->where("order_id IN ($orderIds)")->exec();
            $this->Dao->delete()->from(TABLE_ORDER_ADDRESS)->where("order_id IN ($orderIds)")->exec();
        }
    }

    /**
     * 添加订单评论
     * @param type $openid
     * @param type $orderid
     * @param type $content
     * @param type $stars
     */
    public function addComment($openid, $orderid, $content, $stars) {
        $this->loadModel('User');
        if ($this->User->checkUserExt($openid) && $orderid > 0 && is_numeric($stars)) {
            $ret = $this->Dao->insert(TABLE_ORDERS_COMMENT, 'openid,starts,content,mtime,orderid')
                            ->values(array($openid, $stars, $content, Dao::FIELD_NOW, $orderid))->exec();
            if ($ret) {
                return $this->Dao->update(TABLE_ORDERS)->set(array('is_commented' => 1))->where("order_id = $orderid")->exec();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取评论
     * @param type $orderid
     */
    public function getComment($orderid) {
        if ($orderid > 0) {
            return $this->Dao->select()->from(TABLE_ORDERS_COMMENT)->where("orderid=$orderid")->getOneRow();
        } else {
            return NULL;
        }
    }

    /**
     * 获取评论列表
     * @param type $pageno
     * @param type $pagesize
     */
    public function getCommentList($pageno = 0, $pagesize = 20) {
        if ($pageno >= 0 && $pagesize > 20) {
            return $this->Dao->select()->from(TABLE_ORDERS_COMMENT)->limit($pagesize * $pageno, $pagesize)->orderby('mtime')->desc()->exec();
        } else {
            return NULL;
        }
    }

    /**
     * 获取未评价订单
     * @param type $openid
     */
    public function getUnCommentList($openid) {
        $odlist = $this->Dao->select('pd.product_id,pd.product_name,ods.order_id')
                        ->from(TABLE_ORDERS_DETAILS)->alias('ods')
                        ->leftJoin(TABLE_ORDERS)->alias('od')
                        ->on("od.order_id = ods.order_id")
                        ->leftJoin(TABLE_PRODUCTS)->alias('pd')
                        ->on("pd.product_id = ods.product_id")
                        ->where("od.wepay_openid = '$openid'")
                        ->aw("od.status = 'received'")
                        ->aw('ods.is_commented = 0')->exec();
        return $odlist;
    }

    /**
     * 检查订单归属
     * @param type $openId
     * @param type $orderId
     */
    public function checkOrderBelong($openId, $orderId) {
        $ret = $this->Db->getOneRow("SELECT COUNT(*) AS count FROM `orders` WHERE `wepay_openid` = '$openId' AND `order_id` = $orderId;");
        if ($ret['count'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * 减除库存
     * @param type $orderId
     */
    public function cutInstock($orderId) {
        $orderDetail = $this->GetOrderDetails($orderId);
        foreach ($orderDetail as $dt) {
            if ($dt['product_price_hash_id'] > 0) {
                // 更新库存表
                $this->Dao->update(TABLE_PRODUCT_SPEC)
                        ->set(array("instock" => "instock - {$dt["product_count"]}"), true)
                        ->where("id = $dt[product_price_hash_id]")
                        ->exec();
            }
        }
        return true;
    }

    /**
     * 删除订单
     * @param type $orderId
     * @return boolean
     */
    public function deleteOrder($orderId) {
        if ($orderId > 0) {
            $r1 = $this->Dao->delete()->from(TABLE_ORDERS)->where("order_id = $orderId")->exec();
            $r2 = $this->Dao->delete()->from(TABLE_ORDERS_DETAILS)->where("order_id = $orderId")->exec();
            return $r1 && $r2;
        } else {
            return false;
        }
    }

}
