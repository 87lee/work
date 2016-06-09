<?php 
	namespace Home\Desktop;
	class DesktopLayoutDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_layout';
		protected $connection = 'DB_DESKTOP'; 

		public function addDesktopLayout($options,$error=false)
		{
			$res = $this->where("`model`='%s' and `version`='%s'",array($options['model'],$options['version']))->find();
			if (!$res) {
				$this->add($options);
				return true;
			}else{
				if ($error) {
					return true;
				}else{
					result('该桌面没有改动');
				}
				
			}
		}
		/*
		public function deleteBlock($id)
		{
			$res = $this->find($id);
			if ($res) {
				$res = D('Fragment','Desktop')->where("`block_id`=%d",array($id))->find();
				if (!$res) {
					if ($this->delete($id)) {
						result();
					}else{
						result('unknown');
					}
				}else{
					result('块名正在使用');
				}
			}else{
				result('块不存在');
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