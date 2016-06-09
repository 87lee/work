<?php 

include 'ini.php';
$put =file_get_contents('php://input');
$put = json_decode($put,true);
if (!empty($put['mac'])&&!empty($put['id'])) {
	$where = array(
		'where'=>'mac = "'.$put['mac'].'" and id !='.$put['id']
	);
	$mac = $db->getOne('mac',$where);
	if (!empty($mac)) {
		result('MAC已存在');
	}
	$options = array(
		'mac'=>$put['mac'],
		'update_time'=>time()
	);
	$where = 'id='.$put['id'];
	
	$db->update('mac',$options,$where);
	result();
}else{
	result('param');
}

?>