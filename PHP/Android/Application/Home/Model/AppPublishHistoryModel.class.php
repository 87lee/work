<?php
	namespace Home\Model;
	class AppPublishHistoryModel extends \Think\Model
	{
		protected $tableName = 'app_publish_history';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'versionName' =>'version_name',
			'versionCode' =>'version_code',
			'pkgName'  =>'pkg_name',
			'versionDesc'  =>'version_desc',
			'gitCommitId'  =>'git_commit_id',
			'channelId'  =>'channel_id',
			'systemApp' =>'system_app',
			'minSdk'  =>'min_sdk',
			'pubTime'  =>'pub_time',
			'gitBranch' =>'git_branch',
			'passTest'  =>'pass_test',
			'relyModule'  =>'rely_module',
		);
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addAppPublishHistory($options)
		{
			$this->create($options);
			return $this->add();
		}

		public function deletePublishApp($get)
		{
			if (!empty($get['id'])) {
				if ($res = $this->find($get['id'])) {
					$isAppAdmin = D('AppAdmin')->isAppAdmin($res['name']);
					if ($isAppAdmin) {

						return $this->where("id = %d",array($get['id']))->delete();
					}else{
						result('auth');
					}

				}else{
					result('模块不存在');
				}
			}
		}

		/*public function modifyAppAdmin($put)
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

		public function publishAppLists($get)
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