<?php
	namespace Home\Desktop;
	class DesktopStyleDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_style';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopId'=>'desktop_id',
			'jsonText'=>'json_text',
		);
		public function addDesktopStyle($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}

		public function deleteDesktopStyle($desktopId)
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
		public function styleLists($desktopId)
		{
			$res = $this->where("`desktop_id`=%d",array($desktopId))->find();
			if ($res) {
				$res = json_decode($res['jsonText'],true);
			}

			return $res;
		}
		public function createStyleConfig($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = $this->field("`json_text`")->where("`desktop_id`=%d",array($desktopId))->find();
			if (!empty($desktopJson)) {
				$desktopJson = json_decode($desktopJson['jsonText'],true);
				// echo json_encode($desktopJson,JSON_UNESCAPED_UNICODE);
			}
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