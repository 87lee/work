<?php

require_once dirname(__FILE__).'/../osssdk/sdk.class.php';
require_once dirname(__FILE__).'/../osssdk/util/oss_util.class.php';

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
		$this ->oss = new ALIOSS($this->accessKeyId, $this->accesKeySecret, $this->endpoint);
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
			$res = $this->oss->delete_object($bucket, $object);
			if ($res->status===204) {
				return true;
			}else{
				result('delete oss file error :' .$res->status);
			}
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
				$res = $this->oss->delete_object($bucket, $ossFileName);
				if ($res->status===204) {
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
					result('delete oss file error :' .$res->status);
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
		$res = $this->oss->upload_file_by_file($bucket, $object, $file_path, $options);
		if ($res->status === 200) {
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
		$res = !empty($bucket)?$this->oss->is_object_exist($bucket, $object):$this->oss->is_object_exist($this->bucket, $object);
		if ($res->status === 200) {
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
		 $res = !empty($bucket)?$this->oss->create_object_dir($bucket, $object):$this->oss->create_object_dir($this->bucket, $object);

		if ($res->status === 200) {
		    	return true;
		}else{
			return false;
		}
	}
}
