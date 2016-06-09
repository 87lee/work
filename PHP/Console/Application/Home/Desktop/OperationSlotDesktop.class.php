<?php
	namespace Home\Desktop;
	class OperationSlotDesktop extends \Think\Model
	{
		protected $tableName = 'operation_slot';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'slotID' =>'slot_id',
			'dataSource' =>'data_source' ,
			'isEditable' =>'is_editable',
			'disconnectEnable'=>'disconnect_enable',
			'slotGroupId'=>'slot_group_id'
		);
		/*protected $_validate = array(
			array('slot_id','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/
		public function addOperationSlot($options)
		{
			if ($this->create($options)) {

				if (!$this->where('slot_id = "%s" and slot_group_id = "%s"',array($options['slotID'],$options['slotGroupId']))->find()) {
					return $this->add();
				}else{
					result('运营坑位已存在');
				}

			}else{
				result('运营坑位已存在');
			}
		}

		public function copyOperationSlot($options)
		{
			if ($this->create($options)) {

				if (!$this->where('slot_id = "%s" and slot_group_id = "%s"',array($options['slotID'],$options['slotGroupId']))->find()) {
					return $this->add();
				}else{
					return $res = array('reason'=>'运营坑位已存在');
				}

			}else{
				return $res = array('reason'=>'运营坑位已存在');
			}
		}
		public function modifyOperationSlot($id,$options)
		{
			if ($this->create($options,2)) {
				$this->where("`id`=%d",array($id))->save();
				return $id;
			}
		}

		public function deleteOperationSlot($id)
		{
			$res = $this->find($id);
			if ($res) {
				$this->delete($id);
				return true;
			}else{
				result('运营坑位不存在');
			}
		}
		public function deleteOperationSlotArrForIDArr($idSql)
		{
			if ($this->where("`id` IN (".$idSql.")")->find()) {
				$this->where("`id` IN (".$idSql.")")->delete();
			}
			return true;
		}
		public function getSlotIDForIdArr($put)
		{
			$res['extra'] = array();

			if ( !empty($put) && is_array($put) ) {
				foreach ($put as  $value) {
					$options[] = (int)$value;
				}

				if (!empty($options)) {
					$optionsStr = implode(',', $options);
					$response = $this->field('id,slot_id')->where("id  IN (".$optionsStr.")")->select();
					if (!empty($response)) {
						foreach ($response as $key => $value) {
							$arr[$value['id']] = $value['slotID'];
						}
					}
				}

				foreach ($put as  $value) {
					$value = intval($value);
					if (!empty($arr[$value])) {
						$res['extra'][]= $arr[$value];
					}else{
						$res['extra'][] = new \stdClass();
					}
				}
			}
			return $res;
		}
		public function operationSlotArrLists($slotGroupId,$slotIDArr)
		{
			$slotIDSqlID = implode(',', $slotIDArr);

			$res['extra'] = $this->where("slot_group_id = '" .$slotGroupId."' AND slot_id IN (".$slotIDSqlID.")")->select();


			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = 0;
			}else{
				$response = array();
				foreach ($res['extra'] as $key => $value) {
					if ($res['extra'][$key]['title'] == ' ') {
						$res['extra'][$key]['title'] = '';
					}
					if($value['dataSource'] == 'yunos'){
						unset($res['extra'][$key]['layout']);
						unset($res['extra'][$key]['pic1']);
						unset($res['extra'][$key]['pic2']);
						unset($res['extra'][$key]['pic3']);
					}else{
						$operationSlotBindApp = D('OperationSlotBindApp','Desktop')->getValForOperationId($value['id']);
						if ($operationSlotBindApp) {
							unset($operationSlotBindApp['operationId']);
							unset($operationSlotBindApp['id']);
							$res['extra'][$key]['bindApp'] = $operationSlotBindApp;
						}
						if ($res['extra'][$key]['layout'] =='VIDEO') {
							$operationSlotVideos = D('OperationSlotVideos','Desktop')->getValArrForOperationId($value['id']);
							if ($operationSlotVideos) {
								unset($operationSlotVideos['operationId']);
								unset($operationSlotVideos['id']);
								$res['extra'][$key]['videos'] = $operationSlotVideos;
							}
						}
						$operationSlotAction = D('OperationSlotAction','Desktop')->getValForOperationId($value['id']);
						if ($operationSlotAction) {
							$res['extra'][$key] = array_merge($res['extra'][$key],getQuickOperationActionApp($operationSlotAction));

						}
					}
					foreach ($slotIDArr as  $k =>$row) {
						if ($value['slotID'] == $row) {
							$response[$k] = $res['extra'][$key];
						}
					}
				}
			}

			foreach ($slotIDArr as $key => $value) {
				if (empty($response[$key])) {
					$response[$key] = new \stdClass();
				}
			}
			ksort($response,SORT_NUMERIC);
			return $response;

		}
		/**
		 * 运营坑位列表
		 * @param  [type] $id       [description]
		 * @param  [type] $name     [description]
		 * @param  [type] $page     [description]
		 * @param  [type] $pageSize [description]
		 * @param  [type] $slotID   [description]
		 * @return [type]           [description]
		 */

		public function operationSlotLists($get /*, $id = null,$name= null ,$page=null,$pageSize=null,$slotID = null,$slotGroupId = null*/)
		{
			if (empty($get['id']) && !empty($get['slotGroupId'])) {
				$where = "`slot_group_id` =". intval($get['slotGroupId']);
				$order = "slot_id ASC";
				if (!empty($get['name'])) {
					$appNameSql = D('OperationSlotAction','Desktop')->getSqlOperationIdForName($get['name']);
					$where .= "  and  ( `slot_id` like '%".$get['name']."%'   or  `title` like '%".$get['name']."%'  or `id` IN (".$appNameSql.") )";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->order($order)->select();
				}else{
					$res['extra'] = $this->where($where)->order($order)->select();
				}
				if (!empty($get['slotID'])) {
					$where .= " and `slot_id` = " .intval($get['slotID']);
					$res['extra'] = $this->where($where)->order($order)->find();
				}
				$res['count'] = $this->where($where)->count();
			}else{
				$res['extra'] = $this->find($get['id']);
				$res['count'] = $this->where("`id`=%d",array($get['id']))->count();
				if (empty($res['extra'])) {
					$res['count'] = 0;
				}
			}
			//---------------------------------------

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				if (empty($res['extra'][0])) {
					if($res['extra']['dataSource'] == 'yunos' ){
						$res['extra']['layout'] = '';
						$res['extra']['pic1'] = '';
						$res['extra']['pic2'] = '';
						$res['extra']['pic3'] = '';
					}else{

						$res['extra']['tag'] = json_decode($res['extra']['tag'],true);

						if (empty($res['extra']['tag'])) {
							unset($res['extra']['tag']);
						}
						$operationSlotBindApp = D('OperationSlotBindApp','Desktop')->getValForOperationId($res['extra']['id']);
						if ($operationSlotBindApp) {
							unset($operationSlotBindApp['operationId']);
							unset($operationSlotBindApp['id']);
							$res['extra']['bindApp'] = $operationSlotBindApp;
						}
						if ($res['extra']['layout'] =='VIDEO') {
							$operationSlotVideos = D('OperationSlotVideos','Desktop')->getValArrForOperationId($res['extra']['id']);
							if ($operationSlotVideos) {
								unset($operationSlotVideos['operationId']);
								unset($operationSlotVideos['id']);
								$res['extra']['videos'] = $operationSlotVideos;
							}
						}
						$operationSlotAction = D('OperationSlotAction','Desktop')->getValForOperationId($res['extra']['id']);
						if ($operationSlotAction) {

							$res['extra'] = array_merge($res['extra'],getQuickOperationActionApp($operationSlotAction));
						}
					}
				}else{
					foreach ($res['extra'] as $key => $value) {

						if ($res['extra'][$key]['title'] == ' ') {
							$res['extra'][$key]['title'] = '';
						}
						if($value['dataSource'] == 'yunos'){
							unset($res['extra'][$key]['layout']);
							unset($res['extra'][$key]['pic1']);
							unset($res['extra'][$key]['pic2']);
							unset($res['extra'][$key]['pic3']);
						}else{

							$res['extra'][$key]['tag'] = json_decode($res['extra'][$key]['tag'],true);

							if (empty((array)$res['extra'][$key]['tag'])) {
								unset($res['extra'][$key]['tag']);
							}
							$operationSlotBindApp = D('OperationSlotBindApp','Desktop')->getValForOperationId($value['id']);
							if ($operationSlotBindApp) {
								unset($operationSlotBindApp['operationId']);
								unset($operationSlotBindApp['id']);
								$res['extra'][$key]['bindApp'] = $operationSlotBindApp;
							}
							if ($res['extra'][$key]['layout'] =='VIDEO') {
								$operationSlotVideos = D('OperationSlotVideos','Desktop')->getValArrForOperationId($value['id']);
								if ($operationSlotVideos) {
									unset($operationSlotVideos['operationId']);
									unset($operationSlotVideos['id']);
									$res['extra'][$key]['videos'] = $operationSlotVideos;
								}
							}
							$operationSlotAction = D('OperationSlotAction','Desktop')->getValForOperationId($value['id']);
							if ($operationSlotAction) {
								$res['extra'][$key] = array_merge($res['extra'][$key],getQuickOperationActionApp($operationSlotAction));

								/*$res['extra'][$key]['actionType'] = $operationSlotAction['actionType'];
								switch ($res['extra'][$key]['actionType']) {
									case 'APP':
										$res['extra'][$key]['app']['appName'] = $operationSlotAction['appName'];
										$res['extra'][$key]['app']['pkgName'] = $operationSlotAction['pkgName'];
										break;
									case 'URI':
										$res['extra'][$key]['uri']['uri'] = $operationSlotAction['uri'];
										$res['extra'][$key]['uri']['uriName'] = $operationSlotAction['uriName'];
										break;
									case 'ACTION':
										$res['extra'][$key]['action']['appName'] = $operationSlotAction['appName'];
										$res['extra'][$key]['action']['detailName'] = $operationSlotAction['detailName'];
										$res['extra'][$key]['action']['action'] = $operationSlotAction['action'];
										$res['extra'][$key]['action']['extraData'] =  json_decode($operationSlotAction['extraData'],true);
										break;
									case 'COMPONENT':
										$res['extra'][$key]['component']['appName'] = $operationSlotAction['appName'];
										$res['extra'][$key]['component']['detailName'] = $operationSlotAction['detailName'];
										$res['extra'][$key]['component']['component'] = $operationSlotAction['component'];
										$res['extra'][$key]['component']['clsName'] = $operationSlotAction['clsName'];
										$res['extra'][$key]['component']['extraData'] =  json_decode($operationSlotAction['extraData'],true);
										break;
									default:
										// $res['extra'][$key]= array();
										break;
								}*/
							}
						}
					}
				}

			}



			//------------------------------------------
			return $res;
		}

		/**
		 * 获取桌面运营坑位
		 * @param  [type] $slotId [description]
		 * @return [type]         [description]
		 */
		public function layouInfo($slotId,$slotGroupId)
		{
			$res = $this->where("`slot_id` = %d and `slot_group_id` = '%s'" ,array( $slotId ,$slotGroupId) ) ->find();
			if (empty($res)) {
				$res['action'] = new \stdClass();
			}else{
				if($res['dataSource'] == 'yunos'){
					$res = array(
						// 'slotID'=>$res['slotID'],
						'dataSource'=>$res['dataSource'],
						'isEditable'=>$res['isEditable'],
						'disconnectEnable'=>$res['disconnectEnable'],
					);
				}else{
					$res['tag'] = json_decode($res['tag'],true);
					if (empty($res['tag'])) {
						unset($res['tag']);
					}
					$operationSlotBindApp = D('OperationSlotBindApp','Desktop')->getValFieldForOperationId($res['id']);
					if ($operationSlotBindApp) {
						$res['actionData']['appInfo'] = $operationSlotBindApp;
					}
					if ($res['layout'] =='VIDEO') {
						$operationSlotVideos = D('OperationSlotVideos','Desktop')->getValArrForOperationId($res['id']);
						if ($operationSlotVideos) {
							$res['videos'] = $operationSlotVideos;
						}
					}
					$operationSlotAction = D('OperationSlotAction','Desktop')->getValForOperationId($res['id']);
					if ($operationSlotAction) {

						$operationSlotAction['type'] = $operationSlotAction['actionType'];
						$operationSlotAction['extraData'] = json_decode($operationSlotAction['extraData'],true);
						$res['actionData'] = array_merge($res['actionData'],checkActionApp($operationSlotAction));

						/*$res['actionData']['type'] = $operationSlotAction['actionType'];
						$extraData = json_decode($operationSlotAction['extraData'],true);
						switch ($operationSlotAction['actionType']) {
							case 'APP':
								if (!empty($operationSlotAction['pkgName'])) {
									$res['actionData']['appName'] = $operationSlotAction['appName'];
									$res['actionData']['pkgName'] = $operationSlotAction['pkgName'];
								}
								break;
							case 'ACTION':
								if (!empty($operationSlotAction['action'])) {

									if (!is_array($extraData)) {
										$extraData = array();
									}else{
										foreach ($extraData as $item) {
											if (empty($item['key']) || empty($item['value']) ) {
												$extraData = array();
												break;
											}
										}
									}
									$res['actionData']['extraData'] = $extraData;
									$res['actionData']['action'] = $operationSlotAction['action'];
									$res['actionData']['appName'] = $operationSlotAction['appName'];
									$res['actionData']['detailName'] = $operationSlotAction['detailName'];
								}

								break;
							case 'COMPONENT':
								if (!empty($operationSlotAction['component'])&&!empty($operationSlotAction['clsName'])) {
									if (!is_array($extraData)) {
										$extraData = array();
									}else{
										foreach ($extraData as $item) {
											if (empty($item['key']) || empty($item['value']) ) {
												$extraData = array();
												break;
											}
										}
									}
									$res['actionData']['extraData'] = $extraData;
									$res['actionData']['component']=$operationSlotAction['component'];
									$res['actionData']['clsName']=$operationSlotAction['clsName'];
									$res['actionData']['appName'] = $operationSlotAction['appName'];
									$res['actionData']['detailName'] = $operationSlotAction['detailName'];
								}
								break;
							case 'URI':
								if (!empty($operationSlotAction['uri'])) {
									$res['actionData']['uri']=$operationSlotAction['uri'];
								}
								break;

							default:

								break;
						}*/
					}

				}
			}
			unset($res['id']);
			return $res;
		}
		/**
		 * 生成桌面运营坑位数据
		 * @param  [type]  $id    [description]
		 * @param  boolean $error [description]
		 * @return [type]         [description]
		 */
		public function createDesktopOperationSlotLists($slotId,$error = false,$slotGroupId)
		{
			$res = $this->field("slot_group_id",true)->where("`slot_id` = %d and `slot_group_id` = '%s'" ,array( $slotId ,$slotGroupId) ) ->find();

			if (empty($res)) {
				return  array('reason'=> '运营坑位'.$res['slotID'].'没有跳转参数');
			}

			if($res['dataSource'] == 'yunos'){
				$res = array(
					'slotID'=>$res['slotID'],
					'dataSource'=>$res['dataSource'],
					'isEditable'=>$res['isEditable'],
					'disconnectEnable'=>$res['disconnectEnable'],
				);
			}else{
				$res['tag'] = json_decode($res['tag'],true);
				if (empty($res['tag'])) {
					unset($res['tag']);
				}
				$operationSlotBindApp = D('OperationSlotBindApp','Desktop')->getValCreateFieldForOperationId($res['id']);
				if ($operationSlotBindApp) {
					$res['appInfo'] = $operationSlotBindApp;
				}
				if ($res['layout'] =='VIDEO') {
					$operationSlotVideos = D('OperationSlotVideos','Desktop')->getValArrForOperationId($res['id']);
					if ($operationSlotVideos) {
						$res['videoPlayList'] = $operationSlotVideos;
					}else{
						if ($error) {
							return $result = array('reason'=> '运营坑位'.$res['slotID'].'没有视频参数');
						}else{
							result('运营坑位'.$res['slotID'].'没有视频参数');
						}
					}
				}
				$operationSlotAction = D('OperationSlotAction','Desktop')->getValForOperationId($res['id']);
				$operationSlotAction['type'] = $operationSlotAction['actionType'];
				$operationSlotAction['extraData'] = json_decode($operationSlotAction['extraData'],true);

				$res['action'] = desktopSlotCheckAction($operationSlotAction,'运营坑位',$error);
				if (!empty($res['action']['reason'])) {
					return $res['action'];
				}
				/*$res['action']['type'] = $operationSlotAction['actionType'];
				$extraData = json_decode($operationSlotAction['extraData'],true);
				switch ($operationSlotAction['actionType']) {
					case 'APP':
						if (!empty($operationSlotAction['pkgName'])) {
							$res['action']['appData'] = array(
								'pkgName'=>$operationSlotAction['pkgName']
							);
						}else{
							if ($error) {
								return $result = array('reason'=> '运营坑位'.$res['slotID'].'数据类型出错');
								// return false;
							}else{
								result('运营坑位'.$res['slotID'].'数据类型出错');
							}
						}
						break;
					case 'ACTION':
						if (!empty($operationSlotAction['action'])) {

							if (!is_array($extraData)) {
								$extraData = array();
							}else{
								foreach ($extraData as $item) {
									if (empty($item['key']) || empty($item['value']) ) {
										$extraData = array();
										break;
									}
								}
							}

							$res['action']['extraData'] = $extraData;

							$res['action']['actionData'] = array(
								'action'=>$operationSlotAction['action']
							);
						}else{
							if ($error) {
								return $result = array('reason'=> '运营坑位'.$res['slotID'].'数据类型出错');
								// return false;
							}else{
								result('该桌面运营坑位'.$res['slotID'].'数据类型出错');
							}
						}
						break;
					case 'COMPONENT':
						if (!empty($operationSlotAction['component'])&&!empty($operationSlotAction['clsName'])) {
							if (!is_array($extraData)) {
								$extraData = array();
							}else{
								foreach ($extraData as $item) {
									if (empty($item['key']) || empty($item['value']) ) {
										$extraData = array();
										break;
									}
								}
							}
							$res['action']['extraData'] = $extraData;
							$res['action']['componentData'] = array(
								'pkgName'=>$operationSlotAction['component'],
								'clsName'=>$operationSlotAction['clsName']
							);
						}else{
							if ($error) {
								return $result = array('reason'=> '运营坑位'.$res['slotID'].'数据类型出错');
								// return false;
							}else{
								result('该桌面运营坑位'.$res['slotID'].'数据类型出错');
							}
						}
						break;
					case 'URI':
						if (!empty($operationSlotAction['uri'])) {
							$res['action']['uriData'] = array(
								'uri'=>$operationSlotAction['uri']
							);
						}else{
							if ($error) {
								return $result = array('reason'=> '运营坑位'.$res['slotID'].'数据类型出错');
								// return false;
							}else{
								result('该桌面运营坑位'.$res['slotID'].'数据类型出错');
							}
						}
						break;

					default:
						if ($error) {
							return $result = array('reason'=> '运营坑位'.$res['slotID'].'数据类型出错');
							// return false;
						}else{
							result('运营坑位'.$res['slotID'].'出错');
						}
						break;
				}*/
			}

			unset($res['id']);
			return $res;
		}
		public function getValForSlotIdForBreakout($SlotId,$slotGroupId)
		{
			return $this->where('slot_id = "%s" and slot_group_id = "%s"',array($SlotId,$slotGroupId))->find();
		}
		public function getValForSlotIdForSlotGroupId($SlotId,$SlotGroupId)
		{
			return $this->where('slot_id = "%s" and slot_group_id = "%s"',array($SlotId,$SlotGroupId))->find();
		}

		public function getValOneForId($id)
		{
			return $this->find($id);
		}
		public function getValOneForSlotIdForSlotGroupIdForNotId($slotId,$SlotGroupId,$id)
		{
			return $this->where("`id` != %d and `slot_id`=%d  AND slot_group_id = '%s'",array($id,$slotId,$SlotGroupId))->find();
		}
		public function getValOneForIdArr($arr)
		{
			foreach ($arr as  $value) {
				$options[] = (int)$value;
			}
			$res = null;
			if (!empty($options)) {
				$sqlStr = implode(',', $options);
				$res = $this->where('id IN ('.$sqlStr.')')->select();
			}
			return $res;

		}
		public function getAttentionAppOperationCount($slotGroupId,$slotIDSql,$pkgName)
		{
			$idSql = $this->field('id')->where("slot_group_id = ".$slotGroupId . " and slot_id IN (".$slotIDSql.")")->select(false);
			return D('OperationSlotAction','Desktop')->getAttentionAppOperationCount($idSql,$pkgName);
		}
		public function getAttentionAppOperationCountForDesktopID($slotGroupId,$slotIDSql)
		{
			$idSql = $this->field('id')->where("slot_group_id = ".$slotGroupId . " and slot_id IN (".$slotIDSql.")")->select(false);
			return D('OperationSlotAction','Desktop')->getAttentionAppOperationCountForDesktopID($idSql);
		}
		/*
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