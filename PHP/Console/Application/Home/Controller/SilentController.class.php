<?php
namespace Home\Controller;
use Think\Controller;

class SilentController extends Controller {

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
	 * 静默安装_添加静默安装组
	 * post   /Silent/addAppGroup
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function addAppGroup()
    	{
    		$put = I('put.');
    		$newGroupId = D('AppGroup','Silent')->addAppGroup($put);
    		if (!empty($put['groupId']) && !empty($newGroupId)) {
    			D('AppGroupItems','Silent')->addAppGroupItemArr($put['groupId'],$newGroupId);
    		}
    		result();
    	}

    	/**
	 * 静默安装_修改静默安装组
	 * post   /Silent/addAppGroup
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function modifyAppGroup()
    	{
    		$put = I('put.');
    		D('AppGroup','Silent')->modifyAppGroup($put);
    		result();
    	}
    	/**
	 * 静默安装_删除静默安装组
	 * get   /Silent/deleteAppGroup?id=x
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function deleteAppGroup()
    	{
    		$get = I('get.');
    		if (!empty($get['id'])) {
    			D('AppGroup','Silent')->deleteAppGroup($get['id']);
    		}
    		result();
    	}
    	/**
	 * 静默安装_静默安装组列表
	 * get   /Silent/appGroupLists
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function appGroupLists()
    	{
    		$get= I('get.');
		$res = D('AppGroup','Silent')->appGroupLists($get);

    		result(true,$res);
    	}

    	/**
	 * 静默安装_添加静默安装组成员
	 * post   /Silent/addAppGroupItem
	 * {
	 * 	"groupId":"静默安装组ID",
	 * 	"pkgName": "com.linkin.more.service",//包名 在应用管理--第三方应用获取
	 * 	"weight": 60,//权重 填写
	 * 	"versionName": "2.1.3",//版本名称 在应用管理--第三方应用获取
	 * 	"versionCode": "20103",//版本号 在应用管理--第三方应用获取
	 * 	"action": "active/remove/install",//行为 （active 激活/remove  卸载/install 安装） 选填
	 * 	"download": "http:\/\/192.168.1.199:11081\/\/copy\/apk\/com.linkin.more.service\/none\/20103\/\/.apk.apk",//下载地址 在应用管理--第三方应用获取
	 * 	"appName": "测试静默" //下载地址 在应用管理--第三方应用获取
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function addAppGroupItem()
    	{
    		$put = I('put.');
    		D('AppGroupItems','Silent')->addAppGroupItem($put);
    		result();
    	}
    	/**
	 * 静默安装_修改静默安装组成员
	 * post   /Silent/modifyAppGroupItem
	 * {
	 * 	"id":"静默安装组成员ID",
	 * 	"pkgName": "com.linkin.more.service",//包名 在应用管理--第三方应用获取
	 * 	"weight": 60,//权重 填写
	 * 	"versionName": "2.1.3",//版本名称 在应用管理--第三方应用获取
	 * 	"versionCode": "20103",//版本号 在应用管理--第三方应用获取
	 * 	"action": "active/remove/install",//行为 （active 激活/remove  卸载/install 安装） 选填
	 * 	"download": "http:\/\/192.168.1.199:11081\/\/copy\/apk\/com.linkin.more.service\/none\/20103\/\/.apk.apk",//下载地址 在应用管理--第三方应用获取
	 * 	"appName": "测试静默" //下载地址 在应用管理--第三方应用获取
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function modifyAppGroupItem()
    	{
    		$put = I('put.');
    		D('AppGroupItems','Silent')->modifyAppGroupItem($put);
    		result();
    	}
    	/**
	 * 静默安装_删除静默安装组成员
	 * get   /Silent/deleteAppItemGroup?id=x
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function deleteAppItemGroup()
    	{
    		$get = I('get.');
    		if (!empty($get['id'])) {
    			D('AppGroupItems','Silent')->deleteAppItemGroup($get['id']);
    		}
    		result();
    	}
    	/**
	 * 静默安装_静默安装组成员列表
	 * get   /Silent/appGroupItemLists
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function appGroupItemLists()
    	{
    		$get= I('get.');
		$res = D('AppGroupItems','Silent')->appGroupItemLists($get);

    		result(true,$res);
    	}

    	/**
	 * 静默安装_添加静默黑名单
	 * get   /Silent/addBlackList
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function addBlackList()
    	{
    		$put= I('put.');
		D('SilentBlackList','Silent')->addBlackList($put);

    		result();
    	}
    	/**
	 * 静默安装_修改静默黑名单
	 * get   /Silent/modifyBlackList
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function modifyBlackList()
    	{
    		$put= I('put.');
		D('SilentBlackList','Silent')->modifyBlackList($put);

    		result();
    	}
    	/**
	 * 静默安装_删除静默黑名单
	 * get   /Silent/deleteBlackList
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function deleteBlackList()
    	{
    		$get= I('get.');
    		if (!empty($get['id'])) {
    			D('SilentBlackList','Silent')->deleteBlackList($get['id']);
    		}
    		result();
    	}
    	/**
	 * 静默安装_静默黑名单列表
	 * get   /Silent/blackListLists
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function blackListLists()
    	{
    		$get= I('get.');
		$res = D('SilentBlackList','Silent')->blackListLists($get);

    		result(true,$res);
    	}
    	/**
	 * 静默安装_发布静默安装
	 * get   /Silent/publishSilent
	 * {
	 * 	"silentGroupId":"静默安装组ID",
	 * 	"model": "ALL",//填写  有全厂商概念  ALL
	 * 	"vendorID": "none",//填写  有默认 none
	 * 	"type": "ALL/AB/group",//选填
	 * 	"groupId": "内测组ID",//选填 当type =group
	 * 	"ab": "灰度值",//填写 当type =AB
	 * 	"startActive": "05:00:00",//填写 激活开始时间
	 * 	"endActive": "05:00:00" //填写 激活结束时间
	 * 	"idle": "05:00:00",//填写 空闲激活时间
	 * 	"duration": "05:00:00",//填写 持续激活时间
	 * 	"pubRange": "jbk/unjbk/all",//发布范围：jbk 越狱  /unjbk非越狱/all 全部    选填
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function publishSilent()
    	{
    		$put= I('put.');
		D('PublishSilent','Silent')->publishSilent($put);
    		result();
    	}
    	/**
	 * 静默安装_修改发布静默安装
	 * post   /Silent/modifyPublishSilent
	 * {
	 * 	"id":"发布静默安装组ID",
	 * 	"type": "ALL/AB/group",//选填
	 * 	"groupId": "内测组ID",//选填 当type =group
	 * 	"ab": "灰度值",//填写 当type =AB
	 * 	"pubRange": "jbk/unjbk/all",//发布范围：jbk 越狱  /unjbk非越狱/all 全部    选填
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function modifyPublishSilent()
    	{
    		$put= I('put.');
		D('PublishSilent','Silent')->modifyPublishSilent($put);
    		result();
    	}
    	/**
	 * 静默安装_删除发布静默安装
	 * get   /Silent/deletePublishSilent
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function deletePublishSilent()
    	{
    		$get= I('get.');
    		if (!empty($get['id'])) {
    			D('PublishSilent','Silent')->deletePublishSilent($get['id']);
    		}
    		result();
    	}
    	/**
	 * 静默安装_发布静默安装列表
	 * get   /Silent/blackListLists
	 * {
	 * 	"name":"test"
	 * }
	 * @return {"result":"ok/fail","reason":"xxx"}
	 */
    	public function publishSilentLists()
    	{
    		$get= I('get.');
		$res = D('PublishSilent','Silent')->publishSilentLists($get);
    		result(true,$res);
    	}

}