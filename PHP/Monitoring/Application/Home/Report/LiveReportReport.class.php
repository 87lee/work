<?php
	namespace Home\Report;
	class LiveReportReport extends \Think\Model
	{
		protected $tableName = 'live_report';
		protected $connection = 'DB_REPORT';
		protected $_map = array(
			'p2pId' =>'p2p_id',
			'g3Desc'  =>'g3_desc',
			'g3Region'  =>'g3_region',
		);

		public function getUsersReport($put,$get)
		{
			if (!empty($put['model'])) {
				$where = "model = '" . $put['model'] . "'";
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
			if (!empty($put['startVersion']) && !empty($put['endVersion']) ) {

				if (!empty($where)) {
					$where .= "  AND  version >= '" . $put['startVersion'] . "'  AND  version <='" .$put['endVersion']. "'";
				}else{
					$where = "version >= '" . $put['startVersion'] . "'  AND  version <='" .$put['endVersion']. "'";
				}

			}elseif (!empty($put['startVersion']) ) {
				if (!empty($where)) {
					$where .= "  AND  version >= '" . $put['startVersion'] . "'";
				}else{
					$where = " version >= '" . $put['startVersion'] . "'";
				}
			}elseif (!empty($put['endVersion']) ) {
				if (!empty($where)) {
					$where .= "  AND  version <= '" . $put['endVersion'] . "'";
				}else{
					$where = " version <= '" . $put['endVersion'] . "'";
				}
			}
			if (!empty($put['event'])) {
				if (!empty($where)) {
					$where .= "  AND event = '" . $put['event'] . "'";
				}else{
					$where = " event = '" . $put['event'] . "'";
				}
			}
			if (!empty($put['channel'])) {

				if (!empty($where)) {
					$where .= "  AND channel = '" . $put['channel'] . "'";
				}else{
					$where = "channel = '" . $put['channel'] . "'";
				}
			}
			if (!empty($put['p2pId'])) {
				if (!empty($where)) {
					$where .= "  AND p2p_id = '" . $put['p2pId'] . "'";
				}else{
					$where = "p2p_id = '" . $put['p2pId'] . "'";
				}

			}

			if (!empty($put['g3Desc'])) {
				if (!empty($where)) {
					$where .= "  AND g3_desc LIKE '%" . $put['g3Desc'] . "%'";
				}else{
					$where = "g3_desc LIKE '%" . $put['g3Desc'] . "%'";
				}

			}

			if (!empty($put['g3Region'])) {
				if (!empty($where)) {
					$where .= "  AND g3_region LIKE '%" . $put['g3Region'] . "%'";
				}else{
					$where = "g3_region LIKE '%" . $put['g3Region'] . "%'";
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

			$field = " * "; //,concat('".LIVE_USER_REPORT_ANAS."',json) as json
			$order = 'time desc';
			if (!empty($where)) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field($field)->limit($get['page'],$get['pageSize'])->where($where)->order($order)->select();
					$res['count'] = $this->where($where)->count();
				}else{
					$res['extra'] = $this->field($field)->where($where)->order($order)->select();
					$res['count'] = $this->where($where)->count();
				}
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field($field)->limit($get['page'],$get['pageSize'])->order($order)->select();
					$res['count'] = $this->count();
				}else{
					$res['extra'] = $this->field($field)->order($order)->select();
					$res['count'] = $this->count();
				}
			}

			return $res;

		}
		public function getInterface()
		{
			$res['extra'] = $this->field('interface')->group('interface')->select();
			if (empty($res['extra'])) {
				$data['extra'] = array();
			}else{
				foreach ($res['extra'] as $key => $value) {
					$data['extra'][] = $value['interface'];
				}
			}
			return $data;
		}

	}