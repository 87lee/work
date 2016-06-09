<?php
	namespace Home\Desktop;
	class DesktopBlockVideoDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_block_video';
		protected $connection = 'DB_DESKTOP';

		public function addDesktopBlockVideo($options)
		{
			return $addId = $this->add($options);
		}
		public function addDesktopBlockVideoArr($desktopBlockInfoId,$options)
		{
			foreach ($options as $key => $value) {
				$options[$key]['block_id']=$desktopBlockInfoId;
			}
			return $this->addAll($options);
		}
		public function deleteDesktopBlockVideo($desktopBlocksSql)
		{
			if ( $this->where("`block_id` IN (".$desktopBlocksSql.")")->find()) {
				return $this->where("`block_id` IN (".$desktopBlocksSql.")")->delete();
			}

		}
		public function layouInfo($blockInfoId)
		{
			$res = $this->field('duration,url')->where("`block_id` = %d",array($blockInfoId))->select();
			if (empty($res)) {
				$res = array();
			}
			return $res;
		}
		public function modifyDesktopBlockVideo($desktopBlockInfoId,$options)
		{
			if ($this->where("`block_id` = %d",array($desktopBlockInfoId))->find()) {
				$res = $this->where("`block_id` = %d",array($desktopBlockInfoId))->delete();
			}

			$this->addDesktopBlockVideoArr($desktopBlockInfoId,$options);
		}
		public function getArrForBlockId($BlocksId)
		{
			return $this->where("`block_id` = %d",array($BlocksId))->select();
		}
	}