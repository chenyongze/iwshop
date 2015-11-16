
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

-- 默认自动回复
INSERT INTO `wechat_autoresponse`(`key`,`message`,`rel`,`reltype`) VALUES ("default","默认回复","0","0");

ALTER TABLE `products_info` ADD COLUMN `product_storage`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '存储条件' AFTER `product_supplier`;

ALTER TABLE `products_info` ADD COLUMN `product_origin`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '商品产地' AFTER `product_storage`;

ALTER TABLE `products_info` ADD COLUMN `product_unit`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '商品单位' AFTER `product_origin`;

ALTER TABLE `products_info` ADD COLUMN `product_instocks`  int(11) NULL DEFAULT 0 COMMENT '商品库存，在没有规格的时候此字段可用' AFTER `product_unit`;

ALTER IGNORE TABLE `products_info` CHANGE `delete` `is_delete` TINYINT(1) DEFAULT 0;

ALTER TABLE `products_info` DROP COLUMN `store_id`;