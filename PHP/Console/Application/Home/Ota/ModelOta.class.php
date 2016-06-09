<?php 
	namespace Home\Ota;
	class ModelOta extends \Think\Model
	{
		protected $tableName = 'model';
		protected $connection = 'DB_OTA'; 
		protected $_map = array(         
			'vendorID' =>'vendor_id',         
		);
		/*protected $_validate = array(     
			array('name','','该配置组已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一     
		);
*/
		public function addModel($put)
		{
			if (!empty($put['model'])&&isset($put['vendorID'])) {
				
				if ($this->where("`model`='%s' and `vendor_id`='%s'",array($put['model'],$put['vendorID']))->find()) {
					result('厂商已存在');
				}else{
					if (empty($put['desc'])) {
						$put['desc'] = '';
					}
					$this->create($put);
					return $this->add();
				}
			}else{
				result('参数有误');
			}
		}
		public function modifyModel($put)
		{
			if (isset($put['id'])&&!empty($put['model'])&&isset($put['vendorID'])) {
				
				if ($this->where("`model`='%s' and `vendor_id`='%s' and `id` !=%d",array($put['model'],$put['vendorID'],$put['id']))->find()) {
					result('厂商已存在');
				}else{
					
					if ($this->find($put['id'])) {
						if (empty($put['desc'])) {
							$put['desc'] = '';
						}
						$this->create($put);
						return $this->where("`id` = %d",array($put['id']))->save();
					}else{
						result('厂商不存在');
					}
				}
			}else{
				result('param');
			}
		}
		public function modelLists($id = null,$name= null ,$page=null,$pageSize=null)
		{
			if ($id ===null) {
				if ($name != null) {
					if (!empty($page)&&!empty($pageSize)) {
						$page = $page*$pageSize - $pageSize;
						$res['extra'] = $this->limit($page,$pageSize)->where("`model` like '%".$name."%' or `vendor_id` like '%".$name."%' or `desc` like '%".$name."%' ")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->where("`model` like '%".$name."%' or `vendor_id` like '%".$name."%' or `desc` like '%".$name."%' ")->select();
					}
					$res['count'] = $this->where("`model` like '%".$name."%' or `vendor_id` like '%".$name."%' or `desc` like '%".$name."%' ")->count();
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
			}else{
				$res['extra'] = $this->find($id);
				$res['count'] = 1;
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = 0;
				}
			}
			
			result(true,$res);
		}
		public function deleteModel($id)
		{
			if ($this->find($id)) {
				
				if (D('ModelVersion','Ota')->where("`model_id` = %d",array($id))->find()) {
					result('请先删除版本');
				}else{
					return $this->where("`id`=%d",array($id))->delete($id);
				}
			}else{
				result('厂商不存在');
			}
			
		}

	}