<?php
	namespace Home\Ota;
	class ModelVersionOta extends \Think\Model
	{
		protected $tableName = 'model_version';
		protected $connection = 'DB_OTA';
		protected $_map = array(
			'modelID' =>'model_id',
			'fileID'  =>'file_id',
			'whiteList' =>'white_list',
			'blackList'  =>'black_list',
		);

		public function addModelVersion($put)
		{
			if (!empty($put['modelID'])&&isset($put['fileID'])&&isset($put['version'])&&isset($put['length']) ) {
				if (D('Model','Ota')->find($put['modelID'])) {

					if ($this->where("`model_id`=%d and `version` = '%s'",array($put['modelID'],$put['version']))->find()) {
						result('该版本已存在');
					}else{
						if (!empty($put['whiteList'])&&is_array($put['whiteList'])) {
							foreach ($put['whiteList'] as  $value) {
								$whiteList[] = $value;
							}
							$put['whiteList'] = json_encode($whiteList,JSON_UNESCAPED_UNICODE);
							unset($whiteList);
						}else{
							$put['whiteList'] = '[]';
						}
						if (!empty($put['blackList'])&&is_array($put['blackList'])) {
							foreach ($put['blackList'] as  $value) {
								$blackList[] = $value;
							}
							$put['blackList'] = json_encode($blackList,JSON_UNESCAPED_UNICODE);
							unset($blackList);
						}else{
							$put['blackList'] = '[]';
						}
						if (!empty($put['desc'])&&is_array($put['desc'])) {
							foreach ($put['desc'] as  $value) {
								$desc[] = $value;
							}
							$put['desc'] = json_encode($desc,JSON_UNESCAPED_UNICODE);
							unset($desc);
						}else{
							$put['desc'] = '[]';
						}

						$options = array(
							'black_list'=>$put['blackList'],
							'white_list'=>$put['whiteList'],
							'desc'=>$put['desc'],
							'model_id'=>$put['modelID'],
							'file_id'=>$put['fileID'],
							'version'=>$put['version'],
							'length'=>intval($put['length']),
							'time'=>time()
						);
						return $this->add($options);
					}
				}else{
					result('该厂商不存在');
				}
			}else{
				result('param');
			}
		}

		public function modifyModelVersion($put)
		{
			if (!empty($put['id'])&&isset($put['fileID'])&&isset($put['version'])&&isset($put['length']) ) {
				if ($modelVersion = $this->find($put['id'])) {
					if ($this->where("`model_id`=%d and `version` = '%s' and `id` != %d",array($modelVersion['modelID'],$put['version'],$put['id']))->find()) {
						result('该版本已存在');
					}else{
						if (!empty($put['whiteList'])&&is_array($put['whiteList'])) {
							foreach ($put['whiteList'] as  $value) {
								$whiteList[] = $value;
							}
							$put['whiteList'] = json_encode($whiteList,JSON_UNESCAPED_UNICODE);
							unset($whiteList);
						}else{
							$put['whiteList'] = '[]';
						}
						if (!empty($put['blackList'])&&is_array($put['blackList'])) {
							foreach ($put['blackList'] as  $value) {
								$blackList[] = $value;
							}
							$put['blackList'] = json_encode($blackList,JSON_UNESCAPED_UNICODE);
							unset($blackList);
						}else{
							$put['blackList'] = '[]';
						}
						if (!empty($put['desc'])&&is_array($put['desc'])) {
							foreach ($put['desc'] as  $value) {
								$desc[] = $value;
							}
							$put['desc'] = json_encode($desc,JSON_UNESCAPED_UNICODE);
							unset($desc);
						}else{
							$put['desc'] = '[]';
						}

						$options = array(
							'black_list'=>$put['blackList'],
							'white_list'=>$put['whiteList'],
							'desc'=>$put['desc'],
							'file_id'=>$put['fileID'],
							'version'=>$put['version'],
							'length'=>intval($put['length']),
						);
						return $this->where("`id`=%d",array($put['id']))->save($options);
					}

				}else{
					result('该厂商版本不存在');
				}
			}else{
				result('param');
			}
		}
		public function deleteModelVersion($put)
		{
			if (!empty($put)&&is_array($put)) {
				foreach ($put as  $value) {
					$sqlID[] = (int)$value;
				}
				if (!empty($sqlID)) {
					$sqlID = implode(',', $sqlID);
					$sqlID = trim($sqlID,',');
					return $this->where("`id` IN (".$sqlID.")")->delete();
				}
			}
			/*if ($this->find($id)) {
				return $this->delete($id);
			}else{
				result('该厂商版本不存在');
			}*/
		}
		public function modelLists($id = null,$name= null ,$page=null,$pageSize=null,$modelID = null)
		{
			$field = "`id`,file_id,version,desc,length,white_list,black_list,FROM_UNIXTIME(time,'%Y-%m-%d %H:%i:%s') as time";
			if ($name != null) {
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->field($field)->where("`model_id`='".$modelID."' and (`version` like '%".$name."%' or `file_id` like '%".$name."%' )")->limit($page,$pageSize)->select();
				}else{
					$res['extra'] = $this->field($field)->where("`model_id`='".$modelID."' and (`version` like '%".$name."%' or `file_id` like '%".$name."%' )")->select();
				}
				$res['count'] = $this->where("`model_id`='".$modelID."' and (`version` like '%".$name."%' or `file_id` like '%".$name."%' )")->count();
			}else{
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->field($field)->where("`model_id`='%s'",array($modelID))->limit($page,$pageSize)->select();
				}else{
					$res['extra'] = $this->field($field)->where("`model_id`='%s'",array($modelID))->select();
				}
				$res['count'] = $this->where("`model_id`='%s'",array($modelID))->count();
			}
			if (!empty($res['extra'])) {
				foreach ($res['extra'] as $key => $value) {
					$res['extra'][$key]['desc'] = json_decode($value['desc'],true);
					$res['extra'][$key]['whiteList'] = json_decode($value['whiteList'],true);
					$res['extra'][$key]['blackList'] = json_decode($value['blackList'],true);
				}
			}else{
				$res['extra'] = array();
				$res['count'] = 0;
			}
			result(true,$res);
		}

	}