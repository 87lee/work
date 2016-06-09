<?php
	namespace Home\Desktop;
	class BlockDesktop extends \Think\Model
	{
		protected $tableName = 'block';
		protected $connection = 'DB_DESKTOP';

		public function addBlock($name,$w,$h,$yw,$yh)
		{
			$res = $this->where("`name`='%s'",array($name))->find();
			if (!$res) {
				$options = array(
					'name'=>$name,
					'w'=>$w,
					'h'=>$h,
					'yw'=>$yw,
					'yh'=>$yh
				);
				if ($this->add($options)) {
					result();
				}else{
					result('unknown');
				}
			}else{
				result('块名已存在');
			}
		}
		public function deleteBlock($id)
		{
			$res = $this->find($id);
			if ($res) {

				if ($this->delete($id)) {
					result();
				}else{
					result('unknown');
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
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			result(true,$res);
		}
	}