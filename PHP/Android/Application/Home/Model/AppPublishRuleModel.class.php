<?php
	namespace Home\Model;
	class AppPublishRuleModel extends \Think\Model
	{
		protected $tableName = 'app_publish_rule';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'specifiedApp' =>'specified_app',
			'attrName' =>'attr_name',
			'attrValue' =>'attr_value',
			'attrNode' =>'attr_node',
		);
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addAppPublishRule($put)
		{
			if (isAdmin()) {
				if (!empty($put['column']) && isset($put['value'])  && ($put['condition'] == '==' || $put['condition'] == '!=' || $put['condition'] == '>=' || $put['condition'] == '<=' || $put['condition'] == '>' || $put['condition'] == '<') ) {

					if (!empty($put['specifiedApp'])&&is_array($put['specifiedApp'])) {

						foreach ($put['specifiedApp'] as $key => $value) {
							if ( is_string($value)) {
								$specifiedApp[] = $value;
							}
						}
						if (!empty($specifiedApp)) {
							$put['specifiedApp'] = json_encode($specifiedApp,JSON_UNESCAPED_UNICODE);
						}else{
							$put['specifiedApp'] = '[]';
						}
					}else{
						$put['specifiedApp'] = '[]';
					}


					$options = array(
						'column'=>$put['column'],
						'value'=>$put['value'],
						'condition'=>$put['condition'],
						'note'=>isset($put['note'])?$put['note']:'',
						'dependencies'=>$put['dependencies'],
						'specified_app'=>$put['specifiedApp'],
					);

					if (!empty($put['attrName'])) {
						if (empty($put['attrValue'])) {
							result('请填写属性值');
						}
						$options['attr_name'] = $put['attrName'];
						$options['attr_value'] =$put['attrValue'];
					}else{
						$options['attr_name'] = '';
						$options['attr_value'] = '';
					}
					if (!empty($put['attrNode'])) {
						$options['attr_node'] = $put['attrNode'];
					}else{
						$options['attr_node'] = '';
					}

					if (!empty($put['operator'])) {
						if (!isset($put['param'])) {
							result('param');
						}
						if (!($put['operator'] == '+' || $put['operator'] == '-' || $put['operator'] == '*' || $put['operator'] == '/' || $put['operator'] == '%')) {
							result('param');
						}
						$options['param'] = floatval($put['param']);
						$options['operator'] = $put['operator'];
					}
					if ( $this->getOneForColumnOperatorParamConditionValue($options['column'],$options['operator'],$options['param'],$options['condition'],$options['value']) ) {
						result('该发布规则已存在！');
					}
					return $this->add($options);
				}else{
					result('param');
				}
			}else{
				result('auth');
			}
		}
		public function modifyAppPublishRule($put)
		{
			if (isAdmin()) {
				if (!empty($put['id'])&&!empty($put['column']) &&isset($put['value'])&& ($put['condition'] == '==' || $put['condition'] == '!=' || $put['condition'] == '>=' || $put['condition'] == '<=' || $put['condition'] == '>' || $put['condition'] == '<') ) {

					if (empty($this->getOneForIdSql($put['id']))) {
						result('发布规则不存在');
					}
					if (!empty($put['specifiedApp'])&&is_array($put['specifiedApp'])) {
						foreach ($put['specifiedApp'] as $key => $value) {
							if ( is_string($value)) {
								$specifiedApp[] = $value;
							}
						}
						if (!empty($specifiedApp)) {
							$put['specifiedApp'] = json_encode($specifiedApp,JSON_UNESCAPED_UNICODE);
						}else{
							$put['specifiedApp'] = '[]';
						}
					}else{
						$put['specifiedApp'] = '[]';
					}
					$options = array(
						'column'=>$put['column'],
						'value'=>$put['value'],
						'condition'=>$put['condition'],
						'note'=>isset($put['note'])?$put['note']:'',
						'dependencies'=>$put['dependencies'],
						'specified_app'=>$put['specifiedApp'],
					);
					if (!empty($put['attrName'])) {
						if (empty($put['attrValue'])) {
							result('请填写属性值');
						}
						$options['attr_name'] = $put['attrName'];
						$options['attr_value'] =$put['attrValue'];
					}else{
						$options['attr_name'] = '';
						$options['attr_value'] = '';
					}
					if (!empty($put['attrNode'])) {
						$options['attr_node'] = $put['attrNode'];
					}else{
						$options['attr_node'] = '';
					}

					if (!empty($put['operator'])) {
						if (!isset($put['param'])) {
							result('param');
						}
						if (!($put['operator'] == '+' || $put['operator'] == '-' || $put['operator'] == '*' || $put['operator'] == '/' || $put['operator'] == '%')) {
							result('param');
						}
						$options['param'] = floatval($put['param']);
						$options['operator'] = $put['operator'];
					}else{
						if (isset($options['param'])) {
							unset($options['param']);
						}
						if (isset($options['operator'])) {
							unset($options['operator']);
						}
					}
					if ( $this->getOneForColumnOperatorParamConditionValueNoId($options['column'],$options['operator'],$options['param'],$options['condition'],$options['value'],$put['id'] )) {
						result('该发布规则已存在！');
					}
					return $this->where("id = %d",array($put['id']))->save($options);
				}else{
					result('param');
				}
			}else{
				result('auth');
			}
		}
		public function deleteAppPublishRule($put)
		{
			if (!empty($put)&&is_array($put)) {
				if (isAdmin()) {
					foreach ($put as  $value) {
						$deleteIdArr[] = intval($value);
					}
					$deleteIdArr = array_unique($deleteIdArr);
					$idSql = implode(',', $deleteIdArr);

					if ($this->getOneForIdSql($idSql)) {
						return $this->where("id IN (".$idSql.")")->delete();
					}
				}else{
					result('auth');
				}
			}else{
				result('param');
			}
		}
		public function getOneForIdSql($idSql)
		{
			return $this->where("id IN (".$idSql.")")->find();
		}

		public function getOneForColumnOperatorParamConditionValue($column,$operator,$param,$condition,$value)
		{
			if (!empty($operator)) {

				return $this->where(" `column` = '%s' and `operator` = '%s' and `param` = %d and `condition` = '%s' and `value` = '%s' ",array($column,$operator,$param,$condition,$value))->find();
			}else{

				  return $this->where(" `column` = '%s' and `operator` <=> null and `param` <=> null  and `condition` = '%s' and `value` = '%s' ",array($column,$condition,$value))->select();
			}
		}
		public function getOneForColumnOperatorParamConditionValueNoId($column,$operator,$param,$condition,$value,$id)
		{
			if (!empty($operator)) {

				return $this->where(" `column` = '%s' and `operator` = '%s' and `param` = %d and `condition` = '%s' and `value` = '%s' and `id` != %d",array($column,$operator,$param,$condition,$value,$id))->find();
			}else{

				  return $this->where(" `column` = '%s' and `operator` <=> null and `param` <=> null  and `condition` = '%s' and `value` = '%s'  and `id` != %d",array($column,$condition,$value,$id))->select();
			}
		}
		public function appPublishRuleLists($get)
		{
			if (isAdmin()) {
				$where = '';
				if (!empty($get['id'])) {
					$res['extra'] = $this->find($get['id']);
					$res['count'] = 1;
				}elseif (!empty($get['name'])) {
					$where = " `column` like '%".$get['name']."%'  or `condition` like '%".$get['name']."%'  or `value` like '%".$get['name']."%' ";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						if (!empty($value['specifiedApp'])) {
							$res['extra'][$key]['specifiedApp'] = json_decode($value['specifiedApp'],true);
						}else{
							$res['extra'][$key]['specifiedApp'] = array();
						}
					}
				}else{
					if (!empty($res['extra']['specifiedApp'])) {
						$res['extra']['specifiedApp'] = json_decode($res['extra']['specifiedApp'],true);
					}else{
						$res['extra']['specifiedApp'] = array();
					}
				}
			}

			return $res;
		}
		public function getAll()
		{
			return $this->select();
		}
		public function appRelyListsForName($name)
		{
			$res = $this->where(" `name` = '%s'" ,array($name))->select();

			if (empty($res)) {
				$res = array();
			}
			return $res;
		}

		public function appRelyListsForId($id)
		{

			$res = $this->where(" `id` = '%s'" ,array($id))->select();

			if (empty($res)) {
				$res = array();
			}
			return $res;
		}
	}