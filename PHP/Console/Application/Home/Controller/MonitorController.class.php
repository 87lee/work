<?php
	namespace Home\Controller;
	use Think\Controller;
	class MonitorController extends Controller {
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
		 *
		 * 网管管理_添加网管版本
		 * POST  /monitor/addVersion
		 * DATA  表单，附带信息：{"version":123,"desc":"描述"}
		 */
		public function addVersion()
		{

			if ( !empty($_POST['extra_data']) ) {

	    			$str = str_replace("\r\n", '\n', $_POST['extra_data']);
				$extra_data = json_decode($str ,true );
				// var_dump($extra_data);
				$version = $extra_data['version'];
				$desc = $extra_data['desc'];

				if (empty($desc) || empty($version)) {
					result('附带信息参数不正确');
				}
	    			$desc = str_replace("\r\n", '\n', $desc);
	    			$desc = str_replace("\n", ' ', $desc);
	    			$desc = explode(' ',$desc);
	    			$desc = json_encode($desc);
				$monitor = D('Monitor');
				if ( $monitor->where("`version` = '" . $version . "'")->find() ) {
					result('该版本已存在');
				}
				require_once '../Base/function/Form.class.php';
				$form = new \form();
				$formData = $form->getFormFile();

				require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				if (!empty($formData)) {
					$res = $base->uploadFile($formData);
				}else{
					result('param');
				}
				if (!empty($res)) {
					foreach ($res as $key => $value) {
						$options = array(
							'desc'=>$desc,
							'version'=>$version,
							'time'=> date('Y-m-d H:i:s',time()),
							'md5'=>$formData[$key]['md5_file'],
							'path'=>$value['oss'],
							'download'=>$value['download']
						);
						$monitor->addVersion($options);
						die();
					}
				}else{
					result('param');
				}
			}else{
				result('param');
			}
		}
		/**
		 * 网管管理_获取网管版本列表
		 * GET  /monitor/versionLists
		 * RETURN  [{"id":1, "version":123, "md5":"xxxx", "path":"/xx/xx", "desc":"xxxx", "upload":0/1, "time":"2015-08-06 14:20:12"}]
		 * @return [type] [description]
		 */
		public function versionLists()
		{
			$version = I('get.version');
			if (!empty($version)) {
				D('Monitor')->versionLists($version);
			}else{
				D('Monitor')->versionLists();
			}

		}
		/**
		 * 网管管理_删除网管版本
		 * GET  /monitor/deleteVersion?id=1
		 * * @return [type] [description]
		 */
		public function deleteVersion()
		{
			if (!empty(I('get.id'))) {
				$id = trim(I('get.id'));
				D('Monitor')->deleteVersion($id);
			}else{

				result('param');
			}
		}

		/**
		 * 网管管理_新增网管发布
		 * {"model":"VSOON_3128", "vendorID":"2232",  "type":"group/AB/ALL","group_id":1,"AB":100 ,"version":1}
		 * model厂商 vendorID   type 类型  (group组名/AB灰度/ALL全部） version 版本号 group_id组ID
		 * @param string $value [description]
		 */
		public function addPublish()
		{
			$model = I('put.model');
			$vendorId = I('put.vendorID');
			$type = I('put.type');
			$version = I('put.version');
			if (!empty($model)&&!empty($vendorId)&&!empty($type)&&!empty($version)){
				$versionOne = D('Monitor')->field('id')->where('version=%d',array($version))->find();
				$isExists = D('MonitorPublish')->field('id')->where("`model`='".$model."' AND `vendor_id`='".$vendorId."' AND `type`='".$type."'")->order('id desc')->find();
				if ($isExists) {
					result('重复发布！');
				}
				switch ($type) {
				 	case 'group':
				 		if ( !empty(I('put.group') ) ){
				 			$snList = D('GroupMembers')->field('sn')->where('group_id=%d',array(I('put.group')))->select();
				 			$arr = array();
				 			foreach ($snList as $key => $value) {
				 				$arr[$key] = $value['sn'];
				 			}
							$data['target'] = array(
								'model'=>$model,
								'vendorID'=>$vendorId,
								'type'=>$type,
								'snList'=>$arr
							);
							$options = array(
	                						'model'=>$model,
								'vendor_id'=>$vendorId,
								'type'=>$type,
								'group_id'=>I('put.group'),
								'time'=>date('Y-m-d H:i:s',time()),
								'version'=>$versionOne['id']
	            						);
				 		}
				 		break;
				 	case 'AB':
				 		if( !empty(I('put.AB'))){
					 		$data['target'] = array(
								'model'=>$model,
								'vendorID'=>$vendorId,
								'type'=>$type,
								'AB'=>I('put.AB')
							);
							$options = array(
	                						'model'=>$model,
								'vendor_id'=>$vendorId,
								'type'=>$type,
								'gray'=>I('put.AB'),
								'time'=>date('Y-m-d H:i:s',time()),
								'version'=>$versionOne['id']
	            						);
				 		}
				 		break;
				 	case 'ALL':
				 		$data['target'] = array(
							'model'=>$model,
							'vendorID'=>$vendorId,
							'type'=>$type
						);
				 		$options = array(
                						'model'=>$model,
							'vendor_id'=>$vendorId,
							'type'=>$type,
							'time'=>date('Y-m-d H:i:s',time()),
							'version'=>$versionOne['id']
            						);
				 		break;

				 	default:
				 		result('param');
				 		break;
				 }
			 	$data['content'] = D('Monitor')->field('version,md5,desc,download')->where('version=%d',array($version))->find();
			 	$data['content']['desc'] = json_decode($data['content']['desc']);
			 	if (empty($data['content'])) {
			 		result('版本不存在');
			 	}
				$data['item'] ='monitor';
				$data['id'] = D('MonitorPublish')->add($options);
		 		if ($data['id']) {
		 			$data1 = json_encode($data);

					$result = postUrl( C('C('MONITOR_ACCESS_ADDR')') .'access/publish/add',$data1);
					if (empty($result)) {
						D('MonitorPublish')->delete($data['id']);
			                		result('发布地址出错');
			               	 }
			                	$result = json_decode($result,true);
			               	 if ($result['result']!='ok') {
						D('MonitorPublish')->delete($data['id']);
				                	result($result['reason']);
			               	 }else{
				                	result();
			                	}
			            	}else{
			            		result('添加数据失败');
			            	}
			}else{
				result('param');
			}
		}
		/**
		 * 网管管理_网管版本发布列表
		 * [PublishLists description]
		 */
		public function PublishLists()
		{
			$id = I('get.id');
			if (!empty($id)) {
				$response =  getUrl( C('MONITOR_ACCESS_ADDR') .'access/publish/list?item=monitor&id='.$id);
				if ($response === false) {
					result('接入层连接错误');
				}
				$response = json_decode($response,true);
				$data = array(
					'extra'=>array(
						"snList"=>$response['extra']['snList']
					),
				);
				result(true,$data);
			}else{
				$response =  getUrl( C('MONITOR_ACCESS_ADDR') .'access/publish/list?item=monitor');

				if ($response === false) {
					result('接入层连接错误');
				}else{
					echo $response;
				}
			}
		}
		/**
		 * 网管管理_删除网管版本发布
		 * [deletePublish description]
		 */
		public function deletePublish()
		{
			$id = I('get.id');
			if (!empty($id)) {
				$monitorPublish = D('MonitorPublish')->find($id);
				if ($monitorPublish) {
					$json = getUrl( C('MONITOR_ACCESS_ADDR') .'access/publish/del?item=monitor&id=' . $id);
					if ($json === false ) {
						result('接入层连接错误');
					}
					$json = json_decode($json,true);
					if ($json['result'] !='ok' ) {
						result($json['reason']);
					}else{
						$monitorPublish['time'] = date('Y-m-d H:i:s',time());
						$monitorPublish['reason'] = '下架';
						unset($monitorPublish['id']);
						$versionOne = D('Monitor')->find($monitorPublish['version']);
						$monitorPublish['version'] = $versionOne['version'];
						D('MonitorPublishHistory')->add($monitorPublish);
						D('MonitorPublish')->delete($id);
						result();
					}
				}else{
					result('这个没有发布');
				}
			}else{
				result('param');
			}
		}


	}
 ?>