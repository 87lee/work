<?php
	/**
	 * [result 返回信息]
	 * @param  boolean $result  [description]
	 * @param  string  $options [description]
	 * @return [type]           [description]
	 */
	function result($result = true,$options = 'null')
	{
		if($result === true ){
			if ($options != 'null') {
				$options['result'] = 'ok';
				echo json_encode($options,JSON_UNESCAPED_UNICODE);
			}else{
				$data['result'] = 'ok';
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
			}

		}else {
			switch ($result) {
				case 'login':
					$data['result'] = 'fail' ;
	              			$data['reason'] = '未登录，请重新登录';
					break;
				case 'param':
					$data['result'] = 'fail' ;
	              			$data['reason'] = '参数有误';
					break;
				case 'auth':
					$data['result'] = 'fail' ;
	              			$data['reason'] = '您的权限不够';
					break;
				case 'unknown':
					$data['result'] = 'fail' ;
	              			$data['reason'] = '完成';
					break;
				case 'publish':
					$data['result'] = 'fail' ;
					$data['failList'] = $options;
					break;
				default:
					$data['result'] = 'fail' ;
					$data['reason'] = $result;
					break;
			}
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
		}
		die();
	}
	/**
	 * 是否已登陆
	 * @return boolean [description]
	 */
	function isLogin()
	{
		header('Content-type:text/plain;charset=utf-8');

		if (strstr($_SERVER['REQUEST_URI'], '/user/login') == false && $_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) {
		            if (empty(session('is_login'))) {
	            		result('login');
		            }else{
	            		$isLogin = session('is_login');
	            		session('[regenerate]');
	            		session('is_login',$isLogin);
		            }
	     	}
	}
	/**
	 * [zipErrorMsg zip压缩类错误信息]
	 * @param  [type] $res [description]
	 * @return [type]      [description]
	 */
	function zipErrorMsg($res)
	{
		switch($res){
		            case \ZipArchive::ER_EXISTS:
		                $ErrMsg = "文件已经存在";
		                break;
		            case \ZipArchive::ER_INCONS:
		                $ErrMsg = "Zip归档文件不一致";
		                break;

		            case \ZipArchive::ER_MEMORY:
		                $ErrMsg = "分配内存失败";
		                break;

		            case \ZipArchive::ER_NOENT:
		                $ErrMsg = "没有这样的文件";
		                break;

		            case \ZipArchive::ER_NOZIP:
		                $ErrMsg = "不是一个zip归档";
		                break;

		            case \ZipArchive::ER_OPEN:
		                $ErrMsg = "无法打开文件";
		                break;

		            case \ZipArchive::ER_READ:
		                $ErrMsg = "读取错误";
		                break;

		            case \ZipArchive::ER_SEEK:
		                $ErrMsg = "搜索错误";
		                break;

		            default:
		                $ErrMsg = "未知原因";
		                break;
		}
		return $ErrMsg;
	}
	/**
	 * 获取登陆用户
	 * @return [type] [description]
	 */
	function getUser()
	{
		return $user = session('is_login');
		// return json_decode($user,true);
	}

	/**
	 * [removeDir 删除非空目录]
	 * @param  [type] $dir [目录或文件所在地址]
	 * @return [type]      [description]
	 */
	function removeDir($dir)
	{
		/* 进入目录 */
		$dirList = scandir($dir);

		/* 在目录里面进行作业 */
		foreach($dirList as $row)
		{
			if($row != '.' && $row != '..'){
				if(is_file($dir.'/'.$row))
				{
					unlink($dir.'/'.$row);
				}else{
					removeDir($dir.'/'.$row); //$dir = C:/1/2
				}
			}
		}

		/* 最后删除目录 */
		rmdir($dir);
	}
	/**
	 * php将文件夹打包成zip文件
	 * @param [type] $path 对要打包的根目录进行操作
	 * @param [type] $zip  ZipArchive的对象传递给方法
	 */
	function addFileToZip($path,$zipPath,$zip){
	    	$handler= scandir($path); // 进入目录
	    	foreach ($handler as  $row) {
	    		if($row != '.' && $row != '..'){
				if(is_file($path.'/'.$row))
				{
					$zip->addFile($path."/".$row,$zipPath."/".$row);
				}else{
					$zip->addEmptyDir($zipPath."/".$row);
	                			addFileToZip($path."/".$row,$zipPath."/".$row, $zip);
				}
			}
	    	}

	    	// @closedir($path);
	}
	/**
	 * [requireBase 包含基础文件]
	 * @param  [type] $dirPath [description]
	 * @return [type]          [description]
	 */
	function requireBase($dirPath)
	{
		//上传OSS
		require_once '../Base/'.$dirPath;
	}
	/**
	 * 桌面控件跳转信息
	 * @param  [type]  $value [description]
	 * @param  [type]  $msg   [description]
	 * @param  boolean $error [description]
	 * @return [type]         [description]
	 */
	 function desktopWidgetCheckAction($value,$msg,$error = false)
	{
		switch ($value['type']) {
			case 'APP':
				if (isset($value['pkgName'])) {
					$data = array(
						'type'=>$value['type'],
						'appData'=>array(
							'pkgName'=>$value['pkgName']
						)
					);
				}else{
					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
						// return false;
					}else{
						result('该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
					}
				}
				break;
			case 'ACTION':
				if (isset($value['action'])) {
					if (!is_array($value['extraData'])) {
						$value['extraData'] = array();
					}else{
						foreach ($value['extraData'] as $item) {
							if (!isset($item['key']) || !isset($item['value']) ) {
								$value['extraData'] = array();
								break;
							}
						}
					}
					$data = array(
						'type'=>$value['type'],
						'actionData'=>array(
							'action'=>$value['action']
						),
						'extraData'=>$value['extraData']
					);
				}else{
					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
						// return false;
					}else{
						result('该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
					}
				}
				break;
			case 'SCHEME':
				if ( !isset($value['uri']) ) {

					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
						// return false;
					}else{
						result('该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
					}
				}
				if (!is_array($value['extraData'])) {
					$value['extraData'] = array();
				}else{
					foreach ($value['extraData'] as $item) {
						if (!isset($item['key']) || !isset($item['value']) ) {
							$value['extraData'] = array();
							break;
						}
					}
				}
				$data = array(
					'type'=>$value['type'],
					'schemeData'=>array(
						'action'=>isset($value['action']) ? $value['action'] : '',
						'uri'=>$value['uri'],
					),
					'extraData'=>$value['extraData']
				);

				break;
			case 'COMPONENT':
				if (isset($value['clsName'])&&isset($value['component'])) {
					if (!is_array($value['extraData'])) {
						$value['extraData'] = array();
					}else{
						foreach ($value['extraData'] as $item) {
							if (!isset($item['key']) || !isset($item['value']) ) {
								$value['extraData'] = array();
								break;
							}
						}
					}
					$data = array(
						'type'=>$value['type'],
						'componentData'=>array(
							'pkgName'=>$value['component'],
							'clsName'=>$value['clsName'],
						),
						'extraData'=>$value['extraData']
					);
				}else{
						if ($error) {
							return $result = array('reason'=>'该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
							// return false;
						}else{
							result('该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
						}
					}
				break;
			case 'URI':

				if (isset($value['uri'])) {
					$data = array(
						'type'=>$value['type'],
						'uriData'=>array(
							'uri'=>$value['uri']
						)
					);
				}else{
					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
						// return false;
					}else{
						result('该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
					}
				}
				break;

				default:
					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
						// return false;
					}else{
						result('该桌面'.$msg.'数据id为'.$value['index'].'跳转信息出错');
					}
					break;
		}
		return $data;
	}
	/**
	 * [desktopSlotCheckAction 桌面坑位跳转信息]
	 * @param  [type]  $value [description]
	 * @param  [type]  $msg   [description]
	 * @param  boolean $error [description]
	 * @return [type]         [description]
	 */
	function desktopSlotCheckAction($value,$msg,$error = false)
	{
		$data['type'] = $value['type'];
		switch ($value['type']) {
			case 'APP':
				if (!empty($value['pkgName'])) {
					$data['appData'] = array(
						'pkgName'=>$value['pkgName']
					);
				}else{
					if ($error) {
						return $result = array('reason'=> $msg . isset($value['slotId'])?$value['slotId']:$value['slotID'] .'数据类型出错');
						// return false;
					}else{
						result( $msg . isset($value['slotId'])?$value['slotId']:$value['slotID'] .'数据类型出错');
					}
				}
				break;
			case 'ACTION':
				if (!empty($value['action'])) {

					if (!is_array($value['extraData']) || empty($value['extraData'])) {
						$value['extraData'] = array();
					}else{
						foreach ($value['extraData'] as $item) {
							if (!isset($item['key']) || !isset($item['value']) ) {
								$value['extraData'] = array();
								break;
							}
						}
					}
					$data['extraData'] = $value['extraData'];
					$data['actionData'] = array(
						'action'=>$value['action']
					);
				}else{
					if ($error) {
						return $result = array('reason'=> '坑位'.$value['slotId'].'数据类型出错');

						// return false;
					}else{
						result('该桌面坑位'.$value['slotId'].'数据类型出错');
					}
				}
				break;
			case 'SCHEME':
				if (empty($value['uri']) ) {
					if ($error) {
						return $result = array('reason'=> '坑位'.$value['slotId'].'数据类型出错');

						// return false;
					}else{
						result('该桌面坑位'.$value['slotId'].'数据类型出错');
					}
				}

				if (!is_array($value['extraData']) || empty($value['extraData'])) {
					$value['extraData'] = array();
				}else{
					foreach ($value['extraData'] as $item) {
						if (!isset($item['key']) || !isset($item['value']) ) {
							$value['extraData'] = array();
							break;
						}
					}
				}
				$data['extraData'] = $value['extraData'];
				$data['schemeData']=array(
					'action'=>!empty($value['action'])?$value['action']:'',
					'uri'=>$value['uri']
				);

				break;
			case 'COMPONENT':
				if (!empty($value['component'])&&!empty($value['clsName'])) {
					if (!is_array($value['extraData']) || empty($value['extraData'])) {
						$value['extraData'] = array();
					}else{
						foreach ($value['extraData'] as $item) {
							if (!isset($item['key']) || !isset($item['value']) ) {
								$value['extraData'] = array();
								break;
							}
						}
					}
					$data['extraData'] = $value['extraData'];
					$data['componentData'] = array(
						'pkgName'=>$value['component'],
						'clsName'=>$value['clsName']
					);
				}else{
					if ($error) {
						return $result = array('reason'=> '坑位'.$value['slotId'].'数据类型出错');

						// return false;
					}else{
						result('该桌面坑位'.$value['slotId'].'数据类型出错');
					}
				}
				break;
			case 'URI':
				if (!empty($value['uri'])) {
					$data['uriData'] = array(
						'uri'=>$value['uri']
					);
				}else{
					if ($error) {
						return $result = array('reason'=> '坑位'.$value['slotId'].'数据类型出错');
						// return false;
					}else{
						result('该桌面坑位'.$value['slotId'].'数据类型出错');
					}
				}
				break;

			default:
				if ($error) {
					return $result = array('reason'=> '坑位'. $value['slotId'] .'数据类型出错');
					// return false;
				}else{
					result('该桌面坑位'.$value['slotId'].'数据类型出错');
				}
				break;
		}
		return $data;
	}
	/**
	 * [desktopQuickSlotCheckAction 快捷坑位或运营坑位跳转信息]
	 * @param  [type]  $value [description]
	 * @param  [type]  $msg   [description]
	 * @param  boolean $error [description]
	 * @return [type]         [description]
	 */
	 function desktopQuickSlotCheckAction($value,$msg,$error = false)
	{

		switch ($value['actionType']) {
			case 'APP':
				if (isset($value['app']['pkgName'])) {
					$data = array(
						'type'=>$value['actionType'],
						'appData'=>array(
							'pkgName'=>$value['app']['pkgName']
						)
					);
					unset($value['app']);
				}else{
					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'出错:id为'. (isset($value['slotId'])?$value['slotId']:$value['slotID']) .'APP跳转信息出错');

					}else{
						result('该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'APP跳转信息出错');
					}
				}
				break;
			case 'ACTION':
				if (isset($value['action']['action'])) {
					if (!is_array($value['action']['extraData']) || empty($value['action']['extraData'])) {
						$value['action']['extraData'] = array();
					}else{
						foreach ($value['action']['extraData'] as $item) {
							if (!isset($item['key']) || !isset($item['value']) ) {
								$value['action']['extraData'] = array();
								break;
							}
						}
					}
					$data = array(
						'type'=>$value['actionType'],
						'actionData'=>array(
							'action'=>$value['action']['action']
						),
						'extraData'=>$value['action']['extraData']
					);
				}else{
					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'ACTION跳转信息出错');

					}else{
						result('该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'ACTION跳转信息出错');
					}
				}
				break;
			case 'SCHEME':
				if (!isset($value['scheme']['uri']))  {

					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'SCHEME跳转信息出错');

					}else{
						result('该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'SCHEME跳转信息出错');
					}
				}
				if (!is_array($value['scheme']['extraData']) || empty($value['scheme']['extraData'])) {
					$value['scheme']['extraData'] = array();
				}else{
					foreach ($value['scheme']['extraData'] as $item) {
						if (!isset($item['key']) || !isset($item['value']) ) {
							$value['scheme']['extraData'] = array();
							break;
						}
					}
				}
				$data = array(
					'type'=>$value['actionType'],
					'schemeData'=>array(
						'action'=> isset($value['scheme']['action']) ? $value['scheme']['action']:'',
						'uri'=>$value['scheme']['uri']
					),
					'extraData'=>$value['scheme']['extraData']
				);

				break;
			case 'COMPONENT':
				if (isset($value['component']['clsName'])&&isset($value['component']['component'])) {

					if (!is_array($value['component']['extraData']) || empty($value['component']['extraData'])) {
						$value['component']['extraData'] = array();
					}else{
						foreach ($value['component']['extraData'] as $item) {
							if (!isset($item['key']) || !isset($item['value']) ) {
								$value['component']['extraData'] = array();
								break;
							}
						}
					}

					$data = array(
						'type'=>$value['actionType'],
						'componentData'=>array(
							'pkgName'=>$value['component']['component'],
							'clsName'=>$value['component']['clsName'],
						),
						'extraData'=>$value['component']['extraData']
					);
					unset($value['component']);
				}else{
					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'COMPONENT跳转信息出错');

					}else{
						result('该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'COMPONENT跳转信息出错');
					}
				}
				break;
			case 'URI':

				if (isset($value['uri']['uri'])) {
					$data = array(
						'type'=>$value['actionType'],
						'uriData'=>array(
							'uri'=>$value['uri']['uri']
						)
					);
					unset($value['uri']);
				}else{
					if ($error) {
						return $result = array('reason'=>'该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'URI跳转信息出错');

					}else{
						result('该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'URI跳转信息出错');
					}
				}
				break;

			default:
				if ($error) {
					return $result = array('reason'=>'该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'跳转信息出错');

				}else{
					result('该桌面'.$msg.'出错:id为'.(isset($value['slotId'])?$value['slotId']:$value['slotID']).'跳转信息出错');
				}
				break;
		}
		return $data ;
	}
	/**
	 * 桌面管理_基础数据检查跳转信息
	 *
	 * @param $value [array] 跳转类型的信息
	 * @return [array] 跳转类型的信息
	 */
	 function checkActionApp($value,$isApp=false)
	{
		switch ($value['type']) {
			case 'APP':
				if ( !isset($value['pkgName'])  ) {
					// $put['extra'] = array();
					return false;
				}elseif (!$isApp) {
					if (!isset($value['appName'])) {
						return false;
					}
				}

				$data = array(
					'type'=>$value['type'],
					'pkgName'=>$value['pkgName'],
				);
				if (!$isApp) {
					$data['appName'] = $value['appName'];
				}
				break;
			case 'ACTION':
				if ( !isset($value['action']) ) {
					return false;
				}elseif (!$isApp) {
					if ( !isset($value['appName']) || !isset($value['detailName'])) {
						return false;
					}
				}
				if (!is_array($value['extraData']) || empty($value['extraData'])) {
					$value['extraData'] = array();
				}else{
					foreach ($value['extraData'] as $aa =>$item) {
						if (!isset($item['key']) || !isset($item['value'])) {
							$value['extraData'] = array();
							break;
						}
						if (isset($item['type'])) {
							if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
								unset($value['extraData'][$aa]['type']);
							}
						}
					}
				}
				$data = array(
					'type'=>$value['type'],
					'action'=>$value['action'],
					'extraData' => $value['extraData']
				);
				if (!$isApp) {
					$data['appName'] = $value['appName'];
					$data['detailName'] = $value['detailName'];
				}
				break;
			case 'COMPONENT':
				if (!isset($value['clsName']) || !isset($value['component'])) {
					return false;
				}elseif (!$isApp) {
					if ( !isset($value['appName']) || !isset($value['detailName'])) {
						return false;
					}
				}
				// $put['extra'] = array();
				if (!is_array($value['extraData'])) {
					$value['extraData'] = array();
				}else{
					foreach ($value['extraData'] as $aa => $item) {
						if (!isset($item['key']) || !isset($item['value']) ) {
							$value['extraData'] = array();
							break;
						}
						if (isset($item['type'])) {
							if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
								unset($value['extraData'][$aa]['type']);
							}
						}
					}
				}
				$data = array(
					'type'=>$value['type'],
					'component'=>$value['component'],
					'clsName'=>$value['clsName'],
					'extraData' => $value['extraData'],
				);
				if (!$isApp) {
					$data['appName'] = $value['appName'];
					$data['detailName'] = $value['detailName'];
				}

				break;
			case 'SCHEME':
				if ( !isset($value['uri']) ) {

					return false;
				}elseif (!$isApp) {
					if ( !isset($value['appName']) || !isset($value['detailName'])) {
						return false;
					}
				}
				// $put['extra'] = array();
				if (!is_array($value['extraData'])) {
					$value['extraData'] = array();
				}else{
					foreach ($value['extraData'] as $aa => $item) {
						if (!isset($item['key']) || !isset($item['value']) ) {
							$value['extraData'] = array();
							break;
						}
						if (isset($item['type'])) {
							if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
								unset($value['extraData'][$aa]['type']);
							}
						}
					}
				}
				$data = array(
					'type'=>$value['type'],
					'uri'=>$value['uri'],
					'action'=>!isset($value['action']) ?'':$value['action'],
					'extraData' => $value['extraData'],
				);
				if (!$isApp) {
					$data['appName'] = $value['appName'];
					$data['detailName'] = $value['detailName'];
				}
				break;
			case 'URI':
				if (!isset($value['uri'])) {
					return false;
				}
				$data = array(
					'type'=>$value['type'],
					'uri'=>$value['uri'],
				);
				break;
			default:
				// $put['extra'] = array();
				return false;
				break;
		}
		return $data;
	}
	/**
	 * 桌面管理_基础数据检查快捷坑位与运营坑位跳转信息
	 *
	 * @param $value [array] 跳转类型的信息
	 * @return [array] 跳转类型的信息
	 */
	function checkQuickOperationActionApp($put)
	{
		switch ($put['actionType']) {
			case 'APP':
				if (empty($put['app']['appName']) || empty($put['app']['pkgName'])  ) {
					return false;
				}
				$actionArr=array(
					'appName'=>$put['app']['appName'],
					'pkgName'=>$put['app']['pkgName']
				);
				break;
			case 'ACTION':
				if (empty($put['action']['appName']) || empty($put['action']['detailName']) || empty($put['action']['action']) ) {
					return false;
				}
				if (!is_array($put['action']['extraData']) || empty($put['action']['extraData'])) {
					$put['action']['extraData'] = array();
				}else{
					foreach ($put['action']['extraData'] as $key => $value) {
						if (!isset($value['key']) || !isset($value['value'])) {
							$put['action']['extraData'] = array();
							break;
						}
						if (isset($value['type'])) {
							if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
								unset($put['action']['extraData'][$key]['type']);
							}
						}
					}
				}

				$extraData = json_encode($put['action']['extraData'],JSON_UNESCAPED_UNICODE);

				$actionArr=array(
					'appName'=>$put['action']['appName'],
					'detailName'=>$put['action']['detailName'],
					'action'=>$put['action']['action'],
					'extraData'=>$extraData,
				);

				break;
			case 'COMPONENT':
				if (empty($put['component']['appName']) || empty($put['component']['detailName']) || empty($put['component']['component']) || empty($put['component']['clsName']) ) {
					return false;
				}
				if (!is_array($put['component']['extraData']) || empty($put['component']['extraData'])) {
					$put['component']['extraData'] = array();
				}else{
					foreach ($put['component']['extraData'] as $key => $value) {
						if (!isset($value['key']) || !isset($value['value']) ) {
							$put['component']['extraData'] = array();
							break;
						}
						if (isset($value['type'])) {
							if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
								unset($put['component']['extraData'][$key]['type']);
							}
						}
					}
				}
				$extraData = json_encode($put['component']['extraData'],JSON_UNESCAPED_UNICODE);
				$actionArr=array(
					'appName'=>$put['component']['appName'],
					'detailName'=>$put['component']['detailName'],
					'component'=>$put['component']['component'],
					'clsName'=>$put['component']['clsName'],
					'extraData'=>$extraData,
				);
				break;
			case 'SCHEME':
				if (empty($put['scheme']['appName']) || empty($put['scheme']['detailName']) || empty($put['scheme']['uri']) ) {
					return false;
				}

				if (!is_array($put['scheme']['extraData']) || empty($put['scheme']['extraData'])) {
					$put['scheme']['extraData'] = array();
				}else{
					foreach ($put['scheme']['extraData'] as $key => $value) {
						if (!isset($value['key']) || !isset($value['value']) ) {
							$put['scheme']['extraData'] = array();
							break;
						}
						if (isset($value['type'])) {
							if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
								unset($put['scheme']['extraData'][$key]['type']);
							}
						}
					}
				}
				$extraData = json_encode($put['scheme']['extraData'],JSON_UNESCAPED_UNICODE);
				$actionArr=array(
					'appName'=>$put['scheme']['appName'],
					'detailName'=>$put['scheme']['detailName'],
					'uri'=>$put['scheme']['uri'],
					'action'=> empty($put['scheme']['action'])?'':$put['scheme']['action'],
					'extraData'=>$extraData,
				);
				break;
			case 'URI':
				if (empty($put['uri']['uri']) || empty($put['uri']['uriName']) ) {

					result('uri数据类型数据出错');
				}
				$actionArr=array(
					'uri'=>$put['uri']['uri'],
					'uriName'=>$put['uri']['uriName'],
				);

				break;
			default:
				return false;
				break;
		}
		$actionArr['actionType'] = $put['actionType'];
		return $actionArr;
	}
	/**
	 * 桌面管理_基础数据检查快捷坑位与运营坑位跳转信息
	 *
	 * @param $value [array] 跳转类型的信息
	 * @return [array] 跳转类型的信息
	 */
	function getQuickOperationActionApp($put)
	{
		$data['actionType'] = $put['actionType'];
		switch ($data['actionType']) {
			case 'APP':
				$data['app']['appName'] = $put['appName'];
				$data['app']['pkgName'] = $put['pkgName'];
				break;
			case 'URI':
				$data['uri']['uri'] = $put['uri'];
				$data['uri']['uriName'] = $put['uriName'];
				break;
			case 'ACTION':
				$data['action']['appName'] = $put['appName'];
				$data['action']['detailName'] = $put['detailName'];
				$data['action']['action'] = $put['action'];
				$data['action']['extraData'] =  json_decode($put['extraData'],true);
				break;
			case 'COMPONENT':
				$data['component']['appName'] = $put['appName'];
				$data['component']['detailName'] = $put['detailName'];
				$data['component']['component'] = $put['component'];
				$data['component']['clsName'] = $put['clsName'];
				$data['component']['extraData'] =  json_decode($put['extraData'],true);
				break;
			case 'SCHEME':
				$data['scheme']['appName'] = $put['appName'];
				$data['scheme']['detailName'] = $put['detailName'];
				$data['scheme']['uri'] = $put['uri'];
				$data['scheme']['action'] = $put['action'];
				$data['scheme']['extraData'] =  json_decode($put['extraData'],true);
				break;
			default:
				// $res['extra']= array();
				break;
		}
		return $data;
	}
?>
