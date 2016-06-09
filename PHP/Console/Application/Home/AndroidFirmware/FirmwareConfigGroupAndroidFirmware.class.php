<?php
	namespace Home\AndroidFirmware;
	class FirmwareConfigGroupAndroidFirmware extends \Think\Model
	{
		protected $tableName = 'firmware_config_group';
		protected $connection = 'DB_ANDROID_FIRMWARE';
		protected $_map = array(
			'desktopId' =>'desktop_id',
		);
		protected $_validate = array(
			array('name','','该配置组已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function addConfigGroup($put)
		{
			if (empty($put)) {
				result('附加数据出错');
			}
			$put = json_decode($put,true);

			if (empty($put['name'])){
				result('请填写名称');
			}
			set_time_limit(0);
			$res = $this->where("`name` ='%s'",array($put['name']))->find();
			if ($res) {
				result('该配置组已存在');
			}

			$options = $this->updateFirmwareConfig($put);
			if (empty($options['path'])) {
				result('请上传开机动画文件、环境变量文件');
			}
			if ($this->create($options,1)) {
				return $this->add();
			}else{
				result($this->getError());
			}
		}
		public function modifyConfigGroup($put)
		{
			set_time_limit(0);
			if (empty($put)) {
				result('附加数据出错');
			}
			$put = json_decode($put,true);
			if (empty($put['name'])||empty($put['id'])){
				result('附加数据出错');
			}
			$res = $this->where("`name` ='%s' and `id`!=%d",array($put['name'],$put['id']))->find();
			if ($res) {
				result('该配置组已存在');
			}
			$res = $this->find($put['id']);
			if (!$res) {
				result('该配置组不存在');
			}
			$options = $this->updateFirmwareConfig($put,true,$res);

			if ($this->create($options,2)) {
				return $this->where("`id`=%d",array($put['id']))->save();
			}else{
				result($this->getError());
			}
		}
		public function updateFirmwareConfig($put,$isdownLoad = false,$res = '')
		{
			require_once '../Base/function/Form.class.php';
			$form = new \form();
			$formData = $form->getFormFile();
			if (!empty($formData)) {

				$ossZipFile = $put['name'].'.zip';

				if (file_exists($ossZipFile)) {
					unlink($ossZipFile);
				}

				if ($isdownLoad) {
					$copyFile = @copy(C('DOWNLOAD_PREFIX_ADDR').$res['path'], $ossZipFile);
					if (!$copyFile) {
						result('下载OSS文件失败：'.C('DOWNLOAD_PREFIX_ADDR').$res['path']);
					}
				}

				$path = 'custom/';
				if (file_exists($path)) {
					removeDir($path);
				}
				$zip=new \ZipArchive();
				if (file_exists($ossZipFile)) {
					$res = $zip->open($ossZipFile);
				}else{
					$res = $zip->open($ossZipFile,\ZipArchive::CREATE);
				}

				if( $res !== TRUE){
					$ErrMsg = zipErrorMsg($res);
				        	result( '打包出错: ' . $ErrMsg);
				}

				foreach ($formData as $key => $value) {
					if ($key=='prop') {
						if ($value['extension'] != 'prop') {
							result('环境变量文件类型只能为.prop');
						}
						if ($zip->getFromName($path.'pri.prop')) {
							$zip->deleteName($path.'pri.prop');
						}
						$zip->addFile($value['filepath'],$path."pri.prop");
					}elseif ($key=='zip') {
						if ($value['extension'] != 'zip') {
							result('开机动画文件类型只能为.zip');

						}
						if ($zip->getFromName($path.'bootanimation.zip')) {
							$zip->deleteName($path.'bootanimation.zip');
						}
						$zip->addFile($value['filepath'],$path."bootanimation.zip");
					}elseif ($key=='mp4') {
						if ($value['extension'] != 'mp4') {
							result('开机视频文件类型只能为.mp4');

						}

						if ($zip->getFromName($path.'bootvideo.mp4')) {
							$zip->deleteName($path.'bootvideo.mp4');
						}
						$zip->addFile($value['filepath'],$path."bootvideo.mp4");
					}elseif ($key=='conf') {
						if ($value['extension'] != 'conf') {
							result('开机视频配置文件类型只能为.conf');
						}
						if ($zip->getFromName($path.'bootvideo.conf')) {
							$zip->deleteName($path.'bootvideo.conf');
						}
						$zip->addFile($value['filepath'],$path."bootvideo.conf");
					}
				}
			    	$zip->close(); //关闭处理的zip文件--保存zip

			    	//获取desktopID

				$res = $zip->open($ossZipFile);
				if( $res !== TRUE){
					$ErrMsg = zipErrorMsg($res);
				        	result( '打包出错: ' . $ErrMsg);
				}
				$propStr = $zip->getFromName($path.'pri.prop');
				$zip->close(); //关闭处理的zip文件
				$propArr = explode("\n", $propStr);
				foreach ($propArr as  $value) {
					$value = trim($value);
					$desktopId = strstr($value,"DesktopId=");
					if ( $desktopId !== false ) {
						$desktopId = str_replace("DesktopId=", '', $desktopId);
						break;
					}
				}

			    	$fileZipMd5 = md5_file($ossZipFile);

				$formData = array(
					"fileZip" => array(
				    		"extension" =>"zip",
						"md5_file" => "fwcg_".$fileZipMd5,
				    		"filepath" => $ossZipFile,
					    	"size" => filesize($ossZipFile)
				  	)
				);

				require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				$res = $base->uploadFile($formData);

				if (file_exists($ossZipFile)) {
					unlink($ossZipFile);
				}

				if (file_exists($path)) {
					removeDir($path);
				}

				$options = array(
					'name'=>$put['name'],
					'path'=>$res['fileZip']['oss'],
					'desktop_id'=>!empty($desktopId)?$desktopId:'',
					'md5'=>$fileZipMd5
				);

			}else{
				$options = array(
					'name'=>$put['name'],
				);
			}
			return $options;
		}
		public function deleteConfigGroup($id = null)
		{
			if ($id !=null) {
				if (!$res = $this->find($id)) {
					result('该配置组不存在');
				}
				$this->delete($id);
				return true;
			}
		}
		public function configGroupLists($id = null,$name= null ,$page=null,$pageSize=null)
		{
			$field = "`id`,name,md5,concat('".C('DOWNLOAD_PREFIX_ADDR')."',`path`) as path,desktop_id";
			if ($id ===null) {
				$where = '';
				if ($name != null) {
					$where = "`name` like '%".$name."%'";
				}
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->limit($page,$pageSize)->field($field)->where($where)->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field($field)->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();

			}else{
				$res['extra'] = $this->field($field)->find($id);
				if (!empty($res['extra'])) {
					$res['count'] = 1;
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			result(true,$res);
		}

		public function getConfigGroupLists($id = null)
		{
			if (!empty($id)) {
				return $this->field('id,name')->find($id);
			}else{
				return $this->field('id,name')->select();
			}

		}
		public function getOneForId($id)
		{
			return $this->field("md5,concat('".C('DOWNLOAD_PREFIX_ADDR')."',`path`) as url,desktop_id")->find($id);
		}
	}