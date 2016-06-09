<?php
	namespace Home\Desktop;
	class DesktopQuickEntryGroupDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_quick_entry_group';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'quickInfo'  =>'quick_info', // 把表单中的mail映射到数据表的email字段
			'desktopId'=>'desktop_id',
			'groupName'=>'group_name',
		);
		public function addDesktopQuickEntryGroupArr($options)
		{
			foreach ($options as $key => $value) {
				if ($res = $this->create($value)) {
					$data[] =  $res;
				}
			}
			if (!empty($data)) {
				return $addId = $this->addAll($data);
			}
		}
		public function deleteDesktopQuickEntryGroup($desktopId)
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
		public function quickEntryGroupLists($desktopId)
		{
			 // $res = $this->field('`id`,`desktop_id` as desktopId,`x`,`y`,`style`,`is_show_indicator` as isShowIndicator,attache_id as')->where("`desktop_id`=%d",array($desktopId))->find();
			 $res['mList'] = $this->field('desktop_id,id',true)->where("`desktop_id`=%d",array($desktopId))->select();
			if ($res['mList']) {

				foreach ($res['mList'] as $key => $value) {
					$res['name'] = $value['groupName'];
					unset($res['mList'][$key]['groupName']);
					$res['mList'][$key]['extra'] = json_decode($value['quickInfo'],true);
					if (!$res['mList'][$key]['extra']) {
						$res['mList'][$key]['extra'] = array();
					}
					unset($res['mList'][$key]['quickInfo']);
					/*unset($res['mList'][$key]['desktopId']);
					unset($res['mList'][$key]['id']);*/
				}

			}else{
				return $res=array();
			}
			return $res;
		}
		public function createQuickEntryGroupConfig($desktopId=null ,$desktop =null,$error = false)
		{
			$desktopJson = array();
			if ($desktop ===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}

			if ($desktop) {
					$desktopQuickEntryGroup = $this->getArrForDesktopId($desktopId);
					$desktopJson = array();

					if (!empty($desktopQuickEntryGroup)) {
						foreach ($desktopQuickEntryGroup as $key => $value) {

							$quickInfo = json_decode($value['quickInfo'],true);
							unset($value['quickInfo']);
							if (!empty($quickInfo)) {
								foreach ($quickInfo as $k => $v) {
									$actionData = desktopWidgetCheckAction($v,'快捷入口组',$error);
									if (!empty($actionData['result'])) {
										if ($error) {
											return $actionData;
										}else{
											result($actionData['reason']);
										}
									}

									if ( !isset($v['index'])  ||  !isset($v['name'])  || !isset($v['normalPath']) || !isset($v['forcusPath'])  ) {
										if ($error) {
											return $result = array('reason'=>isset($v['index'])?'快捷入口组:id为'.$v['index'].'跳转参数出错':'快捷入口组:id跳转参数出错');
											// return false;
										}else{
											result(isset($v['index'])?'快捷入口组:id为'.$v['index'].'跳转参数出错':'快捷入口组:id跳转参数出错');
										}
									}

									$desktopJson['mList'][$key]['mData'][$k] = array(
										'id'=>$v['index'],
										'name'=>$v['name'],
										'focusedDrawable'=>$v['forcusPath'],
										'normalDrawable'=>$v['normalPath'],
									);
									if (isset($v['nextFocusLeftId'])  && $v['nextFocusLeftId'] != '') {
										$desktopJson['mList'][$key]['mData'][$k]['nextFocusLeftId'] = $v['nextFocusLeftId'];
									}
									if (isset($v['nextFocusRightId'])  && $v['nextFocusRightId'] != '') {
										$desktopJson['mList'][$key]['mData'][$k]['nextFocusRightId'] = $v['nextFocusRightId'];
									}
									if (isset($v['nextFocusUpId'])   && $v['nextFocusUpId'] != '') {
										$desktopJson['mList'][$key]['mData'][$k]['nextFocusUpId'] = $v['nextFocusUpId'];
									}
									if (isset($v['nextFocusDownId'])   && $v['nextFocusDownId'] != '') {
										$desktopJson['mList'][$key]['mData'][$k]['nextFocusDownId'] = $v['nextFocusDownId'];
									}
									// $desktopJson['mList'][$key]['mData'][$k] = array_merge($desktopJson['mList'][$key]['mData'][$k],$actionData);

									$desktopJson['mList'][$key]['mData'][$k]['action'] = $actionData;
								}
								$desktopJson['mList'][$key] = array_merge($value,$desktopJson['mList'][$key]);
							}else{
		 						if ($error) {
		 							return $result = array('reason'=>'快捷入口组数据信息不完整');
									// return false;
								}else{
									result('快捷入口组数据信息不完整');
								}
		 					}

							// $desktopJson['mList'][$key] =
						}


 						/*echo json_encode($desktopJson,JSON_UNESCAPED_UNICODE);
 						die;*/
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
		public function getArrForDesktopId($desktopId)
		{
			return $this->field('id,desktop_id,group_name',true)->where("`desktop_id`=%d",array($desktopId))->select();
		}
	}