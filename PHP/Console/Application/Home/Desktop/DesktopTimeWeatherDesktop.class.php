<?php
	namespace Home\Desktop;
	class DesktopTimeWeatherDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_time_weather';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopId'=>'desktop_id',
		);
		public function addDesktopTimeWeather($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}

		public function deleteDesktopTimeWeather($desktopId)
		{
			if ($this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				return $this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}


		}
		public function deleteDesktopArrWeather($desktopId)
		{
			if ( $this->where("`desktop_id` IN (".$desktopId.")")->find()) {
				return $this->where("`desktop_id`  IN (".$desktopId.")")->delete();
			}
		}

		public function timeWeatherLists($desktopId)
		{
			return $res= $this->field("x,y,style")->where("`desktop_id`=%d",array($desktopId))->find();

		}
		public function createTimeWeatherConfig($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = array();
			if ($desktop===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
				$desktopJson = $this->field("`style`,`x`,`y`")->where("`desktop_id`=%d",array($desktopId))->find();
				if (!empty($desktopJson)) {

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