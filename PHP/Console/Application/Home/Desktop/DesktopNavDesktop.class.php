<?php
	namespace Home\Desktop;
	class DesktopNavDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_nav';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'isShowIndicator' =>'is_show_indicator', // 把表单中name映射到数据表的username字段
			'navInfo'  =>'nav_info', // 把表单中的mail映射到数据表的email字段
			'desktopId'=>'desktop_id',
		);
		public function addDesktopNav($options)
		{
			$res = $this->create($options);
			if ($this->add()) {
				return true;
			}else{
				return false;
			}
		}
		public function deleteDesktopNav($desktopId)
		{
			if ($this->where("`desktop_id` IN (".$desktopId.")")->find()) {
				return $this->where("`desktop_id` IN (".$desktopId.")")->delete();
			}

		}
		public function navLists($desktopId)
		{
			$res = $this->field('style,x,y,interval,is_show_indicator as isShowIndicator,nav_info')->where("`desktop_id` IN (".$desktopId.")")->find();

			if (empty($res)) {
				$res = array();
			}else{
				if ($res) {
					$res['extraData']=json_decode($res['navInfo'],true);
					if (!$res['extraData']) {
						$res['extraData'] = array();
					}
					unset($res['navInfo']);
				}
			}
			return $res ;
		}
		public function getValOneForDesktopId($desktopId)
		{
			return $this->where("`desktop_id`=%d",array($desktopId))->find();
		}
		public function createNavigatorConfig($desktopId=null,$desktop =null,$error = false)
		{
			$desktopJson = array();
			if ($desktop ===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);

			}
			if ($desktop) {
					$desktopJson = $this->getValOneForDesktopId($desktopId);
				if (!empty($desktopJson)) {
					$navInfo = json_decode($desktopJson['navInfo'],true);
					$desktopJson['intervalSpace'] = $desktopJson['interval'];
					unset($desktopJson['interval']);
					unset($desktopJson['navInfo']);
					unset($desktopJson['desktopId']);
					unset($desktopJson['id']);
					if (isset($desktopJson['name'])) {
						unset( $desktopJson['name'] );
					}
					if ($navInfo) {
						$desktopJson['items'] = $navInfo;
						foreach ($navInfo as $key => $value) {
							if (!empty($value['normalPath']) && !empty($value['normalPath']) ) {
								$desktopJson['items'][$key] = array(
									'id'=>$key,
									'normalDrawable'=>$value['normalPath'],
									'focusedDrawable'=>$value['forcusPath'],
								);
								if (!empty($value['currentDrawable'])) {
									$desktopJson['items'][$key]['currentDrawable'] = $value['currentDrawable'];
								}
								if (isset($value['nextFocusLeftId']) && $value['nextFocusLeftId'] != '') {
									$desktopJson['items'][$key]['nextFocusLeftId'] = $value['nextFocusLeftId'];
								}
								if (isset($value['nextFocusRightId'])  && $value['nextFocusRightId'] != '') {
									$desktopJson['items'][$key]['nextFocusRightId'] = $value['nextFocusRightId'];
								}
								if (isset($value['nextFocusUpId'])  && $value['nextFocusUpId'] != '') {
									$desktopJson['items'][$key]['nextFocusUpId'] = $value['nextFocusUpId'];
								}
								if (isset($value['nextFocusDownId'])  && $value['nextFocusDownId'] != '') {
									$desktopJson['items'][$key]['nextFocusDownId'] = $value['nextFocusDownId'];
								}

							}else{
								if ($error) {
									return $result = array('reason'=>'导航图片信息出错');
									// return false;
								}else{
									result('导航图片信息出错');
								}

							}
						}
					}else{
						if ($error) {
							return $result = array('reason'=>'导航图片信息为空');
							// return false;
						}else{
							result('导航图片信息为空');
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