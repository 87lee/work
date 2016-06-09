<?php
	namespace Home\Desktop;
	class ActionAppDesktop extends \Think\Model
	{
		protected $tableName = 'action_app';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);
		protected $_validate = array(
			array('app_name','','跳转信息应用名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function addActionApp($put,$error = false)
		{
			if (!empty($put['appName']) && !empty($put['pkgName'])) {
				//检查跳转信息
				/*if ($this->where("`pkgname`='%s'",array($put['pkgName']))->find()) {
					if ($error) {
						return array('reason'=>'跳转信息包名已存在');
					}else{
						result('跳转信息包名已存在');
					}

				}*/
				//添加跳转信息
				$options = array(
					'appName'=>$put['appName'],
					'pkgName'=>$put['pkgName'],
				);
				if ($this->create($options)) {
					return $this->add();
				}else{
					if ($error) {
						return array('reason'=>$this->getError());
					}else{
						result($this->getError());
					}

				}

			}else{
				result('param');
			}


		}
		public function deleteActionAppLists($id)
		{
			$res = $this->find($id);
			if ($res) {
				$this->delete($id);
				return true;
			}else{
				result('跳转信息不存在');
			}
		}
		public function actionAppLists($id = null,$name= null ,$page=null,$pageSize=null)
		{
			if ($id ===null) {
				if ($name != null) {
					if (!empty($page)&&!empty($pageSize)) {
						$page = $page*$pageSize - $pageSize;
						$res['extra'] = $this->limit($page,$pageSize)->where("`app_name` like '%".$name."%' or `pkgname` like  '%".$name."%'")->select();
					}else{
						$res['extra'] = $this->where("`app_name` like '%".$name."%' or `pkgname` like  '%".$name."%'")->select();
					}
					$res['count'] = $this->where("`app_name` like '%".$name."%' or `pkgname` like  '%".$name."%'")->count();
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
									unset($res['extra']['extraData'][$key]['uri']);
									break;
								case 'COMPONENT':
									$res['extra']['extraData'][$key]['extraData'] = json_decode($value['extraData'],true);
									$res['extra']['extraData'][$key]['component'] = $value['component'];
									$res['extra']['extraData'][$key]['clsName'] = $value['clsName'];
									unset($res['extra']['extraData'][$key]['action']);
									unset($res['extra']['extraData'][$key]['uri']);
									unset($res['extra']['extraData'][$key]['clsname']);
									break;
								case 'SCHEME':
									$res['extra']['extraData'][$key]['extraData'] = json_decode($value['extraData'],true);
									$res['extra']['extraData'][$key]['uri'] = $value['uri'];
									$res['extra']['extraData'][$key]['action'] = $value['action'];
									unset($res['extra']['extraData'][$key]['clsName']);
									unset($res['extra']['extraData'][$key]['component']);
									unset($res['extra']['extraData'][$key]['clsname']);
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
				$res = $this->where("`app_name`='%s' and id != %d",array($appName,$id))->find();
				if ($res) {
					result('应用名已存在');
				}
				$options = array(
					'appName'=>$appName,
					'pkgName'=>$pkgName
				);
				$this->create($options,2);
				$this->where("`id`=%d",array($id))->save();
				return true;
			}else{
				result('App不存在');
			}
		}
		public function getValOneForId($id)
		{
			return $this->find($id);
		}
		public function getCountForIdSql($actionIdSql)
		{
			return $this->where("id IN (".$actionIdSql.")")->count();
		}
		public function getArrForIdSql($idSql)
		{
			return $this->where("id IN (".$idSql.")")->select();
		}
	}