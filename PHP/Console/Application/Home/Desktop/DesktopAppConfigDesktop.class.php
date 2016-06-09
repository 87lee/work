<?php
	namespace Home\Desktop;
	class DesktopAppConfigDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_app_config';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopId'=>'desktop_id',
			"isCreateNavigation"=>'is_create_navigation',		//是否需要导航栏
			  "isCreateTimeWidget"=>'is_create_time_widget',		//是否需要时间控件
			  "isCreateWeatherWidget"=>'is_create_weather_widget',	//是否需要天气控件
			  "isCreateSlotAttachment"=>'is_create_slot_attachment',	//是否创建坑位附件
			  "isCreateLogoWidget"=>'is_create_logo_widget',		//是否创建Logo
			  "isCreateQuickEntry"=>'is_create_quick_entry',		//是否创建快捷入口
			  "isCreateQuickList"=>'is_create_quick_list',		//是否创建底部快捷栏
			  "isDisposeNavLeft"=>'is_dispose_nav_left',		//是否处理导航左边
			  "isDisposeNavRight"=>'is_dispose_nav_right',		//是否处理导航右边
			  "isCreateNavBottomLine"=>'is_create_nav_bottom_line',	//是否需要导航底线
			  "isCreateUsbWidget"=>'is_create_usb_widget',		//是否创建USB控件
			  "isCreateSnWidget"=>'is_create_sn_widget',		//是否创建SN控件
			  "isCreateNetworkWidget"=>'is_create_network_widget',	//是否创建网络控件
			  "slotCornerRadius"=>'slot_corner_radius',		//坑位是否是圆角，15表示圆角，0表示直角
			  "firstSlotId"=>'first_slot_id',			//第一个获取到焦点的控件的id
			  "isSkipYunOSCheck"=>'is_skip_yunos_check',		//坑位是否是圆角，15表示圆角，0表示直角
			  "isSkipYunOSReport"=>'is_skip_yunos_report',
			  "isAllowSlotEmpty"=>'is_allow_slot_empty',
			  "isAllowReplaceWallpaper"=>'is_allow_replace_wallpaper',
			  "focusTheta"=>'focus_theta',
			  "isCreateTimeWeather"=>'is_create_time_weather',
			  "focusStyle"=>'focus_style',
			  "focusImage"=>'focus_image',
			  "isBlurEnabled"=>'is_blur_enabled',
		);
		public function addDesktopAppConfig($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}
		public function deleteDesktopAppConfig($desktopId)
		{
			if ( $this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				return $this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}
		}
		public function deleteDesktopArrAppConfig($desktopId)
		{
			if ( $this->where("`desktop_id` IN (".$desktopId.")")->find()) {

				return $this->where("`desktop_id`  IN (".$desktopId.")")->delete();
			}
		}
		public function appConfigLists($desktopId)
		{
			 // $res = $this->field('`id`,`desktop_id` as desktopId,`x`,`y`,`style`,`is_show_indicator` as isShowIndicator,attache_id as')->where("`desktop_id`=%d",array($desktopId))->find();
			 $res = $this->where("`desktop_id`=%d",array($desktopId))->find();
			if (empty($res)) {
				$res=array();
			}else{
				unset($res['id']);
				unset($res['desktopId']);
			}
			return $res;
		}
		public function desktopAppConfigToTrue($desktopIdSql,$options)
		{
			$this->create($options);
			$res = $this->where("`desktop_id` IN (".$desktopIdSql.")")->save();
		}

		public function createAppConfig($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = array();
			if ($desktop ===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
				$desktopJson = $this->where("`desktop_id`=%d",array($desktopId))->find();
				if (!empty($desktopJson['isCreateTimeWeather'])) {
					$desktopJson['isCreateTimeWeatherWidget'] = $desktopJson['isCreateTimeWeather'];
					unset($desktopJson['isCreateTimeWeather']);
				}
				if (!empty($desktopJson)) {
					unset($desktopJson['id']);
					unset($desktopJson['desktopId']);
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