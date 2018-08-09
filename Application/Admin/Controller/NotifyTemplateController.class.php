<?php
namespace Admin\Controller;
class NotifyTemplateController extends \Admin\Controller\InitController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('notify_template');
    }

    /* 通知模板配置 */
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
            $lists = model('notify_template')->getField('identifier, content, type');
            //缓存通知模板
            setcache('notify',$lists,'notify');
            $this->success('通知模板保存成功');
        } else {
            $form = new \Common\Library\form();
            include $this->admin_tpl('notify_template');
        }
    }

    /*阿里大鱼短信模板配置*/
    public function sms(){
         if (submitcheck('dosubmit')) {
                $info = $_POST['configs'];
                if (empty($info)) {
                    $this->error('参数错误');
                }
                foreach ($info as $k => $v) {
                if(is_array($v)) $v = array2string($v);
               }
                //检测文件写入权限
                if(!substr(base_convert(@fileperms(CONF_PATH.'sms.php'),10,8),-4)){
                    //设置当前文件权限 
                    if(!chmod(CONF_PATH.'sms.php',0777)){
                        $this->error('当前文件无权限,请手动设置文件权限');
                    }
                };
                if(file_put_contents(CONF_PATH.'sms.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>")){
                    $this->success('操作成功');
                }else{
                    $this->error('保存失败,请检查文件权限！');
                }
                
        } else{
              include $this->admin_tpl('sms_template'); 
        }

    }


    public function two_config(){
          if (submitcheck('dosubmit')) {
                $info = $_POST['configs'];
                if (empty($info)) {
                    $this->error('参数错误');
                }
                foreach ($info as $k => $v) {
                if(is_array($v)) $v = array2string($v);
               }
                @file_put_contents(CONF_PATH.'two_notice.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
                $this->success('操作成功');
        } else{
            include $this->admin_tpl('two_notify_template');

        }
    }

}