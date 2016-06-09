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
			if (!($put['project'] == 'code' || $put['project'] == 'download') || $put['period'] < 1 || $put['value'] < 1 || !($put['method'] == 'count' || $put['method'] =='percent')  || !($put['compare'] == '>' || $put['compare'] == '<') ) {
				result('param');
			}


			if ($this->getOneForProjectMethodCompare($put['project'],$put['method'],$put['compare'])) {
				result('该告警规则已存在');
			}
			$options = array(
				'project'=>$put['project'],
				'period'=>$put['period'],
				'method'=>$put['method'],
				'compare'=>$put['compare'],
				'value'=>$put['value'],
			);
			return $this->add($options);
		}
		public function modifyWarningRules($put)
		{
			if (empty($put['id']) || !($put['project'] == 'code' || $put['project'] == 'download') || $put['period'] < 1 || $put['value'] < 1 || !($put['method'] == 'count' || $put['method'] =='percent')  || !($put['compare'] == '>' || $put['compare'] == '<') ) {
				result('param');
			}
			if (!$this->find(intval($put['id']))) {
				result('此告警规则不存在');
			}
			if ($this->getOneForProjectMethodCompareNoId($put['project'],$put['method'],$put['compare'],$put['id'])) {
				result('该告警规则已存在');
			}
			$options = array(
				'project'=>$put['project'],
				'period'=>$put['period'],
				'method'=>$put['method'],
				'compare'=>$put['compare'],
				'value'=>$put['value'],
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
			if (!empty($get['groupId'])) {

				$field = "";
				$where = '';
				$join = " LEFT JOIN tb_warning_rules_object AS b on a.id = b.rules_id  LEFT JOIN tb_warning_group AS b on c.id = b.group_id ";
				/*if (!empty($get['name'])) {
					$where = " AND (  b.user LIKE  '%".$get['name']."%' ) ";
				}*/
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