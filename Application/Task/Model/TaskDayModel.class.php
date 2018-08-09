<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Task\Model;
use \Common\Library\rewrite;
use Think\Model;
if (!defined('MODULE_CACHE')) {
	define('MODULE_CACHE', DATA_PATH.'caches_model/');
}
class TaskDayModel extends Model {
    protected $_info = array();
    protected $tableName = '';
	/*自动验证*/
	protected $_validate = array (
//		array('title', 'require', '商品标题为必填项', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//		array('catid', 'require', '商品分类为必填项', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//		array('mod', 'require', '活动类型为必填项', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//		array('company_id', 'require', '所属商家为必选项', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array (
		array('userid', 'getUserId', MODEL::MODEL_INSERT, 'callback'),
		array('issystem', 'getIsSystem', MODEL::MODEL_INSERT, 'callback'),
		array('inputtime', NOW_TIME, self::MODEL_INSERT, 'string'),
		array('updatetime', NOW_TIME, self::MODEL_UPDATE, 'string'),
	);

	/* 获取用户UID */
	public function getUserId() {
		if(defined('IN_ADMIN')) {
			return session('userid');
		} else {
			return cookie('_userid');
		}
	}

	/* 获取是否后台 */
	public function getIsSystem() {
		return (defined('IN_ADMIN')) ? 1 : 0;
	}
	
	public function update($data, $iscreate = TRUE) {
		$info = array();
		if ($iscreate === TRUE) $info['system'] = $this->create($data);
		if (empty($info['system'])) {
			$this->error = $this->getError() ? $this->getError() : '数据异常';
			return false;
		}
		$inputclass = APP_PATH.'Task'.DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.'task_input.class.php';
		require_cache($inputclass);
		$classname = 'task_input';
		if(class_exists($classname)) {
			$Input = new $classname();
			$info['model'] = $Input->get($data);
			if($Input->getError()) {
				$this->error = $Input->getError();
				return false;
			}
		}
		$info['system']['goods_albums'] = $info['model']['goods_albums'];
		$info['system']['totalmoney'] = $info['model']['goods_price'] * $info['model']['goods_number']; 
		$this->_info = $info;
		return parent::update($this->_info['system'], $iscreate);
	}
}