<?php
	namespace Home\Live;
	class LiveAdGroupLive extends \Think\Model
	{
		protected $tableName = 'live_ad_group';
		protected $connection = 'DB_LIVE';
		/*protected $_map = array(
			'vendorID' =>'vendor_id',
			'groupId'  =>'group_id',
			'AB'  =>'ab',
		);*/
		public function addLiveAdGroup($put)
		{
			if (!empty($put['name'])) {

				if (!$this->getOneForName($put['name'])) {
					$options = array(
						"name"=>$put['name'],
						'desc'=>isset($put['desc'])?$put['desc']:''
					);
					$this->create($options);
					return $this->add();
				}else{
					result('该直播广告组已存在');
				}
			}else{
				result('param');
			}
		}
		public function modifyLiveAdGroup($put)
		{
			if (!empty($put['id'])&&!empty($put['name'])) {
				if (!$this->find($put['id'])) {
					result('该直播广告组不存在');
				}
				if (!$this->getOneForNameNotID($put['name'],$put['id'])) {

					$this->create($put);
					$this->where("id=%d",array($put['id']))->save();
				}else{
					result('该直播广告组已存在');
				}
			}
		}
		public function deleteLiveAdGroup($get)
		{
			if (!empty($get['id'])) {
				if ($this->find($get['id'])) {
					if (D('LiveAd','Live')->getOneForGroupId($get['id'])) {
						result('该直播广告组下有广告');
					}
					$this->where("id = %d",array($get['id']))->delete();
				}
			}
			/*if (!empty($put) && is_array($put)) {
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
			}*/
		}
		public function liveAdGroupLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else{
				$where = "";
				if (!empty($get['name'])){
					$where = "   `name` like '%".$get['name']."%'";
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
			}
			return $res;
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s' ",array($name))->find();
		}
		public function getOneForNameNotID($name,$id)
		{
			return $this->where("name = '%s' and  id !=%d",array($name,$id))->find();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
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