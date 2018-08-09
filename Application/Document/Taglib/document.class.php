<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
class document { 
    function __construct() {
        $this->categorys = getcache('category', 'commons');
        $this->models = getcache('model', 'commons');
    }

    public function set_modelid($modelid = 0) {
        if (is_numeric($modelid) && $modelid > 0) {
            $this->modelid = (int) $modelid;
        } else {
            $this->modelid = $this->category['modelid'];
        }
        if (!$this->modelid) return FALSE;
        $this->model = $this->models[$this->modelid];
        $this->model_field = getcache('model_field_'.$this->modelid, 'model');
        $this->tableName = $this->model['tablename'];
        return TRUE;
    }

    public function buildMap($attr) {
        $where = array();        
        //栏目id条件
        if (isset($attr['catid']) && (int) $attr['catid']) {
            $catid = (int) $attr['catid'];
            $this->category = $this->categorys[$catid];
            if ($this->category['child']) {
                $catids_str = $this->category['arrchildid'];
                $pos = strpos($catids_str, ',') + 1;
                $catids_str = substr($catids_str, $pos);
                $where['catid'] = array("IN", $catids_str);
            } else {
                $where['catid'] = array("EQ", $catid);
            }
        }
        
        $this->set_modelid();
        //设置SQL where 部分
        if (isset($attr['where']) && $attr['where']) {
            $where['_string'] = $attr['where'];
        }
        //缩略图
        if (isset($attr['thumb'])) {
            if ($attr['thumb']) {
                $where['thumb'] = array("NEQ", "");
            } else {
                $where['thumb'] = array("EQ", "");
            }
        }
        foreach($attr as $att => $name) {
            if(in_array($att, array('catid', 'thumb', 'status', 'where', 'order', 'limit', 'page'))) continue;
            if (isset($this->model_field[$att]) && $this->model_field[$att]['issystem'] == 1 && $this->model_field[$att]['issearch'] == 1 && $name != '') {
                if(strpos($name, ',') === FALSE) {
                    $where[$att] = $name;
                } else {
                    $where[$att] = array("IN", $name);
                }
            }
        }
        //审核状态
        $where['status'] = array("EQ", 1);
        $this->where = $where;
        return $this->where;
    }

    /**
     * 统计栏目数量
     * @author   <jason>
     */
    public function count($data) {
        $map = $this->buildMap($data);        
        return M($this->tableName)->where($map)->count();
    }
    /**
     * 查询数据列表
     * @author   <jason>
     */
    public function lists($data) {
        $map = $this->buildMap($data);
        $order = (isset($data['order']) && !empty($data['order'])) ? $data['order'] : 'id DESC';
        if (isset($data['page'])) {
            $lists = (array) M($this->tableName)->where($map)->page($data['page'], $data['limit'])->order($order)->select();
        } else {
            $lists = (array) M($this->tableName)->where($map)->limit($data['limit'])->order($order)->select();
        }
        //把数据组合成以id为下标的数组集合
        if (empty($lists)) return FALSE;
        $result = array();
        foreach ($lists as $r) {
            $r['url'] = U('Document/Index/show', array('catid' => $r['catid'], 'id' => $r['id']));
            $result[$r['id']] = $r;
        }
        /* 查询是否读附加表 */
        if (isset($data['addfields'])) {
            $ids = array_keys($result);
            $map_data = array();
            $map_data['id'] = array("IN", $ids);
            $lists_data = M($this->tableName.'_data')->where($map_data)->select();
            if (!empty($lists_data)) {
                foreach ($lists_data as $v) {
                    if (isset($result[$v['id']])) {
                        $result[$v['id']] = array_merge($v, $result[$v['id']]);
                    }
                }
            }
        }
        return $result;
    }

    /*调用导航*/
    public function navigate($attr){
        $map = $this->buildMap($attr);
        $order = (isset($attr['order']) && !empty($attr['order'])) ? $attr['order'] : 'navid DESC';
        if (isset($attr['page'])) {
            $lists = model('navigate')->where($map)->page($attr['page'], $attr['limit'])->order($order)->select();
        } else {
            $lists = model('navigate')->where($map)->limit($attr['limit'])->order($order)->select();
        }
        return $lists;
    }
    
