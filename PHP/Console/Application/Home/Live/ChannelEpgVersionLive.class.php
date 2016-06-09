<?php
	namespace Home\Live;
	class ChannelEpgVersionLive extends \Think\Model
	{
		protected $tableName = 'channel_epg_version';
		protected $connection = 'DB_LIVE';
		/*protected $_map = array(
			'typeId'  =>'type_id',
		);*/

		public function getOneForVersionDate($version,$date)
		{
			return $this->where('version = %d and date = %d',array($version,$date))->find();
		}
		public function modifyVersionForVersionDate($version,$date)
		{
			if (!empty($version)&&!empty($date)) {
				$options = array(
					'version'=>$version,
					'date'=>$date
				);
			}
			if ($res = $this->getOneForVersionDate($version,$date)) {
				return $this->where("id=%d",array($res['id']))->save($options);
			}else{
				return $this->add($options);
			}

		}
	}