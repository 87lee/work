<?php
	namespace Home\Silent;
	class SilentBlackListSilent extends \Think\Model
	{
		protected $tableName = 'silent_black_list';
		protected $connection = 'DB_SILENT';
		protected $_map = array(

			'vendorID'  =>'vendor_id',
		);

		/*protected $_validate = array(
			array('name','','静默安装组已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addBlackList($put)
		{
			if (!empty($put['model']) && !empty($put['vendorID']) ) {
				if ( !$this->where("model = '%s' and vendor_id = '%s'",array($put['model'],$put['vendorID']))->find()) {
					$options = array(
						'model'=>$put['model'],
						'vendor_id'=>$put['vendorID'],
						'desc'=>isset($put['desc'])?$put['desc']:''
					);
					return $this->add($options);
				}else{
					result('黑名单已存在');
				}
			}else{
				result('param');
			}

		}
		public function modifyBlackList($put)
		{
			if (!empty($put['model']) && !empty($put['vendorID'])&& !empty($put['id']) ) {
				if ($this->find($put['id'])) {
					if (!$this->where("model = '%s' and vendor_id = '%s' and id != %d ",array($put['model'],$put['vendorID'],$put['id']))->find()) {
						$options = array(
							'model'=>$put['model'],
							'vendor_id'=>$put['vendorID'],
							'desc'=>isset($put['desc'])?$put['desc']:''
						);
						return $this->where("id = %d",array($put['id']))->save($options);
					}else{
						result('黑名单已存在');
					}
				}

			}else{
				result('param');
			}
		}
		public function deleteBlackList($id)
		{
			if ($id !=null) {
				$res = $this->find($id);
				if ($res) {
					$this->delete($id);
					return true;
				}else{
					result('黑名单不存在');
				}
			}
		}

		public function blackListLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}


			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `model` like '%".$get['name']."%' or `vendor_id` like '%".$get['name']."%'")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where(" `model` like '%".$get['name']."%' or `vendor_id` like '%".$get['name']."%'")->select();
				}
				$res['count'] = $this->where(" `model` like '%".$get['name']."%' or `vendor_id` like '%".$get['name']."%'")->count();
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



	}