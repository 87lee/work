<?php

namespace Home\Model;

class UserModel extends \Think\Model
{

    /**
     * 用户权限，依次：超级管理员、客服管理员、在线客服、普通客服、客户
     * @var unknown
     */
    static $permission = array('root', 'admin', 'online', 'normal', 'customer');

    protected $tableName = 'user';
    // protected $connection = 'DB_DESKTOP';
    protected $_map = array('loginIp' => 'login_ip', 'modifyPasswd' => 'modify_passwd');

    protected $_validate = array(array('name', '', '用户名已存在', 0, 'unique', 1));
    // 在新增的时候验证name字段是否唯一
    public function login($put)
    {
        if (! empty($put['user']) && ! empty($put['passwd'])) {
            
            if ($res = $this->field('passwd', true)
                ->where(' user = "%s" and passwd = "%s"', array($put['user'], md5($put['passwd'])))
                ->find()) {
                //用户登录成功，重新生成sessionid
                    //获取用户所属组id写入session
                $res['group_id']=M('AuthGroupAccess')->where(array('uid'=>$res['id']))->getField('group_id');
                session('[regenerate]');
                session('customerIsLogin', $res);
                //登陆成功写入cookie，根据cookie判断是否长时间停留无操作
                cookie('last_action_time', time(), C('NO_ACTION_TIME'));
                
                $data = array(
                    'id' => $res['id'], 
                    'user' => $res['user'], 
                    'name' => $res['name'], 
                    'email' => $res['email'], 
                    'permission' => $res['permission'], 
                    'modifyPasswd' => $res['modifyPasswd']);
                return $data;
            } else {
                result('用户名不存在或者密码不正确');
            }
        } else {
            result('请输入账号密码');
        }
    }

    public function me()
    {
        $user = session('customerIsLogin');
        $res = $this->field('id,passwd,modify_passwd', true)->find($user['id']);
        if(!empty($res)){
            $authAccess=D('AuthGroupAccess','Logic')->getAuthAccessByUids(array($user['id']));
            $res['group_id']=isset($authAccess[$user['id']])?$authAccess[$user['id']]['group_id']:'';
            $res['group_name']=isset($authAccess[$user['id']])?$authAccess[$user['id']]['group_name']:'';
        }
        return $res;
    }

    public function addUser($put)
    {
        if (! empty($put['user']) && ! empty($put['name']) && ! empty($put['passwd']) && is_numeric($put['permission'])) {
            
            $put['passwd'] = md5($put['passwd']);
            $rules = array(
                array('user', 'require', '用户名不能为空'), 
                array('name', 'require', '昵称不能为空'), 
                array('user', '', '用户名已经存在！', 0, 'unique', 1), 
                array('name', '', '昵称已经存在！', 0, 'unique', 1), 
                array('email', '', '邮箱地址已经存在！', 0, 'unique', 1));
            
            //超级管理员角色只能有一个。id=1
            if(isset($put['permission']) && $put['permission'] == 1){
                result('auth');
            }
            
            if (! $this->validate($rules)->create($put)) {
                result($this->getError());
            } else {
                $newUid = $this->add();
                //添加到用户-》角色对应表
                $newUid !== false && M('AuthGroupAccess')->add(array('uid' => $newUid, 'group_id' => (int) $put['permission']));
                return $newUid;
            }
        } else {
            result('param');
        }
    }

    public function modifyUserName($put)
    {
        if (! empty($put['name'])) {
            $user = session('customerIsLogin');
            if ($this->getOneForNameForNoID($put['name'], $user['id'])) {
                result('该呢称已存在');
            }
            $options = array('name' => isset($put['name']) ? $put['name'] : $user['name']);
            return $this->where("id = %d", array($user['id']))->save($options);
        } else {
            result('param');
        }
    }

