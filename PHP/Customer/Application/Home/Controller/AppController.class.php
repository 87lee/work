<?php
namespace Home\Controller;
use Think\Controller;
class AppController extends Controller {
	public function index(){

    	}
    	public function __construct()
    	{
	        	parent::__construct();
	       	isLogin();

    	}
    	public function _empty(){
    		result('请填写正确地址');
    	}
    	/**
    	 * Android前端发布系统_APP_发布APP模块
    	 * post /Android/Home/App/publishApp
    	 *表单上传 type=file name = apkFile   上传文件为.apk
    	 * type=test,value = {"versionDesc":"版本备注"},name=extra
    	 * @return [type] [description]
    	 */
    	public function publishApp()
    	{
    		$put = I('post.extra');
    		if (!empty($put)) {
    			$put = json_decode($put,true);
    		}
    		if ($_FILES['apkFile']) {
			D('AppPublish')->publishApp($put);
			result();
		}else{
			result('没有上传apk文件');
		}
    	}


    	/**
    	 * Android前端发布系统_APP_修改用户APP权限
    	 * post /Android/Home/App/modifyAppAdmin
    	 * {
    	 * 	"id":"用户模块权限ID"
    	 * 	"name":"模块名"
    	 *  	"operator":"用户名",
    	 *  	"note":"备注"
    	 *
    	 *  }
    	 * @return [type] [description]
    	 */
    	/*public function modifyAppAdmin()
    	{
    		$put = I('put.');
    		D('AppAdmin')->modifyAppAdmin($put);
    		result();
    	}*/
    	/**
    	 * Android前端发布系统_APP_删除用户APP
    	 * post /Android/Home/App/deletePublishApp
    	 * ["1","2"]
    	 * @return [type] [description]
    	 */
    	public function deletePublishApp()
    	{
    		$put = I('put.');

		D('AppPublish')->deletePublishApp($put);

    		result();
    	}
    	/**
    	 * Android前端发布系统_APP_APP模块列表
    	 * get /Android/Home/App/publishAppLists?id=x
    	 * get /Android/Home/App/publishAppLists?page=x&pageSize=x
    	 * get /Android/Home/App/publishAppLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function publishAppLists()
    	{
    		$get = I('get.');
    		$res = D('AppPublish')->publishAppLists($get);
    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_APP_APP依赖跳转
    	 * get /Android/Home/Base/relyAppLists?module=依赖的模块名&versionName=依赖的模块版本名&pkg_name=依赖的模块包名
    	 * @return [type] [description]
    	 */
    	public function relyAppLists()
    	{
    		$get = I('get.');
    		$res = D('AppPublish')->relyAppLists($get);

    		result(true,$res);
    	}

    	/**
    	 * Android前端发布系统_APP_APP模块修改状态
    	 * post /Android/Home/App/mofidyPassTest
    	 * {
    	 * 	'id':"发布APPID",
    	 * 	"passTest":"false/true"
    	 * }
    	 * @return [type] [description]
    	 */
    	public function mofidyPassTest()
    	{
    		$put = I('put.');
    		D('AppPublish')->mofidyPassTest($put);
    		result();
    	}
    	/**
    	 * Android前端发布系统_APP_APP模块添加评论
    	 * post /Android/Home/App/addAppComment
    	 *
    	 *{
    	 *	"appId":"发布模块的ID",
    	 *	"content":"评论内容"
    	 *}
    	 *
    	 * @return [type] [description]
    	 */
    	public function addAppComment()
    	{
    		$put = I('put.','','htmlspecialchars');
    		D('AppComment')->addAppComment($put);
    		result();
    	}
    	/**
    	 * Android前端发布系统_APP_用户APP删除评论
    	 * get /Android/Home/App/deleteAppComment?appId=x
    	 * @return [type] [description]
    	 */
    	public function deleteAppComment()
    	{
    		$get = I('get.');
    		D('AppComment')->deleteAppComment($get);
    		result();
    	}
    	/**
    	 * Android前端发布系统_APP_用户APP评论列表
    	 * get /Android/Home/App/AppCommentLists?appId=发布id
    	 * get /Android/Home/App/AppCommentLists?appId=发布id&page=x&pageSize=x
    	 * @return [type] [description]
    	 */
    	public function appCommentLists()
    	{
    		$get = I('get.');
    		$res = D('AppComment')->appCommentLists($get);
    		result(true,$res);
    	}
}