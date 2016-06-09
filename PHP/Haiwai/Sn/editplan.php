<?php 

include './ini/ini.php';
$admin = new Admin();

//post json /editplan.php 一维json{"canSubscribe":1}
//参数id 是必须要填的 
//参数 canSubscribe,dramaType,expirationReminderDayCount,expirationReminderWords,explanation,id,isHidden,isPhonePlan,liveChannelIds,movieType,name,onlineChannelIds,price,releaseList,sort,unit,youtubeType,
$arr_data = file_get_contents('php://input', 'r');

$arr_data = json_decode($arr_data,true);

if ($arr_data === null) {

  die('请输入正确json参数！');

}

if (!isset($arr_data['id'])) {
  die('请加上参数id！');
}

$admin->redis->set('plan_detail_'.$arr_data['id'],json_encode($arr_data));

 ?>