<?php
//接入配置文件
//配置文件地址
defined('CONFIG_FILE_PATH')   or define('CONFIG_FILE_PATH','/home/apache/console.conf'); // /home/apache/console.conf   or  D:/www/PHP/console.conf

@$configFile = file_get_contents(CONFIG_FILE_PATH);
if ($configFile == false) {
	die('{"result":"fail","reason":"没有配置文件"}');
}

$configFile = json_decode($configFile,true);

if (!$configFile) {
	die('{"result":"fail","reason":"读取配置文件出错"}');
}

if ($configFile['IS_RUNTIME'] != 'true') {
	die('{"result":"fail","reason":"系统维护中"}');
}

//OSS
defined('OSS_ACCESS_ID')   or define('OSS_ACCESS_ID',isset($configFile['OSS_ACCESS_ID'])?$configFile['OSS_ACCESS_ID']:'');
defined('OSS_ACCESS_KEY')   or define('OSS_ACCESS_KEY',isset($configFile['OSS_ACCESS_KEY'])?$configFile['OSS_ACCESS_KEY']:'');
defined('OSS_ENDPOINT')   or define('OSS_ENDPOINT',isset($configFile['OSS_ENDPOINT'])?$configFile['OSS_ENDPOINT']:'');
defined('OSS_BUCKET')   or define('OSS_BUCKET',isset($configFile['OSS_BUCKET'])?$configFile['OSS_BUCKET']:'');
// defined('OSS_APK_BUCKET')   or define('OSS_APK_BUCKET',isset($configFile['OSS_APK_BUCKET'])?$configFile['OSS_APK_BUCKET']:'');

//下载桌面图片前缀prefix
// defined('DOWNLOAD_DESKTOP_IMG_PREFIX_ADDR')   or define('DOWNLOAD_DESKTOP_IMG_PREFIX_ADDR',isset($configFile['DOWNLOAD_DESKTOP_IMG_PREFIX_ADDR'])?$configFile['DOWNLOAD_DESKTOP_IMG_PREFIX_ADDR']:'');
//后台监控--直播用户反馈JSON下载前缀
// defined('LIVE_USER_REPORT_ANAS')   or define('LIVE_USER_REPORT_ANAS',isset($configFile['LIVE_USER_REPORT_ANAS'])?$configFile['LIVE_USER_REPORT_ANAS']:'');

