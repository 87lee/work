<?php
	namespace Home\Monitoring;
	class InterfaceWarningRulesMonitoring extends \Think\Model
	{
		protected $tableName = 'interface_warning_rules';
		protected $connection = 'DB_MONITORING';
		protected $_map = array(
			'projectId' =>'project_id',
		);
		public function addWarningRules($put)
		{

			if (empty($put['code']) || empty($put['projectId']) || $put['period'] < 1 || $put['value'] < 1 || !($put['method'] == 'count' || $put['method'] =='percent')  || !($put['compare'] == '&gt;'  /*>*/ || $put['compare'] == '&lt;'  /*<*/) ) {
				result('param');
			}
			if (!D('InterfaceGroupItem','Monitoring')->getOneInId((int)$put['projectId'])) {
				result('该接口不存在');
			}
			if ($put['compare'] == '&gt;'  /*>*/) {
				$put['compare'] = '>';
			}elseif ($put['compare'] == '&lt;') {
				$put['compare'] = '<';
			}
			if ($this->getOneForProjectIdMethodCompareCode($put['projectId'],$put['method'],$put['compare'],$put['code'])) {
				result('该告警规则已存在');
			}
			$options = array(
				'project_id'=>$put['projectId'],
				'period'=>(int)$put['period'],
				'method'=>$put['method'],
				'compare'=>$put['compare'],
				'value'=>(int)$put['value'],
				'code'=>$put['code'],
				'status'=>0,
				'invalid'=>0,
			);
			return $this->add($options);
		}
		public function modifyWarningRules($put)
		{
			if (empty($put['id']) || empty($put['code'])  || $put['period'] < 1 || $put['value'] < 1 || !($put['method'] == 'count' || $put['method'] =='percent')  || !($put['compare'] == '&gt;'  /*>*/ || $put['compare'] == '&lt;'  /*<*/) ) {
				result('param');
			}
			if (!$res = $this->find(intval($put['id']))) {
				result('此告警规则不存在');
			}
			if ($put['compare'] == '&gt;'  /*>*/) {
				$put['compare'] = '>';
			}elseif ($put['compare'] == '&lt;') {
				$put['compare'] = '<';
			}
			if ($this->getOneForProjectIdMethodCompareCodeNoId($res['projectId'],$put['method'],$put['compare'],$put['code'],$put['id'])) {
				result('该告警规则已存在');
			}

			$options = array(
				'period'=>(int)$put['period'],
				'method'=>$put['method'],
				'compare'=>$put['compare'],
				'value'=>(int)$put['value'],
				'code'=>$put['code'],
			);
			$this->where("id = %d",array($put['id']))->save($options);
			return $put['id'];
		}

		public function getOneForProjectIdMethodCompareCodeNoId($projectId,$method,$compare,$code,$id)
		{
			return $this->where("project_id = '%s' and method = '%s' and compare = '%s' and code ='%s' and id != %d and invalid = 0",array($project,$method,$compare,$code,$id))->find();
		}
		public function getOneForProjectIdMethodCompareCode($projectId,$method,$compare,$code)
		{
			return $this->where("project_id = '%s' and method = '%s' and compare = '%s' and code ='%s' and invalid = 0",array($projectId,$method,$compare,$code))->find();
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
					$options = array(
						"invalid"=>1
					);
					$this->where("id IN (%s)",array($sqlId))->save($options);
					// $this->where("id IN (%s)",array($sqlId))->delete();
				}
			}

			return ;

		}
		public function warningRulesLists($get)
		{

			if (empty($get['interfaceId'])) {
				return $res = array(
					"extra"=>array(),
					'count'=>0
					);
			}
			$where = 'project_id ='.(int)$get['interfaceId'] . " and invalid = 0 ";
			$field = "a.id,a.period,a.method,a.code,a.compare,a.status,a.value,b.rules_id,b.group_id,c.name";

			$join = " LEFT JOIN tb_interface_warning_rules_object AS b on a.id = b.rules_id  ";
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
						$res['extra'][$value['id']] = $value;
						unset($res['extra'][$value['id']]['group_id']);
						unset($res['extra'][$value['id']]['rules_id']);
						unset($res['extra'][$value['id']]['name']);

						$res['extra'][$value['id']]['warningGroups'] = [["id"=>$value['group_id'],"name"=>$value['name']]];

					}else{
						$res['extra'][$value['id']]['warningGroups'][] = ["id"=>$value['group_id'],"name"=>$value['name']];

					}

				}
			}
			$res['extra'] = array_values($res['extra']);
			return $res;
		}
		public function getAllWarningRulesLists($get)
		{


			// $where = 'project_id ='.(int)$get['interfaceId'];
			// $field = "a.id,a.period,a.method,a.code,a.compare,a.value,b.rules_id,b.group_id,c.name";
			$where = ' invalid = 0 ';
			$field = "a.id,a.period,a.method,a.code,a.compare,a.status,a.value,b.rules_id,b.group_id,c.name,d.interface as rulesWhere,d.name as rulesName,g.name as rulesGroupName,f.id as uid,f.email";

			$join = " LEFT JOIN tb_interface_warning_rules_object AS b on a.id = b.rules_id  ";
			$join .= " LEFT JOIN tb_warning_group AS c on c.id = b.group_id ";
			$join .= " LEFT JOIN tb_interface_group_item AS d on d.id = a.project_id ";

			$join .= " LEFT JOIN tb_warning_group_items AS e on c.id = e.group_id ";
			$join .= " LEFT JOIN tb_user AS f on f.id = e.user_id ";

			$join .= " LEFT JOIN tb_interface_group AS g on g.id = d.group_id ";
			/*if (!empty($get['name'])) {
				$where = " AND (  b.user LIKE  '%".$get['name']."%' ) ";
			}*/

			/*if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$response = $this->alias(" AS a")->field($field)->limit($get['page'],$get['pageSize'])->join($join)->where($where)->select();
			}else{
				$response = $this->alias(" AS a")->field($field)->where($where)->join($join)->select();
			}*/
			$res['count'] = $this->alias(" AS a")->where($where)->count();
			$response = $this->alias(" AS a")->field($field)->where($where)->join($join)->select();

			if (empty($response)) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				foreach ($response as  $value) {
					if (empty($res['extra'][$value['id']])) {

						$res['extra'][$value['id']] = $value;
						unset($res['extra'][$value['id']]['group_id']);
						unset($res['extra'][$value['id']]['rules_id']);
						unset($res['extra'][$value['id']]['name']);
						unset($res['extra'][$value['id']]['uid']);
						unset($res['extra'][$value['id']]['email']);
						$res['extra'][$value['id']]['emails'] = [$value['email']];
					}else{
						$res['extra'][$value['id']]['emails'][] = $value['email'];
					}
					/*$res['extra'][$value['id']]['warningGroups'] = [["id"=>$value['group_id'],"name"=>$value['name']]];
					}else{
						$res['extra'][$value['id']]['warningGroups'][] = ["id"=>$value['group_id'],"name"=>$value['name']];

					}*/
				}
			}

			$res['extra'] = array_values($res['extra']);
			return $res;
		}
		public function modifyWarningRulesStatus($put)
		{

			if ( !empty($put['id']) && isset($put['status']) ) {

				if ( $this->find($put['id']) ) {

					$options = array(
						'status'=>$put['status']
					);
					$this->where('id =%d',array($put['id']))->save($options);
				}
			}
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
		public function getWarningRules($get)
		{
			$where = ' invalid = 0 ';
			$field = "a.id,a.period,a.method,a.code,a.compare,a.status,a.value,b.name,b.interface ,c.name as groupName";
			$join = " LEFT JOIN tb_interface_group_item AS b on b.id = a.project_id ";
			$join .= " LEFT JOIN tb_interface_group AS c on c.id = b.group_id ";
			if (!empty($get['name'])) {
				$where .= " and (b.name LIKE '%" .$get['name']. "%' OR b.interface LIKE '%" .$get['name']. "%' OR c.name LIKE '%" .$get['name']. "%')";
			}
			if (!empty($get['page'])&&!empty($get['pageSize'])) {

				$get['page'] = ($get['page']-1) * $get['pageSize'];
				$res['extra'] = $this->field($field)->alias('AS a')->where($where)->join($join)->limit((int)$get['page'],(int)$get['pageSize'])->select();
			}else{
				$res['extra'] = $this->field($field)->alias('AS a')->where($where)->join($join)->select();
			}
			$res['count'] = $this->alias('AS a')->where($where)->join($join)->count();
			return $res;
		}
	}