    /*调用幻灯图片*/
    public function turn_rund($attr){
        $order = (isset($attr['order']) && !empty($attr['order'])) ? $attr['order'] : 'id DESC';
        if (isset($attr['page'])) {
            $lists = model('focus')->where($this->buildMap($attr))->page($attr['page'], $attr['limit'])->order($order)->select();
        } else {
            $lists = model('focus')->where($this->buildMap($attr))->limit($attr['limit'])->order($order)->select();
        }
        $setting2 = getcache('setting', 'wap');
        $http_host = $_SERVER['HTTP_HOST'];
        $wap_domain = ltrim($setting2['wap_domain'], "'http://'");
        $detect = new \Wap\Library\Mobile_Detect();

        foreach ($lists as $k=>$v) {//删除已经过期的幻灯片
            if($v['endtime'] != 0 && NOW_TIME > $v['endtime']){
                unset($lists[$k]);
            }

            //手机端图片调用250的
            if ($detect->isMobile() || stripos($http_host, $wap_domain) !== FALSE || cookie('ismobile') == 1) {
                $lists[$k]['image'] = img2thumb($v['image'],'m');
            }

        }



        return $lists;
    }

    /*调用首页推荐品牌*/
    public function brand($attr){
        $order = (isset($attr['order']) && !empty($attr['order'])) ? $attr['order'] : 'id DESC';
        if (isset($attr['page'])) {
            $lists = model('brand')->where($this->buildMap())->page($attr['page'], $attr['limit'])->order($order)->select();
        } else {
            $lists = model('brand')->where($this->buildMap())->limit($attr['limit'])->order($order)->select();
        }
        return $lists;
    }
    /*关键字调用*/
    public function keyword($attr){
        $order = (isset($attr['order']) && !empty($attr['order'])) ? $attr['order'] : 'keywordid DESC';
        if (isset($attr['page'])) {
            $lists = model('keyword_hot')->where($this->buildMap())->page($attr['page'], $attr['limit'])->order($order)->select();
        } else {
            $lists = model('keyword_hot')->where($this->buildMap())->limit($attr['limit'])->order($order)->select();
        }
        foreach ($lists as $k=>$v) {
            if($v['type'] == 0){
                $lists[$k]['url'] = __APP__."?m=search&keyword=".$v['name'];
            }else{
                $lists[$k]['url'] = $v['address'];
            }
        }
        return $lists;
    }
    /**
     * 调用栏目
     * @author   <jason>
     */
    public function category($data) {
        $catid = (int) $data['catid'];
        $map = array();
        $map['ismenu'] = 1;
        if (isset($data['catid'])) {
            $map['parentid'] = $catid;
        }
        if (isset($data['type'])) {
            $map['type'] = (int) $data['type'];
        }
        $order = ($data['order']) ? $data['order'] : 'listorder ASC';
        $lists = D('Category')->where($map)->limit($data['limit'])->order($order)->select();
        foreach ($lists as $key => $value) {
            $value['url'] = U('Document/Index/lists', array('catid' => $value['catid']));
            $lists[$key] = $value;
        }
        return $lists;
    }

