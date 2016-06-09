<?php
require_once  __DIR__ . '/../OSS/OssClient.php';
require_once  dirname(__FILE__).'/../OSS/autoload.php';

class base{
	public $oss = '';
	public $endpoint = '';
	public $accessKeyId = '';
	public $accesKeySecret = '';
	public $bucket = '';
	function __construct($access_id = NULL, $access_key = NULL, $hostname = NULL, $ossBucket = NULL)
	{
		define('OSS_ACCESS_ID',$access_id);
		define('OSS_ACCESS_KEY',$access_key);
		define('OSS_ENDPOINT',$hostname );
		define('OSS_BUCKET',$ossBucket);
		$this->accessKeyId = OSS_ACCESS_ID;
		$this->accesKeySecret = OSS_ACCESS_KEY;
		$this->endpoint = OSS_ENDPOINT;
		$this->bucket = OSS_BUCKET;
		//初始化
		$this ->oss = new \OSS\OssClient($this->accessKeyId, $this->accesKeySecret, $this->endpoint);
	}

	/**
	 * 删除object
	 * @param  [string] $object [文件路径]
	 * @param  string $bucket [插槽]
	 * @return [type]         [description]
	 */
	public function deleteFile($object,$bucket = '')
	{
		$bucket = !empty($bucket)?$bucket:$this->bucket;

		if ($this->isObjectExist($object,$bucket)) {
			try{
				$res = $this->oss->deleteObject($bucket, $object);
			}catch(\OSS\Core\OssException $e){
				result($e->getMessage());
			}
			return true;
		}else{
			return true;
		}

	}
	/**
	 * 上传OSS
	 * @param  [type] $array  array("aaa"=>array("extension"=>"", "md5_file"=>"098f6bcd4621d373cade4e832627b4f6","filepath"=>"D:\xampp\tmp\phpFAFA.tmp","size"=>4))
	 * @param  string $bucket [description]
	 * @return [type]         [description]
	 */
	public function uploadFile($array,$bucket = '')
	{
		$bucket = !empty($bucket)?$bucket:$this->bucket;

		foreach ($array as $key => $value) {

			$ossDir = $this->ossDir( strtolower($value['extension']) );
			// 检查目录
			$xml = $this->isObjectExist($ossDir,$bucket);
			if (!$xml) {
				// 创建OSS目录
				$mkdir = $this->mkdir($ossDir,$bucket);
				if (!$mkdir) {
					result('mkdir error');
				}
			}
			// 检查文件
			if(empty($value['extension'])){
				$ossFileName = $ossDir .'/'.$value['md5_file'];
			}else{
				$ossFileName = $ossDir .'/'.$value['md5_file'].'.'.$value['extension'];
			}

			if (!$this->isObjectExist($ossFileName,$bucket)) {

				if (file_exists($value['filepath'])) {

					if (!$this->uploadFileByFile($bucket,$ossFileName,$value['filepath'])) {
						result('update file error');
					}else{
						$data[$key]['oss']=$ossFileName;
						$data[$key]['download']='http://'.$this->bucket.'.'.$this->endpoint.'/'.$ossFileName;

					}
				}else{
					result('local file not exists');
				}
			}else{
				$data[$key]['oss']=$ossFileName;
				$data[$key]['download']='http://'.$this->bucket.'.'.$this->endpoint.'/'.$ossFileName;
			}

		}
		if (!empty($data)) {
			return $data;
		}else{
			result('param');
		}
	}
	/**
	 * 上传OSS
	 * @param  [type] $array  array("aaa"=>array("extension"=>"", "md5_file"=>"098f6bcd4621d373cade4e832627b4f6","filepath"=>"D:\xampp\tmp\phpFAFA.tmp","size"=>4))
	 * @param  string $bucket [description]
	 * @return [type]         [description]
	 */
	public function coveUploadFile($array,$bucket = '')
	{
		$bucket = !empty($bucket)?$bucket:$this->bucket;

		foreach ($array as $key => $value) {

			$ossDir = $this->ossDir( strtolower($value['extension']) );
			// 检查目录
			$xml = $this->isObjectExist($ossDir,$bucket);
			if (!$xml) {
				// 创建OSS目录
				$mkdir = $this->mkdir($ossDir,$bucket);
				if (!$mkdir) {
					result('mkdir error');
				}
			}
			// 检查文件
			if(empty($value['extension'])){
				$ossFileName = $ossDir .'/'.$value['md5_file'];
			}else{
				$ossFileName = $ossDir .'/'.$value['md5_file'].'.'.$value['extension'];
			}
			if (!$this->isObjectExist($ossFileName,$bucket)) {

				if (file_exists($value['filepath'])) {

					if (!$this->uploadFileByFile($bucket,$ossFileName,$value['filepath'])) {
						result('update file error');
					}else{
						$data[$key]['oss']=$ossFileName;
						$data[$key]['download']='http://'.$this->bucket.'.'.$this->endpoint.'/'.$ossFileName;

					}
				}else{
					result('oss lose :local file not exists');
				}
			}else{
				try{
					$res = $this->oss->deleteObject($bucket, $ossFileName);
					if (file_exists($value['filepath'])) {
						if (!$this->uploadFileByFile($bucket,$ossFileName,$value['filepath'])) {
							result('update file error');
						}else{
							$data[$key]['oss']=$ossFileName;
							$data[$key]['download']='http://'.$this->bucket.'.'.$this->endpoint.'/'.$ossFileName;

						}
					}else{
						result('oss lose :local file not exists');
					}
				}catch(\OSS\Core\OssException $e){
					result($e->getMessage());
				}


			}
		}
		if (!empty($data)) {
			return $data;
		}else{
			result('param');
		}
	}
	/**
	 * 上传OSS
	 * @param  [type] $array  array("aaa"=>array("extension"=>"", "md5_file"=>"098f6bcd4621d373cade4e832627b4f6","filepath"=>"D:\xampp\tmp\phpFAFA.tmp","size"=>4))
	 * @param  string $bucket [description]
	 * @return [type]         [description]
	 */
	public function coveUploadFileForPath($array,$bucket = '')
	{
		$bucket = !empty($bucket)?$bucket:$this->bucket;

		foreach ($array as $key => $value) {


			// 检查文件
			if(empty($value['extension'])){
				$ossFileName = $value['md5_file'];
			}else{
				$ossFileName = $value['md5_file'].'.'.$value['extension'];
			}
			if (!$this->isObjectExist($ossFileName,$bucket)) {

				if (file_exists($value['filepath'])) {

					if (!$this->uploadFileByFile($bucket,$ossFileName,$value['filepath'])) {
						result('update file error');
					}else{
						$data[$key]['oss']=$ossFileName;
						$data[$key]['download']='http://'.$this->bucket.'.'.$this->endpoint.'/'.$ossFileName;

					}
				}else{
					result('oss lose :local file not exists');
				}
			}else{
				try{
					$res = $this->oss->deleteObject($bucket, $ossFileName);
					if (file_exists($value['filepath'])) {
						if (!$this->uploadFileByFile($bucket,$ossFileName,$value['filepath'])) {
							result('update file error');
						}else{
							$data[$key]['oss']=$ossFileName;
							$data[$key]['download']='http://'.$this->bucket.'.'.$this->endpoint.'/'.$ossFileName;

						}
					}else{
						result('oss lose :local file not exists');
					}
				}catch(\OSS\Core\OssException $e){
					result($e->getMessage());
				}


			}
		}
		if (!empty($data)) {
			return $data;
		}else{
			result('param');
		}
	}
	/**
	 * 上传OSS
	 * @param  [type] $array  array("aaa"=>array("extension"=>"", "md5_file"=>"098f6bcd4621d373cade4e832627b4f6","filepath"=>"D:\xampp\tmp\phpFAFA.tmp","size"=>4))
	 * @param  string $bucket [description]
	 * @return [type]         [description]
	 */
	public function uploadFileForPath($array,$bucket = '')
	{
		$bucket = !empty($bucket)?$bucket:$this->bucket;

		foreach ($array as $key => $value) {

			// 检查文件
			if(empty($value['extension'])){
				$ossFileName =  $value['md5_file'];
			}else{
				$ossFileName = $value['md5_file'].'.'.$value['extension'];
			}
			if (!$this->isObjectExist($ossFileName,$bucket)) {

				if (file_exists($value['filepath'])) {

					if (!$this->uploadFileByFile($bucket,$ossFileName,$value['filepath'])) {
						result('update file error');
					}else{
						$data[$key]['oss']=$ossFileName;
						$data[$key]['download']='http://'.$this->bucket.'.'.$this->endpoint.'/'.$ossFileName;

					}
				}else{
					result('local file not exists');
				}
			}else{
				$data[$key]['oss']=$ossFileName;
				$data[$key]['download']='http://'.$this->bucket.'.'.$this->endpoint.'/'.$ossFileName;
			}
		}
		if (!empty($data)) {
			return $data;
		}else{
			result('param');
		}
	}
	/**
	 * 列出Bucket内所有目录和文件, 注意如果符合条件的文件数目超过设置的max-keys， 用户需要使用返回的nextMarker作为入参，通过
	 * 循环调用ListObjects得到所有的文件，具体操作见下面的 listAllObjects 示例
	 *
	 * @param OssClient $ossClient OssClient实例
	 * @param string $bucket 存储空间名称
	 * @return null
	 */
	function listAllObjects($bucket='')
	{
		$bucket = !empty($bucket)?$bucket:$this->bucket;
    		$prefix = 'pic/';
    		$delimiter = '/';
    		$nextMarker = '';
    		$maxkeys = 100;

		 while (true) {

	    		$options = array(
	        			'delimiter' => $delimiter,
	        			'prefix' => $prefix,
	        			'max-keys' => $maxkeys,
	        			'marker' => $nextMarker,
	    		);
	    		try {
	        			$listObjectInfo = $this->oss->listObjects($bucket, $options);
	    		} catch (\OSS\Core\OssException $e) {
	        			printf($e->getMessage() . "\n");
	        			return;
	    		}
	    		$objectList = $listObjectInfo->getObjectList(); // 文件列表


	    		// $prefixList = $listObjectInfo->getPrefixList(); // 目录列表
	    		if (!empty($objectList)) {
	        			foreach ($objectList as $objectInfo) {
	        				if ($prefix ==$objectInfo->getKey()) {
	        					continue;
	        				}
	            				$objectArr[] = $objectInfo->getKey();
	        			}
	    		}
	    		// 得到nextMarker，从上一次listObjects读到的最后一个文件的下一个文件开始继续获取文件列表
	        		$nextMarker = $listObjectInfo->getNextMarker();
	        		if ($nextMarker === '') {
	        			break;
	        		}
	    	}
    		return $objectArr;
	}
	/**
	 * 修改Object Meta
	 * 利用copyObject接口的特性：当目的object和源object完全相同时，表示修改object的meta信息
	 *
	 * @param OssClient $ossClient OssClient实例
	 * @param string $bucket 存储空间名称
	 * @return null
	 */
	function modifyMetaForObject($fromObject, $bucket='')
	{
		$bucket = !empty($bucket)?$bucket:$this->bucket;
	    	$toBucket = $fromBucket = $bucket;
	    	// $fromObject = "oss-php-sdk-test/upload-test-object-name.txt";

	    	$toObject = $fromObject;

	    	$fromObjectArr = explode('.', $fromObject);

	    	if (!empty($fromObjectArr)) {
	    		$suffix = end($fromObjectArr);
	    	}else{
	    		$suffix = 'jpg';
	    	}
	    	$type = ($suffix == 'jpg')?'jpeg':$suffix;
	    	$copyOptions = array(
	        		'Content-Type' => 'image/'.$type
	    	);
	    	try {
	        		$this->oss->copyObject($fromBucket, $fromObject, $toBucket, $toObject, $copyOptions);
	    	} catch (\OSS\Core\OssException $e) {
	        		printf($e->getMessage() . "\n");
	       	 	return;
	    	}
	}

