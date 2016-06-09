<?php
namespace Home\Controller;
use Think\Controller;
use Think\Exception;


class IndexController extends Controller {

    	/**
     	* 首页 安全页
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
}