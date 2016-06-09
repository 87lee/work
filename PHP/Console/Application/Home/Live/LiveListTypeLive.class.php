<?php
	namespace Home\Live;
	class LiveListTypeLive extends \Think\Model
	{
		protected $tableName = 'live_list_type';
		protected $connection = 'DB_LIVE';
		protected $_map = array(
			'isRecommend' =>'is_recommend',
			'typeId'  =>'type_id',
			'typeName'  =>'type_name',
			'nameId'  =>'name_id',
			'urlSimp'  =>'url_simp',
		);
		public function getLiveListTypeLists($get)
		{
			$where= '';
			if (!empty($get['id'])) {
				$where= "name_id ='".$get['id']."'";
			}
			return $this->where($where)->select();
		}
		public function addLiveListTypeArr($put)
		{
			return $this->addAll($put);
		}
		public function addLiveListType($put)
		{

			if ($optons = $this->create($put)) {
				if (!$this->where('type_id ="%s" and name_id ="%s"',array($optons['type_id'],$optons['name_id']))->find()) {
					return $this->add();
				}
			}
			return;

		}
		public function deleteLiveListNameArrForNameIDStr($IDStr)
		{

			if ($this->where("name_id IN (".$IDStr.")")->find()) {
				return $this->where("name_id IN (".$IDStr.")")->delete();
			}
		}
		public function deleteTypeForID($id)
		{

			if ($this->find($id)) {
				$this->where('id = %d',array($id))->delete();
			}
		}
		public function deleteLiveListTypeArrForIDStr($IDStr)
		{

			if ($this->where("id IN (%s)",array($IDStr))->find()) {
				return $this->where("id IN (%s)",array($IDStr))->delete();
			}
		}
		public function getLiveListTypeArrUrlForNameID($NameID)
		{
			$field = "type_id as id ,type_name as name ,concat('".C('LIVE_LIST_PREFIX_ADDR')."',`url`) as url,pinyin,is_recommend as isRecommend,version";
			return $this->field($field)->where("name_id = '%s'",array($NameID))->select();
		}
		public function getLiveListTypeArrUrlSimpForNameID($NameID)
		{
			$field = "type_id as id ,type_name as name ,concat('".C('LIVE_LIST_PREFIX_ADDR')."',`url_simp`) as  url,pinyin,is_recommend as isRecommend,version";
			return $this->field($field)->where("name_id = '%s'",array($NameID))->select();
		}
		public function getListTypeName($get)
		{
			if (!empty($get['nameId'])) {
				$field = 'type_id as id,type_name as name';
				$where = "name_id = '%s' ";
				$res['extra'] = $this->field($field)->where($where,array($get['nameId']))->select();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			return $res;
		}
		public function getListType($get)
		{
			if (!empty($get['nameId'])) {
				$field = "id,version,type_id,type_name,pinyin,is_recommend,concat('".C('LIVE_LIST_PREFIX_ADDR')."',`url_simp`)  as  `urlSimp`,concat('".C('LIVE_LIST_PREFIX_ADDR')."',`url`) as  `url` ";
				$where = "name_id = '%s' ";
				$res['extra'] = $this->field($field)->where($where,array($get['nameId']))->select();

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			return $res;
		}
		public function deleteLiveForNoNameIdStr($NameIdStr)
		{
			if ($this->where("name_id NOT IN (".$NameIdStr.") ")->find()) {
				$this->where("name_id NOT IN (".$NameIdStr.") ")->delete();
			}
		}

		public function deleteLiveForNameIdStr($NameIdStr)
		{
			if ($this->where("name_id IN (".$NameIdStr.") ")->find()) {
				$this->where("name_id  IN (".$NameIdStr.") ")->delete();
			}
		}
		public function deleteLiveForNameId($NameId)
		{
			if ($this->where("name_id = '%s' ",array($NameId))->find()) {
				$this->where("name_id  = '%s' ",array($NameId))->delete();
			}
		}
		public function modifyLiveType($id,$optons)
		{

			if ($this->create($optons)) {
				$this->where('id=%d',array($id))->save();
			}
			return;
		}
		public function deleteLiveForNameIdNoIdArr($nameIdNoIdArr)
		{
			if (!empty($nameIdNoIdArr)&&is_array($nameIdNoIdArr)) {
				foreach ($nameIdNoIdArr as $key => $value) {
					$value = array_unique($value);
    					$idStr = implode(",", $value);

    					if ($this->where(" name_id = '".$key."' and  id NOT IN (".$idStr.") ")->find()) {
						$this->where(" name_id = '".$key."' and  id NOT IN (".$idStr.") ")->delete();
					}
				}
			}
		}
		public function getListTypeChannel($get)
		{
			if (!empty($get['id'])) {
				if ($res = $this->getOneForId($get['id'])) {
					$content = file_get_contents( C('LIVE_LIST_PREFIX_ADDR').$res['urlSimp'] );
					if (!$content) {
						result('获取内容有误');
					}
					// echo $content = str_replace("\r\n", "", $content);
					//$content = str_replace("\r", "", $content);
					//$content = str_replace("\n", "", $content);
					$content = str_replace("\r", "", $content);
					$content = str_replace("\n", "", $content);
					$content = str_replace(" ", "", $content);

					$key = 'YZV3141592653589';
					$vi = 'PLANCKH413566743';

					$model =MCRYPT_MODE_CBC;
					$cipher  = MCRYPT_RIJNDAEL_128;

					$content = mcrypt_decrypt($cipher,$key,base64_decode($content),$model,$vi);

					if ($content[strlen($content) - 1] !== '}') {
						$content = rtrim($content,$content[strlen($content) - 1]);
					}
					if ($content) {
						$contentArr = json_decode($content,true);
					}else{
						result('解密失败');
					}
				}
			}
			if (empty($contentArr['channelLists'])) {
				$contentArr['channelLists'] = array();
			}
			return $contentArr;
		}

		public function getOneForId($id)
		{
			return $this->find($id);
		}
		/*public function modifyLiveAdGroup($put)
		{
			if (!empty($put['id'])&&!empty($put['name'])) {
				if (!$this->find($put['id'])) {
					result('该直播广告组不存在');
				}
				if (!$this->getOneForNameNotID($put['name'],$put['id'])) {

					$this->create($put);
					$this->where("id=%d",array($put['id']))->save();
				}else{
					result('该直播广告组已存在');
				}
			}
		}
		public function deleteLiveAdGroup($get)
		{
			if (!empty($get['id'])) {
				if ($this->find($get['id'])) {
					if (D('LiveAd','Live')->getOneForGroupId($get['id'])) {
						result('该直播广告组下有广告');
					}
					$this->where("id = %d",array($get['id']))->delete();
				}
			}
			if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode($arr, ',');
					if ($res = $this->where("id IN (".$sqlId.")")->select()) {

						return $this->where("id IN (".$sqlId.")")->delete();
					}
				}
			}else{
				result('param');
			}
		}
		public function liveAdGroupLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else{
				$where = "";
				if (!empty($get['name'])){
					$where = "   `name` like '%".$get['name']."%'";
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
			}
			return $res;
		}
		public function getOneForName($name)
		{
			return $this->where("name = '%s' ",array($name))->find();
		}
		public function getOneForNameNotID($name,$id)
		{
			return $this->where("name = '%s' and  id !=%d",array($name,$id))->find();
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function userActionLists($get)
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