<?php
	namespace Home\Desktop;
	class ShortCutsItemsDesktop extends \Think\Model
	{
		protected $tableName = 'short_cuts_items';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'shortId'=>'short_id',
			"keyCode"=>'key_code',		//是否需要导航栏
			  "extraData"=>'extra_data',		//是否需要时间控件
			  "appName"=>'app_name',	//是否需要天气控件
			  "detailName"=>'detail_name',	//是否创建坑位附件
			  "pkgName"=>'pkgname',		//是否创建Logo
			  "clsName"=>'clsname',		//是否创建快捷入口
		);
		public function addShortCutsItems($put)
		{
			//检查桌面快捷键配置
			if (empty($put['shortId']) || !isset($put['keyCode']) || !isset($put['type'])) {
				result('param');
			}
			$res = $this->where("`short_id`=%d and `key_code`='%s'",array($put['shortId'],$put['keyCode']))->find();
			if ($res) {
				result('快捷键已存在：键值为'.$put['keyCode']);
			}
			$res = D('ShortCuts','Desktop')->getValOneForId($put['shortId']);
			if (!$res) {
				result('快捷键不存在');
			}
			$actionData = checkActionApp($put);
			if (empty($actionData)) {
				if ($put['type'] != 'SCREEN') {
					result('快捷键类型不存在');
				}
				if (empty($put['sid'])) {
					result('快捷键屏类型参数有误');
				}
				$actionData = array(
					'type'=>$put['type'],
					'sid'=>$put['sid']
				);
			}

			$data = array(
				'shortId'=>$put['shortId'],
				'keyCode'=>$put['keyCode'],
				'type'=>$actionData['type'],
				'action'=>$actionData['action'],
				'appName'=>isset($actionData['appName'])?$actionData['appName']:'',
				'detailName'=>isset($actionData['detailName'])?$actionData['detailName']:'',
				'extraData'=>isset($actionData['extraData'])?json_encode($actionData['extraData'],JSON_UNESCAPED_UNICODE):'[]',
				'clsName'=>isset($actionData['clsName'])?$actionData['clsName']:'',
				'component'=>isset($actionData['component'])?$actionData['component']:'',
				'pkgName'=>isset($actionData['pkgName'])?$actionData['pkgName']:'',
				'uri'=>isset($actionData['uri'])?$actionData['uri']:'',
				'sid'=>isset($actionData['sid'])?$actionData['sid']:'',
			);

			if (empty($data)) {
				result('param');
			}
			$this->create($data);
			$this->add();
			return true;

		}
		public function addAllShortCutsItems($put)
		{
			//检查桌面快捷键配置
			if (!empty($put['name'])&&!empty($put['extra'])&&is_array($put['extra'])) {
				$data = array();
				foreach ($put['extra'] as  $key => $value) {
					if (isset($value['keyCode']) && isset($value['type'])) {
						$keyCodeArr[] = $value['keyCode'];
						$actionData = checkActionApp($value);
						if (empty($actionData)) {

							if ($value['type'] != 'SCREEN') {
								result('快捷键类型不存在');
							}
							if (empty($value['sid'])) {
								result('快捷键屏类型参数有误');
							}
							$actionData = array(
								'type'=>$value['type'],
								'sid'=>$value['sid']
							);
						}

						$data[] = array(
							'keyCode'=>$value['keyCode'],
							'type'=>$actionData['type'],
							'action'=>$actionData['action'],
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
				}

				if (!empty($data)) {

					$shortCutsId = D('ShortCuts','Desktop')->addShortCuts($put);
					if ($shortCutsId) {
						$keyCodeUniqueArr = array_unique($keyCodeArr);
						if (count($keyCodeUniqueArr) !=count($keyCodeArr)) {
							result('快捷键键值重复');
						}
						foreach ($data as $key => $value) {
							$value['shortId'] = $shortCutsId;
							$res[] = $this->create($value);
						}
						$this->addAll($res);
						return true;
					}
				}else{
					result('param');
				}
			}else{
				result('param');
			}

		}

		public function deleteShortCutsItems($shortId=null,$id=null)
		{
			if ($shortId !=null) {
				if ($this->where("`short_id`=%d",array($shortId))->find()) {
					$this->where("`short_id`=%d",array($shortId))->delete();
				}
			}elseif ($id !=null) {
				if ($this->find($id)) {
					$this->delete($id);
				}else{
					result('该基础快捷键成员不存在');
				}
			}

			return true;
		}
		public function modifyShortCutsItems($put)
		{

			if (!empty($put['id'])&&isset($put['keyCode']) && isset($put['type'])) {
				$res = $this->find($put['id']);
				if (!$res) {
					result('该基础快捷键成员不存在');
				}
				$data = array();
				$actionData = checkActionApp($put);

				if (empty($actionData)) {

					if ($put['type'] != 'SCREEN') {
						result('快捷键类型不存在');
					}
					if (empty($put['sid'])) {
						result('快捷键屏类型参数有误');
					}
					$actionData = array(
						'type'=>$put['type'],
						'sid'=>$put['sid']
					);
				}

				$data = array(
					'shortId'=>$res['shortId'],
					'keyCode'=>$put['keyCode'],
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
				/*switch ($put['type']) {
					case 'ACTION':
						if (!empty($put['action'])&&!empty($put['appName'])&&!empty($put['detailName'])) {
							if (is_array($put['extraData'])) {
								foreach ($put['extraData'] as  $k => $item) {
									if (!isset($item['key']) || !isset($item['value'])) {
										unset($put['extraData']);
										break;
									}

									if (isset($item['type'])) {
										if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
											unset($put['extraData'][$k]['type']);
										}
									}
								}

								if (empty($put['extraData'])) {
									$put['extraData'] = array();
								}else{
									$put['extraData'] = array_values($put['extraData']);
								}
							}else{
								$put['extraData'] = array();
							}
							$put['extraData'] = json_encode($put['extraData'],JSON_UNESCAPED_UNICODE);

							$data = array(
								'type'=>'ACTION',
								'keyCode'=>$put['keyCode'],
								'action'=>$put['action'],
								'appName'=>$put['appName'],
								'detailName'=>$put['detailName'],
								'extraData'=>$put['extraData'],

								'clsName'=>'',
								'component'=>'',
								'pkgName'=>'',
								'uri'=>'',
								'sid'=>''
							);
						}
						break;
					case 'APP':
						if (!empty($put['pkgName'])&&!empty($put['appName'])) {

							$data = array(
								'type'=>'APP',
								'keyCode'=>$put['keyCode'],
								'action'=>'',
								'appName'=>$put['appName'],
								'detailName'=>'',
								'extraData'=>'',
								'clsName'=>'',
								'component'=>'',
								'pkgName'=>$put['pkgName'],
								'uri'=>'',
								'sid'=>'0'
							);
						}
						break;
					case 'COMPONENT':
						if (!empty($put['component'])&&!empty($put['clsName'])&&!empty($put['appName'])&&!empty($put['detailName'])) {
							if (is_array($put['extraData'])) {
								foreach ($put['extraData'] as  $k => $item) {
									if (!isset($item['key']) || !isset($item['value']) ) {
										unset($put['extraData']);
										break;
									}
									if (!($item['type'] == 'int' || $item['type'] == 'long' || $item['type'] == 'float'  || $item['type'] == 'double'  || $item['type'] == 'boolean' || $item['type'] == 'char'  || $item['type'] == 'string' )) {
										unset($put['extraData'][$k]['type']);
									}
								}
								if (empty($put['extraData'])) {
									$put['extraData'] = array();
								}else{
									$put['extraData'] = array_puts($put['extraData']);
								}
							}else{
								$put['extraData'] = array();
							}
							$put['extraData'] = json_encode($put['extraData'],JSON_UNESCAPED_UNICODE);

							$data = array(
								'keyCode'=>$put['keyCode'],
								'type'=>'COMPONENT',
								'action'=>'',
								'appName'=>$put['appName'],
								'detailName'=>$put['detailName'],
								'extraData'=>$put['extraData'],
								'clsName'=>$put['clsName'],
								'component'=>$put['component'],
								'pkgName'=>'',
								'uri'=>'',
								'sid'=>'0'
							);
						}
						break;
					case 'URI':
						if (!empty($put['uri'])) {


							$data = array(
								'type'=>'URI',
								'keyCode'=>$put['keyCode'],
								'action'=>'',
								'appName'=>'',
								'detailName'=>'',
								'extraData'=>'',
								'clsName'=>'',
								'component'=>'',
								'pkgName'=>'',
								'uri'=>$put['uri'],
								'sid'=>'0'
							);
						}
						break;
					case 'SCREEN':
						if (isset($put['sid'])) {

							$data = array(
								'type'=>'SCREEN',
								'keyCode'=>$put['keyCode'],
								'action'=>'',
								'appName'=>'',
								'detailName'=>'',
								'extraData'=>'',
								'clsName'=>'',
								'component'=>'',
								'pkgName'=>'',
								'uri'=>'',
								'sid'=>$put['sid']
							);
						}
						break;

					default:

						break;
				}*/

				if (empty($data)) {
					result('param');
				}
				$this->create($data);
				$this->where("`id` = %d",array($put['id']))->save();
				return true;
			}else{
				result('param');
			}
		}

		public function shortCutsLists($id=null,$shortId=null)
		{
			$data=array();
			if ($id != null) {
				$res = $this->where("`id`=%d",array($id))->find($id);
				if (!empty($res)) {
					$res['extraData'] = json_decode($res['extraData'],true);
					$actionData = checkActionApp($res);

					if ($res['type'] == 'SCREEN') {
						if (isset($res['sid'])) {
							$actionData = array(
								'type'=>'SCREEN',
								'sid'=>$res['sid'],
							);
						}
					}
					$actionData['keyCode'] =  $res['keyCode'];
					$actionData['id']=$value['id'];
					$data = $actionData;
					/*switch ($res['type']) {
						case 'ACTION':
							if (!empty($res['action'])&&!empty($res['appName'])&&!empty($res['detailName'])) {
								if (!empty($res['extraData'])) {
									$res['extraData'] = json_decode($res['extraData'],true);
									if (empty($res['extraData'])) {
										$res['extraData']= array();
									}
								}else{
									$res['extraData'] = array();
								}
								$data = array(
									'type'=>'ACTION',
									'keyCode'=>$res['keyCode'],
									'action'=>$res['action'],
									'appName'=>$res['appName'],
									'detailName'=>$res['detailName'],
									'extraData'=>$res['extraData'],
								);
							}
							break;
						case 'APP':
							if (!empty($res['pkgName'])&&!empty($res['appName'])) {
								$data = array(
									'type'=>'APP',
									'keyCode'=>$res['keyCode'],
									'pkgName'=>$res['pkgName'],
									'appName'=>$res['appName'],
								);
							}
							break;
						case 'COMPONENT':
							if (!empty($res['component'])&&!empty($res['clsName'])&&!empty($res['appName'])&&!empty($res['detailName'])) {
								if (!empty($res['extraData'])) {
									$res['extraData'] = json_decode($res['extraData'],true);
									if (empty($res['extraData'])) {
										$res['extraData']= array();
									}
								}else{
									$res['extraData'] = array();
								}
								$data = array(
									'type'=>'COMPONENT',
									'keyCode'=>$res['keyCode'],
									'component'=>$res['component'],
									'appName'=>$res['appName'],
									'detailName'=>$res['detailName'],
									'clsName'=>$res['clsName'],
									'extraData'=>$res['extraData'],
								);
							}
							break;
						case 'URI':
							if (!empty($res['uri'])) {
								$data = array(
									'type'=>'URI',
									'keyCode'=>$res['keyCode'],
									'uri'=>$res['uri'],
								);
							}
							break;
						case 'SCREEN':
							if (isset($res['sid'])) {
								$data = array(
									'type'=>'SCREEN',
									'keyCode'=>$res['keyCode'],
									'sid'=>$res['sid'],
								);
							}
							break;

						default:

							break;
					}*/
				}
			}elseif ($shortId != null) {
				$res = $this->where("`short_id`=%d",array($shortId))->select();
				if (!empty($res)) {
					foreach ($res as $key => $value) {
						$value['extraData'] = json_decode($value['extraData'],true);

						$actionData = checkActionApp($value);
						if ($value['type'] == 'SCREEN') {
							if (isset($value['sid'])) {
								$actionData = array(
									'type'=>'SCREEN',
									'sid'=>$value['sid'],
								);
							}
						}

						$actionData['keyCode'] =  $value['keyCode'];
						$actionData['id']=$value['id'];
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
										'id'=>$value['id'],
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
										'id'=>$value['id'],
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
										'id'=>$value['id'],
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
										'id'=>$value['id'],
										'type'=>'URI',
										'keyCode'=>$value['keyCode'],
										'uri'=>$value['uri'],
									);
								}
								break;
							case 'SCREEN':
								if (isset($value['sid'])) {
									$data[] = array(
										'id'=>$value['id'],
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
			}
			return $data;
		}
		public function getValForShortId($ShortId)
		{
			return $this->where("`short_id`=%d",array($ShortId))->select();
		}
	}