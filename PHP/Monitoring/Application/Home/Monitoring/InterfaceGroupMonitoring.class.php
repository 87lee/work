<?php
	namespace Home\Monitoring;
	class InterfaceGroupMonitoring extends \Think\Model
	{
		protected $tableName = 'interface_group';
		protected $connection = 'DB_MONITORING';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/


		public function addGroup($put)
		{
			if (empty($put['name'])) {
				result('param');
			}
			if ($this->getOneForName($put['name'])) {
				result('该接口组已存在');
			}
			$options = array(
				'name'=>$put['name'],
				'desc'=>!empty($put['desc'])?$put['desc']:''
			);
			return $this->add($options);
		}
		public function modifyGroup($put)
		{
			if (empty($put['name']) || empty($put['id'])) {
				result('param');
			}
			if (!$res = $this->getOneForId($put['id'])) {
				result('该接口组不存在');
			}
			if ($this->getOneForNameNoId($put['name'],$put['id'])) {
				result('该接口组名称已存在');
			}
			$options = array(
				'name'=>$put['name']
			);
			if (isset($put['desc'])) {
				$options['desc'] = $put['desc'];
			}
			return $this->where("id = %d",array($put['id']))->save($options);
		}
		public function deleteGroup($get)
		{
			if (!empty($get['id'])) {
				if (!$this->getOneForId($get['id'])) {
					result('该接口组不存在');
				}
				if (D('InterfaceGroupItem','Monitoring')->getOneForGroupId($get['id'])) {
					result('该接口组下存在成员');
				}
				$this->where("id =%d",array($get['id']))->delete();
			}
		}
		public function groupLists($get)
		{
			if (empty($get['id'])) {
				$where = '';
				if (!empty($get['name'])) {
					$where = "`name` like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}else{
				$res['extra'] = $this->find($get['id']);

				if ($res['extra']) {
					$res['count'] =1;
				}

			}
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
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s'",array($name))->find();
		}
	}