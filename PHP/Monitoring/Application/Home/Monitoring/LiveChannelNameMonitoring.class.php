<?php
	namespace Home\Monitoring;
	class LiveChannelNameMonitoring extends \Think\Model
	{
		protected $tableName = 'live_channel_name';
		protected $connection = 'DB_MONITORING';
		protected $_map = array(
			'appName' =>'channel_id',
			'pkgName'  =>'pkgname',
		);


		public function getChannelID($get)
		{


			if (!empty($get['id'])) {
				$field  = 'channel_id as id,name';
				$res['extra'] = $this->field($field)->where('parent_id = %d',array($get['id']))->select();
			}else{
				$noField  = 'channel_id,parent_id';
				$res['extra'] = $this->field($noField,true)->where('parent_id = 0')->select();
			}
			return $res;
		}

	}