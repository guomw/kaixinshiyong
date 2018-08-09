<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
//namespace Common\Model;
//use Think\Model;
define('MODULE_FIELDS', MODULE_PATH.'Fields/');
if (!defined('MODULE_CACHE')) {
	define('MODULE_CACHE', DATA_PATH.'caches_model/');
}
class ProductModel extends \Common\Model\SystemModel {
//class ProductModel extends SystemModel {
	protected  $_info = array();
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
//		array('name', 'require', '模型名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//		array('tablename', 'require', '模型表名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//		array('tablename', '', '模型表名已存在', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
	);

	protected $_auto = array (
		//array('userid', 'getUserId', MODEL::MODEL_INSERT, 'callback'),
//		array('issystem', 'getIsSystem', MODEL::MODEL_INSERT, 'callback'),
		array('inputtime', NOW_TIME, self::MODEL_BOTH, 'string'),
		array('updatetime', NOW_TIME, self::MODEL_BOTH, 'string'),
//		array('setting', 'array2string', self::MODEL_BOTH, 'function'),
	);

	/* 获取用户UID */
	public function getUserId() {
		if(defined('IN_ADMIN')) {
			return session('userid');
		} else {
			return cookie('_userid');
		}
	}

	public function update($data, $iscreate = TRUE) {
		$this->_info = $data;
		parent::update($data, $iscreate);
	}

	/* 获取是否后台 */
	public function getIsSystem() {
		return (defined('IN_ADMIN')) ? 1 : 0;
	}

	/* 插入成功 */
	public function _after_insert($data, $options) {
//		var_dump($this->_info);
//		var_dump($data);
//		var_dump($options);
//		die;
//		setcache('data', $data, 'commons');
//		setcache('options', $options, 'commons');
	}

	/* 更新成功 */
	public function _after_update($data, $options){
//		var_dump($options);
//		var_dump($data);
	}

	/* 删除成功 */
	public function _after_delete($data, $options){
//		var_dump($options);
//		var_dump($data);
	}
}