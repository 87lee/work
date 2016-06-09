<?php
	namespace Home\Desktop;
	class PushMessageDesktop extends \Think\Model
	{
		protected $tableName = 'push_message';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'groupId' =>'group_id',
			"vendorID"=>"vendorid",
			'whiteList' =>'white_list',
			"blackList"=>"black_list",
			"playTime"=>"play_time",
			"playCount"=>"play_count",

		);
		/*protected $_validate = array(
			array('desktop2','','桌面ID已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/
		public function addPushMessage($put)
		{
			//检查数据
			if (!isset($put['interval']) || empty($put['msg']) || !isset($put['playTime']) || !isset($put['playCount']) || empty($put['model']) ||  !($put['pub_range'] == 'all' || $put['pub_range'] == 'jbk'  || $put['pub_range'] == 'unjbk' ) || !($put['type'] == 'ALL' || $put['type'] == 'group' )) {
				result('param');
			}
			if ($put['type'] == 'group' ) {
				if (empty($put['groupId'])) {
					result('param');
				}
				$group = D('Group')->getValOneForId($put['groupId']);
				if (!$group) {
					result('组不存在，请选择正确组');
				}
			}else{
				if (!empty($put['groupId'])) {
					unset($put['groupId']);
				}
			}

			$put['vendorID'] = !empty($put['vendorID'])?$put['vendorID']:'none';

			if ($onePushMessage = $this->where("`model`='%s' and `type`='%s' and `vendorid`= '%s'",array($put['model'],$put['type'],$put['vendorID']))->find()) {
				$this->deletePushMessage($onePushMessage);
			}

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
				'version'=>$time,
				"vendorid"=>$put['vendorID'],
				'pub_range'=>$put['pub_range'],
				"black_list"=>$put['blackList'],
				'white_list'=>$put['whiteList'],
				"msg"=>$put['msg'],
				"play_time"=>(int)$put['playTime'],
				"play_count"=>(int)$put['playCount'],
				"interval"=>(int)$put['interval'],
			);
			//添加到数据库
			$this->create($options);
			$id = $this->add();
			return true;
		}

		public function deletePushMessage($get)
		{
			if (!empty($get['id'])) {
				$get['id'] = intval($get['id']);
				if ($this->find($get['id'])) {
					$this->where("`id`=%d",array($get['id']))->delete();
				}
			}
			return ;
		}

		public function pushMessageLists($get)
		{
			if (empty($get['id'])) {
				$where = '';
				if (!empty($get['name'])) {
					$where = "`model` like '%".$get['name']."%' or `vendorid` like  '%".$get['name']."%' ";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();

				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}else{
					foreach ($res['extra'] as $key => $value) {

						if (!empty($value['blackList'])) {
							$res['extra'][$key]['blackList'] = json_decode($value['blackList'],true);
						}
						if (!empty($value['whiteList'])) {
							$res['extra'][$key]['whiteList'] = json_decode($value['whiteList'],true);
						}
					}
				}
			}else{
				$res['extra'] = $this->find($get['id']);
				$res['count'] = 1;
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = 0;
				}else{
					foreach ($res['extra'] as $value) {
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