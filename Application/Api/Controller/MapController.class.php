<?php 
namespace Api\Controller;
use \Common\Controller\BaseController;
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
class MapController extends BaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Document');
	}
	/**
	 * 根据内容获取关键字
	 * @author   <jason>
	 */
	public function index() {
		$field = remove_xss(safe_replace(trim($_GET['field'])));
		$modelid = intval($_GET['modelid']);
		$data = F('model_field_'.$modelid);
		$setting = string2array($data[$field]['setting']);
		$key = $_GET['api_key'] ? safe_replace($_GET['api_key']) : $setting['api_key'];
		$key = str_replace(array('/','(',')','&',';'),'',$key);
		$maptype = $_GET['maptype'] ? intval($_GET['maptype']) : ($setting['maptype'] ? $setting['maptype'] : 1);
		$defaultcity = $_GET['defaultcity'] ? $_GET['defaultcity'] : ($setting['defaultcity'] ? $setting['defaultcity'] : '北京');
		$defaultcity = remove_xss(safe_replace($defaultcity));
		$hotcitys = explode(",",$setting['hotcitys']);
		include template('map');
	}
}