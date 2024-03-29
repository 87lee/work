<?php
	namespace Home\Model;
	class ModulePublishHistoryModel extends \Think\Model
	{
		protected $tableName = 'module_publish_history';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'versionName' =>'version_name',
			'pkgName'  =>'pkg_name',
			'versionDesc'  =>'version_desc',
			'commitId'  =>'commit_id',
			'pubTime'  =>'pub_time',
			'lowestApiVer'  =>'lowest_api_ver',
		);
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addModulePublishHistory($options)
		{
			$this->create($options);
			return $this->add();
		}

		public function deletePublishModule($get)
		{
			if (!empty($get['id'])) {
				if ($res = $this->find($get['id'])) {
					$isModuleAdmin = D('ModuleAdmin')->isModuleAdmin($res['name']);
					if ($isModuleAdmin) {

						return $this->where("id = %d",array($get['id']))->delete();
					}else{
						result('auth');
					}

				}else{
					result('模块不存在');
				}
			}
		}

		/*public function modifyModuleAdmin($put)
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
		}*/

		public function publishModuleLists($get)
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
	}