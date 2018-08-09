<?php
namespace Task\Library;
abstract class TaskInterface {
    protected $task_info = array();
    protected $user_info = array();
    protected $error = '';
    public function __construct($task_info) {
        $this->task_info = $task_info;
        $this->user_info = is_login();
    }

    /**
     * 获取任务记录
     */
    public function get_record() {
    	$sqlmap = array();
    	$sqlmap['tid'] = $this->task_info['id'];
    	return model('task_records')->where($sqlmap)->order("id DESC")->select();
    }
    
    /**
     * 获取任务日志
     */
    public function get_log() {
    	$sqlmap = array();
    	$sqlmap['t_id'] = $this->task_info['id'];
    	return model('task_log')->where($sqlmap)->order("id DESC")->select();
    }
    
    /**
     * 写入任务日志
     * @param type $id
     * @param type $state
     * @param type $msg
     */
    public function write_log($state, $msg) {
    	$info = array();
    	$info['t_id'] = $this->task_info['id'];
    	$info['t_status'] = (int) $state;
    	$info['msg'] = $msg;
    	$info['dateline'] = NOW_TIME;
    	$info['clientip'] = get_client_ip();
    	$info['userid'] = (!defined('IN_ADMIN')) ? cookie('_userid') : session('userid');
    	$info['is_sys'] = (!defined('IN_ADMIN')) ? 0 : 1;
    	return model('task_log')->add($info);
    }
    
    /**
     * 设置任务状态
     * @param int $status
     * @param string $cause
     * @return bool
     */
    public function set_status($status = 0, $cause = '') {
    	if($this->task_info['status'] == $status) {
    		$this->error = '当前状态非法';
    		return FALSE;
    	}
    	$info = array();
    	$info['id'] = $this->task_info['id'];
    	$info['status'] = (int) $status;
    	$info['updatetime'] = NOW_TIME;
    	$result = model('task_day')->save($info);
    	if(!$result) return FALSE;
    	//if($cause && $result) return $this->write_log($status, $cause);
    	return TRUE;
    }
    
    public function getError() {
        return $this->error;
    }
}
