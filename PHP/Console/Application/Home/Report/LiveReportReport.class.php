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
					$where .= "  AND g3_desc = '" . $put['g3Desc'] . "'";
				}else{
					$where = "g3_desc = '" . $put['g3Desc'] . "'";
				}

			}

			if (!empty($put['g3Region'])) {
				if (!empty($where)) {
					$where .= "  AND g3_region = '" . $put['g3Region'] . "'";
				}else{
					$where = "g3_region = '" . $put['g3Region'] . "'";
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

			$field = " * ,concat('". C('LIVE_USER_REPORT_ANAS')."',json) as json";
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
		/*public function addAction($user)
		{
			if (!empty($user)) {*/

				/*$dirPath = scandir(MODULE_PATH.'Controller');
				foreach ($dirPath as $value) {
					if ($value !='.' && $value !='..' && $value !='index.html'  && $value !='IndexController.class.php' ) {

						$className = substr($value, 0,stripos($value, '.'));
						$actionName = C('TMPL_FILE_DEPR').substr($className, 0,stripos($className, 'Controller'))  . C('TMPL_FILE_DEPR');
						$classMethods = get_class_methods('\Home\Controller\\'.$className);
						$reflector = new \ReflectionClass('\Home\Controller\\'.$className);
						foreach ($classMethods as $key => $value) {
							if ($value == '__set' || $value == 'get' || $value == '__get' || $value == '__call' || $value == '__isset' || $value == '__destruct' || $value == '__construct' || $value == 'index') {
								continue;
							}
							preg_match ( '/[\x{4e00}-\x{9fa5}A-Za-z0-9_]+/u', $reflector->getMethod($value)->getDocComment(), $comment );
							$array[$actionName.$value] = $comment[0];
						}
					}
				}
				foreach ($array as $key => $value) {
					$addInterface[] = array(
						'interface'=>$key,
						'desc'=>$value
					);
				}
				var_dump($addInterface);
				D('Interface','Action')->addAll($addInterface);*/


				/*if (!empty( $Interface = D('Interface','Action')->where("Interface = '%s'",array(C('TMPL_FILE_DEPR').CONTROLLER_NAME.C('TMPL_FILE_DEPR').ACTION_NAME))->find() ) ) {
					$optiops = array(
						'time'=>NOW_TIME,
						'interface'=>$Interface['desc'],
						'user'=>$user
					);
					$this->create($optiops);
					$this->add();
				}
			}
		}*/
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