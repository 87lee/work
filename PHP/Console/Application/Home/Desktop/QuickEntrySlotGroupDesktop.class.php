<?php
	namespace Home\Desktop;
	class QuickEntrySlotGroupDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_slot_group';
		protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'slotId' =>'slot_id',
			'isEditable' =>'is_editable',
			'focusedDrawable' =>'focused_drawable',
			'normalDrawable' =>'normal_drawable',
		);*/
		protected $_validate = array(
			array('name','','此快捷坑位组已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function addQuickEntrySlotGroup($options)
		{
			if (!empty($options['name'])) {
				if ($this->create($options)) {
					return $this->add();
				}else{
					result('此快捷坑位组已存在');
				}
			}else{
				result('param');
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
		public function modifyQuickEntrySlotGroup($put)
		{
			if (!empty($put['id'])&&!empty($put['name'])) {

				if ($this->find($put['id'])) {
					if ($this->getOneForNameNoId($put['name'],$put['id'])) {
						result('快捷坑位组已存在');
					}else{
						$options = array(
							'name'=>$put['name'],
						);
						if (isset($put['desc'])) {
							$options['desc']=$put['desc'];
						}
						$this->where("`id`=%d",array($put['id']))->save($options);
						return $put['id'];
					}
				}else{
					result('快捷坑位组不存在');
				}
			}else{
				result('param');
			}

		}
		public function quickEntrySlotGroupLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else {
				if (!empty($get['name'])){
					$where = "    `name` like '%".$get['name']."%'  ";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		public function deleteQuickEntrySlotGroup($get)
		{

			if ($res =  $this->find($get['id'])) {
				if (D('QuickEntrySlot','Desktop')->getOneForGroupId($get['id'])) {
					result('快捷坑位组下存在快捷坑位');
				}
				$this->where("id = %d",array($get['id']))->delete();
				return ;
			}else{
				result('运营坑位不存在');
			}
		}

		/*public function deleteQuickEntrySlotForIDArr($idArr)
		{
			if ($this->where("`id` IN (".$idArr.")")->find()) {
				$this->where("`id` IN (".$idArr.")")->delete();
			}
			return true;
		}*/


		public function getOneForName($Name)
		{
			return $this->where("name = '%s'",array($Name))->find();
		}
		public function getOneForNameNoId($Name,$id)
		{
			return $this->where("name = '%s' and id !=%d",array($Name,$id))->find();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
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