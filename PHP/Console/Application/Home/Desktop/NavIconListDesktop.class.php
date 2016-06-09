<?php
	namespace Home\Desktop;
	class NavIconListDesktop extends \Think\Model
	{
		protected $tableName = 'nav_icon_list';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'navId' =>'nav_id', // 把表单中name映射到数据表的username字段
			'forcusPath' =>'forcus_path', // 把表单中name映射到数据表的username字段
			'normalPath'  =>'normal_path', // 把表单中的mail映射到数据表的email字段
			'functionId'  =>'function_id', // 把表单中的mail映射到数据表的email字段
			'currentDrawable'  =>'current_drawable', // 把表单中的mail映射到数据表的email字段
		);

		public function addNavIconList($options)
		{

			foreach ($options as $key => $value) {
				$res[] = $this->create($value);
			}
			$res = $this->addAll($res);
			if ($res) {
				return true;
			}
		}

		public function deleteNavIconList($id)
		{
			$res = $this->where("`nav_id`=%d",array($id))->find();
			if ($res) {
				$res = $this->where("`nav_id`=%d",array($id))->delete();
				return true;
			}else{
				return true;
			}
		}

		public function navLists($id=null)
		{
			$field = "`id`,function_id,nav_id as navId,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath,case  when   current_drawable = '' then '' else  concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',current_drawable)  end as currentDrawable";
			if ($id != null) {
				$res['extra'] = D('Nav','Desktop')->getValOneForId($id);
				if (empty($res['extra'])) {
					$res['extra'] = array();
				}else{
					$extra = $this->field($field)->where("`nav_id`=%d",array($res['extra']['id']))->select();

					if ($extra) {

						$res['extra']['extra'] = $extra;
					}else{
						$res['extra']['extra'] = array();
					}

				}
			}else{
				$res['extra'] = D('Nav','Desktop')->getAllOrderId();
				if (empty($res['extra'])) {
					$res['extra'] = array();
				}else{
					foreach ($res['extra'] as $key => $value) {
						$extra = $this->field($field)->where("`nav_id`=%d",array($value['id']))->select();
						if ($extra) {
							$res['extra'][$key]['extra'] = $extra;
						}else{
							$res['extra'][$key]['extra'] = array();
						}
					}
				}
			}
			result(true,$res);
		}


		public function modifyNavIconList($id,$options = null)
		{

			$navList = $this->where("`nav_id`=%d",array($id))->select();

			if($options === null){
				if ($navList) {
					$this->where("`nav_id`=%d",array($id))->delete();
				}
				result();
			}else{
				if ($navList) {
					if (count($navList) == count($options)) {

						foreach ($navList as $key => $value) {
							$this->create($options[$key]);
							$this->where("`id`=%d",array($value['id']))->save();
							// echo $this->getLastSql();
						}
					}elseif (count($navList) > count($options)) {

						$modifyId = count($options);

						foreach ($navList as $key => $value) {
							if ($modifyId <= 0) {
								$this->where("`id`=%d",array($value['id']))->delete();
							}else{
								$this->create($options[$key]);
								$this->where("`id`=%d",array($value['id']))->save();
							}
							$modifyId--;
						}
					}elseif (count($navList) < count($options)) {

						$modifyId = 0;
						foreach ($navList as $key => $value) {
							$this->create($options[$key]);
							$this->where("`id`=%d",array($value['id']))->save();
							$modifyId++;
						}
						for ($i=0; $i < $modifyId; $i++) {
							unset($options[$i]);
						}
						$options = array_values($options);
						$this->addNavIconList($options);
					}
				}else{
					$this->addNavIconList($options);
				}
				result();
			}

		}
		public function desktopNavIconLists($navId)
		{
			$NavIconListsSql = $this->field('icon_id')->where("`nav_id`=%d",array($navId))->select(false);
			return D('Icon','Desktop')->desktopIconLists($NavIconListsSql);
		}
	}