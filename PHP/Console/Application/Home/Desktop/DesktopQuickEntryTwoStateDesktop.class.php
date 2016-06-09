<?php
	namespace Home\Desktop;
	class DesktopQuickEntryTwoStateDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_quick_entry_two_state';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'isShowIndicator' =>'is_show_indicator', // 把表单中name映射到数据表的username字段
			'quickInfo'  =>'quick_info', // 把表单中的mail映射到数据表的email字段
			'desktopId'=>'desktop_id',
		);
		public function addDesktopQuickEntry($options)
		{
			$res = $this->create($options);
			return $addId = $this->add();
		}
		public function deleteDesktopQuickEntry($desktopId)
		{
			if ( $this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				return $this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}

		}
		public function deleteDesktopArrQuickEntry($desktopId)
		{
			if ( $this->where("`desktop_id` IN (".$desktopId.")")->find()) {
				return $this->where("`desktop_id` IN (".$desktopId.")")->delete();
			}

		}
		public function quickEntryLists($desktopId)
		{
			 // $res = $this->field('`id`,`desktop_id` as desktopId,`x`,`y`,`style`,`is_show_indicator` as isShowIndicator,attache_id as')->where("`desktop_id`=%d",array($desktopId))->find();
			 $res = $this->where("`desktop_id`=%d",array($desktopId))->find();
			if ($res) {
				$res['extraData']=json_decode($res['quickInfo'],true);
				if (!$res['extraData']) {
					$res['extraData'] = array();
				}
				unset($res['quickInfo']);
				unset($res['desktopId']);
				unset($res['id']);

			}else{
				return $res=array();
			}
			return $res;
		}
		public function getValOneForDesktopId($desktopId)
		{
			return $this->where("`desktop_id`=%d",array($desktopId))->find();
		}
		public function createQuickEntryTwoStateConfig($desktopId=null ,$desktop =null,$error = false)
		{
			$desktopJson = array();
			if ($desktop ===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
				$desktopJson = $this->getValOneForDesktopId($desktopId);

				if ($desktopJson) {
					$quickInfo = json_decode($desktopJson['quickInfo'],true);
					unset($desktopJson['quickInfo']);
					unset($desktopJson['id']);
					unset($desktopJson['desktopId']);
					if (isset($desktopJson['name'])) {
						unset($desktopJson['name']);
					}
					if (!empty($quickInfo)) {
						foreach ($quickInfo as $key => $value) {
							if ( isset($value['index'])&&!empty($value['activeDrawable'])&&!empty($value['normalDrawable'])&&!empty($value['focusedActiveDrawable'])&&!empty($value['focusedNormalDrawable'])&&!empty($value['eventType'])&&!empty($value['name'])&&isset($value['x'])&&isset($value['y'])) {

							$desktopJson['items'][$key] = array(
								'id'=>$value['index'],
								'name'=>$value['name'],
								'x'=>$value['x'],
								'y'=>$value['y'],
								'eventType'=>$value['eventType'],
								'activeDrawable'=>$value['activeDrawable'],
								'normalDrawable'=>$value['normalDrawable'],
								'focusedActiveDrawable'=>$value['focusedActiveDrawable'],
								'focusedNormalDrawable'=>$value['focusedNormalDrawable'],
							);
							if (isset($value['nextFocusLeftId'])   && $value['nextFocusLeftId'] != '') {
								$desktopJson['items'][$key]['nextFocusLeftId'] = $value['nextFocusLeftId'];
							}
							if (isset($value['nextFocusRightId'])   && $value['nextFocusRightId'] != '') {
								$desktopJson['items'][$key]['nextFocusRightId'] = $value['nextFocusRightId'];
							}
							if (isset($value['nextFocusUpId'])   && $value['nextFocusUpId'] != '') {
								$desktopJson['items'][$key]['nextFocusUpId'] = $value['nextFocusUpId'];
							}
							if (isset($value['nextFocusDownId'])   && $value['nextFocusDownId'] != '') {
								$desktopJson['items'][$key]['nextFocusDownId'] = $value['nextFocusDownId'];
							}
							$actionData = desktopWidgetCheckAction($value,$error,'两态快捷入口');
							if (!empty($actionData['reason'])) {
								if ($error) {
									return $actionData;
								}else{
									result($actionData['reason']);
								}
							}
							$desktopJson['items'][$key]['action'] = $actionData;
							/*switch ($value['type']) {
								case 'APP':
									if (isset($value['pkgName'])) {
										$desktopJson['items'][$key]['action']= array(
											'type'=>$value['type'],
											'appData'=>array(
												'pkgName'=>$value['pkgName']
											)
										);
									}else{
										if ($error) {
											return $result = array('reason'=>'两态快捷入口id为'.$value['index'].'action数据出错');
											// return false;
										}else{

											result('两态快捷入口id为'.$value['index'].'action数据出错');
										}
									}
									break;
								case 'ACTION':
									if (isset($value['action'])) {
										if (!is_array($value['extraData'])) {
											$value['extraData'] = array();
										}else{
											foreach ($value['extraData'] as $item) {
												if (!isset($item['key']) || !isset($item['value']) ) {
													$value['extraData'] = array();
													break;
												}
											}
										}
										$desktopJson['items'][$key]['action']= array(
											'type'=>$value['type'],
											'actionData'=>array(
												'action'=>$value['action']
											),
											'extraData'=>$value['extraData']
										);
									}else{
										if ($error) {
											return $result = array('reason'=>'两态快捷入口id为'.$value['index'].'action数据出错');
											// return false;
										}else{
											result('两态快捷入口id为'.$value['index'].'action数据出错');
										}
									}
									break;
								case 'COMPONENT':
									if (isset($value['clsName'])&&isset($value['component'])) {
										if (!is_array($value['extraData'])) {
											$value['extraData'] = array();
										}else{
											foreach ($value['extraData'] as $item) {
												if (!isset($item['key']) || !isset($item['value']) ) {
													$value['extraData'] = array();
													break;
												}
											}
										}
										$desktopJson['items'][$key]['action']= array(
											'type'=>$value['type'],
											'componentData'=>array(
												'pkgName'=>$value['component'],
												'clsName'=>$value['clsName'],
											),
											'extraData'=>$value['extraData']
										);


									}else{
										if ($error) {
											return $result = array('reason'=>'两态快捷入口id为'.$value['index'].'action数据出错');
											// return false;
										}else{
											result('两态快捷入口id为'.$value['index'].'action数据出错');
										}
									}
									break;
								case 'URI':

									if (isset($value['uri'])) {
										$desktopJson['items'][$key]['action']= array(
											'type'=>$value['type'],
											'uriData'=>array(
												'uri'=>$value['uri']
											)
										);
									}else{
										if ($error) {
											return $result = array('reason'=>'两态快捷入口id为'.$value['index'].'action数据出错');
											// return false;
										}else{
											result('两态快捷入口id为'.$value['index'].'action数据出错');
										}
									}
									break;

									default:
										if ($error) {
											return $result = array('reason'=>'两态快捷入口id为'.$value['index'].'action数据出错');
											// return false;
										}else{
											result('两态快捷入口id为'.$value['index'].'action数据出错');
										}
										break;
							}*/
						}else{
							if ($error) {
								return $result = array('reason'=>'两态快捷入口参数出错');
								// return false;
							}else{
								result('两态快捷入口参数出错');
							}
						}
						}
					}else{
						if ($error) {
							return $result = array('reason'=>'两态快捷入口action数据为空');
							// return false;
						}else{
							result('两态快捷入口action数据为空');
						}
					}
					// echo json_encode($desktopJson,JSON_UNESCAPED_UNICODE);
					return $desktopJson;
				}else{
					return false;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面不存在');
					// return false;
				}else{
					result('桌面不存在');
				}
			}
		}
	}