<?php
	namespace Home\Desktop;
	class DesktopBlockPicDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_block_pic';
		protected $connection = 'DB_DESKTOP';

		public function addDesktopBlockPic($options)
		{
			return $addId = $this->add($options);

		}
		public function deleteDesktopBlockPic($desktopScreensSql)
		{
			if ( $this->where("`block_id` IN (".$desktopScreensSql.")")->find()) {
				return $this->where("`block_id` IN (".$desktopScreensSql.")")->delete();
			}

		}
		public function layouInfo($blockInfoId)
		{
			$res = $this->field('pic')->where("`block_id` = %d",array($blockInfoId))->find();
			if (empty($res)) {
				$res = array();
			}
			return $res;
		}
		public function modifyDesktopBlockPic($desktopBlockInfoId,$options)
		{
			$res = $this->where("`block_id` = %d",array($desktopBlockInfoId))->find();
			if ($res) {
				$this->where("`block_id` = %d",array($desktopBlockInfoId))->save($options);
			}else{
				$options['block_id'] = $desktopBlockInfoId;
				$this->addDesktopBlockPic($options);
			}
		}
		public function getArrForBlockId($BlocksId)
		{
			return $this->where("`block_id` = %d",array($BlocksId))->select();
		}
	}