<?php

//接入配置文件
defined('CONFIG_FILE_PATH')   or define('CONFIG_FILE_PATH','/home/apache/monitoring.conf');
@$configFile = file_get_contents(CONFIG_FILE_PATH);// /home/apache/monitoring.conf   or  d:/www/monitoring.conf
// @$configFile = file_get_contents('../../console.conf');// /home/apache/console.conf   or  ../../console.conf

if ($configFile == false) {
	die('{"result":"fail","reason":"没有配置文件"}');
}
$configFile = json_decode($configFile,true);
if (!$configFile) {
	die('{"result":"fail","reason":"读取配置文件出错"}');
}

return array(
	//'配置项'=>'配置值'
	// 'LOAD_EXT_CONFIG' => 'db,sls',
	//SESSION参数
	'SESSION_OPTIONS'=>array(
		'expire'=>3600*6,
	),

	/*'MAIL_CONFIG' => array(
        		'CHATSET' => 'UTF-8',
	        	'SMTP_AUTH' => true,
	        	'PORT' => '25',  //端口
	        	'MAIL_HOST' => 'smtp.qiye.163.com',  //SMTP服务器地址
	        	'MAIL_USERNAME' => 'service@ipmacro.com',
	        	'MAIL_PASSWORD' => 'uxLVqwZjeqFTNcv@',
	        	'MAIL_FROM' => 'service@ipmacro.com',
	        	'MAIL_FROM_NAME' => '张涛',
	        	'WORD_WRAP' => 80
        	),*/



	/*'MAIL_CONFIG' => array(
	  	'CHATSET' => 'UTF-8',
	        	'SMTP_AUTH' => true,
	        	'PORT' => '25',  //端口
		'MAIL_HOST' => 'smtp.163.com',  //SMTP服务器地址
		'MAIL_USERNAME' => 'sbpgktdkj@163.com',
		'MAIL_PASSWORD' => 'q274589954',
		'MAIL_FROM' => 'sbpgktdkj@163.com',
		'MAIL_FROM_NAME' => '后台监控管理员',
		'WORD_WRAP' => 80
        	),*/

	'MAIL_CONFIG' => array(
	  	'CHATSET' => 'UTF-8',
	        	'SMTP_AUTH' => true,
	        	'PORT' => '25',  //端口
		'MAIL_HOST' => 'smtp.vsoontech.com',  //SMTP服务器地址
		'MAIL_USERNAME' => 'backend-warning@vsoontech.com',
		'MAIL_PASSWORD' => 'Backend7o9394',
		'MAIL_FROM' => 'backend-warning@vsoontech.com',
		'MAIL_FROM_NAME' => '后台监控管理员',
		'WORD_WRAP' => 80
        	),

	//后台监控--微信CorpID
	'WIN_XIN_CORP_ID' => isset($configFile['WIN_XIN_CORP_ID'])?$configFile['WIN_XIN_CORP_ID']:'wxea3f04ff682b145b',

	//后台监控--微信corpsecret
	'WIN_XIN_CORP_SECRET' => isset($configFile['WIN_XIN_CORP_SECRET'])?$configFile['WIN_XIN_CORP_SECRET']:'vAYPyuASir-VemR7_M6NrHbAkFJQX5w9oPwgl1ZVj8Bm-_ZkSIrNQlAi7ZuLit1o',

	'WEI_XIN_HOST_ADDR'=>isset($configFile['WEI_XIN_HOST_ADDR'])?$configFile['WEI_XIN_HOST_ADDR']:'',

	//后台监控--直播用户反馈JSON下载前缀
	'LIVE_USER_REPORT_ANAS' => isset($configFile['LIVE_USER_REPORT_ANAS'])?$configFile['LIVE_USER_REPORT_ANAS']:'',

	//SLS亚里云日志库
	'SLS_ACCESS_KEY_ID'=>isset($configFile['SLS_ACCESS_KEY_ID'])?$configFile['SLS_ACCESS_KEY_ID']:'',
	'SLS_ACCESS_KEY'=>isset($configFile['SLS_ACCESS_KEY'])?$configFile['SLS_ACCESS_KEY']:'',
	'SLS_ENDPOINT'=>isset($configFile['SLS_PROJECT'])?$configFile['SLS_PROJECT']:'',
	'SLS_PROJECT'=>isset($configFile['SLS_PROJECT'])?$configFile['SLS_PROJECT']:'',
	'SLS_LOGSTORE'=>isset($configFile['SLS_LOGSTORE'])?$configFile['SLS_LOGSTORE']:'',

	/* 数据库设置 */
	'DB_TYPE'               =>  isset($configFile['DB_TYPE'])?$configFile['DB_TYPE']:'mysql',     // 数据库类型
	'DB_HOST'               =>  isset($configFile['DB_HOST'])?$configFile['DB_HOST']:'localhost', // 服务器地址
	'DB_NAME'               =>  isset($configFile['DB_NAME'])?$configFile['DB_NAME']:'',          // 数据库名
	'DB_USER'               =>  isset($configFile['DB_USER'])?$configFile['DB_USER']:'root',      // 用户名
	'DB_PWD'                =>  isset($configFile['DB_PWD'])?$configFile['DB_PWD']:'',          // 密码
	'DB_PORT'               =>  isset($configFile['DB_PORT'])?$configFile['DB_PORT']:'3306',        // 端口
	'DB_PREFIX'             =>  isset($configFile['DB_PREFIX'])?$configFile['DB_PREFIX']:'',    // 数据库表前缀
	'DB_PARAMS'          	=>  array(
		\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
	),

	// 数据库连接参数
	'DB_DEBUG'  			=>  true, // 数据库调试模式 开启后可以记录SQL日志
	'DB_FIELDS_CACHE'       =>  false,        // 启用字段缓存
	'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
	'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
	'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
	'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
	'DB_SLAVE_NO'           =>  '', // 指定从服务器序号
	'DEFAULT_FILTER'        =>  'htmlspecialchars,addslashes', // 默认参数过滤方法 用于I函数... htmlspecialchars
	'READ_DATA_MAP'=>true,//字段映射


	'DB_DISTANCE_MONITORING' => array(
		'DB_TYPE'               =>  isset($configFile['DB_TYPE'])?$configFile['DB_TYPE']:'mysql',     // 数据库类型
		'DB_HOST'               =>  isset($configFile['DB_DISTANCE_MONITORING_HOST'])?$configFile['DB_DISTANCE_MONITORING_HOST']:'localhost', // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_DISTANCE_MONITORING_NAME'])?$configFile['DB_DISTANCE_MONITORING_NAME']:'',          // 数据库名
		'DB_USER'               =>  isset($configFile['DB_DISTANCE_MONITORING_USER'])?$configFile['DB_DISTANCE_MONITORING_USER']:'',      // 用户名
		'DB_PWD'                =>  isset($configFile['DB_DISTANCE_MONITORING_PWD'])?$configFile['DB_DISTANCE_MONITORING_PWD']:'',          // 密码
		'DB_PORT'               =>  isset($configFile['DB_PORT'])?$configFile['DB_PORT']:'3306',       // 端口
		'DB_PREFIX'             =>  isset($configFile['DB_PREFIX'])?$configFile['DB_PREFIX']:'3306',    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	'DB_REPORT' => array(
		'DB_TYPE'               =>  isset($configFile['DB_TYPE'])?$configFile['DB_TYPE']:'mysql',     // 数据库类型
		'DB_HOST'               =>  isset($configFile['DB_DISTANCE_MONITORING_HOST'])?$configFile['DB_DISTANCE_MONITORING_HOST']:'localhost', // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_DISTANCE_REPORT_NAME'])?$configFile['DB_DISTANCE_REPORT_NAME']:'',           // 数据库名
		'DB_USER'               =>  isset($configFile['DB_DISTANCE_MONITORING_USER'])?$configFile['DB_DISTANCE_MONITORING_USER']:'',      // 用户名
		'DB_PWD'                =>  isset($configFile['DB_DISTANCE_MONITORING_PWD'])?$configFile['DB_DISTANCE_MONITORING_PWD']:'',          // 密码
		'DB_PORT'               =>  isset($configFile['DB_PORT'])?$configFile['DB_PORT']:'3306',       // 端口
		'DB_PREFIX'             =>  isset($configFile['DB_PREFIX'])?$configFile['DB_PREFIX']:'3306',    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),

	'DB_MONITORING' => array(
		'DB_TYPE'               =>  isset($configFile['DB_TYPE'])?$configFile['DB_TYPE']:'mysql',     // 数据库类型
		'DB_HOST'               =>  isset($configFile['DB_HOST'])?$configFile['DB_HOST']:'localhost', // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_MONITORING_NAME'])?$configFile['DB_MONITORING_NAME']:'',          // 数据库名
		'DB_USER'               =>  isset($configFile['DB_USER'])?$configFile['DB_USER']:'root',      // 用户名
		'DB_PWD'                =>  isset($configFile['DB_PWD'])?$configFile['DB_PWD']:'',          // 密码
		'DB_PORT'               =>  isset($configFile['DB_PORT'])?$configFile['DB_PORT']:'3306',        // 端口
		'DB_PREFIX'             =>  isset($configFile['DB_PREFIX'])?$configFile['DB_PREFIX']:'',    // 数据库表前缀
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		),
	)


	// 'SHOW_PAGE_TRACE' =>true,
	// 'LOG_LEVEL'  =>'SQL',
	// 'SHOW_ERROR_MSG'        =>  true,
    	// 开启路由
   	/*'URL_ROUTER_ON'   => true,
	'URL_ROUTE_RULES'=>array(
        		'login' => '/User/login',
        		'passwd' => '/User/passwd'
    	),*/

);