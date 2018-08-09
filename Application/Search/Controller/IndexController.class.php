<?php
namespace Search\Controller;
class IndexController extends \Common\Controller\BaseController
{
    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        $param = I('param.');
        extract($param);
        $type = ($type) ? $type : 'p';
        $keyword = remove_xss($keyword);
//        if(!$keyword) {
//            $this->error('请输入关键字');
//        }
        $SEO = seo(0, '搜索'.$keyword.'的结果');
        include template('index');
    }

    public function search() {
        $SEO = seo(0, '搜索');
        include template('search');
    }

}
