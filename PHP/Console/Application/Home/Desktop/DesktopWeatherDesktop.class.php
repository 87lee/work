<?php
	namespace Home\Desktop;
	class DesktopWeatherDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_weather';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'isShowIndicator' =>'is_show_indicator',
			'desktopId'=>'desktop_id',
			'isShowCity'=>"is_show_city",
			'isShowTemperature'=>"is_show_temperature",
			'isShowDesc'=>"is_show_desc",
			'isShowIcon'=>"is_show_icon"
		);
		public function addDesktopWeather($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}

		public function deleteDesktopWeather($desktopId)
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

		public function weatherLists($desktopId)
		{
			return $res= $this->field("is_show_indicator,is_show_city,is_show_temperature,is_show_icon,is_show_desc,x,y,style")->where("`desktop_id`=%d",array($desktopId))->find();

		}
		public function createWeatherConfig($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = array();
			if ($desktop===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
					$desktopJson = $this->field("`style`,`x`,`y`,is_show_indicator as isShowIndicator,`is_show_city` as isShowCity,`is_show_temperature` as isShowTemperature,`is_show_desc` as isShowDesc,`is_show_icon` as isShowIcon")->where("`desktop_id`=%d",array($desktopId))->find();
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