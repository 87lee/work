<?php
@$configFile = file_get_contents(CONFIG_FILE_PATH);// /home/apache/console.conf   or  ../../console.conf
$configFile = json_decode($configFile,true);

//ACTION数据库名
// defined('DB_ACTION_NAME')   or define('DB_ACTION_NAME',isset($configFile['DB_ACTION_NAME'])?$configFile['DB_ACTION_NAME']:'');


//数据库用户名
defined('DB_USER')   or define('DB_USER',isset($configFile['DB_USER'])?$configFile['DB_USER']:'');
//数据库密码
defined('DB_PWD')   or define('DB_PWD',isset($configFile['DB_PWD'])?$configFile['DB_PWD']:'');
//数据库端口
defined('DB_PORT')   or define('DB_PORT',isset($configFile['DB_PORT'])?$configFile['DB_PORT']:'');
//数据库表前缀
defined('DB_PREFIX')   or define('DB_PREFIX',isset($configFile['DB_PREFIX'])?$configFile['DB_PREFIX']:'');
//数据库类型
defined('DB_TYPE')   or define('DB_TYPE',isset($configFile['DB_TYPE'])?$configFile['DB_TYPE']:'');
//服务器地址
defined('DB_HOST')   or define('DB_HOST',isset($configFile['DB_HOST'])?$configFile['DB_HOST']:'');
//数据库名
defined('DB_NAME')   or define('DB_NAME',isset($configFile['DB_NAME'])?$configFile['DB_NAME']:'');
return array(
	//'配置项'=>'配置值'
	/* 数据库设置 */
	'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
	'DB_HOST'               =>  DB_HOST, // 服务器地址
	'DB_NAME'               =>  DB_NAME,          // 数据库名
	'DB_USER'               =>  DB_USER,      // 用户名
	'DB_PWD'                =>  DB_PWD,          // 密码
	'DB_PORT'               =>  DB_PORT,        // 端口
	'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
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



	'READ_DATA_MAP'=>true,//字段映射
	//桌面数据库名
	'DB_DESKTOP' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_DESKTOP_NAME'])?$configFile['DB_DESKTOP_NAME']:'',          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//资源数据库名
	'DB_RESOURCES' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_RESOURCES_NAME'])?$configFile['DB_RESOURCES_NAME']:'',          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//第三方应用数据库名
	'DB_APP' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_APP_NAME'])?$configFile['DB_APP_NAME']:'',          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//安卓固件数据库名
	'DB_ANDROID_FIRMWARE' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_ANDROID_FIRMWARE_NAME'])?$configFile['DB_ANDROID_FIRMWARE_NAME']:'',          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//OTA数据库名
	'DB_OTA' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_OTA_NAME'])?$configFile['DB_OTA_NAME']:'',          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//静默安装数据库名
	'DB_SILENT' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_SILENT_NAME'])?$configFile['DB_SILENT_NAME']:'',          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//远程后台监控
	'DB_MONITORING' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  isset($configFile['DB_MONITORING_HOST'])?$configFile['DB_MONITORING_HOST']:'', // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_MONITORING_NAME'])?$configFile['DB_MONITORING_NAME']:'',          // 数据库名
		'DB_USER'               =>  isset($configFile['DB_MONITORING_USER'])?$configFile['DB_MONITORING_USER']:'',      // 用户名
		'DB_PWD'                =>  isset($configFile['DB_MONITORING_PWD'])?$configFile['DB_MONITORING_PWD']:'',          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//后台监控--直播用户反馈数据库
	'DB_REPORT' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  isset($configFile['DB_MONITORING_HOST'])?$configFile['DB_MONITORING_HOST']:'', // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_REPORT_NAME'])?$configFile['DB_REPORT_NAME']:'',          // 数据库名
		'DB_USER'               =>  isset($configFile['DB_MONITORING_USER'])?$configFile['DB_MONITORING_USER']:'',      // 用户名
		'DB_PWD'                =>  isset($configFile['DB_MONITORING_PWD'])?$configFile['DB_MONITORING_PWD']:'',          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//直播系统数据库名
	'DB_LIVE' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_LIVE_NAME'])?$configFile['DB_LIVE_NAME']:'',          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//直播系统直播授权数据库
	'DB_LIVE_AUTH' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_LIVE_AUTH_NAME'])?$configFile['DB_LIVE_AUTH_NAME']:'',          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	//直播系统直播已授权详情数据库
	'DB_LIVE_AUTH_DETAIL' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  isset($configFile['DB_LIVE_AUTH_DETAIL_HOST'])?$configFile['DB_LIVE_AUTH_DETAIL_HOST']:'', // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_LIVE_AUTH_DETAIL_NAME'])?$configFile['DB_LIVE_AUTH_DETAIL_NAME']:'',          // 数据库名
		'DB_USER'               => isset($configFile['DB_LIVE_AUTH_DETAIL_USER'])?$configFile['DB_LIVE_AUTH_DETAIL_USER']:'',      // 用户名
		'DB_PWD'                =>  isset($configFile['DB_LIVE_AUTH_DETAIL_PWD'])?$configFile['DB_LIVE_AUTH_DETAIL_PWD']:'',          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
	/*//直播系统直播授权已授权数据库
	'DB_LIVE_AUTH_NUM' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  isset($configFile['DB_LIVE_AUTH_NUM_HOST'])?$configFile['DB_LIVE_AUTH_NUM_HOST']:'', // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_LIVE_AUTH_NUM_NAME'])?$configFile['DB_LIVE_AUTH_NUM_NAME']:'',          // 数据库名
		'DB_USER'               =>  isset($configFile['DB_LIVE_AUTH_NUM_USER'])?$configFile['DB_LIVE_AUTH_NUM_USER']:'',      // 用户名
		'DB_PWD'                =>  isset($configFile['DB_LIVE_AUTH_NUM_PWD'])?$configFile['DB_LIVE_AUTH_NUM_PWD']:'',          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),*/
	//全搜索数据库
	'DB_VOD' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  isset($configFile['DB_VOD_HOST'])?$configFile['DB_VOD_HOST']:'', // 服务器地址
		'DB_NAME'               =>  isset($configFile['DB_VOD_NAME'])?$configFile['DB_VOD_NAME']:'',          // 数据库名
		'DB_USER'               =>  isset($configFile['DB_VOD_USER'])?$configFile['DB_VOD_USER']:'',      // 用户名
		'DB_PWD'                =>  isset($configFile['DB_VOD_PWD'])?$configFile['DB_VOD_PWD']:'',          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  isset($configFile['DB_VOD_PREFIX'])?$configFile['DB_VOD_PREFIX']:DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),
);