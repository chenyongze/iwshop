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
class mCompany extends Model {

    /**
     * 获取代理列表
     * @param type $verifed
     * @return type
     */
    public function getCompanys($verifed = 1) {
        return $this->Db->query("SELECT * FROM companys WHERE verifed = $verifed AND `deleted` = 0 ORDER BY id DESC;", false);
    }

    /**
     * 获取代理列表，options
     * @param type $verifed
     * @return type
     */
    public function getCompanysPairs($verifed = 1) {
        return $this->Db->query("SELECT `id`,`name` FROM companys WHERE verifed = $verifed AND `deleted` = 0 ORDER BY id DESC;", false);
    }

    /**
     * 获取代理信息
     * @param type $id
     * @return boolean
     */
    public function getCompanyInfo($id) {
        if (is_numeric($id)) {
            return $this->Db->getOneRow("SELECT * FROM companys WHERE id = $id;", false);
        } else {
            return false;
        }
    }

    /**
     * 获取代理名下会员
     * @param type $comid
     * @return boolean
     */
    public function getCompanyFellows($comid) {
        if (is_numeric($comid)) {
            $ret = $this->Db->query("SELECT
	cl.*, (
		SELECT
			count(*)
		FROM
			`orders`
		WHERE
			client_id = cl.client_id
	) AS `order_count`
        FROM
	clients AS cl
        WHERE
	cl.client_comid = $comid AND `cl`.deleted = 0;");
            foreach ($ret AS &$l) {
                $l['client_sex'] = $this->sexConv($l['client_sex']);
            }
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * 获取名下会员总数
     * @param type $comid
     * @return type
     */
    public function getCompanyFellowsCount($comid) {
        $ret = $this->Db->getOne("SELECT COUNT(*) FROM `clients` WHERE `client_comid` = $comid;");
        return intval($ret);
    }

    /**
     * 性别eng转换
     * @param type $sex
     * @return string
     */
    private function sexConv($sex) {
        $s = array('f' => '女', 'm' => '男');
        if (array_key_exists($sex, $s)) {
            return $s[$sex];
        } else {
            return '未知';
        }
    }

    /**
     * 获取代理收益总额
     * @param type $comid 代理编号
     * @param type $isset 是否已经结算
     * @param type $month 是否输出本月数据
     * @return type
     */
    public function getCompanyIncomeCount($comid, $isset = 1, $month = false) {
        if ($month) {
            $AND = "AND DATE_FORMAT(`date`,'%Y-%m') = '" . date("Y-m") . "'";
        }
        if ($isset === false) {
            $isset = '';
        } else {
            $isset = "`is_seted` = $isset AND";
        }
        return sprintf('%.2f', $this->Db->getOne("SELECT SUM(amount) AS sum FROM `company_income_record` 
        WHERE $isset `com_id` = $comid $AND;"));
    }

    /**
     * 获取微代理未结算数据
     * @return type
     */
    public function getCompanyCashs($ID = FALSE) {
        $idQuery = $ID ? "AND `uid` = '$ID'" : '';
        return $this->Db->query("SELECT *,SUM(pcount) AS count,SUM(amount) AS sum,`companys`.id FROM `company_income_record` 
LEFT JOIN `companys` ON `company_income_record`.com_id = `companys`.id
WHERE `is_seted` <> 1 $idQuery AND `companys`.deleted = 0
GROUP BY com_id");
    }

    /**
     * 代理结算over
     * @param type $ID
     * @return type
     */
    public function cashCompany($ID) {
        return $this->Db->query("UPDATE `company_income_record` SET `is_seted` = 1 WHERE `com_id` = '$ID';");
    }

    /**
     * 收益计算
     * @param type $id
     * @return type
     */
    public function getCompanyIncome($id) {
        global $config;
        $orderList = $this->Db->query("SELECT * FROM `orders` WHERE `company_com` = $id ORDER BY `order_id` DESC;");
        /**
         * 加工
         */
        foreach ($orderList as $index => $order) {
            // address
            $address = $this->Db->query("SELECT * FROM `orders_address` WHERE order_id = $order[order_id];");
            $orderList[$index]['address'] = $address[0];
            $orderList[$index]['order_time'] = $this->Util->dateTimeFormat($orderList[$index]['order_time']);
            $orderList[$index]['statusX'] = $config->orderStatus[$orderList[$index]['status']];
            $orderList[$index]['expressName'] = $express[$orderList[$index]['express_com']];
            $orderList[$index]['income'] = sprintf('%.2f', floatval($orderList[$index]['order_amount']) * $this->settings['company_profit_percent']);
            // product info
            $orderList[$index]['data'] = $this->Db->query("SELECT catimg,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price FROM `orders_detail` sd LEFT JOIN `vproductinfo` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $order['order_id']);
        }
        return $orderList;
    }

    /**
     * company结算
     * @param type $id
     * @return boolean
     */
    public function payCompanyBills($id) {
        $unSetBillAmount = floatval($this->getCompanyIncomeCount($id, 0, false));
        if ($unSetBillAmount > 0) {
            $r1 = $this->Db->query("UPDATE `company_income_record` SET is_seted = 1 WHERE com_id = '$id';");
            if ($r1) {
                return $this->Db->query("INSERT INTO `company_bills` (`bill_amount`,`comid`,`bill_time`) VALUES ('$unSetBillAmount','$id',NOW());");
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * 获取代理账单列表
     * @param type $id
     * @return type
     */
    public function getCompanyBills($id = false) {
        $id = intval($id);
        $SQL = 'SELECT * FROM `company_bills` cb LEFT JOIN `companys` cs ON cs.id = cb.comid';
        if ($id > 0) {
            $SQL .= " WHERE cs.id = $id;";
        } else {
            $SQL .= ';';
        }
        return $this->Db->query($SQL);
    }

    /**
     * 
     * @param type $openid
     * @return type
     */
    public function getCompanyIdByOpenId($openid) {
        return $this->Db->getOne("SELECT `id` FROM `companys` WHERE openid = '$openid';");
    }

    /**
     * 生成代理商后台密码密文
     * @global type $config
     * @param type $pwd
     * @return type
     */
    public function generateCompanyPwd($pwd) {
        global $config;
        return hash('sha256', hash('md4', $pwd) . $config->wshop_salt . 'pwxd');
    }

    /**
     * 
     * @param type $acc
     * @param type $pwd
     * @return type
     */
    public function validatePwd($acc, $pwd) {
        $realPwd = $this->Db->getOneRow("SELECT id,password FROM `companys` WHERE `phone` = '$acc' AND `deleted` = 0;");
        $phd = $this->generateCompanyPwd($pwd);
        return $phd === $realPwd['password'] ? $realPwd['id'] : FALSE;
    }

    /**
     * 
     * @param type $productId
     * @param type $comId
     * @return boolean
     */
    public function updateCompanySpread($productId, $comId) {
        if (!$this->Db->query("UPDATE " . COMPANY_SPREAD . " SET `readi` = `readi` + 1 WHERE `product_id` = '$productId' AND `com_id` = '$comId';")) {
            return $this->Db->query("REPLACE INTO " . COMPANY_SPREAD . " (`product_id`,`com_id`) VALUES ('$productId','$comId');");
        } else {
            return true;
        }
    }

    /**
     * 获取代理推广记录
     * @param type $comId
     * @return type
     */
    public function getCompanySpreadRecords($comId) {
        if (is_numeric($comId)) {
            # $this->loadModel('Product');
            $SQL = "SELECT
	csr.*,pdi.*,pos.sale_prices,pca.cat_name,pss.serial_name
FROM
	company_spread_record csr
LEFT JOIN products_info pdi ON pdi.product_id = csr.product_id 
LEFT JOIN product_onsale pos ON pos.product_id = csr.product_id
LEFT JOIN product_category pca on pca.cat_id = pdi.product_cat
LEFT JOIN product_serials pss on pss.id = pdi.product_serial
WHERE
	csr.com_id = '$comId';";
            $ret = $this->Db->query($SQL);
            foreach ($ret as &$r) {
                # $r['qrcode'] = $this->Product->getQrcode($r['product_id'], $comId);
                $r['turnrate'] = sprintf('%.2f', $r['readi'] > 0 ? (($r['turned'] / $r['readi']) * 100) : 0);
            }
            return $ret;
        }
    }

}
