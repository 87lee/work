<?php
	namespace Home\Desktop;
	class DesktopSnDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_sn';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'isShowIndicator' =>'is_show_indicator',
			'desktopId'=>'desktop_id',
			'prefixInfo'=>"prefix_info",
			'systemProperty'=>"system_property",
			'ipmacroProperty'=>"ipmacro_property",
		);
		public function addDesktopSn($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}

		public function deleteDesktopSn($desktopId)
		{
			if ($this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				return $this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}
		}
		public function deleteDesktopArrSn($desktopId)
		{
			if ($this->where("`desktop_id` IN (".$desktopId.")")->find()) {
				return $this->where("`desktop_id` IN (".$desktopId.") ")->delete();
			}
		}
		public function snLists($desktopId)
		{
			$res= $this->where("`desktop_id`=%d",array($desktopId))->find();
			if ($res) {
				unset($res['id']);
				unset($res['desktopId']);
			}

			return $res;
		}
		public function createSnConfig($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = array();
			if ($desktop===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
					$desktopJson = $this->field("`style`,`x`,`y`,is_show_indicator ,prefix_info,system_property,ipmacro_property")->where("`desktop_id`=%d",array($desktopId))->find();
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

	}