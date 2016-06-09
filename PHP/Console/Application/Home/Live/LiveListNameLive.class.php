<?php
	namespace Home\Live;
	class LiveListNameLive extends \Think\Model
	{
		protected $tableName = 'live_list_name';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'nameId' =>'name_id',
		);
		protected $_validate = array(
			array('name_id','','ID已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function getLiveListNameLists($get)
		{
			$where= '';
			if (!empty($get['id'])) {
				$where= "name_id ='".$get['id']."'";
			}
			return $this->where($where)->select();
		}
		public function deleteLiveListNameArrForNameIDStr($IDStr)
		{
			if ($this->where("name_id IN (".$IDStr.")")->find()) {
				return $this->where("name_id IN (".$IDStr.")")->delete();
			}
		}
		public function addLiveListNameArr($put)
		{
			foreach ($put as  $value) {

				if ($data = $this->create( $value)) {
					$options[] = $data;
				}
			}
			if (!empty($options)) {
				return $this->addAll($options);
			}
		}

		public function addLiveListName($put)
		{

			if ($this->create($put)) {
				$this->add();
			}
			return;

		}
		public function getListName()
		{
			$res['extra'] = $this->field("id",true)->select();
			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			return $res;
		}
		public function deleteLiveForNoNameIdStr($NameIdStr)
		{
			if ($this->where("name_id NOT IN (".$NameIdStr.") ")->find()) {
				$this->where("name_id NOT IN (".$NameIdStr.") ")->delete();
			}
		}
		/*public function deleteLiveAdGroup($get)
		{
			if (!empty($get['id'])) {
				if ($this->find($get['id'])) {
					if (D('LiveAd','Live')->getOneForGroupId($get['id'])) {
						result('该直播广告组下有广告');
					}
					$this->where("id = %d",array($get['id']))->delete();
				}
			}
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