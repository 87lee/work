<?php
	namespace Home\Monitoring;
	class WarningRulesMonitoring extends \Think\Model
	{
		protected $tableName = 'warning_rules';
		protected $connection = 'DB_MONITORING';
		/*protected $_map = array(
			'groupId' =>'group_id',
			'userId'  =>'user_id',
		);*/
		public function addWarningRules($put)
		{

			if (empty($put['code']) || !($put['project'] == 'code' || $put['project'] == 'download') || $put['period'] < 1 || $put['value'] < 1 || !($put['method'] == 'count' || $put['method'] =='percent')  || !($put['compare'] == '&gt;'  /*>*/ || $put['compare'] == '&lt;'  /*<*/) ) {
				result('param');
			}


			if ($this->getOneForProjectMethodCompare($put['project'],$put['method'],$put['compare'])) {
				result('该告警规则已存在');
			}
			if ($put['compare'] == '&gt;'  /*>*/) {
				$put['compare'] = '>';
			}elseif ($put['compare'] == '&lt;') {
				$put['compare'] = '<';
			}
			$options = array(
				'project'=>$put['project'],
				'period'=>(int)$put['period'],
				'method'=>$put['method'],
				'compare'=>$put['compare'],
				'value'=>(int)$put['value'],
				'code'=>(int)$put['code'],
			);
			return $this->add($options);
		}
		public function modifyWarningRules($put)
		{
			if (empty($put['id']) || empty($put['code']) || !($put['project'] == 'code' || $put['project'] == 'download') || $put['period'] < 1 || $put['value'] < 1 || !($put['method'] == 'count' || $put['method'] =='percent')  || !($put['compare'] == '&gt;'  /*>*/ || $put['compare'] == '&lt;'  /*<*/) ) {
				result('param');
			}
			if (!$this->find(intval($put['id']))) {
				result('此告警规则不存在');
			}
			if ($this->getOneForProjectMethodCompareNoId($put['project'],$put['method'],$put['compare'],$put['id'])) {
				result('该告警规则已存在');
			}
			if ($put['compare'] == '&gt;'  /*>*/) {
				$put['compare'] = '>';
			}elseif ($put['compare'] == '&lt;') {
				$put['compare'] = '<';
			}
			$options = array(
				'project'=>$put['project'],
				'period'=>(int)$put['period'],
				'method'=>$put['method'],
				'compare'=>$put['compare'],
				'value'=>(int)$put['value'],
				'code'=>(int)$put['code'],
			);
			$this->where("id = %d",array($put['id']))->save($options);
			return $put['id'];
		}

		public function getOneForProjectMethodCompareNoId($project,$method,$compare,$id)
		{
			return $this->where("project = '%s' and method = '%s' and compare = '%s' and id != %d",array($project,$method,$compare,$id))->find();
		}
		public function getOneForProjectMethodCompare($project,$method,$compare)
		{
			return $this->where("project = '%s' and method = '%s' and compare = '%s'",array($project,$method,$compare))->find();
		}
		public function deleteWarningRulesForIdArr($put)
		{
			if (empty($put) && !is_array($put)) {

				result('param');
			}
			$arr = array_map('intval', $put);
			$arr = array_unique($arr);
			if (!empty($arr)) {
				$sqlId = implode( ',',$arr);
				if ( $res = $this->getOneInId($sqlId) ) {
					$this->where("id IN (%s)",array($sqlId))->delete();
				}
			}

			return ;

		}
		public function warningRulesLists($get)
		{


			$field = "a.id,a.project,a.period,a.method,a.code,a.compare,a.value,b.rules_id,b.group_id,c.name";
			$where = '';
			$join = " LEFT JOIN tb_warning_rules_object AS b on a.id = b.rules_id  ";
			$join .= " LEFT JOIN tb_warning_group AS c on c.id = b.group_id ";

			// $join .= " LEFT JOIN tb_warning_group_items AS d on d.id = b.group_id ";
			// $join .= " LEFT JOIN tb_user AS f on f.id = d.user_id ";

			/*if (!empty($get['name'])) {
				$where = " AND (  b.user LIKE  '%".$get['name']."%' ) ";
			}*/

			if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$response = $this->alias(" AS a")->field($field)->limit($get['page'],$get['pageSize'])->join($join)->where($where)->select();
			}else{
				$response = $this->alias(" AS a")->field($field)->where($where)->join($join)->select();
			}
			$res['count'] = $this->alias(" AS a")->where($where)->count();


			if (empty($response)) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				foreach ($response as  $value) {
					if (empty($res['extra'][$value['id']])) {
						$res['extra'][$value['id']] = array(
							'id'=>$value['id'],
							'code'=>$value['code'],
							'project'=>$value['project'],
							'period'=>$value['period'],
							'method'=>$value['method'],
							'compare'=>$value['compare'],
							'value'=>$value['value'],
							'warningGroups' => [["id"=>$value['group_id'],"name"=>$value['name']]]
						);
					}else{
						$res['extra'][$value['id']]['warningGroups'][] = ["id"=>$value['group_id'],"name"=>$value['name']];

					}

				}
			}
			$res['extra'] = array_values($res['extra']);
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