<?php
	namespace Home\Desktop;
	class QuickEntrySlotActionDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_slot_action';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'quickEntryId' =>'quick_entry_id',
			'actionType'  =>'action_type',
			'appName'  =>'app_name',
			'detailName'  =>'detail_name',
			'pkgName'  =>'pkgname',
			'clsName'  =>'clsname',
			'uriName'  =>'uri_name',
			'extraData'  =>'extra_data',
		);
		public function addQuickEntrySlotAction($options)
		{
			if ($this->create($options)) {
				return $this->add();
			}else{
				return false;
			}
		}
		public function modifyQuickEntrySlotAction($quickEntryId,$options)
		{
			if ($this->where('quick_entry_id = %d' ,array($quickEntryId))->find($options)) {
				if ($this->create($options)) {
					return $this->where("`quick_entry_id`=%d",array($quickEntryId))->save();
				}else{
					return false;
				}
			}else{
				$options['quickEntryId'] =  $quickEntryId;
				return $this->addQuickEntrySlotAction($options);
			}

		}
		public function deleteQuickEntrySlotAction($quickEntryId)
		{
			if ($this->where("`quick_entry_id`=%d",array($quickEntryId))->find()) {
				$this->where("`quick_entry_id`=%d",array($quickEntryId))->delete();
			}

		}
		public function deleteQuickEntrySlotActionForIDArr($quickEntryIdArr)
		{
			if ($this->where("`quick_entry_id` IN (".$quickEntryIdArr.")")->find()) {
				$this->where("`quick_entry_id` IN (".$quickEntryIdArr.")")->delete();
			}

		}
		/*public function actionAppLists($id = null,$name= null ,$page=null,$pageSize=null)
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

		}*/
		public function getValForQuickEntryId($QuickEntryId)
		{
			return $this->where("`quick_entry_id`=%d",array($QuickEntryId))->find();
		}
		public function getSqlQuickEntryIdForName($name)
		{
			return $this->field("`quick_entry_id`")->where("`app_name` like '%".$name."%' or `detail_name` like '%".$name."%'  or `pkgname` like '%".$name."%'   or `uri_name` like '%".$name."%' ")->select(false);
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