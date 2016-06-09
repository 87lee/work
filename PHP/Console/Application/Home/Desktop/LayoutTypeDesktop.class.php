<?php 
	namespace Home\Desktop;
	class LayoutTypeDesktop extends \Think\Model
	{
		protected $tableName = 'layout_type';
		protected $connection = 'DB_DESKTOP'; 

		public function addLayoutType($name,$type)
		{
			$options = array(
				'name'=>$name,
				'type'=>$type
			);
			$res = $this->where("`name`='%s' or `type`='%s'",array($name,$type))->find();
			if ($res) {
				result('布局类型或名字重复');
			}
			if ($this->add($options)) {
				return true;
			}else{
				result('unknown');
			}
		}
		public function deleteLayoutType($id)
		{
			$res = $this->find($id);
			if ($res) {
				
				if ($this->delete($id)) {
					result();
				}else{
					result('unknown');
				}
			}else{
				result('布局类型不存在');
			}
		}
		public function layoutTypeLists()
		{
			return $this->select();
		}
		
		public function modifyLayoutType($id,$name,$type)
		{
			$options = array(
				'name'=>$name,
				'type'=>$type
			);
			$res = $this->where("(`name`='%s' or `type`='%s') and id !=%d",array($name,$type,$id))->find();
			if ($res) {
				result('布局类型或名字重复');
			}
			if ($this->where("`id`=%d",array($id))->save($options)) {
				return true;
			}else{
				result('unknown');
			}
		}
	}