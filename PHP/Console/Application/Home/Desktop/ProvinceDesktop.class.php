<?php
	namespace Home\Desktop;
	class ProvinceDesktop extends \Think\Model
	{
		protected $tableName = 'province';
		protected $connection = 'DB_DESKTOP';

		public function getAllName()
		{
			$res['extra'] = $this->field('name')->select();
			$res['count'] = $this->count();
			return $res;
		}
	}