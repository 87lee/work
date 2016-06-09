<?php
	namespace Home\Live;
	class LiveBootadLive extends \Think\Model
	{
		protected $tableName = 'live_bootad';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'vendorID' =>'vendor_id',
			'groupId'  =>'group_id',
			'isSkip'  =>'is_skip',
			'showTime'  =>'show_time',
			'AB'  =>'ab',
			'whiteList'=>'white_list',
			'blackList'=>'black_list',
		);
		public function addLiveBootad($put)
		{
			if ( empty($put['model'])  || empty($put['type']) && !($put['isSkip'] == 'true' || $put['isSkip'] == 'false') || !isset($put['showTime']) ) {
				result('param');
			}
			if (empty($put['vendorID'])) {
				$put['vendorID'] = 'none';
			}
			if ($put['type'] == 'group') {
				if ( !isset($put['groupId']) || !D('Group')->getValOneForId($put['groupId']) ) {
					result('param');
				}
				if (isset($put['AB'])) {
					$put['AB'] = '';
				}
			}elseif ($put['type'] == 'AB') {
				if (!isset($put['AB'])) {
					result('param');
				}
				if (empty($put['AB'])) {
					$put['AB'] = 0;
				}else{
					$put['AB'] = (int)$put['AB'];
				}
				if (isset($put['groupId'])) {
					$put['groupId'] = '';
				}
			}else{
				result('param');
			}

			if (empty($put['whiteList'])) {
				$put['whiteList'] = '[]';
			}else{
				$whiteList= explode(';', $put['whiteList']);
				$put['whiteList'] = json_encode($whiteList,JSON_UNESCAPED_UNICODE);
			}

			if (empty($put['blackList'])) {
				$put['blackList'] = '[]';
			}else{
				$blackList = explode(';', $put['blackList']);

				$put['blackList'] = json_encode($blackList,JSON_UNESCAPED_UNICODE);
			}

			//---------------------------------------------------------
			require_once '../Base/function/Form.class.php';
			$form = new \form();
			$formData = $form->getFormFile();

			if (!$formData) {
				result('没有上传文件');
			}

			//上传OSS
			require_once '../Base/Ossupclass/OssBase.class.php';
			$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);

			$res = $base->uploadFile($formData);

			foreach ($res as $key => $value) {
				$put['url'] = C('LIVE_STARTUP_PICTURE_PREFIX_ADDR') . $value['oss'];
				$put['md5'] = $formData[$key]['md5_file'];
			}
			//---------------------------------------------------------
			if ($res = $this->getOneForModelVenderIDType($put['model'],$put['vendorID'],$put['type'])) {
				$this->delectForId($res['id']);
			}
			if (isset($put['id'])) {
				unset($put['id']);
			}
			$put['version'] = time();
			$options = $this->create($put);
			/*var_dump($options);
			die;*/
			$this->add();

		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
		public function modifyLiveBootad($put)
		{
			if (!empty($put['id'])&&!empty($put['type'])) {
				if (!$res = $this->find($put['id'])) {
					result('该直播开机画面不存在');
				}

				if ($put['type'] == 'group') {
					if ( !isset($put['groupId']) || !D('Group')->getValOneForId($put['groupId']) ) {
						result('param');
					}

					if (isset($put['AB'])) {
						$put['AB'] = '';
					}
					$options['group_id'] = $put['groupId'];
				}elseif ($put['type'] == 'AB') {
					if (!isset($put['AB'])) {
						result('param');
					}
					$options['ab'] = $put['AB'];
					if (empty($put['AB'])) {
						$put['AB'] = 0;
					}else{
						$put['AB'] = (int)$put['AB'];
					}
					if (isset($put['groupId'])) {
						$put['groupId'] = '';
					}
				}else{
					if ($put['type'] != 'ALL') {
						result('param');
					}
					if (isset($put['AB'])) {
						$put['AB'] = '';
					}
					if (isset($put['groupId'])) {
						$put['groupId'] = '';
					}
				}
				if ($isRes = $this->getOneForModelVenderIDTypeNotID($res['model'],$res['vendorID'],$put['type'],$put['id'])) {
					$this->delectForId($isRes['id']);
				}
				if ($res['type'] == $put['type']) {
					$options['type'] = $put['type'];
					$options['version'] = time();
					$options = $this->create($options);
					$this->where("id=%d",array($put['id']))->save();
				}else{
					$this->delectForId($put['id']);
					$options = array(
						'model'=>$res['model'],
						'vendor_id'=>$res['vendorID'],
						'name'=>$res['name'],
						'show_time'=>$res['showTime'],
						'is_skip'=>$res['isSkip'],
						'md5'=>$res['md5'],
						'url'=>$res['url'],
						'white_list'=>$res['whiteList'],
						'black_list'=>$res['blackList'],
						'group_id'=>isset($put['groupId'])?$put['groupId']:'',
						'ab'=>isset($put['AB'])?$put['AB']:'',
						'type'=>$put['type'],
						'version' => time()
					);
					$this->add($options);

				}
			}else{
				result('param');
			}
		}
		public function deleteLiveBootad($put)
		{
			if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode(',',$arr);
					if ($res = $this->where("id IN (".$sqlId.")")->select()) {

						return $this->where("id IN (".$sqlId.")")->delete();
					}
				}
			}else{
				result('param');
			}
		}
		public function delectForId($id)
		{
			if ($this->find($id)) {
				$this->where("id =%d",array($id))->delete();
			}
		}
		public function liveBootadLists($get)
		{
			/*$listNameArr = D('LiveListName','Live')->getListName();

			if (!empty($listNameArr['extra'])) {
				foreach ($listNameArr['extra'] as  $value) {
					$data[$value['nameId']] = $value['name'];
				}
			}
			*/
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else{
				$where = "";
				if (!empty($get['name'])){
					$where = "   `model` like '%".$get['name']."%'  or `vendor_id`  like '%".$get['name']."%' ";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{

				if (empty($res['extra'][0])) {
					$res['extra']['whiteList'] = json_decode($res['extra']['whiteList'],true);
					$res['extra']['whiteList'] = implode(';', $res['extra']['whiteList']);
					if (!$res['extra']['whiteList']) {
						$res['extra']['whiteList'] = '';
					}

					$res['extra']['blackList'] = json_decode($res['extra']['blackList'],true);
					$res['extra']['blackList'] = implode(';', $res['extra']['blackList']);
					if (!$res['extra']['blackList']) {
						$res['extra']['blackList'] = '';
					}

					if ($res['extra']['type'] == 'group') {
						unset($res['extra']['AB']);
					}elseif ($put['type'] == 'AB') {
						unset($res['extra']['groupId']);
					}else{
						unset($res['extra']['groupId']);
						unset($res['extra']['AB']);
					}
					/*$res['extra']['name'] = isset($data[$res['extra']['nameId']])?$data[$res['extra']['nameId']]:$res['extra']['nameId'];
					unset($res['extra']['nameId']);*/
				}else{
					foreach ($res['extra'] as $key => $value) {
						$value['whiteList'] = json_decode($value['whiteList'],true);
						$res['extra'][$key]['whiteList'] = implode(';', $value['whiteList']);
						if (!$res['extra'][$key]['whiteList']) {
							$res['extra'][$key]['whiteList'] = '';
						}

						$value['blackList'] = json_decode($value['blackList'],true);
						$res['extra'][$key]['blackList'] = implode(';', $value['blackList']);
						if (!$res['extra'][$key]['blackList']) {
							$res['extra'][$key]['blackList'] = '';
						}

						if ($value['type'] == 'group') {
							unset($res['extra'][$key]['AB']);
						}elseif ($value['type'] == 'AB') {
							unset($res['extra'][$key]['groupId']);
						}else{
							unset($res['extra'][$key]['groupId']);
							unset($res['extra'][$key]['AB']);
						}
						/*$res['extra'][$key]['name'] = isset($data[$value['nameId']])?$data[$value['nameId']]:$value['nameId'];
						unset($res['extra'][$key]['nameId']);*/
					}
				}
			}
			return $res;
		}
		public function getOneForModelVenderIDType($model,$vendorID,$type)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and type = '%s'",array($model,$vendorID,$type))->find();
		}
		public function getOneForModelVenderIDTypeNotID($model,$vendorID,$type,$id)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and type = '%s' and id !=%d",array($model,$vendorID,$type,$id))->find();
		}

		/*public function userActionLists($get)
		{
			if (empty($get['id'])) {
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->limit($get['page'],$get['pageSize'])->where("`user` like '%".$get['name']."%' or `interface` like  '%".$get['name']."%' ")->order("time desc")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->where("`user` like '%".$get['name']."%' or `interface` like  '%".$get['name']."%' ")->order("time desc")->select();
					}
					$res['count'] = $this->where("`user` like '%".$get['name']."%'  or `interface` like  '%".$get['name']."%' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->limit($get['page'],$get['pageSize'])->order("time desc")->select();
					}else{
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->order("time desc")->select();
					}
					$res['count'] = $this->count();
				}

			}else{
				$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->find($get['id']);
				$res['count'] =1;

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = 0;
			}
			return $res;
		}*/
	}