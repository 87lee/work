<?php
	namespace Home\App;
	class MoreAppGroupItemsApp extends \Think\Model
	{
		protected $tableName = 'more_app_group_items';
		protected $connection = 'DB_APP';
		protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
			'groupId'  =>'group_id',
		);

		public function addAppGroupMember($options)
		{
			//检查数据库是否存在版本
			if (!empty($options['groupId'])&&!empty($options['appName'])&&!empty($options['pkgName'])) {
				if (D('MoreAppGroup','App')->getValOneForId($options['groupId'])) {
					$date = array(
						'app_name'=>$options['appName'],
						'pkgname'=>$options['pkgName'],
						'group_id'=>$options['groupId'],

					);
					$this->add($date);
				}else{
					result('应用组不存在');
				}

			}else{
				result('param');
			}


		}

		public function deleteAppGroupMember($id)
		{
			if ($id !=null) {
				$res = $this->find($id);
				if ($res) {
					$this->where('`id`=%d',array($id))->delete();
				}
			}

		}
		public function deleteAppGroupMemberForGroupId($groupId)
		{
			if ($this->where("group_id = %d",array($groupId))->find()) {
				$this->where("group_id = %d",array($groupId))->delete();
			}
		}

		public function appGroupMemberLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->field('id,app_name,pkgname')->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}

			}elseif (!empty($get['groupId'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field('id,app_name,pkgname')->limit($get['page'],$get['pageSize'])->where(" `group_id` = '%s'" ,array($get['groupId']))->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field('id,app_name,pkgname')->where(" `group_id` = '%s'" ,array($get['groupId']))->select();
				}
				$res['count'] = $this->where(" `group_id` like '%".$get['groupId']."%' ")->count();
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field('id,app_name,pkgname')->limit($get['page'],$get['pageSize'])->where(" `app_name` like '%".$get['name']."%'  or `pkgname` like '%".$get['name']."%'")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field('id,app_name,pkgname')->where(" `app_name` like '%".$get['name']."%'  or `pkgname` like '%".$get['name']."%'")->select();
				}
				$res['count'] = $this->where(" `app_name` like '%".$get['name']."%'  or `pkgname` like '%".$get['name']."%'")->count();
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field('id,app_name,pkgname')->limit($get['page'],$get['pageSize'])->select();
				}else{
					$res['extra'] = $this->field('id,app_name,pkgname')->select();
				}
				$res['count'] = $this->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}

			return $res;
		}
		public function modifyAppGroupMember($put)
		{
			if (!empty($put['id'])&&!empty($put['appName'])&&!empty($put['pkgName'])) {
				$res = $this->find($put['id']);
				if ($res) {
					$options = array(
						"pkgname"=>$put['pkgName'],
			 			"app_name"=>$put['appName'],
					);
					$this->where("`id`=%d",array($put['id']))->save($options);
				}

			}else{
				result('param');
			}



		}

	}