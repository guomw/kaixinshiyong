<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Commission\Model;
use \Common\Library\rewrite;
use Think\Model;
if (!defined('MODULE_CACHE')) {
	define('MODULE_CACHE', DATA_PATH.'caches_model/');
}
class CommissionModel extends Model {
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
            $rewrite = new \Common\Library\crewrite();
            $system_info['url'] = $rewrite->show($id);
           
            return $system_info;
	}


	/* 获取是否后台 */
	public function getIsSystem() {
		return (defined('IN_ADMIN')) ? 1 : 0;
	}


	/* 删除产品 */
	public function pro_delete($id){
		$id = (int) $id;
		if($id <  1) {
			$this->error = '参数错误';
			return false;
		}
		$result = $this->delete($id);
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