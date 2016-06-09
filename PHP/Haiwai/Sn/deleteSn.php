<?php 
//参数  sn_id
//需要 get 需要删除的sn_id   /delsn.php?sn_id=XXXX
include './ini/ini.php';
if(isset($_GET['sn_id'])&&!empty($_GET['sn_id'])){

	$admin = new Admin();
  $id = $_GET['sn_id'];
  if ($admin->redis->sismember('sn_lists',$id)) {
  		$admin->delSn($id);
      	result();
  }else{
        result('账号:' . $id . '已不存在！');
  }
}else{
    result('param');
  }

?>
 