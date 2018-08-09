<?php 
$sqlquery = <<<EOT
DROP TABLE IF EXISTS `prefix_message`;
CREATE TABLE `prefix_message` (
  `messageid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `send_from_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `send_to_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `folder` enum('all','inbox','outbox') NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_time` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` char(80) NOT NULL,
  `content` text NOT NULL,
  `replyid` int(10) unsigned NOT NULL DEFAULT '0',
  `del_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issystem` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`messageid`),
  KEY `msgtoid` (`send_to_id`,`folder`),
  KEY `replyid` (`replyid`),
  KEY `folder` (`send_from_id`,`folder`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `prefix_message_data`;
CREATE TABLE `prefix_message_data` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) NOT NULL,
  `group_message_id` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message` (`userid`,`group_message_id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `prefix_message_group`;
CREATE TABLE `prefix_message_group` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `groupid` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '用户组id',
  `subject` char(80) DEFAULT NULL,
  `content` text NOT NULL COMMENT '内容',
  `inputtime` int(10) unsigned DEFAULT '0',
  `status` tinyint(2) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) TYPE=MyISAM;
EOT;
$menuid = D('Node')->add(array('parentid'=> 72, 'name' => '短消息', 'm' => $this->module, 'c' => $this->module, 'a' => 'manage'));

$_manage = D('Node')->add(array('parentid'=> $menuid, 'name' => '私信管理', 'm' => $this->module, 'c' => $this->module, 'a' => 'manage'));
	$_delete = D('Node')->add(array('parentid'=> $_manage, 'name' => '删除私信', 'm' => $this->module, 'c' => $this->module, 'a' => 'delete', 'display' => 0));
	$_search_message = D('Node')->add(array('parentid'=> $_manage, 'name' => '私信搜索', 'm' => $this->module, 'c' => $this->module, 'a' => 'search_message', 'display' => 0));

$_send_one = D('Node')->add(array('parentid'=> $menuid, 'name' => '发送私信', 'm' => $this->module, 'c' => $this->module, 'a' => 'send_one'));

$_message_group_manage = D('Node')->add(array('parentid'=> $menuid, 'name' => '群发消息管理', 'm' => $this->module, 'c' => $this->module, 'a' => 'message_group_manage'));
	$_delete_group = D('Node')->add(array('parentid'=> $_message_group_manage, 'name' => '群发消息管理', 'm' => $this->module, 'c' => $this->module, 'a' => 'delete_group', 'display' => '0'));