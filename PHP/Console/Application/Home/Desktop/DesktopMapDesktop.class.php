<?php
	namespace Home\Desktop;
	class DesktopMapDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_map';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'vendorID' =>'vendorid',
			'createTime' =>'create_time',
		);
		// protected $_validate = array(
			// array('desktop2','','桌面ID已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
			// array('desktop2','','桌面ID已存在',0,'unique',2), // 在新增的时候验证name字段是否唯一
		// );
		public function addDesktopMap($put)
		{
			if (empty($put['desktop2'])) {
				$put['desktop2'] = '';
			}
			if (empty($put['desktop3'])) {
				result('映射桌面不能为空');
			}

			if (empty($put['vendorID'])) {
				$put['vendorID'] = '';
			}
			if (empty($put['model'])) {
				$put['model'] = '';
			}
			$options = array(
				'desktop2'=>$put['desktop2'],
				'desktop3'=>$put['desktop3'],
				'vendorID'=>$put['vendorID'],
				'model'=>$put['model'],
				'create_time'=>time(),
			);

			if (!$this->getOneForDesktop2VendorIDModel($put['desktop2'],$put['vendorID'],$put['model'])) {
				$this->create($options);
				$this->add();
				return true;
			}else{
				result('该桌面映射已存在');
			}

		}
		public function getOneForDesktop2VendorIDModel($desktop2,$vendorID,$model)
		{
			return $this->where("`desktop2` = '%s' and `vendorid`='%s' and `model` = '%s'",array($desktop2,$vendorID,$model))->find();
		}
		public function getOneForDesktop2VendorIDModelNotId($desktop2,$vendorID,$model,$id)
		{
			return $this->where("`desktop2` = '%s' and `vendorid`='%s' and `model` = '%s' and id != %d",array($desktop2,$vendorID,$model,$id))->find();
		}
		public function modifyDesktopMap($put)
		{
			if (empty($put['id'])) {
				result('param');
			}
			if (empty($put['desktop3'])) {
				result('映射桌面不能为空');
			}

			$res = $this->find($put['id']);
			if (empty($res)) {
				result('桌面映射不存在');
			}

			if (!isset($put['desktop2'])) {
				$put['desktop2'] = $res['desktop2'];
			}

			if (!isset($put['vendorID'])) {
				$put['vendorID'] = $res['vendorID'];
			}
			if (!isset($put['model'])) {
				$put['model'] = $res['model'];
			}
			$options = array(
				'desktop2'=>$put['desktop2'],
				'desktop3'=>$put['desktop3'],
				'vendorID'=>$put['vendorID'],
				'model'=>$put['model'],
				'create_time'=>time(),
			);

			if (!$this->getOneForDesktop2VendorIDModelNotId($put['desktop2'],$put['vendorID'],$put['model'],$put['id'])) {
				$this->create($options);
				$this->where("id = %d",array($put['id']))->save();
				return true;
			}else{
				result('该桌面映射已存在');
			}
		}

		public function deleteDesktopMap($id =null,$idLists=array())
		{
			if ($id === null) {

				if (!empty($idLists)&&is_array($idLists)) {
					foreach ($idLists as $key => $value) {
						$res = $this->find($value);
						if (!$res) {
							result('桌面ID'.$value.'不存在');
						}
						$this->where("`id`=%d",array($value))->delete();

					}
				}

			}else{
				$res = $this->find($id);
				if (!$res) {
					result('桌面ID'.$id.'不存在');
				}
				$this->where("`id`=%d",array($id))->delete();

			}


		}

		public function desktopMapLists($id = null,$name= null ,$page=null,$pageSize=null)
		{
			$field = "id,desktop2,`model`,desktop3,vendorid,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as createTime";
			$order = "create_time desc";
			$where = '';
			if ($id ===null) {
				if ($name != null) {
					$where = "`desktop2` like '%".$name."%' or `model` like  '%".$name."%'  or `desktop3` like  '%".$name."%' or `vendorid` like  '%".$name."%'";
				}
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->field($field)->where($where)->limit($page,$pageSize)->order($order)->select();
				}else{
					$res['extra'] = $this->field($field)->where($where)->order($order)->select();
				}
				$res['count'] = $this->count();

				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}
			}else{

				$res['extra'] = $this->field($field)->find($id);
				$res['count'] = 1;
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = 0;
				}

			}
			result(true,$res);
		}
		public function getOneForDesktop2($Desktop2)
		{
			return $this->where("desktop2 = '%s' and vendorid = '' and model = '' and desktop3 != '%s'",array($Desktop2,$Desktop2))->find();
		}
	}