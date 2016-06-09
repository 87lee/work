<?php 

include 'ini.php';
$put =file_get_contents('php://input');
$put = json_decode($put,true);
if (!empty($put['user'])&&!empty($put['passwd'])) {
	$where = array(
		'where'=>'user = "'.USER.'" and passwd = "'.PASSWD.'"',
	);
	if ($put['user'] !=USER || $put['passwd'] !=PASSWD) {
		result('用户名或密码错误');
	}
	$_SESSION['IS_LOGO'] = 1;
	result();
}else{
	result('param');
}

?>