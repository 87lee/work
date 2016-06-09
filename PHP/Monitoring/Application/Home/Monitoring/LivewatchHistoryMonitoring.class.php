<?php
	namespace Home\Monitoring;
	class LivewatchHistoryMonitoring extends \Think\Model
	{
		protected $tableName = 'livewatch_history';
		protected $connection = 'DB_DISTANCE_MONITORING';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/

		public function getLivewatchHistory($get)
		{
			/*$get['startTime']= strtotime('2016-01-29 10:00:00');
			$get['endTime'] = strtotime('2016-01-29 11:00:00');
			$get['interval'] = 5;*/
			set_time_limit(60*3);

			// time = 从时间戳 - 到时间戳

			/*
			select sum(codenum) as codenum,codetype,interface,
				case when DATE_FORMAT(time ,'%i') >= 0 AND DATE_FORMAT(time ,'%i') < 5 then '0-5'
					when DATE_FORMAT(time ,'%i') >= 5 AND DATE_FORMAT(time ,'%i') < 10 then '5-10'
					when DATE_FORMAT(time ,'%i') >= 10 AND DATE_FORMAT(time ,'%i') < 15 then '10-15'
					when DATE_FORMAT(time ,'%i') >= 55 then '55-60' end AS `intervalTime`
			from tb_interface_history
			where time >= '2016-01-29 10:00:00' AND time < '2016-01-29 11:00:00'
			GROUP BY `intervalTime`,`interface`,codetype
			ORDER BY intervalTime ;
			*/
			if (!empty($get['startTime'])&&!empty($get['endTime'])&&!empty($get['interval']) && ($get['interval'] >='1')&&isset($get['channel'])) {
				if ( $get['interval'] === '1' ) {
					$field = " `num`, ceil(( UNIX_TIMESTAMP(time) - {$get['startTime']} )/60) as intervalTime";
					$group = "";
				}else{
					$field = ' sum(num) as num,case ';
					$interval = 0;

					// $field .= "when ( UNIX_TIMESTAMP(time)-{$get['startTime']} )/60  >= 0 AND (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 < {$get['interval']} then '0'";
					$timeNum = ceil( ( $get['endTime']-$get['startTime'] ) / 60 / $get['interval']);

					for ($i=0; $i < $timeNum ; $i++) {

						$intervalNext = $interval + $get['interval'];

						if ($i == ($timeNum-1)) {
							$field .= " when (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 >= " . ($interval) . "  AND (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 <= " .( $intervalNext) . " then '". ($interval). "'";
							break;
						}
						$field .= " when (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 >= " . ($interval) . "  AND (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 < " .( $intervalNext) . " then '". ($interval). "'";

						$interval += $get['interval'];
					}

					/*$interval += $get['interval'];
					$intervalNext = $interval + $get['interval'];
					$field .= " when (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 >= " . ($interval) . "  AND (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 <= " .( $intervalNext) . " then '". ($interval). "'";
*/
					$field .= " end AS `intervalTime`";
					// $group = "`intervalTime`,`channel`";
					$group = "`intervalTime`";
				}
				$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."' AND channel = '" .$get['channel']."'" ;

				$order = "`time` Asc";

				// G('begin');// ...其他代码段
				$table = ' `tb_livewatch_history` force index (time) ';
				// $table = '`tb_livewatch_history`';

				if (!empty($get['page'])&&!empty($get['pageSize'])) {

					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$channelHistory = $this->field($field)->table($table)->limit($get['page'],$get['pageSize'])->where($where)->group($group)->order($order)->select();
				}else{
					$channelHistory = $this->field($field)->table($table)->where($where)->group($group)->order($order)->select();
					// $channelHistory = $this->field($field)->table($table)->where($where)->group($group)->order($order)->select(false);
				}
				// var_dump($channelHistory);
				// echo  $channelHistory;
				// die;
				// G('end');// ...也许这里还有其他代码

				// trace(G('begin','end').'s','SQL语句运行时间');


				// echo $this->getLastSql();

			}
			// var_dump($channelHistory);
			// G('begin');// ...其他代码段
			if (!empty($channelHistory)) {

				foreach ($channelHistory as $k => $v) {

					if (!empty($res['extra']['intervalTimes'])) {

						$aa = ($v['intervalTime'] - end( $res['extra']['intervalTimes'] )) / $get['interval'];
						if ( $aa >1 ) {
							for ($i=1; $i < $aa; $i++) {
								$res['extra']['data'][] = '0' ;
								$res['extra']['intervalTimes'][] =  (string) ( end( $res['extra']['intervalTimes'] ) + $get['interval'] )  ;
							}
						}

					}else{
						if ($v['intervalTime'] != '0' ) {

							$aa = $v['intervalTime']/ $get['interval'];
							for ($i=1; $i < $aa; $i++) {
								if ($i===1) {
									$res['extra']['data'][] = '0' ;
									$res['extra']['intervalTimes'][] =  '0'  ;
								}else{
									$res['extra']['data'][] = '0' ;
									$res['extra']['intervalTimes'][] =  (string) ( end( $res['extra']['intervalTimes'] ) + $get['interval'] )  ;
								}
							}
						}
					}
					$res['extra']['data'][] = $v['num'];
					$res['extra']['intervalTimes'][] = $v['intervalTime'];
					$res['extra']['countData'] += end( $res['extra']['data']);

					unset($channelHistory[$k]);

				}
				// $res['extra'][$key]['count'] = count($res['extra'][$key]['data']);
				// $res['extra'][$key]['timeCount'] = count($res['extra'][$key]['intervalTimes']);
				unset($res['extra']['intervalTimes']);
				$res['extra']['startTime'] = strtotime(date('Y-m-d H:i:s',$get['startTime']));
				if (empty($res['extra']['data'])) {
					unset($res['extra']);
				}
				$countExtraKeyData = count($res['extra']['data']);
				if ( $countExtraKeyData > $countNum) {
					$countNum = $countExtraKeyData;
				}
				if (!empty($res['extra'])) {
					if (!empty($res['extra']['data'])) {
						if (count($res['extra']['data']) < $countNum) {
							// $res['extra'][$key]['add']='true';
							// $res['extra'][$key]['count']=count($value['data']);
							for ($i=0; $i < $countNum-count($res['extra']['data']); $i++) {
								$res['extra']['data'][] = '0' ;
							}
						}
					}
						/*$res['extra'][$key]['nextCount'] = count($res['extra'][$key]['data']);*/
				}

			}

			// G('end');// ...也许这里还有其他代码
			// trace(G('begin','end').'s','处理数据运行时间');
			// var_dump($res);
			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			return $res;
			// return $arr = $this->field("id",true)->limit()->select();

		}
		public function getChannel()
		{
			$res['extra'] = $this->field('channel')->group('channel')->select();
			if (empty($res['extra'])) {
				$data['extra'] = array();
			}else{
				foreach ($res['extra'] as $key => $value) {
					$data['extra'][] = $value['channel'];
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