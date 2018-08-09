<?php 
$menuid = D('Node')->add(array('parentid'=> 72, 'name' => 'WAP模块', 'm' => $this->module, 'c' => $this->module, 'a' => 'setting'));
// D('Node')->add(array('parentid' => $menuid, 'name' => '模块配置','m' => $this->module, 'c'=> $this->module, 'a'=> 'setting'));