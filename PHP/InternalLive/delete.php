<?php 

include 'ini.php';
$id =$_GET['id'];
if (!empty($id)) {
	$where = array(
		'where'=>'id = "'.$id.'"'
	);
	$mac = $db->getOne('mac',$where);
	if (!empty($mac)) {
		$where ='id = "'.$id.'"';
		$db->delete('mac',$where);
	}
	result();
}else{
	result('param');
}

?>