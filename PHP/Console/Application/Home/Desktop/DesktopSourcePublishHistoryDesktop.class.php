<?php 
	namespace Home\Desktop;
	class DesktopSourcePublishHistoryDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_source_publish_history';
		protected $connection = 'DB_DESKTOP'; 

		public function addDesktopSourcePublishHistory($options)
		{
			$desktopLayoutId = $this->add($options);
		}
		
		/*public function deleteDesktopPublish($id)
		{
			$res = $this->find($id);
			if ($res) {
				if ($this->delete($id)) {
					return true;
				}else{
					result('unknown');
				}
				
			}else{
				result('发布桌面不存在');
			}
		}
		
		public function blockLists($name= null ,$page=null,$pageSize=null)
		{
			
			$res['extra'] = $this->select();
			if ($name != null) {
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->limit($page,$pageSize)->where("`name` like '%".$name."%'")->select();
				}else{
					$res['extra'] = $this->where("`name` like '%".$name."%'")->select();
				}
				$res['count'] = $this->where("`name` like '%".$name."%'")->count();
			}else{
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->limit($page,$pageSize)->select();
				}else{
					$res['extra'] = $this->select();
				}
				$res['count'] = $this->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = 0;
			}
			result(true,$res);
		}*/
	}