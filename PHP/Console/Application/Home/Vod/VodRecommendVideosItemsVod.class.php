<?php
	namespace Home\Vod;
	class VodRecommendVideosItemsVod extends \Think\Model
	{
		protected $tableName = 'vod_recommend_videos_items';
		protected $tablePrefix = '' ;
		// protected $tablePrefix = isset( $dbVod = C('DB_VOD') ) ? $dbVod['DB_PREFIX']:DB_PREFIX;
		protected $connection = 'DB_VOD';
		protected $_map = array(
			'groupId'=>'group_id',
			'videoId'=>'video_id',
			'videoName'=>'video_name',
			'videoType'=>'video_type',
			'videoPicture'=>'video_picture',
			'videoThumb'=>'video_thumb',
			'appId'=>'app_id',
			'appName'=>'app_name',
			'appSkipid'=>'app_skipid',
			'appPay'=>'app_pay',
		);
		/*protected $_validate = array(
			array('publish','','该版本已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/
		public function syncVideoGroupForGroupId(VodRecommendVideosVod $vodRecommendVideosVod,$groupId)
		{
			$videosItems = $this->where('group_id = %d and invalid = 0',array($groupId))->select();

			if (!empty($videosItems)) {
				foreach ($videosItems as $key => $value) {
					$data['list'][] = array(
						'id'=>$value['videoId'],
						'name'=>$value['videoName'],
						// 'type'=>$value['videoType'],
						'picture'=>$value['videoPicture'],
						'thumb'=>$value['videoThumb'],
						'desc'=>$value['desc'],
						'doubanid'=>$value['doubanid'],
						'position'=>$value['position'],
						'score'=>$value['score'],
						'important'=>$value['recommend'] == 0?false:true,
						'app'=>array(
							'id'=>$value['appId'],
							'name'=>$value['appName'],
							'skipid'=>$value['appSkipid'],
							'pay'=>$value['appPay'],
						)

					);
				}

			}else{
				$data['list'] = array();
			}
			$videos = json_encode($data,JSON_UNESCAPED_UNICODE);
			$vodRecommendVideosVod->modifyVideosForId($groupId,$videos);
			return;
		}
		public function checkVideoInvalid()
		{
			$res = $this->where('invalid = 0')->select();
			$vodProgramSource = D('VodProgramSource','Vod');
			$vodRecommendVideos = D('VodRecommendVideos','Vod');
			foreach ($res as $key => $value) {

				if (!$vodProgramSource->getOneForProgramIdLinkData($value['videoId'],$value['appSkipid'])) {

					$options = array(
						'invalid' => 1
					);
					$this->where('id =%d',array($value['id']))->save($options);
					$this->syncVideoGroupForGroupId($vodRecommendVideos,$value['groupId']);
				}
			}
			return ;
		}
		public function addVideosConfigItems($put)
		{

			if ( !($put['position'] == '0' || $put['position'] == '1') || !($put['recommend'] == '0' || $put['recommend'] == '1') || empty($put['groupId']) || !isset($put['doubanid'])  || !isset($put['score'])  || !isset($put['videoId'])  || empty($put['videoName']) || empty($put['videoPicture']) || empty($put['videoThumb']) || empty($put['videoType']) || empty($put['appId'])  || empty($put['appName'])  || empty($put['appSkipid'])  || !($put['appPay'] == 'false' || $put['appPay'] == 'true') ){
				result('param');
			}
			$vodRecommendVideosVod  = D('VodRecommendVideos','Vod');


			if (!$vodRecommendVideosVod->getOneForId($put['groupId'])) {
				result('此视频推荐组不存在');
			}


			if ($this->getOneForGroupIdForVideoId($put['groupId'],$put['videoId'])) {
				result('此视频已存在');
			}
			$str = file_get_contents($put['videoPicture']);
			if ($str === false) {
				result('大海报地址错误');
			}

			$pictureSize = intval(strlen($str)/1024) ;

			if ( $pictureSize > 700) {
				result('大海报'.$pictureSize.'K超过700K限制');
			}

			list($width, $height)  = getimagesize($put['videoPicture']);
			if ($width != 1920) {
				result('大海报宽度'.$width.'不等于1920');
			}

			if ($height != 1080 ) {
				result('大海报高度'.$height.'不等于1080');
			}
			$options = array(
				'group_id'=>$put['groupId'],
				'video_id'=>$put['videoId'],
				'video_name'=>$put['videoName'],
				'video_type'=>$put['videoType'],
				'position'=>$put['position'],
				'video_picture'=>$put['videoPicture'],
				'video_thumb'=>isset($put['videoThumb'])?$put['videoThumb']:'',
				'app_id'=>$put['appId'],
				'app_name'=>$put['appName'],
				'app_skipid'=>$put['appSkipid'],
				'app_pay'=>$put['appPay'],
				'invalid'=>0,
				'recommend'=>$put['recommend'],
				'doubanid'=>$put['doubanid'],
				'score'=>$put['score'],
				'desc'=>isset($put['desc'])?$put['desc']:''
			);

			if ($this->create($options,1)) {
				$this->add();
			}else{
				result($this->getError());
			}
			$this->syncVideoGroupForGroupId($vodRecommendVideosVod,$put['groupId']);
			return ;
		}
		public function deleteVideosConfigItems($get)
		{
			if (!empty($get['id'])) {
				if (!$res = $this->find($get['id'])) {
					result('该视频不存在');
				}
				$this->delete($get['id']);
				$vodRecommendVideosVod  = D('VodRecommendVideos','Vod');
				$this->syncVideoGroupForGroupId($vodRecommendVideosVod,$res['groupId']);
			}
			return true;
		}
		public function VideosConfigItemsLists($get)
		{
			$noField = "group_id";
			$where = '';

			if (empty($get['id'])) {
				if (!empty($get['groupId'])) {
					$where = "group_id = ". (int)$get['groupId'];
					if (!empty($get['name'])) {
						$where .= " AND `video_name` like '%".$get['name']."%'";
					}

					if (!empty($get['page']) && !empty($get['pageSize'])) {

						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->field($noField,true)->where($where)->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->field($noField,true)->where($where)->select();
					}
					$res['count'] = $this->where($where)->count();
				}

			}else{
				$res['extra'] = $this->field($noField,true)->find($get['id']);
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
		public function getOneForGroupIdForVideoId($groupId,$videoId)
		{
			return $this->where('group_id =%d and video_id = %d',array($groupId,$videoId))->find();
		}
		public function getOneForGroupIdForVideoIdNoId($groupId,$videoId,$id)
		{
			return $this->where('group_id =%d and video_id = %d and id !=%d',array($groupId,$videoId,$id))->find();
		}
		public function modifyVideosConfigItems($put)
		{
			if ( !($put['position'] == '0' || $put['position'] == '1') || !($put['recommend'] == '0' || $put['recommend'] == '1') || empty($put['id']) || !isset($put['doubanid'])  || !isset($put['score'])  || !isset($put['videoId'])  || empty($put['videoName']) || empty($put['videoPicture']) || empty($put['videoThumb']) || empty($put['videoType']) || !isset($put['appId'])  || empty($put['appName'])  || empty($put['appSkipid'])  || !($put['appPay'] == 'false' || $put['appPay'] == 'true') ){
				result('param');
			}

			if (!$res = $this->find($put['id'])) {
				result('此视频推荐不存在');
			}

			$vodRecommendVideosVod  = D('VodRecommendVideos','Vod');

			if (!$vodRecommendVideosVod->getOneForId($put['groupId'])) {
				result('此视频推荐组不存在');
			}
			if ($this->getOneForGroupIdForVideoIdNoId($put['groupId'],$put['videoId'],$put['id'])) {
				result('此视频已存在');
			}

			$str = file_get_contents($put['videoPicture']);
			if ($str === false) {
				result('大海报地址错误');
			}

			$pictureSize = intval(strlen($str)/1024) ;

			if ( $pictureSize > 700) {
				result('大海报'.$pictureSize.'K超过700K限制');
			}

			list($width, $height)  = getimagesize($put['videoPicture']);
			if ($width != 1920) {
				result('大海报宽度'.$width.'不等于1920');
			}

			if ($height != 1080 ) {
				result('大海报高度'.$height.'不等于1080');
			}

			$options = array(
				// 'group_id'=>$put['groupId'],
				'video_id'=>$put['videoId'],
				'video_name'=>$put['videoName'],
				'video_type'=>$put['videoType'],
				'video_picture'=>$put['videoPicture'],
				'position'=>$put['position'],
				'video_thumb'=>isset($put['videoThumb'])?$put['videoThumb']:'',
				'app_id'=>$put['appId'],
				'app_name'=>$put['appName'],
				'app_skipid'=>$put['appSkipid'],
				'app_pay'=>$put['appPay'],
				'recommend'=>$put['recommend'],
				'doubanid'=>$put['doubanid'],
				'score'=>$put['score'],
				'invalid'=>0,
				'desc'=>isset($put['desc'])?$put['desc']:''
			);

			if ($this->create($options,1)) {
				$this->where("id = %d",array($put['id']))->save();
			}else{
				result($this->getError());
			}
			$this->syncVideoGroupForGroupId($vodRecommendVideosVod,$res['groupId']);
			return ;
		}
		public function copyVideosConfig($put)
		{
			if (!empty($put['copyVideosId'])&&!empty($put['groupId'])) {
				$res = $this->getArrForGroupId($put['copyVideosId']);

				if ($res) {
					foreach ($res as $key => $value) {
						$value['groupId'] = (int)$put['groupId'];
						unset($value['id']);
						$addOptionArr[] = $this->create($value);
					}
				}

				if (!empty($addOptionArr)) {
					$this->addAll($addOptionArr);
				}
			}
			return;
		}
		public function getArrForGroupId($groupId)
		{
			return $this->where('group_id = %d',array($groupId))->select();
		}

	}