<?php
	namespace Home\Desktop;
	class DesktopQuickListDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_quick_list';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopId'=>'desktop_id',
			"pkgName"=>'pkgname',
			 "appIcon"=>'app_icon',
			 "appName"=>'app_name',
			 "apkUrl"=>'apk_url',
			 'versionCode'  =>'version_code'
		);
		public function addDesktopQuickList($desktopId,$options)
		{
			foreach ($options as  $value) {
				$value['desktopId'] = $desktopId;
				if (isset($value['id'])) {
					unset($value['id']);
				}
				$res[] = $this->create($value);
			}

			$this->addAll($res);
		}
		public function deleteDesktopQuickList($desktopId)
		{
			if ($this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				return $this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}

		}
		public function deleteDesktopArrQuickList($desktopId)
		{
			if ( $this->where("`desktop_id` IN (".$desktopId.")")->find()) {

				return $this->where("`desktop_id`  IN (".$desktopId.")")->delete();
			}

		}
		public function desktopQuickLists($desktopId)
		{
			 // $res = $this->field('`id`,`desktop_id` as desktopId,`x`,`y`,`style`,`is_show_indicator` as isShowIndicator,attache_id as')->where("`desktop_id`=%d",array($desktopId))->find();
			 $res = $this->field("`index`,`pkgname`,`app_icon`,`app_name`,`apk_url`,`version_code`,`title`")->where("`desktop_id`=%d",array($desktopId))->select();

			if (empty($res)) {
				$res=array();
			}
			return $res;
		}
		public function modifyDesktopQuickList($desktopId,$options){
			if ($this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				$this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}
			$this->addDesktopQuickList($desktopId,$options);
		}
		public function deleteDesktopQuickListArr($desktopIdSql)
		{
			if ($this->where("`desktop_id` IN (".$desktopIdSql.") ")->find()) {
				$this->where("`desktop_id` IN (".$desktopIdSql.")")->delete();
			}
		}
		public function createQuickList($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = array();
			if ($desktop ===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
					$desktopJson['apps'] = $this->field("`index` as id ,`pkgname`,`app_icon`,`apk_url`,IF(`title`!='',`title`,`app_name`) as appName")->where("`desktop_id`=%d",array($desktopId))->select();
				if (!empty($desktopJson['apps'])) {

					/*echo json_encode($desktopJson,JSON_UNESCAPED_UNICODE);
					die;*/
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