<?php
	namespace Home\Desktop;
	class QuickEntryGroupDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_group';
		protected $connection = 'DB_DESKTOP';

		public function addQuickEntryGroup($name)
		{
			$options = array(
				'name'=>$name
			);

			if ($this->create($options)) {
				return $this->add();
			}
			return ;
		}
		public function deleteQuickEntryGroup($get)
		{
			if (!empty($get['id'])) {
				$get['id'] = intval($get['id']);
				if ($this->find($get['id'])) {
					$this->delete($get['id']);
				}else{
					result('快捷入口不存在');
				}
			}
			return ;
		}
		public function modifyQuickEntryGroup($put)
		{



			if (!empty($put['name'])&&!empty($put['id'])) {
				if ($res = D('QuickEntryGroup','Desktop')->getOneForNameNotId($put['name'],$put['id'])) {
					result('快捷入口组已存在');
				}

				$options = array(
					'name'=>$put['name']
				);
				$this->where("`id`=%d",array($put['id']))->save($options);
				return $put['id'];
			}
			return ;
		}

		public function getOneForName($name)
		{
			return $this->where("`name`='%s'",array($name))->find();
		}
		public function getValOneForId($id)
		{
			return $this->where("`id`=%d",array($id))->find();
		}
		public function getValALL()
		{
			return $this->select();
		}
		public function getOneForNameNotId($name,$id)
		{
			return $this->where("`name`='%s' and `id` !=%d",array($name,$id))->find();
		}
		public function getQuickEntryGroupLists($get)
		{
			$res['extra'] = array();
			$where = '';
			if (!empty($get['name'])) {
				$where = "name LIKE '%".$get['name']."%'";
			}elseif (!empty($get['id'])) {
				$where = "id =" . intval($get['id']);
			}

			if (!empty($get['page'])&& !empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$res['extra'] = $this->where($where)->limit($get['page'],$get['pageSize'])->select();
			}else{
				$res['extra'] = $this->where($where)->select();
			}
			$res['count'] = $this->where($where)->count();
			return $res;
		}
		public function getQuickEntryGroupSqlForWhere($get)
		{
			$where = '';
			$field = 'id';
			if (!empty($get['name'])) {
				$where = "name LIKE '%".$get['name']."%'";
			}elseif (!empty($get['id'])) {
				$where = "id =" . intval($get['id']);
			}
			if (!empty($get['page'])&& !empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				return $this->field($field)->where($where)->limit($get['page'],$get['pageSize'])->select(false);
			}else{
				return $this->field($field)->where($where)->select(false);
			}
		}
	}