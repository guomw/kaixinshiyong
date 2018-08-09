<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
class link
{
    function __construct() {
        $this->db = D('Link');
    }

    private function build_map($attr) {
    	$sqlMap = array();
        if (isset($attr['where']) && $attr['where']) {
            $sqlMap['_string'] = $attr['where'];
        }
    	$attr['linktype'] = (isset($attr['linktype'])) ? (int) $attr['linktype'] : -1;
    	if ($attr['linktype'] == 1) {
    		$sqlMap['image'] = array("NEQ", "");
    	} elseif($attr['linktype'] == 0) {
    		$sqlMap['image'] = array("EQ", "");
    	}
    	$sqlMap['passed'] = 1;
    	$this->where = $sqlMap;
    	return $this->where;
    }

    public function count($attr) {
    	return $this->db->where($this->build_map())->count();
    }

    /* 友情链接列表 */
    public function lists($attr) {
    	$order = (isset($attr['order']) && !empty($attr['order'])) ? $attr['order'] : 'linkid DESC';
        if (isset($attr['page'])) {
            $lists = $this->db->where($this->build_map())->page($attr['page'], $attr['limit'])->order($order)->select();
        } else {
            $lists = $this->db->where($this->build_map())->limit($attr['limit'])->order($order)->select();
        }
        return $lists;
    }
}