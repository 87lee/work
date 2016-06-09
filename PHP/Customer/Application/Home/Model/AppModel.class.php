<?php
	namespace Home\Model;
	class AppModel extends \Think\Model
	{
		protected $tableName = 'app';
		// protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addApp($put)
		{
			if (!empty($put['name'])&&!empty($put['app'])) {

				if (!$res = $this->where(' name = "%s" ' ,array($put['name']))->find()) {
					if (isAdmin()) {
						$options = array(
							'name'=>$put['name'],
							'app'=>$put['app'],
							'note'=>!empty($put['note'])?$put['note']:'',
						);
						return $this->add($options);
					}else{
						result('auth');
					}
				}else{
					result('该应用已存在');
				}


			}else{
				result('param');
			}
		}

		public function deleteApp($put)
		{
			if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode($arr, ',');
					if (isAdmin()) {
						if ($res = $this->where("id IN (".$sqlId.")")->select()) {

							return $this->where("id IN (".$sqlId.")")->delete();
						}
					}else{
						result('auth');
					}
				}


			}else{
				result('param');
			}
		}

		public function modifyApp($put)
		{
			if (!empty($put['name'])&&!empty($put['id'])&&!empty($put['app'])) {
				if ($this->find($put['id'])) {
					if (!$res = $this->where(' name = "%s" and id !=%d' ,array($put['name'],$put['id']))->find()) {

						if (isAdmin()) {
							$options = array(
								'name'=>$put['name'],
								'app'=>$put['app'],
								'note'=>!empty($put['note'])?$put['note']:'',
							);
							return $this->where("id = %d",array($put['id']))->save($options);
						}else{
							result('auth');
						}
					}else{
						result('该应用已存在');
					}
				}else{
					result('该应用不存在');
				}

			}else{
				result('param');
			}
		}

		public function appLists($get)
		{
			if ( $user = isAdmin() ) {

				if (!empty($get['id'])) {
					$res['extra'] = $this->find($get['id']);
					if ($res['extra']) {
						$res['count'] = 1;
					}
				}elseif (!empty($get['name'])) {

					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `name` like '%".$get['name']."%' or `app` like '%".$get['name']."%'")->select();
					}else{
						$res['extra'] = $this->where(" `name` like '%".$get['name']."%'  or `app` like '%".$get['name']."%'")->select();
					}
					$res['count'] = $this->where(" `name` like '%".$get['name']."%' or `app` like '%".$get['name']."%'")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit( $get['page'],$get['pageSize'] )->select();
					}else{
						$res['extra'] = $this->select();
					}
					$res['count'] = $this->count();
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s'",array($name))->find();
		}
	}