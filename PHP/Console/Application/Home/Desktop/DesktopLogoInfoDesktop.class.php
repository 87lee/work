<?php
	namespace Home\Desktop;
	class DesktopLogoInfoDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_logo_info';
		protected $connection = 'DB_DESKTOP';

		public function addDesktopLogoInfo($options)
		{
			return $this->addAll($options);
		}
		public function deleteDesktopLogoInfo($desktopLogoSql)
		{
			if ( $this->where("`desktop_logo_id` IN (".$desktopLogoSql.")")->find()) {
				return $this->where("`desktop_logo_id` IN (".$desktopLogoSql.")")->delete();
			}

		}
		public function getValOneFordDesktopLogoId($desktopLogoId)
		{
			return $this->where("`desktop_logo_id` IN (".$desktopLogoId.")")->find();
		}

		public function DesktopLogoLists($desktopLogoId)
		{
			return $res = $this->field("path")->where("`desktop_logo_id` IN (".$desktopLogoId.")")->select();
		}
		public function modifyDesktopLogoInfo($desktopLogoId,$options)
		{
			$res = $this->where("`desktop_logo_id` IN (".$desktopLogoId.")")->select();

			if (count($res) == count($options)) {
				foreach ($res as $key => $value) {
					$this->where("`id`=%d",array($value['id']))->save($options[$key]);
				}
			}elseif (count($res) > count($options)) {
				$modifyId = count($options);

				foreach ($res as $key => $value) {
					if ($modifyId <= 0) {
						$this->where("`id`=%d",array($value['id']))->delete();
					}else{
						$this->where("`id`=%d",array($value['id']))->save($options[$key]);
					}
					$modifyId--;
				}
			}elseif (count($res) < count($options)) {

				$modifyId = 0;
				foreach ($res as $key => $value) {
					$this->where("`id`=%d",array($value['id']))->save($options[$key]);
					$modifyId++;
				}
				for ($i=0; $i < $modifyId; $i++) {
					unset($options[$i]);
				}
				$options = array_values($options);
				$this->addAll($options);
			}
		}
	}