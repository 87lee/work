<?php
	namespace Home\Model;
	class AppAdminModel extends \Think\Model
	{
		protected $tableName = 'app_admin';
		// protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addAppAdmin($put)
		{
			if (!empty($put['name'])&&!empty($put['operator'])&&($put['operation']=='test' || $put['operation']=='publish' )) {
				if (!$appAdmin = D('User')->getOneForName($put['operator'])) {
					result('该用户不存在');
				}
				
				//判断所选用户是否是对应的发布或者测试用户
				$thisUserGroups=M('AuthGroupAccess')->where(array('uid'=>$appAdmin['id']))->getField('group_id',true);
				
				if($put['operation']=='test' && !in_array(C('SPECIAL_GROUP.TESTER'), $thisUserGroups)){
				    result('该用户不是测试用户');
				}
				if($put['operation']=='publish' && !in_array(C('SPECIAL_GROUP.PUBLISHER'), $thisUserGroups)){
				    result('该用户不是开发用户');
				}
				
				if (!$res = $this->where(' name = "%s"  and operator = "%s"  and operation = "%s"' ,array($put['name'],$appAdmin['user'],$put['operation']))->find()) {

					if (isAdmin()) {
						if (!D('App')->getOneForName($put['name'])) {
							result('该应用不存在');
						}
						$options = array(
							'name'=>$put['name'],
							'operator'=>$appAdmin['user'],
							'operation'=>$put['operation'],
							'note'=>!empty($put['notes'])?$put['notes']:'',
						);
						return $this->add($options);

					}else{
						result('auth');
					}

				}else{
					result('该用户已有该应用权限');
				}


			}else{
				result('param');
			}
		}

		public function deleteAppAdmin($put)
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

		/*public function modifyAppAdmin($put)
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
		}*/

		public function appAdminLists($get)
		{

			if ( $user = isAdmin() ) {

				if (!empty($get['id'])) {
					$res['extra'] = $this->find($get['id']);
					if ($res['extra']) {
						$res['count'] = 1;
					}
				}else{
					$where = '';
					if (!empty($get['name'])) {
						$UserName = D('User')->getAllNameForUserName($get['name']);
						$appNameSql = D('App')->getNameSqlForNameApp($get['name']);
						$where = " `name` like '%".$get['name']."%'  or `operator` IN (".$UserName.")  or  name IN (".$appNameSql.")";
					}
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit( $get['page'],$get['pageSize'] )->where($where)->select();

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
						$appNameSql = D('App')->getNameSqlForNameApp($get['name']);
						$where .= " and ( `name` like '%".$get['name']."%'  or `operator` IN (".$UserName.")  or  name IN (".$appNameSql."))";

						// $where = " `name` like '%".$get['name']."%' or  `pkg_name` like '%".$get['name']."%' or publisher IN (".$UserNameSql.")  or  pkg_name IN (".$appNameSql.") ";
					}
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();

						// echo $this->getLastSql();
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

				$appArr = D('App')->getAll();
				if (!empty($appArr)) {
					foreach ($appArr as  $value) {

						$appData[ $value['name'] ] = $value['app'];
					}
				}

				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						if (!empty($nameArr[$value['operator']])) {
							$res['extra'][$key]['operator'] = $nameArr[$value['operator']];
						}

						if (!empty($appData[$value['name']])) {
							$res['extra'][$key]['app'] = $appData[$value['name']];
						}else{
							$res['extra'][$key]['app'] = $value['name'];
						}
					}
				}else{
					if (!empty($nameArr[$res['extra']['operator']])) {
						$res['extra']['operator'] = $nameArr[$res['extra']['operator']];
					}
					if (!empty($appData[$res['extra']['name']])) {
						$res['extra']['app'] = $appData[$res['extra']['name']];
					}else{
						$res['extra']['app'] = $res['extra']['name'];
					}
				}
			}

			return $res;
		}
		public function currentAppAdminLists($get)
		{
			if ($user = session('androidIsLogin')) {
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field("operator" ,true)->limit($get['page'],$get['pageSize'])->where(" `name` like '%".$get['name']."%'  and operator = '".$user['user']."' ")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->field("operator" ,true)->where(" `name` like '%".$get['name']."%'   and operator = '".$user['user']."' ")->select();
					}
					$res['count'] = $this->where(" `name` like '%".$get['name']."%'  and operator = '".$user['user']."' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field("operator" ,true)->where("operator='%s' ",array($user['user']))->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $this->field("operator" ,true)->where("operator='%s' ",array($user['user']))->select();
					}
					$res['count'] = $this->where("operator='%s' ",array($user['user']))->count();
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
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
	    			// $this->where( "operator ='%s' and name = '%s' and operation ='publish' ",array($user['user'],$name) )->select(false);

    				if ( $this->where( "operator ='%s' and name = '%s' and operation ='publish' ",array($user['user'],$name) )->find() ) {
    					return true;
    				}else{
    					return false;
    				}
	    		}else{
	    			return false;
	    		}
	    	}
	    	/**
	    	 * 检查是否有模块权限
	    	 * @param string $value [description]
	    	 */
	    	public  function isAppTester($name)
	    	{

	    		if ($user = isTester()) {

    				if ($this->where("operator ='%s' and name = '%s' and operation ='test' ",array($user['user'],$name))->find()) {
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