<?php
	namespace Home\Desktop;
	class DesktopGroupDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_group';
		protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'snList' =>'sn_list',
			"vendorID"=>"vendorid",
		);*/
		/*protected $_validate = array(
			array('name','','命令已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/
		public function addDesktopGroup($put)
		{
			//检查数据
			if (!empty($put['name'])) {

				if ($this->where("`name`='%s'",array($put['name']))->find()) {
					result('桌面组已存在');
				}

				$options = array(
					'name'=>$put['name'],
					'desc'=>isset($put['desc'])?$put['desc']:'',
				);
				//添加到数据库
				return $id = $this->add($options);

			}else{
				result('param');
			}
		}

		public function modifyDesktopGroup($put)
		{
			//检查数据
			if (!empty($put['name']) && !empty($put['id'])) {

				if (!$this->find($put['id'])) {
					result('桌面组不存在');
				}
				if ($this->where("`name`='%s' and `id` != %d",array($put['name'],$put['id']))->find()) {

					result('桌面组已存在');
				}
				$options = array(
					'name'=>$put['name'],
				);
				if (isset($put['desc'])) {
					$options['desc'] = $put['desc'];
				}
				//修改数据库
				return $id = $this->where("`id`=%d",array($put['id']))->save($options);


			}else{
				result('param');
			}
		}
		public function deleteDesktopGroup($id)
		{
			$res = $this->find($id);
			if ($res) {
				if (!D('Desktop','Desktop')->getValForGroupId($id)) {
					$this->where("`id`=%d",array($id))->delete();
				}else{
					result('请先删除该桌面组下的桌面');
				}

			}

		}

		public function desktopGroupLists($get)
		{
			if (empty($get['id'])) {
				$where = '';
				if (!empty($get['name'])) {
					$idSql = D('Desktop',"Desktop")->getGroupIdForName($get['name']);
					$where ="`id` IN  (".$idSql.") ";
				}

				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();

			}else{
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;

		}
		public function getValForId($id)
		{
			return $this->find($id);
		}

		public function getArrForArrId($arrID)
		{
			return $this->where('id IN ('.$arrID.')')->select();
		}
		public function getAll()
		{
			return $this->select();
		}

	}