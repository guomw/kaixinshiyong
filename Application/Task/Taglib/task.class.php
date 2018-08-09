<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
class task { 
    function __construct() {
        $this->db = model('task_day');
    }
    
    /**
     * 生成查询条件
     * @param array $attr
     * @return array
     */
 private function build_map($attr) {
    	$sqlMap = array();
        if (isset($attr['where']) && $attr['where']) {
            $sqlMap['_string'] = $attr['where'];
        }else{
        	$sqlMap['status'] = array('EQ',1);
        }
    	$this->where = $sqlMap;
    	return $this->where;
    }

    public function count($attr) {
    	return $this->db->where($this->build_map($attr))->count();
    }

    /* 任务列表 */
    public function lists($attr) {
    	if(!isset($this->where)) $this->where = $this->build_map($attr);    	
    	$order = (isset($attr['order']) && !empty($attr['order'])) ? $attr['order'] : 'id DESC';
        if (isset($attr['page'])) {
            $lists = $this->db->where($this->where)->page($attr['page'], $attr['limit'])->order($order)->select();
        } else {
            $lists = $this->db->where($this->where)->limit($attr['limit'])->order($order)->select();
        }
        foreach ($lists as $k=>$v) {
        	$r = model('task_records')->where(array('tid'=>$v['id'],'userid'=>cookie('_userid'),'status'=>1))->find();
        	if($r) {
        		$lists[$k]['state'] = 1;
        	}else{ 
        		$lists[$k]['state'] = 0;
        	}
        }
        return $lists;
    }
}