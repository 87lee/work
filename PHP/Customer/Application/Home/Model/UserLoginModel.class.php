<?php
	namespace Home\Model;
	class UserLoginModel extends \Think\Model
	{
		protected $tableName = 'user_login';
		// protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function login($put)
		{
			if (!empty($put['user'])) {
				$time = time();
				$options = array(
					'user'=>$put['user'],
					'login'=>$time,
					'ip'=>$put['loginIp'],
				);
				$logoutOptions = array(
					'logout'=>$time,
					'ip'=>$put['loginIp'],
				);
				$user = $this->field("max(id) as id")->where("user = '%s' and logout=0",array($put['user']))->find();
				if ($user) {
					$this->where(" id= %d ",array($user['id']))->save($logoutOptions);
				}

				return $this->add($options);
			}
		}

		public function logout($put)
		{

			if (!empty($put['user'])) {
				if (!empty($put['loginID'])) {
					if (!$res = $this->where(' user = "%s" and id =%d' ,array($put['user'],$put['loginID']))->find()) {
						$options = array(
							'user'=>$put['user'],
							'login'=>'',
							'logout'=>time(),
							'ip'=>$put['loginIp'],
						);
						return $this->add($options);
					}else{
						$options = array(
							'logout'=>time(),
							'ip'=>$put['loginIp'],
						);
						return $this->where("id = %d",array($put['loginID']))->save($options);
					}
				}else{
					$options = array(
						'user'=>$put['user'],
						'login'=>'',
						'logout'=>time(),
						'ip'=>$put['loginIp'],
					);
					return $this->add($options);
				}

			}
		}

		public function compelLogout($id)
		{
			if ($this->find($id)) {
				$options = array(
					'logout'=>time()
				);
				$this->where('id=%d',array($id))->save($options);
			}
		}

		public function userLoginLists($get)
		{
			if ( $user = isAdmin() ) {
				$where = '';
				if (!empty($get['id'])) {
					$res['extra'] = $this->find($get['id']);
					if ($res['extra']) {
						$res['count'] = 1;
					}
				}elseif( !empty($get['name']) ||  !empty($get['ip']) ||  ( !empty($get['endTime']) || !empty($get['startTime']) ) ){
					if (!empty($get['endTime'])) {
						$get['endTime'] =  intval(strtotime($get['endTime'])) ;
					}
					if (!empty($get['startTime'])) {
						$get['startTime'] = intval(strtotime($get['startTime'])) ;
					}
					if (!empty($get['endTime'])  && !empty($get['startTime'])) {
						$where = " (login >= " .$get['startTime'] . " and login <=". $get['endTime'] .")";
					}elseif (!empty($get['endTime'])) {
						$where = " (login <=". $get['endTime'] .")";
					}elseif (!empty($get['startTime'])) {
						$where = " (login >= " .$get['startTime'] . ")";
					}

					if (!empty($where)&&!empty($get['name'])) {
						$where .= " and (`user` like '%".$get['name']."%')  ";
					}elseif (!empty($get['name'])) {
						$where = "  (`user` like '%".$get['name']."%')  ";
					}
					if (!empty($where)&&!empty($get['ip'])) {
						$where .= " and (`ip` like '%".$get['ip']."%')  ";
					}elseif (!empty($get['ip'])) {
						$where = "  (`ip` like '%".$get['ip']."%')  ";
					}
				}
				$order = 'login desc';
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit( $get['page'],$get['pageSize'] )->where($where)->order($order)->select();

				}else{
					$res['extra'] = $this->where($where)->order($order)->select();
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}

			return $res;
		}
	}