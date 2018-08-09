<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
class commission { 
    function __construct() {
        $this->db = model('commission');
        $this->categorys = getcache('product_category', 'commons');
        $this->urlrules = C('URL_RULE');
    }
    
    /**
     * 生成查询条件
     * @param array $attr
     * @return array
     */
    public function build_map($attr) {
        $where = array();        
        //缩略图
        if (isset($attr['thumb'])) {
            if ($attr['thumb']) {
                $where['thumb'] = array("NEQ", "");
            } else {
                $where['thumb'] = array("EQ", "");
            }
        }  

        if(isset($attr['mod']) && !empty($attr['mod'])) {
            $where['mod'] = $attr['mod'];
        }
        
       
        //审核状态
        if(isset($attr['status'])){
        	$where['status'] = array("EQ",$attr['status']);
        }else{
        	$where['status'] = array("EQ", 1);
            $where['start_time'] = array("LT", NOW_TIME);
            $where['end_time'] = array("GT", NOW_TIME);
        }
        //设置SQL where 部分
        if (isset($attr['where']) && $attr['where']) {
            $where['_string'] = $attr['where'];
        }
		
        foreach($attr as $att => $name) {
            if(in_array($att, array('bonus_price','catid', 'thumb', 'status', 'where', 'order', 'limit', 'page'))) continue;
            if (isset($this->model_field[$att]) && $this->model_field[$att]['issystem'] == 1 && $this->model_field[$att]['issearch'] == 1 && $name != '') {
                if(strpos($name, ',') === FALSE) {
                    $where[$att] = $name;
                } else {
                    $where[$att] = array("IN", $name);
                }
            }
        }
        $this->where = $where;
        return $this->where;
    }

    /**
     * 统计栏目数量
     * @author   <jason>
     */
    public function count($data) {
        if(!isset($this->where)) $this->where = $this->build_map($data);
        return $this->db->where($this->where)->count();
    }
    /**
     * 查询数据列表
     * @author   <jason>
     */
    public function lists($data) {
        if(!isset($this->where)) $this->where = $this->build_map($data);    
        $order = (isset($data['order']) && !empty($data['order'])) ? $data['order'] : 'id DESC';
        if (isset($data['page'])) {
            $ids = (array) $this->db->where($this->where)->page($data['page'], $data['limit'])->order($order)->getField('id', TRUE);
        } else {
        	$ids = (array) $this->db->where($this->where)->limit($data['limit'])->order($order)->getField('id', TRUE);
        }
        //把数据组合成以id为下标的数组集合
        if (empty($ids)) return FALSE;
        $result = array();
        foreach ($ids as $key=>$id) {
            $factory = new \Commission\Factory\commission($id);
            $r = $factory->product_info;
            $result[$id] = $r;
        }
        return $result;
    }
    
    /**
     * 调用栏目
     * @param type $attr
     */
    public function category($attr) {
        extract($attr);
        $sqlmap = array();
        if(!empty($where)){
        	$sqlmap['_string'] = $where;
        }else{
        	$catid = (empty($catid)) ? 0 : $catid;
        	if(strpos($catid, ',') === FALSE) {
        		$catid = (int) $catid;
        		$catid = ($catid < 1) ? 0 : $catid;
        		$sqlmap['parentid'] = $catid;
        	} else {
        		$sqlmap['catid'] = array("IN", $catid);
        	}
        }
        $lists = model('product_category')->where($sqlmap)->limit($limit)->order("listorder ASC")->select();
        $result = array();
        $rewrite = new \Common\Library\rewrite();
        foreach($lists as $r) {
            $r['url'] = $rewrite->category($r['catid']);
            $result[$r['catid']] = $r;
        }
        return $result;
    }

    /* 试用详情页->试用报告 [Adalways试用系统] */
    public function report($attr) {
        $sqlmap = array();
        if(isset($attr['userid']) && is_numeric($attr['userid']) && $attr['userid'] > 0) {
            $sqlmap['userid'] = $attr['userid'];
        }
        if(isset($attr['goods_id']) && is_numeric($attr['goods_id']) && $attr['goods_id'] > 0) {
            $sqlmap['goods_id'] = $attr['goods_id'];
        }
        if(isset($attr['order_id']) && is_numeric($attr['order_id']) && $attr['order_id'] > 0) {
            $sqlmap['order_id'] = $attr['order_id'];
        }
        $sqlmap['status'] = 1;
        if (isset($attr['where'])) {
            $sqlmap['_string'] = $attr['where'];
        }
        $lists = model('report')->where($sqlmap)->order("reporttime DESC")->limit($attr['limit'])->select();
        if($lists) {
            foreach ($lists as $k => $v) {
                $v['nickname'] = getUserInfo($v['userid'], 'nickname');
                $v['avatar'] = getavatar($v['userid']);
                $factory = new \Commission\Factory\product($v['goods_id']);
                $v['product'] = $factory->product_info;
                $lists[$k] = $v;
            }
        }
        return $lists;
    }
 
    /**
     * 试用报告
     * @param $attr
     {
        goods_id
        order_id
        userid

     }
     */
    public function trail_report($attr){
        $sqlmap = array();
        if(isset($attr['userid']) && !empty($attr['userid'])) {
            $sqlmap['userid'] = $attr['userid'];
        }
        if(isset($attr['goods_id']) && !empty($attr['goods_id'])) {
            $sqlmap['goods_id'] = $attr['goods_id'];
        }
        if(isset($attr['order_id']) && !empty($attr['order_id'])) {
            $sqlmap['order_id'] = $attr['order_id'];
        }        
        $sqlmap['status'] = 1;
        $result = model('trial_report')->where($sqlmap)->limit($attr['limit'])->order("id ASC")->select();
        foreach ($result as $key => $val) {
            $val['content'] = dstripslashes($val['content']);
            preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $val['content'], $matches);
            $val['albums'] = (array) $matches[3];
            $val['base_info'] = string2array($val['base_info']);
            $factory = new \Commission\Factory\product($val['goods_id']);
            $val['product'] = $factory->product_info;
            $val['nickname'] = getUserInfo($val['userid'], 'nickname');
            $val['avatar'] = getavatar($val['userid']);
            $result[$key] = $val;
        }
        return $result;
    }

    /* 买家晒单
     *
     */
    public function report_list($attr){
        $sqlmap = array();
        if(isset($attr['userid']) && !empty($attr['userid'])) {
            $sqlmap['userid'] = $attr['userid'];
        }
        if(isset($attr['goods_id']) && !empty($attr['goods_id'])) {
            $sqlmap['goods_id'] = $attr['goods_id'];
        }
        if(isset($attr['order_id']) && !empty($attr['order_id'])) {
            $sqlmap['order_id'] = $attr['order_id'];
        }        
        $sqlmap['status'] = 1;
        $result = model('report')->where($sqlmap)->limit($attr['limit'])->order("id ASC")->select();
        return $result;
    }
}