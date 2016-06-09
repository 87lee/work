<?php
	namespace Home\Vod;
	class VodRecommendVideosVod extends \Think\Model
	{
		protected $tableName = 'vod_recommend_videos';
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

		public function addVideosConfig($put)
		{
			if (empty($put['publish'])) {
				result('请添加版本');
			}


			$options = array(
				'publish'=>strtotime($put['publish']),
				'videos'=>'[]'
			);

			if ($this->create($options,1)) {
				return $this->add();
			}else{
				result($this->getError());
			}
		}
		public function deleteVideosConfig($get)
		{
			if (!empty($get['id'])) {
				if (!$res = $this->find($get['id'])) {
					result('该版本不存在');
				}
				$this->delete($get['id']);
			}
			return true;
		}
		public function VideosConfigLists($get)
		{
			$field = "id,FROM_UNIXTIME(`publish`,'%Y-%m-%d %H:%i:%s') as publish,time";
			$where = '';
			if (empty($get['id'])) {


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
			}
			return $res;
		}
		public function modifyVideosConfig($put)
		{
			if (empty($put['publish']) || empty($put['id'])) {
				result('param');
			}
			$put['id'] = intval($put['id']);

			if (!$this->find($put['id'])) {
				result('该版本不存在');
			}

			if ($this->getOneForPublishNoId($put['publish'],$put['id'])) {
				result('该版本已存在');
			}

			$options = array(
				'publish'=>strtotime($put['publish']),
			);

			if ($this->create($options,2)) {
				return $this->where("`id`=%d",array($put['id']))->save();
			}else{
				result($this->getError());
			}
		}



		public function getOneForPublishNoId($publish,$id)
		{
			return $this->where('publish = %d and id != %d',array($publish,$id))->find();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function modifyVideosForId($id,$videos)
		{
			if ($res = $this->find($id)) {
				$options = array(
					'videos'=>$videos
				);
				$this->where('id = %d',array($id))->save($options);
			}
			return;
		}
		public function copyVideosConfig($put)
		{
			if (empty($put['publish'])) {
				result('请添加版本');
			}
			if (empty($put['copyVideosId'])) {
				result('请复制的版本');
			}
			if (!$res = $this->find($put['copyVideosId'])) {
				result('复制的版本不存在');
			}
			$options = array(
				'publish'=>strtotime($put['publish']),
				'videos'=>$res['videos']
			);

			if ($this->create($options,1)) {
				return $this->add();
			}else{
				result($this->getError());
			}
		}
	}