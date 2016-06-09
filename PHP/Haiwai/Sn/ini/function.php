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
	              	$data['reason'] = '登录超时，请重新登录';
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
	              	$data['reason'] = '未知错误';
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

 