<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Library;
abstract class OrderInterface {
    protected $order_info = array();
    protected $product_info = array();
    protected $error = '';
    public function __construct($order_info, $product_info) {
        $this->order_info = $order_info;
        $this->product_info = $product_info;
        $this->userid = cookie('_userid');
    }
    
    /* 日志 */
    public function get_log() {
        $sqlmap = array();
        $sqlmap['order_id'] = $this->order_info['id'];
        return model('order_log')->where($sqlmap)->order("id DESC")->select();
    }
    
    /**
     * 写入订单操作日志
     * @param   string  $cause 操作理由
     * @return  bool
     */
    public function write_log($cause = '',$userid='') {
        if ($userid && $this->order_info['source'] == 1) {
            $user_id = $userid;
        }else{
            $user_id = $this->userid;
        }
        $log = array();
        $log['order_id'] = $this->order_info['id'];
        $log['cause'] = $cause;
        $log['inputtime'] = NOW_TIME;
        $log['ip'] = get_client_ip();
        $log['buyer_id'] = $this->order_info['buyer_id'];
        $log['seller_id'] = $this->order_info['seller_id'];
        $log['userid'] = $user_id;
        $log['issystem'] = (!defined('IN_ADMIN')) ? 0 : 1;
        return model('order_log')->update($log);        
    }
    
    /**
     * 订单删除
     */
    public function delete() {
        if($this->order_info['status'] != 0) {
            $this->error = '该订单不是已关闭状态，禁止删除。';
            return FALSE;
        }
        model('order')->delete($this->order_info['id']);
        model('order_log')->where(array('order_id' => $this->order_info['id']))->delete();
        return TRUE;
    }
    
    /* 通过 */
    abstract public function pass();
    /* 拒绝 */
    abstract public function refuse();
    /* 撤销 */
    abstract public function cancel();
    
    public function getError() {
        return $this->error;
    }
}
