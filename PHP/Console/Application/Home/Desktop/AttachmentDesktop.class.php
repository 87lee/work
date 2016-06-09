<?php
	namespace Home\Desktop;
	class AttachmentDesktop extends \Think\Model
	{
		protected $tableName = 'attachment';
		protected $connection = 'DB_DESKTOP';

		public function addAttachment($name,$x,$y,$interval)
		{
			$options = array(
				'name'=>$name,
				'x'=>$x,
				'y'=>$y,
				'interval'=>$interval
			);
			return $this->add($options);
		}
		public function deleteAttachment($id)
		{
			$res = $this->find($id);
			if ($res) {
				/*$res = D('ActionInfo','Desktop')->where("`action`='%s'",array($res['action']))->find();
				if (!$res) {*/
				if ($this->delete($id)) {
					result();
				}else{
					result('unknown');
				}
				/*}else{
					result('操作正在使用');
				}*/
			}else{
				result('附件不存在');
			}
		}

		public function modifyAttachment($id,$name,$x,$y,$interval)
		{
			$options = array(
				'name'=>$name,
				'x'=>$x,
				'y'=>$y,
				'interval'=>$interval
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
		public function getValAll()
		{
			return $this->select();
		}
		public function getValOneForNameForNotId($name,$id)
		{
			return $this->where("`name`='%s' and `id` !=%d",array($name,$id))->find();
		}
	}