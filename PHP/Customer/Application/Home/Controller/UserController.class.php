<?php

namespace Home\Controller;


class UserController extends HomeBaseController
{
	public function index(){

    	}
    	public function __construct()
    	{
	        	parent::__construct();
//         isLogin();

    	}
    	public function _empty(){
    		result('请填写正确地址');
    	}
    	/**
    	 * 客服系统__登陆
    	 * post /Customer/home/user/login
    	 * {"user":"admin","passwd":"admin"}
    	 * @return [type] [description]
    	 */
    	public function login()
    	{
    		$put = I('put.');
    		$res = D('User')->login($put);
    		result(true,$res);
    	}
    	/**
    	 * 客服系统__个人信息登陆
    	 * get /Customer/home/user/me
    	 * @return [type] [description]
    	 */
    	public function me()
    	{

    		$res = D('User')->me();
    		result(true,$res);
    	}
    	/**
    	 * 客服系统_退出登陆
    	 * get /Customer/home/user/logout
    	 * @return [type] [description]
    	 */
    	public function logout()
    	{
    		// D('UserLogin')->logout(session('customerIsLogin'));
    		session('customerIsLogin',null);
    		result();
    	}

    	/**
    	 * 客服系统_添加用户
    	 * post /Customer/home/user/addUser
    	 * {	"user":"用户名",
    	 * 	"name":"呢称",
    	 * 	"passwd":"密码",
    	 * 	"permission":"root/admin/online/normal/customer",超级管理员、客服管理员、在线客服、普通客服、客户
    	 * 	"email":"用户邮箱",
    	 * 	"note":"备注",
    	 * }
    	 * @return [type] [description]
    	 */
    	public function addUser()
    	{
    		$put = I('put.');
    		D('User')->addUser($put);
    		result();
    	}
    	/**
    	 * 客服系统_修改用户呢称
    	 * post /Customer/home/user/modifyUserName
    	 * {"name":"admin"}
    	 * @return [type] [description]
    	 */
    	public function modifyUserName()
    	{
    		$put = I('put.');
    		D('User')->modifyUserName($put);
    		result();
    	}
    	/**
    	 * 客服系统_修改用户呢称
    	 * post /Customer/home/user/modifyUserEmail
    	 * {"email":"admin"}
    	 * @return [type] [description]
    	 */
    	public function modifyUserEmail()
    	{
    		$put = I('put.');
    		D('User')->modifyUserEmail($put);
    		result();
    	}

    	/**
    	 * 客服系统_删除用户
    	 * post /Customer/home/user/deleteUser
    	 * ["1","2"]
    	 * @return [type] [description]
    	 */
    	public function deleteUser()
    	{
    		$put = I('put.');
    		D('User')->deleteUser($put);
    		result();
    	}
    	/**
    	 * 客服系统_用户列表
    	 * get /Customer/home/user/userLists?id=x
    	 * get /Customer/home/user/userLists?page=x&pageSize=x
    	 * get /Customer/home/user/userLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function userLists()
    	{
    		$get = I('get.');
    		$res = D('User')->userLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 客服系统_发布用户列表
    	 * get /Customer/home/user/publisherLists?id=x
    	 * get /Customer/home/user/publisherLists?page=x&pageSize=x
    	 * get /Customer/home/user/publisherLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function publisherLists()
    	{
    		$get = I('get.');
    		$res = D('User')->publisherLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 客服系统_发布测试用户列表
    	 * get /Customer/home/user/testerLists?id=x
    	 * get /Customer/home/user/testerLists?page=x&pageSize=x
    	 * get /Customer/home/user/testerLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function testerLists()
    	{
    		$get = I('get.');
    		$res = D('User')->testerLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 客服系统_修改密码
    	 * post /Customer/home/user/modifyPasswd
    	 * {"user":"admin","passwd":"admin"} {"newPasswd":"admin","oldPasswd":"admin"}
    	 * @return [type] [description]
    	 */
    	public function modifyPasswd()
    	{
    		$put = I('put.');
    		D('User')->modifyPasswd($put);
    		result();
    	}

