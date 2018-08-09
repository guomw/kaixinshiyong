<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class SeoController extends InitController {
    public function _initialize() {
    	parent::_initialize();
        $this->db = D('Setting');
       
    }
    /* 1.1 SEO基本设置  [Adalways试用系统] */
    public function init() {
        $setting = $this->db->getField('key,value');
        $setting['score_seo'] = string2array($setting['score_seo']);
        $setting['activity_seo'] = string2array($setting['activity_seo']);
        $setting['help_seo'] = string2array($setting['help_seo']);
        $setting['rebate_seo'] = string2array($setting['rebate_seo']);
        $setting['trial_seo'] = string2array($setting['trial_seo']);
        $setting['postal_seo'] = string2array($setting['postal_seo']);
        $setting['shai_seo'] = string2array($setting['shai_seo']);
        $setting['report_seo'] = string2array($setting['report_seo']);
        $setting['rebate_show'] = string2array($setting['rebate_show']);
        $setting['trial_show'] = string2array($setting['trial_show']);
        $setting['postal_show'] = string2array($setting['postal_show']);
        $setting['all_seo'] = string2array($setting['all_seo']);
        $setting['red_seo'] = string2array($setting['red_seo']);
        $form = new \Common\Library\form();
        $style_list = template_list(0);
        foreach ($style_list as $k=>$v) {
            $style_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
            unset($style_list[$k]);
        }
		include $this->admin_tpl('seo_set');
    }



    public function update(){
        if (submitcheck('dosubmit')) {

            $info = $_POST['setting'];
            if (empty($info)) {
                $this->error('参数错误');
            }
            foreach ($info as $k => $v) {
                if(is_array($v)) $v = array2string($v);
                $this->db->where(array('key' => $k))->setField('value', $v);
            }
            $info = $this->db->getField('key, value', TRUE);
            @file_put_contents(CONF_PATH.'setting.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
            $this->success('操作成功');
        } else {
            $this->error('请勿非法提交');
        }
    }
  
}