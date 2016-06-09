<?php
	namespace Home\Monitoring;
	class DownloadWarningRulesHistoryMonitoring extends \Think\Model
	{
		protected $tableName = 'download_warning_rules_history';
		protected $connection = 'DB_MONITORING';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/

		public function addWarningRulesHistory($optiops)
		{

			if ($this->create($optiops)) {
				$this->add();
			}
			return;
		}

		public function getWarningRulesHistory()
		{

			// $where = 'a.status =1';
			$join  = ' LEFT JOIN tb_download_warning_rules AS b ON a.rules_id = b.id  LEFT JOIN tb_domain AS c ON b.project_id = c.id';
			$order = ' a.id asc';
			$field = 'a.id,a.status,a.rules_id,a.time,b.code,b.period,b.method,b.compare,b.value,c.name,c.url';
			$group = 'a.rules_id';

			// return $sql = $this->field($field)->table(" (SELECT * FROM  tb_download_warning_rules_history ORDER BY id DESC) ")->alias(" AS a")->where($where)->join($join)->order($order)->group($group)->select();

			$sql = $this->field($field)->table(" (SELECT * FROM  tb_download_warning_rules_history ORDER BY id DESC) ")->alias(" AS a")->where($where)->join($join)->order($order)->group($group)->select(false);

			// $m = M();
			$Model = M(); // 实例化一个model对象 没有对应任何数据表
			// echo "SELECT * FROM (". $sql . ") AS d WHERE d.status = 0";
			if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = ($get['page']-1)*$get['pageSize'];
				$res['extra'] = $Model->query( "SELECT * FROM (". $sql . ") AS d WHERE d.status = 1 LIMIT ".(int)$get['page'] . ",".(int)$get['pageSize']);
			}else{
				$res['extra'] = $Model->query( "SELECT * FROM (". $sql . ") AS d WHERE d.status = 1");
			}
			 // " SELECT count(*) as counta FROM (". $sql . ") AS d WHERE d.status = 1 LIMIT 1";

			$res['count'] = $Model->query( " SELECT count(*) as count FROM (". $sql . ") AS d WHERE d.status = 1 LIMIT 1");

			return $res;
			// var_dump($res);

			// return $this->field($field)->alias(" AS a")->where($where)->join($join)->order($order)->group($group)->select();
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