<?php
	namespace Home\Monitoring;
	class DownloadWarningRulesObjectMonitoring extends \Think\Model
	{
		protected $tableName = 'download_warning_rules_object';
		protected $connection = 'DB_MONITORING';
		protected $_map = array(
			'groupId' =>'group_id',
			'rulesId'  =>'rules_id',
		);
		public function addWarningRulesObjectArr($rulesId,$arr)
		{
			foreach ($arr as $value) {
    				$addRulesObject[] = array(
    					'rules_id'=>$rulesId,
    					'group_id'=>$value
				);
    			}
			if (!empty($addRulesObject)) {
				$this->addAll($addRulesObject);
			}
			return ;
		}
		public function modifyWarningRulesObjectArr($rulesId,$arr)
		{
			$warningRulesObjectArr = $this->getArrForRulesId($rulesId);

			if (count($warningRulesObjectArr) >= count($arr)) {
				foreach ($warningRulesObjectArr as $key => $value) {
					if (!empty($arr[$key])) {
						$options = array(
							"group_id"=>$arr[$key]
						);
						$this->where("id = %d",array($value['id']))->save($options);
					}else{
						$deleteIdArr[] = $value['id'];

					}

				}
				if (!empty($deleteIdArr)) {
					$deleteIdSql = implode(',', $deleteIdArr);
					$this->deleteArrForIdSql($deleteIdSql);
				}
			}else{
				foreach ($arr as $key => $value) {
					if (!empty($warningRulesObjectArr[$key])) {
						$options = array(
							"group_id"=>$arr[$key]
						);
						$this->where("id = %d",array($warningRulesObjectArr[$key]['id']))->save($options);
					}else{
						$addOptions[] = $value;


					}
				}
				if (!empty($addOptions)) {
					$this->addWarningRulesObjectArr($rulesId,$addOptions);
				}

			}
			return ;
		}

		public function deleteArrForIdSql($idSql)
		{
			if ($this->where("id in (%d) ",array($idSql))->select()) {
				$this->where("id in (%d) ",array($idSql))->delete();
			}
			return ;
		}
		public function getArrForRulesId($rulesId)
		{
			return $this->where("rules_id = %d ",array($rulesId))->select();
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

		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s'",array($name))->find();
		}
	}