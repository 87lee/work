<?php 

include './ini/ini.php';
$admin = new Admin();

//post json /editchannellist.php 
//参数id 是必须要填的 
//参数 canSubscribe,dramaType,expirationReminderDayCount,expirationReminderWords,explanation,id,isHidden,isPhonePlan,liveChannelIds,movieType,name,onlineChannelIds,price,releaseList,sort,unit,youtubeType,
$arr_data = file_get_contents('php://input', 'r');

$arr_data = json_decode($arr_data,true);

if ($arr_data === null) {

  result('请输入正确json参数！');

}

$admin->redis->set('channel_list',json_encode($arr_data));
result();

 ?>