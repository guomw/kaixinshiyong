<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
use Think\Controller;
class EmptyController extends Controller {
	public function index() {
		$this->error('控制器不存在');
	}
}