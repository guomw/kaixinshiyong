<?php
return array(
	// 数据库配置
  	'DB_TYPE'               =>  'Mysql',     // 数据库类型
    'DB_HOST'               =>  '#DB_HOST#', 	// 服务器地址
    'DB_NAME'               =>  '#DB_NAME#',          // 数据库名
    'DB_USER'               =>  '#DB_USER#',      // 用户名
    'DB_PWD'                =>  '#DB_PWD#',          // 密码
    'DB_PORT'               =>  '#DB_PORT#',        // 端口
    'DB_PREFIX'             =>  '#DB_PREFIX#',         // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    =>  false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1
);