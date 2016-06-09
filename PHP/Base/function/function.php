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
			
			case 'param':
				$data['result'] = 'fail' ;
              	$data['reason'] = 'param error';
				break;
			
			case 'unknown':
				$data['result'] = 'fail' ;
              		$data['reason'] = 'unknown';
				break;
			default:
				$data['result'] = 'fail' ;
				$data['reason'] = $result;
				break;
		}
		echo json_encode($data);
		die();
	}
}
