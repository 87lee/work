<?php
	namespace Home\Desktop;
	class ShortCutsDesktop extends \Think\Model
	{
		protected $tableName = 'short_cuts';
		protected $connection = 'DB_DESKTOP';

		protected $_validate = array(
			array('name','','基础快捷键已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function addShortCuts($put)
		{
			if (!empty($put['name'])) {

				//添加跳转信息
				$options = array(
					'name'=>$put['name'],
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
		public function deleteShortCuts($get)
		{
			if (empty($get['id'])) {
				result('param');
			}
			$res = $this->find($get['id']);
			if ($res) {
				$this->delete($get['id']);
				return true;
			}else{
				result('底部快捷栏不存在');
			}
		}
		public function shortCutsLists($id = null,$name= null ,$page=null,$pageSize=null)
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
		public function modifyShortCuts($put)
		{
			if (!empty($put['id'])&&!empty($put['name'])) {
				$res = $this->find($put['id']);
				if ($res) {
					$res = $this->where("`name`='%s' and id != %d",array($put['name'],$put['id']))->find();
					if ($res) {
						result('底部快捷栏已存在');
					}
					$options = array(
						'name'=>$put['name'],
					);
					$this->create($options,2);
					$this->where("`id`=%d",array($put['id']))->save();
					return $put['id'];
				}else{
					result('底部快捷栏不存在');
				}
			}else{
				result('param');
			}


		}
		public function getValOneForId($id)
		{
			return $this->where("`id`=%d",array($id))->find();
		}
		public function getValALL()
		{
			return $this->select();
		}

	}