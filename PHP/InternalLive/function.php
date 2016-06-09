<?php 
	
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
			die();
		}
	}
	function isLogin()
	{
		header('Content-type:text/plain;charset=utf-8');
		if (strstr($_SERVER['REQUEST_URI'], '/desktop/modifyHYDesktop') == false) {
			if (strstr($_SERVER['REQUEST_URI'], '/user/login')==false) { 
			            if (empty(session('is_login'))) {
		            		result('login');
			            }else{
		            		$isLogin = session('is_login');
		            		session('[regenerate]'); 
		            		session('is_login',$isLogin);
			            }
		      	}
		}
		
	}
	/**
	 * 删除非空目录
	 * 
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

?>
