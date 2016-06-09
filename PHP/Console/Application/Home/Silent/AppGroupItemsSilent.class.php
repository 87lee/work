<?php
	namespace Home\Silent;
	class AppGroupItemsSilent extends \Think\Model
	{
		protected $tableName = 'app_group_items';
		protected $connection = 'DB_SILENT';
		protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
			'groupId' =>'group_id',
			'versionName'  =>'version_name',
			'versionCode'  =>'version_code',
		);
		/*protected $_validate = array(
			array('name','','静默安装组已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addAppGroupItem($put)
		{
			if (!empty($put['groupId'])&&!empty($put['appName'])&&!empty($put['pkgName'])&&($put['action']=='active' || $put['action']=='remove' || $put['action']=='install' )) {
				if (D('AppGroup','Silent')->getValOneForId($put['groupId'])) {
					if ($put['action'] == 'active') {

						if (empty($put['weight'])) {
							$put['weight'] = '0';
						}
					}
					if ($put['action'] != 'remove') {
						if (empty($put['versionName'])||empty($put['versionCode'])||empty($put['download'])) {
							result('param');
						}
					}
					$options = array(
						'group_id'=>$put['groupId'],
						'pkgname'=>$put['pkgName'],
						'weight'=>isset($put['weight'])?$put['weight']:'',
						'version_name'=>isset($put['versionName'])?$put['versionName']:'',
						'version_code'=>isset($put['versionCode'])?$put['versionCode']:'',
						'download'=>isset($put['download'])?$put['download']:'',
						'app_name'=>$put['appName'],
						'action'=>$put['action'],
					);
					return $this->add($options);
				}else{
					result('该静默安装组不存在');
				}
			}else{
				result('param');
			}

		}
		public function addAppGroupItemArr($oldGroupId,$newGroupId)
		{

			if ($res = $this->getPublisArrForGroupId($oldGroupId)) {
				foreach ($res as $key => $value) {
					$options[] = array(
						'group_id'=>$newGroupId,
						'pkgname'=>$value['pkgName'],
						'weight'=>isset($value['weight'])?$value['weight']:'',
						'version_name'=>$value['versionName'],
						'version_code'=>$value['versionCode'],
						'download'=>$value['download'],
						'app_name'=>$value['appName'],
						'action'=>$value['action'],
					);
				}
				return $this->addAll($options);
			}

		}
		public function modifyAppGroupItem($put)
		{

			if (!empty($put['id'])&&!empty($put['pkgName'])&&!empty($put['appName'])&&($put['action']=='active' || $put['action']=='remove' || $put['action']=='install' )) {
				if ($this->find($put['id'])) {
					if ($put['action'] == 'active') {

						if (empty($put['weight'])) {
							$put['weight'] = '0';
						}
					}
					if ($put['action'] != 'remove') {
						if (empty($put['versionName'])||empty($put['versionCode'])||empty($put['download'])) {
							result('param');
						}
					}
					$options = array(
						'pkgname'=>$put['pkgName'],
						'weight'=>isset($put['weight'])?$put['weight']:'',
						'version_name'=>isset($put['versionName'])?$put['versionName']:'',
						'version_code'=>isset($put['versionCode'])?$put['versionCode']:'',
						'download'=>isset($put['download'])?$put['download']:'',
						'app_name'=>$put['appName'],
						'action'=>$put['action'],
					);
					return $this->where("id = %d",array($put['id']))->save($options);
				}

			}else{
				result('param');
			}
		}
		public function deleteForGroupId($groupId)
		{
			if ($this->where('group_id = %d',array($groupId))->find()) {
				$this->where('group_id = %d',array($groupId))->delete();
			}
		}
		public function deleteAppItemGroup($id = null)
		{
			if ($id !=null) {
				$res = $this->find($id);
				if ($res) {
					$this->delete($id);
					return true;
				}else{
					result('静默安装组不存在');
				}
			}
		}
		public function appGroupItemLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->field('group_id',true)->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}


			}elseif (!empty($get['groupId'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field('group_id',true)->limit($get['page'],$get['pageSize'])->where(" `group_id` = '%s'",array($get['groupId']))->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field('group_id',true)->where(" `group_id` = '%s'",array($get['groupId']))->select();
				}
				$res['count'] = $this->where(" `group_id` = '%s'",array($get['groupId']))->count();
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field('group_id',true)->limit($get['page'],$get['pageSize'])->where(" `app_name` like '%".$get['name']."%'")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field('group_id',true)->where(" `app_name` like '%".$get['name']."%'  ")->select();
				}
				$res['count'] = $this->where(" `app_name` like '%".$get['name']."%'")->count();
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field('group_id',true)->limit($get['page'],$get['pageSize'])->select();
				}else{
					$res['extra'] = $this->field('group_id',true)->select();
				}
				$res['count'] = $this->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';

			}else{
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						if ($value['action'] !='active') {
							unset($res['extra'][$key]['weight']);
						}
						if ($value['action'] =='remove') {
							unset($res['extra'][$key]['versionName']);
							unset($res['extra'][$key]['versionCode']);
							unset($res['extra'][$key]['download']);
						}
					}
				}else{
					if ($res['extra']['action'] !='active') {
						unset($res['extra']['weight']);
					}
					if ($res['extra']['action'] =='remove') {
						unset($res['extra']['versionName']);
						unset($res['extra']['versionCode']);
						unset($res['extra']['download']);
					}
				}
			}

			return $res;
		}
		public function getPublisArrForGroupId($groupId)
		{
			$res = $this->field("group_id,id",true)->where("group_id = '%s'",array($groupId))->select();
			if (!empty($res)) {
				foreach ($res as $key => $value) {
					if ($value['action'] !='active') {
						unset($res[$key]['weight']);
					}
					if ($value['action'] =='remove') {
						unset($res[$key]['versionName']);
						unset($res[$key]['versionCode']);
						unset($res[$key]['download']);
					}
				}
			}
			return $res;
		}


		public function getPublisArrForGroupIdAction($groupId,$action)
		{
			$res = $this->field("group_id,id",true)->where("group_id = '%s' and action = '%s'",array($groupId,$action))->find();

			return $res;
		}


	}