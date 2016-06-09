<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends HomeBaseController {
	public function index(){

    	}
    	public function __construct()
    	{
	        	parent::__construct();
	       	//isLogin();

    	}
    	public function _empty(){
    		result('请填写正确地址');
    	}
    	/**
    	 * Android前端发布系统__登陆
    	 * post /Android/home/user/login
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
    	 * Android前端发布系统_退出登陆
    	 * get /Android/home/user/logout
    	 * @return [type] [description]
    	 */
    	public function logout()
    	{
    		D('UserLogin')->logout(session('androidIsLogin'));
    		session('androidIsLogin',null);
    		result();
    	}

    	/**
    	 * Android前端发布系统_添加用户
    	 * post /Android/home/user/addUser
    	 * {"user":"admin","passwd":"admin"}
    	 * @return [type] [description]
    	 */
    	public function addUser()
    	{
    		$put = I('put.');
    		D('User')->addUser($put);
    		result();
    	}
    	/**
    	 * Android前端发布系统_修改用户呢称
    	 * post /Android/home/user/addUserName
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
    	 * Android前端发布系统_删除用户
    	 * post /Android/home/user/deleteUser
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
    	 * Android前端发布系统_当前用户信息
    	 * get /Android/home/user/me
    	 *
    	 * @return [type] [description]
    	 */
    	public function me()
    	{
    		$res = D('User')->me();
    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_用户列表
    	 * get /Android/home/user/userLists?id=x
    	 * get /Android/home/user/userLists?page=x&pageSize=x
    	 * get /Android/home/user/userLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function userLists()
    	{
    		$get = I('get.');
    		$res = D('User')->userLists($get);
    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_发布用户列表
    	 * get /Android/home/user/publisherLists?id=x
    	 * get /Android/home/user/publisherLists?page=x&pageSize=x
    	 * get /Android/home/user/publisherLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function publisherLists()
    	{
    		$get = I('get.');
    		$res = D('User')->publisherLists($get);
    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_发布测试用户列表
    	 * get /Android/home/user/testerLists?id=x
    	 * get /Android/home/user/testerLists?page=x&pageSize=x
    	 * get /Android/home/user/testerLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function testerLists()
    	{
    		$get = I('get.');
    		$res = D('User')->testerLists($get);
    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_修改密码
    	 * post /Android/home/user/modifyPasswd
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
    	 * Android前端发布系统_修改权限
    	 * post /Android/home/user/modifyAuth
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
    	public function modifyAuth()
    	{
    		$put = I('put.');
    		D('User')->modifyAuth($put);
    		result();
    	}
    	/**
    	 * Android前端发布系统_添加用户模块权限
    	 * post /Android/home/user/addModuleAdmin
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
    	 * Android前端发布系统_修改用户模块权限
    	 * post /Android/home/user/modifyModuleAdmin
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
    	 * Android前端发布系统_删除用户模块权限
    	 * post /Android/home/user/deleteModuleAdmin
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
    	 * Android前端发布系统_用户模块权限列表
    	 * get  /Android/home/user/moduleAdminLists?id=x
    	 * get  /Android/home/user/moduleAdminLists?page=x&pageSize=x
    	 * get  /Android/home/user/moduleAdminLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function moduleAdminLists()
    	{
    		$get = I('get.');
    		$res = D('ModuleAdmin')->moduleAdminLists($get);
    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_当前用户模块权限列表
    	 * get  /Android/home/user/currentModuleAdminLists?id=x
    	 * get  /Android/home/user/currentModuleAdminLists?page=x&pageSize=x
    	 * get  /Android/home/user/currentModuleAdminLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function currentModuleAdminLists()
    	{
    		$get = I('get.');
    		$res = D('ModuleAdmin')->currentModuleAdminLists($get);
    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_添加用户应用权限
    	 * post /Android/home/user/addAppAdmin
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
    	 * Android前端发布系统_修改用户应用权限
    	 * post  /Android/home/user/modifyAppAdmin
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
    	 * Android前端发布系统_删除用户应用权限
    	 * get /Android/home/user/deleteAppAdmin?id=x
    	 * @return [type] [description]
    	 */
    	public function deleteAppAdmin()
    	{
    		$put = I('put.');
    		D('AppAdmin')->deleteAppAdmin($put);
    		result();
    	}
    	/**
    	 * Android前端发布系统_用户应用权限列表
    	 * get /Android/home/user/appAdminLists?id=x
    	 * get /Android/home/user/appAdminLists?page=x&pageSize=x
    	 * get /Android/home/user/appAdminLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function appAdminLists()
    	{
    		$get = I('get.');
    		$res = D('AppAdmin')->appAdminLists($get);
    		result(true,$res);
    	}

    	/**
    	 * Android前端发布系统_当前用户应用权限列表
    	 * get /Android/home/user/currentAppAdminLists?page=x&pageSize=x
    	 * get /Android/home/user/currentAppAdminLists?page=x&pageSize=x&name=x
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
    	 * Android前端发布系统_添加应用
    	 * post /Android/home/user/addApp
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
    	 * Android前端发布系统_修改应用
    	 * post  /Android/home/user/modifyApp
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
    	 * Android前端发布系统_删除应用
    	 * post /Android/home/user/deleteApp
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
    	 * Android前端发布系统_应用列表
    	 * get /Android/home/user/appLists?id=x
    	 * get /Android/home/user/appLists?page=x&pageSize=x
    	 * get /Android/home/user/appLists?page=x&pageSize=x&name=x
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
    	 * Android前端发布系统_添加应用
    	 * post /Android/home/user/addModule
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
    	 * Android前端发布系统_修改应用
    	 * post  /Android/home/user/modifyModule
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
    	 * Android前端发布系统_删除应用
    	 * post /Android/home/user/deleteModule
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
    	 * Android前端发布系统_应用列表
    	 * get /Android/home/user/ModuleLists?id=x
    	 * get /Android/home/user/ModuleLists?page=x&pageSize=x
    	 * get /Android/home/user/ModuleLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function ModuleLists()
    	{
    		$get = I('get.');
    		$res = D('Module')->moduleLists($get);
    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_登陆列表
    	 * get /Android/home/user/userLoginLists?id=x
    	 * get /Android/home/user/userLoginLists?page=x&pageSize=x
    	 * get /Android/home/user/userLoginLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function userLoginLists()
    	{
    		$get = I('get.');
    		$res = D('UserLogin')->userLoginLists($get);
    		result(true,$res);
    	}
    	//--------------------------------------------------------------------------------------------------

}