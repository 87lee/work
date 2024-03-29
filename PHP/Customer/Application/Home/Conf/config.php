<?php
//接入配置文件
@$configFile = file_get_contents('/home/apache/customer.conf');// /home/apache/android.conf   or  ../../android.conf

if ($configFile == false) {
    die('{"result":"fail","reason":"没有配置文件"}');
}

$configFile = json_decode($configFile,true);
if (!$configFile) {
    die('{"result":"fail","reason":"读取配置文件出错"}');
}

//OSS
defined('OSS_ACCESS_ID')   or define('OSS_ACCESS_ID',isset($configFile['OSS_ACCESS_ID'])?$configFile['OSS_ACCESS_ID']:'');
defined('OSS_ACCESS_KEY')   or define('OSS_ACCESS_KEY',isset($configFile['OSS_ACCESS_KEY'])?$configFile['OSS_ACCESS_KEY']:'');
defined('OSS_ENDPOINT')   or define('OSS_ENDPOINT',isset($configFile['OSS_ENDPOINT'])?$configFile['OSS_ENDPOINT']:'');
defined('OSS_BUCKET')   or define('OSS_BUCKET',isset($configFile['OSS_BUCKET'])?$configFile['OSS_BUCKET']:'');


return array(
	//'配置项'=>'配置值'
	/* 数据库设置 */
	'DB_TYPE'               =>  $configFile['DB_TYPE']?$configFile['DB_TYPE']:'mysql',     // 数据库类型
	'DB_HOST'               =>  $configFile['DB_HOST']?$configFile['DB_HOST']:'localhost', // 服务器地址
	'DB_NAME'               => $configFile['DB_NAME']?$configFile['DB_NAME']:'' ,          // 数据库名db_android_publish
	'DB_USER'               =>  $configFile['DB_USER']?$configFile['DB_USER']:'root',      // 用户名
	'DB_PWD'                => $configFile['DB_PWD']?$configFile['DB_PWD']:'' ,          // 密码709394  mysql7o9394
	'DB_PORT'               =>  $configFile['DB_PORT']?$configFile['DB_PORT']:'3306',        // 端口
	'DB_PREFIX'             =>  $configFile['DB_PREFIX']?$configFile['DB_PREFIX']:'' ,    // 数据库表前缀
	'DB_PARAMS'          	=>  array(
		\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
	), // 数据库连接参数
	'DB_DEBUG'  			=>  true, // 数据库调试模式 开启后可以记录SQL日志
	'DB_FIELDS_CACHE'       =>  false,        // 启用字段缓存
	'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
	'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
	'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
	'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
	'DB_SLAVE_NO'           =>  '', // 指定从服务器序号
	'DEFAULT_FILTER'        =>  'htmlspecialchars', // 默认参数过滤方法 用于I函数... htmlspecialchars
	'READ_DATA_MAP'=>true,//字段映射
	//SESSION参数
	'SESSION_OPTIONS'=>array(
		'expire'=>3600*6
		// 'expire'=>180
	),

	/*'DB_SILENT' => array(
		'DB_TYPE'               =>  DB_TYPE,     // 数据库类型
		'DB_HOST'               =>  DB_HOST, // 服务器地址
		'DB_NAME'               =>  DB_SILENT_NAME,          // 数据库名
		'DB_USER'               =>  DB_USER,      // 用户名
		'DB_PWD'                =>  DB_PWD,          // 密码
		'DB_PORT'               =>  DB_PORT,        // 端口
		'DB_PREFIX'             =>  DB_PREFIX,    // 数据库表前缀
		'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
		'DB_PARAMS'          	=>  array(
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL ,//数据库返回数据区分大小写 //PDO::CASE_NATURAL     PDO::CASE_LOWER
		), // 数据库连接参数
	),*/
	// 'SHOW_PAGE_TRACE' =>true,
	// 'LOG_LEVEL'  =>'SQL',
	// 'SHOW_ERROR_MSG'        =>  true,
    // 开启路由
    /*'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES'=>array(
        'login' => '/User/login',
        'passwd' => '/User/passwd'
    ),*/
    //配置模板替换
    'TMPL_PARSE_STRING'=>array(
        '__HOME_ASSETS__'=>'/Customer/Application/Home/View'
    ),
    'DOWNLOAD_APK_PREFIX_ADDR'=>"http://resrc-test.oss-cn-qingdao.aliyuncs.com/",
    //1小时没有操作重新登录
    'NO_ACTION_TIME'=>6*3600,
    //权限配置
    'AUTH_CONFIG' => array(
        'AUTH_ON' => true, //是否开启权限
        'AUTH_TYPE' => 1, //1实时认证    2登录认证
        'AUTH_GROUP' => 'tb_auth_group', //用户组
        'AUTH_GROUP_ACCESS' => 'tb_auth_group_access', //用户组规则
        'AUTH_RULE' => 'tb_auth_rule', //规则中间表
        'AUTH_USER' => 'tb_user'// 管理员表
    ), 
    'LOAD_EXT_CONFIG' => 'httpCode,mail,expire,upload',

    'LOGIN_URL' => "http://192.168.1.199:180/Pages/customService/index.html",
    'SHOW_PAGE_TRACE' =>true,
);
