<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Oauth\Controller;
class OauthController extends \Admin\Controller\InitController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('module');
    }
    
    /* 模块配置 */
    public function setting() {
        if(IS_POST) {
            $setting = $_POST['setting'];
            $setting = (is_array($setting)) ? serialize($setting) : $setting;
            $result = $this->db->where(array('module' => MODULE_NAME))->setField('setting', $setting);
            if(!$result) {
                $this->error('第三方登录配置失败');
            } else {
                $this->db->build_cache(MODULE_NAME);
                $this->success('第三方登录配置成功');
            }
        } else {
            $form = new \Common\Library\form();
            $show_validator = TRUE;
            $setting = getcache('setting', 'oauth');
            include $this->admin_tpl('oauth_setting');
        }
    }
}
