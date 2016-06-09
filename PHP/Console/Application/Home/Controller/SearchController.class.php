<?php
namespace Home\Controller;
use Think\Controller;

class SearchController extends Controller {

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
    		die;
    	}
    	/**
    	*   全搜索--添加热词配置
    	*   post  /search/addHotConfig
       	*
       	* {
       	* 	"publish":"发布时间",
       	* 	"words":[    //热词内容，可为空
       	* 		{
       	* 			"keys": [
       	* 				"澳门风云3",
       	* 				"叶问3",
       	* 				"美人鱼" //数组 最多只能6个
       	* 				]
       	* 		}
		]
       	* }
         	* @return [type] [description]
         	*/
    	public function addHotConfig()
    	{
    		$put = I('put.');
    		D('VodRecommendWords','Vod')->addHotConfig($put);
    		result();
    	}


    	/**
    	*   全搜索--修改热词配置
    	*   post  /search/modifyHotConfig
       	*
       	* {
       	* 	"id":"版本ID",
       	* 	"publish":"发布时间",
       	* 	"words":[    //热词内容，可为空
       	* 		{
       	* 			"keys": [
       	* 				"澳门风云3",
       	* 				"叶问3",
       	* 				"美人鱼" //数组 最多只能6个
       	* 				]
       	* 		}
		]
       	* }
         	* @return [type] [description]
         	*/
    	public function modifyHotConfig()
    	{
    		$put = I('put.');
    		D('VodRecommendWords','Vod')->modifyHotConfig($put);
    		result();
    	}
    	/**
    	*   全搜索--删除热词配置
    	*   get  /search/addHotConfig?id=x
       	*
         	* @return [type] [description]
         	*/
    	public function deleteHotConfig()
    	{
    		$get = I('get.');
    		D('VodRecommendWords','Vod')->deleteHotConfig($get);
    		result();
    	}

    	/**
    	*   全搜索--复制热词配置
    	*   post  /search/copyHotConfig
       	*   {
       	*   	"copyId":"1",
       	*   	"copyToIds":["1"]
       	*   }
         	* @return [type] [description]
         	*/
    	public function copyHotConfig()
    	{
    		$put = I('put.');
    		D('VodRecommendWords','Vod')->copyHotConfig($put);
    		result();
    	}
    	/**
    	*   全搜索--热词配置列表
    	*   get  /search/hotConfigLists?id=x&name=x&page=x&pageSize=x
       	*
         	* @return [type] [description]
         	*
         	*/
    	public function hotConfigLists()
    	{
    		$get = I('get.');
    		$res = D('VodRecommendWords','Vod')->hotConfigLists($get);
    		result(true,$res);
    	}
    	/**
    	 * [getHotName 获取热词]
    	 *  get  /search/getHotName?id=x&name=x&page=x&pageSize=x
    	 * @return [type] [description]
    	 */
    	public function getHotName()
    	{
    		$get = I('get.');
    		$res = D('VodProgram','Vod')->getHotName($get);
    		result(true,$res);
    	}
    	/**
    	*   全搜索--搜索排序列表
    	*   get  /search/getProgramLists?id=x&name=x&page=x&pageSize=x
       	*
         	* @return [type] [description]
         	*
         	*/
    	public function getProgramLists()
    	{
    		$get = I('get.');
    		$res = D('VodProgram','Vod')->getProgramLists($get);
    		result(true,$res);
    	}

    	/**
    	*   全搜索--修改搜索排序
    	*   post  /search/modifyProgramSort
       	*
       	* {
       	* 	"ids":["1","2"],
       	*  	"recommend":"0-100"
       	* }
         	* @return [type] [description]
         	*/
    	public function modifyProgramSort()
    	{
    		$put = I('put.');
    		D('VodProgramWeight','Vod')->modifyProgramSort($put);
    		result();
    	}
    	/**
    	 * [getAllProgram 获取所有节目名字]
    	 *  get /search/getAllProgram
    	 * @return [type] [description]
    	 */
    	public function getAllProgram()
    	{
    		$get = I('get.');
    		$res['extra']=D('VodProgram','Vod')->getAllName($get);
    		if (empty($res['extra'])) {
    			$res['extra'] = [];
    		}
    		result(true,$res);
    	}
    	/**
    	 * [getProgramApp 获取节目跳转App]
    	 * get /search/getProgramApp?id=节目id
    	 * @return [type] [description]
    	 */
    	public function getProgramApp()
    	{
    		$get = I('get.');
    		$res['extra'] = D('VodProgramSource','Vod')->getProgramApp($get);
    		if (empty($res['extra'])) {
    			$res['extra'] = [];
    		}
    		result(true,$res);
    	}
    	//------------------------------------------------------------------------------------------------------------------------------------------------------------
    	/**
    	*   全搜索--添加视频配置
    	*   post  /search/addVideosConfig
       	*
       	* {
       	* 	"publish":"发布时间"
       	* }
         	* @return [type] [description]
         	*/
    	public function addVideosConfig()
    	{
    		$put = I('put.');
    		D('VodRecommendVideos','Vod')->addVideosConfig($put);
    		result();
    	}


    	/**
    	*   全搜索--修改视频配置
    	*   post  /search/modifyVideosConfig
       	*
       	* {
       	* 	"id":"id"
       	* 	"publish":"发布时间",
       	* }
         	* @return [type] [description]
         	*/
    	public function modifyVideosConfig()
    	{
    		$put = I('put.');
    		D('VodRecommendVideos','Vod')->modifyVideosConfig($put);
    		result();
    	}
    	/**
    	*   全搜索--删除视频配置
    	*   get  /search/addHotConfig?id=x
       	*
         	* @return [type] [description]
         	*/
    	public function deleteVideosConfig()
    	{
    		$get = I('get.');
    		D('VodRecommendVideos','Vod')->deleteVideosConfig($get);
    		result();
    	}

    	/**
    	*   全搜索--视频配置列表
    	*   get  /search/hotConfigLists?id=x&name=x&page=x&pageSize=x
       	*
         	* @return [type] [description]
         	*
         	*/
    	public function VideosConfigLists()
    	{
    		$get = I('get.');
    		$res = D('VodRecommendVideos','Vod')->VideosConfigLists($get);
    		result(true,$res);
    	}
    	//---------------------------------------------------------------------------------------------------------------------------------------
    	/**
    	*   全搜索--添加视频属性配置
    	*   post  /search/addVideosConfigItems
       	*
       	* {
       	*
       	* 		"groupId":"视频组ID",
       	*
       	* 		get /search/getAllProgram     获取所有节目名字
       	*
       	*
       	*	            "videoId": 1,
       	*	            "videoName": "海贼王",
       	*	            "videoPicture": "http: //xx.jpg",
       	*	            "videoThumb": "http: //xx.jpg",
       	*                  	"videoType":"分类",
       	*
     			get /search/getProgramApp?id=节目id   获取节目跳转App

       	*	             "appId": 2,
       	*	             "appName": "电视猫",
       	*	             "appSkipid": "5603645b3977bd5ee34ee05f",
       	*	             "appPay": false
       	*
       	*
       	* }
         	* @return [type] [description]
         	*/
    	public function addVideosConfigItems()
    	{
    		$put = I('put.');
    		D('VodRecommendVideosItems','Vod')->addVideosConfigItems($put);
    		result();
    	}


    	/**
    	*   全搜索--修改视频属性配置
    	*   post  /search/modifyVideosConfigItems
       	*
       	* {
       	* 		"id":"视频属性ID",
       	* 		"groupId":"视频组ID",
       	*
       	* 		get /search/getAllProgram     获取所有节目名字
       	*
       	*
       	*	            "videoId": 1,
       	*	            "videoName": "海贼王",
       	*	            "videoPicture": "http: //xx.jpg",
       	*	            "videoThumb": "http: //xx.jpg",
       	*                  	"videoType":"分类",
       	*
     			get /search/getProgramApp?id=节目id   获取节目跳转App

       	*	             "appId": 2,
       	*	             "appName": "电视猫",
       	*	             "appSkipid": "5603645b3977bd5ee34ee05f",
       	*	             "appPay": false
       	*
       	*
       	* }
         	* @return [type] [description]
         	*/
    	public function modifyVideosConfigItems()
    	{
    		$put = I('put.');
    		D('VodRecommendVideosItems','Vod')->modifyVideosConfigItems($put);
    		result();
    	}
    	/**
    	*   全搜索--删除视频属性配置
    	*   get  /search/deleteVideosConfigItems?id=x
       	*
         	* @return [type] [description]
         	*/
    	public function deleteVideosConfigItems()
    	{
    		$get = I('get.');
    		D('VodRecommendVideosItems','Vod')->deleteVideosConfigItems($get);
    		result();
    	}

    	/**
    	*   全搜索--视频属性配置列表
    	*   get  /search/videosConfigItemsLists?groupId=组ID&name=x&page=x&pageSize=x
       	*
         	* @return [type] [description]
         	*
         	*/
    	public function videosConfigItemsLists()
    	{
    		$get = I('get.','','htmlspecialchars');

    		$res = D('VodRecommendVideosItems','Vod')->VideosConfigItemsLists($get);
    		result(true,$res);
    	}
    	/**
    	 * [checkVideoInvalid 全搜索--视频属性是否有效]
    	 * get  /search/checkVideoInvalid
    	 * @return [type] [description]
    	 */
    	public function checkVideoInvalid()
    	{
    		D('VodRecommendVideosItems','Vod')->checkVideoInvalid();
    		result();
    	}
    	/**
    	 * [copyVideosConfig 全搜索--复制视频]
    	 * post  /search/copyVideosConfig
    	 * {
    	 * 	"publish":"发布时间",
    	 * 	"copyVideosId":"复制的ID"
    	 * }
    	 * @return [type] [description]
    	 */
    	public function copyVideosConfig()
    	{
    		$put = I('put.');
    		$put['groupId'] = D('VodRecommendVideos','Vod')->copyVideosConfig($put);

    		$res = D('VodRecommendVideosItems','Vod')->copyVideosConfig($put);
    		result(true,$res);
    	}
}