<?php
/**
 *      会员中心 - 我的收藏夹
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
namespace Member\Controller;
use \Member\Controller\InitController;
if (!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH.'caches_model/');
class CollectController extends InitController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('member_favorite');
    }
    
    /* 收藏管理 */
    public function lists() {
        $sqlmap = array();
        $sqlmap['userid'] = $this->userid;
        $collect_list = $this->db->where($sqlmap)->select();
        $goods_id = array();
        foreach ($collect_list as $k => $v) {
            $goods_id[] = $v['goods_id'];
        }
        $id = array();
        $ids = array_unique($goods_id);
        $id['id'] =array("IN",$ids);
        $goods = model('product')->where($id)->select();
        $SEO=seo(0,"我的收藏");
        include template('collect_list');
    }

    /* 添加收藏 */
    public function add($goods_id = 0) {
        if (!$this->userid) {
            $this->error('请先登录，再收藏该商品');
        }
        $goods_id = (int) $goods_id;
        if(!model('product')->find($goods_id))
           $this->error('商品不存在，加入失败');
        $sqlmap             = array();
        $sqlmap['goods_id'] = $goods_id;
        $sqlmap['userid']  = $this->userid;
        if ($this->db->where($sqlmap)->count()) {
            $this->error('您已收藏过本商品！');
        }
        $sqlmap['dateline'] = NOW_TIME;
        $this->db->add($sqlmap);
        $this->success('商品收藏成功');
    }
    
    /* 收藏删除 */
    public function delete() {
        $id = $_GET['id'];
        if (empty($id)) {
            showmessage('参数错误');
        }
        $sqlmap = array();
        $sqlmap['id'] = array("EQ", $id);
        $sqlmap['userid'] = $this->userid;
        $rs = $this->db->where($sqlmap)->delete();
        if (!$rs) {
          
             $this->error('收藏记录删除失败');
        } else {
           
              $this->success('指定收藏删除成功');
        }
    }
}