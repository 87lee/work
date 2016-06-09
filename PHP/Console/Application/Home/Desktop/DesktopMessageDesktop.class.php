<?php
	namespace Home\Desktop;
	class DesktopMessageDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_message';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopId'=>'desktop_id',
			'fontSize'=>'font_size',
		);
		public function addDesktopMessage($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}

		public function deleteDesktopMessage($desktopId)
		{
			if ($this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				return $this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}
		}
		public function deleteDesktopArrStyle($desktopId)
		{
			if ( $this->where("`desktop_id` IN (".$desktopId.")")->find()) {
				return $this->where("`desktop_id`  IN (".$desktopId.")")->delete();
			}
		}
		public function messageLists($desktopId)
		{
			$res = $this->field("desktop_id,id",true)->where("`desktop_id`=%d",array($desktopId))->find();

			return $res;
		}
		public function createMessageConfig($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = $this->field("desktop_id,id",true)->where("`desktop_id`=%d",array($desktopId))->find();

			return $desktopJson;
			/*$desktopJson = array();
			if ($desktop===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
				$desktopJson = $this->field("`json_text`")->where("`desktop_id`=%d",array($desktopId))->find();
				if (!empty($desktopJson)) {
					$desktopJson = json_decode($desktopJson['jsonText'],true);
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
			}*/
		}

	}