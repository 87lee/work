<?php
	namespace Home\Silent;
	class PublishSilentSilent extends \Think\Model
	{
		protected $tableName = 'publish_silent';
		protected $connection = 'DB_SILENT';
		protected $_map = array(
			'vendorID' =>'vendor_id',
			'groupName'  =>'group_name',
			'groupId' =>'group_id',
			'startActive'  =>'start_active',
			'endActive'  =>'end_active',
			'pubRange'  =>'pub_range',
			'groupContent'  =>'group_content',
			'AB'  =>'ab',
		);
		/*protected $_validate = array(
			array('name','','静默安装组已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

// {"groupId":"静默安装组ID","model": "ALL","vendorID": "none","type": "ALL/AB/group","groupId": "内测组ID","ab": "灰度值","startActive": "05:00:00","endActive": "05:00:00","idle": "05:00:00","duration": "05:00:00","pubRange": "jbk/unjbk/all"}
		public function publishSilent($put)
		{
			if (!empty($put['silentGroupId'])&&!empty($put['model'])&&!empty($put['vendorID'])&&!empty($put['type'])&&($put['pubRange']=='jbk' || $put['pubRange']=='unjbk' || $put['pubRange']=='all' )) {

				if ($put['type'] == 'group') {
					if (!empty($put['groupId'])) {
						if (!D('Group')->getValOneForId($put['groupId'])) {
							result('内测组不存在');
						}
					}else{
						result('param');
					}
				}elseif ($put['type'] == 'AB') {
					if (!isset($put['AB'])) {
						result('param');
					}
				}else{
					if ($put['type'] != 'ALL') {
						result('param');
					}
				}
				if (D('AppGroupItems','Silent')->getPublisArrForGroupIdAction($put['silentGroupId'],'active')) {
					if (empty($put['startActive']) || empty($put['endActive']) || empty($put['idle']) || empty($put['duration'])) {
						result('请填写激活信息');
					}
				}
				$put['model'] = isset($put['model'])?$put['model']:'ALL';
				$put['vendorID'] = isset($put['vendorID'])?$put['vendorID']:'none';

				$appGroup = D('AppGroup','Silent')->getValOneForId($put['silentGroupId']);

				if (  $appGroupItems = D('AppGroupItems','Silent')->getPublisArrForGroupId($put['silentGroupId'])) {


					if ($publishSilent = $this->where("model='%s'  and vendor_id='%s' and type='%s'  ",array($put['model'],$put['vendorID'],$put['type']))->find()) {
						$this->where("id = %d" ,array($publishSilent['id']))->delete();
					}
					$options = array(
						'model'=>$put['model'],
						'vendor_id'=>$put['vendorID'],
						'group_name'=>$appGroup['name'],
						'version'=>time(),
						'type'=>$put['type'],
						'group_id'=>isset($put['groupId'])?$put['groupId']:'0',
						'ab'=>isset($put['AB'])?intval($put['AB']):'',
						'start_active'=>isset($put['startActive'])?$put['startActive']:'',
						'end_active'=>isset($put['endActive'])?$put['endActive']:'',
						'idle'=>isset($put['idle'])?$put['idle']:'',
						'duration'=>isset($put['duration'])?$put['duration']:'',
						'pub_range'=>$put['pubRange'],
						'group_content'=>json_encode($appGroupItems,JSON_UNESCAPED_UNICODE),
					);
					return $this->add($options);

				}else{
					result('该静默安装组为空');
				}
			}else{
				result('param');
			}

		}
		public function modifyPublishSilent($put)
		{

			if (!empty($put['id'])&&!empty($put['type'])&&($put['pubRange']=='jbk' || $put['pubRange']=='unjbk' || $put['pubRange']=='all' )) {

				if (!$publishOne = $this->find($put['id'])) {
					result('该发布不存在');
				}
				if ($put['type'] == 'group') {
					if (!empty($put['groupId'])) {
						if (!D('Group')->getValOneForId($put['groupId'])) {
							result('内测组不存在');
						}
					}else{
						result('param');
					}
				}elseif ($put['type'] == 'AB') {
					if (!isset($put['AB'])) {
						result('param');
					}
				}else{
					if ($put['type'] != 'ALL') {
						result('param');
					}
				}

				/*if (D('AppGroupItems','Silent')->getPublisArrForGroupIdAction($put['silentGroupId'],'active')) {
					if (empty($put['startActive']) || empty($put['endActive']) || empty($put['idle']) || empty($put['duration'])) {
						result('请填写激活信息');
					}
				}

				$put['model'] = isset($put['model'])?$put['model']:'ALL';
				$put['vendorID'] = isset($put['vendorID'])?$put['vendorID']:'none';

				$appGroup = D('AppGroup','Silent')->getValOneForId($put['silentGroupId']);
				*/

				$options = array(
					'model'=>$publishOne['model'],
					'vendor_id'=>$publishOne['vendorID'],
					'group_name'=>$publishOne['groupName'],
					'version'=>time(),
					'type'=>$put['type'],
					'group_id'=>isset($put['groupId'])?$put['groupId']:'0',
					'ab'=>isset($put['AB'])?intval($put['AB']):'0',
					'start_active'=>$publishOne['startActive'],
					'end_active'=>$publishOne['endActive'],
					'idle'=>$publishOne['idle'],
					'duration'=>$publishOne['duration'],
					'pub_range'=>$put['pubRange'],
					'group_content'=>$publishOne['groupContent'],
				);


				if ($publishOne['type'] ==  $put['type']) {
					if (!$publishSilent = $this->where("model='%s'  and vendor_id='%s' and type='%s' and id !=%d",array($publishOne['model'],$publishOne['vendorID'],$put['type'],$put['id']))->find()) {
						$this->where("id = %d" ,array($put['id']))->save($options);
					}else{
						$this->where("id IN (".$publishSilent['id'].",".$put['id'].")" ,array())->delete();
						$this->add($options);
					}

				}else{
					$this->where("id = %d" ,array($put['id']))->delete();
					if ($publishSilent = $this->where("model='%s'  and vendor_id='%s' and type='%s' and id !=%d",array($publishOne['model'],$publishOne['vendorID'],$put['type'],$put['id']))->find()) {
						$this->where("id = %d" ,array($publishSilent['id']))->delete();
					}

					$this->add($options);
				}
			}else{
				result('param');
			}
		}

		/*public function deleteForGroupId($groupId)
		{
			if ($this->where('group_id = %d',array($groupId))->find()) {
				$this->where('group_id = %d',array($groupId))->delete();
			}
		}*/
		public function deletePublishSilent($id = null)
		{
			if ($id !=null) {
				$res = $this->find($id);
				if ($res) {
					$this->delete($id);
					return true;
				}else{
					result('静默安装组不存在');
				}
			}
		}
		public function publishSilentLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else{
				$where = '';
				if (!empty($get['name'])) {
					$where = " `model` like '%".$get['name']."%' or `group_name`  like '%".$get['name']."%' or vendor_id  like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						$isActive = false;
						$res['extra'][$key]['groupContent'] = json_decode($value['groupContent'],true);
						foreach ($res['extra'][$key]['groupContent'] as  $val) {
							if ($val['action'] == 'active') {
								$isActive = true;
								break;
							}
						}
						if (!$isActive) {
							unset($res['extra'][$key]['startActive']);
							unset($res['extra'][$key]['endActive']);
							unset($res['extra'][$key]['idle']);
							unset($res['extra'][$key]['duration']);
						}

						if ($res['extra'][$key]['type'] == 'group') {
							unset($res['extra'][$key]['AB']);
						}elseif ($res['extra'][$key]['type'] == 'AB') {
							unset($res['extra'][$key]['groupId']);
						}else{
							unset($res['extra'][$key]['AB']);
							unset($res['extra'][$key]['groupId']);
						}
					}
				}else{
					$res['extra']['groupContent'] = json_decode($res['extra']['groupContent'],true);

					$isActive = false;
					foreach ($res['extra']['groupContent'] as  $val) {
						if ($val['action'] == 'active') {
							$isActive = true;
							break;
						}
					}
					if (!$isActive) {
						unset($res['extra']['startActive']);
						unset($res['extra']['endActive']);
						unset($res['extra']['idle']);
						unset($res['extra']['duration']);
					}
					if ($res['extra']['type'] == 'group') {
							unset($res['extra']['AB']);
						}elseif ($res['extra']['type'] == 'AB') {
							unset($res['extra']['groupId']);
						}else{
							unset($res['extra']['AB']);
							unset($res['extra']['groupId']);
						}
				}
			}

			return $res;
		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
	}