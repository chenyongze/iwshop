<?php

// 支付授权目录 112.124.44.172/wshop/
// 支付请求示例 index.php
// 支付回调URL http://112.124.44.172/wshop/?/Order/payment_callback
// 维权通知URL http://112.124.44.172/wshop/?/Service/safeguarding
// 告警通知URL http://112.124.44.172/wshop/?/Service/warning

/**
 * 订单类
 */
class Order extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

    // 支付回调页面 代付
    public function payment_notify_req() {
        // postStr
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $reqId = str_replace('req-', '', $postObj->out_trade_no);
        // 微信交易单号
        $transaction_id = $postObj->transaction_id;
        // 更新订单状态
        $this->Db->query(sprintf("UPDATE `order_reqpay` SET `wepay_serial` = '%s',`dt` = NOW() WHERE `id` = %s AND `openid` = '%s';", $transaction_id, $reqId, $postObj->openid));
        // 邮件通知
        if ($reqId > 0) {
            // order_reqpay
            $this->loadModel('mOrder');
            $this->loadModel('WechatSdk');
            $orderId = $this->Dao->select('order_id')->from('order_reqpay')->where("id = $reqId")->getOne();
            // 检查募集成功
            $orderInfo = $this->mOrder->getOrderInfo($orderId);
            $reqEd = $this->mOrder->getOrderReqAmount($orderId);
            if ($reqEd == $orderInfo['order_amount']) {
                // 成功
                $this->Dao->update(TABLE_ORDERS)->set(array('status' => 'payed'))->where("order_id = $orderId")->exec();
                Messager::sendText(WechatSdk::getServiceAccessToken(), $orderInfo['wepay_openid'], "您有一笔代付进度已经到达100%！请进入个人中心查看");
            } else {
                Messager::sendText(WechatSdk::getServiceAccessToken(), $orderInfo['wepay_openid'], "您有一笔订单成功获得代付！请进入个人中心查看");
            }
        }
        // 返回success
        echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";
    }

    // 支付回调页面
    public function payment_notify() {
        global $config;
        // postStr
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        // orderid 
        $orderId = str_replace($config->out_trade_no_prefix, '', $postObj->out_trade_no);
        // 微信交易单号
        $transaction_id = $postObj->transaction_id;
        // 更新订单状态
        $wepay_serial = $this->Dao->select('wepay_serial')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne(false);

        if ($wepay_serial == '') {
            $UpdateSQL = sprintf("UPDATE `orders` SET `wepay_serial` = '%s',`status` = 'payed',`wepay_openid` = '%s' WHERE `order_id` = %s AND `status` <> 'payed';", $transaction_id, $postObj->openid, $orderId);
            $r1 = $this->Db->query($UpdateSQL);
            // 邮件通知
            if ($r1 !== false && $orderId > 0) {
                $this->loadModel('User');
                $this->loadModel('mOrder');
                // 商户订单通知
                @$this->mOrder->comNewOrderNotify($orderId);
                // 用户订单通知 模板消息
                @$this->mOrder->userNewOrderNotify($orderId, $postObj->openid);
                // 导入订单数据到个人信息
                @$this->User->importFromOrderAddress($orderId);
                // 积分结算
                @$this->mOrder->creditFinalEstimate($orderId);
                // 减库存
                @$this->mOrder->cutInstock($orderId);
            }
            // 返回success
            echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";
        }
    }

    // 订单确认页面
    public function cart() {
        global $config;
        $this->loadModel('User');
        $this->loadModel('Envs');
        $this->loadModel('JsSdk');
        $this->Smarty->caching = false;
        // Unix时间戳
        $timestamp = time();
        // 随机字符串
        $nonceStr = rand(100000, 999999);
        $envs = $this->Envs->getUserEnvs($this->getUid());
        $this->initSettings(true);
        if (Controller::inWechat() && $config->useWechatAddr) {
            $OauthURL = $this->root . $config->wxpayroot . '?id=' . $_GET['id'];
            $FinalURL = "http://" . $this->server('HTTP_HOST') . $this->server('REQUEST_URI');
            include_once(dirname(__FILE__) . "/../lib/Tools.php");
            $this->loadModel('WechatSdk');
            $AccessCode = WechatSdk::getAccessCode($OauthURL, "snsapi_base");
            if ($AccessCode !== FALSE) {
                // 获取到accesstoken和openid
                $AResult = WechatSdk::getAccessToken($AccessCode);
                #$openId = $AResult->openid;
                $AccessToken = $AResult->access_token;
            }
            // shareaddress
            $myaddr = new SignTool();
            $myaddr->setParameter("appid", APPID);
            $myaddr->setParameter("url", $FinalURL);
            $myaddr->setParameter("noncestr", $nonceStr);
            $myaddr->setParameter("timestamp", $timestamp);
            $myaddr->setParameter("accesstoken", $AccessToken);
            $addrsign = $myaddr->genSha1Sign();
            unset($AccessCode);
            // shareaddress
        }
        $signPackage = $this->JsSdk->GetSignPackage();
        // 收货地址接口Json包
        $addrsignPackage = array(
            "appId" => APPID,
            "scope" => "jsapi_address",
            "signType" => "sha1",
            "addrSign" => isset($addrsign) ? $addrsign : false,
            "timeStamp" => (string) $timestamp,
            "nonceStr" => (string) $nonceStr
        );
        $this->assign('recis', explode(',', $this->settings['reci_cont']));
        $this->Smarty->assign('envs', $envs);
        $this->Smarty->assign('signPackage', $signPackage);
        $this->Smarty->assign('addrsignPackage', $this->toJson($addrsignPackage));
        $this->Smarty->assign('title', '购物车');
        $this->Smarty->assign('promId', $_GET['id']);
        $this->Smarty->assign('promAva', $this->checkPromLimit($_GET['id']) ? 1 : 0);
        $this->Smarty->assign('userInfo', (array) $this->User->getUserInfo());
        $this->show();
    }

    /**
     * Ajax生成订单
     */
    public function ajaxCreateOrder() {
        $this->loadModel('mOrder');
        $openid = $this->getOpenId();
        $cartData = $this->pPost('cartData');
        $addrData = $this->pPost('addrData');
        if (empty($cartData)) {
            return $this->echoMsg(-1, '订单数据非法');
        } else {
            $cartData = json_decode($cartData, true);
        }
        if (empty($addrData)) {
            return $this->echoMsg(-1, '地址数据非法');
        }
        try {
            $orderId = $this->mOrder->create($openid, $cartData, $addrData, [
                'remark' => $this->post('remark'),
                'balancePay' => $this->post('balancePay') == 1,
                'expfee' => $this->post('expfee'),
                'envsid' => intval($this->post('envsId')),
            ]);
            $this->echoMsg(0, intval($orderId));
        } catch (Exception $ex) {
            $this->echoMsg(-1, $ex->getMessage());
        }
    }

    /**
     * Ajax获取订单请求数据包
     */
    public function ajaxGetBizPackage() {
        global $config;
        $orderId = intval($this->post('orderId'));
        $openid = $this->getOpenId();
        // 订单总额
        $totalFee = $this->countOrderSum($orderId) * 100;

        $nonceStr = $this->Util->createNoncestr();

        $timeStamp = strval(time());

        $pack = array(
            'appid' => APPID,
            'body' => $config->shopName,
            'timeStamp' => $timeStamp,
            'mch_id' => PARTNER,
            'nonce_str' => $nonceStr,
            'notify_url' => $config->order_wxpay_notify,
            'out_trade_no' => $config->out_trade_no_prefix . $orderId,
            'spbill_create_ip' => $this->getIp(),
            'total_fee' => $totalFee,
            'trade_type' => 'JSAPI',
            'openid' => $openid
        );

        $pack['sign'] = $this->Util->paySign($pack);

        $xml = $this->Util->toXML($pack);

        $ret = Curl::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);

        $postObj = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)));

        if (empty($postObj->prepay_id) || $postObj->return_code == "FAIL") {
            // 支付发起错误 记录到logs
            $this->log('wepay_error:' . $postObj->return_msg . ' ' . $xml);
        }

        $packJs = array(
            'appId' => APPID,
            'timeStamp' => $timeStamp,
            'nonceStr' => $nonceStr,
            'package' => "prepay_id=" . $postObj->prepay_id,
            'signType' => 'MD5'
        );

        $JsSign = $this->Util->paySign($packJs);
        
        unset($packJs['timeStamp']);

        $packJs['timestamp'] = $timeStamp;

        $packJs['paySign'] = $JsSign;

        $this->echoJson($packJs);
    }

    /**
     * 计算订单总量
     * @return <float>
     */
    private function countOrderSum($orderid) {
        return $this->Dao->select('order_amount')->from(TABLE_ORDERS)->where("`order_id` = $orderid")->getOne();
    }

    /**
     * 订单详情
     * @param type $Query
     */
    public function expressDetail($Query) {
        header('Location: ?/Uc/expressDetail/order_id=' . $Query->order_id);
    }

    /**
     * 订单取消
     * @todo a lot
     */
    public function cancelOrder() {
        $orderId = $_POST['orderId'];
        $cancelSql = "UPDATE " . TABLE_ORDERS . " SET `status` = 'canceled' WHERE `order_id` = $orderId;";
        $rst = $this->Db->query($cancelSql);
        # echo $cancelSql;
        echo $rst > 0 ? "1" : "0";
    }

    /**
     * ajax确认收货 | 过期自动确认订单
     * @param type $Q
     * @return boolean
     */
    public function confirmExpress($Q) {
        // orders >> received
        $this->loadModel('mOrder');
        $this->loadModel('WechatSdk');
        $orderIds = array();
        $recycle = isset($Q->rec);
        if ($recycle) {
            $expDay = $this->getSetting('order_confirm_day');
            $expDate = date('Y-m-d', strtotime('-' . $expDay . ' DAY'));
            $idStr = $this->Dao->select("GROUP_CONCAT(order_id)")->from(TABLE_ORDERS)->where("`send_time` <= '$expDate' AND `status` = 'delivering'")->getOne();
            if ($orderIds == '') {
                return false;
            } else {
                $orderIds = explode(',', $idStr);
            }
        } else {
            $orderIds[] = intval($this->pPost('orderId'));
        }
        // 遍历订单列表
        foreach ($orderIds as $orderId) {
            if ($orderId > 0) {
                $updateSql = "UPDATE `orders` SET status = 'received',`receive_time` = NOW() WHERE `order_id` = $orderId;";
                // 商品信息
                $orderData = $this->mOrder->GetOrderDetail($orderId);
                // 推广结算
                $companyCom = $orderData['company_com'];
                if ($companyCom != '0' && $companyCom > 0) {
                    // 代理商结算
                    $clientId = $orderData['client_id'];
                    $orderCount = $orderData['product_count'];
                    // todo model
                    foreach ($orderData['products'] as $productId => $count) {
                        $_rst = $this->Db->query("UPDATE `" . COMPANY_SPREAD . "` SET `turned` = `turned` + 1 WHERE `com_id` = '$companyCom' AND `product_id` = $productId;");
                        if (!$_rst) {
                            $this->Db->query("INSERT INTO `" . COMPANY_SPREAD . "` (`product_id`,`com_id`,`turned`) VALUES ($productId,'$companyCom',1);");
                        }
                    }
                    $companyInfo = $this->Dao->select()->from('companys')->where("id=$companyCom")->getOneRow();
                    // 代理回报比例
                    $percent = floatval($companyInfo['return_percent']);
                    // 代理Openid
                    $openid = $companyInfo['openid'];
                    // 代理UID
                    $comUid = $companyInfo['uid'];
                    // 代理所获得收益
                    $comAmount = floatval($orderData['order_amount'] * $percent);
                    // 查询二级分销
                    // 上级代理ID
                    $comcom = $this->Dao->select('client_comid')->from('clients')->where("client_id=$comUid")->getOne();
                    if ($comcom !== false) {
                        $comcomIncome = $comAmount * floatval($this->settings['com_sale_pcent']);
                        $comAmount = $comAmount - $comcomIncome;
                        // 二级回报
                        $this->Db->query("INSERT INTO `company_income_record` (`amount`,`date`,`client_id`,`order_id`,`com_id`,`pcount`) VALUE ($comcomIncome, NOW(), $clientId, $orderId, '$comcom',$orderCount);");
                    }
                    // 第一级回报
                    $this->Db->query("INSERT INTO `company_income_record` (`amount`,`date`,`client_id`,`order_id`,`com_id`,`pcount`) VALUE ($comAmount, NOW(), $clientId, $orderId, '$companyCom',$orderCount);");
                    Messager::sendText(WechatSdk::getServiceAccessToken(), $openid, date('Y-m-d') . " 您名下的会员总额为" . $orderData['order_amount'] . "的订单已完成，您获得 $comAmount 元收益！");
                }
                $ret = $this->Db->query($updateSql);
                if ($recycle) {
                    return $ret;
                } else {
                    echo $ret;
                }
            } else {
                if ($recycle) {
                    return false;
                } else {
                    echo 0;
                }
            }
        }
    }

    /**
     * @HttpPost only
     * 获取快递跟踪情况
     * @return <html>
     */
    public function ajaxGetExpressDetails() {
        $typeCom = $_POST["com"]; //快递公司
        $typeNu = $_POST["nu"];  //快递单号
        $url = "http://api.ickd.cn/?id=105049&secret=c246f9fa42e4b2c1783ef50699aa2c4d&com=$typeCom&nu=$typeNu&type=html&encode=utf8";
        //优先使用curl模式发送数据
        $res = Curl::get($url);
        echo $res;
    }

    /**
     * ajax 订单退款处理
     */
    public function orderRefund() {
        $this->loadModel('mOrder');
        $orderId = intval($this->pPost('id'));
        // 退款金额
        $amount = floatval($this->pPost('amount'));
        // 退款结果
        $ret = $this->mOrder->orderRefund($orderId, $amount);
        // 可退款金额
        $rAmount = $this->mOrder->getUnRefunded($orderId);
        // 已退款金额
        $rAmounted = $this->mOrder->getRefunded($orderId);
        if ($ret !== false) {
            if (isset($ret->return_code) && (string) $ret->return_code === 'SUCCESS') {
                // 申请已提交 进一步处理订单
                if ($rAmount == $amount || $rAmount < 0.01) {
                    // 已经全部退款
                    $this->mOrder->updateOrderStatus($orderId, 'refunded', $rAmounted + $rAmount);
                } else {
                    // 部分退款
                    $this->mOrder->updateOrderStatus($orderId, 'canceled', $rAmounted + $amount);
                }
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    /**
     * 检查限购
     * @param type $key
     * @return boolean
     */
    private function checkPromLimit($key) {
        if ($key == '') {
            return false;
        } else {
            $matchs = array();
            preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
            // product id
            $pid = intval($matchs[1]);
            $uid = $this->getUid();
            $limitDay = $this->Dao->select('product_prom_limitdays')->from(TABLE_PRODUCTS)->where("product_id = $pid")->getOne();
            $orderS = $this->Db->query("select order_time as `date` from orders_detail `dt`
left join orders `od` on `od`.order_id = `dt`.order_id
where `dt`.product_id = $pid and `od`.client_id = $uid
and 
(`status` = 'payed' or `status` = 'delivering' or `status` = 'received')");
            foreach ($orderS as $od) {
                if ($od['date'] > $limitDay) {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * 代付
     * @param type $Q
     */
    public function reqPay($Q) {
        if (isset($Q->id) && $Q->id > 0) {
            $orderId = intval($Q->id);

            $this->cacheId = $orderId;

            if (!$this->isCached()) {

                $this->loadModel('User');
                $this->loadModel('mOrder');
                $this->loadModel('JsSdk');

                $orderInfo = $this->mOrder->getOrderInfo($orderId);

                $orderDetail = $this->mOrder->GetOrderDetailList($orderId);

                $userInfo = $this->User->getUserInfoRaw($orderInfo['client_id']);

                $reqEd = $this->mOrder->getOrderReqAmount($orderId);

                $reqCount = $this->mOrder->getOrderReqCount($orderId);

                // 参与朋友
                $reqList = $this->mOrder->getOrderReqList($orderId);

                $signPackage = $this->JsSdk->GetSignPackage();

                $this->assign('signPackage', $signPackage);
                $this->assign('userInfo', $userInfo);
                $this->assign('orderInfo', $orderInfo);
                $this->assign('orderDetail', $orderDetail);
                $this->assign('reqed', $reqEd);
                $this->assign('reqcount', $reqCount);
                $this->assign('reqlist', $reqList);
                $this->assign('isfinish', $reqEd == $orderInfo['order_amount']);
            }

            $this->show();
        }
    }

    /**
     * ajax检查购物车
     */
    public function checkCart() {
        if (empty($_POST['data'])) {
            $this->echoJson(array());
        } else {
            $this->loadModel('Product');
            $this->Smarty->caching = false;
            $data = json_decode($_POST['data'], true);
            $pdList = array();
            $matchs = array();
            foreach ($data as $key => $count) {
                preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
                $pid = intval($matchs[1]);
                if (count($this->Product->checkExt($pid)) === 0) {
                    $pdList[] = $key;
                }
            }
            $this->echoJson($pdList);
        }
    }

    /**
     * 下单成功页面
     * 提示分享，返回首页，返回个人中心选项
     */
    public function order_success($Query) {
        $orderAddress = $this->Db->getOneRow("SELECT * FROM `orders_address` WHERE `order_id` = $Query->orderid;");
        $this->assign('orderAddress', $orderAddress);
        $this->assign('title', '下单成功');
        $this->show();
    }

    /**
     * 订单评价
     * @param type $Query
     */
    public function commentOrder($Query) {
        $orderId = intval($Query->order_id);
        if ($orderId > 0) {
            $this->Load->model('mOrder');
            $orderData = $this->mOrder->GetOrderDetail($orderId);
            $this->assign('order', $orderData);
            $this->assign('title', '订单评价');
            $this->show();
        }
    }

    /**
     * 订单评价
     */
    public function addComment() {
        $content = intval($this->pPost('commentText'));
        $stars = intval($this->pPost('stars'));
        $orderId = intval($this->pPost('orderId'));
        $openId = $this->getOpenId();
        if ($orderId > 0 && !empty($openId)) {
            $this->loadModel('mOrder');
            if ($this->mOrder->checkOrderBelong($openId, $orderId)) {
                // 检查订单归属
                if ($this->mOrder->addComment($openId, $orderId, $content, $stars)) {
                    $this->echoMsg(0);
                } else {
                    $this->echoMsg(-1);
                }
            } else {
                $this->echoMsg(-1);
            }
        } else {
            $this->echoMsg(-1);
        }
    }

}
