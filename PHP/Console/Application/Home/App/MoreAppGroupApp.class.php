<?php
	namespace Home\App;
	class MoreAppGroupApp extends \Think\Model
	{
		protected $tableName = 'more_app_group';
		protected $connection = 'DB_APP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
			'blacklistId'  =>'blacklist_id',
		);*/

		public function addAppGroup($name)
		{

			if (!$this->where('`name` = "%s" ',array($name))->find()) {
				$options = array(
					'name'=>$name
				);
				return $this->add($options);
			}else{
				result('应用组已存在');
			}

		}
		public function deleteAppGroup($id = null)
		{
			if ($id !=null) {
				$res = $this->find($id);
				if ($res) {
					D('MoreAppGroupItems','App')->deleteAppGroupMemberForGroupId($id);
					$this->delete($id);
					return true;
				}else{
					result('该应用不存在');
				}
			}

		}
		public function appGroupLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}

				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `name` like '%".$get['name']."%'  ")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where(" `name` like '%".$get['name']."%' ")->select();
				}
				$res['count'] = $this->where(" `name` like '%".$get['name']."%'  ")->count();
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
		public function modifyAppGroup($options)
		{

			if (!$this->where('`name` = "%s" and `id` !=%d ',array($options['name'],$options['id']))->find()) {
				$data = array(
					'name'=>$options['name']
				);
				$this->where("`id`=%d",array($options['id']))->save($data);
			}else{
				result('应用组已存在');
			}

		}
		public function getValOneForId($id)
		{
			return $this->find($id);
		}

	}