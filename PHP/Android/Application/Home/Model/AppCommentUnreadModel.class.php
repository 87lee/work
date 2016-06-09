<?php
	namespace Home\Model;
	class AppCommentUnreadModel extends \Think\Model
	{
		protected $tableName = 'app_comment_unread';
		// protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addAppCommentUnread($commentId,$publishId)
		{
			if (!empty($commentId)&&!empty($publishId)) {
				$publish = D('AppPublish')->getValOneForIdToComment($publishId);
				$comment = D('AppComment')->getOneForId($commentId);
				if (!empty($publish)&&!empty($comment)) {
					$user = session('androidIsLogin');
					$users = D('User')->getArrIdForNotId($user['id']);
					if (!empty($users)) {
						foreach ($users as  $value) {
							if (!$this->getOneForCommentIdPublishIdUserId($commentId,$publishId,$value['id'])) {
								$options[] = array(
									'publish_id'=>$publishId,
									'comment_id'=>$commentId,
									'user_id'=>$value['id'],
								);
							}
						}
					}
					if (!empty($options)) {
						$this->addAll($options);
					}
					return ;
				}
			}
		}
		public function getOneForCommentIdPublishIdUserId($commentId,$publishId,$userId)
		{
			return $this->where("comment_id = %d  and publish_id = %d ",array($commentId,$publishId,$userId))->find();
		}
		public function deleteCommentUnread($put)
		{
			if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode($arr, ',');
					$user = session('androidIsLogin');
					if ($res = $this->where("publish_id IN (".$sqlId.") and user_id = %d",array($user['id']))->find()) {
						return $this->where("publish_id IN (".$sqlId.") and user_id = %d",array($user['id']))->delete();
					}
				}
			}else{
				result('param');
			}
			return ;
		}

		public function commentUnreadCount()
		{
			$user = session('androidIsLogin');
			$res['count'] = $this->where(" user_id = %d ",array( $user['id'] ) )->count('distinct publish_id');
			/*echo "\n\r";
			echo $this->getLastSql();
			die;*/
			return $res;
		}
		public function getCurrentCommentPublishIdSql()
		{
			$user = session('androidIsLogin');
			return $this->field('publish_id')->where(" user_id = %d  ",array($user['id']))->group("publish_id")->select(false);
		}
		public function commentUnreadLists($get)
		{
			$user = session('androidIsLogin');
			$query = "SELECT a.comment_id FROM `tb_app_comment_unread` AS a WHERE (  (a.publish_id ,a.comment_id)  IN (   SELECT b.publish_id,b.comment_id FROM tb_app_comment_unread AS b WHERE (  b.user_id = {$user['id']}   ) ) ) GROUP BY a.comment_id";

			// $aaa = $this->query($query);
			// $userObject = D('User');
			$appCommentObject = D('AppComment');
			$appCommentArr = $appCommentObject->getArrInId($query);
			$appPublishObject = D('AppPublish');
			$appPublishSql = $this->field("publish_id")->where(" user_id = %d  ",array($user['id']))->select(false);
			$appPublishArr = $appPublishObject->getArrIdPkgNameInId($appPublishSql);
			$data = array();
			if (!empty($appPublishArr)) {
				$appArr = D('App')->getAll();
				if (!empty($appArr)) {
					foreach ($appArr as  $value) {
						$appData[ $value['name'] ] = $value['app'];
					}
				}
				/*var_dump($appPublishArr);
				die;*/
				foreach ($appPublishArr as  $value) {
					$value['name'] = !empty($appData[ $value['pkgName'] ])?$appData[ $value['pkgName'] ]:$value['pkgName'];
					unset($value['pkgName']);
					$data[$value['id']] = $value;
				}
				$UserArr = D('User')->getAllNameUser();
				foreach ($UserArr as  $value) {
					$nameArr[$value['user']] = $value['name'];
				}
				if (!empty($appCommentArr)) {
					foreach ($appCommentArr as $key => $value) {
						if (!empty($data[$value['publishId']])) {
							$data[ $value['publishId'] ]['commentLists'][] = array(
								'content'=>$value['content'],
								'user'=>!empty($nameArr[$value['user']])?$nameArr[$value['user']]:$value['user'],
								'time'=>$value['time'],
							);
						}
					}
				}
			}
			// echo $appPublishObject->getLastSql();
			// $userArr = $this->field("u.*,c.*")->alias("c")->join('tb_user AS u ON u.id=c.user_id')->where(" user_id = %d  ",array($user['id']))->order("user")->select();
			$res['extra'] = array_values($data);
			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			return $res;
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
	}