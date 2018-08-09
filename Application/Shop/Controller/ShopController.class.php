<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Shop\Controller;
class ShopController extends \Admin\Controller\InitController
{
    public function _initialize(){
        parent::_initialize();
        $this->db = model('shop');
    }

    /* 管理商品 */
    public function index() {
        $count = $this->db->count();
        $lists = $this->db->page(PAGE, 10)->order("dateline DESC,id DESC")->select();
        $pages = page($count, 10);
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add',array('t'=>1)).'\', title:\''.L('添加积分商品').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('添加积分商品'));
        include $this->admin_tpl("shop_list");
    }

    /* 添加商品 */
    public function add(){
        if (IS_POST) {
            $info = I('post.');
            $info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
            $result = $this->db->update($info);
            if ($result) {
                 $this->success('操作成功','javascript:close_dialog();', 1);
            } else {
                 $this->error('操作失败');
            }
        } else {
            $show_header = false;
            $form = new \Common\Library\form(); 
            include $this->admin_tpl("shop_add");
        }
    }

    /* 编辑商品 */
    public function edit($id = 0) {
        $id = (int) $id;
        if($id < 1) $this->error('参数错误');
        $rs = $this->db->find($id);
        if(!$rs) $this->error('您查看的记录不存在');
        if (IS_POST) {
            $info = I('post.');
            $info['id'] = $id;
            $info['desc'] = stripslashes($info['desc']);
            $info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
            $result = $this->db->update($info);
            if ($result) {
                 $this->success('操作成功','javascript:close_dialog();', 1);
            }else{
                 $this->error('操作失败');
            }
        }else{            
            $show_header = false;
            $form = new \Common\Library\form(); 
            include $this->admin_tpl("shop_edit");
        }
    }

    /* 删除商品 */
    public function delete() {
        $ids = (array) I('ids');
            if (empty($ids)) {
                $this->error('参数错误');
            }
            foreach ($ids as $id) {
                $id = (int) $id;
              
                $this->db->where(array('id' => $id))->delete();
               
            }
            $this->success('操作成功');
    }


    /*兑换记录表*/
    public function log(){
        $id = (int) I('id');
        if($id < 1) $this->error('参数错误');
        $count = model('shop_log')->where(array('shop_id'=>$id))->count();
        $lists = model('shop_log')->page(PAGE, 10)->where(array('shop_id'=>$id))->order("apply_time DESC,id DESC")->select();
        $shop_ids = array();
        foreach ($lists as $k => $v) {
            $shop_ids[] = $v['shop_id'];
        }
        $sqlmap['id'] = array('IN',$shop_ids);
        $shop = model('shop')->where($sqlmap)->select();
      
        foreach ($shop as $key => $s) {
            $lists[$key]['title'] = $s['title'];
        }
        $pages = page($count, 10);
         include $this->admin_tpl("log_show");
        
    }
}