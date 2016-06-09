<?php
	namespace Home\Model;
	class ModulePublilshModel extends \Think\Model
	{
		protected $tableName = 'module_publish';
		// protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function publishModule($put)
		{
			if (!empty($put['name'])&&!empty($put['version'])&&!empty($put['versionName'])&&!empty($put['versionDesc'])&&!empty($put['pkgName'])&&!empty($put['commitId'])&&!empty($put['lowestApiVer'])) {

				if (!$res = $this->where(' name = "%s" and operator = "%s"' ,array($put['name'],$put['operator']))->find()) {
					if (isAdmin()) {
						$options = array(
							"name"=>$put['name'],
    							"version"=>$put['version'],
    							"versionName"=>$put['versionName'],
    							"versionDesc"=>$put['versionDesc'],
    							"pkgName"=>$put['pkgName'],
    							"commitId"=>$put['commitId'],
    							"lowestApiVer"=>$put['lowestApiVer'],
							"pubTime":time(),
						);
						return $this->add($options);
					}else{
						result('auth');
					}

				}else{
					result('该用户已有该权限');
				}
			}
		}

		public function deleteModuleAdmin($get)
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

		public function modifyModuleAdmin($put)
		{

			if (isAdmin()) {
				if (!empty($put['id']) && !empty($put['name']) && !empty($put['operator'])) {
					if ($this->find($put['id'])) {
						if ($this->where("name = '%s'  and  operator = '%s' and id !=%d",array($user['name'],$put['operator'],$put['id']))->find()) {
							$options = array(
								'name' => $put['name'],
								'operator' => $put['operator'],
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

		public function moduleAdminLists($get)
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
				$res['count'] = 0;
			}

			return $res;
		}
	}