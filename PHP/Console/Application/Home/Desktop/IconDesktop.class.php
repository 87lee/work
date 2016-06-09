<?php
	namespace Home\Desktop;
	class IconDesktop extends \Think\Model
	{
		protected $tableName = 'icon';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'forcusPath' =>'forcus_path', // 把表单中name映射到数据表的username字段
			'normalPath'  =>'normal_path', // 把表单中的mail映射到数据表的email字段

		);
		public function addIcon($options)
		{
			if (!empty($options['name'])&&!empty($options['forcusPath'])&&!empty($options['normalPath'])) {
				$res = $this->where("`name`='%s'",array($options['name']))->find();
				if ($res) {
					result('Icon已存在');
				}else{
					$this->create($options);
					if ($this->add()) {
						result();
					}else{
						result('unknown');
					}
				}

			}else{
				result('param');
			}
		}
		public function modifyIcon($id,$options)
		{
			if (!empty($options['name'])) {
				$res = $this->find($id);
				if (!$res) {
					result('Icon不存在');
				}
				$res = $this->where("`name`='%s' and `id` != %d ",array($options['name'],$id))->find();
				// echo $this->getLastSql();
				if ($res) {
					result('Icon已存在');
				}else{
					$this->create($options);
					$this->where("`id`=%d",array($id))->save();
					result();
				}

			}else{
				result('param');
			}
		}
		public function deleteIcon($id)
		{
			$res = $this->find($id);
			if ($res) {

				if ($this->delete($id)) {
					result();
				}else{
					result('unknown');
				}
			}else{
				result('Icon不存在');
			}
		}

		public function iconLists($name= null ,$page=null,$pageSize=null)
		{
			// $res['extra'] = $this->field("`id`,name,concat('".DOWNLOAD_PREFIX_ADDR."',normal_path) as normalPath,concat('".DOWNLOAD_PREFIX_ADDR."',forcus_path) as forcusPath")->select();

			if ($name != null) {
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->field("`id`,name,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath")->limit($page,$pageSize)->where("`name` like '%".$name."%'")->select();
				}else{
					$res['extra'] = $this->field("`id`,name,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath")->where("`name` like '%".$name."%'")->select();
				}
				$res['count'] = $this->where("`name` like '%".$name."%'")->count();
			}else{
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->field("`id`,name,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath")->limit($page,$pageSize)->select();
				}else{
					$res['extra'] = $this->field("`id`,name,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath")->select();
				}
				$res['count'] = $this->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			result(true,$res);
		}
		public function desktopIconLists($NavIconListsSql)
		{
			return $NavIconLogoLists= $this->field("concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath")->where("`id` IN (".$NavIconListsSql.")")->select();
		}
	}