<?php
	namespace Home\Silent;
	class AppGroupSilent extends \Think\Model
	{
		protected $tableName = 'app_group';
		protected $connection = 'DB_SILENT';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		protected $_validate = array(
			array('name','','静默安装组已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);

		public function addAppGroup($put)
		{
			if (!empty($put['name'])) {
				if ($this->create($put,1)) {
					return $this->add();
				}else{
					result($this->getError());
				}
			}else{
				result('param');
			}

		}
		public function modifyAppGroup($put)
		{

			if (!empty($put['name']) && !empty($put['id'])) {
				if ($this->find($put['id'])) {
					if (!$this->where("name = '%s' and id != %d",array($put['name'],$put['id']))->find()) {
						$options = array('name'=>$put['name']);
						return $this->where("id = %d",array($put['id']))->save($options);
					}else{
						result('静默安装组已存在');
					}
				}

			}else{
				result('param');
			}
		}
		public function deleteAppGroup($id)
		{
			if ($id !=null) {
				$res = $this->find($id);
				if ($res) {
					D('AppGroupItems','Silent')->deleteForGroupId($id);
					$this->delete($id);
					return true;
				}else{
					result('静默安装组不存在');
				}
			}
		}
		public function appGroupLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}


			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `name` like '%".$get['name']."%'")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where(" `name` like '%".$get['name']."%'  ")->select();
				}
				$res['count'] = $this->where(" `name` like '%".$get['name']."%'")->count();
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->select();
				}else{
					$res['extra'] = $this->select();
				}
				$res['count'] = $this->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}

			return $res;
		}


		public function getValOneForId($id)
		{
			return $this->find($id);
		}

	}