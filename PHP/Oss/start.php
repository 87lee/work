<?php 
define('CONFIG_FILE', '/home/apache/ossconfig.json');
require_once 'synChannels.class.php';
require_once '../Base/function/function.php';

ignore_user_abort(true);//关闭浏览器后，继续执行php代码

set_time_limit(0);//程序执行时间无限制

if (!is_file(CONFIG_FILE)) {
	result('config file not exists');
}
if (is_file('run.txt')) {
	$config = file_get_contents(CONFIG_FILE);
	$config = json_decode($config,true);
	
	if ($config['RUN_SCRIPT']) {
		$runFile = file_get_contents('run.txt','w+');
		if ($runFile == 'runing') {
			result('runing');
		}
	}
}

$fp = fopen('run.txt','w+');
if (!$fp) {
	result('run file open error');
}

if (!fwrite($fp,"runing")) {
	result('run file write error');
}
fclose($fp);
do{
	$startTime = time();
	@$config = file_get_contents(CONFIG_FILE);
	$config = json_decode($config,true);
	defined('OSS_ACCESS_ID')   or define('OSS_ACCESS_ID',$config['OSS_ACCESS_ID']);
	defined('OSS_ACCESS_KEY')   or define('OSS_ACCESS_KEY',$config['OSS_ACCESS_KEY']);
	defined('OSS_ENDPOINT')   or define('OSS_ENDPOINT', $config['OSS_ENDPOINT']);
	defined('OSS_BUCKET')   or define('OSS_BUCKET',empty($config['OSS_BUCKET'])?'':$config['OSS_BUCKET']);
	$interval = $config['TIME_INTERVAL'];//多长时间执行一次
	$runScript = $config['RUN_SCRIPT'];
	$channels = new synChannels(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET,$config);
	if( $runScript ){
		$channels->autoFile();
	}else{
		$fp = fopen('run.txt','w+');
		fwrite($fp," ");
		fclose($fp);
		result('stop');
		break;
	}
	$time = time()-$startTime;
	$fp = fopen('test.txt','a+');
	fwrite($fp,$time." ");
	fclose($fp);
	sleep($interval);
}while(true);

 ?>