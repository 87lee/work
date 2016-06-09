<?php
	namespace Home\Desktop;
	class DesktopTimebarDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_timebar';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'isShowIndicator' =>'is_show_indicator',
			'desktopId'=>'desktop_id',
			'timeFormat'=>"time_format"
		);
		public function addDesktopTimebar($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}

		public function deleteDesktopTimebar($desktopId)
		{
			if ($this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				return $this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}

		}
		public function deleteDesktopArrTimebar($desktopId)
		{
			if ( $this->where("`desktop_id` IN (".$desktopId.")")->find()) {

				return $this->where("`desktop_id`  IN (".$desktopId.")")->delete();
			}
		}

		public function timebarLists($desktopId)
		{
			$res = $this->field("is_show_indicator,time_format,x,y,style")->where("`desktop_id`=%d",array($desktopId))->find();
			return $res;
		}
		public function createTimeConfig($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = array();
			if ($desktop ===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
					$desktopJson = $this->field("`style`,`x`,`y`,`time_format` as timeFormat,is_show_indicator as isShowIndicator")->where("`desktop_id`=%d",array($desktopId))->find();
				if (!empty($desktopJson)) {
					// echo json_encode($desktopJson,JSON_UNESCAPED_UNICODE);
					return $desktopJson;
				}else{
					return false;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面不存在');
					// return false;
				}else{
					result('桌面不存在');
				}
			}
		}
		/*
		public function modifyDesktopBlockInfo($blockId,$options)
		{
			$res = $this->where("`block_id` =%d ",array($blockId))->find();
			if ($res) {
				$this->where("`block_id` =%d ",array($blockId))->save($options);
				return $res['id'];
			}else{
				$options['block_id'] = $blockId;
				return $this->addDesktopBlockInfo($options);
			}
		}*/
	}