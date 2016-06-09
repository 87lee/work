<?php
	namespace Home\Live;
	class LiveListPublishLive extends \Think\Model
	{
		protected $tableName = 'live_list_publish';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'vendorID' =>'vendor_id',
			'groupId'  =>'group_id',
			'nameId'  =>'name_id',
			'AB'  =>'ab',
		);
		public function addListPublish($put)
		{
			if (!empty($put['model'])&&!empty($put['vendorID'])&&!empty($put['nameId'])&&!empty($put['type'])) {

				if ($put['type'] == 'group') {
					if ( !isset($put['groupId']) || !D('Group')->getValOneForId($put['groupId']) ) {
						result('param');
					}
					if (isset($put['AB'])) {
						$put['AB'] = '';
					}
				}elseif ($put['type'] == 'AB') {
					if (!isset($put['AB'])) {
						result('param');
					}

					if (empty($put['AB'])) {
						$put['AB'] = 0;
					}else{
						$put['AB'] = (int)$put['AB'];
					}
					if (isset($put['groupId'])) {
						$put['groupId'] = '';
					}
				}else{
					if ($put['type'] != 'ALL') {
						result('param');
					}
					if (isset($put['AB'])) {
						$put['AB'] = '';
					}
					if (isset($put['groupId'])) {
						$put['groupId'] = '';
					}
				}

				if ($res = $this->getOneForModelVenderIDType($put['model'],$put['vendorID'],$put['type'])) {
					$this->delectForId($res['id']);
				}
				if (isset($put['id'])) {
					unset($put['id']);
				}
				$put['time'] = time();
				$this->create($put);
				$this->add();
			}else{
				result('param');
			}
		}
		public function modifyListPublish($put)
		{
			if (!empty($put['id'])&&!empty($put['type'])) {
				if (!$res = $this->find($put['id'])) {
					result('该直播列表不存在');
				}
				if ($put['type'] == 'group') {
					if ( !isset($put['groupId']) || !D('Group')->getValOneForId($put['groupId']) ) {
						result('param');
					}
					if (isset($put['AB'])) {
						$put['AB'] = '';
					}
				}elseif ($put['type'] == 'AB') {
					if (!isset($put['AB'])) {
						result('param');
					}

					if (empty($put['AB'])) {
						$put['AB'] = 0;
					}else{
						$put['AB'] = (int)$put['AB'];
					}
					if (isset($put['groupId'])) {
						$put['groupId'] = '';
					}
				}else{
					if ($put['type'] != 'ALL') {
						result('param');
					}
					if (isset($put['AB'])) {
						$put['AB'] = '';
					}
					if (isset($put['groupId'])) {
						$put['groupId'] = '';
					}
				}
				if ($isRes = $this->getOneForModelVenderIDTypeNotID($res['model'],$res['vendorID'],$put['type'],$put['id'])) {
					$this->delectForId($isRes['id']);
				}
				if ($res['type'] == $put['type']) {
					$put['time'] = time();
					$options = $this->create($put);
					$this->where("id=%d",array($put['id']))->save();
				}else{
					$this->delectForId($put['id']);
					$options = array(
						'model'=>$res['model'],
						'vendor_id'=>$res['vendorID'],
						'name_id'=>$res['nameId'],
						'group_id'=>isset($put['groupId'])?$put['groupId']:'',
						'ab'=>isset($put['AB'])?$put['AB']:'',
						'type'=>$put['type'],
						'time'=>time(),
					);
					$this->add($options);
				}
			}else{
				result('param');
			}
		}
		public function deleteListPublish($put)
		{
			if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode($arr, ',');
					if ($res = $this->where("id IN (".$sqlId.")")->select()) {

						return $this->where("id IN (".$sqlId.")")->delete();
					}
				}
			}else{
				result('param');
			}
		}
		public function delectForId($id)
		{
			if ($this->find($id)) {
				$this->where("id =%d",array($id))->delete();
			}
		}
		public function listPublishLists($get)
		{
			$listNameArr = D('LiveListName','Live')->getListName();

			if (!empty($listNameArr['extra'])) {
				foreach ($listNameArr['extra'] as  $value) {
					$data[$value['nameId']] = $value['name'];
				}
			}
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else{
				$where = "";
				if (!empty($get['name'])){
					$where = "   `model` like '%".$get['name']."%'  or `vendor_id`  like '%".$get['name']."%' ";
				}

				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				if (!empty($data)) {

				}
				if (empty($res['extra'][0])) {
					if ($res['extra']['type'] == 'group') {
						unset($res['extra']['AB']);
					}elseif ($put['type'] == 'AB') {
						unset($res['extra']['groupId']);
					}else{
						unset($res['extra']['groupId']);
						unset($res['extra']['AB']);
					}
					$res['extra']['name'] = isset($data[$res['extra']['nameId']])?$data[$res['extra']['nameId']]:$res['extra']['nameId'];
					unset($res['extra']['nameId']);
				}else{
					foreach ($res['extra'] as $key => $value) {
						if ($value['type'] == 'group') {
							unset($res['extra'][$key]['AB']);
						}elseif ($value['type'] == 'AB') {
							unset($res['extra'][$key]['groupId']);
						}else{
							unset($res['extra'][$key]['groupId']);
							unset($res['extra'][$key]['AB']);
						}
						$res['extra'][$key]['name'] = isset($data[$value['nameId']])?$data[$value['nameId']]:$value['nameId'];
						unset($res['extra'][$key]['nameId']);
					}
				}
			}
			return $res;
		}
		public function getOneForModelVenderIDType($model,$vendorID,$type)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and type = '%s'",array($model,$vendorID,$type))->find();
		}
		public function getOneForModelVenderIDTypeNotID($model,$vendorID,$type,$id)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and type = '%s' and id !=%d",array($model,$vendorID,$type,$id))->find();
		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
		/*public function userActionLists($get)
		{
			if (empty($get['id'])) {
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->limit($get['page'],$get['pageSize'])->where("`user` like '%".$get['name']."%' or `interface` like  '%".$get['name']."%' ")->order("time desc")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->where("`user` like '%".$get['name']."%' or `interface` like  '%".$get['name']."%' ")->order("time desc")->select();
					}
					$res['count'] = $this->where("`user` like '%".$get['name']."%'  or `interface` like  '%".$get['name']."%' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->limit($get['page'],$get['pageSize'])->order("time desc")->select();
					}else{
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->order("time desc")->select();
					}
					$res['count'] = $this->count();
				}

			}else{
				$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->find($get['id']);
				$res['count'] =1;

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = 0;
			}
			return $res;
		}*/
	}