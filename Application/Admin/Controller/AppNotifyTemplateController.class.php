<?php
namespace Admin\Controller;
class AppNotifyTemplateController extends \Admin\Controller\InitController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('notify_template');
    }
    
    /* 模板配置 */
    /* 模板配置 */
    public function config() {
        $configs = $this->db->getField('identifier, content, type');
        if(submitcheck('dosubmit')) {
            $configs = $_POST['configs'];
            foreach($configs as $identifier => $config) {
                $info = array();
                $info['identifier'] = $identifier;
                if($identifier == 'member_email_activate' || $identifier == 'member_email_webcome' || $identifier == 'member_email_retpwd') {
                    $info['type'] = 'email';
                    $info['content'] = $config;
                } else {
                    $info['type'] = implode(',', $config);
                }
                $info['dateline'] = NOW_TIME;
                $this->db->update($info);
                

            }
           
            $this->success('通知模板保存成功');
        } else {
            $form = new \Common\Library\form();
            include $this->admin_tpl('app_notify_template');
        }
    }
}