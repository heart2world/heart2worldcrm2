<?php

define('UC_AUTH_KEY', 'k]J$[8b)%0Fv=E234cn12=B3qI|D4@#LdC`xOaN6-{QWZ'); //加密KEY
return array(
	'DEFAULT_MODULE' => 'Admin', //默认模型
	'DEFAULT_ACTION' => 'Index',
	'MODULE_DENY_LIST' => array('Common'), //禁止访问的模型
	'LOAD_EXT_FILE' => 'data,houses',
	/* 系统数据加密设置 */
	'DATA_AUTH_KEY' => 'puhb>4T`#7q-QA[3eC}w$9KMFORfW2EBXI+SD&od', //默认数据加密KEY

	/* 用户相关设置 */
	'USER_MAX_CACHE' => 1000, //最大缓存用户数
	'USER_ADMINISTRATOR' => 1, //管理员用户ID

	/* URL配置 */
	'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
	'URL_MODEL' => 2, //URL模式
	'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
	'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符

	/* 全局过滤配置 */
	'DEFAULT_FILTER' => '', //全局过滤函数
	//    'URL_PARAMS_SAFE' => true,
	//    'DEFAULT_FILTER' => 'think_filter',
	/*  3des加密 */
	'DES3_KEY_VALUE' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB',
	'DES3_VI_VALUE' => '01234567',
	/* 数据库配置 */
	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => '127.0.0.1', // 服务器地址
	'DB_NAME' => 'sys', // 数据库名
	'DB_USER' => 'root', // 用户名
	'DB_PWD' => 'root', // 密码
	'DB_PORT' => '3306', // 端口
	'DB_PREFIX' => 'sys_', // 数据库表前缀

);
