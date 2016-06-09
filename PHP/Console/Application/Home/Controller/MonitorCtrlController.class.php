<?php
	namespace Home\Controller;
	use Think\Controller;
	class MonitorCtrlController extends Controller
	{
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
		 * 网管管理_新增网管命令
		 *POST /monitorCtrl/addCmd
		 *DATA: {"cmd_type":"shell", "shell":"ls /root"} || {"cmd_type":"upload", "upload_file":"/etc/a.conf"}
		 */
		public function addCmd()
		{
			$cmdType = I('put.cmd_type');
			$cmd = I('put.');
			if (!empty($cmdType)) {
				D('MonitorCmdManager')->addCmd($cmd);
			}else{
				result('param');
			}
		}
		/**
		 * 网管管理_删除网管命令
		 * GET /monitorCtrl/deleteCmd?id=1
		 * @return [type] [description]
		 */
		public function deleteCmd()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('MonitorCmdManager')->deleteCmd($id);
			}else{
				result('param');
			}
		}
		/**
		 * 网管管理_网管命令列表
		 * GET /monitorCtrl/cmdLists
		 * {"result":"ok", "extra":[{"id":1, "cmd_type":"shell/upload", "shell":"xxxx", "upload_file":"xxxx", "time":"2015-08-26 22:22:00"}]}
		 * @return [type] [description]
		 */
		public function cmdLists()
		{
			$data['extra'] = M('monitor_cmd_manager')->select();
			if (!empty($data['extra'])) {
				result(true,$data);
			}else{
				$data['extra'] = array();
				result(true,$data);
			}
		}
		/**
		 * 网管管理_新增网管命令发布
		 * POST  /monitorCtrl/addPublish
		 * PROXY POST  http://172.16.10.254:20000/access/publish/add
		 * {"model":"VSOON_3128", "vendorID":"2232",  "type":"group/AB/ALL","group":1,"AB":100 ,"cmd_id":1}
		 * model 型号 vendorID  客户ID  type 类型  (group组名/AB灰度/ALL全部） 	cmd_id 命令ID group_id组ID
		 * @return {"result":"ok/failed","reason":"xxxx"}
		 */
		public function addPublish()
		{
			$model = I('put.model');
			$vendorId = I('put.vendorID');
			$type = I('put.type');
			$cmdId = I('put.cmd_id');

			if (!empty($model)&&!empty($vendorId)&&!empty($type)&&!empty($cmdId)){

				$cmdOne = D('MonitorCmdManager')->field('id,cmd_type,shell,upload_file')->find($cmdId);
				if(empty($cmdOne)){
					result('命令不存在');
				}

				$isExists = D('MonitorCmdPublish')->field('id')->where("`model`='".$model."' AND `vendor_id`='".$vendorId."' AND `type`='".$type."'")->order('id desc')->find();

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
								'sn_list'=>json_encode($arr),
								'time'=>date('Y-m-d H:i:s',time()),
								'cmd_id'=>$cmdOne['id'],
								'AB'=>0,
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
								'AB'=>I('put.AB'),
								'sn_list'=>'',
								'time'=>date('Y-m-d H:i:s',time()),
								'cmd_id'=>$cmdOne['id']
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
							'AB'=>0,
							'sn_list'=>'',
							'time'=>date('Y-m-d H:i:s',time()),
							'cmd_id'=>$cmdOne['id']
            					);
				 		break;

				 	default:
				 		result('param');
				 		break;
				 }

			 	if (empty($cmdOne['shell'])) {
			 		unset($cmdOne['shell']);
			 	}elseif (empty($cmdOne['upload_file'])) {
			 		unset($cmdOne['upload_file']);
			 	}
			 	unset($cmdOne['id']);
			 	$data['content'] = $cmdOne;

			 	if (empty($data['content'])) {
			 		result('命令不存在');
			 	}
				$data['item'] ='monitorCtrl';

				$data['id'] = D('MonitorCmdPublish')->add($options);

		 		if ($data['id']) {

		 			$data1 = json_encode($data);


					$result = postUrl( C('MONITOR_CTRL_ACCESS_ADDR') .'access/publish/add',$data1);
					if ($result ===false) {
						D('MonitorCmdPublish')->delete($data['id']);
			                		result('发布地址出错');
			               	 }
				           $result = json_decode($result,true);
				           if ($result['result']!='ok') {
						D('MonitorCmdPublish')->delete($data['id']);
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
		 *  网管管理_获取网管命令已发布列表
		 *  GET  /monitorCtrl/PublishLists
		 * @return {"result":"ok", "extra":[
		 *  {"id":1, "time":"2015-08-16 12:33:24", "target":{"model":"VSOON_3128", "vendorID":"2232",  "type":"group/AB/ALL"}, "content":{"cmd_type":"shell", "shell":"ls /root"}
		 *  ]}
		 *
		 */
		public function PublishLists()
		{
			$id = I('get.id');
			if (!empty($id)) {
				$response =  getUrl( C('MONITOR_CTRL_ACCESS_ADDR') .'access/publish/list?item=monitorCtrl&id='.$id);
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

				$response =  getUrl( C('MONITOR_CTRL_ACCESS_ADDR') .'access/publish/list?item=monitorCtrl');
				if ($response === false) {
					result('接入层连接错误');
				}
				$response = json_decode($response,true);

				foreach ($response['extra']  as $key => $value) {
					if (!empty($value['content']['upload_file'])) {
						$response['extra'][$key]['content']['shell']= '';
					}elseif (!empty($value['content']['shell'])) {
						$response['extra'][$key]['content']['upload_file']= '';
					}
				}
				echo json_encode($response);
			}



		}
		/**
		 *  网管管理_删除发布网管命令
		 *  GET /monitorCtrl/deletePublish?id=6
		 * @return {"result":"ok/failed","reason":"xxxx"}
		 */
		public function deletePublish()
		{
			$id = I('get.id');

			if (!empty($id)) {
				$MonitorCmdPublish = D('MonitorCmdPublish')->find($id);

				if ($MonitorCmdPublish) {
					$json = getUrl( C('MONITOR_CTRL_ACCESS_ADDR') .'access/publish/del?item=monitorCtrl&id=' . $id);
					if ($json === false) {
						result('接入层连接错误');
					}
					$json = json_decode($json,true);
					if ($json['result'] !='ok' ) {
						result($json['reason']);
					}else{
						/*$MonitorCmdPublish['time'] = date('Y-m-d H:i:s',time());
						$MonitorCmdPublish['reason'] = '下架';
						unset($MonitorCmdPublish['id']);
						$versionOne = D('Monitor')->find($MonitorCmdPublish['version']);
						$MonitorCmdPublish['version'] = $versionOne['version'];
						D('MonitorCmdPublishHistory')->add($MonitorCmdPublish);*/
						D('MonitorCmdPublish')->delete($id);
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
