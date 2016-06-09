<?php
	namespace Home\Resources;
	class OssFileGroupResources extends \Think\Model
	{
		protected $tableName = 'oss_file_group';
		protected $connection = 'DB_RESOURCES';
		public function addGroup($put)
		{
			if (empty($put['group'])) {
				result('param');
			}
			if ($this->where("`group`='%s'",array($put['group']))->find()) {
				result('资源组已存在');
			}
			$options = array(
				'group'=>$put['group']
			);
			$this->add($options);
			return ;
		}
		public function deleteGroup($get)
		{
			if (empty($get['id'])) {
				result('param');
			}
			;
			if (!$res = $this->find($get['id'])) {
				result('资源组不存在');
			}

			if ($res = D('OssFile','Resources')->getOneForGroupId($get['id'])) {
				result('资源组存在文件');
			}
			$this->delete($get['id']);
			return ;

		}
		public function groupLists($get)
		{
			$where = '';
			if (!empty($get['name'])) {
				$where = "group like  '%".$get['name']."%'";
			}
			if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = ($get['page']-1) * $get['pageSize'];
				$res['extra'] = $this->where($where)->limit($get['page'],$get['pageSize'])->select();
			}else{
				$res['extra'] = $this->where($where)->select();
			}
			$res['count'] = $this->where($where)->count();
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