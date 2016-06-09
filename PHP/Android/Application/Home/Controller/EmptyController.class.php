<?php
namespace Home\Controller;
use Think\Controller;

class EmptyController extends Controller {

    	/**
     	* 访问不存在的地址
     	*
     	*/

    	public function index(){        //根据当前控制器名来判断要执行那个城市的操作
	   	$cityName = CONTROLLER_NAME;

	    	$this->city($cityName);
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