    	/**
    	 * 客服系统_修改权限
    	 * post /Customer/home/user/modifyAuth
    	 * {"user":"admin","passwd":"admin"}
    	 * {
    	 * 	"id":"用户ID",
    	 * 	"tourist":"false/true",是否普通游客
    	 *  	"tester":"false/true",是否测试用户
    	 *  	"publisher":"false/true",是否发布用户
    	 *  	"admin":"false/true",是否系统管理员
    	 *  }
    	 * @return [type] [description]
    	 */
    	/*public function modifyAuth()
    	{
    		$put = I('put.');
    		D('User')->modifyAuth($put);
    		result();
    	}*/
    	/**
    	 * 客服系统_修改信息
    	 * post /Customer/home/user/modifyUserInfo
    	 * {
    	 * 	"user":"用户名",
    	 * 	"email":"邮箱",
    	 * 	"name":"呢称"
    	 * 	"permission":"root/admin/online/normal/customer",超级管理员、客服管理员、在线客服、普通客服、客户
    	 * }
    	 * @return [type] [description]
    	 */
    	public function modifyUserInfo()
    	{
    		$put = I('put.');
    		D('User')->modifyUserInfo($put);
    		result();
    	}
    	/**
    	 * 客服系统_添加用户模块权限
    	 * post /Customer/home/user/addModuleAdmin
    	 * {
    	 * 	"name":"模块名"
    	 *  	"operator":"用户名",
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	public function addModuleAdmin()
    	{
    		$put = I('put.');
    		D('ModuleAdmin')->addModuleAdmin($put);
    		result();
    	}
    	/**
    	 * 客服系统_修改用户模块权限
    	 * post /Customer/home/user/modifyModuleAdmin
    	 * {
    	 * 	"id":"用户模块权限ID"
    	 * 	"name":"模块名"
    	 *  	"operator":"用户名",
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	/*public function modifyModuleAdmin()
    	{
    		$put = I('put.');
    		D('ModuleAdmin')->modifyModuleAdmin($put);
    		result();
    	}*/
    	/**
    	 * 客服系统_删除用户模块权限
    	 * post /Customer/home/user/deleteModuleAdmin
    	 * ["2","1"]
    	 * @return [type] [description]
    	 */
    	public function deleteModuleAdmin()
    	{
    		$put = I('put.');
    		D('ModuleAdmin')->deleteModuleAdmin($put);
    		result();
    	}
    	/**
    	 * 客服系统_用户模块权限列表
    	 * get  /Customer/home/user/moduleAdminLists?id=x
    	 * get  /Customer/home/user/moduleAdminLists?page=x&pageSize=x
    	 * get  /Customer/home/user/moduleAdminLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function moduleAdminLists()
    	{
    		$get = I('get.');
    		$res = D('ModuleAdmin')->moduleAdminLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 客服系统_当前用户模块权限列表
    	 * get  /Customer/home/user/currentModuleAdminLists?id=x
    	 * get  /Customer/home/user/currentModuleAdminLists?page=x&pageSize=x
    	 * get  /Customer/home/user/currentModuleAdminLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function currentModuleAdminLists()
    	{
    		$get = I('get.');
    		$res = D('ModuleAdmin')->currentModuleAdminLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 客服系统_添加用户应用权限
    	 * post /Customer/home/user/addAppAdmin
    	 * {
    	 * 	"name":"模块名"
    	 *  	"operator":"用户名",
    	 *  	"operation":"选项",
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	public function addAppAdmin()
    	{
    		$put = I('put.');
    		D('AppAdmin')->addAppAdmin($put);
    		result();
    	}
    	/**
    	 * 客服系统_修改用户应用权限
    	 * post  /Customer/home/user/modifyAppAdmin
    	 * {
    	 * 	"id":"用户模块权限ID"
    	 * 	"name":"应用名"
    	 * 	"operation":"选项",
    	 *  	"operator":"用户名",
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	public function modifyAppAdmin()
    	{
    		$put = I('put.');
    		D('AppAdmin')->modifyAppAdmin($put);
    		result();
    	}
    	/**
    	 * 客服系统_删除用户应用权限
    	 * get /Customer/home/user/deleteAppAdmin?id=x
    	 * @return [type] [description]
    	 */
    	public function deleteAppAdmin()
    	{
    		$put = I('put.');
    		D('AppAdmin')->deleteAppAdmin($put);
    		result();
    	}
    	/**
    	 * 客服系统_用户应用权限列表
    	 * get /Customer/home/user/appAdminLists?id=x
    	 * get /Customer/home/user/appAdminLists?page=x&pageSize=x
    	 * get /Customer/home/user/appAdminLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function appAdminLists()
    	{
    		$get = I('get.');
    		$res = D('AppAdmin')->appAdminLists($get);
    		result(true,$res);
    	}

    	/**
    	 * 客服系统_当前用户应用权限列表
    	 * get /Customer/home/user/currentAppAdminLists?page=x&pageSize=x
    	 * get /Customer/home/user/currentAppAdminLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function currentAppAdminLists()
    	{
    		$get = I('get.');
    		$res = D('AppAdmin')->currentAppAdminLists($get);
    		result(true,$res);
    	}
    	//------------------------------------------------------------------------------------------------
    	/**
    	 * 客服系统_添加应用
    	 * post /Customer/home/user/addApp
    	 * {
    	 * 	"name":"模块名"
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	public function addApp()
    	{
    		$put = I('put.');
    		D('App')->addApp($put);
    		result();
    	}
    	/**
    	 * 客服系统_修改应用
    	 * post  /Customer/home/user/modifyApp
    	 * {
    	 * 	"id":"应用ID"
    	 * 	"name":"应用名"
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	public function modifyApp()
    	{
    		$put = I('put.');
    		D('App')->modifyApp($put);
    		result();
    	}
    	/**
    	 * 客服系统_删除应用
    	 * post /Customer/home/user/deleteApp
    	 * ["1"]
    	 * @return [type] [description]
    	 */
    	public function deleteApp()
    	{
    		$put = I('put.');
    		D('App')->deleteApp($put);
    		result();
    	}
    	/**
    	 * 客服系统_应用列表
    	 * get /Customer/home/user/appLists?id=x
    	 * get /Customer/home/user/appLists?page=x&pageSize=x
    	 * get /Customer/home/user/appLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function appLists()
    	{
    		$get = I('get.');
    		$res = D('App')->appLists($get);
    		result(true,$res);
    	}
    	//--------------------------------------------------------------------------------------------------
    	//
    	//------------------------------------------------------------------------------------------------
    	/**
    	 * 客服系统_添加应用
    	 * post /Customer/home/user/addModule
    	 * {
    	 * 	"name":"模块名"
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	public function addModule()
    	{
    		$put = I('put.');
    		D('Module')->addModule($put);
    		result();
    	}
    	/**
    	 * 客服系统_修改应用
    	 * post  /Customer/home/user/modifyModule
    	 * {
    	 * 	"id":"应用ID"
    	 * 	"name":"应用名"
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	public function modifyModule()
    	{
    		$put = I('put.');
    		D('Module')->modifyModule($put);
    		result();
    	}
    	/**
    	 * 客服系统_删除应用
    	 * post /Customer/home/user/deleteModule
    	 * ["1"]
    	 * @return [type] [description]
    	 */
    	public function deleteModule()
    	{
    		$put = I('put.');
    		D('Module')->deleteModule($put);
    		result();
    	}
    	/**
    	 * 客服系统_应用列表
    	 * get /Customer/home/user/ModuleLists?id=x
    	 * get /Customer/home/user/ModuleLists?page=x&pageSize=x
    	 * get /Customer/home/user/ModuleLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function ModuleLists()
    	{
    		$get = I('get.');
    		$res = D('Module')->moduleLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 客服系统_登陆列表
    	 * get /Customer/home/user/userLoginLists?id=x
    	 * get /Customer/home/user/userLoginLists?page=x&pageSize=x
    	 * get /Customer/home/user/userLoginLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function userLoginLists()
    	{
    		$get = I('get.');
    		$res = D('UserLogin')->userLoginLists($get);
    		result(true,$res);
    	}
    	//--------------------------------------------------------------------------------------------------

    /**
     * 获取客户列表
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月17日
     */
    public function customerList()
    {
        try {
            $type = I('get.type', 'customer');
            $permissons = strpos($type, '-') === false ? $type : explode('-', $type);
            $res = D('User', 'Logic')->getUserByPermission($permissons, I('get.name'), 'u.id,u.user,u.name,u.email,u.note');
        } catch (\Exception $e) {
            json_echo($e->getCode(), $e->getMessage());
        }
        json_echo(C('SUCCESS'), '成功', $res);
    }

