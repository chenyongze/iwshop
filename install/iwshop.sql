/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50528
Source Host           : localhost:3306
Source Database       : wshop1

Target Server Type    : MYSQL
Target Server Version : 50528
File Encoding         : 65001

Date: 2015-11-03 00:21:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_account` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_permission` tinyint(2) DEFAULT '0',
  `admin_last_login` datetime DEFAULT NULL,
  `admin_ip_address` varchar(255) DEFAULT NULL,
  `admin_auth` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`admin_account`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for admin_login_records
-- ----------------------------
DROP TABLE IF EXISTS `admin_login_records`;
CREATE TABLE `admin_login_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `ldate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for clients
-- ----------------------------
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `client_id` int(25) NOT NULL AUTO_INCREMENT COMMENT '会员卡号',
  `client_nickname` varchar(512) COLLATE utf8mb4_bin DEFAULT NULL,
  `client_name` varchar(512) COLLATE utf8mb4_bin NOT NULL COMMENT '会员姓名',
  `client_sex` varchar(1) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '会员性别',
  `client_phone` varchar(20) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '会员电话',
  `client_email` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `client_head` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `client_head_lastmod` datetime DEFAULT NULL,
  `client_password` varchar(255) COLLATE utf8mb4_bin DEFAULT '' COMMENT '会员密码',
  `client_level` tinyint(3) DEFAULT '0' COMMENT '会员种类\\r\\n1为普通会员\\r\\n0为合作商',
  `client_wechat_openid` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '会员微信openid',
  `client_joindate` date NOT NULL COMMENT '入会日期',
  `client_province` varchar(60) COLLATE utf8mb4_bin DEFAULT NULL,
  `client_city` varchar(60) COLLATE utf8mb4_bin DEFAULT NULL,
  `client_address` varchar(60) COLLATE utf8mb4_bin DEFAULT '' COMMENT '会员住址',
  `client_money` float(15,2) NOT NULL DEFAULT '0.00' COMMENT '会员存款',
  `client_credit` int(15) NOT NULL DEFAULT '0' COMMENT '会员积分',
  `client_remark` varchar(255) COLLATE utf8mb4_bin DEFAULT '' COMMENT '会员备注',
  `client_groupid` int(11) DEFAULT '0',
  `client_storeid` int(10) DEFAULT '0' COMMENT '会员所属店号',
  `client_personid` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `client_comid` int(11) DEFAULT '0',
  `client_autoenvrec` tinyint(4) DEFAULT '0',
  `unionid` varchar(256) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_com` tinyint(4) DEFAULT '0',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `index_openid` (`client_wechat_openid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for clients_group
-- ----------------------------
DROP TABLE IF EXISTS `clients_group`;
CREATE TABLE `clients_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for client_autoenvs
-- ----------------------------
DROP TABLE IF EXISTS `client_autoenvs`;
CREATE TABLE `client_autoenvs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `envid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for client_credit_record
-- ----------------------------
DROP TABLE IF EXISTS `client_credit_record`;
CREATE TABLE `client_credit_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `amount` int(5) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `reltype` tinyint(2) DEFAULT NULL,
  `relid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for client_envelopes
-- ----------------------------
DROP TABLE IF EXISTS `client_envelopes`;
CREATE TABLE `client_envelopes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `envid` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT '0',
  `exp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for client_envelopes_type
-- ----------------------------
DROP TABLE IF EXISTS `client_envelopes_type`;
CREATE TABLE `client_envelopes_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `type` int(11) DEFAULT '0',
  `req_amount` float DEFAULT NULL,
  `dis_amount` float DEFAULT NULL,
  `pid` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `remark` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for client_level
-- ----------------------------
DROP TABLE IF EXISTS `client_level`;
CREATE TABLE `client_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `level_credit` int(11) NOT NULL,
  `level_discount` float DEFAULT NULL,
  `level_credit_feed` float DEFAULT NULL,
  `upable` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for client_messages
-- ----------------------------
DROP TABLE IF EXISTS `client_messages`;
CREATE TABLE `client_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) DEFAULT NULL,
  `msgtype` tinyint(2) DEFAULT '0',
  `msgcont` text,
  `msgdirect` tinyint(4) DEFAULT '0',
  `autoreped` tinyint(4) DEFAULT '0',
  `send_time` datetime DEFAULT NULL,
  `sreaded` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for client_message_session
