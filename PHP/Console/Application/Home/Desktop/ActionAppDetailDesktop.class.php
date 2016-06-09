<?php
	namespace Home\Desktop;
	class ActionAppDetailDesktop extends \Think\Model
	{
		protected $tableName = 'action_app_detail';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'actionAppId' =>'action_app_id', // 把表单中name映射到数据表的username字段
			'detailName'  =>'detail_name', // 把表单中的mail映射到数据表的email字段
			'actionType' =>'action_type', // 把表单中name映射到数据表的username字段
			'clsName'  =>'clsname',
			'extraData'  =>'extra_data'
		);
		public function addActionAppDetail($put)
		{

			if( empty($put['detailName']) || empty($put['actionAppId']) ){
				result('param');
			}
			$res = D('ActionApp','Desktop')->getValOneForId($put['actionAppId']);
			if (empty($res)) {
				result('APP不存在');
			}
			$data = $this->checkAppAction($put);

			if (empty($data)) {
				result('跳转参数有误');
			}

			$data['action_app_id'] = $put['actionAppId'];
			$this->create($data);

			return $this->add();


		}
		public function deleteActionAppDetailArr($actionAppId)
		{
			if ($this->where("action_app_id = %d",array($actionAppId))->find()) {
				$this->where("action_app_id = %d",array($actionAppId))->delete();
			}
			return true;
		}

		public function deleteActionAppDetail($id)
		{
			if ($this->find($id)) {
				$this->delete($id);
			}
			return true;
		}
		public function checkAppAction($put)
		{
			switch ($put['actionType']) {
				case 'ACTION':
					if (!isset($put['action'])) {
						return false;
					}
					if (is_array($put['extraData']) && !empty($put['extraData'])) {
						foreach ($put['extraData'] as $k => $v) {
							if (!isset($v['key'])||!isset($v['value']) ) {
								$put['extraData'] = array();
								break;
							}
							if (isset($v['type'])) {
								if (!($v['type'] == 'int' || $v['type'] == 'long' || $v['type'] == 'float'  || $v['type'] == 'double'  || $v['type'] == 'boolean' || $v['type'] == 'char'  || $v['type'] == 'string' )) {
									unset($put['extraData'][$k]['type']);
								}
							}
						}
					}else{
						$put['extraData'] = array();
					}

					$put['extraData'] = json_encode($put['extraData'],JSON_UNESCAPED_UNICODE);
					$data = array(
						'clsName'=>'',
						'detailName'=>$put['detailName'],
						'actionType'=>$put['actionType'],
						'extraData'=>$put['extraData'],
						'action'=>$put['action'],
						'component'=>'',
						'uri'=>'',
					);

					break;
				case 'SCHEME':
						if (empty($put['uri']) ) {
							return false;
						}

						if (is_array($put['extraData']) && !empty($put['extraData'])) {
							foreach ($put['extraData'] as $k => $v) {
								if (!isset($v['key'])||!isset($v['value']) ) {
									$put['extraData'] = array();
									break;
								}
								if (isset($v['type'])) {
									if (!($v['type'] == 'int' || $v['type'] == 'long' || $v['type'] == 'float'  || $v['type'] == 'double'  || $v['type'] == 'boolean' || $v['type'] == 'char'  || $v['type'] == 'string' )) {
										unset($put['extraData'][$k]['type']);
									}
								}
							}
						}else{
							$put['extraData'] = array();
						}

						$put['extraData'] = json_encode($put['extraData'],JSON_UNESCAPED_UNICODE);
						$data = array(
							'detailName'=>$put['detailName'],
							'actionType'=>$put['actionType'],
							'extraData'=>$put['extraData'],
							'action'=>empty($put['action']) ? '' :$put['action'],
							'uri'=>$put['uri'],
							'actionAppId'=>$put['actionAppId']
						);
						break;
				case 'COMPONENT':
					if (!isset($put['clsName']) || !isset($put['component'])) {
						return false;
					}
					if (is_array($put['extraData']) && !empty($put['extraData'])) {
						foreach ($put['extraData'] as $k => $v) {
							if (!isset($v['key'])||!isset($v['value']) ) {
								$put['extraData'] = array();
								break;
							}
							if (isset($v['type'])) {
								if (!($v['type'] == 'int' || $v['type'] == 'long' || $v['type'] == 'float'  || $v['type'] == 'double'  || $v['type'] == 'boolean' || $v['type'] == 'char'  || $v['type'] == 'string' )) {
									unset($put['extraData'][$k]['type']);
								}
							}
						}
					}else{
						$put['extraData'] = array();
					}
					$put['extraData'] = json_encode($put['extraData'],JSON_UNESCAPED_UNICODE);
					$data = array(
						'detailName'=>$put['detailName'],
						'actionType'=>$put['actionType'],
						'extraData'=>$put['extraData'],
						'component'=>$put['component'],
						'clsName'=>$put['clsName'],
						'action'=>'',
						'uri'=>'',
					);

					break;
				default:
					return false;
					break;
			}

			return $data;
		}
		public function modifyActionAppDetail($put)
		{
			$res = $this->find($put['id']);
			if (empty($res)) {
				result('此APP信息不存在');
			}
			$data = $this->checkAppAction($put);
			if (empty($data)) {
				result('跳转参数有误');
			}
			$data = $this->create($data);
			$this->where("`id`=%d",array($put['id']))->save();
			return true;
		}

		public function actionAppDetailLists($id)
		{

			$res = $this->find($id);
			if ($res) {
				switch ($res['actionType']) {
					case 'ACTION':
						$res['extraData'] = json_decode($res['extraData'],true);

						unset($res['component']);
						unset($res['clsName']);
						unset($res['clsname']);
						unset($res['uri']);
						break;
					case 'COMPONENT':
						$res['extraData'] = json_decode($res['extraData'],true);
						unset($res['action']);
						unset($res['uri']);
						break;
					case 'SCHEME':
						$res['extraData'] = json_decode($res['extraData'],true);
						unset($res['component']);
						unset($res['clsName']);
						unset($res['clsname']);
						break;
					default:
						$res = array();
						break;
				}
				unset($res['actionAppId']);
			}else{
				$res = array();
			}

			return $res;
		}
		public function getValArrForActionAppId($ActionAppId)
		{
			return $this->where("`action_app_id`=%d",array($ActionAppId))->select();
		}
		public function copyActionApp($put)
		{
			if (empty($put['fromId']) || empty($put['toIds']) || !is_array($put['toIds'])) {

				result('param');
			}

			$actionApp = D('ActionApp','Desktop');
			if (!$actionApp->getValOneForId($put['fromId'])) {
				result('需要复制的跳转信息不存在');
			}
			$copyActionAppArr = $this->getArrForActionAppIdToCopy($put['fromId']);

			$copyActionAppDetailName = $this->getDetailNameSqlForActionAppId($put['fromId']);

			foreach ($put['toIds'] as  $value) {
				$IdSqlArr[] = intval($value);
			}

			if (!empty($IdSqlArr)) {
				$IdSql = implode(',', $IdSqlArr);
				if ($actionAppArr = $actionApp->getArrForIdSql($IdSql)) {
					foreach ($actionAppArr as  $value) {
						if ($appDetail = $this->getOneForDetailNameSqlForActionAppId($copyActionAppDetailName,$value['id'])) {
							result('跳转信息：'.$value['appName'].'的详情页：'.$appDetail['detailName'].'已存在');
						}
						if (empty($copyActionAppArr)) {
							continue;
						}

						foreach ($copyActionAppArr as  $v) {
							$copyOptions[] = array(
								'action_app_id'=>$value['id'],
								'detail_name'=>$v['detailName'],
								'action_type'=>$v['actionType'],
								'uri'=>!empty($v['uri'])?$v['uri']:'',
								'action'=>!empty($v['action'])?$v['action']:'',
								'component'=>!empty($v['component'])?$v['component']:'',
								'clsname'=>!empty($v['clsName'])?$v['clsName']:'',
								'extra_data'=>!empty($v['extraData'])?$v['extraData']:'[]',
							);
						}
					}

					if (!empty($copyOptions)) {
						$this->addAll($copyOptions);
					}
				}
			}
			return ;

		}
		/**
		 * [copyActionDetailApp 复制跳转信息详情]
		 *
		 * {
		 * 	"actionDetailIds":["跳转详情APPID"],
		 * 	"actionAppIds":["跳转APPID"]
		 * }
		 *
		 * @param  [type] $put [description]
		 * @return [type]      [description]
		 */
		public function copyActionDetailApp($put)
		{
			if (empty($put['actionDetailIds']) || !is_array($put['actionDetailIds']) || empty($put['actionAppIds']) || !is_array($put['actionAppIds'])) {
				result('param');
			}
			$put['actionDetailIds'] = array_map('intval', $put['actionDetailIds']);
			$put['actionDetailIds'] = array_unique($put['actionDetailIds']);

			$put['actionAppIds'] = array_map('intval',$put['actionAppIds']);
			$put['actionAppIds'] = array_unique($put['actionAppIds']);


			//检查新增的跳转信息对象是否存在
			$actionApp = D('ActionApp','Desktop');
			$actionIdSql = implode(',',$put['actionAppIds']);
			if ($actionApp->getCountForIdSql($actionIdSql) != count($put['actionAppIds'])) {
				result('新增的跳转信息对象不存在');
			}

			//检查复制的跳转信息是否存在
			$detailIdSql = implode(',',$put['actionDetailIds']);
			$copyActionAppArr = $this->getArrForIdSql($detailIdSql);
			if (count($copyActionAppArr) != count($put['actionDetailIds'])) {
				result('复制的跳转信息不存在');
			}

			foreach ($put['actionAppIds'] as  $value) {
				foreach ($copyActionAppArr as  $v) {
					if ($appDetail = $this->getOneForDetailNameForActionAppId($v['detailName'],$value)) {
						continue;
					}
					$copyOptions[] = array(
						'action_app_id'=>$value,
						'detail_name'=>$v['detailName'],
						'action_type'=>$v['actionType'],
						'uri'=>!empty($v['uri'])?$v['uri']:'',
						'action'=>!empty($v['action'])?$v['action']:'',
						'component'=>!empty($v['component'])?$v['component']:'',
						'clsname'=>!empty($v['clsName'])?$v['clsName']:'',
						'extra_data'=>!empty($v['extraData'])?$v['extraData']:'[]',
					);
				}
			}

			if (!empty($copyOptions)) {
				$this->addAll($copyOptions);
			}


			return ;

		}
		public function getArrForIdSql($detailIdSql)
		{
			return $this->where("id IN (".$detailIdSql.")")->select();
		}
		public function getOneForDetailNameForActionAppId($detailName,$actionAppId)
		{
			return $this->where("detail_name = '%s' and action_app_id= %d",array($detailName,$actionAppId))->find();
		}
		public function getOneForDetailNameSqlForActionAppId($detailNameSql,$actionAppId)
		{
			return $this->where('detail_name IN (%s) and action_app_id= %d',array($detailNameSql,$actionAppId))->find();
		}
		public function getDetailNameSqlForActionAppId($actionAppId)
		{
			return $this->field("detail_name")->where("action_app_id =%d",array($actionAppId))->select(false);
		}
		public function getArrForActionAppIdToCopy($actionAppId)
		{
			return $this->field("id,action_app_id",true)->where("action_app_id =%d",array($actionAppId))->select();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}

		/*
		public function createDesktopAction($id)
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