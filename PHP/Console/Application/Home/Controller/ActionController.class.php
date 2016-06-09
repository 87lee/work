<?php
namespace Home\Controller;
use Think\Controller;

class ActionController extends Controller {

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
    	public function userActionLists()
    	{
    		$get = I('get.');
    		$res = D('UserAction','Action')->userActionLists($get);
    		result(true,$res);
    	}
}