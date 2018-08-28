<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
error_reporting(E_ALL || ~ E_NOTICE);
define('CHARSET', 'utf-8');

define('APP_DEBUG', TRUE);
define('BUILD_LITE_FILE', TRUE); // 地址不区分大小写

define('SITE_PATH', dirname(__FILE__));

// 定义应用目录
define('APP_PATH', dirname(__FILE__).'/Application/');
define('RUNTIME_PATH', SITE_PATH .'/~runtime/');

define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
// 拓展常量定义
define('IN_TPCMS', TRUE);
define('TPL_PATH', SITE_PATH.'/templates/');

header('Content-type: text/html; charset='.CHARSET);
//关闭PC官网，跳转值商家后台页面
if(!$_GET["m"]){
    $_REQUEST["m"]=$_GET["m"]="Member";
    $_REQUEST["c"]=$_GET["c"]="Index";
    $_REQUEST["a"]=$_GET["a"]="login";

}

define('HOME_PAGE','http://www.dealswill.com/');

//载入框架入口文件
require APP_PATH . 'Framework/system.php';