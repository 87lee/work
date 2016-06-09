<?php
	namespace Home\Desktop;
	class DesktopVersionDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_version';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'sourcePath' =>'source_path',
			'layoutPath'  =>'layout_path',
			'layoutVersion'  =>'layout_version',
		);
		public function addDesktopVersion($options,$error = false)
		{


			if (!$res = $this->getValOneForModelForVersionForLayoutVersion($options['model'],$options['version'],$options['layoutVersion'])) {
				$this->create($options);
				$this->add();
				return true;
			}else{
				if ($error) {
					return true;
				}else{
					result('该桌面没有改动');
				}
			}
		}
		public function modifyDesktopVersion($array,$options)
		{
			if (!$this->getValOneForModelForVersionForLayoutVersion($array['model'],$array['version'],$options['layoutVersion'])) {
				$this->create($options);
				$this->where("id = %d",array($array['id']))->save();
				return true;
			}else{
				if ($error) {
					return true;
				}else{
					result('该桌面没有改动');
				}
			}
		}
		public function getOneForModelVersion($model,$updateTime)
		{
			// return $this->where("`model`='%s' and `version`='%s' ",array($model,$updateTime))->order("`version`  DESC")->find();
			return $this->where("`model`='%s' and `version`='%s' ",array($model,$updateTime))->order("`time`  DESC")->find();

		}
		public function getOneForModelLayoutVersion($model,$layoutUpdateTime)
		{
			return $this->where("`model`='%s' and `layout_version`='%s' ",array($model,$layoutUpdateTime))->order("`time`  DESC")->find();
		}
		public function getValOneForModel($model)
		{
			return $this->where("`model`='%s'",array($model))->order("`time`  DESC")->find();
		}
		public function getValArrForModel($model)
		{
			return $this->field("`id`,model,version,IF(`layout_version`= '0', `version`,`layout_version`) as `layoutVersion`,concat('".C('DOWNLOAD_DESKTOP_SOURCER_PREFIX_ADDR')."',`source_path`) as sourcePath,concat('".C('DOWNLOAD_DESKTOP_LAYOUT_PREFIX_ADDR')."',`layout_path`) as layoutPath,FROM_UNIXTIME(time,'%Y-%m-%d %H:%i:%s') as time")->where("`model`='%s'",array($model))->select();;
		}
		public function getValOneForModelForVersionForLayoutVersion($model,$updateTime,$layoutUpdateTime)
		{
			return $this->where("`model`='%s' and `version`='%s'  and `layout_version`='%s'",array($model,$updateTime,$layoutUpdateTime))->find();
		}
		public function getValOneForId($id)
		{
			return $this->field("`id`,model,version,layout_version,source_path,layout_path,md5")->where("`id`=%d",array($id))->find();
		}
		/*
		public function deleteBlock($id)
		{
			$res = $this->find($id);
			if ($res) {
				$res = D('Fragment','Desktop')->where("`block_id`=%d",array($id))->find();
				if (!$res) {
					if ($this->delete($id)) {
						result();
					}else{
						result('unknown');
					}
				}else{
					result('块名正在使用');
				}
			}else{
				result('块不存在');
			}
		}

		public function blockLists($name= null ,$page=null,$pageSize=null)
		{

			$res['extra'] = $this->select();
			if ($name != null) {
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->limit($page,$pageSize)->where("`name` like '%".$name."%'")->select();
				}else{
					$res['extra'] = $this->where("`name` like '%".$name."%'")->select();
				}
				$res['count'] = $this->where("`name` like '%".$name."%'")->count();
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
			result(true,$res);
		}*/
	}