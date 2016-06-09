<?php
	namespace Home\Model;
	class UserModel extends \Think\Model
	{
		protected $tableName = 'user';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'loginIp' =>'login_ip',
			'isMofidyPasswd' =>'is_mofidy_passwd',
		);
		protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);

		public function login($put)
		{
			if (!empty($put['user'])&&!empty($put['passwd'])) {

				if ($res = $this->field('passwd',true)->where(' user = "%s" and passwd = "%s"' ,array($put['user'],md5($put['passwd'])))->find()) {
				    
				    if($res['status'] != 1){
				        result('账号已被删除');
				    }
					$options = array(
						'login_ip'=>$_SERVER['REMOTE_ADDR']
					);
					$res['loginIp'] = $_SERVER['REMOTE_ADDR'];
					$this->where("id =%d",array($res['id']))->save($options);
    					$res['loginID'] = D('UserLogin')->login($res);
    					
    			    //获取用户组,可以多角色
    			    $groupIds=M('AuthGroupAccess')->where(array('uid'=>$res['id']))->getField('group_id',true);
    			    $res['gids']=$groupIds;

					session('androidIsLogin',$res);
					foreach ($res as $key => $value) {
						if ($key != 'isMofidyPasswd') {
							if ($res[$key] == "true") {
								$arr[] = $key;
							}
						}

					}
					//获取所属组
					$group=$this->getGroupTagByUids(array($res['id']));
					$data = array(
					    'id'=>$res['id'],
						'user'=>$res['user'],
						'name'=>$res['name'],
						'email'=>$res['email'],
						'isMofidyPasswd'=>$res['isMofidyPasswd'],
						'ip'=>$res['loginIp'],
						'auths'=>!empty($arr)?$arr:array(),
					    'group'=>empty($group)?'':$group[$res['id']],
					);
					//登陆成功写入cookie，根据cookie判断是否长时间停留无操作
					cookie(md5(session_id()), time(), C('NO_ACTION_TIME'));
					return $data;

				}else{
					result('用户名不存在或者密码不正确');
				}
			}else{
				result('请输入账号密码');
			}
		}
		public function addUser($put)
		{
			if (!empty($put['user'])&&!empty($put['passwd'] && !empty($put['group']) && is_array($put['group']))) {
              //&&($put['publisher'] == 'false' || $put['publisher'] == 'true' )&&($put['tester'] == 'false' || $put['tester'] == 'true' )
              $options = array(
                  'user'=>$put['user'],
                  'name'=>isset($put['name'])?$put['name']:$put['user'],
                  'email'=>isset($put['email'])?$put['email']:'',
                  'passwd'=>md5($put['passwd']),
                  'tourist'=>'true',
                  'login_ip'=>'',
                  'tester'=>in_array(C('SPECIAL_GROUP.TESTER'), $put['group'])?'true':'false',
                  'publisher'=>'false',
                  'admin'=>'false',
                  'is_mofidy_passwd'=>'false',
              );
				if ($this->getOneForName($put['name'])) {
					result('呢称已存在');
				}
				if ($this->getOneForUser($put['user'])) {
					result('用户名已存在');
				}
				$userId=$this->add($options);
				if($userId > 0){
				    //添加到权限表
				    $access=array();
				    foreach ($put['group'] as $v){
				        $access[]=array(
				            'uid'=>$userId,
				            'group_id'=>$v
				        );
				    }
				    !empty($access) && M('AuthGroupAccess')->addAll($access);
				    
				}
				return $userId;
			}else{
				result('param');
			}
		}
		public function modifyUserName($put)
		{
			if (isset($put['name']) || isset($put['email'])) {
				$user = session('androidIsLogin');
				if (!empty($put['name'])) {
					if ($this->getOneForNameNoId($put['name'],$user['id'])) {
						result('该呢称已存在');
					}
					$options = array(
						'name'=>$put['name'],
					);
				}
				if (isset($put['email'])) {
					$options['email'] = $put['email'];
				}
				return $this->where("id = %d",array($user['id']))->save($options);
			}
		}
		public function deleteUser($put)
		{
			if (!empty($put) && is_array($put)) {
				if ($user = isRoot()) {
					foreach ($put as  $value) {
						if ($user['id'] != $value ) {
							$arr[] = (int)$value;
						}
					}
					$arr = array_unique($arr);
					if (!empty($arr)) {
						$sqlId = implode($arr, ',');
					}
				}elseif ($user = isAdmin()) {
					foreach ($put as  $value) {
						if ($user['id'] != $value ) {
							$arr[] = (int)$value;
						}
					}
					$arr = array_unique($arr);
					if (!empty($arr)) {
						$sqlId = implode($arr, ',');
						if ($res = $this->where("id IN (".$sqlId.") ")->select()) {

							foreach ($res as $value) {
								if ($value['user'] != 'root') {
									if ( $value['admin'] != 'true') {
										$deleteArr[] = $value['id'];
									}
								}

							}
							$sqlId = implode($deleteArr, ',');

						}

					}
				}else{
					result('auth');
				}
				if (!empty($sqlId)) {
					if ($res = $this->where("id IN (".$sqlId.") ")->select()) {
						return $this->where("id IN (".$sqlId.")")->setField('status',0);
					}
				}else{
					result('auth');
				}

			}else{
				result('param');
			}
		}

    public function userLists($get)
    {
        //规则：1、排除自己。，2、root用户显是所有人，系统管理员账号显是除root和系统管理员的用户
        $isRoot = isRoot();
        $isAdmin = isAdmin();
        
        if ($isRoot) {
            $user = $isRoot;
            $where = array('u.user' => array('neq', 'root'),'u.status'=>1);
        } else if ($isAdmin) {
            $user = $isAdmin;
            $where = array(
                'u.id' => array('neq', $user['id']), 
                'u.user' => array('neq', 'root'), 
                'u.status' => 1,
                'aga.group_id' => array('neq', C('SPECIAL_GROUP.ADMIN')));
        } else {
            result('auth');
        }
        
        //检索用户名和昵称
        if (! empty($get['name'])) {
            $where['u.user|u.name'] = array('LIKE', '%' . $get['name'] . '%');
        }
        
        $fields = 'DISTINCT u.id,u.user,u.name,u.passwd,u.email,u.is_mofidy_passwd,u.tourist,u.tester,u.publisher,u.admin';
        if (! empty($get['page']) && ! empty($get['pageSize'])) {
            $get['page'] = $get['page'] * $get['pageSize'] - $get['pageSize'];
            $res['extra'] = $this->alias('u')
                ->join('LEFT JOIN tb_auth_group_access aga ON u.id=aga.uid')
                ->where($where)
                ->field($fields)
                ->limit($get['page'], $get['pageSize'])
                ->order('u.id desc')
                ->select();
        } else {
            $res['extra'] = $this->alias('u')
                ->join('LEFT JOIN tb_auth_group_access aga ON u.id=aga.uid')
                ->where($where)
                ->field($fields)
                ->order('id desc')
                ->select();
        }
        
        if (! empty($res['extra'])) {
            $uids = array_column($res['extra'], 'id');
            $groupTag = $this->getGroupTagByUids($uids);
            foreach ($res['extra'] as $k => $v) {
                $res['extra'][$k]['group'] = isset($groupTag[$v['id']]) ? $groupTag[$v['id']] : '';
            }
        }
        $res['count'] = $this->alias('u')
            ->join('LEFT JOIN tb_auth_group_access aga ON u.id=aga.uid')
            ->where($where)
            ->count('DISTINCT u.user');
        return $res;
    }

		public function publisherLists($get)
		{
			if (isAdmin()) {
				$where = "publisher = 'true'  and user !='root'";
				if (!empty($get['id'])) {
					$where .= " and id=" .$get['id'];
					$res['extra'] = $this->field('passwd',true)->where($where)->find();
					$res['count'] = 1;

				}else{
					if (!empty($get['name'])) {
						$where .= "' and (`user` like '%".$get['name']."%' or `name` like '%".$get['name']."%')";
					}
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field('passwd',true)->where($where)->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $this->field('passwd',true)->where($where)->select();
					}
					$res['count'] = $this->where($where)->count();
				}
			}


			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}

			return $res;
		}
		public function testerLists($get)
		{
			if (isAdmin()) {
				$where = "tester = 'true' and user !='root'";
				if (!empty($get['id'])) {
					$where .= " and id=" .$get['id'];
					$res['extra'] = $this->field('passwd',true)->where($where)->find($get['id']);
					$res['count'] = 1;

				}else{
					if (!empty($get['name'])) {
						$where .= "' and (`user` like '%".$get['name']."%' or `name` like '%".$get['name']."%')";
					}
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field('passwd',true)->where($where)->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $this->field('passwd',true)->where($where)->select();
					}
					$res['count'] = $this->where($where)->count();
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}

			return $res;
		}

		public function modifyPasswd($put)
		{
			if ($user = isRoot()) {
				if (!empty($put['newPasswd']) && !empty($put['oldPasswd'])) {
					if ( $user = $this->where("user = '%s'  and  passwd = '%s'",array($user['user'],md5($put['oldPasswd'])))->find())  {
						$options = array(
							'passwd' => md5($put['newPasswd']),
							'is_mofidy_passwd' => 'true',
						);
						$this->where("id = %d",array($user['id']))->save($options);
						session('androidIsLogin',null);
					}else{
						result('旧密码不正确');
					}
				}elseif (!empty($put['user']) && !empty($put['passwd'])) {
					if ($res = $this->where("user = '%s' ",array($put['user']))->find()) {
						$options = array(
							'passwd' => md5($put['passwd']),
						);
						$this->where("user = '%s'",array($put['user']))->save($options);
						if ($put['user'] == $user['user']) {
							session('androidIsLogin',null);
						}
					}else{
						result('用户不存在');
					}
				}
			}elseif ($user = isAdmin()) {
				if (!empty($put['newPasswd']) && !empty($put['oldPasswd'])) {
					if ( $user = $this->where("user = '%s'  and  passwd = '%s'",array($user['user'],md5($put['oldPasswd'])))->find())  {
						$options = array(
							'passwd' => md5($put['newPasswd']),
							'is_mofidy_passwd' => 'true',
						);

						$this->where("id = %d",array($user['id']))->save($options);
						session('androidIsLogin',null);
					}else{

						result('旧密码不正确');
					}
				}elseif (!empty($put['user']) && !empty($put['passwd'])) {
					if ($res = $this->where("user = '%s' ",array($put['user']))->find()) {
						if ($res['user'] == 'root' || $res['admin']=='true') {
							result('auth');
						}
						$options = array(
							'passwd' => md5($put['passwd']),
						);
						$this->where("user = '%s'",array($put['user']))->save($options);
						if ($put['user'] == $user['user']) {
							session('androidIsLogin',null);
						}
					}else{
						result('用户不存在');
					}
				}
			}else{
				$user = session('androidIsLogin');
				if (!empty($put['newPasswd']) && !empty($put['oldPasswd'])) {
					if ($user = $this->where("user = '%s'  and  passwd = '%s'",array($user['user'],md5($put['oldPasswd'])))->find()) {
						$options = array(
							'passwd' => md5($put['newPasswd']),
							'is_mofidy_passwd' => 'true',
						);
						$this->where("id = %d",array($user['id']))->save($options);
						session('androidIsLogin',null);
					}else{
						result('旧密码不正确');
					}
				}
			}
		}

		/**
		 * 管理员修改用户
		 * 
		 * 
		 * @param unknown $put
		 * @return Ambigous <boolean, unknown>
		 * @author 张涛<1353178739@qq.com>
		 * @since  2016年5月24日
		 */
		public function modifyAuth($put)
		{
		    $userId=(int)$put['id'];
		    if($userId <=0 || !$this->find($userId)){
		        result('此用户不存在');
		    }else{
		        $group=!empty($put['group'])?$put['group']:array();
		        $name=!empty($put['name'])?$put['name']:'';
		        $email=!empty($put['email'])?$put['email']:'';
		        if(empty($name)){
		            result('昵称不能为空');
		        }
		        if(empty($email)){
		            result('邮箱地址不能为空');
		        }
		        if(empty($group) || !is_array($group)){
		            result('请分配权限');
		        }
		        if ($this->getOneForNameNoId($name,$userId)) {
		            result('呢称已存在');
		        }
		        if($this->where(array('email'=>$email,'id'=>array('neq',$userId)))->find()){
		            result('邮箱地址已存在');
		        }
		        $modifyData=array(
		            'name'=>$name,
		            'email'=>$email,
		            'tester'=>in_array(C('SPECIAL_GROUP.TESTER'), $group)?'true':'false',
		        );
		        $res=$this->where(array('id'=>$userId))->save($modifyData);
		        if(false !== $res){
		            //先删除用户对应组，再添加
		            $authGroupAccessMod=M('AuthGroupAccess');
		            $authGroupAccessMod->where(array('uid'=>$userId))->delete();
		            //添加到权限表
		            $access=array();
		            foreach ($group as $v){
		                $access[]=array(
		                    'uid'=>$userId,
		                    'group_id'=>$v
		                );
		            }
		            $authGroupAccessMod->addAll($access);
		        }
		        return $res;
		    }
		}
		public function me()
		{
			$user = session('androidIsLogin');
			$res['extra'] = $this->field('id,passwd,is_mofidy_passwd',true)->find($user['id']);
			$groupTag=$this->getGroupTagByUids(array($user['id']));
			$res['extra']['group']=isset($groupTag[$user['id']])?$groupTag[$user['id']]:'';
			return $res ;
		}
		public function getArrIdForNotId($id)
		{
			return $this->field("id")->where("id != %s",array($id))->select();
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s'",array($name))->find();
		}
		public function getOneForNameNoId($name,$id)
		{
			return $this->where("name = '%s' and id != %d",array($name,$id))->find();
		}
		public function getOneForUser($user)
		{
			return $this->where("user = '%s'",array($user))->find();
		}
		public function getOneForID($id)
		{
			return $this->where("id = '%s'",array($id))->find();
		}
		public function getAllNameUser()
		{
			return $this->field('name,user')->select();
		}
		public function getAllNameForUserName($name)
		{
			return $this->field('user')->where("user LIKE '%".$name ."%' or name LIKE '%".$name ."%'")->select(false);
		}
		
		/**
		 * 根据用户id获取组字符串
		 *
		 *
		 * @param array $uids
		 * @return array
		 * @author 张涛<1353178739@qq.com>
		 * @since  2016年5月25日
		 */
		public function getGroupTagByUids($uids)
		{
		    $groupTag = array();
		    if (! empty($uids) && is_array($uids)) {
		        $groupIds = M('authGroupAccess')->alias('aga')
		        ->join('tb_auth_group ag ON aga.group_id=ag.id')
		        ->field('aga.uid,aga.group_id,ag.title')
		        ->where(array('aga.uid' => array('IN', $uids)))
		        ->order('aga.group_id ASC')
		        ->select();
		        if (! empty($groupIds)) {
		            foreach ($groupIds as $v) {
		                if (isset($groupTag[$v['uid']])) {
		                    $groupTag[$v['uid']] .= '、' . $v['title'];
		                } else {
		                    $groupTag[$v['uid']] = $v['title'];
		                }
		            }
		        }
		    }
		    return $groupTag;
		}
	}