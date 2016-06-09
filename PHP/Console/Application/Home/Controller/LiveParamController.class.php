<?php
	namespace Home\Controller;
	use Think\Controller;

	class LiveParamController extends Controller {
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
		 * 直播参数管理_添加参数分类
		 * POST /liveParam/addType
		 * POST_DATA: {"type":"xxx"}
		 */
		public function addType()
		{	$type = I('put.type');

			if ($type) {

				D('LiveParamType','Live')->addType($type);
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_参数类型列表
		 * GET /liveParam/typeLists
		 * @return [json] [{"id":"type":"xxxx"}]
		 */
		public function typeLists()
		{
			D('LiveParamType','Live')->typeLists();

		}
		/**
		 * 直播参数管理_删除参数类型
		 * GET /liveParam/deleteType?type_id=1
		 * @return [type] [description]
		 */
		public function deleteType()
		{
			if (I('get.type_id')) {
				D('LiveParamType','Live')->deleteType(I('get.type_id'));
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_添加参数
		 * POST /liveParam/addParam
		 * POST_DATA: {"type_id":1, "param":"xxxx", "desc":"xxxx"}
		 */
		public function addParam()
		{
			$data = I('put.');
			if (!empty($data['type_id'])&&isset($data['param'])&&isset($data['desc'])) {
				D('LiveParamList','Live')->addParam($data['type_id'],$data['param'],$data['desc']);
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_参数列表
		 * GET /liveParam/paramLists?type_id=1
		 * 参数带type_id
		 * RETURN  [{"id":2,  "param":"xxxx", "desc":"xxxx"}]
		 * 参数不带type_id
		 * RETURN  [{"type":"xxxx", params:[{"id":2,  "param":"xxxx", "desc":"xxxx"}]}]
		 *
		 */
		public function paramLists()
		{
			$typeId = I('get.type_id');
			if (!empty($typeId)) {
				D('LiveParamList','Live')->paramLists($typeId);
			}else{
				D('LiveParamList','Live')->paramLists();
			}

		}
		/**
		 * 直播参数管理_删除参数
		 * GET /liveParam/deleteParam?param_id=1
		 * @return [type] [description]
		 */
		public function deleteParam()
		{
			if (I('get.param_id')) {
				D('LiveParamList','Live')->deleteParam(I('get.param_id'));
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_添加参数预选值
		 * POST /liveParam/addOption
		 * POST_DATA: {"param_id":"xxxx", "value":"xxxx"}
		 */
		public function addOption()
		{
			$put = I('put.','','');
			if (isset($put['value'])&&!empty($put['param_id'])) {
				D('LiveParamOptions','Live')->addOption($put['param_id'],$put['value']);
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_参数预选值列表
		 * GET /liveParam/optionLists?param_id=1
		 * 或
		 * GET /liveParam/optionLists?param=xxx
		 * RETURN: [{"id":1,"value":"xxx","default":"true"}]
		 * @return [type] [description]
		 */
		public function optionLists()
		{
			$get = I('get.','','');
			if (!empty($get['param_id'])) {
				D('LiveParamOptions','Live')->optionLists($get['param_id']);
			}elseif (isset($get['param'])) {
				D('LiveParamOptions','Live')->optionLists(false,$get['param']);
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_删除参数预选值
		 * GET /liveParam/deleteOption?option_id=1
		 * @return [type] [description]
		 */
		public function deleteOption()
		{
			if (I('get.option_id')) {
				D('LiveParamOptions','Live')->deleteOption(I('get.option_id'));
			}else{
				result('param');
			}
		}



		/**
		 * 直播参数管理_修改参数分类
		 * 修改参数管理参数分类 GET /liveParam/modifyParamType?id=1&type=xxxx 改 POST /liveParam/modifyParamType{"id":1,"type":"xxxx"
		 * 修改参数管理参数 GET /liveParam/modifyParamType?id=1&param=xxxx&desc=xxxx 改 POST /liveParam/modifyParamType{"id":1,"param":"xxxx","desc":"xxxx"}
		 * 修改参数管理预选值列表 post /liveParam/modifyParamType {"id":1,"value":"xxx","default":"true"}
		 * @return [type] [description]
		 */
		public function modifyParamType()
		{
			$put = I('put.','','');
			if (isset($put['id'])&&isset($put['type'])) {
				D('LiveParamType','Live')->modifyParamType($put['id'],$put['type']);
			}elseif (isset($put['id'])&&isset($put['param'])&&isset($put['desc'])) {
				D('LiveParamList','Live')->modifyParam($put['id'],$put['param'],$put['desc']);
			}elseif (isset($put['id'])&&isset($put['value'])&&isset($put['default'])) {
				D('LiveParamOptions','Live')->modifyParam($put['id'],$put['value'],$put['default']);
			}else{
				result('param');
			}
		}

		/**
		 * 直播参数管理_添加参数组
		 * POST /liveParam/addGroup
		 * POST_DATA: {"group":"xxxx","start":1000, "end":2000}
		 */
		public function addGroup()
		{
			$group = I('put.group','','');
			/*$start = I('put.start');
			$end = I('put.end');*/
			if (!empty($group)/*&&isset($start)&&isset($end)*/) {
				D('LiveParamGroup','Live')->addGroup($group/*,$start,$end*/);
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_组列表
		 * GET /liveParam/groupLists
		 * @return [json] [{"id":1, "group":"xxxx", "start":1000, "end":2000}]
		 */
		public function groupLists()
		{
			D('LiveParamGroup','Live')->groupLists();

		}
		/**
		 * 直播参数管理_删除组
		 * GET /liveParam/deleteGroup?group_id=1
		 * @return [type] [description]
		 */
		public function deleteGroup()
		{
			if (I('get.group_id')) {
				D('LiveParamGroup','Live')->deleteGroup(I('get.group_id'));
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_参数组添加参数
		 * POST /liveParam/addGroupParam
		 * POST_DATA: {"group_id":2, "param":"xxxx", "value":"xxx", "type":"xxx", "desc":"xxxx","default":"false"}
		 */
		public function addGroupParam()
		{
			$group = I('put.');
			if (!empty($group['group_id'])&&isset($group['param'])&&isset($group['value'])&&isset($group['type'])&&isset($group['desc'])&&isset($group['default'])) {
				D('LiveParamGroupInfo','Live')->addGroupParam($group['group_id'],$group['param'],$group['value'],$group['type'],$group['desc'],$group['default']);
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_参数组参数列表
		 * GET /liveParam/groupParamLists?group_id=1
		 * RETURN [{"type":"xxx", "params":[{"id":1, "param":"xxxx", "value":"xxx",  "desc":"xxxx"}]}]
		 * @return [type] [description]
		 */
		public function groupParamLists()
		{
			$group = I('get.group_id');
			if (!empty($group)) {
				D('LiveParamGroupInfo','Live')->groupParamLists($group);
			}else{
				D('LiveParamGroupInfo','Live')->groupParamLists();
			}

		}
		/**
		 * 直播参数管理_删除参数组参数
		 * GET /liveParam/deleteGroupParam?id=1
		 * @return [type] [description]
		 */
		public function deleteGroupParam()
		{
			if (I('get.id')) {
				D('LiveParamGroupInfo','Live')->deleteGroupParam(I('get.id'));
			}else{
				result('param');
			}
		}
		/**
		 * 直播参数管理_修改参数值
		 * 修改参数组参数值 GET /liveParam/modifyGroupParam?id=1&value=xxxx&default=xxxx
		 * 修改参数组GET /liveParam/modifyGroupParam?id=1&group=xxxx
		 * @return [type] [description]
		 */
		public function modifyGroupParam()
		{
			$get = I('get.','','');
			if (!empty($get['id'])&&isset($get['value'])&&isset($get['default'])) {
				D('LiveParamGroupInfo','Live')->modifyGroupParam($get['id'],$get['value'],$get['default']);
			}else{
				result('param');
			}
		}
		//--------------------------------------------------------------------------------------------
		/**
		 * 直播系统--直播参数管理--乐视库配置--添加乐视库
		 * POST /liveParam/addLeLibrary
		 * POST_DATA:
		 * {"group":"xxxx"}
		 */
		public function addLeLibrary()
		{
			$put = I('put.');
			D('LiveLeLibrary','Live')->addLeLibrary($put);
			result();

		}
		/**
		 * 直播系统--直播参数管理--乐视库配置--乐视库列表
		 * GET /liveParam/leLibraryLists?page=x&pageSize=x&name=x
		 * @return [json] [{"id":1, "group":"xxxx"}]
		 */
		public function leLibraryLists()
		{
			$get = I('get.');
			$res = D('LiveLeLibrary','Live')->leLibraryLists($get);
			result(true,$res);
		}
		/**
		 * 直播系统--直播参数管理--乐视库配置--删除乐视库
		 * GET /liveParam/deleteLeLibrary?id=1
		 * @return [type] [description]
		 */
		public function deleteLeLibrary()
		{
			$get = I('get.');
			D('LiveLeLibrary','Live')->deleteLeLibrary($get);
			result();

		}
		/**
		 * 直播系统--直播参数管理--乐视库配置--乐视库添加参数
		 * POST /liveParam/addLeLibraryParam
		 * POST_DATA:
		 * 	{
		 * 		"groupId":2,
		 * 		"param":"xxxx",
		 * 		"value":"xxx",
		 * 		"type":"xxx",
		 * 		"desc":"xxxx",
		 * 		"default":"false"
		 * 	}
		 */
		/*public function addLeLibraryParam()
		{
			$put = I('put.');
			D('LiveLeLibraryInfo','Live')->addLeLibraryParam($put);
			result();
		}*/
		/**
		 * 直播系统--直播参数管理--乐视库配置--乐视库参数列表
		 * GET /liveParam/leLibraryParamLists?groupId=1
		 * RETURN
		 * [{"type":"xxx", "params":[{"id":1, "param":"xxxx", "value":"xxx",  "desc":"xxxx"}]}]
		 * @return [type] [description]
		 */
		public function leLibraryParamLists()
		{
			$get = I('get.');
			$res = D('LiveLeLibraryInfo','Live')->leLibraryParamLists($get);
			result(true,$res);

		}
		/**
		 * 直播系统--直播参数管理--乐视库配置--删除乐视库参数
		 * GET /liveParam/deleteLeLibraryParam?id=1
		 * @return [type] [description]
		 */
		/*public function deleteLeLibraryParam()
		{
			$get = I('get.');
			D('LiveLeLibraryInfo','Live')->deleteLeLibraryParam($get);
			result();

		}*/
		/**
		 * 直播系统--直播参数管理--乐视库配置--修改乐视库参数
		 *
		 * 修改参数组参数值 GET /liveParam/modifyLeLibraryParam?id=1&value=xxxx&default=xxxx
		 * @return [type] [description]
		 */
		/*public function modifyLeLibraryParam()
		{
			$get = I('get.');

			D('LiveLeLibraryInfo','Live')->modifyLeLibraryParam($get);

			result();

		}*/

		/**
		 * 直播系统--直播参数管理--乐视库配置--保存乐视库参数到发布
		 * [saveLeLibraryParamPublish description]
		 *
		 * POST /liveParam/saveLeLibraryParamPublish
		 * {
		 * 		"groupId":2,
		 * 		"items":[
		 * 			{"param":"xxxx",
		 * 			"value":"xxx",
		 * 			"type":"xxx",
		 * 			"desc":"xxxx",
		 * 			"default":"false"}
		 * 		]
		 * }
		 * @return [type] [description]
		 */
		public function saveLeLibraryParamPublish()
		{
			$put = I('put.');

			D('LiveLeLibraryInfo','Live')->saveLeLibraryParamPublish($put);

			result();
		}
	}
