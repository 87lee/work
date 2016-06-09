<?php
namespace Home\Controller;
use Think\Controller;
class FunctionalActionController extends Controller {
	public function __construct()
    	{
	        	parent::__construct();
	       	isLogin();
    	}

    	/**
         	* 访问不存在的地址
         	* @param  [type] $name [description]
         	* @return [type]       [description]
         	*/
    	public function _empty($name){        //把所有城市的操作解析到city方法
    		echo '{"result":"fail","reason":"当前地址不存在"}';
    	}
    	public function snycOperator()
    	{
    		$str = '[
			{"name":"中国电信","pinyin":"shanxi"},
			{"name":"中国网通","pinyin":"ningxia"},
			{"name":"中国铁通","pinyin":"qinghai"},
			{"name":"中国联通","pinyin":"ningxia"},
			{"name":"教育网","pinyin":"qinghai"},
			{"name":"中国移动","pinyin":"shanghai"}
		]';
		$arr = json_decode($str,true);
		$provinceObjcet = D('Operator','Desktop');
		if (!$provinceObjcet->find()) {
			$provinceObjcet->addAll($arr);
		}
		result();
    	}
    	public function snycProvince()
    	{
    		$str = '[
			{"name":"陕西","pinyin":"shanxi"},
			{"name":"宁夏","pinyin":"ningxia"},
			{"name":"青海","pinyin":"qinghai"},
			{"name":"上海","pinyin":"shanghai"},
			{"name":"新疆","pinyin":"xiangjiang"},
			{"name":"辽宁","pinyin":"liaoning"},
			{"name":"山西","pinyin":"shanxi"},
			{"name":"湖北","pinyin":"hubei"},
			{"name":"湖南","pinyin":"hunan"},
			{"name":"澳门","pinyin":"aomen"},
			{"name":"香港","pinyin":"xianggan"},
			{"name":"贵州","pinyin":"guizhou"},
			{"name":"浙江","pinyin":"zhejian"},
			{"name":"天津","pinyin":"tianjin"},
			{"name":"安徽","pinyin":"anhui"},
			{"name":"西藏","pinyin":"xizang"},
			{"name":"内蒙古","pinyin":"neimenggu"},
			{"name":"江西","pinyin":"jiangxi"},
			{"name":"广东","pinyin":"guangdong"},
			{"name":"广西","pinyin":"guangxi"},
			{"name":"福建","pinyin":"fujian"},

			{"name":"海南","pinyin":"hainan"},
			{"name":"台湾","pinyin":"taiwan"},

			{"name":"北京","pinyin":"beijing"},
			{"name":"黑龙江","pinyin":"heilongjiang"},
			{"name":"河北","pinyin":"hebei"},

			{"name":"四川","pinyin":"sichuan"},
			{"name":"河南","pinyin":"henan"},

			{"name":"山东","pinyin":"shandong"},
			{"name":"吉林","pinyin":"jilin"},
			{"name":"江苏","pinyin":"jiangsu"},
			{"name":"重庆","pinyin":"chongqing"},

			{"name":"云南","pinyin":"yunnan"},
			{"name":"甘肃","pinyin":"gansu"}

		]';
		$arr = json_decode($str,true);
		$provinceObjcet = D('Province','Desktop');
		if (!$provinceObjcet->find()) {
			$provinceObjcet->addAll($arr);
		}
		result();
    	}
    	/*public function syncVideosScore()
    	{
    		// $vodRecommendVideos = D('VodRecommendVideos','Vod');
    		$vodRecommendVideosItems = D('VodRecommendVideosItems','Vod');

    		$vodProgram = D('VodProgram','Vod');

    		$vodProgramIdSql = $vodRecommendVideosItems->field('video_id')->select(false);

    		$where = 'id IN ('.$vodProgramIdSql.')';
		$field = 'a.id,a.name,CASE WHEN a.douban_id IS NULL THEN "" WHEN a.douban_id IS NOT NULL THEN a.douban_id END  as doubanid,CASE WHEN a.douban_score IS NULL THEN "" WHEN a.douban_score IS NOT NULL THEN a.douban_score END  AS score';

		$vodProgramArr = $vodProgram->alias(" AS a")->field($field)->where($where)->select();

		$vodProgramData = [];
		foreach ($vodProgramArr as  $value) {
			$vodProgramData[$value['id']] = $value;
		}

		$vodRecommendVideosItemsArr = $vodRecommendVideosItems->field('id,video_id')->select();

		foreach ($vodRecommendVideosItemsArr as  $value) {

			if (!empty($vodProgramData[$value['videoId']])) {
				$options = array(
					'doubanid'=>$vodProgramData[$value['videoId']]['doubanid'],
					'score'=>$vodProgramData[$value['videoId']]['score'],
				);
				$vodRecommendVideosItems->where('id =%d',array($value['id']))->save($options);
			}
		}
		result();
    	}*/
	/*public function syncVideosNewsTable()
    	{
    		$vodRecommendVideos = D('VodRecommendVideos','Vod');
    		$vodRecommendVideosItems = D('VodRecommendVideosItems','Vod');

    		if ($vodRecommendVideosItems->find()) {
    			result('已存在数据');
    		}

    		$videos = $vodRecommendVideos->select();
    		foreach ($videos as  $value) {
    			$list = json_decode($value['videos'],true);
    			$addVideosOptions = array();

    			foreach ($list['list'] as  $v) {

    				$addVideosOptions[] = array(
    					'group_id'=>$value['id'],
    					'video_id'=>$v['id'],
    					'video_name'=>$v['name'],
    					'video_picture'=>$v['picture'],
    					'video_thumb'=>$v['thumb'],
    					'video_type'=>$v['type'],
    					'app_id'=>$v['app']['id'],
    					'app_name'=>$v['app']['name'],
    					'app_skipid'=>$v['app']['skipid'],
    					'app_pay'=>$v['app']['pay'],

    					'invalid'=>0,
    					'recommend'=>0,
    					'desc'=>'',
    					'doubanid'=>'',
    					'score'=>'',
    					'position'=>0,
				);
    			}
    			if (!empty($addVideosOptions)) {
    				$vodRecommendVideosItems->addAll($addVideosOptions);
    			}

    		}
    		result();
    	}*/
    	/*public function syncVideos()
    	{
    		$vodRecommendVideos = D('VodRecommendVideos','Vod');
    		$videos = $vodRecommendVideos->select();
    		foreach ($videos as  $value) {
    			$list = json_decode($value['videos'],true);
    			foreach ($list['list'] as  $v) {
    				$programIds[] = $v['id'];
    			}
    		}

    		$programIds = array_unique($programIds);

    		$programSqlIds = implode(',', $programIds);

    		$programObject = D('VodProgram','Vod');

    		$where = "a.id IN (".$programSqlIds.")";
		$field = 'a.id,b.name AS type';
		$join = " LEFT JOIN vod_program_type AS b ON b.id = a.program_type_id";

		$programArr = $programObject->alias(" AS a")->field($field)->where($where)->join($join)->select();
		foreach ($programArr as  $value) {
			$programTypeArr[$value['id']] = $value['type'];
		}
		foreach ($videos as $key => $value) {
			$list = json_decode($value['videos'],true);
    			foreach ($list['list'] as  $k =>$v) {
    				$list['list'][$k]['type'] = $programTypeArr[$v['id']];

    			}
    			$options = array(
    				'videos'=>json_encode($list,JSON_UNESCAPED_UNICODE)
			);
    			$vodRecommendVideos->where('id = %d',array($value['id']))->save($options);
		}
		result();
    	}*/
    	/**
    	 * 同步桌面发布md5
    	 * @return [type] [description]
    	 */
    	/*public function desktopVersionSyncMd5()
    	{
    		set_time_limit(0);
    		$desktopVersion = D('DesktopVersion','Desktop');
    		$desktopVersionArr = $desktopVersion->table("( SELECT * FROM `tb_desktop_version` WHERE `md5` != '' ORDER BY `time` DESC ) AS b")->group('model')->select();
    		 if (!empty($desktopVersionArr)) { //SELECT * FROM ( SELECT * FROM `tb_desktop_version` WHERE `md5` != '' ORDER BY `layout_version` DESC ) AS b GROUP BY `model`
    			foreach ($desktopVersionArr as  $value) {

    				if (!empty($value['md5'])) {
    					$name = explode('/',$value['sourcePath']);
					$name = end($name);
					$savePath = C('LOCALHOST_PATH_ADDR').'temp/'.$name;

					if (file_exists($savePath)) {
    						unlink(C('LOCALHOST_PATH_ADDR').'temp/'.$name);
    					}

    					$bool = copy(C('DOWNLOAD_DESKTOP_SOURCER_PREFIX_ADDR') . $value['sourcePath'], $savePath);

    					if (file_exists($savePath) ) {
    						$md5 = md5_file($savePath);
    						if ($md5 != $value['md5']) {
    							$options = array(
	    							'md5'=>md5_file(C('LOCALHOST_PATH_ADDR').'temp/'.$name)
							);
	    						$desktopVersion->where('id = %d',array($value['id']))->save($options);
    						}
    					}

    					if (file_exists($savePath)) {
    						unlink(C('LOCALHOST_PATH_ADDR').'temp/'.$name);
    					}
    				}
    			}
    		}
    		result();
    	}*/
    	/**
    	 * 同步桌面发布md5
    	 * @return [type] [description]
    	 */
    	/*public function desktopVersionPublishSyncMd5()
    	{
    		set_time_limit(0);
    		$desktopVersionPublish = D('DesktopVersionPublish','Desktop');
    		$desktopVersionPublishArr = $desktopVersionPublish->select();
    		if (!empty($desktopVersionPublishArr)) {
    			foreach ($desktopVersionPublishArr as  $value) {
    				// if (empty($value['md5'])) {
    				//
    					$name = explode('/',$value['sourcePath']);
					$name = end($name);

					if (file_exists(C('LOCALHOST_PATH_ADDR').'temp/'.$name)) {
    						unlink(C('LOCALHOST_PATH_ADDR').'temp/'.$name);
    					}

    					$bool = copy($value['sourcePath'], C('LOCALHOST_PATH_ADDR').'temp/'.$name);
    					if (file_exists(C('LOCALHOST_PATH_ADDR').'temp/'.$name)) {
    						$options = array(
    							'md5'=>md5_file(C('LOCALHOST_PATH_ADDR').'temp/'.$name)
						);
    						$desktopVersionPublish->where('id = %d',array($value['id']))->save($options);
    					}

    					if (file_exists(C('LOCALHOST_PATH_ADDR').'temp/'.$name)) {
    						unlink(C('LOCALHOST_PATH_ADDR').'temp/'.$name);
    					}
    				// }
    			}
    		}
    		result();
    	}*/
    	/*public function liveListPublishSync()
    	{
    		$liveListPublish = D('LiveListPublish','Live');
    		$llRelease = M('llRelease ','tb_',"mysql://".DB_USER.":".DB_PWD."@".DB_HOST.":".DB_PORT."/db_access_configure#utf8");
    		if ($liveListPublishArr = $liveListPublish->select()) {
    			result('发布列表已存在');
    		}
    		if ($llReleaseArr = $llRelease->where("f_type = 'all' and f_valid = 1 ")->select()) {

    			foreach ($llReleaseArr as  $value) {

    				$options[] = array(
    					'type'=>"ALL",
    					'model'=>$value['f_model'],
    					'vendor_id'=>$value['f_vendor_id'],
    					'name_id'=>$value['f_release_id'],
    					'group_id'=>'0',
    					'ab'=>"",
    					'time'=>strtotime($value['f_time']),
				);
    			}
    			$liveListPublish->addAll($options);
    			result();
    		}
    	}*/
    	/**
	 * 静默安装--同步黑名单
	 * @return [type] [description]
	 */
	/*public function silentSyncOld()
	{
		$silent = M('silentBlackList ','tb_',"mysql://".DB_USER.":".DB_PWD."@".DB_HOST.":".DB_PORT."/db_apk_silent#utf8");
		$silentArr = $silent->select();
		if (!empty($silentArr)) {
			foreach ($silentArr as $key => $value) {
				$options[] = array(
					'model'=>$value['f_model'],
					'vendor_id'=>$value['f_vendor_id']
				);
			}
			// var_dump($options);
			D('SilentBlackList','Silent')->addAll($options);
		}
	}*/

    	/**
	 * 桌面系统--桌面运营坑位同步越狱前后
	 * @return [type] [description]
	 */

    	/*public function desktopSlotSync()
    	{
    		$Desktop = D('Desktop','Desktop');
    		$DesktopScreens = D('DesktopScreens','Desktop');

		$sql = $Desktop->field("id")->where('breakout = "false" ')->select(false);

		$sql = $DesktopScreens->field("id")->where("desktop_id IN (".$sql.")")->select();

		if ($sql) {
			foreach ($sql as $key => $value) {
				$aaa[] = $value['id'];
			}
			$bbb = implode($aaa,',');
			$options = array(
				'slot_group_id'=>'1'
			);
			$DesktopScreens->where("`id` IN (".$bbb.")")->save($options);
			// $ccc = $DesktopScreens->where("`id` IN (".$bbb.")")->select();

			echo $DesktopScreens->getLastSql();
		}
		// var_dump($ccc);
		// die;

		$sql1 = $Desktop->field("id")->where('breakout = "true" ')->select(false);
		$aaa = array();
		$sql1 = $DesktopScreens->field("id")->where("desktop_id IN (".$sql1.")")->select();

		if ($sql1) {
			foreach ($sql1 as $key => $value) {
				$aaa[] = $value['id'];
			}
			$bbb = implode($aaa,',');
			$options = array(
				'slot_group_id'=>'2'
			);
			$DesktopScreens->where("`id` IN (".$bbb.")")->setField($options);

			echo $DesktopScreens->getLastSql();
		}

    	}*/



    	/**
	 * 桌面系统--运营坑位同步越狱前后
	 * @return [type] [description]
	 */
    	/*public function desktopSlotSyncBreakout()
    	{
    		$OperationSlot = D('OperationSlot','Desktop');
    		if ($OperationSlot) {
    			$sql = $OperationSlot->field("id")->where('slot_group_id ="false"')->select();


			if ($sql) {
				foreach ($sql as $key => $value) {
					$aaa[] = $value['id'];
				}
				$bbb = implode($aaa,',');

				$OperationSlot->where("id IN(".$bbb.")")->setField('slot_group_id','1');

				// echo $OperationSlot->getLastSql();
			}

			$sql1 = $OperationSlot->field("id")->where('slot_group_id ="true"')->select();


			if ($sql1) {
				foreach ($sql1 as $key => $value) {
					$aaa[] = $value['id'];
				}
				$bbb = implode($aaa,',');

				$OperationSlot->where("id IN(".$bbb.")")->setField('slot_group_id','2');

				// echo $OperationSlot->getLastSql();
			}
    		}
    	}*/
    	/**
	 * 应用管理--第三方应用添加版本名称
	 * @return [type] [description]
	 */
    	/*public function add3rdAppVersionName()
    	{
    		$App3rdVersions = D('App3rdVersions','App');
    		$allValue = $App3rdVersions->select();
    		if (!empty($allValue)) {
    			foreach ($allValue as $key => $value) {
    				$options = array(
    					'version_name'=>$value['versionCode'],
				);
    				$App3rdVersions->where('id = %d',array($value['id']))->save($options);
    			}
    		}
    	}*/
    	/**
	 * 同步桌面旧数据redis到新桌面
	 * @return [type] [description]
	 */
	/*public function syncDesktopRedis()
	{
		$res = getUrl(DESKTOP3_ACCESS_ADDR.'access/publish/list?item=desktop3.0');
		$res = json_decode($res,true);
		$options = array();
		foreach ($res['extra'] as $key => $value) {
			if ($value['target']['type'] =='ALL') {
				$options[] = array(
					'model'=>$value['target']['model'],
					'type'=>$value['target']['type'],
					'group_id'=>'0',
					'version'=>$value['content']['version'],
					'layout_version'=>!empty($value['content']['layoutVersion'])?$value['content']['layoutVersion']:'0',
					'gray'=>'0',
					'layout_path'=>$value['content']['layoutPath'],
					'source_path'=>$value['content']['sourcePath'],
					'time'=>strtotime($value['time']),
				);
			}
		}
		if (!empty($options)) {
			if (!D('DesktopVersionPublish','Desktop')->where('type="ALL" or type="group_id"')->find()) {
				D('DesktopVersionPublish','Desktop')->addAll($options);
			}
		}
	}*/
	/**
	 * 获取桌面名字
	 * @return [type] [description]
	 */
	public function getDesktopName()
	{
		$id = I("get.id");
		$where = ' is_effective = "true"';
		if (!empty($id)) {
			$where .= ' and group_id = '.intval($id);
		}
		$desktopNameArr = D('Desktop','Desktop')->field('name')->where($where)->select();

		if (!empty($desktopNameArr)) {
			foreach ($desktopNameArr as $value) {
				echo $value['name']  . "\n";
			}
		}
	}
	/**
	 * 桌面管理_更改桌面坑位比例大小或者颜色与透明度
	 * post {"desktopIDLists":["test","test1"],"bg":"3300ACFF"}
	 * @return [type] [description]
	 */
	/*public function modifyDesktopRatio()
	{

		$put = I('put.');

		if (!empty($put['desktopID'])&&!empty($put['number'])) {
			$desktopScreensSql = D('DesktopScreens','Desktop')->field("id")->where("desktop_id IN (".$put['desktopID'].")")->select(false);
			$desktopBlocks = D('DesktopBlocks','Desktop')->where("screen_id IN (".$desktopScreensSql.")")->select();
			foreach ($desktopBlocks as  $value) {
				$options = array(
					'width'=>ceil($value['w']* $put['number']),
					'height'=>ceil($value['h']* $put['number']),
					'yw'=>ceil($value['yw']* $put['number']),
					'yh'=>ceil($value['yh']* $put['number']),
				);
				D('DesktopBlocks','Desktop')->where("id = %d",array($value['id']))->save($options);
			}
		}elseif (!empty($put['desktopIDLists'])&&!empty($put['bg'])&&is_array($put['desktopIDLists'])) {

			$desktopScreensSql  = "'".implode("','",$put['desktopIDLists'])."'";

			$desktopScreensSql  = D('Desktop','Desktop')->field("id")->where("name IN (".$desktopScreensSql.")")->select(false);
			$desktopScreensSql = D('DesktopScreens','Desktop')->field("id")->where("desktop_id IN (".$desktopScreensSql.")")->select(false);
			$options = array(
				'bg'=>'#' . $put['bg'],
			);
			$desktopBlocks = D('DesktopBlocks','Desktop')->where("screen_id IN (".$desktopScreensSql.")")->save($options);


		}elseif (!empty($put['desktopLike'])&&!empty($put['bg'])) {
			$desktopScreensSql = $put['desktopLike'];
			$desktopScreensSql  = D('Desktop','Desktop')->field("id")->where(" binary name LIKE ".$desktopScreensSql)->select(false);
			$desktopScreensSql = D('DesktopScreens','Desktop')->field("id")->where("desktop_id IN (".$desktopScreensSql.")")->select(false);
			$options = array(
				'bg'=>'#' . $put['bg'],
			);
			$desktopBlocks = D('DesktopBlocks','Desktop')->where("screen_id IN (".$desktopScreensSql.")")->save($options);
		}
		result();
	}*/
	/**
	 * 桌面管理_同步桌面是否越狱
	 * @return [type] [description]
	 */
	/*public function breakoutDesktopSync()
	{
		$desktop = D('Desktop','Desktop');
		$desktopLists= $desktop->field('id')->where("name like '%_C'")->select();
		if ($desktopLists) {
			$options = array(
				'breakout'=>'true'
			);
			foreach ($desktopLists as $value) {
				$arr[] = $value['id'];
			}
			$arr = implode(',', $arr);
			$desktop->alias(' as b')->where("b.id IN (".$arr.")")->save($options);
		}

	}*/

	/**
	 * 桌面管理_运营坑位同步越狱前后
	 * [OperationSlotSync description]
	 */

	/*public function OperationSlotSync()
	{
		$count = D('OperationSlot','Desktop')->count();
		$breakoutTrue = D('OperationSlot','Desktop')->operationSlotLists(null,null,1,$count,null,'true');
		$breakoutFalse = D('OperationSlot','Desktop')->operationSlotLists(null,null,1,$count,null,'false');
		if (empty($breakoutTrue['extra'])||empty($breakoutFalse['extra'])) {
			if (!empty($breakoutTrue['extra'])) {
				foreach ($breakoutTrue['extra'] as $key => $value) {
					$value['breakout'] = 'false';
					$this->addOperationSlot($value);
				}
			}else{
				foreach ($breakoutFalse['extra'] as $key => $value) {
					$value['breakout'] = 'true';
					$this->addOperationSlot($value);
				}
			}
		}

	}*/
	/**
	 * 桌面管理_跳转信息同步到第三方应用
	 * [appSync description]
	 * @return [type] [description]
	 */
	/*public function appSync()
	{
		$arr = D('ActionApp','Desktop')->field('`app_name`,`pkgname`')->select();

		$appArr = array();
		foreach ($arr as $value) {
			$appArr[] = D('App3rd','App')->create($value);
		}
		if (!D('App3rd','App')->find()) {
			D('App3rd','App')->addAll($appArr);
			result();
		}else{

			result('已同步');
		}


	}*/
	/**
	 * 桌面管理_发布数据库全部桌面命令
	 * @return [type] [description]
	 */
	/*public function publishDesktopCmd()
	{
		$arr = D('DesktopCmd','Desktop')->select();
		set_time_limit(0);
		foreach ($arr as  $value) {
			$value['snList'] = json_decode($value['snList'],true);
			if (empty($value['snList'])) {
				$value['snList'] = array();
			}
			$value['cmd'] = json_decode($value['cmd'],true);
			if (empty($value['cmd'])) {
				$value['cmd'] = array();
			}
			$value['blackList'] = json_decode($value['blackList'],true);
			if (empty($value['blackList'])) {
				$value['blackList'] = array();
			}
			$value['whiteList'] = json_decode($value['whiteList'],true);
			if (empty($value['whiteList'])) {
				$value['whiteList'] = array();
			}
			//桌面旧接口发布 start

			$data = array(
				'id'=>$value['id'],
				'item'=>'desktopCMD',
				'target'=>array(
					'model'=>$value['model'],
					'type'=>$value['type'],
					"vendorID"=>$value['vendorID'],
				),
				'content'=>array(
					'cmd'=>$value['cmd'],
					'version'=>$value['version'],
					'pub_range'=>$value['pub_range'],
					'blackList'=>$value['blackList'],
					'whiteList'=>$value['whiteList'],
				)
			);
			if ($value['type']=='group') {
				$data['target']['snList'] = $value['snList'];
			}
			$data = json_encode($data,JSON_UNESCAPED_UNICODE);
			$json = postUrl(DESKTOP3_ACCESS_ADDR.'access/publish/add',$data);
			//桌面旧接口发布 end
		}

	}*/
	/**
	 * 桌面管理_发布数据库全部安卓固件
	 * @return [type] [description]
	 */
	/*public function FirmwareConfigGroupPublish()
	{
		$arr = D('FirmwareConfigGroupPublish','AndroidFirmware')->select();
		foreach ($arr as  $value) {
			$value['snList'] = json_decode($value['snList'],true);
			if (empty($value['snList'])) {
				$value['snList'] = array();
			}
			$data = array(
				'id'=>$value['id'],
				'item'=>'firmwareConfig',
				'target'=>array(
					'model'=>$value['model'],
					'type'=>$value['type'],
					'vendorID'=>$value['vendorID'],
					'snList'=>$value['snList']
				),
				'content'=>array(
					'url'=>$value['url'],
					'version'=>$value['version'],
					'md5'=>$value['md5'],
				)
			);
			$data = json_encode($data,JSON_UNESCAPED_UNICODE);
			$json = postUrl(ANDROID_FIRMWARE_ACCESS_ADDR.'access/publish/add',$data);
		}
	}*/
	/**
	 * 桌面管理_发布数据库全部桌面密码
	 * @return [type] [description]
	 */
	/*public function publishAllJbkPasswd()
	{
		$arr = D('DesktopJbkPasswd','Desktop')->field("`vendorid`,`passwd`")->select();
		foreach ($arr as  $value) {

			$data = array(
				'vendorID'=>$value['vendorid'],
				'passwd'=>$value['passwd'],
				'event'=>'add'
			);

			$data = json_encode($data,JSON_UNESCAPED_UNICODE);

			$json = postUrl(DESKTOP3_JBK_PASSWD_ACCESS_ADDR.'jbkPasswdPub',$data);
		}
	}*/
	/**
	 * 桌面管理_发布数据库全部桌面映射
	 * @return [type] [description]
	 */
	/*public function publishAllMap()
	{
		$arr = D('DesktopMap','Desktop')->field("`vendorid`,`desktop2`,`desktop3`")->select();
		foreach ($arr as  $value) {
			$options = array(
				'desktop2'=>$value['desktop2'],
				'desktop3'=>$value['desktop3'],
				'event'=>'add'
			);
			if (!empty($value['vendorID'])) {
				$options['vendorID'] = $value['vendorID'];
			}
			$data = json_encode($options,JSON_UNESCAPED_UNICODE);

			postUrl(DESKTOP3_JBK_PASSWD_ACCESS_ADDR.'desktopMapPub',$data);
		}
	}*/
	//旧桌面应用数据添加到新桌面
	/*public function importAppUpdate()
	{
		//实例化
		$appUpdatePublish = M('ApkRelease','tb_',"mysql://".DB_USER.":".DB_PWD."@".DB_HOST.":".DB_PORT."/db_access_configure#utf8");
		$appUpdateListsM = M('ApkList','tb_',"mysql://".DB_USER.":".DB_PWD."@".DB_HOST.":".DB_PORT."/db_apk_configure#utf8");
		$appUpdateReleaseListsM = M('ReleaseList','tb_',"mysql://".DB_USER.":".DB_PWD."@".DB_HOST.":".DB_PORT."/db_apk_configure#utf8");
		$appUpdateVersionListsM = M('VersionList','tb_',"mysql://".DB_USER.":".DB_PWD."@".DB_HOST.":".DB_PORT."/db_apk_configure#utf8");

		//查出来
		//发布版本
		$appUpdateArr = $appUpdatePublish->where('f_type = "%s" and f_valid = %d and f_approve = %d',array('all',1,1))->select();

		foreach ($appUpdateArr as  $value) {

			//APK
			$appUpdateLists = $appUpdateListsM->where("f_packname = '%s' and  f_channel = '%s'",array($value['f_item'],$value['f_channel']))->find();
			//APK版本
			$appUpdateVersionLists = $appUpdateVersionListsM->where('f_apk_id=%d and f_version = "%s"',array($appUpdateLists['f_id'],$value['f_version_code']))->find();
			//APK版本生成
			$appUpdateReleaseLists = $appUpdateReleaseListsM->where('f_version_id = %d',array($appUpdateVersionLists['f_id']))->order('f_id desc')->find();

			$options[] = array(
				"app_name"=>$value['f_name'],
				"pkgname"=>$value['f_item'],
				"channel"=>$value['f_channel'],
				"version_code"=>$value['f_version_code'],
				"version_name"=>$appUpdateVersionLists['f_version_name'],
				"path"=>'http://cdn.linkinme.com/copy'.$appUpdateVersionLists['f_path'],
				"md5"=>$appUpdateVersionLists['f_md5'],
				"desc"=>'["'.$appUpdateReleaseLists['f_desc'].'"]',
				"black_list"=>'[]',
				"white_list"=>'[]',
				"model"=>$value['f_model'],
				"vendorid"=>$value['f_vendor_id'],
				"umeng"=>$value['f_umeng'] == '0' ?'false':'true',
				"show_tips"=>'true',
				"mini_force_update_version"=>$appUpdateReleaseLists['f_mini_force_update_version'],
				"mini_update_version"=>$appUpdateReleaseLists['f_mini_update_version'],
				"fake"=>'false',
				"type"=>'ALL',
				'time'=>strtotime($value['f_time']),
			);

		}
		D('AppUpdatePublish','App')->addAll($options);
	}*/
}