<?php
	namespace Home\Model;
	use Think\Model;
	class UserModel extends Model
	{
		protected $tableName = 'user';
		protected $_validate = array(
			array('user','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
			array('passwd','checkPwd','密码格式不正确',0,'function') // 自定义函数验证密码格式
		);

	    public function index()
	    {
    		// var_dump(D('UserAuth')->getAuth());
	    }
	    /**
	     * 退出登陆
	     */
	    public function logout()
	    {
	    	session('isMonitoringLogin',null);
	    	result();
	    }
	    /**
	     * 登陆用户
	     * @param  [string] $user [用户账号]
	     * @param  [string] $passwd  [用户密码]
	     * @return [json]
	     */
		public function login($user,$passwd)
		{
			if (empty($user)||empty($passwd))
			{
				result('请输入账号或密码');
			}
		            $response = $this->where("`user` = '". $user . "'")->find();

		            $data = array();

		            if (!$response){
		            		result('账号不存在');
		            }
			if ($response['passwd'] != md5($passwd)) {
		            		result('密码不正确');
		            	}

			$response['uid'] = $response['id'];
			$response['name'] = $response['user'];
	            		session('isMonitoringLogin',$response);

	            		if ($this->_isAdmin()) {
	            			$module = M('module');
	            			$sub_module =M('module_sub');
	            			$modules = $module->select();
	            			if ($modules) {
	            				foreach ($modules as $key => $value) {
		            				$arr = $value;
		            				$sub_modules = $sub_module->field('`id` AS sub_id,`sub_module`,`sub_module_name`')->where("`module_id`=". $value['id'] )->select();

		            				if ($sub_modules) {
		            					$arr['sub_modules'] = $sub_modules;
		            				}else{
		            					$arr['sub_modules'] = array();
		            				}
		            				$options[]=$arr;
		            			}
	            			}else{
	            				$options=array();
	            			}


            			}else{
            				$user_auth = M('user_auth');
		            		$module = M('module');
		            		$module_group = $user_auth->field('module_id')->where("`user_id` = " . $response['id'])->group('module_id')->select();
				$model = M();
		            		if ($module_group) {
		            			foreach ($module_group as $key => $value) {

			            			$arr = $module->find($value['module_id']);
			            			$sub_modules = $model->query("SELECT * FROM `tb_module_sub` WHERE `module_id`=". $value['module_id'] . " AND `id` IN( SELECT `sub_module_id` FROM `tb_user_auth` WHERE `user_id`=".$response['id'] . " AND module_id= ". $value['module_id'] .  ")");
			            			if ($sub_modules) {
			            				$arr['sub_modules'] = $sub_modules;
			            			}else{
			            				$arr['sub_modules'] = array();
			            			}

			            			$options[]=$arr;
			            		}
		            		}else{
		            			$options =array();
		            		}
            			}
            			$data = array(
        				'modules'=>$options,
        				"email"=>$response['email']
        			);
            			result(true,$data);
		}
		/**
		 * 增加用户
		 * @param [string] $name [用户账号]
		 * @param [string] $passwd [用户密码]
		 */
		public function addUser($put)
		{
			if (empty($put['user'])){
				result('请输入账号');
			}
			if (empty($put['passwd'])){
				result('请输入密码');
			}
			if (empty($put['email'])){
				result('请输入邮箱');
			}
			// $user = $this->_getLoginUserInfo();

			if (!$this->_isAdmin()) {
				result('auth');
			}
			$options = array(
				'user'=>$put['user'],
				'email'=>$put['email'],
				'passwd'=>md5($put['passwd']),
			);
			if (!$options = $this->create($options)) {
				result('添加失败！用户已存在！');
			}
			$result = $this->add();

			if (!M('module_sub')->field('id')->where("`sub_module_name` = '修改密码'")->find()) {
				result('添加用户成功，添加修改密码功能失败：请手动添加');
			}
			$res = M('module_sub')->where("`sub_module_name` = '修改密码'")->find();
			$options = array(
				'module_id'=>$res['module_id'],
				'user_id'=>$result,
				'sub_module_id'=>$res['id']
			);
			if (!M('user_auth')->add($options)) {
				result('添加用户成功，添加修改密码功能失败：请手动添加');
			}
			return ;
		}
		/**
		 * 删除用户
		 * @param  [string] $id [用户ID]
		 * @return json
		 */
		public function delUser($id)
		{
			if (empty($id)) {
				result('param');
			}
				// $user = $this->_getLoginUserInfo();
			if (!$this->_isAdmin()) {
				result('auth');
			}
			if (!$this->find($id)){
				result('您要删除的账号不存在！');
			}
			$del_user = $this->find($id);

			if ( $del_user['root'] =='1' ) {
				result('auth');
			}
			if (M('user_auth')->where("`user_id`= '" . $id ."'")->find()) {
				result('用户功能没有删除，请手动删除');
			}
			if (!$this->where("`id`='" . $id. "'")->delete()) {
				result('删除用户失败');
			}
			result();



		}


		/**
		 * 修改用户信息
		 * @param  [string] $name    [用户名]
		 * @param  [string] $old_psw [用户旧密码]
		 * @param  [string] $new_psw [用户新密码] 超级用户可以直接填写新密码和用户名改
		 * @return [json]
		 */
		public function editPasswd($put)
		{
			if (empty($put['user']) || empty($put['new'])){
				result('param');
			}
			if (!empty($put['old'])){
				$user = $this->_getLoginUserInfo();
				$where = '`id` = ' . $user['id'];
				if ($user['passwd'] != md5($put['old'])){
					result('原密码不正确');
				};

				$options['passwd'] = md5($put['new']);

			}elseif ($this->_isAdmin()){

				$create['user'] = $put['user'];

				if ($this->create($create)) {
					result('修改的账号不存在');
				}

				$where = "`user` = '" . $put['user'] . "'";
				$options =array(
					'passwd'=>md5($put['new'])
				);
			}
			if (isset($put['email'])) {
				if (empty($put['email'])) {
					result('邮件地址不能为空');
				}
				$options['email'] = $put['email'];
			}
			$this->where($where)->save($options);
			return ;
		}

		/**
		 * 获取用户 ID 用户名
		 * @param [string] $id 用户ID 如果有这个选项就获取单个用户 没有就获取所有用户
		 * @return json
		 */
		public function getAllUser($get)
		{
			$field = 'id,user,email';
			if ( !empty($get['id'])) {
				$data = $this->field($field)->find($id);
				if(!$data){
					result('SQL出错');
				}


			}else{

				$data['content'] = $this->field($field)->select();
				if (empty($data['content'])) {
					$data['content'] = array();
				}
			}
			return $data;
		}



		/**
		 * 获取登陆用户信息
		 * @return array 登陆用户信息
		 */
		public function _getLoginUserInfo()
		{
			if ( empty( session('isMonitoringLogin') ) ) {
				result('isLogin');
			}

			$user_info = session('isMonitoringLogin');
			return $this->find($user_info['uid']);
		}

		/**
		 * 是否为超级管理员
		 * @return [type] [description]
		 */
		public function _isAdmin()
		{
			$user_info = session('isMonitoringLogin');
			$user = $this->find($user_info['uid']);
			if ($user['root']=='1') {
				return true;
			}else{
				return false;
			};
		}
		public function getCountForIdSql($idSql)
		{
			return $this->where("id IN (".$idSql.")")->count();
		}
		public function checkEmailForIdSql($idSql)
		{
			$res = $this->where("id IN (".$idSql.")")->select();
			if (!empty($res)) {
				$msg = '';
				foreach ($res as $value) {
					if (empty($value['email'])) {
						$msg .= $value['user'] . '、';
					}
				}
				if (!empty($msg)) {
					return ltrim($msg,'、').'邮箱为空';
				}
			}
			return ;
		}
	}
?>