    /**
     * 推荐位
     * @author   <jason>
     */
    public function position($data) {
        $sql = '';
        $array = array();
        $posid = intval($data['posid']);
        $order = $data['order'];
        $thumb = (empty($data['thumb']) || intval($data['thumb']) == 0) ? 0 : 1;
        $catid = (empty($data['catid']) || $data['catid'] == 0) ? '' : intval($data['catid']);
        if($catid) $this->category = $this->categorys[$catid];

        $sqlMap = array();
        if($catid && $this->category['child']) {
            $catids_str = $this->category['arrchildid'];
            $pos = strpos($catids_str,',')+1;
            $catids_str = substr($catids_str, $pos);
            $sqlMap['catid'] = array('IN', $catids_str);
        }  elseif($catid && !$this->category['child']) {
            $sqlMap['catid'] = $catid;
        }
        if($thumb) {
            $sqlMap['thumb'] = '1';
        } 
        if(isset($data['where'])) {
            $sqlMap['_string'] = $data['where'];
        }
        if(isset($data['expiration']) && $data['expiration']==1) {
            $sqlMap['expiration'] = array(array("EQ", 0), array("EGT", NOW_TIME), 'OR');
        }
        $sqlMap['posid'] = $posid;

        $pos_arr = M('PositionData')->where($sqlMap)->limit($data['limit'])->order($order)->select();
        if(!empty($pos_arr)) {
            foreach ($pos_arr as $info) {
                $key = $info['catid'].'-'.$info['id'];
                $array[$key] = string2array($info['data']);
                $array[$key]['url'] = go($info['catid'],$info['id']);
                $array[$key]['id'] = $info['id'];
                $array[$key]['catid'] = $info['catid'];
                $array[$key]['listorder'] = $info['listorder'];
            }
        }
        return $array;
    }

    /**
     * 相关文章
     * @author   <jason>
     */
    public function relation($data) {
        $catid = intval($data['catid']);
        $modelid = intval($data['modelid']);
        $this->category = $this->categorys[$catid];
        $this->set_modelid();
        if(!is_array($this->category)) return false;

        $map = $array = $array = array();
        if($data['relation']) {
            $relations = explode('|',trim($data['relation'],'|'));
            $relations = array_diff($relations, array(null));
            $relations = implode(',',$relations);
            $map['id'] = array("IN", $relations);
            $array = M($this->tableName)->where($map)->select();
        } elseif($data['keywords']) {
            $data['keywords'] = trim($data['keywords']);
            $keywords = str_replace('%', '',$data['keywords']);
            $keywords_arr = explode(' ',$keywords);
            $map['id'] = array("NEQ", $data['id']);
            $map['catid'] = $catid;
            $map['keywords'] = array("IN", $keywords_arr);
            $array = M($this->tableName)->where($map)->select();
        }
        if (is_array($array) && !empty($array)) {
            foreach ($array as $r) {
                $result[$r['id']] = $r;
            }
        }
        if($data['id']) unset($result[$data['id']]);
        return $result;
    }

    /**
     * 点击排行榜
     * @author   <jason>
     */
    public function hits($data) {
        $cacheName = md5(serialize($data));
        if (S($cacheName)) return S($cacheName);
        $catid = intval($data['catid']);
        $this->category = $this->categorys[$catid];
        if(!is_array($this->category)) return false;        
        
        $hitsid = 'c-'.$this->category['modelid'].'-%';
        $map = array();
        $map['hitsid'] = array("LIKE", $hitsid);
        // 调用多少天之内的数据
        if(isset($data['day'])) {
            $updatetime = SYS_TIME - intval($data['day'])*86400;
            $map['updatetime'] = array("GT", $updatetime);
        }
        // 查询出所有递归栏目
        if($this->category['child']) {
            $catids_str = $this->category['arrchildid'];
            $pos = strpos($catids_str,',')+1;
            $catids_str = substr($catids_str, $pos);
            $map['catid'] = array("IN", explode(",", $catids_str));
        } else {
            $map['catid'] = $catid;
        }
        $ids = $hits = array();
        $hitsinfo = D('Hits')->where($map)->limit($data['limit'])->order($data['order'])->select();
        foreach ($hitsinfo as $r) {
            $pos = strpos($r['hitsid'],'-',2) + 1;
            $ids[] = $id = substr($r['hitsid'],$pos);
            $hits[$id] = $r;
        }
        
        $this->set_modelid();
        $result = array();
        $condition = array();
        $condition['id'] = array("IN", $ids);
        if ($data['industry']) {
            $condition['industry'] = array("IN", explode(",", $data['industry']));
        }
        $array = D($this->tableName)->where($condition)->order("FIELD(id,".implode(',', $ids).")")->select();
        if (is_array($array) && !empty($array)) {
            foreach ($array as $v) {
                $v = array_merge($v, $hits[$v['id']]);
                $result[$v['id']]  = $v;
            }
        }
        return $result;
    }
}