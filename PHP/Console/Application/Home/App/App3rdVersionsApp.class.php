<?php
	namespace Home\App;
	class App3rdVersionsApp extends \Think\Model
	{
		protected $tableName = '3rd_app_versions';
		protected $connection = 'DB_APP';
		protected $_map = array(
			'versionCode'  =>'version_code',
			'versionName'  =>'version_name',
			'path3rd'  =>'path_3rd',
			'appId'  =>'app_id',
		);
		public function addNewApk($options)
		{

			if ($this->create($options)) {
				return $this->add();
			}

		}

		public function addApk($put)
		{
			//检查数据库是否存在版本
			$app = D('App3rd','App')->find($put['appId']);
 			if (!$app) {
				result('该应用不存在');
			}
			if ($_FILES['apkFile']) {
				set_time_limit(0);

				$filename = $_FILES['apkFile']['tmp_name'];
				$appObj  = new \Org\ApkParser();
				$res   = $appObj->open($filename);

				if (false === $res) {
				   result("文件损坏,请重新上传");
				}
				$pkgName = $appObj->getPackage();    // 应用包名

				$versionCode = $appObj->getVersionCode();  // 版本代码
				$versionName = $appObj->getVersionName();// 版本名称

				if ($app['pkgName'] !=$pkgName ) {
					result('上传应用与原来应用包名不一样');
				}
				$res = $this->where("`app_id`=%d and version_code='%s'",array($put['appId'],$versionCode))->find();
				if ($res) {
					result('该应用版本已存在');
				}

				require_once '../Base/function/Form.class.php';
				$form = new \form();
				$formData = $form->getFormFile();

				$formData['apkFile']['md5_file']= $pkgName.'/'.$versionCode.'_'.$formData['apkFile']['md5_file'];

				require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				$res = $base->uploadFile($formData);
				$options = array(
					'appId'=>$put['appId'],
					'versionCode'=>$versionCode,
					'versionName'=>$versionName,
					'path3rd'=>'',
					'path'=>$res['apkFile']['oss']
				);

			}elseif (!empty($put['versionCode']) &&!empty($put['path3rd']) &&!empty($put['versionName']) ) {
				$options = array(
					'appId'=>$put['appId'],
					'versionCode'=>$put['versionCode'],
					'versionName'=>$put['versionName'],
					'path3rd'=>$put['path3rd'],
					'path'=>''
				);
			}else{
				result('param');
			}
			$this->create($options);
			return $this->add();

		}
		public function deleteApkVersion($id)
		{
			$res = $this->find($id);
			if ($res) {
				$this->delete($id);
				return true;
			}else{
				result('该应用不存在');
			}
		}
		public function deleteApkVersionAll($appId)
		{
			$res = $this->where('app_id=%d',array($appId))->find();
			if ($res) {
				$this->where('app_id=%d',array($appId))->delete();
				return true;
			}else{
				return true;
			}
		}
		public function apkLists($get)
		{
			$field = "`id`,`version_code`,`version_name`,if( `path` != '',concat('".C('DOWNLOAD_APK_PREFIX_ADDR')."',`path`),'' ) as `path`,`path_3rd`";

			if (!empty($get['id'])) {
				$where = "`app_id`=" . intval($get['id']);
				if (!empty($get['name'])) {
					$where .= " AND `version_code` like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$res['extra'] = $this->field($field)->limit($page,$pageSize)->where($where)->select();
				}else{
					$res['extra'] = $this->field($field)->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}elseif (!empty($get['appName'])) {
				$App3rd = D('App3rd','App')->where("`app_name`='%s'",array($get['appName']))->find();
				if ($App3rd) {
					$res['extra'] = $this->field($field)->where("`app_id`=%d",array($App3rd['id']))->select();
					$res['count'] = $this->field($field)->where("`app_id`=%d",array($App3rd['id']))->count();
				}
				}

			/*if ($name != null) {
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->field("`id`,`version_code`,`version_name`,if( `path` != '',concat('".C('DOWNLOAD_APK_PREFIX_ADDR')."',`path`),'' ) as `path`,`path_3rd`")->limit($page,$pageSize)->where("`version_code` like '%".$name."%' AND `app_id` =".$appId)->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field("`id`,`version_code`,`version_name`,if( `path` != '',concat('".C('DOWNLOAD_APK_PREFIX_ADDR')."',`path`),'' ) as `path`,`path_3rd`")->where("`version_code` like '%".$name."%' AND `app_id` =".$appId)->select();
				}
				$res['count'] = $this->where("`version_code` like '%".$name."%' ")->count();
			}else{
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->field("`id`,`version_code`,`version_name`,if( `path` != '',concat('".C('DOWNLOAD_APK_PREFIX_ADDR')."',`path`),'' ) as `path`,`path_3rd`")->limit($page,$pageSize)->where("`app_id`=%d",array($appId))->select();
				}else{
					$res['extra'] = $this->field("`id`,`version_code`,`version_name`,if( `path` != '',concat('".C('DOWNLOAD_APK_PREFIX_ADDR')."',`path`),'' ) as `path`,`path_3rd`")->where("`app_id`=%d",array($appId))->select();
				}
				$res['count'] = $this->count();
			}*/
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			result(true,$res);
		}
		public function modifyApkVersion($put)
		{
			if (!empty($put['id'])&&!empty($put['path3rd'])) {
				$res = $this->find($put['id']);
				if (!$res) {
					result('该应用不存在');
				}
				$options = array(
					'path3rd'=>$put['path3rd']
				);
				if ($this->create($options)) {
					$this->where("`id`=%d",array($put['id']))->save();
					// echo $this->getLastSql();
				}
			}else{
				result('param');
			}


		}

	}