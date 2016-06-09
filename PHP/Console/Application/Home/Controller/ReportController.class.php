<?php
namespace Home\Controller;
use Think\Controller;

class ReportController extends Controller {

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
       	* http://192.168.1.199:180/Report/getUsersReport
       	* {
       	* 	"id": "22",
       	* 	"model": "VSOON_3128",
       	* 	"sn": "1074834554",
       	* 	"mac": "10:00:00:00:0b:45",
       	* 	"version": "3.3.77",
       	* 	"event": "换台慢",
       	* 	"channel": "宁夏卫视",
       	* 	"json": "07c5c0aaa237b80b58e369d6c2bffc76",
       	* 	"time": "2016-02-02 15:04:47",
       	* 	"p2pId": "58084879",
       	* 	"g3Desc": "中国-广东省-广州市-电信",
       	* 	"g3Region": "CN.19.246.1"
       	* }
         	* @return [type] [description]
         	*/
    	public function getUsersReport()
    	{
    		$put = I('put.');
    		$get = I('get.');
    		$res = D('LiveReport','Report')->getUsersReport($put,$get);
    		result(true,$res);
    	}
    	/**
       	* 获取接口
         	* @return [type] [description]
         	*/
    	public function getInterface()
    	{
    		$res = D('InterfaceHistory','Monitoring')->getInterface();
    		result(true,$res);
    	}


}