    /*
     
     public function modifyUserEmail($put)
     {
     if (!empty($put['name'])) {
     $user = session('customerIsLogin');
     
     $options = array(
     'name'=>isset($put['name'])?$put['name']:$user['name'],
     );
     return $this->where("id = %d",array($user['id']))->save($options);
     
     }else{
     result('param');
     }
     }
     */
    public function modifyUserEmail($put)
    {
        if (! empty($put['email'])) {
            $user = session('customerIsLogin');
            //判断邮箱是否已经绑定
            if($this->where(array('email'=>$put['email'],'user'=>array('neq',$user['user'])))->find()){
                result('邮箱已被其他账号绑定，请更换');
            }
            
            $options = array('email' => isset($put['email']) ? $put['email'] : $user['email']);
            return $this->where("id = %d", array($user['id']))->save($options);
        } else {
            result('param');
        }
    }

    /**
     * 删除用户
     * 
     * 
     * @param unknown $put
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月7日
     */
    public function deleteUser(array $put)
    {
        $loginUser = session('customerIsLogin');
        if (! empty($put) && is_array($put)) {
            //排除登陆用户
            foreach ($put as $k => $v) {
                if ($v == $loginUser['id'] || $v == 1) {
                    unset($put[$k]);
                }
            }
            $put = array_unique($put);
            
            if(false !== $this->where(array('id' => array('in', $put)))->delete()){
                //删除用户对应橘色
                M('authGroupAccess')->where(array('uid' => array('in', $put)))->delete();
            }
            return;
        } else {
            result('param');
        }
    }

    public function userLists($get)
    {
        $user = session('customerIsLogin');
        //获取用户列表，排除root用户即用户id=1
        if (! empty($get['id'])) {
            $where = "user != '" . $user['user'] . "' and id= " . $get['id'];
            $res['extra'] = $this->field('passwd', true)
                ->where($where)
                ->find();
            if (! empty($res['extra'])) {
                $res['count'] = 1;
            }
        } else {
            $where = "user != '" . $user['user'] . "' and id > 1 ";
            if (! empty($get['name'])) {
                $where .= " and ( `name` like '%" . $get['name'] . "%' or `user`  like '%" . $get['name'] . "%')";
            }
            if (! empty($get['page']) && ! empty($get['pageSize'])) {
                $get['page'] = $get['page'] * $get['pageSize'] - $get['pageSize'];
                $res['extra'] = $this->where($where)
                    ->field('passwd', true)
                    ->limit($get['page'], $get['pageSize'])
                    ->select();
            } else {
                $res['extra'] = $this->where($where)
                    ->field('passwd', true)
                    ->select();
            }
            $res['count'] = $this->where($where)->count();
        }
        
        if (empty($res['extra'])) {
            $res['extra'] = array();
            $res['count'] = isset($res['count']) ? $res['count'] : '0';
        } else {
            //获取用户所在的组合组名称
            $isGetById = ! empty($get['id']) ? true : false;
            $uids = $isGetById ? array($res['extra']['id']) : array_column($res['extra'], 'id');
            $groupList = D('AuthGroupAccess','Logic')->getAuthAccessByUids($uids);
            if ($isGetById) {
                $res['extra']['group_id'] = isset($groupList[$res['extra']['id']]) ? $groupList[$res['extra']['id']]['group_id'] : '';
                $res['extra']['group_name'] = isset($groupList[$res['extra']['id']]) ? $groupList[$res['extra']['id']]['group_name'] : '';
            } else {
                foreach ($res['extra'] as $k => $v) {
                    $res['extra'][$k]['group_id'] = isset($groupList[$v['id']]) ? $groupList[$v['id']]['group_id'] : '';
                    $res['extra'][$k]['group_name'] = isset($groupList[$v['id']]) ? $groupList[$v['id']]['group_name'] : '';
                }
            }
        }
        return $res;
    }

    public function getAllForIDSql($IDSql)
    {
        return $this->where("id IN (" . $IDSql . ")")->select();
    }

