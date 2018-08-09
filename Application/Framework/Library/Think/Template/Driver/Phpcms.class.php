<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614<weibo.com/luofei614>
// +----------------------------------------------------------------------
namespace Think\Template\Driver;
use \Common\Library\template;
/**
 * PHPCMS模板引擎驱动 
 */
class Phpcms {
    /**
     * 渲染模板输出
     * @author   <jason>
     */
    public function fetch($templateFile,$var) {
        $template = new template();
        return $template->fetch($templateFile, $var);
    }
}
