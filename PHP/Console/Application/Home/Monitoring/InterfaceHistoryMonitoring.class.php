<?php
	namespace Home\Monitoring;
	class InterfaceHistoryMonitoring extends \Think\Model
	{
		protected $tableName = 'interface_history';
		protected $connection = 'DB_MONITORING';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/

		public function getCodeTypeHistory($get)
		{
			/*$get['startTime']= strtotime('2016-01-29 10:00:00');
			$get['endTime'] = strtotime('2016-01-29 11:00:00');
			$get['interval'] = 5;*/
			set_time_limit(60*3);
			// time = 从时间戳 - 到时间戳
			if (!empty($get['startTime'])&&!empty($get['endTime'])&&!empty($get['interval']) && ($get['interval'] >='1')&&!empty($get['interface'])) {
			/*

			select sum(codenum) as codenum,codetype,interface,
				case when DATE_FORMAT(time ,'%i') >= 0 AND DATE_FORMAT(time ,'%i') < 5 then '0-5'
					when DATE_FORMAT(time ,'%i') >= 5 AND DATE_FORMAT(time ,'%i') < 10 then '5-10'
					when DATE_FORMAT(time ,'%i') >= 10 AND DATE_FORMAT(time ,'%i') < 15 then '10-15'
					when DATE_FORMAT(time ,'%i') >= 15 AND DATE_FORMAT(time ,'%i') < 20 then '15-20'
			   		when DATE_FORMAT(time ,'%i') >= 20 AND DATE_FORMAT(time ,'%i') < 25 then '20-25'
					when DATE_FORMAT(time ,'%i') >= 25 AND DATE_FORMAT(time ,'%i') < 30 then '25-30'
					when DATE_FORMAT(time ,'%i') >= 25 AND DATE_FORMAT(time ,'%i') < 35 then '30-35'
					when DATE_FORMAT(time ,'%i') >= 35 AND DATE_FORMAT(time ,'%i') < 40 then '35-40'
					when DATE_FORMAT(time ,'%i') >= 40 AND DATE_FORMAT(time ,'%i') < 45 then '40-45'
					when DATE_FORMAT(time ,'%i') >= 45 AND DATE_FORMAT(time ,'%i') < 50 then '45-50'
					when DATE_FORMAT(time ,'%i') >= 50 AND DATE_FORMAT(time ,'%i') < 55 then '50-55'
					when DATE_FORMAT(time ,'%i') >= 55 then '55-60' end AS `intervalTime`
			from tb_interface_history
			where time >= '2016-01-29 10:00:00' AND time < '2016-01-29 11:00:00'
			GROUP BY `intervalTime`,`interface`,codetype
			ORDER BY intervalTime ;

			*/


				if ( $get['interval'] === '1' ) {
					$field = " `codenum`,`codetype`, ceil(( UNIX_TIMESTAMP(time) - {$get['startTime']} )/60) as intervalTime";
					$group = "";
				}else{
					$field = ' sum(codenum) as codenum,codetype,';
					$field .= "case when ( UNIX_TIMESTAMP(time)-{$get['startTime']} )/60  >= 0 AND (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 < {$get['interval']} then '0'";
					for ($i=1; $i < ceil( ( $get['endTime']-$get['startTime'] ) / 60 / $get['interval'] ); $i++) {
						$interval += $get['interval'];
						$intervalNext = $interval + $get['interval'];
						$field .= " when (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 >= " . ($interval) . "  AND (UNIX_TIMESTAMP(time)-{$get['startTime']})/60 < " .( $intervalNext) . " then '". ($interval). "'";
					}
					$field .= " end AS `intervalTime`";
					$group = "`intervalTime`,`interface`,`codetype`";
				}

				if (!empty($get['interface'])&&!empty($get['codeType']) && ($get['codeType'] !='all') ) {
					$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."' AND interface = '" .$get['interface']."' AND codetype >= ".$get['codeType']."00 AND codetype <=".$get['codeType']."99";
					$codeType = $this->where(" codetype >= ".$get['codeType']."00 AND codetype <=".$get['codeType']."99")->field('codetype')->group('codetype')->select();

				}elseif (!empty($get['codeType'])&&($get['codeType'] != 'all' )) {
					$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."'  AND codetype >= ".$get['codeType']."00 AND codetype <=".$get['codeType']."99";
					$codeType = $this->where(" codetype >= ".$get['codeType']."00 AND codetype <=".$get['codeType']."99")->field('codetype')->group('codetype')->select();

				}elseif (!empty($get['interface'])) {
					$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."' AND interface = '" .$get['interface']."'" ;
					$codeType = $this->field('codetype')->group('codetype')->select();

				}else{
					$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."'";
					$codeType = $this->field('codetype')->group('codetype')->select();
				}

				$order = "`time` Asc";
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$interfaceHistory = $this->field($field)->limit($get['page'],$get['pageSize'])->where($where)->group($group)->order($order)->select();
				}else{
					/*echo $interfaceHistory = $this->field($field)->where($where)->group($group)->order($order)->select(false);
					die;*/

					$interfaceHistory = $this->field($field)->where($where)->group($group)->order($order)->select();

					// $res['extra'] = $this->field($field)->where($where)->group($group)->order($order)->select();
				}
				// echo $this->getLastSql();

			}
			// var_dump($interfaceHistory);

			if (!empty($codeType)&&!empty($interfaceHistory)) {

				$countNum = 0;
				foreach ($codeType as $key => $value) {
					$res['extra'][$key]['type'] = $value['codetype'];

					foreach ($interfaceHistory as $k => $v) {
						if ($res['extra'][$key]['type'] ==  $v['codetype']) {

							if (!empty($res['extra'][$key]['intervalTimes'])) {

								if ( end($res['extra'][$key]['intervalTimes'] ) !=  $currentTime ) {
									$aa = ($v['intervalTime'] - end( $res['extra'][$key]['intervalTimes'] )) / $get['interval'];
									for ($i=1; $i < $aa; $i++) {
										$res['extra'][$key]['data'][] = '0' ;
										$res['extra'][$key]['intervalTimes'][] =  (string) ( end( $res['extra'][$key]['intervalTimes'] ) + $get['interval'] )  ;
									}
								}
								$res['extra'][$key]['data'][] = $v['codenum'];
								$res['extra'][$key]['intervalTimes'][] = $v['intervalTime'];
							}else{
								if ($v['intervalTime'] === '0' ) {
									$res['extra'][$key]['data'][] = $v['codenum'];
									$res['extra'][$key]['intervalTimes'][] = $v['intervalTime'];
								}else{
									$aa = $v['intervalTime']/ $get['interval'];
									for ($i=1; $i < $aa; $i++) {
										if ($i===1) {
											$res['extra'][$key]['data'][] = '0' ;
											$res['extra'][$key]['intervalTimes'][] =  '0'  ;
										}else{
											$res['extra'][$key]['data'][] = '0' ;
											$res['extra'][$key]['intervalTimes'][] =  (string) ( end( $res['extra'][$key]['intervalTimes'] ) + $get['interval'] )  ;
										}

									}
								}
							}
							$res['extra'][$key]['countData'] += end( $res['extra'][$key]['data']);
							unset($interfaceHistory[$k]);
						}
					}
					// $res['extra'][$key]['count'] = count($res['extra'][$key]['data']);
					// $res['extra'][$key]['timeCount'] = count($res['extra'][$key]['intervalTimes']);
					unset($res['extra'][$key]['intervalTimes']);
					$res['extra'][$key]['startTime'] = strtotime(date('Y-m-d H:i:s',$get['startTime']));
					if (empty($res['extra'][$key]['data'])) {
						unset($res['extra'][$key]);
					}
					$countExtraKeyData = count($res['extra'][$key]['data']);
					if ( $countExtraKeyData > $countNum) {
						$countNum = $countExtraKeyData;
					}
				}

				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						if (!empty($value['data'])) {
							if (count($value['data']) < $countNum) {
								// $res['extra'][$key]['add']='true';
								// $res['extra'][$key]['count']=count($value['data']);
								for ($i=0; $i < $countNum-count($value['data']); $i++) {
									$res['extra'][$key]['data'][] = '0' ;
								}

							}
						}
						/*$res['extra'][$key]['nextCount'] = count($res['extra'][$key]['data']);*/
					}
				}

			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
			}else{
				$res['extra'] = array_values($res['extra']);
			}
			return $res;
			// return $arr = $this->field("id",true)->limit()->select();

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