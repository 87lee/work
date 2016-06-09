<?php
	namespace Home\Model;
	class AppRelyModel extends \Think\Model
	{
		protected $tableName = 'app_rely';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'versionName' =>'version_name',
			'pkgName'  =>'pkg_name',
			'versionName' =>'version_name',
			'commitId'  =>'commit_id',
			'pubTime'  =>'pub_time',
		);
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addApp($put)
		{
			if (!empty($put['modules']['module'])) {
				foreach ($put['modules']['module'] as $key => $value) {
					$options[] = array(
						"id"=>$put["id"],
						"name"=>$put["@attributes"]['name'],
						"module"=>$value['@attributes']['name'] ,
						"version_name"=> $value['version_name'],
						"version"=>'',
						"pkg_name"=>$value['package_name'],
						"commit_id"=>'',
						"pub_time"=>$put['pub_time'],
					);
				}
				return $this->addAll($options);
			}



		}

		public function deleteAppAdmin($get)
		{
			if (!empty($get['id'])) {
				if (isAdmin()) {
					if ($this->find($get['id'])) {
						return $this->where("id = %d",array($get['id']))->delete();
					}
				}else{
					result('auth');
				}

			}
		}

		public function modifyAppAdmin($put)
		{
			if (isAdmin()) {
				if (!empty($put['id']) && !empty($put['name']) && !empty($put['operator']) &&($put['operation']=='test' || $put['operation']=='publish' )) {
					if ($this->find($put['id'])) {
						if ($this->where("name = '%s'  and  operator = '%s' and id !=%d and operation='%s'",array($user['name'],$put['operator'],$put['id'],$put['operation']))->find()) {
							$options = array(
								'name' => $put['name'],
								'operator' => $put['operator'],
								'operation'=>$put['operation'],
							);
							if (!empty($put['note'])) {
								$options['note'] = $put['note'];
							}
							$this->where("id = %d",array($user['id']))->save($options);

						}else{
							result('该用户已有该权限');
						}
					}else{
						result('数据不存在');
					}
				}else{
					result('param');
				}
			}else{
				result('auth');
			}
		}

		public function appAdminLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				$res['count'] = 1;

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
		public function appRelyListsForName($name)
		{

			$res = $this->where(" `name` = '%s'" ,array($name))->select();

			if (empty($res)) {
				$res = array();
			}
			return $res;
		}
		public function appRelyListsForId($id)
		{

			$res = $this->where(" `id` = '%s'" ,array($id))->select();

			if (empty($res)) {
				$res = array();
			}
			return $res;
		}
	}