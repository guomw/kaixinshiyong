<?php
return array(
	'URL_MODEL'            => 0,
	'URL_CASE_INSENSITIVE' => TRUE,
	
	'TMPL_PARSE_STRING'    => array(
		'__PUBLIC__'           => __ROOT__.'/static/',
		'__CSSPATH__'          => __ROOT__.'/static/css/',
		'__IMGPATH__'          => __ROOT__.'/static/images/',
		'__JSPATH__'           => __ROOT__.'/static/js/'
	),
	
	'DEFAULT_MODULE'       => 'Product',
	'DEFAULT_CONTROLLER'   => 'Index',
	'DEFAULT_ACTION'       => 'index',
	
	// 模板配置
	'VIEW_PATH'             => TPL_PATH,
	'DEFAULT_THEME'       	=> 'test',
		
	// 配置提示页
	'TMPL_ACTION_SUCCESS'  => TPL_PATH.'success.tpl',
	'TMPL_ACTION_ERROR'    => TPL_PATH.'error.tpl',
	
	// 标签库配置
	//'TMPL_ENGINE_TYPE'     => 'Phpcms',
	'TAGLIB_BEGIN'         => '{',
	'TAGLIB_END'           => '}',
	'TAGLIB_NAME'          => 'pc',

	'BACKUP_PATH'		   => 'backup',
	
	// 分页变量
	'VAR_PAGE'             => 'page',
	
	'HTML_ROOT'            => '/html',
	
	// 多语言
	'LANG_SWITCH_ON'       => true,
    
	'SESSION_PREFIX'	=> 'TPCMS_',
	'COOKIE_PREFIX'		=> 'TPCMS_',
	/* Apache 伪静态规则模块检测去除REDIRECT_URL参数兼容 */
	'URL_PATHINFO_FETCH'	=> 'ORIG_PATH_INFO,REDIRECT_PATH_INFO',	
	'SHOW_PAGE_TRACE'      => FALSE,// 调试模式
	'LOAD_EXT_CONFIG'      => 'database,setting,order,product,rebate,trial,postal,rewrite,version,task,api,push,commission,app_version,sms,download,sso,two_notice' // 拓展配置
);