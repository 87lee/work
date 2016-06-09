<?php
	namespace Home\Monitoring;
	class WarningGroupItemsMonitoring extends \Think\Model
	{
		protected $tableName = 'warning_group_items';
		protected $connection = 'DB_MONITORING';
		protected $_map = array(
			'groupId' =>'group_id',
			'userId'  =>'user_id',
		);
		public function addWarningGroupItems($put)
		{
			if (empty($put['userIdList'])||empty($put['groupId']) || !is_array($put['userIdList'])) {
				result('param');
			}

			$put['userIdList'] = array_map( 'intval' , $put['userIdList']);
			$put['userIdList'] = array_unique($put['userIdList'] );
			$put['groupId'] = intval($put['groupId']);

			if (!D('WarningGroup','Monitoring')->getOneForId($put['groupId'])) {
				result('该告警组不存在');
			}

			$userIdSql = implode(',', $put['userIdList']);
			//实例化
			$userObject = D('user');
			if (count($put['userIdList']) != $userObject->getCountForIdSql($userIdSql)) {
				result('用户不存在');
			}
			//检查邮箱是否为空

			if ($msg = $userObject->checkEmailForIdSql($userIdSql)) {
				result($msg);
			}

			$warningGroupItems = $this->getArrForGroupIdUserIdSql($put['groupId'],$userIdSql);

			foreach ($put['userIdList'] as $key => $value) {
				$isBool = false;
				foreach ($warningGroupItems as $k => $v) {

					if ($v['userId'] == $value) {
						$isBool = true;
					}
				}
				if ($isBool) {
					continue;
				}
				$options[] = array(
					'group_id'=>$put['groupId'],
					'user_id'=>$value,
				);
			}
			if (!empty($options)) {
				$this->addAll($options);
			}
			return ;
		}
		public function getArrForGroupIdUserIdSql($groupId,$userIdSql)
		{
			return $this->where("group_id = %d and user_id in (%d)",array($groupId,$userIdSql))->select();
		}
		public function deleteWarningGroupItems($put)
		{
			if (!empty($put) && is_array($put)) {
				$arr = array_map('intval', $put);
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode( ',',$arr);
					if ( $res = $this->getOneInId($sqlId) ) {
						$this->where("id IN (%s)",array($sqlId))->delete();
					}
				}
			}else{
				result('param');
			}
			return ;

		}
		public function warningGroupItemsLists($get)
		{
			if (!empty($get['groupId'])) {
				$get['groupId'] = intval($get['groupId']);
				$field = "a.id,b.user,b.email";
				$where = ' a.group_id = '.$get['groupId'];
				$join = " LEFT JOIN tb_user AS b on a.user_id = b.id";
				if (!empty($get['name'])) {
					$where .= " AND (  b.user LIKE  '%".$get['name']."%' ) ";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->alias(" AS a")->field($field)->limit($get['page'],$get['pageSize'])->join($join)->where($where)->select();
				}else{
					$res['extra'] = $this->alias(" AS a")->field($field)->where($where)->join($join)->select();
				}
				$res['count'] = $this->alias(" AS a")->where($where)->join($join)->count();

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		public function getOneForNameNoId($name,$id)
		{
			return $this->where("name = '%s' and id != %d",array($name,$id))->find();
		}
		public function getOneInId($id)
		{
			return $this->where("id IN (%s)",array($id))->find();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s'",array($name))->find();
		}
	}