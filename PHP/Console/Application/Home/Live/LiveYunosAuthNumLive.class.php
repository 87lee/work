<?php
	namespace Home\Live;
	class LiveYunosAuthNumLive extends \Think\Model
	{

		protected $tableName = 'yunos_auth_num';

		protected $connection = 'DB_LIVE_AUTH_DETAIL';
		protected $_map = array(
			'vendorID' =>'vendor_id',
		);
		public function getNumForModelSql($modelSql)
		{
			return $this->field("model,num,vendor_id")->where(" `model` IN ($modelSql) ")->select();
		}

	}