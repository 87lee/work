<?php
	namespace Home\Desktop;
	class QuickListItemsDesktop extends \Think\Model
	{
		protected $tableName = 'quick_list_items';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'quickListId' =>'quick_list_id', // 把表单中name映射到数据表的username字段
			'appIcon'  =>'app_icon', // 把表单中的mail映射到数据表的email字段
			'appName' =>'app_name', // 把表单中name映射到数据表的username字段
			'apkUrl'  =>'apk_url',
			'pkgName'  =>'pkgname',
			'versionCode'  =>'version_code'
		);
		public function addQuickListArr($quickListId,$options)
		{
			foreach ($options as $key => $value) {
				$value['quickListId'] = $quickListId;
				$res[] = $this->create($value);
			}
			return $this->addAll($res);

		}
		public function deleteQuickListArr($quickListId)
		{
			if ($this->where("`quick_list_id`=%d",array($quickListId))->find()) {
				$this->where("`quick_list_id`=%d",array($quickListId))->delete();
			}
			return true;
		}
		public function modifyQuickListArr($quickListId,$options)
		{

			$this->deleteQuickListArr($quickListId);
			$this->addQuickListArr($quickListId,$options);
			return true;
		}

		public function quickLists($quickListId)
		{
			$res['extra'] = $this->field("app_icon,app_name,apk_url,pkgname,index,version_code,title")->where("`quick_list_id`=%d",array($quickListId))->select();
			return $res;
		}
		public function getValForQuickListId($QuickListId)
		{
			return $this->field("index,app_icon,app_name,apk_url,pkgname,version_code,title")->where("`quick_list_id`=%d",array($QuickListId))->select();
		}

	}