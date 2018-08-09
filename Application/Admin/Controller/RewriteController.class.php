<?php
namespace Admin\Controller;
class RewriteController extends \Admin\Controller\InitController
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('setting');
    }
    public function index() {
    	$sqlmap = array('key' => 'rewrite_rule');
    	$setting = $this->db->where($sqlmap)->getField('value');
    	$setting = (is_string($setting) && $setting) ? string2array($setting) : $setting;
    	if(IS_POST) {
    		$rewrite_rule = $_POST['setting']['rewrite_rule'];
    		if($rewrite_rule && is_array($rewrite_rule)) $rewrite_rule = array2string($rewrite_rule);
    		$this->db->where(array('key' => 'rewrite_rule'))->setField('value', $rewrite_rule);
    		$this->db->build_cache();
    		$this->success('伪静态规则配置成功');
    	} else {
    		include $this->admin_tpl('rewrite_setting');
    	}
    }
}
