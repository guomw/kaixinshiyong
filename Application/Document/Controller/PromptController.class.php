<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Document\Controller;
use \Common\Controller\BaseController;
defined('MODULE_CACHE') or define('MODULE_CACHE', DATA_PATH.'caches_model/');
Class PromptController extends BaseController {
	public function index(){
		$info = I('param.');
		$msg = $info['msg'];
        $userinfo = is_login();
        $bind_tbs = get_bind_taobao($userinfo['userid']);

//        $id = (int) I('id');
//        $factory = new \Product\Factory\product($id);
//        $rs = $factory->product_info;
//        $rs['buyer_user_count'] = count($factory->buyer_list());
//        $rs['report_user_count'] = count($factory->report_list());

		include template('error');
	}
}
    
  