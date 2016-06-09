<?php
	namespace Home\App;
	class AppUpdatePublishHistoryApp extends \Think\Model
	{
		protected $tableName = 'app_update_publish_history';
		protected $connection = 'DB_APP';
		protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
			'versionCode' =>'version_code',
			'versionName'  =>'version_name',
			'showTips' =>'show_tips',
			'miniForceUpdateVersion' =>'mini_force_update_version',
			'miniUpdateVersion'  =>'mini_update_version',
			'whiteList'  =>'white_list',
			'blackList'  =>'black_list',
			'groupId'=>'group_id',
			'AB'=>'ab',
		);
		public function addHistory($put)
		{
			$put['user'] = session('is_login');
			$put['user'] = $put['user']['name'];
			if (empty($put['user'])) {
				$put['user'] = '未识别';
			}
			$this->create($put);
			$this->add();
		}

		public function appUpdateHistoryLists($get)
		{
			if ( empty($get['id'])) {
				if ( !empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `model` like  '%".$get['name']."%' ")->order("time desc")->select();
					}else{
						$res['extra'] = $this->where("`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `model` like  '%".$get['name']."%'  ")->order("time desc")->select();

					}
					$res['count'] = $this->where("`app_name` like '%".$get['name']."%'  or `pkgname` like  '%".$get['name']."%'  or `model` like  '%".$get['name']."%' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->order("time desc")->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $this->order("time desc")->select();
					}
					$res['count'] = $this->count();
				}
				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['desc'] = json_decode($value['desc'],true);
						$res['extra'][$key]['blackList'] = json_decode($value['blackList'],true);
						$res['extra'][$key]['whiteList'] = json_decode($value['whiteList'],true);
						$res['extra'][$key]['time'] = date('Y-m-d H:i:s',$value['time']);
						if ($res['extra'][$key]['type'] == 'ALL') {
							unset($res['extra'][$key]['AB']);
							unset($res['extra'][$key]['groupId']);
						}elseif ($res['extra'][$key]['type'] == 'group') {
							unset($res['extra'][$key]['AB']);
							// unset($res['extra'][$key]['groupId']);
						}elseif ($res['extra'][$key]['type'] == 'AB') {
							unset($res['extra'][$key]['groupId']);
						}
					}
				}
			}else{
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}

				$snList = array();
				if (!empty($res['extra'])) {
					if ($res['extra']['type'] == 'group') {
						unset($res['extra']['AB']);
						$groupMembers = D('GroupMembers')->field('sn')->where("`group_id`=%d",array($res['extra']['groupId']))->select();
						if ($groupMembers) {
							foreach ($groupMembers as $key => $value) {
								$snList[] = $value['sn'];
							}
						}
					}
				}
				$res['extra'] = $snList;
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		/*public function modifyAppUpdate($put)

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
			if ($get['id'] ===null) {
				if ($get['name'] != null) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `desc` like  '%".$get['name']."%'")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->where("`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `desc` like  '%".$get['name']."%'")->select();
					}
					$res['count'] = $this->where("`app_name` like '%".$get['name']."%'  or `pkgname` like  '%".$get['name']."%'  or ` or `desc` like  '%".$get['name']."%'` like  '%".$get['name']."%'")->count();
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
				if ($get['name'] != null) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = D('AppUpdateVersions','App')->limit($get['page'],$get['pageSize'])->where("``app_id`=".$get['id']." and ( `version_code` like '%".$get['name']."%')")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = D('AppUpdateVersions','App')->where("``app_id`=".$get['id']." and ( `version_code` like '%".$get['name']."%')")->select();
					}
					$res['count'] = D('AppUpdateVersions','App')->where("``app_id`=".$get['id']." and ( `version_code` like '%".$get['name']."%')")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = D('AppUpdateVersions','App')->where("`app_id`=%d",array($get['id']))->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = D('AppUpdateVersions','App')->where("`app_id`=%d",array($get['id']))->select();
					}
					$res['count'] = $this->count();
				}
				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['desc'] = json_decode($value['desc'],true);
						$res['extra'][$key]['path'] = DOWNLOAD_APK_PREFIX_ADDR.$value['path'];
					}
				}

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = 0;
			}
			return $res;
		}*/


	}