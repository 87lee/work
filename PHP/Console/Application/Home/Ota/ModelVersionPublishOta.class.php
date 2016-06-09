<?php
	namespace Home\Ota;
	class ModelVersionPublishOta extends \Think\Model
	{
		protected $tableName = 'model_version_publish';
		protected $connection = 'DB_OTA';
		protected $_map = array(
			'fileID'  =>'file_id',
			'vendorID'  =>'vendor_id',
			'whiteList' =>'white_list',
			'blackList'  =>'black_list',
			'groupId'  =>'group_id',
			'AB'  =>'ab',
			'forceUpdate'  =>'force_update',
		);

		public function publishModelVersion($put,$action = 'add')
		{
			if (isset($put['length'])&&!empty($put['fileID'])&&!empty($put['model'])&&!empty($put['vendorID'])&&!empty($put['version'])&&!empty($put['type'])&& ($put['forceUpdate'] == 'false' || $put['forceUpdate'] == 'true' ) && ($put['fake'] == 'false' || $put['fake'] == 'true' )) {

				if ($put['type']=='group') {
					if (empty($put['groupId'])) {
						result('param');
					}else{
						if (!D('Group')->find($put['groupId'])) {
							result('组不存在');
						}
					}
				}elseif ($put['type']=='AB') {
					if (empty($put['AB'])) {
						result('param');
					}
				}
				//重复删除
				if ($publishVersion = $this->where("`model`='%s' and `type` = '%s' and vendor_id = '%s'",array($put['model'],$put['type'],$put['vendorID']))->find()) {
					$this->deletePublishModelVersion($publishVersion['id']);
					if ($action == 'add' ) {
						$this->actionPublish($publishVersion,'','覆盖');
					}
				}
				if (!empty($put['whiteList'])&&is_array($put['whiteList'])) {
					foreach ($put['whiteList'] as  $value) {
						$whiteList[] = $value;
					}
					$put['whiteList'] = json_encode($whiteList,JSON_UNESCAPED_UNICODE);
				}else{
					$put['whiteList'] = '[]';
					$whiteList = array();
				}
				if (!empty($put['blackList'])&&is_array($put['blackList'])) {
					foreach ($put['blackList'] as  $value) {
						$blackList[] = $value;
					}
					$put['blackList'] = json_encode($blackList,JSON_UNESCAPED_UNICODE);
				}else{
					$put['blackList'] = '[]';
					$blackList = array();
				}
				if (!empty($put['desc'])&&is_array($put['desc'])) {
					foreach ($put['desc'] as  $value) {
						$desc[] = $value;
					}
					$put['desc'] = json_encode($desc,JSON_UNESCAPED_UNICODE);
				}else{
					$put['desc'] = '[]';
					$desc = array();
				}
				$options = array(
					'model'=>$put['model'],
					'vendor_id'=>$put['vendorID'],
					'file_id'=>$put['fileID'],
					'version'=>$put['version'],
					'length'=>intval($put['length']),
					'desc'=>$put['desc'],
					'white_list'=>$put['whiteList'],
					'black_list'=>$put['blackList'],
					'type'=>$put['type'],
					'fake'=>$put['fake'],
					'force_update'=>$put['forceUpdate'],
					'time'=>time(),
				);
				if ($put['type']=='group') {
					$options['group_id'] = $put['groupId'];

				}elseif ($put['type']=='AB') {
					$options['ab'] = $put['AB'];
				}
				$publisId = $this->add($options);
				if ($action == 'add') {
					$this->actionPublish($options,'add');
				}else{
					$this->actionPublish($options,$action);
				}

				/*$data = array(
					"id"=>$publisId,
					"item"=>"ota",
					"target"=>array(
						"model"=>$put['model'],
						"vendorID"=>$put['vendorID'],
						"type"=>$put['type'],
					),
					"content"=>array(
						"version"=>$put['version'],
						"fileID"=>$put['fileID'],
						"forceUpdate"=>$put['forceUpdate'],
						"whiteList"=>$whiteList,
						"blackList"=>$blackList,
						"desc"=>$desc,
					)
				);
				if ($put['type']=='group') {
					$data['target']['snList']= $snList;
				}elseif ($put['type']=='AB') {
					$data['target']['AB'] = $put['AB'];
				}

				$data = json_encode($data,JSON_UNESCAPED_UNICODE);
				$json = postUrl(OTA_ACCESS_ADDR.'access/publish/add',$data);

				if ($json) {
					$res = json_decode($json,true);
					if ($res['result'] !='ok') {
						$this->deleteModelVersion($publisId);
						result($res['reason']);
					}else{
						return true;
					}
				}else{
					$this->deleteModelVersion($publisId);
					result('请求发布失败');
				}*/
			}else{
				result('param');
			}
		}
		public function modifyPublishModelVersion($put)
		{
			if (/*isset($put['length'])&&!empty($put['fileID'])&&*/!empty($put['id'])&&/*!empty($put['model'])&&!empty($put['vendorID'])&&!empty($put['version'])&&*/!empty($put['type'])/*&& ($put['forceUpdate'] == 'false' || $put['forceUpdate'] == 'true' ) && ($put['fake'] == 'false' || $put['fake'] == 'true' )*/) {

				/*if ($res = $this->find($put['id'])) {
					$this->deletePublishModelVersion($put['id'],'modify');
					unset($put['id']);
					$this->publishModelVersion($put,'modify');
				}else{
					unset($put['id']);
					$this->publishModelVersion($put,'modify');
				}*/

				if ($publishModelVersion = $this->find($put['id'])) {
					if ($put['type']=='group') {
						if (empty($put['groupId'])) {
							result('param');
						}else{
							if (!D('Group')->find($put['groupId'])) {
								result('组不存在');
							}
						}
					}elseif ($put['type']=='AB') {
						if (empty($put['AB'])) {
							result('param');
						}
					}
					/*if (!empty($put['whiteList'])&&is_array($put['whiteList'])) {
						foreach ($put['whiteList'] as  $value) {
							$whiteList[] = $value;
						}
						$put['whiteList'] = json_encode($whiteList,JSON_UNESCAPED_UNICODE);
					}else{
						$put['whiteList'] = '[]';
						$whiteList = array();
					}
					if (!empty($put['blackList'])&&is_array($put['blackList'])) {
						foreach ($put['blackList'] as  $value) {
							$blackList[] = $value;
						}
						$put['blackList'] = json_encode($blackList,JSON_UNESCAPED_UNICODE);
					}else{
						$put['blackList'] = '[]';
						$blackList = array();
					}
					if (!empty($put['desc'])&&is_array($put['desc'])) {
						foreach ($put['desc'] as  $value) {
							$desc[] = $value;
						}
						$put['desc'] = json_encode($desc,JSON_UNESCAPED_UNICODE);
					}else{
						$put['desc'] = '[]';
						$desc = array();
					}
					*/
					$options = array(
						'model'=>$publishModelVersion['model'],
						'vendor_id'=>$publishModelVersion['vendorID'],
						'length'=>intval($publishModelVersion['length']),
						'file_id'=>$publishModelVersion['fileID'],
						'version'=>$publishModelVersion['version'],
						'desc'=>$publishModelVersion['desc'],
						'white_list'=>$publishModelVersion['whiteList'],
						'black_list'=>$publishModelVersion['blackList'],
						'type'=>$put['type'],
						'fake'=>$publishModelVersion['fake'],
						'force_update'=>$publishModelVersion['forceUpdate'],
						'time'=>time(),
					);

					if ($put['type']=='group') {
						$options['group_id'] = $put['groupId'];
					}elseif ($put['type']=='AB') {
						$options['ab'] = $put['AB'];
					}

					if ($publishModelVersion['type'] == $put['type']) {
						//重复删除
						if (!$publishVersion = $this->where("`model`='%s' and `type` = '%s' and vendor_id = '%s' and id !=%d",array($put['model'],$put['type'],$put['vendorID'],$put['id']))->find()) {
							$this->where('id=%d',array($put['id']))->save($options);
							$this->actionPublish($options,'modify');
						}

					}else{
						//重复删除
						if ($publishVersion = $this->where("`model`='%s' and `type` = '%s' and vendor_id = '%s' and id !=%d",array($put['model'],$put['type'],$put['vendorID'],$put['id']))->find()) {
							$this->deletePublishModelVersion($publishVersion['id'],'modify');
							$this->actionPublish($publishVersion,'','覆盖');
						}

						$this->deletePublishModelVersion($put['id'],'modify');
						unset($put['id']);
						$this->add($options);
						$this->actionPublish($publishModelVersion,'delete');
						$this->actionPublish($options,'add');
					}

				}

			}else{
				result('param');
			}
		}
		public function deletePublishModelVersion($id,$action = 'delete')
		{

			/*$publish = $this->find($id);
			var_dump($publish);
			die;*/
			/*$json = getUrl(OTA_ACCESS_ADDR.'access/publish/del?item=ota&id='.$id);
			if ($json) {
				$json = json_decode($json,true);
				if ($json['result'] != 'ok') {
					if (strstr($json['reason'],'not such id')!=true) {
						result($json['reason']);
					}
				}*/
			$publish = $this->find($id);
			if ($publish) {
				$this->deleteModelVersion($id);
				if ($action == 'delete') {
					$this->actionPublish($publish,'delete');
				}

			}

			/*}else{
				result('发布地址出错');
			}*/
			return true;
		}
		public function deleteModelVersion($id)
		{
			if ($this->find($id)) {
				return $this->delete($id);
			}

		}
		/*public function publishModelVersionLists($id = null)
		{
			if ($id === null) {
				$json = getUrl(OTA_ACCESS_ADDR.'access/publish/list?item=ota');
			}else{
				$json = getUrl(OTA_ACCESS_ADDR.'access/publish/list?item=ota&id='.$id);
			}
			if ($json) {
				$json = json_decode($json,true);
				if ($json['result'] != 'ok') {
					if (strstr($res['reason'],'not such id')!=true) {
						result($res['reason']);
					}
				}else{
					echo json_encode($json,JSON_UNESCAPED_UNICODE);

				}
			}else{
				result('发布地址出错');
			}
		}*/
		public function publishModelVersionLists($get)
		{
			if (empty($get['id'])) {
				if (!empty($get['type'])&&($get['type'] == 'authentication')) {
					if (!empty($get['name'])) {
						if (!empty($get['page'])&&!empty($get['pageSize'])) {
							$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
							$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`type` IN ('ALL','AB') AND (`model` like '%".$get['name']."%' or `vendor_id` like  '%".$get['name']."%' or `version` like  '%".$get['name']."%') ")->order('time desc')->select();
						}else{
							$res['extra'] = $this->where("`type` IN ('ALL','AB') AND (`model` like '%".$get['name']."%' or `vendor_id` like  '%".$get['name']."%' or `version` like  '%".$get['name']."%') ")->order('time desc')->select();

						}

						$res['count'] = $this->where("`type` IN ('ALL','AB') AND (`model` like '%".$get['name']."%' or `vendor_id` like  '%".$get['name']."%' or `version` like  '%".$get['name']."%') ")->count();
					}else{
						if (!empty($get['page'])&&!empty($get['pageSize'])) {
							$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
							$res['extra'] = $this->where("`type` IN ('ALL','AB')")->limit($get['page'],$get['pageSize'])->order('time desc')->select();
						}else{
							$res['extra'] = $this->where("`type` IN ('ALL','AB')")->order('time desc')->select();
						}
						$res['count'] = $this->where("`type` IN ('ALL','AB')")->count();
					}
				}elseif (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`model` like '%".$get['name']."%' or `vendor_id` like  '%".$get['name']."%' or `version` like  '%".$get['name']."%' ")->order('time desc')->select();
					}else{
						$res['extra'] = $this->where("`model` like '%".$get['name']."%' or `vendor_id` like  '%".$get['name']."%' or `version` like  '%".$get['name']."%' ")->order('time desc')->select();

					}

					$res['count'] = $this->where("`model` like '%".$get['name']."%' or `vendor_id` like  '%".$get['name']."%' or `version` like  '%".$get['name']."%' ")->count();

				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->order('time desc')->select();
					}else{
						$res['extra'] = $this->order('time desc')->select();
					}
					$res['count'] = $this->count();
				}
				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['desc'] = json_decode($value['desc'],true);
						$res['extra'][$key]['blackList'] = json_decode($value['blackList'],true);
						$res['extra'][$key]['whiteList'] = json_decode($value['whiteList'],true);
						$res['extra'][$key]['time'] = date('Y-m-d H:i:s',$value['time']);
						if ($res['extra'][$key]['type'] == 'ALL') {
							unset($res['extra'][$key]['AB']);
							unset($res['extra'][$key]['groupId']);
						}elseif ($res['extra'][$key]['type'] == 'group') {
							unset($res['extra'][$key]['AB']);
							// unset($res['extra'][$key]['groupId']);
						}elseif ($res['extra'][$key]['type'] == 'AB') {
							unset($res['extra'][$key]['groupId']);
						}
					}
				}
			}else{
				$res['extra'] = $this->find($get['id']);
				$res['count'] = 1;
				$snList = array();
				if (!empty($res['extra'])) {
					if ($res['extra']['type'] == 'group') {
						unset($res['extra']['AB']);
						$groupMembers = D('GroupMembers')->field('sn')->where("`group_id`=%d",array($res['extra']['groupId']))->select();
						if ($groupMembers) {
							foreach ($groupMembers as $key => $value) {
								$snList[] = $value['sn'];
							}
						}
					}
				}
				$res['extra'] = $snList;
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = 0;
			}
			return $res;
		}

		public function actionPublish($put,$action,$reason = null)
		{
			if (isset($put['id'])) {
				unset($put['id']);
			}
			$put['time'] = time();

			if ($reason != null) {
				$put['reason'] = $reason;
			}elseif ($action == 'add') {
				$put['reason'] = '发布';
			}elseif ($action == 'modify') {
				$put['reason'] = '修改';
			}elseif ($action == 'delete') {
				$put['reason'] = '下架';
			}
			D('ModelVersionPublishHistory','Ota')->addModelVersionPublishHistory($put);
		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
	}