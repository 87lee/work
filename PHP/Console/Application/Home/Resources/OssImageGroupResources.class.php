<?php
	namespace Home\Resources;
	class OssImageGroupResources extends \Think\Model
	{
		protected $tableName = 'oss_image_group';
		protected $connection = 'DB_RESOURCES';
		public function addImageGroup($put)
		{
			if (empty($put['group'])) {
				result('param');
			}
			if ($this->where("`group`='%s'",array($put['group']))->find()) {
				result('图片组已存在');
			}
			$options = array(
				'group'=>$put['group']
			);
			$this->add($options);
			return ;
		}
		public function deleteImageGroup($get)
		{
			if (empty($get['id'])) {
				result('param');
			}
			;
			if (!$res = $this->find($get['id'])) {
				result('图片组不存在');
			}

			if ($res = D('OssImage','Resources')->getOneForGroupId($get['id'])) {
				result('图片组存在图片');
			}

			$this->delete($get['id']);

			return ;

		}
		public function imageGroupLists()
		{
			$res['extra'] = $this->select();
			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			return $res;
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
	}