<?php 
/**
 * 修改sn余额
 * 需要 get 需要修改余额的id   需要添加的余额或减少的余额数  /editsnbalance.php?sn_id=XXXX&balance=xxx
 */
include './ini/ini.php';
if(isset($_GET['sn_id'])&&!empty($_GET['sn_id'])){
 	
  	$admin = new Admin();
  	if (empty($_GET['sn_id'])) {
  		result('请填写用户');
  	}
	  $id = $_GET['sn_id'];
	  
	  if (!$admin->redis->sismember('sn_lists' , $id)) {
		result('这个sn号不存在！');
	}
	
	$balance = $admin->redis->get('sn_balance_' . $id);
	  if ( !empty($_GET['addbalance']) && !empty($_GET['cutbalance'] )) {
	  	result('请添加或减少余额！');
	  }elseif (!empty($_GET['addbalance'])) {
	  	$addbalance = (int)$_GET['addbalance'];
	  	$admin->addSnBalance($id,$addbalance);
	  	result();

	  }elseif (!empty($_GET['cutbalance'])) {
	  	$cutbalance = (int)$_GET['cutbalance'];
	  	
	  	if( ( $balance-$cutbalance ) < 0 ){
	  		result('您的余额不足！您的余额为：' . $balance);
	  	}
	  	$admin->delSnBalance($id,$cutbalance);
	  	result();
	  	
	  }
	
	
}else{
	result('param');
}

?>