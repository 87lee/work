<?php
	namespace Home\Desktop;
	class QuickEntryGroupItemsDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_group_items';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'groupId'  =>'group_id', // 把表单中的actionId映射到数据表的action_id字段
		);
		public function addQuickEntryGroupItems($quickEntryGroupId,$options)
		{

			$options['group_id'] = $quickEntryGroupId;

			if ($this->create($options)) {
				return $this->add($res);
			}
			return ;

		}
		public function deleteQuickEntryGroupItemsForGroupId($groupId)
		{
			if ($this->where("`group_id`=%d",array($groupId))->find()) {
				$this->where("`group_id`=%d",array($groupId))->delete();
			}
			return true;
		}
		public function getQuickEntryGroupItemsListsForGroupId($groupId)
		{
			return $this->where("group_id IN (%s)",array($groupId))->select();
		}
		public function getQuickEntryGroupItemsIdSqlForGroupId($groupId)
		{
			return $this->field('id')->where("group_id IN (%s)",array($groupId))->select(false);
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