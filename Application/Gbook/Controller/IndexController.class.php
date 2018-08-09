<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Gbook\Controller;
use \Common\Controller\BaseController;
/* 留言薄前台业务 */
class IndexController extends BaseController
{
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Gbook');
	}

	/* 留言提交 */
	public function add() {
		if (isset($_POST['dosubmit'])) {
			$info = I('post.');
			$result = $this->db->update($info);
			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->success('留言提交成功');
		} else {
			$this->error('请勿非法访问');
		}
	}
	/* 后置操作 */
	public function _after_add() {
		$setting = getcache('gbook_setting', 'module');
		if (!$setting) {
			return FALSE;
		}
		if($setting['gbook_notify_email'] && isemail($setting['gbook_notify_email'])) {
			helpers('mail');
			sendmail($setting['gbook_notify_email'], '您的网站有新的客户留言', '您的网站有新的客户留言');
		}
		if ($setting['gbook_notify_sms'] && is_mobile($setting['gbook_notify_sms']) && module_exists('Sms')) {
			$SmsApi = new \Sms\Api\SmsApi();
			$sms_result = $SmsApi->send($setting['gbook_notify_sms'], '您的网站在 '.dgmdate(NOW_TIME).' 有新的客户留言');
		}
		return TRUE;
	}
}