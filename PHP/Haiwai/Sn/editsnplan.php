<?php 
//更新用户套餐信息
//json
//[{"aaaa":"babb"},{"sn_id":111}]接收的参数  sn_id必填  
///editsnplan.php   [{"aaaa":"babb"},{"sn_id":111}]

include './ini/ini.php';
$admin = new Admin();
$arr_data = file_get_contents('php://input', 'r');

$arr_data = json_decode($arr_data,true);

if ($arr_data === null) {

  die('请输入正确json参数！');

}

foreach ($arr_data as $key => $value) {
	
	if (!empty($value['sn_id'])) {
		$sn_id = $value['sn_id'];
		unset($arr_data[$key]);
	}
}


if (!$admin->redis->sismember('sn_lists',$sn_id)) {
	die('这个sn号不存在！');
}

$arr_data = json_encode($arr_data);
$admin->redis->set('sn_plan_'. $sn_id,$arr_data);

 ?>