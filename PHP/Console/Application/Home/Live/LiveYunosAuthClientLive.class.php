<?php
	namespace Home\Live;
	class LiveYunosAuthClientLive extends \Think\Model
	{
		protected $tableName = 'yunos_auth_client';
		protected $connection = 'DB_LIVE_AUTH_DETAIL';
		protected $_map = array(
			'vendorID' =>'vendor_id',
			'yunosDevice'  =>'yunos_device',
			'yunosChip'  =>'yunos_chip',
			'yunosModel'  =>'yunos_model',
			'yunosVendor'  =>'yunos_vendor',
		);
		public function getLiveAuthDetail($get,$put)
		{
			$where = '';
			if (!empty($put['model'])) {
				$where = "model = '" . $put['model'] . "'";
			}
			if (!empty($put['vendorID'])) {
				if (!empty($where)) {
					$where .= "  AND vendor_id = '" . $put['vendorID'] . "'";
				}else{
					$where = "vendor_id = '" . $put['vendorID'] . "'";
				}

			}
			if (!empty($put['ip'])) {
				if (!empty($where)) {
					$where .= "  AND ip = '" . $put['ip'] . "'";
				}else{
					$where = "ip = '" . $put['ip'] . "'";
				}

			}
			if (!empty($put['sn'])) {
				if (!empty($where)) {
					$where .= "  AND sn = '" . $put['sn'] . "'";
				}else{
					$where = "sn = '" . $put['sn'] . "'";
				}

			}
			if (!empty($put['mac'])) {
				if (!empty($where)) {
					$where .= "  AND mac = '" . $put['mac'] . "'";
				}else{
					$where = "mac = '" . $put['mac'] . "'";
				}
			}
			if (!empty($put['wifi'])) {
				if (!empty($where)) {
					$where .= "  AND wifi = '" . $put['wifi'] . "'";
				}else{
					$where = " wifi = '" . $put['wifi'] . "'";
				}
			}
			if (!empty($put['channel'])) {

				if (!empty($where)) {
					$where .= "  AND channel = '" . $put['channel'] . "'";
				}else{
					$where = "channel = '" . $put['channel'] . "'";
				}
			}

			if (!empty($put['startTime']) && !empty($put['endTime']) ) {
				if (!empty($where)) {
					$where .= "  AND  time >= '" . $put['startTime'] . "'  AND  time <='" .$put['endTime']. "'";
				}else{
					$where = "time >= '" . $put['startTime'] . "'  AND  time <='" .$put['endTime']. "'";
				}
			}elseif (!empty($put['startTime'])) {
				if (!empty($where)) {
					$where .= "  AND  time >= '" . $put['startTime'] . "'";
				}else{
					$where = "time >= '" . $put['startTime'] . "'";
				}
			}elseif (!empty($put['endTime'])) {
				if (!empty($where)) {
					$where .= "  AND  time <= '" . $put['endTime'] . "'";
				}else{
					$where = "time <= '" . $put['endTime'] . "'";
				}
			}
			$order = 'time desc';
			$noField = 'id';
			if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$res['extra'] = $this->field($noField,true)->limit($get['page'],$get['pageSize'])->where($where)->order($order)->select();

			}else{
				$res['extra'] = $this->field($noField,true)->where($where)->order($order)->select();

			}
			$res['count'] = $this->where($where)->count();

			return $res;
		}
		/*
		public function modifyLiveBootad($get)
		{
			if (!empty($get['id'])&&!empty($get['type'])) {
				if (!$res = $this->find($get['id'])) {
					result('该直播开机画面不存在');
				}

				if ($get['type'] == 'group') {
					if ( !isset($get['groupId']) || !D('Group')->getValOneForId($get['groupId']) ) {
						result('param');
					}

					if (isset($get['AB'])) {
						$get['AB'] = '';
					}
					$options['group_id'] = $get['groupId'];
				}elseif ($get['type'] == 'AB') {
					if (!isset($get['AB'])) {
						result('param');
					}
					$options['ab'] = $get['AB'];
					if (empty($get['AB'])) {
						$get['AB'] = 0;
					}else{
						$get['AB'] = (int)$get['AB'];
					}
					if (isset($get['groupId'])) {
						$get['groupId'] = '';
					}
				}else{
					if ($get['type'] != 'ALL') {
						result('param');
					}
					if (isset($get['AB'])) {
						$get['AB'] = '';
					}
					if (isset($get['groupId'])) {
						$get['groupId'] = '';
					}
				}
				if ($isRes = $this->getOneForModelVenderIDTypeNotID($res['model'],$res['vendorID'],$get['type'],$get['id'])) {
					$this->delectForId($isRes['id']);
				}
				if ($res['type'] == $get['type']) {
					$options['type'] = $get['type'];
					$options['version'] = time();
					$options = $this->create($options);
					$this->where("id=%d",array($get['id']))->save();
				}else{
					$this->delectForId($get['id']);
					$options = array(
						'model'=>$res['model'],
						'vendor_id'=>$res['vendorID'],
						'name'=>$res['name'],
						'show_time'=>$res['showTime'],
						'is_skip'=>$res['isSkip'],
						'md5'=>$res['md5'],
						'url'=>$res['url'],
						'white_list'=>$res['whiteList'],
						'black_list'=>$res['blackList'],
						'group_id'=>isset($get['groupId'])?$get['groupId']:'',
						'ab'=>isset($get['AB'])?$get['AB']:'',
						'type'=>$get['type'],
						'version' => time()
					);
					$this->add($options);

				}
			}else{
				result('param');
			}
		}
		public function deleteLiveBootad($get)
		{
			if (!empty($get) && is_array($get)) {
				foreach ($get as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode(',',$arr);
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
		public function liveBootadLists($get)
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
					}elseif ($get['type'] == 'AB') {
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

		public function userActionLists($get)
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