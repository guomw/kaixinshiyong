<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class ActivitySettingController extends InitController {
    public function _initialize() {
    	parent::_initialize();
     
    }
    /* 活动管理->全局配置  下单方式设置 [Adalways试用系统] */
    public function way() {
            $form = new \Common\Library\form();
            $order_way = model('order_way')->select();
            include $this->admin_tpl('order_way_list');
        
    }

    public function way_edit(){
        if (IS_POST) {
           $info = I('post.');
           $result = model('order_way')->where(array('id'=>$info['id']))->save($info);
           if ($result) {
               $this->success('操作成功');
           }
        }
          $form = new \Common\Library\form();
          $id = I('id');
          $order = model('order_way')->find($id);
         include $this->admin_tpl('order_way_edit');
    }
    /* 活动管理->全局配置 店铺来源设置 [试用系统] */
    public function shop_source() {
        if (submitcheck('dosubmit')) {
            $infos = $_POST['setting'];
            $this->success('操作成功');
        } else {
            $this->pagecurr = max(1, I('page', 0, 'intval'));
            $this->pagesize = 10;
            $form = new \Common\Library\form();
            $shopCount = model('shop_set')->count();
            $shoplist = model('shop_set')->page($this->pagecurr, $this->pagesize)->select();
            $pages = page($shopCount, $this->pagesize);
            /* 附加菜单 */
            $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('添加店铺来源').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('添加店铺来源'));
            $form =  new \Common\Library\form();
            include $this->admin_tpl('shop_source');
        }
    }
    /**
     * 添加 店铺来源 [Adalways试用系统]
     * @author   <jason>
     */
    public function add() {
        if (submitcheck('dosubmit') && I('post.fromhash') == session('FROMHASH')) {
            $info = I('post.');
            if (!$info['name']) $this->error('请填写店铺来源名称');
            if (!$info['small_logo'] || !$info['big_logo']) $this->error('请上传图片LOGO');
            $info['inputtime'] = NOW_TIME;
            $result = model('shop_set')->add($info);
            if (!$result) {
                $this->error('操作失误');
            }
            $this->success('操作成功','javascript:close_dialog();', 1);
        } else {
            $form =  new \Common\Library\form();        
            $show_header = true;
            $show_validator = true;
            include $this->admin_tpl('shop_add');
        }
    }
    /* 修改 店铺来源 [Adalways试用系统] */
    public function edit($id = 0) {
        $id = (int) $id;
        if($id < 0 ){$this->error('参数错误');}
        $shop_info = model("shop_set")->where(array('id'=>$id))->find();
        if (submitcheck('dosubmit')  && I('post.fromhash') == session('FROMHASH')) {
            $info = I('post.');
            if (!$info['id']) $this->error('非法访问!');
            $info['updatetime'] = NOW_TIME;
            $result = model('shop_set')->where(array('id'=>$info['id']))->save($info);
            if(!$result){
                $this->error('修改失败，请重试！');
            }
            $this->success('操作成功','javascript:close_dialog();', 1);
        } else {
            $form =  new \Common\Library\form();
            include $this->admin_tpl('shop_edit');
        }               
    }
    /* 删除 店铺来源 [Adalways试用系统] */
    public function delete() {
        $ids = I('param.id');
        if (!$ids)  $this->error('请选择要删除的记录！');
        foreach ($ids as $id) {
            if($id < 2) continue;
            model('shop_set')->delete($id);
        }
        $this->success('删除成功',U('shop_source'));
    }
    
    public function upload(){
        if(!empty($_FILES)){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize  =     3145728 ;
            $upload->exts     =     array('jpg', 'gif', 'png');
            $upload->rootPath = './uploadfile/code/';
            if(!file_exists($upload->rootPath)){//不存在，则创建
               mkdir($upload->rootPath, 0777);
            }
            $upload->savePath = '';
            $upload->replace  = TRUE;
            $upload->saveName = NOW_TIME.random(5,1).'_code';
            $upload->autoSub = FALSE;
            $upload->saveExt  = 'jpg';
            $result = $upload->upload($_FILES);
            $name = __ROOT__.'./uploadfile/code/'.$result['Filedata']['savename'];
            if($result){
                echo $name;exit();
            }else{
                exit('0');
            }
        }
    }
    
}