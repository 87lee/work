<?php
	namespace Home\Desktop;
	class OperationSlotGroupDesktop extends \Think\Model
	{
		protected $tableName = 'operation_slot_group';
		protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'slotID' =>'slot_id',
			'dataSource' =>'data_source' ,
			'isEditable' =>'is_editable',
			'disconnectEnable'=>'disconnect_enable',
		);*/
		protected $_validate = array(
			array('name','','运营坑位组已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);

		public function addOperationSlotGroup($put)
		{
			if ($options = $this->create($put)) {
				// if ($this->count() <= '5') {
					return $this->add();
				/*}else{
					result('运营坑位组大于5：请产品部提出申请');
				}*/
			}else{
				result($this->getError());
			}
		}
		public function modifyOperationSlotGroup($put)
		{
			if (!empty($put['name'])&&!empty($put['id'])) {
				if (!$this->where("name = '%s' and id !=%d",array($put['name'],$put['id']))->find()) {
					$options = array(
						'name'=>$put['name']
					);
					return $this->where("id=%d",array($put['id']))->save($options);
				}else{
					result('运营坑位组已存在');
				}
			}else{
				result('param');
			}

		}
		public function deleteOperationSlotGroup($get)
		{
			if ($this->find($get['id'])) {

				if (!D('OperationSlot','Desktop')->where("slot_group_id = %d",array($get['id']))->find()) {
					$this->where("id = %d",array($get['id']))->delete();
				}else{
					result('运营坑位组下存在运营坑位');
				}
			}
		}
		public function operationSlotGroupLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}


			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `name` like '%".$get['name']."%'")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where(" `name` like '%".$get['name']."%'  ")->select();
				}
				$res['count'] = $this->where(" `name` like '%".$get['name']."%'")->count();
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->select();
				}else{
					$res['extra'] = $this->select();
				}
				$res['count'] = $this->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}

			return $res;
		}
		public function operationSlotGroupForId($id)
		{
			return $this->find($id);
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getOperationSlotGroupAll($id)
		{
			return $this->select();
		}
	}