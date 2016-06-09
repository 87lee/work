<?php
	namespace Home\App;
	class AppUpdateApp extends \Think\Model
	{
		protected $tableName = 'app_update';
		protected $connection = 'DB_APP';
		protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);
		public function addAppUpdate($put)

		{

			if(!empty($put['appName']) &&!empty($put['pkgName'])  &&!empty($put['channel']) ) {
				//检查数据库是否存在版本
				$res = $this->where("`app_name` ='%s'",array($put['appName']))->find();
	 			if ($res) {
					result('该应用已存在');
				}
				$options = array(
					"appName"=>$put['appName'],
					"pkgName"=>$put['pkgName'],
					"channel"=>$put['channel']
				);
				if (!empty($put['desc'])) {
					$options['desc'] = $put['desc'];
				}else{
					$options['desc'] = '';
				}
				$this->create($options);
				return $id = $this->add();
			}else{
				result('param');
			}

			return $options;

		}
		public function modifyAppUpdate($put)

		{

			if(!empty($put['id']) &&!empty($put['appName']) &&!empty($put['pkgName'])  &&!empty($put['channel']) ) {
				//检查数据库是否存在版本
				$res = $this->find($put['id']);
	 			if (!$res) {
					result('该应用不存在');
				}
				//检查数据库是否存在版本
				$res = $this->where("`app_name` ='%s' and `id` !=%d",array($put['appName'],$put['id']))->find();
	 			if ($res) {
					result('该应用已存在');
				}
				$options = array(
					"appName"=>$put['appName'],
					"pkgName"=>$put['pkgName'],
					"channel"=>$put['channel']
				);
				if (!empty($put['desc'])) {
					$options['desc'] = $put['desc'];
				}else{
					$options['desc'] = '';
				}
				$this->create($options);
				return $id = $this->where("`id`=%d",array($put['id']))->save();
			}else{
				result('param');
			}

			return $options;

		}
		public function deleteAppUpdate($id)
		{
			$res = $this->find($id);
			if ($res) {
				$res = D('AppUpdateVersions','App')->where("`app_id`=%d",array($id))->find();
				if (!$res) {
					$this->delete($id);
					return true;
				}else{
					result('请先删除该应用的版本');
				}
			}
		}
		public function appUpdateLists($get)
		{
			if ( empty($get['id'])) {
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `desc` like  '%".$get['name']."%'")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->where("`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `desc` like  '%".$get['name']."%'")->select();
					}
					$res['count'] = $this->where("`app_name` like '%".$get['name']."%'  or `pkgname` like  '%".$get['name']."%'  or `desc` like  '%".$get['name']."%'")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $this->select();
					}
					$res['count'] = $this->count();
				}

			}else{
				$AppUpdateVersions = D('AppUpdateVersions','App');
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $AppUpdateVersions->limit($get['page'],$get['pageSize'])->where("`app_id`=".$get['id']." and ( `version_code` like '%".$get['name']."%')")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $AppUpdateVersions->where("`app_id`=".$get['id']." and ( `version_code` like '%".$get['name']."%')")->select();
					}
					$res['count'] = $AppUpdateVersions->where("`app_id`=".$get['id']." and ( `version_code` like '%".$get['name']."%')")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $AppUpdateVersions->where("`app_id`=%d",array($get['id']))->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $AppUpdateVersions->where("`app_id`=%d",array($get['id']))->select();
					}
					$res['count'] = $AppUpdateVersions->where("`app_id`=%d",array($get['id']))->count();
				}
				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['desc'] = json_decode($value['desc'],true);
						$res['extra'][$key]['path'] = C('DOWNLOAD_APK_PREFIX_ADDR') . $value['path'];
					}
				}

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}


	}