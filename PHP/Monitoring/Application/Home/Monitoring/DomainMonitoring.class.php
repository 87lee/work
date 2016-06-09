<?php
	namespace Home\Monitoring;
	class DomainMonitoring extends \Think\Model
	{
		protected $tableName = 'domain';
		protected $connection = 'DB_MONITORING';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/


		public function addDomain($put)
		{
			if (empty($put['name'])||empty($put['url'])) {
				result('param');
			}
			if ($this->getOneForName($put['name'])) {
				result('该域名已存在');
			}
			$options = array(
				'name'=>$put['name'],
				'url'=>$put['url'],
				'desc'=>!empty($put['desc'])?$put['desc']:''
			);
			return $this->add($options);
		}
		public function modifyDomain($put)
		{
			if (empty($put['name']) ||empty($put['url']) || empty($put['id'])) {
				result('param');
			}
			if (!$res = $this->getOneForId($put['id'])) {
				result('该域名不存在');
			}
			if ($this->getOneForNameNoId($put['name'],$put['id'])) {
				result('修改域名名称已存在');
			}
			$options = array(
				'url'=>$put['url'],
				'name'=>$put['name']
			);
			if (isset($put['desc'])) {
				$options['desc'] = $put['desc'];
			}
			return $this->where("id = %d",array($put['id']))->save($options);
		}
		public function deleteDomain($put)
		{
			if (!empty($put) && is_array($put)) {
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
			}
			return ;

		}
		public function domainLists($get)
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
	}