    /**
     * 修改用户密码
     * 
     * 
     * @param unknown $put
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月7日
     */
    public function modifyPasswd($put)
    {
        //获取登陆用户信息
        $userInfo = session('customerIsLogin');
        //个人修改密码
        if (! empty($put['newPasswd']) && ! empty($put['oldPasswd'])) {
            $loginUser = $this->find($userInfo['id']);
            if ($loginUser) {
                if ($loginUser['passwd'] != md5($put['oldPasswd'])) {
                    result('旧密码不正确');
                } else {
                    $options = array('passwd' => md5($put['newPasswd']), 'modify_passwd' => 'true');
                    $this->where(array('id' => $loginUser['id']))->save($options);
                }
            } else {
                result('非法操作');
            }
        } elseif (! empty($put['user']) && ! empty($put['passwd'])) {
            //管理员重置密码
            $user = $this->where(array('user' => $put['user']))->find();
            if ($user) {
                $this->where(array('id' => $user['id']))->setField('passwd', md5($put['passwd']));
                $user['user'] == $userInfo['user'] && session('customerIsLogin', null);
            } else {
                result('用户不存在');
            }
        } else {
            result('参数有误');
        }
    }

    public function modifyUserInfo($put)
    {
        if (empty($put['user']) && ! (! empty($put['note']) || ! empty($put['email']) || ! empty($put['name']) || ! empty(
            $put['permission']))) {
            result('param');
        }
        
        //超级管理员角色只能有一个。id=1
        if(isset($put['permission']) && $put['permission'] == 1){
            result('auth');
        }
        
        if (! $res = $this->getOneForUser($put['user'])) {
            result('此用户不存在');
        }
        
        if (isset($put['email'])) {
            //判断邮箱是否已经绑定
            if($this->where(array('email'=>$put['email'],'user'=>array('neq',$put['user'])))->find()){
                result('邮箱已被其他账号绑定，请更换');
            }
            $options['email'] = $put['email'];
        }
        if (isset($put['note'])) {
            $options['note'] = $put['note'];
        }
        if (isset($put['name'])) {
            if ($this->getOneForNameForNoID($put['name'], $res['id'])) {
                result('该呢称已存在');
            }
            $options['name'] = $put['name'];
        }
        /*if (! empty($put['permission'])) {
            $options['permission'] = $put['permission'];
        }*/
        if (! empty($options)) {
            $this->where('id = %d', array($res['id']))->save($options);
        }
        //添加到用户-》角色对应表
        if (! empty($put['permission'])) {
            $authGroupAccessMod = M('AuthGroupAccess');
            if ($authGroupAccessMod->where(array('uid' => $res['id']))->find()) {
                $authGroupAccessMod->where(array('uid' => $res['id']))->setField('group_id', (int) $put['permission']);
            } else {
                $authGroupAccessMod->add(array('uid' => $res['id'], 'group_id' => (int) $put['permission']));
            }
        }
        return;
    }

    public function getOneForName($name)
    {
        return $this->where("name = '%s'", array($name))->find();
    }

    public function getOneForNameForNoID($name, $id)
    {
        return $this->where("name = '%s' and id != %d", array($name, $id))->find();
    }

    public function getOneForUser($user)
    {
        return $this->where("user = '%s'", array($user))->find();
    }

    public function getOneForID($id)
    {
        return $this->where("id = '%s'", array($id))->find();
    }

    public function getAllNameUser()
    {
        return $this->field('name,user')->select();
    }

    public function getAllNameForUserName($name)
    {
        return $this->field('user')
            ->where("user LIKE '%" . $name . "%' or name LIKE '%" . $name . "%'")
            ->select(false);
    }

    /**
     * 根据用户id获取用户信息
     * 
     * 
     * @param array $ids
     * @param string $widthIndex
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月29日
     */
    public function getUserByIds(array $ids, $widthIndex = false)
    {
        if ($widthIndex) {
            return $this->where(array('id' => array('in', $ids)))
                ->index('id')
                ->select();
        } else {
            return $this->where(array('id' => array('in', $ids)))->select();
        }
    }

    /**
     * 根据用户名获取昵称
     * 
     * 
     * @param array $ids
     * @param string $widthIndex
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月8日
     */
    public function getUserByUser(array $users, $widthIndex = false)
    {
        if ($widthIndex) {
            return $this->where(array('user' => array('in', $users)))
                ->field('user,name')
                ->index('user')
                ->select();
        } else {
            return $this->where(array('user' => array('in', $users)))
                ->field('user,name')
                ->select();
        }
    }
}
