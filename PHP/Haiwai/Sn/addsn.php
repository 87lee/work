<?php 
/**
 * 添加sn
 * 需要 get 需要添加的sn_id   /addsn.php?sn_id=XXXX
 *
 * 
 */
include './ini/ini.php';
if(isset($_GET['sn_id'])&&!empty($_GET['sn_id'])){
 	
  	$admin = new Admin();
      $id = $_GET['sn_id'];
      
      	if (!$admin->redis->sismember('sn_lists',$id)) {
	      	$admin->addSn($id);
	          result();
	      }else{
	      		result('账号:' . $id . '已存在！');
	      }
      
}else{
      	result('param');
      }

 ?>