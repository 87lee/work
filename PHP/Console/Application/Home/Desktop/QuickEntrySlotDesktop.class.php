<?php
	namespace Home\Desktop;
	class QuickEntrySlotDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_slot';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'slotId' =>'slot_id',
			'groupId' =>'group_id',
			'isEditable' =>'is_editable',
			'focusedDrawable' =>'focused_drawable',
			'normalDrawable' =>'normal_drawable',
		);
		protected $_validate = array(
			array('slot_id','','此快捷坑位已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function addQuickEntry($options)
		{
			if ($this->create($options)) {
				return $this->add();
			}else{
				result('此快捷坑位已存在');
			}
		}
		/*public function copyOperationSlot($options)
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
		}*/
		public function modifyQuickEntrySlot($id,$options)
		{
			if ($this->create($options,2)) {
				$this->where("`id`=%d",array($id))->save();
				return $id;
			}
		}
		/*public function deleteOperationSlot($id)
		{
			$res = $this->find($id);
			if ($res) {
				$this->delete($id);
				return true;
			}else{
				result('运营坑位不存在');
			}
		}*/
		public function deleteQuickEntrySlotForIDArr($idArr)
		{
			if ($this->where("`id` IN (".$idArr.")")->find()) {
				$this->where("`id` IN (".$idArr.")")->delete();
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
							$arr[$value['id']] = $value['slotId'];
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

		/**
		 * 运营坑位列表
		 * @param  [type] $id       [description]
		 * @param  [type] $name     [description]
		 * @param  [type] $page     [description]
		 * @param  [type] $pageSize [description]
		 * @param  [type] $slotID   [description]
		 * @return [type]           [description]
		 */
		public function quickEntrySlotLists($get)
		{

			if (empty($get['id'])) {
				$noField = 'group_id';
				$order = "`slot_id`  ASC ";

				if (!empty($get['groupId'])) {
					$where = 'group_id = ' . $get['groupId'];
					if (!empty($get['name'])) {
						$appNameSql = D('QuickEntrySlotAction','Desktop')->getSqlQuickEntryIdForName($get['name']);
						$where .= " and (`slot_id` like '%".$get['name']."%'   or  `title` like '%".$get['name']."%'  or `id` IN (".$appNameSql.")) ";
					}
				}elseif (!empty($get['name'])) {
					$appNameSql = D('QuickEntrySlotAction','Desktop')->getSqlQuickEntryIdForName($get['name']);
					$where = "  (`slot_id` like '%".$get['name']."%'   or  `title` like '%".$get['name']."%'  or `id` IN (".$appNameSql.")) ";


				}elseif ($get['slotIDArrStr']) {
					$where = "  `slot_id` IN (".$get['slotIDArrStr'].")  ";

				}elseif ($get['slotID']) {
					$where = "   `slot_id` =  " . $get['slotID'];
				}

				if ($get['slotID']) {
					$res['extra'] = $this->field($noField,true)->where($where)->order($order)->find();
				}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field($noField,true)->where($where)->limit($get['page'],$get['pageSize'])->order($order)->select();

				}else{
					$res['extra'] = $this->field($noField,true)->where($where)->order($order)->select();

				}
				$res['count'] = $this->where($where)->count();

				//------------------------------------------
			}else{
				$res['extra'] = $this->find($get['id']);
				$res['count'] = 1;
				// dump($res['extra']);
				// $res['extra'] = $this->where("`slot_id` = %d" ,array( $id) ) ->find();
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
					$quickEntrySlotBindApp = D('QuickEntrySlotBindApp','Desktop')->getValForQuickEntryId($res['extra']['id']);
					if ($quickEntrySlotBindApp) {
						unset($quickEntrySlotBindApp['quickEntryId']);
						unset($quickEntrySlotBindApp['id']);
						$res['extra']['bindApp'] = $quickEntrySlotBindApp;
					}

					$quickEntrySlotAction = D('QuickEntrySlotAction','Desktop')->getValForQuickEntryId($res['extra']['id']);
					if ($quickEntrySlotAction) {
						$res['extra'] = array_merge($res['extra'],getQuickOperationActionApp($quickEntrySlotAction));
						/*$res['extra']['actionType'] = $quickEntrySlotAction['actionType'];
						switch ($res['extra']['actionType']) {
							case 'APP':
								$res['extra']['app']['appName'] = $quickEntrySlotAction['appName'];
								$res['extra']['app']['pkgName'] = $quickEntrySlotAction['pkgName'];
								break;
							case 'URI':
								$res['extra']['uri']['uri'] = $quickEntrySlotAction['uri'];
								$res['extra']['uri']['uriName'] = $quickEntrySlotAction['uriName'];
								break;
							case 'ACTION':
								$res['extra']['action']['appName'] = $quickEntrySlotAction['appName'];
								$res['extra']['action']['detailName'] = $quickEntrySlotAction['detailName'];
								$res['extra']['action']['action'] = $quickEntrySlotAction['action'];
								$res['extra']['action']['extraData'] =  json_decode($quickEntrySlotAction['extraData'],true);
								break;
							case 'COMPONENT':
								$res['extra']['component']['appName'] = $quickEntrySlotAction['appName'];
								$res['extra']['component']['detailName'] = $quickEntrySlotAction['detailName'];
								$res['extra']['component']['component'] = $quickEntrySlotAction['component'];
								$res['extra']['component']['clsName'] = $quickEntrySlotAction['clsName'];
								$res['extra']['component']['extraData'] =  json_decode($quickEntrySlotAction['extraData'],true);
								break;
							default:
								// $res['extra']= array();
								break;
						}*/
					}
				}else{
					foreach ($res['extra'] as $key => $value) {
						$quickEntrySlotBindApp = D('QuickEntrySlotBindApp','Desktop')->getValForQuickEntryId($value['id']);
						if ($quickEntrySlotBindApp) {
							unset($quickEntrySlotBindApp['quickEntryId']);
							unset($quickEntrySlotBindApp['id']);
							$res['extra'][$key]['bindApp'] = $quickEntrySlotBindApp;
						}
						$quickEntrySlotAction = D('QuickEntrySlotAction','Desktop')->getValForQuickEntryId($value['id']);
						if ($quickEntrySlotAction) {
							$res['extra'][$key] = array_merge($res['extra'][$key],getQuickOperationActionApp($quickEntrySlotAction));
							/*$res['extra'][$key]['actionType'] = $quickEntrySlotAction['actionType'];
							switch ($res['extra'][$key]['actionType']) {
								case 'APP':
									$res['extra'][$key]['app']['appName'] = $quickEntrySlotAction['appName'];
									$res['extra'][$key]['app']['pkgName'] = $quickEntrySlotAction['pkgName'];
									break;
								case 'URI':
									$res['extra'][$key]['uri']['uri'] = $quickEntrySlotAction['uri'];
									$res['extra'][$key]['uri']['uriName'] = $quickEntrySlotAction['uriName'];
									break;
								case 'ACTION':
									$res['extra'][$key]['action']['appName'] = $quickEntrySlotAction['appName'];
									$res['extra'][$key]['action']['detailName'] = $quickEntrySlotAction['detailName'];
									$res['extra'][$key]['action']['action'] = $quickEntrySlotAction['action'];
									$res['extra'][$key]['action']['extraData'] =  json_decode($quickEntrySlotAction['extraData'],true);
									break;
								case 'COMPONENT':
									$res['extra'][$key]['component']['appName'] = $quickEntrySlotAction['appName'];
									$res['extra'][$key]['component']['detailName'] = $quickEntrySlotAction['detailName'];
									$res['extra'][$key]['component']['component'] = $quickEntrySlotAction['component'];
									$res['extra'][$key]['component']['clsName'] = $quickEntrySlotAction['clsName'];
									$res['extra'][$key]['component']['extraData'] =  json_decode($quickEntrySlotAction['extraData'],true);
									break;
								default:
									break;
							}*/
						}
					}
				}
			}
			return $res;
		}
		public function moveQuickEntrySlot($put)
		{
			if (!empty($put['ids'])&&is_array($put['ids'])&&!empty($put['groupId'])) {

				$idSql = implode(',', $put['ids']);
				if (!$this->getValOneForSqlId($idSql)) {
					result('快捷坑位不存在');
				}
				if (!D('QuickEntrySlotGroup','Desktop')->getOneForId($put['groupId'])) {
					result('快捷坑位组不存在');
				}
				$options = array(
					'group_id'=>$put['groupId']
				);
				$this->where("id IN (".$idSql.")")->save($options);
				return;
			}else{
				result('param');
			}
		}

		public function getValForSlotIdForSlotGroupId($SlotId,$SlotGroupId)
		{
			return $this->where('slot_id = "%s" and slot_group_id = "%s"',array($SlotId,$SlotGroupId))->find();
		}
		public function getValOneForId($id)
		{
			return $this->find($id);
		}
		public function getValOneForSqlId($id)
		{
			return $this->find($id);
		}
		public function getValOneForNotIDForSlotID($id,$slotId)
		{
			return $this->where("`id` != %d and `slot_id`=%d ",array($id,$slotId))->find();
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
		public function getValOneForIdSlotID($slotID)
		{
			return $this->where("slot_id = '%s'",array($slotID))->find();
		}
		public function getOneForGroupId($GroupId)
		{
			return $this->where("group_id = %d",array($GroupId))->find();
		}
		public function getCountForArrId($slotIDStr)
		{
			return $this->where("slot_id IN (".$slotIDStr.") ")->count();
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