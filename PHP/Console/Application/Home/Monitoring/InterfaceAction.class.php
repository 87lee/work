<?php
	namespace Home\Action;
	class InterfaceAction extends \Think\Model
	{
		protected $tableName = 'interface';
		protected $connection = 'DB_ACTION';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected function interfaceDesc()
	    	{
	    		return array(
	    			'Desktop/index'=>'桌面默认接口',
	    			'Desktop/publishDesktopCmd'=>'发布数据库全部桌面命令',
	    			'Desktop/FirmwareConfigGroupPublish'=>'发布数据库全部安卓固件',
	    			'Desktop/index'=>'桌面默认接口',
	    			'Desktop/publishDesktopCmd'=>'发布数据库全部桌面命令',
	    			'Desktop/FirmwareConfigGroupPublish'=>'发布数据库全部安卓固件',
			);
	    	}*/

		public function addAction($user)
		{
			if (!empty($user)) {

				$dirPath = scandir(MODULE_PATH.'Controller');
				// var_dump($dirPath);
				foreach ($dirPath as $value) {
					if ($value !='.' && $value !='..' && $value !='index.html'  && $value !='IndexController.class.php' ) {

						$className = substr($value, 0,stripos($value, '.'));
						$actionName = C('TMPL_FILE_DEPR').substr($className, 0,stripos($className, 'Controller'))  . C('TMPL_FILE_DEPR');
						$classMethods = get_class_methods('\Home\Controller\\'.$className);
						$reflector = new \ReflectionClass('\Home\Controller\\'.$className);
						// if(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$str)) //UTF-8汉字字母数字下划线正则表达式
						// preg_match ( '/[\x{4e00}-\x{9fa5}]+$/u','地地左有睛', $comment );

						// to get the Method DocBlock
						// preg_match ( '/[\x{4e00}-\x{9fa5}]+/u', $reflector->getMethod('publishDesktopCmd')->getDocComment(), $comment );
						// echo $reflector->getMethod('publishDesktopCmd')->getDocComment();
						// var_dump($comment[0]);

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

				/*
				if (!empty($array[C('TMPL_FILE_DEPR').CONTROLLER_NAME.C('TMPL_FILE_DEPR').ACTION_NAME])) {
					$optiops = array(
						'time'=>NOW_TIME,
						'interface'=>$array[C('TMPL_FILE_DEPR').CONTROLLER_NAME.C('TMPL_FILE_DEPR').ACTION_NAME],
						'user'=>$user
					);
				}else{
					$optiops = array(
						'time'=>NOW_TIME,
						'interface'=>C('TMPL_FILE_DEPR').CONTROLLER_NAME.C('TMPL_FILE_DEPR').ACTION_NAME,
						'user'=>$user
					);
				}
				$this->create($optiops);
				$this->add();*/
			}
		}
		public function userActionLists($get)
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

				if ($res['extra']) {
					$res['count'] =1;
				}

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
	}