    /**
     * 个人中心
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function personal()
    {
        $this->display();
    }

    /**
     * 用户管理
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function admin()
    {
        $this->display();
    }

    /**
     * 我的问题单
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function myQlist()
    {
        $this->display();
    }

    /**
     * 问题管理
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function Qmanage()
    {
        $this->display();
    }

    /**
     * 发送找回密码邮件
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public function sendFindPwdEmail()
    {
        $this->isPost();
        try {
            $email = I('post.email', '');
            $res = D('User', 'Logic')->sendFindPwdEmail($email);
            if ($res) {
                json_echo(C('SUCCESS'), '发送邮件成功');
            }
            json_echo(C('SUCCESS'), '发送邮件失败');
        } catch (\Exception $e) {
            if ($e->getCode()) {
                $this->getException($e);
            } else {
                json_echo(C('UNKNOWN_ERROR'), '发送邮件异常');
            }
        }
    }

    /**
     * 用户重置密码
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public function userResetPwd()
    {
        //$_POST = ['key' => $_GET['key'], 'pwd' => '123456789123','re_pwd'=>'123456789123'];
        $this->isPost();
        try {
            if (D('User', 'Logic')->userResetPwdByEmail(I('post.')) !== false) {
                session('customerIsLogin', null);
                json_echo(C('SUCCESS'), '密码重置成功');
            }
            json_echo(C('SUCCESS'), '密码重置失败');
        } catch (\Exception $e) {
            json_echo($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 获取客户列表，支持分页和检索
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月12日
     */
    public function getCustomerList()
    {
        try {
            json_echo(C('SUCCESS'), '成功', D('User', 'Logic')->getUserList(I('get.'), 5));
        } catch (\Exception $e) {
            json_echo($e->getCode(), $e->getMessage());
        }
    }
}
