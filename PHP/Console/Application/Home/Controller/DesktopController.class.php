<?php
namespace Home\Controller;
	use Think\Controller;
	class DesktopController extends Controller {
		public function __construct()
	    	{
		        	parent::__construct();
		       	isLogin();
	    	}
		public function index()
		{
		}
		/**
	         	* 访问不存在的地址
	         	* @param  [type] $name [description]
	         	* @return [type]       [description]
	         	*/
	    	public function _empty($name){        //把所有城市的操作解析到city方法
	    		echo '{"result":"fail","reason":"当前地址不存在"}';
	    	}

		/**
		 * 桌面管理_添加基础数据快捷键
		 * [addShortCuts description]
		 */
		public function addShortCuts()
		{
			$put = I('put.');
			D('ShortCuts','Desktop')->addShortCuts($put);
			result();
		}
		/**
		 * 桌面管理_删除基础数据快捷键
		 * [addShortCuts description]
		 */
		public function deleteShortCuts()
		{
			$get = I('get.');
			D('ShortCuts','Desktop')->deleteShortCuts($get);
			result();
		}
		/**
		 * 桌面管理_基础数据快捷键列表
		 * [addShortCuts description]
		 */
		public function shortCutsLists()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				$res['extra'] = D('ShortCutsItems','Desktop')->shortCutsLists(null,$get['id']);
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$res = D('ShortCuts','Desktop')->shortCutsLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					$res = D('ShortCuts','Desktop')->shortCutsLists(null,$get['name']);
				}
			}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {

				$res = D('ShortCuts','Desktop')->shortCutsLists(null,null,$get['page'],$get['pageSize']);
			}else{
				$res = D('ShortCuts','Desktop')->shortCutsLists();
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_修改基础数据快捷键列表
		 * [addShortCuts description]
		 */
		public function modifyShortCuts()
		{
			$put = I('put.');
			D('ShortCuts','Desktop')->modifyShortCuts($put);
			result();
		}
		/**
		 * 桌面管理_修改基础数据快捷键成员
		 * [addShortCuts description]
		 */
		public function modifyShortCutsItems()
		{
			$put = I('put.');
			D('ShortCutsItems','Desktop')->modifyShortCutsItems($put);
			result();
		}
		/**
		 * 桌面管理_添加基础数据快捷键成员
		 * [addShortCuts description]
		 */
		public function addShortCutsItems()
		{
			$put = I('put.');
			D('ShortCutsItems','Desktop')->addShortCutsItems($put);
			result();
		}
		/**
		 * 桌面管理_基础数据快捷键成员列表
		 * [addShortCuts description]
		 */
		public function shortCutsItemsLists()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				$res['extra'] = D('ShortCutsItems','Desktop')->shortCutsLists($get['id']);
			}else{
				$res['extra'] = array();
			}

			result(true,$res);

		}
		/**
		 * 桌面管理_删除基础数据快捷键成员
		 * [addShortCuts description]
		 */
		public function deleteShortCutsItems()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('ShortCutsItems','Desktop')->deleteShortCutsItems(null,$get['id']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_添加基础数据快捷键全部成员
		 * [addShortCuts description]
		 */
		public function addAllShortCutsItems()
		{
			$put = I('put.');
			D('ShortCutsItems','Desktop')->addAllShortCutsItems($put);
			result();
		}
		/**
		 * 桌面管理_底部快捷栏发布桌面
		 * POST /desktop/shortCutsPublishDesktop
		 * POSTDATA:  {"desktopIDList":["1", "2"],"shortCutsId":"底部快捷栏ID","type":"group/ALL","groupId":"组ID"}  PS：当type=group时，groupId存在
		 * @return {"result":"failed/ok", failList:[{"desktopName":"a", "reason":"生成版本失败/发布版本失败，请检查后手动发布"}]}
		 *
		 */
		public function shortCutsPublishDesktop()
		{
		 	set_time_limit(0);
			$put = I('put.');
			if (is_array($put['desktopIDList'])&&!empty($put['desktopIDList'])&&!empty($put['shortCutsId'])&&($put['type']=='group' || $put['type']=='ALL' )) {

				if ($put['type']=='group') {
					if (!empty($put['groupId'])) {
						$group = D('Group')->find($put['groupId']);
						if (!$group) {
							$msg[] = array(
								'desktopName'=>'组不存在，请选择正确组',
								'reason'=>''
							);
							result('publish',$msg);
						}
					}
				}
				$shortCuts = D('ShortCutsItems','Desktop')->getValForShortId($put['shortCutsId']);
				if (empty($shortCuts)) {
					$msg[] = array(
						'desktopName'=>'快捷键不存在',
						'reason'=>''
					);
					result('publish',$msg);
				}
				$desktopIdLists = '';
				foreach ($put['desktopIDList'] as $key => $value) {
					$value = (int)$value;
					$desktopIdLists .=is_int($value)?$value.',':result('param');
				}

				$desktopIdLists = trim($desktopIdLists,',');

				$desktopLists = D('Desktop','Desktop')->getOperationDesktopLists($desktopIdLists);
				if (!empty($reason)) {
					foreach ($desktopLists as $key => $value) {
						$msg[] = array(
							'desktopName'=>$value['name'],
							'reason'=>$reason
						);
					}
					result('publish',$msg);
				}
				foreach ($desktopLists as $key => $value) {
					D('DesktopShortCuts','Desktop')->modifyDesktopShortCuts($value['id'],$shortCuts);
				}
				D('Desktop','Desktop')->modifySourceVersion($desktopIdLists);

				$this->autoPublishDesktop($put);
			}else{
				$msg[] = array(
					'desktopName'=>'参数有误',
					'reason'=>''
				);
				result('publish',$msg);
			}

		}
		/**
		 * 桌面管理_修改海外桌面接口
		 * [modifyHYDesktop 修改海外桌面接口]
		 * {
		 *	"desktopID":"桌面名称ID",
		 * 	"blocks":[
		 *		{
		 *			"title":"块标题",
		 *			"slotId":"0",（自定义块ID）
		 *			"isEditable":"true/false",（是否可编辑）
		 *			"layout":"IMAGE/APP/VIDEO/APP_CENTER_IMG_BOTTOM_TEXT",（布局类型）
		 *			"actionData":{
		 *				"type": "ACTION/APP/COMPONENT/URI",
		 *				"extraData": [{ "key": "key","value": "value"}],(类型为ACTION、COMPONENT的时候有)
		 *
		 *				"action": "this.is.action.name",(类型为ACTION)
		 *
		 *				"pkgName": "this.is.package.name",(类型为APP)
		 *
		 *				"clsName": "this.is.package.name",(类型为COMPONENT)
		 *				"component": "this.is.class.name",(类型为COMPONENT)
		 *
		 *				"uri": "this.is.uri",(类型为URI)
		 *
		 *				"appInfo":{"path":"1231","pkgName":"111","versionCode":"2132"}}(类型为APP，有插槽绑定时)
		 *			"pic":"数据类型管理图片链接",
		 *			"videos":[{"url":"http://xx", "duration":234}] （布局类型为）
		 *		}
		 *	]
		 *}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 * {"desktopID":"test1","blocks":[{"actionData": {"type": "APP","pkgName": "com.ktcp.video","appName": "腾讯视频渠道包"},"title": "","isEditable": "false","dataSource": "linkin","layout": "IMAGE","pic": "http://resrc-test.oss-cn-qingdao.aliyuncs.com/pic/85e705c85e7e3046a8c120d20be743d0.png", "slotId": "101"}]}
		 */
		public function modifyHYDesktop()
		{
			$put = I('put.');
			if (!empty($put['desktopID']) && !empty($put['blocks']) ) {
				$put['type'] = 'ALL';
				if (!is_array($put['blocks'])) {
					result('param');
				}
				$res = D('Desktop','Desktop')->getDesktopForName($put['desktopID']);

				if (!$res) {
					result('桌面'.$put['desktopID'].'不存在');
				}

				//检查修改数据

				$desktopScreensSql = D('DesktopScreens','Desktop')->getScreenIdSqlForDesktopId($res['id']);
				$desktopBlocks = D('DesktopBlocks','Desktop')->getIdAndSlotIdForDesktopScreensIdSql($desktopScreensSql);

				$slotArr = array();
				foreach ($desktopBlocks as $key => $value) {
					$slotArr[$value['slotId']] = $value;
				}
				foreach ($put['blocks'] as $key => $value) {
					if (empty($value['slotId']) || !($value['isEditable'] == 'false' || $value['isEditable'] == 'true')  || empty($value['layout']) || empty($value['pic']) || empty($value['actionData'])) {
						if (empty($value['slotId'])) {
							result('没有坑位ID存在');
						}else{
							// var_dump(!($value['isEditAble'] == 'false' || $value['isEditAble'] == 'true' ) );
							result('坑位'.$value['slotId'].'数据出错');
						}
					}
					if (empty($slotArr[$value['slotId']])) {
						result('坑位'.$value['slotId'].'不存在');
					}
					if (empty($value['actionData']['type'])) {
						result('坑位'.$value['slotId'].'跳转数据类型不存在');
					}
					switch ($value['actionData']['type']) {
						case 'APP':
							if (isset($value['actionData']['pkgName'])) {
								$data[$key]['actionInfo'] = array(
									'type'=>$value['actionData']['type'],
									'pkgName'=>$value['actionData']['pkgName'],
								);
							}else{
								result('坑位'.$value['slotId'].'跳转数据APP类型出错');
							}
							break;
						case 'ACTION':
							if (isset($value['actionData']['action'])) {
								if (!is_array($value['actionData']['extraData'])) {
									$value['actionData']['extraData'] = array();
								}else{
									foreach ($value['actionData']['extraData'] as $item) {
										if (!isset($item['key']) || !isset($item['value']) ) {
											$value['actionData']['extraData'] = array();
											break;
										}
									}
								}
								$data[$key]['actionInfo'] = array(
									'type'=>$value['actionData']['type'],
									'action'=>$value['actionData']['action'],
									'extraData' => $value['actionData']['extraData'],
								);
							}else{
								result('坑位'.$value['slotId'].'跳转数据ACTION类型出错');
							}
							break;
						case 'COMPONENT':
							if (isset($value['actionData']['clsName'])/*&&isset($value['actionData']['extraData'])*/&&isset($value['actionData']['component'])) {
								if (!is_array($value['actionData']['extraData'])) {
									$value['actionData']['extraData'] = array();
								}else{
									foreach ($value['actionData']['extraData'] as $item) {
										if (!isset($item['key']) || !isset($item['value']) ) {
											$value['actionData']['extraData'] = array();
											break;
										}
									}
								}
								$data[$key]['actionInfo'] = array(
									'type'=>$value['actionData']['type'],
									'component'=>$value['actionData']['component'],
									'clsName'=>$value['actionData']['clsName'],
									'extraData' => $value['actionData']['extraData'],
								);

							}else{
								result('坑位'.$value['slotId'].'跳转数据COMPONENT类型出错');
							}
							break;
						case 'URI':
							if (isset($value['actionData']['uri'])) {
								$data[$key]['actionInfo']  = array(
									'type'=>$value['actionData']['type'],
									'uri'=>$value['actionData']['uri'],
									// 'extraData' => $v['actionData']['extraData']
								);
							}else{
								result('坑位'.$value['slotId'].'跳转数据URI类型出错');
							}
							break;

						default:
							result('坑位'.$value['slotId'].'跳转数据类型不存在出错');
							break;
					}
					if (!empty($value['actionData']['appInfo'])) {
						if (!isset($value['actionData']['appInfo']['path'])||!isset($value['actionData']['appInfo']['pkgName'])||!isset($value['actionData']['appInfo']['versionCode'])) {
							unset($put['blocks'][$key]['actionData']['appInfo']);
						}else{
							$data[$key]['appInfo'] = $value['actionData']['appInfo'];
						}
					}
					if ($value['layout'] == 'VIDEO') {
						if (is_array($value['videos'])) {
							foreach ($value['videos'] as $item) {
								if (empty($item['duration']) || empty($item['url'])) {
									result($value['slotId'].'坑位视频类型数据出错');
								}
							}
							$data[$key]['videos'] = $value['videos'];
						}else{
							result('坑位'.$value['slotId'].'布局类型为VIDEO，但没有VIDEO数据');
						}
					}
					if (empty($value['pic'])) {
						result($value['slotId'].'坑位参数图片出错');
					}
					unset($put['blocks'][$key]['actionData']);
				}
				//修改桌面坑位
				foreach ($put['blocks'] as $key => $value) {
					if(!empty($data[$key]['actionInfo'])){
						$value['actionInfo'] =json_encode($data[$key]['actionInfo'],JSON_UNESCAPED_UNICODE);
					}
					if(!empty($data[$key]['appInfo'])){
						$value['appInfo'] =json_encode($data[$key]['appInfo'],JSON_UNESCAPED_UNICODE);
					}
					$blockInfoOptions = array(
						'dataSource'=>'linkin',
						'actionInfo'=>!empty($value['actionInfo'])?$value['actionInfo']:'{}',
						'appInfo'=>!empty($value['appInfo'])?$value['appInfo']:'{}',
						'title'=>!empty($value['title'])?$value['title']:'',
						'isEditable'=>$value['isEditable'],
						'layout'=>$value['layout'],
						'operationId'=>'0',
						'operation'=>'false',
					);
					$desktopBlockInfoId = D('DesktopBlockInfo','Desktop')->modifyDesktopBlockInfo($slotArr[$value['slotId']]['id'],$blockInfoOptions);
					//桌面坑位图片
					if ($value['layout'] == 'VIDEO') {
						D('DesktopBlockVideo','Desktop')->modifyDesktopBlockVideo($desktopBlockInfoId,$value['videos']);
					}else{
						D('DesktopBlockVideo','Desktop')->deleteDesktopBlockVideo($desktopBlockInfoId);
					}
					$picOptions = array(
						'pic'=> $value['pic'],
					);
					D('DesktopBlockPic','Desktop')->modifyDesktopBlockPic($desktopBlockInfoId,$picOptions);
					D('Desktop','Desktop')->modifyDesktop($res['id'],$put['desktopID']);
				}
				//生成桌面版本
				$desktopSourceFiles = $this->createDesktopVersion($res['id'],$res,true);
				if (!empty($desktopSourceFiles['reason'])) {
					if ($desktopSourceFiles[$key]['reason'] != '该桌面没有修改') {
						result("生成桌面版本失败:".$desktopSourceFiles['reason']);
					}
				}
				//发布桌面版本
				$version = D('DesktopVersion','Desktop')->getValOneForModel($res['name']);
				$publish = D('DesktopVersionPublish','Desktop')->getValAllForModelForType($res['name'],$put['type']);
				if ($publish) {
					foreach ($publish as $k => $v) {
						//先删除发布版本
						//去除桌面旧接口发布 start
						D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($v['id']);
						unset($v['id']);
						$v['reason'] = '下架';
						D('DesktopVersionPublishHistory','Desktop')->addDesktopVersionPublishHistory($v);
						$put['id']=$version['id'];
						//添加发布版本
						$res = $this->publishDesktopVersion($put,$version,true);
						//去除桌面旧接口发布 end
						//桌面旧接口发布 start

						/*$json = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/del?item=desktop3.0&id='.$v['id']);
						if ($json) {
							$res = json_decode($json,true);
							if ($res['result'] !='ok') {
								if (strstr($res['reason'],'not such id')!=true) {
									result("发布桌面版本失败:".$res['reason']);
								}else{
									D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($v['id']);
									unset($v['id']);
									$v['reason'] = '下架';
									D('DesktopVersionPublishHistory','Desktop')->addDesktopVersionPublishHistory($v);
									$put['id']=$version['id'];
									//添加发布版本
									$res = $this->publishDesktopVersion($put,$version,true);
									if (!empty($res['reason'])) {
										if ($res['reason'] != '已发布') {
											result("发布桌面版本失败:".$res['reason']);
										}
									}
								}

							}else{
								D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($v['id']);
								unset($v['id']);
								$v['reason'] = '下架';
								D('DesktopVersionPublishHistory','Desktop')->addDesktopVersionPublishHistory($v);
								$put['id']=$version['id'];
								//添加发布版本
								$res = $this->publishDesktopVersion($put,$version,true);
								if (!empty($res['reason'])) {
									if ($res['reason'] != '已发布') {
										result("发布桌面版本失败:".$res['reason']);
									}
								}
							}
						}else{
							result("发布桌面版本失败，发布路径出错");

						}*/
						//桌面旧接口发布 end
					}
				}else{
					$put['id']=$version['id'];
					//添加发布版本
					$res = $this->publishDesktopVersion($put,$version,true);
					if (!empty($res['reason'])) {
						if ($res['reason'] != '已发布') {
							if ($res['reason'] == 'duplicate') {
								result("发布桌面版本失败:桌面发布重复，请先手动删除");
							}else{
								result("发布桌面版本失败:".$res['reason']);
							}


						}

					}
				}
			}else{
				result('param');
			}
			result();
		}
		/**
		 * 桌面管理_底部快捷栏发布桌面
		 * POST /desktop/quickListsPublishDesktop
		 * POSTDATA:  {"desktopIDList":["1", "2"],"quickListId":"底部快捷栏ID","type":"group/ALL","groupId":"组ID"}  PS：当type=group时，groupId存在
		 * @return {"result":"failed/ok", failList:[{"desktopName":"a", "reason":"生成版本失败/发布版本失败，请检查后手动发布"}]}
		 *
		 */
		public function quickListsPublishDesktop()
		{
		 	set_time_limit(0);
			$put = I('put.');
			if (is_array($put['desktopIDList'])&&!empty($put['desktopIDList'])&&!empty($put['quickListId'])&&($put['type']=='group' || $put['type']=='ALL' )) {

				if ($put['type']=='group') {
					if (!empty($put['groupId'])) {
						$group = D('Group')->find($put['groupId']);
						if (!$group) {
							$msg[] = array(
								'desktopName'=>'组不存在，请选择正确组',
								'reason'=>''
							);
							result('publish',$msg);
						}
					}
				}

				$quickList = D('QuickListItems','Desktop')->getValForQuickListId($put['quickListId']);
				if (empty($quickList)) {

					$desktopIdLists = '';
					foreach ($put['desktopIDList'] as $key => $value) {
						$value = (int)$value;
						$desktopIdLists .=is_int($value)?$value.',':result('param');
					}
					$desktopIdLists = trim($desktopIdLists,',');

					$desktopLists = D('Desktop','Desktop')->getOperationDesktopLists($desktopIdLists);
					D('DesktopAppConfig','Desktop')->desktopAppConfigToTrue($desktopIdLists,array('isCreateQuickList'=>'true'));

					D('DesktopQuickList','Desktop')->deleteDesktopQuickListArr($desktopIdLists);

					D('Desktop','Desktop')->modifySourceVersion($desktopIdLists);

				}else{
					$desktopIdLists = '';
					foreach ($put['desktopIDList'] as $key => $value) {
						$value = (int)$value;
						$desktopIdLists .=is_int($value)?$value.',':result('param');
					}
					$desktopIdLists = trim($desktopIdLists,',');

					$desktopLists = D('Desktop','Desktop')->getOperationDesktopLists($desktopIdLists);

					D('DesktopAppConfig','Desktop')->desktopAppConfigToTrue($desktopIdLists,array('isCreateQuickList'=>'true'));
					foreach ($desktopLists as $key => $value) {
						D('DesktopQuickList','Desktop')->modifyDesktopQuickList($value['id'],$quickList);
					}
					D('Desktop','Desktop')->modifySourceVersion($desktopIdLists);
				}

				$this->autoPublishDesktop($put);
			}else{
				$msg[] = array(
					'desktopName'=>'参数有误',
					'reason'=>''
				);
				result('publish',$msg);
			}

		}
		/**
		 * 桌面管理_添加底部快捷栏
		 * [addQuickLists 添加底部快捷栏]
		 *
		 * {
		 * "name":"底部快捷栏名称",
		 * "extra":[{
		 * 	"index": 0, // 底部栏的 item 最多有 7 个，id范围是 0~6
		 *  	"pkgName": "com.linkin.apps" // 应用的包名
		 *   	"appIcon": "app.png", // 应用的显示图标
		 *    	"appName": "应用", // 应用的显示名称
		 *     	"apkUrl": "http://121.42.137.110:3000" // apk下载路径
		 * }]
		 *     }
		 * @param string $value [description]
		 */
		public function addQuickLists()
		{
			$put = I('put.');
			if (!empty($put['name'])) {
				if (!empty($put['extra'])) {
					foreach ($put['extra'] as $key => $value) {
						if (!isset($value['versionCode']) || !isset($value['index']) || !isset($value['pkgName']) || !isset($value['appIcon']) || !isset($value['appName']) || !isset($value['apkUrl'])) {
							result('param');
						}
						if (empty($value['title'])) {
							$put['extra'][$key]['title']='';
						}
					}

					$quickListId = D('QuickList','Desktop')->addQuickList($put['name']);
					D('QuickListItems','Desktop')->addQuickListArr($quickListId,$put['extra']);
				}else{
					D('QuickList','Desktop')->addQuickList($put['name']);
				}
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_删除底部快捷栏成员
		 * get /desktop/deleteQuickLists?id=底部快捷栏ID
		 * @return return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteQuickLists()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				D('QuickListItems','Desktop')->deleteQuickListArr($get['id']);
				D('QuickList','Desktop')->deleteQuickList($get['id']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_底部快捷栏成员列表
		 * get /desktop/quickLists?page=xxx&pageSize=xx
		 * get /desktop/quickLists?page=xxx&pageSize=xx&name=xx
		 * get /desktop/quickLists?id=底部快捷栏ID
		 * @param string $value [description]
		 */

		public function quickLists(){
			$get = I('get.');
			if (!empty($get['id'])) {
				$res = D('QuickListItems','Desktop')->quickLists($get['id']);
				$quickList = D('QuickList','Desktop')->quickLists($get['id']);
				if (!empty($quickList)) {
					$res['id']=$quickList['id'];
					$res['name']=$quickList['name'];
				}
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$res = D('QuickList','Desktop')->quickLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					$res = D('QuickList','Desktop')->quickLists(null,$get['name']);
				}
			}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {

				$res = D('QuickList','Desktop')->quickLists(null,null,$get['page'],$get['pageSize']);
			}else{
				$res = D('QuickList','Desktop')->quickLists();
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_修改底部快捷栏成员
		 * post /desktop/modifyQuickLists
		 *  {
		 *  "id":"底部快捷栏名称",
		 *  "name":"底部快捷栏名称",
		 *  "extra":[{
		 *  "index": 0, // 底部栏的 item 最多有 7 个，id范围是 0~6
		 *  "pkgName": "com.linkin.apps" // 应用的包名
		 *  "appIcon": "app.png", // 应用的显示图标
		 *  "appName": "应用", // 应用的显示名称
		 *  "apkUrl": "http://121.42.137.110:3000" // apk下载路径
		 *  }]
		 *      }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */

		public function modifyQuickLists()
		{
			$put = I('put.');
			if (!empty($put['name'])&&!empty($put['id'])) {
				if (!empty($put['extra'])) {
					foreach ($put['extra'] as $key => $value) {
						if (!isset($value['versionCode']) || !isset($value['index']) || !isset($value['pkgName']) || !isset($value['appIcon']) || !isset($value['appName']) || !isset($value['apkUrl'])) {
							result('param');
						}
						if (empty($value['title'])) {
							$put['extra'][$key]['title']='';
						}
					}
					$quickListId = D('QuickList','Desktop')->modifyQuickList($put['id'],$put['name']);
					D('QuickListItems','Desktop')->modifyQuickListArr($quickListId,$put['extra']);
				}else{
					D('QuickList','Desktop')->modifyQuickList($put['id'],$put['name']);
					D('QuickListItems','Desktop')->deleteQuickListArr($put['id']);
				}
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_添加命令组
		 *
		 * post /desktop/addCmdLine
		 *
		 * {
		 * "name":"名称",
		 * "cmd":["命令行"]
		 * }
		 */
		public function addCmdLine()
		{
			$put = I('put.');
			D('Cmd','Desktop')->addCmdLine($put);
			result();
		}
		/**
		 * 桌面管理_修改命令组
		 *
		 * post /desktop/modifyCmdLine
		 *
		 * {
		 * "id":"1",
		 * "name":"名称",
		 * "cmd":["命令行"]
		 * }
		 * @return [type] [description]
		 */
		public function modifyCmdLine()
		{
			$put = I('put.');
			D('Cmd','Desktop')->modifyCmdLine($put);
			result();
		}
		/**
		 * 桌面管理_删除命令组
		 *
		 * get /desktop/deleteCmdLine?id=x
		 * @param string $value [description]
		 */
		public function deleteCmdLine()
		{
			$get = I('get.');
			D('Cmd','Desktop')->deleteCmdLine($get['id']);
			result();
		}
		/**
		 * 桌面管理_命令行列表
		 *
		 * get /desktop/cmdLineLists?page=x&pageSize=x
		 * get /desktop/cmdLineLists?page=x&pageSize=x&name=x
		 * @param string $value [description]
		 */
		public function cmdLineLists()
		{
			$get = I('get.');
			$res = D('Cmd','Desktop')->cmdLineLists($get);
			result(true,$res);
		}
		//---------------------------------------------------------------------------------------------------------------------------
		/**
		 * 桌面管理_添加桌面命令
		 * post /desktop/addCmd
		 * post{"desktop2":"桌面ID","desktop3":"映射桌面ID"}
		 *@return  {"result":"ok/fail","reason":"xxx"}
		 */
		public function addCmd()
		{
			$put = I('put.');
			D('DesktopCmd','Desktop')->addCmd($put);
			result();

		}
		/**
		 * 桌面管理_修改桌面命令
		 * post /desktop/modifyCmd
		 * post {"id":"1","desktop2":"桌面ID","desktop3":"映射桌面ID"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		/*public function modifyCmd()
		{
			$put = I('put.');
			D('DesktopCmd','Desktop')->addCmd($put);
			result();
		}*/
		/**
		 * 桌面管理_删除桌面命令
		 * get /desktop/deleteCmd?id=1,2,3
		 * post /desktop/deleteCmd
		 * {"desktopMaps":["1","2"]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteCmd()
		{
			$get = I('get.');
			$put = I('put.');
			if (isset($get['id'])) {
				D('DesktopCmd','Desktop')->deleteCmd($get['id']);
			}elseif(!empty($put['desktopMaps'])){
				D('DesktopCmd','Desktop')->deleteCmd(null,$put['desktopMaps']);
			}else{
				result('param');
			}
			result();
		}
		/**
		 * 桌面管理_桌面命令行列表
		 * get /desktop/cmdLists?name=xxx&page=x&pageSize=x    搜索vendorid 或 desc
		 * get /desktop/cmdLists?page=x&pageSize=x
		 * @param  [type] $id       [description]
		 * @param  [type] $name     [description]
		 * @param  [type] $page     [description]
		 * @param  [type] $pageSize [description]
		 * @return [type]   [description]
		 */
		public function cmdLists(){
			$get = I('get.');
			$res = D('DesktopCmd','Desktop')->cmdLists($get);
			result(true,$res);
		}
		//---------------------------------------------------------------------------------------------------------------------------
		/**
		 * 桌面管理_添加桌面映射
		 * post /desktop/addDesktopMap
		 * post{"desktop2":"桌面ID","desktop3":"映射桌面ID"}
		 *@return  {"result":"ok/fail","reason":"xxx"}
		 */
		public function addDesktopMap()
		{
			$put = I('put.');
			D('DesktopMap','Desktop')->addDesktopMap($put);
			result();

		}
		/**
		 * 桌面管理_修改桌面映射
		 * post /desktop/modifyDesktopMap
		 * post {"id":"1","desktop2":"桌面ID","desktop3":"映射桌面ID"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyDesktopMap()
		{
			$put = I('put.');

			D('DesktopMap','Desktop')->modifyDesktopMap($put);

			result();

		}
		/**
		 * 桌面管理_删除桌面映射
		 * get /desktop/deleteDesktopMap?id=1,2,3
		 * post /desktop/deleteDesktopMap
		 * {"desktopMaps":["1","2"]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteDesktopMap()
		{
			$get = I('get.');
			$put = I('put.');
			if (isset($get['id'])) {
				D('DesktopMap','Desktop')->deleteDesktopMap($get['id']);
			}elseif(!empty($put['desktopMaps'])){
				D('DesktopMap','Desktop')->deleteDesktopMap(null,$put['desktopMaps']);
			}else{
				result('param');
			}
			result();
		}
		/**
		 * 桌面管理_桌面映射列表
		 * get /desktop/desktopMapLists?name=xxx&page=x&pageSize=x    搜索vendorid 或 desc
		 * get /desktop/desktopMapLists?page=x&pageSize=x
		 * @param  [type] $id       [description]
		 * @param  [type] $name     [description]
		 * @param  [type] $page     [description]
		 * @param  [type] $pageSize [description]
		 * @return [type]           [description]
		 */
		public function desktopMapLists($id = null,$name= null ,$page=null,$pageSize=null){
			$get = I('get.');
			if (!empty($get['id'])) {
				D('DesktopMap','Desktop')->desktopMapLists($get['id']);
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('DesktopMap','Desktop')->desktopMapLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					D('DesktopMap','Desktop')->desktopMapLists(null,$get['name']);
				}
			}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {

				D('DesktopMap','Desktop')->desktopMapLists(null,null,$get['page'],$get['pageSize']);

			}else{
				D('DesktopMap','Desktop')->desktopMapLists();
			}
		}

		/**
		 * 桌面管理_添加桌面密码管理
		 * post /desktop/addJbk
		 * post{"vendorid":"客户ID","passwd":"密码","desc":"描述"}
		 *@return  {"result":"ok/fail","reason":"xxx"}
		 */
		public function addJbk()
		{
			$put = I('put.');
			if (isset($put['vendorid'])&&isset($put['passwd'])) {
				if (empty($put['desc'])) {
					$put['desc']='';
				}
				D('DesktopJbkPasswd','Desktop')->addJbk($put['vendorid'],$put['passwd'],$put['desc']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_修改客户桌面密码管理
		 * post /desktop/modifyJbk
		 * post {"id":"1","vendorid":"客户ID","passwd":"密码","desc":"描述"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyJbk()
		{
			$put = I('put.');
			if (isset($put['id'])&&isset($put['vendorid'])&&isset($put['passwd'])) {
				if (empty($put['desc'])) {
					$put['desc']='';
				}
				D('DesktopJbkPasswd','Desktop')->modifyJbk($put['id'],$put['vendorid'],$put['passwd'],$put['desc']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_删除客户桌面密码管理
		 * get /desktop/deleteJbk?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteJbk()
		{
			$get = I('get.');
			if (isset($get['id'])) {
				D('DesktopJbkPasswd','Desktop')->deleteJbk($get['id']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_桌面密码管理列表
		 * get /desktop/jbkLists?name=xxx&page=x&pageSize=x    搜索vendorid 或 desc
		 * get /desktop/jbkLists?page=x&pageSize=x
		 *  get /desktop/jbkLists?mac=x
		 * @param  [type] $id       [description]
		 * @param  [type] $name     [description]
		 * @param  [type] $page     [description]
		 * @param  [type] $pageSize [description]
		 * @return [type]           [description]
		 */
		public function jbkLists($id = null,$name= null ,$page=null,$pageSize=null,$mac=null){
			$get = I('get.');
			if (!empty($get['id'])) {
				D('DesktopJbkPasswd','Desktop')->jbkLists($get['id']);
			}elseif (!empty($get['name'])) {

				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('DesktopJbkPasswd','Desktop')->jbkLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					D('DesktopJbkPasswd','Desktop')->jbkLists(null,$get['name']);
				}

			}elseif (!empty($get['mac'])) {
				D('DesktopJbkPasswd','Desktop')->jbkLists(null,null,null,null,$get['mac']);
			}elseif (!empty($get['page'])&&!empty($get['pageSize'])) {
				D('DesktopJbkPasswd','Desktop')->jbkLists(null,null,$get['page'],$get['pageSize']);

			}else{
				D('DesktopJbkPasswd','Desktop')->jbkLists();
			}
		}

		//----------------------------------------------------------------
		/**
		 * 桌面管理_添加桌面越狱
		 * post /desktop/addDesktopJbk
		 * post
		 * 	{
		 * 		"province":"省份",
		 * 		"operator":"运营商",
		 * 		"source":"yunos 云/android 非云",
		 * 		"action":"0 不可越狱/1 越狱"
		 * 	}
		 *@return  {"result":"ok/fail","reason":"xxx"}
		 */
		public function addDesktopJbk()
		{
			$put = I('put.');
			D('DesktopJbk','Desktop')->addDesktopJbk($put);
			result();

		}
		/**
		 * 桌面管理_修改桌面越狱
		 * post /desktop/modifyDesktopJbk
		 * 	{
		 * 		"id":"1",
		 * 		"province":"省份",
		 * 		"operator":"运营商",
		 * 		"source":"yunos 云/android 非云",
		 * 		"action":"0 不可越狱/1 越狱"
		 * 	}
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyDesktopJbk()
		{
			$put = I('put.');

			D('DesktopJbk','Desktop')->modifyDesktopJbk($put);
			result();

		}
		/**
		 * 桌面管理_删除桌面越狱
		 * get /desktop/deleteDesktopJbk?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteDesktopJbk()
		{
			$get = I('get.');
			D('DesktopJbk','Desktop')->deleteDesktopJbk($get);
			result();

		}
		/**
		 * 桌面管理_桌面越狱管理列表
		 * get /desktop/jbkDesktopLists?name=xxx&page=x&pageSize=x
		 *
		 * @param  [type] $id       [description]
		 * @param  [type] $name     [description]
		 * @param  [type] $page     [description]
		 * @param  [type] $pageSize [description]
		 * @return [type]           [description]
		 */
		public function jbkDesktopLists(){
			$get = I('get.');
			$res = D('DesktopJbk','Desktop')->jbkDesktopLists($get);
			result(true,$res);
		}
		/**
		 * [getAllProvinceName 获取所有的省份]
		 * get  /desktop/getAllProvinceName
		 * @return [type] [description]
		 */
		public function getAllProvinceName()
		{
			$res = D('Province','Desktop')->getAllName();
			result(true,$res);
		}
		/**
		 * [getAllProvinceName 获取所有的运营商]
		 * get  /desktop/getAllOperatorName
		 * @return [type] [description]
		 */
		public function getAllOperatorName()
		{
			$res = D('Operator','Desktop')->getAllName();
			result(true,$res);
		}
		//----------------------------------------------------------------
		/**
		 * 桌面管理_基础数据添加附件
		 * post /desktop/addAttachment
		 * {"name":"附件名称","x":"x坐标","y":"y坐标","extra":[{"name":"附件","normal":"正常图标","forcus":"焦点图标","radiusTopLeft":"左上角是否圆角","radiusTopRight":"右上角是否圆角","radiusBottomLeft":"左下角是否圆角","radiusBottomRghit":"右下角是否圆角","actionId":"操作类型ID"}]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addAttachment()
		{
			$put = I('put.');
			if (!empty($put['name'])&&isset($put['x'])&&isset($put['y'])&&isset($put['interval'])) {

				$res = D('Attachment','Desktop')->getValOneForName($put['name']);
				if ($res) {
					result('附件已存在');
				}
				if (!empty($put['extra'])) {
					// {"name":"附件","iconId":"1","radiusTopLeft":"左上角是否圆角","radiusTopRight":"右上角是否圆角","radiusBottomLeft":"左下角是否圆角","radiusBottomRight":"右下角是否圆角","actionId":"操作类型ID"}
					foreach ($put['extra'] as $key => $value) {
						if (empty($value['name']) || !isset($value['radiusTopLeft']) || !isset($value['radiusTopRight']) || !isset($value['radiusBottomLeft']) || !isset($value['radiusBottomRight'])) {
							result('第'.($key+1).'附件参数有误');
						}
						if (!isset($value['forcusPath']) || !isset($value['normalPath'])) {
							result('第'.($key+1).'附件图片参数有误');
						}else{
							$put['extra'][$key]['forcusPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['forcusPath']);
							$put['extra'][$key]['normalPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['normalPath']);
						}

						$data = checkActionApp($value);
						if ($data) {
							$data = json_encode($data,JSON_UNESCAPED_UNICODE);
							$put['extra'][$key]['actionInfo'] = $data;
						}else{
							result('第'.($key+1).'附件跳转参数有误');
						}
					}
					$attachmentId = D('Attachment','Desktop')->addAttachment($put['name'],$put['x'],$put['y'],$put['interval']);
					if (!empty($put['extra'])) {
						D('AttachmentItems','Desktop')->addAttachmentItems($attachmentId,$put['extra']);
					}
				}else{
					$attachmentId = D('Attachment','Desktop')->addAttachment($put['name'],$put['x'],$put['y'],$put['interval']);
				}
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除附件
		 * get /desktop/deleteAttachment?id=111
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteAttachment()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('AttachmentItems','Desktop')->deleteAttachmentItems($id);
				D('Attachment','Desktop')->deleteAttachment($id);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据附件列表
		 * get /desktop/attachmentLists
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function attachmentLists()
		{
			$id = I('get.id');
			if (!empty($id)) {
				$res = D('AttachmentItems','Desktop')->attachmentLists($id);
			}else{
				$res = D('AttachmentItems','Desktop')->attachmentLists();
			}
			result(true,$res);
		}

		/**
		 * 桌面管理_基础数据修改附件
		 * post /desktop/addAttachment
		 * {"id":"1","name":"附件名称","x":"x坐标","y":"y坐标","extra":[{"name":"附件","normal":"正常图标","forcus":"焦点图标","radiusTopLeft":"左上角是否圆角","radiusTopRight":"右上角是否圆角","radiusBottomLeft":"左下角是否圆角","radiusBottomRghit":"右下角是否圆角","actionId":"操作类型ID"}]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */

		public function modifyAttachment()
		{
			$put = I('put.');
			if (!empty($put['id'])&&!empty($put['name'])&&isset($put['x'])&&isset($put['y'])&&isset($put['interval'])) {
				$res = D('Attachment','Desktop')->getValOneForNameForNotId($put['name'],$put['id']);
				if ($res) {
					result('附件名称已存在');
				}
				$res = D('Attachment','Desktop')->getValOneForId($put['id']);
				if (!$res) {
					result('附件不存在');
				}
				if (!empty($put['extra'])) {

					// {"name":"附件","iconId":"1","radiusTopLeft":"左上角是否圆角","radiusTopRight":"右上角是否圆角","radiusBottomLeft":"左下角是否圆角","radiusBottomRight":"右下角是否圆角","actionId":"操作类型ID"}
					foreach ($put['extra'] as $key => $value) {
						if (empty($value['name']) || !isset( $value['radiusTopLeft']) || !isset($value['radiusTopRight']) || !isset($value['radiusBottomLeft']) || !isset($value['radiusBottomRight'])) {
							result('第'.($key+1).'附件参数有误');
						}

						if (!isset($value['forcusPath']) || !isset($value['normalPath'])) {
							result('第'.($key+1).'附件图片参数有误');
						}else{
							$put['extra'][$key]['forcusPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['forcusPath']);
							$put['extra'][$key]['normalPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['normalPath']);
						}

						$data = checkActionApp($value);

						if ($data) {
							$data = json_encode($data,JSON_UNESCAPED_UNICODE);
							$put['extra'][$key]['actionInfo'] = $data;
						}else{
							result('第'.($key+1).'附件跳转参数有误');
						}
					}
					$attachmentId = $res['id'];
					D('Attachment','Desktop')->modifyAttachment($put['id'],$put['name'],$put['x'],$put['y'],$put['interval']);
					D('AttachmentItems','Desktop')->modifyAttachmentItems($attachmentId,$put['extra']);
				}else{
					$attachmentId = D('Attachment','Desktop')->modifyAttachment($put['id'],$put['name'],$put['x'],$put['y'],$put['interval']);
					D('AttachmentItems','Desktop')->deleteAttachmentItems($put['id']);
				}
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据添加快捷入口
		 * post /desktop/addQuickEntry
		 * {"name":"附件名称","x":"x坐标","interval":"1","y":"y坐标","extra":[{"name":"附件","iconId":"图标ID","actionId":"操作类型ID"}]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addQuickEntry()
		{
			$put = I('put.');
			if (!empty($put['name'])) {

				$res = D('QuickEntry','Desktop')->getValOneForName($put['name']);
				if ($res) {
					result('快捷入口已存在');
				}
				if (!empty($put['extra'])) {

					// {"name":"附件","iconId":"1","radiusTopLeft":"左上角是否圆角","radiusTopRight":"右上角是否圆角","radiusBottomLeft":"左下角是否圆角","radiusBottomRight":"右下角是否圆角","actionId":"操作类型ID"}
					foreach ($put['extra'] as $key => $value) {
						if (!isset($value['name']) ) {
							result('param');
						}
						if (!isset($value['forcusPath']) || !isset($value['normalPath']) || !isset($value['itemX']) ||!isset($value['itemY']) ) {
							result('第'.($key+1).'快捷入口图片参数有误');
						}else{
							$put['extra'][$key]['forcusPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['forcusPath']);
							$put['extra'][$key]['normalPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['normalPath']);
						}

						$data = checkActionApp($value);
						if ($data) {
							$data = json_encode($data,JSON_UNESCAPED_UNICODE);
							$put['extra'][$key]['actionInfo'] = $data;
						}else{
							result('第'.($key+1).'快捷入口图片参数有误');
						}
						if (!empty($put['extra'][$key]['id'])) {
							unset($put['extra'][$key]['id']);
						}
					}
					$quickEntryId = D('QuickEntry','Desktop')->addQuickEntry($put['name'],$put['x'],$put['y'],$put['interval']);
					D('QuickEntryItems','Desktop')->addQuickEntryItems($quickEntryId,$put['extra']);
				}else{
					$quickEntryId = D('QuickEntry','Desktop')->addQuickEntry($put['name'],$put['x'],$put['y'],$put['interval']);
				}

				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除快捷入口
		 * get /desktop/deleteQuickEntry?id=111
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteQuickEntry()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('QuickEntryItems','Desktop')->deleteQuickEntryItems($id);
				D('QuickEntry','Desktop')->deleteQuickEntry($id);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据快捷入口列表
		 * get /desktop/quickEntryLists
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function quickEntryLists()
		{
			$id = I('get.id');

			if (!empty($id)) {
				$res = D('QuickEntryItems','Desktop')->quickEntryLists($id);
			}else{
				$res = D('QuickEntryItems','Desktop')->quickEntryLists();
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据修改快捷入口
		 * post /desktop/addQuickEntry
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyQuickEntry()
		{
			$put = I('put.');
			if (!empty($put['id'])&&!empty($put['name'])) {

				$res = D('QuickEntry','Desktop')->getValOneForNameForNotId($put['name'],$put['id']);

				if ($res) {
					result('附件名称已存在');
				}
				$res = D('QuickEntry','Desktop')->getValOneForId($put['id']);
				if (!$res) {
					result('附件不存在');
				}
				if (!empty($put['extra'])) {

					foreach ($put['extra'] as $key => $value) {
						if (!isset($value['name']) ) {
							result('第'.($key+1).'快捷入口参数有误');
						}
						if (!isset($value['forcusPath']) || !isset($value['normalPath']) || !isset($value['itemX']) || !isset($value['itemY'])) {
							result('第'.($key+1).'快捷入口图片参数有误');
						}else{
							$put['extra'][$key]['forcusPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['forcusPath']);
							$put['extra'][$key]['normalPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['normalPath']);
						}
						$data = checkActionApp($value);
						if ($data) {
							$data = json_encode($data,JSON_UNESCAPED_UNICODE);
							$put['extra'][$key]['actionInfo'] = $data;
						}else{
							result('第'.($key+1).'快捷入口跳转参数有误');
						}
						if (!empty($put['extra'][$key]['id'])) {
							unset($put['extra'][$key]['id']);
						}
					}

					$quickEntryId = $res['id'];
					D('QuickEntry','Desktop')->modifyQuickEntry($put['id'],$put['name'],$put['x'],$put['y'],$put['interval']);
					D('QuickEntryItems','Desktop')->modifyQuickEntryItems($quickEntryId,$put['extra']);
				}else{
					$quickEntryId = D('QuickEntry','Desktop')->modifyQuickEntry($put['id'],$put['name'],$put['x'],$put['y'],$put['interval']);
					D('QuickEntryItems','Desktop')->deleteQuickEntryItems($put['id']);
				}
				result();
			}else{
				result('param');
			}
		}
		//-//********************************************************************************************
		/**
		 * 桌面管理_基础数据添加快捷入口组
		 * post /desktop/addQuickEntryGroup
		 *
		 * {
		 * "id":"快捷入口组ID",
		 * "name":"快捷入口组名称",
		 * "mList": [
		 * {
		 * "x": 1000,                    #快捷入口组x坐标
  		 *  "y": 100,                     #快捷入口组y坐标
  		 *  "direction": "right",         #伸展方向(左：left/右：right)
  	    	 *  "distance":50,                #每个控件间的间距
  		 *  "layout":"horizontal",        #布局（水平：horizontal/垂直:vertical）
    		 *  "name":"快捷入口名称",
    		 *  "extra": [
            		 *  {
                     	 *  "name": "附件",
                     	 *  "normalPath": "正常显示图片相对路径",
            	     	 *  "forcusPath": "焦点显示图片相对路径",
                     	 *  "type": "ACTION/APP/COMPONENT/URI",
                     	 *  "extraData": [
                	 *  {
                    	 *  "key": "key",
                    	 *  "value": "value",
		 *  "type": "int/long/float/double/boolean/char/string"
                	 *  }
            		 *  ],
            		 *  (类型为ACTION)"action": "this.is.action.name",
            		 *  (类型为APP)"pkgName": "this.is.package.name",
            		 *  (类型为COMPONENT)"clsName": "this.is.package.name",
            		 *  "pkgName": "应用名字",
            		 *  "detailName": "详情名字",
            		 *  "applName": "详情名字",
            		 *  "component": "this.is.class.name",
            		 *  (类型为URI)"uri": "this.is.uri"
        		 *  }
    		 *  ]
		 *  }
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addQuickEntryGroup()
		{
			$put = I('put.');
			if (!empty($put['name'])) {
				$res = D('QuickEntryGroup','Desktop')->getOneForName($put['name']);
				if ($res) {
					result('快捷入口组已存在');
				}
				if (!empty($put['mList']) && is_array($put['mList'])) {
					foreach ($put['mList'] as  $key => $value) {
						if ( !( $value['direction'] == 'right' || $value['direction'] =='left') || !isset($value['x']) || !isset($value['y']) ||  !isset($value['distance']) || !($value['layout'] =='horizontal' || $value['layout'] =='vertical')) {
							result('第'.($key+1).'快捷入口组参数有误');
						}
						if (!empty($put['mList'][$key]['id'])) {
							unset($put['mList'][$key]['id']);
						}
						$value['x'] = intval($value['x']);
						$value['y'] = intval($value['y']) ;
						$value['distance'] = intval($value['distance']);
						if (empty($value['name'])) {
							$put['mList'][$key]['name'] = '';
						}
						if (empty($value['extra']) || !is_array($value['extra'])) {
							result('第'.($key+1).'快捷入口组参数有误');
						}
						if (empty($value['extra']) || !is_array($value['extra'])) {
							result('第'.($key+1).'快捷入口第'.($k+1).'跳转参数有误');
						}
						foreach ($value['extra'] as $k => $v) {
							if (!isset($v['forcusPath']) || !isset($v['normalPath'])  ) {
								result('第'.($key+1).'快捷入口第'.($k+1).'图片参数有误');
							}
							if (empty($v['name'])) {
								$value['extra'][$k]['name'] = '';
							}
							$value['extra'][$k]['forcusPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$v['forcusPath']);
							$value['extra'][$k]['normalPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$v['normalPath']);

							$data = checkActionApp($v);

							if (!empty($data)) {
								$data = json_encode($data,JSON_UNESCAPED_UNICODE);
								$value['extra'][$k]['actionInfo'] = $data;
							}else{
								if (!isset($v['forcusPath']) || !isset($v['normalPath'])  ) {
									result('第'.($key+1).'快捷入口第'.($k+1).'跳转参数有误');
								}
							}
						}

						$quickEntryGroupId = D('QuickEntryGroup','Desktop')->addQuickEntryGroup($put['name']);
						if (empty($quickEntryGroupId)) {
							result('添加快捷入口组失败');
						}
						$quickEntryGroupItemsId = D('QuickEntryGroupItems','Desktop')->addQuickEntryGroupItems($quickEntryGroupId,$put['mList'][$key]);
						if ($quickEntryGroupItemsId) {
							D('QuickEntryGroupContent','Desktop')->addQuickEntryGroupContent($quickEntryGroupItemsId,$value['extra']);
						}

					}
				}else{
					$quickEntryGroupId = D('QuickEntryGroup','Desktop')->addQuickEntryGroup($put['name']);
				}

				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除快捷入口组
		 * get /desktop/deleteQuickEntryGroup?id=111
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteQuickEntryGroup()
		{
			$get = I('get.');
			D('QuickEntryGroup','Desktop')->deleteQuickEntryGroup($get);
			result();
		}
		/**
		 * 桌面管理_基础数据快捷入口组列表
		 * get /desktop/getQuickEntryGroupLists?name=x&page=x&pageSize=x
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function getQuickEntryGroupLists()
		{
			$get = I('get.');
			$res = D('QuickEntryGroup','Desktop')->getQuickEntryGroupLists($get);
			$quickEntryGroupIdSql = D('QuickEntryGroup','Desktop')->getQuickEntryGroupSqlForWhere($get);
			if (!empty($res['extra'])) {
				$quickEntryGroupItems = D('QuickEntryGroupItems','Desktop')->getQuickEntryGroupItemsListsForGroupId($quickEntryGroupIdSql);

				$quickEntryGroupItemsIdSql = D('QuickEntryGroupItems','Desktop')->getQuickEntryGroupItemsIdSqlForGroupId($quickEntryGroupIdSql);

				if (!empty($quickEntryGroupItems)) {
					$quickEntryGroupContents = D('QuickEntryGroupContent','Desktop')->getQuickEntryGroupContentListsForGroupId($quickEntryGroupItemsIdSql);
					foreach ($quickEntryGroupItems as  $key => $value) {
						foreach ($quickEntryGroupContents as $k => $v) {
							if ($value['id'] === $v['groupItemId']) {
								unset($v['groupItemId']);
								unset($v['id']);
								$quickEntryGroupItems[$key]['extra'][] = $v;

								unset($quickEntryGroupContents[$k]);
							}
						}
					}
				}
				foreach ($res['extra'] as $key => $value) {
					if (!empty($quickEntryGroupItems)) {
						foreach ($quickEntryGroupItems as $k => $v) {
							if ($value['id'] === $v['groupId']) {
								unset($v['groupId']);
								unset($v['id']);
								$res['extra'][$key]['mList'][] = $v;

								unset($quickEntryGroupItems[$k]);
							}
						}
					}else{

						$res['extra'][$key]['mList'] = array();
					}
				}
			}

			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据修改快捷入口
		 * post /desktop/addQuickEntry
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyQuickEntryGroup()
		{
			$put = I('put.');
			if (!empty($put['name'])&&!empty($put['id'])) {
				$quickEntryGroupId = D('QuickEntryGroup','Desktop')->modifyQuickEntryGroup($put);
				if (empty($quickEntryGroupId)) {
					result('添加快捷入口组失败');
				}
				D('QuickEntryGroupItems','Desktop')->deleteQuickEntryGroupItemsForGroupId($quickEntryGroupId);

				if (!empty($put['mList']) && is_array($put['mList'])) {
					// {"name":"附件","iconId":"1","radiusTopLeft":"左上角是否圆角","radiusTopRight":"右上角是否圆角","radiusBottomLeft":"左下角是否圆角","radiusBottomRight":"右下角是否圆角","actionId":"操作类型ID"}
					foreach ($put['mList'] as  $key => $value) {
						if ( !( $value['direction'] == 'right' || $value['direction'] =='left') || !isset($value['x']) || !isset($value['y']) ||  !isset($value['distance']) || !($value['layout'] =='horizontal' || $value['layout'] =='vertical')) {
							result('第'.($key+1).'快捷入口组参数有误');
						}
						if (!empty($put['mList'][$key]['id'])) {
							unset($put['mList'][$key]['id']);
						}
						$value['x'] = intval($value['x']);
						$value['y'] = intval($value['y']) ;
						$value['distance'] = intval($value['distance']);
						if (empty($value['name'])) {
							$put['mList'][$key]['name'] = '';
						}
						if (empty($value['extra']) || !is_array($value['extra'])) {
							result('第'.($key+1).'快捷入口第'.($k+1).'跳转参数有误');
						}

						foreach ($value['extra'] as $k => $v) {
							if (!isset($v['forcusPath']) || !isset($v['normalPath'])  || !isset($v['normalPath']) ) {
								result('第'.($key+1).'快捷入口第'.($k+1).'图片参数有误');
							}
							if (empty($v['name'])) {
								$value['extra'][$k]['name'] = '';
							}
							$value['extra'][$k]['forcusPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$v['forcusPath']);
							$value['extra'][$k]['normalPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$v['normalPath']);

							$data = checkActionApp($v);

							if (!empty($data)) {
								$data = json_encode($data,JSON_UNESCAPED_UNICODE);
								$value['extra'][$k]['actionInfo'] = $data;
							}else{
								result('第'.($key+1).'快捷入口第'.($k+1).'跳转参数有误');
							}
						}
						if (empty($value['extra'])) {
							unset($put['mList'][$key]);
							continue;
						}
						$quickEntryGroupItemsId = D('QuickEntryGroupItems','Desktop')->addQuickEntryGroupItems($quickEntryGroupId,$put['mList'][$key]);
						if ($quickEntryGroupItemsId) {
							D('QuickEntryGroupContent','Desktop')->addQuickEntryGroupContent($quickEntryGroupItemsId,$value['extra']);
						}
					}
				}
				result();
			}else{
				result('param');
			}
		}
		//---------------------------------------------------------------------------------------------------------------------------------------------------------------
		/**
		 * 桌面管理_基础数据添加两态快捷入口
		 * post /desktop/addQuickEntryTwoState
		 * {"name":"附件名称",
		 * "extra":[{
  		 * "name": "wifi",
        		 * "x": 116,
        		 * "y": 497,
        		 * "eventType": "NETWORK",
		 * "activeDrawable": "image    active 为 WIFI 已连接、ETH 已连接、USB 已插入、MSG 有信息的 图片连接",
        		 * "normalDrawable": "image     normal 为 WIFI 没连接、ETH 没连接、USB 没插入、MSG 无信息的 图片连接",
        		 * "focusedActiveDrawable": "image   获取焦点并且是 active 的时候",
        		 * "focusedNormalDrawable": "image   获取焦点并且是 normal 的时候",
        		 *
		 * "type": "APP/COMPONENT/ACTION/URI",
		 * "extraData": [{
		 *    	"key": "key",
		 *   	"value": "value"
		 * }],
		 *
		 * "action": "this.is.action.name" ,
		 * "appName": "应用名" ,
		 * "detailName": "详情名" ,
		 *
		 * "pkgName": "包名" ,
		 * "appName": "应用名" ,
		 *
		 * "component": "com.linkin.setting",
		 * "clsName": "com.linkin.setting2.detail.NetworkActivity",
		 * "appName": "应用名" ,"detailName": "详情名",
		 * "detailName": "详情名" ,
		 *
		 * "uri": "this.is.uri"
		 *
		 *}]
		 *}
		 *]}
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addQuickEntryTwoState()
		{
			$put = I('put.');
			if (!empty($put['name'])&&isset($put['x'])&&isset($put['y'])) {

				$res = D('QuickEntryTwoState','Desktop')->getValOneForName($put['name']);
				if ($res) {
					result('两态快捷入口已存在');
				}
				if (!empty($put['extra'])) {

					// {"name":"附件","iconId":"1","radiusTopLeft":"左上角是否圆角","radiusTopRight":"右上角是否圆角","radiusBottomLeft":"左下角是否圆角","radiusBottomRight":"右下角是否圆角","actionId":"操作类型ID"}
					foreach ($put['extra'] as $key => $value) {
						if (!empty($put['extra'][$key]['id'])) {
							unset($put['extra'][$key]['id']);
						}
						if (empty($value['name'])  || !isset($value['x']) || !isset($value['y']) || empty($value['eventType']) || empty($value['activeDrawable']) || empty($value['normalDrawable']) || empty($value['focusedActiveDrawable'])  || empty($value['focusedNormalDrawable']) || empty($value['type']) ) {
							result('第'.($key+1).'两态快捷入口参数有误');
						}else{
							$put['extra'][$key]['activeDrawable'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['activeDrawable']);
							$put['extra'][$key]['normalDrawable'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['normalDrawable']);
							$put['extra'][$key]['focusedActiveDrawable'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedActiveDrawable']);
							$put['extra'][$key]['focusedNormalDrawable'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedNormalDrawable']);
						}
						$data = checkActionApp($value);
						if ($data) {
							$data = json_encode($data,JSON_UNESCAPED_UNICODE);
							$put['extra'][$key]['actionInfo'] = $data;
						}else{
							result('第'.($key+1).'两态快捷入口跳转参数有误');
						}
					}
					$quickEntryId = D('QuickEntryTwoState','Desktop')->addQuickEntry($put['name'],$put['x'],$put['y']);
					if (!empty($put['extra'])) {
						$put['extra'] = array_values($put['extra']);
						D('QuickEntryTwoStateItems','Desktop')->addQuickEntryItems($quickEntryId,$put['extra']);
					}

				}else{
					$quickEntryId = D('QuickEntryTwoState','Desktop')->addQuickEntry($put['name'],$put['x'],$put['y']);
				}

				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除两态快捷入口
		 * get /desktop/deleteQuickEntryTwoState?id=111
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteQuickEntryTwoState()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('QuickEntryTwoStateItems','Desktop')->deleteQuickEntryItems($id);
				D('QuickEntryTwoState','Desktop')->deleteQuickEntry($id);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据两态快捷入口列表
		 * get /desktop/quickEntryThreeStateLists
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function quickEntryTwoStateLists()
		{
			$id = I('get.id');

			if (!empty($id)) {
				$res = D('QuickEntryTwoStateItems','Desktop')->quickEntryLists($id);
			}else{
				$res = D('QuickEntryTwoStateItems','Desktop')->quickEntryLists();
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据修改两态快捷入口
		 * post /desktop/modifyQuickEntryTwoState
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyQuickEntryTwoState()
		{
			$put = I('put.');
			if (!empty($put['id'])&&!empty($put['name'])&&isset($put['x'])&&isset($put['y'])) {

				$res = D('QuickEntryTwoState','Desktop')->getValOneForNameForNotId($put['name'],$put['id']);

				if ($res) {
					result('附件名称已存在');
				}
				$res = D('QuickEntryTwoState','Desktop')->getValOneForId($put['id']);
				if (!$res) {
					result('附件不存在');
				}
				if (!empty($put['extra'])) {
					foreach ($put['extra'] as $key => $value) {
						if (!empty($put['extra'][$key]['id'])) {
							unset($put['extra'][$key]['id']);
						}
						if (empty($value['name'])  || !isset($value['x']) || !isset($value['y']) || empty($value['eventType']) || empty($value['activeDrawable']) || empty($value['normalDrawable']) || empty($value['focusedActiveDrawable'])  || empty($value['focusedNormalDrawable']) || empty($value['type']) ) {
							result('第'.($key+1).'三态快捷入口参数有误');
						}else{
							$put['extra'][$key]['activeDrawable'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['activeDrawable']);
							$put['extra'][$key]['normalDrawable'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['normalDrawable']);
							$put['extra'][$key]['focusedActiveDrawable'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedActiveDrawable']);
							$put['extra'][$key]['focusedNormalDrawable'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedNormalDrawable']);
						}
						$data = checkActionApp($value);
						if ($data) {
							$data = json_encode($data,JSON_UNESCAPED_UNICODE);
							$put['extra'][$key]['actionInfo'] = $data;
						}else{
							result('第'.($key+1).'三态快捷入口跳转参数有误');
						}
					}
					$quickEntryId = $res['id'];
					D('QuickEntryTwoState','Desktop')->modifyQuickEntry($put['id'],$put['name'],$put['x'],$put['y']);
					if (!empty($put['extra'])) {
						$put['extra'] = array_values($put['extra']);
						D('QuickEntryTwoStateItems','Desktop')->modifyQuickEntryItems($quickEntryId,$put['extra']);
					}

				}else{
					$quickEntryId = D('QuickEntryTwoState','Desktop')->modifyQuickEntry($put['id'],$put['name'],$put['x'],$put['y']);
					D('QuickEntryTwoStateItems','Desktop')->deleteQuickEntryItems($put['id']);
				}
				result();
			}else{
				result('param');
			}
		}
		//---------------------------------------------------------------------------------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------------------------------------------------------------------------
		/**
		 * 桌面管理_基础数据添加三态快捷入口
		 * post /desktop/addQuickEntryThreeState
		 * {"name":"附件名称",
		 * "extra":[{
  		 * "name": "wifi",
        		 * "x": 116,
        		 * "y": 497,
        		 * "eventType": "NETWORK",
		 * "eventA": "WifiConnectedEvent",
		 * "eventB": "EthernetConnectedEvent",
		 * "eventC": "NetworkDisconnectedEvent",
		 * "drawableA": "图片1",
	    	 * "drawableB": "图片2",
		 * " drawableC": "图片3",
		 * "focusedDrawableA": "焦点图片1",
		 * "focusedDrawableB": "焦点图片2",
		 * "focusedDrawableC": "焦点图片3",
		 * "type": "APP/COMPONENT/ACTION/URI",
		 * "extraData": [{
		 *    	"key": "key",
		 *   	"value": "value"
		 * }],
		 *
		 * "action": "this.is.action.name" ,
		 * "appName": "应用名" ,
		 * "detailName": "详情名" ,
		 *
		 * "pkgName": "包名" ,
		 * "appName": "应用名" ,
		 *
		 * "component": "com.linkin.setting",
		 * "clsName": "com.linkin.setting2.detail.NetworkActivity",
		 * "appName": "应用名" ,"detailName": "详情名",
		 * "detailName": "详情名" ,
		 *
		 * "uri": "this.is.uri"
		 *
		 *}]
		 *}
		 *]}
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addQuickEntryThreeState()
		{
			$put = I('put.');
			if (!empty($put['name'])&&isset($put['x'])&&isset($put['y'])) {

				$res = D('QuickEntryThreeState','Desktop')->getValOneForName($put['name']);
				if ($res) {
					result('三态快捷入口已存在');
				}
				if (!empty($put['extra'])) {
					/*$indexArr = array();
					foreach ($put['extra'] as $value) {
						$indexArr[] = $value['index'];
					}
					$indexArr = array_unique($indexArr);
					if (count($indexArr) != count($put['extra'])) {
						result('三态快捷入口id重复已存在或不存在');
					}*/
					// {"name":"附件","iconId":"1","radiusTopLeft":"左上角是否圆角","radiusTopRight":"右上角是否圆角","radiusBottomLeft":"左下角是否圆角","radiusBottomRight":"右下角是否圆角","actionId":"操作类型ID"}
					foreach ($put['extra'] as $key => $value) {
						if (!empty($put['extra'][$key]['id'])) {
							unset($put['extra'][$key]['id']);
						}
						if (empty($value['name'])  || !isset($value['x']) || !isset($value['y']) || empty($value['eventType']) || empty($value['eventA']) || empty($value['eventB']) || empty($value['eventC'])  || empty($value['drawableA'])  || empty($value['drawableB'])  || empty($value['drawableC']) || empty($value['focusedDrawableA'])  || empty($value['focusedDrawableB'])  || empty($value['focusedDrawableC']) || empty($value['type']) ) {
							result('第'.($key+1).'三态快捷入口参数有误');
						}else{
							$put['extra'][$key]['drawableA'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['drawableA']);
							$put['extra'][$key]['drawableB'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['drawableB']);
							$put['extra'][$key]['drawableC'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['drawableC']);
							$put['extra'][$key]['focusedDrawableA'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedDrawableA']);
							$put['extra'][$key]['focusedDrawableB'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedDrawableB']);
							$put['extra'][$key]['focusedDrawableC'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedDrawableC']);
						}
						$data = checkActionApp($value);
						if ($data) {
							$data = json_encode($data,JSON_UNESCAPED_UNICODE);
							$put['extra'][$key]['actionInfo'] = $data;
						}else{
							result('第'.($key+1).'三态快捷入口跳转参数有误');
						}
					}
					$quickEntryId = D('QuickEntryThreeState','Desktop')->addQuickEntry($put['name'],$put['x'],$put['y']);
					if (!empty($put['extra'])) {
						$put['extra'] = array_values($put['extra']);
						D('QuickEntryThreeStateItems','Desktop')->addQuickEntryItems($quickEntryId,$put['extra']);
					}

				}else{
					$quickEntryId = D('QuickEntryThreeState','Desktop')->addQuickEntry($put['name'],$put['x'],$put['y']);
				}

				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除三态快捷入口
		 * get /desktop/deleteQuickEntryThreeState?id=111
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteQuickEntryThreeState()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('QuickEntryThreeStateItems','Desktop')->deleteQuickEntryItems($id);
				D('QuickEntryThreeState','Desktop')->deleteQuickEntry($id);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据三态快捷入口列表
		 * get /desktop/quickEntryThreeStateLists
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function quickEntryThreeStateLists()
		{
			$id = I('get.id');

			if (!empty($id)) {
				$res = D('QuickEntryThreeStateItems','Desktop')->quickEntryLists($id);
			}else{
				$res = D('QuickEntryThreeStateItems','Desktop')->quickEntryLists();
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据修改三态快捷入口
		 * post /desktop/modifyQuickEntryThreeState
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyQuickEntryThreeState()
		{
			$put = I('put.');
			if (!empty($put['id'])&&!empty($put['name'])&&isset($put['x'])&&isset($put['y'])) {

				$res = D('QuickEntryThreeState','Desktop')->getValOneForNameForNotId($put['name'],$put['id']);
				if ($res) {
					result('附件名称已存在');
				}
				$res = D('QuickEntryThreeState','Desktop')->getValOneForId($put['id']);
				if (!$res) {
					result('附件不存在');
				}
				if (!empty($put['extra'])) {
					/*$indexArr = array();
					foreach ($put['extra'] as $value) {
						$indexArr[] = $value['index'];
					}
					$indexArr = array_unique($indexArr);
					if (count($indexArr) != count($put['extra'])) {
						result('三态快捷入口id重复已存在或不存在');
					}*/
					foreach ($put['extra'] as $key => $value) {
						if (!empty($put['extra'][$key]['id'])) {
							unset($put['extra'][$key]['id']);
						}
						if (empty($value['name'])  || !isset($value['x']) || !isset($value['y']) || empty($value['eventType']) || empty($value['eventA']) || empty($value['eventB']) || empty($value['eventC'])  || empty($value['drawableA'])  || empty($value['drawableB'])  || empty($value['drawableC']) || empty($value['focusedDrawableA'])  || empty($value['focusedDrawableB'])  || empty($value['focusedDrawableC']) || empty($value['type']) ) {
							result('第'.($key+1).'附件参数有误');
						}else{
							$put['extra'][$key]['drawableA'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['drawableA']);
							$put['extra'][$key]['drawableB'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['drawableB']);
							$put['extra'][$key]['drawableC'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['drawableC']);
							$put['extra'][$key]['focusedDrawableA'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedDrawableA']);
							$put['extra'][$key]['focusedDrawableB'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedDrawableB']);
							$put['extra'][$key]['focusedDrawableC'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['focusedDrawableC']);
						}
						$data = checkActionApp($value);
						if ($data) {
							$data = json_encode($data,JSON_UNESCAPED_UNICODE);
							$put['extra'][$key]['actionInfo'] = $data;
						}else{
							result('第'.($key+1).'附件跳转参数有误');
						}
					}
					$quickEntryId = $res['id'];
					D('QuickEntryThreeState','Desktop')->modifyQuickEntry($put['id'],$put['name'],$put['x'],$put['y']);
					if (!empty($put['extra'])) {
						$put['extra'] = array_values($put['extra']);
						D('QuickEntryThreeStateItems','Desktop')->modifyQuickEntryItems($quickEntryId,$put['extra']);
					}

				}else{
					$quickEntryId = D('QuickEntryThreeState','Desktop')->modifyQuickEntry($put['id'],$put['name'],$put['x'],$put['y']);
					D('QuickEntryThreeStateItems','Desktop')->deleteQuickEntryItems($put['id']);
				}
				result();
			}else{
				result('param');
			}
		}
		//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		/**
		 * 桌面管理_基础数据添加块
		 * post /desktop/addBlock
		 * post_data {"name":"test","w":100,"h":100,"yw":720,"yh":540}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addBlock()
		{
			$put = I('put.');
			if (!empty($put['name'])&&!empty($put['w'])&&!empty($put['h'])&&!empty($put['yw'])&&!empty($put['yh'])) {
				D('Block','Desktop')->addBlock($put['name'],$put['w'],$put['h'],$put['yw'],$put['yh']);
			}else{
				result('param');
			}
		}

		/**
		 * 桌面管理_基础数据删除块
		 * get /desktop/deleteBlock?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteBlock()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('Block','Desktop')->deleteBlock($id);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据块列表
		 * get /desktop/blockLists
		 * get /desktop/blockLists?page=xx&pageSize=xxx
		 * get /desktop/blockLists?page=xx&pageSize=xxx&name=xx
		 * @return {"extra":[{"id":1,"name":"test","w":100,"h":100,"yw":720,"yh":540}],"result":"ok"}
		 * }
		 */
		public function blockLists()
		{
			$get = I('get.');
			if (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('Block','Desktop')->blockLists($get['name'],$get['page'],$get['pageSize']);
				}else{
					D('Block','Desktop')->blockLists($get['name']);
				}
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('Block','Desktop')->blockLists(null,$get['page'],$get['pageSize']);
				}else{
					D('Block','Desktop')->blockLists();
				}
			}
			// D('Block','Desktop')->blockLists();
		}
		//******************************************************************************************************************
		/**
		 * 桌面管理_基础数据添加布局类型
		 * post /desktop/addLayoutType
		 * post_data {"name":"布局类型名称","type":"布局类型"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addLayoutType()
		{
			$put = I('put.');
			if (!empty($put['name'])&&!empty($put['type'])) {
				$res = D('LayoutType','Desktop')->addLayoutType($put['name'],$put['type']);
				if ($res) {
					result();
				}
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除布局类型
		 * get /desktop/deleteLayoutType?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteLayoutType()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('LayoutType','Desktop')->deleteLayoutType($id);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据布局类型列表
		 * get /desktop/layoutTypeLists
		 * @return {"extra":[{"id":1,"name":"test","type":"h"}],"result":"ok"}
		 * }
		 */
		public function layoutTypeLists()
		{
			$res['extra'] = D('LayoutType','Desktop')->layoutTypeLists();
			if (empty($res['extra'])) {
				$res['extra'] = array();
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据修改布局类型
		 * post /desktop/modifyLayoutType
		 * {"id":1,"name":"test","type":"h"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyLayoutType()
		{
			$put = I('put.');
			if (!empty($put['id'])&&!empty($put['name'])&&!empty($put['type'])) {
				$res = D('LayoutType','Desktop')->modifyLayoutType($put['id'],$put['name'],$put['type']);
				if ($res) {
					result();
				}
			}else{
				result('param');
			}
		}
		//******************************************************************************************************************
		/**
		 * 桌面管理_基础数据添加跳转信息管理
		 * post /desktop/addActionApp
		 *
		 *{"appName":"应用名称","pkgName":"包名"}
		 *
		 */

		public function addActionApp()
		{
			$put = I('put.');
			$actionAppId = D('ActionApp','Desktop')->addActionApp($put);
			result();
		}
		/**
		 * 桌面管理_基础数据添加跳转详细信息
		 * post /desktop/addActionAppDetail
		 *{"actionAppId":"1","detailName":"详情页名称","actionType":"ACTION/COMPONENT","action":"当actionType=ACTION时有效","clsName":"当actionType=COMPONENT时有效","component":"当actionType=COMPONENT时有效","extraData":[{"key":"xxx","value":"xxx"}]}
		 *
		 */
		public function addActionAppDetail()
		{
			$put = I('put.');
			D('ActionAppDetail','Desktop')->addActionAppDetail($put);
			result();
		}
		/**
		 * 桌面管理_基础数据复制跳转信息
		 * post /desktop/copyActionApp
		 * {"fromId":"2","toIds":["1"]}
		 * @return
		 * }
		 */
		public function copyActionApp()
		{
			$put = I('put.');
			D('ActionAppDetail','Desktop')->copyActionApp($put);
			result();
		}
		/**
		 * 桌面管理_基础数据_复制跳转信息详情
		 * post /desktop/copyActionDetailApp
		 * {
		 * 	"actionDetailIds":["跳转详情APPID"],
		 * 	"actionAppIds":["跳转APPID"]
		 * }
		 * @return
		 * }
		 */
		public function copyActionDetailApp()
		{
			$put = I('put.');
			D('ActionAppDetail','Desktop')->copyActionDetailApp($put);
			result();
		}
		/**
		 * 桌面管理_基础数据跳转信息管理列表
		 * get /desktop/actionAppLists
		 * get /desktop/actionAppLists?id=1
		 * get /desktop/actionAppLists?page=1&pageSize=xx
		 * get /desktop/actionAppLists?page=1&pageSize=xx&name=xx
		 * @return
		 * }
		 */
		public function actionAppLists()
		{

			$get = I('get.');
			if (!empty($get['id'])) {
				D('ActionApp','Desktop')->actionAppLists($get['id']);
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('ActionApp','Desktop')->actionAppLists(null,$get['name'],$get['page'],$get['pageSize']);
				}else{
					D('ActionApp','Desktop')->actionAppLists(null,$get['name']);
				}
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('ActionApp','Desktop')->actionAppLists(null,null,$get['page'],$get['pageSize']);
				}else{
					D('ActionApp','Desktop')->actionAppLists();
				}
			}
		}
		/**
		 * 桌面管理_基础数据跳转信息详细列表
		 * get /desktop/actionAppDetailLists?id=1
		 * @return
		 * }
		 */
		public function actionAppDetailLists()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				$res['extra'] = D('ActionAppDetail','Desktop')->actionAppDetailLists($get['id']);
			}else{
				$res['extra'] = array();
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据删除跳转信息
		 * get /desktop/deleteActionApp?id=1
		 * @return [type] [description]
		 */
		public function deleteActionApp()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('ActionApp','Desktop')->deleteActionAppLists($id);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除跳转详细信息
		 * get /desktop/deleteActionApp?id=1
		 * @return [type] [description]
		 */
		public function deleteActionAppDetail()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('ActionAppDetail','Desktop')->deleteActionAppDetail($id);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据修改跳转信息
		 * get /desktop/modifyActionApp
		 *{"id":"跳转信息ID","appName":"应用名称","pkgName":"包名"}
		 *
		 * @return [type] [description]
		 */
		public function modifyActionApp()
		{
			$put = I('put.');
			if (!empty($put['id']) && !empty($put['appName']) && !empty($put['pkgName'])) {
				D('ActionApp','Desktop')->modifyActionApp($put['id'],$put['appName'],$put['pkgName']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据修改跳转详细信息
		 * get /desktop/modifyActionAppDetail
		 *{"id":"跳转详细信息ID","detailName":"详情页名称","actionType":"ACTION/COMPONENT","action":"当actionType=ACTION时有效","clsName":"当actionType=COMPONENT时有效","component":"当actionType=COMPONENT时有效","extraData":[{"key":"xxx","value":"xxx"}]}
		 *
		 * @param string $value [description]
		 */
		public function modifyActionAppDetail()
		{
			$put = I('put.');
			D('ActionAppDetail','Desktop')->modifyActionAppDetail($put);
			result();

		}
		/**
		 * 桌面管理_基础数据上传图片
		 * /desktop/updataImage
		 * 表单上传
		 * {"additional":"wallpaper/slot"}
		 * @return  json {"normalFile":"http:\/\/resrc-test.oss-cn-qingdao.aliyuncs.com\/pic\/0b41ba54a65b547089d76b8d2673b72e.png","result":"ok"} PS :  normalFile 上传表单的名字
		 */
		public function updataImage()
		{
			$post = I('post.');
			require_once '../Base/function/Form.class.php';
			$form = new \form();
			$formData = $form->getFormFile();


			if (!$formData) {
				result('没有上传文件');
			}
			foreach ($_FILES as $value) {
				if (!empty($post['additional'])&&$post['additional'] == 'wallpaper') {
					if ($value['size'] >= C('DESKTOP_WALLPAPER_IMG')) {
						result('上传图标不能超过'.(C('DESKTOP_WALLPAPER_IMG')/1024/1024).'M');
					}
				}elseif (!empty($post['additional'])&&$post['additional'] == 'slot') {
					if ($value['size'] >= C('DESKTOP_SLOT_IMG') ) {
						result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
					}
				}
			}
			//上传OSS
			require_once '../Base/Ossupclass/OssBase.class.php';
			$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);

			$res = $base->uploadFile($formData);
			foreach ($res as $key => $value) {
				$data[$key] = C('DOWNLOAD_IMG_PREFIX_HOST') . $value['oss'];
			}
			//上传本地
			$config = array(
				'rootPath'      =>  C('LOCALHOST_PATH_ADDR') .'pic/',
				'savePath'   =>    '',
				'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
				'saveName'   => '',
				'autoSub' => false
			);

			if (!empty($post['additional'])&&$post['additional'] == 'wallpaper') {
				$config['maxSize'] = C('DESKTOP_WALLPAPER_IMG');
			}elseif (!empty($post['additional'])&&$post['additional'] == 'slot') {
				$config['maxSize'] = C('DESKTOP_SLOT_IMG') ;
			}


			$upload = new \Think\Upload($config);// 实例化上传类

			$res = $form->getFileNameForMd5();


			if ($res) {
				foreach ($res as $key => $value) {
					if (!file_exists( C('LOCALHOST_PATH_ADDR') .'pic/'. $value['name'])) {
						//保存本地
						$info   =   $upload->uploadOne($res[$key]);
						if (!$info) {
							if (!empty($post['additional'])&&$post['additional'] == 'wallpaper') {
								result('上传图标不能超过'. ( C('DESKTOP_WALLPAPER_IMG')/1024/1024 ) . 'M');
							}elseif (!empty($post['additional'])&&$post['additional'] == 'slot') {
								result('上传图标不能超过'.( C('DESKTOP_SLOT_IMG')/1024 ).'KB');
							}else{
								result( $upload->getError() );
							}
						}
					}/*else{
						if (!empty($post['additional'])&&$post['additional'] == 'wallpaper') {
							if ($value['size'] >= 1*1024*1024) {
								result('上传壁纸图片不能超过1M');
							}
						}elseif (!empty($post['additional'])&&$post['additional'] == 'slot') {
							if ($value['size'] >= 500*1024) {
								result('上传运营坑位图片不能超过500KB');
							}
						}
					}*/
				}
			}

			result(true,$data);
		}
		/**
		 * 桌面管理_复制运营坑位组
		 * post   /desktop/copyOperationSlot
		 * {
		 * 	"slotGroupId":"运营坑位组ID"
		 * 	"idLists":["运营坑位ID1","运营坑位ID2"]
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
	    	public function copyOperationSlot()
	    	{
	    		$put = I('put.');
	    		if (!empty($put['slotGroupId']) && !empty($put['idLists'])&&is_array($put['idLists'])) {
	    			$operationSlotGroup = D('OperationSlotGroup','Desktop')->operationSlotGroupForId($put['slotGroupId']);
				if ($operationSlotGroup) {
					$reason = '';
					$operationSlot = D('OperationSlot','Desktop')->getValOneForIdArr($put['idLists']);
					if ($operationSlot) {
						foreach ($operationSlot as $value) {
							$operationSlotId = $value['id'];
							unset($value['id']);
							$value['slotGroupId'] = $put['slotGroupId'];

							$operationSlotNewId = D('OperationSlot','Desktop')->copyOperationSlot($value);
							if (empty($operationSlotNewId['reason'])) {
								if ($operationSlotVideos = D('OperationSlotVideos','Desktop')->getValArrForOperationId($operationSlotId)) {
									D('OperationSlotVideos','Desktop')->addOperationSlotVideosArr($operationSlotNewId,$operationSlotVideos);
								}
								if ($operationSlotBindApp = D('OperationSlotBindApp','Desktop')->getValForOperationId($operationSlotId)) {
									unset($operationSlotBindApp['id']);
									$operationSlotBindApp['operationId'] =$operationSlotNewId;
									D('OperationSlotBindApp','Desktop')->addOperationSlotBindApp($operationSlotBindApp);
								}
								if ($operationSlotAction = D('OperationSlotAction','Desktop')->getValForOperationId($operationSlotId)) {
									unset($operationSlotAction['id']);
									$operationSlotAction['operationId'] = $operationSlotNewId;
									D('OperationSlotAction','Desktop')->addOperationSlotAction($operationSlotAction);
								}
							}else{
								$reason .= $value['slotID'] . '、';
							}

						}

					}

				}else{
					result('运营坑位组不存在');
				}
			}else{
				result('param');
			}
			if (!empty($reason)) {
				result('运营坑位'.trim($reason,'、').'已存在');
			}else{
				result();
	    		}
	    	}

		/**
		 * 桌面管理_基础数据添加运营坑位组
		 * post   /desktop/addOperationSlotGroup
		 * {
		 * 	"name":"test"
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
	    	public function addOperationSlotGroup()
	    	{
	    		$put = I('put.');
	    		 D('OperationSlotGroup','Desktop')->addOperationSlotGroup($put);

	    		result();
	    	}
	    	/**
		 * 桌面管理_基础数据修改运营坑位组
		 * post   /desktop/modifyOperationSlotGroup
		 * {
		 * 	"name":"test"
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
	    	public function modifyOperationSlotGroup()
	    	{
	    		$put = I('put.');
	    		 D('OperationSlotGroup','Desktop')->modifyOperationSlotGroup($put);
	    		result();
	    	}
	    	/**
		 * 桌面管理_基础数据删除运营坑位组
		 * post   /desktop/deleteOperationSlotGroup
		 * {
		 * 	"name":"test"
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
	    	public function deleteOperationSlotGroup()
	    	{
	    		$get = I('get.');
	    		 D('OperationSlotGroup','Desktop')->deleteOperationSlotGroup($get);

	    		result();
	    	}
	    	/**
		 * 桌面管理_基础数据运营坑位组列表
		 * post   /desktop/deleteOperationSlotGroup
		 * {
		 * 	"name":"test"
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
	    	public function operationSlotGroupLists()
	    	{
	    		$get = I('get.');
	    		$res =  D('OperationSlotGroup','Desktop')->operationSlotGroupLists($get);

	    		result(true ,$res);
	    	}
		/**
		 * 桌面管理_基础数据添加运营坑位
		 * POST /desktop/addOperationSlot
		 * 跳转到应用
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"APP",  "app":{"appName":"爱奇艺", "pkgName":"aqy.com","extraData":{"key":"value"}}}
		 *
		 *跳转到详情页--ACTION类型
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"ACTION",  "action":{"appName":"爱奇艺", "detailName":"琅琊榜","action":"aqy.com.xxx","extraData":{"key":"value"}}}
		 *
		 *跳转到详情页--COMPONENT类型
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"COMPONENT",  "component":{"appName":"爱奇艺", "detailName":"琅琊榜","component":"aqy.com.xxx", "clsName":"aqy.com.xx.xxx","extraData":{"key":"value"}}}
		 *
		 *跳转到链接
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"URI",  "uri":"http://xxxxx"}
		 */
		public function addOperationSlot($put = null)
		{
			if ($put === null) {
				$put = I('put.');
			}

			if (empty($put['slotGroupId'])  || empty($put['slotID'])  ||  !($put['disconnectEnable'] =='false' || $put['disconnectEnable'] =='true') || !($put['isEditable'] =='false' || $put['isEditable'] =='true') || !isset($put['title']) || !($put['dataSource']=='yunos' || $put['dataSource']=='linkin' || $put['dataSource']=='linkinOnly') ) {
				result('param');
			}
			if (!D('OperationSlotGroup','Desktop')->operationSlotGroupForId($put['slotGroupId'])) {
				result('运营坑位组不存在');
			}

			if ($put['dataSource']=='yunos') {
				$operationSlotArr = array(
					'slotID' =>$put['slotID'],
					'dataSource' =>$put['dataSource'],
					'isEditable' =>$put['isEditable'],
					'disconnectEnable' =>$put['disconnectEnable'],
					'title'=>!empty($put['title'])?$put['title']:'',
					'slotGroupId'=>$put['slotGroupId'],
				);
				//添加运营坑位
				if (D('OperationSlot','Desktop')->getValForSlotIdForSlotGroupId($put['slotID'],$put['slotGroupId']) )  {
					result('该运营坑位已存在');
				}
				$operationSlotId = D('OperationSlot','Desktop')->addOperationSlot($operationSlotArr);

			}else{
				if (!($put['dataSource'] == 'linkin' || $put['dataSource'] == 'linkinOnly') || empty($put['layout']) || empty($put['pic1'])  || empty($put['pic2'])  || empty($put['pic3'])   || empty($put['actionType']) ) {
					result('param');
				}
				//检查数据
				if ($put['layout'] == 'VIDEO') {
					if (empty($put['videos'])) {
						result('布局类型参数出错');
					}
					foreach ($put['videos'] as $key => $value) {
						if (empty($value['url']) || !isset($value['duration'])) {
							result('布局类型参数出错');
						}
						$videoArr[] = array(
							'url'=>$value['url'],
							'duration'=>$value['duration'],
						);
					}
				}
				if (!empty($put['tag'])) {
					if (!isset($put['tag']['position']) || !isset($put['tag']['height']) || !isset($put['tag']['width']) || empty($put['tag']['image']) || !($put['tag']['clickDisplay'] == 'true' || $put['tag']['clickDisplay'] == 'false' )) {
						result('坑位标签数据出错');
					}
					$put['tag'] = array(
						'position' =>$put['tag']['position'],
						'clickDisplay' =>$put['tag']['clickDisplay'],
						'image' =>$put['tag']['image'],
						'height' =>$put['tag']['height'],
						'width' =>$put['tag']['width'],
					);
				}else{
					$put['tag'] = new \stdClass();
				}
				$operationSlotArr = array(
					'slotID' =>$put['slotID'],
					'layout' =>$put['layout'],
					'title'=>!empty($put['title'])?$put['title']:'',
					'dataSource' =>$put['dataSource'],
					'isEditable' =>$put['isEditable'],
					'disconnectEnable' =>$put['disconnectEnable'],
					'pic1' =>$put['pic1'],
					'pic2' =>$put['pic2'],
					'pic3' =>$put['pic3'],
					'slotGroupId' =>$put['slotGroupId'],
					'tag'=>json_encode($put['tag'],JSON_UNESCAPED_UNICODE)
				);
				if (!empty($put['bindApp'])) {
					if (empty($put['bindApp']['appName']) || empty($put['bindApp']['pkgName'])  || !isset($put['bindApp']['versionCode'])   || empty($put['bindApp']['url'])  || !($put['bindApp']['autoInstall'] == 'true' || $put['bindApp']['autoInstall'] == 'false')) {
						result('插槽数据出错');
					}
					$bindAppArr=array(
						'appName'=>$put['bindApp']['appName'],
						'pkgName'=>$put['bindApp']['pkgName'],
						'versionCode'=>$put['bindApp']['versionCode'],
						'url'=>$put['bindApp']['url'],
						'autoInstall'=>$put['bindApp']['autoInstall']
					);

				}

				/*switch ($put['actionType']) {
					case 'APP':
						if (!empty($put['app']['appName']) && !empty($put['app']['pkgName'])  ) {
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['app']['appName'],
								'pkgName'=>$put['app']['pkgName']
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'ACTION':
						if (!empty($put['action']['appName']) && !empty($put['action']['detailName']) &&!empty($put['action']['action']) ) {
							if (!is_array($put['action']['extraData'])) {
								$put['action']['extraData'] = array();
							}else{
								foreach ($put['action']['extraData'] as $key => $value) {
									if (!isset($value['key']) || !isset($value['value'])) {
										$put['action']['extraData'] = array();
										break;
									}
									if (isset($value['type'])) {
										if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
											unset($put['action']['extraData'][$key]['type']);
										}
									}
								}
							}
							$extraData = json_encode($put['action']['extraData'],JSON_UNESCAPED_UNICODE);
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['action']['appName'],
								'detailName'=>$put['action']['detailName'],
								'action'=>$put['action']['action'],
								'extraData'=>$extraData,
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'COMPONENT':
						if (!empty($put['component']['appName']) && !empty($put['component']['detailName']) &&!empty($put['component']['component']) &&!empty($put['component']['clsName']) ) {
							if (!is_array($put['component']['extraData'])) {
								$put['component']['extraData'] = array();
							}else{
								foreach ($put['component']['extraData'] as $key => $value) {
									if (!isset($value['key']) || !isset($value['value']) ) {
										$put['component']['extraData'] = array();
										break;
									}
									if (isset($value['type'])) {
										if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
											unset($put['component']['extraData'][$key]['type']);
										}
									}
								}
							}
							$extraData = json_encode($put['component']['extraData'],JSON_UNESCAPED_UNICODE);
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['component']['appName'],
								'detailName'=>$put['component']['detailName'],
								'component'=>$put['component']['component'],
								'clsName'=>$put['component']['clsName'],
								'extraData'=>$extraData,
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'URI':
						if (!empty($put['uri']['uri']) && !empty($put['uri']['uriName']) ) {
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'uri'=>$put['uri']['uri'],
								'uriName'=>$put['uri']['uriName'],
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					default:
						result('数据类型出错');
						break;
				}*/
				if (!$actionArr = checkQuickOperationActionApp($put)) {
					result('跳转信息有误');
				}
				//添加运营坑位
				if (D('OperationSlot','Desktop')->getValForSlotIdForSlotGroupId($put['slotID'],$put['slotGroupId']) ) {
					result('该运营坑位已存在');
				}
				$operationSlotId = D('OperationSlot','Desktop')->addOperationSlot($operationSlotArr);
				if (!empty($actionArr)) {
					//添加运营坑位数据类型
					$actionArr['operationId'] = $operationSlotId;
					D('OperationSlotAction','Desktop')->addOperationSlotAction($actionArr);
				}
				if (!empty($bindAppArr)) {
					//添加运营坑位槽位绑定
					$bindAppArr['operationId'] =  $operationSlotId;
					D('OperationSlotBindApp','Desktop')->addOperationSlotBindApp($bindAppArr);
				}

				//添加运营坑位布局为Videos
				if (!empty($videoArr)) {
					D('OperationSlotVideos','Desktop')->addOperationSlotVideosArr($operationSlotId,$videoArr);
				}

			}
			result();

		}
		/**
		 * 桌面管理_基础数据运营坑位列表
		 * {"slotGroupId":"1","slotIDArr":["103"]}
		 * @return [type] [description]
		 */
		public function operationSlotArrLists()
		{
			$put = I('put.');
			$res['extra'] = array();
			if (!empty($put['slotGroupId'])&&!empty($put['slotIDArr'])&&is_array($put['slotIDArr'])) {
				$res['extra'] = D('OperationSlot','Desktop')->operationSlotArrLists($put['slotGroupId'],$put['slotIDArr']);
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据修改运营坑位
		 * POST /desktop/modifyOperationSlot
		 * 跳转到应用
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"APP",  "app":{"appName":"爱奇艺", "pkgName":"aqy.com","extraData":{"key":"value"}}}
		 *
		 *跳转到详情页--ACTION类型
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"ACTION",  "action":{"appName":"爱奇艺", "detailName":"琅琊榜","action":"aqy.com.xxx","extraData":{"key":"value"}}}
		 *
		 *跳转到详情页--COMPONENT类型
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"COMPONENT",  "component":{"appName":"爱奇艺", "detailName":"琅琊榜","component":"aqy.com.xxx", "clsName":"aqy.com.xx.xxx","extraData":{"key":"value"}}}
		 *
		 *跳转到链接
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"URI",  "uri":"http://xxxxx"}
		 */
		public function modifyOperationSlot()
		{
			$put = I('put.');
			if ( empty($put['slotGroupId']) || empty($put['id']) || !( $put['isModifySource'] =='false' || $put['isModifySource'] =='true' ) || !($put['disconnectEnable'] =='false' || $put['disconnectEnable'] =='true') || !($put['isEditable'] =='false' || $put['isEditable'] =='true') || !($put['dataSource']=='yunos' || $put['dataSource']=='linkin' || $put['dataSource']=='linkinOnly')  || empty($put['slotID']) || !isset($put['title'])  ) {

				result('param');
			}
			$operationSlot = D('OperationSlot','Desktop')->getValOneForId($put['id']);
			if (!$operationSlot) {
				result('没有找到此运营坑位');
			}

			if ($operationSlot['slotID'] != $put['slotID'] ) {

				$isOperationSlot = D('DesktopBlockInfo','Desktop')->getOperationSlot($operationSlot['slotID'],$operationSlot['slotGroupId']);
				if ($isOperationSlot) {
					result('要修改的坑位ID有桌面使用');
				}
			}
			$res = D('OperationSlot','Desktop')->getValOneForSlotIdForSlotGroupIdForNotId($put['slotID'],$put['slotGroupId'],$put['id']);
			if ($res) {
				result('运营坑位ID已存在');
			}

			$operationSlotIDInfo = D('OperationSlot','Desktop')->operationSlotLists(null,null,null,null,$operationSlot['slotID'],$operationSlot['slotGroupId']);
			$desktopSlotUpdateTime = false;
			//运营数据是否有改变
			foreach ($operationSlotIDInfo['extra'] as $key => $value) {
				if (isset($operationSlotIDInfo['extra']['videos'])&&!isset($put['videos'])) {
					$desktopSlotUpdateTime = true;
					break;
				}elseif (!isset($operationSlotIDInfo['extra']['videos'])&&isset($put['videos'])) {
					$desktopSlotUpdateTime = true;
					break;
				}elseif (isset($operationSlotIDInfo['extra']['bindApp'])&&!isset($put['bindApp'])) {
					$desktopSlotUpdateTime = true;
					break;
				}elseif (!isset($operationSlotIDInfo['extra']['bindApp'])&&isset($put['bindApp'])) {
					$desktopSlotUpdateTime = true;
					break;
				}
				if ($key == 'action') {
					foreach ($value as $k => $v) {

						if ($k == 'extraData' ) {
							if (count($operationSlotIDInfo['extra'][$key][$k]) !=count($put[$key][$k])) {
								$desktopSlotUpdateTime = true;
								break;
							}else{
								foreach ($v as $a => $b) {
									if ($v[$a]['key'] !=$put[$key][$k][$a]['key']) {
										$desktopSlotUpdateTime = true;
										break;
									}elseif ($v[$a]['value'] !=$put[$key][$k][$a]['value']) {
										$desktopSlotUpdateTime = true;
										break;
									}elseif ($v[$a]['type'] !=$put[$key][$k][$a]['type']) {
										$desktopSlotUpdateTime = true;
										break;
									}
								}
							}

						}elseif ($operationSlotIDInfo['extra'][$key][$k] !=$put[$key][$k]) {
							$desktopSlotUpdateTime = true;
							break;
						}
					}
				}elseif ($key == 'videos') {

					if (count($operationSlotIDInfo['extra'][$key]) != count($put[$key]) ) {
						$desktopSlotUpdateTime = true;
						break;
					}else{
						foreach ($value as $k => $v) {
							if ($operationSlotIDInfo['extra'][$key][$k]['url'] !=$put[$key][$k]['url']) {
								$desktopSlotUpdateTime = true;
								break;
							}elseif ($operationSlotIDInfo['extra'][$key][$k]['duration'] !=$put[$key][$k]['duration']) {
								$desktopSlotUpdateTime = true;
								break;
							}
						}
					}
				}elseif ($key == 'bindApp') {
					foreach ($value as $k => $v) {
						if ($value[$k] != $put[$key][$k]) {
							$desktopSlotUpdateTime = true;
							break;
						}
					}
				}else{
					if ($operationSlotIDInfo['extra'][$key] !=$put[$key]) {
						$desktopSlotUpdateTime = true;
						break;
					}
				}
			}

			if ($put['dataSource'] =='yunos') {
				$operationSlotArr = array(
					'slotID' =>$put['slotID'],
					'title'=>!empty($put['title'])?$put['title']:'',
					'dataSource' =>$put['dataSource'],
					'isEditable' =>$put['isEditable'],
					'disconnectEnable' =>$put['disconnectEnable'],
				);
				//添加运营坑位
				$operationSlotId = D('OperationSlot','Desktop')->modifyOperationSlot($put['id'],$operationSlotArr);
				D('OperationSlotVideos','Desktop')->deleteOperationSlotVideosArr($operationSlotId);
				D('OperationSlotBindApp','Desktop')->deleteOperationSlotBindApp($operationSlotId);
			}else{
				if ( !( $put['isModifySource'] =='false' || $put['isModifySource'] =='true' ) || !($put['dataSource'] == 'linkin' || $put['dataSource'] =='linkinOnly') || empty($put['layout']) || empty($put['pic1']) || empty($put['pic2'])  || empty($put['pic3'])   || empty($put['actionType'])) {
					result('非云坑位参数有误');
				}
				//检查数据
				if ($put['layout'] == 'VIDEO') {
					if (empty($put['videos'])) {
						result('布局类型VIDEO参数出错');
					}
					foreach ($put['videos'] as $key => $value) {
						if (empty($value['url']) || !isset($value['duration'])) {
							result('布局类型VIDEO参数出错');
						}
						$videoArr[] = array(
							'url'=>$value['url'],
							'duration'=>$value['duration'],
						);
					}

				}
				if (!empty($put['tag'])) {
					if (!isset($put['tag']['position']) || !isset($put['tag']['width']) || !isset($put['tag']['height']) || empty($put['tag']['image']) || !($put['tag']['clickDisplay'] == 'true' || $put['tag']['clickDisplay'] == 'false' )) {
						result('坑位标签数据出错');
					}
					$put['tag'] = array(
						'position' =>$put['tag']['position'],
						'clickDisplay' =>$put['tag']['clickDisplay'],
						'image' =>$put['tag']['image'],
						'width' =>$put['tag']['width'],
						'height' =>$put['tag']['height'],
					);
				}else{
					$put['tag'] = new \stdClass();
				}

				$operationSlotArr = array(
					'slotID' =>$put['slotID'],
					'layout' =>$put['layout'],
					'title'=>!empty($put['title'])?$put['title']:'',
					'dataSource' =>$put['dataSource'],
					'isEditable' =>$put['isEditable'],
					'disconnectEnable' =>$put['disconnectEnable'],
					'pic1' =>$put['pic1'],
					'pic2' =>$put['pic2'],
					'pic3' =>$put['pic3'],
					'tag'=>json_encode($put['tag'],JSON_UNESCAPED_UNICODE)
				);
				if (!empty($put['bindApp'])) {
					if (!empty($put['bindApp']['appName']) && !empty($put['bindApp']['pkgName'])  && isset($put['bindApp']['versionCode'])   && !empty($put['bindApp']['url'])  && ($put['bindApp']['autoInstall'] == 'true' || $put['bindApp']['autoInstall'] == 'false')) {
						$bindAppArr=array(
							'appName'=>$put['bindApp']['appName'],
							'pkgName'=>$put['bindApp']['pkgName'],
							'versionCode'=>$put['bindApp']['versionCode'],
							'url'=>$put['bindApp']['url'],
							'autoInstall'=>$put['bindApp']['autoInstall']
						);
					}else{
						result('插槽数据出错');
					}
				}
				//跳转数据
				/*switch ($put['actionType']) {
					case 'APP':
						if (!empty($put['app']['appName']) && !empty($put['app']['pkgName'])  ) {
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['app']['appName'],
								'pkgName'=>$put['app']['pkgName']
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'ACTION':
						if (!empty($put['action']['appName']) && !empty($put['action']['detailName']) &&!empty($put['action']['action']) ) {
							if (!is_array($put['action']['extraData'])) {
								$put['action']['extraData'] = array();
							}else{
								foreach ($put['action']['extraData'] as $key => $value) {
									if (!isset($value['key']) || !isset($value['value'])) {
										$put['action']['extraData'] = array();
										break;
									}
									if (isset($value['type'])) {
										if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
											unset($put['action']['extraData'][$key]['type']);
										}
									}
								}
							}
							$extraData = json_encode($put['action']['extraData'],JSON_UNESCAPED_UNICODE);
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['action']['appName'],
								'detailName'=>$put['action']['detailName'],
								'action'=>$put['action']['action'],
								'extraData'=>$extraData,
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'COMPONENT':
						if (!empty($put['component']['appName']) && !empty($put['component']['detailName']) &&!empty($put['component']['component']) &&!empty($put['component']['clsName']) ) {
							if (!is_array($put['component']['extraData'])) {
								$put['component']['extraData'] = array();
							}else{
								foreach ($put['component']['extraData'] as $key => $value) {
									if (!isset($value['key']) || !isset($value['value'])) {
										$put['component']['extraData'] = array();
										break;
									}
									if (isset($value['type'])) {
										if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
											unset($put['component']['extraData'][$key]['type']);
										}
									}

								}
							}
							$extraData = json_encode($put['component']['extraData']);
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['component']['appName'],
								'detailName'=>$put['component']['detailName'],
								'component'=>$put['component']['component'],
								'clsName'=>$put['component']['clsName'],
								'extraData'=>$extraData,
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'URI':
						if (!empty($put['uri']['uri']) && !empty($put['uri']['uriName']) ) {
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'uri'=>$put['uri']['uri'],
								'uriName'=>$put['uri']['uriName'],
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					default:
						result('数据类型出错');
						break;
				}*/
				if (!$actionArr = checkQuickOperationActionApp($put)) {
					result('跳转信息有误');
				}
				//添加运营坑位
				$operationSlotId = D('OperationSlot','Desktop')->modifyOperationSlot($put['id'],$operationSlotArr);

				//添加运营坑位数据类型
				if (!empty($actionArr)) {
					if ( D('OperationSlotAction','Desktop')->getValForOperationId($put['id']) ) {
						D('OperationSlotAction','Desktop')->modifyOperationSlotAction($put['id'],$actionArr);
					}else{
						//添加运营坑位数据类型
						$actionArr['operationId'] = $operationSlotId;
						D('OperationSlotAction','Desktop')->addOperationSlotAction($actionArr);
					}
				}

				if (!empty($bindAppArr)) {
					//添加运营坑位槽位绑定
					if (D('OperationSlotBindApp','Desktop')->getValForOperationId($put['id'])){
						D('OperationSlotBindApp','Desktop')->modifyOperationSlotBindApp($put['id'],$bindAppArr);
					}else{
						//添加运营坑位槽位绑定
						$bindAppArr['operationId'] =  $operationSlotId;
						D('OperationSlotBindApp','Desktop')->addOperationSlotBindApp($bindAppArr);
					}

				}else{
					D('OperationSlotBindApp','Desktop')->deleteOperationSlotBindApp($operationSlotId);
				}

				//添加运营坑位布局为Videos
				if (!empty($videoArr)) {
					if (D('OperationSlotVideos','Desktop')->getValForOperationId($put['id']) ) {
						D('OperationSlotVideos','Desktop')->modifyOperationSlotVideosArr($operationSlotId,$videoArr);
					}else{
						D('OperationSlotVideos','Desktop')->addOperationSlotVideosArr($operationSlotId,$videoArr);
					}
				}else{
					D('OperationSlotVideos','Desktop')->deleteOperationSlotVideosArr($operationSlotId);
				}
			}

			//修改桌面修改时间
			D('DesktopBlockInfo','Desktop')->modifyOperationSlot($operationSlot['slotID'],$put['isModifySource'],$desktopSlotUpdateTime,$operationSlot['slotGroupId']);
			result();

		}
		/**
		 * 桌面管理_基础数据运营坑位列表
		 * get /desktop/operationSlotLists
		 * get /desktop/operationSlotLists?id=1
		 * get /desktop/operationSlotLists?slotID=1&slotGroupId=false
		 * get /desktop/operationSlotLists?page=1&pageSize=xx&slotGroupId=false
		 * get /desktop/operationSlotLists?page=1&pageSize=xx&name=xx&slotGroupId=false
		 * @return [type] [description]
		 */
		public function operationSlotLists()
		{
			$get = I('get.');
			$res = D('OperationSlot','Desktop')->operationSlotLists($get);
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据获取运营坑位slotID
		 * post /desktop/getOperationSlotId
		 * ['"1","2"]
		 * @param string $value [description]
		 */
		public function getOperationSlotId()
		{
			$put = I('put.');
			$res = D('OperationSlot','Desktop')->getSlotIDForIdArr($put);
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据删除运营坑位
		 * post /desktop/deleteOperationSlot
		 *
		 * @param string $value [description]
		 */
		public function deleteOperationSlot()
		{
			$put = I('put.');
			if (!empty($put['idLists'])&&is_array($put['idLists'])) {
				$res = D('OperationSlot','Desktop')->getValOneForIdArr($put['idLists']);
				if ($res) {
					$reason = '';
					foreach ($res as $value) {
						$res1 = D('DesktopBlockInfo','Desktop')->getOperationSlot($value['slotID'],$value['slotGroupId']);

						if (!empty($res1)) {
							$desktopName = '正在桌面';
							foreach ($res1 as  $v) {
								$desktopName .= $v['desktopName'] . '、';
							}
							$reason .= '运营坑位'.$value['slotID'] . trim($desktopName,'、') .'使用，';
						}else{
							$deleteIDArr[] = $value['id'];
						}
					}
					if (!empty($deleteIDArr)) {
						$deleteIDStr = implode(',', $deleteIDArr);
						D('OperationSlotVideos','Desktop')->deleteVideosArrForOperationSlotArr($deleteIDStr);
						D('OperationSlotBindApp','Desktop')->deleteOperationSlotBindAppArrForIDArr($deleteIDStr);
						D('OperationSlotAction','Desktop')->deleteOperationSlotActionArrForIDArr($deleteIDStr);
						D('OperationSlot','Desktop')->deleteOperationSlotArrForIDArr($deleteIDStr);
					}
					if (!empty($reason)) {
						$res = array('reason'=>trim($reason,'，'));
						result(true,$res);
					}else{
						result();
					}

				}else{
					result('运营坑位不存在');
				}
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据获取应用运营坑位桌面
		 *GET /desktop/getOperationSlot?id=1
		 *return {"result":"ok", "extra":[{"desktopID":1, "desktopName":"aaaa"}]}
		 * @return [type] [description]
		 */
		public function getOperationSlot()
		{
			$id = I('get.id');
			$res['extra'] = array();
			if (!empty($id)) {
				$slotID = D('OperationSlot','Desktop')->getValOneForId($id);
				if ($slotID) {
					$res['extra'] = D('DesktopBlockInfo','Desktop')->getOperationSlot($slotID['slotID'],$slotID['slotGroupId']);
					if (!$res['extra']) {
						$res['extra'] = array();
					}
				}
			}
			result(true,$res);
		}

		//---------------------------------------------------------------------------------------------------------------------------------------
		/**
		 * 桌面管理_基础数据_添加快捷坑位组
		 * POST /desktop/addQuickEntrySlotGroup
		 */
		public function addQuickEntrySlotGroup()
		{
			$put = I('put.');
			D('QuickEntrySlotGroup','Desktop')->addQuickEntrySlotGroup($put);
			result();
		}
		/**
		 * 桌面管理_基础数据_修改快捷坑位组
		 * POST /desktop/modifyQuickEntrySlotGroup
		 */
		public function modifyQuickEntrySlotGroup()
		{
			$put = I('put.');
			D('QuickEntrySlotGroup','Desktop')->modifyQuickEntrySlotGroup($put);
			result();
		}
		/**
		 * 桌面管理_基础数据_删除快捷坑位组
		 * get /desktop/deleteQuickEntrySlotGroup?id=x
		 */
		public function deleteQuickEntrySlotGroup()
		{
			$get = I('get.');
			D('QuickEntrySlotGroup','Desktop')->deleteQuickEntrySlotGroup($get);
			result();
		}
		/**
		 * 桌面管理_基础数据_删除快捷坑位组
		 * get /desktop/deleteQuickEntrySlotGroup?id=x
		 */
		public function quickEntrySlotGroupLists()
		{
			$get = I('get.');
			$res = D('QuickEntrySlotGroup','Desktop')->quickEntrySlotGroupLists($get);
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据添加快捷坑位
		 * POST /desktop/addQuickEntrySlot
		 *  {
		 *  "groupId":"100",
		 *  "slodID":"100",
		 *  "title":"标题",
		 *  "isEditable":"false/true",   //是否可编辑
		 *  "focusedDrawable": "focused_1.png",       //拿到焦点后显示的图片
		 *  "normalDrawable": "normal_1.png", //未获得焦点时的图片
		 *  "bindApp": {
		 *                             "url": "http:\/\/pic.moretv.com.cn\/download\/channel\/MoreTVApp_xunma.apk", //未安装应用，会根据这个下载 app
		 *                             "pkgName": "com.moons.tether.wifi",      //要下载的应用的包名
		 *                             "versionCode": "263"                                         //要下载应用的versionCode
		 *  "appName":"应用名称",
		 *                    },
		 *
		 *              "actionType": "ACTION/APP/COMPONENT/URI",                      
		 *               "extraData": [{                                              
		 *                                      "key": "Data",
		 *                                      "value": "page=list&contentType=movie",
		 *        "type": "int/long/float/double/boolean/char/string"
		 *                       }],
		 *  "action": { 	                                                                           // 当type为ACTION生效
		 *  "action": "moretv.action.applaunch",
		 *  "appName":"爱奇艺",
		 *  "detailName":"琅琊榜"
		 *              },
		 *  "app": {                                                            // 当type为APP生效
		 *                                      "pkgName": "com.linkin.tv",
		 *        "appName":"应用名称",
		 *              },
		 *             "component": {                                               // 当type为COMPONENT生效
		 *                                      "component": "com.yunos.tv.yingshi.publics",
		 *                                      "clsName": "com.yunos.tv.yingshi.activity.FavorActivity",
		 *        "appName":"爱奇艺",
		 *        "detailName":"琅琊榜"
		 *             },
		 *             "uri": {                                                              // 当type为URI生效
		 *                                      "uri": "appstore:\/\/startbytype?type=230&typename=影音”,
		 *  "uriName":"淘宝"
		 *             }                       
		 *                        
		 *  }
		 */
		public function addQuickEntrySlot($put = null)
		{
			if ($put === null) {
				$put = I('put.');
			}
			if (empty($put['actionType']) || empty($put['groupId']) || empty($put['normalDrawable'])  ||  empty($put['focusedDrawable'])  || empty($put['slotId'])  || !($put['isEditable'] =='false' || $put['isEditable'] =='true') ) {

				result('param');
			}

			if (!D('QuickEntrySlotGroup','Desktop')->getOneForId($put['groupId'])) {
				result('坑位不存在');
			}
			//检查数据

			$quickEntrySlotArr = array(
				'slotId' =>$put['slotId'],
				'title'=>!empty($put['title'])?$put['title']:'',
				'isEditable' =>$put['isEditable'],
				'group_id' =>$put['groupId'],
				'normalDrawable' =>$put['normalDrawable'],
				'focusedDrawable' =>$put['focusedDrawable'],
			);

			if (!empty($put['bindApp'])) {
				if (empty($put['bindApp']['appName']) || empty($put['bindApp']['pkgName'])  ||  !isset($put['bindApp']['versionCode'])   || empty($put['bindApp']['url'])) {
					result('插槽数据出错');
				}
				$bindAppArr=array(
					'appName'=>$put['bindApp']['appName'],
					'pkgName'=>$put['bindApp']['pkgName'],
					'versionCode'=>$put['bindApp']['versionCode'],
					'url'=>$put['bindApp']['url'],
				);
			}

			/*switch ($put['actionType']) {

				case 'APP':
					if (!empty($put['app']['appName']) && !empty($put['app']['pkgName'])  ) {
						$actionArr=array(
							'actionType'=>$put['actionType'],
							'appName'=>$put['app']['appName'],
							'pkgName'=>$put['app']['pkgName']
						);
					}else{
						result('app数据类型数据出错');
					}
					break;
				case 'ACTION':
					if (!empty($put['action']['appName']) && !empty($put['action']['detailName']) &&!empty($put['action']['action']) ) {
						if (!is_array($put['action']['extraData'])) {
							$put['action']['extraData'] = array();
						}else{
							foreach ($put['action']['extraData'] as $key => $value) {
								if (!isset($value['key']) || !isset($value['value'])) {
									$put['action']['extraData'] = array();
									break;
								}
								if (isset($value['type'])) {
									if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
										unset($put['action']['extraData'][$key]['type']);
									}
								}
							}
						}
						$extraData = json_encode($put['action']['extraData'],JSON_UNESCAPED_UNICODE);
						$actionArr=array(
							'actionType'=>$put['actionType'],
							'appName'=>$put['action']['appName'],
							'detailName'=>$put['action']['detailName'],
							'action'=>$put['action']['action'],
							'extraData'=>$extraData,
						);
					}else{
						result('action数据类型数据出错');
					}
					break;
				case 'COMPONENT':
					if (!empty($put['component']['appName']) && !empty($put['component']['detailName']) &&!empty($put['component']['component']) &&!empty($put['component']['clsName']) ) {
						if (!is_array($put['component']['extraData'])) {
							$put['component']['extraData'] = array();
						}else{
							foreach ($put['component']['extraData'] as $key => $value) {
								if (!isset($value['key']) || !isset($value['value']) ) {
									$put['component']['extraData'] = array();
									break;
								}
								if (isset($value['type'])) {
									if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
										unset($put['component']['extraData'][$key]['type']);
									}
								}
							}
						}
						$extraData = json_encode($put['component']['extraData'],JSON_UNESCAPED_UNICODE);
						$actionArr=array(
							'actionType'=>$put['actionType'],
							'appName'=>$put['component']['appName'],
							'detailName'=>$put['component']['detailName'],
							'component'=>$put['component']['component'],
							'clsName'=>$put['component']['clsName'],
							'extraData'=>$extraData,
						);
					}else{
						result('component数据类型数据出错');
					}
					break;
				case 'URI':
					if (!empty($put['uri']['uri']) && !empty($put['uri']['uriName']) ) {
						$actionArr=array(
							'actionType'=>$put['actionType'],
							'uri'=>$put['uri']['uri'],
							'uriName'=>$put['uri']['uriName'],
						);
					}else{
						result('uri数据类型数据出错');
					}
					break;
				default:
					result('数据类型出错');
					break;
			}*/

			if (!$actionArr = checkQuickOperationActionApp($put)) {
				result('跳转信息有误');
			}
			//添加运营坑位

			$quickEntrySlotId = D('QuickEntrySlot','Desktop')->addQuickEntry($quickEntrySlotArr);
			if (!empty($actionArr)) {
				//添加运营坑位数据类型
				$actionArr['quickEntryId'] = $quickEntrySlotId;
				D('QuickEntrySlotAction','Desktop')->addQuickEntrySlotAction($actionArr);
			}
			if (!empty($bindAppArr)) {
				//添加运营坑位槽位绑定
				$bindAppArr['quickEntryId'] =  $quickEntrySlotId;
				D('QuickEntrySlotBindApp','Desktop')->addQuickEntrySlotBindApp($bindAppArr);
			}

			result();

		}
		/**
		 * 桌面管理_基础数据快捷坑位列表
		 * {"slotGroupId":"1","slotIDArr":["103"]}
		 * @return [type] [description]
		 */
		/*public function quickEntrySlotArrLists()
		{
			$put = I('put.');
			$res['extra'] = array();
			if (!empty($put['slotGroupId'])&&!empty($put['slotIDArr'])&&is_array($put['slotIDArr'])) {
				$res['extra'] = D('OperationSlot','Desktop')->operationSlotArrLists($put['slotGroupId'],$put['slotIDArr']);
			}
			result(true,$res);
		}*/
		/**
		 * 桌面管理_基础数据修改快捷坑位
		 * POST /desktop/modifyQuickEntrySlot
		 * 跳转到应用
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"APP",  "app":{"appName":"爱奇艺", "pkgName":"aqy.com","extraData":{"key":"value"}}}
		 *
		 *跳转到详情页--ACTION类型
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"ACTION",  "action":{"appName":"爱奇艺", "detailName":"琅琊榜","action":"aqy.com.xxx","extraData":{"key":"value"}}}
		 *
		 *跳转到详情页--COMPONENT类型
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"COMPONENT",  "component":{"appName":"爱奇艺", "detailName":"琅琊榜","component":"aqy.com.xxx", "clsName":"aqy.com.xx.xxx","extraData":{"key":"value"}}}
		 *
		 *跳转到链接
		 *{"slotID":100, "layout":"IMAGE", "pic1":"http://xxxx", "pic2":"http://xxxx", "pic3":"http://xxxx", "videos":[{"url":"http://xx", "duration":234}], "bindApp":{"appName":"爱奇艺","pkgName":"com.aqiy.xx", "versionCode":"2343", "url":"http://xxx", "autoInstall":"true"}, "actionType":"URI",  "uri":"http://xxxxx"}
		 */
		public function modifyQuickEntrySlot()
		{
			$put = I('put.');
			if (!empty($put['id'])&&!empty($put['actionType']) && !empty($put['normalDrawable'])  &&  !empty($put['focusedDrawable'])  && !empty($put['slotId'])  && ($put['isEditable'] =='false' || $put['isEditable'] =='true') ) {

				$QuickEntrySlotObject = D('QuickEntrySlot','Desktop');
				$quickEntrySlot = $QuickEntrySlotObject->getValOneForId($put['id']);
				if (!$quickEntrySlot) {
					result('没有找到此快捷坑位');
				}
				$DesktopDownloadQuickEntryObject = D('DesktopDownloadQuickEntry','Desktop');
				if ($quickEntrySlot['slotId'] != $put['slotId'] ) {
					$isQuickEntrySlot = $DesktopDownloadQuickEntryObject->getDesktopNameArrForSlotID($quickEntrySlot['slotId']);

					if (!empty($isQuickEntrySlot)) {
						foreach ($isQuickEntrySlot as  $value) {
							$desktopNameArr[] = $value['name'] ;
						}
						$desktopNameStr = implode('，', $desktopNameArr);
						result('要修改的坑位ID有桌面'.$desktopNameStr.'使用');
					}
				}

				$res = $QuickEntrySlotObject->getValOneForNotIDForSlotID($put['id'],$put['slotId']);
				if ($res) {
					result('快捷坑位ID已存在');
				}

				$quickEntrySlotIDInfo = $QuickEntrySlotObject->quickEntrySlotLists($quickEntrySlot);
				$desktopSourceUpdateTime = false;
				//检查数据是否有改动
				foreach ($quickEntrySlotIDInfo['extra'] as $key => $value) {
					if (isset($quickEntrySlotIDInfo['extra']['bindApp'])&&!isset($put['bindApp'])) {
						$desktopSourceUpdateTime = true;
						break;
					}elseif (!isset($quickEntrySlotIDInfo['extra']['bindApp'])&&isset($put['bindApp'])) {
						$desktopSourceUpdateTime = true;

						break;
					}

					if ($key == 'action') {
						foreach ($value as $k => $v) {

							if ($k == 'extraData' ) {

								if (count($quickEntrySlotIDInfo['extra'][$key][$k]) != count($put[$key][$k])) {
									$desktopSourceUpdateTime = true;

									break;
								}else{
									foreach ($v as $a => $b) {
										if ($v[$a]['key'] !=$put[$key][$k][$a]['key']) {
											$desktopSourceUpdateTime = true;

											break;
										}elseif ($v[$a]['value'] !=$put[$key][$k][$a]['value']) {
											$desktopSourceUpdateTime = true;

											break;
										}elseif ($v[$a]['type'] != $put[$key][$k][$a]['type']) {

											$desktopSourceUpdateTime = true;

											break;
										}
									}
								}

							}elseif ($quickEntrySlotIDInfo['extra'][$key][$k] !=$put[$key][$k]) {
								$desktopSourceUpdateTime = true;

								break;
							}
						}
					}elseif ($key == 'bindApp') {
						foreach ($value as $k => $v) {
							if ($value[$k] != $put[$key][$k]) {
								$desktopSourceUpdateTime = true;

								break;
							}
						}
					}else{
						if ($quickEntrySlotIDInfo['extra'][$key] != $put[$key]) {
							$desktopSourceUpdateTime = true;

							break;
						}
					}
				}
				//检查数据
				$quickEntrySlotArr = array(
					'slotId' =>$put['slotId'],
					'title'=>!empty($put['title'])?$put['title']:'',
					'isEditable' =>$put['isEditable'],
					'normalDrawable' =>$put['normalDrawable'],
					'focusedDrawable' =>$put['focusedDrawable'],
				);
				if (!empty($put['bindApp'])) {
					if (empty($put['bindApp']['appName'])  || empty($put['bindApp']['pkgName'])  || !isset($put['bindApp']['versionCode'])   || empty($put['bindApp']['url']) ) {
						result('插槽数据出错');
					}
					$bindAppArr=array(
						'appName'=>$put['bindApp']['appName'],
						'pkgName'=>$put['bindApp']['pkgName'],
						'versionCode'=>$put['bindApp']['versionCode'],
						'url'=>$put['bindApp']['url'],
					);
				}
				/*switch ($put['actionType']) {
					case 'APP':
						if (!empty($put['app']['appName']) && !empty($put['app']['pkgName'])  ) {
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['app']['appName'],
								'pkgName'=>$put['app']['pkgName']
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'ACTION':
						if (!empty($put['action']['appName']) && !empty($put['action']['detailName']) &&!empty($put['action']['action']) ) {
							if (!is_array($put['action']['extraData'])) {
								$put['action']['extraData'] = array();
							}else{
								foreach ($put['action']['extraData'] as $key => $value) {
									if (!isset($value['key']) || !isset($value['value'])) {
										$put['action']['extraData'] = array();
										break;
									}
									if (isset($value['type'])) {
										if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
											unset($put['action']['extraData'][$key]['type']);
										}
									}
								}
							}
							$extraData = json_encode($put['action']['extraData'],JSON_UNESCAPED_UNICODE);
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['action']['appName'],
								'detailName'=>$put['action']['detailName'],
								'action'=>$put['action']['action'],
								'extraData'=>$extraData,
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'COMPONENT':
						if (!empty($put['component']['appName']) && !empty($put['component']['detailName']) &&!empty($put['component']['component']) &&!empty($put['component']['clsName']) ) {
							if (!is_array($put['component']['extraData'])) {
								$put['component']['extraData'] = array();
							}else{
								foreach ($put['component']['extraData'] as $key => $value) {
									if (!isset($value['key']) || !isset($value['value'])) {
										$put['component']['extraData'] = array();
										break;
									}
									if (isset($value['type'])) {
										if (!($value['type'] == 'int' || $value['type'] == 'long' || $value['type'] == 'float'  || $value['type'] == 'double'  || $value['type'] == 'boolean' || $value['type'] == 'char'  || $value['type'] == 'string' )) {
											unset($put['component']['extraData'][$key]['type']);
										}
									}

								}
							}
							$extraData = json_encode($put['component']['extraData']);
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'appName'=>$put['component']['appName'],
								'detailName'=>$put['component']['detailName'],
								'component'=>$put['component']['component'],
								'clsName'=>$put['component']['clsName'],
								'extraData'=>$extraData,
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					case 'URI':
						if (!empty($put['uri']['uri']) && !empty($put['uri']['uriName']) ) {
							$actionArr=array(
								'actionType'=>$put['actionType'],
								'uri'=>$put['uri']['uri'],
								'uriName'=>$put['uri']['uriName'],
							);
						}else{
							result('数据类型数据出错');
						}
						break;
					default:
						result('数据类型出错');
						break;
				}*/
				if (!$actionArr = checkQuickOperationActionApp($put)) {
					result('跳转信息有误');
				}
				//添加运营坑位
				$quickEntrySlotId = $QuickEntrySlotObject->modifyQuickEntrySlot($put['id'],$quickEntrySlotArr);

				//添加运营坑位数据类型
				if (!empty($actionArr)) {
					$quickEntrySlotActionObject = D('QuickEntrySlotAction','Desktop');
					$quickEntrySlotActionObject->modifyQuickEntrySlotAction($put['id'],$actionArr);
				}else{
					D('QuickEntrySlotAction','Desktop')->deleteQuickEntrySlotAction($quickEntrySlotId);
				}

				if (!empty($bindAppArr)) {
					//添加运营坑位槽位绑定
					$quickEntrySlotBindAppObject = D('QuickEntrySlotBindApp','Desktop');
					$quickEntrySlotBindAppObject->modifyQuickEntrySlotBindApp($put['id'],$bindAppArr);
				}else{
					D('QuickEntrySlotBindApp','Desktop')->deleteQuickEntrySlotBindApp($quickEntrySlotId);
				}
				//修改桌面修改时间
				$DesktopDownloadQuickEntryObject->modifyQuickEntrySlot($quickEntrySlot['slotId'],$desktopSourceUpdateTime);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据_快捷坑位移动
		 * post /desktop/moveQuickEntrySlot
		 * {"id":"快捷坑位ID","groupId":"快捷快捷坑位组ID"}
		 * @return [type] [description]
		 */
		public function moveQuickEntrySlot()
		{
			$put = I('put.');
			D('QuickEntrySlot','Desktop')->moveQuickEntrySlot($put);
			result();
		}
		/**
		 * 桌面管理_基础数据快捷坑位列表
		 * get /desktop/quickEntrySlotLists
		 * get /desktop/quickEntrySlotLists?id=1
		 * get /desktop/quickEntrySlotLists?slotId=1
		 * get /desktop/quickEntrySlotLists?page=1&pageSize=xx
		 * get /desktop/quickEntrySlotLists?page=1&pageSize=xx&name=xx
		 * @return [type] [description]
		 */
		public function quickEntrySlotLists()
		{
			$get = I('get.');
			$res = D('QuickEntrySlot','Desktop')->quickEntrySlotLists($get);
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据获取快捷坑位slotID
		 * post /desktop/getQuickEntrySlotId
		 * ['"1","2"]
		 * @param string $value [description]
		 */
		public function getQuickEntrySlotId()
		{
			$put = I('put.');
			$res = D('QuickEntrySlot','Desktop')->getSlotIDForIdArr($put);
			result(true,$res);
		}
		/**
		 * 桌面管理_基础数据删除快捷坑位
		 * post /desktop/deleteQuickEntrySlot
		 *["1","2"]
		 * @param string $value [description]
		 */
		public function deleteQuickEntrySlot()
		{
			$put = I('put.');
			if (empty($put) || !is_array($put)) {

				result('param');
			}
			$res = D('QuickEntrySlot','Desktop')->getValOneForIdArr($put);
			if (!$res) {
				result('快捷坑位不存在');
			}
			$reason = '';
			foreach ($res as $value) {
				$res1 = D('DesktopDownloadQuickEntry','Desktop')->getQuickEntrySlot($value['slotId']);

				if (!empty($res1)) {
					$desktopName = '正在桌面';
					foreach ($res1 as $v) {
						$desktopName .= $v['desktopName'] . '、';
					}
					$reason .= '快捷入口坑位'.$value['slotId'] . trim($desktopName,'、') .'使用，';
				}else{
					$deleteIDArr[] = $value['id'];
				}
			}
			if (!empty($deleteIDArr)) {
				$deleteIDStr = implode(',', $deleteIDArr);
				/*D('QuickEntrySlotBindApp','Desktop')->deleteQuickEntrySlotBindAppForIDArr($deleteIDStr);
				D('QuickEntrySlotAction','Desktop')->deleteQuickEntrySlotActionForIDArr($deleteIDStr);*/
				D('QuickEntrySlot','Desktop')->deleteQuickEntrySlotForIDArr($deleteIDStr);
			}
			if (!empty($reason)) {
				$res = array('reason'=>trim($reason,'，'));
				result(true,$res);
			}else{
				result();
			}



		}
		/**
		 * 桌面管理_基础数据获取应用运营坑位桌面
		 *GET /desktop/getOperationSlot?id=1
		 *return {"result":"ok", "extra":[{"desktopID":1, "desktopName":"aaaa"}]}
		 * @return [type] [description]
		 */
		/*public function getQuickEntrySlot()
		{
			$id = I('get.id');
			$res['extra'] = array();
			if (!empty($id)) {
				$slotID = D('OperationSlot','Desktop')->getValOneForId($id);
				if ($slotID) {
					$res['extra'] = D('DesktopBlockInfo','Desktop')->getOperationSlot($slotID['slotID'],$slotID['slotGroupId']);
					if (!$res['extra']) {
						$res['extra'] = array();
					}
				}
			}
			result(true,$res);
		}*/


		//---------------------------------------------------------------------------------------------------------------------------------------

		/**
		 * 桌面管理_基础数据添加屏
		 * post /desktop/addFragment
		 * post_data{"name":"test","property":[{"block_id":1,"x":344,"y":20,"bg":"#aa68c1"}]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addFragment()
		{
			$put = I('put.');
			if (!empty($put['name'])&& !empty($put['property']) ) {

				if (!empty($put['property'])) {
					foreach ($put['property'] as $key => $value) {
						if (!isset($value['type']) || !isset($value['w']) || !isset($value['h']) || !isset($value['yw']) || !isset($value['yh']) || !isset($value['x']) || !isset($value['y']) || !isset($value['bg'])) {
							result('param');
						}
					}
				}
				$fragmentTypeId = D('FragmentType','Desktop')->addFragmentType($put['name']);

				foreach ($put['property'] as $key => $value) {
					$put['property'][$key]['fragment_type_id'] = $fragmentTypeId;
					if ( isset($put['property'][$key]['id']) ) {
						unset($put['property'][$key]['id']);
					}
				}
				D('Fragment','Desktop')->addFragment($put['property']);
			}elseif (!empty($put['name'])) {
				D('FragmentType','Desktop')->addFragmentType($put['name']);

			}else{
				result('param');
			}
			result();
		}
		/**
		 * 桌面管理_基础数据删除屏
		 * get /desktop/deleteFragment?id=1
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteFragment()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('Fragment','Desktop')->deleteFragment($id);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据屏列表
		 * get /desktop/fragmentLists?id=1
		 * get /desktop/fragmentLists?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function fragmentLists()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('Fragment','Desktop')->fragmentLists($id);
			}else{
				D('Fragment','Desktop')->fragmentLists();
			}
		}
		/**
		 * 桌面管理_基础数据修改屏
		 * post /desktop/modifyFragment
		 * post_data{"id":1,"name":"test","property":[{"w":"1010","h":"100","yw":"720","yh":"540","x":344,"y":20,"bg":"#aa68c1"}]} id 屏ID name 屏名字 extra 屏内容 （  x为left宽度  y为top宽度 block_id 块ID bg 背景色）
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyFragment()
		{
			$put = I('put.');

			if (!empty($put['id'])&&!empty($put['name'])&&( !empty($put['property']) || !empty($put['quickEntry']) )) {

				foreach ($put['property'] as $key => $value) {
					if (!isset($value['type']) || !isset($value['w']) || !isset($value['h']) || !isset($value['yw']) || !isset($value['yh']) || !isset($value['x']) || !isset($value['y']) || !isset($value['bg'])) {
						result('param');
					}else{
						$put['property'][$key]['fragment_type_id'] = $put['id'];
					}
				}
				$fragmentTypeId = D('FragmentType','Desktop')->modifyFragmentType($put['id'],$put['name']);
				D('Fragment','Desktop')->modifyFragment($put['id'],$put['property']);

			}elseif (!empty($put['id']) &&!empty($put['name'])) {
				D('FragmentType','Desktop')->modifyFragmentType($put['id'],$put['name']);
				D('Fragment','Desktop')->modifyFragment($put['id']);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据添加基础导航
		 * post /desktop/addNav
		 * postData {"name":"导航名字","x":"导航X坐标","y":"导航y坐标","interval":"导航间距","iconIds":[7]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addNav()
		{
			$put = I('put.');
			if (!empty($put['name'])&&isset($put['x'])&&isset($put['y'])&&isset($put['interval'])) {
				$nav = D('Nav','Desktop');
				if (empty($put['extra'])) {
					if (!$nav->addNav($put['name'],$put['x'],$put['y'],$put['interval'])) {
						result('添加导航失败');
					}
				}else{
					$navId = $nav->addNav($put['name'],$put['x'],$put['y'],$put['interval']);
					$data = array();
					if ($navId) {
						foreach ($put['extra'] as $key => $value) {
							if (empty($value['functionId'])) {
								$nav->deleteNav($navId);
								result('请添加功能ID');
							}
							if (empty($value['forcusPath']) || empty($value['normalPath'])  ) {
								$nav->deleteNav($navId);
								result('请上传导航焦点图片与正常图片');

							}
							$data[$key]['navId'] = $navId;
							$data[$key]['forcusPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['forcusPath']);
							$data[$key]['normalPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['normalPath']);
							$data[$key]['functionId'] = isset($value['functionId'])?$value['functionId']:"1";
							$data[$key]['currentDrawable'] = isset($value['currentDrawable'])?str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['currentDrawable']):"";
						}
					}else{
						result('添加失败');
					}
					D('NavIconList','Desktop')->addNavIconList($data);
				}
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除基础导航
		 * get /desktop/deleteNav?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteNav($id = null)
		{
			if (empty($id)) {
				$id = I('get.id');
			}
			if (!empty($id)) {
				D('Nav','Desktop')->deleteNav($id);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据基础导航列表
		 * get /desktop/navLists
		 * return {"extra":[{"id":"2","name":"导航名字","x":"导航X坐标","y":"导航y坐标","interval":"导航间距","extra":[{"name":"控件名称","normal_name":"正常状态图片名称","normal_path":"正常状态图片路径","forcus_name":"焦点状态图片名称","forcus_path":"焦点状态图片路径"}]}],"result":"ok"}
		 * get /desktop/navLists?id=2
		 * @return {"extra":{"id":"2","name":"导航名字","extra":[{"name":"控件名称","normal_name":"正常状态图片名称","normal_path":"正常状态图片路径","forcus_name":"焦点状态图片名称","forcus_path":"焦点状态图片路径"}]}},"result":"ok"}
		 */
		public function navLists()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('NavIconList','Desktop')->navLists($id);
			}else{
				D('NavIconList','Desktop')->navLists();
			}
		}
		/**
		 * 桌面管理_基础数据修改基础导航
		 * post /desktop/modifyNav
		 * postData {"id":"2","name":"导航名字","iconIds":[]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function modifyNav()
		{
			$put = I('put.');
			if (!empty($put['id'])&&!empty($put['name'])&&isset($put['x'])&&isset($put['y'])&&isset($put['interval'])) {
				if (empty($put['extra'])){
					D('Nav','Desktop')->modifyNav($put['id'],$put['name'],$put['x'],$put['y'],$put['interval']);
					D('NavIconList','Desktop')->deleteNavIconList($put['id']);
					result();
				}else{
					D('Nav','Desktop')->modifyNav($put['id'],$put['name'],$put['x'],$put['y'],$put['interval']);
					$data = array();
					foreach ($put['extra'] as $key => $value) {
						$data[$key]['navId'] = $put['id'];
						$data[$key]['forcusPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['forcusPath']);
						$data[$key]['normalPath'] = str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['normalPath']);
						$data[$key]['functionId'] = isset($value['functionId'])?$value['functionId']:"1";
						$data[$key]['currentDrawable'] = isset($value['currentDrawable'])?str_replace(C('DOWNLOAD_IMG_PREFIX_HOST'),'',$value['currentDrawable']):"";
					}

					D('NavIconList','Desktop')->modifyNavIconList($put['id'],$data);
				}

			}else{
				result('param');
			}
		}
		/*public function serverPhpInfo()
		{
			phpinfo();
		}*/
		/**
		 * 桌面管理_基础数据添加图标（改完成）
		 * post /desktop/addIcon
		 * 表单上传
		 * type=file name=normalFile   name =forcusFile
		 * 附加信息   name=extraData   value={"name":"控件名称"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addIcon()
		{
			set_time_limit(0);
			$put = I('post.extraData');
			$put = json_decode($put,true);
			if (!empty($put['name'])) {
				require_once '../Base/function/Form.class.php';
				$form = new \form();
				$formData = $form->getFormFile();
				if (!empty($formData['normalFile'])&&!empty($formData['forcusFile'])) {
					require_once '../Base/Ossupclass/OssBase.class.php';
					$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
					$res = $base->uploadFile($formData);
					$config = array(
						'rootPath'      =>  C('LOCALHOST_PATH_ADDR') .'pic/',
						'savePath'   =>    '',
						'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
						'saveName'   => '',
						'autoSub' => false
					);
					$upload = new \Think\Upload($config);// 实例化上传类
					$res = $form->getFileNameForMd5();
					if ($res) {
						foreach ($res as $key => $value) {
							if (!file_exists( C('LOCALHOST_PATH_ADDR') .'pic/'. $value['name'])) {
								$info   =   $upload->uploadOne($res[$key]);
								if(!$info) {// 上传错误提示错误信息
									result($upload->getError());
								}
							}
							if ($key=='normalFile') {
								$options['normalPath'] = 'pic/'. $value['name'];
							}elseif ($key=='forcusFile') {
								$options['forcusPath'] = 'pic/'. $value['name'];
							}
						}
					}
				}else{
					result('没有上传文件');
				}
				$options['name']=$put['name'];
				D('Icon','Desktop')->addIcon($options);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据修改图标
		 * post /desktop/modifyIcon
		 * 表单上传
		 * type=file name=normalFile   name =forcusFile
		 * 附加信息   name=extraData   value={"id"="1","name":"控件名称"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyIcon()
		{
			set_time_limit(0);
			$put = I('post.extraData');
			$put = json_decode($put,true);
			if (!empty($put['name'])&&!empty($put['id'])) {
				require_once '../Base/function/Form.class.php';
				$form = new \form();
				$formData = $form->getFormFile();
				if (!empty($formData['normalFile'])||!empty($formData['forcusFile'])) {
					require_once '../Base/Ossupclass/OssBase.class.php';
					$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
					$res = $base->uploadFile($formData);
					$config = array(
						'rootPath'      =>  C('LOCALHOST_PATH_ADDR') .'pic/',
						'savePath'   =>    '',
						'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
						'saveName'   => '',
						'autoSub' => false
					);
					$upload = new \Think\Upload($config);// 实例化上传类
					$res = $form->getFileNameForMd5();
					if ($res) {
						foreach ($res as $key => $value) {
							if (!file_exists( C('LOCALHOST_PATH_ADDR') .'pic/'. $value['name'])) {
								$info   =   $upload->uploadOne($res[$key]);
								if(!$info) {// 上传错误提示错误信息
									result($upload->getError());
								}
							}
							if ($key=='normalFile') {
								$options['normalPath'] = 'pic/'. $value['name'];
							}elseif ($key=='forcusFile') {
								$options['forcusPath'] = 'pic/'. $value['name'];
							}
						}
					}
				}

				$options['name']=$put['name'];

				D('Icon','Desktop')->modifyIcon($put['id'],$options);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据删除图标（改完成）
		 * get /desktop/deleteIcon?id=1
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteIcon()
		{
			$id = I('get.id');
			if (!empty($id)) {
				D('Icon','Desktop')->deleteIcon($id);
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_基础数据图标列表（改完成）
		 * get /desktop/iconLists
		 * get /desktop/iconLists?page=xx&pageSize=xxx
		 * get /desktop/iconLists?page=xx&pageSize=xxx&name=xx
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function iconLists()
		{
			// D('Icon','Desktop')->iconLists();
			$get = I('get.');
			if (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('Icon','Desktop')->iconLists($get['name'],$get['page'],$get['pageSize']);
				}else{
					D('Icon','Desktop')->iconLists($get['name']);
				}
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					D('Icon','Desktop')->iconLists(null,$get['page'],$get['pageSize']);
				}else{
					D('Icon','Desktop')->iconLists();
				}
			}
		}
		//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


		/**
		 * 桌面管理_添加桌面组
		 * post /desktop/addDesktopGroup
		 * {
		 * 	"name":"桌面组名称",
		 * 	"desc":"备注"
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addDesktopGroup()
		{
			$put = I('put.');
			$res = D('DesktopGroup','Desktop')->addDesktopGroup($put);
			result(true,$res);
		}
		/**
		 * 桌面管理_删除桌面组
		 * get /desktop/deleteDesktopGroup?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteDesktopGroup()
		{

			$get = I('get.');
			if (!empty($get['id'])) {
				D('DesktopGroup','Desktop')->deleteDesktopGroup($get['id']);
				result();
			}else{
				result('param');
			}
		}
		/**
		 * 桌面管理_桌面组列表
		 * get /desktop/desktopGroupLists?id=1
		 * /desktop/desktopGroupLists?page=xxxx&pageSize=xxx
		 * /desktop/desktopGroupLists?name=aaa&page=xxxx&pageSize=xxx
		 * @return
		 */
		public function desktopGroupLists()
		{
			$get = I('get.');
			$res = D('DesktopGroup','Desktop')->desktopGroupLists($get);
			result(true,$res);

		}
		/**
		 * 桌面管理_修改桌面组
		 * post /desktop/modifyDesktopGroup
		 * {
		 * 	"id":"桌面组ID",
		 * 	"name":"桌面组名称",
		 * 	"desc":"备注"
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyDesktopGroup()
		{

			$put = I('put.');
			$res = D('DesktopGroup','Desktop')->modifyDesktopGroup($put);
			result(true,$res);

		}
		/**
		 * 桌面管理_移动桌面
		 * post /desktop/removeDesktop
		 * {
		 * 	"idLists":["桌面组ID","桌面组ID"],
		 * 	"groupId":"桌面组ID",
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function moveDesktop()
		{

			$put = I('put.');
			$res = D('Desktop','Desktop')->moveDesktop($put);
			result(true,$res);

		}

		/**
		 * 桌面管理_移动回收站桌面
		 * post /desktop/removeDesktop
		 * {
		 * 	"idLists":["桌面组ID","桌面组ID"],
		 * 	"groupId":"桌面组ID",
		 * }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function moveRecycleDesktop()
		{
			$put = I('put.');
			$res = D('Desktop','Desktop')->moveRecycleDesktop($put);
			result(true,$res);

		}
		/**
		 * 桌面管理_回收删除
		 * post /desktop/deleteRecycleDesktop
		 *["1","2"]
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteRecycleDesktop()
		{
			$put = I('put.');

			D('Desktop','Desktop')->deleteFullArrDesktop($put);

			result();
		}
		/**
		 * 桌面管理_回收站还原桌面
		 * post /desktop/reductionDesktop
		 *["1","2"]
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function reductionDesktop()
		{
			$put = I('put.');
			$res = D('Desktop','Desktop')->reductionDesktop($put);
			result();
		}
		/**
		 * 桌面管理_回收站桌面列表
		 * post /desktop/recycleLists?page=x&pageSize=x
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function recycleLists()
		{
			$get = I('get.');
			$res = D('Desktop','Desktop')->recycleLists($get);
			result(true,$res);

		}
		//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

		/**
		 * 桌面管理_添加桌面
		 * post /desktop/addDesktop
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function addDesktop()
		{
			$put = I('put.');
			$res = D('Desktop','Desktop')->addFullDesktop($put);
			result(true,$res);
		}
		/**
		 * 桌面管理_根据ID获取桌面名字
		 * post /desktop/getDesktopNameForIdArr
		 * ["1","2"]
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function getDesktopNameForIdArr()
		{
			$put = I('put.');
			$res = D('Desktop','Desktop')->getDesktopNameForIdArr($put);
			result(true,$res);
		}
		/**
		 * 桌面管理_删除回收站桌面
		 * get /desktop/deleteFullDesktop?id=1
		 * return {"result":"ok/fail","reason":"xxx"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function deleteDesktop()
		{

			$put = I('put.');
			D('Desktop','Desktop')->DesktopToFalse($put);
			result();

		}
		/**
		 * 桌面管理_桌面列表
		 * get /desktop/desktopLists?id=1
		 * /desktop/desktopLists?page=xxxx&pageSize=xxx
		 * /desktop/desktopLists?name=aaa
		 * /desktop/desktopLists?name=aaa&page=xxxx&pageSize=xxx
		 * @return
		 */
		public function desktopLists()
		{
			$get = I('get.');
			$res = D('Desktop','Desktop')->fullDesktopLists($get);
			result(true,$res);
		}
		/**
		 * 桌面管理_全部桌面列表
		 * /desktop/getAlldesktopLists?page=xxxx&pageSize=xxx
		 * /desktop/getAlldesktopLists?name=aaa
		 * /desktop/getAlldesktopLists?name=aaa&page=xxxx&pageSize=xxx
		 * @return
		 */
		/*public function getAlldesktopLists()
		{
			$get = I('get.');
			$res = D('Desktop','Desktop')->getAlldesktopLists($get);
			result(true,$res);
		}*/
		/**
		 * 桌面管理_修改桌面
		 * post /desktop/modifyDesktop
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function modifyDesktop()
		{
			$put = I('put.');
			$res = D('Desktop','Desktop')->modifyFullDesktop($put);
			result(true,$res);
		}

		/**
		 * 桌面管理_创建桌面屏文件
		 * post  /desktop/createDesktopSlotsFile
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function createDesktopSlotsFile($desktopId=null,$desktop =null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = array();
				if ($desktop===null) {
					$desktop = D('Desktop','Desktop')->getValForId($desktopId);
				}
				if ($desktop) {
					$desktopJson['layoutVersion'] = $desktop['layout_update_time'];
					$desktopJson['sourceVersion'] = $desktop['update_time'];
					$desktopJson['updateInterval'] = 600;
					$desktopJson['slotStyle'] = 'rightAngle';
					$desktopJson['animation'] = $desktop['animation'];
					if ($desktop['animation'] == 'enlarge' || $desktop['animation'] == 'enlarge_rotate') {
						if (empty($desktop['enlargeVal'])) {
							return array('reason'=>'桌面坑位动画效果为放大/放大翻转时，没有放大值');
						}else{
							$desktopJson['enlargeVal'] = $desktop['enlargeVal'];
						}
					}
					$desktopStyle = D('DesktopStyle','Desktop')->createStyleConfig($desktopId,$desktop,$error);
					if (!empty($desktopStyle)) {
						$desktopJson['style'] =  $desktopStyle;
					}
					$desktopJson['screens'] = D('DesktopScreens','Desktop')->createDesktopScreens($desktopId,$error,$desktop['breakout']);
					// $desktopJson['screens'] = D('DesktopScreens','Desktop')->createDesktopScreens($desktopId,$error);
					if ($error) {
						if(!empty($desktopJson['screens']['reason'])){
							return $desktopJson['screens'];
						}
					}

				}else{
					if ($error) {

						return $result = array('reason'=> '桌面不存在');
						// return false;
					}else{
						result('桌面不存在');
					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=> '参数有误');
					// return false;
				}else{
					result('param');
				}
			}
			if (!empty($desktopJson)) {
				/*echo  json_encode($desktopJson,JSON_UNESCAPED_UNICODE);
				die;*/
				return $desktopJson;
			}else{
				return false;
			}
		}
		/**
		 *  桌面管理_生成桌面布局文件
		 *  get /desktop/addDesktopLayout?id=1
		 * @param string $value [description]
		 */
		/*public function addDesktopLayout($desktopId =null)
		{
			if ($desktopId ===null) {
				$desktopId =I('get.id');
			}
			$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			if (!$desktop) {
				result('桌面不存在');
			}
			$res = D('DesktopLayout','Desktop')->where("`model`='%s' and `version`='%s'",array($desktop['name'],$desktop['update_time']))->find();
			if ($res) {
				result('该桌面没有修改');
			}
			$desktopJson = $this->createDesktopSlotsFile($desktopId);
			$this->createDesktopLayout($desktopId,$desktop,$desktopJson);
		}*/
		/**
		 *桌面管理_生成桌面版本
		 */
		/*public function createDesktopLayout($desktopId,$desktop,$desktopJson,$error=false)
		{
			$slotsFileStr = json_encode($desktopJson,JSON_UNESCAPED_UNICODE);
			//生成发布
			$myfile = @fopen("./slots.json", "w");
			if (!$myfile) {
				return $result = array('reason'=> '无法打开 slots.json文件');
			}
			fwrite($myfile, $slotsFileStr);
			fclose($myfile);
			$formData = array(
				"desktopSlotsFile" => array(
			    		"extension" =>"json",
					"md5_file" => $desktop['name']."layout_".$desktop['update_time'],
			    		"filepath" => './slots.json',
				    	"size" => filesize("./slots.json")
			  	)
			);
			require_once '../Base/Ossupclass/OssBase.class.php';
			$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
			$res = $base->uploadFile($formData);
			$options = array(
				'model'=>$desktop['name'],
				'path'=>$res['desktopSlotsFile']['oss'],
				'version'=>$desktop['update_time'],
				'time'=>time()
			);
			unlink('./slots.json');
			$res = D('DesktopLayout','Desktop')->addDesktopLayout($options,$error);
			if ($error) {
				return true;
			}else{
				result();
			}
		}*/
		/**
		 * 桌面管理_自动发布桌面布局与资源包（新的）
		 * POST /desktop/autoPublishDesktop
		 * POSTDATA:  {"desktopIDList":["1", "2"],"type":"group/ALL/AB","groupId":"组ID"}  PS：当type=group时，groupId存在
		 * @return {"result":"failed/ok", failList:[{"desktopName":"a", "reason":"生成版本失败/发布版本失败，请检查后手动发布"}]}
		 */
		public function autoPublishDesktop($put = null)
		{
			set_time_limit(0);
			if ($put ===null) {
				$put = I('put.');
			}
			if (is_array($put['desktopIDList'])&&!empty($put['desktopIDList'])&&($put['type']=='group' || $put['type']=='ALL'  || $put['type']=='AB' )) {
				if ($put['type']=='group') {
					if (!empty($put['groupId'])) {
						$group = D('Group')->getValOneForId($put['groupId']);
						if (!$group) {
							$reason = '组不存在，请选择正确组';
						}
					}
					if ($put['type']=='AB') {

						if (!isset($put['AB'])) {

							if ($error) {
								return $result = array('reason'=> '请添加灰度值');
							}else{
								result('请添加灰度值');
							}
						}
					}
				}

				$desktopIdLists = '';
				foreach ($put['desktopIDList'] as $key => $value) {
					$value = (int)$value;
					$desktopIdLists .=is_int($value)?$value.',':result('param');
				}
				$desktopIdLists = trim($desktopIdLists,',');
				$desktopLists = D('Desktop','Desktop')->getOperationDesktopLists($desktopIdLists);
				if (!empty($reason)) {
					foreach ($desktopLists as $key => $value) {
						$msg[] = array(
							'desktopName'=>$value['name'],
							'reason'=>$reason
						);
					}
					result('publish',$msg);
				}
				//桌面列表
				if (!empty($desktopLists)) {
					foreach ($desktopLists as $key => $value) {
						//生成桌面版本
						$desktopSourceFiles[$key] = $this->createDesktopVersion($value['id'],$value,true);
						if (!empty($desktopSourceFiles[$key]['reason'])) {

							if ($desktopSourceFiles[$key]['reason'] != '该桌面没有修改') {
								$msg[] = array(
									'desktopName'=>$value['name'],
									'reason'=>"生成桌面版本失败：".$desktopSourceFiles[$key]['reason']
								);
								$desktopLists[$key]['sourceResult'] = 'fail';
							}else{
								unset($desktopSourceFiles[$key]['reason']);
							}
						}
					}
					foreach ($desktopLists as $key => $value) {
						//发布桌面版本
						if (empty($value['sourceResult'])) {

							$version = D('DesktopVersion','Desktop')->getValOneForModel($value['name']);
							$publish = D('DesktopVersionPublish','Desktop')->getValAllForModelForType($value['name'],$put['type']);

							if ($publish) {
								foreach ($publish as $k => $v) {
									//去除桌面旧接口发布 start

									D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($v['id']);
									unset($v['id']);
									$v['reason'] = '下架';
									D('DesktopVersionPublishHistory','Desktop')->addDesktopVersionPublishHistory($v);
									$put['id']=$version['id'];
									//添加发布版本
									$res = $this->publishDesktopVersion($put,$version,true);
									//去除桌面旧接口发布 end
								}
							}else{
								$put['id']=$version['id'];
								//添加发布版本
								$res = $this->publishDesktopVersion($put,$version,true);
								if (!empty($res['reason'])) {
									if ($res['reason'] != '已发布') {
										if ($res['reason'] == 'duplicate') {
											$msg[] = array(
												'desktopName'=>$value['name'],
												'reason'=>"发布桌面版本失败:桌面发布重复，请先手动删除"
											);
										}else{
											$msg[] = array(
												'desktopName'=>$value['name'],
												'reason'=>"发布桌面版本失败:".$res['reason']
											);
										}

									}
								}

							}
						}
					}
				}
				if (!empty($msg)) {
					result('publish',$msg);
				}else{
					result();
				}
			}else{
				$msg[] = array(
					'desktopName'=>'桌面参数错误',
					'reason'=>" "
				);
				result('publish',$msg);
			}
		}
		/**
		 * 旧的自动发布桌面布局与资源包（准备弃除）
		 * POST /desktop/autoPublishDesktopOld
		 * POSTDATA:  {"desktopIDList":["1", "2"],"type":"group/ALL","groupId":"组ID"}  PS：当type=group时，groupId存在
		 * @return {"result":"failed/ok", failList:[{"desktopName":"a", "reason":"生成版本失败/发布版本失败，请检查后手动发布"}]}
		 */
		/*public function autoPublishDesktopOld()
		{
			set_time_limit(0);
			$put = I('put.');
			if (is_array($put['desktopIDList'])&&($put['type']=='group' || $put['type']=='ALL' )) {
				if ($put['type']=='group') {
					if (!empty($put['groupId'])) {
						$group = D('Group')->find($put['groupId']);
						if (!$group) {
							$reason = '组不存在，请选择正确组';
						}
					}
				}
				$desktopIdLists = '';
				foreach ($put['desktopIDList'] as $key => $value) {
					$value = (int)$value;
					$desktopIdLists .=is_int($value)?$value.',':result('param');
				}
				$desktopIdLists = trim($desktopIdLists,',');
				$desktopLists = D('Desktop','Desktop')->getOperationDesktopLists($desktopIdLists);
				if (!empty($reason)) {
					foreach ($desktopLists as $key => $value) {
						$msg[] = array(
							'desktopName'=>$value['name'],
							'reason'=>$reason
						);
					}
					result('publish',$msg);
				}
				//桌面列表
				if (!empty($desktopLists)) {
					foreach ($desktopLists as $key => $value) {
						//生成版本
						$desktopSlotsFiles[$key] = $this->createDesktopSlotsFile($value['id'],$value,true);
 						// var_dump($desktopSlotsFiles[$key]);
 						if (!empty($desktopSlotsFiles[$key]['reason'])) {
							$msg[] = array(
								'desktopName'=>$value['name'],
								'reason'=>"生成布局失败：".$desktopSlotsFiles[$key]['reason']
							);
							$desktopLists[$key]['result'] = 'fail';
						}else{
							$this->createDesktopLayout($value['id'],$value,$desktopSlotsFiles[$key],true);
						}
						//生成资源包
						$desktopSourceFiles[$key] = $this->createDesktopSource($value['id'],$value,true);
						// var_dump($desktopSourceFiles[$key]);
						if (!empty($desktopSourceFiles[$key]['reason'])) {

							if ($desktopSourceFiles[$key]['reason'] != '该桌面没有修改') {
								$msg[] = array(
									'desktopName'=>$value['name'],
									'reason'=>"生成资源包失败：".$desktopSourceFiles[$key]['reason']
								);
								$desktopLists[$key]['sourceResult'] = 'fail';
							}else{
								unset($desktopSourceFiles[$key]['reason']);
							}
						}
					}
					foreach ($desktopLists as $key => $value) {
						//发布资源包
						if (empty($value['sourceResult'])) {

							$source = D('DesktopSource','Desktop')->where("`model`='%s'",array($value['name']))->order("`version`  DESC")->find();
							$publish = D('DesktopSourcePublish','Desktop')->where("`model`='%s' and `type`='%s'",array($value['name'],$put['type']))->select();

							if ($publish) {
								foreach ($publish as $k => $v) {
									// $put = $v;
									//先删除发布版本

									$json = getUrl(DESKTOP_SOURCE_ACCESS_ADDR.'access/publish/del?item=source3.0&id='.$v['id']);

									if ($json) {
										$res = json_decode($json,true);
										if ($res['result'] !='ok') {
											if (strstr($res['reason'],'not such id')!=true) {
												$msg[] = array(
													'desktopName'=>$value['name'],
													'reason'=>"发布资源包失败：".$res['reason']
												);
											}else{
												$put['id']=$source['id'];
												//添加发布版本
												$res = $this->publishDesktopSource($put,$source,true);
												if (!empty($res['reason'])) {
													if ($res['reason'] != '已发布') {
														$msg[] = array(
															'desktopName'=>$value['name'],
															'reason'=>"发布资源包失败:".$res['reason']
														);
													}
												}
											}

										}else{
											D('DesktopSourcePublish','Desktop')->deleteDesktopSourcePublish($v['id']);
											unset($v['id']);
											$v['reason'] = '下架';
											D('DesktopSourcePublishHistory','Desktop')->addDesktopSourcePublishHistory($v);
											$put['id']=$source['id'];
											//添加发布版本
											$res = $this->publishDesktopSource($put,$source,true);
											if (!empty($res['reason'])) {
												if ($res['reason'] != '已发布') {
													$msg[] = array(
														'desktopName'=>$value['name'],
														'reason'=>"发布资源包失败:".$res['reason']
													);
												}
											}
										}
									}else{
										$msg[] = array(
											'desktopName'=>$value['name'],
											'reason'=>"发布资源包失败，发布路径出错"
										);
									}
								}
							}else{
								$put['id']=$source['id'];
								//添加发布版本
								$res = $this->publishDesktopSource($put,$source,true);
								if (!empty($res['reason'])) {
									if ($res['reason'] != '已发布') {
										$msg[] = array(
											'desktopName'=>$value['name'],
											'reason'=>"发布资源包失败:".$res['reason']
										);
									}

								}
							}
						}
						//发布布局
						if (empty($value['result'])) {

							$layout = D('DesktopLayout','Desktop')->where("`model`='%s'",array($value['name']))->order("`version`  DESC")->find();
							$publish = D('DesktopPublish','Desktop')->where("`model`='%s' and `type`='%s'",array($value['name'],$put['type']))->select();

							if ($publish) {
								foreach ($publish as $k => $v) {
									// $put = $v;
									//先删除发布版本

									$json = getUrl(DESKTOP_LAYOUT_ACCESS_ADDR.'access/publish/del?item=desktop3.0&id='.$v['id']);
									if ($json) {
										$res = json_decode($json,true);
										if ($res['result'] !='ok') {
											if (strstr($res['reason'],'not such id')!=true) {
												$msg[] = array(
													'desktopName'=>$value['name'],
													'reason'=>"发布版本失败：".$res['reason']
												);
											}else{
												$put['id']=$layout['id'];
												//添加发布版本
												$res = $this->addDesktopSlotsFile($put,$layout,true);
												if (!empty($res['reason'])) {

													if ($res['reason'] != '已发布') {
														$msg[] = array(
															'desktopName'=>$value['name'],
															'reason'=>"发布版本失败:".$res['reason']
														);
													}
												}
											}
										}else{
											D('DesktopPublish','Desktop')->deleteDesktopPublish($v['id']);
											unset($v['id']);
											$v['reason'] = '下架';

											D('DesktopPublishHistory','Desktop')->addDesktopPublishHistory($v);

											$put['id']=$layout['id'];
											//添加发布版本
											$res = $this->addDesktopSlotsFile($put,$layout,true);
											if (!empty($res['reason'])) {

												if ($res['reason'] != '已发布') {
													$msg[] = array(
														'desktopName'=>$value['name'],
														'reason'=>"发布版本失败:".$res['reason']
													);
												}
											}
										}
									}else{
										$msg[] = array(
											'desktopName'=>$value['name'],
											'reason'=>"发布版本失败：发布路径出错"
										);
									}
								}
							}else{
								$put['id']=$layout['id'];
								//添加发布版本
								$res = $this->addDesktopSlotsFile($put,$layout,true);
								if (!empty($res['reason'])) {
									if ($res['reason'] != '已发布') {
										$msg[] = array(
											'desktopName'=>$value['name'],
											'reason'=>"发布版本失败:".$res['reason']
										);
									}
								}
							}
						}

					}
				}
				if (!empty($msg)) {
					result('publish',$msg);
				}else{
					result();
				}
			}else{
				$msg[] = array(
					'desktopName'=>'桌面参数错误',
					'reason'=>" "
				);
				result('publish',$msg);
			}
		}*/
		/**
		 * 6. 自动发布桌面
		 * POST /desktop/publishOperationSlotDesktop
		 * POSTDATA:  {"desktopIDList":["1", "2"]}
		 * @return [type] [description]
		 */
		/*public function publishOperationSlotDesktop()
		{
			set_time_limit(0);
			$put = I('put.');
			if (is_array($put['desktopIDList'])&&($put['type']=='group' || $put['type']=='ALL' )) {
				if ($put['type']=='group') {
					if (!empty($put['groupId'])) {
						$group = D('Group')->find($put['groupId']);
						if (!$group) {
							$reason = '组不存在，请选择正确组';
						}
					}
				}
				$desktopIdLists = '';
				foreach ($put['desktopIDList'] as $key => $value) {
					$value = (int)$value;
					$desktopIdLists .=is_int($value)?$value.',':result('param');
				}
				$desktopIdLists = trim($desktopIdLists,',');
				$desktopLists = D('Desktop','Desktop')->getOperationDesktopLists($desktopIdLists);
				if (!empty($reason)) {
					foreach ($desktopLists as $key => $value) {
						$msg[] = array(
							'desktopName'=>$value['name'],
							'reason'=>$reason
						);
					}
					result('publish',$msg);
				}
				if (!empty($desktopLists)) {
					foreach ($desktopLists as $key => $value) {
						$desktopSlotsFiles[$key] = $this->createDesktopSlotsFile($value['id'],$value,true);
						if (!empty($desktopSlotsFiles[$key]['reason'])) {
							$msg[] = array(
								'desktopName'=>$value['name'],
								'reason'=>"生成布局失败：".$desktopSlotsFiles[$key]['reason']
							);
							$desktopLists[$key]['result'] = 'fail';
						}else{
							$this->createDesktopLayout($value['id'],$value,$desktopSlotsFiles[$key],true);
						}
					}
					foreach ($desktopLists as $key => $value) {
						if (empty($value['result'])) {

							$layout = D('DesktopLayout','Desktop')->where("`model`='%s'",array($value['name']))->order("`version`  DESC")->find();
							$publish = D('DesktopPublish','Desktop')->where("`model`='%s' and `type`='%s'",array($value['name'],$put['type']))->select();

							if ($publish) {
								foreach ($publish as $k => $v) {
									// $put = $v;
									//先删除发布版本


									D('DesktopPublishHistory','Desktop')->addDesktopPublishHistory($v);
									$json = getUrl(DESKTOP_LAYOUT_ACCESS_ADDR.'access/publish/del?item=desktop3.0&id='.$v['id']);
									if ($json) {
										$res = json_decode($json,true);
										if ($res['result'] !='ok') {
											if (strstr($res['reason'],'not such id')!=true) {
												$msg[] = array(
													'desktopName'=>$value['name'],
													'reason'=>"发布版本失败：".$res['reason']
												);
											}else{
												$put['id']=$layout['id'];
												//添加发布版本
												$res = $this->addDesktopSlotsFile($put,$layout,true);
												if (!empty($res['reason'])) {
													$msg[] = array(
														'desktopName'=>$value['name'],
														'reason'=>"发布版本失败:".$res['reason']
													);
												}
											}
										}else{
											D('DesktopPublish','Desktop')->deleteDesktopPublish($v['id']);
											unset($v['id']);
											$v['reason'] = '下架';
											$put['id']=$layout['id'];
											//添加发布版本
											$res = $this->addDesktopSlotsFile($put,$layout,true);
											if (!empty($res['reason'])) {
												$msg[] = array(
													'desktopName'=>$value['name'],
													'reason'=>"发布版本失败:".$res['reason']
												);
											}
										}
									}else{
										$msg[] = array(
											'desktopName'=>$value['name'],
											'reason'=>"发布版本失败，发布路径出错"
										);
									}
								}
							}else{
								$put['id']=$layout['id'];
								//添加发布版本
								$res = $this->addDesktopSlotsFile($put,$layout,true);
								if (!empty($res['reason'])) {
									$msg[] = array(
										'desktopName'=>$value['name'],
										'reason'=>"发布版本失败:".$res['reason']
									);
								}

							}
						}

					}
				}
				if (!empty($msg)) {
					result('publish',$msg);
				}else{
					result();
				}
			}else{
				$msg[] = array(
					'desktopName'=>'桌面参数错误',
					'reason'=>" "
				);
				result('publish',$msg);
			}
		}*/
		/**
		 *  生成桌面布局列表
		 *  get /desktop/desktopLayoutLists?desktopName=test1
		 * @return {"extra":[{"id":"1","model":"test1","version":"1444375221","path":"http:\/\/style-lee.oss-cn-shenzhen.aliyuncs.com\/json\/test1layout_1444375221.json","time":"2015-10-10 09:40:53"}],"result":"ok"}
		 */
		/*public function desktopLayoutLists(){
			$get = I('get.');
			if ($get['desktopName']) {
				$res['extra'] = D('DesktopLayout','Desktop')->field("`id`,model,version,concat('".DOWNLOAD_PREFIX_ADDR."',`path`) as path,FROM_UNIXTIME(time,'%Y-%m-%d %H:%i:%s') as time")->where("`model`='%s'",array($get['desktopName']))->select();
			}
			if (empty($res['extra'])) {
				$res['extra'] =array();
			}
			result(true,$res);
		}*/

		/**
		 * 发布桌面版本（准备废弃）
		 * post  /desktop/addDesktopSlotsFile
		 * post_data {"id":"布局桌面版本id","type":"group/AB/ALL","groupId":"组ID","AB":"1000"} PS：当type=AB时，AB存在，当type=group时，groupId存在
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */

		/*public function addDesktopSlotsFile($put = null,$desktopLayout=null,$error = false)
		{
			if ($put ===null) {
				$put = I('put.');
			}
			if (!empty($put['id'])) {
				if ($desktopLayout ===null) {
					$desktopLayout = D('DesktopLayout','Desktop')->field("`id`,model,version,path")->where("`id`=%d",array($put['id']))->find();
					if (!$desktopLayout) {
						if ($error) {
							return $result = array('reason'=>'该桌面布局不存在');
							// return false;
						}else{
							result('该桌面布局不存在');
						}

					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'参数有误');
					// return false;
				}else{
					result('param');
				}
			}
			$options = array(
				'model'=>$desktopLayout['model'],
				'path'=>$desktopLayout['path'],
				'type'=>$put['type'],
				'version'=>$desktopLayout['version'],
				'time'=>time()
			);
			if ($put['type']=='group') {
				if (empty($put['groupId'])) {
					if ($error) {
						return $result = array('reason'=>'参数有误');
						// result('param');
						// return false;
					}else{
						result('param');
					}
				}
				$options['groupId'] = $put['groupId'];
			}elseif ($put['type']=='AB') {
				if (empty($put['AB'])) {
					if ($error) {
						return $result = array('reason'=>'参数有误');
						// result('param');
						// return false;
					}else{
						result('param');
					}
				}
				$options['AB'] = $put['AB'];
			}
			$desktopPublishId = D('DesktopPublish','Desktop')->addDesktopPublish($options,$error);
			{"id":"1", "item":"desktop3.0", "target":{"model":"ALL", "snList":["222","333"], "type":"group/AB/ALL", "AB":"100"}, "content":{"path":"http://xxxx"}}
			if (!empty($desktopPublishId['reason'])) {
				return $desktopPublishId;
				// return false;
			}
			$data = array(
				'id'=>$desktopPublishId,
				'item'=>'desktop3.0',
				'target'=>array(
					'model'=>$desktopLayout['model'],
					'type'=>$put['type']
				),
				'content'=>array(
					'path'=>DOWNLOAD_PREFIX_ADDR.$desktopLayout['path'],
					'version'=>$desktopLayout['version'],
				)
			);
			if ($put['type']=='group') {
				$groupMembers = D('GroupMembers')->where("`group_id`=%d",array($options['groupId']))->select();
				if ($groupMembers) {
					foreach ($groupMembers as $key => $value) {
						$data['target']['snList'][] = $value['sn'];
					}
				}else{
					$data['target']['snList'][] = array();
				}
			}elseif ($put['type']=='AB') {
				$data['target']['AB'] = $put['AB'];
			}
			$data = json_encode($data,JSON_UNESCAPED_UNICODE);
			$json = postUrl(DESKTOP_LAYOUT_ACCESS_ADDR.'access/publish/add',$data);
			if ($json) {
				$res = json_decode($json,true);
				if ($res['result'] !='ok') {
					D('DesktopPublish','Desktop')->deleteDesktopPublish($desktopPublishId);
					if ($error) {
						return $res;
						// return false;
					}else{
						result($res['reason']);
					}

				}else{
					if ($error) {
						return true;
					}else{
						result();
					}
				}
			}else{
				D('DesktopPublish','Desktop')->deleteDesktopPublish($desktopPublishId);
				if ($error) {
					return $result = array('reason'=>'请求发布失败');
					// return false;
				}else{
					result('该桌面请求发布失败');
				}
			}

		}
*/
		/**
		 * 桌面管理_桌面发布版本列表
		 * get /desktop/desktopSlotsFileLists
		 * get /desktop/desktopSlotsFileLists?id=1
		 *
		 */
		public function desktopSlotsFileLists()
		{
			$get = I('get.');
			//去除桌面旧接口发布 start
			$res = D('DesktopVersionPublish','Desktop')->desktopSlotsFileLists($get);
			result(true,$res);
			//去除桌面旧接口发布 end
			//桌面旧接口发布 start
			/*if (!empty($get['id'])) {
				$res = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/list?item=desktop3.0&id='.$get['id']);
				if ($res){
					$res = json_decode($res,true);
					if ($res['result'] == 'ok') {
						if (!empty($res['extra'])) {
							if (empty($res['extra']['snList'])) {
								$res['extra']['snList'] = array();
							}
						}
						$res = json_encode($res,JSON_UNESCAPED_UNICODE);
					}else{
						result($res['reason']);
					}

				}else{
					result('发布地址出错');
				}
			}else{
				$res = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/list?item=desktop3.0');

				if ($res) {
					$res = json_decode($res,true);
					if ($res['result'] == 'ok') {
						if (empty($res['extra'])) {
							$res['extra']= array();
						}
						$res = json_encode($res,JSON_UNESCAPED_UNICODE);
					}else{
						result($res['reason']);
					}

				}else{
					result('发布地址出错');
				}
			}
			if ($res) {
				echo $res;
			}else{
				result('发布地址出错');
			}*/
			//桌面旧接口发布 end
		}

		/**
		 * 桌面管理_获取桌面发布名字
		 * get /desktop/getDesktopPublishNameLists
		 * {
		 * 	"1","2","3","4"
		 * }
		 *
		 */

		public function getDesktopPublishNameLists()
		{
			$put = I('put.');
			//去除桌面旧接口发布 start
			$res = D('DesktopVersionPublish','Desktop')->getDesktopPublishNameArrForIdArr($put);
			result(true,$res);
			//去除桌面旧接口发布 end
			//桌面旧接口发布 start
			/*if (!empty($get['id'])) {
				$res = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/list?item=desktop3.0&id='.$get['id']);
				if ($res){
					$res = json_decode($res,true);
					if ($res['result'] == 'ok') {
						if (!empty($res['extra'])) {
							if (empty($res['extra']['snList'])) {
								$res['extra']['snList'] = array();
							}
						}
						$res = json_encode($res,JSON_UNESCAPED_UNICODE);
					}else{
						result($res['reason']);
					}

				}else{
					result('发布地址出错');
				}
			}else{
				$res = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/list?item=desktop3.0');

				if ($res) {
					$res = json_decode($res,true);
					if ($res['result'] == 'ok') {
						if (empty($res['extra'])) {
							$res['extra']= array();
						}
						$res = json_encode($res,JSON_UNESCAPED_UNICODE);
					}else{
						result($res['reason']);
					}

				}else{
					result('发布地址出错');
				}
			}
			if ($res) {
				echo $res;
			}else{
				result('发布地址出错');
			}*/
			//桌面旧接口发布 end
		}
		/**
		 * 桌面管理_删除桌面发布版本
		 * post /desktop/deleteDesktopSlotsFile
		 * ["1","2"]
		 * @return {"result":"ok/fail","failList":["desktopName":"","reason":""]}
		 */
		public function deleteDesktopSlotsFile()
		{
			$put = I('put.');
			if (!empty($put)&&is_array($put)) {
				$sqlIArr = implode(',',$put);
				$sqlIArr = trim($sqlIArr,',');
				$desktopLists = D('DesktopVersionPublish','Desktop')->getValAllForIdArr($sqlIArr);
				$desktopNameArr = array();
				foreach ($desktopLists as $value) {
					$desktopNameArr[$value['id']] = $value;
				}
				foreach ($put as $value) {
					//去除桌面旧接口发布 start
					if (!empty($desktopNameArr[$value])) {
						D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($value);
						unset($desktopNameArr[$value]['id']);
						$desktopNameArr[$value]['reason'] = '下架';
						D('DesktopVersionPublishHistory','Desktop')->addDesktopVersionPublishHistory($desktopNameArr[$value]);
					}
					//去除桌面旧接口发布 end
					//桌面旧接口发布 start

					/*$json = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/del?item=desktop3.0&id='.$value);
					if ($json) {
						$json = json_decode($json,true);
						if ($json['result'] != 'ok') {
							if (strstr($res['reason'],'not such id')!=true) {
								$msg[] = array(
									'desktopName'=>$desktopNameArr[$value]['model'],
									'reason'=>$json['reason']
								);
							}
						}

						if (!empty($desktopNameArr[$value])) {
							D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($value);
							unset($desktopNameArr[$value]['id']);
							$desktopNameArr[$value]['reason'] = '下架';
							D('DesktopVersionPublishHistory','Desktop')->addDesktopVersionPublishHistory($desktopNameArr[$value]);
						}

					}else{
						$msg[] = array(
							'desktopName'=>isset($desktopNameArr[$value]['model'])?$desktopNameArr[$value]['model']:'',
							'reason'=>'发布地址出错'
						);
					}*/
					//桌面旧接口发布 end
				}
				if (!empty($msg)) {
					result('publish',$msg);
				}else{
					result();
				}
			}else{
				$msg[] = array(
					'desktopName'=>'参数错误',
					'reason'=>" "
				);
				result('publish',$msg);
			}
		}
		/**
		 * 桌面管理_创建桌面附件文件createSlotAttachmentConfig
		 * @return [type] [description]
		 */

		public function createSlotAttachmentConfig($desktopId=null,$desktop = null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopAttachment','Desktop')->createSlotAttachmentConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}
			}else{
				if ($error) {
					return false;
				}else{
					result('param');
				}
			}

		}
		/**
		 * 桌面管理_创建桌面可下载快捷入口坑位文件
		 * @return [type] [description]
		 */

		public function createDesktopDownloadQuickEntryLists($desktopId=null,$desktop = null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopDownloadQuickEntry','Desktop')->createDesktopDownloadQuickEntryLists($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}
			}else{
				if ($error) {
					return false;
				}else{
					result('param');
				}
			}

		}
		/**
		 * 桌面管理_创建桌面LOGO配置文件createLogoConfig
		 * @return [type] [description]
		 */
		public function createLogoConfig($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopLogo','Desktop')->createLogoConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面logo参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建导航配置文件createNavigatorConfig
		 * @return [type] [description]
		 */
		public function createNavigatorConfig($desktopId=null,$desktop =null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopNav','Desktop')->createNavigatorConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数存在');
					// return false;
				}else{
					result('param');
				}
			}

		}

		/**
		 * 桌面管理_创建快捷入口配置文件createQuickEntryConfig
		 * @return [type] [description]
		 */
		public function createQuickEntryConfig($desktopId=null ,$desktop =null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}

			if (!empty($desktopId)) {
				$desktopJson = D('DesktopQuickEntry','Desktop')->createQuickEntryConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建快捷入口组配置文件 createQuickEntryGroupConfig
		 * @return [type] [description]
		 */
		public function createQuickEntryGroupConfig($desktopId=null ,$desktop =null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}

			if (!empty($desktopId)) {
				$desktopJson = D('DesktopQuickEntryGroup','Desktop')->createQuickEntryGroupConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建两态快捷入口配置文件createQuickEntryTwoStateConfig
		 * @return [type] [description]
		 */
		public function createQuickEntryTwoStateConfig($desktopId=null ,$desktop =null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopQuickEntryTwoState','Desktop')->createQuickEntryTwoStateConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}

		/**
		 * 桌面管理_创建三态快捷入口配置文件createQuickEntryThreeStateConfig
		 * @return [type] [description]
		 */
		public function createQuickEntryThreeStateConfig($desktopId=null ,$desktop =null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopQuickEntryThreeState','Desktop')->createQuickEntryThreeStateConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面时间配置文件
		 * @return [type] [description]
		 */
		public function createTimeConfig($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopTimebar','Desktop')->createTimeConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}

			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面消息（走马灯）文件
		 * @return [type] [description]
		 */
		public function createMessageConfig($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopMessage','Desktop')->createMessageConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}

			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面基础设置文件
		 * @return [type] [description]
		 */
		public function createAppConfig($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopAppConfig','Desktop')->createAppConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}


			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面底部快捷栏配置
		 * @return [type] [description]
		 */
		public function createQuickList($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {

				$desktopJson = D('DesktopQuickList','Desktop')->createQuickList($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}

			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面快捷键
		 * @return [type] [description]
		 */
		public function createQuickKey($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {

				$desktopJson = array();
				if ($desktop ===null) {
					$desktop = D('Desktop','Desktop')->getValForId($desktopId);
				}

				if ($desktop) {
 					$desktopJson = D('DesktopShortCuts','Desktop')->createDesktopShortCuts($desktopId,$desktop,$error);

					if (!empty($desktopJson['reason'])) {
						if ($error) {
							return $desktopJson;
							// return false;
						}else{
							result($desktopJson['reason']);
						}
					}

					if (!empty($desktopJson)) {

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
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面天气配置文件
		 * @return [type] [description]
		 */
		public function createWeatherConfig($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopWeather','Desktop')->createWeatherConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}


			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面天气配置文件
		 * @return [type] [description]
		 */
		public function createTimeWeatherConfig($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopTimeWeather','Desktop')->createTimeWeatherConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					/*echo json_encode($desktopJson);
					die;*/
					return $desktopJson;
				}


			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面SN
		 * @return [type] [description]
		 */
		public function createSnConfig($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopSn','Desktop')->createSnConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}


			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}
		/**
		 * 桌面管理_创建桌面风格
		 * @return [type] [description]
		 */
		/*public function createStyleConfig($desktopId=null,$desktop=null,$error = false)
		{
			if ($desktopId===null) {
				$desktopId = I('get.id');
			}
			if (!empty($desktopId)) {
				$desktopJson = D('DesktopStyle','Desktop')->createStyleConfig($desktopId,$desktop,$error);
				if (!empty($desktopJson['reason'])) {
					if ($error) {
						return $result = array('reason'=>$desktopJson['reason']);
						// return false;
					}else{
						result($desktopJson['reason']);
					}
				}else{
					return $desktopJson;
				}


			}else{
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
					// return false;
				}else{
					result('param');
				}
			}
		}*/
		/**
		 * 桌面管理_自动发布桌面资源包
		 * POST /desktop/publishOperationSlotSource
		 * POSTDATA:  {"desktopIDList":["1", "2"]}
		 * @return [type] [description]
		 */
		/*public function publishOperationSlotSource()
		{
			set_time_limit(0);
			$put = I('put.');
			if (is_array($put['desktopIDList'])&&($put['type']=='group' || $put['type']=='ALL' )) {
				if ($put['type']=='group') {
					if (!empty($put['groupId'])) {
						$group = D('Group')->find($put['groupId']);
						if (!$group) {
							$reason = '组不存在，请选择正确组';
						}
					}
				}
				$desktopIdLists = '';
				foreach ($put['desktopIDList'] as $key => $value) {
					$value = (int)$value;
					$desktopIdLists .=is_int($value)?$value.',':result('param');
				}
				$desktopIdLists = trim($desktopIdLists,',');
				$desktopLists = D('Desktop','Desktop')->getOperationDesktopLists($desktopIdLists);
				if (!empty($reason)) {
					foreach ($desktopLists as $key => $value) {
						$msg[] = array(
							'desktopName'=>$value['name'],
							'reason'=>$reason
						);
					}
					result('publish',$msg);
				}
				//桌面列表
				if (!empty($desktopLists)) {
					foreach ($desktopLists as $key => $value) {

						$desktopSlotsFiles[$key] = $this->createDesktopSource($value['id'],$value,true);

						if (!empty($desktopSourceFiles[$key]['reason'])) {

							if ($desktopSourceFiles[$key]['reason'] != '该桌面没有修改') {
								$msg[] = array(
									'desktopName'=>$value['name'],
									'reason'=>"生成资源包失败：".$desktopSourceFiles[$key]['reason']
								);
								$desktopLists[$key]['sourceResult'] = 'fail';
							}else{
								unset($desktopSourceFiles[$key]['reason']);
							}
						}
					}
					foreach ($desktopLists as $key => $value) {
						if (empty($value['result'])) {
							//生成资源包
							$source = D('DesktopSource','Desktop')->where("`model`='%s'",array($value['name']))->order("`version`  DESC")->find();
							$publish = D('DesktopSourcePublish','Desktop')->where("`model`='%s' and `type`='%s'",array($value['name'],$put['type']))->select();

							if ($publish) {
								foreach ($publish as $k => $v) {
									// $put = $v;
									//先删除发布版本

									$json = getUrl(DESKTOP_SOURCE_ACCESS_ADDR.'access/publish/del?item=source3.0&id='.$v['id']);
									if ($json) {
										$res = json_decode($json,true);
										if ($res['result'] !='ok') {
											if (strstr($res['reason'],'not such id')!=true) {
												$msg[] = array(
													'desktopName'=>$value['name'],
													'reason'=>"发布资源包失败：".$res['reason']
												);
											}else{
												$put['id']=$source['id'];
												//添加发布版本
												$res = $this->publishDesktopSource($put,$source,true);
												if (!empty($res['reason'])) {
													$msg[] = array(
														'desktopName'=>$value['name'],
														'reason'=>"发布资源包失败:".$res['reason']
													);
												}
											}

										}else{
											D('DesktopSourcePublish','Desktop')->deleteDesktopSourcePublish($v['id']);
											unset($v['id']);
											$v['reason'] = '下架';
											D('DesktopSourcePublishHistory','Desktop')->addDesktopSourcePublishHistory($v);
											$put['id']=$source['id'];
											//添加发布版本
											$res = $this->publishDesktopSource($put,$source,true);
											if (!empty($res['reason'])) {
												$msg[] = array(
													'desktopName'=>$value['name'],
													'reason'=>"发布资源包失败:".$res['reason']
												);
											}
										}

									}else{
										$msg[] = array(
											'desktopName'=>$value['name'],
											'reason'=>"发布资源包失败，发布路径出错"
										);
									}
								}
							}else{
								$put['id']=$source['id'];
								//添加发布版本
								$res = $this->publishDesktopSource($put,$source,true);
								if (!empty($res['reason'])) {
									$msg[] = array(
										'desktopName'=>$value['name'],
										'reason'=>"发布资源包失败:".$res['reason']
									);
								}
							}
						}

					}
				}
				if (!empty($msg)) {
					result('publish',$msg);
				}else{
					result();
				}
			}else{
				$msg[] = array(
					'desktopName'=>'桌面参数错误',
					'reason'=>" "
				);
				result('publish',$msg);
			}
		}*/
		/**
		 * 桌面管理_生成桌面资源包
		 * get /desktop/createDesktopSource?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		/*public function createDesktopSource($id=null,$desktop=null,$error = false)
		{


			set_time_limit(0);
			if ($id===null) {
				$id = I('get.id');
			}
			if (empty($id)) {
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
				}else{
					result('param');
				}
			}
			if ($desktop===null) {
				$desktop = D('Desktop','Desktop')->find($id);
			}
			if ($desktop) {
				$desktopSource = D('DesktopSource','Desktop')->where("`model`='%s' and `version`='%s'",array($desktop['name'],$desktop['update_time']))->find();
				if ($desktopSource) {

					if ($error) {
						return $result = array('reason'=>'该桌面没有修改');
						// return false;
					}else{
						result('该桌面没有修改');
					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面不存在');
					// return false;
				}else{
					result('桌面不存在');
				}
			}
			$uniqueIndexArr = array();
			if ($error) {
				//创建桌面基础设置文件
				$app = $this->createAppConfig($id,$desktop,true);
				if (!$app) {
					return $result = array('reason'=>'创建桌面基础设置文件出错');
					// return false;
				}
				//判断桌面基础设置--导航
				if ($app['isCreateNavigation'] == 'true') {
					//创建导航配置文件
					$navigator = $this->createNavigatorConfig($id,$desktop,true);
					if ($navigator === false) {
						if ($error) {
							return $result = array('reason'=>'导航数据出错');
							// return false;
						}else{
							result('导航数据出错');
						}
					}

				}else{
					$navigator = false;
				}
				//判断桌面基础设置--时间
				if ($app['isCreateTimeWidget'] == 'true' ) {
					//创建桌面时间配置文件
					$time = $this->createTimeConfig($id,$desktop,true);
					if ($time ===false) {
						if ($error) {
							return $result = array('reason'=>'时间数据出错');
							// return false;
						}else{
							result('时间数据出错');
						}
					}

				}else{
					$time =false;
				}
				//判断桌面基础设置--天气
				if ($app['isCreateWeatherWidget'] == 'true'  ) {
					//创建桌面天气配置文件
					$weather = $this->createWeatherConfig($id,$desktop,true);
					if ($weather ===false) {
						if ($error) {
							return $result = array('reason'=>'天气数据出错');

							// return false;
						}else{
							result('天气数据出错');
						}
					}
				}else{
					$weather =false;
				}
				//判断桌面基础设置--附件
				if ($app['isCreateSlotAttachment'] == 'true') {
					//创建桌面附件文件
					$slotAttachment = $this->createSlotAttachmentConfig($id,$desktop,true);
					if ($slotAttachment ===false) {
						if ($error) {
							return $result = array('reason'=>'附件数据出错');
							 // $result;
							// return false;
						}else{
							result('附件数据出错');
						}
					}else{
						foreach ($slotAttachment['items'] as $key => $value) {
							$uniqueIndexArr[] = $value['id'];
						}
					}

				}else{
					$slotAttachment =false;
				}
				//判断桌面基础设置--Logo
				if ($app['isCreateLogoWidget'] == 'true') {
					//创建桌面LOGO配置文件
					$logo = $this->createLogoConfig($id,$desktop,true);
					if ($logo ===false) {
						if ($error) {
							return $result = array('reason'=>'Logo数据出错');
							// return false;
						}else{
							result('Logo数据出错');
						}
					}

				}else{
					$logo =false;
				}
				//判断桌面基础设置--快捷入口
				if ($app['isCreateQuickEntry'] == 'true' ) {
					//创建快捷入口配置文件
					$quickEntry = $this->createQuickEntryConfig($id,$desktop,true);
					if ($quickEntry ===false) {
						if ($error) {
							return $result = array('reason'=>'快捷入口数据出错');
							// return false;
						}else{
							result('快捷入口数据出错');
						}
					}else{
						foreach ($quickEntry['items'] as $key => $value) {
							$uniqueIndexArr[] = $value['id'];
						}
					}

				}else{
					$quickEntry =false;
				}
				//判断桌面基础设置--SN控件
				if ($app['isCreateSnWidget'] == 'true'  ) {
					//创建桌面SN文件
					$sn = $this->createSnConfig($id,$desktop,true);
					if ($sn ===false) {
						if ($error) {
							return $result = array('reason'=>'SN控件数据出错');
							// return false;
						}else{
							result('SN控件数据出错');
						}
					}

				}else{
					$sn =false;
				}
				//创建桌面底部快捷栏配置
				$quickList = $this->createQuickList($id,$desktop,true);
				//创建桌面快捷键
				$quickKey = $this->createQuickKey($id,$desktop,true);
				//创建桌面屏文件
				$slots = $this->createDesktopSlotsFile($id,$desktop,true);
				//创建三态快捷入口配置文件
				$quickEntryThreeState = $this->createQuickEntryThreeStateConfig($id,$desktop,true);
				//创建两态快捷入口配置文件
				$quickEntryTwoState = $this->createQuickEntryTwoStateConfig($id,$desktop,true);
			}else{
				//创建桌面基础设置文件
				$app = $this->createAppConfig($id);
				if (!$app) {
					return $result = array('reason'=>'创建桌面基础设置文件出错');
					// return false;
				}
				//判断桌面基础设置--导航
				if ($app['isCreateNavigation'] == 'true') {
					//创建导航配置文件
					$navigator = $this->createNavigatorConfig($id);
					if ($navigator === false) {
						if ($error) {
							return $result = array('reason'=>'导航数据出错');
							// return false;
						}else{
							result('导航数据出错');
						}
					}

				}else{
					$navigator = false;
				}
				//判断桌面基础设置--时间
				if ($app['isCreateTimeWidget'] == 'true' ) {
					//创建桌面时间配置文件
					$time = $this->createTimeConfig($id);
					if ($time ===false) {
						if ($error) {
							return $result = array('reason'=>'时间数据出错');
							// return false;
						}else{
							result('时间数据出错');
						}
					}

				}else{
					$time =false;
				}
				//判断桌面基础设置--天气
				if ($app['isCreateWeatherWidget'] == 'true'  ) {
					//创建桌面天气配置文件
					$weather = $this->createWeatherConfig($id);
					if ($weather ===false) {
						if ($error) {
							return $result = array('reason'=>'天气数据出错');

							// return false;
						}else{
							result('天气数据出错');
						}
					}
				}else{
					$weather =false;
				}
				//判断桌面基础设置--附件
				if ($app['isCreateSlotAttachment'] == 'true') {
					//创建桌面附件文件
					$slotAttachment = $this->createSlotAttachmentConfig($id);
					if ($slotAttachment ===false) {
						if ($error) {
							return $result = array('reason'=>'附件数据出错');
							 // $result;
							// return false;
						}else{
							result('附件数据出错');
						}
					}else{
						foreach ($slotAttachment['items'] as $key => $value) {
							$uniqueIndexArr[] = $value['id'];
						}
					}

				}else{
					$slotAttachment =false;
				}
				//判断桌面基础设置--Logo
				if ($app['isCreateLogoWidget'] == 'true') {
					//创建桌面LOGO配置文件
					$logo = $this->createLogoConfig($id);
					if ($logo ===false) {
						if ($error) {
							return $result = array('reason'=>'Logo数据出错');
							// return false;
						}else{
							result('Logo数据出错');
						}
					}

				}else{
					$logo =false;
				}
				//判断桌面基础设置--快捷入口
				if ($app['isCreateQuickEntry'] == 'true' ) {
					//创建快捷入口配置文件
					$quickEntry = $this->createQuickEntryConfig($id);
					if ($quickEntry ===false) {
						if ($error) {
							return $result = array('reason'=>'快捷入口数据出错');
							// return false;
						}else{
							result('快捷入口数据出错');
						}
					}else{
						foreach ($quickEntry['items'] as $key => $value) {
							$uniqueIndexArr[] = $value['id'];
						}
					}

				}else{
					$quickEntry =false;
				}
				//判断桌面基础设置--SN控件
				if ($app['isCreateSnWidget'] == 'true'  ) {
					//创建桌面SN文件
					$sn = $this->createSnConfig($id);
					if ($sn ===false) {
						if ($error) {
							return $result = array('reason'=>'SN控件数据出错');
							// return false;
						}else{
							result('SN控件数据出错');
						}
					}

				}else{
					$sn =false;
				}
				//创建桌面底部快捷栏配置
				$quickList = $this->createQuickList($id);
				//创建桌面快捷键
				$quickKey = $this->createQuickKey($id);
				//创建桌面屏文件
				$slots = $this->createDesktopSlotsFile($id);

				//创建三态快捷入口配置文件
				$quickEntryThreeState = $this->createQuickEntryThreeStateConfig($id);
				//创建两态快捷入口配置文件
				$quickEntryTwoState = $this->createQuickEntryTwoStateConfig($id);
			}
			if (!empty($quickEntryThreeState)) {
				foreach ($quickEntryThreeState['items'] as $key => $value) {
					$uniqueIndexArr[] = $value['id'];
				}
			}
			if (!empty($quickEntryTwoState)) {
				foreach ($quickEntryTwoState['items'] as $key => $value) {
					$uniqueIndexArr[] = $value['id'];
				}
			}
			if (!empty($quickList)&&$app['isCreateQuickList'] == 'true') {
				foreach ($quickList['apps'] as $key => $value) {
					$uniqueIndexArr[] = $value['id'];
				}
			}
			//检查ID是否重复

			$indexCount = count($uniqueIndexArr);
			$uniqueIndexArr = array_unique($uniqueIndexArr);
			if ($indexCount != count($uniqueIndexArr) ) {
				if ($error) {
					return $result = array('reason'=>'快捷入口或三态快捷入口或两态快捷入口或底部快捷栏或附件栏ID重复');
					// return false;
				}else{
					result('快捷入口或三态快捷入口或两态快捷入口或底部快捷栏或附件栏ID重复');
				}
			}
			$uniqueIndexArr = array_count_values($uniqueIndexArr);
			$indexStr = '';
			foreach ($uniqueIndexArr as $key => $value) {
				if ($value > 1) {
					$indexStr .= 'ID'.$key .'出现'.$value .'次，';
				}
			}

			if (!empty($indexStr)) {
				if ($error) {
					return $result = array('reason'=>'快捷入口或三态快捷入口或两态快捷入口或底部快捷栏或附件栏ID重复:'.$indexStr);
					// return false;
				}else{
					result('快捷入口或三态快捷入口或两态快捷入口或底部快捷栏或附件栏ID重复:'.$indexStr);
				}
			}
			$path = './'.$desktop['name'].'/';
			$zipFile = './'.$desktop['name'].'_'.$desktop['update_time'].'.zip';
			if (file_exists($path)) {
				removeDir($path);
			}
			if (file_exists($zipFile)) {
				removeDir($zipFile);
			}
			if (!empty($quickList['reason'])) {
				return $quickList;
			}elseif (!empty($quickKey['reason'])) {
				return $quickKey;
			}elseif (!empty($weather['reason'])) {
				return $weather;
			}elseif (!empty($time['reason'])) {
				return $time;
			}elseif (!empty($quickEntry['reason'])) {
				return $quickEntry;
			}elseif (!empty($quickEntryThreeState['reason'])) {
				return $quickEntryThreeState;
			}elseif (!empty($navigator['reason'])) {
				return $navigator;
			}elseif (!empty($logo['reason'])) {
				return $logo;
			}elseif (!empty($slotAttachment['reason'])) {
				return $slotAttachment;
			}elseif (!empty($quickEntryTwoState['reason'])) {
				return $quickEntryTwoState;
			}
			if (!empty($slots['reason'])) {
				return $slots;
			}elseif (empty($slots)) {
				if ($error) {
					return $result = array('reason'=>'存在屏，但没有屏信息');
					// return false;
				}else{
					result('存在屏，但没有屏信息');
				}
				if (empty($slots['screens'])) {
					if ($navigator) {
						if (count($slots['screens']) != count($navigator['items'])) {
							if ($error) {
								return $result = array('reason'=>'桌面屏与导航不一致');
								// return false;
							}else{
								result('桌面屏与导航不一致');
							}
						}
					}else{
						if (count($slots['screens']) != 1) {
							if ($error) {
								return $result = array('reason'=>'桌面屏大于1：需要导航');
								// return false;
							}else{
								result('桌面屏大于1：需要导航');
							}
						}
					}
				}else{
					$navigator = false;
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'存在屏，但没有屏信息');
					// return false;
				}else{
					result('存在屏，但没有屏信息');
				}
			}

			//创建桌面资源包
			mkdir($path.'source',0777,true);
			$msg = '';
			//复制桌面壁纸
			if (!empty($desktop['image'])) {
				$end = explode('.',$desktop['image']);
				$end = end($end);
				$name = explode('/',$desktop['image']);
				$name = end($name);
				if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
					$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/'.'default_wallpaper.'.$end);
				}else{
					$bool = @copy($desktop['image'],$path.'source/'.'default_wallpaper.'.$end);
					@copy($path.'source/'.'default_wallpaper.'.$end,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
				}
				if (!$bool) {
					if ($error) {
						return $result = array('reason'=>'下载桌面壁纸'.$name. '失败！');
						// return false;
					}else{
						$msg .= '下载桌面壁纸'.$name. '失败！';
						result($msg);
					}

				}
			}
			//创建桌面坑位文件夹
			mkdir($path.'source/slot',0777,true);
			//创建桌面坑位图片文件夹
			mkdir($path.'source/slot/drawable',0777,true);
			if ($slots) {
				foreach ($slots['screens'] as $key => $value) {
					foreach ($value['slots'] as $k => $v) {
						if (!empty($v['picUrl'])) {
							$end = explode('.',$v['picUrl']);
							$end = end($end);
							$name = explode('/',$v['picUrl']);
							$name = end($name);
							if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
								$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/slot/drawable/'.$v['id'].'.'.$end);

							}else{
								$bool = @copy($v['picUrl'],$path.'source/slot/drawable/'.$v['id'].'.'.$end);
								@copy($path.'source/slot/drawable/'.$v['id'].'.'.$end,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
							}

							if (!$bool) {
								if ($error) {
									return $result = array('reason'=>'下载坑位图片'.$v['id'].'.'.$end. '失败！');

									// return false;
								}else{
									$msg .= '下载坑位图片'.$v['id'].'.'.$end. '失败！';
									result($msg);
								}

							}
						}
					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'该桌面没有坑位数据');
					// return false;
				}else{
					result('该桌面没有坑位数据');
				}
			}

			//创建桌面坑位json文件夹
			mkdir($path.'source/slot/json',0777,true);
			//生成坑位json文件
			$myfile = @fopen($path.'source/slot/json/slots.json', "w");
			if (!$myfile) {
				return $result = array('reason'=> '无法打开 slots.json文件');
			}
			$slotsFileStr = json_encode($slots,JSON_UNESCAPED_UNICODE);
			fwrite($myfile, $slotsFileStr);
			fclose($myfile);
			//创建桌面底部快捷栏文件夹
			mkdir($path.'source/quicklist/drawable',0777,true);
			//创建桌面底部快捷栏文件夹
			mkdir($path.'source/quicklist/json',0777,true);
			//判断桌面基础设置--底部快捷栏
			if ($app['isCreateQuickList'] == 'true' && $quickList === false) {
				$slotsFileStr = '{"apps":[]}';
				//创建桌面底部快捷栏配置
				$myfile = @fopen($path.'source/quicklist/json/quicklist_config.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 quicklist_config.json文件');
				}
				// $slotsFileStr = json_encode($quickList,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}elseif (!empty($quickList)&&$app['isCreateQuickList'] == 'true') {
				foreach ($quickList['apps'] as $key => $value) {
					$name = explode('/',$value['appIcon']);
					$name = end($name);
					if (!file_exists($path.'source/quicklist/drawable/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/quicklist/drawable/'.$name);
						}else{
							$bool = @copy($value['normalDrawable'],$path.'source/quicklist/drawable/'.$name);
							@copy($path.'source/quicklist/drawable/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
					}

					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载底部快捷栏Icon'.$name. '失败！');
							// return false;
						}else{
							$msg .= '下载底部快捷栏Icon'.$name. '失败！';
							result($msg);
						}
					}
					$quickList['apps'][$key]['appIcon'] = $name;

				}
				//创建桌面底部快捷栏配置
				$myfile = @fopen($path.'source/quicklist/json/quicklist_config.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 quicklist_config.json文件');
				}
				$slotsFileStr = json_encode($quickList,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);

			}
			//创建桌面控件文件夹
			mkdir($path.'source/widget',0777,true);
			//创建桌面控件图片文件夹
			mkdir($path.'source/widget/drawable',0777,true);
			//创建桌面控件logo图片文件夹
			mkdir($path.'source/widget/drawable/logo',0777,true);
			if (!empty($logo)) {
				foreach ($logo['logoArray'] as $key => $value) {
					$end = explode('.',$value);
					$end = end($end);
					$name = explode('/',$value);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/logo/'.$key.'.'.$end);
					}else{
						$bool = @copy($value,$path.'source/widget/drawable/logo/'.$key.'.'.$end);
						@copy($path.'source/widget/drawable/logo/'.$key.'.'.$end,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载Logo图片'.$key.'.'.$end . '失败！');
							// return false;
						}else{
							$msg .= '下载Logo图片'.$key.'.'.$end . '失败！';
							result($msg);
						}

					}
					$logo['logoArray'][$key]=$key.'.'.$end;
				}
			}
			//创建桌面控件导航图片文件夹
			mkdir($path.'source/widget/drawable/navigator',0777,true);
			if (!empty($navigator)) {
				foreach ($navigator['items'] as $key => $value) {
					$normalEnd = explode('.',$value['normalDrawable']);
					$normalEnd = end($normalEnd);
					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/navigator/normal_'.($key+1).'.'.$normalEnd);
					}else{
						$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/navigator/normal_'.($key+1).'.'.$normalEnd);
						@copy($path.'source/widget/drawable/navigator/normal_'.($key+1).'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载导航图片normal_'.($key+1).'.'.$normalEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载导航图片normal_'.($key+1).'.'.$normalEnd. '失败！';
							result($msg);
						}
					}
					$navigator['items'][$key]['normalDrawable'] = 'normal_'.($key+1).'.'.$normalEnd;
					$focusedEnd = explode('.',$value['focusedDrawable']);
					$focusedEnd = end($focusedEnd);
					$name = explode('/',$value['focusedDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/navigator/focused_'.($key+1).'.'.$focusedEnd);
					}else{
						$bool = @copy($value['focusedDrawable'],$path.'source/widget/drawable/navigator/focused_'.($key+1).'.'.$focusedEnd);
						@copy($path.'source/widget/drawable/navigator/focused_'.($key+1).'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载导航图片focused_'.($key+1).'.'.$focusedEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载导航图片focused_'.($key+1).'.'.$focusedEnd. '失败！';
							result($msg);
						}

					}
					$navigator['items'][$key]['focusedDrawable'] = 'focused_'.($key+1).'.'.$focusedEnd;
				}
			}
			//创建桌面控件快捷入口图片文件夹
			mkdir($path.'source/widget/drawable/quick_entry',0777,true);
			if (!empty($quickEntry)) {
				foreach ($quickEntry['items'] as $key => $value) {
					$normalEnd = explode('.',$value['normalDrawable']);
					$normalEnd = end($normalEnd);
					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_entry/normal_'.($key+1).'.'.$normalEnd);
					}else{
						$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/quick_entry/normal_'.($key+1).'.'.$normalEnd);
						@copy($path.'source/widget/drawable/quick_entry/normal_'.($key+1).'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载快捷入口图片normal_'.($key+1).'.'.$normalEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载快捷入口图片normal_'.($key+1).'.'.$normalEnd. '失败！';
							result($msg);
						}
					}
					$quickEntry['items'][$key]['normalDrawable'] = 'normal_'.($key+1).'.'.$normalEnd;
					$focusedEnd = explode('.',$value['focusedDrawable']);
					$focusedEnd = end($focusedEnd);
					$name = explode('/',$value['focusedDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_entry/focused_'.($key+1).'.'.$focusedEnd);
					}else{
						$bool = @copy($value['focusedDrawable'],$path.'source/widget/drawable/quick_entry/focused_'.($key+1).'.'.$focusedEnd);
						@copy($path.'source/widget/drawable/quick_entry/focused_'.($key+1).'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载快捷入口图片focused_'.($key+1).'.'.$focusedEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载快捷入口图片focused_'.($key+1).'.'.$focusedEnd. '失败！';
							result($msg);
						}
					}
					$quickEntry['items'][$key]['focusedDrawable'] = 'focused_'.($key+1).'.'.$focusedEnd;
				}
			}
			//创建桌面控件两态快捷入口图片文件夹
			mkdir($path.'source/widget/drawable/two_state_quick_entry',0777,true);
			if (!empty($quickEntryTwoState)) {
				foreach ($quickEntryTwoState['items'] as $key => $value) {

					$name = explode('/',$value['activeDrawable']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/two_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/two_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['activeDrawable'],$path.'source/widget/drawable/two_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/two_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载两态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载两态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryTwoState['items'][$key]['activeDrawable'] = $name;
					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/two_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/two_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/two_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/two_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载两态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载两态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryTwoState['items'][$key]['normalDrawable'] = $name;
					$name = explode('/',$value['focusedActiveDrawable']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/two_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/two_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedActiveDrawable'],$path.'source/widget/drawable/two_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/two_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载两态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载两态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryTwoState['items'][$key]['focusedActiveDrawable'] = $name;
					$name = explode('/',$value['focusedNormalDrawable']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/two_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/two_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedNormalDrawable'],$path.'source/widget/drawable/two_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/two_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载两态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载两态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryTwoState['items'][$key]['focusedNormalDrawable'] = $name;
				}
			}
			//创建桌面控件三态快捷入口图片文件夹
			mkdir($path.'source/widget/drawable/three_state_quick_entry',0777,true);
			if (!empty($quickEntryThreeState)) {
				foreach ($quickEntryThreeState['items'] as $key => $value) {

					$name = explode('/',$value['drawableA']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['drawableA'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryThreeState['items'][$key]['drawableA'] = $name;
					$name = explode('/',$value['drawableB']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['drawableB'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryThreeState['items'][$key]['drawableB'] = $name;
					$name = explode('/',$value['drawableC']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['drawableC'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryThreeState['items'][$key]['drawableC'] = $name;
					$name = explode('/',$value['focusedDrawableA']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedDrawableA'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryThreeState['items'][$key]['focusedDrawableA'] = $name;
					$name = explode('/',$value['focusedDrawableB']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedDrawableB'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载三态快捷入口图片'.$name);
							}else{
								$msg .= '下载三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryThreeState['items'][$key]['focusedDrawableB'] = $name;

					$name = explode('/',$value['focusedDrawableC']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedDrawableC'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					$quickEntryThreeState['items'][$key]['focusedDrawableC'] = $name;
				}
			}
			//创建桌面控件附件图片文件夹
			mkdir($path.'source/widget/drawable/slot_attachment',0777,true);
			if ($slotAttachment) {
				foreach ($slotAttachment['items'] as $key => $value) {
					$normalEnd = explode('.',$value['normalDrawable']);
					$normalEnd = end($normalEnd);

					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/slot_attachment/normal_'.($key+1).'.'.$normalEnd);
					}else{
						$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/slot_attachment/normal_'.($key+1).'.'.$normalEnd);
						@copy($path.'source/widget/drawable/slot_attachment/normal_'.($key+1).'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载三态快捷入口图片'.$name);
							// return false;
						}else{
							$msg .= '下载附件图片normal_'.($key+1).'.'.$normalEnd. '失败！';
							result($msg);
						}
					}
					$slotAttachment['items'][$key]['normalDrawable'] = 'normal_'.($key+1).'.'.$normalEnd;
					$focusedEnd = explode('.',$value['focusedDrawable']);
					$focusedEnd = end($focusedEnd);

					$name = explode('/',$value['focusedDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/slot_attachment/focused_'.($key+1).'.'.$focusedEnd);
					}else{
						$bool = @copy($value['focusedDrawable'],$path.'source/widget/drawable/slot_attachment/focused_'.($key+1).'.'.$focusedEnd);
						@copy($path.'source/widget/drawable/slot_attachment/focused_'.($key+1).'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载三态快捷入口图片'.$name);
							// return false;
						}else{
							$msg .= '下载附件图片focused_'.($key+1).'.'.$focusedEnd. '失败！';
							result($msg);
						}
					}
					$slotAttachment['items'][$key]['focusedDrawable'] = 'focused_'.($key+1).'.'.$focusedEnd;
				}
			}
			//创建桌面控件时间与天气图片文件夹
			mkdir($path.'source/widget/drawable/time_weather',0777,true);
			//创建桌面全局配置文件夹
			mkdir($path.'source/config',0777,true);
			//创建桌面基础设置文件
			if (!empty($app)) {
				$myfile = @fopen($path.'source/config/app_config.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 app_config.json文件');
				}
				$slotsFileStr = json_encode($app,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}

			//创建桌面快捷键文件夹
			mkdir($path.'source/shortcut',0777,true);
			//创建桌面快捷键
			if (!empty($quickKey)) {
				$myfile = @fopen($path.'source/shortcut/shortcuts.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 shortcuts.json文件');
				}
				$slotsFileStr = json_encode($quickKey,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			//创建桌面控件json文件夹
			mkdir($path.'source/widget/json',0777,true);

			if (!empty($weather)) {
				$myfile = @fopen($path.'source/widget/json/widget_weather.json', "w") ;
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_weather.json文件');
				}
				$slotsFileStr = json_encode($weather,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($time)) {
				$myfile = @fopen($path.'source/widget/json/widget_time.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_time.json文件');
				}
				$slotsFileStr = json_encode($time,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($quickEntry)) {
				$myfile = @fopen($path.'source/widget/json/widget_quick_entry.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_quick_entry.json文件');
				}
				$slotsFileStr = json_encode($quickEntry,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($quickEntryTwoState)) {
				$myfile = @fopen($path.'source/widget/json/widget_two_state_quick_entry.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_two_state_quick_entry.json文件');
				}
				$slotsFileStr = json_encode($quickEntryTwoState,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($quickEntryThreeState)) {
				$myfile = @fopen($path.'source/widget/json/widget_three_state_quick_entry.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_three_state_quick_entry.json文件');
				}
				$slotsFileStr = json_encode($quickEntryThreeState,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($navigator)) {
				$myfile = @fopen($path.'source/widget/json/widget_navigator.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_navigator.json文件');
				}
				$slotsFileStr = json_encode($navigator,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($logo)) {
				$myfile = @fopen($path.'source/widget/json/widget_logo.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_logo.json文件');
				}
				$slotsFileStr = json_encode($logo,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($sn)) {
				$myfile = @fopen($path.'source/widget/json/widget_info.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_info.json文件');
				}
				$slotsFileStr = json_encode($sn,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($slotAttachment)) {
				$myfile = @fopen($path.'source/widget/json/widget_slot_attachment.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_slot_attachment.json文件');
				}
				$slotsFileStr = json_encode($slotAttachment,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}

			$zip=new \ZipArchive();
			$zipFile = './'.$desktop['name'].'_'.$desktop['update_time'].'.zip';
			$res = $zip->open($zipFile, \ZipArchive::CREATE);

			if( $res=== TRUE){
			    	addFileToZip($path.'source','source',$zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
			    	$zip->close(); //关闭处理的zip文件
			}else{
				switch($res){
				            case \ZipArchive::ER_EXISTS:
				                $ErrMsg = "文件已经存在";
				                break;
				            case \ZipArchive::ER_INCONS:
				                $ErrMsg = "Zip归档文件不一致";
				                break;

				            case \ZipArchive::ER_MEMORY:
				                $ErrMsg = "分配内存失败";
				                break;

				            case \ZipArchive::ER_NOENT:
				                $ErrMsg = "没有这样的文件";
				                break;

				            case \ZipArchive::ER_NOZIP:
				                $ErrMsg = "不是一个zip归档";
				                break;

				            case \ZipArchive::ER_OPEN:
				                $ErrMsg = "无法打开文件";
				                break;

				            case \ZipArchive::ER_READ:
				                $ErrMsg = "读取错误";
				                break;

				            case \ZipArchive::ER_SEEK:
				                $ErrMsg = "搜索错误";
				                break;

				            default:
				                $ErrMsg = "未知原因";
				                break;

				        }
			        	if ($error) {
			        		return $result = array('reason'=> '打包出错: ' . $ErrMsg);
					// return false;
				}else{
					result( '打包出错: ' . $ErrMsg);
					// die( '打包出错: ' . $ErrMsg);
				}
			}
			$formData = array(
				"desktopSourceFile" => array(
			    		"extension" =>"zip",
					"md5_file" => $desktop['name']."source_".$desktop['update_time'],
			    		"filepath" => $zipFile,
				    	"size" => filesize($zipFile)
			  	)
			);
			require_once '../Base/Ossupclass/OssBase.class.php';
			$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
			$res = $base->uploadFile($formData);
			$options = array(
				'model'=>$desktop['name'],
				'path'=>$res['desktopSourceFile']['oss'],
				'version'=>$desktop['update_time'],
				'time'=>time()
			);
			unlink($zipFile);
			D('DesktopSource','Desktop')->addDesktopSource($options,$error);
			//删除文件
			$path = './'.$desktop['name'];
			if (file_exists($path)) {
				removeDir($path);
			}
			if (file_exists('source')) {
				removeDir('source');
			}
			if (!empty($error)) {
				$response['error'] = $msg;
				if ($error) {
					return true;
				}else{
					result(true,$response);
				}
			}else{
				if ($error) {
					return true;
				}else{
					result();
				}
			}
			// echo $runTime = time() - $startTime;
		}
		*/
		/**
		 * 桌面管理_生成桌面版本
		 * get /desktop/createDesktopSource?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function createDesktopVersion($id=null,$desktop=null,$error = false)
		{

			set_time_limit(0);
			if ($id===null) {
				$id = I('get.id');
			}
			//实例化
			$DesktopVersion = D('DesktopVersion','Desktop');

			if (empty($id)) {
				if ($error) {
					return $result = array('reason'=>'桌面参数有误');
				}else{
					result('param');
				}
			}
			if ($desktop===null) {
				$desktop = D('Desktop','Desktop')->getValForId($id);
			}
			if ($desktop) {
				$desktopVersion = $DesktopVersion->getValOneForModelForVersionForLayoutVersion($desktop['name'],$desktop['update_time'],$desktop['layout_update_time']);
				if ($desktopVersion) {
					if ($error) {
						return $result = array('reason'=>'该桌面没有修改');
						// return false;
					}else{
						result('该桌面没有修改');
					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面不存在');
					// return false;
				}else{
					result('桌面不存在');
				}
			}

			$uniqueIndexArr = array();

			//创建桌面基础设置文件
			$app = $this->createAppConfig($id,$desktop,$error);
			if ($error) {
				if (!$app) {
					return $result = array('reason'=>'创建桌面基础设置文件出错');
					// return false;
				}
			}else{
				result('创建桌面基础设置文件出错');
			}
			//判断桌面基础设置--导航
			if ($app['isCreateNavigation'] == 'true') {
				//创建导航配置文件
				$navigator = $this->createNavigatorConfig($id,$desktop,$error);
				if ($navigator === false) {
					if ($error) {
						return $result = array('reason'=>'导航数据出错');
						// return false;
					}else{
						result('导航数据出错');
					}
				}elseif (!empty($navigator['reason'])) {
					if ($error) {
						return $navigator;
						// return false;
					}else{
						result($navigator['reason']);
					}
				}
			}else{
				$navigator = false;
			}
			//判断桌面基础设置--时间
			if ($app['isCreateTimeWidget'] == 'true' ) {
				//创建桌面时间配置文件
				$time = $this->createTimeConfig($id,$desktop,$error);
				if ($time === false) {
					if ($error) {
						return $result = array('reason'=>'时间数据出错');
						// return false;
					}else{
						result('时间数据出错');
					}
				}elseif (!empty($time['reason'])) {
					if ($error) {
						return $time ;
						// return false;
					}else{
						result($time['reason']);
					}
				}


			}else{
				$time =false;
			}
			//判断桌面基础设置--天气
			if ($app['isCreateWeatherWidget'] == 'true'  ) {
				//创建桌面天气配置文件
				$weather = $this->createWeatherConfig($id,$desktop,$error);
				if ($weather ===false) {
					if ($error) {
						return $result = array('reason'=>'天气数据出错');

						// return false;
					}else{
						result('天气数据出错');
					}
				}elseif (!empty($weather['reason'])) {
					if ($error) {
						return $weather;

						// return false;
					}else{
						result($weather['reason']);
					}
				}

			}else{
				$weather =false;
			}
			//判断桌面基础设置--附件
			if ($app['isCreateSlotAttachment'] == 'true') {
				//创建桌面附件文件
				$slotAttachment = $this->createSlotAttachmentConfig($id,$desktop,$error);
				if ($slotAttachment ===false) {
					if ($error) {
						return $result = array('reason'=>'附件数据出错');
						 // $result;
						// return false;
					}else{
						result('附件数据出错');
					}
				}elseif (!empty($slotAttachment['reason'])) {
					if ($error) {
						return $slotAttachment;
						 // $result;
						// return false;
					}else{
						result($slotAttachment['reason']);
					}
					return $slotAttachment;
				}else{
					foreach ($slotAttachment['items'] as $key => $value) {
						$uniqueIndexArr[] = $value['id'];
					}
				}
			}else{
				$slotAttachment =false;
			}



			//判断桌面基础设置--Logo
			if ($app['isCreateLogoWidget'] == 'true') {
				//创建桌面LOGO配置文件
				$logo = $this->createLogoConfig($id,$desktop,$error);
				if ($logo ===false) {
					if ($error) {
						return $result = array('reason'=>'Logo数据出错');
						// return false;
					}else{
						result('Logo数据出错');
					}
				}elseif (!empty($logo['reason'])) {
					if ($error) {
						return $logo;
						// return false;
					}else{
						result($logo['reason']);
					}
				}


			}else{
				$logo =false;
			}
			//判断桌面基础设置--快捷入口
			if ($app['isCreateQuickEntry'] == 'true' ) {
				//创建快捷入口配置文件
				$quickEntry = $this->createQuickEntryConfig($id,$desktop,$error);
				if ($quickEntry === false) {
					if ($error) {
						return $result = array('reason'=>'快捷入口数据出错');
						// return false;
					}else{
						result('快捷入口数据出错');
					}
				}elseif (!empty($quickEntry['reason'])) {
					if ($error) {
						return $quickEntry;
						// return false;
					}else{
						result($quickEntry['reason']);
					}
				}else{
					foreach ($quickEntry['items'] as $key => $value) {
						$uniqueIndexArr[] = $value['id'];
					}
				}

			}else{
				$quickEntry =false;
			}
			//判断桌面基础设置--SN控件
			if ($app['isCreateSnWidget'] == 'true'  ) {
				//创建桌面SN文件
				$sn = $this->createSnConfig($id,$desktop,$error);
				if ($sn ===false) {
					if ($error) {
						return $result = array('reason'=>'SN控件数据出错');
						// return false;
					}else{
						result('SN控件数据出错');
					}
				}

			}else{
				$sn =false;
			}
			//判断桌面基础设置--时间天气控件
			if ($app['isCreateTimeWeatherWidget'] == 'true'  ) {
				//创建桌面时间天气配置文件
				$timeWeather = $this->createTimeWeatherConfig($id,$desktop,$error);
				if ($timeWeather === false ) {
					if ($error) {
						return $result = array('reason'=>'时间天气数据出错');
						// return false;
					}else{
						result('时间天气数据出错');
					}
				}elseif (!empty($timeWeather['reason'])) {
					if ($error) {
						return $timeWeather;
						// return false;
					}else{
						result($timeWeather['reason']);
					}
				}


				//创建桌面时间配置文件
				if ($time) {
					$time['x'] = '2000';
					$time['y'] = '2000';
				}
				//桌面天气配置文件
				if ($weather) {
					$weather['x'] = '2000';
					$weather['y'] = '2000';
				}
			}else{
				$timeWeather =false;
			}

			//创建桌面风格设置文件
			// $style = $this->createStyleConfig($id,$desktop,true);

			//判断桌面基础设置--创建桌面底部快捷栏配置

			if ($app['isCreateQuickList'] == 'true') {
				$quickList = $this->createQuickList($id,$desktop,$error);
				//创建桌面底部快捷栏配置
				if (!empty($quickList['apps'])) {
					foreach ($quickList['apps'] as $key => $value) {
						$uniqueIndexArr[] = $value['id'];
					}
				}
				if (!empty($quickList['reason'])) {
					if (!empty($quickList['reason'])) {
						if ($error) {
							return $quickList;
							// return false;
						}else{
							result($quickList['reason']);
						}
					}
				}

			}else{
				$quickList = false;
			}

			//创建桌面快捷键
			$quickKey = $this->createQuickKey($id,$desktop,$error);
			if (!empty($quickKey['reason'])) {
				if ($error) {
					return $quickKey;
					// return false;
				}else{
					result($quickKey['reason']);
				}
			}

			//创建桌面消息（走马灯）配置文件
			$messageConfig = $this->createMessageConfig($id,$desktop,$error);
			if (!empty($messageConfig['reason'])) {
				if ($error) {
					return $messageConfig ;
					// return false;
				}else{
					result($messageConfig['reason']);
				}
			}
			//创建桌面屏文件
			$slots = $this->createDesktopSlotsFile($id,$desktop,$error);
			if (!empty($slots['reason'])) {
				return $slots;
			}elseif (empty($slots)) {
				if ($error) {
					return $result = array('reason'=>'存在屏，但没有屏信息');
					// return false;
				}else{
					result('存在屏，但没有屏信息');
				}
				/*if (empty($slots['screens'])) {
					if ($navigator) {
						if (count($slots['screens']) != count($navigator['items'])) {
							if ($error) {
								return $result = array('reason'=>'桌面屏与导航不一致');
								// return false;
							}else{
								result('桌面屏与导航不一致');
							}
						}
					}else{
						if (count($slots['screens']) != 1) {
							if ($error) {
								return $result = array('reason'=>'桌面屏大于1：需要导航');
								// return false;
							}else{
								result('桌面屏大于1：需要导航');
							}
						}
					}
				}else{
					$navigator = false;
				}*/
			}elseif ( empty($slots['screens']) && ( $app['isAllowSlotEmpty'] == 'false') ) {//判断桌面基础设置--坑位是否为零
				if ($error) {
					return $result = array('reason'=>'基础设置:坑位数量不能为零');
					// return false;
				}else{
					result('基础设置:坑位数量不能为零');
				}
			}
			//创建三态快捷入口配置文件
			$quickEntryThreeState = $this->createQuickEntryThreeStateConfig($id,$desktop,$error);
			if (!empty($quickEntryThreeState['reason'])) {
				if ($error) {
					return $quickEntryThreeState;
					// return false;
				}else{
					result($quickEntryThreeState['reason']);
				}
			}
			//创建两态快捷入口配置文件
			$quickEntryTwoState = $this->createQuickEntryTwoStateConfig($id,$desktop,$error);
			if (!empty($quickEntryTwoState['reason'])) {
				if ($error) {
					return $quickEntryTwoState;
					// return false;
				}else{
					result($quickEntryTwoState['reason']);
				}
			}

			//创建桌面可下载快捷入口坑位文件
			$downloadQuickEntry = $this->createDesktopDownloadQuickEntryLists($id,$desktop,$error);
			if (!empty($downloadQuickEntry['reason'])) {
				if ($error) {
					return $downloadQuickEntry;
					// return false;
				}else{
					result($downloadQuickEntry['reason']);
				}
			}

			//创建桌面快捷入口组
			$quickEntryGroup = $this->createQuickEntryGroupConfig($id,$desktop,$error);
			if (!empty($downloadQuickEntry['reason'])) {
				if ($error) {
					return $downloadQuickEntry;
					// return false;
				}else{
					result($downloadQuickEntry['reason']);
				}
			}

			/*}else{
				//创建桌面基础设置文件
				$app = $this->createAppConfig($id);
				if (!$app) {
					return $result = array('reason'=>'创建桌面基础设置文件出错');
					// return false;
				}
				//判断桌面基础设置--导航
				if ($app['isCreateNavigation'] == 'true') {
					//创建导航配置文件
					$navigator = $this->createNavigatorConfig($id);
					if ($navigator === false) {
						if ($error) {
							return $result = array('reason'=>'导航数据出错');
							// return false;
						}else{
							result('导航数据出错');
						}
					}

				}else{
					$navigator = false;
				}
				//判断桌面基础设置--时间
				if ($app['isCreateTimeWidget'] == 'true' ) {
					//创建桌面时间配置文件
					$time = $this->createTimeConfig($id);
					if ($time ===false) {
						if ($error) {
							return $result = array('reason'=>'时间数据出错');
							// return false;
						}else{
							result('时间数据出错');
						}
					}

				}else{
					$time =false;
				}
				//判断桌面基础设置--天气
				if ($app['isCreateWeatherWidget'] == 'true'  ) {
					//创建桌面天气配置文件
					$weather = $this->createWeatherConfig($id);
					if ($weather === false) {
						if ($error) {
							return $result = array('reason'=>'天气数据出错');

							// return false;
						}else{
							result('天气数据出错');
						}
					}
				}else{
					$weather =false;
				}
				//判断桌面基础设置--附件
				if ($app['isCreateSlotAttachment'] == 'true') {
					//创建桌面附件文件
					$slotAttachment = $this->createSlotAttachmentConfig($id);
					if ($slotAttachment ===false) {
						if ($error) {
							return $result = array('reason'=>'附件数据出错');
							 // $result;
							// return false;
						}else{
							result('附件数据出错');
						}
					}else{
						foreach ($slotAttachment['items'] as $key => $value) {
							$uniqueIndexArr[] = $value['id'];
						}
					}

				}else{
					$slotAttachment =false;
				}
				//判断桌面基础设置--Logo
				if ($app['isCreateLogoWidget'] == 'true') {
					//创建桌面LOGO配置文件
					$logo = $this->createLogoConfig($id);
					if ($logo ===false) {
						if ($error) {
							return $result = array('reason'=>'Logo数据出错');
							// return false;
						}else{
							result('Logo数据出错');
						}
					}

				}else{
					$logo =false;
				}
				//判断桌面基础设置--快捷入口
				if ($app['isCreateQuickEntry'] == 'true' ) {
					//创建快捷入口配置文件
					$quickEntry = $this->createQuickEntryConfig($id);
					if ($quickEntry ===false) {
						if ($error) {
							return $result = array('reason'=>'快捷入口数据出错');
							// return false;
						}else{
							result('快捷入口数据出错');
						}
					}else{
						foreach ($quickEntry['items'] as $key => $value) {
							$uniqueIndexArr[] = $value['id'];
						}
					}

				}else{
					$quickEntry =false;
				}
				//判断桌面基础设置--SN控件
				if ($app['isCreateSnWidget'] == 'true'  ) {
					//创建桌面SN文件
					$sn = $this->createSnConfig($id);
					if ($sn ===false) {
						if ($error) {
							return $result = array('reason'=>'SN控件数据出错');
							// return false;
						}else{
							result('SN控件数据出错');
						}
					}

				}else{
					$sn =false;
				}
				//判断桌面基础设置--时间天气控件
				if ($app['isCreateTimeWeatherWidget'] == 'true'  ) {

					//创建桌面时间天气配置文件
					$timeWeather = $this->createTimeWeatherConfig($id);
					if ($timeWeather === false ) {
						if ($error) {
							return $result = array('reason'=>'时间天气数据出错');
							// return false;
						}else{
							result('时间天气数据出错');
						}
					}
					//创建桌面时间配置文件
					if ($time) {
						$time['x'] = '2000';
						$time['y'] = '2000';
					}
					//桌面天气配置文件
					if ($weather) {
						$weather['x'] = '2000';
						$weather['y'] = '2000';
					}

				}else{
					$timeWeather =false;
				}
				//创建桌面底部快捷栏配置
				$quickList = $this->createQuickList($id);
				//创建桌面快捷键
				$quickKey = $this->createQuickKey($id);
				//创建桌面屏文件
				$slots = $this->createDesktopSlotsFile($id);
				//创建桌面风格设置文件
				// $style = $this->createStyleConfig($id);
				//创建三态快捷入口配置文件
				$quickEntryThreeState = $this->createQuickEntryThreeStateConfig($id);
				//创建两态快捷入口配置文件
				$quickEntryTwoState = $this->createQuickEntryTwoStateConfig($id);
				//创建桌面可下载快捷入口坑位文件
				$downloadQuickEntry = $this->createDesktopDownloadQuickEntryLists($id);

			}*/

			if (!empty($quickEntryGroup)) {
				foreach ($quickEntryGroup['mList'] as $key => $value) {
					foreach ($value['mData'] as $k => $v) {
						$uniqueIndexArr[] = $v['id'];
					}

				}
			}

			if (!empty($quickEntryThreeState)) {
				foreach ($quickEntryThreeState['items'] as $key => $value) {
					$uniqueIndexArr[] = $value['id'];
				}
			}
			if (!empty($quickEntryTwoState)) {
				foreach ($quickEntryTwoState['items'] as $key => $value) {
					$uniqueIndexArr[] = $value['id'];
				}
			}

			//控件ID是否重复
			$uniqueIndexArr = array_count_values($uniqueIndexArr);
			$indexStr = '';
			foreach ($uniqueIndexArr as $key => $value) {
				if ($value > 1) {
					$indexStr .='ID'.$key .'出现'.$value .'次，';
				}
			}

			if (!empty($indexStr)) {
				if ($error) {
					return $result = array('reason'=>'快捷入口或快捷入口组或三态快捷入口或两态快捷入口或底部快捷栏或附件栏ID重复:'.$indexStr);
					// return false;
				}else{
					result('快捷入口或三态快捷入口或两态快捷入口或底部快捷栏或附件栏ID重复:'.$indexStr);
				}
			}

			$path = C('LOCALHOST_PATH_ADDR') .'temp/'.$desktop['name'].'/';

			if (file_exists($path)) {
				removeDir($path);
			}

			/*else{
				if ($error) {
					return $result = array('reason'=>'存在屏，但没有屏信息');
					// return false;
				}else{
					result('存在屏，但没有屏信息');
				}
			}*/

			//创建桌面资源包
			mkdir($path.'source',0777,true);
			$msg = '';
			//复制桌面壁纸
			if (!empty($desktop['image'])) {
				$end = explode('.',$desktop['image']);
				$end = end($end);
				$name = explode('/',$desktop['image']);
				$name = end($name);
				if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
					$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/'.'default_wallpaper.'.$end);
				}else{
					$bool = @copy($desktop['image'],$path.'source/'.'default_wallpaper.'.$end);
					@copy($path.'source/'.'default_wallpaper.'.$end,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
				}
				if (!$bool) {
					if ($error) {
						return $result = array('reason'=>'下载桌面壁纸'.$name. '失败！');
						// return false;
					}else{
						$msg .= '下载桌面壁纸'.$name. '失败！';
						result($msg);
					}
				}
				if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

					if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_WALLPAPER_IMG')) {

						if ($error) {
							return $result = array('reason'=>'桌面壁纸不能超过'.(C('DESKTOP_WALLPAPER_IMG')/1024/1024).'M');
							// return false;
						}else{
							result('桌面壁纸不能超过'.(C('DESKTOP_WALLPAPER_IMG')/1024/1024).'M');
						}
					}

					/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
						result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
					}*/
				}else{
					if ($error) {
						return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
						// return false;
					}else{
						$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
						result($msg);
					}
				}
			}

			//复制焦点框图片
			if (!empty($app['focusImage'])) {
				$end = explode('.',$app['focusImage']);
				$end = end($end);
				$name = explode('/',$app['focusImage']);
				$name = end($name);
				if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
					$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/focus.9.png');
				}else{
					$bool = @copy($app['focusImage'],$path.'source/focus.9.png');
					@copy($path.'source/focus.9.png',C('LOCALHOST_PATH_ADDR').'pic/'.$name);
				}
				if (!$bool) {
					if ($error) {
						return $result = array('reason'=>'下载桌面焦点框图片'.$name. '失败！');
						// return false;
					}else{
						$msg .= '下载桌面焦点框图片'.$name. '失败！';
						result($msg);
					}
				}
				if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

					if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_WALLPAPER_IMG')) {

						if ($error) {
							return $result = array('reason'=>'桌面焦点框图片不能超过'. ( C('DESKTOP_SLOT_IMG')/1024 ) . 'KB');
							// return false;
						}else{
							result('桌面焦点框图片不能超过' . ( C('DESKTOP_SLOT_IMG')/1024 ) . 'KB');
						}
					}

					/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
						result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
					}*/
				}else{
					if ($error) {
						return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
						// return false;
					}else{
						$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
						result($msg);
					}
				}
				$app['focusImage'] = 'focus.9.png';
			}
			//创建桌面坑位文件夹
			mkdir($path.'source/slot',0777,true);
			//创建桌面坑位图片文件夹
			mkdir($path.'source/slot/drawable',0777,true);
			if (!empty($slots['screens'])) {
				foreach ($slots['screens'] as $key => $value) {
					foreach ($value['slots'] as $k => $v) {
						if (!empty($v['picUrl'])) {
							$end = explode('.',$v['picUrl']);
							$end = end($end);
							$name = explode('/',$v['picUrl']);
							$name = end($name);
							if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
								$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/slot/drawable/'.$v['id'].'.'.$end);

							}else{
								$bool = @copy($v['picUrl'],$path.'source/slot/drawable/'.$v['id'].'.'.$end);
								@copy($path.'source/slot/drawable/'.$v['id'].'.'.$end,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
							}

							if (!$bool) {
								if ($error) {
									return $result = array('reason'=>'下载'.$v['id'].'坑位的图片'.$name. '失败！');

									// return false;
								}else{
									$msg .= '下载'.$v['id'].'坑位的图片'. $name.'失败！';
									result($msg);
								}
							}
							//判断图片大小
							if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

								if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

									if ($error) {
										return $result = array('reason'=>$v['id'].'坑位的图片不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
										// return false;
									}else{
										result($v['id'].'坑位的图片不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
									}
								}

								/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
									result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								}*/
							}else{
								if ($error) {
									return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
									// return false;
								}else{
									$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
									result($msg);
								}
							}
						}
					}
				}
			}

			//创建桌面坑位json文件夹
			mkdir($path.'source/slot/json',0777,true);
			//生成坑位json文件
			$myfile = @fopen($path.'source/slot/json/slots.json', "w");
			if (!$myfile) {
				return $result = array('reason'=> '无法打开 slots.json文件');
			}
			$slotsFileStr = json_encode($slots,JSON_UNESCAPED_UNICODE);
			fwrite($myfile, $slotsFileStr);
			fclose($myfile);

			//判断是否更新source包
			if ($desktopVersionOne = $DesktopVersion->getOneForModelVersion($desktop['name'],$desktop['update_time'])) {
				$formData = array(
					/*"desktopSourceFile" => array(
				    		"extension" =>"zip",
						"md5_file" => $desktop['name']."_source_".$desktop['update_time'],
				    		"filepath" => $zipFile,
					    	"size" => filesize($zipFile)
				  	),*/
				  	"desktopSlotsFile" => array(
				    		"extension" =>"json",
						"md5_file" => $desktop['name']."_layout_".$desktop['layout_update_time'],
				    		"filepath" => $path.'source/slot/json/slots.json',
					    	"size" => filesize($path.'source/slot/json/slots.json')
				  	)
				);
				require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				$res = $base->uploadFile($formData);
				// $zipFile = realpath($zipFile);
				if (empty($desktopVersionOne['md5'])) {

					$name = explode('/',$desktopVersionOne['sourcePath']);
					$name = end($name);
					if ( file_exists( C('LOCALHOST_PATH_ADDR').'temp/'.$name ) ) {
						unlink(C('LOCALHOST_PATH_ADDR').'temp/'.$name);
					}

					@copy(C('DOWNLOAD_DESKTOP_SOURCER_PREFIX_ADDR') . $desktopVersionOne['sourcePath'], C('LOCALHOST_PATH_ADDR').'temp/'.$name);

					if (file_exists(C('LOCALHOST_PATH_ADDR').'temp/'.$name)) {
						$desktopVersionOne['md5'] = md5_file( C('LOCALHOST_PATH_ADDR').'temp/'.$name );
					}else{
						if ($error) {
							return $result = array('reason'=>'更新MD5:下载source包失败！');
							// return false;
						}else{
							result('更新MD5:下载source包失败！');
						}
					}


					if (file_exists(C('LOCALHOST_PATH_ADDR').'temp/'.$name)) {
						unlink(C('LOCALHOST_PATH_ADDR').'temp/'.$name);
					}
				}
				$options = array(
					'model'=>$desktop['name'],
					'sourcePath'=>$desktopVersionOne['sourcePath'],
					'layoutPath'=>$res['desktopSlotsFile']['oss'],
					'version'=>$desktopVersionOne['version'],
					'md5'=>$desktopVersionOne['md5'],
					'layoutVersion'=>$desktop['layout_update_time'],
					'time'=>time()
				);


				//测试专用slots--end
				if (file_exists($zipFile)) {
					unlink($zipFile);
				}
				$DesktopVersion->addDesktopVersion($options,$error);
				//删除文件

				if (file_exists($path)) {
					removeDir($path);
				}
				if (file_exists('source')) {
					removeDir('source');
				}
				if ($error) {
					return true;
				}else{
					result();
				}

			}

			//创建桌面底部快捷栏文件夹
			mkdir($path.'source/quicklist/drawable',0777,true);
			//创建桌面底部快捷栏文件夹
			mkdir($path.'source/quicklist/json',0777,true);
			//判断桌面基础设置--底部快捷栏
			if ($app['isCreateQuickList'] == 'true' && $quickList === false) {
				$slotsFileStr = '{"apps":[]}';
				//创建桌面底部快捷栏配置
				$myfile = @fopen($path.'source/quicklist/json/quicklist_config.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 quicklist_config.json文件');
				}
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}elseif (!empty($quickList)&&$app['isCreateQuickList'] == 'true') {
				foreach ($quickList['apps'] as $key => $value) {
					$name = explode('/',$value['appIcon']);
					$name = end($name);
					if (!file_exists($path.'source/quicklist/drawable/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/quicklist/drawable/'.$name);
						}else{
							$bool = @copy($value['appIcon'],$path.'source/quicklist/drawable/'.$name);
							@copy($path.'source/quicklist/drawable/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
					}

					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载底部快捷栏Icon'.$name. '失败！');
							// return false;
						}else{
							$msg .= '下载底部快捷栏Icon'.$name. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'底部快捷栏Icon'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('底部快捷栏Icon'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickList['apps'][$key]['appIcon'] = $name;

				}
				//创建桌面底部快捷栏配置
				$myfile = @fopen($path.'source/quicklist/json/quicklist_config.json', "w") ;
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 quicklist_config.json文件');
				}
				$slotsFileStr = json_encode($quickList,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);

			}
			//创建桌面控件文件夹
			mkdir($path.'source/widget',0777,true);
			//创建桌面控件图片文件夹
			mkdir($path.'source/widget/drawable',0777,true);
			//创建桌面控件logo图片文件夹
			mkdir($path.'source/widget/drawable/logo',0777,true);
			if (!empty($logo)) {
				foreach ($logo['logoArray'] as $key => $value) {
					$end = explode('.',$value);
					$end = end($end);
					$name = explode('/',$value);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/logo/'.$key.'.'.$end);
					}else{
						$bool = @copy($value,$path.'source/widget/drawable/logo/'.$key.'.'.$end);
						@copy($path.'source/widget/drawable/logo/'.$key.'.'.$end,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载第'.($key+1).'个Logo图片'.$name. '失败！');
							// return false;
						}else{
							$msg .= '下载第'.($key+1).'个Logo图片'. $name . '失败！';
							result($msg);
						}

					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个Logo图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个Logo图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$logo['logoArray'][$key]=$key.'.'.$end;
				}
			}


			//创建桌面控件导航图片文件夹
			mkdir($path.'source/widget/drawable/navigator',0777,true);
			if (!empty($navigator)) {
				foreach ($navigator['items'] as $key => $value) {
					$normalEnd = explode('.',$value['normalDrawable']);
					$normalEnd = end($normalEnd);
					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/navigator/normal_'.($key+1).'.'.$normalEnd);
					}else{
						$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/navigator/normal_'.($key+1).'.'.$normalEnd);
						@copy($path.'source/widget/drawable/navigator/normal_'.($key+1).'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载第'.($key+1).'个导航图片normal_'.($key+1).'.'.$normalEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载第'.($key+1).'个导航图片normal_'.($key+1).'.'.$normalEnd. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个导航图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个导航图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$navigator['items'][$key]['normalDrawable'] = 'normal_'.($key+1).'.'.$normalEnd;

					$focusedEnd = explode('.',$value['focusedDrawable']);
					$focusedEnd = end($focusedEnd);
					$name = explode('/',$value['focusedDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/navigator/focused_'.($key+1).'.'.$focusedEnd);
					}else{
						$bool = @copy($value['focusedDrawable'],$path.'source/widget/drawable/navigator/focused_'.($key+1).'.'.$focusedEnd);
						@copy($path.'source/widget/drawable/navigator/focused_'.($key+1).'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载第'.($key+1).'个导航图片focused_'.($key+1).'.'.$focusedEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载第'.($key+1).'个导航图片focused_'.($key+1).'.'.$focusedEnd. '失败！';
							result($msg);
						}

					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个导航图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个导航图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$navigator['items'][$key]['focusedDrawable'] = 'focused_'.($key+1).'.'.$focusedEnd;

					//当前焦点图
					if (empty($value['currentDrawable'])) {
						continue;
					}

					$currentEnd = explode('.',$value['currentDrawable']);
					$currentEnd = end($currentEnd);
					$name = explode('/',$value['currentDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/navigator/current_'.($key+1).'.'.$currentEnd);
					}else{
						$bool = @copy($value['currentDrawable'],$path.'source/widget/drawable/navigator/current_'.($key+1).'.'.$currentEnd);
						@copy($path.'source/widget/drawable/navigator/current_'.($key+1).'.'.$currentEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载第'.($key+1).'个导航图片current_'.($key+1).'.'.$currentEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载第'.($key+1).'个导航图片current_'.($key+1).'.'.$currentEnd. '失败！';
							result($msg);
						}

					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个导航图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个导航图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$navigator['items'][$key]['currentDrawable'] = 'current_'.($key+1).'.'.$currentEnd;

				}
			}
			//创建桌面控件快捷入口图片文件夹
			mkdir($path.'source/widget/drawable/quick_entry',0777,true);
			if (!empty($quickEntry)) {
				foreach ($quickEntry['items'] as $key => $value) {
					$normalEnd = explode('.',$value['normalDrawable']);
					$normalEnd = end($normalEnd);
					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_entry/normal_'.($key+1).'.'.$normalEnd);
					}else{
						$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/quick_entry/normal_'.($key+1).'.'.$normalEnd);
						@copy($path.'source/widget/drawable/quick_entry/normal_'.($key+1).'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载第'.($key+1).'个快捷入口图片normal_'.($key+1).'.'.$normalEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载第'.($key+1).'个快捷入口图片normal_'.($key+1).'.'.$normalEnd. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntry['items'][$key]['normalDrawable'] = 'normal_'.($key+1).'.'.$normalEnd;
					$focusedEnd = explode('.',$value['focusedDrawable']);
					$focusedEnd = end($focusedEnd);
					$name = explode('/',$value['focusedDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_entry/focused_'.($key+1).'.'.$focusedEnd);
					}else{
						$bool = @copy($value['focusedDrawable'],$path.'source/widget/drawable/quick_entry/focused_'.($key+1).'.'.$focusedEnd);
						@copy($path.'source/widget/drawable/quick_entry/focused_'.($key+1).'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载第'.($key+1).'个快捷入口图片focused_'.($key+1).'.'.$focusedEnd. '失败！');

							// return false;
						}else{
							$msg .= '下载第'.($key+1).'个快捷入口图片focused_'.($key+1).'.'.$focusedEnd. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntry['items'][$key]['focusedDrawable'] = 'focused_'.($key+1).'.'.$focusedEnd;
				}
			}
			//创建桌面控件快捷入口组图片文件夹
			mkdir($path.'source/widget/drawable/quick_entry_group',0777,true);
			if ( !empty($quickEntryGroup['mList']) ) {

				foreach ( $quickEntryGroup['mList'] as $key => $value ) {

					foreach ( $value['mData'] as $k => $v ) {

						$normalEnd = explode('.',$v['normalDrawable']);
						$normalEnd = end($normalEnd);
						$name = explode('/',$v['normalDrawable']);
						$name = end($name);
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_entry_group/normal_'.$v['id'].'.'.$normalEnd);
						}else{
							$bool = @copy($v['normalDrawable'],$path.'source/widget/drawable/quick_entry_group/normal_'.$v['id'].'.'.$normalEnd);
							@copy($path.'source/widget/drawable/quick_entry_group/normal_'.$v['id'].'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载ID为'.$v['id'].'快捷入口组图片normal_'.$v['id'].'.'.$normalEnd. '失败！');

								// return false;
							}else{
								$msg .= '下载ID为'.$v['id'].'快捷入口组图片normal_'.$v['id'].'.'.$normalEnd. '失败！';
								result($msg);
							}
						}
						//判断图片大小
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

							if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

								if ($error) {
									return $result = array('reason'=>'ID为'.$v['id'].'的快捷入口组图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
									// return false;
								}else{
									result('ID为'.$v['id'].'的快捷入口组图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								}
							}

							/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
								result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}*/
						}else{
							if ($error) {
								return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
								// return false;
							}else{
								$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
								result($msg);
							}
						}
						$quickEntryGroup['mList'][$key]['mData'][$k]['normalDrawable'] = 'normal_'.$v['id'].'.'.$normalEnd;

						$focusedEnd = explode('.',$v['focusedDrawable']);
						$focusedEnd = end($focusedEnd);
						$name = explode('/',$v['focusedDrawable']);
						$name = end($name);
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_entry_group/focused_'.$v['id'].'.'.$focusedEnd);
						}else{
							$bool = @copy($v['focusedDrawable'],$path.'source/widget/drawable/quick_entry_group/focused_'.$v['id'].'.'.$focusedEnd);
							@copy($path.'source/widget/drawable/quick_entry_group/focused_'.$v['id'].'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载ID为'.$v['id'].'快捷入口组图片focused_'.$v['id'].'.'.$focusedEnd. '失败！');

								// return false;
							}else{
								$msg .= '下载ID为'.$v['id'].'快捷入口组图片focused_'.$v['id'].'.'.$focusedEnd. '失败！';
								result($msg);
							}
						}

						//判断图片大小
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

							if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

								if ($error) {
									return $result = array('reason'=>'ID为'.$v['id'].'快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
									// return false;
								}else{
									result('ID为'.$v['id'].'快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								}
							}

							/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
								result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}*/
						}else{
							if ($error) {
								return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
								// return false;
							}else{
								$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
								result($msg);
							}
						}
						$quickEntryGroup['mList'][$key]['mData'][$k]['focusedDrawable'] = 'focused_'.$v['id'].'.'.$focusedEnd;
					}

				}
			}

			//创建桌面控件两态快捷入口图片文件夹
			mkdir($path.'source/widget/drawable/two_state_quick_entry',0777,true);
			if (!empty($quickEntryTwoState)) {
				foreach ($quickEntryTwoState['items'] as $key => $value) {
					$name = explode('/',$value['activeDrawable']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/two_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/two_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['activeDrawable'],$path.'source/widget/drawable/two_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/two_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个两态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个两态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个两态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个两态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryTwoState['items'][$key]['activeDrawable'] = $name;
					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/two_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/two_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/two_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/two_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个两态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个两态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个两态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个两态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryTwoState['items'][$key]['normalDrawable'] = $name;
					$name = explode('/',$value['focusedActiveDrawable']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/two_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/two_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedActiveDrawable'],$path.'source/widget/drawable/two_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/two_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个两态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个两态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个两态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个两态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryTwoState['items'][$key]['focusedActiveDrawable'] = $name;
					$name = explode('/',$value['focusedNormalDrawable']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/two_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/two_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedNormalDrawable'],$path.'source/widget/drawable/two_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/two_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个两态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个两态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个两态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个两态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryTwoState['items'][$key]['focusedNormalDrawable'] = $name;
				}
			}
			//创建桌面控件三态快捷入口图片文件夹
			mkdir($path.'source/widget/drawable/three_state_quick_entry',0777,true);
			if (!empty($quickEntryThreeState)) {
				foreach ($quickEntryThreeState['items'] as $key => $value) {

					$name = explode('/',$value['drawableA']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['drawableA'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个三态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个三态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryThreeState['items'][$key]['drawableA'] = $name;
					$name = explode('/',$value['drawableB']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['drawableB'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个三态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个三态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryThreeState['items'][$key]['drawableB'] = $name;
					$name = explode('/',$value['drawableC']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['drawableC'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个三态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个三态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryThreeState['items'][$key]['drawableC'] = $name;
					$name = explode('/',$value['focusedDrawableA']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedDrawableA'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个三态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个三态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryThreeState['items'][$key]['focusedDrawableA'] = $name;
					$name = explode('/',$value['focusedDrawableB']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedDrawableB'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个三态快捷入口图片'.$name);
							}else{
								$msg .= '下载第'.($key+1).'个三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个三态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个三态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryThreeState['items'][$key]['focusedDrawableB'] = $name;

					$name = explode('/',$value['focusedDrawableC']);
					$name = end($name);
					if (!file_exists($path.'source/widget/drawable/three_state_quick_entry/'.$name)) {
						if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
							$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/three_state_quick_entry/'.$name);
						}else{
							$bool = @copy($value['focusedDrawableC'],$path.'source/widget/drawable/three_state_quick_entry/'.$name);
							@copy($path.'source/widget/drawable/three_state_quick_entry/'.$name,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
						}
						if (!$bool) {
							if ($error) {
								return $result = array('reason'=>'下载第'.($key+1).'个三态快捷入口图片'.$name);
								// return false;
							}else{
								$msg .= '下载第'.($key+1).'个三态快捷入口图片'.$name;
								result($msg);
							}
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个三态快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个三态快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$quickEntryThreeState['items'][$key]['focusedDrawableC'] = $name;
				}
			}
			//创建桌面控件附件图片文件夹
			mkdir($path.'source/widget/drawable/slot_attachment',0777,true);
			if ($slotAttachment) {
				foreach ($slotAttachment['items'] as $key => $value) {
					$normalEnd = explode('.',$value['normalDrawable']);
					$normalEnd = end($normalEnd);

					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/slot_attachment/normal_'.($key+1).'.'.$normalEnd);
					}else{
						$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/slot_attachment/normal_'.($key+1).'.'.$normalEnd);
						@copy($path.'source/widget/drawable/slot_attachment/normal_'.($key+1).'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载第'.($key+1).'个附件图片'.$name);
							// return false;
						}else{
							$msg .= '下载第'.($key+1).'个附件图片normal_'.($key+1).'.'.$normalEnd. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'第'.($key+1).'个附件图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('第'.($key+1).'个附件快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$slotAttachment['items'][$key]['normalDrawable'] = 'normal_'.($key+1).'.'.$normalEnd;
					$focusedEnd = explode('.',$value['focusedDrawable']);
					$focusedEnd = end($focusedEnd);

					$name = explode('/',$value['focusedDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/slot_attachment/focused_'.($key+1).'.'.$focusedEnd);
					}else{
						$bool = @copy($value['focusedDrawable'],$path.'source/widget/drawable/slot_attachment/focused_'.($key+1).'.'.$focusedEnd);
						@copy($path.'source/widget/drawable/slot_attachment/focused_'.($key+1).'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载第'.($key+1).'个附件图片'.$name);
							// return false;
						}else{
							$msg .= '下载第'.($key+1).'个附件图片focused_'.($key+1).'.'.$focusedEnd. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'附件快捷入口图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('附件快捷入口图片'. $name .'不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$slotAttachment['items'][$key]['focusedDrawable'] = 'focused_'.($key+1).'.'.$focusedEnd;
				}
			}
			//创建桌面可下载快捷入口坑位图片文件夹

			mkdir($path.'source/widget/drawable/quick_slots',0777,true);
			if (!empty($downloadQuickEntry['items'])) {

				foreach ($downloadQuickEntry['items'] as $key => $value) {
					$normalEnd = explode('.',$value['normalDrawable']);
					$normalEnd = end($normalEnd);
					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_slots/normal_'.($key+1).'.'.$normalEnd);
					}else{
						$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/quick_slots/normal_'.($key+1).'.'.$normalEnd);
						@copy($path.'source/widget/drawable/quick_slots/normal_'.($key+1).'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载快捷入口坑位'.($value['id']).'未获得焦点时的图片'.$name. '失败！');
							// return false;
						}else{
							$msg .= '下载快捷入口坑位'.($value['id']).'未获得焦点时的图片'.$name. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'快捷入口坑位'.($value['id']).'未获得焦点时的图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('快捷入口坑位'.($value['id']).'未获得焦点时的图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$downloadQuickEntry['items'][$key]['normalDrawable'] = 'normal_'.($key+1).'.'.$normalEnd;

					$focusedEnd = explode('.',$value['focusedDrawable']);
					$focusedEnd = end($focusedEnd);

					$name = explode('/',$value['focusedDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_slots/focused_'.($key+1).'.'.$focusedEnd);
					}else{
						$bool = @copy($value['focusedDrawable'],$path.'source/widget/drawable/quick_slots/focused_'.($key+1).'.'.$focusedEnd);
						@copy($path.'source/widget/drawable/quick_slots/focused_'.($key+1).'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载快捷入口坑位'.($value['id']).'拿到焦点后显示的图片'.$name. '失败！');
							// return false;
						}else{
							$msg .= '下载快捷入口坑位'.($value['id']).'拿到焦点后显示的图片'.$name. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'快捷入口坑位'.($value['id']).'拿到焦点后显示的图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('快捷入口坑位'.($value['id']).'拿到焦点后显示的图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$downloadQuickEntry['items'][$key]['focusedDrawable'] = 'focused_'.($key+1).'.'.$focusedEnd;
				}
			}
			if (!empty($downloadQuickEntry['globalItems'])) {

				foreach ($downloadQuickEntry['globalItems'] as $key => $value) {
					$normalEnd = explode('.',$value['normalDrawable']);
					$normalEnd = end($normalEnd);
					$name = explode('/',$value['normalDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_slots/global_normal_'.($key+1).'.'.$normalEnd);
					}else{
						$bool = @copy($value['normalDrawable'],$path.'source/widget/drawable/quick_slots/global_normal_'.($key+1).'.'.$normalEnd);
						@copy($path.'source/widget/drawable/quick_slots/global_normal_'.($key+1).'.'.$normalEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载快捷入口坑位'.($value['id']).'未获得焦点时的图片'.$name. '失败！');
							// return false;
						}else{
							$msg .= '下载快捷入口坑位'.($value['id']).'未获得焦点时的图片'.$name. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'快捷入口坑位'.($value['id']).'未获得焦点时的图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('快捷入口坑位'.($value['id']).'未获得焦点时的图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$downloadQuickEntry['globalItems'][$key]['normalDrawable'] = 'global_normal_'.($key+1).'.'.$normalEnd;

					$focusedEnd = explode('.',$value['focusedDrawable']);
					$focusedEnd = end($focusedEnd);

					$name = explode('/',$value['focusedDrawable']);
					$name = end($name);
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {
						$bool = @copy(C('LOCALHOST_PATH_ADDR').'pic/'.$name,$path.'source/widget/drawable/quick_slots/global_focused_'.($key+1).'.'.$focusedEnd);
					}else{
						$bool = @copy($value['focusedDrawable'],$path.'source/widget/drawable/quick_slots/global_focused_'.($key+1).'.'.$focusedEnd);
						@copy($path.'source/widget/drawable/quick_slots/global_focused_'.($key+1).'.'.$focusedEnd,C('LOCALHOST_PATH_ADDR').'pic/'.$name);
					}
					if (!$bool) {
						if ($error) {
							return $result = array('reason'=>'下载快捷入口坑位'.($value['id']).'拿到焦点后显示的图片'.$name. '失败！');
							// return false;
						}else{
							$msg .= '下载快捷入口坑位'.($value['id']).'拿到焦点后显示的图片'.$name. '失败！';
							result($msg);
						}
					}
					//判断图片大小
					if (file_exists(C('LOCALHOST_PATH_ADDR').'pic/'.$name)) {

						if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {

							if ($error) {
								return $result = array('reason'=>'快捷入口坑位'.($value['id']).'拿到焦点后显示的图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
								// return false;
							}else{
								result('快捷入口坑位'.($value['id']).'拿到焦点后显示的图片'.$name. '不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
							}
						}

						/*if (filesize(C('LOCALHOST_PATH_ADDR').'pic/'.$name) >= C('DESKTOP_SLOT_IMG')) {
							result('上传图标不能超过'.(C('DESKTOP_SLOT_IMG')/1024).'KB');
						}*/
					}else{
						if ($error) {
							return $result = array('reason'=>'保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！');
							// return false;
						}else{
							$msg .= '保存图片到本地'.C('LOCALHOST_PATH_ADDR').'pic/'.'失败！';
							result($msg);
						}
					}
					$downloadQuickEntry['globalItems'][$key]['focusedDrawable'] = 'global_focused_'.($key+1).'.'.$focusedEnd;
				}
			}

			//创建桌面控件时间与天气图片文件夹
			mkdir($path.'source/widget/drawable/time_weather',0777,true);

			//创建桌面全局配置文件夹
			mkdir($path.'source/config',0777,true);
			//创建桌面基础设置文件
			if (!empty($app)) {
				$myfile = @fopen($path.'source/config/app_config.json', "w") ;
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 app_config.json文件');
				}
				$slotsFileStr = json_encode($app,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			//创建桌面风格设置文件
			/*if (!empty($style)) {
				$myfile = @fopen($path.'source/config/style_config.json', "w") ;
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 style_config.json文件');
				}
				$slotsFileStr = json_encode($style,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}*/
			//创建桌面快捷键文件夹
			mkdir($path.'source/shortcut',0777,true);
			//创建桌面快捷键
			if (!empty($quickKey)) {
				$myfile = @fopen($path.'source/shortcut/shortcuts.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 shortcuts.json文件');
				}
				$slotsFileStr = json_encode($quickKey,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			//创建桌面控件json文件夹
			mkdir($path.'source/widget/json',0777,true);

			if (!empty($weather)) {
				$myfile = @fopen($path.'source/widget/json/widget_weather.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_weather.json文件');
				}
				$slotsFileStr = json_encode($weather,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			//创建桌面消息（走马灯）配置json文件
			if (!empty($messageConfig)) {
				$myfile = @fopen($path.'source/widget/json/widget_message.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_message.json文件');
				}
				$slotsFileStr = json_encode($messageConfig,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}


			if (!empty($timeWeather)) {
				$myfile = @fopen($path.'source/widget/json/widget_time_weather.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_time_weather.json文件');
				}
				$slotsFileStr = json_encode($timeWeather,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}

			if (!empty($downloadQuickEntry)) {
				$myfile = @fopen($path.'source/widget/json/quick_slots.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 quick_slots.json文件');
				}
				$slotsFileStr = json_encode($downloadQuickEntry,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}

			if (!empty($time)) {
				$myfile = @fopen($path.'source/widget/json/widget_time.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_time.json文件');
				}
				$slotsFileStr = json_encode($time,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}

			if (!empty($quickEntry)) {
				$myfile = @fopen($path.'source/widget/json/widget_quick_entry.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_quick_entry.json文件');
				}
				$slotsFileStr = json_encode($quickEntry,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($quickEntryGroup)) {
				$myfile = @fopen($path.'source/widget/json/widget_quick_entry_group.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_quick_entry_group.json文件');
				}
				$slotsFileStr = json_encode($quickEntryGroup,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($quickEntryTwoState)) {
				$myfile = @fopen($path.'source/widget/json/widget_two_state_quick_entry.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_two_state_quick_entry.json文件');
				}
				$slotsFileStr = json_encode($quickEntryTwoState,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($quickEntryThreeState)) {
				$myfile = @fopen($path.'source/widget/json/widget_three_state_quick_entry.json', "w") ;
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_three_state_quick_entry.json文件');
				}
				$slotsFileStr = json_encode($quickEntryThreeState,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($navigator)) {
				$myfile = @fopen($path.'source/widget/json/widget_navigator.json', "w") ;
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_navigator.json文件');
				}
				$slotsFileStr = json_encode($navigator,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($logo)) {
				$myfile = @fopen($path.'source/widget/json/widget_logo.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_logo.json文件');
				}
				$slotsFileStr = json_encode($logo,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($sn)) {
				$myfile = @fopen($path.'source/widget/json/widget_info.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开 widget_info.json文件');
				}
				$slotsFileStr = json_encode($sn,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}
			if (!empty($slotAttachment)) {
				$myfile = @fopen($path.'source/widget/json/widget_slot_attachment.json', "w");
				if (!$myfile) {
					return $result = array('reason'=> '无法打开widget_slot_attachment.json文件');
				}
				$slotsFileStr = json_encode($slotAttachment,JSON_UNESCAPED_UNICODE);
				fwrite($myfile, $slotsFileStr);
				fclose($myfile);
			}

			$zip=new \ZipArchive();

			$zipFile = C('LOCALHOST_PATH_ADDR') .'temp/'.$desktop['name'].'_source_'.$desktop['update_time'].'.zip';
			if (file_exists($zipFile)) {
				unlink($zipFile);
			}


			$res = $zip->open($zipFile, \ZipArchive::CREATE);

			if( $res=== TRUE){
			    	addFileToZip($path.'source','source',$zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
			    	$zip->close(); //关闭处理的zip文件
			}else{
				switch($res){
				            case \ZipArchive::ER_EXISTS:
				                $ErrMsg = "文件已经存在";
				                break;
				            case \ZipArchive::ER_INCONS:
				                $ErrMsg = "Zip归档文件不一致";
				                break;

				            case \ZipArchive::ER_MEMORY:
				                $ErrMsg = "分配内存失败";
				                break;

				            case \ZipArchive::ER_NOENT:
				                $ErrMsg = "没有这样的文件";
				                break;

				            case \ZipArchive::ER_NOZIP:
				                $ErrMsg = "不是一个zip归档";
				                break;

				            case \ZipArchive::ER_OPEN:
				                $ErrMsg = "无法打开文件";
				                break;

				            case \ZipArchive::ER_READ:
				                $ErrMsg = "读取错误";
				                break;

				            case \ZipArchive::ER_SEEK:
				                $ErrMsg = "搜索错误";
				                break;

				            default:
				                $ErrMsg = "未知原因";
				                break;

				        }
			        	if ($error) {
			        		return $result = array('reason'=> '打包出错: ' . $ErrMsg);
					// return false;
				}else{
					result( '打包出错: ' . $ErrMsg);
					// die( '打包出错: ' . $ErrMsg);
				}
			}

			//测试专用slots--start
			/*$myfile = @fopen('slots.json', "w");
			if (!$myfile) {
				return $result = array('reason'=> '无法打开 slots.json文件');
			}
			foreach ($slots['screens'] as $key => $value) {
				foreach ($value['slots'] as $k => $v) {
					if ($v['dataSource'] == 'yunos') {
						unset($slots['screens'][$key]['slots'][$k]);
					}
				}
				if (!empty($slots['screens'][$key]['slots'])) {
					$slots['screens'][$key]['slots'] = array_values($slots['screens'][$key]['slots']);
				}else{
					unset($slots['screens'][$key]['slots'][$k]);
					$slots['screens'][$key]['slots'] = array();
				}
			}

			$slotsFileStr = json_encode($slots,JSON_UNESCAPED_UNICODE);
			fwrite($myfile, $slotsFileStr);
			fclose($myfile);*/
			//测试专用slots--end


			$formData = array(
				"desktopSourceFile" => array(
			    		"extension" =>"zip",
					"md5_file" => $desktop['name']."_source_".$desktop['update_time'],
			    		"filepath" => $zipFile,
				    	"size" => filesize($zipFile)
			  	),
			  	"desktopSlotsFile" => array(
			    		"extension" =>"json",
					"md5_file" => $desktop['name']."_layout_".$desktop['layout_update_time'],
			    		"filepath" => $path.'source/slot/json/slots.json',
				    	"size" => filesize($path.'source/slot/json/slots.json')
			  	)
			);

			require_once '../Base/Ossupclass/OssBase.class.php';
			$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
			$res = $base->uploadFile($formData);
			$options = array(
				'model'=>$desktop['name'],
				'sourcePath'=>$res['desktopSourceFile']['oss'],
				'layoutPath'=>$res['desktopSlotsFile']['oss'],
				'version'=>$desktop['update_time'],
				'md5'=>md5_file($zipFile),
				'layoutVersion'=>$desktop['layout_update_time'],
				'time'=>time()
			);

			//测试专用slots--start
			if (file_exists('slots.json')) {
				unlink('slots.json');
			}
			//测试专用slots--end
			if (file_exists($zipFile)) {
				unlink($zipFile);
			}
			$DesktopVersion->addDesktopVersion($options,$error);
			//删除文件

			if (file_exists($path)) {
				removeDir($path);
			}
			if (file_exists('source')) {
				removeDir('source');
			}

			if (!empty($error)) {
				$response['error'] = $msg;
				if ($error) {
					return true;
				}else{
					result(true,$response);
				}
			}else{
				if ($error) {
					return true;
				}else{
					result();
				}
			}
			// echo $runTime = time() - $startTime;
		}
		/**
		 * 桌面管理_发布桌面版本
		 * post  /desktop/publishDesktopVersion
		 * post_data
		 * {
		 *     "id": "资源包版本id",
		 *     "type": "group/AB/ALL",
		 *     "groupId": "组ID",
		 *     "AB": "1000"
		 * } PS：当type=AB时，AB存在，当type=group时，groupId存在
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		public function publishDesktopVersion($put =null,$desktopVersion =null ,$error=false)
		{
			if ($put ===null) {
				$put = I('put.');

				if (($put['type']=='group' || $put['type']=='ALL' || $put['type']=='AB' )) {
					if ($put['type']=='group') {
						if (!empty($put['groupId'])) {
							$group = D('Group')->getValOneForId($put['groupId']);
							if (!$group) {
								if ($error) {
									return $result = array('reason'=> '组不存在，请选择正确组');
								}else{
									result('组不存在，请选择正确组');
								}
							}
						}
					}
					if ($put['type']=='AB') {

						if (!isset($put['AB'])) {

							if ($error) {
								return $result = array('reason'=> '请添加灰度值');
							}else{
								result('请添加灰度值');
							}
						}
					}
				}else{
					if ($error) {
						return $result = array('reason'=> '参数有误');
					}else{
						result('param');
					}
				}

			}

			if (!empty($put['id'])) {
				if ($desktopVersion === null) {
					$desktopVersion = D('DesktopVersion','Desktop')->getValOneForId($put['id']);
					if (!$desktopVersion) {
						if ($error) {
							return $result = array('reason'=> '版本不存在');
						}else{
							result('该桌面版本不存在');
						}
					}
					$deleteDesktopVersion = true;
				}
			}else{
				if ($error) {
					return $result = array('reason'=> '参数有误');
					// return false;
				}else{
					result('param');
				}
			}

			$options = array(
				'model'=>$desktopVersion['model'],
				'sourcePath'=>C('DOWNLOAD_DESKTOP_SOURCER_PREFIX_ADDR') . $desktopVersion['sourcePath'],
				'layoutPath'=>C('DOWNLOAD_DESKTOP_LAYOUT_PREFIX_ADDR') .$desktopVersion['layoutPath'],
				'type'=>$put['type'],
				'version'=>$desktopVersion['version'],
				'md5'=>$desktopVersion['md5'],
				'layoutVersion'=>$desktopVersion['layoutVersion'],
				'time'=>time()
			);
			if ($put['type']=='group') {
				if (empty($put['groupId'])) {
					if ($error) {
						return $result = array('reason'=> '参数有误');
						// return false;
					}else{
						result('param');
					}
				}
				$options['groupId'] = $put['groupId'];
			}elseif ($put['type']=='AB') {
				if (empty($put['AB'])) {
					if ($error) {
						return $result = array('reason'=> '请添加灰度值');
						// return false;
					}else{
						result('请添加灰度值');
					}
				}
				$options['gray'] = $put['AB'];
			}

			if ($deleteDesktopVersion) {
				$res = D('DesktopVersionPublish','Desktop')->getValOneForModelForType($options['model'],$options['type']);

				if ($res) {

					//去除桌面旧接口发布 start

					D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($res['id']);
					unset($res['id']);
					$res['reason'] = '下架';
					D('DesktopVersionPublishHistory','Desktop')->addDesktopVersionPublishHistory($res);

					//去除桌面旧接口发布 end

					//先删除发布版本

					//桌面旧接口发布 start

					/*$json = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/del?item=desktop3.0&id='.$res['id']);
					if ($json) {
						$jsonArr = json_decode($json,true);
						if ($jsonArr['result'] !='ok') {
							if (strstr($jsonArr['reason'],'not such id')!=true) {
								result($jsonArr['reason']);
							}
						}
						D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($res['id']);
						unset($res['id']);
						$res['reason'] = '下架';
						D('DesktopVersionPublishHistory','Desktop')->addDesktopVersionPublishHistory($res);

					}else{
						result("发布路径出错");
					}*/
					//桌面旧接口发布 end
				}
			}
			$desktopVersionPublishId = D('DesktopVersionPublish','Desktop')->addDesktopVersionPublish($options,$error);
			/*{"id":"1", "item":"desktop3.0", "target":{"model":"ALL", "snList":["222","333"], "type":"group/AB/ALL", "AB":"100"}, "content":{"path":"http://xxxx"}}*/
			if (!empty($desktopVersionPublishId['reason'])) {
				return $desktopVersionPublishId;
			}

			//去除桌面旧接口发布 start
			if ($error) {
				return true;
			}else{
				result();
			}
			//去除桌面旧接口发布 end

			//桌面旧接口发布 start
			/*$data = array(
				'id'=>$desktopVersionPublishId,
				'item'=>'desktop3.0',
				'target'=>array(
					'model'=>$desktopVersion['model'],
					'type'=>$put['type']
				),
				'content'=>array(
					'sourcePath'=>DOWNLOAD_PREFIX_ADDR.$desktopVersion['sourcePath'],
					'layoutPath'=>DOWNLOAD_PREFIX_ADDR.$desktopVersion['layoutPath'],
					'version'=>$desktopVersion['version'],
					'layoutVersion'=>$desktopVersion['layoutVersion'],
				)
			);
			if ($put['type']=='group') {
				$groupMembers = D('GroupMembers')->getValArrForGroupId($options['groupId']);
				if ($groupMembers) {
					foreach ($groupMembers as $key => $value) {
						$data['target']['snList'][] = $value['sn'];
					}
				}else{
					$data['target']['snList'][] = array();
				}
			}elseif ($put['type']=='AB') {
				$data['target']['AB'] = $put['AB'];
			}
			$data = json_encode($data,JSON_UNESCAPED_UNICODE);

			$json = postUrl(DESKTOP3_ACCESS_ADDR.'access/publish/add',$data);


			if ($json) {
				$jsonArr = json_decode($json,true);
				if ($jsonArr['result'] !='ok') {
					D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($desktopVersionPublishId);
					if ($error) {
						return $result = array('reason'=>  $jsonArr['reason']);
						// return false;
					}else{
						result($jsonArr['reason']);
					}
				}else{
					if ($error) {
						return true;
					}else{
						result();
					}
				}
			}else{

				D('DesktopVersionPublish','Desktop')->deleteDesktopVersionPublish($desktopVersionPublishId);
				if ($error) {
					return $result = array('reason'=> '请求发布失败');
				}else{
					result('请求发布失败');
				}

			}*/
			//桌面旧接口发布 end
		}
		/**
		 *  桌面管理_生成桌面版本列表
		 *  get /desktop/desktopVersionLists?desktopName=test1
		 * @return {"extra":[{"id":"1","model":"test1","version":"1444375221","path":"http:\/\/style-lee.oss-cn-shenzhen.aliyuncs.com\/json\/test1layout_1444375221.json","time":"2015-10-10 09:40:53"}],"result":"ok"}
		 *
		 */
		public function desktopVersionLists(){
			$get = I('get.');
			if ($get['desktopName']) {
				$res['extra'] = D('DesktopVersion','Desktop')->getValArrForModel($get['desktopName']);
			}
			if (empty($res['extra'])) {
				$res['extra'] =array();
			}
			result(true,$res);
		}

		/**
		 *  桌面管理_生成桌面资源包列表
		 *  get /desktop/desktopSourceLists?desktopName=test1
		 * @return {"extra":[{"id":"1","model":"test1","version":"1444375221","path":"http:\/\/style-lee.oss-cn-shenzhen.aliyuncs.com\/json\/test1layout_1444375221.json","time":"2015-10-10 09:40:53"}],"result":"ok"}
		 *
		 */
		/*public function desktopSourceLists(){
			$get = I('get.');
			if ($get['desktopName']) {
				$res['extra'] = D('DesktopSource','Desktop')->field("`id`,model,version,concat('".DOWNLOAD_PREFIX_ADDR."',`path`) as path,FROM_UNIXTIME(time,'%Y-%m-%d %H:%i:%s') as time")->where("`model`='%s'",array($get['desktopName']))->select();
			}
			if (empty($res['extra'])) {
				$res['extra'] =array();
			}
			result(true,$res);
		}*/
		/**
		 * 桌面管理_发布桌面资源包文件
		 * post  /desktop/publishDesktopSource
		 * post_data {"id":"资源包版本id","type":"group/AB/ALL","groupId":"组ID","AB":"1000"} PS：当type=AB时，AB存在，当type=group时，groupId存在
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		/*public function publishDesktopSource($put =null,$desktopSource =null,$error=false)
		{
			if ($put ===null) {
				$put = I('put.');
			}
			if (!empty($put['id'])) {
				if ($desktopSource ===null) {
					$desktopSource = D('DesktopSource','Desktop')->field("`id`,model,version,path")->where("`id`=%d",array($put['id']))->find();
					if (!$desktopSource) {
						if ($error) {
							return $result = array('reason'=> '资源包不存在');
						}else{
							result('该桌面资源包不存在');
						}
					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=> '参数有误');
					// return false;
				}else{
					result('param');
				}
			}
			$options = array(
				'model'=>$desktopSource['model'],
				'path'=>$desktopSource['path'],
				'type'=>$put['type'],
				'version'=>$desktopSource['version'],
				'time'=>time()
			);
			if ($put['type']=='group') {
				if (empty($put['groupId'])) {
					if ($error) {
						return $result = array('reason'=> '参数有误');
						// return false;
					}else{
						result('param');
					}
				}
				$options['groupId'] = $put['groupId'];
			}elseif ($put['type']=='AB') {
				if (empty($put['AB'])) {
					if ($error) {
						return $result = array('reason'=> '参数有误');
						// return false;
					}else{
						result('param');
					}
				}
				$options['gray'] = $put['AB'];
			}
			$desktopSourcePublishId = D('DesktopSourcePublish','Desktop')->addDesktopSourcePublish($options,$error);
			if (!empty($desktopSourcePublishId['reason'])) {
				return $desktopSourcePublishId;
				// return false;
			}
			$data = array(
				'id'=>$desktopSourcePublishId,
				'item'=>'source3.0',
				'target'=>array(
					'model'=>$desktopSource['model'],
					'type'=>$put['type']
				),
				'content'=>array(
					'path'=>DOWNLOAD_PREFIX_ADDR.$desktopSource['path'],
					'version'=>$desktopSource['version'],
				)
			);
			if ($put['type']=='group') {
				$groupMembers = D('GroupMembers')->where("`group_id`=%d",array($options['groupId']))->select();
				if ($groupMembers) {
					foreach ($groupMembers as $key => $value) {
						$data['target']['snList'][] = $value['sn'];
					}
				}else{
					$data['target']['snList'][] = array();
				}

			}elseif ($put['type']=='AB') {
				$data['target']['AB'] = $put['AB'];
			}
			$data = json_encode($data,JSON_UNESCAPED_UNICODE);
			$json = postUrl(DESKTOP_SOURCE_ACCESS_ADDR.'access/publish/add',$data);

			if ($json) {
				$res = json_decode($json,true);
				if ($res['result'] !='ok') {
					D('DesktopSourcePublish','Desktop')->deleteDesktopSourcePublish($desktopSourcePublishId);
					if ($error) {
						return $result = array('reason'=>  $res['reason']);
						// return false;
					}else{
						result($res['reason']);
					}

				}else{
					if ($error) {
						return true;
					}else{
						result();
					}
				}
			}else{
				D('DesktopSourcePublish','Desktop')->deleteDesktopSourcePublish($desktopSourcePublishId);
				if ($error) {
					return $result = array('reason'=> '请求发布失败');

					// return false;
				}else{
					result('请求发布失败');
				}

			}
		}*/
		/**
		 * 桌面管理_发布桌面资源包版本列表
		 * get /desktop/publishDesktopSource
		 * get /desktop/publishDesktopSource?id=1
		 *
		 */
		/*public function publishDesktopSourceLists()
		{
			$get = I('get.');
			if (!empty($get['id'])) {
				$res = getUrl(DESKTOP_SOURCE_ACCESS_ADDR.'access/publish/list?item=source3.0&id='.$get['id']);
				if ($res) {
					$res = json_decode($res,true);
					if (!empty($res['extra'])) {
						if (empty($res['extra']['snList'])) {
							$res['extra']['snList'] = array();
						}
					}else{
						$res['extra']= array();
					}
					$res = json_encode($res,JSON_UNESCAPED_UNICODE);
				}else{
					result('发布地址出错');
				}
			}else{
				$res = getUrl(DESKTOP_SOURCE_ACCESS_ADDR.'access/publish/list?item=source3.0');
				$res = json_decode($res,true);
				if (empty($res['extra'])) {
					$res['extra']= array();
				}
				$res = json_encode($res,JSON_UNESCAPED_UNICODE);
			}
			if ($res) {
				echo $res;
			}else{
				result('发布地址出错');
			}
		}*/

		/**
		 * 桌面管理_删除桌面资源包发布版本
		 * get /desktop/deletepublishDesktopSource?id=1
		 * @return {"result":"ok/fail","reason":"xxx"}
		 */
		/*public function deletepublishDesktopSource()
		{
			$get = I('get.');
			if (!empty($get['id'])) {

				$json = getUrl(DESKTOP_SOURCE_ACCESS_ADDR.'access/publish/del?item=source3.0&id='.$get['id']);
				if ($json) {
					$json = json_decode($json,true);
					if ($json['result'] != 'ok') {
						if (strstr($res['reason'],'not such id')!=true) {
							result($res['reason']);
						}
					}else{
						$desktopSourcePublish = D('DesktopSourcePublish','Desktop')->find($get['id']);
						if ($desktopSourcePublish) {
							D('DesktopSourcePublish','Desktop')->deleteDesktopSourcePublish($get['id']);
							unset($desktopSourcePublish['id']);
							$desktopSourcePublish['reason'] = '下架';
							D('DesktopSourcePublishHistory','Desktop')->addDesktopSourcePublishHistory($desktopSourcePublish);
						}
						result();
					}
				}else{
					result('发布地址出错');
				}
			}else{
				result('param');
			}
		}*/
		/**
		 * 桌面管理_上传桌面LOGO图片
		 * post /desktop/addDesktopLogoFile
		 *
		 * {"desktopId":"桌面ID","x":"x坐标","y":"y坐标","style":"风格","isShowIndicator":"是否显示光标框","intervalTime":"显示时间间隔","logoLists":["download","download"]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function addDesktopLogoFile()
		{
			$put = I('put.');
			//检查是否有桌面LOGO
			if ( !isset($put['desktopId']) || !isset($put['x']) || !isset($put['y']) || empty($put['style'])  ||  !($put['isShowIndicator']  === 'false' || $put['isShowIndicator']  === 'true') || !isset($put['intervalTime'])) {
				result('param');
			}
			$desktop= D('Desktop','Desktop')->getValForId($put['desktopId']);
			if (!$desktop) {
				result('桌面不存在');
			}
			if (D('DesktopLogo','Desktop')->getValOneForDesktopId($put['desktopId'])) {
				result('桌面Logo已存在');
			}
			//添加LOGO
			$desktopLogoId = D('DesktopLogo','Desktop')->addDesktopLogo($put['desktopId'],$put['x'],$put['y'],$put['style'],$put['isShowIndicator'],$put['intervalTime']);

			if (!empty($put['logoLists'])) {
				/*require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);

				$res = $base->uploadFile($formData);
				$config = array(
					'rootPath'      =>  C('LOCALHOST_PATH_ADDR') .'pic/',
					'savePath'   =>    '',
					'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
					'saveName'   => '',
					'autoSub' => false
				);
				$upload = new \Think\Upload($config);// 实例化上传类
				$res = $form->getFileNameForMd5();
				if ($res) {
					foreach ($res as $key => $value) {

						if (!file_exists( C('LOCALHOST_PATH_ADDR') .'pic/'. $value['name'])) {
							$info   =   $upload->uploadOne($res[$key]);
							if(!$info) {// 上传错误提示错误信息
								result($upload->getError());
							}
						}
						$arr[] = array(
							'path' => 'pic/'. $value['name'],
							'desktop_logo_id' =>$desktopLogoId
						);
					}
				}*/
				foreach ($put['logoLists'] as $value) {
					$arr[] = array(
						'path' => $value,
						'desktop_logo_id' =>$desktopLogoId
					);
				}
				D('DesktopLogoInfo','Desktop')->addDesktopLogoInfo($arr);
			}
			result();

		}
		/**
		 * 桌面管理_删除桌面LOGO图片
		 * post /desktop/deleteDesktopLogoFile?desktopId=1
		 *
		 *
		 *  @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteDesktopLogoFile()
		{
			$id = I('get.desktopId');
			if (!empty($id)) {
				$res = D('DesktopLogo','Desktop')->getValOneForDesktopId($id);
				if ($res) {
					//删除桌面Logo信息
					D('DesktopLogoInfo','Desktop')->deleteDesktopLogoInfo($res['id']);
					//删除桌面Logo
					D('DesktopLogo','Desktop')->deleteDesktopLogo($id);
					result();
				}else{
					result('桌面不存在LOGO');
				}
			}else{
				result('param');
			}

		}
		/**
		 * 桌面管理_桌面LOGO列表
		 * get /desktop/desktopLogoFileLists?desktopId=桌面ID
		 *
		 * @return {"extra":{"id":"桌面LOGO ID","desktopId":"桌面ID","x":"0","y":"0","style":"SIMPLE","isShowIndicator":"false","intervalTime":"0","logoLists":[{"id":"69","path":"http:\/\/192.168.1.199:180\/..\/download\/pic\/3df417007c7f04a1dc86e76b21fc55e4.jpg"}]},"result":"ok"}
		 *
		 *
		 */
		public function desktopLogoFileLists()
		{
			$id = I('get.desktopId');
			if (!empty($id)) {
				$res['extra'] = D('DesktopLogo','Desktop')->getValOneForDesktopId($id);
				if ($res['extra']) {
					$res['extra'] = D('DesktopLogo','Desktop')->logoLists($id);
				}else{
					$res['extra'] = array();
				}
			}else{
				$res['extra'] = array();
			}
			result(true,$res);
		}
		/**
		 * 桌面管理_修改桌面LOGO图片
		 * post /desktop/modifyDesktopLogoFile
		 * {"desktopId":"桌面ID","x":"x坐标","y":"y坐标","style":"风格","isShowIndicator":"是否显示光标框","intervalTime":"显示时间间隔","logoLists":["download","download"]}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function modifyDesktopLogoFile()
		{
			$put = I('put.');
			//检查是否有桌面LOGO
			if (!isset($put['desktopId']) || !isset($put['x']) || !isset($put['y']) || empty($put['style'])  ||  !($put['isShowIndicator']  === 'false' || $put['isShowIndicator']  === 'true') || !isset($put['intervalTime'])) {
				result('param');
			}
			$desktop= D('Desktop','Desktop')->getValForId($put['desktopId']);
			if (!$desktop) {
				result('桌面不存在');
			}
			/*if ($_FILES) {
				foreach ($_FILES as $key => $value) {
					if ($key!='logoFile') {
						unset($_FILES[$key]);
					}
				}
			}*/
			//添加LOGO
			$desktopLogoId = D('DesktopLogo','Desktop')->modifyDesktopLogo($put['desktopId'],$put['x'],$put['y'],$put['style'],$put['isShowIndicator'],$put['intervalTime']);
			/*require_once '../Base/function/Form.class.php';
			$form = new \form();
			$formData = $form->getFormFile();*/

			if (!empty($put['logoLists'])) {
				/*require_once '../Base/Ossupclass/OssBase.class.php';
				$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
				$res = $base->uploadFile($formData);
				$config = array(
					'rootPath'      =>  C('LOCALHOST_PATH_ADDR') .'pic/',
					'savePath'   =>    '',
					'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
					'saveName'   => '',
					'autoSub' => false
				);
				$upload = new \Think\Upload($config);// 实例化上传类
				$res = $form->getFileNameForMd5();
				if ($res) {
					foreach ($res as $key => $value) {
						if (!file_exists( C('LOCALHOST_PATH_ADDR') .'pic/'. $value['name'])) {
							$info   =   $upload->uploadOne($res[$key]);
							if(!$info) {// 上传错误提示错误信息
								result($upload->getError());
							}
						}
						$arr[]= array(
							'path'=>'pic/'. $value['name'],
							'desktop_logo_id'=>$desktopLogoId
						);
					}
				}*/
				foreach ($put['logoLists'] as $value) {
					$arr[] = array(
						'path' => $value,
						'desktop_logo_id' =>$desktopLogoId
					);
				}

				if (D('DesktopLogoInfo','Desktop')->DesktopLogoLists($desktopLogoId)) {
					D('DesktopLogoInfo','Desktop')->modifyDesktopLogoInfo($desktopLogoId,$arr);
				}else{
					D('DesktopLogoInfo','Desktop')->addDesktopLogoInfo($arr);
				}
			}else{
				//删除桌面Logo信息
				// D('DesktopLogoInfo','Desktop')->deleteDesktopLogoInfo($desktopLogoId);
			}
			result();
		}
		/**
		 * 桌面管理_添加桌面MAC授权
		 * post /desktop/addDesktopMacBlack
		 * {
		 * 	"mac":"ab:as:da:da:sa:sa",
		 *  	"desc":"备注"
		 *  }
		 * 或
		 * 导入 表单上传  name='mac' type=file
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function addDesktopMacBlack()
		{
			$put = I('put.');
			D('MacBlackList','Desktop')->addDesktopMacBlack($put);
			result();
		}
		/**
		 * 桌面管理_修改桌面MAC授权
		 * post /desktop/modifyDesktopMacBlack
		 * {"id":"1","mac":"ab:as:da:da:sa:sa","desc":"备注"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function modifyDesktopMacBlack()
		{
			$put = I('put.');
			D('MacBlackList','Desktop')->modifyDesktopMacBlack($put);
			result();
		}
		/**
		 * 桌面管理_删除桌面MAC授权
		 * post /desktop/deleteDesktopMacBlack
		 * ["1","2"]
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteDesktopMacBlack()
		{
			$put = I('put.');
			D('MacBlackList','Desktop')->deleteDesktopMacBlack($put);
			result();
		}
		/**
		 * 桌面管理_桌面MAC授权列表
		 * get /desktop/desktopMacBlackLists?name=x&page=x&pageSize=x
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function desktopMacBlackLists()
		{
			$get = I('get.');
			$res = D('MacBlackList','Desktop')->desktopMacBlackLists($get);
			result(true,$res);
		}
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		/**
		 * 桌面管理_添加关注APP
		 * post /desktop/addAttentionApp
		 * {
		 * 	"appName":"应用名",
		 *  	"pkgName":"包名"
		 *  }
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function addAttentionApp()
		{
			$put = I('put.');
			D('AttentionApp','Desktop')->addAttentionApp($put);
			result();
		}
		/**
		 * 桌面管理_修改关注APP
		 * post /desktop/modifyAttentionApp
		 * {"id":"1","appName":"应用名","pkgName":"包名"}
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function modifyAttentionApp()
		{
			$put = I('put.');
			D('AttentionApp','Desktop')->modifyAttentionApp($put);
			result();
		}
		/**
		 * 桌面管理_删除关注APP
		 * post /desktop/deleteAttentionApp
		 * ["1","2"]
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function deleteAttentionApp()
		{
			$put = I('put.');
			D('AttentionApp','Desktop')->deleteAttentionApp($put);
			result();
		}
		/**
		 * 桌面管理_关注APP列表
		 * get /desktop/attentionAppLists?name=x&page=x&pageSize=x
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function attentionAppLists()
		{
			$get = I('get.');
			$res = D('AttentionApp','Desktop')->attentionAppLists($get);
			result(true,$res);
		}
		/**
		 * 桌面管理_桌面关注APP占量总数列表
		 * get /desktop/desktopAttentionAppLists
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function desktopAttentionAppLists()
		{

			$res = D('AttentionApp','Desktop')->desktopAttentionAppLists();
			result(true,$res);
		}
		/**
		 * 桌面管理_桌面关注APP是否在桌面列表
		 * get /desktop/isDesktopAttentionAppLists
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function isDesktopAttentionAppLists()
		{
			$get = I('get.');
			$res = D('AttentionApp','Desktop')->isDesktopAttentionAppLists($get);
			result(true,$res);
		}
		/**
		 * 桌面管理_桌面批量复制布局功能
		 * put /desktop/isDesktopAttentionAppLists
		 *
		 * @return {"result":"ok/fail","reason":"xxx"}
		 *
		 */
		public function copyLayoutToDesktop()
		{
			$put = I('put.');
			D('Desktop','Desktop')->copyLayoutToDesktop($put);
			result();
		}
		/**
		 * [addPushMessage 添加桌面消息推送]
		 *  post /desktop/addPushMessage

		 *  {
		 *  "model":"型号",
		 *  "vendorID":"vendorID",
		 *  "type":"ALL/group", //公开/内测
		 *  "groupId":"1",   内测组ID
		 *  "pub_range":"all/jbk/unjbk",   all -- 全部 jbk -- 越狱  unjbk -- 非越狱
		 *  "whiteList":["123213","23423"],
		 *   "blackList":["23423","234234"],
		 *  "msg":"信息内容",
		 *  "playTime":"播放时长秒",
		 *  "playCount":"播放次数",
		 *  }
		 * @param string $value [description]
		 */
		public function addPushMessage()
		{
			$put = I('put.');
			D('PushMessage','Desktop')->addPushMessage($put);
			result();
		}
		/**
		 * [deletePushMessage 删除桌面消息推送]
		 * get /desktop/deletePushMessage?id=x
		 * @param  string $value [description]
		 * @return [type]        [description]
		 */
		public function deletePushMessage()
		{
			$get = I('get.');
			D('PushMessage','Desktop')->deletePushMessage($get);
			result();
		}
		/**
		 * [pushMessageLists 桌面消息推送列表]
		 * get /desktop/pushMessageLists?name=x&page=x&pageSize=x
		 * @param  string $value [description]
		 * @return [type]        [description]
		 */
		public function pushMessageLists()
		{
			$get = I('get.');
			$res = D('PushMessage','Desktop')->pushMessageLists($get);
			result(true,$res);
		}
		/**
		 * [addSlotTagImg 添加坑位标签图片]
		 * post /desktop/addSlotTagImg
		 * type=file  name=tagImg
		 * type=text name =name
		 */
		public function addSlotTagImg()
		{
			$post = I('post.');
			D('SlotTagImg','Desktop')->addSlotTagImg($post);
			result();
		}
		/**
		 * [deleteSlotTagImg 删除坑位标签图片]
		 * post /desktop/deleteSlotTagImg
		 * ["1","2"]
		 * @return [type] [description]
		 */
		public function deleteSlotTagImg()
		{
			$put = I("put.");
			D('SlotTagImg','Desktop')->deleteSlotTagImg($put);
			result();
		}
		/**
		 * [slotTagImgLists 坑位标签图片列表]
		 * get /desktop/slotTagImgLists?name=x&page=x&pageSize=x
		 *
		 * @return [type] [description]
		 */
		public function slotTagImgLists()
		{
			$get = I("get.");
			$res = D('SlotTagImg','Desktop')->slotTagImgLists($get);
			result(true,$res);
		}
		//------------------------------------------------------------------------------------
		/**
		 * [addPreloadedApp 桌面系统-预装应用-添加预装应用]
		 * post /desktop/addPreloadedApp
		 * {
		 * 	"desktopID":"桌面ID",
		 * 	"appList":[
		 * 			{
		 * 				"name":" 电视猫",
		 * 				"packname":"com.moretv",
		 * 				"url":"http://xxx"
		 * 			}
		 * 	]
		 * }
		 */
		public function addPreloadedApp()
		{
			$put = I('put.');
			D('PreloadedApp','Desktop')->addPreloadedApp($put);
			result();
		}
		/**
		 * [modifyPerloadedApp 桌面系统-预装应用-修改预装应用]
		 * post /desktop/modifyPreloadedApp
		 * {
		 * 	"id":"预装应用ID",
		 * 	"desktopID":"桌面ID",
		 * 	"appList":[
		 * 			{
		 * 				"name":" 电视猫",
		 * 				"packname":"com.moretv",
		 * 				"url":"http://xxx"
		 * 			}
		 * 	]
		 * }
		 */
		public function modifyPreloadedApp()
		{
			$put = I('put.');
			D('PreloadedApp','Desktop')->modifyPreloadedApp($put);
			result();
		}
		/**
		 * [deletePreloadedApp 桌面系统-预装应用-删除预装应用]
		 * post /desktop/deletePreloadedApp?id=x
		 *
		 * @return [type] [description]
		 */
		public function deletePreloadedApp()
		{
			$get = I("get.");
			D('PreloadedApp','Desktop')->deletePreloadedApp($get);
			result();
		}
		/**
		 * [preloadedAppLists 桌面系统-预装应用-预装应用列表]
		 * get /desktop/preloadedAppLists?name=x&page=x&pageSize=x
		 *
		 * @return [type] [description]
		 */
		public function preloadedAppLists()
		{
			$get = I("get.");
			$res = D('PreloadedApp','Desktop')->preloadedAppLists($get);
			result(true,$res);
		}

	}
 ?>