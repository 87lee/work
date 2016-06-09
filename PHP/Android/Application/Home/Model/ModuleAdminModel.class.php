<?php
	namespace Home\Model;
	class ModuleAdminModel extends \Think\Model
	{
		protected $tableName = 'module_admin';
		// protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addModuleAdmin($put)
		{
			if (!empty($put['names'])&&!empty($put['operator'])&&($put['operation'] == 'publish')) {
				if (!$moduleAdmin = D('User')->getOneForName($put['operator'])) {
					result('该用户不存在');
				}
				if (!$res = $this->where(' name = "%s"  and operator = "%s" and operation = "%s"' ,array($put['names'],$moduleAdmin['user'],$put['operation']))->find()) {

					if (isAdmin()) {
						if (!D('Module')->getOneForName($put['names'])) {
							result('该模块不存在');
						}

						$options = array(
							'name'=>$put['names'],
							'operator'=>$moduleAdmin['user'],
							'operation'=>$put['operation'],
							'note'=>!empty($put['notes'])?$put['notes']:'',
						);

						return $this->add($options);


					}else{
						result('auth');
					}

				}else{
					result('该用户已有该模块权限');
				}
			}else{
				result('param');
			}
		}

		public function deleteModuleAdmin($put)
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

							return $this->where("id IN (".$sqlId.")")->delete();

							/*if (!($res['admin'] == 'true')) {

							}else{

								result('不能删除系统管理员');
							}*/
						}

						// var_dump($res);

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
				if (!empty($put['id']) && !empty($put['name']) && !empty($put['operator']) &&($put['operation'] == 'publish')) {
					if ($this->find($put['id'])) {
						if ($this->where("name = '%s'  and  operator = '%s' and id !=%d",array($user['name'],$put['operator'],$put['id']))->find()) {
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
		}*/

		public function moduleAdminLists($get)
		{
			if (isAdmin()) {

				if (!empty($get['id'])) {
					$res['extra'] = $this->find($get['id']);
					if ($res['extra']) {
						$res['count'] = 1;
					}

				}else{
					$where = "";
					if (!empty($get['name'])){
						$UserName = D('User')->getAllNameForUserName($get['name']);
						$where = "  ( `name` like '%".$get['name']."%' or `operator` IN (".$UserName.") )";
					}

					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();

					}else{
						$res['extra'] = $this->where($where)->select();
					}
					$res['count'] = $this->where($where)->count();
				}
			}elseif ($user = isPOrTOrAdmin()) {
				if (!empty($get['id'])) {
					$res['extra'] = $this->find($get['id']);
					if ($res['extra']) {
						$res['count'] = 1;
					}
				}else {

					$where = "operator != '".$user['user']."'";
					if (!empty($get['name'])){
						$UserName = D('User')->getAllNameForUserName($get['name']);
						$where .= " and ( `name` like '%".$get['name']."%' or `operator` IN (".$UserName.") )";
					}

					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();

					}else{
						$res['extra'] = $this->where($where)->select();
					}
					$res['count'] = $this->where($where)->count();
				}
			}


			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				$UserArr = D('User')->getAllNameUser();
				foreach ($UserArr as  $value) {
					$nameArr[$value['user']] = $value['name'];
				}
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						if (!empty($nameArr[$value['operator']])) {
							$res['extra'][$key]['operator'] = $nameArr[$value['operator']];
						}
					}
				}else{
					if (!empty($nameArr[$res['extra']['operator']])) {
						$res['extra']['operator'] = $nameArr[$res['extra']['operator']];
					}
				}
			}

			return $res;
		}

		public function currentModuleAdminLists($get)
		{
			if ($user = session('androidIsLogin')) {

				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field("operator" ,true)->limit($get['page'],$get['pageSize'])->where("`name` like '%".$get['name']."%'  and operator = '".$user['user']."' ")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->field("operator" ,true)->where(" `name` like '%".$get['name']."%' and  operator = '".$user['user']."' ")->select();
					}
					$res['count'] = $this->where(" `name` like '%".$get['name']."%' and operator = '".$user['user']."' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field("operator" ,true)->limit($get['page'],$get['pageSize'])->where("operator='%s' ",array($user['user']))->select();
					}else{
						$res['extra'] = $this->field("operator" ,true)->where("operator='%s' ",array($user['user']))->select();
					}
					$res['count'] = $this->where("operator='%s' ",array($user['user']))->count();
				}
			}


			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = 0;
			}

			return $res;
		}
		/**
	    	 * 检查是否有模块权限
	    	 * @param string $value [description]
	    	 */
	    	public  function isAppPublish($name)
	    	{

	    		if ($user = isPublisher()) {

    				if ($this->where("operator ='%s' and name = '%s' and operation ='publish' ",array($user['user'],$name))->find()) {
    					return true;
    				}else{
    					return false;
    				}
	    		}else{
	    			return false;
	    		}
	    	}
	    	public function getOneForName($name)
		{
			return $this->where("name = '%s'",array($name))->find();
		}
	}