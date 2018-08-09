<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/12/24
 * Time: 11:23
 */
namespace Document\Controller;
use \Admin\Controller\InitController;
class ProductController extends InitController
{
    public function _initialize(){
        parent::_initialize();
        $this->db = model('product');
    }

    public function add(){
        var_dump($this->db->update(array('title' => '7', 'test' => '123')));
        $mod = I('mod', 'rebate');
        if(submitcheck('dosubmit')) {
            $result = $this->db->update($info);
            if(!$result) {
                $this->error($this->db->getError());
            } else {
                $this->success('产品发布成功', U('manage'));
            }
        } else {
//            include $this->admin_tpl('product_add');
            $show_header = $show_dialog = $show_validator = 1;
            $form = new \Common\Library\form();
            include $this->admin_tpl('document_add_'.$mod);
        }
    }
}