<?php
	namespace Home\Model;
	class ModulePublishModel extends \Think\Model
	{
		protected $tableName = 'module_publish';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'versionName' =>'version_name',
			'pkgName'  =>'pkg_name',
			'versionDesc'  =>'version_desc',
			'commitId'  =>'commit_id',
			'pubTime'  =>'pub_time',
			'minSdk'  =>'min_sdk',
			'gitCommitId'  =>'git_commit_id',
			'gitBranch'  =>'git_branch',
			"relyModule"=>"rely_module"
		);
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function publishModule($xmlData,$versionDesc = '' )
		{
			$isAppPublish = D('ModuleAdmin')->isAppPublish($xmlData['@attributes']['name']);
			if ($isAppPublish) {
				if (empty($xmlData['git_commit_id']) || empty($xmlData['git_branch']) || empty($xmlData['min_sdk']) || empty($xmlData['@attributes']['name']) || empty($xmlData['package_name']) || empty($xmlData['version_name']))  {
					result('上传失败，pom文件内容出错');
				}
				if (!empty($xmlData['dependencies'])) {
					$dependencies = (array)$xmlData['dependencies'];
					if (!empty($dependencies['dependency'])) {
						if (is_array($dependencies['dependency'])) {
							foreach ($dependencies['dependency'] as $key => $value) {
								$dependency[] = (array)$value;
							}
						}else{
							$dependency[] = (array)$dependencies['dependency'];
						}
					}
				}

				if (!empty($dependency)) {
					foreach ($dependency as $key => $value) {
						if ( !empty($value['@attributes']['name']) && !empty($value['package_name']) && !empty($value['version_name']) )  {
							$dependencyItem[] = array(
								'module'=>$value['@attributes']['name'],
								'version_name'=>$value['version_name'],
								'pkg_name'=>$value['package_name']
							);
						}
					}
				}
				if ($res = $this->where(' name = "%s" AND   pkg_name = "%s" AND   version_name = "%s"' ,array($xmlData['@attributes']['name'],$xmlData['package_name'],$xmlData['version_name']))->find()) {
					result("上传失败，版本已存在");
				}
				$user = session('androidIsLogin');
				$options = array(
					"name"=>$xmlData['@attributes']['name'],
					"version_name"=>$xmlData['version_name'],
					"version_desc"=>$versionDesc,
					"pkg_name"=>$xmlData['package_name'],
					"git_commit_id"=>$xmlData['git_commit_id'],
					"min_sdk"=>$xmlData['min_sdk'],
					"git_branch"=>$xmlData['git_branch'],
					"publisher"=>$user['user'],
					"pub_time"=>time(),
					"rely_module"=>!empty($dependencyItem)?json_encode($dependencyItem,JSON_UNESCAPED_UNICODE):'[]',
				);
				return $this->add($options);
			}else{
				result('auth');
			}


		}

		public function deletePublishModule($put)
		{

			if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode($arr, ',');
					if (isAdmin()) {
						if ($res = $this->where("id IN (".$sqlId.")")->select()) {
							foreach ($res as  $value) {
								D("ModulePublishHistory")->addModulePublishHistory($value);
							}
							D("ModuleComment")->deleteCommentArrForId($sqlId);
							return $this->where("id IN (".$sqlId.")")->delete();

						}

					}else{
						result('auth');
					}
				}


			}else{
				result('param');
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
				if (!empty($res['extra'])) {
					// $res['extra']['rely'] =D('ModuleRely')->appRelyListsForId($res['extra']['id']);
					$res['count'] = 1;
				}

			}else{
				$where = "";
				if (!empty($get['name'])) {
					$UserName = D('User')->getAllNameForUserName($get['name']);
					$where = " `name` like '%".$get['name']."%' or  `pkg_name` like '%".$get['name']."%' or publisher IN (".$UserName.")";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->where($where)->limit($get['page'],$get['pageSize'])->select();

				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				$UserArr = D('User')->getAllNameUser();
				foreach ($UserArr as  $value) {
					$nameArr[$value['user']] = $value['name'];
				}
				if (empty($res['extra'][0])) {
					$jsonArr = json_decode($res['extra']['relyModule'],true);
					$res['extra']['relyModule'] = !empty($jsonArr)?$jsonArr:array();
					if (!empty($nameArr[$res['extra']['publisher']])) {
						$res['extra']['publisher'] = $nameArr[$res['extra']['publisher']];
					}
				}else{
					foreach ($res['extra'] as $key => $value) {
						$jsonArr = json_decode($value['relyModule'],true);
						$res['extra'][$key]['relyModule'] = !empty($jsonArr)?$jsonArr:array();

						if (!empty($nameArr[$value['publisher']])) {
							$res['extra'][$key]['publisher'] = $nameArr[$value['publisher']];
						}
					}
				}
			}
			return $res;
		}

		public function relyModuleLists($get)
		{
			if (!empty($get['module'])&&!empty($get['versionName'])&&!empty($get['pkgName'])) {
				$res['extra'] = $this->where("name = '%s' and version_name = '%s'  and pkg_name = '%s'",array($get['module'],$get['versionName'],$get['pkgName']))->find();

			}
			if (empty($res['extra'])) {
				$res['extra'] = new \stdClass();
			}else{
				$res['extra']['relyModule'] = json_decode($res['extra']['relyModule'],true);
				if (empty($res['extra']['relyModule'])) {
					$res['extra']['relyModule'] = array();
				}
			}
			return $res;
		}
		public function getValOneForId($id)
		{
			if (isPOrT()) {
				return $this->find($id);
			}

		}
		public function getValOneForIdToComment($id)
		{
			return $this->find($id);

		}
	}