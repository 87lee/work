<?php
	namespace Home\Desktop;
	class DesktopDownloadQuickEntryDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_download_quick_entry';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopId'=>'desktop_id',
			'slotId'=>'slot_id',
			'w'=>'width',
			'h'=>'height',
			'nextFocusLeftId'=>'next_focus_left_id',
			'nextFocusRightId'=>'next_focus_right_id',
			'nextFocusUpId'=>'next_focus_up_id',
			'nextFocusDownId'=>'next_focus_down_id',
		);
		public function addDesktopDownloadQuickEntryArr($options)
		{
			if (!empty($options)) {

				foreach ($options as  $value) {
					$res = $this->create($value);
					if ($res) {
						$arr[] = $res;
					}
				}
				/*var_dump($arr);
				die;*/
				if (!empty($arr)) {

					return $addId = $this->addAll($arr);
				}

			}

		}
		public function deleteDesktopDownloadQuickEntryForArrId($desktopIDSql)
		{
			if ( $this->where("`desktop_id` IN (".$desktopIDSql.")")->find()) {
				return $this->where("`desktop_id` IN (".$desktopIDSql.")")->delete();
			}
		}
		public function downloadQuickEntryLists($desktopID,$sid)
		{

			$res = $this->where("`desktop_id` = %d and sid = '%s' and type = 'quickEntry'",array($desktopID,$sid))->select();
			if ($res) {
				foreach ($res as $key => $value) {
					$slotIDArr[] = $value['slotID'];
				}
				if (!empty($slotIDArr)) {
					$slotIDArr = array_unique($slotIDArr);
					$slotIDSqlStr = implode(',', $slotIDArr);
					$get['slotIDArrStr'] = $slotIDSqlStr;
					$quickEntrySlot = D('QuickEntrySlot','Desktop')->quickEntrySlotLists($get);
					foreach ($quickEntrySlot['extra'] as $key => $value) {
						unset($value['id']);
						$slotIDArray[$value['slotId']] =$value;
					}
					foreach ($res as $key => $value) {
						$data[$key] = $slotIDArray[$value['slotId']];
						$data[$key]['bg'] = $value['bg'];
						$data[$key]['type'] = $value['type'];
						$data[$key]['x'] = $value['x'];
						$data[$key]['y'] = $value['y'];
						$data[$key]['w'] = $value['w'];
						$data[$key]['h'] = $value['h'];
						$data[$key]['nextFocusLeftId'] = $value['nextFocusLeftId'];
						$data[$key]['nextFocusRightId'] = $value['nextFocusRightId'];
						$data[$key]['nextFocusUpId'] = $value['nextFocusUpId'];
						$data[$key]['nextFocusDownId'] = $value['nextFocusDownId'];
					}
				}
			}else{
				$data = array();
			}
			return $data;
		}
		public function downloadQuickEntryStyleLists($desktopID)
		{
			$data = $this->field('style,animation')->where("`desktop_id` = %d ",array($desktopID))->find();
			if (!$data) {
				$data = array();
			}else{
				$res = $this->where("`desktop_id` = %d  and type = 'globalQuickEntry'",array($desktopID))->select();
				if ($res) {
					foreach ($res as $key => $value) {
						$slotIDArr[] = $value['slotID'];
					}
					if (!empty($slotIDArr)) {
						$slotIDArr = array_unique($slotIDArr);
						$slotIDSqlStr = implode(',', $slotIDArr);
						$get['slotIDArrStr'] = $slotIDSqlStr;
						$quickEntrySlot = D('QuickEntrySlot','Desktop')->quickEntrySlotLists($get);
						foreach ($quickEntrySlot['extra'] as $key => $value) {
							unset($value['id']);
							$slotIDArray[$value['slotId']] =$value;
						}
						foreach ($res as $key => $value) {
							$data['globalItems'][$key] = $slotIDArray[$value['slotId']];
							$data['globalItems'][$key]['bg'] = $value['bg'];
							$data['globalItems'][$key]['type'] = $value['type'];
							$data['globalItems'][$key]['x'] = $value['x'];
							$data['globalItems'][$key]['y'] = $value['y'];
							$data['globalItems'][$key]['w'] = $value['w'];
							$data['globalItems'][$key]['h'] = $value['h'];
							$data['globalItems'][$key]['nextFocusLeftId'] = $value['nextFocusLeftId'];
							$data['globalItems'][$key]['nextFocusRightId'] = $value['nextFocusRightId'];
							$data['globalItems'][$key]['nextFocusUpId'] = $value['nextFocusUpId'];
							$data['globalItems'][$key]['nextFocusDownId'] = $value['nextFocusDownId'];
						}
					}
				}else{
					$data['globalItems'] = array();
				}
			}
			return $data;
		}
		public function downloadQuickEntryListsForDesktopID($desktopID)
		{
			$data = $this->field('id,desktop_id',true)->where("`desktop_id` = %d ",array($desktopID))->order('type')->select();
			if (!$data) {
				$data = array();
			}
			return $data;
		}
		public function createDesktopDownloadQuickEntryLists($desktopId=null,$desktop = null,$error = false)
		{
			$desktopJson = array();
			if ($desktop === null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}

			if ($desktop) {
				$res = $this->where("`desktop_id` = %d",array($desktop['id']))->select();
				if ($res) {
					$desktopJson['style'] = $res[0]['style'];
					$desktopJson['animation'] = $res[0]['animation'];
					foreach ($res as $key => $value) {
						$slotIDArr[] = $value['slotId'];
					}
					if (!empty($slotIDArr)) {
						$slotIDArr = array_unique($slotIDArr);
						$slotIDSqlStr = implode(',', $slotIDArr);
						$get['slotIDArrStr'] = $slotIDSqlStr;
						$quickEntrySlot = D('QuickEntrySlot','Desktop')->quickEntrySlotLists($get);
						if ($quickEntrySlot) {
							foreach ($quickEntrySlot['extra'] as $key => $value) {

								if (!isset($value['actionType']) || !isset($value['slotId']) || !isset($value['isEditable'])  || !isset($value['focusedDrawable'])  || !isset($value['normalDrawable']) ) {
									if ($error) {
										return $result = array('reason'=>isset($value['slotId'])?'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'参数出错':'该桌面可下载快捷入口坑位出错:id参数出错');

									}else{
										result(isset($value['slotId'])?'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'参数出错':'该桌面可下载快捷入口坑位出错:id参数出错');
									}
								}
								$value['id'] = $value['slotId'];
								if (!empty($value['bindApp'])) {
									if (!empty($value['bindApp']['url'])&&!empty($value['bindApp']['pkgName'])&&!empty($value['bindApp']['versionCode'])) {
										$value['appInfo'] = array(
											'url'=>$value['bindApp']['url'],
											'pkgName'=>$value['bindApp']['pkgName'],
											'versionCode'=>$value['bindApp']['versionCode'],
										);
										unset($value['bindApp']);
									}else{
										if ($error) {
											return $result = array('reason'=>isset($value['slotId'])?'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'绑定APP参数出错':'该桌面可下载快捷入口坑位出错:id绑定APP参数出错');
										}else{
											result(isset($value['slotId'])?'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'绑定APP参数出错':'该桌面可下载快捷入口坑位出错:id绑定APP参数出错');
										}
									}
								}

								$value['action'] = desktopQuickSlotCheckAction($value,'快捷坑位',$error);

								if (!empty($value['action']['reason'])) {
									if ($error) {
										return $value['action'];
									}else{
										result($value['action']['reason']);
									}
								}

									/*switch ($value['actionType']) {
										case 'APP':
											if (isset($value['app']['pkgName'])) {
												$value['action']= array(
													'type'=>$value['actionType'],
													'appData'=>array(
														'pkgName'=>$value['app']['pkgName']
													)
												);
												unset($value['app']);
											}else{
												if ($error) {
													return $result = array('reason'=>'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'APP跳转信息出错');

												}else{
													result('该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'APP跳转信息出错');
												}

											}
											break;
										case 'ACTION':
											if (isset($value['action']['action'])) {
												if (!is_array($value['action']['extraData'])) {
													$value['action']['extraData'] = array();
												}else{
													foreach ($value['action']['extraData'] as $item) {
														if (!isset($item['key']) || !isset($item['value']) ) {
															$value['action']['extraData'] = array();
															break;
														}
													}
												}
												$value['action']= array(
													'type'=>$value['actionType'],
													'actionData'=>array(
														'action'=>$value['action']['action']
													),
													'extraData'=>$value['action']['extraData']
												);
											}else{
												if ($error) {
													return $result = array('reason'=>'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'ACTION跳转信息出错');

												}else{
													result('该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'ACTION跳转信息出错');
												}
											}
											break;
										case 'COMPONENT':
											if (isset($value['component']['clsName'])&&isset($value['component']['component'])) {
												if (!is_array($value['component']['extraData'])) {
													$value['component']['extraData'] = array();
												}else{
													foreach ($value['component']['extraData'] as $item) {
														if (!isset($item['key']) || !isset($item['value']) ) {
															$value['component']['extraData'] = array();
															break;
														}
													}
												}
												$value['action']= array(
													'type'=>$value['actionType'],
													'componentData'=>array(
														'pkgName'=>$value['component']['component'],
														'clsName'=>$value['component']['clsName'],
													),
													'extraData'=>$value['component']['extraData']
												);
												unset($value['component']);
											}else{
												if ($error) {
													return $result = array('reason'=>'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'COMPONENT跳转信息出错');

												}else{
													result('该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'COMPONENT跳转信息出错');
												}
											}
											break;
										case 'URI':

											if (isset($value['uri']['uri'])) {
												$value['action']= array(
													'type'=>$value['actionType'],
													'uriData'=>array(
														'uri'=>$value['uri']['uri']
													)
												);
												unset($value['uri']);
											}else{
												if ($error) {
													return $result = array('reason'=>'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'URI跳转信息出错');

												}else{
													result('该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'URI跳转信息出错');
												}
											}
											break;

										default:
											if ($error) {
												return $result = array('reason'=>'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'跳转信息出错');

											}else{
												result('该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'跳转信息出错');
											}
											break;
									}*/
								if (strtolower($value['actionType']) != 'action') {
									unset($value[ strtolower($value['actionType'])]);
								}
								unset($value['actionType']);
								unset($value['slotId']);
								$slotIDArray[$value['id']] = $value;
							}
						}else{
							if ($error) {
								return $result = array('reason'=>'该桌面可下载快捷入口坑位出错:坑位不存在');
							}else{
								result('该桌面可下载快捷入口坑位出错:坑位不存在');
							}
						}
						foreach ($res as $key => $value) {

							if ( !isset($value['slotId']) ||  !isset($value['type']) || !isset($value['bg']) || !isset($value['x']) || !isset($value['y']) || !isset($value['w']) || !isset($value['h'] ) || !isset($value['sid'])  ) {
								if ($error) {
									return $result = array('reason'=>isset($value['slotID'])?'该桌面可下载快捷入口坑位出错:id为'.$value['slotID'].'参数出错':'该桌面可下载快捷入口坑位出错:id参数出错');

								}else{
									result(isset($value['slotID'])?'该桌面可下载快捷入口坑位出错:id为'.$value['slotID'].'参数出错':'该桌面可下载快捷入口坑位出错:id参数出错');
								}
							}
							if (empty($slotIDArray[$value['slotId']])) {

								if ($error) {
									return $result = array('reason'=>isset($value['slotID'])?'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'参数出错':'该桌面可下载快捷入口坑位出错:id参数出错');

								}else{
									result(isset($value['slotId'])?'该桌面可下载快捷入口坑位出错:id为'.$value['slotId'].'参数出错':'该桌面可下载快捷入口坑位出错:id参数出错');
								}
							}
							if ($value['type'] == 'quickEntry') {
								$desktopJson['items'][$key] = $slotIDArray[$value['slotId']];
								$desktopJson['items'][$key]['bg'] = $value['bg'];
								$desktopJson['items'][$key]['x'] = $value['x'];
								$desktopJson['items'][$key]['y'] = $value['y'];
								$desktopJson['items'][$key]['width'] = $value['w'];
								$desktopJson['items'][$key]['height'] = $value['h'];
								$desktopJson['items'][$key]['sid'] = $value['sid'];
								if (isset($value['nextFocusLeftId'])     && $value['nextFocusLeftId'] != '') {
									$desktopJson['items'][$key]['nextFocusLeftId'] = $value['nextFocusLeftId'];
								}
								if (isset($value['nextFocusRightId'])     && $value['nextFocusRightId'] != '') {
									$desktopJson['items'][$key]['nextFocusRightId'] = $value['nextFocusRightId'];
								}
								if (isset($value['nextFocusUpId'])     && $value['nextFocusUpId'] != '') {
									$desktopJson['items'][$key]['nextFocusUpId'] = $value['nextFocusUpId'];
								}
								if (isset($value['nextFocusDownId'])  && $value['nextFocusDownId'] != '') {
									$desktopJson['items'][$key]['nextFocusDownId'] = $value['nextFocusDownId'];
								}
							}elseif ($value['type'] == 'globalQuickEntry') {
								$desktopJson['globalItems'][$key] = $slotIDArray[$value['slotId']];
								$desktopJson['globalItems'][$key]['bg'] = $value['bg'];
								$desktopJson['globalItems'][$key]['x'] = $value['x'];
								$desktopJson['globalItems'][$key]['y'] = $value['y'];
								$desktopJson['globalItems'][$key]['width'] = $value['w'];
								$desktopJson['globalItems'][$key]['height'] = $value['h'];
								$desktopJson['globalItems'][$key]['sid'] = $value['sid'];
								if (isset($value['nextFocusLeftId'])     && $value['nextFocusLeftId'] != '') {
									$desktopJson['globalItems'][$key]['nextFocusLeftId'] = $value['nextFocusLeftId'];
								}
								if (isset($value['nextFocusRightId'])     && $value['nextFocusRightId'] != '') {
									$desktopJson['globalItems'][$key]['nextFocusRightId'] = $value['nextFocusRightId'];
								}
								if (isset($value['nextFocusUpId'])     && $value['nextFocusUpId'] != '') {
									$desktopJson['globalItems'][$key]['nextFocusUpId'] = $value['nextFocusUpId'];
								}
								if (isset($value['nextFocusDownId'])  && $value['nextFocusDownId'] != '') {
									$desktopJson['globalItems'][$key]['nextFocusDownId'] = $value['nextFocusDownId'];
								}
							}
						}
						if (!empty($desktopJson['globalItems'])) {
							$desktopJson['globalItems'] = array_values($desktopJson['globalItems']);
						}else{
							$desktopJson['globalItems']=array();
						}
						if (!empty($desktopJson['items'])) {
							$desktopJson['items'] = array_values($desktopJson['items']);
						}else{
							$desktopJson['items']=array();
						}
					}
				}

			}else{
				if ($error) {
					return $result = array('reason'=>'桌面不存在:参数为空');
					// return false;
				}else{
					result('桌面不存在');
				}
			}
			return $desktopJson;
		}
		public function modifyDesktopBlockInfo($blockId,$options)
		{
			$res = $this->where("`block_id` =%d ",array($blockId))->find();
			if ($res) {
				$this->create($options);
				$this->where("`block_id` =%d ",array($blockId))->save();
				return $res['id'];
			}else{
				$options['block_id'] = $blockId;
				return $this->addDesktopBlockInfo($options);
			}
		}

		public function getValOneForSlotID($SlotID)
		{
			return $this->where("`slot_id` = '%s'",array($SlotID))->find();
		}
		public function getDesktopNameArrForSlotID($SlotID)
		{
			$desktopIdArr = $this->field('desktop_id')->where("`slot_id` = '%s'",array($SlotID))->select(false);
			return D('Desktop','Desktop')->getDesktopNameLists($desktopIdArr);
		}
		public function modifyQuickEntrySlot($slotID,$desktopSlotUpdateTime=false)
		{
			if ($desktopSlotUpdateTime) {
				$desktopIDSql = $this->field('desktop_id')->where("slot_id = %d",array($slotID))->select(false);
				D('Desktop','Desktop')->modifySourceVersion($desktopIDSql);
			}
		}
		public function getQuickEntrySlot($SlotID)
		{
			$desktopIDSql = $this->field('desktop_id')->where("slot_id = %d",array($SlotID))->select(false);
			return D('Desktop','Desktop')->getDesktopLists($desktopIDSql);
		}
	}