<?php
	namespace Home\App;
	class App3rdApp extends \Think\Model
	{
		protected $tableName = '3rd_app';
		protected $connection = 'DB_APP';
		protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);
		public function addNewApk($put)

		{
			//检查数据库是否存在版本
			$res = $this->where("`app_name` ='%s'",array($put['appName']))->find();
 			if ($res) {
				result('该应用已存在');
			}
			if (empty($_FILES['icon'])) {
				result('请上传图标');
			}
			if ($_FILES['icon']['size'] >= C('DESKTOP_SLOT_IMG') ) {
				result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
			}elseif ($_FILES['icon']['size'] == 0) {
				result('请上传图标');
			}
			//是否为上传apk文件
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

				require_once '../Base/function/Form.class.php';
				$form = new \form();
				$formData = $form->getFormFile();

				$formData['apkFile']['md5_file']= $pkgName.'/'.$versionCode.'_'.$formData['apkFile']['md5_file'];

				require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				$res = $base->uploadFile($formData);

				$options = array(
					"appName"=>$put['appName'],
					"pkgName"=>$pkgName,
					"icon"=>$res['icon']['oss']
				);

				$this->create($options);
				$id = $this->add();
				$options = array(
					'appId'=>$id,
					'versionCode'=>$versionCode,
					'versionName'=>$versionName,
					'path3rd'=>'',
					'path'=>$res['apkFile']['oss'],
					"appName"=>$put['appName'],
					"pkgName"=>$pkgName,
				);
			}elseif (!empty($put['appName']) &&!empty($put['pkgName']) ) {
				require_once '../Base/function/Form.class.php';
				$form = new \form();
				$formData = $form->getFormFile();
				require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				$res = $base->uploadFile($formData);
				$options = array(
					"appName"=>$put['appName'],
					"pkgName"=>$put['pkgName'],
					"icon"=>$res['icon']['oss']
				);
				$this->create($options);
				$id = $this->add();
				$options = false;
			}else{
				result('param');
			}

			return $options;

		}
		public function deleteApk($id)
		{
			$res = $this->find($id);
			if (!$res) {
				result('该应用不存在');
			}
			D('App3rdVersions','App')->deleteApkVersionAll($id);
			$this->delete($id);
			return true;
		}
		public function apkLists($id = null,$name= null ,$page=null,$pageSize=null,$appName=null)
		{
			$field = "`id`,`app_name`,`pkgname`,if( `icon` != '',concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',`icon`),'' ) as `icon`";
			if ($id ===null) {
				$where = '';
				if ($name != null) {
					$where = "`app_name` like '%".$name."%'  or `pkgname` like  '%".$name."%'";
				}
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->field($field)->limit($page,$pageSize)->where($where)->select();
				}else{
					$res['extra'] = $this->field($field)->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}
			}else{
				$res['extra'] = $this->field($field)->find($id);
				if (empty($res['extra'])) {
					$res['extra'] = array();
				}
			}

			result(true,$res);
		}
		public function modifyApk($put)
		{
			$res = $this->find($put['id']);
			if (!$res) {
				result('该应用不存在');
			}else{
				$res = $this->where("`app_name` ='%s' and id !=%d",array($put['appName'],$put['id']))->find();
				if ($res) {
					result('该应用名称已存在');
				}
			}
			$options = array(
				'appName'=>$put['appName'],
			);

			if (!empty($_FILES['icon'])) {
				if ($_FILES['icon']['size'] >= C('DESKTOP_SLOT_IMG')) {
					result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
				}elseif ($_FILES['icon']['size'] == 0) {
					result('请上传图标');
				}
				require_once '../Base/function/Form.class.php';
				$form = new \form();
				$formData = $form->getFormFile();
				require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				$res = $base->uploadFile($formData);
				$options['icon'] = $res['icon']['oss'];
			}
			if ($this->create($options)) {
				return $this->where("`id`=%d",array($put['id']))->save();
			}
		}

	}