<?php 
// 检测PHP环境
session_start();
ini_set('session.gc_maxlifetime', 3600*6);
ini_set('session.cookie_lifetime', 3600*6);
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('{"result":"fail","reason":"PHP > 5.3.0 !"}');  
defined('APP_PATH')   or define('APP_PATH','./');
include APP_PATH.'db.class.php';
include APP_PATH.'function.php';          
//接入配置文件
// @$configFile = file_get_contents('/home/nginx/config/console.conf');// /home/apache/console.conf   or  ../../console.conf
@$configFile = file_get_contents('console.conf');// /home/apache/console.conf   or  ../../console.conf

if ($configFile == false) {
	result('没有配置文件');
}
$configFile = json_decode($configFile,true);
if (!$configFile) {
	result('读取配置文件出错');
}
//服务器地址
defined('DB_HOST')   or define('DB_HOST',isset($configFile['DB_HOST'])?$configFile['DB_HOST']:'');
//数据库名
defined('DB_NAME')   or define('DB_NAME',isset($configFile['DB_NAME'])?$configFile['DB_NAME']:'');
//数据库用户名
defined('DB_USER')   or define('DB_USER',isset($configFile['DB_USER'])?$configFile['DB_USER']:'');
//数据库密码
defined('DB_PWD')   or define('DB_PWD',isset($configFile['DB_PWD'])?$configFile['DB_PWD']:'');
//数据库表前缀
defined('DB_PREFIX')   or define('DB_PREFIX',isset($configFile['DB_PREFIX'])?$configFile['DB_PREFIX']:'');
//登陆名
defined('USER')   or define('USER',isset($configFile['USER'])?$configFile['USER']:'');
//登陆密码
defined('PASSWD')   or define('PASSWD',isset($configFile['PASSWD'])?$configFile['PASSWD']:'');


$db = new DB(DB_HOST,DB_USER,DB_PWD,DB_NAME,DB_PREFIX);
if (!$db) {
	result('数据库连接失败');
}
if (empty($_SESSION['IS_LOGO'])) {
	if (strstr($_SERVER['REQUEST_URI'], '/login')==false) {
		result('login');
	}
	
}else{
	session_regenerate_id();
	$_SESSION['IS_LOGO'] = $_SESSION['IS_LOGO'];
}

?>