<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class SettingController extends InitController {
    public function _initialize() {
    	parent::_initialize();
        $this->db = D('Setting');
        $this->keyword_db = D('Keyword');
        $this->groupConfig = array(
            'base'   => '基本设置',
            'safe'   => '安全设置',
            'upload' => '上传设置',
            'email'  => '邮箱设置',
            'other'  => '其它设置',
        );
        $this->groupid = getgpc('groupid'); 
        $this->groupid = (isset($this->groupConfig[$this->groupid])) ? $this->groupid : 'base';
    }
    /* 1.1 站点基本设置  [Adalways试用系统] */
    public function init() {
        $setting = $this->db->getField('key,value');
        $form = new \Common\Library\form();
        $style_list = template_list(0);
        foreach ($style_list as $k=>$v) {
            $style_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
            unset($style_list[$k]);
        }
		include $this->admin_tpl('setting');
    }
    /* 1.2 联系人设置   [Adalways试用系统] */
    public function contact() {
        $setting = $this->db->getField('key,value');
        $region = D('Linkage')->where(array('parentid'=>0))->select();
        if ($setting['site_contact_city']) {
             $area = D('Linkage')->where(array('parentid'=>$setting['site_contact_city']))->select();
        }
        include $this->admin_tpl('contact');
    }
    public function code(){
    	if(!empty($_FILES)){
    		$upload = new \Think\Upload();// 实例化上传类
    		$upload->maxSize  =     3145728 ;
    		$upload->exts     =     array('jpg', 'gif', 'png');
    		$upload->rootPath = SITE_PATH.'/uploadfile/code/';
    		if(!file_exists($upload->rootPath)){//不存在，则创建
    			mkdir($upload->rootPath, 0777);
    		}
    		$upload->savePath = '';
    		$upload->replace  = TRUE;
    		$upload->saveName = NOW_TIME.random(5,1).'_code';
    		$upload->autoSub = FALSE;
    		$upload->saveExt  = 'jpg';
    		$result = $upload->upload($_FILES);
    		$name = __ROOT__.'/uploadfile/code/'.$result['Filedata']['savename'];
    		if($result){
    			echo $name;exit();
    		}else{
    			exit('0');
    		}
    	}
    }
    /* 获取省市 [Adalways试用系统] */
    public function get_area(){
        $id = I('id');
        $area = D('Linkage')->where(array('parentid'=>$id))->select();
        echo json_encode($area);
    }
    /* 1.4 图片设置 [Adalways试用系统] */
    public function imgset(){
        $setting = $this->db->getField('key,value');
        include $this->admin_tpl('imgset');
    }
    /* 1.5 淘宝联盟设置 [Adalways试用系统] */
    public function taobaoset(){
        $setting = $this->db->getField('key,value');
        include $this->admin_tpl('taobaoset');
    }
    /*1.7邮箱设置*/
    public function email(){
    	$setting = $this->db->getField('key,value');
    	include $this->admin_tpl('email');
    }
	/* 1.6 关键词列表[Adalways试用系统] */
    public function shieldset(){
    	$pagecurr = max(1,I('page',0,'intval'));
    	$pagesize = 10;
    	$sqlMap = array();
    	$keyword = I('keyword');
    	if(isset($keyword) && $keyword != '搜索关键字'){
    		$sqlMap['keyword'] = array("LIKE","%$keyword%");
    	}
    	$count = $this->keyword_db->where($sqlMap)->count();
    	$keyword_lists = $this->keyword_db->where($sqlMap)->page($pagecurr,$pagesize)->select();
    	$pages = page($count,$pagesize);
    	$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('keyword_add').'\', title:\''.L('keyword_add').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('keyword_add'));
    	include $this->admin_tpl('keyword_lists');
    }
	/*关键词添加[Adalways试用系统]  */
	public function keyword_add(){
		if(IS_POST){
			$content = I('content');
			//将回车符替换成‘，‘
			$strs = str_replace("\n", ',', $content);
			$attr = explode(',',$strs);
			$sqlMap = array();
			$sqlMap['inputtime'] = NOW_TIME; 
			foreach ($attr as $k=>$v){
				$sqlMap['keyword'] = $v;
				$result = $this->keyword_db->add($sqlMap);
			}
			if($result){
				exit('1');
			}else{
				exit('0');
			}
		}else{
			include $this->admin_tpl('keyword_add');
		}
	}
	/*删除关键词[Adalways试用系统]*/
	public function  delete($ids = array()){
		$ids = (array) $ids;
		if(empty($ids)){$this->error('参数错误');}
		$result = $this->keyword_db->where(array('keywordid'=>array('in',$ids)))->delete();
		if(!$result){
			$this->error('操作失败');
		}
		$this->success('删除成功');
	}
    /**
     * 保存站点设置  [Adalways试用系统]
     * @author   <jason>
     */
    public function update() {
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
    /**
     * 字段值
     * @param  string $fieldtype 字段类型
     * @param  string $key       字段名
     * @author   <jason>
     */
    public function public_field_setting($fieldtype = 'text', $key='') {
        if (!empty($key)) {
            $data = (array) $this->setting_db->find($key);
            $data['config'] = unserialize($data['config']);
            $this->assign('data', $data);
        }
        $result = '';
        $result['field_minlength'] = 0;
        $result['field_maxlength'] = '';
        $result['setting'] = $this->fetch('_util_field_'.$fieldtype);
        $this->ajaxReturn($result);
    }
    /**
     * 添加拓展字段
     * @author   <jason>
     */
    public function field_add() {
        if (submitcheck('dosubmit')) {
            $info = $_POST;
            $result = $this->setting_db->update($info);
            if (!$result) {
                $this->error($this->stting_db->getError());
            }
            $this->success('操作成功');
        } else {
            $this->display();
        }
    }
    /**
     * 编辑拓展字段
     * @param  string $key 字段名
     * @author   <jason>
     */
    public function field_edit($key) {
        $data = $this->setting_db->detail_by_key($key);
        $data['config'] = unserialize($data['config']);
        if (submitcheck('dosubmit')) {
            $result = $this->setting_db->update($_POST, 'update');
            if (!$result) {
                $this->error($this->setting_db->getError());
            }
            $this->success('操作成功');
        } else {
            $this->assign('data', $data);
            $this->display();
        }
        print_r($data);
    }
    /* 字段列表 */
    public function field_lists() {
        $data = $this->setting_db->order("listorder ASC")->select();
        $this->assign('data', $data);
        $this->display();
    }
    /**
     * 字段排序
     * @author   <jason>
     */
    public function public_listorder() {        
        if (submitcheck('dosubmit')) {
            $listorders = (array) $_POST['listorders'];
            if (empty($listorders)) {
                $this->error('数据非法');
            }
            foreach ($listorders as $key => $value) {
                $this->setting_db->save(array('key' => $key, 'listorder' => (int) $value));
            }
            $this->success('操作成功');
        } else {
            $this->error('请勿非法提交');
        }
    }
    /**
     * 发送测试邮件
     * @author   <jason>
     */
    public function public_test_mail() {
        helpers('mail');
        $subject = 'tpcms test mail';
        $message = 'this is a test mail from tpcms team';
        $mail = array (
            'mailsend' => 2,
            'maildelimiter' => 1,
            'mailusername' => 1,
            'server' => $_POST['mail_server'],
            'port' => intval($_POST['mail_port']),
            'mail_type' => intval($_POST['mail_type']),
            'auth' => intval($_POST['mail_auth']),
            'from' => $_POST['mail_from'],
            'auth_username' => $_POST['mail_user'],
            'auth_password' => $_POST['mail_password']
        );    
        if(sendmail($_POST['mail_to'],$subject,$message,$_POST['mail_from'],$mail)) {
            echo L('test_email_succ').$_POST['mail_to'];
        } else {
            echo L('test_email_faild');
        }
    }


    
    /**
     * 检测GD库
     * @author   <jason>
     */
    private function check_gd() {
        if(!function_exists('imagepng') && !function_exists('imagejpeg') && !function_exists('imagegif')) {
            $gd = L('gd_unsupport');
        } else {
            $gd = L('gd_support');
        }
        return $gd;
    }

     /**
     * 积分配置
     * @author   <jason>
     */

    public function score_set(){
         $models = getcache('model', 'commons');
         $this->module_db = D('Module');
        foreach ($models as $k => $v) {
            if($v['module'] != 'member' || $v['disabled'] == 1) unset($models[$k]);
        }
        $_grouplist = getcache('member_group', 'member');
        $grouplist = array();
        foreach ($_grouplist as $k => $v) {
            if($k < 2) continue;
            $grouplist[$k] = $v['name'];
        }

        $member_setting = $this->module_db->getFieldByModule('Member', 'setting');
        $member_setting = dstripslashes(unserialize($member_setting));
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $cash_config = array();
            foreach ($info['cash_config']['money'] as $k => $v) {
                if(empty($v)) continue;
                $cash_config[$v] = $info['cash_config']['number'][$k+1];
            }
            $info['cash_config'] = $cash_config;
            $result = $this->module_db->where(array('module' => 'Member'))->setField('setting', serialize($info));
            if (!$result) {
                $this->error('模块配置失败');
            }
            setcache('setting', $info, strtolower(MODULE_NAME));
            $this->success('操作成功');
        }
        $form = new \Common\Library\form();
        include $this->admin_tpl('score_set');
    }


    public function setting() {
        $models = getcache('model', 'commons');
        foreach ($models as $k => $v) {
            if($v['module'] != 'member' || $v['disabled'] == 1) unset($models[$k]);
        }
        $_grouplist = getcache('member_group', 'member');
        $grouplist = array();
        foreach ($_grouplist as $k => $v) {
            if($k < 2) continue;
            $grouplist[$k] = $v['name'];
        }

        $member_setting = $this->module_db->getFieldByModule('Member', 'setting');
        $member_setting = dstripslashes(unserialize($member_setting));
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $cash_config = array();
            foreach ($info['cash_config']['money'] as $k => $v) {
                if(empty($v)) continue;
                $cash_config[$v] = $info['cash_config']['number'][$k+1];
            }
            $info['cash_config'] = $cash_config;
            $result = $this->module_db->where(array('module' => 'Member'))->setField('setting', serialize($info));
            if (!$result) {
                $this->error('模块配置失败');
            }
            setcache('setting', $info, strtolower(MODULE_NAME));
            $this->success('操作成功');
        } else {
            $form = new \Common\Library\form();
            include $this->admin_tpl('member_setting');         
        }
    }

    public function score(){
        $task = model("task")->select();
       /* $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('task_add',array('t'=>1)).'\', title:\''.L('自定义任务').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('自定义任务'));*/
        include $this->admin_tpl('task_list');    
    }

    public function task_add(){
        include $this->admin_tpl('task_add'); 

    }

    public function task_edit(){
        if (IS_POST) {
           $info = I('post.');
           $result = model("task")->where(array('id'=>$info['id']))->save($info);
           if ($result) {
               $this->success('操作成功');
           }else{
                $this->error('操作失误');
           }
        }
        $id = I('id');
        $task = model("task")->find($id);
        include $this->admin_tpl('task_edit'); 
    }

    public function api(){
         if (submitcheck('dosubmit')) {
            $info = $_POST['setting'];
            if (empty($info)) {
                $this->error('参数错误');
            }
            foreach ($info as $k => $v) {
                if(is_array($v)) $v = array2string($v);
/*                $this->db->where(array('key' => $k))->setField('value', $v);
*/            }
         //   $info = $this->db->getField('key, value', TRUE);
            @file_put_contents(CONF_PATH.'api.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
            $this->success('操作成功');
        } else{
            include $this->admin_tpl('api_set'); 
        }
    }


    public function push_set(){
         if (submitcheck('dosubmit')) {
            $info = $_POST['setting'];
            if (empty($info)) {
                $this->error('参数错误');
            }
            foreach ($info as $k => $v) {
            @file_put_contents(CONF_PATH.'push.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
        }
            $this->success('操作成功');
        } else{
            include $this->admin_tpl('push_set'); 
        }
    }

    public function push_message(){
        if (IS_POST) {
            $info = $_POST['push'];
            $infos = array();
            if ($info['recipient_type'] == 1) {
                $receive = 'all';
                $infos['type'] = 'all';
            }elseif($info['recipient_type'] == 3){
                $receive['alias'] = explode(',', $info['device_alias']);
                 $infos['type'] = 'alias';
            }
            if ($info['title']) {
               $title = $info['title'];
            }

            if ($info['content']) {
                $content = $info['content'];
                 $infos['content'] = $content;
                 $infos['send_time'] = NOW_TIME;
            }

          

            $push = A('Api/PushSDK');


            if ($info['recipient_type'] == 3) {

                $userinfo = model('member_app')->field('alias,userid')->select();
                foreach ($userinfo as $k => $v) {

                    if (in_array($v['alias'],$receive['alias'])) {
                        $result = $push->new_push($receive,$title,$content,'',$m_txt,$m_time='0',$v['userid']);
                    }
                        
              
                }
            }elseif ($info['recipient_type'] == 1){
                     $result = $push->new_push($receive,$title,$content,'',$m_txt,$m_time='0',0);

            }else{
              $this->error('参数错误');

            }
            



             $this->success('发送成功');

           /* $result = test($receive,$content,'',$m_txt);
            if ($result) {
                $this->success('发送成功');
            }*/

          
        }
        $form =  new \Common\Library\form();
        include $this->admin_tpl('push_message'); 

    }

    public function message(){
         $sqlMap = array();
         if (IS_GET) {
            $info = I('get.');
            $info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
            $info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
            /* 注册时间 */
            if ($info['start_time'] && $info['end_time']){
                $sqlMap['send_time'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
            }else{
                if ($info['start_time'] > 0) {
                $sqlMap['send_time'] = array("EGT", $info['start_time']);
                }
                if ($info['end_time'] > 0) {
                    $sqlMap['send_time'] = array("ELT", $info['end_time']);
                }
            }

        $uids = model('member')->where(array('nickname'=>array("LIKE", "%".$info['nickname']."%")))->getfield('userid',true);
        $sqlMap['userid'] = array("IN", $uids);
        }
        $count = model('push_log')->count();
        $lists = model('push_log')->where($sqlMap)->order('id desc')->page(PAGE,10)->select();
        $form =  new \Common\Library\form();
        $pages = page($count, 10);
        include $this->admin_tpl('push_list'); 

    }

    public function push_del(){
        $id = I('ids');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $sqlMap = array();
        if (is_array($id)) {
            $sqlMap['id'] = array("LIKE", $id);
        } else {
            $sqlMap['id'] = $id;
        }
        model('push_log')->where($sqlMap)->delete();
        $this->success('操作成功');     
    }

    public function download_set(){
          if (submitcheck('dosubmit')) {
                $info = $_POST['setting'];
                if (empty($info)) {
                    $this->error('参数错误');
                }
                foreach ($info as $k => $v) {
                if(is_array($v)) $v = array2string($v);
               }
                @file_put_contents(CONF_PATH.'download.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
                $this->success('操作成功');
        }
        include $this->admin_tpl('download_set');
    }


}