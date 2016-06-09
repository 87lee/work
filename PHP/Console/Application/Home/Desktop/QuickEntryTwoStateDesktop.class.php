<?php
	namespace Home\Desktop;
	class QuickEntryTwoStateDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_two_state';
		protected $connection = 'DB_DESKTOP';

		public function addQuickEntry($name,$x,$y)
		{
			$options = array(
				'name'=>$name,
				'x'=>$x,
				'y'=>$y,
			);
			return $this->add($options);
		}
		public function deleteQuickEntry($id)
		{
			$res = $this->find($id);
			if ($res) {
				if ($this->delete($id)) {
					result();
				}else{
					result('unknown');
				}

			}else{
				result('快捷入口不存在');
			}
		}
		public function modifyQuickEntry($id,$name,$x,$y)
		{
			$options = array(
				'name'=>$name,
				'x'=>$x,
				'y'=>$y,
				// 'interval'=>$interval
			);
			return $this->where("`id`=%d",array($id))->save($options);
		}
		public function getValOneForName($name)
		{
			return $this->where("`name`='%s'",array($name))->find();
		}
		public function getValOneForId($id)
		{
			return $this->where("`id`=%d",array($id))->find();
		}
		public function getValALL()
		{
			return $this->select();
		}
		public function getValOneForNameForNotId($name,$id)
		{
			return $this->where("`name`='%s' and `id` !=%d",array($name,$id))->find();
		}
	}