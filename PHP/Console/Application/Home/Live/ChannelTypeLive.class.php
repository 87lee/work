<?php
	namespace Home\Live;
	class ChannelTypeLive extends \Think\Model
	{
		protected $tableName = 'channel_type';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'typeId'  =>'type_id',
			'typeName'  =>'type_name',
		);
		public function getAll()
		{
			return $this->select();
		}
		public function addChannelType($put)
		{

			if ($optons = $this->create($put)) {
				if (!$this->getOneForTypeId($optons['type_id'])) {
					return $this->add();
				}
			}
			return;

		}
		public function getOneForTypeId($typeId)
		{
			return $this->where('type_id ="%s" ',array($typeId))->find();
		}
		public function getChannelType()
		{
			$field = "type_id as nameId,type_name as name";
			$res['extra'] = $this->field($field)->select();
			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			return $res;
		}

	}