	/**
	 * 根据文件后缀存放文件夹
	 * @param  [type] $fileExtension [文件后缀]
	 * @return [type]                [description]
	 */
	public function ossDir($fileExtension)
	{
		$dirType =  array(
			'pic' => array('gif','jpg','png','swf','swc','psd','tiff','bmp','iff','jp2','jpx','jb2','jpc','xbm','jpeg'),
			'apk'=>array('apk'),
			'json'=>array('json'),
			'zip'=>array('rar','zip','tar','cab','uue','jar','iso','z','7-zip','ace','lzh','arj','gzip','bz2','gz','tgz')
		);
		foreach ($dirType as $key => $value) {
			if (in_array($fileExtension,$value)) {
				return $key;
			}
		}
		return 'other';
	}

	/**
	 * 简单上传
	 *上传指定的本地文件内容
	 * @param  [type] $bucket
	 * @param  [string] $object    [OSS文件路径]
	 * @param  [type] $file_path [本地文件路径]
	 * @param  [type] $options   [上传配置]
	 * @return [type]            [description]
	 */
	public function uploadFileByFile($bucket, $object, $file_path, $options = array())
	{

		try{
			/*$res = */!empty($bucket)?$this->oss->uploadFile($bucket, $object, $file_path, $options):$this->oss->uploadFile($this->bucket, $object, $file_path, $options);
			$res = true;
		}catch(\OSS\Core\OssException $e){
			// echo $e->getMessage();
			$res = false;
		}

		if ($res) {
		    	return true;
		}else{
			return false;
		}

	}
	/**
	 * 文件是否存在
	 * @param  [type]  $object [文件存放路径]
	 * @param  string  $bucket [description]
	 * @return boolean         [description]
	 */
	public function isObjectExist($object,$bucket = '')
	{
		try{
			$res = !empty($bucket)?$this->oss->doesObjectExist($bucket, $object):$this->oss->doesObjectExist($this->bucket, $object);

		}catch(\OSS\Core\OssException $e){

			$res = false;
		}
		if ($res) {
		    	return true;
		}else{
			return false;
		}
	}

	/**
	 *创建模拟文件夹
	 *OSS服务是没有文件夹这个概念的，所有元素都是以Object来存储。但给用户提供了创建模拟文件夹的方式
	 */
	public function mkdir($object,$bucket = '')
	{

		try{
			/*$res = */!empty($bucket)?$this->oss->createObjectDir($bucket, $object):$this->oss->createObjectDir($this->bucket, $object);
			$res = true;
		}catch(\OSS\Core\OssException $e){
			$e->getMessage();
			$res = false;
		}

		if ($res) {
		    	return true;
		}else{
			return false;
		}
	}
}
