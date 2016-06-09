<?php
	namespace Home\App;
	class LibraryUpdateVersionsApp extends \Think\Model
	{
		protected $tableName = 'app_update_versions';
		protected $connection = 'DB_APP';
		protected $_map = array(
			'appId' =>'app_id',
			'versionName'  =>'version_name',
			'versionCode'  =>'version_code',
		);
		public function addAppUpdateVersion($put)
 		{

			if (!empty($put['desc'])) {
				$put['desc'] = json_encode($put['desc'],JSON_UNESCAPED_UNICODE);
			}else{
				$put['desc'] = '[]';
			}
			if ($_FILES['appFile']) {
				set_time_limit(0);
				$filename = $_FILES['appFile']['tmp_name'];
				$appObj  = new \Org\ApkParser();
				$res   = $appObj->open($filename);
				if (null == $res) {
				   	result("文件损坏,请重新上传");
				}
				$pkgName = $appObj->getPackage();    // 应用包名
				$versionCode = $appObj->getVersionCode();  // 版本代码
				$res = D('AppUpdate','App')->find($put['appId']);
	 			if ($res) {

					if ($pkgName != $res['pkgName']) {
						result('上传版本包名与应用包名不相同');
					}else{
						if ($this->where("`app_id`=%d and `version_code` = '%s'",array($put['appId'],$versionCode))->find()) {
							result('该版本已存在');
						}
					}
				}else{
					result('该应用不存在');
					D('AppUpdate','App')->where()->find();
				}

				$versionName = $appObj->getVersionName();// 版本名称
				$channel = $appObj->getLinkinChannel();// 版本名称
				if (empty($channel)) {
					$channel = 'none';
				}
				if ($channel !=$res['channel']) {
					result('上传版本渠道与应用渠道不相同');
				}
				require_once '../Base/function/Form.class.php';
				$form = new \form();
				$formData = $form->getFormFile();

				$formData['appFile']['md5_file']= $pkgName.'/'.$versionCode.'_'.$channel.'_'.$formData['appFile']['md5_file'];

				require_once '../Base/Ossupclass/OssBase.class.php';

				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				$res = $base->uploadFile($formData);
				$options = array(
					"app_id"=>$put['appId'],
					"version_code"=>$versionCode,
					"version_name"=>$versionName,
					'md5'=>md5_file($_FILES['appFile']['tmp_name']),
					"path"=>$res['appFile']['oss'],
					"desc"=>$put['desc']
				);
				$this->create($options);
				return $id = $this->add();
			}else{
				result('没有上传文件');
			}

		}
		public function deleteAppUpdateVersion($put)
		{
			$put = array_unique($put);
			$deleteSqlStr = implode(',',$put);
			$deleteSqlStr = trim($deleteSqlStr);
			return $this->where("`id` IN (".$deleteSqlStr.")")->delete();
		}

	}