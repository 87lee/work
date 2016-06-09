<?php
namespace Home\Controller;
use Think\Controller;

class MonitoringController extends Controller {

 	public function __construct()
    	{
	        	parent::__construct();
	       	isLogin();

    	}

    	/**
     	* 首页安全页
     	*
     	*/

    	public function index(){
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
       	* 操作历史记录列表
         	* @return [type] [description]
         	*/
    	/*public function getAdmin()
    	{
    		$get = I('get.');
    		$res = D('InterfaceHistory','Monitoring')->getCodeTypeHistory($get);
    		result(true,$res);
    	}*/
    	/**
       	* 获取接口
         	* @return [type] [description]
         	*/
    	/*public function getInterface()
    	{
    		$res = D('InterfaceHistory','Monitoring')->getInterface();
    		result(true,$res);
    	}*/
}