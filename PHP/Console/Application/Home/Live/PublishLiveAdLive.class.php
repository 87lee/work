<?php
	namespace Home\Live;
	class PublishLiveAdLive extends \Think\Model
	{
		protected $tableName = 'publish_live_ad';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'adList' =>'ad_list',
			'groupId'  =>'group_id',
			'vendorID'  =>'vendor_id',
			'AB'  =>'ab',
			'updateTime'  =>'update_time',
		);
		public function publishLiveAd($put)
		{

			if (!empty($put['adGroupId'])&&!empty($put['model'])&&!empty($put['vendorID'])&&!empty($put['type'])) {
				if (!$LiveGroupAd = D('LiveAdGroup','Live')->getOneForId($put['adGroupId'])) {
					result('直播广告组不存在');
				}
				if (empty($put['vendorID'])) {
					$put['vendorID'] = 'none';
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
				$time = time();
				$liveAdArr['ad'] = D('LiveAd','Live')->getArrForGroupIdToPublish($put['adGroupId']);
				if (!empty($liveAdArr['ad'])) {
					$liveAdArr['version'] = $time;
				}else{
					$liveAdArr = array(
						'ad'=>array(),
						'version'=>$time
					);
				}
				if ($res = $this->getOneForModelVenderIDType($put['model'],$put['vendorID'],$put['type'])) {
					$this->delectForId($res['id']);
				}

				$options = array(
					"model"=>$put['model'],
					"vendor_id"=>$put['vendorID'],
					"type"=>$put['type'],
					"ab"=>isset($put['AB'])?$put['AB']:'',
					"group_id"=>isset($put['groupId'])?$put['groupId']:'', //json_unescaped_unicode
					"ad_list"=>json_encode($liveAdArr,JSON_UNESCAPED_UNICODE),
					"name"=>$LiveGroupAd['name'],
					'version'=>$time,
					'update_time'=>$time,
					/*
					"interval"=>$LiveAd['interval'],
					"duration"=>$LiveAd['duration'],
					"max_show_times"=>$LiveAd['maxShowTimes'],
					"start_time"=>$LiveAd['startTime'],
					"end_time"=>$LiveAd['endTime'],
					"x"=>$LiveAd['x'],
					"y"=>$LiveAd['y'],
					"width"=>$LiveAd['width'],
					"height"=>$LiveAd['height'],
					"url"=>$LiveAd['url'],
					"channel_list"=>$LiveAd['channelList'],
					*/

				);
				return $this->add($options);
			}else{
				result('param');
			}
		}
		public function modifyPublishLiveAd($put)
		{
			if (/*!empty($put['adId'])&&!empty($put['model'])&&!empty($put['vendorID'])&&*/!empty($put['id'])&&!empty($put['type'])) {

				if (!$res = $this->find($put['id'])) {
					result('发布直播广告不存在');
				}
				/*if ($LiveAd = D('LiveAd','Live')->getOneForId($put['adId'])) {*/
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
						/*if (empty($put['vendorID'])) {
							$put['vendorID'] = 'none';
						}*/
						$options = array(
							/*"model"=>$put['model'],
							"vendor_id"=>$put['vendorID'],*/
							"type"=>$put['type'],
							"ab"=>isset($put['AB'])?$put['AB']:'',
							"group_id"=>isset($put['groupId'])?$put['groupId']:'',
							/*"name"=>$LiveAd['name'],
							"interval"=>$LiveAd['interval'],
							"duration"=>$LiveAd['duration'],
							"max_show_times"=>$LiveAd['maxShowTimes'],
							"start_time"=>$LiveAd['startTime'],
							"end_time"=>$LiveAd['endTime'],
							"x"=>$LiveAd['x'],
							"y"=>$LiveAd['y'],
							"width"=>$LiveAd['width'],
							"height"=>$LiveAd['height'],
							"url"=>$LiveAd['url'],
							"channel_list"=>$LiveAd['channelList'],*/
							'update_time'=>time(),
						);
						return $this->where("id=%d",array($put['id']))->save($options);
					}else{
						$this->delectForId($put['id']);
						$options = array(
							"model"=>$res['model'],
							"vendor_id"=>$res['vendorID'],
							"type"=>$put['type'],
							"ab"=>isset($put['AB'])?$put['AB']:'',
							"group_id"=>isset($put['groupId'])?$put['groupId']:'',
							"name"=>$res['name'],
							'version'=>$res['version'],
							"ad_list"=>$res['adList'],
							'update_time'=>time(),
						);
						$this->add($options);
					}


				/*}else{
					result('直播广告不存在');
				}*/

			}else{
				result('param');
			}
		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
		public function deletePublishLiveAd($put)
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
		public function publishLiveAdLists($get)
		{

			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else {
				if (!empty($get['name'])){
					$where = "    `name` like '%".$get['name']."%' or   `vendor_id` like '%".$get['name']."%' or   `model` like '%".$get['name']."%' ";
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
				$channleArr = D('ChannelList','Live')->getAllGroupByChannleId();

				if (!empty($channleArr)) {
					foreach ($channleArr as $key => $value) {
						$channleNameArr[$value['channelId']] = $value['channelName'];
					}
				}
				if (empty($res['extra'][0])) {

					$res['extra']['adList'] = json_decode($res['extra']['adList'],true);
					if (!empty($res['extra']['adList']['ad'])) {
						foreach ($res['extra']['adList']['ad'] as  $k=>$v) {
							if (!empty($v['channelList'])) {
								$res['extra']['adList']['ad'][$k]['channelList'] = array();
								foreach ($v['channelList'] as  $item) {
									$res['extra']['adList']['ad'][$k]['channelList'][]  =  !empty($channleNameArr[$item])?$channleNameArr[$item]:$item;
								}
							}
						}

					}

					if (!empty($adList['version'])) {
						$res['extra']['adList']['version']=$adList['version'];
					}
					if ($res['extra']['type'] == 'group') {
						unset($res['extra']['AB']);
					}elseif ($put['type'] == 'AB') {
						unset($res['extra']['groupId']);
					}else{
						unset($res['extra']['groupId']);
						unset($res['extra']['AB']);
					}
				}else{
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['adList'] = json_decode($value['adList'],true);

						if (!empty($res['extra'][$key]['adList']['ad'])) {
							foreach ($res['extra'][$key]['adList']['ad'] as  $k=>$v) {
								if (!empty($v['channelList'])) {
									$res['extra'][$key]['adList']['ad'][$k]['channelList'] = array();
									foreach ($v['channelList'] as  $item) {
										$res['extra'][$key]['adList']['ad'][$k]['channelList'][]  =  !empty($channleNameArr[$item])?$channleNameArr[$item]:$item;
									}
								}
							}

						}

						if ($value['type'] == 'group') {
							unset($res['extra'][$key]['AB']);
						}elseif ($value['type'] == 'AB') {
							unset($res['extra'][$key]['groupId']);
						}else{
							unset($res['extra'][$key]['groupId']);
							unset($res['extra'][$key]['AB']);
						}
					}
				}
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
		public function getOneForNameGroupId($name,$groupId)
		{
			return $this->where("name = '%s' and group_id = %d",array($name,$groupId))->find();
		}
		public function getOneForModelVenderIDType($model,$vendorID,$type)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and type = '%s'",array($model,$vendorID,$type))->find();
		}
		public function getOneForModelVenderIDTypeNotID($model,$vendorID,$type,$id)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and type = '%s' and id !=%d",array($model,$vendorID,$type,$id))->find();
		}
	}