<?php
	namespace Home\Desktop;
	class DesktopCmdDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_cmd';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'groupId' =>'group_id',
			"vendorID"=>"vendorid",
			'whiteList' =>'white_list',
			"blackList"=>"black_list",
		);
		/*protected $_validate = array(
			array('desktop2','','桌面ID已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/
		public function addCmd($put)
		{
			//检查数据
			if (!empty($put['model']) && ($put['pub_range'] == 'all' || $put['pub_range'] == 'jbk'  || $put['pub_range'] == 'unjbk' )&&($put['type'] == 'ALL' || $put['type'] == 'group' )&&!empty($put['cmd'])&&is_array($put['cmd'])) {

				if ($put['type'] == 'group' ) {
					if (!empty($put['groupId'])) {
						$group = D('Group')->getValOneForId($put['groupId']);
						if (!$group) {
							result('组不存在，请选择正确组');
						}
					}else{
						result('param');
					}
				}
				$put['vendorID'] = !empty($put['vendorID'])?$put['vendorID']:'none';
				if ($oneCmd = $this->where("`model`='%s' and `type`='%s' and `vendorid`= '%s'",array($put['model'],$put['type'],$put['vendorID']))->find()) {
					$this->deleteCmd($oneCmd['id'],'覆盖');
				}
				$put['cmd'] = array_values($put['cmd']);
				$jsonCmd = json_encode($put['cmd'],JSON_UNESCAPED_UNICODE);

				if (!empty($put['whiteList'])&&is_array($put['whiteList'])) {
					$whiteList = array_values($put['whiteList']);
					$put['whiteList'] = json_encode($whiteList,JSON_UNESCAPED_UNICODE);
				}else{
					$put['whiteList'] = '[]';
				}
				if (!empty($put['blackList'])&&is_array($put['blackList'])) {
					$blackList = array_values($put['blackList']);
					$put['blackList'] = json_encode($blackList,JSON_UNESCAPED_UNICODE);
				}else{
					$put['blackList'] = '[]';
				}
				$time = time();
				$options = array(
					'model'=>$put['model'],
					'type'=>$put['type'],
					'group_id'=>!empty($put['groupId'])?$put['groupId']:'0',
					'cmd'=>$jsonCmd,
					'version'=>$time,
					"vendorID"=>$put['vendorID'],
					'pub_range'=>$put['pub_range'],
					"black_list"=>$put['blackList'],
					'white_list'=>$put['whiteList']
				);

				//添加到数据库
				$this->create($options);
				$id = $this->add();
				D('DesktopCmdHistory','Desktop')->addCmdHistory($options,'发布');
				return true;
				/*$data = array(
					'id'=>$id,
					'item'=>'desktopCMD',
					'target'=>array(
						'model'=>$put['model'],
						'type'=>$put['type'],
						"vendorID"=>$put['vendorID'],
					),
					'content'=>array(
						'cmd'=>$put['cmd'],
						'version'=>$time,
						'pub_range'=>$put['pub_range'],
						'blackList'=>$blackList,
						'whiteList'=>$whiteList,
					)
				);

				if ($put['type']=='group') {
					$data['target']['snList'] = $snList;
				}

				$data = json_encode($data,JSON_UNESCAPED_UNICODE);
				//发布
				$json = postUrl(DESKTOP3_ACCESS_ADDR.'access/publish/add',$data);

				if ($json) {
					$res = json_decode($json,true);
					if ($res['result'] !='ok') {
						$this->deleteCmd($id,$res['reason']);
						result($res['reason']);

					}else{
						return true;
					}
				}else{
					$this->deleteCmd($id,'请求发布失败');

					result('请求发布失败');
				}*/
			}else{
				result('param');
			}
		}


		public function deleteCmd($id =null,$reason= '')
		{	if ($id != null) {
				/*if (!empty($idLists)&&is_array($idLists)) {
					foreach ($idLists as $key => $value) {
						$res = $this->find($value);
						if (!$res) {
							result('桌面ID'.$value.'不存在');
						}
						$data = array(
							'desktop2'=>$res['desktop2'],
							'desktop3'=>$res['desktop3'],
							'event'=>'delete'
						);
						if (!empty($res['vendorID'])) {
							$options['vendorID'] = $res['vendorID'];
						}
						$data = json_encode($data,JSON_UNESCAPED_UNICODE);
						$json = postUrl(DESKTOP3_JBK_PASSWD_ACCESS_ADDR.'desktopMapPub',$data);
						if ($json) {
							$json = json_decode($json,true);
							if ($json['result']=='ok'){
								$this->where("`id`=%d",array($value))->delete();
							}else{
								if (!empty($json['reason'])) {
									result($json['reason']);
								}else{
									result('发布地址返回数据出错');
								}

							}
						}else{
							result('发布地址出错');
						}
					}
				}*/

			/*}else{*/
				$res = $this->find($id);
				if ($res) {
					$this->where("`id`=%d",array($id))->delete();
					unset($res['id']);
					if ($reason === '' ) {
						D('DesktopCmdHistory','Desktop')->addCmdHistory($res);
					}else{
						D('DesktopCmdHistory','Desktop')->addCmdHistory($res,$reason);
					}
					return true;
					/*$json = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/del?item=desktopCMD&id='.$id);
					if ($json) {
						$json = json_decode($json,true);
						if ($json['result']!='ok'){
							if (strstr($json['reason'],'not such id')!=true) {
								result($json['reason']);
							}
						}
						return true;
					}else{
						result('发布地址出错');
					}
				}else{

					$json = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/del?item=desktopCMD&id='.$id);
					if ($json) {
						$json = json_decode($json,true);
						if ($json['result']=='ok'){
							$this->where("`id`=%d",array($id))->delete();

						}else{
							if (strstr($json['reason'],'not such id')!=true) {
								result($json['reason']);
							}else{
								$this->where("`id`=%d",array($id))->delete();
							}
						}

						unset($res['id']);
						if ($reason === '' ) {
							D('DesktopCmdHistory','Desktop')->addCmdHistory($res);
						}else{
							D('DesktopCmdHistory','Desktop')->addCmdHistory($res,$reason);
						}
						return true;
					}else{
						result('发布地址出错');
					}*/
				}
			}
		}

		public function cmdLists($get)
		{
			if (empty($get['id'])) {
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`model` like '%".$get['name']."%' or `vendorid` like  '%".$get['name']."%' ")->select();
					}else{
						$res['extra'] = $this->where("`model` like '%".$get['name']."%' or `vendorid` like  '%".$get['name']."%' ")->select();
					}
					$res['count'] = $this->where("`model` like '%".$get['name']."%' or `vendorid` like  '%".$get['name']."%' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $this->select();
					}
					$res['count'] = $this->count();
				}
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}else{
					foreach ($res['extra'] as $key => $value) {
						if (!empty($value['cmd'])) {
							$res['extra'][$key]['cmd'] = json_decode($value['cmd'],true);
						}
						if (!empty($value['blackList'])) {
							$res['extra'][$key]['blackList'] = json_decode($value['blackList'],true);
						}
						if (!empty($value['whiteList'])) {
							$res['extra'][$key]['whiteList'] = json_decode($value['whiteList'],true);
						}
					}
				}

				/*$json = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/list?item=desktopCMD');
				if ($json) {
					$json = json_decode($json,true);
					echo json_encode($json,JSON_UNESCAPED_UNICODE);

				}else{
					result('发布地址出错');
				}*/
			}else{
				/*$json = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/list?item=desktopCMD&id='.$id);
				if ($json) {
					$json = json_decode($json,true);
					if (!empty($json['extra']['snList'])){
						$res['extra'] =$json['extra']['snList'];
					}else{
						$res['extra'] =array();
					}
					result(true,$res);
				}else{
					result('发布地址出错');
				}*/

				$res['extra'] = $this->find($get['id']);
				$res['count'] = 1;
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = 0;
				}else{
					foreach ($res['extra'] as $value) {
						if (!empty($value['cmd'])) {
							$res['extra']['cmd'] = json_decode($value['cmd'],true);
						}
						if (!empty($value['blackList'])) {
							$res['extra']['blackList'] = json_decode($value['blackList'],true);
						}
						if (!empty($value['whiteList'])) {
							$res['extra']['whiteList'] = json_decode($value['whiteList'],true);
						}
					}
				}
			}


			return $res;
		}


	}