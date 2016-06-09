<?php
	namespace Home\App;
	class AppUpdatePublishApp extends \Think\Model
	{
		protected $tableName = 'app_update_publish';
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

		public function publishAppUpdateVersion($put,$action = 'add')
		{
			if(!empty($put['md5']) &&!empty($put['versionName']) && !empty($put['path']) && !empty($put['versionCode']) && !empty($put['channel']) && !empty($put['appName']) && !empty($put['pkgName'])  &&!empty($put['model'])  && !empty($put['vendorid'])  && !empty($put['type'])   && isset($put['miniUpdateVersion']) && isset($put['miniForceUpdateVersion'])   && ($put['fake']=='true' || $put['fake']=='false')  && ($put['showTips']=='true' || $put['showTips']=='false') && ($put['umeng']=='true' || $put['umeng']=='false') ) {

				$res = $this->where("`pkgname` ='%s' and `vendorid` = '%s' and `model` = '%s' and `type` = '%s' and `channel` = '%s' ",array($put['pkgName'],$put['vendorid'],$put['model'],$put['type'],$put['channel']))->find();
	 			if ($res) {
					$this->deletePublishAppUpdateVersion($res['id'],$action);
					if ($action == 'add' ) {
						$this->actionPublish($res,'','覆盖');
					}
				}
				if (!empty($put['desc']) && is_array($put['desc'])) {
					$put['desc'] = array_values($put['desc']);
					$put['desc'] = json_encode($put['desc'],JSON_UNESCAPED_UNICODE);
				}else{
					$put['desc'] = '[]';
				}
				if (!empty($put['whiteList']) && is_array($put['whiteList'])) {
					$put['whiteList'] = array_values($put['whiteList']);
					$put['whiteList'] = json_encode($put['whiteList'],JSON_UNESCAPED_UNICODE);
				}else{
					$put['whiteList'] = '[]';
				}
				if (!empty($put['blackList']) && is_array($put['blackList'])) {
					$put['blackList'] = array_values($put['blackList']);
					$put['blackList'] = json_encode($put['blackList'],JSON_UNESCAPED_UNICODE);
				}else{
					$put['blackList'] = '[]';
				}
				$options = array(
					"app_name"=>$put['appName'],
					"pkgname"=>$put['pkgName'],
					"channel"=>$put['channel'],
					"version_code"=>$put['versionCode'],
					"version_name"=>$put['versionName'],
					"path"=>$put['path'],
					"md5"=>$put['md5'],
					"desc"=>$put['desc'],
					"black_list"=>$put['blackList'],
					"white_list"=>$put['whiteList'],
					"model"=>$put['model'],
					"vendorid"=>$put['vendorid'],
					"umeng"=>$put['umeng'],
					"show_tips"=>$put['showTips'],
					"mini_force_update_version"=>$put['miniForceUpdateVersion'],
					"mini_update_version"=>$put['miniUpdateVersion'],
					"fake"=>$put['fake'],
					"type"=>$put['type'],
					'time'=>time(),
				);
				if ($put['type'] == 'group') {
					if (!isset($put['groupId'])) {
						result('param');
					}

					$group = D('Group')->find($put['groupId']);
					if (!$group) {
						result('组不存在');
					}
					$options['group_id'] = $put['groupId'];
				}elseif ($put['type'] == 'AB') {
					if (!isset($put['AB'])) {
						result('param');
					}
					$options['ab'] = $put['AB'];
				}
				$aa = $this->create($options);

				$this->actionPublish($options,$action);
				return $id = $this->add();
			}else{
				result('param');
			}
		}

		public function modifyPublishAppUpdateVersion($put)

		{
			if( !empty($put['id'])  && !empty($put['type'])   && isset($put['miniUpdateVersion']) && isset($put['miniForceUpdateVersion'])   && ($put['fake']=='true' || $put['fake']=='false')  && ($put['showTips']=='true' || $put['showTips']=='false') && ($put['umeng']=='true' || $put['umeng']=='false') ) {

				$publishAppUpdateVersion = $this->find($put['id']);
				if (!$publishAppUpdateVersion) {
					result('该发布不存在');
				}

				//检查数据

				if (!empty($put['desc']) && is_array($put['desc'])) {
					$put['desc'] = array_values($put['desc']);
					$put['desc'] = json_encode($put['desc'],JSON_UNESCAPED_UNICODE);
				}else{
					$put['desc'] = '[]';
				}
				if (!empty($put['whiteList']) && is_array($put['whiteList'])) {
					$put['whiteList'] = array_values($put['whiteList']);
					$put['whiteList'] = json_encode($put['whiteList'],JSON_UNESCAPED_UNICODE);
				}else{
					$put['whiteList'] = '[]';
				}
				if (!empty($put['blackList']) && is_array($put['blackList'])) {
					$put['blackList'] = array_values($put['blackList']);
					$put['blackList'] = json_encode($put['blackList'],JSON_UNESCAPED_UNICODE);
				}else{
					$put['blackList'] = '[]';
				}
				$options = array(
					"umeng"=>$put['umeng'],
					"show_tips"=>$put['showTips'],
					"mini_force_update_version"=>$put['miniForceUpdateVersion'],
					"mini_update_version"=>$put['miniUpdateVersion'],
					"fake"=>$put['fake'],
					"type"=>$put['type'],
					"desc"=>$put['desc'],
					"black_list"=>$put['blackList'],
					"white_list"=>$put['whiteList'],
					'time'=>time(),
				);
				if ($put['type'] == 'group') {
					if (!isset($put['groupId'])) {
						result('param');
					}

					$group = D('Group')->find($put['groupId']);
					if (!$group) {
						result('组不存在');
					}
					$options['group_id'] = $put['groupId'];

				}elseif ($put['type'] == 'AB') {
					if (!isset($put['AB'])) {
						result('param');
					}
					$options['ab'] = $put['AB'];
				}
				$res = $this->where("`pkgname` ='%s' and `vendorid` = '%s' and `model` = '%s' and `type` = '%s' and `channel` = '%s' and `id` !=%d",array($publishAppUpdateVersion['pkgName'],$publishAppUpdateVersion['vendorid'],$publishAppUpdateVersion['model'],$put['type'],$publishAppUpdateVersion['channel'],$publishAppUpdateVersion['id']))->find();

				if ( $publishAppUpdateVersion['type'] == $put['type']) {
					if (!$res) {
						$this->create($options);
						$this->where('`id`=%d',array($put['id']))->save();
						$this->actionPublish($publishAppUpdateVersion,'modify');
					}

				}else{

		 			if ($res) {
						$this->deletePublishAppUpdateVersion($res['id'],'modify');
						$this->actionPublish($res,'','覆盖');
					}
					$options["app_name"] = $publishAppUpdateVersion['appName'];
					$options["pkgname"] = $publishAppUpdateVersion['pkgName'];
					$options["channel"] = $publishAppUpdateVersion['channel'];
					$options["version_code"] = $publishAppUpdateVersion['versionCode'];
					$options["version_name"] = $publishAppUpdateVersion['versionName'];
					$options["path"] = $publishAppUpdateVersion['path'];
					$options["md5"] = $publishAppUpdateVersion['md5'];
					$options["model"] = $publishAppUpdateVersion['model'];
					$options["vendorid"] = $publishAppUpdateVersion['vendorid'];
					$this->deletePublishAppUpdateVersion($put['id'],'modify');
					unset($put['id']);
					$this->create($options);

					$this->add();
					$this->actionPublish($publishAppUpdateVersion,'delete');//下架
					$this->actionPublish($options,'add');//新增
				}

			}else{
				result('param');
			}
		}
		public function deletePublishAppUpdateVersion($id,$atcion= 'delete')
		{
			$res = $this->find($id);
			if ($res) {
				$this->delete($id);
				if ($atcion == 'delete') {
					$this->actionPublish($res,$atcion);
				}
				return true;
			}
		}

		public function publishAppUpdateVersionLists($get)
		{
			if (empty($get['id'])) {
				if (!empty($get['type'])&&($get['type'] == 'authentication')) {

					if (!empty($get['name'])) {
						if (!empty($get['page'])&&!empty($get['pageSize'])) {
							$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
							$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `type` IN ('ALL','AB') AND (`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `model` like  '%".$get['name']."%') ")->order('`time` desc')->select();

						}else{
							$res['extra'] = $this->where("`type` IN ('ALL','AB') AND (`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `model` like  '%".$get['name']."%')  ")->order('`time` desc')->select();

						}
						$res['count'] = $this->where("`type` IN ('ALL','AB') AND (`app_name` like '%".$get['name']."%'  or `pkgname` like  '%".$get['name']."%'  or `model` like  '%".$get['name']."%') ")->count();
					}else{
						if (!empty($get['page'])&&!empty($get['pageSize'])) {
							$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
							$res['extra'] = $this->where("`type` IN ('ALL','AB')")->limit($get['page'],$get['pageSize'])->order('`time` desc')->select();
						}else{
							$res['extra'] = $this->where("`type` IN ('ALL','AB')")->order('id desc')->select();
						}
						$res['count'] = $this->where("`type` IN ('ALL','AB')")->count();
					}

				}elseif (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `model` like  '%".$get['name']."%' ")->order('`time` desc')->select();

					}else{
						$res['extra'] = $this->where("`app_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `model` like  '%".$get['name']."%'  ")->order('`time` desc')->select();

					}
					$res['count'] = $this->where("`app_name` like '%".$get['name']."%'  or `pkgname` like  '%".$get['name']."%'  or `model` like  '%".$get['name']."%' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->order('`time` desc')->select();
					}else{
						$res['extra'] = $this->order('id desc')->select();
					}
					$res['count'] = $this->count();
				}

				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['desc'] = json_decode($value['desc'],true);
						$res['extra'][$key]['blackList'] = json_decode($value['blackList'],true);
						$res['extra'][$key]['whiteList'] = json_decode($value['whiteList'],true);
						$res['extra'][$key]['time'] = date('Y-m-d H:i:s',empty($value['time'])?time():$value['time']);
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
					$res['extra']['time'] = date('Y-m-d H:i:s',empty($res['extra']['time'])?time():$res['extra']['time']);
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

		public function actionPublish($put,$action,$reason = null)
		{
			if (isset($put['id'])) {
				unset($put['id']);
			}
			$put['time'] = time();

			if ($reason != null) {
				$put['reason'] = $reason;
			}elseif ($action == 'add') {
				$put['reason'] = '发布';
			}elseif ($action == 'modify') {
				$put['reason'] = '修改';
			}elseif ($action == 'delete') {
				$put['reason'] = '下架';

			}
			D('AppUpdatePublishHistory','App')->addHistory($put);
		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}

	}