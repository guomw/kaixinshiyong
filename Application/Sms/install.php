<?php 
$sqlquery = <<<EOT
DROP TABLE IF EXISTS `prefix_sms_report`;
CREATE TABLE `prefix_sms_report` (
`id`  bigint(15) NOT NULL AUTO_INCREMENT ,
`mobile`  varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
`posttime`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`id_code`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
`msg`  varchar(90) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
`userid`  mediumint(8) UNSIGNED NOT NULL DEFAULT 0 ,
`status`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`ip`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
`enum`  enum('notify','password','register') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'register' ,
PRIMARY KEY (`id`),
INDEX `mobile` (`mobile`, `posttime`) USING BTREE 
)
TYPE=MyISAM;
EOT;
$menuid = D('Node')->add(array('parentid'=> 72, 'name' => '短信平台', 'm' => $this->module, 'c' => $this->module, 'a' => 'manage'));
D('Node')->add(array('parentid' => $menuid, 'name' => '接口配置','m' => $this->module, 'c'=> $this->module, 'a'=> 'setting'));