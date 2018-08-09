<?php
$sqlquery = <<< EOT
DROP TABLE IF EXISTS `prefix_announce`;
CREATE TABLE `prefix_announce` (
`announceid`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '公告标题' ,
`content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '公告内容' ,
`starttime`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间' ,
`endtime`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束时间' ,
`inputtime`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间' ,
`tenantid`  mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家id' ,
`hits`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点击量' ,
`passed`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否审核' ,
`listorder`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序' ,
`updatetime`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间' ,
`username`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '录入者' ,
`type`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0为普通会员   1为商家会员' ,
PRIMARY KEY (`announceid`)
)
TYPE=MyISAM;
EOT;

$menuid = D('Node')->add(array('parentid'=>72,'name'=>'公告','m'=>'Announce','c'=>'Announce','a'=>'init'));
D('Node')->add(array('parentid'=>$menuid,'name'=>'商家公告','m'=>'Announce','c'=>'Announce','a'=>'init','data'=>'type=3','listorder'=>2));
D('Node')->add(array('parentid'=>$menuid,'name'=>'会员公告','m'=>'Announce','c'=>'Announce','a'=>'init','data'=>'type=2','listorder'=>3));
D('Node')->add(array('parentid'=>$menuid,'name'=>'审核公告','m'=>'Announce','c'=>'Announce','a'=>'init','data'=>'type=1','listorder'=>1));
?>