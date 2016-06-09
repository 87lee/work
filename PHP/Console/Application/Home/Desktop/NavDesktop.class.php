<?php
	namespace Home\Desktop;
	class NavDesktop extends \Think\Model
	{
		protected $tableName = 'nav';
		protected $connection = 'DB_DESKTOP';

		public function addNav($name,$x,$y,$interval)
		{
			$res = $this->where("`name`='%s'",array($name))->find();
			if (!$res) {
				$options = array(
					'name'=>$name,
					'x'=>$x,
					'y'=>$y,
					'interval'=>$interval
				);
				return $id = $this->add($options);

			}else{
				result('导航已存在');
			}

		}
		public function deleteNav($id)
		{
			$res = $this->find($id);
			if ($res) {
				return $this->delete($id);
			}else{
				result('导航不存在');
			}
		}
		public function modifyNav($id,$name,$x,$y,$interval)
		{

			$res = $this->find($id);
			if ($res) {
				$res = $this->where("`name`='%s' and `id` != %d",array($name,$id))->find();
				if (!$res) {
					$options = array(
						'name'=>$name,
						'x'=>$x,
						'y'=>$y,
						'interval'=>$interval
					);
					return $id = $this->where("`id` = %d",array($id))->save($options);
				}else{
					result('导航已存在');
				}

			}else{
				result('导航不存在');
			}

		}
		public function desktopNav($id)
		{
			return $this->field("`id`,`name`")->find($id);
		}
		public function getValOneForId($id)
		{
			return $this->find($id);
		}
		public function getAllOrderId()
		{
			return $this->order('id desc')->select();
		}
	}