<?php
	namespace Home\Desktop;
	class PreloadedAppDesktop extends \Think\Model
	{
		protected $tableName = 'preloaded_app';
		// protected $tablePrefix = '' ;
		// protected $tablePrefix = isset( $dbVod = C('DB_VOD') ) ? $dbVod['DB_PREFIX']:DB_PREFIX;
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopID' =>'desktop_id',
			'appList'  =>'app_list',
		);
		protected $_validate = array(
			array('desktop_id','','该桌面预装应用已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);

		public function addPreloadedApp($put)
		{
			if (empty($put['desktopID'])) {
				result('请添加桌面ID');
			}
			$appList = array();
			if (!empty($put['appList'])&&is_array($put['appList'])){
				foreach ($put['appList'] as $key => $value) {
					if (empty($value['name']) || empty($value['packname']) || empty($value['url'])) {
						result('预装应用第'.($key+1)."参数有误");
					}
					$appList[] = array(
						"name"=>$value['name'],
						"packname"=>$value['packname'],
						"url"=>$value['url'],
					);
				}
			}
			$options = array(
				'desktop_id'=>$put['desktopID'],
				'app_list'=>!empty($appList)?json_encode($appList,JSON_UNESCAPED_UNICODE):'[]'
			);

			if ($this->create($options,1)) {
				return $this->add();
			}else{
				result($this->getError());
			}
		}
		public function modifyPreloadedApp($put)
		{
			if (empty($put['desktopID']) || empty($put['id'])) {
				result('param');
			}
			$appList = array();
			$put['id'] = intval($put['id']);

			if (!$this->find($put['id'])) {
				result('该桌面预装应用不存在');
			}

			if ($this->getOneForDesktopIdNoId($put['desktopID'],$put['id'])) {
				result('该桌面预装应用已存在');
			}

			if (!empty($put['appList'])&&is_array($put['appList'])){
				foreach ($put['appList'] as $key => $value) {
					if (empty($value['name']) || empty($value['packname']) || empty($value['url'])) {
						result('预装应用第'.($key+1)."参数有误");
					}
					$appList[] = array(
						"name"=>$value['name'],
						"packname"=>$value['packname'],
						"url"=>$value['url'],
					);
				}
			}

			$options = array(
				'desktop_id'=>$put['desktopID'],
				'app_list'=>!empty($appList)?json_encode($appList,JSON_UNESCAPED_UNICODE):'[]'
			);

			if ($this->create($options,2)) {
				return $this->where("`id`=%d",array($put['id']))->save();
			}else{
				result($this->getError());
			}
		}
		public function deletePreloadedApp($get)
		{
			if (!empty($get['id'])) {
				if (!$res = $this->find($get['id'])) {
					result('该桌面预装应用不存在');
				}
				$this->delete($get['id']);
			}
			return true;
		}
		public function preloadedAppLists($get)
		{
			$field = "*";
			$where = '';
			if (empty($get['id'])) {

				if (!empty($get['name'])) {
					$where = "`desktop_id` like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($pageSize)) {
					$get['page'] = $get['page']*$pageSize - $pageSize;
					$res['extra'] = $this->limit($get['page'],$pageSize)->field($field)->where($where)->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field($field)->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();

			}else{
				$res['extra'] = $this->field($field)->find($get['id']);
				if (!empty($res['extra'])) {
					$res['count'] = 1;
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['appList'] = json_decode($value['appList'],true);
					}
				}else{
					$res['extra']['appList'] = json_decode($res['extra']['appList'],true);
				}
			}
			return $res;
		}

		public function getOneForDesktopIdNoId($desktopID,$id)
		{
			return $this->where("desktop_id = '%s' and id != %d",array($desktopID,$id))->find();
		}

	}