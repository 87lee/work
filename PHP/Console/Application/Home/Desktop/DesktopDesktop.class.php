<?php
	namespace Home\Desktop;
	class DesktopDesktop extends \Think\Model
	{
		protected $tableName = 'desktop';
		protected $connection = 'DB_DESKTOP';
		protected $user = array();
		protected $_map = array(
			'enlargeVal' =>'enlarge_val',
		);
		public function __construct()
	    	{
		        	parent::__construct();
		         	$this->user = session('is_login');
	    	}
		public function addDesktop($put)
		{
			if (!empty($put['name'])) {
				$time = time();
				if (!empty($put['enlargeVal'])) {
					$put['enlargeVal'] = floatval($put['enlargeVal']);
					$put['enlargeVal'] = $put['enlargeVal'] > 1 ? $put['enlargeVal'] :1;
				}else{
					$put['enlargeVal'] = 1;
				}

				$options = array(
					'name'=>$put['name'],
					'group_id'=>$put['groupId'],
					'update_time'=>$time,
					'create_time'=>$time,
					'animation'=>isset($put['animation'])?$put['animation']:"",
					'enlarge_val'=>$put['enlargeVal'],
					'layout_update_time'=>$time,
					'is_effective' =>'true',
					'user'=>$this->user['user'],
				);
				if (isset($put['image']) &&isset($put['desc'])) {
					$options['image'] = $put['image'];
					$options['desc'] = $put['desc'];
				}elseif(isset($put['image'])){
					$options['image'] = $put['image'];
					$options['desc'] = '';
				}elseif(isset($put['desc'])){
					$options['image'] = '';
					$options['desc'] = $put['desc'];
				}else{
					$options['image'] = '';
					$options['desc'] = '';
				}
				return $this->add($options);
			}else{
				result('param');
			}
		}
		public function deleteDesktop($id)
		{
			if ( $this->where("`id` IN ( ".$id.")")->find()) {
				return $this->where("`id` IN ( ".$id.")")->delete();
			}
		}
		public function modifyDesktop($put)
		{
			$res = $this->where('`id`=%d',array($put['id']))->find();
			if ($res) {
				if (!empty($put['enlargeVal'])) {

					$put['enlargeVal'] = floatval($put['enlargeVal']);
					$put['enlargeVal'] = $put['enlargeVal'] > 1 ? $put['enlargeVal'] :1;
				}else{
					$put['enlargeVal'] = 1;
				}

				$time = time();
				$options = array(
					'name'=>$put['name'],
					'animation'=>$put['animation'],
					'layout_update_time'=> $time,
					'user'=>$this->user['user'],
					'enlarge_val'=>$put['enlargeVal']
				);
				if (isset($put['image'])  && isset($put['desc'])) {
					$options['image'] = $put['image'];
					$options['desc'] = $put['desc'];
				}elseif(isset($put['image'])){
					$options['image'] = $put['image'];
					$options['desc'] = '';
				}elseif(isset($put['desc'])){
					$options['image'] = '';
					$options['desc'] = $put['desc'];
				}else{
					$options['image'] = '';
					$options['desc'] = '';
				}

				if ($put['layout_update_time'] == 'true') {
					$options['update_time'] = $time;
				}
				$this->where('`id`=%d',array($put['id']))->save($options);
				return $res['id'];
			}else{
				return false;
			}
		}
		public function modifyDesktopTimeForId($id)
		{

			if ($this->find($id)) {
				$time  = time();
				$options['update_time'] = $time;
				$options['layout_update_time'] = $time;
				$this->where('id = %d',array($id))->save($options);
			}
		}
		public function modifyOperationSlot($DesktopIdSql)
		{
			if ($this->where("`id` IN (".$DesktopIdSql.") and is_effective ='true'")->find()) {
				$options = array(
					'layout_update_time'=>time(),
					'user'=>$this->user['user'],
				);
				$this->where("`id` IN (".$DesktopIdSql.")  and is_effective ='true'")->save($options);
			}
		}
		public function modifySourceVersion($DesktopIdSql)
		{
			if ($this->where("`id` IN (".$DesktopIdSql.")  and is_effective ='true'")->find()) {
				$time = time();
				$options = array(
					'layout_update_time'=>$time,
					'update_time'=>$time,
					'user'=>$this->user['user'],
				);
				$this->where("`id` IN (".$DesktopIdSql.")  and is_effective ='true'")->save($options);
			}
		}
		public function getDesktopLists($DesktopIdSql)
		{
			return $desktopLists = $this->field("`id` as desktopID,`name` as desktopName")->where("`id` IN (".$DesktopIdSql.")  and is_effective ='true'")->select();
			// $desktopLists = $this->field("`id` as desktopID,`name` as desktopName")->where("`id` IN (".$DesktopIdSql.") ")->select();
		}
		public function getDesktopNameLists($DesktopIdSql)
		{
			return $this->field("`id`,`name`")->where("`id` IN (".$DesktopIdSql.")  and is_effective ='true'")->select();
		}
		public function getOperationDesktopLists($DesktopIdSql)
		{
			return $desktopLists = $this->where("`id` IN (".$DesktopIdSql.")  and is_effective ='true'")->select();
		}
		/**
		 * 40、添加桌面
		 * post /desktop/addDesktop
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addFullDesktop($put,$isModifyDesktop = false)
		{
			//检查信息
			if ($isModifyDesktop){
				$desktopInfo = $put['desktopInfo'];
				$put = $put['put'];
			}else{
				//检查桌面数据
				$res1 = $this->checkDesktopData($put);
				$put = $res1['put'];
				$desktopInfo = $res1['desktopInfo'];
				unset($res1);
			}
			if (!empty($put['name']) && ($put['animation'] == 'enlarge' || $put['animation'] == 'rotate' || $put['animation'] == 'enlarge_rotate' || $put['animation'] == '')) {
				//是否为修改桌面
				if (!$isModifyDesktop) {
					$res = $this->where("`name`='%s'",array($put['name']))->find();
					$desktopMap = D('DesktopMap','Desktop')->getOneForDesktop2($put['name']);
					if ($res) {
						result('该桌面已存在');
					}elseif ($desktopMap) {
						result('该桌面已映射');
					}
				}
				//添加或修改桌面
				//是否为修改桌面
				if ($isModifyDesktop) {

					$desktopId = $this->modifyDesktop($put);

				}else{
					if (!empty($put['groupId'])) {
						if (!D('DesktopGroup','Desktop')->getValForId($put['groupId'])) {
							result('桌面组不存在');
						}
					}else{
						result('param');
					}
					$desktopId = $this->addDesktop($put);

				}

				//添加桌面可下载快捷入口坑位
				if (!empty($put['quickEntrySlot'])) {
					foreach ( $put['quickEntrySlot'] as  $key => $value ) {
						$put['quickEntrySlot'][$key]['desktopId'] = $desktopId ;
					}
					D('DesktopDownloadQuickEntry','Desktop')->addDesktopDownloadQuickEntryArr($put['quickEntrySlot']);
				}
				//添加消息（走马灯）
				if (!empty($put['messageConfig'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'y'=>$put['messageConfig']['y'],
						'x'=>$put['messageConfig']['x'],
						'width'=>$put['messageConfig']['width'],
						'font_size'=>$put['messageConfig']['fontSize'],
					);
					D('DesktopMessage','Desktop')->addDesktopMessage($arr);
				}
				//添加时间天气
				if (!empty($put['timeWeather'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'y'=>$put['timeWeather']['y'],
						'x'=>$put['timeWeather']['x'],
						'style'=>$put['timeWeather']['style'],
					);
					D('DesktopTimeWeather','Desktop')->addDesktopTimeWeather($arr);
				}
				//添加天气
				if (!empty($put['weather'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'x'=>$put['weather']["x"],
						'y'=>$put['weather']["y"],
						'style'=>$put['weather']["style"],
						'isShowIndicator'=>$put['weather']["isShowIndicator"],
						'isShowCity'=>$put['weather']["isShowCity"],
						'isShowTemperature'=>$put['weather']["isShowTemperature"],
						'isShowDesc'=>$put['weather']["isShowDesc"],
						'isShowIcon'=>$put['weather']["isShowIcon"]
					);
					D('DesktopWeather','Desktop')->addDesktopWeather($arr);
				}
				//添加风格
				if (!empty($put['style'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'json_text'=>$put['style']["jsonText"],
					);
					D('DesktopStyle','Desktop')->addDesktopStyle($arr);
				}
				//添加桌面SN
				if (!empty($put['sn'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'x'=>$put['sn']["x"],
						'y'=>$put['sn']["y"],
						'style'=>$put['sn']["style"],
						'isShowIndicator'=>$put['sn']["isShowIndicator"],
						'prefixInfo'=>isset($put['sn']["prefixInfo"])?$put['sn']["prefixInfo"]:'SN : ',
						'systemProperty'=>isset($put['sn']["systemProperty"])?$put['sn']["systemProperty"]:'',
						'ipmacroProperty'=>isset($put['sn']["ipmacroProperty"])?$put['sn']["ipmacroProperty"]:'SN',
					);
					D('DesktopSn','Desktop')->addDesktopSn($arr);
				}
				//添加时间
				if (!empty($put['timebar'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'x'=>$put['timebar']["x"],
						'y'=>$put['timebar']["y"],
						'style'=>$put['timebar']["style"],
						'is_show_indicator'=>$put['timebar']["isShowIndicator"],
						'time_format'=>$put['timebar']["timeFormat"],
					);
					D('DesktopTimebar','Desktop')->addDesktopTimebar($arr);
				}
				//添加桌面附件
				if (!empty($put['attachment'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'x'=>$put['attachment']["x"],
						'y'=>$put['attachment']["y"],
						'style'=>$put['attachment']["style"],
						'isShowIndicator'=>$put['attachment']["isShowIndicator"],
						'interval'=>$put['attachment']["interval"],
						'attacheInfo'=>$put['attachment']["attacheInfo"],
						'name'=>isset($put['attachment']["name"])?$put['attachment']["name"]:'',
					);
					D('DesktopAttachment','Desktop')->addDesktopAttachment($arr);
				}

				//添加快捷入口
				if (!empty($put['quickEntry'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'name'=>isset($put['quickEntry']["name"])?$put['quickEntry']["name"]:'',
						'x'=>$put['quickEntry']["x"],
						'y'=>$put['quickEntry']["y"],
						'style'=>$put['quickEntry']["style"],
						'isShowIndicator'=>$put['quickEntry']["isShowIndicator"],
						'interval'=>$put['quickEntry']["interval"],
						'quickInfo'=>$put['quickEntry']["quickInfo"]
					);
					D('DesktopQuickEntry','Desktop')->addDesktopQuickEntry($arr);
				}

				//添加三态快捷入口
				if (!empty($put['quickEntryThreeState'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'name'=>isset($put['quickEntryThreeState']["name"])?$put['quickEntryThreeState']["name"]:'',
						'x'=>$put['quickEntryThreeState']["x"],
						'y'=>$put['quickEntryThreeState']["y"],
						'style'=>$put['quickEntryThreeState']["style"],
						'isShowIndicator'=>$put['quickEntryThreeState']["isShowIndicator"],
						'quickInfo'=>$put['quickEntryThreeState']["quickInfo"]
					);
					D('DesktopQuickEntryThreeState','Desktop')->addDesktopQuickEntry($arr);
				}
				//添加快捷入口组
				if (!empty($put['quickEntryGroup'])) {
					$arr = array();
					foreach ($put['quickEntryGroup']['mList'] as $key => $value) {
						$arr[] = array(
							'desktopId'=>$desktopId,
							'name'=>isset($value["name"])?$value["name"]:'',
							'x'=>$value["x"],
							'y'=>$value["y"],
							'layout'=>$value["layout"],
							'direction'=>$value["direction"],
							'distance'=>$value["distance"],
							'quickInfo'=>$value["quickInfo"],
							'group_name'=>isset($put['quickEntryGroup']["name"])?$put['quickEntryGroup']["name"]:'',
						);
					}
					D('DesktopQuickEntryGroup','Desktop')->addDesktopQuickEntryGroupArr($arr);
				}
				//添加两态快捷入口
				if (!empty($put['quickEntryTwoState'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						'name'=>isset($put['quickEntryTwoState']["name"])?$put['quickEntryTwoState']["name"]:'',
						'x'=>$put['quickEntryTwoState']["x"],
						'y'=>$put['quickEntryTwoState']["y"],
						'style'=>$put['quickEntryTwoState']["style"],
						'isShowIndicator'=>$put['quickEntryTwoState']["isShowIndicator"],
						'quickInfo'=>$put['quickEntryTwoState']["quickInfo"]
					);
					D('DesktopQuickEntryTwoState','Desktop')->addDesktopQuickEntry($arr);
				}
				//添加桌面基础设置
				if (!empty($put['appConfig'])) {
					$arr = array(
						'desktopId'=>$desktopId,
						"isCreateNavigation"=>$put['appConfig']['isCreateNavigation'],		//是否需要导航栏
						  "isCreateTimeWidget"=>$put['appConfig']['isCreateTimeWidget'],		//是否需要时间控件
						  "isCreateWeatherWidget"=>$put['appConfig']['isCreateWeatherWidget'],	//是否需要天气控件
						  "isCreateSlotAttachment"=>$put['appConfig']['isCreateSlotAttachment'],	//是否创建坑位附件
						  "isCreateLogoWidget"=>$put['appConfig']['isCreateLogoWidget'],		//是否创建Logo
						  "isCreateQuickEntry"=>$put['appConfig']['isCreateQuickEntry'],		//是否创建快捷入口
						  "isCreateQuickList"=>$put['appConfig']['isCreateQuickList'],		//是否创建底部快捷栏
						  "isDisposeNavLeft"=>$put['appConfig']['isDisposeNavLeft'],		//是否处理导航左边
						  "isDisposeNavRight"=>$put['appConfig']['isDisposeNavRight'],		//是否处理导航右边
						  "isCreateNavBottomLine"=>$put['appConfig']['isCreateNavBottomLine'],	//是否需要导航底线
						  "isCreateUsbWidget"=>$put['appConfig']['isCreateUsbWidget'],		//是否创建USB控件
						  "isCreateSnWidget"=>$put['appConfig']['isCreateSnWidget'],		//是否创建SN控件
						  "isCreateNetworkWidget"=>$put['appConfig']['isCreateNetworkWidget'],	//是否创建网络控件
						  "slotCornerRadius"=>$put['appConfig']['slotCornerRadius'],		//坑位是否是圆角，15表示圆角，0表示直角
						  "firstSlotId"=>$put['appConfig']['firstSlotId'],  //第一个获取到焦点的控件的id
						  "isSkipYunOSCheck"=>$put['appConfig']['isSkipYunOSCheck'], //是否跳过阿里审核
						  "isSkipYunOSReport"=>$put['appConfig']['isSkipYunOSReport'],			//是否跳过爆光
						  "isAllowSlotEmpty"=>$put['appConfig']['isAllowSlotEmpty'],
						  "isAllowReplaceWallpaper"=>$put['appConfig']['isAllowReplaceWallpaper'], //是否允许替换壁纸
						  "focusTheta"=>$put['appConfig']['focusTheta'],
						  "focusImage"=>$put['appConfig']['focusImage'],
						  "focusStyle"=>$put['appConfig']['focusStyle'],
						  "isCreateTimeWeather"=>$put['appConfig']['isCreateTimeWeather'],
						  "isBlurEnabled"=>$put['appConfig']['isBlurEnabled'],
					);
					D('DesktopAppConfig','Desktop')->addDesktopAppConfig($arr);
				}
				//添加桌面底部快捷栏配置
				if (!empty($put['quickList'])) {
					D('DesktopQuickList','Desktop')->addDesktopQuickList($desktopId,$put['quickList']);
				}
				//添加桌面快捷键
				if (!empty($put['shortCutConfig'])) {

					D('DesktopShortCuts','Desktop')->addDesktopShortCuts($desktopId,$put['shortCutConfig']);
				}
				//添加桌面LOGO
				if (!empty($put['logo'])) {
					$arr = array();
					//判断是否为修改桌面
					if ($isModifyDesktop) {
						$desktopLogoId = D('DesktopLogo','Desktop')->modifyDesktopLogo($desktopId,$put['logo']['x'],$put['logo']['y'],$put['logo']['style'],$put['logo']['isShowIndicator'],$put['logo']['intervalTime']);
						if (!empty($put['logo']['logoLists'])) {
							foreach ($put['logo']['logoLists'] as $value) {
								$arr[] = array(
									'path' => $value,
									'desktop_logo_id' =>$desktopLogoId
								);
							}
							if (D('DesktopLogoInfo','Desktop')->getValOneFordDesktopLogoId($desktopLogoId)) {
								D('DesktopLogoInfo','Desktop')->modifyDesktopLogoInfo($desktopLogoId,$arr);
							}else{
								D('DesktopLogoInfo','Desktop')->addDesktopLogoInfo($arr);
							}
						}else{
							//删除桌面Logo信息
							// D('DesktopLogoInfo','Desktop')->deleteDesktopLogoInfo($desktopLogoId);
						}
					}else{
						$desktopLogoId = D('DesktopLogo','Desktop')->addDesktopLogo($desktopId,$put['logo']['x'],$put['logo']['y'],$put['logo']['style'],$put['logo']['isShowIndicator'],$put['logo']['intervalTime']);
						if (!empty($put['logo']['logoLists'])) {
							foreach ($put['logo']['logoLists'] as $value) {
								$arr[] = array(
									'path' => $value,
									'desktop_logo_id' =>$desktopLogoId
								);
							}
							D('DesktopLogoInfo','Desktop')->addDesktopLogoInfo($arr);
						}
					}
				}else{
					//判断是否为修改桌面
					if ($isModifyDesktop) {
						$desktopLogo = D('DesktopLogo','Desktop')->getValOneForDesktopId($desktopId);
						if ($desktopLogo) {
							//删除桌面Logo信息
							D('DesktopLogoInfo','Desktop')->deleteDesktopLogoInfo($desktopLogo['id']);
							//删除桌面Logo
							D('DesktopLogo','Desktop')->deleteDesktopLogo($desktopLogo['desktop_id']);
						}
					}
				}
				//添加桌面导航
				if (!empty($put['nav'])) {
					$arr = array(
						'desktopId' => $desktopId,
						'style'=>$put['nav']['style'],
						'isShowIndicator'=>$put['nav']['isShowIndicator'],
						'navInfo'=>$put['nav']['navInfo'],
						'x'=>$put['nav']['x'],
						'y'=>$put['nav']['y'],
						'name'=>isset($put['nav']['name'])?$put['nav']['name']:'',
						'interval'=>$put['nav']['interval'],
					);
					D('DesktopNav','Desktop')->addDesktopNav($arr);
				}
				//添加桌面屏
				if (!empty($put['screens'])) {
					foreach ($put['screens'] as $key => $value) {
						$options = array(
							'desktopId'=>$desktopId,
							'slotGroupId'=>empty($value['slotGroupId'])?'':$value['slotGroupId'],
							'itemStyle'=>empty($value['itemStyle'])?'':$value['itemStyle'],
							'index'=>empty($value['index'])?($key+1):$value['index']
						);
						$desktopScreensId = D('DesktopScreens','Desktop')->addDesktopScreens($options);
						foreach ($value['blocks'] as $k => $v) {
							$options = array(
								'screenId'=>$desktopScreensId,
								'w'=>$v['w'],
								'h'=>$v['h'],
								'yw'=>$v['yw'],
								'yh'=>$v['yh'],
								'bg'=>$v['bg'],
								'x'=>$v['x'],
								'y'=>$v['y'],
								'slotId'=>$v['slotId'],
								'updateTime'=>!empty($v['updateTime'])?$v['updateTime']:time(),
								'nextFocusLeftId' => isset($v['nextFocusLeftId'])?$v['nextFocusLeftId']:'',
								'nextFocusRightId' => isset($v['nextFocusRightId'])?$v['nextFocusRightId']:'',
								'nextFocusUpId' =>  isset($v['nextFocusUpId'])?$v['nextFocusUpId']:'',
								'nextFocusDownId' =>  isset($v['nextFocusDownId'])?$v['nextFocusDownId']:'',
							);
							$desktopBlocksId = D('DesktopBlocks','Desktop')->addDesktopBlocks($options);
							if ($desktopInfo[$v['slotId']]) {
								$options = array(
									'blockId'=>$desktopBlocksId,
									'title'=>!empty($v['title'])?$v['title']:'',
									'isEditable'=>!empty($v['isEditable'])?$v['isEditable']:'false',
									'disconnectEnable' =>$v['disconnectEnable'],
									'dataSource'=>!empty($v['dataSource'])?$v['dataSource']:'yunos',
									'layout'=>!empty($v['layout'])?$v['layout']:'IMAGE',
									'actionInfo'=>!empty($v['actionInfo'])?$v['actionInfo']:'{}',
									'appInfo'=>!empty($v['appInfo'])?$v['appInfo']:'{}',
									'operation'=>$v['operation'],
									'tag'=>!empty($v['tag'])?$v['tag']:'{}',
									'operationId'=>isset($v['operationId'])?$v['operationId']:'0',
								);
								$desktopBlockInfoId = D('DesktopBlockInfo','Desktop')->addDesktopBlockInfo($options);
								if ($v['operation']=='false' && ($v['dataSource'] == 'linkin' || $v['dataSource'] == 'linkinOnly') ) {
									if ($v['layout'] == 'VIDEO') {
										D('DesktopBlockVideo','Desktop')->addDesktopBlockVideoArr($desktopBlockInfoId,$v['videos']);
									}
									$options = array(
										'pic'=> $v['pic'],
										'block_id' =>$desktopBlockInfoId
									);
									D('DesktopBlockPic','Desktop')->addDesktopBlockPic($options);
								}
							}
						}
					}
				}
				//添加导航与屏
				/*if (!empty($put['nav'])) {
					$arr = array(
						'desktopId' => $desktopId,
						'style'=>$put['nav']['style'],
						'isShowIndicator'=>$put['nav']['isShowIndicator'],
						'navInfo'=>$put['nav']['navInfo'],
						'x'=>$put['nav']['x'],
						'y'=>$put['nav']['y'],
						'interval'=>$put['nav']['interval']
					);
					D('DesktopNav','Desktop')->addDesktopNav($arr);
					// var_dump($put['screens']);
					// var_dump($desktopInfo);
					if (!empty($put['screens'])) {
						foreach ($put['screens'] as $key => $value) {
							$options = array(
								'desktopId'=>$desktopId,
								'index'=>empty($put['screens'][$key]['index'])?($key+1):$put['screens'][$key]['index']
							);
							$desktopScreensId = D('DesktopScreens','Desktop')->addDesktopScreens($options);
							foreach ($value['blocks'] as $k => $v) {
								$options = array(
									'screenId'=>$desktopScreensId,
									'w'=>$v['w'],
									'h'=>$v['h'],
									'yw'=>$v['yw'],
									'yh'=>$v['yh'],
									'bg'=>$v['bg'],
									'x'=>$v['x'],
									'y'=>$v['y'],
									'slotId'=>$v['slotId'],
									'updateTime'=>time()
								);
								$desktopBlocksId = D('DesktopBlocks','Desktop')->addDesktopBlocks($options);
								if ($desktopInfo[$key][$k]) {
									$options = array(
										'blockId'=>$desktopBlocksId,
										'title'=>!empty($v['title'])?$v['title']:'',
										'isEditable'=>!empty($v['isEditable'])?$v['isEditable']:'',
										'dataSource'=>!empty($v['dataSource'])?$v['dataSource']:'',
										'layout'=>!empty($v['layout'])?$v['layout']:'',
										'actionInfo'=>!empty($v['actionInfo'])?$v['actionInfo']:'{}',
										'appInfo'=>!empty($v['appInfo'])?$v['appInfo']:'{}',
										'operation'=>$v['operation'],
										'operationId'=>isset($v['operationId'])?$v['operationId']:'0',
									);
									$desktopBlockInfoId = D('DesktopBlockInfo','Desktop')->addDesktopBlockInfo($options);
									if ($v['operation']=='false' &&$v['dataSource'] == 'linkin') {
										if ($v['layout'] == 'VIDEO') {
											D('DesktopBlockVideo','Desktop')->addDesktopBlockVideoArr($desktopBlockInfoId,$v['videos']);
										}
										$options = array(
											'pic'=> $v['pic'],
											'block_id' =>$desktopBlockInfoId
										);
										D('DesktopBlockPic','Desktop')->addDesktopBlockPic($options);
									}
								}
							}
						}
					}
				}elseif (empty($put['nav']) && !empty($put['screens'])) {
					foreach ($put['screens'] as $key => $value) {
						$options = array(
							'desktopId'=>$desktopId,
							'index'=>empty($put['screens'][$key]['index'])?($key+1):$put['screens'][$key]['index']
						);
						$desktopScreensId = D('DesktopScreens','Desktop')->addDesktopScreens($options);
						foreach ($value['blocks'] as $k => $v) {
							$options = array(
								'screen_id'=>$desktopScreensId,
								'w'=>$v['w'],
								'h'=>$v['h'],
								'yw'=>$v['yw'],
								'yh'=>$v['yh'],
								'bg'=>$v['bg'],
								'x'=>$v['x'],
								'y'=>$v['y'],
								'slotId'=>$v['slotId'],
								'updateTime'=>time()
							);
							$desktopBlocksId = D('DesktopBlocks','Desktop')->addDesktopBlocks($options);
							if ($desktopInfo[$key][$k]) {
								$options = array(
									'blockId'=>$desktopBlocksId,
									'title'=>!empty($v['title'])?$v['title']:'',
									'isEditable'=>!empty($v['isEditable'])?$v['isEditable']:'',
									'dataSource'=>!empty($v['dataSource'])?$v['dataSource']:'',
									'layout'=>!empty($v['layout'])?$v['layout']:'',
									'actionInfo'=>!empty($v['actionInfo'])?$v['actionInfo']:'{}',
									'appInfo'=>!empty($v['appInfo'])?$v['appInfo']:'{}',
									'operation'=>$v['operation'],
									'operationId'=>isset($v['operationId'])?$v['operationId']:'0',
								);
								$desktopBlockInfoId = D('DesktopBlockInfo','Desktop')->addDesktopBlockInfo($options);
								if ($v['operation']=='false' &&$v['dataSource'] == 'linkin') {
									if ($v['layout'] == 'VIDEO') {
										D('DesktopBlockVideo','Desktop')->addDesktopBlockVideoArr($desktopBlockInfoId,$v['videos']);
									}
									$options = array(
										'pic'=> $v['pic'],
										'block_id' =>$desktopBlockInfoId
									);
									D('DesktopBlockPic','Desktop')->addDesktopBlockPic($options);
								}
							}
						}
					}
				}*/
				$res = array(
					'id'=>$desktopId
				);
				return $res;
			}else{
				result('param');
			}
		}

		/**
		 * 41、删除桌面
		 * get /desktop/deleteDesktop?id=1
		 * return {"result":"ok/fail","reason":"xxx"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteFullDesktop($id = '',$isModifyDesktop = false)
		{
			//是否为修改桌面
			if (!$isModifyDesktop) {
				$id = I('get.id');
			}
			if (!empty($id)) {
				$desktop = $this->find($id);
				if ($desktop) {
					//删除桌面
					if (!$isModifyDesktop) {
						$this->deleteDesktop($id);
						return true;
					}

					/*$desktopScreensSql = D('DesktopScreens','Desktop')->getSqlIdForDesktopId($desktop['id']);
					$desktopBlocksSql = D('DesktopBlocks','Desktop')->getSqlIdForDesktopScreensIdSql($desktopScreensSql);
					$desktopLogoSql = D('DesktopLogo','Desktop')->getSqlIdForDesktopId($desktop['id']);
					$desktopBlocksInfoSql= D('DesktopBlockInfo','Desktop')->getSqlIdArrForBlackId($desktopBlocksSql);
					//删除VIDEO
					D('DesktopBlockVideo','Desktop')->deleteDesktopBlockVideo($desktopBlocksInfoSql);
					//删除PIC
					D('DesktopBlockPic','Desktop')->deleteDesktopBlockPic($desktopBlocksInfoSql);
					D('DesktopBlockInfo','Desktop')->deleteDesktopBlockInfo($desktopBlocksSql);
					//删除桌面块
					D('DesktopBlocks','Desktop')->deleteDesktopBlocks($desktopScreensSql);*/
					//删除桌面屏
					D('DesktopScreens','Desktop')->deleteDesktopScreens($id);
					//删除桌面导航
					D('DesktopNav','Desktop')->deleteDesktopNav($id);
					//删除是否为修改桌面Logo
					/*if (!$isModifyDesktop) {
						//删除桌面Logo信息
						D('DesktopLogoInfo','Desktop')->deleteDesktopLogoInfo($desktopLogoSql);
						//删除桌面Logo
						D('DesktopLogo','Desktop')->deleteDesktopLogo($id);
					}*/
					//删除桌面Logo
					D('DesktopLogo','Desktop')->deleteDesktopLogo($id);
					//删除桌面天气
					D('DesktopWeather','Desktop')->deleteDesktopWeather($id);
					//删除桌面时间天气
					D('DesktopTimeWeather','Desktop')->deleteDesktopTimeWeather($id);
					//删除桌面时间
					D('DesktopTimebar','Desktop')->deleteDesktopTimebar($id);
					//删除桌面SN
					D('DesktopSn','Desktop')->deleteDesktopSn($id);
					//删除桌面附件栏
					D('DesktopAttachment','Desktop')->deleteDesktopAttachment($id);
					//删除桌面快捷入口
					D('DesktopQuickEntry','Desktop')->deleteDesktopQuickEntry($id);
					//删除桌面快捷入口组
					D('DesktopQuickEntryGroup','Desktop')->deleteDesktopQuickEntryGroup($id);
					//删除桌面三态快捷入口getValOneForDesktopId
					D('DesktopQuickEntryThreeState','Desktop')->deleteDesktopQuickEntry($id);
					//删除桌面两态快捷入口
					D('DesktopQuickEntryTwoState','Desktop')->deleteDesktopQuickEntry($id);
					//删除桌面app配置
					D('DesktopAppConfig','Desktop')->deleteDesktopAppConfig($id);
					//删除桌面底部快捷栏配置
					D('DesktopQuickList','Desktop')->deleteDesktopQuickList($id);
					//删除桌面快捷键
					D('DesktopShortCuts','Desktop')->deleteDesktopShortCuts($id);
					//删除桌面风格
					D('DesktopStyle','Desktop')->deleteDesktopStyle($id);
					//删除桌面消息（走马灯）
					D('DesktopMessage','Desktop')->deleteDesktopMessage($id);
					//删除桌面可下载快捷入口坑位
					D('DesktopDownloadQuickEntry','Desktop')->deleteDesktopDownloadQuickEntryForArrId($id);

				}else{
					result('桌面不存在');
				}
			}else{
				result('param');
			}
		}
		/**
		 * 41、删除桌面（备份）
		 * get /desktop/deleteDesktop?id=1
		 * return {"result":"ok/fail","reason":"xxx"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		/*public function deleteFullDesktop($id = '',$isModifyDesktop = false)
		{
			//是否为修改桌面
			if (!$isModifyDesktop) {
				$id = I('get.id');
			}
			if (!empty($id)) {
				$desktop = $this->find($id);
				if ($desktop) {
					$desktopScreensSql = D('DesktopScreens','Desktop')->getSqlIdForDesktopId($desktop['id']);
					$desktopBlocksSql = D('DesktopBlocks','Desktop')->getSqlIdForDesktopScreensIdSql($desktopScreensSql);
					$desktopLogoSql = D('DesktopLogo','Desktop')->getSqlIdForDesktopId($desktop['id']);
					//是否为修改桌面
					if (D('DesktopScreens','Desktop')->getValOneForDesktopId($desktop['id'])) {
						if (D('DesktopBlocks','Desktop')->getValOneForScreensId($desktopScreensSql)) {
							if (D('DesktopBlockInfo','Desktop')->getValOneForBlackId($desktopBlocksSql)) {
								$desktopBlocksInfoSql= D('DesktopBlockInfo','Desktop')->getSqlIdArrForBlackId($desktopBlocksSql);
								//删除VIDEO
								D('DesktopBlockVideo','Desktop')->deleteDesktopBlockVideo($desktopBlocksInfoSql);
								//删除PIC
								D('DesktopBlockPic','Desktop')->deleteDesktopBlockPic($desktopBlocksInfoSql);
								D('DesktopBlockInfo','Desktop')->deleteDesktopBlockInfo($desktopBlocksSql);
							}
							//删除桌面块
							D('DesktopBlocks','Desktop')->deleteDesktopBlocks($desktopScreensSql);
						}
						//删除桌面屏
						D('DesktopScreens','Desktop')->deleteDesktopScreens($id);
					}
					//删除桌面导航
					D('DesktopNav','Desktop')->deleteDesktopNav($id);
					//删除是否为修改桌面Logo
					if (!$isModifyDesktop) {
						if (D('DesktopLogo','Desktop')->getValOneForDesktopId($desktop['id'])) {
							//删除桌面Logo信息
							D('DesktopLogoInfo','Desktop')->deleteDesktopLogoInfo($desktopLogoSql);
							//删除桌面Logo
							D('DesktopLogo','Desktop')->deleteDesktopLogo($id);
						}
					}
					//删除桌面天气
					if (D('DesktopWeather','Desktop')->weatherLists($desktop['id'])) {
						//删除桌面天气
						D('DesktopWeather','Desktop')->deleteDesktopWeather($id);
					}
					//删除桌面时间
					if (D('DesktopTimebar','Desktop')->timebarLists($desktop['id'])) {
						//删除桌面时间
						D('DesktopTimebar','Desktop')->deleteDesktopTimebar($id);
					}
					//删除桌面SN
					if (D('DesktopSn','Desktop')->snLists($desktop['id'])) {
						//删除桌面时间
						D('DesktopSn','Desktop')->deleteDesktopSn($id);
					}
					//删除桌面附件栏
					if (D('DesktopAttachment','Desktop')->getValOneForDesktopId($desktop['id'])) {
						//删除桌面附件栏
						D('DesktopAttachment','Desktop')->deleteDesktopAttachment($id);
					}
					//删除桌面快捷入口
					if (D('DesktopQuickEntry','Desktop')->getValOneForDesktopId($desktop['id'])) {
						//删除桌面快捷入口
						D('DesktopQuickEntry','Desktop')->deleteDesktopQuickEntry($id);
					}
					//删除桌面三态快捷入口getValOneForDesktopId
					if (D('DesktopQuickEntryThreeState','Desktop')->getValOneForDesktopId($desktop['id'])) {
						// 删除桌面三态快捷入口
						D('DesktopQuickEntryThreeState','Desktop')->deleteDesktopQuickEntry($id);
					}
					//删除桌面两态快捷入口
					if (D('DesktopQuickEntryTwoState','Desktop')->getValOneForDesktopId($desktop['id'])) {
						// 删除桌面两态快捷入口
						D('DesktopQuickEntryTwoState','Desktop')->deleteDesktopQuickEntry($id);
					}
					//删除桌面app配置
					if (D('DesktopAppConfig','Desktop')->appConfigLists($desktop['id'])) {
						//删除桌面app配置
						D('DesktopAppConfig','Desktop')->deleteDesktopAppConfig($id);
					}
					//删除桌面底部快捷栏配置
					if (D('DesktopQuickList','Desktop')->desktopQuickLists($desktop['id'])) {
						//删除桌面底部快捷栏配置
						D('DesktopQuickList','Desktop')->deleteDesktopQuickList($id);
					}
					//删除桌面快捷键
					if (D('DesktopShortCuts','Desktop')->getValOneForDesktopId($desktop['id'])) {
						//删除桌面快捷键
						D('DesktopShortCuts','Desktop')->deleteDesktopShortCuts($id);
					}
					//删除桌面风格
					if (D('DesktopStyle','Desktop')->styleLists($desktop['id'])) {
						//删除桌面风格
						D('DesktopStyle','Desktop')->deleteDesktopStyle($id);
					}
					//删除桌面
					if (!$isModifyDesktop) {
						$this->deleteDesktop($id);
						return true;
					}
				}else{
					result('桌面不存在');
				}
			}else{
				result('param');
			}
		}*/
		/**
		 * 41、批量删除桌面
		 * get /desktop/deleteDesktop?id=1
		 * return {"result":"ok/fail","reason":"xxx"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteFullArrDesktop($put)
		{
			if (empty($put) || !is_array($put)) {
				result('param');
			}
			$sqlStr = implode(',', $put);
			$desktopArr = $this->getArrForIdSql($sqlStr);
			if (count($put) != count($desktopArr) ) {
				result('回收站不存在该桌面');
			}
			/*$desktopScreensSql = D('DesktopScreens','Desktop')->getSqlIdForDesktopId($sqlStr);
			$desktopBlocksSql = D('DesktopBlocks','Desktop')->getSqlIdForDesktopScreensIdSql($desktopScreensSql);
			$desktopBlocksInfoSql= D('DesktopBlockInfo','Desktop')->getSqlIdArrForBlackId($desktopBlocksSql);
			$desktopLogoSql = D('DesktopLogo','Desktop')->getSqlIdForDesktopIdArr($sqlStr);
			//删除VIDEO
			D('DesktopBlockVideo','Desktop')->deleteDesktopBlockVideo($desktopBlocksInfoSql);
			//删除PIC
			D('DesktopBlockPic','Desktop')->deleteDesktopBlockPic($desktopBlocksInfoSql);
			D('DesktopBlockInfo','Desktop')->deleteDesktopBlockInfo($desktopBlocksSql);
			//删除桌面块
			D('DesktopBlocks','Desktop')->deleteDesktopBlocks($desktopScreensSql);
			//删除桌面屏
			D('DesktopScreens','Desktop')->deleteDesktopScreens($sqlStr);
			//删除桌面导航
			D('DesktopNav','Desktop')->deleteDesktopNav($sqlStr);

			//删除桌面Logo信息
			D('DesktopLogoInfo','Desktop')->deleteDesktopLogoInfo($desktopLogoSql);
			//删除桌面Logo
			D('DesktopLogo','Desktop')->deleteDesktopLogo($sqlStr);
			//删除桌面天气
			D('DesktopWeather','Desktop')->deleteDesktopArrWeather($sqlStr);
			//删除桌面时间
			D('DesktopTimebar','Desktop')->deleteDesktopArrTimebar($sqlStr);
			//删除桌面SN
			D('DesktopSn','Desktop')->deleteDesktopArrSn($sqlStr);
			//删除桌面附件栏
			D('DesktopAttachment','Desktop')->deleteDesktopArrAttachment($sqlStr);
			//删除桌面快捷入口
			D('DesktopQuickEntry','Desktop')->deleteDesktopArrQuickEntry($sqlStr);
			// 删除桌面三态快捷入口
			D('DesktopQuickEntryThreeState','Desktop')->deleteDesktopArrQuickEntry($sqlStr);
			// 删除桌面两态快捷入口
			D('DesktopQuickEntryTwoState','Desktop')->deleteDesktopArrQuickEntry($sqlStr);
			//删除桌面app配置
			D('DesktopAppConfig','Desktop')->deleteDesktopArrAppConfig($sqlStr);
			//删除桌面底部快捷栏配置
			D('DesktopQuickList','Desktop')->deleteDesktopArrQuickList($sqlStr);
			//删除桌面快捷键
			D('DesktopShortCuts','Desktop')->deleteDesktopArrShortCuts($sqlStr);
			//删除桌面风格
			D('DesktopStyle','Desktop')->deleteDesktopArrStyle($sqlStr);
			//删除桌面可下载快捷入口坑位
			D('DesktopDownloadQuickEntry','Desktop')->deleteDesktopDownloadQuickEntryForArrId($sqlStr);*/
			//删除桌面
			$this->deleteDesktop($sqlStr);
			return true;
		}
		/**
		 * 42、桌面列表带参数
		 * get /desktop/desktopLists?id=1
		 * /desktop/desktopLists?page=xxxx&pageSize=xxx
		 * /desktop/desktopLists?name=aaa
		 * /desktop/desktopLists?name=aaa&page=xxxx&pageSize=xxx
		 * @return
		 */
		public function fullDesktopLists($get)
		{
			// $field = 'id,name,animation,image,user,IF(`desc`= " ", "",`desc`) as `desc`,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,IF(`layout_update_time`= "0", FROM_UNIXTIME(`update_time`,"%Y-%m-%d %H:%i:%s"),FROM_UNIXTIME(`layout_update_time`,"%Y-%m-%d %H:%i:%s") ) as `layoutUpdateTme`,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime';
			if (!empty($get['id'])) {
				$res['extra'] = $this->field('id,name,animation,enlarge_val,image,user,IF(`desc`= " ", "",`desc`) as `desc`,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,IF(`layout_update_time`= "0", FROM_UNIXTIME(`update_time`,"%Y-%m-%d %H:%i:%s"),FROM_UNIXTIME(`layout_update_time`,"%Y-%m-%d %H:%i:%s") ) as `layoutUpdateTme`,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime')->find($get['id']);
				if (empty($res['extra']['image'])) {
					unset($res['extra']['image']);
				}
				if (empty($res['extra']['desc'])) {
					$res['extra']['desc'] = '';
				}else{
					if ($res['extra']['desc'] ==' ' ) {
						$res['extra']['desc'] = '';
					}
				}
				if ($res['extra']) {
					$desktopNav = D('DesktopNav','Desktop')->navLists($res['extra']['id']);
					$desktopLogo = D('DesktopLogo','Desktop')->logoLists($res['extra']['id']);
					$desktopTimebar = D('DesktopTimebar','Desktop')->timebarLists($res['extra']['id']);
					$desktopSn = D('DesktopSn','Desktop')->snLists($res['extra']['id']);
					$desktopWeather = D('DesktopWeather','Desktop')->weatherLists($res['extra']['id']);
					$desktopAttachment = D('DesktopAttachment','Desktop')->attachmentLists($res['extra']['id']);
					$desktopQuickEntry = D('DesktopQuickEntry','Desktop')->quickEntryLists($res['extra']['id']);

					$desktopQuickEntryGroup = D('DesktopQuickEntryGroup','Desktop')->quickEntryGroupLists($res['extra']['id']);

					$desktopQuickEntryThreeState = D('DesktopQuickEntryThreeState','Desktop')->quickEntryLists($res['extra']['id']);
					$desktopQuickEntryTwoState = D('DesktopQuickEntryTwoState','Desktop')->quickEntryLists($res['extra']['id']);
					$desktopAppConfig = D('DesktopAppConfig','Desktop')->appConfigLists($res['extra']['id']);
					$desktopQuickList = D('DesktopQuickList','Desktop')->desktopQuickLists($res['extra']['id']);
					$desktopShortCuts = D('DesktopShortCuts','Desktop')->desktopShortCutsLists($res['extra']['id']);
					$desktopStyle = D('DesktopStyle','Desktop')->styleLists($res['extra']['id']);
					$desktopDownloadQuickEntry = D('DesktopDownloadQuickEntry','Desktop')->downloadQuickEntryStyleLists($res['extra']['id']);
					$desktopTimeWeather = D('DesktopTimeWeather','Desktop')->timeWeatherLists($res['extra']['id']);

					$DesktopMessage = D('DesktopMessage','Desktop')->messageLists($res['extra']['id']);

					if (!empty($desktopTimeWeather)) {
						$res['extra']['timeWeather'] = $desktopTimeWeather;
					}
					if (!empty($desktopDownloadQuickEntry)) {
						$res['extra']['quickEntrySlot'] = $desktopDownloadQuickEntry;
					}
					if (!empty($desktopStyle)) {
						$res['extra']['style'] = $desktopStyle;
					}
					if (!empty($DesktopMessage)) {
						$res['extra']['messageConfig'] = $DesktopMessage;
					}
					if (!empty($desktopNav)) {
						$res['extra']['nav'] = $desktopNav;
					}
					if (!empty($desktopLogo)) {
						$res['extra']['logo'] = $desktopLogo;
					}
					if (!empty($desktopTimebar)) {
						$res['extra']['timebar'] = $desktopTimebar;
					}
					if (!empty($desktopWeather)) {
						$res['extra']['weather'] = $desktopWeather;
					}
					if (!empty($desktopAttachment)) {
						$res['extra']['attachment'] = $desktopAttachment;
					}
					if (!empty($desktopQuickEntry)) {
						$res['extra']['quickEntry'] = $desktopQuickEntry;
					}
					if (!empty($desktopQuickEntryGroup)) {
						$res['extra']['quickEntryGroup'] = $desktopQuickEntryGroup;
					}
					if (!empty($desktopQuickEntryThreeState)) {
						$res['extra']['quickEntryThreeState'] = $desktopQuickEntryThreeState;
					}
					if (!empty($desktopQuickEntryTwoState)) {
						$res['extra']['quickEntryTwoState'] = $desktopQuickEntryTwoState;
					}
					if (!empty($desktopAppConfig)) {
						$res['extra']['appConfig'] = $desktopAppConfig;
					}
					if ($desktopAppConfig['isCreateQuickList'] !='false') {
						if (!empty($desktopQuickList)) {
							$res['extra']['quickList'] = $desktopQuickList;
						}else{
							$res['extra']['quickList'] = array();
						}
					}
					if (!empty($desktopSn)) {
						$res['extra']['sn'] = $desktopSn;
					}
					if (!empty($desktopShortCuts)) {
						$res['extra']['shortCutConfig'] = $desktopShortCuts;
					}
					$res['extra']['screens'] = D('DesktopScreens','Desktop')->screensLists($res['extra']['id']);
				}
			}elseif ( !empty($get['groupId']) ) {
				$field = 'id,name,user,IF(`desc`= " ", "",`desc`) as `desc`,IF(`layout_update_time`= "0", FROM_UNIXTIME(`update_time`,"%Y-%m-%d %H:%i:%s"),FROM_UNIXTIME(`layout_update_time`,"%Y-%m-%d %H:%i:%s") ) as `layoutUpdateTme`,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime';

				$where = "group_id =".$get['groupId'].  "  and is_effective ='true'";

				if (!empty($get['name'])) {
					$where = " is_effective ='true' and ( `name` like '%".$get['name']."%'  or `desc` like '%".$get['name']."%' )";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field($field)->limit($get['page'],$get['pageSize'])->where($where)->order("layout_update_time DESC")->select();
				}else{
					// $res['extra'] = D('Desktop','Desktop')->field('id,name,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime')->select();
					$res['extra'] = $this->field('id,name,user,IF(`desc`= " ", "",`desc`) as `desc`')->where($where)->order("layout_update_time DESC")->select();
				}
				$res['count'] = $this->where($where)->count();
			}else{
				$res['extra'] = $this->field('id,name,user,IF(`desc`= " ", "",`desc`) as `desc`')->where("is_effective ='true'")->order("layout_update_time DESC")->select();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		public function getAlldesktopLists($get)
		{
			$field = 'id,name,user,IF(`desc`= " ", "",`desc`) as `desc`,`group_id` as `group`,IF(`layout_update_time`= "0", FROM_UNIXTIME(`update_time`,"%Y-%m-%d %H:%i:%s"),FROM_UNIXTIME(`layout_update_time`,"%Y-%m-%d %H:%i:%s") ) as `layoutUpdateTme`,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime';

			$where = " is_effective ='true' ";
			if (!empty($get['name'])) {
				$where .= "  and ( `name` like '%".$get['name']."%'  or `desc` like '%".$get['name']."%' )";
			}
			if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$res['extra'] = $this->field($field)->limit($get['page'],$get['pageSize'])->where($where)->order("layout_update_time DESC")->select();
			}else{
				// $res['extra'] = D('Desktop','Desktop')->field('id,name,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime')->select();
				$res['extra'] = $this->field($field)->where($where)->order("layout_update_time DESC")->select();
			}
			$res['count'] = $this->where($where)->count();

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				$desktopGroup = D('DesktopGroup','Desktop')->getAll();
				if (!empty($desktopGroup)) {
					foreach ($desktopGroup as  $value) {
						$desktopGroupArr[$value['id']] = $value['name'];
					}
				}
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['group'] =!empty($desktopGroupArr[$value['group']])?$desktopGroupArr[$value['group']]:$value['group'];
					}
				}
			}
			return $res;
		}
		public function getGroupIdForName($name)
		{
			return $this->field("group_id")->where("name LIKE '%".$name."%'")->select(false);
		}
		/**
		 * 43、修改桌面
		 * post /desktop/modifyDesktop
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyFullDesktop()
		{
			$put = I('put.');
			if (isset($put['id'])&&isset($put['name'])&&($put['animation'] == 'enlarge' || $put['animation'] == 'rotate' || $put['animation'] == 'enlarge_rotate' || $put['animation'] == '')) {
				//检查信息
				$res = $this->where("`name`='%s' and `id`!=%d",array($put['name'],$put['id']))->find();
				$desktopMap = D('DesktopMap','Desktop')->getOneForDesktop2($put['name']);
				if ($res) {
					result('该桌面已存在');
				}elseif ($desktopMap) {
					result('该桌面已映射');
				}
				$res = $this->find($put['id']);
				if (!$res) {
					result('桌面不存在');
				}
				//检查桌面数据
				$put1 = $this->checkDesktopData($put);
				if ($put['image'] != $res['image']) {
					$put1['put']['layout_update_time'] = true;
				}
				//检查附件有没有变化
				$desktopNav = D('DesktopNav','Desktop')->navLists($res['id']);
				$desktopLogo = D('DesktopLogo','Desktop')->logoLists($res['id']);
				$desktopTimebar = D('DesktopTimebar','Desktop')->timebarLists($res['id']);
				$desktopSn = D('DesktopSn','Desktop')->snLists($res['id']);
				$desktopWeather = D('DesktopWeather','Desktop')->weatherLists($res['id']);
				$desktopAttachment = D('DesktopAttachment','Desktop')->attachmentLists($res['id']);
				$desktopQuickEntry = D('DesktopQuickEntry','Desktop')->quickEntryLists($res['id']);

				$desktopQuickEntryGroup = D('DesktopQuickEntryGroup','Desktop')->quickEntryGroupLists($res['id']);

				$desktopQuickEntryThreeState = D('DesktopQuickEntryThreeState','Desktop')->quickEntryLists($res['id']);
				$desktopQuickEntryTwoState = D('DesktopQuickEntryTwoState','Desktop')->quickEntryLists($res['id']);
				$desktopAppConfig = D('DesktopAppConfig','Desktop')->appConfigLists($res['id']);
				$desktopQuickList = D('DesktopQuickList','Desktop')->desktopQuickLists($res['id']);
				$desktopShortCuts = D('DesktopShortCuts','Desktop')->desktopShortCutsLists($res['id']);
				$desktopScreens = D('DesktopScreens','Desktop')->screensLists($res['id']);
				$desktopStyle = D('DesktopStyle','Desktop')->styleLists($res['id']);
				$desktopDownloadQuickEntry = D('DesktopDownloadQuickEntry','Desktop')->downloadQuickEntryListsForDesktopID($res['id']);
				$desktopTimeWeather = D('DesktopTimeWeather','Desktop')->timeWeatherLists($res['id']);
				$DesktopMessage = D('DesktopMessage','Desktop')->messageLists($res['id']);

				//检查桌面消息（走马灯）有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($DesktopMessage)&&!empty($put1['put']['messageConfig'])) {
						foreach ($DesktopMessage as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($DesktopMessage[$key])&&!empty($put1['put']['messageConfig'][$key])) {
								if ($DesktopMessage[$key] !=$put1['put']['messageConfig'][$key]) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
							}elseif (!empty($DesktopMessage[$key])&&empty($put1['put']['messageConfig'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($DesktopMessage[$key])&&!empty($put1['put']['messageConfig'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (( !empty($DesktopMessage)&&empty($put1['put']['messageConfig']) ) || ( empty($DesktopMessage)&&!empty($put1['put']['messageConfig']) ))  {
						$put1['put']['layout_update_time'] = true;
					}
				}

				//检查桌面快捷入口组有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopQuickEntryGroup)&&!empty($put1['put']['quickEntryGroup'])) {

						foreach ($desktopQuickEntryGroup as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopQuickEntryGroup[$key])&&!empty($put1['put']['quickEntryGroup'][$key])) {
								if ($key == 'mList') {
									if (count($desktopQuickEntryGroup[$key]) !=count($put1['put']['quickEntryGroup'][$key])) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
									foreach ($desktopQuickEntryGroup[$key] as $k => $v) {
										if (!empty($put1['put']['layout_update_time'])) {
											break;
										}
										foreach ($v as $l => $item) {
											if (!empty($put1['put']['layout_update_time'])) {
												break;
											}
											if ($l == 'extra' ) {

												if (count($desktopQuickEntryGroup[$key][$k][$l]) !=count($put1['put']['quickEntryGroup'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
												foreach ($item as $a => $i) {



													if (!empty($put1['put']['layout_update_time'])) {
														break;
													}
													foreach ($i as $b => $c) {
														if (!empty($desktopQuickEntryGroup[$key][$k][$l][$a][$b])&&!empty($put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b])) {
															if ($b == 'extraData') {
																if (count($desktopQuickEntryGroup[$key][$k][$l][$a][$b]) !=count($put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b])) {
																	$put1['put']['layout_update_time'] = true;
																	break;
																}
																foreach ($c as $d => $f) {
																	if (!empty($put1['put']['layout_update_time'])) {
																		break;
																	}
																	foreach ($f as $e => $g) {
																		if (!empty($desktopQuickEntryGroup[$key][$k][$l][$a][$b][$d][$g])&&!empty($put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b][$d][$g])) {

																			if ($desktopQuickEntryGroup[$key][$k][$l][$a][$b][$d][$g] != $put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b][$d][$g]) {
																				$put1['put']['layout_update_time'] = true;
																				break;
																			}

																		}elseif (empty($desktopQuickEntryGroup[$key][$k][$l][$a][$b][$d][$g])&&!empty($put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b][$d][$g])) {
																			$put1['put']['layout_update_time'] = true;
																			break;
																		}elseif (!empty($desktopQuickEntryGroup[$key][$k][$l][$a][$b][$d][$g])&&empty($put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b][$d][$g])) {
																			$put1['put']['layout_update_time'] = true;
																			break;
																		}
																	}
																}
															}else{
																if ($desktopQuickEntryGroup[$key][$k][$l][$a][$b] != $put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b]) {
																	$put1['put']['layout_update_time'] = true;
																	break;
																}
															}
														}elseif (empty($desktopQuickEntryGroup[$key][$k][$l][$a][$b])&&!empty($put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b])) {
															$put1['put']['layout_update_time'] = true;
															break;
														}elseif (!empty($desktopQuickEntryGroup[$key][$k][$l][$a][$b])&&empty($put1['put']['quickEntryGroup'][$key][$k][$l][$a][$b])) {
															$put1['put']['layout_update_time'] = true;
															break;
														}
													}

												}


											}elseif (!empty($desktopQuickEntryGroup[$key][$k][$l])&&!empty($put1['put']['quickEntryGroup'][$key][$k][$l])) {
												if ($desktopQuickEntryGroup[$key][$k][$l] !=$put1['put']['quickEntryGroup'][$key][$k][$l]) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopQuickEntryGroup[$key][$k][$l])&&empty($put1['put']['quickEntryGroup'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}elseif (empty($desktopQuickEntryGroup[$key][$k][$l])&&!empty($put1['put']['quickEntryGroup'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}
									}
								}else{
									if ($desktopQuickEntryGroup[$key] !=$put1['put']['quickEntryGroup'][$key]) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
								}
							}elseif (!empty($desktopQuickEntryGroup[$key])&&empty($put1['put']['quickEntryGroup'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopQuickEntryGroup[$key])&&!empty($put1['put']['quickEntryGroup'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopQuickEntryGroup)&&empty($put1['put']['quickEntryGroup'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopQuickEntryGroup)&&!empty($put1['put']['quickEntryGroup'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}

				//检查桌面时间天气有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopTimeWeather)&&!empty($put1['put']['timeWeather'])) {
						foreach ($desktopTimeWeather as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopTimeWeather[$key])&&!empty($put1['put']['timeWeather'][$key])) {
								if ($desktopTimeWeather[$key] !=$put1['put']['timeWeather'][$key]) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
							}elseif (!empty($desktopTimeWeather[$key])&&empty($put1['put']['timeWeather'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopTimeWeather[$key])&&!empty($put1['put']['timeWeather'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopTimeWeather)&&empty($put1['put']['timeWeather'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopTimeWeather)&&!empty($put1['put']['timeWeather'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}

				//检查桌面可下载快捷入口坑位有没有修改
				if (!empty($desktopDownloadQuickEntry)&&!empty($put1['put']['quickEntrySlot'])) {
					foreach ($desktopDownloadQuickEntry as $key => $value) {
						if (!empty($put1['put']['layout_update_time'])) {
							break;
						}
						foreach ($value as $k => $v) {
							if (!empty($desktopDownloadQuickEntry[$key][$k])&&!empty($put1['put']['quickEntrySlot'][$key][$k])) {
								if ($desktopDownloadQuickEntry[$key][$k] != $put1['put']['quickEntrySlot'][$key][$k]) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
							}elseif (!empty($desktopDownloadQuickEntry[$key][$k])&&empty($put1['put']['quickEntrySlot'][$key][$k])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopDownloadQuickEntry[$key][$k])&&!empty($put1['put']['quickEntrySlot'][$key][$k])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}

					}
				}elseif (!empty($desktopDownloadQuickEntry)&&empty($put1['put']['quickEntrySlot'])) {
					$put1['put']['layout_update_time'] = true;
				}elseif (empty($desktopDownloadQuickEntry)&&!empty($put1['put']['quickEntrySlot'])) {
					$put1['put']['layout_update_time'] = true;
				}
				//检查桌面风格有没有修改
				if (!empty($desktopStyle)&&!empty($put1['put']['style'])) {
					foreach ($desktopStyle as $key => $value) {
						if (!empty($put1['put']['layout_update_time'])) {
							break;
						}
						if (!empty($desktopStyle[$key])&&!empty($put1['put']['style'][$key])) {
							if ($desktopStyle[$key] !=$put1['put']['style'][$key]) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}elseif (!empty($desktopStyle[$key])&&empty($put1['put']['style'][$key])) {
							$put1['put']['layout_update_time'] = true;
							break;
						}elseif (empty($desktopStyle[$key])&&!empty($put1['put']['style'][$key])) {
							$put1['put']['layout_update_time'] = true;
							break;
						}
					}
				}elseif (!empty($desktopStyle)&&empty($put1['put']['style'])) {
					$put1['put']['layout_update_time'] = true;
				}elseif (empty($desktopStyle)&&!empty($put1['put']['style'])) {
					$put1['put']['layout_update_time'] = true;
				}
				//检查桌面导航有没有修改

				if (!empty($desktopNav)&&!empty($put1['put']['nav'])) {
					foreach ($desktopNav as $key => $value) {
						if (!empty($put1['put']['layout_update_time'])) {
							break;
						}
						if (!empty($desktopNav[$key])&&!empty($put1['put']['nav'][$key])) {
							if ($key == 'extraData') {
								if (count($desktopNav[$key]) !=count($put1['put']['nav'][$key])) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
								foreach ($desktopNav[$key] as $k => $v) {
									if (!empty($put1['put']['layout_update_time'])) {
										break;
									}
									foreach ($v as $l => $item) {
										if (!empty($desktopNav[$key][$k][$l])&&!empty($put1['put']['nav'][$key][$k][$l])) {
											if ($desktopNav[$key][$k][$l] !=$put1['put']['nav'][$key][$k][$l]) {
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}elseif ( (!empty($desktopNav[$key][$k][$l])&&empty($put1['put']['nav'][$key][$k][$l])) || (empty($desktopNav[$key][$k][$l])&&!empty($put1['put']['nav'][$key][$k][$l]))){
											$put1['put']['layout_update_time'] = true;
											break;
										}
									}
								}
							}else{
								if ($desktopNav[$key] !=$put1['put']['nav'][$key]) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
							}
						}elseif (!empty($desktopNav[$key])&&empty($put1['put']['nav'][$key])) {
							$put1['put']['layout_update_time'] = true;
							break;
						}elseif (empty($desktopNav[$key])&&!empty($put1['put']['nav'][$key])) {
							$put1['put']['layout_update_time'] = true;
							break;
						}
					}
				}elseif (!empty($desktopNav)&&empty($put1['put']['nav'])) {
					$put1['put']['layout_update_time'] = true;
				}elseif (empty($desktopNav)&&!empty($put1['put']['nav'])) {
					$put1['put']['layout_update_time'] = true;
				}

				//检查桌面LOGO有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopLogo)&&!empty($put1['put']['logo'])) {
						foreach ($desktopLogo as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopLogo[$key])&&!empty($put1['put']['logo'][$key])) {
								if ($key == 'logoLists') {
									foreach ($desktopLogo[$key] as $k => $v) {
										if (!empty($put1['put']['layout_update_time'])) {
											break;
										}
										if (count($desktopLogo[$key][$k])!= count($put1['put']['logo'][$key][$k])) {
											$put1['put']['layout_update_time'] = true;
											break;
										}
										if (!empty($desktopLogo[$key][$k])&&!empty($put1['put']['logo'][$key][$k])) {
											if ($desktopLogo[$key][$k] !=$put1['put']['logo'][$key][$k]) {
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}else{
											$put1['put']['layout_update_time'] = true;
											break;
										}
									}
								}else{
									if ($desktopLogo[$key] !=$put1['put']['logo'][$key]) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
								}
							}elseif (!empty($desktopLogo[$key])&&empty($put1['put']['logo'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopLogo[$key])&&!empty($put1['put']['logo'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopLogo)&&empty($put1['put']['logo'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopLogo)&&!empty($put1['put']['logo'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面时间有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopTimebar)&&!empty($put1['put']['timebar'])) {
						foreach ($desktopTimebar as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopTimebar[$key])&&!empty($put1['put']['timebar'][$key])) {
								if ($desktopTimebar[$key] !=$put1['put']['timebar'][$key]) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
							}elseif (!empty($desktopTimebar[$key])&&empty($put1['put']['timebar'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopTimebar[$key])&&!empty($put1['put']['timebar'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopTimebar)&&empty($put1['put']['timebar'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopTimebar)&&!empty($put1['put']['timebar'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面天气有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopWeather)&&!empty($put1['put']['weather'])) {
						foreach ($desktopWeather as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopWeather[$key])&&!empty($put1['put']['weather'][$key])) {
								if ($desktopWeather[$key] !=$put1['put']['weather'][$key]) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
							}elseif (!empty($desktopWeather[$key])&&empty($put1['put']['weather'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopWeather[$key])&&!empty($put1['put']['weather'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopWeather)&&empty($put1['put']['weather'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopWeather)&&!empty($put1['put']['weather'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面附件栏有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopAttachment)&&!empty($put1['put']['attachment'])) {
						foreach ($desktopAttachment as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopAttachment[$key])&&!empty($put1['put']['attachment'][$key])) {
								if ($key == 'extraData') {
									if (count($desktopAttachment[$key]) !=count($put1['put']['attachment'][$key])) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
									foreach ($desktopAttachment[$key] as $k => $v) {
										if (!empty($put1['put']['layout_update_time'])) {
											break;
										}
										foreach ($v as $l => $item) {
											if (!empty($put1['put']['layout_update_time'])) {
												break;
											}
											if ($l == 'extraData' ) {
												if (count($desktopAttachment[$key][$k][$l]) !=count($put1['put']['attachment'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
												if (!empty($desktopAttachment[$key][$k][$l])&&!empty($put1['put']['attachment'][$key][$k][$l])) {
													foreach ($item as $a => $i) {
														if (!empty($put1['put']['layout_update_time'])) {
															break;
														}
														if (($desktopAttachment[$key][$k][$l][$a]['key'] !=$put1['put']['attachment'][$key][$k][$l][$a]['key']) || ($desktopAttachment[$key][$k][$l][$a]['value'] !=$put1['put']['attachment'][$key][$k][$l][$a]['value'])  || ($desktopAttachment[$key][$k][$l][$a]['type'] !=$put1['put']['attachment'][$key][$k][$l][$a]['type']) ) {
															$put1['put']['layout_update_time'] = true;
															break;
														}
													}
												}elseif (empty($desktopAttachment[$key][$k][$l])&&!empty($put1['put']['attachment'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}elseif (!empty($desktopAttachment[$key][$k][$l])&&empty($put1['put']['attachment'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopAttachment[$key][$k][$l])&&!empty($put1['put']['attachment'][$key][$k][$l])) {
												if ($desktopAttachment[$key][$k][$l] !=$put1['put']['attachment'][$key][$k][$l]) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopAttachment[$key][$k][$l])&&empty($put1['put']['attachment'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}elseif (empty($desktopAttachment[$key][$k][$l])&&!empty($put1['put']['attachment'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}
									}
								}else{
									if ($desktopAttachment[$key] !=$put1['put']['attachment'][$key]) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
								}
							}elseif (!empty($desktopAttachment[$key])&&empty($put1['put']['attachment'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopAttachment[$key])&&!empty($put1['put']['attachment'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopAttachment)&&empty($put1['put']['attachment'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopAttachment)&&!empty($put1['put']['attachment'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面快捷入口有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopQuickEntry)&&!empty($put1['put']['quickEntry'])) {
						foreach ($desktopQuickEntry as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopQuickEntry[$key])&&!empty($put1['put']['quickEntry'][$key])) {
								if ($key == 'extraData') {
									if (count($desktopQuickEntry[$key]) !=count($put1['put']['quickEntry'][$key])) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
									foreach ($desktopQuickEntry[$key] as $k => $v) {
										if (!empty($put1['put']['layout_update_time'])) {
											break;
										}
										foreach ($v as $l => $item) {
											if (!empty($put1['put']['layout_update_time'])) {
												break;
											}
											if ($l == 'extraData' ) {
												if (count($desktopQuickEntry[$key][$k][$l]) !=count($put1['put']['quickEntry'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
												if (!empty($desktopQuickEntry[$key][$k][$l])&&!empty($put1['put']['quickEntry'][$key][$k][$l])) {
													foreach ($item as $a => $i) {
														if (!empty($put1['put']['layout_update_time'])) {
															break;
														}
														if (($desktopQuickEntry[$key][$k][$l][$a]['key'] !=$put1['put']['quickEntry'][$key][$k][$l][$a]['key']) || ($desktopQuickEntry[$key][$k][$l][$a]['value'] !=$put1['put']['quickEntry'][$key][$k][$l][$a]['value'])  || ($desktopQuickEntry[$key][$k][$l][$a]['type'] !=$put1['put']['quickEntry'][$key][$k][$l][$a]['type']) ) {
															$put1['put']['layout_update_time'] = true;
															break;
														}
													}
												}elseif (empty($desktopQuickEntry[$key][$k][$l])&&!empty($put1['put']['quickEntry'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}elseif (!empty($desktopQuickEntry[$key][$k][$l])&&empty($put1['put']['quickEntry'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopQuickEntry[$key][$k][$l])&&!empty($put1['put']['quickEntry'][$key][$k][$l])) {
												if ($desktopQuickEntry[$key][$k][$l] !=$put1['put']['quickEntry'][$key][$k][$l]) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopQuickEntry[$key][$k][$l])&&empty($put1['put']['quickEntry'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}elseif (empty($desktopQuickEntry[$key][$k][$l])&&!empty($put1['put']['quickEntry'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}
									}
								}else{
									if ($desktopQuickEntry[$key] !=$put1['put']['quickEntry'][$key]) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
								}
							}elseif (!empty($desktopQuickEntry[$key])&&empty($put1['put']['quickEntry'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopQuickEntry[$key])&&!empty($put1['put']['quickEntry'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopQuickEntry)&&empty($put1['put']['quickEntry'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopQuickEntry)&&!empty($put1['put']['quickEntry'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面三态快捷入口有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopQuickEntryThreeState)&&!empty($put1['put']['quickEntryThreeState'])) {
						foreach ($desktopQuickEntryThreeState as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopQuickEntryThreeState[$key])&&!empty($put1['put']['quickEntryThreeState'][$key])) {
								if ($key == 'extraData') {
									if (count($desktopQuickEntry[$key]) !=count($put1['put']['quickEntry'][$key])) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
									foreach ($desktopQuickEntryThreeState[$key] as $k => $v) {
										if (!empty($put1['put']['layout_update_time'])) {
											break;
										}
										foreach ($v as $l => $item) {
											if (!empty($put1['put']['layout_update_time'])) {
												break;
											}
											if ($l == 'extraData' ) {
												if (count($desktopQuickEntry[$key][$k][$l]) !=count($put1['put']['quickEntry'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
												if (!empty($desktopQuickEntryThreeState[$key][$k][$l])&&!empty($put1['put']['quickEntryThreeState'][$key][$k][$l])) {
													foreach ($item as $a => $i) {
														if (!empty($put1['put']['layout_update_time'])) {
															break;
														}
														if (($desktopQuickEntryThreeState[$key][$k][$l][$a]['key'] !=$put1['put']['quickEntryThreeState'][$key][$k][$l][$a]['key']) || ($desktopQuickEntryThreeState[$key][$k][$l][$a]['value'] !=$put1['put']['quickEntryThreeState'][$key][$k][$l][$a]['value'])  || ($desktopQuickEntryThreeState[$key][$k][$l][$a]['type'] !=$put1['put']['quickEntryThreeState'][$key][$k][$l][$a]['type']) ) {
															$put1['put']['layout_update_time'] = true;
															break;
														}
													}
												}elseif (empty($desktopQuickEntryThreeState[$key][$k][$l])&&!empty($put1['put']['quickEntryThreeState'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}elseif (!empty($desktopQuickEntryThreeState[$key][$k][$l])&&empty($put1['put']['quickEntryThreeState'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopQuickEntryThreeState[$key][$k][$l])&&!empty($put1['put']['quickEntryThreeState'][$key][$k][$l])) {
												if ($desktopQuickEntryThreeState[$key][$k][$l] !=$put1['put']['quickEntryThreeState'][$key][$k][$l]) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopQuickEntryThreeState[$key][$k][$l])&&empty($put1['put']['quickEntryThreeState'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}elseif (empty($desktopQuickEntryThreeState[$key][$k][$l])&&!empty($put1['put']['quickEntryThreeState'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}
									}
								}else{
									if ($desktopQuickEntryThreeState[$key] !=$put1['put']['quickEntryThreeState'][$key]) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
								}
							}elseif (!empty($desktopQuickEntryThreeState[$key])&&empty($put1['put']['quickEntryThreeState'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopQuickEntryThreeState[$key])&&!empty($put1['put']['quickEntryThreeState'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopQuickEntryThreeState)&&empty($put1['put']['quickEntryThreeState'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopQuickEntryThreeState)&&!empty($put1['put']['quickEntryThreeState'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面两态快捷入口有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopQuickEntryTwoState)&&!empty($put1['put']['quickEntryTwoState'])) {
						foreach ($desktopQuickEntryTwoState as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopQuickEntryTwoState[$key])&&!empty($put1['put']['quickEntryTwoState'][$key])) {
								if ($key == 'extraData') {
									if (count($desktopQuickEntry[$key]) !=count($put1['put']['quickEntry'][$key])) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
									foreach ($desktopQuickEntryTwoState[$key] as $k => $v) {
										if (!empty($put1['put']['layout_update_time'])) {
											break;
										}
										foreach ($v as $l => $item) {
											if (!empty($put1['put']['layout_update_time'])) {
												break;
											}
											if ($l == 'extraData' ) {
												if (count($desktopQuickEntry[$key][$k][$l]) !=count($put1['put']['quickEntry'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
												if (!empty($desktopQuickEntryTwoState[$key][$k][$l])&&!empty($put1['put']['quickEntryTwoState'][$key][$k][$l])) {
													foreach ($item as $a => $i) {
														if (!empty($put1['put']['layout_update_time'])) {
															break;
														}
														if (($desktopQuickEntryTwoState[$key][$k][$l][$a]['key'] !=$put1['put']['quickEntryTwoState'][$key][$k][$l][$a]['key']) || ($desktopQuickEntryTwoState[$key][$k][$l][$a]['value'] !=$put1['put']['quickEntryTwoState'][$key][$k][$l][$a]['value'])  || ($desktopQuickEntryTwoState[$key][$k][$l][$a]['type'] !=$put1['put']['quickEntryTwoState'][$key][$k][$l][$a]['type']) ) {
															$put1['put']['layout_update_time'] = true;
															break;
														}
													}
												}elseif (empty($desktopQuickEntryTwoState[$key][$k][$l])&&!empty($put1['put']['quickEntryTwoState'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}elseif (!empty($desktopQuickEntryTwoState[$key][$k][$l])&&empty($put1['put']['quickEntryTwoState'][$key][$k][$l])) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopQuickEntryTwoState[$key][$k][$l])&&!empty($put1['put']['quickEntryTwoState'][$key][$k][$l])) {
												if ($desktopQuickEntryTwoState[$key][$k][$l] !=$put1['put']['quickEntryTwoState'][$key][$k][$l]) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (!empty($desktopQuickEntryTwoState[$key][$k][$l])&&empty($put1['put']['quickEntryTwoState'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}elseif (empty($desktopQuickEntryTwoState[$key][$k][$l])&&!empty($put1['put']['quickEntryTwoState'][$key][$k][$l])){
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}
									}
								}else{
									if ($desktopQuickEntryTwoState[$key] !=$put1['put']['quickEntryTwoState'][$key]) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
								}
							}elseif (!empty($desktopQuickEntryTwoState[$key])&&empty($put1['put']['quickEntryTwoState'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopQuickEntryTwoState[$key])&&!empty($put1['put']['quickEntryTwoState'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopQuickEntryTwoState)&&empty($put1['put']['quickEntryTwoState'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopQuickEntryTwoState)&&!empty($put1['put']['quickEntryTwoState'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面基础配置有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopAppConfig)&&!empty($put1['put']['appConfig'])) {
						foreach ($desktopAppConfig as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopAppConfig[$key])&&!empty($put1['put']['appConfig'][$key])) {
								if ($desktopAppConfig[$key] !=$put1['put']['appConfig'][$key]) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
							}elseif (!empty($desktopAppConfig[$key])&&empty($put1['put']['appConfig'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopAppConfig[$key])&&!empty($put1['put']['appConfig'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopAppConfig)&&empty($put1['put']['appConfig'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopAppConfig)&&!empty($put1['put']['appConfig'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面底部快捷栏有没有修改
				if (!empty($desktopQuickList)&&!empty($put1['put']['quickList'])) {
					if (count($desktopQuickList) !=count($put1['put']['quickList'])) {
						$put1['put']['layout_update_time'] = true;
					}
					foreach ($desktopQuickList as $key => $value) {
						if (!empty($put1['put']['layout_update_time'])) {
							break;
						}
						if (!empty($desktopQuickList[$key])&&!empty($put1['put']['quickList'][$key])) {
							if ($desktopQuickList[$key] !=$put1['put']['quickList'][$key]) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}elseif (!empty($desktopQuickList[$key])&&empty($put1['put']['quickList'][$key])) {
							$put1['put']['layout_update_time'] = true;
							break;
						}elseif (empty($desktopQuickList[$key])&&!empty($put1['put']['quickList'][$key])) {
							$put1['put']['layout_update_time'] = true;
							break;
						}
					}
				}elseif (!empty($desktopQuickList)&&empty($put1['put']['quickList'])) {
					$put1['put']['layout_update_time'] = true;
				}elseif (empty($desktopQuickList)&&!empty($put1['put']['quickList'])) {
					$put1['put']['layout_update_time'] = true;
				}
				//检查桌面SN有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopSn)&&!empty($put1['put']['sn'])) {
						foreach ($desktopSn as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopSn[$key])&&!empty($put1['put']['sn'][$key])) {
								if ($desktopSn[$key] !=$put1['put']['sn'][$key]) {
									$put1['put']['layout_update_time'] = true;
									break;
								}
							}elseif (!empty($desktopSn[$key])&&empty($put1['put']['sn'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopSn[$key])&&!empty($put1['put']['sn'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopSn)&&empty($put1['put']['sn'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopSn)&&!empty($put1['put']['sn'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面快捷键有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopShortCuts)&&!empty($put1['put']['shortCutConfig'])) {
						if (count($desktopShortCuts)!=count($put1['put']['shortCutConfig'])) {
							$put1['put']['layout_update_time'] = true;
						}
						foreach ($desktopShortCuts as $key => $value) {
							if (!empty($put1['put']['layout_update_time'])) {
								break;
							}
							if (!empty($desktopShortCuts[$key])&&!empty($put1['put']['shortCutConfig'][$key])) {
								foreach ($value as $k => $v) {
									if (!empty($put1['put']['layout_update_time'])) {
										break;
									}
									if (!empty($desktopShortCuts[$key][$k])&&!empty($put1['put']['shortCutConfig'][$key][$k])) {
										if ($k == 'extraData' ) {
											if (count($desktopShortCuts[$key][$k])!=count($put1['put']['shortCutConfig'][$key][$k])) {
												$put1['put']['layout_update_time'] = true;
												break;
											}
											if (!empty($desktopShortCuts[$key][$k])&&!empty($put1['put']['shortCutConfig'][$key][$k])) {
												$desktopShortCuts[$key][$k] = json_encode($desktopShortCuts[$key][$k],JSON_UNESCAPED_UNICODE);
												if ($desktopShortCuts[$key][$k] != $put1['put']['shortCutConfig'][$key][$k]) {
													$put1['put']['layout_update_time'] = true;
													break;
												}
											}elseif (empty($desktopShortCuts[$key][$k])&&!empty($put1['put']['shortCutConfig'][$key][$k])) {
												$put1['put']['layout_update_time'] = true;
												break;
											}elseif (!empty($desktopShortCuts[$key][$k])&&empty($put1['put']['shortCutConfig'][$key][$k])) {
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}elseif (!empty($desktopShortCuts[$key][$k])&&!empty($put1['put']['shortCutConfig'][$key][$k])) {
											if ($desktopShortCuts[$key][$k] !=$put1['put']['shortCutConfig'][$key][$k]) {
												$put1['put']['layout_update_time'] = true;
												break;
											}
										}elseif (empty($desktopShortCuts[$key][$k])&&!empty($put1['put']['shortCutConfig'][$key][$k])) {
											$put1['put']['layout_update_time'] = true;
											break;
										}elseif (!empty($desktopShortCuts[$key][$k])&&empty($put1['put']['shortCutConfig'][$key][$k])) {
											$put1['put']['layout_update_time'] = true;
											break;
										}
									}elseif (!empty($desktopShortCuts[$key])&&empty($put1['put']['shortCutConfig'][$key])) {
										$put1['put']['layout_update_time'] = true;
										break;
									}elseif (empty($desktopShortCuts[$key])&&!empty($put1['put']['shortCutConfig'][$key])) {
										$put1['put']['layout_update_time'] = true;
										break;
									}
								}
							}elseif (!empty($desktopShortCuts[$key])&&empty($put1['put']['shortCutConfig'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}elseif (empty($desktopShortCuts[$key])&&!empty($put1['put']['shortCutConfig'][$key])) {
								$put1['put']['layout_update_time'] = true;
								break;
							}
						}
					}elseif (!empty($desktopShortCuts)&&empty($put1['put']['shortCutConfig'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopShortCuts)&&!empty($put1['put']['shortCutConfig'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				//检查桌面坑位ID有没有修改
				if (!$put1['put']['layout_update_time']) {
					if (!empty($desktopScreens)&&!empty($put1['put']['screens'])) {
						$slotsArr = array();
						$putSlotsArr = array();
						foreach ($desktopScreens as $value) {
							foreach ($value['blocks'] as $key => $val) {
								$slotsArr[$val['slotId']] = $val;
							}
						}
						foreach ($put1['put']['screens'] as $value) {
							foreach ($value['blocks'] as $key => $val) {
								$putSlotsArr[$val['slotId']] = $val;
							}
						}
						if(count($slotsArr) != count($putSlotsArr)){
							$put1['put']['layout_update_time'] = true;
						}else{
							$mergeSlot = array_intersect_key($putSlotsArr,$slotsArr);
							if (count($slotsArr) != count($mergeSlot)) {
								$put1['put']['layout_update_time'] = true;
							}
						}
					}elseif (!empty($desktopScreens)&&empty($put1['put']['screens'])) {
						$put1['put']['layout_update_time'] = true;
					}elseif (empty($desktopScreens)&&!empty($put1['put']['screens'])) {
						$put1['put']['layout_update_time'] = true;
					}
				}
				$this->deleteFullDesktop($put['id'],true);
				// -----------------------------------------------------------------------------------
				//检查坑位是否有变
				//修改桌面屏
				if (!empty($put1['put']['screens']) ) {

					$time = time();
					foreach ($put1['put']['screens'] as $key => $value) {
						$screenUpdateTime = false;
						$desktopScreens[$key]['itemStyle'] = (array)$desktopScreens[$key]['itemStyle'];
						if (!empty($desktopScreens[$key]['itemStyle'])&&!empty($value['itemStyle'])) {
							$value['itemStyle'] = json_decode($value['itemStyle'],true);

							foreach ($value['itemStyle'] as $k => $v) {

								if ( (isset($value['itemStyle'][$k])&&!isset($desktopScreens[$key]['itemStyle'][$k]))  ||  (!isset($value['itemStyle'][$k])&&isset($desktopScreens[$key]['itemStyle'][$k]))) {
									$screenUpdateTime = true;

								}elseif (!isset($value['itemStyle'][$k])&&!isset($desktopScreens[$key]['itemStyle'][$k])) {
									if ($value['itemStyle'][$k] != $desktopScreens[$key]['itemStyle'][$k]) {
										$screenUpdateTime = true;

									}
								}
							}

						}elseif ( ( !empty($desktopScreens[$key]['itemStyle'])&&empty($value['itemStyle']) ) || (empty($desktopScreens[$key]['itemStyle'])&&!empty($value['itemStyle'])) ) {
							$screenUpdateTime = true;
						}
						if (!empty($desktopScreens[$key]['slotGroupId'])&&!empty($value['slotGroupId'])) {
							if ($value['slotGroupId'] != $desktopScreens[$key]['slotGroupId']) {
								$screenUpdateTime = true;

							}
						}elseif ( ( !empty($desktopScreens[$key]['slotGroupId'])&&empty($value['slotGroupId']) ) || (empty($desktopScreens[$key]['slotGroupId'])&&!empty($value['slotGroupId'])) ) {
							$screenUpdateTime = true;
						}
						foreach ($value['blocks'] as $k => $v) {

							if ($screenUpdateTime) {
								$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;
								continue;
							}
							if (empty($slotsArr[$v['slotId']])) {
								$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;
							}else{
								foreach ($slotsArr[$v['slotId']] as $i => $item) {
									if (isset($slotsArr[$v['slotId']]['videos'])&&!isset($value['blocks'][$k]['videos'])) {
										$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

										break;
									}elseif (!isset($slotsArr[$v['slotId']]['videos'])&&isset($value['blocks'][$k]['videos'])) {
										$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

										break;
									}
									if (!empty($slotsArr[$v['slotId']][$i])&&!empty($value['blocks'][$k][$i])) {
										if ($i == 'actionInfo' || $i == 'updateTime' || $i == 'appInfo') {
											continue;
										}elseif ($i == 'videos') {
											if (count($slotsArr[$v['slotId']][$i]) != count($value['blocks'][$k][$i]) ) {
												$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

												break;
											}else{
												foreach ($item as $a => $b) {
													if ($slotsArr[$v['slotId']][$i][$a]['url'] !=$b['url']) {
														$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

														break;
													}elseif ($slotsArr[$v['slotId']][$i][$a]['duration'] !=$b['duration']) {
														$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

														break;
													}
												}
											}
										}elseif ($i == 'actionData' ) {
											foreach ($item as $a => $b) {
												if (isset($slotsArr[$v['slotId']][$i]['appInfo'])&&!isset($value['blocks'][$k][$i]['appInfo'])) {
													$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

													break;
												}elseif (!isset($slotsArr[$v['slotId']][$i]['appInfo'])&&isset($value['blocks'][$k][$i]['appInfo'])) {
													$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

													break;
												}
												if (!empty($slotsArr[$v['slotId']][$i][$a])&&!empty($value['blocks'][$k][$i][$a])) {
													if ($a == 'extraData' ) {
														if (count($slotsArr[$v['slotId']][$i][$a]) != count($value['blocks'][$k][$i][$a]) ) {
															$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

															break;
														}else{
															foreach ($b as $c => $d) {

																if ($d['key'] != $value['blocks'][$k][$i][$a][$c]['key']) {
																	$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

																	break;
																}elseif ($d['velue'] !=$value['blocks'][$k][$i][$a][$c]['velue']) {
																	$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

																	break;
																}elseif ($d['type'] !=$value['blocks'][$k][$i][$a][$c]['type']) {
																	$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

																	break;
																}
															}
														}
													}elseif ($a == 'appInfo') {
														foreach ($b as $c => $d) {
															if ($d !=$value['blocks'][$k][$i][$a][$c]) {
																$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

																break;
															}
														}
													}else{
														if ($slotsArr[$v['slotId']][$i][$a] !=$value['blocks'][$k][$i][$a]) {
															$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

															break;
														}
													}
												}elseif (!empty($slotsArr[$v['slotId']][$i][$a])&&empty($value['blocks'][$k][$i][$a])) {
													$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;
													break;
												}
											}
										}elseif ($i == 'tag') {

											$tag = json_decode($v['tag'],true);

											if (!empty($slotsArr[$v['slotId']][$i])&&!empty($tag)) {

												foreach ($slotsArr[$v['slotId']][$i] as $a => $b) {
													if ($slotsArr[$v['slotId']][$i][$a] != $tag[$a]) {

														$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;
														break;
													}
												}


											}elseif ( (!empty($slotsArr[$v['slotId']][$i])&&empty($tag)) || (empty($slotsArr[$v['slotId']][$i])&& !empty($tag))) {
												$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

												break;
											}

										}else{
											if ($slotsArr[$v['slotId']][$i] !=$value['blocks'][$k][$i]) {
												$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

												break;
											}
										}
									}elseif (!empty($slotsArr[$v['slotId']][$i])&&empty($value['blocks'][$k][$i]) &&  $i != 'actionInfo' && $i != 'updateTime' && $i != 'appInfo' ) {
										$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $time;

										break;
									}
								}
							}

							if (empty($put1['put']['screens'][$key]['blocks'][$k]['updateTime'])) {
								$put1['put']['screens'][$key]['blocks'][$k]['updateTime'] = $slotsArr[$v['slotId']]['updateTime'];
							}
						}
					}
				}
				// die;
				//--------------------------------------------------------------------------------------
				return $this->addFullDesktop($put1,true);
			}else{
				result('param');
			}
		}
		/**
		 * 检查桌面坑位数据
		 * @param  [type] $v 检查的数据
		 * @return [type]
		 */
		public function checkDesktopslot($v,$slotGroupId)
		{
			if ( !isset($v['slotId']) || !isset($v['x']) || !isset($v['y']) || !isset($v['w']) || !isset($v['h']) || !isset($v['yw']) || !isset($v['yh']) || !isset($v['bg']) ) {
				result('您的桌面'.isset($v['slotId'])?$v['slotId'].'坑位信息错误':'没有坑位信息');
			}
			//可选参数
			if ( !( $v['operation'] == 'false' || $v['operation'] == 'true') ) {
				// $response['desktopInfo'] = false;
				result('您的桌面'.isset($v['slotId']).'坑位是否为运营坑位');
			}
			//是否为运营坑位
			if ($v['operation'] == 'false') {
				if (!( $v['disconnectEnable'] == 'false' || $v['disconnectEnable'] == 'true') || !( $v['isEditable'] == 'false' || $v['isEditable'] == 'true') || !($v['dataSource'] == 'yunos' || $v['dataSource'] == 'linkin' || $v['dataSource'] == 'linkinOnly') ) {
					result('您的桌面'.isset($v['slotId']).'坑位是否可编辑、云、非云、强制非云、网络是否响应、');
					// $response['desktopInfo'] = false;
				}
				$data = array();
				//是否为云
				if ($v['dataSource'] == 'linkin' || $v['dataSource'] == 'linkinOnly') {
					if (empty($v['layout'])) {
						result('您的桌面'.$v['slotId'].'坑位布局信息错误');
						// $response['desktopInfo'] = false;
					}
					if (empty($v['actionData'])) {
						result('您的桌面'.$v['slotId'].'坑位跳转信息错误');
						// $response['desktopInfo'] = false;
					}
					$data = checkActionApp($v['actionData']);
					if (empty($data)) {
						result('您的桌面'.$v['slotId'].'坑位跳转信息错误');
					}
					/*switch ($v['actionData']['type']) {
						case 'APP':
							if (isset($v['actionData']['pkgName'])&&isset($v['actionData']['appName'])) {
								$data = array(
									'type'=>$v['actionData']['type'],
									'pkgName'=>$v['actionData']['pkgName'],
									'appName'=>$v['actionData']['appName'],
								);
							}
							break;
						case 'ACTION':
							if (isset($v['actionData']['action'])&&isset($v['actionData']['extraData'])&&isset($v['actionData']['appName'])&&isset($v['actionData']['detailName'])) {
								if (!is_array($v['actionData']['extraData'])) {
									$v['actionData']['extraData'] = array();
								}else{
									foreach ($v['actionData']['extraData'] as $aa => $item) {
										if (!isset($item['key']) || !isset($item['value'])) {
											$v['actionData']['extraData'] = array();
											break;
										}
										if (isset($item['type'])) {
											if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
												unset($v['actionData']['extraData'][$aa]['type']);
											}
										}
									}
								}
								$data = array(
									'type'=>$v['actionData']['type'],
									'action'=>$v['actionData']['action'],
									'extraData' => $v['actionData']['extraData'],
									'appName'=>$v['actionData']['appName'],
									'detailName'=>$v['actionData']['detailName'],
								);
							}
							break;
						case 'COMPONENT':
							if (isset($v['actionData']['clsName'])&&isset($v['actionData']['extraData'])&&isset($v['actionData']['component'])&&isset($v['actionData']['appName'])&&isset($v['actionData']['detailName'])) {
								if (!is_array($v['actionData']['extraData'])) {
									$v['actionData']['extraData'] = array();
								}else{
									foreach ($v['actionData']['extraData'] as $aa =>$item) {
										if (!isset($item['key']) || !isset($item['value']) ) {
											$v['actionData']['extraData'] = array();
											break;
										}
										if (isset($item['type'])) {
											if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
												unset($v['actionData']['extraData'][$aa]['type']);
											}
										}
									}
								}
								$data = array(
									'type'=>$v['actionData']['type'],
									'component'=>$v['actionData']['component'],
									'clsName'=>$v['actionData']['clsName'],
									'extraData' => $v['actionData']['extraData'],
									'appName'=>$v['actionData']['appName'],
									'detailName'=>$v['actionData']['detailName'],
								);
							}
							break;
						case 'URI':
							if (isset($v['actionData']['uri'])) {
								$data = array(
									'type'=>$v['actionData']['type'],
									'uri'=>$v['actionData']['uri'],
									// 'extraData' => $v['actionData']['extraData']
								);
							}
							break;
						default:
							break;
					}*/
					if (!empty($v['tag'])) {
						if (!isset($v['tag']['position']) || !isset($v['tag']['width']) || !isset($v['tag']['height']) || empty($v['tag']['image']) || !($v['tag']['clickDisplay'] == 'true' || $v['tag']['clickDisplay'] == 'false' )) {
							result('您的桌面'.$v['slotId'].'坑位标签数据出错');
						}
						$v['tag'] = array(
							'position' =>$v['tag']['position'],
							'clickDisplay' =>$v['tag']['clickDisplay'],
							'image' =>$v['tag']['image'],
							'height' =>$v['tag']['height'],
							'width' =>$v['tag']['width'],
						);
					}
					if (!empty($v['actionData']['appInfo'])) {
						if (!isset($v['actionData']['appInfo']['path'])||!isset($v['actionData']['appInfo']['pkgName'])||!isset($v['actionData']['appInfo']['versionCode'])/*||!isset($v['actionData']['appInfo']['appName'])*/) {
							// unset($put['screens'][$key]['blocks'][$k]['actionData']['appInfo']);
							result('您的桌面'.$v['slotId'].'坑位绑定APP信息错误');
						}
						$data['appInfo'] = $v['actionData']['appInfo'];
					}
					if ($v['layout'] == 'VIDEO') {
						if (empty($v['videos'])) {
							result($v['slotId'].'坑位参数视频类型出错');
						}
						foreach ($v['videos'] as $item) {
							if (empty($item['duration']) || empty($item['url'])) {
								result($v['slotId'].'坑位参数视频类型出错');
							}
						}

					}
					if (empty($v['pic'])) {
						result($v['slotId'].'坑位参数图片出错');
					}
					if (!empty($v['tag'])) {
						$response['tag'] = json_encode($v['tag'],JSON_UNESCAPED_UNICODE);
						unset($v['tag']);
					}
					if (!empty($data['appInfo'])) {
						$response['appInfo'] = json_encode($data['appInfo'],JSON_UNESCAPED_UNICODE);
						unset($data['appInfo']);
					}
					$response['actionInfo'] = json_encode($data,JSON_UNESCAPED_UNICODE);

				}
				/*if (!empty($put['screens'][$key]['blocks'][$k]['actionData'])) {
					unset($put['screens'][$key]['blocks'][$k]['actionData']);
				}*/
				$response['desktopInfo'] = true;
					// $desktopInfo[$key][$k] = true;

			}else{
				if (!isset($v['operationId'])){
					result('您的桌面'.$v['slotId'].'坑位错误');
				}else{
					if (!D('OperationSlot','Desktop')->getValForSlotIdForSlotGroupId( $v['operationId'] , $slotGroupId )) {
						result($v['slotId'].'运营坑位不存在');
					}
				}
				$response['desktopInfo'] = true;
			}

			return $response;
		}
		/**
		 * 检查快捷跳转信息
		 * @param  [type] $value [description]
		 * @return [type]        [description]
		 */
		/*public function checkActionApp($value)
		{
			$arr = array();
			switch ($value['type']) {
				case 'APP':
					if (isset($value['pkgName'])) {
						$arr = array(
							'type'=>$value['type'],
							'pkgName'=>$value['pkgName'],
						);
					}
					break;
				case 'ACTION':
					if (isset($value['action']) ) {
						if (!is_array($value['extraData']) || empty($value['extraData'])) {
							$value['extraData'] = array();
						}else{
							foreach ($value['extraData'] as $k => $item) {
								if (!isset($item['key']) || !isset($item['value']) ) {
									$value['extraData'] = array();
									break;
								}
								if (isset($item['type'])) {
									if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
										unset($value['extraData'][$k]['type']);
									}
								}
							}
						}
						$arr = array(
							'type'=>$value['type'],
							'action'=>$value['action'],
							'extraData'=>$value['extraData'],
						);
					}
					break;
				case 'SCHEME':
					if (isset($value['action']) && isset($value['uri']) ) {
						if (!is_array($value['extraData'])  || empty($value['extraData'])) {
							$value['extraData'] = array();
						}else{
							foreach ($value['extraData'] as $k => $item) {
								if (!isset($item['key']) || !isset($item['value']) ) {
									$value['extraData'] = array();
									break;
								}
								if (isset($item['type'])) {
									if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
										unset($value['extraData'][$k]['type']);
									}
								}
							}
						}
						$arr = array(
							'type'=>$value['type'],
							'action'=>$value['action'],
							'uri'=>$value['uri'],
							'extraData'=>$value['extraData'],
						);
					}
					break;
				case 'COMPONENT':
					if (isset($value['clsName'])&&isset($value['component']) ) {
						if (!is_array($value['extraData'])  || empty($value['extraData'])) {
							$value['extraData'] = array();
						}else{
							foreach ($value['extraData'] as $k => $item) {
								if (!isset($item['key']) || !isset($item['value'])) {
									$value['extraData'] = array();
									break;
								}
								if (isset($item['type'])) {
									if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
										unset($value['extraData'][$k]['type']);
									}
								}
							}
						}
						$arr = array(
							'type'=>$value['type'],
							'component'=>$value['component'],
							'clsName'=>$value['clsName'],
							'extraData'=>$value['extraData'],
						);
					}
					break;
				case 'URI':
					if (isset($value['uri'])) {
						$arr = array(
							'type'=>$value['type'],
							'uri'=>$value['uri'],
						);
					}
					break;
				default:
					break;
			}
			return $arr;
		}*/
		/**
		 *
		 * 检查桌面数据
		 * {"style":"SIMPLE","isShowIndicator":"true/false","x":"11","y":"111","attacheId":"附件ID"}
		 *
		 */
		public function checkDesktopData($put)
		{
			//检查桌面基础设置
			if ( !empty( $put['appConfig']) ) {
				if (!isset($put['appConfig']['focusStyle']) ) {
					result('基础设置：请设置焦点框移动效果');
				}
				if (!isset($put['appConfig']['isCreateTimeWeather']) ) {
					result('基础设置：请设置时间天气');
				}
				if (!isset($put['appConfig']['focusImage']) ) {
					result('基础设置：请设置焦点框图片');
				}
				if (!isset($put['appConfig']['focusTheta']) ) {
					result('基础设置：请设置寻找焦点的角度范围');
				}
				if ( !($put['appConfig']['isBlurEnabled']  == 'false' || $put['appConfig']['isBlurEnabled'] == 'true') ) {
					result('基础设置：请设置是否启用模糊效果');
				}
				if ( !($put['appConfig']['isAllowReplaceWallpaper']  == 'false' || $put['appConfig']['isAllowReplaceWallpaper'] == 'true')) {
					result('基础设置：请设置是否允许替换壁纸');
				}
				if (!($put['appConfig']['isSkipYunOSReport']  == 'false' || $put['appConfig']['isSkipYunOSReport'] == 'true') ) {
					result('基础设置：请设置是否跳过爆光');

				}
				if ( !($put['appConfig']['isSkipYunOSCheck']  == 'false' || $put['appConfig']['isSkipYunOSCheck'] == 'true') ) {
					result('基础设置：请设置是否跳过阿里审核');
				}
				if ( !($put['appConfig']['isAllowSlotEmpty']  == 'false' || $put['appConfig']['isAllowSlotEmpty'] == 'true') ) {
					result('基础设置：请设置是否允许坑位数为0');
				}

				if (!($put['appConfig']['isCreateNavigation']  == 'false' || $put['appConfig']['isCreateNavigation'] == 'true') ) {
					result('基础设置：请设置是否需要导航栏');
				}
				if (!($put['appConfig']['isCreateTimeWidget']  == 'false' || $put['appConfig']['isCreateTimeWidget'] == 'true')) {
					result('基础设置：请设置是否需要时间控件');
				}
				if ( !($put['appConfig']['isCreateWeatherWidget']  == 'false' || $put['appConfig']['isCreateWeatherWidget'] == 'true') ) {
					result('基础设置：请设置是否需要天气控件');
				}
				if (!($put['appConfig']['isCreateSlotAttachment']  == 'false' || $put['appConfig']['isCreateSlotAttachment'] == 'true')) {
					result('基础设置：请设置是否需要坑位附件');
				}
				if ( !($put['appConfig']['isCreateLogoWidget']  == 'false' || $put['appConfig']['isCreateLogoWidget'] == 'true')) {
					result('基础设置：请设置是否创建LOGO');
				}
				if (!($put['appConfig']['isCreateQuickEntry']  == 'false' || $put['appConfig']['isCreateQuickEntry'] == 'true')) {
					result('基础设置：请设置是否创建快捷入口');
				}
				if ( !($put['appConfig']['isCreateQuickList']  == 'false' || $put['appConfig']['isCreateQuickList'] == 'true') ) {
					result('基础设置：请设置是否创建底部快捷栏');
				}
				if (!($put['appConfig']['isDisposeNavLeft']  == 'false' || $put['appConfig']['isDisposeNavLeft'] == 'true') ) {
					result('基础设置：请设置是否处理左边导航');
				}
				if ( !($put['appConfig']['isDisposeNavRight']  == 'false' || $put['appConfig']['isDisposeNavRight'] == 'true') ) {
					result('基础设置：请设置是否处理右边导航');
				}
				if ( !($put['appConfig']['isCreateNavBottomLine']  == 'false' || $put['appConfig']['isCreateNavBottomLine'] == 'true') ) {
					result('基础设置：请设置是否需要导航底部');
				}
				if (  !($put['appConfig']['isCreateUsbWidget']  == 'false' || $put['appConfig']['isCreateUsbWidget'] == 'true')) {
					result('基础设置：请设置是否创建USB控件');
				}
				if (!($put['appConfig']['isCreateSnWidget']  == 'false' || $put['appConfig']['isCreateSnWidget'] == 'true') ) {
					result('基础设置：请设置是否创建SN控件');
				}
				if (!($put['appConfig']['isCreateNetworkWidget']  == 'false' || $put['appConfig']['isCreateNetworkWidget'] == 'true') ) {
					result('基础设置：请设置是否创建网络控件');
				}
				if ( !isset($put['appConfig']['firstSlotId'] )) {
					result('基础设置：请设置焦点ID');
				}
				if (  !isset($put['appConfig']['slotCornerRadius'])  ) {
					result('基础设置：请设置圆角值');
				}
			}else{
				// $put['appConfig'] = array();
				result('请设置基础设置');
			}
			//检查桌面消息(走马灯)设置
			if ( !empty( $put['messageConfig']) ) {
				if ( !isset($put['messageConfig']['y']) ||  !isset($put['messageConfig']['x']) ||  !isset($put['messageConfig']['width']) ||  !isset($put['messageConfig']['fontSize'])  ) {
					result('消息(走马灯)参数有误');
				}
			}else{
				$put['messageConfig'] = array();
			}

			//检查桌面时间天气设置
			if ( !empty( $put['timeWeather']) ) {
				if ( !isset($put['timeWeather']['y']) ||  !isset($put['timeWeather']['x']) ||  !isset($put['timeWeather']['style'])  ) {
					result('时间天气参数有误');
				}
			}else{
				$put['timeWeather'] = array();
			}
			//检查桌面风格设置
			if ( !empty( $put['style']) ) {
				if ( !isset($put['style']['y']) ||  !isset($put['style']['x']) ||  !isset($put['style']['height']) ||  !isset($put['style']['width']) || !isset($put['style']['name']) || !isset($put['style']['displayQuantity']) || !isset($put['style']['slotAngle']) || !isset($put['style']['isCircle'])  ) {
					result('桌面风格设置参数不完整');
				}else{
					$options = array(
						'name'=>$put['style']['name'],
						'displayQuantity'=>$put['style']['displayQuantity'],
						'slotAngle'=>$put['style']['slotAngle'],
						'isCircle'=>$put['style']['isCircle'],
						'width'=>$put['style']['width'],
						'height'=>$put['style']['height'],
						'x'=>$put['style']['x'],
						'y'=>$put['style']['y'],
					);
					$put['style']['jsonText'] = json_encode($options,JSON_UNESCAPED_UNICODE);
				}
			}else{
				$put['style'] = array();
			}
			//检查桌面三态快捷入口
			if (!empty($put['quickEntryThreeState']) ) {
				if ( empty($put['quickEntryThreeState']['extraData']) || empty($put['quickEntryThreeState']['style']) || empty($put['quickEntryThreeState']['isShowIndicator']) || !isset($put['quickEntryThreeState']['x']) || !isset($put['quickEntryThreeState']['y']) ){
					result('三态快捷入口参数有误');
				}
				$indexArr = array();
				foreach ($put['quickEntryThreeState']['extraData'] as $value) {
					$indexArr[] = $value['index'];
					$uniqueIndexArr[] = $value['index'];
				}
				$indexArr = array_unique($indexArr);
				if (count($indexArr) != count($put['quickEntryThreeState']['extraData'])) {
					result('三态快捷入口ID重复');
				}
				foreach ($put['quickEntryThreeState']['extraData'] as $key => $value) {
					if (empty($value['type']) || empty($value['focusedDrawableC']) || empty($value['focusedDrawableB']) || empty($value['focusedDrawableA']) || empty($value['drawableC']) || empty($value['drawableB']) || empty($value['drawableA']) || empty($value['eventC']) || empty($value['eventB']) || empty($value['eventA']) || empty($value['eventType']) || empty($value['name']) || !isset($value['x']) || !isset($value['y']) || !isset($value['index'])) {
						result('第'.($key+1).'三态快捷入口参数有误');
					}
					$arr = /*$this->*/checkActionApp($value,true);
					if (empty($arr)) {
						result('第'.($key+1).'三态快捷入口跳转参数有误');
					}
					$data[$key] = $arr;
					$data[$key]['focusedDrawableC']=$value['focusedDrawableC'];
					$data[$key]['focusedDrawableB']=$value['focusedDrawableB'];
					$data[$key]['focusedDrawableA']=$value['focusedDrawableA'];
					$data[$key]['drawableC']=$value['drawableC'];
					$data[$key]['drawableB']=$value['drawableB'];
					$data[$key]['drawableA']=$value['drawableA'];
					$data[$key]['eventC']=$value['eventC'];
					$data[$key]['eventB']=$value['eventB'];
					$data[$key]['eventA']=$value['eventA'];
					$data[$key]['x']=$value['x'];
					$data[$key]['y']=$value['y'];
					$data[$key]['name']=$value['name'];
					$data[$key]['eventType']=$value['eventType'];
					$data[$key]['index']=$value['index'];
					$data[$key]['nextFocusLeftId'] = isset($value['nextFocusLeftId'])?$value['nextFocusLeftId']:'';
					$data[$key]['nextFocusRightId'] = isset($value['nextFocusRightId'])?$value['nextFocusRightId']:'';
					$data[$key]['nextFocusUpId'] =  isset($value['nextFocusUpId'])?$value['nextFocusUpId']:'';
					$data[$key]['nextFocusDownId'] =  isset($value['nextFocusDownId'])?$value['nextFocusDownId']:'';
				}
				if (!empty($data)) {
					$data = array_values($data);
					$data = json_encode($data,JSON_UNESCAPED_UNICODE);
					$put['quickEntryThreeState']['quickInfo'] = $data;
				}else{
					$put['quickEntryThreeState']['quickInfo'] = '[]';
				}
			}else{
				$put['quickEntryThreeState'] = array();
			}
			//检查桌面两态快捷入口
			if (!empty($put['quickEntryTwoState'])) {
				if (empty($put['quickEntryTwoState']['extraData']) || empty($put['quickEntryTwoState']['style']) || empty($put['quickEntryTwoState']['isShowIndicator']) || !isset($put['quickEntryTwoState']['x']) || !isset($put['quickEntryTwoState']['y'])) {
					result('两态快捷入口参数有误');
				}
				$indexArr = array();
				foreach ($put['quickEntryTwoState']['extraData'] as $value) {
					$indexArr[] = $value['index'];
					$uniqueIndexArr[] = $value['index'];
				}
				$indexArr = array_unique($indexArr);
				if (count($indexArr) != count($put['quickEntryTwoState']['extraData'])) {
					result('两态快捷入口ID重复');
				}
				$data = array();
				foreach ($put['quickEntryTwoState']['extraData'] as $key => $value) {
					if (empty($value['type']) || empty($value['activeDrawable']) || empty($value['normalDrawable']) || empty($value['focusedActiveDrawable']) || empty($value['focusedNormalDrawable']) || empty($value['eventType']) || empty($value['name']) || !isset($value['x']) || !isset($value['y'])) {
						result('第'.($key+1).'两态快捷入口跳转参数有误');
					}

					$arr = /*$this->*/checkActionApp($value,true);
					if (empty($arr)) {
						result('第'.($key+1).'两态快捷入口跳转参数有误');
					}
					$data[$key] = $arr;
					$data[$key]['focusedNormalDrawable']=$value['focusedNormalDrawable'];
					$data[$key]['focusedActiveDrawable']=$value['focusedActiveDrawable'];
					$data[$key]['normalDrawable']=$value['normalDrawable'];
					$data[$key]['activeDrawable']=$value['activeDrawable'];
					$data[$key]['x']=$value['x'];
					$data[$key]['y']=$value['y'];
					$data[$key]['name']=$value['name'];
					$data[$key]['eventType']=$value['eventType'];
					$data[$key]['index']=$value['index'];
					$data[$key]['nextFocusLeftId'] = isset($value['nextFocusLeftId'])?$value['nextFocusLeftId']:'';
					$data[$key]['nextFocusRightId'] = isset($value['nextFocusRightId'])?$value['nextFocusRightId']:'';
					$data[$key]['nextFocusUpId'] =  isset($value['nextFocusUpId'])?$value['nextFocusUpId']:'';
					$data[$key]['nextFocusDownId'] =  isset($value['nextFocusDownId'])?$value['nextFocusDownId']:'';
				}
				if (!empty($data)) {
					$data = array_values($data);
					$data = json_encode($data,JSON_UNESCAPED_UNICODE);
					$put['quickEntryTwoState']['quickInfo'] = $data;
				}else{
					$put['quickEntryTwoState']['quickInfo'] = '[]';
				}


			}else{
				$put['quickEntryTwoState'] = array();
			}
			//检查桌面底部快捷栏
			if ( !empty( $put['quickList']) ) {
				$data = array();
				foreach ($put['quickList'] as  $value) {
					if ( isset($value['versionCode']) && isset($value['index']) && isset($value['pkgName'])  && isset($value['appIcon'])  && isset($value['appName'])  && isset($value['apkUrl']) ) {
						$uniqueIndexArr[] = $value['index'];
						$data[] = array(
							'index'=>$value['index'],
							'title'=>!empty($value['title'])?$value['title']:"",
							'pkgName'=>$value['pkgName'],
							'appIcon'=>$value['appIcon'],
							'appName'=>$value['appName'],
							'apkUrl'=>$value['apkUrl'],
							'versionCode'=>$value['versionCode'],
						);
					}
				}
				$put['quickList'] = $data;
			}else{
				$put['quickList'] = array();
			}
			//检查桌面快捷键配置
			if (!empty($put['shortCutConfig'])) {
				$data = array();
				foreach ($put['shortCutConfig'] as  $key => $value) {
					if ( !isset($value['keyCode']) || !isset($value['type'])) {
						result('第'.($key+1).'快捷键值不存在');
					}
					$actionData = checkActionApp($value);
					if (empty($actionData)) {
						if ($value['type'] != 'SCREEN') {
							result('第'.($key+1).'快捷键类型不存在');
						}
						if (empty($value['sid'])) {
							result('第'.($key+1).'快捷键屏类型参数有误');
						}
						$actionData = array(
							'type'=>$value['type'],
							'sid'=>$value['sid']
						);
					}

					$data[] = array(
						'keyCode'=>$value['keyCode'],
						'type'=>$actionData['type'],
						'action'=>isset($actionData['action'])?$actionData['action']:'',
						'appName'=>isset($actionData['appName'])?$actionData['appName']:'',
						'detailName'=>isset($actionData['detailName'])?$actionData['detailName']:'',
						'extraData'=>isset($actionData['extraData'])?json_encode($actionData['extraData'],JSON_UNESCAPED_UNICODE):'[]',
						'clsName'=>isset($actionData['clsName'])?$actionData['clsName']:'',
						'component'=>isset($actionData['component'])?$actionData['component']:'',
						'pkgName'=>isset($actionData['pkgName'])?$actionData['pkgName']:'',
						'uri'=>isset($actionData['uri'])?$actionData['uri']:'',
						'sid'=>isset($actionData['sid'])?$actionData['sid']:'',
					);
						/*switch ($value['type']) {
							case 'ACTION':
								if (!empty($value['action'])&&!empty($value['appName'])&&!empty($value['detailName'])) {
									if (is_array($value['extraData'])) {
										foreach ($value['extraData'] as  $k => $item) {
											if (!isset($item['key']) || !isset($item['value'])) {
												unset($value['extraData']);
												break;
											}
											if (isset($item['type'])) {
												if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
													unset($value['extraData'][$k]['type']);
												}
											}
										}
										if (empty($value['extraData'])) {
											$value['extraData'] = array();
										}else{
											$value['extraData'] = array_values($value['extraData']);
										}
									}else{
										$value['extraData'] = array();
									}
									$value['extraData'] = json_encode($value['extraData'],JSON_UNESCAPED_UNICODE);
									$data[] = array(
										'type'=>'ACTION',
										'keyCode'=>$value['keyCode'],
										'action'=>$value['action'],
										'appName'=>$value['appName'],
										'detailName'=>$value['detailName'],
										'extraData'=>$value['extraData'],
										'clsName'=>'',
										'component'=>'',
										'pkgName'=>'',
										'uri'=>'',
										'sid'=>''
									);
								}
								break;
							case 'APP':
								if (!empty($value['pkgName'])&&!empty($value['appName'])) {
									$data[] = array(
										'type'=>'APP',
										'keyCode'=>$value['keyCode'],
										'action'=>'',
										'appName'=>$value['appName'],
										'detailName'=>'',
										'extraData'=>'',
										'clsName'=>'',
										'component'=>'',
										'pkgName'=>$value['pkgName'],
										'uri'=>'',
										'sid'=>'0'
									);
								}
								break;
							case 'COMPONENT':
								if (!empty($value['component'])&&!empty($value['clsName'])&&!empty($value['appName'])&&!empty($value['detailName'])) {
									if (is_array($value['extraData'])) {
										foreach ($value['extraData'] as  $k => $item) {
											if (!isset($item['key']) || !isset($item['value']) ) {
												unset($value['extraData']);
												break;
											}
											if (isset($item['type'])) {
												if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
													unset($value['extraData'][$k]['type']);
												}
											}
										}
										if (empty($value['extraData'])) {
											$value['extraData'] = array();
										}else{
											$value['extraData'] = array_values($value['extraData']);
										}
									}else{
										$value['extraData'] = array();
									}
									$value['extraData'] = json_encode($value['extraData'],JSON_UNESCAPED_UNICODE);
									$data[] = array(
										// 'type'=>'COMPONENT',
										'keyCode'=>$value['keyCode'],
										'type'=>'COMPONENT',
										'action'=>'',
										'appName'=>$value['appName'],
										'detailName'=>$value['detailName'],
										'extraData'=>$value['extraData'],
										'clsName'=>$value['clsName'],
										'component'=>$value['component'],
										'pkgName'=>'',
										'uri'=>'',
										'sid'=>'0'
									);
								}
								break;
							case 'URI':
								if (!empty($value['uri'])) {
									$data[] = array(
										'type'=>'URI',
										'keyCode'=>$value['keyCode'],
										'action'=>'',
										'appName'=>'',
										'detailName'=>'',
										'extraData'=>'',
										'clsName'=>'',
										'component'=>'',
										'pkgName'=>'',
										'uri'=>$value['uri'],
										'sid'=>'0'
									);
								}
								break;
							case 'SCREEN':
								if (isset($value['sid'])) {
									$data[] = array(
										'type'=>'SCREEN',
										'keyCode'=>$value['keyCode'],
										'action'=>'',
										'appName'=>'',
										'detailName'=>'',
										'extraData'=>'',
										'clsName'=>'',
										'component'=>'',
										'pkgName'=>'',
										'uri'=>'',
										'sid'=>$value['sid']
									);
								}
								break;
							default:
								break;
						}*/
				}

				$put['shortCutConfig'] = $data;
			}else{
				$put['shortCutConfig'] = array();
			}
			//检查是否有桌面导航
			if (!empty($put['nav'])) {
				if ( empty($put['nav']['extraData']) || !($put['nav']['style'] == 'SIMPLE' || $put['nav']['style'] == 'JAV' || $put['nav']['style'] == 'SAST' || $put['nav']['style'] == 'RAIZEA' ) || !($put['nav']['isShowIndicator'] == 'false' || $put['nav']['isShowIndicator'] == 'true') || !isset($put['nav']['x']) || !isset($put['nav']['y']) || !isset($put['nav']['interval'])) {
					result('桌面导航数据出错');
				}
				foreach ($put['nav']['extraData'] as $key => $value) {
					if( !isset($value['normalPath']) || !isset($value['forcusPath']) || !isset($value['functionId'])){
						result('桌面导航图片为空或功能ID为空出错');
					}/*else{
						$navFunctionIdArr[] = $value['functionId'];
					}*/
					$put['nav']['extraData'][$key]['nextFocusLeftId'] = isset($value['nextFocusLeftId'])?$value['nextFocusLeftId']:'';
					$put['nav']['extraData'][$key]['nextFocusRightId'] = isset($value['nextFocusRightId'])?$value['nextFocusRightId']:'';
					$put['nav']['extraData'][$key]['nextFocusUpId'] =  isset($value['nextFocusUpId'])?$value['nextFocusUpId']:'';
					$put['nav']['extraData'][$key]['nextFocusDownId'] =  isset($value['nextFocusDownId'])?$value['nextFocusDownId']:'';
				}
				$put['nav']['navInfo'] = json_encode($put['nav']['extraData'],JSON_UNESCAPED_UNICODE);
			}else{
				$put['nav']=array();
			}
			//检查是否有桌面坑位
			if (!empty($put['screens'])) {
				$quickEntrySlot = array();
				$quickEntrySlotIDArr = array();
				$globalQuickEntrySlotIDArr = array();
				foreach ($put['screens'] as $key => $value) {
					if (empty($value['blocks'])  ) {
						result('您的桌面第'.($key+1).'屏没有块信息');
					}
					if (($value['itemStyle']['name'] == 'coverflow') &&isset($value['itemStyle']['displayQuantity'])  &&isset($value['itemStyle']['slotAngle'])  && ($value['itemStyle']['isCircle'] =='true' || $value['itemStyle']['isCircle'] =='false' )  && isset($value['itemStyle']['width'])  && isset($value['itemStyle']['height'])  && isset($value['itemStyle']['x'])  && isset($value['itemStyle']['y']) ) {
						$put['screens'][$key]['itemStyle'] = json_encode($value['itemStyle'],JSON_UNESCAPED_UNICODE);
					}else{
						$put['screens'][$key]['itemStyle'] = '';
					}
					foreach ($put['screens'][$key]['blocks'] as $k => $v) {
						if ($v['type'] =='common') {
							if ( !isset($v['slotId']) ||  !isset($v['x']) || !isset($v['y']) || !isset($v['w']) || !isset($v['h']) || !isset($v['yw']) || !isset($v['yh']) || !isset($v['bg']) ) {
								result('您的桌面第'.($key+1).'屏'.isset($v['slotId'])?$v['slotId'].'坑位信息错误':'没有坑位信息');
							}
							$response = $this->checkDesktopslot($v,$put['screens'][$key]['slotGroupId']);

							if (!empty($response['appInfo'])) {
								$put['screens'][$key]['blocks'][$k]['appInfo'] = $response['appInfo'];
							}
							if (!empty($response['tag'])) {
								$put['screens'][$key]['blocks'][$k]['tag'] = $response['tag'];
							}

							$put['screens'][$key]['blocks'][$k]['actionInfo'] = $response['actionInfo'];

							$desktopInfo[$v['slotId']] = $response['desktopInfo'];

						}elseif ($v['type'] == 'quickEntry') {
							if (isset($v['slotId']) && isset($v['bg']) && isset($v['x']) && isset($v['y'])  && isset($v['w'])   && isset($v['h']) ) {
								$quickEntrySlotIDArr[] = $v['slotId'];
								$quickEntrySlot[] = array(
									'slotId' => $v['slotId'],
									'bg' => $v['bg'],
									'x' => $v['x'],
									'y' => $v['y'],
									'w' => $v['w'],
									'h' => $v['h'],
									'type' => 'quickEntry',
									'sid' => $key,
									"title"=>isset($v["title"])?$v["title"]:'',
									"nextFocusLeftId"=>isset($v["nextFocusLeftId"])?$v["nextFocusLeftId"]:'',
									"nextFocusRightId"=>isset($v["nextFocusRightId"])?$v["nextFocusRightId"]:'',
									"nextFocusUpId"=>isset($v["nextFocusUpId"])?$v["nextFocusUpId"]:'',
									"nextFocusDownId"=>isset($v["nextFocusDownId"])?$v["nextFocusDownId"]:'',
								);

							}else{
								$quickEntrySlot = array();
							}
							unset($put['screens'][$key]['blocks'][$k]);
						}/*elseif ($v['type'] == 'globalQuickEntry') {
							if (isset($v['slotId']) && isset($v['bg']) && isset($v['x']) && isset($v['y'])  && isset($v['w'])   && isset($v['h']) ) {
								$globalQuickEntrySlotIDArr[] = $v['slotId'];
								$globalQuickEntrySlot[] = array(
									'slotId' => $v['slotId'],
									'bg' => $v['bg'],
									'x' => $v['x'],
									'y' => $v['y'],
									'w' => $v['w'],
									'h' => $v['h'],
									'type' => 'globalQuickEntry',
									'sid' => $key,
									"title"=>isset($v["title"])?$v["title"]:'',
									"nextFocusLeftId"=>isset($v["nextFocusLeftId"])?$v["nextFocusLeftId"]:'',
									"nextFocusRightId"=>isset($v["nextFocusRightId"])?$v["nextFocusRightId"]:'',
									"nextFocusUpId"=>isset($v["nextFocusUpId"])?$v["nextFocusUpId"]:'',
									"nextFocusDownId"=>isset($v["nextFocusDownId"])?$v["nextFocusDownId"]:'',
								);

							}else{
								$globalQuickEntrySlot = array();
							}
							unset($put['screens'][$key]['blocks'][$k]);
						}*/
					}
				}
			}else{
				$put['screens']  = array();
			}
			//检查桌面可下载快捷入口坑位
			if (!empty($quickEntrySlot)) {
				if (!empty($quickEntrySlotIDArr)) {
					$quickEntrySlotIDArr = array_unique($quickEntrySlotIDArr);
					$slotIDSqlStr = implode(',', $quickEntrySlotIDArr);
					$QuickEntrySlotArrCount = D('QuickEntrySlot','Desktop')->getCountForArrId($slotIDSqlStr);
					if ($QuickEntrySlotArrCount != count($quickEntrySlotIDArr)) {
						result('请检查快捷坑位是否存在');
					}
				}
			}

			if (!empty($put['quickEntrySlot']['globalItems'])&&is_array($put['quickEntrySlot']['globalItems'])) {
				foreach ($put['quickEntrySlot']['globalItems'] as $k => $v) {
					if (!isset($v['slotId']) || !isset($v['bg']) || !isset($v['x']) || !isset($v['y']) || !isset($v['w']) || !isset($v['h']) ) {
						result('全局快捷坑位'.$v['slotId'].'参数有误');
					}
					$globalQuickEntrySlotIDArr[] = $v['slotId'];
					$globalQuickEntrySlot[] = array(
						'slotId' => $v['slotId'],
						'bg' => $v['bg'],
						'x' => $v['x'],
						'y' => $v['y'],
						'w' => $v['w'],
						'h' => $v['h'],
						'type' => 'globalQuickEntry',
						'sid' => -1,
						"title"=>isset($v["title"])?$v["title"]:'',
						"nextFocusLeftId"=>isset($v["nextFocusLeftId"])?$v["nextFocusLeftId"]:'',
						"nextFocusRightId"=>isset($v["nextFocusRightId"])?$v["nextFocusRightId"]:'',
						"nextFocusUpId"=>isset($v["nextFocusUpId"])?$v["nextFocusUpId"]:'',
						"nextFocusDownId"=>isset($v["nextFocusDownId"])?$v["nextFocusDownId"]:'',
					);
				}
				if (!empty($globalQuickEntrySlotIDArr)) {
					$globalQuickEntrySlotIDArr = array_unique($globalQuickEntrySlotIDArr);
					$slotIDSqlStr = implode(',', $globalQuickEntrySlotIDArr);
					$QuickEntrySlotArrCount = D('QuickEntrySlot','Desktop')->getCountForArrId($slotIDSqlStr);
					if ($QuickEntrySlotArrCount != count($globalQuickEntrySlotIDArr)) {
						result('请检查快捷坑位是否存在');
					}
				}
			}

			if (!empty($put['quickEntrySlot']['style'])&&!empty($put['quickEntrySlot']['animation'])) {
				if (!empty($quickEntrySlot)) {
					foreach ($quickEntrySlot as $key => $value) {
						$quickEntrySlot[$key]['style'] = $put['quickEntrySlot']['style'];
						$quickEntrySlot[$key]['animation'] = $put['quickEntrySlot']['animation'];
					}
				}
				if (!empty($globalQuickEntrySlot)) {
					foreach ($globalQuickEntrySlot as $key => $value) {
						$globalQuickEntrySlot[$key]['style'] = $put['quickEntrySlot']['style'];
						$globalQuickEntrySlot[$key]['animation'] = $put['quickEntrySlot']['animation'];
					}
				}
				if (!empty($quickEntrySlot)&&!empty($globalQuickEntrySlot)) {
					$put['quickEntrySlot'] = array_merge($quickEntrySlot,$globalQuickEntrySlot);
				}elseif (!empty($quickEntrySlot)) {
					$put['quickEntrySlot'] = $quickEntrySlot;
				}elseif (!empty($globalQuickEntrySlot)) {
					$put['quickEntrySlot'] = $globalQuickEntrySlot;
				}else{
					$put['quickEntrySlot'] = array();
				}
			}else{
				$put['quickEntrySlot'] = array();
			}

			//检查是否有桌面导航
			/*if (!empty($put['nav']['extraData'])&&!empty($put['nav']['style'])&&!empty($put['nav']['isShowIndicator'])&&isset($put['nav']['x'])&&isset($put['nav']['y'])&&isset($put['nav']['interval'])) {
				if (is_array($put['nav']['extraData'])) {
					foreach ($put['nav']['extraData'] as $key => $value) {
						if(!isset($value['normalPath'])||!isset($value['forcusPath'])){
							$put['nav']['extraData']=null;
						}
					}
				}
				if (!empty($put['nav']['extraData'])) {
					if (!empty($put['screens'])) {
						if (count($put['nav']['extraData']) != count($put['screens'])) {
							result('屏与导航数量不一致');
						}else{
 							foreach ($put['screens'] as $key => $value) {
 								if (empty($put['screens'][$key]['blocks'])) {
 									result('您的桌面有的屏没有块信息');
 								}
 								foreach ($put['screens'][$key]['blocks'] as $k => $v) {
 									if ( !isset($v['slotId']) || !isset($v['x']) || !isset($v['y']) || !isset($v['w']) || !isset($v['h']) || !isset($v['yw']) || !isset($v['yh']) || !isset($v['bg']) ) {
 										result('您的桌面有的屏块信息错误');
 									}
 									$response = $this->checkDesktopslot($v);
 									if (!empty($response['appInfo'])) {
										$put['screens'][$key]['blocks'][$k]['appInfo'] = $response['appInfo'];
										$put['screens'][$key]['blocks'][$k]['actionInfo'] = $response['actionInfo'];
									}else{
										$put['screens'][$key]['blocks'][$k]['actionInfo'] = $response['actionInfo'];
									}
									$desktopInfo[$key][$k] =$response['desktopInfo'];
									if (!empty($put['screens'][$key]['blocks'][$k]['actionData'])) {
										unset($put['screens'][$key]['blocks'][$k]['actionData']);
									}
 								}
 							}
						}
					}else{
						result('屏参数出错');
					}
					if ((empty($put['nav']['style']) || $put['nav']['style'] != 'SIMPLE') || !($put['nav']['isShowIndicator'] == 'false' || $put['nav']['isShowIndicator'] == 'true') || empty($put['nav']['extraData'])) {
						result('导航参数出错');
					}else{
						foreach ($put['nav']['extraData'] as $key => $value) {
							if( !isset($value['normalPath']) || !isset($value['forcusPath'])){
								result('导航附加参数出错');
							}
						}
						$put['nav']['navInfo'] = json_encode($put['nav']['extraData'],JSON_UNESCAPED_UNICODE);
					}
				}else{
					$put['nav'] = array();
					if (!empty($put['screens'])) {
						if (count($put['screens']) !=1) {
							result('屏与导航数量不一致');
						}else{
							foreach ($put['screens'] as $key => $value) {
								if (empty($put['screens'][$key]['blocks'])) {
									result('您的桌面有的屏没有块信息');
								}
								foreach ($put['screens'][$key]['blocks'] as $k => $v) {
									if ( !isset($v['slotId']) ||  !isset($v['x']) || !isset($v['y']) || !isset($v['w']) || !isset($v['h']) || !isset($v['yw']) || !isset($v['yh']) || !isset($v['bg']) ) {
										result('您的桌面有的屏块信息错误');
									}
 									$response = $this->checkDesktopslot($v);
 									if (!empty($response['appInfo'])) {
										$put['screens'][$key]['blocks'][$k]['appInfo'] = $response['appInfo'];
										$put['screens'][$key]['blocks'][$k]['actionInfo'] = $response['actionInfo'];
									}else{
										$put['screens'][$key]['blocks'][$k]['actionInfo'] = $response['actionInfo'];
									}
									$desktopInfo[$key][$k] =$response['desktopInfo'];
									if (!empty($put['screens'][$key]['blocks'][$k]['actionData'])) {
										unset($put['screens'][$key]['blocks'][$k]['actionData']);
									}
								}
							}
						}
					}else{
						$put['screens']  = array();
					}
				}
			}else{
				$put['nav'] = array();
				if (!empty($put['screens'])) {
					if (count($put['screens']) !=1) {
						result('屏与导航数量不一致');
					}else{
						foreach ($put['screens'] as $key => $value) {
							if (empty($put['screens'][$key]['blocks'])) {
								result('您的桌面有的屏没有块信息');
							}
							foreach ($put['screens'][$key]['blocks'] as $k => $v) {
								if ( !isset($v['slotId']) ||  !isset($v['x']) || !isset($v['y']) || !isset($v['w']) || !isset($v['h']) || !isset($v['yw']) || !isset($v['yh']) || !isset($v['bg']) ) {
									result('您的桌面有的屏块信息错误');
								}
								$response = $this->checkDesktopslot($v);
								if (!empty($response['appInfo'])) {
									$put['screens'][$key]['blocks'][$k]['appInfo'] = $response['appInfo'];
									$put['screens'][$key]['blocks'][$k]['actionInfo'] = $response['actionInfo'];
								}else{
									$put['screens'][$key]['blocks'][$k]['actionInfo'] = $response['actionInfo'];
								}
								$desktopInfo[$key][$k] =$response['desktopInfo'];
								if (!empty($put['screens'][$key]['blocks'][$k]['actionData'])) {
									unset($put['screens'][$key]['blocks'][$k]['actionData']);
								}
							}
						}
					}
				}else{
					$put['screens']  = array();
				}
			}*/
			//检查是否有桌面LOGO
			if (!empty($put['logo'])) {

				if (!is_array($put['logo']['logoLists']) || !isset($put['logo']['x']) || !isset($put['logo']['y']) || !($put['logo']['style'] == 'SIMPLE') || !($put['logo']['isShowIndicator']  === 'false' || $put['logo']['isShowIndicator']  === 'true') || !isset($put['logo']['intervalTime'])) {
					result('Logo数据出错');
				}
			}
			//检查是否有桌面天气
			if (!empty($put['weather'])) {
				if (!($put['weather']['isShowIcon'] == 'false' || $put['weather']['isShowIcon'] == 'true') || !($put['weather']['isShowDesc'] == 'false' || $put['weather']['isShowDesc'] == 'true') || !($put['weather']['isShowTemperature'] == 'false' || $put['weather']['isShowTemperature'] == 'true') || !($put['weather']['isShowCity'] == 'false' || $put['weather']['isShowCity'] == 'true') || !($put['weather']['style'] == 'SIMPLE') || !($put['weather']['isShowIndicator'] == 'false' || $put['weather']['isShowIndicator'] == 'true') || ($put['weather']['style'] != 'SIMPLE') || !isset($put['weather']['y']) || !isset($put['weather']['x'])) {
					result('天气数据出错');
				}
			}

			//检查是否有桌面SN

			if (!empty($put['sn'])) {
				if (!isset($put['sn']['ipmacroProperty']) || !isset($put['sn']['prefixInfo']) || !isset($put['sn']['systemProperty']) || !($put['sn']['isShowIndicator'] == 'false' || $put['sn']['isShowIndicator'] == 'true') || empty($put['sn']['style']) ||  !isset($put['sn']['y']) || !isset($put['sn']['x'])) {
					result('SN数据出错');
				}
			}

			//检查是否有附件
			//{"attachment":{"style":"SIMPLE","isShowIndicator":"true/false","x";"x坐标","y":"y坐标","attacheId";"基础附件ID"}}
			if (!empty($put['attachment'])) {
				if (!isset($put['attachment']['interval']) || !($put['attachment']['style'] == 'SIMPLE') || !($put['attachment']['isShowIndicator'] == 'false' || $put['attachment']['isShowIndicator'] == 'true') || !isset($put['attachment']['y']) || !isset($put['attachment']['x']) || !isset($put['attachment']['extraData'])) {
					result('附件数据出错');
				}
				$data = array();
				$indexArr = array();
				foreach ($put['attachment']['extraData'] as $value) {
					$indexArr[] = $value['index'];
					$uniqueIndexArr[] = $value['index'];
				}
				$indexArr = array_unique($indexArr);
				if (count($indexArr) != count($put['attachment']['extraData'])) {
					result('附件ID重复');
				}
				foreach ($put['attachment']['extraData'] as $key => $value) {
					if ( !isset($value['name']) || !isset($value['radiusTopLeft'] ) || !isset($value['radiusTopRight']) || !isset($value['radiusBottomLeft'] ) || !isset($value['radiusBottomRight']) || !isset($value['normalPath']) || !isset($value['forcusPath']) ) {
						result('附件ID为'.$value['index'].'参数有误');
					}
					$arr = /*$this->*/checkActionApp($value,true);
					if (empty($arr)) {
						result('附件ID为'.$value['index'].'跳转参数有误');
					}
					$data[$key] = $arr;
					$data[$key]['index']  = $value['index'];
					$data[$key]['name']  = $value['name'];
					$data[$key]['radiusTopLeft']  = $value['radiusTopLeft'];
					$data[$key]['radiusTopRight']  = $value['radiusTopRight'];
					$data[$key]['forcusPath']  = $value['forcusPath'];
					$data[$key]['normalPath']  = $value['normalPath'];
					$data[$key]['radiusBottomRight']  = $value['radiusBottomRight'];
					$data[$key]['radiusBottomLeft']  = $value['radiusBottomLeft'];
					$data[$key]['nextFocusLeftId']   =  isset($value['nextFocusLeftId'])?$value['nextFocusLeftId']:'';
					$data[$key]['nextFocusRightId']   =  isset($value['nextFocusRightId'])?$value['nextFocusRightId']:'';
					$data[$key]['nextFocusUpId']   =   isset($value['nextFocusUpId'])?$value['nextFocusUpId']:'';
					$data[$key]['nextFocusDownId']   =   isset($value['nextFocusDownId'])?$value['nextFocusDownId']:'';

				}

				if (!empty($data)) {
					$data = array_values($data);
					$put['attachment']['attacheInfo'] =  json_encode($data,JSON_UNESCAPED_UNICODE);
				}else{
					$put['attachment']['attacheInfo'] = '[]';
				}
			}

			//检查是否有快捷入口
			//{"attachment":{"style":"SIMPLE","isShowIndicator":"true/false","x";"x坐标","y":"y坐标","attacheId";"基础附件ID"}}
			if (!empty($put['quickEntry'])) {

				if (empty($put['quickEntry']['style']) || !($put['quickEntry']['isShowIndicator'] == 'false' || $put['quickEntry']['isShowIndicator'] == 'true') || !isset($put['quickEntry']['extraData'])) {
					result('快捷入口数据出错');
				}
				$data = array();
				$indexArr = array();
				foreach ($put['quickEntry']['extraData'] as $value) {
					$indexArr[] = $value['index'];
					$uniqueIndexArr[] = $value['index'];
				}
				$indexArr = array_unique($indexArr);
				if (count($indexArr) != count($put['quickEntry']['extraData'])) {
					result('快捷入口ID重复');
				}
				foreach ($put['quickEntry']['extraData'] as $key => $value) {
					if (!isset($value['type']) || !isset($value['name']) || !isset($value['normalPath']) || !isset($value['forcusPath'])  || !isset($value['itemY']) || !isset($value['itemX'])  ) {
						result('快捷入口ID为'.$value['index'].'参数有误');
					}
					$arr = /*$this->*/checkActionApp($value,true);
					if (empty($arr)) {
						result('快捷入口ID为'.$value['index'].'跳转参数有误');
					}
					$data[$key] = $arr;
					$data[$key]['index'] = $value['index'];
					$data[$key]['name'] = $value['name'];
					$data[$key]['forcusPath'] = $value['forcusPath'];
					$data[$key]['normalPath'] = $value['normalPath'];
					$data[$key]['itemY'] = $value['itemY'];
					$data[$key]['itemX'] = $value['itemX'];
					$data[$key]['nextFocusLeftId']  =  isset($value['nextFocusLeftId'])?$value['nextFocusLeftId']:'';
					$data[$key]['nextFocusRightId']  =  isset($value['nextFocusRightId'])?$value['nextFocusRightId']:'';
					$data[$key]['nextFocusUpId']  =   isset($value['nextFocusUpId'])?$value['nextFocusUpId']:'';
					$data[$key]['nextFocusDownId']  =   isset($value['nextFocusDownId'])?$value['nextFocusDownId']:'';
				}

				if (!empty($data)) {
					$data = array_values($data);
					$put['quickEntry']['quickInfo'] =  json_encode($data,JSON_UNESCAPED_UNICODE);
				}else{
					$put['quickEntry']['quickInfo'] = '[]';
				}
			}
			//检查是否有快捷入口组
			//{"attachment":{"style":"SIMPLE","isShowIndicator":"true/false","x";"x坐标","y":"y坐标","attacheId";"基础附件ID"}}
			if (!empty($put['quickEntryGroup'])) {
				$data = array();
				$indexArr = array();
				$indexNum = 0;
				if ( empty($put['quickEntryGroup']['mList']) || !is_array($put['quickEntryGroup']['mList']) ) {
					result('快捷入口组缺少快捷入口');
				}
				foreach ($put['quickEntryGroup']['mList'] as $key => $value) {

					if (empty($value['extra']) || !is_array($value['extra']) || !($value['layout'] == 'horizontal' || $value['layout'] == 'vertical') || !($value['direction'] == 'right' || $value['direction'] == 'left') || !isset($value['x']) || !isset($value['y']) || !isset($value['distance'])  || !isset($value['name']) ) {
						result('快捷入口组参数有误');
					}
					$arr = array();
					foreach ($value['extra'] as $k => $v) {
						if (!isset($v['index']) || empty($v['normalPath']) || empty($v['forcusPath'])) {
							result('快捷入口组控件参数有误');
						}
						$v['index'] = intval($v['index']);
						$indexArr[] = $v['index'];
						$uniqueIndexArr[] = $v['index'];

						$indexNum++;

						$arr[$k] = /*$this->*/checkActionApp($v,true);

						if (empty($arr[$k])) {
							result('快捷入口组控件跳转参数有误');
						}
						$arr[$k]['index'] = $v['index'];
						$arr[$k]['name'] = $v['name'];
						$arr[$k]['normalPath'] = $v['normalPath'];
						$arr[$k]['forcusPath'] = $v['forcusPath'];
						$arr[$k]['nextFocusLeftId']  =  isset($v['nextFocusLeftId'])?$v['nextFocusLeftId']:'';
						$arr[$k]['nextFocusRightId']  =  isset($v['nextFocusRightId'])?$v['nextFocusRightId']:'';
						$arr[$k]['nextFocusUpId']  =   isset($v['nextFocusUpId'])?$v['nextFocusUpId']:'';
						$arr[$k]['nextFocusDownId']  =   isset($v['nextFocusDownId'])?$v['nextFocusDownId']:'';
					}
					$put['quickEntryGroup']['mList'][$key]['quickInfo'] =  json_encode($arr,JSON_UNESCAPED_UNICODE);

				}
				//对比ID是否重复
				$indexArr = array_unique($indexArr);

				if (count($indexArr) != $indexNum) {
					result('快捷入口组ID重复');
				}

			}
			//检查是否有桌面时间
			if (!empty($put['timebar'])) {
				if (!isset($put['timebar']['timeFormat']) ||  !($put['timebar']['style'] == 'SIMPLE') || !($put['timebar']['isShowIndicator'] == 'false' || $put['timebar']['isShowIndicator'] == 'true') || !isset($put['timebar']['y']) || !isset($put['timebar']['x'])) {
					result('天气数据有误');
				}
			}

			/*$indexCount = count($uniqueIndexArr);
			$uniqueIndexArr = array_unique($uniqueIndexArr);
			if ($indexCount != count($uniqueIndexArr)) {
				result('快捷入口或三态快捷入口或两态快捷入口或底部快捷栏或附件栏ID重复');
			}*/

			$uniqueIndexArr = array_count_values($uniqueIndexArr);
			$indexStr = '';
			foreach ($uniqueIndexArr as $key => $value) {
				if ($value > 1) {
					$indexStr .= 'ID'.$key .'出现'.$value .'次，';
				}
			}
			if (!empty($indexStr)) {
				result('快捷入口或三态快捷入口或两态快捷入口或底部快捷栏或附件栏ID重复:'.$indexStr);
			}
			$data = array(
				'put'=>$put,
				'desktopInfo' => $desktopInfo
			);


			//判断桌面基础设置--导航
			if ($put['appConfig']['isCreateNavigation'] == 'true') {
				//创建导航配置文件
				if (empty($put['nav'])) {
					result('导航数据出错');
				}
			}
			//判断桌面基础设置--时间
			if ($put['appConfig']['isCreateTimeWidget'] == 'true' ) {
				//创建桌面时间配置文件
				if (empty($put['timebar'])) {
					result('时间数据出错');
				}
			}
			//判断桌面基础设置--天气
			if ($put['appConfig']['isCreateWeatherWidget'] == 'true'  ) {
				//创建桌面天气配置文件
				if (empty($put['weather'])) {
					result('天气数据出错');
				}
			}
			//判断桌面基础设置--时间天气
			if ($put['appConfig']['isCreateTimeWeather'] == 'true'  ) {
				//创建桌面天气配置文件
				if (empty($put['timeWeather'])) {
					result('时间天气数据出错');
				}
			}
			//判断桌面基础设置--附件
			if ($put['appConfig']['isCreateSlotAttachment'] == 'true') {
				//创建桌面附件文件
				if (empty($put['attachment'])) {
					result('附件数据出错');
				}
			}
			//判断桌面基础设置--Logo
			if ($put['appConfig']['isCreateLogoWidget'] == 'true') {
				//创建桌面LOGO配置文件
				if (empty($put['logo'])) {
					result('Logo数据出错');
				}

			}
			//判断桌面基础设置--快捷入口
			if ($put['appConfig']['isCreateQuickEntry'] == 'true' ) {
				//创建快捷入口配置文件

				if (empty($put['quickEntry'])) {
					result('快捷入口出错');
				}
			}
			//判断桌面基础设置--SN控件
			if ($put['appConfig']['isCreateSnWidget'] == 'true'  ) {
				//创建桌面SN文件
				if (empty($put['sn'])) {
					result('SN控件数据出错');
				}
			}
			return $data;
		}
		public function getDesktopForName($name)
		{
			return $this->where("`name`='%s'   and is_effective ='true'",array($name))->find();
		}
		public function getValForId($id)
		{
			return $this->find($id);
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getValForGroupId($groupId)
		{
			return $this->where("group_id = %d   and is_effective ='true' ",array($groupId))->find();
		}
		public function moveDesktop($put)
		{
			if (!empty($put['idLists'])&&is_array($put['idLists'])&&!empty($put['groupId'])) {
				$sqlStr = implode(',', $put['idLists']);
				$desktopArr = $this->where("id IN (".$sqlStr.")  and is_effective ='true' ")->select();
				if (count($put['idLists']) == count($desktopArr) ) {
					if (D('DesktopGroup','Desktop')->getValForId($put['groupId'])) {
						$options = array(
							'group_id'=>$put['groupId']
						);
						return $this->where("id IN (".$sqlStr.")  and is_effective ='true' ")->save($options);
					}else{
						result('桌面组不存在');
					}
				}else{
					result('桌面不存在');
				}
			}
		}
		public function moveRecycleDesktop($put)
		{
			if (!empty($put['idLists'])&&is_array($put['idLists'])&&!empty($put['groupId'])) {
				$sqlStr = implode(',', $put['idLists']);
				$desktopArr = $this->where("id IN (".$sqlStr.")  and is_effective ='true' ")->select();
				if (count($put['idLists']) == count($desktopArr) ) {
					if (D('DesktopGroup','Desktop')->getValForId($put['groupId'])) {
						$options = array(
							'group_id'=>$put['groupId']
						);
						return $this->where("id IN (".$sqlStr.")  and is_effective ='false' ")->save($options);
					}else{
						result('桌面组不存在');
					}
				}else{
					result('桌面不存在');
				}
			}
		}
		public function getDesktopNameForIdArr($put)
		{
			$res['extra'] = array();
			$sqlStr = '';
			$arr = array();
			if ( !empty($put) && is_array($put) ) {
				foreach ($put as $key => $value) {
					$value = intval($value);
					if ($value != 0) {
						$sqlStr .= $value . ',';
					}
				}
				$sqlStr = trim($sqlStr,',');
				$response = $this->field("id,name")->where("id IN (".$sqlStr.") ")->select();
				if (!empty($response)) {
					foreach ($response as $key => $value) {
						$arr[$value['id']] = $value['name'];
					}
				}
				foreach ($put as $key => $value) {
					$value = intval($value);
					if (!empty($arr[$value])) {
						$res['extra'][]= $arr[$value];
					}else{
						$res['extra'][] = new \stdClass();
					}
				}
			}
			return $res;
		}
		public function DesktopToFalse($put)
		{
			if (!empty($put['idLists'])&&is_array($put['idLists'])) {
				$sqlStr = implode(',', $put['idLists']);
				$desktopArr = $this->where("id IN (".$sqlStr.")")->select();
				if (count($put['idLists']) == count($desktopArr) ) {
					foreach ($desktopArr as $key => $value) {
						$options = array(
							'name'=>$value['name'].'_'.date('Y-m-d H:i:s',time()),
							'is_effective'=>'false',
						);
						$this->where("id = %d ",array($value['id']))->save($options);
					}
					return ;
				}else{
					result('桌面不存在');
				}
			}else{
				result('param');
			}
		}
		public function reductionDesktop($put)
		{
			if (!empty($put)&&is_array($put)) {
				$sqlStr = implode(',', $put);
				$desktopArr = $this->where("id IN (".$sqlStr.")  and is_effective ='false'")->select();
				$desktopGroupIdSql = $this->field('group_id')->where("id IN (".$sqlStr.")  and is_effective ='false'")->group('group_id')->select(false);
				if (count($put) == count($desktopArr) && !empty($desktopArr)) {
					$options = array(
						'is_effective'=>'true',
					);
					$desktopGroup = D('DesktopGroup','Desktop')->getArrForArrId($desktopGroupIdSql);
					if (!empty($desktopGroup)) {
						$reductionDesktopGroup = array();
						foreach ($desktopGroup as  $value) {
							$reductionDesktopGroup[$value['id']] =  $value['name'];
						}
						foreach ($desktopArr as $value) {
							if (!empty($reductionDesktopGroup[$value['group_id']])) {
								$reductionDesktopArrId[] = $value['id'];
							}else{
								$noReductionDesktopArrName[] = $value['name'];
							}
						}
						$sqlStr = implode(',', $reductionDesktopArrId);
						$this->where("id IN (".$sqlStr.")  and is_effective ='false'")->save($options);
						if (!empty($noReductionDesktopArrName)) {
							$noReductionDesktopArrNameStr = implode('、', $noReductionDesktopArrName);
							result('还原部分桌面失败：'.$noReductionDesktopArrNameStr .'所在桌面组已不存在');
						}else{
							return true;
						}
					}else{
						result('还原的桌面所在的桌面组不存在');
					}
				}else{
					result('回收站不存在该桌面');
				}
			}else{
				result('param');
			}
		}
		public function recycleLists($get)
		{
			$field = 'id,name,user,IF(`desc`= " ", "",`desc`) as `desc`,IF(`layout_update_time`= "0", FROM_UNIXTIME(`update_time`,"%Y-%m-%d %H:%i:%s"),FROM_UNIXTIME(`layout_update_time`,"%Y-%m-%d %H:%i:%s") ) as `layoutUpdateTme`,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime';
			if (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field($field)->limit($get['page'],$get['pageSize'])->where(" is_effective ='false' and ( `name` like '%".$get['name']."%'  or `desc` like '%".$get['name']."%' )")->order("layout_update_time DESC")->select();
				}else{
					// $res['extra'] = D('Desktop','Desktop')->field('id,name,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime')->select();
					$res['extra'] = $this->field('id,name,user,IF(`desc`= " ", "",`desc`) as `desc`')->where("is_effective ='false' and ( `name` like '%".$get['name']."%' or `desc` like '%".$get['name']."%' )")->order("layout_update_time DESC")->select();
				}
				$res['count'] = $this->where("is_effective ='false' and ( `name` like '%".$get['name']."%'  or `desc` like '%".$get['name']."%')")->count();
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field($field)->limit($get['page'],$get['pageSize'])->where(" is_effective ='false'")->order("layout_update_time DESC")->select();
				}else{
					// $res['extra'] = D('Desktop','Desktop')->field('id,name,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as createTime')->select();
					$res['extra'] = $this->field('id,name,user,IF(`desc`= " ", "",`desc`) as `desc`')->where("is_effective = 'false' ")->order("layout_update_time DESC")->select();
				}
				$res['count'] = $this->where(" is_effective ='false'")->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}

		public function isDesktopAttentionAppLists($get,$appLists)
		{

			$field = 'id,name';
			$publishNameSql = D("DesktopVersionPublish","Desktop")->getSqlModelForTypeAll();
			$where = "name IN (".$publishNameSql.")";
			if ($get['name']=='jbk') {
				$where .= "AND  is_effective = 'true' and `name` like '%_C' ";
			}elseif ($get['name']=='unjbk') {
				$where .= "AND  is_effective = 'true' and `name` NOT like '%_C' ";
			}


			/*if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$res = $this->field($field)->limit($get['page'],$get['pageSize'])->where($where)->select();
			}else{*/
			$res = $this->field($field)->where($where)->select();

			/*}*/

			if (empty($res)) {
				$res = array();
			}/*else{

				foreach ($res as $key => $value) {
					$pkgName = D("DesktopScreens","Desktop")->getAttentionAppCountForDesktopID($value['id']);
					$operationPkgName = D("DesktopScreens","Desktop")->getAttentionAppOperationCountForDesktopID($value['id']);
					$pkgNameArr = array_merge($pkgName,$operationPkgName);
					$pkgNameArr = array_unique($pkgNameArr);
					if (!empty($appLists)) {
						foreach ($appLists as $k => $v) {
							if (!empty($pkgNameArr)) {
								foreach ($pkgNameArr as $row) {
									if ($row == $v['pkgName']) {
										$res[$key]['appLists'][$k] = 'true';
										break;
									}else{
										$res[$key]['appLists'][$k] = 'false';
									}
								}
							}else{
								$res[$key]['appLists'][$k] = 'false';
							}
						}
					}
					unset($res[$key]['id']);
				}


			}*/

			return $res;
		}
		/**
		 * 桌面管理_桌面批量复制布局功能
		 * @param  [type] $put [description]
		 * @return [type]      [description]
		 */
		public function copyLayoutToDesktop($put)
		{
			if (empty($put['desktops']) || !is_array($put['desktops']) || empty($put['copyLayoutDesktopId'])) {
				result('param');
			}
			if (!$layoutDesktop = $this->getOneForId($put['copyLayoutDesktopId'])) {
				result('没有获取到布局数据');
			}
			//实例化
			$desktopScreens = D('DesktopScreens','Desktop');
			$desktopBlocks = D('DesktopBlocks','Desktop');
			$desktopBlockInfo = D('DesktopBlockInfo','Desktop');
			$desktopLogo = D('DesktopLogo','Desktop');
			$desktopBlockVideo = D('DesktopBlockVideo','Desktop');
			$desktopBlockPic = D('DesktopBlockPic','Desktop');
			$desktopNav = D('DesktopNav','Desktop');

			// $desktops = $this->deleteArrDesktopSlotNav($put['desktops']);
			//删除桌面坑位布局与坑位

			$sqlStr = implode(',', $put['desktops']);
			$desktopArr = $this->getArrForIdSql($sqlStr);
			if (count($put['desktops']) != count($desktopArr) ) {
				result('不存在该桌面');
			}
			$desktopScreensSql = $desktopScreens->getSqlIdForDesktopId($sqlStr);
			$desktopBlocksSql = $desktopBlocks->getSqlIdForDesktopScreensIdSql($desktopScreensSql);
			$desktopBlocksInfoSql= $desktopBlockInfo->getSqlIdArrForBlackId($desktopBlocksSql);
			$desktopLogoSql = $desktopLogo->getSqlIdForDesktopIdArr($sqlStr);
			//删除VIDEO
			$desktopBlockVideo->deleteDesktopBlockVideo($desktopBlocksInfoSql);
			//删除PIC
			$desktopBlockPic->deleteDesktopBlockPic($desktopBlocksInfoSql);
			$desktopBlockInfo->deleteDesktopBlockInfo($desktopBlocksSql);
			//删除桌面块
			$desktopBlocks->deleteDesktopBlocks($desktopScreensSql);
			//删除桌面屏
			$desktopScreens->deleteDesktopScreens($sqlStr);
			//删除桌面导航
			$desktopNav->deleteDesktopNav($sqlStr);

			//获取布局数据
			//获取桌面导航
			$layoutNav = $desktopNav->getValOneForDesktopId($layoutDesktop['id']);
			$layoutScreens = $desktopScreens->getArrForDesktopId($layoutDesktop['id']);
			if (!empty($layoutScreens)) {
				foreach ($layoutScreens as $key => $value) {
					$layoutScreens[$key]['blocks'] = $desktopBlocks->getArrForScreenId($value['id']);
					if (!empty($layoutScreens[$key]['blocks'])) {
						foreach ($layoutScreens[$key]['blocks'] as $k => $v) {
							$layoutScreens[$key]['blocks'][$k]['blocksInfo'] = $desktopBlockInfo->getArrForBlockId($v['id']);
							if (!empty($layoutScreens[$key]['blocks'][$k]['blocksInfo'])) {
								foreach ($layoutScreens[$key]['blocks'][$k]['blocksInfo'] as $a => $b) {
									$layoutScreens[$key]['blocks'][$k]['blocksInfo'][$a]['video'] = $desktopBlockVideo->getArrForBlockId($b['id']);
									$layoutScreens[$key]['blocks'][$k]['blocksInfo'][$a]['pic'] = $desktopBlockPic->getArrForBlockId($b['id']);
								}

							}
						}
					}
				}

			}
			foreach ($desktopArr as  $row) {
				$this->modifyDesktopTimeForId($row['id']);
				//添加桌面导航
				if (!empty($layoutNav)) {
					$arr = array(
						'desktopId' => $row['id'],
						'style'=>$layoutNav['style'],
						'isShowIndicator'=>$layoutNav['isShowIndicator'],
						'navInfo'=>$layoutNav['navInfo'],
						'x'=>$layoutNav['x'],
						'y'=>$layoutNav['y'],
						'name'=>$layoutNav['name'],
						'interval'=>$layoutNav['interval'],
					);
					$desktopNav->addDesktopNav($arr);
				}

				//添加桌面屏
				if (empty($layoutScreens)) {
					continue;
				}
				foreach ($layoutScreens as $key => $value) {
					$options = array(
						'desktopId'=>$row['id'],
						'slotGroupId'=>$value['slotGroupId'],
						'index'=>$value['index']
					);
					$desktopScreensId = $desktopScreens->addDesktopScreens($options);
					if (empty($value['blocks'])) {
						continue;
					}
					foreach ($value['blocks'] as  $v) {
						$options = array(
							'screenId'=>$desktopScreensId,
							'w'=>$v['w'],
							'h'=>$v['h'],
							'yw'=>$v['yw'],
							'yh'=>$v['yh'],
							'bg'=>$v['bg'],
							'x'=>$v['x'],
							'y'=>$v['y'],
							'slotId'=>$v['slotId'],
							'updateTime'=>time(),
							'nextFocusLeftId' => $v['nextFocusLeftId'],
							'nextFocusRightId' => $v['nextFocusRightId'],
							'nextFocusUpId' =>  $v['nextFocusUpId'],
							'nextFocusDownId' =>  $v['nextFocusDownId'],
						);
						$desktopBlocksId = $desktopBlocks->addDesktopBlocks($options);
						if (empty($v['blocksInfo'])) {
							continue;
						}

						foreach ($v['blocksInfo'] as  $a) {
							$options = array(
								'blockId'=>$desktopBlocksId,
								'title'=>$a['title'],
								'isEditable'=>$a['isEditable'],
								'disconnectEnable' =>$a['disconnectEnable'],
								'dataSource'=>$a['dataSource'],
								'layout'=>$a['layout'],
								'actionInfo'=>$a['actionInfo'],
								'appInfo'=>$a['appInfo'],
								'tag'=>$a['tag'],
								'operation'=>$a['operation'],
								'operationId'=>$a['operationId'],
							);
							$desktopBlockInfoId = $desktopBlockInfo->addDesktopBlockInfo($options);

							if (!empty($a['pic'])) {
								foreach ($a['pic'] as  $b) {
									$options = array(
										'pic'=> $b['pic'],
										'block_id' =>$desktopBlockInfoId
									);
									$desktopBlockPic->addDesktopBlockPic($options);
								}
							}
							if (!empty($a['video'])) {
								$videos = array();
								foreach ($a['video'] as  $b) {
									$videos[] = array(
										'url'=> $b['url'],
										'duration'=> $b['duration'],
										'block_id' =>$desktopBlockInfoId
									);
								}
								if (!empty($videos)) {
									$desktopBlockVideo->addDesktopBlockVideoArr($desktopBlockInfoId,$videos);
								}
							}

						}

					}
				}

			}
		}
		/**
		 * 41、批量删除桌面坑位与导航
		 * get /desktop/deleteDesktop?id=1
		 * return {"result":"ok/fail","reason":"xxx"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteArrDesktopSlotNav($put)
		{
			if (empty($put) || !is_array($put)) {
				result('param');
			}
			$sqlStr = implode(',', $put);
			$desktopArr = $this->getArrForIdSql($sqlStr);
			if (count($put) != count($desktopArr) ) {
				result('不存在该桌面');
			}
			$desktopScreensSql = D('DesktopScreens','Desktop')->getSqlIdForDesktopId($sqlStr);
			$desktopBlocksSql = D('DesktopBlocks','Desktop')->getSqlIdForDesktopScreensIdSql($desktopScreensSql);
			$desktopBlocksInfoSql= D('DesktopBlockInfo','Desktop')->getSqlIdArrForBlackId($desktopBlocksSql);
			$desktopLogoSql = D('DesktopLogo','Desktop')->getSqlIdForDesktopIdArr($sqlStr);
			//删除VIDEO
			D('DesktopBlockVideo','Desktop')->deleteDesktopBlockVideo($desktopBlocksInfoSql);
			//删除PIC
			D('DesktopBlockPic','Desktop')->deleteDesktopBlockPic($desktopBlocksInfoSql);
			D('DesktopBlockInfo','Desktop')->deleteDesktopBlockInfo($desktopBlocksSql);
			//删除桌面块
			D('DesktopBlocks','Desktop')->deleteDesktopBlocks($desktopScreensSql);
			//删除桌面屏
			D('DesktopScreens','Desktop')->deleteDesktopScreens($sqlStr);
			//删除桌面导航
			D('DesktopNav','Desktop')->deleteDesktopNav($sqlStr);
			return $desktopArr;
		}
		public function getArrForIdSql($sqlStr)
		{
			return $this->where("id IN (".$sqlStr.")")->select();
		}
	}