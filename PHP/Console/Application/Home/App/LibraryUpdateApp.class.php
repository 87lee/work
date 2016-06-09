<?php
	namespace Home\App;
	class LibraryUpdateApp extends \Think\Model
	{
		protected $tableName = 'library_update';
		protected $connection = 'DB_APP';
		protected $_map = array(
			'libraryName' =>'library_name',
			'pkgName'  =>'pkgname',
		);
		public function addLibraryUpdate($put)
		{
			if(empty($put['libraryName']) || empty($put['pkgName'])  ) {
				result('param');
			}
			//检查数据库是否存在名称
			$res = $this->getOneForName($put['libraryName']);
				if ($res) {
				result('该库升级已存在');
			}
			$options = array(
				"library_name"=>$put['libraryName'],
				"pkgname"=>$put['pkgName'],
			);
			if (!empty($put['desc'])) {
				$options['desc'] = $put['desc'];
			}else{
				$options['desc'] = '';
			}
			$this->create($options);
			return $id = $this->add();
			// return $options;
		}
		public function getOneForName($name)
		{
			return $this->where("`library_name` ='%s'",array($name))->find();
		}
		public function getOneForNameNoId($name,$id)
		{
			return $this->where("`library_name` ='%s' and id !=%d",array($name,$id))->find();
		}
		public function modifyLibraryUpdate($put)
		{

			if(empty($put['id']) || empty($put['libraryName']) || empty($put['pkgName'])   ) {

				result('param');
			}
			//检查数据库是否存在版本
			$res = $this->find($put['id']);
 			if (!$res) {
				result('该应用不存在');
			}
			//检查数据库是否存在版本
			$res = $this->getOneForNameNoId($put['libraryName'],$put['id']);
 			if ($res) {
				result('该应用已存在');
			}
			$options = array(
				"library_name"=>$put['libraryName'],
				"pkgname"=>$put['pkgName'],
			);
			if (!empty($put['desc'])) {
				$options['desc'] = $put['desc'];
			}else{
				$options['desc'] = '';
			}
			$this->create($options);
			return $id = $this->where("`id`=%d",array($put['id']))->save();
		}
		public function deleteLibraryUpdate($get)
		{
			$res = $this->find($get['id']);
			if ($res) {
				$res = D('LibraryUpdateVersions','App')->where("`library_id`=%d",array($get['id']))->find();
				if (!$res) {
					$this->delete($get['id']);
					return true;
				}else{
					result('请先删除该库的版本');
				}
			}
		}
		public function libraryUpdateLists($get)
		{
			if ( empty($get['id'])) {
				$where = '';
				if (!empty($get['name'])) {

					$where = "`library_name` like '%".$get['name']."%' or `pkgname` like  '%".$get['name']."%' or `desc` like  '%".$get['name']."%'";
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
				$res = D('LibraryUpdateVersions','App')->getArrForLibraryId($get['id'],$get);
				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['desc'] = json_decode($value['desc'],true);
						$res['extra'][$key]['path'] = C('DOWNLOAD_APK_PREFIX_ADDR') . $value['path'];
					}
				}

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}


	}