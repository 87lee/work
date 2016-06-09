<?php 
	namespace Home\Desktop;
	class DesktopSourcePublishDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_source_publish';
		protected $connection = 'DB_DESKTOP'; 
		protected $_map = array(         
			'AB'=>'gray',          
			'groupId'=>'group_id',
			
		);
		public function addDesktopSourcePublish($options,$error = false)
		{
			$desktop = D('Desktop','Desktop')->where("`name`='%s'",array($options['model']))->find();
			if ($desktop) {
				$res = $this->where("`model`='%s' and `type`='%s'",array($options['model'],$options['type']))->find();
				if (!$res) {
					if ( $options['type']=='group' || $options['type']=='AB' || $options['type']=='ALL' ) {
						if ($options['type']=='group') {
							$res = D('Group')->find($options['groupId']);
							if (!$res) {
								if ($error) {
									return $result = array('reason'=>  '组不存在');
								}else{
									result('组不存在');
								}
							}
						}
						$this->create($options);
						$desktopSourceId = $this->add();
						if ($desktopSourceId) {
							return $desktopSourceId;
						}else{
							if ($error) {
								return $result = array('reason'=>  '添加失败');
							}else{
								result('unknown');
							}
							
						}
					}else{
						if ($error) {
							return $result = array('reason'=>  '类型出错');
						}else{
							result('类型出错');
						}
						
					}
					
				}else{
					if ($error) {
						return $result = array('reason'=>  '已发布');
					}else{
						result('已发布');
					}
				}	
			}else{
				if ($error) {
					return $result = array('reason'=>  '桌面不存在');
				}else{
					result('桌面不存在');
				}
				
			}
		}
		
		public function deleteDesktopSourcePublish($id)
		{
			$res = $this->find($id);
			if ($res) {
				if ($this->delete($id)) {
					return true;
				}else{
					result('unknown');
				}
				
			}else{
				result('发布桌面资源包不存在');
			}
		}
		/*
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