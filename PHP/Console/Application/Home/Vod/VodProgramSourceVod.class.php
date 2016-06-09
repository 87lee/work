<?php
	namespace Home\Vod;
	class VodProgramSourceVod extends \Think\Model
	{
		protected $tableName = 'vod_program_source';
		protected $tablePrefix = '' ;
		protected $connection = 'DB_VOD';

		/*protected $_map = array(
			'picUrl'=>'thumb',
			'bigPicUrl'=>'picture',
		);*/
		public function getProgramApp($get)
		{
			if (!empty($get['id'])) {
				$get['id'] = intval($get['id']);
				return $this->field('b.id,b.name,a.link_data as skipid, case when a.is_pay  = 0 then "false"  when a.is_pay  = 1 then "true" end as pay')->alias(" a ")->where("a.program_id =%d ",array($get['id']))->join(" LEFT JOIN vod_app_source  AS b ON b.id = a.app_source_id")->select();
			}
			return ;
		}
		public function getOneForProgramIdLinkData($programId,$linkData)
		{
			return $this->where("program_id = %d and link_data = '%s'" ,array($programId,$linkData))->find();
		}

	}