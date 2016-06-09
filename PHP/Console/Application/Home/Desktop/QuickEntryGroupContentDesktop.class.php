<?php
	namespace Home\Desktop;
	class QuickEntryGroupContentDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_group_content';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'forcusPath' =>'forcus_path', // 把表单中name映射到数据表的username字段
			'normalPath'  =>'normal_path', // 把表单中的mail映射到数据表的email字段
			'groupItemId'  =>'group_item_id', // 把表单中的actionId映射到数据表的action_id字段
			'actionInfo'  =>'action_info', // 把表单中的actionId映射到数据表的action_id字段

		);
		public function addQuickEntryGroupContent($quickEntryGroupItemsId,$options)
		{
			foreach ($options as $key => $value) {
				if (!empty($value)) {
					$value['group_item_id'] = $quickEntryGroupItemsId;
					$res[] = $this->create($value);
				}
			}
			$this->addAll($res);
		}
		public function deleteQuickEntryItems($id)
		{
			if ($this->where("`quick_id`=%d",array($id))->find()) {
				$this->where("`quick_id`=%d",array($id))->delete();
			}

			return true;
		}

		public function getQuickEntryGroupContentListsForGroupId($groupId)
		{
			$res =  $this->where("group_item_id IN (%s)",array($groupId))->select();
			$data = array();

			if (!empty($res)) {
				foreach ($res as $key => $value) {
					$data[$key] = array_merge($value,json_decode($value['actionInfo'],true)) ;
					$data[$key]['forcusPath'] = C('DOWNLOAD_IMG_PREFIX_HOST').$value['forcusPath'];
					$data[$key]['normalPath'] = C('DOWNLOAD_IMG_PREFIX_HOST').$value['normalPath'];
				}
			}
			return $data;
		}
		public function modifyQuickEntryItems($quickEntrytId,$options)
		{
			if ($this->where("`quick_id`=%d",array($quickEntrytId))->find()) {
				$this->where("`quick_id`=%d",array($quickEntrytId))->delete();
			}

			foreach ($options as $key => $value) {
				if (!empty($value)) {
					$value['quick_id'] = $quickEntrytId;
					$value['quickId'] = $quickEntrytId;
					$res[] = $this->create($value);
				}
			}
			if (!empty($res)) {
				$this->addAll($res);
			}

		}
	}