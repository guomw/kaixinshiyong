<?php 
$sqlquery = <<<EOT
DROP TABLE IF EXISTS `prefix_gbook`;
CREATE TABLE `prefix_gbook` (
`id`  mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cname`  varchar(250) NOT NULL DEFAULT '' ,
`linkname`  varchar(250) NOT NULL DEFAULT '' ,
`email`  varchar(250) NOT NULL DEFAULT '' ,
`mobile`  varchar(250) NOT NULL DEFAULT '' ,
`address`  varchar(250) NOT NULL DEFAULT '' ,
`content`  text  NOT NULL ,
`extra`  text  NOT NULL ,
`dateline`  int(10) NOT NULL ,
`ip`  char(15) NOT NULL DEFAULT '' ,
PRIMARY KEY (`id`)
)
TYPE=MyISAM;
EOT;
$menuid = D('Node')->add(array('parentid'=> 72, 'name' => '留言薄', 'm' => $this->module, 'c' => $this->module, 'a' => 'manage'));
D('Node')->add(array('parentid' => $menuid, 'name' => '模块配置','m' => $this->module, 'c'=> $this->module, 'a'=> 'setting'));
D('Node')->add(array('parentid' => $menuid, 'name' => '留言编辑','m' => $this->module, 'c'=> $this->module, 'a'=> 'edit', 'display' => 0));
D('Node')->add(array('parentid' => $menuid, 'name' => '留言删除','m' => $this->module, 'c'=> $this->module, 'a'=> 'delete', 'display' => 0));