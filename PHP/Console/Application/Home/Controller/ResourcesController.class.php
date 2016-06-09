<?php
namespace Home\Controller;
	use Think\Controller;
	class ResourcesController extends Controller {
		public function __construct()
	    	{
		        	parent::__construct();
		       	isLogin();

	    	}
		public function index()
		{

		}
		/**
	         	* 访问不存在的地址
	         	* @param  [type] $name [description]
	         	* @return [type]       [description]
	         	*/
	    	public function _empty($name){        //把所有城市的操作解析到city方法
	    		echo '{"result":"fail","reason":"当前地址不存在"}';
	    	}
		/**
		 * 资源管理_添加组
		 * post /resources/addGroup
		 * post_data{"group":"xxxx"}
		 */
		public function addGroup()
		{
			$put = I('put.');
			D('OssFileGroup','Resources')->addGroup($put);
			result();

		}
		/**
		 * 资源管理_删除图片组
		 * get /resources/deleteGroup?id=x
		 *
		 */
		public function deleteGroup()
		{
			$get = I('get.');

			D('OssFileGroup','Resources')->deleteGroup($get);

			result();

		}
		/**
		 * 资源管理_图片组列表
		 * get /resources/groupLists?page=x&pageSize=x&name=x
		 *
		 */
		public function groupLists()
		{
			$get = I('get.');
			$res = D('OssFileGroup','Resources')->groupLists($get);
			result(true,$res);
		}

		/**
		 * 资源管理_添加文件
		 * post /resources/uploadFile
		 * 表单提交 上传图片 类型为file   
		 * name="groupId";value=组ID
		 * name="name";value=文件名称
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function uploadFile()
		{
			$post = I('post.');
			D('OssFile','Resources')->uploadFile($post);
			result();
		}
		/**
		 * 资源管理_删除文件
		 * get /resources/deleteFile?id=1
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteFile()
		{
			$get = I('get.');
			D('OssFile','Resources')->deleteFile($get);
			result();

		}
		/**
		 * 资源管理_文件列表
		 * get /resources/fileLists?groupId=1&page=xxx&pageSize=xxx
		 * get /resources/fileLists?page=xxx&pageSize=xxx
		 * get /resources/fileLists?name=xxxx&page=xxx&pageSize=xxx
		 * return {"result":"ok","extra":[{"id":1,"name":"xxxx","download":"xx"}]}
		 */
		public function fileLists()
		{
			$get = I('get.');
			$res = D('OssFile','Resources')->fileLists($get);
			result(true,$res);
		}
	}
 ?>