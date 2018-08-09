<?php
$sqlquery = <<< EOT
DROP TABLE IF EXISTS `prefix_pay_order`;
CREATE TABLE `prefix_pay_order` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '递增ID',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '支付接口',
  `trade_sn` varchar(200) NOT NULL DEFAULT '' COMMENT '唯一订单号',
  `subject` varchar(250) NOT NULL DEFAULT '' COMMENT '商品名称',
  `total_fee` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '交易总额（单位：分）',
  `buyer_email` varchar(100) NOT NULL DEFAULT '' COMMENT '买家支付宝账号',
  `method` varchar(50) NOT NULL DEFAULT '' COMMENT '支付方式',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态',
  `trade_no` char(64) NOT NULL DEFAULT '' COMMENT '支付宝交易号',
  `notify_id` varchar(200) NOT NULL DEFAULT '' COMMENT '通知校验ID',
  `notify_time` int(1) unsigned NOT NULL COMMENT '通知时间',
  PRIMARY KEY (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `prefix_pay_payment`;
CREATE TABLE `prefix_pay_payment` (
  `pay_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `pay_name` varchar(120) NOT NULL,
  `pay_code` varchar(20) NOT NULL,
  `pay_desc` text NOT NULL,
  `pay_method` tinyint(1) NOT NULL,
  `pay_fee` varchar(10) NOT NULL,
  `config` text NOT NULL,
  `is_cod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_online` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `author` varchar(100) NOT NULL,
  `website` varchar(100) NOT NULL,
  `version` varchar(20) NOT NULL,
  PRIMARY KEY (`pay_id`),
  KEY `pay_code` (`pay_code`)
) TYPE=MyISAM;

EOT;
$menuid = D('Node')->add(array('parentid'=>72,'name'=>'在线充值','m'=>'Pay','c'=>'Pay','a'=>'pay_list'));
D('Node')->add(array('parentid'=>$menuid,'name'=>'支付模块','m'=>'Pay','c'=>'Pay','a'=>'init'));
D('Node')->add(array('parentid'=>$menuid,'name'=>'充值记录','m'=>'Pay','c'=>'Pay','a'=>'pay_list'));
D('Node')->add(array('parentid'=>$menuid,'name'=>'充值入账','m'=>'Pay','c'=>'Pay','a'=>'modify_deposit'));
D('Node')->add(array('parentid'=>$menuid,'name'=>'消费记录','m'=>'Pay','c'=>'Spend','a'=>'init'));
?>