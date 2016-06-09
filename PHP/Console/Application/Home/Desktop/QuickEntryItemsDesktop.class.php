<?php
	namespace Home\Desktop;
	class QuickEntryItemsDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_items';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'forcusPath' =>'forcus_path', // 把表单中name映射到数据表的username字段
			'normalPath'  =>'normal_path', // 把表单中的mail映射到数据表的email字段
			'quickId'  =>'quick_id', // 把表单中的actionId映射到数据表的action_id字段
			'actionInfo'  =>'action_info', // 把表单中的actionId映射到数据表的action_id字段
			'itemX'  =>'itemx', // 把表单中的actionId映射到数据表的action_id字段
			'itemY'  =>'itemy', // 把表单中的actionId映射到数据表的action_id字段
		);
		public function addQuickEntryItems($quickEntrytId,$options)
		{
			foreach ($options as $key => $value) {
				if (!empty($value)) {
					$value['quick_id'] = $quickEntrytId;
					$value['quickId'] = $quickEntrytId;
					$res[] = $this->create($value);
				}
			}
			$this->addAll($res);
		}
		public function deleteQuickEntryItems($id)
		{
			if ($this->where("`quick_id`=%d",array($id))->find()) {
				$this->where("`quick_id`=%d",array($id))->delete();
			}

			return true;
		}
		/*public function actionAppLists($response)
		{
			foreach ($response as $key => $value) {
				$actionInfo = json_decode($value['actionInfo'],true);
				if (!empty($actionInfo)) {
					switch ($actionInfo['type']) {
						case 'APP':
							if (isset($actionInfo['pkgName']) &&isset($actionInfo['appName']) ) {
								$response[$key]['type']=$actionInfo['type'];
								$response[$key]['pkgName']=$actionInfo['pkgName'];
								$response[$key]['appName']=$actionInfo['appName'];
							}
							break;
						case 'ACTION':
							if (isset($actionInfo['action']) && isset($actionInfo['extraData']) && isset($actionInfo['appName']) && isset($actionInfo['detailName'])) {
								if (!is_array($actionInfo['extraData'])) {
									$actionInfo['extraData'] = array();
								}else{
									foreach ($actionInfo['extraData'] as $item) {
										if (!isset($item['key']) || !isset($item['value']) ) {
											$actionInfo['extraData'] = array();
											break;
										}
									}
								}
								$response[$key]['type']=$actionInfo['type'];
								$response[$key]['action']=$actionInfo['action'];
								$response[$key]['extraData'] = $actionInfo['extraData'];
								$response[$key]['appName']=$actionInfo['appName'];
								$response[$key]['detailName']=$actionInfo['detailName'];
							}
							break;
						case 'COMPONENT':
							if (isset($actionInfo['clsName']) && isset($actionInfo['extraData']) && isset($actionInfo['component']) && isset($actionInfo['appName']) && isset($actionInfo['detailName'])) {
								if (!is_array($actionInfo['extraData'])) {
									$actionInfo['extraData'] = array();
								}else{
									foreach ($actionInfo['extraData'] as $item) {
										if (!isset($item['key']) || !isset($item['value']) ) {
											$actionInfo['extraData'] = array();
											break;
										}
									}
								}
								$response[$key]['type']=$actionInfo['type'];
								$response[$key]['clsName']=$actionInfo['clsName'];
								$response[$key]['component']=$actionInfo['component'];
								$response[$key]['appName']=$actionInfo['appName'];
								$response[$key]['detailName']=$actionInfo['detailName'];
								$response[$key]['extraData'] =$actionInfo['extraData'];

							}
							break;
						case 'URI':
							if (isset($actionInfo['uri'])) {
								$response[$key]['type']=$actionInfo['type'];
								$response[$key]['uri']=$actionInfo['uri'];

							}
							break;
						default:

							break;
					}
				}
				unset($response[$key]['actionInfo']);
			}
			$response = array_values($response);
			if (empty($response)) {
				$response = array();
			}
			return $response;
		}*/

		public function quickEntryLists($id =null)
		{
			$field = "`name`,`itemx`,`itemy`,`action_info` as actionInfo,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath";
			if (!empty($id)) {

				$res['extra'] = D('QuickEntry','Desktop')->getValOneForId($id);

				if (!empty($res['extra'])) {
					$resSub = $this->field($field)->where("`quick_id`=%d",array($id))->select();

					if ($resSub) {
						foreach ($resSub as $key => $value) {
							$actionInfo = json_decode($value['actionInfo'],true);
							unset($value['actionInfo']);
							$data[$key] = array_merge($value,checkActionApp($actionInfo));
						}
						$res['extra']['extra'] = $data;
					}else{
						$res['extra']['extra']= array();
					}
				}else{
					$res['extra'] = array();
				}
			}else{
				$res['extra'] = D('QuickEntry','Desktop')->getValALL();
				if (empty($res['extra'])) {
					$res['extra'] = array();
				}else{

					foreach ($res['extra'] as $k => $v) {
						$resSub = $this->field($field)->where("`quick_id`=%d",array($v['id']))->select();
						if ($resSub) {
							$data = array();
							foreach ($resSub as $key => $value) {
								$actionInfo = json_decode($value['actionInfo'],true);
								unset($value['actionInfo']);
								$data[$key] = array_merge($value,checkActionApp($actionInfo));
							}

							$res['extra'][$k]['extra'] = $data;
							/*$resSub = $this->actionAppLists($resSub);
							$res['extra'][$k]['extra'] = $resSub;*/
						}else{
							$res['extra'][$k]['extra'] = array();
						}
					}
				}
			}
			return $res;
		}
		public function modifyQuickEntryItems($quickEntrytId,$options)
		{
			if ($this->where("`quick_id`=%d",array($quickEntrytId))->find()) {
				$this->where("`quick_id`=%d",array($quickEntrytId))->delete();
			}

			foreach ($options as $key => $value) {
				if (!empty($value)) {
					$value['quick_id'] = $quickEntrytId;
					$value['quickId'] = $quickEntrytId;
					$res[] = $this->create($value);
				}
			}
			if (!empty($res)) {
				$this->addAll($res);
			}

		}
	}