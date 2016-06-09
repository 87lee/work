<?php
	namespace Home\Monitoring;
	class WarningGroupMonitoring extends \Think\Model
	{
		protected $tableName = 'warning_group';
		protected $connection = 'DB_MONITORING';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		protected $_validate = array(
            			array('name','','告警组已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一

        		);

		public function addWarningGroup($put)
		{
			if (empty($put['name'])) {
				result('param');
			}

			$options = array(
				'name'=>$put['name'],
			);

			if ($this->create($options)) {
				$this->add($options);
			}else{
				result($this->getError());
			}
			return ;
		}
		public function modifyWarningGroup($put)
		{
			if (empty($put['name']) || empty($put['id'])) {
				result('param');
			}
			if (!$res = $this->getOneForId($put['id'])) {
				result('该告警组不存在');
			}
			if ($this->getOneForNameNoId($put['name'],$put['id'])) {
				result('修改告警组已存在');
			}
			$options = array(
				'name'=>$put['name']
			);
			return $this->where("id = %d",array($put['id']))->save($options);
		}
		public function deleteWarningGroup($get)
		{
			/*if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode( ',',$arr);
					if ( $res = $this->getOneInId($sqlId) ) {
						$this->where("id IN (%s)",array($sqlId))->delete();
					}
				}
			}else{
				result('param');
			}*/
			if (!empty($get['id'])) {
				if ($this->find($get['id'])) {

					if (D('InterfaceWarningRulesObject','Monitoring')->getOneForGroupId($get['id'])) {
						result("接口告警规则正在使用此告警组");
					}
					if (D('DownloadWarningRulesObject','Monitoring')->getOneForGroupId($get['id'])) {
						result("下载告警规则正在使用此告警组");
					}
					$this->delete($get['id']);
				}
			}
			return ;
		}
		public function warningGroupLists($get)
		{
			if (empty($get['id'])) {
				$where = '';
				if (!empty($get['name'])) {
					$where = "`name` like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}else{
				$res['extra'] = $this->find($get['id']);

				if ($res['extra']) {
					$res['count'] =1;
				}

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		public function getOneForNameNoId($name,$id)
		{
			return $this->where("name = '%s' and id != %d",array($name,$id))->find();
		}
		public function getOneInId($id)
		{
			return $this->where("id IN (%s)",array($id))->find();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s'",array($name))->find();
		}
		public function getCountForIdSql($idSql)
		{
			return $this->where("id in (".$idSql.")")->count();
		}
	}