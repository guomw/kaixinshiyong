<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Model;
use \Common\Library\rewrite;
use Think\Model;
if (!defined('MODULE_CACHE')) {
	define('MODULE_CACHE', DATA_PATH.'caches_model/');
}
class ProductModel extends Model {
    protected $_info = array();
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

	public function detail($id) {
		$system_info = $this->find($id);
		if(!$system_info) {
			$this->error = '您查看的活动不存在';
			return FALSE;
		}

        //手机端图片调用150的
        $setting2 = getcache('setting', 'wap');
        $http_host = $_SERVER['HTTP_HOST'];
        $wap_domain = ltrim($setting2['wap_domain'], "'http://'");
        $detect = new \Wap\Library\Mobile_Detect();
        if ($detect->isMobile() || stripos($http_host, $wap_domain) !== FALSE || cookie('ismobile') == 1) {
            $system_info['thumb'] = img2thumb($system_info['thumb'],'s');
        }else{
            $system_info['thumb'] = img2thumb($system_info['thumb'],'m');
        }


		$rewrite = new rewrite();
		$system_info['url'] = $rewrite->show($id, $system_info['mod']);
		$model_info = model($this->getProTableName($system_info['mod']))->getById($id);
		if (!$model_info)	{
			$this->error = '该商品信息有误，请查看其它商品';
			return FALSE;
		}

        $putoutclass = APP_PATH.'Product'.DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.$system_info['mod'].'_output.class.php';
        $classname = $system_info['mod'].'_output';
        require_cache($putoutclass);
        if(class_exists($classname)) {
			$output = new $classname();
			$model_info = $output->get($model_info);
        }
		if($model_info) {
			$system_info = array_merge($system_info, $model_info);
		}
		return $system_info;
	}

	public function getProTableName($mod){
		return 'product_'.$mod;
	}

	public function update($data, $iscreate = TRUE) {
		$info = array();
		if ($iscreate === TRUE) $info['system'] = $this->create($data);
		if (empty($info['system']) || empty($data['mod'])) {
			$this->error = $this->getError() ? $this->getError() : '数据异常';
			return false;
		}
		$inputclass = APP_PATH.'Product'.DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.$data['mod'].'_input.class.php';
		require_cache($inputclass);
		$classname = $data['mod'].'_input';
		if(class_exists($classname)) {
			$Input = new $classname();
			$info['model'] = $Input->get($data);
			if($Input->getError()) {
				$this->error = $Input->getError();
				return false;
			}
		}

		if($data['start_time']) {
			$info['system']['start_time'] = strtotime($data['start_time']);
            $info['system']['end_time'] = $info['system']['start_time'] + 86400 * $info['model']['activity_days'];
		}
		if($data['mod'] == 'postal' && !isset($data['id'])){
			$info['system']['status'] = -2;
		}
		$info['system']['thumb'] = $info['model']['thumb'];
		$info['system']['goods_price'] = $info['model']['goods_price'];
		$this->_info = $info;
		return parent::update($this->_info['system'], $iscreate);
	}

	/* 获取是否后台 */
	public function getIsSystem() {
		return (defined('IN_ADMIN')) ? 1 : 0;
	}

	/* 插入成功 */
	public function _after_insert($data, $options) {
		$tablename = 'product_'.$this->_info['system']['mod'];
		$this->_info['model']['id'] = $data['id'];
		return model($tablename)->add($this->_info['model']);
	}

	/* 更新成功 */
	public function _after_update($data, $options) {
		$tablename = 'product_'.$this->_info['system']['mod'];
		return model($tablename)->save($this->_info['model']);
	}

	/* 删除产品 */
	public function pro_delete($id){
		$id = (int) $id;
		if($id <  1) {
			$this->error = '参数错误';
			return false;
		}
		$mod = $this->getFieldById($id, 'mod');
		$result = $this->delete($id);
		if($result) {
			$tablename = 'product_'.$mod;
			model($tablename)->where(array('id' => $id))->delete();
		}
		return TRUE;
	}
    
    private function geturlrule($id = 0, $page = 0, $pos = 'show') {
        $urlrules = C('URL_RULE');
        $id = (int) $id;
        $page = (int) $page;
        $urlrule = $urlrules[$pos];
        $r = array(
            '{id}' => $id,
            '{page}' => $page,
        );
        return str_replace(array_keys($r), $r, $str);
    }
}