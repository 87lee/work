<?php
	namespace Home\Desktop;
	class DesktopShortCutsDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_short_cuts';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopId'=>'desktop_id',
			"keyCode"=>'key_code',		//是否需要导航栏
			  "extraData"=>'extra_data',		//是否需要时间控件
			  "appName"=>'app_name',	//是否需要天气控件
			  "detailName"=>'detail_name',	//是否创建坑位附件
			  "pkgName"=>'pkgname',		//是否创建Logo
			  "clsName"=>'clsname',		//是否创建快捷入口
		);
		public function addDesktopShortCuts($desktopId,$options)
		{
			foreach ($options as  $value) {
				$value['desktopId'] = $desktopId;

				if (isset($value['id'])) {
					unset($value['id']);
				}
				if ($aa = $this->create($value)) {
					$res[] = $aa;
				}
				// $this->add($res);
			}
			if (!empty($res)) {
				$this->addAll($res);
			}

		}
		public function deleteDesktopShortCuts($desktopId)
		{
			if ( $this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				return $this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}

		}
		public function deleteDesktopArrShortCuts($desktopId)
		{
			if ( $this->where("`desktop_id` IN (".$desktopId.")")->find()) {
				return $this->where("`desktop_id`  IN (".$desktopId.")")->delete();
			}

		}

		public function desktopShortCutsLists($desktopId)
		{
			$data=array();
			 // $res = $this->field('`id`,`desktop_id` as desktopId,`x`,`y`,`style`,`is_show_indicator` as isShowIndicator,attache_id as')->where("`desktop_id`=%d",array($desktopId))->find();
			 $res = $this->where("`desktop_id`=%d",array($desktopId))->select();
			if (!empty($res)) {
				foreach ($res as $key => $value) {

					$value['extraData'] = json_decode($value['extraData'],true);
					$actionData = checkActionApp($value);
					if (empty($actionData)) {
						if ($value['type'] != 'SCREEN') {
							// result('快捷键类型不存在');
							continue;
						}
						if (empty($value['sid'])) {
							// result('快捷键屏类型参数有误');
							continue;
						}
						$actionData = array(
							'type'=>$value['type'],
							'sid'=>$value['sid']
						);
					}
					$actionData['keyCode'] =  $value['keyCode'];
					// $actionData['id']=$value['id'];
					$data[] = $actionData;

					/*switch ($value['type']) {
						case 'ACTION':
							if (!empty($value['action'])&&!empty($value['appName'])&&!empty($value['detailName'])) {
								if (!empty($value['extraData'])) {
									$value['extraData'] = json_decode($value['extraData'],true);
									if (empty($value['extraData'])) {
										$value['extraData']= array();
									}
								}else{
									$value['extraData'] = array();
								}
								$data[] = array(
									'type'=>'ACTION',
									'keyCode'=>$value['keyCode'],
									'action'=>$value['action'],
									'appName'=>$value['appName'],
									'detailName'=>$value['detailName'],
									'extraData'=>$value['extraData'],
								);
							}
							break;
						case 'APP':
							if (!empty($value['pkgName'])&&!empty($value['appName'])) {
								$data[] = array(
									'type'=>'APP',
									'keyCode'=>$value['keyCode'],
									'pkgName'=>$value['pkgName'],
									'appName'=>$value['appName'],
								);
							}
							break;
						case 'COMPONENT':
							if (!empty($value['component'])&&!empty($value['clsName'])&&!empty($value['appName'])&&!empty($value['detailName'])) {
								if (!empty($value['extraData'])) {
									$value['extraData'] = json_decode($value['extraData'],true);
									if (empty($value['extraData'])) {
										$value['extraData']= array();
									}
								}else{
									$value['extraData'] = array();
								}
								$data[] = array(
									'type'=>'COMPONENT',
									'keyCode'=>$value['keyCode'],
									'component'=>$value['component'],
									'appName'=>$value['appName'],
									'detailName'=>$value['detailName'],
									'clsName'=>$value['clsName'],
									'extraData'=>$value['extraData'],
								);
							}
							break;
						case 'URI':
							if (!empty($value['uri'])) {
								$data[] = array(
									'type'=>'URI',
									'keyCode'=>$value['keyCode'],
									'uri'=>$value['uri'],
								);
							}
							break;
						case 'SCREEN':
							if (isset($value['sid'])) {
								$data[] = array(
									'type'=>'SCREEN',
									'keyCode'=>$value['keyCode'],
									'sid'=>$value['sid'],
								);
							}
							break;

						default:

							break;
					}*/
				}
			}
			return $data;
		}
		public function createDesktopShortCuts($desktopId=null,$desktop=null,$error = false)
		{
			$data = array();
			 // $res = $this->field('`id`,`desktop_id` as desktopId,`x`,`y`,`style`,`is_show_indicator` as isShowIndicator,attache_id as')->where("`desktop_id`=%d",array($desktopId))->find();
			 $res = $this->where("`desktop_id`=%d",array($desktopId))->select();
			if (!empty($res)) {
				foreach ($res as $key => $value) {
					$value['extraData'] = json_decode($value['extraData'],true);
					$value['index'] = $key+1;
					$actionData = desktopWidgetCheckAction($value,'快捷键',$error);
					if (!empty($actionData['reason'])) {
						if (empty($value['sid']) || $value['type'] != 'SCREEN') {
							if ($error) {
								return ['reason'=>'第'.($key+1).'快捷键参数有误'];
							}else{
								result('第'.($key+1).'快捷键参数有误');
							}
						}
						$actionData = array(
							'type'=>$value['type'],
							'screenData'=>array('sid'=>$value['sid'])
						);
					}

					$actionData['keyCode'] =  $value['keyCode'];
					// $actionData['id']=$value['id'];
					$data[] = $actionData;

					/*switch ($value['type']) {
						case 'ACTION':
							if (!empty($value['action'])&&!empty($value['appName'])&&!empty($value['detailName'])) {
								if (!empty($value['extraData'])) {
									$value['extraData'] = json_decode($value['extraData'],true);
									if (empty($value['extraData'])) {
										$value['extraData']= array();
									}
								}else{
									$value['extraData'] = array();
								}
								$data[] = array(
									'type'=>'ACTION',
									'keyCode'=>$value['keyCode'],
									'actionData'=>array(
										'action'=>$value['action'],
									),
									'extraData'=>$value['extraData'],
								);
							}
							break;
						case 'APP':
							if (!empty($value['pkgName'])&&!empty($value['appName'])) {
								$data[] = array(
									'type'=>'APP',
									'keyCode'=>$value['keyCode'],
									'appData'=>array(
										'pkgName'=>$value['pkgName'],
									)
								);
							}
							break;
						case 'COMPONENT':
							if (!empty($value['component'])&&!empty($value['clsName'])&&!empty($value['appName'])&&!empty($value['detailName'])) {
								if (!empty($value['extraData'])) {
									$value['extraData'] = json_decode($value['extraData'],true);
									if (empty($value['extraData'])) {
										$value['extraData']= array();
									}
								}else{
									$value['extraData'] = array();
								}
								$data[] = array(
									'type'=>'COMPONENT',
									'keyCode'=>$value['keyCode'],
									'componentData'=>array(
										'pkgName'=>$value['component'],
										'clsName'=>$value['clsName'],
									),

									'extraData'=>$value['extraData'],
								);
							}
							break;
						case 'URI':
							if (!empty($value['uri'])) {
								$data[] = array(
									'type'=>'URI',
									'keyCode'=>$value['keyCode'],
									'uriData'=>array(
										'uri'=>$value['uri'],
									),
								);
							}
							break;
						case 'SCREEN':
							if (isset($value['sid'])) {
								$data[] = array(
									'type'=>'SCREEN',
									'keyCode'=>$value['keyCode'],
									'screenData'=>array(
										'sid'=>$value['sid'],
									),
								);
							}
							break;

						default:

							break;
					}*/
				}
			}
			return $data;
		}

		public function modifyDesktopShortCuts($desktopId,$options){
			if ($this->where("`desktop_id` =%d ",array($desktopId))->find()) {
				$this->where("`desktop_id` =%d ",array($desktopId))->delete();
			}
			$this->addDesktopShortCuts($desktopId,$options);
		}
		public function getValOneForDesktopId($desktopId)
		{
			return $this->where("`desktop_id`=%d",array($desktopId))->find();
		}
	}