return array(
	//'配置项'=>'配置值'
	'LOAD_EXT_CONFIG' => 'db',
	//SESSION参数
	'SESSION_OPTIONS'=>array(
		'expire'=>3600*6
	),

	'DEFAULT_FILTER'        =>  '', // 默认参数过滤方法 用于I函数... htmlspecialchars
	//直播P2P在线查询HOST

	'LIVE_P2P_ONLINE_SEARCH_HOST'=> isset($configFile['LIVE_P2P_ONLINE_SEARCH_HOST'])?$configFile['LIVE_P2P_ONLINE_SEARCH_HOST']:'',

	//monitorCtrl接入层HOST
	'MONITOR_CTRL_ACCESS_ADDR' => isset($configFile['MONITOR_CTRL_ACCESS_ADDR'])?$configFile['MONITOR_CTRL_ACCESS_ADDR']:'',

	//monitor接入层HOST
	'MONITOR_ACCESS_ADDR' => isset($configFile['MONITOR_ACCESS_ADDR'])?$configFile['MONITOR_ACCESS_ADDR']:'',

	//桌面密码管理配置、桌面映射发布地址
	'DESKTOP3_JBK_PASSWD_ACCESS_ADDR' => isset($configFile['DESKTOP3_JBK_PASSWD_ACCESS_ADDR'])?$configFile['DESKTOP3_JBK_PASSWD_ACCESS_ADDR']:'',

	//下载地址前缀prefix
	'DOWNLOAD_PREFIX_ADDR'=>isset($configFile['DOWNLOAD_PREFIX_ADDR'])?$configFile['DOWNLOAD_PREFIX_ADDR']:'',

	//下载桌面图片前缀prefix
	'DOWNLOAD_IMG_PREFIX_HOST' =>isset($configFile['DOWNLOAD_DESKTOP_IMG_PREFIX_ADDR'])?$configFile['DOWNLOAD_DESKTOP_IMG_PREFIX_ADDR']:'',

	//APK下载地址前缀prefix
	'DOWNLOAD_APK_PREFIX_ADDR' => isset($configFile['DOWNLOAD_APK_PREFIX_ADDR'])?$configFile['DOWNLOAD_APK_PREFIX_ADDR']:'',
	//下载桌面资源包前缀prefix
	'DOWNLOAD_DESKTOP_SOURCER_PREFIX_ADDR' => isset($configFile['DOWNLOAD_DESKTOP_SOURCER_PREFIX_ADDR'])?$configFile['DOWNLOAD_DESKTOP_SOURCER_PREFIX_ADDR']:'',
	//下载桌面布局前缀prefix
	'DOWNLOAD_DESKTOP_LAYOUT_PREFIX_ADDR' => isset($configFile['DOWNLOAD_DESKTOP_LAYOUT_PREFIX_ADDR'])?$configFile['DOWNLOAD_DESKTOP_LAYOUT_PREFIX_ADDR']:'',


	//直播列表--直播列表下载前缀
	'GET_LIVE_CHANNEL_EPG_PREFIX_ADDR' =>isset($configFile['GET_LIVE_CHANNEL_EPG_PREFIX_ADDR'])?$configFile['GET_LIVE_CHANNEL_EPG_PREFIX_ADDR']:'',

	//直播列表--直播频道EPG同步获取数据HOST
	'LIVE_CHANNEL_EPG_PREFIX_ADDR' =>isset($configFile['LIVE_CHANNEL_EPG_PREFIX_ADDR'])?$configFile['LIVE_CHANNEL_EPG_PREFIX_ADDR']:'',

	//直播列表--直播频道同步EPG点播获取版本
	'LIVE_CHANNEL_EPG_VERSION_ADDR' =>isset($configFile['LIVE_CHANNEL_EPG_VERSION_ADDR'])?$configFile['LIVE_CHANNEL_EPG_VERSION_ADDR']:'',

	//直播列表--直播频道同步EPG点播获取频道列表
	'LIVE_CHANNEL_EPG_SNYC_PREFIX_ADDR' =>isset($configFile['LIVE_CHANNEL_EPG_SNYC_PREFIX_ADDR'])?$configFile['LIVE_CHANNEL_EPG_SNYC_PREFIX_ADDR']:'',


	//直播列表--直播开机画面下载前缀
	'LIVE_STARTUP_PICTURE_PREFIX_ADDR' => isset($configFile['LIVE_STARTUP_PICTURE_PREFIX_ADDR'])?$configFile['LIVE_STARTUP_PICTURE_PREFIX_ADDR']:'',
	//直播列表--直播列表下载前缀
	'LIVE_LIST_PREFIX_ADDR' => isset($configFile['LIVE_LIST_PREFIX_ADDR'])?$configFile['LIVE_LIST_PREFIX_ADDR']:'',

	//直播列表--直播列表名称同步地址前缀
	'LIVE_LIST_NAME_SYNC_PREFIX' => isset($configFile['LIVE_LIST_NAME_SYNC_PREFIX'])?$configFile['LIVE_LIST_NAME_SYNC_PREFIX']:'',
	//直播列表--直播列表分类同步地址前缀
	'LIVE_LIST_TYPE_SYNC_PREFIX' => isset($configFile['LIVE_LIST_TYPE_SYNC_PREFIX'])?$configFile['LIVE_LIST_TYPE_SYNC_PREFIX']:'',
	//直播列表--直播列表分类旧内容同步地址前缀
	'LIVE_LIST_CONTENT_SYNC_PREFIX' => isset($configFile['LIVE_LIST_CONTENT_SYNC_PREFIX'])?$configFile['LIVE_LIST_CONTENT_SYNC_PREFIX']:'',
	//直播列表--直播列表分类新内容同步地址前缀
	'LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX' => isset($configFile['LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX'])?$configFile['LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX']:'',


	//应用管理--第三方链接可用链接下载前缀
	'LINK_REDIRECT_PREFIX' => isset($configFile['LINK_REDIRECT_PREFIX'])?$configFile['LINK_REDIRECT_PREFIX']:'',

	//桌面坑位图标大小
	'DESKTOP_SLOT_IMG' => isset($configFile['DESKTOP_SLOT_IMG'])?(int)$configFile['DESKTOP_SLOT_IMG']:500*1024,
	//桌面壁纸大小
	'DESKTOP_WALLPAPER_IMG' => isset($configFile['DESKTOP_WALLPAPER_IMG'])?(int)$configFile['DESKTOP_WALLPAPER_IMG']:1*1024*1024,

	//pic PATH
	'LOCALHOST_PATH_ADDR' => isset($configFile['LOCALHOST_PATH_ADDR'])?$configFile['LOCALHOST_PATH_ADDR']:'',

);