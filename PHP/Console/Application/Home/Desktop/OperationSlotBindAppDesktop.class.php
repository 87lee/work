<?php
	namespace Home\Desktop;
	class OperationSlotBindAppDesktop extends \Think\Model
	{
		protected $tableName = 'operation_slot_bind_app';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
			'operationId' =>'operation_id',
			'versionCode'  =>'version_code',
			'autoInstall'  =>'auto_install',
		);
		public function addOperationSlotBindApp($options)
		{
			if ($this->create($options)) {
				return $this->add();
			}else{
				return false;
			}
		}
		public function modifyOperationSlotBindApp($operationId,$options)
		{

			if ($this->where("`operation_id`=%d",array($operationId))->find()) {
				$this->create($options);
				return $this->where("`operation_id`=%d",array($operationId))->save();
			}else{
				$options['operationId']=$operationId;
				$this->create($options);
				return $this->add();
			}
		}
		public function deleteOperationSlotBindApp($operationId)
		{
			if ($this->where("`operation_id`=%d",array($operationId))->find()) {
				$this->where("`operation_id`=%d",array($operationId))->delete();
			}
			return true;

		}
		public function deleteOperationSlotBindAppArrForIDArr($operationIdSql)
		{
			if ($this->where("`operation_id` IN (".$operationIdSql.")")->find()) {
				$this->where("`operation_id` IN (".$operationIdSql.")")->delete();
			}
			return true;

		}
		public function actionAppLists($id = null,$name= null ,$page=null,$pageSize=null)
		{
			if ($id ===null) {
				if ($name != null) {
					if (!empty($page)&&!empty($pageSize)) {
						$page = $page*$pageSize - $pageSize;
						$res['extra'] = $this->limit($page,$pageSize)->where("`name` like '%".$name."%'")->select();
					}else{
						$res['extra'] = $this->where("`name` like '%".$name."%'")->select();
					}
					$res['count'] = $this->where("`name` like '%".$name."%'")->count();
				}else{
					if (!empty($page)&&!empty($pageSize)) {
						$page = $page*$pageSize - $pageSize;
						$res['extra'] = $this->limit($page,$pageSize)->select();
					}else{
						$res['extra'] = $this->select();
					}
					$res['count'] = $this->count();
				}
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}
			}else{
				$res['extra'] = $this->find($id);

				if (empty($res['extra'])) {
					$res['extra'] = array();
				}else{
					$res['extra']['extraData'] = D('ActionAppDetail','Desktop')->getValArrForActionAppId($res['extra']['id']);
					if (!empty($res['extra']['extraData'])) {
						foreach ($res['extra']['extraData'] as $key => $value) {
							switch ($value['actionType']) {
								case 'ACTION':
									$res['extra']['extraData'][$key]['extraData'] = json_decode($value['extraData'],true);
									$res['extra']['extraData'][$key]['action'] = $value['action'];
									unset($res['extra']['extraData'][$key]['component']);
									unset($res['extra']['extraData'][$key]['clsName']);
									unset($res['extra']['extraData'][$key]['clsname']);
									break;
								case 'COMPONENT':
									$res['extra']['extraData'][$key]['extraData'] = json_decode($value['extraData'],true);
									$res['extra']['extraData'][$key]['component'] = $value['component'];
									$res['extra']['extraData'][$key]['clsName'] = $value['clsName'];
									unset($res['extra']['extraData'][$key]['action']);
									break;
								default:
									$res['extra']['extraData'][$key]= array();
									break;
							}
							unset($res['extra']['extraData'][$key]['actionAppId']);
						}
					}


				}
			}

			result(true,$res);
		}
		public function modifyActionApp($id,$appName,$pkgName)
		{
			$res = $this->find($id);
			if ($res) {
				$res = $this->where("`pkgname`='%s' and id != %d",array($pkgName,$id))->find();
				if ($res) {
					result('包名已存在');
				}
				$options = array(
					'appName'=>$appName,
					'pkgName'=>$pkgName
				);
				$this->create($options);
				$this->where("`id`=%d",array($id))->save();
				return true;
			}else{
				result('App不存在');
			}

		}
		public function getValForOperationId($OperationId)
		{
			return $this->where("`operation_id`=%d",array($OperationId))->find();
		}
		public function getValFieldForOperationId($OperationId)
		{
			return $this->field("`url` as path,`pkgname`,`version_code`")->where("`operation_id`=%d",array($OperationId))->find();
		}
		public function getValCreateFieldForOperationId($OperationId)
		{
			return $this->field("`url`,`pkgname`,`version_code`")->where("`operation_id`=%d",array($OperationId))->find();
		}
		/*public function createDesktopAction($id)
		{

			$res = $this->field('action as type,action_info_id,app_info_id')->find($id);
			if (empty($res)) {
				$res = array();
			}else{
				switch ($res['type']) {
					case 'APP':
						if (!empty($res['app_info_id'])) {
							$res['appInfo'] = D('AppInfo','Desktop')->field('version_code as versionCode,path as url,pkgname as pkgName')->find($res['app_info_id']);
						}
						$response = D('ActionInfo','Desktop')->field('pkgname')->find($res['action_info_id']);
						$res['appData'] =array(
							'pkgName'=>$response['pkgName']
						);
						break;
					case 'ACTION':
						$response = D('ActionInfo','Desktop')->field('action,extra_data')->find($res['action_info_id']);
						$res['extraData'] = json_decode($response['extraData'],true);
						$res['actionData'] = array(
							'action'=>$response['action']
						);
						break;
					case 'COMPONENT':
						$response = D('ActionInfo','Desktop')->field('component,target,extra_data')->find($res['action_info_id']);
						$res['extraData'] = json_decode($response['extraData'],true);

						$res['componentDate'] = array(

							$res['pkgName'] =$response['component'],
							$res['clsName'] = $response['target'],
						);

						break;
					case 'URI':
						$response = D('ActionInfo','Desktop')->field('uri,extra_data')->find($res['action_info_id']);
						$res['extraData'] = json_decode($response['extraData'],true);
						$res['uriData'] = array(
							'uri'=>$response['uri']
						);

						break;
					default:

						break;
				}

			}
			unset($res['action_info_id']);
			unset($res['app_info_id']);
			return $res;
		}
		public function modifyActionInfo($id,$options)
		{
			$this->create($options);

			if ($this->where("`id`=%d",array($id))->save()) {
				return true;
			}else{
				return false;
			}
		}*/
	}