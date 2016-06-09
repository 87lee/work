<?php 

include 'ini.php';
$put =file_get_contents('php://input');
$put = json_decode($put,true);
if (!empty($put['mac'])) {
	$put['mac'] = strtolower($put['mac']);
	$where = array(
		'where'=>'mac = "'.$put['mac'].'"'
	);
	$mac = $db->getOne('mac',$where);
	if (!empty($mac)) {
		result('MAC已存在');
	}
	$options = array(
		'mac'=>$put['mac'],
		'create_time'=>time(),
		'update_time'=>time()
	);
	$add = $db->insert('mac',$options);
	result();
}else{
	result('param');
}

?>