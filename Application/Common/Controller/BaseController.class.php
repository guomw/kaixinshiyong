<?php
namespace Common\Controller;
use Common\Controller\SystemController;
abstract class BaseController extends SystemController {
    public function _initialize() {
    	parent::_initialize();
    }

    public function _empty(){
        $this->error('请勿非法访问');
    }
}