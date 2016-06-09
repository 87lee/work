<?php
	namespace Home\Model;
	class AppCommentModel extends \Think\Model
	{
		protected $tableName = 'app_comment';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'publishId' =>'publish_id',
			// 'pkgName'  =>'pkgname',
		);
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addAppComment($put)
		{
			if (!empty($put['appId'])&&!empty($put['content'])) {
				if ($res = D('AppPublish')->getValOneForIdToComment($put['appId'])) {
					$user = session('androidIsLogin');
					$options = array(
						'publish_id'=>$put['appId'],
						'content'=>$put['content'],
						'time'=>time(),
						'user'=>!empty($user['user'])?$user['user']:'',
					);
					$id = $this->add($options);
					D('AppCommentUnread')->addAppCommentUnread($id,$put['appId']);
					return;
				}else{
					result('该应用没有发布');
				}
			}
		}

		public function deleteAppComment($get)
		{
			if (!empty($get['id'])) {

				if ($res = $this->where(" id = %d",array($get['id']))->find()) {
					/*$res = D('AppPublish')->getValOneForIdToComment($res['publish_id']);
					$isAppPublish = D('AppAdmin')->isAppPublish($res['name']);*/
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

		public function appCommentLists($get)
		{

			if (!empty($get['appId']) ) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `publish_id` = %d",array($get['appId']))->order("time desc")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where(" `publish_id` = %d",array($get['appId']))->order("time desc")->select();
				}
				$res['count'] = $this->where(" `publish_id` = %d",array($get['appId']))->count();
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
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getArrInId($id)
		{
			return $this->where("id IN (%s)",array($id))->order('time desc')->select();
		}
	}