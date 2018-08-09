<?php
$sqlquery = <<<EOT
DROP TABLE IF EXISTS `prefix_comment`;
CREATE TABLE `prefix_comment` (
`commentid`  char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评论ID号' ,
`userid`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID' ,
`username`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名' ,
`creat_at`  int(10) NOT NULL COMMENT '发布时间' ,
`ip`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户IP地址' ,
`status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '评论状态{0:未审核,-1:未通过审核,1:通过审核}' ,
`content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评论内容' ,
`reply`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为回复' ,
PRIMARY KEY (`commentid`),
INDEX `commentid` (`commentid`) USING BTREE 
) TYPE=MyISAM;
EOT;
$menuid = D('Node')->add(array('parentid' => 72,'name' => '评论模块','m' => $this->module, 'c'=> $this->module, 'a'=> 'setting'));
D('Node')->add(array('parentid' => 31, 'name' => '评论管理','m' => $this->module, 'c'=> $this->module, 'a'=> 'manage'));
?>