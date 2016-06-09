<?php
	namespace Home\Live;
	class ChannelEpgLive extends \Think\Model
	{
		protected $tableName = 'channel_epg';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'typeId'  =>'type_id',
		);

		public function getChannelForTypeIdSqlDate($typeId,$date)
		{
			$where= "type_id  IN (".$typeId.") and date = '".$date."'";

			return $this->where($where)->select();
		}

		public function addChannelEpg($optons)
		{
			if ($this->create($optons)) {
				return $this->add();
			}
			return;
		}
		public function getOneForTypeIdForChannelId($typeId,$channelId)
		{
			return $this->where("type_id = '%s' and channel_id = '%s'" ,array($typeId,$channelId))->find();
		}
		public function getChannelForDate($channelId)
		{
			return $this->where("channel_id = '%s'" ,array($channelId))->select();
		}
		public function deleteChannelForID($id)
		{
			 if ($this->find($id)) {
				$this->where("id = %d",array($id))->delete();
			}
			return ;
		}
		public function getChannelList($get)
		{
			if (!empty($get['nameId'])) {
				$field = 'channel_id as id,channel_name as name';
				$where = "type_id = '%s' ";
				$res['extra'] = $this->field($field)->where($where,array($get['nameId']))->select();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			return $res;
		}
		public function getAllGroupByChannleId()
		{
			return $this->field('channel_id,channel_name')->group('channel_id')->select();
		}
		public function getAllChannel()
		{
			return $this->select();
		}
		public function modifyChannelEpg($id,$options)
		{
			return $this->where("id = %d",array($id))->save($options);
		}

	}