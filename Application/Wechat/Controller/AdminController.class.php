<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Wechat\Controller;
class AdminController extends \Admin\Controller\InitController
{
	public function _initialize() {
		parent::_initialize();
        $this->db = D('Module');
	}

	public function setting() {
        $clicks = array('wechat_order_wait_check', 'wechat_order_wait_fill', 'wechat_order_wait_trial_report', 'wechat_order_wait_report', 'wechat_order_wait_appeal', 'wechat_account_balance', 'wechat_account_cash', 'wechat_account_point', 'wechat_account_info', 'wechat_account_bind', 'wechat_product_rebate', 'wechat_product_trial', 'wechat_product_postal', 'wechat_product_commend', 'wechat_product_news');
        if (submitcheck('dosubmit')) {
            $buttons = $_POST['setting']['menu']['button'];
            if($buttons) {
                foreach ($buttons as $key => $button) {
                    if(isset($button['sub_button'])) {
                        unset($buttons[$key]['key'], $buttons[$key]['type']);
                        foreach ($button['sub_button'] as $k => $v) {
                            if($v['type'] == 'view') {
                                $buttons[$key]['sub_button'][$k]['url'] = $v['key'];
                                unset($buttons[$key]['sub_button'][$k]['key']);
                            }
                        }
                    }
                    if($button['type'] == 'view') {
                        $buttons[$key]['url'] = $button['key'];
                        unset($buttons[$key]['key']);
                    }
                }
            }
			$_POST['setting']['menu']['button'] = $buttons;
            $buttons = $setting['menu']['button'];
            $setting = serialize($_POST['setting']);
			$this->db->where(array('module' => MODULE_NAME))->setField('setting', $setting);
			setcache('setting', $_POST['setting'], strtolower(MODULE_NAME));
/*            与微信服务器通信 配置自定义菜单*/
            $setting = getcache('setting', 'wechat');
            $buttons = $setting['menu']['button'];
            $options = $setting['options'];
            if($buttons) {
                foreach ($buttons as $key => $value) {

                    if($value['status'] == 0) unset($buttons[$key]);
                    if($value['sub_button']) {
                        foreach ($value['sub_button'] as $k => $v) {
                            if($v['status'] == 0) {
                                array_splice($buttons[$key]['sub_button'],$k,1);

                            }
                            unset($buttons[$key]['sub_button'][$k]['status']);
                        }
                    }
                    unset($buttons[$key]['status']);
                }
            }

            if($buttons) {
                $buttons = array('button' => $buttons);
            }

            $wechat = new \Wechat\Library\Wechat($options);
            $access_token = $wechat->checkAuth();
            $status = $wechat->createMenu($buttons);
            if($status)
                $this->success('操作成功', U('setting'));
            
            else
                $this->error('配置通信失败,请检查相关配置', U('setting'));

			
        } else {
			$setting = $this->db->getFieldByModule(MODULE_NAME, 'setting');
			$setting = unserialize($setting);
            include $this->admin_tpl('setting');
        }
	}
}