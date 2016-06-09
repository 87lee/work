<?php
	namespace Home\Model;
	class ModuleCommentModel extends \Think\Model
	{
		protected $tableName = 'module_comment';
		// protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addModuleComment($put)
		{
			if (!empty($put['moduleId'])&&!empty($put['content'])) {
				if ( D('ModulePublish')->getValOneForIdToComment($put['moduleId'])) {
					$user = session('androidIsLogin');
					$options = array(
						'publish_id'=>$put['moduleId'],
						'content'=>$put['content'],
						'time'=>time(),
						'user'=>!empty($user['user'])?$user['user']:'游客',
					);
					return $this->add($options);


				}else{
					result('该模块没有发布');
				}
			}else{
				result('param');
			}
		}

		public function deleteModuleComment($get)
		{
			if (!empty($get['id'])) {

				if ($res = $this->where(" id = %d",array($get['id']))->find()) {
					/*$res = D('ModulePublish')->getValOneForIdToComment($res['publish_id']);
					$isAppPublish = D('ModuleAdmin')->isAppPublish($res['name']);*/
					if (isAdmin()) {
						return $this->where(" id = %d",array($get['id']))->delete();
					}else{
						result('auth');
					}
				}
			}
		}
		public function deleteCommentArrForId($sqlId)
		{
			if ($this->where("publish_id IN (".$sqlId.")")->find()) {
				$this->where("publish_id IN (".$sqlId.")")->delete();
			}
		}
		/*public function modifyAppAdmin($put)
		{

			if (isAdmin()) {
				if (!empty($put['id']) && !empty($put['name']) && !empty($put['operator']) &&($put['operation']=='test' || $put['operation']=='publish' )) {
					if ($this->find($put['id'])) {
						if ($this->where("name = '%s'  and  operator = '%s' and id !=%d and operation='%s'",array($user['name'],$put['operator'],$put['id'],$put['operation']))->find()) {
							$options = array(
								'name' => $put['name'],
								'operator' => $put['operator'],
								'operation'=>$put['operation'],
							);
							if (!empty($put['note'])) {
								$options['note'] = $put['note'];
							}
							$this->where("id = %d",array($user['id']))->save($options);

						}else{
							result('该用户已有该权限');
						}
					}else{
						result('数据不存在');
					}
				}else{
					result('param');
				}
			}else{
				result('auth');
			}
		}*/

		public function moduleCommentLists($get)
		{
			if (!empty($get['moduleId']) ) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field('publish_id',true)->limit($get['page'],$get['pageSize'])->where(" `publish_id` = %d",array($get['moduleId']))->order("time desc")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field('publish_id',true)->where(" `publish_id` = %d",array($get['moduleId']))->order("time desc")->select();
				}
				$res['count'] = $this->where(" `publish_id` = %d",array($get['moduleId']))->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				$UserArr = D('User')->getAllNameUser();
				foreach ($UserArr as  $value) {
					$nameArr[$value['user']] = $value['name'];
				}
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						if (!empty($nameArr[$value['user']])) {
							$res['extra'][$key]['user'] = $nameArr[$value['user']];
						}
					}
				}else{
					if (!empty($nameArr[$res['extra']['user']])) {
						$res['extra']['user'] = $nameArr[$res['extra']['user']];
					}
				}
			}
			return $res;
		}
	}