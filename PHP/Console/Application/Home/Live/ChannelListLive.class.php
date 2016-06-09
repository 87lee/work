<?php
	namespace Home\Live;
	class ChannelListLive extends \Think\Model
	{
		protected $tableName = 'channel_list';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'typeId'  =>'type_id',
			'channelId'  =>'channel_id',
			'channelName'  =>'channel_name',
		);

		public function getValForTypeId($typeId)
		{
			$where= "type_id ='".$typeId."'";

			return $this->where($where)->select();
		}

		public function addChannelList($optons)
		{

			if (!empty($optons['type_id'])&&$optons['channel_id']) {
				if ( !$this->getOneForTypeIdForChannelId($optons['type_id'],$optons['channel_id']) ) {
					return $this->add($optons);
				}
			}
			return;

		}
		public function getOneForTypeIdForChannelId($typeId,$channelId)
		{
			return $this->where("type_id = '%s' and channel_id = '%s'" ,array($typeId,$channelId))->find();
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

	}