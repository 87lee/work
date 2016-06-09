<?php
	namespace Home\App;
	class MoreAppPublishWhiteBlackApp extends \Think\Model
	{
		protected $tableName = 'more_app_publish_white_black';
		protected $connection = 'DB_APP';
		protected $_map = array(
			'vendorID' =>'vendor_id',
			'groupId' =>'group_id',
			'whiteList' =>'white_list',
			'blackList' =>'black_list',
		);

		public function publishAppWhiteBlack($put)
		{
			if (!empty($put['model'])&&($put['type'] == 'ALL' || $put['type'] == 'group')) {
				if ($publishTypt = $this->where('model = "%s" and vendor_id = "%s" and type = "%s"',array($put['model'],$put['vendorID'],$put['type']))->find()) {
					$this->where('id = %d',array($publishTypt['id']))->delete();
				}
				$whiteList = array();
				$blackList = array();
				if ($put['type'] == 'group') {
					if (!empty($put['groupId'])) {
						if (!D('Group')->getValOneForId($put['groupId'])) {
							result('内测组不存在');
						}
					}else{
						result('param');
					}
				}

				if (!empty($put['whiteList'])&&is_array($put['whiteList'])) {
					foreach ($put['whiteList'] as $key => $value) {
						if (isset($value['appName'])&&isset($value['pkgName'])) {
							$whiteList[] = array(
								'appName'=>$value['appName'],
								'pkgName'=>$value['pkgName']
							);
						}
					}
				}
				if (!empty($put['blackList'])&&is_array($put['blackList'])) {
					foreach ($put['blackList'] as $key => $value) {
						if (isset($value['appName'])&&isset($value['pkgName'])) {
							$blackList[] = array(
								'appName'=>$value['appName'],
								'pkgName'=>$value['pkgName']
							);
						}
					}
				}
				$put['vendorID'] = !empty($put['vendorID'])?$put['vendorID']:'none';
				$data = array(
					'model'=>$put['model'],
					'vendor_id'=>$put['vendorID'],
					'type'=>$put['type'],
					'group_id'=>!empty($put['groupId'])?$put['groupId']:'0',
					'white_list'=>json_encode($whiteList,JSON_UNESCAPED_UNICODE),
					'black_list'=>json_encode($blackList,JSON_UNESCAPED_UNICODE),
					'time'=>time(),
				);
				//存在删除
				if ($publishTypt = $this->where('model = "%s" and vendor_id = "%s" and type = "%s"',array($put['model'],$put['vendorID'],$put['type']))->find()) {
					$this->where('id = %d',array($publishTypt['id']))->delete();
				}
				return $this->add($data);

			}else{
				result('param');
			}
		}
		public function modifyAppWhiteBlack($put)
		{
			if (!empty($put['id'])&&($put['type'] == 'ALL' || $put['type'] == 'group')) {

				if ($res = $this->find($put['id'])) {

					$whiteList = array();
					$blackList = array();
					if ($put['type'] == 'group') {
						if (!empty($put['groupId'])) {
							if (!D('Group')->getValOneForId($put['groupId'])) {
								result('内测组不存在');
							}
						}else{
							result('param');
						}
					}

					if (!empty($put['whiteList'])&&is_array($put['whiteList'])) {
						foreach ($put['whiteList'] as $key => $value) {
							if (isset($value['appName'])&&isset($value['pkgName'])) {
								$whiteList[] = array(
									'appName'=>$value['appName'],
									'pkgName'=>$value['pkgName']
								);
							}
						}
					}
					if (!empty($put['blackList'])&&is_array($put['blackList'])) {
						foreach ($put['blackList'] as $key => $value) {
							if (isset($value['appName'])&&isset($value['pkgName'])) {
								$blackList[] = array(
									'appName'=>$value['appName'],
									'pkgName'=>$value['pkgName']
								);
							}
						}
					}
					$res['vendorID'] = !empty($res['vendorID'])?$res['vendorID']:'none';
					$data = array(
						'model'=>$res['model'],
						'vendor_id'=>$res['vendorID'],
						'type'=>$put['type'],
						'group_id'=>!empty($put['groupId'])?$put['groupId']:'0',
						'white_list'=>json_encode($whiteList,JSON_UNESCAPED_UNICODE),
						'black_list'=>json_encode($blackList,JSON_UNESCAPED_UNICODE),
						'time'=>time(),
					);
					$publishTypt = $this->where('model = "%s" and vendor_id = "%s" and type = "%s" and `id` != %d',array($res['model'],$res['vendorID'],$put['type'],$put['id']))->find();

					if ($res['type'] == $put['type']) {
						if (!$publishTypt) {
							$this->where('id = %d',array($put['id']))->save($data);
						}else{
							result('厂商型号已发布');
						}

					}else{
						if ($publishTypt) {
							$this->where('id = %d',array($publishTypt['id']))->delete();
						}
						$this->where('id = %d',array($put['id']))->delete();
						return $this->add($data);
					}
				}else{
					result('厂商型号未发布');
				}


			}else{
				result('param');
			}
		}
		public function deleteAppWhiteBlack($get)
		{
			if (!empty($get['id'])) {
				if ($this->where('id = %d',array($get['id']))->find()) {
					$this->where('id = %d',array($get['id']))->delete();
				}
			}

		}
		public function appWhiteBlackLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}


			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `model` like '%".$get['name']."%'  or `vendor_id` like '%".$get['name']."%'")->select();

				}else{
					$res['extra'] = $this->where(" `model` like '%".$get['name']."%'  or `vendor_id` like '%".$get['name']."%'")->select();
				}
				$res['count'] = $this->where(" `model` like '%".$get['name']."%'  or `vendor_id` like '%".$get['name']."%'")->count();
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
				if (empty($res['extra'][0])&&!is_array($res['extra'][0])) {


					$res['extra']['whiteList'] = !empty($whiteList = json_decode($res['extra']['whiteList'],true))?$whiteList:array();
					$res['extra']['blackList'] = !empty($blackList = json_decode($res['extra']['blackList'],true))?$blackList:array();
					$res['extra']['time'] = date('Y-m-d H:i:s',!empty($res['extra']['time'])?$res['extra']['time']:time());
				}else{
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['whiteList'] = !empty($whiteList = json_decode($value['whiteList'],true))?$whiteList:array();
						$res['extra'][$key]['blackList'] = !empty($blackList = json_decode($value['blackList'],true))?$blackList:array();
						$res['extra'][$key]['time'] = date('Y-m-d H:i:s',!empty($value['time'])?$value['time']:time());
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