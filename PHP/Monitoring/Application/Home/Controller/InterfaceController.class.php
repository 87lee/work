<?php
namespace Home\Controller;
use Think\Controller;

class InterfaceController extends Controller {

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
    	 * 接口监控_接口管理_添加接口组
    	 * post /Monitoring/home/Interface/addGroup
    	 * {
    	 * 	"name":"name",
    	 * 	"desc":""
    	 * }
    	 */
    	public function addGroup()
    	{
    		$put = I('put.');
    		D('InterfaceGroup','Monitoring')->addGroup($put);
    		result();
    	}
    	/**
    	 * 接口监控_接口管理_修改接口组
    	 *post /Monitoring/home/Interface/modifyGroup
    	 * {
    	 * 	"id":"1",
    	 * 	"name":"name",
    	 * 	"desc":""
    	 * }
    	 */
    	public function modifyGroup()
    	{
    		$put = I('put.');
    		D('InterfaceGroup','Monitoring')->modifyGroup($put);
    		result();
    	}
    	/**
    	 * 接口监控_接口管理_删除接口组
    	 *get /Monitoring/Home/Interface/deleteGroup?id=x
    	 *
    	 */
    	public function deleteGroup()
    	{
    		$get = I('get.');
    		D('InterfaceGroup','Monitoring')->deleteGroup($get);
    		result();
    	}
    	/**
    	 * 接口监控_接口管理_接口组列表
    	 *get /Monitoring/Home/Interface/groupLists?name=x&page=x&pageSize=x
    	 *
    	 */
    	public function groupLists()
    	{
    		$get = I('get.');
    		$res = D('InterfaceGroup','Monitoring')->groupLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 接口监控_接口管理_添加接口
    	 * post /Monitoring/home/Interface/addGroupItem
    	 * {
    	 * 	"name":"name",
    	 * 	"groupId":"组ID",
    	 * 	"interface":"接口  /update/live",
    	 * 	"desc":""
    	 * }
    	 */
    	public function addGroupItem()
    	{
    		$put = I('put.');
    		D('InterfaceGroupItem','Monitoring')->addGroupItem($put);
    		result();
    	}
    	/**
    	 * 接口监控_接口管理_修改接口
    	 *post /Monitoring/home/Interface/modifyGroupItem
    	 * {
    	 * 	"id":"1",
    	 * 	"name":"name",
    	 * 	"interface":"接口  /update/live",
    	 * 	"desc":""
    	 * }
    	 */
    	public function modifyGroupItem()
    	{
    		$put = I('put.');
    		D('InterfaceGroupItem','Monitoring')->modifyGroupItem($put);
    		result();
    	}
    	/**
    	 * 接口监控_接口管理_删除接口
    	 *post /Monitoring/Home/Interface/deleteGroupItem
    	 *["1","2"]
    	 */
    	public function deleteGroupItem()
    	{
    		$put = I('put.');
    		D('InterfaceGroupItem','Monitoring')->deleteGroupItem($put);
    		result();
    	}
    	/**
    	 * 接口监控_接口管理_接口组列表
    	 *get /Monitoring/Home/Interface/groupItemLists?name=x&page=x&pageSize=x
    	 *
    	 */
    	public function groupItemLists()
    	{
    		$get = I('get.');
    		$res = D('InterfaceGroupItem','Monitoring')->groupItemLists($get);
    		result(true,$res);
    	}
}