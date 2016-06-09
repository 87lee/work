<?php
	namespace Home\Live;
	class LiveAdLive extends \Think\Model
	{
		protected $tableName = 'live_ad';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'maxShowTimes' =>'max_show_times',
			'groupId'  =>'group_id',
			'startTime'  =>'start_time',
			'endTime'  =>'end_time',
			'channelList'  =>'channel_list',
			'screenSize'  =>'screen_size',
		);
		public function addLiveAd($put)
		{

			if (isset($put['groupId'])&&!empty($put['name'])/*&&isset($put['interval'])&&isset($put['duration'])&&isset($put['maxShowTimes'])&&isset($put['startTime'])&&isset($put['endTime'])&&isset($put['x'])&&isset($put['y'])&&isset($put['width'])&&isset($put['height'])&&!empty($put['url'])&&isset($put['channelList'])*/) {
				if (D('LiveAdGroup','Live')->getOneForId($put['groupId'])) {
					if (!$this->getOneForNameGroupId($put['name'],$put['groupId'])) {

						if (is_array($put['channelList']) && !empty($put['channelList'])) {
							foreach ($put['channelList'] as  $value) {
								if (empty($value['name'])|| empty($value['id'])) {
									$channelList = array();
									break;
								}else{
									$channelList[] = array(
										'name'=>$value['name'],
										'id'=>$value['id'],
									);
								}
							}
							if (!empty($channelList)) {
								$put['channelList'] = json_encode($channelList,JSON_UNESCAPED_UNICODE);
							}else{
								$put['channelList'] = "[]";
							}
						}else{
							$put['channelList'] = "[]";
						}
						$options = array(
							"group_id"=>(int)$put['groupId'],
							"name"=>$put['name'],
							"interval"=>isset($put['interval'])?intval($put['interval']):0,
							"duration"=>isset($put['duration'])?intval($put['duration']):0,
							"max_show_times"=>isset($put['maxShowTimes'])?intval($put['maxShowTimes']):0,
							"start_time"=>isset($put['startTime'])?intval($put['startTime']):'00:00:00',
							"end_time"=>isset($put['endTime'])?intval($put['endTime']):'00:00:00',
							"x"=>isset($put['x'])?intval($put['x']):0,
							"y"=>isset($put['y'])?intval($put['y']):0,
							"width"=>isset($put['width'])?intval($put['width']):0,
							"height"=>isset($put['height'])?intval($put['height']):0,
							'screen_size'=>isset($put['screenSize'])?intval($put['screenSize']):'1280',
							"url"=>isset($put['url'])?$put['url']:'',
							"channel_list"=>$put['channelList'],
							'time'=>time(),
						);

						return $this->add($options);
					}else{
						result('该直播广告已存在直播广告组');
					}
				}else{
					result('直播广告组不存在');
				}

			}else{
				result('param');
			}
		}
		public function modifyLiveAd($put)
		{
			if (!empty($put['id'])&&($put['screenSize'] == '1280' || $put['screenSize'] == '1920')&&!empty($put['name'])&&isset($put['interval'])&&isset($put['duration'])&&isset($put['maxShowTimes'])&&isset($put['startTime'])&&isset($put['endTime'])&&isset($put['x'])&&isset($put['y'])&&isset($put['width'])&&isset($put['height'])&&!empty($put['url'])&&isset($put['channelList'])) {

				if ($res = $this->find($put['id'])) {
					if (!$this->getOneForNameNotId($put['name'],$put['id'])) {
						if (is_array($put['channelList']) && !empty($put['channelList'])) {
							foreach ($put['channelList'] as  $value) {
								if (empty($value['name'])|| empty($value['id'])) {
									$channelList = array();
									break;
								}else{
									$channelList[] = array(
										'name'=>$value['name'],
										'id'=>$value['id'],
									);
								}
							}
							if (!empty($channelList)) {
								$put['channelList'] = json_encode($channelList,JSON_UNESCAPED_UNICODE);
							}else{
								$put['channelList'] = "[]";
							}
						}else{
							$put['channelList'] = "[]";
						}
						$options = array(
							"name"=>$put['name'],
							"interval"=>(int)$put['interval'],
							"duration"=>(int)$put['duration'],
							"max_show_times"=>(int)$put['maxShowTimes'],
							"start_time"=>$put['startTime'],
							"end_time"=>$put['endTime'],
							"x"=>(int)$put['x'],
							"y"=>(int)$put['y'],
							"width"=>(int)$put['width'],
							"height"=>(int)$put['height'],
							'screen_size'=>(int)$put['screenSize'],
							"url"=>$put['url'],
							"channel_list"=>$put['channelList'],
							'time'=>time(),
						);
						return $this->where("id=%d",array($put['id']))->save($options);
					}else{
						result('该直播广告已存在直播广告组');
					}
				}else{
					result('直播广告不存在');
				}

			}else{
				result('param');
			}
		}
		public function deleteLiveAd($put)
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
		public function liveAdLists($get)
		{
			$fieldNot = 'group_id';
			if (!empty($get['id'])) {
				$res['extra'] = $this->field($fieldNot,true)->find($get['id']);
				if ($res['extra']) {
					$res['count'] = '1';
				}
			}elseif (!empty($get['groupId'])) {
				$where = "group_id = ".(int)$get['groupId'];
				if (!empty($get['name'])){
					$where .= "  AND  `name` like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field($fieldNot,true)->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->field($fieldNot,true)->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				if (empty($res['extra'][0])) {
					$res['extra']['channelList'] = json_decode($res['extra']['channelList'],true);
				}else{
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['channelList'] = json_decode($value['channelList'],true);
					}
				}
			}
			return $res;
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s' ",array($name))->find();
		}
		public function getOneForGroupId($GroupId)
		{
			return $this->where("group_id = %d ",array($GroupId))->find();
		}
		public function getOneForId($id)
		{
			return $this->where("id = '%s' ",array($id))->find();
		}
		public function getOneForNameNotID($name,$id)
		{
			return $this->where("name = '%s' and  id !=%d",array($name,$id))->find();
		}
		public function getOneForNameGroupId($name,$groupId)
		{
			return $this->where("name = '%s' and group_id = %d",array($name,$groupId))->find();
		}
		public function getArrForGroupIdToPublish($GroupId)
		{
			$field = 'id,start_time,width,duration,end_time,x as posX,max_show_times,height,y as posY,url,channel_list,name,interval';
			$res = $this->field($field)->where("group_id = %d ",array($GroupId))->select();
			if (!empty($res)) {
				foreach ($res as $key => $value) {

					$res[$key]['channelList'] = array();
					$channelList = json_decode($value['channelList'],true);
					if (!empty($channelList)) {
						foreach ($channelList as $k => $v) {
							$res[$key]['channelList'][] = $v['id'];
						}
					}

				}
			}
			return $res;
		}
	}