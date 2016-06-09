<?php
	namespace Home\Monitoring;
	class InterfaceGroupItemMonitoring extends \Think\Model
	{
		protected $tableName = 'interface_group_item';
		protected $connection = 'DB_MONITORING';
		protected $_map = array(
			'groupId' =>'group_id',
		);


		public function addGroupItem($put)
		{
			if (empty($put['name'])||empty($put['groupId'])||empty($put['interface'])) {
				result('param');
			}
			if (!D('InterfaceGroup','Monitoring')->getOneForId($put['groupId'])) {
				result('该接口组不存在');
			}
			if ($this->getOneForNameGroupId($put['name'],$put['groupId'])) {
				result('该接口组已存在接口'.$put['name']);
			}
			$options = array(
				'group_id'=>$put['groupId'],
				'name'=>$put['name'],
				'interface'=>$put['interface'],
				'desc'=>!empty($put['desc'])?$put['desc']:''
			);
			return $this->add($options);
		}
		public function modifyGroupItem($put)
		{
			if (empty($put['interface']) || empty($put['name']) || empty($put['id'])) {
				result('param');
			}
			if (!$res = $this->getOneForId($put['id'])) {
				result('该接口不存在');
			}
			if ($this->getOneForNameGroupIdNoId($put['name'],$res['groupId'],$put['id'])) {
				result('该接口组已存在接口'.$put['name']);
			}
			$options = array(
				'name'=>$put['name'],
				'interface'=>$put['interface'],
			);
			if (isset($put['desc'])) {
				$options['desc'] = $put['desc'];
			}
			return $this->where("id = %d",array($put['id']))->save($options);
		}
		public function deleteGroupItem($put)
		{
			if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode( ',',$arr);
					if ( $res = $this->getOneInId($sqlId) ) {
						$this->where("id IN (%s)",array($sqlId))->delete();
					}
				}
			}else{
				result('param');
			}
			return ;
		}
		public function groupItemLists($get)
		{
			if (!empty($get['groupId'])) {

				$where = 'group_id = %d';
				if (!empty($get['name'])) {
					$where = " and (`name` like '%".$get['name']."%' or interface  like '%".$get['name']."%' )";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where,array($get['groupId']))->select();
				}else{
					$res['extra'] = $this->where($where,array($get['groupId']))->select();
				}
				$res['count'] = $this->where($where,array($get['groupId']))->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}

		public function getInterfaceLists($get)
		{

			$where = '';
			$join = " LEFT JOIN tb_interface_group AS b ON a.group_id = b.id";
			$field = "a.id,a.name,a.interface,a.desc,b.name AS groupName";
			if (!empty($get['name'])) {
				$where = " a.name like '%".$get['name']."%' or a.interface  like %".$get['name']."% or b.name like '%".$get['name']."%'";
			}
			if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$res['extra'] = $this->field($field)->alias(" AS a")->limit($get['page'],$get['pageSize'])->join($join)->where($where)->select();
			}else{
				$res['extra'] = $this->field($field)->alias(" AS a")->where($where)->join($join)->select();
			}

			$res['count'] = $this->alias(" AS a")->where($where)->join($join)->count();

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		public function getOneForNameNoId($name,$id)
		{
			return $this->where("name = '%s' and id != %d",array($name,$id))->find();
		}
		public function getOneInId($id)
		{
			return $this->where("id IN (%s)",array($id))->find();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s'",array($name))->find();
		}
		public function getOneForNameGroupId($name,$groupId)
		{
			return $this->where("name = '%s' and group_id = %d",array($name,$groupId))->find();
		}
		public function getOneForNameGroupIdNoId($name,$groupId,$id)
		{
			return $this->where("name = '%s' and group_id = %d and id !=%d",array($name,$groupId,$id))->find();
		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
	}