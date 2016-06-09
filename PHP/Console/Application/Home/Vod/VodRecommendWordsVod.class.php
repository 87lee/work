<?php
	namespace Home\Vod;
	class VodRecommendWordsVod extends \Think\Model
	{
		protected $tableName = 'vod_recommend_words';
		protected $tablePrefix = '' ;
		// protected $tablePrefix = isset( $dbVod = C('DB_VOD') ) ? $dbVod['DB_PREFIX']:DB_PREFIX;
		protected $connection = 'DB_VOD';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		protected $_validate = array(
			array('publish','','该版本已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);

		public function addHotConfig($put)
		{
			if (empty($put['publish'])) {
				result('请添加版本');
			}
			$words = array();
			if (!empty($put['words'])&&is_array($put['words'])){
				foreach ($put['words'] as $key => $value) {
					if (empty($value['keys']) || !is_array($value['keys'])) {
						result('热词格式有误');
					}
					foreach ($value['keys'] as  $v) {
						$words[$key]['keys'][] = $v;
					}
				}
			}
			$options = array(
				'publish'=>strtotime($put['publish']),
				'words'=>!empty($words)?json_encode($words,JSON_UNESCAPED_UNICODE):'[]'
			);

			if ($this->create($options,1)) {
				return $this->add();
			}else{
				result($this->getError());
			}
		}
		public function deleteHotConfig($get)
		{
			if (!empty($get['id'])) {
				if (!$res = $this->find($get['id'])) {
					result('该版本不存在');
				}
				$this->delete($get['id']);
			}
			return true;
		}
		public function hotConfigLists($get)
		{
			$field = "id,FROM_UNIXTIME(`publish`,'%Y-%m-%d %H:%i:%s') as publish,words,time";
			$where = '';
			if (empty($get['id'])) {

				if (!empty($get['name'])) {
					$where = "`name` like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($pageSize)) {
					$get['page'] = $get['page']*$pageSize - $pageSize;
					$res['extra'] = $this->limit($get['page'],$pageSize)->field($field)->where($where)->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->field($field)->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();

			}else{
				$res['extra'] = $this->field($field)->find($get['id']);
				if (!empty($res['extra'])) {
					$res['count'] = 1;
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['words'] = json_decode($value['words'],true);
					}
				}else{
					$res['extra']['words'] = json_decode($res['extra']['words'],true);
				}
			}
			return $res;
		}
		public function modifyHotConfig($put)
		{
			if (empty($put['publish']) || empty($put['id'])) {
				result('param');
			}
			$words = array();
			$put['id'] = intval($put['id']);

			if (!$this->find($put['id'])) {
				result('该版本不存在');
			}

			if ($this->getOneForPublishNoId($put['publish'],$put['id'])) {
				result('该版本已存在');
			}

			if (!empty($put['words'])&&is_array($put['words'])){
				foreach ($put['words'] as $key => $value) {
					if (empty($value['keys']) || !is_array($value['keys'])) {
						result('热词格式有误');
					}
					foreach ($value['keys'] as  $v) {
						$words[$key]['keys'][] = $v;
					}
				}
			}

			$options = array(
				'publish'=>strtotime($put['publish']),
				'words'=>!empty($words)?json_encode($words,JSON_UNESCAPED_UNICODE):'[]'
			);

			if ($this->create($options,2)) {
				return $this->where("`id`=%d",array($put['id']))->save();
			}else{
				result($this->getError());
			}
		}
		public function copyHotConfig($put)
		{
			if (!empty($put['copyId'])&&!empty($put['copyToIds'])&&is_array($put['copyToIds'])) {
				$put['copyToIds'] = array_map('intval', $put['copyToIds']);
				$put['copyToIds'] = array_unique($put['copyToIds']);
				$copyOne = $this->getOneForId($put['copyId']);
				if (!$copyOne) {
					result('复制的热词不存在');
				}
				$idSql = implode(',', $put['copyToIds']);
				$copyToArr = $this->getArrForIdSql($idSql);
				if (count($copyToArr) != count($put['copyToIds'])) {
					result('需要复制热词的对象不存在');
				}
				$copyWords = json_decode($copyOne['words'],true);
				foreach ($copyToArr as $key => $value) {
					$value['words'] = json_decode($value['words'],true);
					$modifyWordsArr = array_merge($value['words'],$copyWords);
					$modifyOptions = array(
						'words'=>json_encode($modifyWordsArr,JSON_UNESCAPED_UNICODE)
					);
					// var_dump($value);
					$this->where("id = %d",array($value['id']))->save($modifyOptions);
				}
			}
		}
		public function getArrForIdSql($idSql)
		{
			return $this->where("id IN (".$idSql.")")->select();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getOneForPublishNoId($publish,$id)
		{
			return $this->where('publish = %d and id != %d',array($publish,$id))->find();
		}

	}