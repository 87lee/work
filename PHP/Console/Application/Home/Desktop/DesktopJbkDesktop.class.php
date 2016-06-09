<?php
	namespace Home\Desktop;
	class DesktopJbkDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_jbk';
		protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected $_validate = array(
			array('vendorid','','客户名已存在',0,'unique',3), // 在新增的时候验证name字段是否唯一
		);*/
		public function addDesktopJbk($put)
		{

			if (empty($put['province'])|| empty($put['operator'])|| !($put['source'] == 'yunos' || $put['source'] == 'android')  || !($put['action'] == '1' || $put['action'] == '0')) {
				result('param');
			}

			if ($this->getOneForProvinceOperatorSource($put['province'],$put['operator'],$put['source'])) {
				result('该项已存在');
			}
			if (!empty($put['id'])) {
				unset($put['id']);
			}
			if ($this->create($put)) {
				$this->add();
			}else{
				result($this->getError());
			}

		}
		public function getOneForProvinceOperatorSource($province,$operator,$source)
		{
			return $this->where("province = '%s' and operator = '%s' and source = '%s'",array($province,$operator,$source))->find();
		}
		public function getOneForProvinceOperatorSourceNotId($province,$operator,$source,$id)
		{
			return $this->where("province = '%s' and operator = '%s' and source = '%s' and id != %d",array($province,$operator,$source,$id))->find();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function modifyDesktopJbk($put)
		{
			if (empty($put['id']) || empty($put['province']) || empty($put['operator'])|| !($put['source'] == 'yunos' || $put['source'] == 'android')  || !($put['action'] == '1' || $put['action'] == '0')) {
				result('param');
			}

			if (!$this->getOneForId($put['id'])) {
				result('该项不存在');
			}

			if ($this->getOneForProvinceOperatorSourceNotId($put['province'],$put['operator'],$put['source'],$put['id'])) {
				result('该项已存在');
			}

			if ($res = $this->create($put)) {

				$this->where("id = %d",array($put['id']))->save();
			}else{
				result($this->getError());
			}

		}
		public function deleteDesktopJbk($get)
		{	$res = $this->find($get['id']);
			if (!$res) {
				result('该项不存在');
			}
			$this->where("`id`=%d",array($get['id']))->delete();
		}

		public function jbkDesktopLists($get)
		{
			if (empty($get['id'])) {
				$where = '';
				if (!empty($get['name'])) {
					$where = "`province` like '%".$get['name']."%' or `operator` like  '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}else{
				$res['extra'] = $this->find($id);

				if (!empty($res['extra'])) {
					$res['count'] = 1;
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}


	}