-- ----------------------------
DROP TABLE IF EXISTS `client_message_session`;
CREATE TABLE `client_message_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) DEFAULT NULL,
  `unread` int(11) DEFAULT '0',
  `undesc` varchar(255) DEFAULT NULL,
  `lasttime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniopenid` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for client_order_address
-- ----------------------------
DROP TABLE IF EXISTS `client_order_address`;
CREATE TABLE `client_order_address` (
  `addr_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `tel` varchar(255) COLLATE utf8_bin NOT NULL,
  `postal_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`addr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for client_product_likes
-- ----------------------------
DROP TABLE IF EXISTS `client_product_likes`;
CREATE TABLE `client_product_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni` (`openid`,`product_id`),
  KEY `uopenid` (`openid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for client_sign_record
-- ----------------------------
DROP TABLE IF EXISTS `client_sign_record`;
CREATE TABLE `client_sign_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dt` date DEFAULT NULL,
  `credit` int(11) DEFAULT NULL,
  `openid` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dt` (`dt`,`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for companys
-- ----------------------------
DROP TABLE IF EXISTS `companys`;
CREATE TABLE `companys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `return_percent` float(5,3) DEFAULT '0.050',
  `money` float DEFAULT '0',
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account` varchar(255) DEFAULT NULL,
  `bank_personname` varchar(255) DEFAULT NULL,
  `person_id` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `login_ip` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `utype` tinyint(4) DEFAULT NULL,
  `verifed` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniname` (`name`,`email`,`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for company_bills
-- ----------------------------
DROP TABLE IF EXISTS `company_bills`;
CREATE TABLE `company_bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comid` int(11) DEFAULT NULL,
  `bill_amount` float(10,2) DEFAULT NULL,
  `bill_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for company_income_record
-- ----------------------------
DROP TABLE IF EXISTS `company_income_record`;
CREATE TABLE `company_income_record` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` float(11,2) NOT NULL DEFAULT '0.00',
  `date` datetime NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `com_id` varchar(255) COLLATE utf8_bin NOT NULL,
  `pcount` int(11) NOT NULL,
  `is_seted` tinyint(4) DEFAULT '0',
  `is_reqed` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for company_spread_record
-- ----------------------------
DROP TABLE IF EXISTS `company_spread_record`;
CREATE TABLE `company_spread_record` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `com_id` varchar(255) COLLATE utf8_bin NOT NULL,
  `product_id` int(11) NOT NULL,
  `readi` int(11) NOT NULL DEFAULT '1',
  `turned` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for company_spread_record_details
-- ----------------------------
DROP TABLE IF EXISTS `company_spread_record_details`;
CREATE TABLE `company_spread_record_details` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `spread_id` int(11) NOT NULL,
  `cclient_id` int(11) NOT NULL,
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for company_users
-- ----------------------------
DROP TABLE IF EXISTS `company_users`;
CREATE TABLE `company_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `comid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniopenid` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for enterprise
-- ----------------------------
DROP TABLE IF EXISTS `enterprise`;
CREATE TABLE `enterprise` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ename` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ephone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for enterprise_users
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_users`;
CREATE TABLE `enterprise_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) DEFAULT NULL,
  `eid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniopenid` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for envs_robblist
-- ----------------------------
DROP TABLE IF EXISTS `envs_robblist`;
CREATE TABLE `envs_robblist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `on` int(11) DEFAULT NULL,
  `remains` int(11) DEFAULT NULL,
  `envsid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for envs_robrecord
-- ----------------------------
DROP TABLE IF EXISTS `envs_robrecord`;
CREATE TABLE `envs_robrecord` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `envsid` int(11) DEFAULT NULL,
  `eid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for gmess_category
-- ----------------------------
DROP TABLE IF EXISTS `gmess_category`;
CREATE TABLE `gmess_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL,
  `parent` int(11) DEFAULT '0',
  `sort` tinyint(4) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for gmess_page
-- ----------------------------
DROP TABLE IF EXISTS `gmess_page`;
CREATE TABLE `gmess_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `desc` varchar(255) DEFAULT NULL,
  `catimg` varchar(255) DEFAULT NULL,
  `thumb_media_id` varchar(255) DEFAULT NULL,
  `media_id` varchar(255) DEFAULT NULL,
  `createtime` date DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for gmess_send_stat
-- ----------------------------
DROP TABLE IF EXISTS `gmess_send_stat`;
CREATE TABLE `gmess_send_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_id` int(11) NOT NULL,
  `send_date` datetime DEFAULT NULL,
  `send_count` int(11) DEFAULT NULL,
  `read_count` int(11) DEFAULT '0',
  `share_count` int(11) DEFAULT '0',
  `receive_count` int(11) DEFAULT NULL,
  `send_type` tinyint(4) DEFAULT '0',
  `msg_type` enum('text','images') DEFAULT 'images',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(20) NOT NULL AUTO_INCREMENT COMMENT '订单编号',
  `client_id` int(20) DEFAULT NULL COMMENT '客户编号',
  `order_time` datetime DEFAULT NULL COMMENT '订单交易时间',
  `receive_time` datetime DEFAULT NULL,
  `send_time` datetime DEFAULT NULL,
  `order_balance` float DEFAULT '0',
  `order_yunfei` float(11,2) DEFAULT '0.00',
  `order_amount` float(10,2) DEFAULT '0.00' COMMENT '总价',
  `order_refund_amount` float DEFAULT '0',
  `company_com` varchar(255) COLLATE utf8_bin DEFAULT '0',
  `envs_id` int(11) DEFAULT '0',
  `product_count` int(11) DEFAULT '0',
  `order_dixian` float(10,2) NOT NULL DEFAULT '0.00',
  `serial_number` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `wepay_serial` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `wepay_openid` varchar(255) COLLATE utf8_bin DEFAULT '',
  `wepay_unionid` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `bank_billno` varchar(255) COLLATE utf8_bin DEFAULT '',
  `leword` text COLLATE utf8_bin,
  `status` enum('unpay','payed','received','canceled','closed','refunded','delivering','reqing') COLLATE utf8_bin NOT NULL DEFAULT 'unpay' COMMENT '订单状态',
  `express_openid`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,  
  `express_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `express_com` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `exptime` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `enterprise_id` int(11) DEFAULT '0',
  `reci_head` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `reci_cont` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `reci_tex` float DEFAULT NULL,
  `staff_id` int(20) DEFAULT NULL COMMENT '职员工号',
  `store_id` int(10) NOT NULL DEFAULT '0' COMMENT '商店编号',
  `is_commented` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`order_id`),
  KEY `index_openid` (`wepay_openid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for orders_address
-- ----------------------------
DROP TABLE IF EXISTS `orders_address`;
CREATE TABLE `orders_address` (
  `addr_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `user_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `tel_number` varchar(255) COLLATE utf8_bin NOT NULL,
  `province` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `hash` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`addr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for orders_comment
-- ----------------------------
DROP TABLE IF EXISTS `orders_comment`;
CREATE TABLE `orders_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) DEFAULT NULL,
  `starts` tinyint(4) DEFAULT NULL,
  `content` tinytext,
  `mtime` datetime DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL,
  `anonymous` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`(191))
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for orders_detail
-- ----------------------------
DROP TABLE IF EXISTS `orders_detail`;
CREATE TABLE `orders_detail` (
  `order_id` int(20) NOT NULL COMMENT '订单编号',
  `product_id` int(20) NOT NULL COMMENT '商品编号',
  `product_count` int(10) NOT NULL COMMENT '商品数量',
  `product_discount_price` float(11,2) NOT NULL DEFAULT '0.00',
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_price_hash_id` int(11) NOT NULL DEFAULT '0',
  `is_returned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for order_reqpay
-- ----------------------------
DROP TABLE IF EXISTS `order_reqpay`;
CREATE TABLE `order_reqpay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `wepay_serial` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `amount` float NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for pageview_records
-- ----------------------------
DROP TABLE IF EXISTS `pageview_records`;
CREATE TABLE `pageview_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `openid` varchar(255) COLLATE utf8_bin DEFAULT '',
  `ip` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `referer` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for products_info
-- ----------------------------
DROP TABLE IF EXISTS `products_info`;
CREATE TABLE `products_info` (
    `product_id`  int(20) NOT NULL AUTO_INCREMENT COMMENT '商品编号' ,
    `product_code`  varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '商品条码' ,
    `product_type`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品类型' ,
    `product_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称' ,
    `product_subname`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品颜色' ,
    `product_size`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品大小' ,
    `product_cat`  int(11) NOT NULL DEFAULT 1 ,
    `product_brand`  int(11) NULL DEFAULT 0 ,
    `product_readi`  int(11) NOT NULL DEFAULT 0 ,
    `product_desc`  longtext CHARACTER SET utf8 COLLATE utf8_bin NULL ,
    `product_subtitle`  text CHARACTER SET utf8 COLLATE utf8_bin NULL ,
    `product_serial`  int(11) NULL DEFAULT 0 ,
    `product_weight`  varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '0.00' ,
    `product_online`  tinyint(4) NULL DEFAULT 1 ,
    `product_credit`  int(11) NULL DEFAULT 0 ,
    `product_prom`  int(11) NULL DEFAULT 0 ,
    `product_prom_limit`  int(11) NULL DEFAULT 0 ,
    `product_prom_limitdate`  varchar(0) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
    `product_prom_limitdays`  int(11) NULL DEFAULT 0 ,
    `product_prom_discount`  int(11) NULL DEFAULT 0 ,
    `product_expfee`  float(5,2) NULL DEFAULT 0.00 COMMENT '商品快递费用' ,
    `product_supplier`  int(11) NULL DEFAULT 0 ,
    `product_storage`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '存储条件' ,
    `product_origin`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '商品产地' ,
    `product_unit`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '商品单位' ,
    `product_instocks`  int(11) NULL DEFAULT 0 COMMENT '商品库存，在没有规格的时候此字段可用' ,
    `sell_price`  float(5,2) NULL DEFAULT 0.00 COMMENT '商品价格' ,
    `market_price`  float(5,2) NULL DEFAULT NULL COMMENT '市场参考价' ,
    `catimg`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '商品首图' ,
    `sort`  int(10) NULL DEFAULT 0 ,
    `is_delete`  tinyint(4) NULL DEFAULT 0 COMMENT '商品删除标记' ,
    PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_brand
-- ----------------------------
DROP TABLE IF EXISTS `product_brand`;
CREATE TABLE `product_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(255) DEFAULT NULL,
  `brand_img1` varchar(255) DEFAULT NULL,
  `brand_img2` varchar(255) DEFAULT NULL,
  `brand_cat` int(11) DEFAULT NULL,
  `sort` tinyint(4) DEFAULT '0',
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniname` (`brand_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_category
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `cat_descs` text COLLATE utf8_bin,
  `cat_image` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `cat_parent` int(11) NOT NULL DEFAULT '0',
  `cat_level` int(11) DEFAULT '0',
  `cat_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_enterprise_discount
-- ----------------------------
DROP TABLE IF EXISTS `product_enterprise_discount`;
CREATE TABLE `product_enterprise_discount` (
  `productId` int(11) DEFAULT NULL,
  `entId` int(11) DEFAULT NULL,
  `discount` float(5,2) DEFAULT NULL,
  UNIQUE KEY `uni` (`productId`,`entId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_images
-- ----------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(512) COLLATE utf8_bin NOT NULL,
  `image_sort` tinyint(4) DEFAULT '0',
  `image_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_id`),
  KEY `index_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_instock
-- ----------------------------
DROP TABLE IF EXISTS `product_instock`;
CREATE TABLE `product_instock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `product_spec_id` int(11) DEFAULT NULL,
  `product_stock` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for product_onsale
-- ----------------------------
DROP TABLE IF EXISTS `product_onsale`;
CREATE TABLE `product_onsale` (
  `product_id` int(20) NOT NULL AUTO_INCREMENT COMMENT '商品编号',
  `sale_prices` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '售价',
  `store_id` int(8) NOT NULL DEFAULT '0' COMMENT '商店编号',
  `discount` int(3) NOT NULL DEFAULT '100' COMMENT '折扣',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_serials
-- ----------------------------
DROP TABLE IF EXISTS `product_serials`;
CREATE TABLE `product_serials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_name` varchar(255) DEFAULT NULL COMMENT '序列名称',
  `serial_image` varchar(255) DEFAULT NULL,
  `serial_desc` varchar(255) DEFAULT NULL,
  `relcat` tinyint(4) DEFAULT NULL,
  `relevel` tinyint(4) DEFAULT NULL,
  `sort` varchar(255) DEFAULT '0' COMMENT '排序',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_spec
-- ----------------------------
DROP TABLE IF EXISTS `product_spec`;
CREATE TABLE `product_spec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `spec_det_id1` int(11) DEFAULT NULL,
  `spec_det_id2` int(11) DEFAULT NULL,
  `sale_price` float(11,2) DEFAULT NULL,
  `market_price` float(11,2) DEFAULT '0.00',
  `instock` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_view_record
-- ----------------------------
DROP TABLE IF EXISTS `product_view_record`;
CREATE TABLE `product_view_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_wechat_recomemt
-- ----------------------------
DROP TABLE IF EXISTS `product_wechat_recomemt`;
CREATE TABLE `product_wechat_recomemt` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for product_wechat_recommend
-- ----------------------------
DROP TABLE IF EXISTS `product_wechat_recommend`;
CREATE TABLE `product_wechat_recommend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wechat_autoresponse
-- ----------------------------
DROP TABLE IF EXISTS `wechat_autoresponse`;
CREATE TABLE `wechat_autoresponse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `message` text,
  `rel` int(11) DEFAULT '0',
  `reltype` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wechat_subscribe_record
-- ----------------------------
DROP TABLE IF EXISTS `wechat_subscribe_record`;
CREATE TABLE `wechat_subscribe_record` (
  `recordid` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) COLLATE utf8_bin NOT NULL,
  `date` date DEFAULT NULL,
  `dv` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`recordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wshop_banners
-- ----------------------------
DROP TABLE IF EXISTS `wshop_banners`;
CREATE TABLE `wshop_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `banner_href` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `banner_image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `banner_position` tinyint(4) DEFAULT '0',
  `reltype` tinyint(4) DEFAULT NULL,
  `relid` varchar(255) COLLATE utf8_bin DEFAULT '0',
  `sort` tinyint(4) DEFAULT '0',
  `exp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wshop_discountcode
-- ----------------------------
DROP TABLE IF EXISTS `wshop_discountcode`;
CREATE TABLE `wshop_discountcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) DEFAULT NULL,
  `code_total` int(11) DEFAULT NULL,
  `code_remains` int(11) DEFAULT NULL,
  `code_discount` float(5,2) DEFAULT '0.00',
  `template` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wshop_discountcodes
-- ----------------------------
DROP TABLE IF EXISTS `wshop_discountcodes`;
CREATE TABLE `wshop_discountcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codes` varchar(255) DEFAULT NULL,
  `qid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT NULL,
  `rectime` datetime DEFAULT NULL,
  `isvalid` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wshop_discountcode_record
-- ----------------------------
DROP TABLE IF EXISTS `wshop_discountcode_record`;
CREATE TABLE `wshop_discountcode_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) DEFAULT NULL,
  `rectime` datetime DEFAULT NULL,
  `codeid` int(11) DEFAULT NULL,
  `qid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wshop_expresstaff
-- ----------------------------
DROP TABLE IF EXISTS `wshop_expresstaff`;
CREATE TABLE `wshop_expresstaff` (
  `id` int(11) NOT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `headimg` varchar(255) DEFAULT NULL,
  `uname` varchar(255) DEFAULT NULL,
  `isnotify` tinyint(1) DEFAULT '0',
  `isexpress` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wshop_menu
-- ----------------------------
DROP TABLE IF EXISTS `wshop_menu`;
CREATE TABLE `wshop_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relid` int(11) DEFAULT NULL,
  `reltype` tinyint(4) DEFAULT NULL,
  `relcontent` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wshop_recomment_company
-- ----------------------------
DROP TABLE IF EXISTS `wshop_recomment_company`;
CREATE TABLE `wshop_recomment_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `status` enum('unfix','fixed','close') DEFAULT 'unfix',
  `content` text,
  `comid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wshop_search_record
-- ----------------------------
DROP TABLE IF EXISTS `wshop_search_record`;
CREATE TABLE `wshop_search_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wshop_settings
-- ----------------------------
DROP TABLE IF EXISTS `wshop_settings`;
CREATE TABLE `wshop_settings` (
  `key` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(512) DEFAULT NULL,
  `last_mod` datetime NOT NULL,
  PRIMARY KEY (`key`),
  KEY `index_key` (`key`) USING HASH
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wshop_settings_expfee
-- ----------------------------
DROP TABLE IF EXISTS `wshop_settings_expfee`;
CREATE TABLE `wshop_settings_expfee` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `province` varchar(255) COLLATE utf8mb4_bin DEFAULT '',
  `citys` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `ffee` float DEFAULT NULL,
  `ffeeadd` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wshop_settings_section
-- ----------------------------
DROP TABLE IF EXISTS `wshop_settings_section`;
CREATE TABLE `wshop_settings_section` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `pid` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `banner` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `relid` int(5) DEFAULT NULL,
  `bsort` tinyint(5) DEFAULT '0',
  `ftime` datetime DEFAULT NULL,
  `ttime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wshop_spec
-- ----------------------------
DROP TABLE IF EXISTS `wshop_spec`;
CREATE TABLE `wshop_spec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spec_name` varchar(255) NOT NULL,
  `spec_remark` varchar(255) DEFAULT NULL,
  `spec_deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wshop_spec_det
-- ----------------------------
DROP TABLE IF EXISTS `wshop_spec_det`;
CREATE TABLE `wshop_spec_det` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spec_id` int(11) NOT NULL,
  `det_name` varchar(255) NOT NULL,
  `det_sort` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wshop_suppliers
-- ----------------------------
DROP TABLE IF EXISTS `wshop_suppliers`;
CREATE TABLE `wshop_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supp_name` varchar(120) DEFAULT NULL,
  `supp_phone` varchar(255) DEFAULT NULL,
  `supp_stime` varchar(255) DEFAULT NULL,
  `supp_sprice` varchar(255) DEFAULT NULL,
  `supp_sarea` varchar(255) DEFAULT NULL,
  `supp_desc` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniname` (`supp_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wshop_user_cumulate
-- ----------------------------
CREATE TABLE `wshop_user_cumulate` (
  `ref_date` date NOT NULL,
  `user_source` tinyint(2) DEFAULT '0',
  `cumulate_user` int(11) DEFAULT '0',
  PRIMARY KEY (`ref_date`),
  UNIQUE KEY `ref_date` (`ref_date`,`user_source`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wshop_user_summary
-- ----------------------------
CREATE TABLE `wshop_user_summary` (
  `ref_date` date NOT NULL,
  `user_source` tinyint(2) DEFAULT NULL COMMENT '0代表其他（包括带参数二维码） 3代表扫二维码 17代表名片分享 35代表搜号码（即微信添加朋友页的搜索） 39代表查询微信公众帐号 43代表图文页右上角菜单',
  `new_user` int(11) DEFAULT NULL,
  `cancel_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`ref_date`),
  UNIQUE KEY `ref_date` (`ref_date`,`user_source`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `client_level` VALUES ('0', '普通会员', '0', '100', '0', '0');

INSERT INTO `product_serials` VALUES ('0', '默认', null, null, null, null, '0', '0');

UPDATE `client_level` SET id = 0;

UPDATE `product_serials` SET id = 0;