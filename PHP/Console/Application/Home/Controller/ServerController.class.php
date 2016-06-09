<?php
	namespace Home\Controller;
	use Think\Controller;
	class ServerController extends Controller {
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
		 * 服务参数管理_添加服务参数
		 */
		public function addServerConf()
		{
			$put = I('put.');
			if (!empty($put['name'])&&!empty($put['value'])) {
				D('ServerConfig','Live')->addServerConf($put['name'],$put['value']);
			}else{
				result('param');
			}
		}
		/**
		 * 服务参数管理_删除服务参数
		 */
		public function deleteServerConf()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('ServerConfig','Live')->deleteServerConf($id);
			}else{
				result('param');
			}
		}
		/**
		 * 服务参数管理_服务参数列表
		 */
		public function serverConfLists()
		{
			D('ServerConfig','Live')->serverConfLists($id);

		}
		/**
		 * 服务参数管理_修改服务参数
		 */
		public function modifyServerConf()
		{
			$put = I('put.');
			if (!empty($put['name'])&&!empty($put['value'])&&!empty($put['id'])) {
				D('ServerConfig','Live')->modifyServerConf($put['id'],$put['name'],$put['value']);
			}else{
				result('param');
			}

		}
	}
