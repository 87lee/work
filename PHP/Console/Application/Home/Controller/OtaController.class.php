<?php
	namespace Home\Controller;
	use Think\Controller;
 	class OtaController extends Controller {
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
		 * OTA升级_添加OTA客户
		 * post /ota/addModel
		 * {
		 * "model":"厂商",
		 * "vendorID":"23",
		 * "desc":"描述"
		 * }
		 */
		public function addModel()
		{
			$put = I('put.');
			D('Model','Ota')->addModel($put);
			result();
		}
		/**
		 * OTA升级_修改OTA客户
		 * post /ota/modifyModel
		 * {
		 * "id":"1",
		 * "model":"厂商",
		 * "vendorID":"23",
		 * "desc":"描述"
		 * }
		 */
		public function modifyModel()
		{
			$put = I('put.');
			D('Model','Ota')->modifyModel($put);
			result();
		}
		/**
		 * OTA升级_删除OTA客户
		 * post /ota/deleteModel?id
		 *
		 */
		public function deleteModel()
		{
			$get = I('get.');
			D('Model','Ota')->deleteModel($get['id']);
			result();
		}
		/**
		 * OTA升级_OTA客户列表
		 * get /ota/modelLists?page=xxx&pageSize=xx
		 *
		 * get /ota/modelLists?page=xxx&pageSize=xx&name=xx
		 *
		 * {
		 *   "extra": [{
		 * "id":"客户列表ID",
		 *  "model":"厂商",
		 * "vendorID":"vendorID",
		 *  "desc":"描述",
		 *
		 *   }],
		 *   "result": "ok",
		 *   "count":"1"
		 * }
		 * @return [type] [description]
		 */
		public function modelLists()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('Model','Ota')->modelLists($get['id']);
			}elseif(!empty($get['modelID'])){
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						D('ModelVersion','Ota')->modelLists(null,$get['name'],$get['page'],$get['pageSize'],$get['modelID']);
					}else{
						D('ModelVersion','Ota')->modelLists(null,$get['name'],null,null,$get['modelID']);
					}
				}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('ModelVersion','Ota')->modelLists(null,null,$get['page'],$get['pageSize'],$get['modelID']);
				}else{
					D('ModelVersion','Ota')->modelLists(null,null,null,null,$get['modelID']);
				}

			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('Model','Ota')->modelLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					D('Model','Ota')->modelLists(null,$get['name']);
				}
			}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {

				D('Model','Ota')->modelLists(null,null,$get['page'],$get['pageSize']);
			}else{
				D('Model','Ota')->modelLists();
			}
		}
		/**
		 * OTA升级_添加OTA客户版本
		 * "modelID":"客户ID",
		 * "fileID":"文件ID",
		 * "version":"版本号",
		 * "desc":["修复xx","优化xx"],
		 * "whiteList":["123","234"],
		 * "blackList":["123","234"]
		 * }
		 */
		public function addModelVersion()
		{
			$put = I('put.');
			D('ModelVersion','Ota')->addModelVersion($put);
			result();
		}
		/**
		 * OTA升级_修改OTA客户版本
		 * post /ota/modifyModelVersion
		 * {
		 * "id":"1",
		 * "model":"厂商",
		 * "vendorID":"23",
		 * "desc":"描述"
		 * }
		 */
		public function modifyModelVersion()
		{
			$put = I('put.');
			D('ModelVersion','Ota')->modifyModelVersion($put);
			result();
		}
		/**
		 * OTA升级_删除OTA客户版本
		 * get /ota/deleteModel?id
		 *
		 */
		public function deleteModelVersion()
		{
			$put = I('put.');
			D('ModelVersion','Ota')->deleteModelVersion($put);
			result();
		}
		/**
		 * OTA升级_发布OTA客户版本
		 * post /ota/publishModelVersion?
		 *
		 */
		public function publishModelVersion()
		{
			$put = I('put.');
			D('ModelVersionPublish','Ota')->publishModelVersion($put);
			result();
		}
		/**
		 * OTA升级_修改发布OTA客户版本
		 * post /ota/modifyPublishModelVersion
		 *
		 */
		public function modifyPublishModelVersion()
		{
			$put = I('put.');
			D('ModelVersionPublish','Ota')->modifyPublishModelVersion($put);
			result();
		}
		/**
		 * OTA升级_删除发布OTA客户版本
		 * get /ota/deletePublishModelVersion
		 *
		 */
		public function deletePublishModelVersion()
		{
			$get = I('get.');
			D('ModelVersionPublish','Ota')->deletePublishModelVersion($get['id']);
			result();
		}
		/**
		 * OTA升级_发布OTA客户版本列表
		 * get /ota/publishModelVersionLists
		 *
		 */
		public function publishModelVersionLists()
		{
			$get = I('get.');

			$res = D('ModelVersionPublish','Ota')->publishModelVersionLists($get);

			result(true,$res);
		}
	}
 ?>