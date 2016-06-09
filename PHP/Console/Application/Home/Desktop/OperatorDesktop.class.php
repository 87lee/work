<?php
	namespace Home\Desktop;
	class OperatorDesktop extends \Think\Model
	{
		protected $tableName = 'operator';
		protected $connection = 'DB_DESKTOP';

		public function getAllName()
		{
			$res['extra'] = $this->field('name')->select();
			$res['count'] = $this->count();
			return $res;
		}
	}