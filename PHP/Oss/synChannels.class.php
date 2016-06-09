<?php 
require_once dirname(__FILE__).'/../Base/osssdk/sdk.class.php';
require_once dirname(__FILE__).'/../Base/osssdk/util/oss_util.class.php';
require_once '../Base/function/function.php';
class synChannels{
	
	public $oss = '';
	public $config = '';
	public $endpoint = '';
	public $accessKeyId = '';
	public $accesKeySecret = '';
	public $bucket = '';
	function __construct($access_id = NULL, $access_key = NULL, $hostname = NULL, $ossBucket = NULL,$config)
	{
		define('OSS_ACCESS_ID',$access_id);
		define('OSS_ACCESS_KEY',$access_key);
		define('OSS_ENDPOINT',$hostname );
		define('OSS_BUCKET',$ossBucket);
		$this->accessKeyId = OSS_ACCESS_ID;
		$this->accesKeySecret = OSS_ACCESS_KEY;
		$this->endpoint = OSS_ENDPOINT;
		$this->bucket = OSS_BUCKET;
		$this->config = $config;
		//初始化
		$this ->oss = new ALIOSS($this->accessKeyId, $this->accesKeySecret, $this->endpoint);
	}

	/**
	 * 开始同步
	 * @param  string $dirBootPath [description]
	 * @param  string $bucket      [description]
	 * @param  string $savePath    [description]
	 * @return [type]              [description]
	 */
	// public function iniSny($dirBootPath = '',$bucket='',$savePath='')
	// {
	// 	$bucket = !empty($bucket)?$bucket:$this->bucket;

	// 	$dirBootPath = !empty($dirBootPath)?$dirBootPath:realpath($this->config['CHANNEL_BOOT_PATH']);
	// 	if (is_array($this->config['CHANNEL_LISTS'])) {
	// 		$dirBootPathList = $this->config['CHANNEL_LISTS'];
	// 	}else{
	// 		result('param');
	// 	}
		
	// 	if (empty($dirBootPathList)) {
	// 		result('not channels list');
	// 	}

	// 	/* 在目录里面进行作业 */
	// 	foreach($dirBootPathList as $row)
	// 	{	

	// 		if (is_dir($dirBootPath .DIRECTORY_SEPARATOR. $row)) {

	// 			$dirList = scandir($dirBootPath .DIRECTORY_SEPARATOR. $row);

	// 			foreach ($dirList as $value) {
	// 				/*检查目录*/
	// 				$xml = $this->ossDirList($bucket,$row.'/'.$value);

	// 				if (empty($xml['Contents'])) {
	// 					/*创建目录*/
	// 					if ( file_exists( $dirBootPath.DIRECTORY_SEPARATOR.$row.DIRECTORY_SEPARATOR.$value ) ) {
							
						// 	$mkdir = $this->mkdir($row.'/'.$value,$bucket);
						// }
	// 				}

	// 				$fileList = scandir($dirBootPath .DIRECTORY_SEPARATOR. $row .DIRECTORY_SEPARATOR.$value);

	// 				foreach ($fileList as $val) {

	// 					$localFileName = $dirBootPath.DIRECTORY_SEPARATOR.$row.DIRECTORY_SEPARATOR.$value . DIRECTORY_SEPARATOR . $val;	
	// 					$ossFileName = $row . '/'.$value . '/' .$val ;
	// 					/*OSS文件是否存在*/
	// 					if (!$this->isObjectExist($ossFileName,$bucket)) {
	// 						/*本地文件是否存在*/
	// 						if (is_file($localFileName)) {
	// 							$this->uploadFileByFile($bucket,$ossFileName,$localFileName);
	// 						}
						
	// 					}
	// 				}
	// 			}
	// 		}
			
	// 	}
	// 	result();
	// }
	
	/**
	 * 每隔一段时间同步一次
	 * @param  [type]
	 * @param  string
	 * @param  string
	 * @return [type]
	 */
	
