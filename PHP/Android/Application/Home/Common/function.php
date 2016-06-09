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
	              			$data['reason'] = '未登录，请先登录系统';
					break;
				case 'isModifyPasswd':
					$data['result'] = 'fail' ;
	              			$data['reason'] = '请修改初始密码';
					break;
				case 'loginIp':
					$data['result'] = 'fail' ;
	              			$data['reason'] = '账号已在其它地方登录，请重新登录';
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

		if (strstr($_SERVER['REQUEST_URI'], '/user/login')==false) {
		            if (empty(session('androidIsLogin'))) {
	            			result('login');
		            }else{
	            		$isLogin = session('androidIsLogin');
	            		$user = D('User')->getOneForID($isLogin['id']);
	            		if ($user) {
	            			if ($user['loginIp'] != $isLogin['loginIp']) {
	            				result('loginIp');
	            			}
	            		}
	            		session('[regenerate]');
	            		session('androidIsLogin',$isLogin);
		            }
	      	}
	}
	/**
	 * 是否为系统管理员
	 * @param  string  $value [description]
	 * @return boolean        [description]
	 */
	function isAdmin($return=true)
	{
	    $user = session('androidIsLogin');
	    if(in_array(C('SPECIAL_GROUP.ADMIN'), $user['gids'])){
	        return $return?$user:true;
	    }else{
	        return false;
	    }
		/* $user = session('androidIsLogin');
		if ($user['admin'] === 'true') {
			return $user;
		}else{
			return false;
		} */
	}
	
	/**
	 * 是否是产品用户
	 * 
	 * 
	 * @param string $return
	 * @return Ambigous <boolean, mixed>|boolean
	 * @author 张涛<1353178739@qq.com>
	 * @since  2016年5月26日
	 */
	function isProduct($return=true)
	{
	    $user = session('androidIsLogin');
	    if(in_array(C('SPECIAL_GROUP.PRODUCT'), $user['gids'])){
	        return $return?$user:true;
	    }else{
	        return false;
	    }
	}
	
	/**
	 * 是否为超级管理员
	 * @param  string  $value [description]
	 * @return boolean        [description]
	 */
	function isRoot($return=true)
	{
	    $user = session('androidIsLogin');
	    if(in_array(C('SPECIAL_GROUP.ROOT'), $user['gids'])){
	        return $return?$user:true;
	    }else{
	        return false;
	    }
		/* $user = session('androidIsLogin');
		if ($user['user'] === 'root') {
			return $user;
		}else{
			return false;
		} */
	}
	/**
	 * 是否为测试用户
	 * @param  string  $value [description]
	 * @return boolean        [description]
	 */
	function isTester($return=true)
	{
	    $user = session('androidIsLogin');
	    if(in_array(C('SPECIAL_GROUP.TESTER'), $user['gids'])){
	        return $return?$user:true;
	    }else{
	        return false;
	    }
		/* $user = session('androidIsLogin');
		if ($user['tester']  === 'true') {
			return $user;
		}else{
			return false;
		} */

	}
	/**
	 *  是否为发布用户
	 * @param  string  $value [description]
	 * @return boolean        [description]
	 */
	function isPublisher($return=true)
	{
	    $user = session('androidIsLogin');
	    if(in_array(C('SPECIAL_GROUP.PUBLISHER'), $user['gids'])){
	        return $return?$user:true;
	    }else{
	        return false;
	    }
		/* $user = session('androidIsLogin');

		if ($user['publisher']  === 'true') {
			return $user;
		}else{
			return false;
		} */

	}
	/**
	 *  是否为普通用户
	 * @param  string  $value [description]
	 * @return boolean        [description]
	 */
	function isTourist()
	{
		$user = session('androidIsLogin');

		return $user;
		if ($user['tourist']  === 'true') {
			return $user;
		}else{
			return false;
		}

	}
	/**
	 *  是否为发布用户或者测试用户
	 * @param  string  $value [description]
	 * @return boolean        [description]
	 */
	function isPOrTOrAdmin()
	{
		$user = session('androidIsLogin');
		if ($user['admin'] === 'true') {
			return $user;
		}else{
			if ( $user['publisher']  === 'true' || $user['tester']  === 'true' ) {
				return $user;
			}else{
				return false;
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
