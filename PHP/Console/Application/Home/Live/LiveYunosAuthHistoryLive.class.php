<?php
	namespace Home\Live;
	class LiveYunosAuthHistoryLive extends \Think\Model
	{
		protected $tableName = 'yunos_auth_history';
		protected $connection = 'DB_LIVE_AUTH';
		protected $_map = array(
			'vendorID' =>'vendor_id',
			/*'groupId'  =>'group_id',
			'isSkip'  =>'is_skip',
			'showTime'  =>'show_time',
			'AB'  =>'ab',
			'whiteList'=>'white_list',
			'blackList'=>'black_list',*/
		);
		public function addLiveAuthHistory($put,$action = '未获取')
		{
			if (isset($put['id'])) {
				unset($put['id']);
			}
			if (isset($put['id'])) {
				unset($put['id']);
			}
			$user = getUser();
			$put['action'] = $action;
			$put['authorizer'] = $user['user'];
			$this->create($put);
			$this->add();
		}

		public function liveAuthHistoryLists($get)
		{

			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else{
				$order = 'time desc';
				$where = "";
				if (!empty($get['name'])){
					$where = "`model` like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->order($order)->select();
				}else{
					$res['extra'] = $this->where($where)->order($order)->select();
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}

	}