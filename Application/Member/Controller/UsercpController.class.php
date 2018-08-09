<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use \Member\Controller\InitController;
class UsercpController extends InitController {
	public $userid = '';
	public $userinfo = '';
	public function _initialize() {
		parent::_initialize();
	}

	public function _empty() {
		$this->error(L('_CONTROLLER_NOT_EXIST_'));
	}

	public function index() {
		redirect(U('Profile/index'));
		// include template('index');
	}

   //异步获取会员信息
   public function userinfo() {
     $data= is_login();
     if(!$data['nickname']){
     	if($data['email']){
     		$data['nickname'] = substr_replace($data['email'],'***',3,8);
     	}else{		
     		$data['nickname'] = substr_replace($data['phone'],'***',5,6);
     	} 
     }
     echo json_encode($data);

   }


}