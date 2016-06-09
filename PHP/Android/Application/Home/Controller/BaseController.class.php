<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
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
    	 * Android前端发布系统_基础库_添加用户模块
    	 * post /Android/Home/Base/publishModule
    	 *
    	 *表单上传 type=file name = name   上传文件为.pom
    	 * type=test,value = {"versionDesc":"版本备注"},name=extra
    	 *
    	 *
    	 * @return [type] [description]
    	 */
    	public function publishModule()
    	{
    		$put = I('post.extra');

    		if (!$xmlData = (array)simplexml_load_file($_FILES['name']['tmp_name'])) {
			result('请上传pom文件');
		}
		if (empty($put)) {
			$put = '';
		}
		D('ModulePublish')->publishModule($xmlData,$put);
		result();
    	}


    	/**
    	 * Android前端发布系统_基础库_修改用户模块权限
    	 * post /Android/Home/Base/modifyModuleAdmin
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
    	 * Android前端发布系统_基础库_删除用户模块
    	 * post  /Android/Home/Base/deletePublishModule
    	 * ["1","2"]
    	 * @return [type] [description]
    	 */
    	public function deletePublishModule()
    	{
    		$put = I('put.');
		D('ModulePublish')->deletePublishModule($put);
    		result();
    	}
    	/**
    	 * Android前端发布系统_基础库_用户模块列表
    	 * get /Android/Home/Base/publishModuleLists?id=x
    	 * get /Android/Home/Base/publishModuleLists?page=x&pageSize=x
    	 * get /Android/Home/Base/publishModuleLists?page=x&pageSize=x&name=x
    	 * @return [type] [description]
    	 */
    	public function publishModuleLists()
    	{
    		$get = I('get.');
    		$res = D('ModulePublish')->publishModuleLists($get);

    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_基础库_用户依赖模块跳转
    	 * get /Android/Home/Base/relyModuleLists?module=依赖的模块名&versionName=依赖的模块版本名&pkg_name=依赖的模块包名
    	 * @return [type] [description]
    	 */
    	public function relyModuleLists()
    	{
    		$get = I('get.');
    		$res = D('ModulePublish')->relyModuleLists($get);

    		result(true,$res);
    	}
    	/**
    	 * Android前端发布系统_基础库_用户模块添加评论
    	 * post /Android/Home/Base/addModuleComment
    	 *
    	 *{
    	 *	"id":"发布模块的ID",
    	 *	"content":"评论内容"
    	 *}
    	 *
    	 * @return [type] [description]
    	 */
    	public function addModuleComment()
    	{
    		$put = I('put.','','htmlspecialchars');
    		D('ModuleComment')->addModuleComment($put);
    		result();
    	}
    	/**
    	 * Android前端发布系统_基础库_用户模块删除评论
    	 * get /Android/Home/Base/deleteModuleComment?id=x&time=时间戳
    	 * @return [type] [description]
    	 */
    	public function deleteModuleComment()
    	{
    		$get = I('get.');
    		D('ModuleComment')->deleteModuleComment($get);
    		result();
    	}
    	/**
    	 * Android前端发布系统_基础库_用户模块评论列表
    	 * get /Android/Home/Base/moduleCommentLists?id=发布id
    	 * get /Android/Home/Base/moduleCommentLists?id=发布id&page=x&pageSize=x
    	 * @return [type] [description]
    	 */
    	public function moduleCommentLists()
    	{
    		$get = I('get.');
    		$res = D('ModuleComment')->moduleCommentLists($get);
    		result(true,$res);
    	}
}