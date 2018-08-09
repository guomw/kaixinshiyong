<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
 **/
class announce{
	public function __construct(){
		$this->db = D('Announce');
	}
	private function build_map($attr){
		$sqlMap = array();
		if(isset($attr['where']) && $attr['where']){
			$sqlMap['_string'] = $attr['where'];
		}
		//$sqlMap['type']= (isset($attr['type'])) ? (int)$attr['type'] : -1;
		if(isset($attr['type'])){
			$sqlMap['type'] = (int)$attr['type'];
		}
		$sqlMap['passed'] = 1;
		$this->where = $sqlMap;
		return $this->where;
	}
	public function count($attr){
		return $this->db->where($this->build_map($attr))->count();
	}
	public function lists($attr){
		$order = isset($attr['order']) && !empty($attr['order']) ? $attr['order'] : 'announceid  DESC';
		if(isset($attr['page'])){
			$lists = $this->db->where($this->build_map($attr))->page($attr['page'],$attr['limit'])->order($order)->select();
		}else{
			$lists = $this->db->where($this->build_map($attr))->limit($attr['limit'])->order($order)->select();
		}
		return $lists;
	}
}