<?php
	namespace Home\App;
	class JoinApp extends \Think\Model
	{
		protected $tableName = '3rd_app';
		protected $connection = 'DB_APP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/


		public function app3rdAndAppUpdateLists()
		{
			$field = "a.app_name as appName,a.pkgname as pkgName";

			$app3rd = $this->field($field)->table('tb_3rd_app as a')->join("tb_3rd_app_versions as b ON a.id = b.app_id")->group("a.app_name")->select();

			$appUpdate = $this->field($field)->table('tb_app_update as a')->join("tb_app_update_versions as b ON a.id = b.app_id")->group("a.app_name")->select();



			if (!empty($app3rd) && !empty($appUpdate) ) {
				$appNameArr = array_merge($app3rd,$appUpdate);
			}elseif (!empty($app3rd)) {
				$appNameArr = $app3rd;
			}elseif (!empty($appUpdate)) {
				$appNameArr = $appUpdate;
			}
			if (empty($appNameArr)) {
				$res['extra'] = array();
			}else{
				$res['extra'] = $appNameArr;
			}
			return $res;
		}
		public function app3rdVersionAndAppUpdateVersionLists($get)
		{
			if (!empty($get['name'])) {
				$field = "a.app_name as appName,a.pkgname as pkgName,b.version_code as versionCode,b.version_name as versionName,if( b.path != '',concat('".C('DOWNLOAD_APK_PREFIX_ADDR')."',b.path),'' ) as `path`,b.path_3rd as path3rd";


				$app3rdVersion = $this->field($field)->table('tb_3rd_app as a')->join(" RIGHT JOIN  tb_3rd_app_versions as b ON a.id = b.app_id")->where("a.app_name = '%s'",array($get['name']))->select();

				$field = "a.app_name as appName,a.pkgname as pkgName,b.version_code as versionCode,b.version_name as versionName,if( b.path != '',concat('".C('DOWNLOAD_APK_PREFIX_ADDR')."',b.path),'' ) as `path`,'' as path3rd";

				$appUpdateVersion = $this->field($field)->table('tb_app_update as a')->join("RIGHT JOIN  tb_app_update_versions as b ON a.id = b.app_id")->where("a.app_name = '%s'",array($get['name']))->select();

				/*if (!empty($app3rd)) {
					foreach ($app3rd as $key => $value) {
						$appNameArr[] = $value['app_name'];
					}
				}
				if (!empty($appUpdate)) {
					foreach ($appUpdate as $key => $value) {
						$appNameArr[] = $value['app_name'];
					}
				}*/
				if (!empty($app3rdVersion) && !empty($appUpdateVersion) ) {
					$appArr = array_merge($app3rdVersion,$appUpdateVersion);
				}elseif (!empty($app3rdVersion)) {
					$appArr = $app3rdVersion;
				}elseif (!empty($appUpdateVersion)) {
					$appArr = $appUpdateVersion;
				}

				/*
				$field = "a.app_name as appName,a.pkgname as pkgName,b.version_code as versionCode,b.version_name as versionName,if( b.path != '',concat('".DOWNLOAD_PREFIX_ADDR."',b.path),'' ) as `path`,b.path_3rd as path3rd";
				$app3rd = $this->field($field)->table('tb_3rd_app as a')->join("tb_3rd_app_versions as b ON a.id = b.app_id")->select();
				*/

			}
			if (empty($appArr)) {
				$res['extra'] = array();
			}else{
				$res['extra'] = $appArr;
			}
			return $res;
		}

	}