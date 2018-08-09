<?php
namespace Commission\Library;
abstract class CommissionInterface {
    protected $product_info = array();
    protected $user_info = array();
    protected $error = '';
    public function __construct($product_info) {
        $this->product_info = $product_info;
        $this->user_info = is_login();
    }
    
    /**
     * 获取产品日志
     */
    public function get_log() {
        $sqlmap = array();
        $sqlmap['p_id'] = $this->product_info['id'];
        return model('commission_log')->where($sqlmap)->order("id DESC")->select();
    }
    
    /**
     * 写入产品日志
     * @param type $id
     * @param type $state
     * @param type $msg
     */
    public function write_log($state, $msg) {
        $info = array();
        $info['p_id'] = $this->product_info['id'];
        $info['p_state'] = (int) $state;
        $info['msg'] = $msg;
        $info['dateline'] = NOW_TIME;
        $info['clientip'] = get_client_ip();
        $info['uid'] = (!defined('IN_ADMIN')) ? cookie('_userid') : session('userid');
        $info['is_sys'] = (!defined('IN_ADMIN')) ? 0 : 1;
        return model('commission_log')->add($info);
    }

    /**
     * 设置产品状态
     * @param int $status
     * @param string $cause
     * @return bool
     */
    public function set_status($status = 0, $cause = '') {
        if($this->product_info['status'] == $status) {
            $this->error = '当前状态非法';
            return FALSE;
        }
        $info = array();
        $info['id'] = $this->product_info['id'];
        $info['status'] = (int) $status;
        $info['updatetime'] = NOW_TIME;
        $result = model('commission')->save($info);
        if($result === false) return FALSE;
        if($cause && $result) return $this->write_log($status, $cause);
        return TRUE;
    }    
    
    public function getError() {
        return $this->error;
    }
}
