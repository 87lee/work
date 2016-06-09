<?php
namespace Home\Controller;
use Think\Controller;

class DomainController extends Controller {

    	/**
     	* 访问不存在的地址
     	*
     	*/
     	public function __construct()
    	{
	        	parent::__construct();
	       	isLogin();
    	}
    	public function index(){        //根据当前控制器名来判断要执行那个城市的操作

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
    	 * 下载监控_域名管理_添加域名
    	 * post /Monitoring/home/Domain/addDomain
    	 * {
    	 * 	"name":"name",
    	 * 	"url":"url",
    	 * 	"desc":""
    	 * }
    	 */
    	public function addDomain()
    	{
    		$put = I('put.');
    		D('Domain','Monitoring')->addDomain($put);
    		result();
    	}
    	/**
    	 * 接口监控_域名管理_修改域名
    	 *post /Monitoring/home/Domain/modifyDomain
    	 * {
    	 * 	"id":"1",
    	 * 	"name":"name",
    	 * 	"url":"url",
    	 * 	"desc":""
    	 * }
    	 */
    	public function modifyDomain()
    	{
    		$put = I('put.');
    		D('Domain','Monitoring')->modifyDomain($put);
    		result();
    	}
    	/**
    	 * 接口监控_域名管理_删除域名
    	 *post /Monitoring/Home/Domain/deleteDomain
    	 *["1","2"]
    	 *
    	 */
    	public function deleteDomain()
    	{
    		$put = I('put.');
    		D('Domain','Monitoring')->deleteDomain($put);
    		result();
    	}
    	/**
    	 * 接口监控_域名管理_域名列表
    	 *get /Monitoring/Home/Domain/domainLists?name=x&page=x&pageSize=x
    	 *
    	 */
    	public function domainLists()
    	{
    		$get = I('get.');
    		$res = D('Domain','Monitoring')->domainLists($get);
    		result(true,$res);
    	}
}