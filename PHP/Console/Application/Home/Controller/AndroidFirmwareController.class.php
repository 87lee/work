<?php
	namespace Home\Controller;
	use Think\Controller;
 	class AndroidFirmwareController extends Controller {
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
		 * 安卓固件_添加配置包管理
		 * post   /androidFirmware/addConfigGroup
		 * file  name="prop"    上传后缀为.prop文件
		 * file name="zip" 上传后缀为.zip文件
		 * name="extra"  value='{"name":"名称"}'
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addConfigGroup()
		{

			$put = I('post.extra');
			D('FirmwareConfigGroup','AndroidFirmware')->addConfigGroup($put);
			result();
		}
		/**
		 * 安卓固件_删除配置包管理
		 *
		 * get /androidFirmware/deleteConfigGroup?id=1
		 * @return [json] {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteConfigGroup()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				if (D('FirmwareConfigGroup','AndroidFirmware')->deleteConfigGroup($get['id'])) {
					result();
				}
			}else{
				result('param');
			}
		}
		/**
		 * 安卓固件_修改配置包管理
		 * post /androidFirmware/modifyBlacklist
		 * file  name="prop"    上传后缀为.prop文件
		 * file name="zip" 上传后缀为.zip文件
		 * name="extra"  value='{"name":"名称"}'
		 * @return [type] {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyConfigGroup()
		{
			$put = I('post.extra');
			D('FirmwareConfigGroup','AndroidFirmware')->modifyConfigGroup($put);
			result();

		}
		/**
		 * 安卓固件_配置包管理列表
		 * get /androidFirmware/configGroupLists?page=xxx&pageSize=xxx
		 * get /androidFirmware/configGroupLists?page=xxx&pageSize=xxx&name=xxx
		 * get /androidFirmware/configGroupLists?appName=xxx
		 * get /androidFirmware/configGroupLists?id=1
		 * get /androidFirmware/configGroupLists
		 * @return [type] {"result":"ok/fail","reason":"xxx"}
		 */
		public function configGroupLists()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('FirmwareConfigGroup','AndroidFirmware')->configGroupLists($get['id']);
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('FirmwareConfigGroup','AndroidFirmware')->configGroupLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					D('FirmwareConfigGroup','AndroidFirmware')->configGroupLists(null,$get['name']);
				}
			}elseif(!empty($get['appName'])){
				D('FirmwareConfigGroup','AndroidFirmware')->configGroupLists(null,null,null,null,$get['appName']);
			}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {

				D('FirmwareConfigGroup','AndroidFirmware')->configGroupLists(null,null,$get['page'],$get['pageSize']);
			}else{
				D('FirmwareConfigGroup','AndroidFirmware')->configGroupLists();
			}
		}


		//----------------------------------------------------------------------------------------------------------------------------------------------------------


		/**
		 * 安卓固件_发布配置包
		 * POST /androidFirmware/publishConfigGroup/
		 *
		 *post {"vendorID":"2232","type":"group/ALL","model":"用户填写","configGroupId":"配置包组ID"}
		 *
		 * param: {"id":"1", "item":"firmwareConfig", "target":{"model":"ALL", "vendorID":"2232", "snList":["222","333"], "type":"group/ALL"}, "content":{"url":"http://","version":10,"md5":"xxxxx"} }
		 *
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function publishConfigGroup()
		{
			$put = I('put.');
			if (!empty($put)) {
				D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroup($put);
			}else{
				result('param');
			}
			result();
		}
		/**
		 * 安卓固件_修改发布配置包备注
		 * POST /androidFirmware/modifyPublishConfigGroupDesc/
		 *
		 *post {"vendorID":"2232","type":"group/ALL","model":"用户填写","configGroupId":"配置包组ID"}
		 *
		 * param: {"id":"1", "item":"firmwareConfig", "target":{"model":"ALL", "vendorID":"2232", "snList":["222","333"], "type":"group/ALL"}, "content":{"url":"http://","version":10,"md5":"xxxxx"} }
		 *
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyPublishConfigGroupDesc()
		{
			$put = I('put.');
			D('FirmwareConfigGroupPublish','AndroidFirmware')->modifyPublishConfigGroupDesc($put);
			result();
		}
		/**
		 * 安卓固件_删除发布配置包
		 * POST /androidFirmware/deletePublishConfigGroup?id
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deletePublishConfigGroup()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('FirmwareConfigGroupPublish','AndroidFirmware')->deletePublishConfigGroup($get['id']);
			}else{
				result('param');
			}
			result();
		}
		/**
		 * 安卓固件_发布配置包列表
		 * POST /androidFirmware/publishConfigGroupLists?id=x
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function publishConfigGroupLists()
		{
			/*$get = I('get.');
			if (!empty($get['id'])) {
				D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists($get['id']);
			}else{
				result('param');
			}
			*/
			$get = I('get.');
			if (!empty($get['id'])) {
				$res = D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists($get['id']);
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$res = D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					$res = D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists(null,$get['name']);
				}
			}elseif(!empty($get['appName'])){
				$res = D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists(null,null,null,null,$get['appName']);
			}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {

				$res = D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists(null,null,$get['page'],$get['pageSize']);
			}else{
				$res = D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists();
			}
			result(true,$res);
		}
		/*public function publishConfigGroupLists1()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists1($get['id']);

			}else{
				D('FirmwareConfigGroupPublish','AndroidFirmware')->publishConfigGroupLists1();
			}
		}*/
	}
 ?>