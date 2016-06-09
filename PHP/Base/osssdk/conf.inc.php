<?php

/*defined('OSS_ACCESS_ID')   or define('OSS_ACCESS_ID',$config['OSS_ACCESS_ID']);
defined('OSS_ACCESS_KEY')   or define('OSS_ACCESS_KEY',$config['OSS_ACCESS_KEY']);
defined('OSS_ENDPOINT')   or define('OSS_ENDPOINT', $config['OSS_ENDPOINT']);
defined('OSS_BUCKET')   or define('OSS_BUCKET',empty($config['OSS_BUCKET'])?'':$config['OSS_BUCKET']);
*/
//是否记录日志
define('ALI_LOG', FALSE);

//自定义日志路径，如果没有设置，则使用系统默认路径，在./logs/
//define('ALI_LOG_PATH','');

//是否显示LOG输出
define('ALI_DISPLAY_LOG', FALSE);

//语言版本设置
define('ALI_LANG', 'zh');
