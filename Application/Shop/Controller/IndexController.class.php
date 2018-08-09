<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Shop\Controller;
class IndexController extends \Common\Controller\BaseController
{
    public function _initialize(){
        parent::_initialize();
        $this->db = model('shop');
    }

    public function index(){
        $map = array();
        $map['end_time'] = array('GT',NOW_TIME);
        $shop = $this->db->where($map)->order('dateline DESC')->select(); 
        $SEO = seo(0,"积分商城");
        unset($map);
        $map['type'] = array('NEQ','reg');
        $task = model('task')->where($map)->limit(4)->order('sort ASC')->select();
        include template("index");
    }

    public function show(){
        $id = I('id', 0, 'intval');
        if ($id < 1) {
            $this->error('参数错误');
        }
        $shop = $this->db->find($id);
        if(!$shop) $this->error('您查看的商品不存在');
        $specs = explode(',', $shop['spec']);
        $SEO = seo(0, $shop['title']);
        include template('show');
    } 
}