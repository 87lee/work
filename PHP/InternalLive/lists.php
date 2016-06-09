<?php 

include 'ini.php';
$get =$_GET;

if (!empty($get['id'])) {
	$where = array(
		'where'=>'id = "'.$id.'"'
	);
	$mac['extra'] = $db->getOne('mac',$where);
}elseif (!empty($get['name'])) {
	$where = array(
		'where'=>"mac like '%{$get['name']}%'",
		'fields'=>' id ,mac,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as create_time,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as update_time',
		'order'=>'update_time desc',
	);
	if (!empty($get['page'])&&!empty($get['pageSize'])) {
		$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
		$where['limit'] = $get['page'] .','.$get['pageSize'];
	}
	$res['extra'] = $db->getAll('mac',$where);
	$res['count'] = $db->getCount('mac',$where['where']);
}else{
	$where = array(
		'order'=>'update_time desc',
		'fields'=>' id ,mac,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as create_time,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as update_time',
	);
	if (!empty($get['page'])&&!empty($get['pageSize'])) {
		$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
		$where['limit'] = $get['page'] .','.$get['pageSize'];
	}
	if (!empty($where)) {
		$res['extra'] = $db->getAll('mac',$where);
	}else{
		$res['extra'] = $db->getAll('mac');
	}
	
	$res['count'] = $db->getCount('mac');
}
result(true,$res);
?>