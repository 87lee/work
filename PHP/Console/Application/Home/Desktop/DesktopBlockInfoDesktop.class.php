<?php
	namespace Home\Desktop;
	class DesktopBlockInfoDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_block_info';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'blockId'=>'block_id',
			'isEditable'=>'is_editable',
			'disconnectEnable'=>'disconnect_enable',
			'dataSource'=>'data_source',
			'actionInfo'=>'action_info',
			'appInfo'=>'app_info',
			'operationId'=>'operation_id',
			'slotId'=>'slot_id'
		);
		public function addDesktopBlockInfo($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}
		public function deleteDesktopBlockInfo($desktopBlocksInfoSql)
		{
			if ( $this->where("`block_id` IN (".$desktopBlocksInfoSql.")")->find()) {
				return $this->where("`block_id` IN (".$desktopBlocksInfoSql.")")->delete();
			}
		}
		public function blocksInfoLists($blockId,$slotGroupId)
		{
			$res = $this->where("`block_id` = %d",array($blockId))->find();
			if ($res) {
				// dump($res);operation
				if ($res['operation'] =='true') {
					$data= array();
					$data = D('OperationSlot','Desktop')->layouInfo($res['operationId'],$slotGroupId);
					$data['operationId'] = $res['operationId'];
					$data['operation'] = 'true';
				}elseif ($res['operation'] =='false') {

					if ($res['dataSource'] == 'linkin' ||   $res['dataSource'] == 'linkinOnly') {
						$actionInfo = json_decode($res['actionInfo'],true);
						$appInfo = json_decode($res['appInfo'],true);
						$tag = json_decode($res['tag'],true);
						if (!empty((array)$tag)) {
							$data['tag'] = $tag;
						}
						if (!empty((array)$actionInfo)) {
							$data['actionData'] = $actionInfo;
						}
						if (!empty((array)$appInfo)) {
							$data['actionData']['appInfo'] = $appInfo;
						}
						if ($res['layout'] == 'VIDEO') {
							$response = D('DesktopBlockVideo','Desktop')->layouInfo($res['id']);
							if ($response) {
								$data['videos'] = $response;
							}
						}
						$data['title'] = $res['title']==' '?'':$res['title'];
						$data['isEditable'] = $res['isEditable'];
						$data['disconnectEnable'] = $res['disconnectEnable'];
						$data['dataSource'] = $res['dataSource'];
						$data['layout'] = $res['layout'];
						$data['operation'] = $res['operation'];
						$response = D('DesktopBlockPic','Desktop')->layouInfo($res['id']);
						if ($response) {
							$data['pic'] = $response['pic'];
						}
					}else{
						$data['isEditable'] = $res['isEditable'];
						$data['operation'] = $res['operation'];
						$data['dataSource'] = $res['dataSource'];
						$data['disconnectEnable'] = $res['disconnectEnable'];
					}

				}
			}else{
				$data = array();
			}
			return $data;
		}
		public function createDesktopBlocksInfoLists($blockId,$error = false,$desktopBlock =null,$slotGroupId)
		{

			$res = $this->where("`block_id` = %d",array($blockId))->find();
			if ($desktopBlock ===null) {
				$desktopBlock  = D('DesktopBlocks','Desktop')->getValOneForId($blockId);
			}
			if ($res) {
				if ($res['operation'] =='true') {
					$data = D('OperationSlot','Desktop')->createDesktopOperationSlotLists($res['operationId'],$error,$slotGroupId);
					if (!empty($data['reason'])) {
						if ($error) {
							return $data;
						}else{
							result($data['reason']);
						}
					}
				}elseif ($res['operation'] =='false') {
					if ($res['dataSource'] == 'linkin' ||  $res['dataSource'] == 'linkinOnly') {
						$actionInfo = json_decode($res['actionInfo'],true);
						$appInfo = json_decode($res['appInfo'],true);

						$tag = json_decode($res['tag'],true);
						if (!empty((array)$tag)) {
							$data['tag'] = $tag;
						}
						if (empty($actionInfo)) {
							if ($error) {
								return $actionData;
							}else{
								result($actionData['reason']);
								result('该桌面坑位'.$desktopBlock['slotId'].'跳转数据出错');
							}
						}

						$actionInfo['slotId'] = $desktopBlock['slotId'];
						$data['action'] = desktopSlotCheckAction($actionInfo);

						if (!empty($data['action']['reason'])) {
							if ($error) {
								return $data['action'];
							}else{
								result($data['action']['reason']);
							}
						}

						/*switch ($actionInfo['type']) {
							case 'APP':
								if (!empty($actionInfo['pkgName'])) {
									$data['action']['appData'] = array(
										'pkgName'=>$actionInfo['pkgName']
									);
								}else{
									if ($error) {
										return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'数据类型出错');
										// return false;
									}else{
										result('该桌面坑位'.$desktopBlock['slotId'].'数据类型出错');
									}
								}
								break;
							case 'ACTION':
								if (!empty($actionInfo['action'])) {

									if (!is_array($actionInfo['extraData']) || empty($actionInfo['extraData'])) {
										$actionInfo['extraData'] = array();
									}else{
										foreach ($actionInfo['extraData'] as $item) {
											if (!isset($item['key']) || !isset($item['value']) ) {
												$actionInfo['extraData'] = array();
												break;
											}
										}
									}
									$data['action']['extraData'] = $actionInfo['extraData'];
									$data['action']['actionData'] = array(
										'action'=>$actionInfo['action']
									);
								}else{
									if ($error) {
										return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'数据类型出错');

										// return false;
									}else{
										result('该桌面坑位'.$desktopBlock['slotId'].'数据类型出错');
									}
								}
								break;
							case 'SCHEME':
								if (!empty($actionInfo['action']) && !empty($actionInfo['uri']) ) {

									if (!is_array($actionInfo['extraData']) || empty($actionInfo['extraData'])) {
										$actionInfo['extraData'] = array();
									}else{
										foreach ($actionInfo['extraData'] as $item) {
											if (!isset($item['key']) || !isset($item['value']) ) {
												$actionInfo['extraData'] = array();
												break;
											}
										}
									}
									$data['action']['extraData'] = $actionInfo['extraData'];
									$data['action']['actionData'] = array(
										'action'=>$actionInfo['action'],
										'uri'=>$actionInfo['uri'],
									);
								}else{
									if ($error) {
										return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'数据类型出错');

										// return false;
									}else{
										result('该桌面坑位'.$desktopBlock['slotId'].'数据类型出错');
									}
								}
								break;
							case 'COMPONENT':
								if (!empty($actionInfo['component'])&&!empty($actionInfo['clsName'])) {
									if (!is_array($actionInfo['extraData']) || empty($actionInfo['extraData'])) {
										$actionInfo['extraData'] = array();
									}else{
										foreach ($actionInfo['extraData'] as $item) {
											if (!isset($item['key']) || !isset($item['value']) ) {
												$actionInfo['extraData'] = array();
												break;
											}
										}
									}
									$data['action']['extraData'] = $actionInfo['extraData'];
									$data['action']['componentData'] = array(
										'pkgName'=>$actionInfo['component'],
										'clsName'=>$actionInfo['clsName']
									);
								}else{
									if ($error) {
										return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'数据类型出错');

										// return false;
									}else{
										result('该桌面坑位'.$desktopBlock['slotId'].'数据类型出错');
									}
								}
								break;
							case 'URI':
								if (!empty($actionInfo['uri'])) {
									$data['action']['uriData'] = array(
										'uri'=>$actionInfo['uri']
									);
								}else{
									if ($error) {
										return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'数据类型出错');
										// return false;
									}else{
										result('该桌面坑位'.$desktopBlock['slotId'].'数据类型出错');
									}
								}
								break;

							default:
								if ($error) {
									return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'数据类型出错');
									// return false;
								}else{
									result('该桌面坑位'.$desktopBlock['slotId'].'数据类型出错');
								}
								break;
						}
						$data['action']['type'] = $actionInfo['type'];*/


						if (!empty($appInfo) ){
							if (!empty($appInfo['path'])&&!empty($appInfo['pkgName'])&&!empty($appInfo['versionCode'])) {
								$data['appInfo'] = array(
									'pkgName'=>$appInfo['pkgName'],
									'versionCode'=>$appInfo['versionCode'],
									'url'=>$appInfo['path']
								);
							}else{
								if ($error) {
									return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'槽位绑定出错');
									// return false;
								}else{
									result('该桌面坑位'.$desktopBlock['slotId'].'槽位绑定出错');
								}
							}
						}
						if ($res['layout'] == 'VIDEO') {
							$response = D('DesktopBlockVideo','Desktop')->layouInfo($res['id']);
							if ($response) {
								$data['videoPlayList']= $response;
							}else{
								if ($error) {
									return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'参数出错,类型为VIDEO,但没有VIDEO信息');
									// return false;
								}else{
									result('该桌面坑位'.$desktopBlock['slotId'].'参数出错,类型为VIDEO,但没有VIDEO信息');
								}
							}
						}
						$response = D('DesktopBlockPic','Desktop')->layouInfo($res['id']);
						if ($response) {
							$data['picUrl'] = $response['pic'];
						}else{
							if ($error) {
								return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'参数出错：没有图片');
								// return false;
							}else{
								result('该桌面坑位'.$desktopBlock['slotId'].'参数出错：没有图片');
							}
						}
						$data['title'] = $res['title'];
						$data['isEditable'] = $res['isEditable'];
						$data['disconnectEnable'] = $res['disconnectEnable'];
						$data['dataSource'] = $res['dataSource'];
						$data['layout'] = $res['layout'];
					}elseif ($res['dataSource'] == 'yunos') {
						$data['dataSource'] =$res['dataSource'];
						$data['isEditable'] = $res['isEditable'];
						$data['disconnectEnable'] = $res['disconnectEnable'];
					}else{
						if ($error) {
							return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'参数出错');
							// return false;
						}else{
							result('该桌面坑位'.$desktopBlock['slotId'].'参数出错');
						}
					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=> '坑位'.$desktopBlock['slotId'].'参数出错,没有找到块信息');
					// return false;
				}else{
					result('该桌面坑位'.$desktopBlock['slotId'].'参数出错,没有找到块信息');
				}
			}
			return $data;
		}
		public function modifyDesktopBlockInfo($blockId,$options)
		{
			$res = $this->where("`block_id` =%d ",array($blockId))->find();
			if ($res) {
				$this->create($options);
				$this->where("`block_id` =%d ",array($blockId))->save();
				return $res['id'];
			}else{
				$options['block_id'] = $blockId;
				return $this->addDesktopBlockInfo($options);
			}
		}

		public function getOperationSlot($operationSlotId,$slotGroupId)
		{
			$getBlockIdSql = $this->field("`block_id`")->where("`operation`='true' and `operation_id` = %d",array($operationSlotId))->select(false);
			return D('DesktopBlocks','Desktop')->getScreensIdLists($getBlockIdSql,$slotGroupId);
		}

		public function getAttentionAppCount($desktopBlockIdSql,$pkgName)
		{
			// $desktopBlockTrue = $this->field('id,operation_id')->where("block_id IN (".$desktopBlockIdSql.") and operation = 'true' and operation_id != 0  ")->select();
			$desktopBlockIdFalse = $this->field("action_info")->where("block_id IN (".$desktopBlockIdSql.") and operation = 'false' and  action_info != '{}' and  action_info != '' ")->select();

			$count = 0;
			if ($desktopBlockIdFalse) {
				foreach ($desktopBlockIdFalse as $value) {
					$actionInfo = json_decode($value['actionInfo'],true);
					if ($actionInfo) {
						if ($actionInfo['pkgName'] ==$pkgName) {
							$count +=1;
						}
					}
				}
			}
			return $count;
		}
		public function getAttentionAppOperationCount($desktopBlockIdSql,$pkgName)
		{
			return  $this->field('block_info.operation_id')->alias('block_info')->where("block_info.block_id IN (".$desktopBlockIdSql.") and block_info.operation = 'true' and block_info.operation_id != 0  ")->select(false);
		}
		public function getAttentionAppCountForDesktopID($desktopBlockIdSql)
		{
			// $desktopBlockTrue = $this->field('id,operation_id')->where("block_id IN (".$desktopBlockIdSql.") and operation = 'true' and operation_id != 0  ")->select();
			// echo $desktopBlockIdFalse = $this->field("action_info")->where("block_id IN (".$desktopBlockIdSql.") and operation = 'false' and  action_info != '{}' and  action_info != '' ")->select(false);
			$desktopBlockIdFalse = $this->field("action_info")->where("block_id IN (".$desktopBlockIdSql.") and operation = 'false' and  action_info != '{}' and  action_info != '' ")->select();

			$pkgName = array();
			if ($desktopBlockIdFalse) {
				foreach ($desktopBlockIdFalse as $value) {
					$actionInfo = json_decode($value['actionInfo'],true);
					if ($actionInfo) {
						if (!empty($actionInfo['pkgName'])) {
							$pkgName[] = $actionInfo['pkgName'];
						}
					}
				}
			}

			return $pkgName;

		}
		public function getAttentionAppOperationCountForDesktopID($desktopBlockIdSql)
		{
			return  $this->field('block_info.operation_id')->alias('block_info')->where("block_info.block_id IN (".$desktopBlockIdSql.") and block_info.operation = 'true' and block_info.operation_id != 0  ")->select(false);
		}

		public function modifyOperationSlot($operationSlotId,$modifySourceVersion,$desktopSlotUpdateTime,$slotGroupId)
		{
			$getBlockIdSql = $this->field("`block_id`")->where("`operation`='true' and `operation_id` = %d",array($operationSlotId))->select(false);


				/*foreach ($getBlockIdArr as $value) {
					$getBlockIdSql[] = $value['block_id'];
				}
				echo $getBlockIdSql = implode(',', $getBlockIdSql);
				die;*/
			if ($modifySourceVersion == 'true') {
				$modifySourceVersion = true;
			}else{
				$modifySourceVersion = false;
			}
			if ( $desktopSlotUpdateTime === true ) {
				// $desktopSql = D('Desktop','Desktop')->getSqlIdForBreakout($slotGroupId);

				$desktopScreensSql = D('DesktopScreens','Desktop')->getSqlIdForSlotGroupId($slotGroupId);

				$desktopBlocksArr = D('DesktopBlocks','Desktop')->getArrIdForDesktopScreensIdSql($desktopScreensSql);

				if ($desktopBlocksArr) {
					foreach ($desktopBlocksArr as $value) {
						$desktopBlocksSql[] = $value['id'];
					}
					$desktopBlocksIDSql = implode(',', $desktopBlocksSql);
					$getBlockIdSql = $this->field("`block_id`")->where("`operation`='true' and `operation_id` = %d AND block_id IN (".$desktopBlocksIDSql.")",array($operationSlotId))->select(false);
					D('DesktopBlocks','Desktop')->modifyUpdateTime($getBlockIdSql,$desktopSlotUpdateTime);
				}
			}
			D('DesktopBlocks','Desktop')->modifyOperationSlot($getBlockIdSql,$modifySourceVersion,$slotGroupId);

		}
		public function getValOneForBlackId($BlocksId)
		{
			return $this->where("`block_id` IN (".$BlocksId.")")->find();
		}
		public function getSqlIdArrForBlackId($BlocksId)
		{
			return $this->field('`id`')->where("`block_id` IN (".$BlocksId.")")->select(false);
		}
		public function getArrForBlockId($BlocksId)
		{
			return $this->where("`block_id` = %d",array($BlocksId))->select();
		}
	}