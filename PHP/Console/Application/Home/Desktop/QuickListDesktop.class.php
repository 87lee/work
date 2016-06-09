<?php
	namespace Home\Desktop;
	class QuickListDesktop extends \Think\Model
	{
		protected $tableName = 'quick_list';
		protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		protected $_validate = array(
			array('name','','底部快捷栏已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
			// array('pkgname','','跳转信息包名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function addQuickList($name)
		{
			if (!empty($name)) {

				//添加跳转信息
				$options = array(
					'name'=>$name,
				);
				if ($this->create($options)) {
					return $this->add();
				}else{
					result($this->getError());
				}

			}else{
				result('param');
			}


		}
		public function deleteQuickList($id)
		{
			$res = $this->find($id);
			if ($res) {
				$this->delete($id);
				return true;
			}else{
				result('底部快捷栏不存在');
			}
		}
		public function quickLists($id = null,$name= null ,$page=null,$pageSize=null)
		{
			if ($id ===null) {
				if ($name != null) {
					if (!empty($page)&&!empty($pageSize)) {
						$page = $page*$pageSize - $pageSize;
						$res['extra'] = $this->limit($page,$pageSize)->where("`name` like '%".$name."%' ")->select();
					}else{
						$res['extra'] = $this->where("`name` like '%".$name."%' ")->select();
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
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}
			}else{
				$res = $this->find($id);
				if (empty($res)) {
					$res = array();
				}
			}
			return $res;
		}
		public function modifyQuickList($id,$name)
		{
			$res = $this->find($id);
			if ($res) {
				$res = $this->where("`name`='%s' and id != %d",array($name,$id))->find();
				if ($res) {
					result('底部快捷栏已存在');
				}
				$options = array(
					'name'=>$name,
				);
				$this->create($options,2);
				$this->where("`id`=%d",array($id))->save();
				return $id;
			}else{
				result('底部快捷栏不存在');
			}

		}

	}