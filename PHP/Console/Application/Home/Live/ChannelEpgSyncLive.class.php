<?php
	namespace Home\Live;
	class ChannelEpgSyncLive extends \Think\Model
	{
		protected $tableName = 'channel_epg_sync';
		protected $connection = 'DB_LIVE';
		/*protected $_map = array(
			'typeId'  =>'type_id',
		);*/
		public function getArrForDate($get)
		{
			$where = '';
			if (!empty($get['date']) ) {
				$where = " date ='" .$get['date'] . "'";
			}
			return $this->where($where)->select();
		}
		public function modifyEpg($id,$epg)
		{
			if (!empty($epg)&&!empty($id)) {
				$modifyOption = array(
					'epg'=>$epg
				);
				if ($this->find($id)) {
				 	$this->where("id =%d",array($id))->save($modifyOption);
				 }
			}
			return;
		}
		public function addEpgArr($arr)
		{
			foreach ($arr as $key => $value) {
				$addOption[] = $this->create($value);
				if ( ($key%10) === 0 ) {
					$this->addAll($addOption);
					$addOption = array();
				}
				unset($arr[$key]);
			}
			if (!empty($arr)) {
				$arr = array_values($arr);
				$this->addAll($arr);
			}
			return ;
		}
	}