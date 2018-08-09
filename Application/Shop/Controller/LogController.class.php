<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Shop\Controller;
class LogController extends \Admin\Controller\InitController
{
    public function _initialize(){
        parent::_initialize();
        $this->db = model('shop_log');
    }

    /* 兑换记录 */
    public function index() {
        $count = $this->db->count();
        $lists = $this->db->page(PAGE, 10)->order("apply_time DESC,id DESC")->select();
        $shop_ids = array();
        foreach ($lists as $k => $v) {
           $shop = model('shop')->where(array('id'=>$v['shop_id']))->select();
            foreach ($shop as $key => $s) {
                $lists[$k]['title'] = $s['title'];
                $userinfo = getUserInfo($v['userid']);
                $lists[$k]['phone'] = $userinfo['phone']; 
                $lists[$k]['email'] = $userinfo['email']; 

            }
        }
        $pages = page($count, 10);
        include $this->admin_tpl("log_list");

       
    }


    /* 删除记录 */
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

    /* 审核订单 */
    public function check($id = 0) {
        $id = (int) $id;
        if($id < 1) $this->error('参数错误');
        $info = $this->db->find($id);
        if(!$info) {
            $this->error('该订单不存在');
        }
        if($info['status'] != 0) {
            $this->error('该订单已通过审核，请勿重复操作');
        }
        if(submitcheck('dosubmit')) {
            $data = array();
            $data['id'] = $id;
            $data['status'] = (int) $_POST['status'];
            $data['remark'] = $_POST['remark'];
            $data['complete_time'] = NOW_TIME;
            $result = $this->db->update($data);
            if(!$result) {
                $this->error('审核操作失败');
            }
            if($data['status'] == -1) {
                model('shop')->where(array('id' => $info['shop_id']))->setDec('sale_num');
            }
            $this->success('审核操作成功', 'javascript:close_dialog();');
        } else {
           include $this->admin_tpl('log_remark'); 
        }
    } 
}