	public function autoFile($dirBootPath = '',$bucket='',$savePath='')
	{	
		
		$bucket = !empty($bucket)?$bucket:$this->bucket;

		$dirBootPath = !empty($dirBootPath)?$dirBootPath:realpath($this->config['CHANNEL_BOOT_PATH']);

		if (is_array($this->config['CHANNEL_LISTS'])) {
			$dirBootPathList = $this->config['CHANNEL_LISTS'];
		}else{
			result('param');
		}
		
		if (empty($dirBootPathList)) {
			result('not channels list');
		}
		
		/* 在目录里面进行作业 */
		foreach($dirBootPathList as $row)
		{
			if (is_dir($dirBootPath.DIRECTORY_SEPARATOR.$row)) {

				$dirPath = intval( ( time()-30 )/3600)*3600;

				$filePath = (intval( (time()-30)/10)*10).$this->config['FILE_EXTENSION'];
				/*检查目录*/
				$xml = $this->isObjectExist($row . '/'.$dirPath,$bucket);
				if (!$xml) {
					if ( file_exists( $dirBootPath.DIRECTORY_SEPARATOR.$row.DIRECTORY_SEPARATOR.$dirPath ) ) {
						// 创建OSS目录
						$mkdir = $this->mkdir($row . '/'.$dirPath,$bucket);
						if (!$mkdir) {
							result('mkdir error');
						}
						
					}
				}
				$localFileName = $dirBootPath.DIRECTORY_SEPARATOR.$row.DIRECTORY_SEPARATOR.$dirPath . DIRECTORY_SEPARATOR . $filePath;	
				$ossFileName = $row . '/'.$dirPath . '/' .$filePath ;	
				
				if (!$this->isObjectExist($ossFileName,$bucket)) {

					if (is_file($localFileName)) {
						
						if (!$this->uploadFileByFile($bucket,$ossFileName,$localFileName)) {
							result('update file error');
						}
					}
				}
			}
			

		}

		
	}
	
	/**
	 * 简单上传
	 *上传指定的本地文件内容
	 * @param  [type] $bucket    
	 * @param  [string] $object    [OSS文件路径]
	 * @param  [type] $file_path [本地文件路径]
	 * @param  [type] $options   [description]
	 * @return [type]            [description]
	 */
	public function uploadFileByFile($bucket, $object, $file_path, $options = array())
	{
		$res = $this->oss->upload_file_by_file($bucket, $object, $file_path, $options);
		if ($res->status === 200) {
		    	return true;
		}else{
			return false;
		}
		
	}

	public function isObjectExist($object,$bucket = '')
	{	
		$res = !empty($bucket)?$this->oss->is_object_exist($bucket, $object):$this->oss->is_object_exist($this->bucket, $object);
		if ($res->status === 200) {
		    	return true;
		}else{
			return false;
		}
	}
	
	/**
	 *列出目录下的文件和子目录
	 */
	/*
	public function ossDirList($bucket,$prefix = ''){

		$prefix = $prefix;
		$marker = '';
		$delimiter = '/';
		$next_marker = '';
		$maxkeys = 1000;
		$index = 1;
		$options = array(
		        'delimiter' => $delimiter,
		        'prefix' => $prefix,
		        'max-keys' => $maxkeys,
		        'marker' => $next_marker,
	    	);
		$bucket = !empty($bucket)?$bucket:$this->bucket;
		$res = $this->oss->list_object($bucket, $options);
		if ($res->isOk()){
		    $body = $res->body;
		    $xml = new SimpleXMLElement($body);
		    return $xml;
		}
	}*/
	/**
	 *创建模拟文件夹
	 *OSS服务是没有文件夹这个概念的，所有元素都是以Object来存储。但给用户提供了创建模拟文件夹的方式
	 */
	public function mkdir($object,$bucket = '')
	{	
		 $res = !empty($bucket)?$this->oss->create_object_dir($bucket, $object):$this->oss->create_object_dir($this->bucket, $object);
		
		if ($res->status === 200) {
		    	return true;
		}else{
			return false;
		}
	}
}

 ?>