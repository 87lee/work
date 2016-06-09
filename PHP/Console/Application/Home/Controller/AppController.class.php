<?php
	namespace Home\Controller;
	use Think\Controller;
 	class AppController extends Controller {
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
		 * 应用升级_新增应用
		 * post  /App/addAppUpdate
		 *
		 *  {
		 * "appName":"应用名称----填写",
		 * "pkgName":"包名----填写",
		 * "channel":"渠道----填写",
		 * "desc":"描述----填写",
		 * }
		 *
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addAppUpdate()
		{
			$put = I('put.');
			D('AppUpdate','App')->addAppUpdate($put);
			result();
		}
		/**
		 * 应用升级_删除应用
		 *
		 * get /App/deleteAppUpdate?id=1
		 *
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteAppUpdate()
		{
			$get = I('get.');
			D('AppUpdate','App')->deleteAppUpdate($get['id']);
			result();
		}
		/**
		 * 应用升级_修改应用
		 *
		 * post /App/modifyAppUpdate
		 *
		 * post {
		 * "id":"1",
		 * "appName":"应用名称----填写",
		 * "pkgName":"包名----填写",
		 * "channel":"渠道----填写",
		 * "desc":"描述----填写",
		 * }
		 * return {"result":"ok/fail","reason":"xxx"}
		 *
		 * @param string $value [description]
		 */
		public function modifyAppUpdate()
		{
			$put = I('put.');
			D('AppUpdate','App')->modifyAppUpdate($put);
			result();
		}
		/**
		 * 应用升级_应用列表
		 *
		 * get /App/appUpdateLists?page=xxx&pageSize=xxx
		 * get /App/appUpdateLists?page=xxx&pageSize=xxx&name=xxx
		 * @return [type] [description]
		 */
		public function appUpdateLists()
		{
			$get = I('get.');
			$res = D('AppUpdate','App')->appUpdateLists($get);
			result(true,$res);
		}
		/**
		 * 应用升级_新增应用版本
		 *
		 * post  /App/addAppUpdateVersion
		 *
		 * name="appFile",上传APK文件
		 * name="extraData" "value"='{"desc":"描述----填写","appId":"应用ID"}'
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addAppUpdateVersion()
		{
			set_time_limit(0);
			$put = I('post.extra');
			if (!empty($put)) {
				$put = json_decode($put,true);
				if (empty($put['appId'])){
					result('附加数据出错');
				}
			}else{
				result('附加数据出错');
			}
			D('AppUpdateVersions','App')->addAppUpdateVersion($put);
			result();
		}

		/**
		 * 应用升级_删除应用版本
		 *
		 * get /App/deleteAppUpdateVersion
		 *
		 * ["1","2"]
		 *
		 * return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteAppUpdateVersion()
		{
			$put = I('put.');
			$res = D('AppUpdateVersions','App')->deleteAppUpdateVersion($put);
			result();
		}
		/**
		 * 应用升级_发布应用版本
		 * @return [type] [description]
		 */
		public function publishAppUpdateVersion()
		{
			set_time_limit(0);
			$put = I('put.');
			$res = D('AppUpdatePublish','App')->publishAppUpdateVersion($put);
			result();
		}
		/**
		 * 应用升级_修改应用升级版本
		 * @return [type] [description]
		 */
		public function modifyPublishAppUpdateVersion()
		{
			$put = I('put.');
			$res = D('AppUpdatePublish','App')->modifyPublishAppUpdateVersion($put);
			result();
		}
		/**
		 * 应用升级_删除应用升级版本
		 * @return [type] [description]
		 */
		public function deletePublishAppUpdateVersion()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				$res = D('AppUpdatePublish','App')->deletePublishAppUpdateVersion($get['id']);
			}
			result();
		}
		/**
		 * 应用升级_应用升级版本列表
		 * @return [type] [description]
		 */
		public function publishAppUpdateVersionLists()
		{
			$get = I('get.');
			$res = D('AppUpdatePublish','App')->publishAppUpdateVersionLists($get);
			result(true,$res);
		}
		/**
		 * 应用升级_应用升级版本操作列表
		 * @return [type] [description]
		 */
		public function appUpdateHistoryLists()
		{	$get = I('get.');
			$res = D('AppUpdatePublishHistory','App')->appUpdateHistoryLists($get);
			result(true,$res);
		}
		//-------------------------------------------------------------------------------------------------------------------------------------------------------------
		/**
		 * 库升级_新增库
		 * post  /App/addLibraryUpdate
		 *
		 *  {
		 * "libraryName":"库名称----填写",
		 * "pkgName":"包名----填写",
		 * "desc":"描述----填写",
		 * }
		 *
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addLibraryUpdate()
		{
			$put = I('put.');
			D('LibraryUpdate','App')->addLibraryUpdate($put);
			result();
		}
		/**
		 * 库升级_删除库
		 *
		 * get /App/deleteLibraryUpdate?id=1
		 *
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteLibraryUpdate()
		{
			$get = I('get.');
			D('LibraryUpdate','App')->deleteLibraryUpdate($get);
			result();
		}
		/**
		 * 库升级_修改库
		 *
		 * post /App/modifyLibraryUpdate
		 *
		 * post {
		 * "id":"1",
		 * "libraryName":"库名称----填写",
		 * "pkgName":"包名----填写",
		 * "desc":"描述----填写",
		 * }
		 * return {"result":"ok/fail","reason":"xxx"}
		 *
		 * @param string $value [description]
		 */
		public function modifyLibraryUpdate()
		{
			$put = I('put.');
			D('LibraryUpdate','App')->modifyLibraryUpdate($put);
			result();
		}
		/**
		 * 库升级_库列表
		 *
		 * get /App/libraryUpdateLists?page=xxx&pageSize=xxx
		 * get /App/libraryUpdateLists?page=xxx&pageSize=xxx&name=xxx
		 * @return [type] [description]
		 */
		public function libraryUpdateLists()
		{
			$get = I('get.');
			$res = D('LibraryUpdate','App')->libraryUpdateLists($get);
			result(true,$res);
		}
		/**
		 * 库升级_新增应用版本
		 *
		 * post  /App/addAppUpdateVersion
		 *
		 * name="appFile",上传APK文件
		 * name="extraData" "value"='{"desc":"描述----填写","appId":"应用ID"}'
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addLibraryUpdateVersion()
		{
			set_time_limit(0);
			$put = I('post.extra');
			if (!empty($put)) {
				$put = json_decode($put,true);
				if (empty($put['appId'])){
					result('附加数据出错');
				}
			}else{
				result('附加数据出错');
			}
			D('AppUpdateVersions','App')->addAppUpdateVersion($put);
			result();
		}

		/**
		 * 库升级_删除应用版本
		 *
		 * get /App/deleteAppUpdateVersion
		 *
		 * ["1","2"]
		 *
		 * return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteLibraryUpdateVersion()
		{
			$put = I('put.');
			$res = D('AppUpdateVersions','App')->deleteAppUpdateVersion($put);
			result();
		}
		/**
		 * 库升级_发布应用版本
		 * @return [type] [description]
		 */
		public function publishLibraryUpdateVersion()
		{
			set_time_limit(0);
			$put = I('put.');
			$res = D('AppUpdatePublish','App')->publishAppUpdateVersion($put);
			result();
		}
		/**
		 * 库升级_修改应用升级版本
		 * @return [type] [description]
		 */
		public function modifyPublishLibraryUpdateVersion()
		{
			$put = I('put.');
			$res = D('AppUpdatePublish','App')->modifyPublishAppUpdateVersion($put);
			result();
		}
		/**
		 * 库升级_删除应用升级版本
		 * @return [type] [description]
		 */
		public function deletePublishLibraryUpdateVersion()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				$res = D('AppUpdatePublish','App')->deletePublishAppUpdateVersion($get['id']);
			}
			result();
		}
		/**
		 * 库升级_应用升级版本列表
		 * @return [type] [description]
		 */
		public function publishLibraryUpdateVersionLists()
		{
			$get = I('get.');
			$res = D('AppUpdatePublish','App')->publishAppUpdateVersionLists($get);
			result(true,$res);
		}
		/**
		 * 库升级_应用升级版本操作列表
		 * @return [type] [description]
		 */
		public function libraryUpdateHistoryLists()
		{	$get = I('get.');
			$res = D('AppUpdatePublishHistory','App')->appUpdateHistoryLists($get);
			result(true,$res);
		}


		//-------------------------------------------------------------------------------------------------------------------------------------------------------------


		/**
		 * 应用管理_添加第三方应用
		 * post   /App/addApk
		 * 1、上传文件
		 * name="apkFile",上传APK文件
		 * name="extraData" value ='{"desc":"备注","appName":"应用名称"}'
		 * 2、post {
		 * 	"appName":"应用名称",
		 * 	"pkgName":"包名",
		 * 	"versionCode":"版本号",
		 * 	"path3rd":"下载路径",
		 * 	"desc":"备注"
		 * 	}
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addNewApk()
		{

			$put = I('post.extraData');
			if (!empty($put)) {
				$put = json_decode($put,true);
				if (empty($put['appName'])){
					result('附加数据出错');
				}
			}else{
				result('附加数据出错');
			}
			$options = D('App3rd','App')->addNewApk($put);
			if (!empty($options)) {
				D('App3rdVersions','App')->addNewApk($options);
				$result = D('ActionApp','Desktop')->addActionApp($options,true);
			}else{
				$result = D('ActionApp','Desktop')->addActionApp($put,true);
			}

			if (!empty($result['reason'])) {
				result('添加第三方应用成功，添加跳转信息失败：'.$result['reason']);
			}
			result();
		}

		/**
		 * 应用管理_删除第三方应用
		 *
		 * get /App/deleteApk?id=1
		 * @return [json] {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteApk()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				if (D('App3rd','App')->deleteApk($get['id'])) {
					result();
				}
			}else{
				result('param');
			}
		}

		/**
		 * 应用管理_修改第三方应用
		 * post /App/modifyApk
		 * post {
		 * "id":"1",
		 * "appName":"应用名称",
		 * "path3rd":"下载路径",
		 * "desc":"备注"
		 * }
		 * @return [type] {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyApk()
		{
			$put = I('post.extraData');
			$put = json_decode($put,true);
			if (!empty($put['id'])&&!empty($put['appName'])) {
				D('App3rd','App')->modifyApk($put);
				result();
			}else{
				result('param');
			}
		}

		/**
		 * 应用管理_第三方应用列表
		 * get /App/apkLists?page=xxx&pageSize=xxx
		 * get /App/apkLists?page=xxx&pageSize=xxx&name=xxx
		 * get /App/apkLists?appName=xxx
		 * get /App/apkLists?id=1
		 * get /App/apkLists
		 * @return [type] [description]
		 */
		public function apkLists()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('App3rd','App')->apkLists($get['id']);
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('App3rd','App')->apkLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					D('App3rd','App')->apkLists(null,$get['name']);
				}
			}elseif(!empty($get['appName'])){
				D('App3rd','App')->apkLists(null,null,null,null,$get['appName']);
			}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {

				D('App3rd','App')->apkLists(null,null,$get['page'],$get['pageSize']);
			}else{
				D('App3rd','App')->apkLists();
			}
		}
		/**
		 * 应用管理_第三方应用版本列表
		 * get /App/apkVersionLists?page=xxx&pageSize=xxx
		 * get /App/apkVersionLists?page=xxx&pageSize=xxx&name=xxx
		 * get /App/apkVersionLists?appName=xxx
		 * get /App/apkVersionLists?id=1
		 * get /App/apkVersionLists
		 * @return [type] [description]
		 */
		public function apkVersionLists()
		{
			$get = I('get.');
			$res = D('App3rdVersions','App')->apkLists($get);
			result(true,$res);
		}
		/**
		 * 应用管理_新增第三方应用版本
		 *
		 * post   /App/addApk
		 *
		 * 自定义          {
		 * "appId":"应用ID----填写",
		 * "versionCode":"渠道----填写",
		 * "path3rd":"下载路径----填写",
		 * }
		 *
		 * 上传
		 * name="apkFile",上传APK文件
 		 *          {
		 * "appId":"应用ID----填写",
		 * }
		 * @param string $value [description]
		 */
		public function addApk()
		{
			$put = I('post.extraData');
			if (!empty($put)) {
				$put = json_decode($put,true);
				if (empty($put['appId'])){
					result('附加数据出错');
				}
			}else{
				result('附加数据出错');
			}
			D('App3rdVersions','App')->addApk($put);
			result();
		}
		/**
		 * 应用管理_删除第三方应用版本
		 *
		 * get /App/deleteApkVersion?id=1    (id版本ID)
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteApkVersion()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('App3rdVersions','App')->deleteApkVersion($get['id']);
				result();
			}else{
				result('parm');
			}
		}
		/**
		 * 应用管理_修改第三方应用版本
		 *
		 * post /App/modifyApkVersion
		 * post {
		 * "id":"1",
		 * "path3rd":"第三方路径",
		 * }
		 * return {"result":"ok/fail","reason":"xxx"}
		 * @param string $value [description]
		 */
		public function modifyApkVersion()
		{
			$put = I('put.');
			D('App3rdVersions','App')->modifyApkVersion($put);
			result();
		}
		//----------------------------------------------------------------------------------------------------------------------------------------------------------


		/**
		 * 应用管理_添加应用组
		 * post   /App/addAppGroup
		 *
		 * 2、post {
		 * 	{"name":"组名"};
		 *
		 * 	}
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addAppGroup()
		{
			// $put = I('put.');
			$put = I('put.');
			if (!empty($put['name'])) {
				D('MoreAppGroup','App')->addAppGroup($put['name']);
			}else{
				result('param');
			}
			result();
		}
		/**
		 * 应用管理_删除应用组
		 *
		 * get /App/deleteAppGroup?id=1
		 * @return [json] {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteAppGroup()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('MoreAppGroup','App')->deleteAppGroup($get['id']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 *  应用管理_修改应用组
		 * post /App/modifyAppGroup
		 * post {
		 * 	"id":"1",
		 *  	"name":"应用组名称",
		 *
		 * }
		 * @return [type] {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyAppGroup()
		{
			$put = I('put.');
			if(!empty($put['id']) && !empty($put['name'])  ){
				D('MoreAppGroup','App')->modifyAppGroup($put);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 应用管理_应用组列表
		 * get /App/appGroupLists?page=xxx&pageSize=xxx
		 * get /App/appGroupLists?page=xxx&pageSize=xxx&blacklistId=更多应用黑名单ID
		 * @return [type] [description]
		 */

		public function appGroupLists()
		{
			$get = I('get.');
			$res = D('MoreAppGroup','App')->appGroupLists($get);
			result(true,$res);
		}

		/**
		 * 应用管理_添加应用组成员
		 * post   /App/addBlacklist
		 *
		 * 2、post {
		 * 	"id":"组成员ID",
		 * 	"appName":"应用名"
		 * 	"pkgName":"包名
		 *
		 * 	}
		 * return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addAppGroupMember()
		{
			// $put = I('put.');
			$put = I('put.');
			D('MoreAppGroupItems','App')->addAppGroupMember($put);
			result();
		}
		/**
		 * 应用管理_删除应用组成员
		 *
		 * get /App/deleteAppGroupMember?id=1
		 * @return [json] {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteAppGroupMember()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('MoreAppGroupItems','App')->deleteAppGroupMember($get['id']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 *  应用管理_应用组列表成员
		 * post /App/appGroupMemberLists?groupId=组ID
		 * post {
		 * 	"id":"1",
		 *  	"pkgName":"应用白名单名称",
		 * 	"appName":"应用白名单名称",
		 * }
		 * @return [type] {"result":"ok/fail","reason":"xxx"}
		 */
		public function appGroupMemberLists()
		{
			$get = I('get.');

			$res = D('MoreAppGroupItems','App')->appGroupMemberLists($get);
			result(true,$res);

		}

		/**
		 * 应用管理_修改应用组成员
		 * get /App/modifyAppGroupMember
		 * @return [type] [description]
		 */
		public function modifyAppGroupMember()
		{
			$put = I('put.');
			$res = D('MoreAppGroupItems','App')->modifyAppGroupMember($put);
			result();
		}
		/**
		 * 应用管理_应用黑白名单发布
		 * get /App/modifyAppGroupMember
		 * @return [type] [description]
		 */
		public function publishAppWhiteBlack()
		{
			$put = I('put.');
			$res = D('MoreAppPublishWhiteBlack','App')->publishAppWhiteBlack($put);
			result();
		}
		/**
		 * 应用管理_修改应用黑白名单发布
		 * get /App/modifyAppGroupMember
		 * @return [type] [description]
		 */
		public function modifyAppWhiteBlack()
		{
			$put = I('put.');
			$res = D('MoreAppPublishWhiteBlack','App')->modifyAppWhiteBlack($put);
			result();
		}
		/**
		 * 应用管理_删除应用黑白名单发布
		 * get /App/modifyAppGroupMember
		 * @return [type] [description]
		 */
		public function deleteAppWhiteBlack()
		{
			$get = I('get.');
			$res = D('MoreAppPublishWhiteBlack','App')->deleteAppWhiteBlack($get);
			result();
		}
		/**
		 * 应用管理_应用黑白名单发布列表
		 * get /App/modifyAppGroupMember
		 * @return [type] [description]
		 */
		public function appWhiteBlackLists()
		{
			$get = I('get.');
			$res = D('MoreAppPublishWhiteBlack','App')->appWhiteBlackLists($get);
			result(true,$res);
		}
		/**
		 * 应用_第三应用和应用升级列表
		 *  get /App/app3rdAndAppUpdateLists
		 * @param string $value [description]
		 */
		public function app3rdAndAppUpdateLists()
		{
			$res = D('Join','App')->app3rdAndAppUpdateLists();
			result(true,$res);
		}

		/**
		 * 应用_第三应用和应用升级列表
		 *  get /App/modifyAppGroupMember?name=xx
		 * @param string $value [description]
		 */
		public function app3rdVersionAndAppUpdateVersionLists()
		{
			$get= I('get.');
			$res = D('Join','App')->app3rdVersionAndAppUpdateVersionLists($get);

			result(true,$res);
		}

		/**
		 * 应用管理_第三方链接管理添加
		 * post /App/add3rdLink
		 * {
		 * 	"name":"链接名称",
		 *  	"www":"真实链接",
		 *  	"desc":"链接备注",
		 * }
		 * @return [type] [description]
		 */
		public function add3rdLink()
		{
			$put = I('put.');
			$res = D('Link3rd','App')->add3rdLink($put);
			result();
		}
		/**
		 * 应用管理_修改第三方链接管理
		 * post /App/modify3rdLink
		 * {
		 * 	"id":"第三方链接ID",
		 * 	"name":"链接名称",
		 *  	"www":"真实链接",
		 *  	"desc":"链接备注",
		 * }
		 * @return [type] [description]
		 */
		public function modify3rdLink()
		{
			$put = I('put.');
			$res = D('Link3rd','App')->modify3rdLink($put);
			result();
		}
		/**
		 * 应用管理_删除第三方链接管理
		 * get /App/delete3rdLink?id=xx
		 * @return [type] [description]
		 */
		public function delete3rdLink()
		{
			$get = I('get.');
			$res = D('Link3rd','App')->delete3rdLink($get);
			result();
		}
		/**
		 * 应用管理_第三方链接管理列表
		 * get /App/app3rdLinkLists
		 * @return [type] [description]
		 */
		public function app3rdLinkLists()
		{
			$get = I('get.');
			$res = D('Link3rd','App')->app3rdLinkLists($get);
			result(true,$res);
		}
	}
 ?>