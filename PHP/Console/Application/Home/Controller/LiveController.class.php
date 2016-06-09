<?php
namespace Home\Controller;
use Think\Controller;
class LiveController extends Controller {
 	public function __construct()
    	{
	        	parent::__construct();
	       	isLogin();
    	}
    	/**
     	* 首页安全页
     	*
     	*/
    	public function index(){
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
       	* 直播系统_添加直播列表发布
       	* /Live/addListPublish
       	* {
       	* "model":"all",    型号 有全型号概念all     填写
       	* "vendorID":"vendorID", 填写
       	* "name":"名称"   填写
       	* "type":"ALL/group/AB",    公开/内测/灰度   选项
       	* "AB":"100",   当type=AB   填写
       	* "groupId":"100",   当type=group   选项
       	* }
         	* @return [type] [description]
         	*/
    	public function addListPublish()
    	{
    		$put = I('put.');
    		D('LiveListPublish','Live')->addListPublish($put);
    		result();
    	}
    	/**
       	* 直播系统_修改直播列表发布
       	* /Live/modifyListPublish
         	* @return [type] [description]
         	*/
    	public function modifyListPublish()
    	{
    		$put = I('put.');
    		D('LiveListPublish','Live')->modifyListPublish($put);
    		result();
    	}
    	/**
       	* 直播系统_删除直播列表发布
       	* /Live/deleteListPublish
       	* ["1"]
         	* @return [type] [description]
         	*/
    	public function deleteListPublish()
    	{
    		$put = I('put.');
    		D('LiveListPublish','Live')->deleteListPublish($put);
    		result();
    	}
    	/**
       	* 直播系统_直播列表发布列表
       	* /Live/listPublishLists?page=x&pageSize=x&name=x
         	* @return [type] [description]
         	*/
    	public function listPublishLists()
    	{
    		$get = I('get.');
    		$res = D('LiveListPublish','Live')->listPublishLists($get);
    		result(true,$res);
    	}
    	/**
       	* 直播系统_添加直播广告组
       	* /Live/addLiveAdGroup
       	* {
       	* "name":"名称",
       	* "desc":"备注"
       	* }
         	* @return [type] [description]
         	*/
    	public function addLiveAdGroup()
    	{
    		$put = I('put.');
    		D('LiveAdGroup','Live')->addLiveAdGroup($put);
    		result();
    	}
    	/**
       	* 直播系统_修改直播广告组
       	* /Live/modifyLiveAdGroup
         	* @return [type] [description]
         	*/
    	public function modifyLiveAdGroup()
    	{
    		$put = I('put.');
    		D('LiveAdGroup','Live')->modifyLiveAdGroup($put);
    		result();
    	}
    	/**
       	* 直播系统_删除直播广告组
       	* /Live/deleteLiveAdGroup?id=x
         	* @return [type] [description]
         	*/
    	public function deleteLiveAdGroup()
    	{
    		$get = I('get.');
    		D('LiveAdGroup','Live')->deleteLiveAdGroup($get);
    		result();
    	}
    	/**
       	* 直播系统_直播广告组列表
       	* /Live/liveAdGroupLists?page=x&pageSize=x&name=x
         	* @return [type] [description]
         	*/
    	public function liveAdGroupLists()
    	{
    		$get = I('get.');
    		$res = D('LiveAdGroup','Live')->liveAdGroupLists($get);
    		result(true,$res);
    	}
    	//------------------------------------------------------------------------
    	/**
       	* 直播系统_添加直播广告
       	* /Live/addLiveAd
       	* {
       	* 	"groupId":"1",
       	* 	"name":"广告名称",
       	*  	"interval":"展示间隔",
       	*  	"duration":"展示持续时间",
       	* 	"maxShowTimes":"最大展示次数",
       	*  	"startTime":"开始时间",
       	*  	"endTime":"结束时间",
       	* 	"x":"x坐标",
       	*  	"y":"y坐标",
       	*  	"width":"广告宽",
       	* 	"height":"广告高",
       	*  	"url":"广告链接",
       	*  	"channelList":[   //频道列表
       	*  		{
       	*  			"name":"购物频道",//频道名称
       	*  			"id":"8a29913352f3fda10152f3ffea040007"//频道ID
       	*  		}
       	*  	]
       	* }
         	* @return [type] [description]
         	*/
    	public function addLiveAd()
    	{
    		$put = I('put.');
    		D('LiveAd','Live')->addLiveAd($put);
    		result();
    	}
    	/**
       	* 直播系统_修改直播广告
       	* /Live/modifyLiveAd
       	* {
       	* 	"id":"直播广告ID",
       	* 	"name":"广告名称",
       	*  	"interval":"展示间隔",
       	*  	"duration":"展示持续时间",
       	* 	"maxShowTimes":"最大展示次数",
       	*  	"startTime":"开始时间",
       	*  	"endTime":"结束时间",
       	* 	"x":"x坐标",
       	*  	"y":"y坐标",
       	*  	"width":"广告宽",
       	* 	"height":"广告高",
       	*  	"url":"广告链接",
       	*  	"channelList":[   //频道列表
       	*  		{
       	*  			"name":"购物频道",//频道名称
       	*  			"id":"8a29913352f3fda10152f3ffea040007"//频道ID
       	*  		}
       	*  	]
       	* }
         	* @return [type] [description]
         	*/
    	public function modifyLiveAd()
    	{
    		$put = I('put.');
    		D('LiveAd','Live')->modifyLiveAd($put);
    		result();
    	}
    	/**
       	* 直播系统_删除直播广告组
       	* /Live/deleteLiveAd
       	* ["id1","id2"]
         	* @return [type] [description]
         	*/
    	public function deleteLiveAd()
    	{
    		$put = I('put.');
    		D('LiveAd','Live')->deleteLiveAd($put);
    		result();
    	}
    	/**
       	* 直播系统_直播广告组列表
       	* /Live/liveAdLists?page=x&pageSize=x&groupId=组ID
       	* /Live/liveAdLists?page=x&pageSize=x&groupId=组ID&name=x
         	* @return [type] [description]
         	*/
    	public function liveAdLists()
    	{
    		$get = I('get.');
    		$res = D('LiveAd','Live')->liveAdLists($get);
    		result(true,$res);
    	}
    	/**
       	* 直播系统_发布直播广告
       	* /Live/publishLiveAd
       	* {
       	* 	"adGroupId":"直播广告组ID",
       	* 	"model":"型号",
       	*  	"vendorID":"vendorID",
       	*  	"type":"ALL/AB/group",
       	* 	"groupId":"内测组ID",
       	*  	"AB":"灰度值"
       	*
       	* }
         	* @return [type] [description]
         	*/
    	public function publishLiveAd()
    	{
    		$put = I('put.');
    		D('PublishLiveAd','Live')->publishLiveAd($put);
    		result();
    	}
    	/**
       	* 直播系统_修改发布直播广告
       	* /Live/modifyPublishLiveAd
       	* {
       	* 	"id":"发布广告ID"
       	* 	"adId":"直播广告ID",
       	* 	"model":"型号",
       	*  	"vendorID":"vendorID",
       	*  	"type":"ALL/AB/group",
       	* 	"groupId":"内测组ID",
       	*  	"AB":"灰度值"
       	*
       	* }
         	* @return [type] [description]
         	*/
    	public function modifyPublishLiveAd()
    	{
    		$put = I('put.');
    		D('PublishLiveAd','Live')->modifyPublishLiveAd($put);
    		result();
    	}
    	/**
       	* 直播系统_删除发布直播广告
       	* /Live/deletePublishLiveAd
       	* ["id1","id2"]
         	* @return [type] [description]
         	*/
    	public function deletePublishLiveAd()
    	{
    		$put = I('put.');
    		D('PublishLiveAd','Live')->deletePublishLiveAd($put);
    		result();
    	}
    	/**
       	* 直播系统_发布直播广告列表
       	* /Live/publishLiveAdLists?page=x&pageSize=x
       	* /Live/publishLiveAdLists?page=x&pageSize=x&name=x
         	* @return [type] [description]
         	*/
    	public function publishLiveAdLists()
    	{
    		$get = I('get.');
    		$res = D('PublishLiveAd','Live')->publishLiveAdLists($get);
    		result(true,$res);
    	}
    	/**
       	* 直播系统_同步频道名称列表
       	* /Live/syncChannelLists
       	* /Live/syncChannelLists
         	* @return [type] [description]
         	*/
    	public function syncChannelLists()
    	{
    		//获取本地分类
    		$channelType = D('ChannelType','Live');
    		$channelList = D('ChannelList','Live');
    		$channelTypeArr = $channelType->getAll();

    		$typeId = '8a2991334e1e054c014e1f1b722d4dcc';

    		//获取线上分类
    		$typeUrl = C('LIVE_LIST_TYPE_SYNC_PREFIX') . $typeId;

		$syncTypeJson = getUrl($typeUrl);
		$syncTypeArr = json_decode($syncTypeJson,true);
		//能否解析分类JSON
		if ( !isset($syncTypeArr['typeLists']) ) {
			trace('解析分类列表失败',date('Y-m-d H:i:s',time()));
			// result('解析分类列表失败');
		}
		if (!empty($syncTypeArr['typeLists'])) {
			//本地不存在就添加，本地存在并版本更新就更新
			foreach ($syncTypeArr['typeLists'] as $value) {
				$isset = false;
				if (!empty($channelTypeArr)) {
					foreach ($channelTypeArr as  $v) {
						if ($value['id'] == $v['typeId']) {
							$isset = true;
							//查看频道列表
							$contentSimpUrl = C('LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX').$typeId."&aes=false&typeId=".$value['id'];
							$syncContentSimpJson = getUrl($contentSimpUrl);
							$syncChannelArr = json_decode($syncContentSimpJson,true);
							//能否解析分类JSON
							if ( !isset($syncChannelArr['channelLists']) ) {
								trace( '解析分类频道列表失败',date('Y-m-d H:i:s',time()) );
								continue;
								// result('解析分类列表失败');
							}
							$channelListArr = $channelList->getValForTypeId($value['id']);
							foreach ($syncChannelArr['channelLists'] as  $item) {
								$isChannel = false;
								if (!empty($channelListArr)) {
									foreach ($channelListArr as  $row) {
										if ($item['id'] == $row['channelId']) {
											$isChannel = true;
										}
									}
								}

								if (!$isChannel) {
									$addOptions = array(
										'type_id'=>$value['id'],
										'channel_id'=>$item['id'],
										'channel_name'=>$item['name']
									);
									//添加频道
									$channelList->addChannelList($addOptions);
								}
							}
							if (!empty($channelListArr)) {
								//本地存在外网不存在 删除
								foreach ($channelListArr as  $item) {
									$isChannel = false;
									foreach ($syncChannelArr['channelLists'] as $row) {
										if ($row['id'] == $item['typeId']) {
											$isChannel = true;
										}
									}
									if (!$isset) {
										$channelList->deleteChannelForID($item['id']);
									}

								}
							}
						}
					}
				}

				if (!$isset) {
					$addOptions = array(
						'type_id'=>$value['id'],
						'type_name'=>$value['name']
					);
					//添加分类
					$channelType->addChannelType($addOptions);
				}
			}
		}

		/*if (!empty($syncTypeArr)) {
			//本地存在外网不存在 删除
			foreach ($liveListTypeArr as  $value) {
				$isset = false;
				foreach ($syncTypeArr['typeLists'] as $v) {
					if ($v['id'] == $value['typeId']) {
						$isset = true;
					}
				}
				if (!$isset) {
					$modifyContentArr[] = $row['id'];
					$liveListType->deleteTypeForID($value['id']);
				}

			}
		}*/
    	}
    	/**
       	* 直播系统_同步直播列表
       	* /Live/liveListSync1?page=x&pageSize=x
       	* /Live/liveListSync1?page=x&pageSize=x&name=x
         	* @return [type] [description]
         	*/

         	public function liveListSync1()
         	{
         		$get = I('get.');
         		$liveListName = D('LiveListName','Live');
    		$liveListType = D('LiveListType','Live');
    		$liveListNameArr = $liveListName->getLiveListNameLists($get);

    		//获取同步直播列表名称列表
    		$nameUrl = C('LIVE_LIST_NAME_SYNC_PREFIX');
    		$syncNameJson = getUrl($nameUrl);
    		$syncNameArr = json_decode($syncNameJson,true);
    		if (empty($syncNameArr)) {
    			result('列表为空');
    		}
		//外网对比本地，不存在就添加
		foreach ($syncNameArr as  $key => $value) {
			if (!empty($get['id'])) {
				if ($get['id'] != $value['id']) {
					continue;
				}
			}

			$isset = false;

			foreach ($liveListNameArr as  $v) {
				if ( $value['id'] == $v['name_id'] ) {
					$isset = true;
					break;
				}
			}

			if (!$isset) {
				$addNameOptions[] = array(
					'name_id'=>$value['id'],
					'name'=>$value['name'],
				);
			}
		}

		if (!empty($addNameOptions)) {
			$liveListName->addLiveListNameArr($addNameOptions);
		}

		//本地对比外网，不存在就删除
		/*foreach ($liveListNameArr as  $key => $value) {
			if (!empty($get['id'])) {
				if ($get['id'] != $value['id']) {
					continue;
				}
			}
			$isset = false;
			if (!empty($syncNameArr)) {
				foreach ($syncNameArr as  $v) {
					if ( $value['name_id'] == $v['id'] ) {
						$isset = true;
					}
				}

			}else{
				$deleteNameOptions[] =  $value['name_id'];
			}
			if (!$isset) {
				$deleteNameOptions[] =  $value['name_id'];
			}
		}


		if (!empty($deleteNameOptions)) {
			$deleteNameOptions = array_unique($deleteNameOptions);
			$nameIdStr = "'".implode("','", $deleteNameOptions)."'";

			//删除分类
			$liveListType->deleteLiveListNameArrForNameIDStr($nameIdStr);
			//删除列表
			$liveListName->deleteLiveListNameArrForNameIDStr($nameIdStr);
		}*/

		foreach ($syncNameArr as  $row) {

			$isModifyContent = false;

			if (!empty($get['id'])) {
				if ($get['id'] != $row['id']) {
					continue;
				}
			}
			$typeUrl = C('LIVE_LIST_TYPE_SYNC_PREFIX') .$row['id'];
			$syncTypeJson = getUrl($typeUrl);
			$syncTypeArr = json_decode($syncTypeJson,true);
			//能否解析分类JSON
			if ( !isset($syncTypeArr['typeLists']) ) {
				trace('解析分类列表失败',date('Y-m-d H:i:s',time()));
				continue;
				// result('解析分类列表失败');
			}
			$liveListTypeArr = $liveListType->getLiveListTypeLists($row);
			if (!empty($syncTypeArr['typeLists'])) {
				//本地不存在就添加，本地存在并版本更新就更新
				foreach ($syncTypeArr['typeLists'] as $value) {
					$isset = false;
					if (!empty($liveListTypeArr)) {
						foreach ($liveListTypeArr as  $v) {

							if ($value['id'] == $v['typeId']) {
								$isset = true;
								if ($value['version'] != $v['version']) {
									$isModifyContent = true;
									$modifyOptions = array(
										'type_id'=>$value['id'],
										'type_name'=>$value['name'],
										'is_recommend'=>$value['isRecommend']?'true':'false',
										'pinyin'=>$value['pinyin'],
										'version'=>$value['version'],
										'name_id'=>$row['id'],
									);

									$contentUrl = C('LIVE_LIST_CONTENT_SYNC_PREFIX').$row['id']."&typeId=".$value['id'];
	    								$syncContentJson = getUrl($contentUrl);

	    								$contentSimpUrl = C('LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX').$row['id']."&typeId=".$value['id'];

	    								$syncContentSimpJson = getUrl($contentSimpUrl);

		    							if (!empty($syncContentJson)&&!empty($syncContentSimpJson)) {
										$url=$this->getOssPathForLiveListForLiveTypeForLiveContentJson($row,$value,$syncContentJson);
										$urlSimp=$this->getOssPathForLiveListForLiveTypeForLiveContentJson($row,$value,$syncContentSimpJson,'_simp');
	    									$modifyOptions['url'] = !empty($url)?$url:'';
										$modifyOptions['url_simp'] = !empty($urlSimp)?$urlSimp:'';
	    								}
	    								//修改分类
	    								$liveListType->modifyLiveType($v['id'],$modifyOptions);
	    								break;
								}
							}
						}
					}

					if (!$isset) {
						$isModifyContent = true;
						$addOptions = array(
							'type_id'=>$value['id'],
							'type_name'=>$value['name'],
							'is_recommend'=>$value['isRecommend']?'true':'false',
							'pinyin'=>$value['pinyin'],
							'version'=>$value['version'],
							'name_id'=>$row['id'],
						);
						$contentUrl = C('LIVE_LIST_CONTENT_SYNC_PREFIX').$row['id']."&typeId=".$value['id'];
							$syncContentJson = getUrl($contentUrl);
							$contentSimpUrl = C('LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX').$row['id']."&typeId=".$value['id'];
							$syncContentSimpJson = getUrl($contentSimpUrl);

							if (!empty($syncContentJson)&&!empty($syncContentSimpJson)) {
								$url=$this->getOssPathForLiveListForLiveTypeForLiveContentJson($row,$value,$syncContentJson);
								$urlSimp=$this->getOssPathForLiveListForLiveTypeForLiveContentJson($row,$value,$syncContentSimpJson,'_simp');
								$addOptions['url'] = !empty($url)?$url:'';
								$addOptions['url_simp'] = !empty($urlSimp)?$urlSimp:'';
							}
							//添加分类
							$liveListType->addLiveListType($addOptions);
					}
				}
			}

			if (!empty($liveListTypeArr)) {
				//本地存在外网不存在 删除
				foreach ($liveListTypeArr as  $value) {
					$isset = false;
					foreach ($syncTypeArr['typeLists'] as $v) {
						if ($v['id'] == $value['typeId']) {
							$isset = true;
						}
					}
					if (!$isset) {

						$isModifyContent = true;
						$liveListType->deleteTypeForID($value['id']);

					}

				}
			}


			//更新列表   如果有变量modifyContent的存在，证明有修改
	    		if ($isModifyContent) {
	    			//列表唯一
	    			$liveListContent = D('LiveListContent','Live');
	    			$liveListContent->updateLiveListContent($row['id'],$syncTypeArr['typeLists']);
	    		}

		}

    		result();
         	}
    	/**
       	* 直播系统_同步直播列表
       	* /Live/liveListSync?page=x&pageSize=x
       	* /Live/liveListSync?page=x&pageSize=x&name=x
         	* @return [type] [description]
         	*/
         	/*public function liveListSync()
         	{

         		$get = I('get.');
         		set_time_limit(60*5);
         		$liveListName = D('LiveListName','Live');
    		$liveListType = D('LiveListType','Live');
         		if (!empty($get['id'])) {
         			$liveListNameArr = $liveListName->getLiveListNameLists($get);
    			$liveListTypeArr = $liveListType->getLiveListTypeLists($get);
     			if (!empty($liveListNameArr)) {
	    			foreach ($liveListNameArr as $key => $value) {
	    				if (!empty($get['id'])) {
						if ($value['nameId'] != $get['id']) {
							continue;
						}
					}
					$liveList[$value['nameId']] = array();
	    				if (!empty($liveListTypeArr)) {
	    					foreach ($liveListTypeArr as $k => $v) {
	    						if ($value['nameId'] == $v['nameId']) {
	    							$liveList[$value['nameId']][$v['typeId']] = $v;
	    						}
	    					}
	    				}
	    			}
	    		}
         		}else{
         			$liveListNameArr = $liveListName->getLiveListNameLists();
    			$liveListTypeArr = $liveListType->getLiveListTypeLists();
     			if (!empty($liveListNameArr)) {
	    			foreach ($liveListNameArr as $key => $value) {
					$liveList[$value['nameId']] = array();
	    				if (!empty($liveListTypeArr)) {
	    					foreach ($liveListTypeArr as $k => $v) {
	    						if ($value['nameId'] == $v['nameId']) {
	    							$liveList[$value['nameId']][$v['typeId']] = $v;
	    						}
	    					}
	    				}
	    			}
	    		}
         		}
         		//获取同步直播列表名称列表
    		$nameUrl = LIVE_LIST_NAME_SYNC_PREFIX;
    		$syncNameJson = getUrl($nameUrl);
    		$syncNameArr = json_decode($syncNameJson,true);
    		if (!empty($syncNameArr)) {
    			foreach ($syncNameArr as  $value) {
    				// 不删除存在的name_id
				$noDeleteLiveNameIdArr[] = $value['id'];
    				if (!empty($get['id'])) {
    					if ($value['id'] != $get['id']) {
    						continue;
    					}
    				}
    				$typeUrl = LIVE_LIST_TYPE_SYNC_PREFIX .$value['id'];
    				$syncTypeJson = getUrl($typeUrl);
    				$syncTypeArr = json_decode($syncTypeJson,true);
    				//能否解析分类JSON
				if ( !isset($syncTypeArr['typeLists']) ) {
					trace('解析分类列表失败',date('Y-m-d H:i:s',time()));
					continue;
					// result('解析分类列表失败');
				}
				// trace('类型解密了',date('Y-m-d H:i:s',time()));
    				if (!empty($syncTypeArr['typeLists'])) {
    					if (!empty($liveList[$value['id']])) {
    						foreach ($syncTypeArr['typeLists'] as $k => $v) {
    							if (!empty($liveList[$value['id']][$v['id']])) {
    								// 不删除存在的本地分类的 id
    								$noDeleteLiveTypeIdArr[$value['id']][] = $liveList[$value['id']][$v['id']]['id'];

    								if ($liveList[$value['id']][$v['id']]['version'] != $v['version']) {
    									$contentUrl = LIVE_LIST_CONTENT_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
		    							$syncContentJson = getUrl($contentUrl);
		    							$contentSimpUrl = LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
		    							$syncContentSimpJson = getUrl($contentSimpUrl);
		    							// trace('列表:'.$value['name'].'，分类:'.$v['name'].'版本改变',date("Y-m-d  H:i:s",time()));
    									// error_log(date("Y-m-d  H:i:s",time()).'列表:'.$value['name'].'，分类:'.$v['name'].'版本改变'."\n\r",3,'/home/apache/Runtime/Logs/Home/16_03_08.log');
		    							if (!empty($syncContentJson)&&!empty($syncContentSimpJson)) {
		    								$options = array(
											'type_id'=>$v['id'],
											'type_name'=>$v['name'],
											'is_recommend'=>$v['isRecommend']?'true':'false',
											'pinyin'=>$v['pinyin'],
											'version'=>$v['version'],
											'name_id'=>$value['id'],
											'url'=>$this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentJson),
											'url_simp'=>$this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentSimpJson,'_simp'),
										);
		    								//修改的列表数组
		    								$modifyContent[] = $value['id'];
		    								// error_log($options['url']."\n\r",3,'/home/apache/Runtime/Logs/Home/16_03_08.log');
		    								//修改分类
		    								$liveListType->modifyLiveType($liveList[$value['id']][$v['id']]['id'],$options);
	    								}
    								}
    							}else{
								// error_log(date("Y-m-d  H:i:s",time()).'-列表'.$value['name'].'分类：'.$v['name'].'，本地不存在分类，添加'."\n\r",3,'/home/apache/Runtime/Logs/Home/16_03_08.log');
    								// trace('添加',date('Y-m-d H:i:s',time()));
    								// trace('列表:'.$value['name'].'，分类:'.$v['name'].'，本地不存在分类，添加',date("Y-m-d  H:i:s",time()));
    								$contentUrl = LIVE_LIST_CONTENT_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
	    							$syncContentJson = getUrl($contentUrl);
	    							$contentSimpUrl = LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
	    							$syncContentSimpJson = getUrl($contentSimpUrl);
	    							// trace('列表:'.$value['name'].'，分类:'.$v['name'].'版本改变',date("Y-m-d  H:i:s",time()));
								// error_log(date("Y-m-d  H:i:s",time()).'列表:'.$value['name'].'，分类:'.$v['name'].'版本改变'."\n\r",3,'/home/apache/Runtime/Logs/Home/16_03_08.log');
	    							if (!empty($syncContentJson)&&!empty($syncContentSimpJson)) {
	    								$options = array(
										'type_id'=>$v['id'],
										'type_name'=>$v['name'],
										'is_recommend'=>$v['isRecommend']?'true':'false',
										'pinyin'=>$v['pinyin'],
										'version'=>$v['version'],
										'name_id'=>$value['id'],
										'url'=>$this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentJson),
										'url_simp'=>$this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentSimpJson,'_simp'),
									);
	    								//修改的列表数组
	    								$modifyContent[] = $value['id'];
									// error_log($options['url']."\n\r",3,'/home/apache/Runtime/Logs/Home/16_03_08.log');
	    								//添加分类
	    								$typeId = $liveListType->addLiveListType($options);
	    								if (!empty($typeId)) {
	    									// 不删除存在的本地分类的 id
    										$noDeleteLiveTypeIdArr[$value['id']][] = $typeId;
	    								}
    								}
    							}
    						}
    					}else{
    						$options = array(
							'name_id'=>$value['id'],
							'name'=>$value['name']
						);
    						//修改的列表数组
						$modifyContent[] = $value['id'];
    						//添加列表
    						$liveListName->addLiveListName($options);
    						foreach ($syncTypeArr['typeLists'] as $k => $v) {
    							$contentUrl = LIVE_LIST_CONTENT_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
    							$syncContentJson = getUrl($contentUrl);
    							$contentSimpUrl = LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
    							$syncContentSimpJson = getUrl($contentSimpUrl);
    							// trace('列表:'.$value['name'].'，分类:'.$v['name'].'版本改变',date("Y-m-d  H:i:s",time()));
							// error_log(date("Y-m-d  H:i:s",time()).'列表:'.$value['name'].'，分类:'.$v['name'].'版本改变'."\n\r",3,'/home/apache/Runtime/Logs/Home/16_03_08.log');
    							if (!empty($syncContentJson)&&!empty($syncContentSimpJson)) {
    								$options = array(
									'type_id'=>$v['id'],
									'type_name'=>$v['name'],
									'is_recommend'=>$v['isRecommend']?'true':'false',
									'pinyin'=>$v['pinyin'],
									'version'=>$v['version'],
									'name_id'=>$value['id'],
									'url'=>$this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentJson),
									'url_simp'=>$this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentSimpJson,'_simp'),
								);
    								// error_log($options['url']."\n\r",3,'/home/apache/Runtime/Logs/Home/16_03_08.log');
    								//添加分类
    								$typeId = $liveListType->addLiveListType($options);
    								if (!empty($typeId)) {
    									// 不删除存在的本地分类的 id
									$noDeleteLiveTypeIdArr[$value['id']][] = $typeId;
    								}
							}
    						}
    					}
    				}else{
    					if (!empty($liveList[$value['id']])) {
    						//修改的列表数组
						$modifyContent[] = $value['id'];
    						//清空分类
    						$liveListType->deleteLiveForNameId($value['id']);
    					}
    				}
    			}
    		}else{
    			result('列表为空');
    		}
    		// 删除：远程不存在分类，但本地存在分类
    		//$noDeleteLiveTypeIdArr远程分类存在
		if (!empty($noDeleteLiveTypeIdArr)) {
    			$liveListType->deleteLiveForNameIdNoIdArr($noDeleteLiveTypeIdArr);
		}
    		//删除：远程不存在列表，但本地存在列表
    		//$noDeleteLiveNameIdArr远程列表存在
    		if (!empty($noDeleteLiveNameIdArr)) {
    			$noDeleteLiveNameIdArr = array_unique($noDeleteLiveNameIdArr);
    			$noDeleteLiveNameIdStr = "'".implode("','", $noDeleteLiveNameIdArr) ."'";
    			$liveListType->deleteLiveForNoNameIdStr($noDeleteLiveNameIdStr);
    			$liveListName->deleteLiveForNoNameIdStr($noDeleteLiveNameIdStr);
    		}
    		//更新列表   如果有变量modifyContent的存在，证明有修改
    		if (!empty($modifyContent)) {
    			//列表唯一
    			$modifyContent = array_unique($modifyContent);
    			$liveListContent = D('LiveListContent','Live');
    			$liveListContent->updateLiveListContent($modifyContent);
    			if (!empty($noDeleteLiveNameIdStr)) {
    				$liveListContent->deleteLiveForNoNameIdStr($noDeleteLiveNameIdStr);
    			}
    		}
    		result();
         	}*/
    	/*

    	 public function liveListSync()
    	{
		// trace('开始同步',date("Y-m-d  H:i:s",time()));
		$get = I('get.');
    		set_time_limit(60*5);
    		$liveList = array();
    		$liveListName = D('LiveListName','Live');
    		$liveListType = D('LiveListType','Live');
    		$liveListNameArr = $liveListName->getLiveListNameLists();
    		$liveListTypeArr = $liveListType->getLiveListTypeLists();
    		if (!empty($liveListNameArr)) {
    			foreach ($liveListNameArr as $key => $value) {
				$liveList[$value['nameId']] = array();
    				if (!empty($liveListTypeArr)) {
    					foreach ($liveListTypeArr as $k => $v) {
    						if ($value['nameId'] == $v['nameId']) {
    							$liveList[$value['nameId']][$v['typeId']] = $v;
    						}
    					}
    				}
    			}
    		}
    		//获取同步直播列表名称列表
    		$nameUrl = LIVE_LIST_NAME_SYNC_PREFIX;
    		$syncNameJson = getUrl($nameUrl);
    		$syncNameArr = json_decode($syncNameJson,true);
    		if (!empty($syncNameArr)) {
    			foreach ($syncNameArr as  $value) {
    				$noDeleteLiveNameIdArr[] = $value['id'];
    				$typeUrl = LIVE_LIST_TYPE_SYNC_PREFIX .$value['id'];
    				$syncTypeJson = getUrl($typeUrl);
    				$syncTypeArr = json_decode($syncTypeJson,true);
				if ( !isset($syncTypeArr['typeLists']) ) {
					trace('解析分类列表失败',date('Y-m-d H:i:s',time()));
					if (!empty($liveList[$value['id']])) {
						unset($liveList[$value['id']]);
					}
					continue;
					// result('解析分类列表失败');
				}
				// trace('类型解密了',date('Y-m-d H:i:s',time()));
    				if (!empty($syncTypeArr['typeLists'])) {
    					if (empty($addLiveList[$value['id']])) {
						$addLiveList[$value['id']] = $value;
					}
    					if (!empty($liveList[$value['id']])) {
    						foreach ($syncTypeArr['typeLists'] as $k => $v) {
    							if (!empty($liveList[$value['id']][$v['id']])) {
    								if ($liveList[$value['id']][$v['id']]['version'] != $v['version']) {
    									$contentUrl = LIVE_LIST_CONTENT_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
		    							$syncContentJson = getUrl($contentUrl);
		    							$contentSimpUrl = LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
		    							$syncContentSimpJson = getUrl($contentSimpUrl);
		    							// trace('列表:'.$value['name'].'，分类:'.$v['name'].'版本改变',date("Y-m-d  H:i:s",time()));
    									$addLiveList[$value['id']][$v['id']] = $v;
		    							if (!empty($syncContentJson)) {
		    								$addLiveList[$value['id']][$v['id']]['url'] = $this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentJson);
		    							}
		    							if (!empty($syncContentSimpJson)) {
		    								$addLiveList[$value['id']][$v['id']]['url_simp'] = $this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentSimpJson,'_simp');
		    							}
    								}else{
    									unset($liveList[$value['id']][$v['id']]);
    								}
    							}else{
    								// trace('添加',date('Y-m-d H:i:s',time()));
    								// trace('列表:'.$value['name'].'，分类:'.$v['name'].'，本地不存在分类，添加',date("Y-m-d  H:i:s",time()));
    								$contentUrl = LIVE_LIST_CONTENT_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
	    							$syncContentJson = getUrl($contentUrl);
	    							$contentSimpUrl = LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
	    							$syncContentSimpJson = getUrl($contentSimpUrl);
    								$addLiveList[$value['id']][$v['id']] = $v;
	    							if (!empty($syncContentJson)) {
	    								$addLiveList[$value['id']][$v['id']]['url'] = $this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentJson);
	    							}
	    							if (!empty($syncContentSimpJson)) {
	    								$addLiveList[$value['id']][$v['id']]['url_simp'] = $this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentSimpJson,'_simp');
	    							}
    							}
    						}
    					}else{
    						foreach ($syncTypeArr['typeLists'] as $k => $v) {
    							// trace('列表:'.$value['name'].'，分类:'.$v['name'].'，本地不存在列表，添加',date("Y-m-d  H:i:s",time()));
							// trace('添加',date('Y-m-d H:i:s',time()));
    							$addLiveList[$value['id']][$v['id']] = $v;
    							$contentUrl = LIVE_LIST_CONTENT_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
    							$syncContentJson = getUrl($contentUrl);
    							if (!empty($syncContentJson)) {
    								$addLiveList[$value['id']][$v['id']]['url'] = $this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentJson);
    							}
    							$contentSimpUrl = LIVE_LIST_CONTENT_SIMP_SYNC_PREFIX.$value['id']."&typeId=".$v['id'];
    							$syncContentSimpJson = getUrl($contentSimpUrl);
    							if (!empty($syncContentSimpJson)) {
    								$addLiveList[$value['id']][$v['id']]['url_simp'] = $this->getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$syncContentSimpJson,'_simp');
    							}
    						}
    					}
    				}else{
    					if (!empty($liveList[$value['id']])) {
    						$deleteLiveTypeForNameId[] = $value['id'];
    					}
    				}
    			}
    		}else{
    			result('列表为空');
    		}
    		if (!empty($addLiveList)) {
    			foreach ($addLiveList as  $value) {
    				if (!empty($value['id'])&&!empty($value['name'])) {
    					$addLiveNameArr[] = array(
    						'name_id'=>$value['id'],
    						'name'=>$value['name'],
					);
    				}
    				foreach ($value as $key => $v) {
    					if ($key !='id' && $key !='name') {
    						$addLiveTyeArr[] = array(
    							'type_id'=>$v['id'],
    							'type_name'=>$v['name'],
    							'is_recommend'=> is_bool($v['isRecommend'])?'true':"false",
    							'pinyin'=>$v['pinyin'],
    							'version'=>$v['version'],
    							'url'=>isset($v['url']) ? $v['url'] :"",
    							'url_simp'=>isset($v['url_simp'])?$v['url_simp']:"",
    							'name_id'=>$value['id'],
						);
    						$updateContent[] =  $value['id'];
    					}
    				}
    			}
    		}
    		if (!empty($liveList)) {
    			foreach ($liveList as $key => $value) {
	    			if (!empty($liveList[$key])) {
	    				$typeUrl = LIVE_LIST_NAME_SYNC_PREFIX. '?listId='.$key;
	    				$syncTypeJson = getUrl($typeUrl);
	    				$syncTypeArr = json_decode($syncTypeJson,true);
	    				if ( !isset($syncTypeArr['typeLists']) ) {
						trace('解析分类列表失败',date('Y-m-d H:i:s',time()));
						continue;
					}
	    				$updateContent[] =  $key;
	    				foreach ($value as $k => $v) {
	    					$deleteLiveTypeArrID[] = $v['id'];
	    				}
	    			}
	    		}
    		}
    		//删除分类
    		if (!empty($deleteLiveTypeArrID)) {
    			$deleteLiveTypeStrID = implode(',', $deleteLiveTypeArrID);
    			$liveListType->deleteLiveListTypeArrForIDStr($deleteLiveTypeStrID);
    		}
    		//删除列表
    		if (!empty($noDeleteLiveNameIdArr)) {
    			$noDeleteLiveNameIdArr = array_unique($noDeleteLiveNameIdArr);
    			$noDeleteLiveNameIdStr = "'".implode("','", $noDeleteLiveNameIdArr) ."'";
    			$liveListType->deleteLiveForNoNameIdStr($noDeleteLiveNameIdStr);
    			$liveListName->deleteLiveForNoNameIdStr($noDeleteLiveNameIdStr);
    		}
    		//删除分类列表
    		if (!empty($deleteLiveTypeForNameId)) {
    			$deleteLiveTypeForNameId = array_unique($deleteLiveTypeForNameId);
    			$deleteLiveTypeForNameIdStr = "'".implode("','", $deleteLiveTypeForNameId) ."'";
    			$liveListType->deleteLiveForNameIdStr($deleteLiveTypeForNameIdStr);
    		}
    		//添加分类
    		if (!empty($addLiveTyeArr)) {
    			if (!empty($addLiveNameArr)) {
				$liveListName->addLiveListNameArr($addLiveNameArr);
			}
    			$liveListType->addLiveListTypeArr($addLiveTyeArr);
    		}
    		//更新列表
    		if (!empty($updateContent)) {
    			$updateContent = array_unique($updateContent);
    			$liveListContent = D('LiveListContent','Live');
    			$liveListContent->updateLiveListContent($updateContent);
    			if (!empty($noDeleteLiveNameIdStr)) {
    				$liveListContent->deleteLiveForNoNameIdStr($noDeleteLiveNameIdStr);
    			}
    		}
    		// die;
    		// trace('同步结束',date("Y-m-d  H:i:s",time()));
    		result();
    	}*/
    	public function getOssPathForLiveListForLiveTypeForLiveContentJson($value,$v,$slotsFileStr,$suffix='')
    	{
    		$filePath = '/var/www/html/download/temp/'.$v['version'];
    		if (file_exists($filePath)) {
			unlink($filePath);
		}
		$myfile = @fopen($filePath, "w");
		if (!$myfile) {
			result( '无法打开 '.$v['version'].'文件');
		}
		fwrite($myfile, $slotsFileStr);
		fclose($myfile);
		//上传OSS
		require_once '../Base/Ossupclass/OssBase.class.php';
		$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
		$newUrl = !empty($suffix) ? $suffix : '';
		$formData = array(
			"liveList" => array(
		    		"extension" =>"",
				"md5_file" => "live_list/".$value['id']."/".$v['id']."/".$v['version']. $newUrl,
		    		"filepath" => $filePath,
			    	"size" => filesize($filePath)
		  	)
		);
		$res = $base->uploadFileForPath($formData);

		if (file_exists($filePath)) {
			unlink($filePath);
		}
		return $res['liveList']['oss'];
    	}

    	/**
    	 * 直播系统_获取直播列表名称
       	* /Live/getListName
       	* /Live/getListName
    	 * @param  string $value [description]
    	 * @return [type]        [description]
    	 */
    	public function getListName()
    	{
    		$res = D('LiveListName','Live')->getListName();
    		result(true,$res);
    	}
    	/**
    	 * 直播系统_直播列表管理_获取直播列表
       	* /Live/getListName?
       	* /Live/getListName
    	 * @param  string $value [description]
    	 * @return [type]        [description]
    	 */
    	public function getListTypeName()
    	{
    		$get = I('get.');
    		$res = D('LiveListType','Live')->getListTypeName($get);
    		result(true,$res);
    	}
    	/**
    	 * 直播系统_直播列表管理_获取直播列表分类
       	* /Live/getListType?nameId=直播列表名称id
    	 * @param  string $value [description]
    	 * @return [type]        [description]
    	 */
    	public function getListType()
    	{
    		$get = I('get.');
    		$res = D('LiveListType','Live')->getListType($get);
    		result(true,$res);
    	}
    	/**
    	 * 直播系统_直播列表管理_获取直播列表分类频道
       	* /Live/getListTypeChannel?id=直播列表分类id
    	 * @param  string $value [description]
    	 * @return [type]        [description]
    	 */
    	public function getListTypeChannel()
    	{

    		$get = I('get.');
    		$res = D('LiveListType','Live')->getListTypeChannel($get);
    		result(true,$res);
    	}
    	/**
    	 * 直播系统_直播广告_获取直播类型
       	* /Live/getChannelType
    	 * @return [type] [description]
    	 */
    	public function getChannelType()
    	{
    		$get = I('get.');
    		$res = D('ChannelType','Live')->getChannelType($get);
    		result(true,$res);
    	}
    	/**
    	 * 直播系统_直播广告_获取直播频道
       	* /Live/getChannelList?nameId=直播列表名称id
    	 * @return [type] [description]
    	 */
    	public function getChannelList()
    	{
    		$get = I('get.');
    		$res = D('ChannelList','Live')->getChannelList($get);
    		result(true,$res);
    	}

    	/**
    	 * 直播系统_添加直播开机广告
       	 * post /Live/addLiveStartupPic
       	 * 表单上传 ：type=file name=img
       	 *
       	 * type=text name="model" value="ALL"
       	 * type=text name="vendorID" value="vendorID"
       	 * type=text name="name" value="开机画面名称，可为空"
       	 * type=text name="type" value="AB/group"   灰度，内测
       	 * type=text name="groupId" value="内测组id"
       	 * type=text name="AB" value="灰度值"
       	 * type=text name="isSkip" value="false/true"   //是否跳过
       	 * type=text name="showTime" value="展示次数"
       	 * type=text name="blackList" value="a;b"
       	 * type=text name="whiteList" value="a;b"
       	 *
    	 * @param string $value [description]
    	 */
    	public function addLiveStartupPic()
    	{
    		$post = I('post.');
    		D('LiveBootad','Live')->addLiveBootad($post);
    		result();
    	}
    	/**
    	 * 直播系统_修改直播开机广告
       	 * post /Live/modifyLiveStartupPic
       	 * {
       	 * "id":"直播开机广告ID",
       	 * "type":"AB/group/All"  灰度，内测，公开
       	 * "groupId":"内测组id",
       	 * "AB":"灰度值"
       	 * }
    	 * @param string $value [description]
    	 */
    	public function modifyLiveStartupPic()
    	{
    		$put = I('put.');
    		D('LiveBootad','Live')->modifyLiveBootad($put);
    		result();
    	}
    	/**
    	 * 删除直播开机广告
    	 * post /Live/deleteLiveStartupPic
    	 * ["id1","id2"]
    	 * @param string $value [description]
    	 */
    	public function deleteLiveStartupPic()
    	{
    		$put = I('put.');
    		D('LiveBootad','Live')->deleteLiveBootad($put);
    		result();
    	}
    	/**
    	 * 直播开机广告列表
    	 * get /Live/liveStartupPicLists?page=x&pageSize=x&name=x
    	 * @param string $value [description]
    	 */
    	public function liveStartupPicLists()
    	{
    		$get = I('get.');
    		$res = D('LiveBootad','Live')->liveBootadLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 直播系统_直播频道EPG同步
       	* /Live/channelEpgSnyc?date=时间 20160401
    	 * @return [type] [description]
    	 */
    	public function channelEpgSnyc()
    	{
    		$get = I('get.');
    		if (!empty($get['date'])) {
    			$snycChannel = getUrl(C("GET_LIVE_CHANNEL_EPG_PREFIX_ADDR").$get['date'].'.html');

    			if (empty($snycChannel)) {
    				result('获取同步直播频道EPG失败');
    			}
    			$snycChannel = json_decode($snycChannel,true);
    			//获取本地频道列表
    			$channel = D('ChannelList','Live')->getAllChannel();

    			if ($channel) {
    				$channelEpg = array();
    				$typeIdArr = array();

    				//加载OSS
    				requireBase('Ossupclass/OssBase.class.php');
    				$ossBase  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
    				foreach ($channel as  $value) {
    					//判断同步频道是否存在频道
    					if (!empty($snycChannel[$value['channelId']])){
    						if (!in_array($value['typeId'], $typeIdArr)) {
	    						$typeIdArr[] = $value['typeId'];
	    					}
    						//添加到对比频道EPG
						$channelEpg[$value['typeId']]['channels'][] = array(
    							'id'=>$value['channelId'],
    							'epg'=>$snycChannel[$value['channelId']]
						);
						$channelEpg[$value['typeId']]['id'] = $value['typeId'];
						$channelEpg[$value['typeId']]['date'] = $get['date'];

    					}
    				}

    				if (!empty($channelEpg)) {

					//实例化
    					$channelEpgObject = D('ChannelEpg','Live');
    					$typeIdArr = array_unique($typeIdArr);
    					$typeIdSql = "'".implode("','", $typeIdArr)."'";
    					//获取某时间段内、某些直播列表分类本地EPG
    					$channelEpgLists = $channelEpgObject->getChannelForTypeIdSqlDate($typeIdSql,$get['date']);
    					//更新时间
    					$time = time();
    					$date = date('YmdHis',$time);
    					$channelEpgTypeIdArr = array();

    					if (!empty($channelEpgLists)) {
    						foreach ($channelEpgLists as  $value) {
    							$channelEpgTypeIdArr[] = $value['typeId'];
	    						if (!empty($channelEpg[$value['typeId']])) {
	    							//频道EPGMD5不一样修改
	    							$channelEpg[$value['typeId']] = json_encode( $channelEpg[$value['typeId']], JSON_UNESCAPED_UNICODE);
	    							$epgMd5 = md5($channelEpg[$value['typeId']]);

	    							//加密内容

	    							if ($value['md5'] != $epgMd5) {

    									$modifyOptions = array(
	    									'update_time'=>$date,
	    									'type_id' =>$value['typeId'],
	    									'date'=>$get['date'],
	    									'md5'=>$epgMd5,
										'url'=>C("LIVE_CHANNEL_EPG_PREFIX_ADDR").$this->getOssPathForOssObjectChannelEpgForDate($ossBase,$channelEpg[$value['typeId']],$value['typeId'],$get['date'],$time),
										"en_url"=>C("LIVE_CHANNEL_EPG_PREFIX_ADDR").$this->getOssPathForOssObjectChannelEpgForDate($ossBase,cbc_encontent($channelEpg[$value['typeId']]),$value['typeId'],$get['date'],$date.rand(0,999)),
									);
    									$channelEpgObject->modifyChannelEpg($value['id'],$modifyOptions);
	    							}
	    						}

	    					}
    					}
    					// ff8080813bd0097f013bd00fd3660040
    					foreach ($channelEpg as $key => $value) {
    						if (!in_array($key, $channelEpgTypeIdArr)) {

    							$value = json_encode($value, JSON_UNESCAPED_UNICODE);
    							$addOptions = array(
								'update_time'=>$date,
								'type_id' =>$key,
								'date'=>$get['date'],
								'md5'=>md5($value),
								'url'=>C("LIVE_CHANNEL_EPG_PREFIX_ADDR").$this->getOssPathForOssObjectChannelEpgForDate($ossBase,$value,$key,$get['date'],$time),
								"en_url"=>C("LIVE_CHANNEL_EPG_PREFIX_ADDR").$this->getOssPathForOssObjectChannelEpgForDate($ossBase,cbc_encontent($value),$key,$get['date'],$date.rand(0,999)),
							);

							$channelEpgObject->addChannelEpg($addOptions);

    						}
    					}
    				}
    				result();
    				// var_dump($channelEpg);
    				// die;
    			}
    		}else{
    			result('param');
    		}

    	}
    	/**
    	 * 同步直接频道EPG上传OSS
    	 * @param  [type] $ossBase    [description]
    	 * @param  [type] $ChannelEpg [description]
    	 * @param  [type] $date       [description]
    	 * @param  [type] $time       [description]
    	 * @return [type]             [description]
    	 */
    	public function getOssPathForOssObjectChannelEpgForDate($ossBase,$ChannelEpg,$typeId,$date,$time)
    	{

    		//test
		$fileName = 'epg_'.$date.'_'. $typeId;
		$filePath = '/var/www/html/download/temp/'.$fileName;
    		if (file_exists($filePath)) {
			unlink($filePath);
		}
		$myfile = @fopen($filePath, "w");
		if (!$myfile) {
			result( '无法打开 '.$fileName.'文件');
		}

		fwrite($myfile, $ChannelEpg);
		fclose($myfile);
		//上传OSS

		$formData = array(
			"liveList" => array(
		    		"extension" =>"",
				"md5_file" => "epg/".$date."/".$typeId."_".$time,
		    		"filepath" => $filePath,
			    	"size" => filesize($filePath)
		  	)
		);
		$res = $ossBase->coveUploadFileForPath($formData);
		if (file_exists($filePath)) {
			unlink($filePath);
		}
		return $res['liveList']['oss'];
    	}
    	/**
    	 * 直播系统_添加直播授权
    	 * post /Live/addLiveAuth
    	 * {
    	 * 	"model":"厂商",
    	 * 	"num":"数量"
    	 * }
    	 * [addLiveAuth description]
    	 */
    	public function addLiveAuth()
    	{
    		$put = I("put.");
    		D('LiveYunosAuthModel','Live')->addLiveAuth($put);
    		result();
    		// D('LiveYunosAuthModel','Live')->addLiveAuth();
    	}
    	/**
    	 * 直播系统_修改直播授权设置
    	 * post /Live/modifyLiveAuth
    	 * {
    	 * 	"id":"直播授权ID",
    	 * 	"model":"厂商",
    	 * 	"num":"数量"
    	 * }
    	 * [addLiveAuth description]
    	 */
    	public function modifyLiveAuth()
    	{
    		$put = I("put.");
    		D('LiveYunosAuthModel','Live')->modifyLiveAuth($put);
    		result();
    		// D('LiveYunosAuthModel','Live')->addLiveAuth();

    	}
    	/**
    	 * 直播系统_删除直播授权设置
    	 * post /Live/deleteLiveAuth
    	 * ["id1","id2"]
    	 * @param string $value [description]
    	 */
    	public function deleteLiveAuth()
    	{

    		$put = I('put.');
    		D('LiveYunosAuthModel','Live')->deleteLiveAuth($put);
    		result();
    	}
    	/**
    	 * 直播系统_直播授权列表
    	 * get /Live/liveAuthLists?page=x&pageSize=x&name=x
    	 * @param string $value [description]
    	 */
    	public function liveAuthLists()
    	{
    		$get = I('get.');
    		$res = D('LiveYunosAuthModel','Live')->liveAuthLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 直播系统_直播操作记录
    	 * get /Live/liveAuthHistoryLists?page=x&pageSize=x&name=x
    	 * @param string $value [description]
    	 */
    	public function liveAuthHistoryLists()
    	{
    		$get = I('get.');
    		$res = D('LiveYunosAuthHistory','Live')->liveAuthHistoryLists($get);
    		result(true,$res);
    	}
    	/**
    	 * 直播系统_直播已授权记录
    	 * get /Live/getLiveAuthDetail?page=x&pageSize=x
    	 * {
    	 * 	"model":"型号",
    	 * 	"vendorID":"vendorID",
    	 * 	"mac":"mac",
    	 * 	"wifi":"wifi",
    	 * 	"channel":"渠道",
    	 * 	"startTime":"开始时间",
    	 * 	"endTime":"结束时间"
    	 * }
    	 * @param string $value [description]
    	 */
    	public function getLiveAuthDetail()
    	{
    		$get = I('get.');
    		$put = I('put.');
    		$res = D('LiveYunosAuthClient','Live')->getLiveAuthDetail($get,$put);
    		result(true,$res);
    	}
    	/**
    	 * [同步EPG点播信息 description]
    	 * get /Live/syncEpgBunchPlanting?date=20160511
    	 * @param string $value [description]
    	 */
    	public function syncEpgBunchPlanting()
    	{
    		set_time_limit(0);
    		// ini_set ('memory_limit', '10M');
    		// trace("开始","运行");
    		$get = I('get.');
    		if (empty($get['date'])) {
			result('param');
		}

    		$channelEpgVersion = D('ChannelEpgVersion','Live');

    		$epgVersion = getUrl(C('LIVE_CHANNEL_EPG_VERSION_ADDR'));

    		$epgVersion = json_decode($epgVersion,true);
    		if (empty($epgVersion['ver'])) {
    			result('获取远程点播版本失败');
    		}
    		$epgVersionDB = $channelEpgVersion->getOneForVersionDate($epgVersion['ver'],$get['date']);

    		if ($epgVersion['ver'] > $epgVersionDB['version']) {

    			$where['date'] = intval($get['date']);
    			$channelEpgSync = D('ChannelEpgVod','Live');
    			$epgDBArr = $channelEpgSync->getArrForDate($where);
			$url = C('LIVE_CHANNEL_EPG_SNYC_PREFIX_ADDR') . $where['date'] . '.html';

			$channelContent = getUrl($url);
			$epgArr = json_decode( $channelContent ) ;
			if (empty($epgArr)) {
				result('获取远程点播失败');
			}

			unset($channelContent);

			foreach ($epgDBArr as  $value) {
				if (!empty($epgArr->$value['channel'])) {
					$channelEpgSync->modifyEpg($value['id'],json_encode($epgArr->$value['channel'],JSON_UNESCAPED_UNICODE));
					unset($epgArr->$value['channel']);
				}
			}

			if (!empty($epgArr)) {

				foreach ($epgArr as $key => $value) {

					$addOptions[] = array(
						'date'=>$get['date'],
						'channel'=>$key,
						'epg'=>json_encode($value,JSON_UNESCAPED_UNICODE)
					);

				}
				// var_dump(count($addOptions));
				$channelEpgSync->addEpgArr($addOptions);
			}
			$channelEpgVersion->modifyVersionForVersionDate($epgVersion['ver'],$get['date']);
			// var_dump($epgArr);
    		}
    		// trace("结束","运行");
    		result();
    	}
}