<?php
	namespace Home\Monitoring;
	class DownloadHistoryMonitoring extends \Think\Model
	{
		protected $tableName = 'download_history';
		protected $connection = 'DB_DISTANCE_MONITORING';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/

		public function getDownloadHistory($get)
		{
			/*$get['startTime']= strtotime('2016-01-29 10:00:00');
			$get['endTime'] = strtotime('2016-01-29 11:00:00');
			$get['interval'] = 5;*/
			set_time_limit(60*3);
			// time = 从时间戳 - 到时间戳
			if (!empty($get['startTime'])&&!empty($get['endTime'])&&!empty($get['interval']) && ($get['interval'] >='1')&&!empty($get['url'])) {
				if ( $get['interval'] === '1' ) {
					$field = " `codenum`,`codetype`, ceil(( UNIX_TIMESTAMP(time) - {$get['startTime']} )/60) as intervalTime";
					$group = "";
				}else{
					$field = ' sum(codenum) as codenum,codetype,case ';
					$interval = 0;

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

					$field .= " end AS `intervalTime`";
					$group = "`intervalTime`,`url`,`codetype`";
				}

				if (!empty($get['url'])&&!empty($get['codeType']) && ($get['codeType'] !='all') ) {
					$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."' AND url = '" .$get['url']."' AND codetype >= ".$get['codeType']."00 AND codetype <=".$get['codeType']."99";
					$codeType = $this->where(" codetype >= ".$get['codeType']."00 AND codetype <=".$get['codeType']."99")->field('codetype')->group('codetype')->select();

				}elseif (!empty($get['codeType'])&&($get['codeType'] != 'all' )) {
					$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."'  AND codetype >= ".$get['codeType']."00 AND codetype <=".$get['codeType']."99";
					$codeType = $this->where(" codetype >= ".$get['codeType']."00 AND codetype <=".$get['codeType']."99")->field('codetype')->group('codetype')->select();

				}elseif (!empty($get['url'])) {
					$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."' AND url = '" .$get['url']."'" ;
					$codeType = $this->field('codetype')->group('codetype')->select();

				}else{
					$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time <= '".date('Y-m-d H:i:s',$get['endTime'])."'";
					$codeType = $this->field('codetype')->group('codetype')->select();
				}

				$order = "`time` Asc";
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$urlHistory = $this->field($field)->limit($get['page'],$get['pageSize'])->where($where)->group($group)->order($order)->select();
				}else{
					/*echo $urlHistory = $this->field($field)->where($where)->group($group)->order($order)->select(false);
					die;*/

					$urlHistory = $this->field($field)->where($where)->group($group)->order($order)->select();

					// $res['extra'] = $this->field($field)->where($where)->group($group)->order($order)->select();
				}
				// echo $this->getLastSql();

			}
			// var_dump($urlHistory);

			if (!empty($codeType)&&!empty($urlHistory)) {

				$countNum = 0;
				foreach ($codeType as $key => $value) {
					$res['extra'][$key]['type'] = $value['codetype'];

					foreach ($urlHistory as $k => $v) {
						if ($res['extra'][$key]['type'] ==  $v['codetype']) {

							if (!empty($res['extra'][$key]['intervalTimes'])) {

								if ( end($res['extra'][$key]['intervalTimes'] )  !=  $v['intervalTime']) {
									$aa = ($v['intervalTime'] - end( $res['extra'][$key]['intervalTimes'] )) / $get['interval'];
									for ($i=1; $i < $aa; $i++) {
										$res['extra'][$key]['data'][] = '0' ;
										$res['extra'][$key]['intervalTimes'][] =  (string) ( end( $res['extra'][$key]['intervalTimes'] ) + $get['interval'] )  ;
									}
								}
							}else{
								if ($v['intervalTime'] != '0' ) {


									$aa = $v['intervalTime']/ $get['interval'];
									for ($i=0; $i < $aa; $i++) {
										if ($i===0) {
											$res['extra'][$key]['data'][] = '0' ;
											$res['extra'][$key]['intervalTimes'][] =  '0'  ;
										}else{
											$res['extra'][$key]['data'][] = '0' ;
											$res['extra'][$key]['intervalTimes'][] =  (string) ( end( $res['extra'][$key]['intervalTimes'] ) + $get['interval'] )  ;
										}

									}
								}
							}
							$res['extra'][$key]['data'][] = $v['codenum'];
							$res['extra'][$key]['intervalTimes'][] = $v['intervalTime'];
							$res['extra'][$key]['countData'] += end( $res['extra'][$key]['data']);
							unset($urlHistory[$k]);
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


		public function getArrForCheckRules($get)
		{
			// INSERT INTO `feedback`.`tb_download_history` (`id`, `url`, `time`, `codetype`, `codenum`) VALUES (NULL, 'http://app.znds.com', '2016-04-02 01:48:00', '403', '2');

			if (!empty($get['endTime'])&&!empty($get['startTime'])&&!empty($get['startCode'])&&!empty($get['endCode'])) {
				$where = "time >= '".date('Y-m-d H:i:s',$get['startTime'])."' AND time < '".date('Y-m-d H:i:s',$get['endTime']) ."'" ;

				if (!empty($get['rulesWhere'])) {
					$where .= " AND url = '".$get['rulesWhere']."' ";
				}
				/*if (!empty($get['codetype'])) {
					$where .= " AND codetype = '".$get['codetype']."' ";
				}*/


				$startCode = (int)$get['startCode'];
				$endCode = (int)$get['endCode'];

				$whereCodeTypeXX = $where . " AND codetype >= ".$startCode." AND codetype <=  ".$endCode."";

				$where .= " AND ( codetype < ".$startCode." OR codetype >  ".$endCode.")";
				if (!empty($get['method']) && $get['method'] == 'percent') {
					$field = "sum(codenum) as num ,codetype,url";
					$group = "codetype,url";
					$res = $this->field($field)->where($where)->group($group)->select();
				}
				$field = "sum(codenum) as num ,concat('".$startCode.'-'.$endCode."') as codetype,url";
				$group = "url";
				$valueOne = $this->field($field)->where($whereCodeTypeXX)->group($group)->find();
				if ($valueOne) {
					$res[] = $valueOne;
				}


			}
			return $res;
		}
		/*
		public function getUrl()
		{
			$res['extra'] = $this->field('url')->group('url')->select();
			if (empty($res['extra'])) {
				$data['extra'] = array();
			}else{
				foreach ($res['extra'] as $key => $value) {
					$data['extra'][] = $value['url'];
				}
			}
			return $data;
		}
		public function addAction($user)
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