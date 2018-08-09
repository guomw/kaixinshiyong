<?php 
namespace Member\Controller;
use \Admin\Controller\InitController;
class TaskController extends InitController {
	public function _initialize() {
		parent::_initialize();
		
	}
    
	public function init(){
		$form =  new \Common\Library\form();
		include $this->admin_tpl('task_list'); 
	}
    
    
}
?>