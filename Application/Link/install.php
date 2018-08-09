<?php
$sqlquery = <<< EOT
DROP TABLE IF EXISTS `prefix_link`;
CREATE TABLE `prefix_link` (
  `linkid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `typeid` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '链接分类',
  `userid` mediumint(8) unsigned NOT NULL COMMENT '添加者',
  `linktype` varchar(20) NOT NULL DEFAULT '' COMMENT '链接类型',
  `webname` varchar(50) NOT NULL DEFAULT '' COMMENT '文字连接名称',
  `image` varchar(250) NOT NULL DEFAULT '' COMMENT '图片连接的图片名称',
  `url` varchar(250) NOT NULL DEFAULT '' COMMENT '连接地址',
  `color` char(6) NOT NULL DEFAULT '' COMMENT '颜色',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `display` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `description` text NOT NULL COMMENT '描述',
  `inputtime` int(10) NOT NULL DEFAULT '0' COMMENT '插入时间',
  `updatetime` int(10) NOT NULL COMMENT '更新时间',
  `passed`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT ' 审核' ,
  PRIMARY KEY (`linkid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `prefix_link_type`;
CREATE TABLE `prefix_link_type` (
  `typeid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `typename` varchar(50) NOT NULL DEFAULT '' COMMENT '链接分类',
  `describe` varchar(500) NOT NULL DEFAULT '' COMMENT '描述',
  `listorder` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '授权值',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '添加分类时间',
  PRIMARY KEY (`typeid`)
) TYPE=MyISAM;
EOT;
$menuid = D('Node')->add(array('parentid' => 72,'name' => '友情链接','m' => 'Link', 'c'=> 'Link', 'a'=> 'init'));
D('Node')->add(array('parentid' => $menuid, 'name' => '分类管理','m' => 'Link', 'c'=> 'LinkType', 'a'=> 'manage'));
D('Node')->add(array('parentid' => $menuid,'name' => '添加类别','m' => 'Link', 'c'=> 'LinkType', 'a'=> 'add'));
?>