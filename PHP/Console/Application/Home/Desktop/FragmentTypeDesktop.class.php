<?php
	namespace Home\Desktop;
	class FragmentTypeDesktop extends \Think\Model
	{
		protected $tableName = 'fragment_type';
		protected $connection = 'DB_DESKTOP';

		public function addFragmentType($name)
		{
			$res = $this->where("`name`='%s' ",array($name))->find();
			if (!$res) {
				$options = array(
					'name'=>$name,
				);
				return $addId = $this->add($options);

			}else{
				result('屏已存在');
			}
		}
		public function deleteFragmentType($id)
		{
			if ($this->delete($id)) {
				return true;
			}

		}
		public function fragmentTypeLists($id)
		{
			if (!empty($id)) {
				return $this->find($id);
			}else{
				return $this->select();
			}
		}
		public function modifyFragmentType($id,$name)
		{
			$res = $this->find($id);
			if ($res) {
				$res = $this->where("`name`='%s' and `id`!=%d",array($name,$id))->find();
				if (!$res) {
					$options = array(
						'name'=>$name,
					);
					$this->where("`id`=%d",array($id))->save($options);
					return true;

				}else{
					result('屏已存在');
				}
			}else{
				result('屏不存在');
			}

		}
		public function getValOneForId($id)
		{
			return $this->find($id);
		}
	}