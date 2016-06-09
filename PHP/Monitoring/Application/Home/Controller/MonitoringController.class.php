<?php
namespace Home\Controller;
use Think\Controller;

class MonitoringController extends Controller {

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
       	* 状态码统计
       	* get http://139.129.37.240:22222/Monitoring/home/Monitoring/getAdmin?startTime=1454032800（开始时间戳）
       	* &endTime=1454032800（结束时间戳）&interval=5(间隔分钟)&page=1&pageSize=10 & interface = (接口--可有可没有)&codeType=200(状态码)
         	* @return [type] [description]
         	*/
    	public function getAdmin()
    	{
    		$get = I('get.');
    		$res = D('InterfaceHistory','Monitoring')->getCodeTypeHistory($get);
    		result(true,$res);
    	}

    	/**
       	* 获取接口
       	* hhttp://139.129.37.240:22222/Monitoring/home/Monitoring/getInterface
         	* @return [type] [description]
         	*/
    	public function getInterface()
    	{
    		$res = D('InterfaceHistory','Monitoring')->getInterface();
    		result(true,$res);
    	}

    	/**
       	* 下载状态统计
       	* get /Monitoring/home/Monitoring/getDownloadHistory?startTime=1459501320&endTime=1459503120&interval=1&url=http://dlcdn.linkinme.com&codeType=2
         	* @return [type] [description]
         	*/
    	public function getDownloadHistory()
    	{
    		$get = I('get.');
    		$res = D('DownloadHistory','Monitoring')->getDownloadHistory($get);
    		result(true,$res);
    	}

    	/**
       	* 状态码统计
    	 * get /Monitoring/home/Monitoring/getLivewatchHistory?startTime=1459501320&endTime=1459503120&interval=1&channel=6513
       	*
         	* @return [type] [description]
         	*/
    	public function getLivewatchHistory()
    	{
    		$get = I('get.');
    		$res = D('LivewatchHistory','Monitoring')->getLivewatchHistory($get);
    		result(true,$res);
    	}

    	/**
       	* 获取频道ID接口
       	* http://192.168.1.199:180/Monitoring/home/Monitoring/getChannelID?id=1
         	* @return [type] [description]
         	*/
    	public function getChannelID()
    	{
    		$get = I('get.');
    		$res = D('LiveChannelName','Monitoring')->getChannelID($get);
    		result(true,$res);
    	}
    	/**
    	 * [addWarningGroup 添加告警组]
    	 * post /Monitoring/home/Monitoring/addWarningGroup
    	 *{
    	 *	"name":"名称"
    	 *}
    	 * @param string $value [description]
    	 */
    	public function addWarningGroup()
    	{
    		$put = I('put.');
    		D('WarningGroup','Monitoring')->addWarningGroup($put);
    		result();
    	}
    	/**
    	 * [modifyWarningGroup 修改告警组]
    	 * post /Monitoring/home/Monitoring/modifyWarningGroup
    	 *{
    	 *	"id":"告警组ID",
    	 *	"name":"名称"
    	 *}
    	 * @param string $value [description]
    	 */
    	public function modifyWarningGroup()
    	{
    		$put = I('put.');
    		D('WarningGroup','Monitoring')->modifyWarningGroup($put);
    		result();
    	}
    	/**
    	 * [deleteWarningGroup 删除告警组]
    	 * get /Monitoring/home/Monitoring/deleteWarningGroup?id=x
    	 *
    	 * @param string $value [description]
    	 */
    	public function deleteWarningGroup()
    	{
    		$get = I('get.');
    		D('WarningGroup','Monitoring')->deleteWarningGroup($get);
    		result();
    	}
    	/**
    	 * [warningGroupLists 告警组列表]
    	 * get /Monitoring/home/Monitoring/warningGroupLists?id=x&page=x&pageSize=x&name=x
    	 *
    	 * @param string $value [description]
    	 */
    	public function warningGroupLists()
    	{
    		$get = I('get.');
    		$res = D('WarningGroup','Monitoring')->warningGroupLists($get);
    		result(true,$res);
    	}

    	/**
    	 * [addWarningGroupItems 添加告警组成员]
    	 * post /Monitoring/home/Monitoring/addWarningGroupItems
    	 *{
    	 *	"userIdList":["用户ID"],
    	 *	"groupId":"告警组ID"
    	 *}
    	 * @param string $value [description]
    	 */
    	public function addWarningGroupItems()
    	{
    		$put = I('put.');
    		D('WarningGroupItems','Monitoring')->addWarningGroupItems($put);
    		result();
    	}

    	/**
    	 * [deleteWarningGroupItems 删除告警组成员]
    	 * post /Monitoring/home/Monitoring/deleteWarningGroupItems
    	 * ["id1","id2"]
    	 * @param string $value [description]
    	 */
    	public function deleteWarningGroupItems()
    	{
    		$put = I('put.');
    		D('WarningGroupItems','Monitoring')->deleteWarningGroupItems($put);
    		result();
    	}
    	/**
    	 * [warningGroupItemsLists 告警组成员列表]
    	 * get /Monitoring/home/Monitoring/warningGroupItemsLists?groupId=告警组ID(必填)&page=x&pageSize=x&name=x
    	 *
    	 * @param string $value [description]
    	 */
    	public function warningGroupItemsLists()
    	{
    		$get = I('get.');
    		$res = D('WarningGroupItems','Monitoring')->warningGroupItemsLists($get);
    		result(true,$res);
    	}
    	/**
    	 * [channelNameSnyc 同步频道名称]
    	 * get /Monitoring/home/Monitoring/channelNameSnyc
    	 *
    	 * @return [type] [description]
    	 */
    	public function channelNameSnyc()
    	{
    		//实例化
    		$liveChannelName = D('LiveChannelName','Monitoring');


    		if ($res = $liveChannelName->find()) {
    			die;
    		}
    		$json = '[{"name":"贵州","list":[{"id":"0","name":"遵义县频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"1","name":"CCTV1综合频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"2","name":"CCTV2财经频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"3","name":"CCTV3综艺频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"4","name":"CCTV4中文国际"}]},';
    		$json .= '{"name":"央视","list":[{"id":"5","name":"CCTV5体育频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"6","name":"CCTV6电影频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"7","name":"CCTV7军事农业频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"8","name":"CCTV8电视剧频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"9","name":"CCTV9纪录频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"10","name":"CCTV10科教频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"11","name":"CCTV11戏曲频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"12","name":"CCTV12社会与法频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"13","name":"CCTV13新闻频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"14","name":"CCTV14少儿频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"15","name":"CCTV15音乐频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"16","name":"CCTV世界地理频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"18","name":"CCTV怀旧剧场频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"19","name":"CCTV风云音乐频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"20","name":"CCTV国防军事频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"21","name":"CCTV证券资讯频道"}]},';
    		$json .= '{"name":"央视","list":[{"id":"22","name":"CCTV中学生"}]},';
    		$json .= '{"name":"央视","list":[{"id":"26","name":"CCTV第一剧场频道"}]},';
    		$json .= '{"name":"卫视","list":[{"id":"49","name":"环球购物"}]},';
    		$json .= '{"name":"卫视","list":[{"id":"50","name":"湖南卫视"}]},';
    		$json .= '{"name":"卫视","list":[{"id":"51","name":"浙江卫视"}]},';
    		$json .= '{"name":"卫视","list":[{"id":"52","name":"江苏卫视"}]},';
    		$json .= '{"name":"卫视","list":[{"id":"53","name":"东方卫视"}]},';
    		$json .= '{"name":"卫视","list":[{"id":"54","name":"广东卫视"}]},{"name":"卫视","list":[{"id":"55","name":"深圳卫视"}]},{"name":"卫视","list":[{"id":"56","name":"北京卫视"}]},{"name":"卫视","list":[{"id":"57","name":"湖北卫视"}]},{"name":"卫视","list":[{"id":"58","name":"安徽卫视"}]},{"name":"卫视","list":[{"id":"59","name":"天津卫视"}]},{"name":"卫视","list":[{"id":"60","name":"山东卫视"}]},{"name":"卫视","list":[{"id":"61","name":"辽宁卫视"}]},{"name":"卫视","list":[{"id":"62","name":"云南卫视"}]},{"name":"卫视","list":[{"id":"63","name":"黑龙江卫视"}]},{"name":"卫视","list":[{"id":"64","name":"贵州卫视"}]},{"name":"卫视","list":[{"id":"65","name":"东南卫视"}]},{"name":"卫视","list":[{"id":"66","name":"河北卫视"}]},{"name":"卫视","list":[{"id":"67","name":"山西卫视"}]},{"name":"卫视","list":[{"id":"68","name":"重庆卫视"}]},{"name":"卫视","list":[{"id":"69","name":"四川卫视"}]},{"name":"卫视","list":[{"id":"70","name":"吉林卫视"}]},{"name":"卫视","list":[{"id":"71","name":"青海卫视"}]},{"name":"卫视","list":[{"id":"72","name":"甘肃卫视"}]},{"name":"卫视","list":[{"id":"73","name":"江西卫视"}]},{"name":"卫视","list":[{"id":"74","name":"广西卫视"}]},{"name":"卫视","list":[{"id":"75","name":"河南卫视"}]},{"name":"卫视","list":[{"id":"76","name":"西藏卫视"}]},{"name":"卫视","list":[{"id":"77","name":"陕西卫视"}]},{"name":"卫视","list":[{"id":"78","name":"宁夏卫视"}]},{"name":"卫视","list":[{"id":"79","name":"新疆卫视"}]},{"name":"卫视","list":[{"id":"80","name":"内蒙古卫视"}]},{"name":"卫视","list":[{"id":"81","name":"旅游卫视"}]},{"name":"卫视","list":[{"id":"82","name":"海峡卫视"}]},{"name":"卫视","list":[{"id":"83","name":"厦门卫视"}]},{"name":"卫视","list":[{"id":"84","name":"康巴卫视"}]},{"name":"高清","list":[{"id":"100","name":"CCTV1综合频道HD"}]},{"name":"高清","list":[{"id":"102","name":"CCTV3综艺频道HD"}]},{"name":"高清","list":[{"id":"104","name":"CCTV5体育频道HD"}]},{"name":"高清","list":[{"id":"105","name":"CCTV5+体育频道HD"}]},{"name":"高清","list":[{"id":"106","name":"CCTV6电影频道HD"}]},{"name":"高清","list":[{"id":"108","name":"CCTV8电视剧频道HD"}]},{"name":"高清","list":[{"id":"111","name":"湖南卫视HD"}]},{"name":"高清","list":[{"id":"112","name":"浙江卫视HD"}]},{"name":"高清","list":[{"id":"113","name":"江苏卫视HD"}]},{"name":"高清","list":[{"id":"114","name":"东方卫视HD"}]},{"name":"高清","list":[{"id":"115","name":"广东卫视HD"}]},{"name":"高清","list":[{"id":"116","name":"深圳卫视HD"}]},{"name":"高清","list":[{"id":"117","name":"北京卫视HD"}]},{"name":"高清","list":[{"id":"118","name":"湖北卫视HD"}]},{"name":"高清","list":[{"id":"120","name":"天津卫视HD"}]},{"name":"高清","list":[{"id":"121","name":"山东卫视HD"}]},{"name":"高清","list":[{"id":"124","name":"黑龙江卫视HD"}]},{"name":"高清","list":[{"id":"317","name":"北京纪实频道HD"}]},{"name":"高清","list":[{"id":"318","name":"北京文艺频道HD"}]},{"name":"高清","list":[{"id":"319","name":"北京体育频道HD"}]},{"name":"特色","list":[{"id":"406","name":"车迷频道频道"}]},{"name":"特色","list":[{"id":"415","name":"央视娱乐国际"}]},{"name":"特色","list":[{"id":"416","name":"快乐垂钓频道"}]},{"name":"特色","list":[{"id":"430","name":"CBN第一财经"}]},{"name":"特色","list":[{"id":"436","name":"上海炫动卡通"}]},{"name":"特色","list":[{"id":"441","name":"四海钓鱼频道"}]},{"name":"特色","list":[{"id":"450","name":"看看新闻频道"}]},{"name":"特色","list":[{"id":"455","name":"CETV中国教育1"}]},{"name":"特色","list":[{"id":"456","name":"CETV中国教育2"}]},{"name":"特色","list":[{"id":"457","name":"CETV中国教育3"}]},{"name":"特色","list":[{"id":"458","name":"CETV中国教育4"}]},{"name":"特色","list":[{"id":"459","name":"空中课堂频道"}]},{"name":"特色","list":[{"id":"463","name":"天元围棋"}]},{"name":"特色","list":[{"id":"467","name":"茶频道"}]},';
    		$json .= '{"name":"特色","list":[{"id":"468","name":"CEN城市频道"}]},{"name":"特色","list":[{"id":"469","name":"全纪实频道"}]},{"name":"少儿","list":[{"id":"600","name":"广州少儿频道"}]},{"name":"少儿","list":[{"id":"601","name":"深圳少儿频道"}]},{"name":"少儿","list":[{"id":"603","name":"山东少儿频道"}]},{"name":"少儿","list":[{"id":"604","name":"南京少儿频道"}]},{"name":"少儿","list":[{"id":"606","name":"南方少儿频道"}]},{"name":"少儿","list":[{"id":"607","name":"哈哈少儿频道"}]},{"name":"少儿","list":[{"id":"608","name":"卡酷少儿频道"}]},{"name":"少儿","list":[{"id":"610","name":"山西少儿频道"}]},{"name":"少儿","list":[{"id":"612","name":"江西少儿家庭"}]},{"name":"少儿","list":[{"id":"613","name":"河北少儿科教"}]},{"name":"少儿","list":[{"id":"614","name":"济南少儿频道"}]},{"name":"少儿","list":[{"id":"615","name":"甘肃少儿频道"}]},{"name":"少儿","list":[{"id":"616","name":"福建少儿频道"}]},{"name":"少儿","list":[{"id":"617","name":"重庆少儿频道"}]},{"name":"少儿","list":[{"id":"619","name":"黑龙江少儿频道"}]},{"name":"少儿","list":[{"id":"620","name":"广东嘉佳卡通"}]},{"name":"少儿","list":[{"id":"621","name":"CCTV新科动漫"}]},{"name":"少儿","list":[{"id":"622","name":"金鹰卡通频道"}]},{"name":"少儿","list":[{"id":"624","name":"杭州少儿频道"}]},{"name":"少儿","list":[{"id":"625","name":"浙江少儿频道"}]},{"name":"体育","list":[{"id":"650","name":"CCTV5+"}]},{"name":"体育","list":[{"id":"651","name":"CCTV台球频道"}]},{"name":"体育","list":[{"id":"652","name":"CCTV风云足球"}]},{"name":"体育","list":[{"id":"653","name":"CCTV高尔夫网球"}]},{"name":"体育","list":[{"id":"657","name":"北京体育频道"}]},{"name":"体育","list":[{"id":"658","name":"上海五星体育"}]},{"name":"体育","list":[{"id":"661","name":"福建体育频道"}]},{"name":"体育","list":[{"id":"668","name":"广东体育频道"}]},{"name":"体育","list":[{"id":"669","name":"陕西体育休闲"}]},{"name":"体育","list":[{"id":"670","name":"山东体育频道"}]},{"name":"体育","list":[{"id":"675","name":"湖北体育生活"}]},{"name":"体育","list":[{"id":"677","name":"高尔夫频道"}]},{"name":"体育","list":[{"id":"679","name":"辽宁电子体育"}]},{"name":"体育","list":[{"id":"680","name":"辽宁体育频道"}]},{"name":"电竞","list":[{"id":"752","name":"GTV游戏竞技"}]},{"name":"电竞","list":[{"id":"753","name":"SiTV游戏风云"}]},{"name":"广东","list":[{"id":"800","name":"广东新闻频道"}]},{"name":"广东","list":[{"id":"801","name":"广东珠江频道"}]},{"name":"广东","list":[{"id":"802","name":"广东公共频道"}]},{"name":"广东","list":[{"id":"803","name":"广东国际频道"}]},{"name":"广东","list":[{"id":"805","name":"广东会展频道"}]},{"name":"广东","list":[{"id":"809","name":"广东英语辅导"}]},{"name":"广东","list":[{"id":"811","name":"广东国际美洲"}]},{"name":"广东","list":[{"id":"812","name":"广东房产频道"}]},{"name":"广东","list":[{"id":"813","name":"珠江高清频道"}]},{"name":"广东","list":[{"id":"814","name":"广州综合频道"}]},{"name":"广东","list":[{"id":"815","name":"广州影视频道"}]},{"name":"广东","list":[{"id":"816","name":"广州经济频道"}]},';
    		$json .= '{"name":"广东","list":[{"id":"817","name":"广州生活频道"}]},{"name":"广东","list":[{"id":"819","name":"广州新闻频道"}]},{"name":"广东","list":[{"id":"821","name":"睛彩广州频道"}]},{"name":"广东","list":[{"id":"822","name":"南方卫视省内版"}]},{"name":"广东","list":[{"id":"823","name":"南方综艺频道"}]},{"name":"广东","list":[{"id":"824","name":"南方卫视频道"}]},{"name":"广东","list":[{"id":"825","name":"南方影视频道"}]},{"name":"广东","list":[{"id":"826","name":"南方经视频道"}]},{"name":"广东","list":[{"id":"827","name":"南方经济频道"}]},{"name":"广东","list":[{"id":"828","name":"深圳财经生活"}]},{"name":"广东","list":[{"id":"829","name":"深圳电视剧"}]},{"name":"广东","list":[{"id":"830","name":"深圳娱乐频道"}]},{"name":"广东","list":[{"id":"835","name":"深圳都市频道"}]},{"name":"广东","list":[{"id":"840","name":"深圳体育健康"}]},{"name":"广东","list":[{"id":"841","name":"蛇口电视台"}]},{"name":"广东","list":[{"id":"844","name":"韶关综合频道"}]},{"name":"广东","list":[{"id":"845","name":"韶关经济生活"}]},{"name":"广东","list":[{"id":"849","name":"惠州一套"}]},{"name":"广东","list":[{"id":"850","name":"惠州二套"}]},{"name":"广东","list":[{"id":"851","name":"珠海一套"}]},{"name":"广东","list":[{"id":"852","name":"珠海二套"}]},{"name":"广东","list":[{"id":"855","name":"台山新闻综合"}]},{"name":"广东","list":[{"id":"856","name":"河源综合频道"}]},{"name":"广东","list":[{"id":"857","name":"河源公共频道"}]},{"name":"广东","list":[{"id":"858","name":"佛山公共频道"}]},{"name":"广东","list":[{"id":"859","name":"肇庆综合频道"}]},{"name":"广东","list":[{"id":"861","name":"汕头影视文艺"}]},{"name":"广东","list":[{"id":"862","name":"汕头经济生活"}]},{"name":"广东","list":[{"id":"863","name":"汕头新闻综合"}]},{"name":"广东","list":[{"id":"866","name":"潮州综合频道"}]},{"name":"广东","list":[{"id":"867","name":"潮州公共频道"}]},{"name":"广东","list":[{"id":"868","name":"揭阳综合频道"}]},{"name":"广东","list":[{"id":"869","name":"揭阳公共频道"}]},';
    		$json .= '{"name":"广东","list":[{"id":"870","name":"清远公共频道"}]},{"name":"广东","list":[{"id":"871","name":"清远综合频道"}]},{"name":"广东","list":[{"id":"876","name":"茂名综合频道"}]},{"name":"广东","list":[{"id":"879","name":"江门综合频道"}]},{"name":"广东","list":[{"id":"880","name":"江门公共频道"}]},{"name":"广东","list":[{"id":"881","name":"佛山新闻综合"}]},{"name":"广东","list":[{"id":"882","name":"紫金电视台"}]},{"name":"广东","list":[{"id":"883","name":"鹤山综合频道"}]},{"name":"广东","list":[{"id":"885","name":"梅州一套"}]},{"name":"广东","list":[{"id":"888","name":"潮州综合频道"}]},{"name":"广东","list":[{"id":"889","name":"潮州公共频道"}]},{"name":"江苏","list":[{"id":"950","name":"江苏国际频道"}]},{"name":"江苏","list":[{"id":"951","name":"江苏城市频道"}]},{"name":"江苏","list":[{"id":"952","name":"江苏影视频道"}]},{"name":"江苏","list":[{"id":"955","name":"江苏靓妆频道"}]},{"name":"江苏","list":[{"id":"956","name":"江苏休闲体育"}]},{"name":"江苏","list":[{"id":"957","name":"江苏教育频道"}]},{"name":"江苏","list":[{"id":"958","name":"江苏好享购物"}]},{"name":"江苏","list":[{"id":"959","name":"江苏优漫卡通"}]},{"name":"江苏","list":[{"id":"960","name":"江苏公共频道"}]},{"name":"江苏","list":[{"id":"963","name":"江苏综艺频道"}]},{"name":"江苏","list":[{"id":"964","name":"江苏财富天下"}]},{"name":"江苏","list":[{"id":"965","name":"江苏体育休闲"}]},{"name":"江苏","list":[{"id":"968","name":"南京新闻综合"}]},{"name":"江苏","list":[{"id":"969","name":"南京信息频道"}]},{"name":"江苏","list":[{"id":"970","name":"南京十八频道"}]},{"name":"江苏","list":[{"id":"971","name":"南京娱乐频道"}]},{"name":"江苏","list":[{"id":"972","name":"南京教科频道"}]},{"name":"江苏","list":[{"id":"973","name":"南京生活频道"}]},{"name":"江苏","list":[{"id":"974","name":"南京影视频道"}]},{"name":"江苏","list":[{"id":"975","name":"南通明珠频道"}]},{"name":"江苏","list":[{"id":"977","name":"南通社教频道"}]},{"name":"江苏","list":[{"id":"978","name":"南通信息频道"}]},{"name":"江苏","list":[{"id":"979","name":"南通新闻综合"}]},{"name":"江苏","list":[{"id":"980","name":"如东影视频道"}]},{"name":"江苏","list":[{"id":"981","name":"如东生活服务"}]},{"name":"江苏","list":[{"id":"982","name":"如东新闻综合"}]},{"name":"江苏","list":[{"id":"985","name":"常州新闻综合"}]},{"name":"江苏","list":[{"id":"986","name":"常州生活频道"}]},{"name":"江苏","list":[{"id":"988","name":"徐州公共频道"}]},{"name":"江苏","list":[{"id":"989","name":"徐州经济生活"}]},{"name":"江苏","list":[{"id":"990","name":"徐州新闻综合"}]},{"name":"江苏","list":[{"id":"991","name":"徐州文艺影视"}]},{"name":"江苏","list":[{"id":"992","name":"扬州新闻综合"}]},{"name":"江苏","list":[{"id":"996","name":"扬州城市频道"}]},{"name":"江苏","list":[{"id":"997","name":"泰州影视娱乐"}]},{"name":"江苏","list":[{"id":"998","name":"泰州经济生活"}]},{"name":"江苏","list":[{"id":"1000","name":"泰州新闻综合"}]},{"name":"江苏","list":[{"id":"1001","name":"苏州文化生活"}]},{"name":"江苏","list":[{"id":"1002","name":"苏州新闻综合"}]},{"name":"江苏","list":[{"id":"1003","name":"苏州社会经济"}]},{"name":"江苏","list":[{"id":"1004","name":"苏州生活资讯"}]},{"name":"江苏","list":[{"id":"1005","name":"盐城电视一套"}]},{"name":"江苏","list":[{"id":"1006","name":"盐城电视二套"}]},{"name":"江苏","list":[{"id":"1007","name":"盐城电视三套"}]},{"name":"江苏","list":[{"id":"1008","name":"盐城七一健康"}]},{"name":"江苏","list":[{"id":"1013","name":"无锡新闻综合"}]},{"name":"江苏","list":[{"id":"1014","name":"镇江城市资讯"}]},{"name":"江苏","list":[{"id":"1015","name":"镇江民生频道"}]},{"name":"江苏","list":[{"id":"1016","name":"镇江新闻综合"}]},{"name":"江苏","list":[{"id":"1021","name":"昆山新闻综合"}]},{"name":"江苏","list":[{"id":"1022","name":"淮安新闻综合"}]},{"name":"江苏","list":[{"id":"1027","name":"连云港新闻综合"}]},{"name":"江苏","list":[{"id":"1028","name":"连云港城市公共"}]},{"name":"江苏","list":[{"id":"1037","name":"邗江电视台"}]},{"name":"江苏","list":[{"id":"1038","name":"海门经济生活"}]},';
    		$json .= '{"name":"江苏","list":[{"id":"1039","name":"大丰新闻综合"}]},{"name":"江苏","list":[{"id":"1041","name":"张家港新闻综合"}]},{"name":"江苏","list":[{"id":"1045","name":"武进人文频道"}]},{"name":"江苏","list":[{"id":"1046","name":"武进生活频道"}]},{"name":"江苏","list":[{"id":"1047","name":"武进影视频道"}]},{"name":"江苏","list":[{"id":"1048","name":"太仓党建频道"}]},{"name":"江苏","list":[{"id":"1049","name":"太仓娄东民生"}]},{"name":"江苏","list":[{"id":"1050","name":"太仓新闻综合"}]},{"name":"江苏","list":[{"id":"1051","name":"吴江电视台"}]},{"name":"浙江","list":[{"id":"1150","name":"浙江第六频道"}]},{"name":"浙江","list":[{"id":"1151","name":"浙江钱江频道"}]},{"name":"浙江","list":[{"id":"1152","name":"浙江经视频道"}]},{"name":"浙江","list":[{"id":"1153","name":"浙江科技教育"}]},{"name":"浙江","list":[{"id":"1154","name":"浙江民生频道"}]},{"name":"浙江","list":[{"id":"1155","name":"浙江公共频道"}]},{"name":"浙江","list":[{"id":"1156","name":"浙江国际频道"}]},{"name":"浙江","list":[{"id":"1157","name":"浙江教育科技"}]},{"name":"浙江","list":[{"id":"1158","name":"浙江留学世界"}]},{"name":"浙江","list":[{"id":"1159","name":"浙江影视频道"}]},{"name":"浙江","list":[{"id":"1162","name":"浙江公共新农村"}]},{"name":"浙江","list":[{"id":"1165","name":"浙江网络电视台"}]},{"name":"浙江","list":[{"id":"1169","name":"杭州明珠频道"}]},{"name":"浙江","list":[{"id":"1171","name":"宁波社会生活"}]},{"name":"浙江","list":[{"id":"1172","name":"宁波新闻综合"}]},{"name":"浙江","list":[{"id":"1175","name":"宁波文化娱乐"}]},{"name":"浙江","list":[{"id":"1177","name":"绍兴影视娱乐"}]},{"name":"浙江","list":[{"id":"1179","name":"绍兴新闻综合"}]},{"name":"浙江","list":[{"id":"1180","name":"绍兴公共频道"}]},{"name":"浙江","list":[{"id":"1181","name":"绍兴文化影视"}]},{"name":"浙江","list":[{"id":"1182","name":"义乌商贸频道"}]},{"name":"浙江","list":[{"id":"1183","name":"义乌新闻综合"}]},{"name":"浙江","list":[{"id":"1184","name":"义乌公共文艺"}]},{"name":"浙江","list":[{"id":"1185","name":"台州公共财富"}]},{"name":"浙江","list":[{"id":"1187","name":"台州新闻综合"}]},{"name":"浙江","list":[{"id":"1189","name":"金华公共频道"}]},{"name":"浙江","list":[{"id":"1190","name":"金华都市农村"}]},{"name":"浙江","list":[{"id":"1191","name":"金华教育科技"}]},{"name":"浙江","list":[{"id":"1192","name":"金华新闻综合"}]},{"name":"浙江","list":[{"id":"1195","name":"舟山群岛旅游"}]},{"name":"浙江","list":[{"id":"1196","name":"舟山生活频道"}]},{"name":"浙江","list":[{"id":"1197","name":"舟山公共频道"}]},{"name":"浙江","list":[{"id":"1199","name":"安吉新闻综合"}]},{"name":"浙江","list":[{"id":"1200","name":"安吉生活娱乐"}]},{"name":"浙江","list":[{"id":"1201","name":"安吉美丽乡村"}]},{"name":"浙江","list":[{"id":"1202","name":"温州经济科教"}]},{"name":"浙江","list":[{"id":"1203","name":"温州都市生活"}]},{"name":"浙江","list":[{"id":"1204","name":"温州新闻综合"}]},{"name":"浙江","list":[{"id":"1205","name":"温州瓯江先锋"}]},{"name":"浙江","list":[{"id":"1206","name":"温州公共民生"}]},{"name":"浙江","list":[{"id":"1208","name":"湖州公共民生"}]},{"name":"浙江","list":[{"id":"1209","name":"湖州新闻综合"}]},{"name":"浙江","list":[{"id":"1210","name":"湖州文化娱乐"}]},{"name":"浙江","list":[{"id":"1211","name":"嵊州新闻综合"}]},{"name":"浙江","list":[{"id":"1212","name":"衢州经济信息"}]},{"name":"浙江","list":[{"id":"1215","name":"青田新闻综合"}]},{"name":"浙江","list":[{"id":"1225","name":"海宁生活服务"}]},{"name":"浙江","list":[{"id":"1226","name":"好易购频道"}]},';
    		$json .= '{"name":"浙江","list":[{"id":"1228","name":"萧山新闻综合"}]},{"name":"浙江","list":[{"id":"1229","name":"平阳新闻综合"}]},{"name":"浙江","list":[{"id":"1230","name":"平阳娱乐生活"}]},{"name":"浙江","list":[{"id":"1231","name":"兰溪新闻综合"}]},{"name":"浙江","list":[{"id":"1232","name":"上虞新闻综合"}]},{"name":"浙江","list":[{"id":"1233","name":"上虞经济文化"}]},{"name":"浙江","list":[{"id":"1234","name":"上虞商都频道"}]},{"name":"浙江","list":[{"id":"1235","name":"兰溪生活娱乐"}]},{"name":"浙江","list":[{"id":"1237","name":"乐清新闻综合"}]},{"name":"浙江","list":[{"id":"1238","name":"余姚新闻综合"}]},{"name":"浙江","list":[{"id":"1239","name":"玉环一套"}]},{"name":"浙江","list":[{"id":"1240","name":"玉环二套"}]},{"name":"浙江","list":[{"id":"1241","name":"奉化娱乐频道"}]},{"name":"浙江","list":[{"id":"1242","name":"奉化新闻综合"}]},{"name":"浙江","list":[{"id":"1243","name":"德清文化生活"}]},{"name":"浙江","list":[{"id":"1245","name":"余姚姚江文化"}]},{"name":"浙江","list":[{"id":"1246","name":"余姚幸福生活"}]},{"name":"浙江","list":[{"id":"1247","name":"新昌休闲影视"}]},{"name":"浙江","list":[{"id":"1248","name":"新昌新闻综合"}]},{"name":"上海","list":[{"id":"1350","name":"上视新闻综合"}]},{"name":"上海","list":[{"id":"1351","name":"上海纪实频道"}]},{"name":"上海","list":[{"id":"1352","name":"上海电视剧"}]},{"name":"上海","list":[{"id":"1354","name":"上海星尚频道"}]},{"name":"上海","list":[{"id":"1356","name":"上海新闻综合"}]},{"name":"上海","list":[{"id":"1357","name":"上海教育频道"}]},{"name":"上海","list":[{"id":"1358","name":"上海东方财经"}]},{"name":"上海","list":[{"id":"1360","name":"上海东方娱乐"}]},{"name":"上海","list":[{"id":"1361","name":"上海艺术人文"}]},{"name":"上海","list":[{"id":"1362","name":"上海第一财经"}]},{"name":"上海","list":[{"id":"1363","name":"上海娱乐频道"}]},{"name":"上海","list":[{"id":"1365","name":"上海东方购物"}]},{"name":"上海","list":[{"id":"1366","name":"上海外语频道"}]},{"name":"上海","list":[{"id":"1367","name":"东方电影频道"}]},{"name":"上海","list":[{"id":"1368","name":"东方国际频道"}]},{"name":"上海","list":[{"id":"1369","name":"上海这一刻"}]},{"name":"上海","list":[{"id":"1373","name":"极速汽车频道"}]},{"name":"上海","list":[{"id":"1374","name":"金山电视台"}]},{"name":"上海","list":[{"id":"1377","name":"法治天地频道"}]},{"name":"北京","list":[{"id":"1450","name":"北京文艺频道"}]},{"name":"北京","list":[{"id":"1451","name":"北京科教频道"}]},{"name":"北京","list":[{"id":"1452","name":"北京影视频道"}]},{"name":"北京","list":[{"id":"1453","name":"北京财经频道"}]},{"name":"北京","list":[{"id":"1454","name":"北京生活频道"}]},{"name":"北京","list":[{"id":"1455","name":"北京青少频道"}]},{"name":"北京","list":[{"id":"1456","name":"北京青年频道"}]},{"name":"北京","list":[{"id":"1457","name":"北京新闻频道"}]},{"name":"北京","list":[{"id":"1458","name":"北京纪实频道"}]},{"name":"北京","list":[{"id":"1459","name":"北京国际频道"}]},{"name":"北京","list":[{"id":"1465","name":"新华中文频道"}]},{"name":"北京","list":[{"id":"1472","name":"北京公共新闻"}]},{"name":"山东","list":[{"id":"1550","name":"山东齐鲁频道"}]},{"name":"山东","list":[{"id":"1551","name":"山东影视频道"}]},{"name":"山东","list":[{"id":"1552","name":"山东生活频道"}]},{"name":"山东","list":[{"id":"1553","name":"山东国际频道"}]},{"name":"山东","list":[{"id":"1554","name":"山东公共频道"}]},{"name":"山东","list":[{"id":"1556","name":"山东综艺频道"}]},{"name":"山东","list":[{"id":"1559","name":"山东教育频道"}]},{"name":"山东","list":[{"id":"1561","name":"济南都市频道"}]},{"name":"山东","list":[{"id":"1562","name":"济南新闻频道"}]},{"name":"山东","list":[{"id":"1563","name":"济南影视频道"}]},{"name":"山东","list":[{"id":"1564","name":"济南娱乐频道"}]},{"name":"山东","list":[{"id":"1565","name":"济南生活频道"}]},{"name":"山东","list":[{"id":"1566","name":"济南商务频道"}]},{"name":"山东","list":[{"id":"1567","name":"济南泉天下"}]},{"name":"山东","list":[{"id":"1570","name":"青岛影视频道"}]},{"name":"山东","list":[{"id":"1572","name":"青岛青少旅游"}]},{"name":"山东","list":[{"id":"1573","name":"青岛都市频道"}]},{"name":"山东","list":[{"id":"1574","name":"青岛生活服务"}]},{"name":"山东","list":[{"id":"1575","name":"青岛财经资讯"}]},{"name":"山东","list":[{"id":"1578","name":"菏泽新闻综合"}]},{"name":"山东","list":[{"id":"1579","name":"菏泽经济生活"}]},{"name":"山东","list":[{"id":"1580","name":"菏泽综艺频道"}]},';
    		$json .= '{"name":"山东","list":[{"id":"1581","name":"菏泽影视频道"}]},{"name":"山东","list":[{"id":"1583","name":"威海综合频道"}]},{"name":"山东","list":[{"id":"1587","name":"临沂公共频道"}]},{"name":"山东","list":[{"id":"1589","name":"临沂综合频道"}]},{"name":"山东","list":[{"id":"1590","name":"临沂新闻综合"}]},';
    		$json .= '{"name":"山东","list":[{"id":"1591","name":"临沂财经频道"}]},{"name":"山东","list":[{"id":"1592","name":"临沂影视频道"}]},{"name":"山东","list":[{"id":"1596","name":"诸城娱乐频道"}]},{"name":"山东","list":[{"id":"1598","name":"诸城影视频道"}]},{"name":"山东","list":[{"id":"1600","name":"诸城生活频道"}]},{"name":"山东","list":[{"id":"1601","name":"诸城新闻综合"}]},{"name":"山东","list":[{"id":"1602","name":"诸城党建频道"}]},{"name":"山东","list":[{"id":"1603","name":"德州娱乐频道"}]},{"name":"山东","list":[{"id":"1605","name":"德州新闻频道"}]},{"name":"山东","list":[{"id":"1606","name":"德州生活频道"}]},{"name":"山东","list":[{"id":"1607","name":"德州时尚频道"}]},{"name":"山东","list":[{"id":"1608","name":"日照新闻综合"}]},{"name":"山东","list":[{"id":"1609","name":"日照图文频道"}]},{"name":"山东","list":[{"id":"1610","name":"日照科教频道"}]},{"name":"山东","list":[{"id":"1611","name":"日照科教都市"}]},{"name":"山东","list":[{"id":"1612","name":"日照公共影视"}]},{"name":"山东","list":[{"id":"1613","name":"日照公共频道"}]},{"name":"山东","list":[{"id":"1617","name":"潍坊图文频道"}]},{"name":"山东","list":[{"id":"1627","name":"嘉祥生活频道"}]},{"name":"山东","list":[{"id":"1628","name":"嘉祥新闻综合"}]},{"name":"山东","list":[{"id":"1631","name":"烟台新闻综合"}]},{"name":"山东","list":[{"id":"1635","name":"寿光综艺频道"}]},{"name":"山东","list":[{"id":"1636","name":"寿光蔬菜频道"}]},{"name":"山东","list":[{"id":"1637","name":"寿光生活频道"}]},{"name":"山东","list":[{"id":"1638","name":"泗水新闻综合"}]},{"name":"山东","list":[{"id":"1639","name":"泗水综艺频道"}]},{"name":"山东","list":[{"id":"1640","name":"泗水电影频道"}]},{"name":"山东","list":[{"id":"1645","name":"青岛新闻综合"}]},{"name":"山东","list":[{"id":"1646","name":"烟台经济生活"}]},{"name":"山东","list":[{"id":"1647","name":"济宁生活频道"}]},{"name":"山东","list":[{"id":"1648","name":"济宁党建频道"}]},{"name":"山东","list":[{"id":"1649","name":"济宁新闻综合"}]},{"name":"山东","list":[{"id":"1650","name":"济宁都市频道"}]},{"name":"山东","list":[{"id":"1651","name":"济宁影视频道"}]},{"name":"山东","list":[{"id":"1657","name":"鱼台综合频道"}]},{"name":"山东","list":[{"id":"1658","name":"鱼台生活频道"}]},{"name":"山东","list":[{"id":"1667","name":"东营图文频道"}]},{"name":"山东","list":[{"id":"1672","name":"泰山电视台"}]},{"name":"山东","list":[{"id":"1673","name":"潍坊测试频道"}]},{"name":"山东","list":[{"id":"1674","name":"新泰生活频道"}]},{"name":"山东","list":[{"id":"1675","name":"新泰乡村频道"}]},{"name":"山东","list":[{"id":"1676","name":"新泰影视频道"}]},{"name":"山东","list":[{"id":"1677","name":"新泰综合频道"}]},{"name":"湖南","list":[{"id":"1750","name":"湖南公共频道"}]},{"name":"湖南","list":[{"id":"1751","name":"湖南经视频道"}]},{"name":"湖南","list":[{"id":"1752","name":"湖南都市频道"}]},{"name":"湖南","list":[{"id":"1753","name":"湖南国际频道"}]},{"name":"湖南","list":[{"id":"1754","name":"湖南娱乐频道"}]},{"name":"湖南","list":[{"id":"1755","name":"湖南电视剧"}]},{"name":"湖南","list":[{"id":"1756","name":"湖南快乐购"}]},{"name":"湖南","list":[{"id":"1757","name":"长沙公共频道"}]},{"name":"湖南","list":[{"id":"1758","name":"长沙政法频道"}]},{"name":"湖南","list":[{"id":"1759","name":"长沙女性频道"}]},{"name":"湖南","list":[{"id":"1760","name":"长沙嘉丽购物"}]},{"name":"湖南","list":[{"id":"1761","name":"长沙新闻频道"}]},{"name":"湖南","list":[{"id":"1764","name":"长沙经贸频道"}]},{"name":"湖南","list":[{"id":"1765","name":"长沙家庭消费"}]},{"name":"湖南","list":[{"id":"1766","name":"长沙互动新农村"}]},{"name":"湖南","list":[{"id":"1769","name":"先锋纪录频道"}]},{"name":"湖南","list":[{"id":"1770","name":"株洲新闻综合"}]},{"name":"湖南","list":[{"id":"1772","name":"株州房产频道"}]},{"name":"湖南","list":[{"id":"1773","name":"株洲商务频道"}]},{"name":"湖南","list":[{"id":"1774","name":"株洲公共民生"}]},{"name":"湖南","list":[{"id":"1775","name":"株洲法制频道"}]},{"name":"湖南","list":[{"id":"1776","name":"株州生活频道"}]},{"name":"湖南","list":[{"id":"1779","name":"现代女性频道"}]},{"name":"湖南","list":[{"id":"1784","name":"金鹰纪实频道"}]},{"name":"湖南","list":[{"id":"1787","name":"邵阳新闻频道"}]},{"name":"湖南","list":[{"id":"1788","name":"邵阳政法民生"}]},{"name":"湖南","list":[{"id":"1789","name":"怀化经济频道"}]},{"name":"湖南","list":[{"id":"1790","name":"怀化新闻综合"}]},{"name":"湖南","list":[{"id":"1791","name":"怀化移动频道"}]},{"name":"湖南","list":[{"id":"1792","name":"娄底综合频道"}]},{"name":"湖南","list":[{"id":"1793","name":"怀化文化旅游"}]},{"name":"湖南","list":[{"id":"1793","name":"娄底公共频道"}]},{"name":"湖南","list":[{"id":"1794","name":"常德新闻频道"}]},{"name":"湖南","list":[{"id":"1795","name":"常德公共频道"}]},{"name":"湖南","list":[{"id":"1796","name":"常德都市频道"}]},{"name":"湖南","list":[{"id":"1797","name":"常德武陵频道"}]},{"name":"湖北","list":[{"id":"1850","name":"蔡甸资讯频道"}]},{"name":"湖北","list":[{"id":"1851","name":"湖北综合频道"}]},{"name":"湖北","list":[{"id":"1852","name":"湖北经视频道"}]},{"name":"湖北","list":[{"id":"1853","name":"湖北影视频道"}]},{"name":"湖北","list":[{"id":"1854","name":"湖北教育频道"}]},{"name":"湖北","list":[{"id":"1855","name":"湖北公共频道"}]},{"name":"湖北","list":[{"id":"1860","name":"湖北垄上频道"}]},{"name":"湖北","list":[{"id":"1861","name":"武汉综合频道"}]},{"name":"湖北","list":[{"id":"1864","name":"武汉文体频道"}]},{"name":"湖北","list":[{"id":"1866","name":"武汉新闻综合"}]},{"name":"湖北","list":[{"id":"1867","name":"武汉经济频道"}]},{"name":"湖北","list":[{"id":"1868","name":"武汉少儿频道"}]},{"name":"湖北","list":[{"id":"1869","name":"武汉电视剧"}]},{"name":"湖北","list":[{"id":"1870","name":"武汉电视台"}]},{"name":"湖北","list":[{"id":"1872","name":"武汉科技生活"}]},{"name":"湖北","list":[{"id":"1880","name":"三峡综合频道"}]},{"name":"湖北","list":[{"id":"1881","name":"三峡公共频道"}]},{"name":"湖北","list":[{"id":"1882","name":"晴彩武汉频道"}]},{"name":"湖北","list":[{"id":"1883","name":"十堰新闻频道"}]},{"name":"湖北","list":[{"id":"1884","name":"十堰公共频道"}]},{"name":"湖北","list":[{"id":"1885","name":"十堰网络电视"}]},{"name":"湖北","list":[{"id":"1888","name":"恩施新闻频道"}]},{"name":"湖北","list":[{"id":"1889","name":"恩施电视台"}]},{"name":"湖北","list":[{"id":"1890","name":"恩施公共频道"}]},{"name":"湖北","list":[{"id":"1891","name":"恩施综艺频道"}]},{"name":"湖北","list":[{"id":"1893","name":"荆州垄上频道"}]},{"name":"湖北","list":[{"id":"1894","name":"荆门公共旅游"}]},{"name":"湖北","list":[{"id":"1896","name":"荆门新闻综合"}]},{"name":"湖北","list":[{"id":"1897","name":"荆门农谷频道"}]},{"name":"湖北","list":[{"id":"1898","name":"荆门鄂中经视"}]},{"name":"湖北","list":[{"id":"1901","name":"荆州新闻频道"}]},{"name":"湖北","list":[{"id":"1902","name":"襄阳晴彩频道"}]},{"name":"湖北","list":[{"id":"1903","name":"襄阳综合频道"}]},{"name":"湖北","list":[{"id":"1920","name":"恩施综合频道"}]},{"name":"天津","list":[{"id":"2002","name":"天津购物频道"}]},{"name":"天津","list":[{"id":"2005","name":"天津滨海一套"}]},{"name":"天津","list":[{"id":"2006","name":"天津滨海二套"}]},{"name":"天津","list":[{"id":"2007","name":"天津国际频道"}]},{"name":"重庆","list":[{"id":"2050","name":"重庆新闻频道"}]},{"name":"重庆","list":[{"id":"2051","name":"重庆都市频道"}]},{"name":"重庆","list":[{"id":"2052","name":"重庆时尚频道"}]},{"name":"重庆","list":[{"id":"2053","name":"重庆科教频道"}]},{"name":"重庆","list":[{"id":"2055","name":"重庆移动电视"}]},{"name":"重庆","list":[{"id":"2056","name":"重庆娱乐频道"}]},{"name":"重庆","list":[{"id":"2057","name":"重庆影视频道"}]},{"name":"重庆","list":[{"id":"2058","name":"重庆生活频道"}]},{"name":"重庆","list":[{"id":"2060","name":"重庆汽摩频道"}]},{"name":"重庆","list":[{"id":"2061","name":"重庆国际频道"}]},{"name":"重庆","list":[{"id":"2062","name":"重庆手持电视"}]},{"name":"重庆","list":[{"id":"2063","name":"重庆公共农村"}]},{"name":"重庆","list":[{"id":"2064","name":"晴彩重庆频道"}]},{"name":"重庆","list":[{"id":"2069","name":"万州综合频道"}]},{"name":"重庆","list":[{"id":"2070","name":"万州三峡移民"}]},{"name":"重庆","list":[{"id":"2073","name":"涪陵综合频道"}]},{"name":"重庆","list":[{"id":"2074","name":"万州影视文艺"}]},{"name":"重庆","list":[{"id":"2075","name":"万州科教频道"}]},{"name":"重庆","list":[{"id":"2076","name":"巴南生活服务"}]},{"name":"重庆","list":[{"id":"2077","name":"巴南综合频道"}]},{"name":"重庆","list":[{"id":"2078","name":"巴南娱乐频道"}]},{"name":"重庆","list":[{"id":"2079","name":"长寿综合频道"}]},{"name":"四川","list":[{"id":"2153","name":"四川国际频道"}]},{"name":"四川","list":[{"id":"2155","name":"四川新闻资讯"}]},{"name":"四川","list":[{"id":"2156","name":"四川公共频道"}]},{"name":"四川","list":[{"id":"2157","name":"四川文化旅游"}]},{"name":"四川","list":[{"id":"2158","name":"四川经济频道"}]},{"name":"四川","list":[{"id":"2161","name":"乐山公共新农村"}]},{"name":"四川","list":[{"id":"2164","name":"成都新闻综合"}]},{"name":"四川","list":[{"id":"2166","name":"成都公共频道"}]},{"name":"四川","list":[{"id":"2169","name":"成都新闻频道"}]},{"name":"四川","list":[{"id":"2178","name":"绵阳公共频道"}]},{"name":"四川","list":[{"id":"2180","name":"绵阳新闻频道"}]},{"name":"四川","list":[{"id":"2185","name":"广安互动频道"}]},{"name":"四川","list":[{"id":"2186","name":"广安新闻综合"}]},{"name":"四川","list":[{"id":"2187","name":"广安公共频道"}]},{"name":"四川","list":[{"id":"2189","name":"南充文娱频道"}]},{"name":"四川","list":[{"id":"2190","name":"南充资讯频道"}]},{"name":"四川","list":[{"id":"2191","name":"南充科教频道"}]},{"name":"四川","list":[{"id":"2195","name":"宜宾新闻综合"}]},{"name":"四川","list":[{"id":"2199","name":"泸州新闻综合"}]},{"name":"四川","list":[{"id":"2203","name":"遂宁互动影视"}]},{"name":"四川","list":[{"id":"2204","name":"遂宁公共公益"}]},{"name":"四川","list":[{"id":"2206","name":"遂宁新闻综合"}]},{"name":"四川","list":[{"id":"2207","name":"遂宁影视互动"}]},{"name":"四川","list":[{"id":"2209","name":"雅安新闻综合"}]},{"name":"四川","list":[{"id":"2210","name":"雅安经视频道"}]},{"name":"四川","list":[{"id":"2211","name":"雅安公共频道"}]},{"name":"四川","list":[{"id":"2212","name":"甘孜电视台"}]},{"name":"四川","list":[{"id":"2220","name":"广元综合频道"}]},{"name":"四川","list":[{"id":"2221","name":"广元公共频道"}]},{"name":"四川","list":[{"id":"2222","name":"广元影视频道"}]},{"name":"四川","list":[{"id":"2223","name":"广元科教频道"}]},{"name":"四川","list":[{"id":"2226","name":"南充综合频道"}]},{"name":"四川","list":[{"id":"2228","name":"宜宾公共频道"}]},{"name":"四川","list":[{"id":"2231","name":"南充公共频道"}]},{"name":"四川","list":[{"id":"2232","name":"自汞综合频道"}]},{"name":"四川","list":[{"id":"2233","name":"自汞公共频道"}]},{"name":"四川","list":[{"id":"2234","name":"温江电视台"}]},{"name":"四川","list":[{"id":"2235","name":"江油文化教育"}]},';
    		$json .= '{"name":"四川","list":[{"id":"2236","name":"德阳公共频道"}]},{"name":"四川","list":[{"id":"2237","name":"攀枝花新闻综合"}]},{"name":"四川","list":[{"id":"2238","name":"攀枝花影视文艺"}]},{"name":"四川","list":[{"id":"2239","name":"双流综合频道"}]},{"name":"四川","list":[{"id":"2240","name":"内江公共频道"}]},{"name":"四川","list":[{"id":"2241","name":"内江综合频道"}]},{"name":"四川","list":[{"id":"2243","name":"眉山综合频道"}]},{"name":"四川","list":[{"id":"2244","name":"眉山影视频道"}]},{"name":"四川","list":[{"id":"2245","name":"眉山公共频道"}]},{"name":"安徽","list":[{"id":"2350","name":"宿州科技教育"}]},{"name":"安徽","list":[{"id":"2351","name":"安徽经济生活"}]},{"name":"安徽","list":[{"id":"2352","name":"安徽公共频道"}]},{"name":"安徽","list":[{"id":"2353","name":"安徽人物频道"}]},{"name":"安徽","list":[{"id":"2354","name":"安徽影视频道"}]},{"name":"安徽","list":[{"id":"2357","name":"安徽科教频道"}]},{"name":"安徽","list":[{"id":"2358","name":"安徽综艺频道"}]},{"name":"安徽","list":[{"id":"2360","name":"安徽国际频道"}]},{"name":"安徽","list":[{"id":"2361","name":"合肥新闻频道"}]},{"name":"安徽","list":[{"id":"2362","name":"合肥生活频道"}]},{"name":"安徽","list":[{"id":"2363","name":"合肥财经频道"}]},{"name":"安徽","list":[{"id":"2364","name":"合肥教育法制"}]},{"name":"安徽","list":[{"id":"2365","name":"合肥文体博览"}]},{"name":"安徽","list":[{"id":"2366","name":"芜湖教育频道"}]},{"name":"安徽","list":[{"id":"2368","name":"芜湖生活频道"}]},{"name":"安徽","list":[{"id":"2369","name":"芜湖徽商频道"}]},{"name":"安徽","list":[{"id":"2370","name":"安庆新闻综合"}]},{"name":"安徽","list":[{"id":"2371","name":"安庆黄梅戏"}]},{"name":"安徽","list":[{"id":"2372","name":"安庆公共频道"}]},{"name":"安徽","list":[{"id":"2374","name":"淮北教育频道"}]},{"name":"安徽","list":[{"id":"2375","name":"淮北公共频道"}]},{"name":"安徽","list":[{"id":"2376","name":"淮北都市频道"}]},{"name":"安徽","list":[{"id":"2378","name":"宿州新闻综合"}]},{"name":"安徽","list":[{"id":"2385","name":"铜陵影视娱乐"}]},{"name":"安徽","list":[{"id":"2386","name":"铜陵生活服务"}]},{"name":"安徽","list":[{"id":"2390","name":"阜阳新闻综合"}]},{"name":"安徽","list":[{"id":"2391","name":"阜阳都市频道"}]},{"name":"安徽","list":[{"id":"2392","name":"阜阳公共频道"}]},{"name":"安徽","list":[{"id":"2393","name":"阜阳教育频道"}]},{"name":"安徽","list":[{"id":"2394","name":"蚌埠生活频道"}]},{"name":"安徽","list":[{"id":"2395","name":"蚌埠公共频道"}]},{"name":"安徽","list":[{"id":"2396","name":"蚌埠新闻综合"}]},{"name":"安徽","list":[{"id":"2397","name":"蚌埠文教频道"}]},{"name":"安徽","list":[{"id":"2398","name":"六安新闻综合"}]},{"name":"安徽","list":[{"id":"2399","name":"马鞍山影视频道"}]},{"name":"安徽","list":[{"id":"2404","name":"黄山新闻综合"}]},{"name":"安徽","list":[{"id":"2409","name":"黄山公共生活"}]},{"name":"安徽","list":[{"id":"2410","name":"阜南新闻综合"}]},{"name":"福建","list":[{"id":"2450","name":"厦门一套"}]},{"name":"福建","list":[{"id":"2451","name":"厦门二套"}]},{"name":"福建","list":[{"id":"2452","name":"厦门三套"}]},{"name":"福建","list":[{"id":"2453","name":"厦门四套"}]},{"name":"福建","list":[{"id":"2454","name":"厦门全心购物"}]},{"name":"福建","list":[{"id":"2456","name":"厦门移动频道"}]},{"name":"福建","list":[{"id":"2457","name":"福建经济频道"}]},{"name":"福建","list":[{"id":"2458","name":"福建电视剧"}]},{"name":"福建","list":[{"id":"2459","name":"福建都市时尚"}]},{"name":"福建","list":[{"id":"2460","name":"福建综合频道"}]},{"name":"福建","list":[{"id":"2461","name":"福建新闻频道"}]},{"name":"福建","list":[{"id":"2462","name":"福建公共频道"}]},{"name":"福建","list":[{"id":"2463","name":"福州新闻综合"}]},{"name":"福建","list":[{"id":"2469","name":"德化新闻综合"}]},{"name":"福建","list":[{"id":"2472","name":"厦门国际频道"}]},{"name":"福建","list":[{"id":"2473","name":"漳浦综合频道"}]},{"name":"福建","list":[{"id":"2474","name":"漳浦数字频道"}]},{"name":"福建","list":[{"id":"2475","name":"石狮新闻频道"}]},{"name":"福建","list":[{"id":"2478","name":"宁德新闻频道"}]},{"name":"江西","list":[{"id":"2553","name":"江西经视频道"}]},{"name":"江西","list":[{"id":"2554","name":"江西红色经典"}]},{"name":"江西","list":[{"id":"2555","name":"江西都市频道"}]},{"name":"江西","list":[{"id":"2556","name":"江西公共频道"}]},{"name":"江西","list":[{"id":"2557","name":"江西影视频道"}]},{"name":"江西","list":[{"id":"2558","name":"江西炫彩频道"}]},{"name":"江西","list":[{"id":"2559","name":"江西风尚购物"}]},{"name":"江西","list":[{"id":"2560","name":"南昌公共频道"}]},{"name":"江西","list":[{"id":"2561","name":"南昌资讯政法"}]},{"name":"江西","list":[{"id":"2562","name":"南昌新闻综合"}]},{"name":"江西","list":[{"id":"2563","name":"南昌都市频道"}]},{"name":"江西","list":[{"id":"2565","name":"新余公共频道"}]},{"name":"江西","list":[{"id":"2566","name":"九江一台频道"}]},{"name":"江西","list":[{"id":"2570","name":"九江新闻综合"}]},{"name":"江西","list":[{"id":"2572","name":"九江民生频道"}]},{"name":"江西","list":[{"id":"2573","name":"九江旅游频道"}]},{"name":"江西","list":[{"id":"2581","name":"新余教育频道"}]},{"name":"江西","list":[{"id":"2582","name":"新余新闻综合"}]},{"name":"江西","list":[{"id":"2583","name":"上饶都市生活"}]},{"name":"江西","list":[{"id":"2584","name":"上饶新闻综合"}]},{"name":"江西","list":[{"id":"2585","name":"上饶旅游经济"}]},{"name":"江西","list":[{"id":"2586","name":"上饶青少娱乐"}]},{"name":"江西","list":[{"id":"2587","name":"南昌法制频道"}]},{"name":"河南","list":[{"id":"2650","name":"河南都市频道"}]},{"name":"河南","list":[{"id":"2651","name":"河南民生频道"}]},{"name":"河南","list":[{"id":"2652","name":"河南政法频道"}]},';
    		$json .= '{"name":"河南","list":[{"id":"2653","name":"河南电视剧"}]},{"name":"河南","list":[{"id":"2654","name":"河南新闻频道"}]},{"name":"河南","list":[{"id":"2655","name":"河南购物频道"}]},{"name":"河南","list":[{"id":"2657","name":"河南公共频道"}]},{"name":"河南","list":[{"id":"2658","name":"河南新农村"}]},{"name":"河南","list":[{"id":"2659","name":"河南欢腾购物"}]},{"name":"河南","list":[{"id":"2660","name":"河南国际频道"}]},{"name":"河南","list":[{"id":"2661","name":"郑州商都频道"}]},{"name":"河南","list":[{"id":"2665","name":"郑州教育台"}]},{"name":"河南","list":[{"id":"2666","name":"郑州电视剧"}]},';
    		$json .= '{"name":"河南","list":[{"id":"2668","name":"郑州时政频道"}]},{"name":"河南","list":[{"id":"2669","name":"郑州影视频道"}]},{"name":"河南","list":[{"id":"2672","name":"洛阳科教法制"}]},{"name":"河南","list":[{"id":"2673","name":"洛阳公共频道"}]},{"name":"河南","list":[{"id":"2674","name":"洛阳影视文娱"}]},{"name":"河南","list":[{"id":"2675","name":"洛阳综合频道"}]},{"name":"河南","list":[{"id":"2685","name":"郑州法制频道"}]},{"name":"河南","list":[{"id":"2686","name":"郑州影视戏曲"}]},{"name":"河南","list":[{"id":"2687","name":"郑州都市生活"}]},{"name":"河南","list":[{"id":"2688","name":"郑州文体频道"}]},{"name":"河南","list":[{"id":"2689","name":"郑州综合频道"}]},{"name":"河南","list":[{"id":"2690","name":"睛彩安阳频道"}]},{"name":"河南","list":[{"id":"2693","name":"晴彩中原频道"}]},{"name":"河南","list":[{"id":"2698","name":"安阳新闻综合"}]},{"name":"河南","list":[{"id":"2700","name":"安阳科教频道"}]},{"name":"河南","list":[{"id":"2701","name":"安阳图文生活"}]},{"name":"河南","list":[{"id":"2705","name":"项城房产频道"}]},{"name":"河南","list":[{"id":"2714","name":"周口新闻综合"}]},{"name":"河南","list":[{"id":"2715","name":"周口科教文化"}]},{"name":"河南","list":[{"id":"2716","name":"周口图文信息"}]},{"name":"河南","list":[{"id":"2717","name":"周口经济生活"}]},{"name":"河南","list":[{"id":"2718","name":"晴彩平顶山"}]},{"name":"河南","list":[{"id":"2719","name":"遂平一套"}]},{"name":"河北","list":[{"id":"2801","name":"河北都市频道"}]},{"name":"河北","list":[{"id":"2802","name":"河北公共频道"}]},{"name":"河北","list":[{"id":"2803","name":"河北农民频道"}]},{"name":"河北","list":[{"id":"2804","name":"河北经济频道"}]},{"name":"河北","list":[{"id":"2805","name":"河北影视频道"}]},{"name":"河北","list":[{"id":"2806","name":"河北购物频道"}]},{"name":"河北","list":[{"id":"2811","name":"衡水影视娱乐"}]},{"name":"河北","list":[{"id":"2812","name":"衡水新闻综合"}]},{"name":"河北","list":[{"id":"2813","name":"衡水公共频道"}]},{"name":"河北","list":[{"id":"2814","name":"邯郸新闻综合"}]},{"name":"河北","list":[{"id":"2815","name":"邯郸录播频道"}]},{"name":"河北","list":[{"id":"2816","name":"邯郸公共频道"}]},{"name":"河北","list":[{"id":"2817","name":"邯郸民生都市"}]},{"name":"河北","list":[{"id":"2820","name":"邢台公共频道"}]},{"name":"河北","list":[{"id":"2821","name":"邢台综合频道"}]},{"name":"河北","list":[{"id":"2822","name":"邢台公共娱乐"}]},{"name":"河北","list":[{"id":"2823","name":"石家庄娱乐频道"}]},{"name":"河北","list":[{"id":"2824","name":"石家庄新闻综合"}]},{"name":"河北","list":[{"id":"2825","name":"石家庄生活频道"}]},{"name":"河北","list":[{"id":"2826","name":"石家庄都市频道"}]},{"name":"河北","list":[{"id":"2834","name":"安平剧好看频道"}]},{"name":"河北","list":[{"id":"2836","name":"无极生活频道"}]},{"name":"河北","list":[{"id":"2837","name":"无极影视频道"}]},';
    		$json .= '{"name":"河北","list":[{"id":"2838","name":"无极新闻频道"}]},{"name":"河北","list":[{"id":"2839","name":"三佳购物频道"}]},{"name":"河北","list":[{"id":"2840","name":"沧州一台"}]},{"name":"河北","list":[{"id":"2841","name":"沧州二台"}]},{"name":"河北","list":[{"id":"2842","name":"沧州三台"}]},{"name":"河北","list":[{"id":"2843","name":"秦皇岛一套"}]},{"name":"河北","list":[{"id":"2847","name":"张家口一台"}]},{"name":"河北","list":[{"id":"2848","name":"张家口二台"}]},{"name":"河北","list":[{"id":"2849","name":"张家口四台"}]},{"name":"山西","list":[{"id":"2900","name":"山西经济资讯"}]},{"name":"山西","list":[{"id":"2901","name":"山西影视频道"}]},{"name":"山西","list":[{"id":"2902","name":"山西科教频道"}]},{"name":"山西","list":[{"id":"2903","name":"山西公共频道"}]},{"name":"山西","list":[{"id":"2904","name":"山西彩民在线"}]},{"name":"山西","list":[{"id":"2905","name":"山西老年福"}]},{"name":"山西","list":[{"id":"2906","name":"太原家庭消费"}]},{"name":"山西","list":[{"id":"2907","name":"太原影视频道"}]},{"name":"山西","list":[{"id":"2908","name":"太原文体频道"}]},{"name":"山西","list":[{"id":"2909","name":"太原百姓频道"}]},{"name":"山西","list":[{"id":"2910","name":"太原新闻频道"}]},{"name":"山西","list":[{"id":"2911","name":"太原法制频道"}]},{"name":"山西","list":[{"id":"2912","name":"太原黄河电视"}]},{"name":"山西","list":[{"id":"2913","name":"中国黄河频道"}]},{"name":"山西","list":[{"id":"2914","name":"阳泉影视频道"}]},{"name":"山西","list":[{"id":"2915","name":"阳泉综合频道"}]},{"name":"山西","list":[{"id":"2916","name":"阳泉科教频道"}]},{"name":"山西","list":[{"id":"2917","name":"晴彩阳泉频道"}]},{"name":"山西","list":[{"id":"2918","name":"晴彩山西频道"}]},{"name":"山西","list":[{"id":"2921","name":"吕梁新闻综合"}]},{"name":"山西","list":[{"id":"2923","name":"黄河电视台"}]},{"name":"山西","list":[{"id":"2924","name":"晋城导视频道"}]},{"name":"山西","list":[{"id":"2925","name":"朔州新闻综合"}]},{"name":"山西","list":[{"id":"2926","name":"朔州生活公共"}]},{"name":"山西","list":[{"id":"2927","name":"孝义电视频道"}]},{"name":"山西","list":[{"id":"2928","name":"潞城新闻综合"}]},{"name":"山西","list":[{"id":"2929","name":"潞城文化旅游"}]},{"name":"山西","list":[{"id":"2930","name":"潞城财经农业"}]},{"name":"辽宁","list":[{"id":"3000","name":"辽宁经济频道"}]},{"name":"辽宁","list":[{"id":"3001","name":"辽宁公共频道"}]},{"name":"辽宁","list":[{"id":"3002","name":"辽宁教育青少"}]},{"name":"辽宁","list":[{"id":"3013","name":"辽宁都市频道"}]},{"name":"辽宁","list":[{"id":"3015","name":"辽宁北方频道"}]},{"name":"辽宁","list":[{"id":"3017","name":"辽宁宜家购物"}]},{"name":"辽宁","list":[{"id":"3018","name":"辽宁生活频道"}]},{"name":"辽宁","list":[{"id":"3019","name":"辽宁影视频道"}]},{"name":"辽宁","list":[{"id":"3023","name":"大连新闻综合"}]},{"name":"辽宁","list":[{"id":"3024","name":"大连公共频道"}]},{"name":"辽宁","list":[{"id":"3025","name":"大连文体频道"}]},{"name":"辽宁","list":[{"id":"3028","name":"大连财经频道"}]},{"name":"辽宁","list":[{"id":"3030","name":"大连影视频道"}]},{"name":"辽宁","list":[{"id":"3031","name":"大连生活频道"}]},{"name":"辽宁","list":[{"id":"3034","name":"鞍山图文频道"}]},{"name":"辽宁","list":[{"id":"3035","name":"鞍山新闻集锦"}]},{"name":"辽宁","list":[{"id":"3038","name":"沈阳交通频道"}]},{"name":"辽宁","list":[{"id":"3039","name":"沈阳新闻频道"}]},{"name":"辽宁","list":[{"id":"3040","name":"鞍山新闻综合"}]},{"name":"辽宁","list":[{"id":"3041","name":"鞍山娱乐频道"}]},{"name":"辽宁","list":[{"id":"3045","name":"云动V电影频道"}]},{"name":"黑龙江","list":[{"id":"3100","name":"黑龙江影视频道"}]},{"name":"黑龙江","list":[{"id":"3101","name":"黑龙江文艺频道"}]},{"name":"黑龙江","list":[{"id":"3102","name":"黑龙江都市频道"}]},{"name":"黑龙江","list":[{"id":"3103","name":"黑龙江新闻频道"}]},{"name":"黑龙江","list":[{"id":"3104","name":"黑龙江公共频道"}]},{"name":"黑龙江","list":[{"id":"3105","name":"黑龙江第七频道"}]},{"name":"黑龙江","list":[{"id":"3106","name":"黑龙江导视频道"}]},{"name":"黑龙江","list":[{"id":"3107","name":"黑龙江考试频道"}]},{"name":"黑龙江","list":[{"id":"3109","name":"黑龙江法制频道"}]},{"name":"黑龙江","list":[{"id":"3112","name":"大庆百湖频道"}]},{"name":"黑龙江","list":[{"id":"3113","name":"大庆综合频道"}]},{"name":"黑龙江","list":[{"id":"3114","name":"大庆科教频道"}]},{"name":"黑龙江","list":[{"id":"3118","name":"齐齐哈尔专题频道"}]},{"name":"黑龙江","list":[{"id":"3119","name":"齐齐哈尔新闻综合"}]},{"name":"黑龙江","list":[{"id":"3120","name":"哈尔滨资讯频道"}]},{"name":"黑龙江","list":[{"id":"3121","name":"哈尔滨影视频道"}]},{"name":"黑龙江","list":[{"id":"3122","name":"哈尔滨新闻综合"}]},{"name":"吉林","list":[{"id":"3200","name":"吉林生活频道"}]},{"name":"吉林","list":[{"id":"3202","name":"吉林公共新闻"}]},{"name":"吉林","list":[{"id":"3204","name":"吉林综艺文化"}]},{"name":"吉林","list":[{"id":"3206","name":"吉林东北戏曲"}]},{"name":"吉林","list":[{"id":"3208","name":"吉林影视频道"}]},{"name":"吉林","list":[{"id":"3209","name":"吉林乡村频道"}]},{"name":"吉林","list":[{"id":"3210","name":"吉林都市频道"}]},{"name":"吉林","list":[{"id":"3211","name":"吉林家有购物"}]},{"name":"吉林","list":[{"id":"3212","name":"吉林新闻综合"}]},{"name":"吉林","list":[{"id":"3213","name":"吉林经济频道"}]},{"name":"吉林","list":[{"id":"3215","name":"吉林市新闻综合"}]},{"name":"吉林","list":[{"id":"3216","name":"吉林教育电视台"}]},{"name":"吉林","list":[{"id":"3217","name":"延边新闻综合"}]},{"name":"吉林","list":[{"id":"3218","name":"延边卫视频道"}]},{"name":"吉林","list":[{"id":"3219","name":"延边卫视汉语"}]},{"name":"吉林","list":[{"id":"3222","name":"长春商业频道"}]},{"name":"吉林","list":[{"id":"3223","name":"长春新闻综合"}]},{"name":"吉林","list":[{"id":"3224","name":"长春市民频道"}]},{"name":"吉林","list":[{"id":"3225","name":"长春娱乐频道"}]},{"name":"吉林","list":[{"id":"3226","name":"长春综合频道"}]},{"name":"吉林","list":[{"id":"3227","name":"长春新知频道"}]},{"name":"吉林","list":[{"id":"3229","name":"辽源新闻综合"}]},{"name":"吉林","list":[{"id":"3233","name":"白山综合频道"}]},{"name":"吉林","list":[{"id":"3234","name":"白山生活频道"}]},{"name":"吉林","list":[{"id":"3235","name":"白城新闻综合"}]},{"name":"吉林","list":[{"id":"3236","name":"四平新闻综合"}]},{"name":"吉林","list":[{"id":"3237","name":"松原新闻综合"}]},{"name":"吉林","list":[{"id":"3238","name":"通化新闻综合"}]},{"name":"吉林","list":[{"id":"3240","name":"瓦房店经济生活"}]},{"name":"吉林","list":[{"id":"3241","name":"瓦房店新闻综合"}]},{"name":"内蒙古","list":[{"id":"3300","name":"内蒙古蒙语频道"}]},{"name":"内蒙古","list":[{"id":"3301","name":"内蒙古新闻综合"}]},{"name":"内蒙古","list":[{"id":"3302","name":"内蒙古经济频道"}]},{"name":"内蒙古","list":[{"id":"3303","name":"内蒙古影视频道"}]},{"name":"内蒙古","list":[{"id":"3304","name":"内蒙古文体频道"}]},{"name":"内蒙古","list":[{"id":"3306","name":"内蒙古少儿频道"}]},{"name":"内蒙古","list":[{"id":"3310","name":"鄂尔多斯新闻"}]},{"name":"内蒙古","list":[{"id":"3311","name":"鄂尔多斯经济"}]},{"name":"内蒙古","list":[{"id":"3315","name":"鄂尔多斯三套"}]},{"name":"内蒙古","list":[{"id":"3316","name":"鄂尔多斯城市生活"}]},';
    		$json .= '{"name":"内蒙古","list":[{"id":"3318","name":"鄂尔多斯经济服务"}]},{"name":"内蒙古","list":[{"id":"3319","name":"鄂尔多斯蒙语综合"}]},{"name":"内蒙古","list":[{"id":"3322","name":"呼和浩特影视娱乐"}]},{"name":"内蒙古","list":[{"id":"3324","name":"包头生活服务"}]},{"name":"内蒙古","list":[{"id":"3325","name":"包头经济频道"}]},{"name":"内蒙古","list":[{"id":"3326","name":"包头新闻综合"}]},{"name":"内蒙古","list":[{"id":"3327","name":"呼和浩特都市生活"}]},{"name":"内蒙古","list":[{"id":"3328","name":"赤峰综合频道"}]},{"name":"内蒙古","list":[{"id":"3329","name":"赤峰影视频道"}]},{"name":"广西","list":[{"id":"3400","name":"广西新闻频道"}]},{"name":"广西","list":[{"id":"3401","name":"广西都市频道"}]},{"name":"广西","list":[{"id":"3402","name":"广西综艺频道"}]},{"name":"广西","list":[{"id":"3403","name":"广西影视频道"}]},{"name":"广西","list":[{"id":"3404","name":"广西公共频道"}]},{"name":"广西","list":[{"id":"3405","name":"晴彩广西交通"}]},{"name":"广西","list":[{"id":"3406","name":"移动交通电视"}]},{"name":"广西","list":[{"id":"3411","name":"柳州生活科教"}]},{"name":"广西","list":[{"id":"3412","name":"柳州新闻综合"}]},{"name":"广西","list":[{"id":"3419","name":"乐思购频道"}]},{"name":"广西","list":[{"id":"3420","name":"梧州公共频道"}]},{"name":"贵州","list":[{"id":"3500","name":"贵阳都市频道"}]},{"name":"云南","list":[{"id":"3500","name":"云南都市频道"}]},{"name":"贵州","list":[{"id":"3501","name":"贵阳经济生活"}]},{"name":"贵州","list":[{"id":"3502","name":"贵阳旅游生活"}]},{"name":"云南","list":[{"id":"3502","name":"云南影视频道"}]},{"name":"贵州","list":[{"id":"3503","name":"贵阳新闻综合"}]},{"name":"云南","list":[{"id":"3503","name":"云南国际频道"}]},{"name":"贵州","list":[{"id":"3504","name":"贵阳法制频道"}]},{"name":"云南","list":[{"id":"3505","name":"云南公共频道"}]},{"name":"贵州","list":[{"id":"3506","name":"遵义新闻综合"}]},{"name":"云南","list":[{"id":"3506","name":"云南生活频道"}]},{"name":"云南","list":[{"id":"3507","name":"昆明教育频道"}]},{"name":"贵州","list":[{"id":"3508","name":"贵州法制频道"}]},{"name":"云南","list":[{"id":"3508","name":"昆明阳光频道"}]},{"name":"贵州","list":[{"id":"3509","name":"贵州生活娱乐"}]},{"name":"云南","list":[{"id":"3509","name":"昆明春城频道"}]},{"name":"云南","list":[{"id":"3510","name":"昆明健康频道"}]},{"name":"云南","list":[{"id":"3511","name":"昆明影视频道"}]},{"name":"贵州","list":[{"id":"3512","name":"黔东南综合频道"}]},{"name":"云南","list":[{"id":"3512","name":"昆明财富频道"}]},{"name":"云南","list":[{"id":"3521","name":"普洱一台"}]},{"name":"云南","list":[{"id":"3522","name":"普洱二台"}]},{"name":"云南","list":[{"id":"3523","name":"普洱三台"}]},{"name":"云南","list":[{"id":"3524","name":"普洱四台"}]},{"name":"云南","list":[{"id":"3527","name":"红河电视台"}]},{"name":"云南","list":[{"id":"3529","name":"昆明新闻综合"}]},{"name":"云南","list":[{"id":"3530","name":"丽江公共频道"}]},{"name":"云南","list":[{"id":"3531","name":"文山公共频道"}]},{"name":"海南","list":[{"id":"3550","name":"海南综合频道"}]},{"name":"海南","list":[{"id":"3551","name":"海南影视剧"}]},{"name":"海南","list":[{"id":"3554","name":"海南新闻频道"}]},{"name":"海南","list":[{"id":"3555","name":"海南公共频道"}]},{"name":"海南","list":[{"id":"3556","name":"海南青少频道"}]},{"name":"海南","list":[{"id":"3558","name":"海南影视综艺"}]},{"name":"海南","list":[{"id":"3559","name":"海南三沙卫视"}]},{"name":"海南","list":[{"id":"3560","name":"海口新闻频道"}]},{"name":"海南","list":[{"id":"3562","name":"海口双创频道"}]},{"name":"海南","list":[{"id":"3563","name":"海口生活娱乐"}]},{"name":"陕西","list":[{"id":"3601","name":"陕西都市青春"}]},{"name":"陕西","list":[{"id":"3602","name":"陕西家庭生活"}]},{"name":"陕西","list":[{"id":"3603","name":"陕西公共频道"}]},{"name":"陕西","list":[{"id":"3605","name":"陕西生活频道"}]},{"name":"陕西","list":[{"id":"3606","name":"陕西秦腔频道"}]},{"name":"陕西","list":[{"id":"3607","name":"陕西农林卫视"}]},{"name":"陕西","list":[{"id":"3608","name":"陕西新闻资讯"}]},{"name":"陕西","list":[{"id":"3609","name":"陕西影视频道"}]},{"name":"陕西","list":[{"id":"3610","name":"陕西公共政法"}]},{"name":"陕西","list":[{"id":"3611","name":"西安新闻综合"}]},{"name":"陕西","list":[{"id":"3612","name":"西安白鸽都市"}]},{"name":"陕西","list":[{"id":"3613","name":"西安商务资讯"}]},{"name":"陕西","list":[{"id":"3619","name":"榆林电视台1套"}]},{"name":"陕西","list":[{"id":"3620","name":"榆林电视台2套"}]},{"name":"陕西","list":[{"id":"3621","name":"榆林电视台3套"}]},{"name":"陕西","list":[{"id":"3622","name":"安康公共频道"}]},{"name":"陕西","list":[{"id":"3624","name":"陕西都市生活"}]},{"name":"陕西","list":[{"id":"3625","name":"安康新闻频道"}]},{"name":"陕西","list":[{"id":"3626","name":"陕西西部电影"}]},{"name":"甘肃","list":[{"id":"3700","name":"甘肃公共频道"}]},{"name":"甘肃","list":[{"id":"3702","name":"甘肃都市频道"}]},{"name":"甘肃","list":[{"id":"3704","name":"甘肃文化影视"}]},{"name":"甘肃","list":[{"id":"3706","name":"兰州新闻综合"}]},{"name":"甘肃","list":[{"id":"3707","name":"兰州生活经济"}]},{"name":"甘肃","list":[{"id":"3708","name":"兰州晴彩频道"}]},{"name":"甘肃","list":[{"id":"3709","name":"兰州公共频道"}]},{"name":"甘肃","list":[{"id":"3710","name":"兰州综艺体育"}]},{"name":"甘肃","list":[{"id":"3713","name":"嘉峪关综合频道"}]},{"name":"甘肃","list":[{"id":"3714","name":"嘉峪关公共频道"}]},{"name":"甘肃","list":[{"id":"3719","name":"甘南新闻综合"}]},{"name":"甘肃","list":[{"id":"3720","name":"甘南藏语频道"}]},{"name":"甘肃","list":[{"id":"3721","name":"庆阳综合频道"}]},{"name":"甘肃","list":[{"id":"3722","name":"庆阳公共频道"}]},{"name":"甘肃","list":[{"id":"3723","name":"张掖新闻综合"}]},{"name":"甘肃","list":[{"id":"3724","name":"张掖公共频道"}]},{"name":"新疆","list":[{"id":"3753","name":"克拉玛依一套"}]},{"name":"新疆","list":[{"id":"3754","name":"兵团卫视"}]},{"name":"新疆","list":[{"id":"3755","name":"新疆二台"}]},{"name":"新疆","list":[{"id":"3756","name":"新疆三台"}]},{"name":"新疆","list":[{"id":"3757","name":"新疆四台"}]},{"name":"新疆","list":[{"id":"3758","name":"新疆五台"}]},{"name":"新疆","list":[{"id":"3759","name":"新疆七台"}]},{"name":"新疆","list":[{"id":"3760","name":"新疆八台"}]},{"name":"新疆","list":[{"id":"3761","name":"新疆九台"}]},{"name":"新疆","list":[{"id":"3762","name":"新疆十台"}]},{"name":"新疆","list":[{"id":"3763","name":"新疆十一"}]},{"name":"新疆","list":[{"id":"3764","name":"新疆十二"}]},{"name":"新疆","list":[{"id":"3765","name":"新疆十三"}]},{"name":"新疆","list":[{"id":"3766","name":"新疆十四"}]},{"name":"宁夏","list":[{"id":"3853","name":"银川公共频道"}]},{"name":"宁夏","list":[{"id":"3854","name":"银川生活频道"}]},{"name":"宁夏","list":[{"id":"3855","name":"银川文体频道"}]},{"name":"宁夏","list":[{"id":"3856","name":"银川网台频道"}]},{"name":"青海","list":[{"id":"3900","name":"青海生活频道"}]},{"name":"青海","list":[{"id":"3901","name":"青海都市频道"}]},{"name":"青海","list":[{"id":"3902","name":"西宁新闻频道"}]},{"name":"青海","list":[{"id":"3903","name":"西宁生活频道"}]},{"name":"青海","list":[{"id":"3904","name":"西宁夏都房车"}]},{"name":"青海","list":[{"id":"3905","name":"西宁文化先锋"}]},{"name":"Test","list":[{"id":"4000","name":"蓝天白云Letv2"}]},{"name":"Test","list":[{"id":"4001","name":"蓝天白云Letvy"}]},{"name":"Test","list":[{"id":"4002","name":"蓝天白云Letvz"}]},{"name":"Test","list":[{"id":"4005","name":"湖南卫视Letv2"}]},{"name":"Test","list":[{"id":"4006","name":"CCTV6Letvy"}]},{"name":"Test","list":[{"id":"4007","name":"民视频道"}]},{"name":"Test","list":[{"id":"4008","name":"珠江电影"}]},{"name":"Test","list":[{"id":"4009","name":"test珠江"}]},{"name":"Test","list":[{"id":"4010","name":"hunantv test"}]},{"name":"Test","list":[{"id":"4011","name":"蓝天白云"}]},{"name":"Test","list":[{"id":"4013","name":"Letv4切换模式"}]},{"name":"Test","list":[{"id":"4015","name":"CCTV1"}]},{"name":"Test","list":[{"id":"4017","name":"济南影视频道"}]},{"name":"Test","list":[{"id":"4018","name":"济南娱乐频道"}]},{"name":"Test","list":[{"id":"4019","name":"济南生活频道"}]},{"name":"Test","list":[{"id":"4022","name":"qiyi test"}]},{"name":"Test","list":[{"id":"4023","name":"广州新闻频道"}]},';
    		$json .= '{"name":"Test","list":[{"id":"4024","name":"北京卫视备用"}]},{"name":"Test","list":[{"id":"4025","name":"深圳卫视备用"}]},{"name":"Test","list":[{"id":"4026","name":"天津卫视备用"}]},{"name":"Test","list":[{"id":"4027","name":"东方卫视备用"}]},{"name":"Test","list":[{"id":"4030","name":"风云足球备用"}]},{"name":"Test","list":[{"id":"4032","name":"安徽卫视备用"}]},{"name":"Test","list":[{"id":"4033","name":"岭南戏曲频道"}]},{"name":"Test","list":[{"id":"4034","name":"测试"}]},{"name":"Test","list":[{"id":"4035","name":"珠江P2P"}]},{"name":"轮播","list":[{"id":"6000","name":"1080P"}]},{"name":"轮播","list":[{"id":"6001","name":"2015夏季达沃斯"}]},{"name":"轮播","list":[{"id":"6007","name":"LIVE生活"}]},{"name":"轮播","list":[{"id":"6014","name":"一路上有你"}]},{"name":"轮播","list":[{"id":"6015","name":"丁子峻"}]},{"name":"轮播","list":[{"id":"6016","name":"两生花"}]},{"name":"轮播","list":[{"id":"6017","name":"中国国际时装周"}]},{"name":"轮播","list":[{"id":"6018","name":"中国摇滚"}]},{"name":"轮播","list":[{"id":"6019","name":"中国梦之声"}]},{"name":"轮播","list":[{"id":"6020","name":"中国民谣"}]},{"name":"轮播","list":[{"id":"6021","name":"乐视发布会"}]},{"name":"轮播","list":[{"id":"6022","name":"乐视游戏"}]},{"name":"轮播","list":[{"id":"6023","name":"乐视生态"}]},{"name":"轮播","list":[{"id":"6024","name":"乐迷频道"}]},{"name":"轮播","list":[{"id":"6025","name":"乐队组合"}]},{"name":"轮播","list":[{"id":"6026","name":"乔任梁"}]},{"name":"轮播","list":[{"id":"6027","name":"于小伟"}]},{"name":"轮播","list":[{"id":"6028","name":"于明加"}]},{"name":"轮播","list":[{"id":"6029","name":"于正"}]},{"name":"轮播","list":[{"id":"6030","name":"亚洲足球"}]},{"name":"轮播","list":[{"id":"6031","name":"亮剑"}]},{"name":"轮播","list":[{"id":"6032","name":"亲子"}]},{"name":"轮播","list":[{"id":"6033","name":"亲宝儿歌"}]},{"name":"轮播","list":[{"id":"6034","name":"人文地理"}]},{"name":"轮播","list":[{"id":"6035","name":"人物传记"}]},{"name":"轮播","list":[{"id":"6036","name":"人生第一次第2季"}]},{"name":"轮播","list":[{"id":"6037","name":"今晚80后"}]},{"name":"轮播","list":[{"id":"6038","name":"代乐乐"}]},{"name":"轮播","list":[{"id":"6039","name":"任泉"}]},{"name":"轮播","list":[{"id":"6040","name":"伪装者"}]},{"name":"轮播","list":[{"id":"6041","name":"体育"}]},{"name":"轮播","list":[{"id":"6042","name":"体育2"}]},{"name":"轮播","list":[{"id":"6043","name":"体育动画"}]},{"name":"轮播","list":[{"id":"6044","name":"体育资讯"}]},{"name":"轮播","list":[{"id":"6045","name":"何以笙箫默"}]},{"name":"轮播","list":[{"id":"6046","name":"何晟铭"}]},{"name":"轮播","list":[{"id":"6047","name":"何润东"}]},{"name":"轮播","list":[{"id":"6048","name":"何炅"}]},{"name":"轮播","list":[{"id":"6049","name":"余心恬"}]},{"name":"轮播","list":[{"id":"6050","name":"佛山龙狮"}]},{"name":"轮播","list":[{"id":"6051","name":"佟丽娅"}]},{"name":"轮播","list":[{"id":"6052","name":"佟大为"}]},{"name":"轮播","list":[{"id":"6053","name":"你是我的姐妹"}]},{"name":"轮播","list":[{"id":"6054","name":"倒霉熊"}]},{"name":"轮播","list":[{"id":"6055","name":"偏偏喜欢你"}]},{"name":"轮播","list":[{"id":"6056","name":"催泪剧场"}]},{"name":"轮播","list":[{"id":"6057","name":"儿歌"}]},{"name":"轮播","list":[{"id":"6058","name":"兄弟剧场"}]},{"name":"轮播","list":[{"id":"6059","name":"克拉恋人"}]},{"name":"轮播","list":[{"id":"6060","name":"党建"}]},{"name":"轮播","list":[{"id":"6061","name":"全员加速中"}]},{"name":"轮播","list":[{"id":"6062","name":"公开课"}]},{"name":"轮播","list":[{"id":"6063","name":"公益"}]},{"name":"轮播","list":[{"id":"6064","name":"养生堂"}]},{"name":"轮播","list":[{"id":"6065","name":"军事瞭望 "}]},{"name":"轮播","list":[{"id":"6066","name":"军旅剧场"}]},{"name":"轮播","list":[{"id":"6067","name":"冯绍峰"}]},{"name":"轮播","list":[{"id":"6068","name":"冯远征"}]},{"name":"轮播","list":[{"id":"6069","name":"冰与火的青春"}]},';
    		$json .= '{"name":"轮播","list":[{"id":"6070","name":"凤凰传奇"}]},{"name":"轮播","list":[{"id":"6071","name":"出彩中国人"}]},{"name":"轮播","list":[{"id":"6072","name":"刘亦菲"}]},{"name":"轮播","list":[{"id":"6073","name":"刘力扬"}]},{"name":"轮播","list":[{"id":"6074","name":"刘威葳"}]},{"name":"轮播","list":[{"id":"6075","name":"刘恺威"}]},{"name":"轮播","list":[{"id":"6076","name":"刘江"}]},{"name":"轮播","list":[{"id":"6077","name":"刘涛"}]},{"name":"轮播","list":[{"id":"6078","name":"刘芸"}]},{"name":"轮播","list":[{"id":"6079","name":"刘诗诗"}]},{"name":"轮播","list":[{"id":"6080","name":"初音未来"}]},{"name":"轮播","list":[{"id":"6081","name":"动作电影"}]},{"name":"轮播","list":[{"id":"6082","name":"动漫"}]},{"name":"轮播","list":[{"id":"6084","name":"动物世界"}]},{"name":"轮播","list":[{"id":"6085","name":"励志剧场"}]},{"name":"轮播","list":[{"id":"6086","name":"包小柏"}]},{"name":"轮播","list":[{"id":"6087","name":"包贝尔"}]},{"name":"轮播","list":[{"id":"6088","name":"包青天"}]},{"name":"轮播","list":[{"id":"6089","name":"北京傲客"}]},{"name":"轮播","list":[{"id":"6090","name":"北平无战事"}]},{"name":"轮播","list":[{"id":"6091","name":"十周嫁出去"}]},{"name":"轮播","list":[{"id":"6092","name":"华语独立音乐"}]},{"name":"轮播","list":[{"id":"6093","name":"华语电影"}]},{"name":"轮播","list":[{"id":"6094","name":"华鼎奖颁奖典礼"}]},{"name":"轮播","list":[{"id":"6096","name":"卡奇社"}]},{"name":"轮播","list":[{"id":"6097","name":"反法西斯"}]},{"name":"轮播","list":[{"id":"6098","name":"变形金刚"}]},{"name":"轮播","list":[{"id":"6099","name":"古典音乐"}]},{"name":"轮播","list":[{"id":"6100","name":"古力娜扎"}]},{"name":"轮播","list":[{"id":"6101","name":"可爱巧虎岛"}]},{"name":"轮播","list":[{"id":"6102","name":"叶璇"}]},{"name":"轮播","list":[{"id":"6103","name":"名侦探柯南"}]},{"name":"轮播","list":[{"id":"6104","name":"后海不是海"}]},{"name":"轮播","list":[{"id":"6105","name":"吕丽萍"}]},{"name":"轮播","list":[{"id":"6106","name":"吕颂贤"}]},{"name":"轮播","list":[{"id":"6107","name":"吴亦凡"}]},{"name":"轮播","list":[{"id":"6108","name":"吴宗宪"}]},{"name":"轮播","list":[{"id":"6109","name":"吴秀波"}]},{"name":"轮播","list":[{"id":"6110","name":"周星驰影视"}]},{"name":"轮播","list":[{"id":"6111","name":"周笔畅"}]},{"name":"轮播","list":[{"id":"6112","name":"周韦彤"}]},{"name":"轮播","list":[{"id":"6113","name":"周韵"}]},{"name":"轮播","list":[{"id":"6114","name":"和平的全盛时代"}]},{"name":"轮播","list":[{"id":"6115","name":"咕力咕力"}]},{"name":"轮播","list":[{"id":"6116","name":"哆啦A梦"}]},{"name":"轮播","list":[{"id":"6117","name":"哈利讲故事"}]},{"name":"轮播","list":[{"id":"6118","name":"唐卡"}]},{"name":"轮播","list":[{"id":"6119","name":"唐嫣"}]},{"name":"轮播","list":[{"id":"6120","name":"唐艺昕"}]},{"name":"轮播","list":[{"id":"6121","name":"喜剧电影"}]},{"name":"轮播","list":[{"id":"6123","name":"嘻哈音乐"}]},{"name":"轮播","list":[{"id":"6124","name":"因为爱情有奇迹"}]},{"name":"轮播","list":[{"id":"6125","name":"团圆饭"}]},{"name":"轮播","list":[{"id":"6126","name":"国安"}]},{"name":"轮播","list":[{"id":"6127","name":"士兵突击"}]},{"name":"轮播","list":[{"id":"6128","name":"备片_乐视视频英超升级提示"}]},{"name":"轮播","list":[{"id":"6129","name":"大头儿子"}]},{"name":"轮播","list":[{"id":"6130","name":"大汉情缘之云中歌"}]},{"name":"轮播","list":[{"id":"6131","name":"大陆女歌手"}]},{"name":"轮播","list":[{"id":"6132","name":"大陆男歌手"}]},{"name":"轮播","list":[{"id":"6133","name":"天才在左疯子在右"}]},{"name":"轮播","list":[{"id":"6135","name":"奔跑吧兄弟"}]},{"name":"轮播","list":[{"id":"6136","name":"奔跑吧兄弟第二季"}]},{"name":"轮播","list":[{"id":"6137","name":"奥特曼"}]},{"name":"轮播","list":[{"id":"6138","name":"女性剧场"}]},{"name":"轮播","list":[{"id":"6140","name":"如果爱"}]},{"name":"轮播","list":[{"id":"6141","name":"姐妹淘剧场"}]},{"name":"轮播","list":[{"id":"6142","name":"姚笛"}]},{"name":"轮播","list":[{"id":"6143","name":"姚芊羽"}]},{"name":"轮播","list":[{"id":"6144","name":"姜武"}]},{"name":"轮播","list":[{"id":"6145","name":"娱乐"}]},{"name":"轮播","list":[{"id":"6146","name":"孔二狗"}]},{"name":"轮播","list":[{"id":"6147","name":"孙俪"}]},{"name":"轮播","list":[{"id":"6148","name":"孙坚"}]},{"name":"轮播","list":[{"id":"6149","name":"孙海英"}]},{"name":"轮播","list":[{"id":"6150","name":"孙红雷"}]},{"name":"轮播","list":[{"id":"6151","name":"孙耀威"}]},{"name":"轮播","list":[{"id":"6152","name":"孙茜"}]},{"name":"轮播","list":[{"id":"6153","name":"孙骁骁"}]},{"name":"轮播","list":[{"id":"6154","name":"孟广美"}]},{"name":"轮播","list":[{"id":"6155","name":"宁浩"}]},{"name":"轮播","list":[{"id":"6156","name":"宋丹丹"}]},{"name":"轮播","list":[{"id":"6157","name":"宋佳伦"}]},{"name":"轮播","list":[{"id":"6158","name":"完美游戏"}]},{"name":"轮播","list":[{"id":"6159","name":"宝宝巴士"}]},{"name":"轮播","list":[{"id":"6160","name":"宠物小精灵"}]},{"name":"轮播","list":[{"id":"6161","name":"宫斗剧场"}]},{"name":"轮播","list":[{"id":"6162","name":"家和万事兴"}]},{"name":"轮播","list":[{"id":"6164","name":"封神英雄"}]},{"name":"轮播","list":[{"id":"6166","name":"小宋佳"}]},{"name":"轮播","list":[{"id":"6167","name":"小时代"}]},{"name":"轮播","list":[{"id":"6168","name":"小马宝莉"}]},{"name":"轮播","list":[{"id":"6170","name":"少年四大名捕"}]},{"name":"轮播","list":[{"id":"6171","name":"少年神探狄仁杰"}]},{"name":"轮播","list":[{"id":"6172","name":"巍子"}]},{"name":"轮播","list":[{"id":"6173","name":"左小祖咒"}]},{"name":"轮播","list":[{"id":"6174","name":"巨神战击队"}]},{"name":"轮播","list":[{"id":"6175","name":"巩峥"}]},{"name":"轮播","list":[{"id":"6176","name":"巴啦啦小魔仙"}]},{"name":"轮播","list":[{"id":"6177","name":"布衣"}]},{"name":"轮播","list":[{"id":"6178","name":"平凡的世界"}]},{"name":"轮播","list":[{"id":"6179","name":"年代剧场"}]},{"name":"轮播","list":[{"id":"6180","name":"幸福归来"}]},{"name":"轮播","list":[{"id":"6181","name":"广厦篮球频道"}]},{"name":"轮播","list":[{"id":"6182","name":"广告频道"}]},{"name":"轮播","list":[{"id":"6183","name":"广场舞"}]},{"name":"轮播","list":[{"id":"6184","name":"应采儿"}]},{"name":"轮播","list":[{"id":"6185","name":"张丰毅"}]},{"name":"轮播","list":[{"id":"6186","name":"张俪"}]},{"name":"轮播","list":[{"id":"6187","name":"张北音乐节第一日"}]},{"name":"轮播","list":[{"id":"6188","name":"张北音乐节第三日"}]},{"name":"轮播","list":[{"id":"6189","name":"张北音乐节第二日"}]},{"name":"轮播","list":[{"id":"6190","name":"张博"}]},{"name":"轮播","list":[{"id":"6191","name":"张慧雯"}]},{"name":"轮播","list":[{"id":"6192","name":"张晓龙"}]},{"name":"轮播","list":[{"id":"6193","name":"张智尧"}]},{"name":"轮播","list":[{"id":"6194","name":"张杰"}]},{"name":"轮播","list":[{"id":"6195","name":"张梓琳"}]},{"name":"轮播","list":[{"id":"6196","name":"张歆艺"}]},{"name":"轮播","list":[{"id":"6197","name":"张睿"}]},{"name":"轮播","list":[{"id":"6198","name":"张艺谋"}]},{"name":"轮播","list":[{"id":"6199","name":"张若昀"}]},{"name":"轮播","list":[{"id":"6200","name":"张译"}]},{"name":"轮播","list":[{"id":"6201","name":"影视剧原声"}]},{"name":"轮播","list":[{"id":"6203","name":"徐克"}]},{"name":"轮播","list":[{"id":"6204","name":"徐百卉"}]},{"name":"轮播","list":[{"id":"6205","name":"徐翠翠"}]},{"name":"轮播","list":[{"id":"6206","name":"德甲"}]},{"name":"轮播","list":[{"id":"6207","name":"情景喜剧剧场"}]},{"name":"轮播","list":[{"id":"6208","name":"想明白了再结婚"}]},{"name":"轮播","list":[{"id":"6209","name":"意甲"}]},{"name":"轮播","list":[{"id":"6210","name":"戏曲"}]},{"name":"轮播","list":[{"id":"6211","name":"成龙影视"}]},{"name":"轮播","list":[{"id":"6212","name":"我是歌手NEW"}]},{"name":"轮播","list":[{"id":"6213","name":"我的老师是传奇"}]},{"name":"轮播","list":[{"id":"6214","name":"我看你有戏"}]},{"name":"轮播","list":[{"id":"6215","name":"战菁一"}]},{"name":"轮播","list":[{"id":"6216","name":"戚薇"}]},{"name":"轮播","list":[{"id":"6217","name":"戴军"}]},{"name":"轮播","list":[{"id":"6218","name":"扭曲机器"}]},{"name":"轮播","list":[{"id":"6219","name":"抓住彩虹的男人"}]},{"name":"轮播","list":[{"id":"6220","name":"抗战剧场"}]},{"name":"轮播","list":[{"id":"6221","name":"拐个皇帝回现代"}]},{"name":"轮播","list":[{"id":"6222","name":"挑战者联盟"}]},{"name":"轮播","list":[{"id":"6223","name":"搏击"}]},{"name":"轮播","list":[{"id":"6224","name":"文化遗产"}]},{"name":"轮播","list":[{"id":"6225","name":"斓曦"}]},{"name":"轮播","list":[{"id":"6226","name":"新济公活佛"}]},{"name":"轮播","list":[{"id":"6227","name":"新番动画"}]},{"name":"轮播","list":[{"id":"6228","name":"新疆广汇"}]},{"name":"轮播","list":[{"id":"6229","name":"方中信"}]},{"name":"轮播","list":[{"id":"6230","name":"旅游"}]},{"name":"轮播","list":[{"id":"6234","name":"日韩电影"}]},{"name":"轮播","list":[{"id":"6235","name":"曹征"}]},{"name":"轮播","list":[{"id":"6236","name":"曹方"}]},{"name":"轮播","list":[{"id":"6237","name":"曹曦文"}]},{"name":"轮播","list":[{"id":"6238","name":"曾沛慈"}]},{"name":"轮播","list":[{"id":"6239","name":"曾艳芬"}]},{"name":"轮播","list":[{"id":"6240","name":"木玛"}]},{"name":"轮播","list":[{"id":"6241","name":"朱亚文"}]},{"name":"轮播","list":[{"id":"6242","name":"朱锐"}]},{"name":"轮播","list":[{"id":"6243","name":"朱雨辰"}]},{"name":"轮播","list":[{"id":"6244","name":"李东学"}]},{"name":"轮播","list":[{"id":"6245","name":"李丹"}]},{"name":"轮播","list":[{"id":"6246","name":"李依晓"}]},{"name":"轮播","list":[{"id":"6247","name":"李健"}]},{"name":"轮播","list":[{"id":"6248","name":"李光洁"}]},{"name":"轮播","list":[{"id":"6249","name":"李威"}]},{"name":"轮播","list":[{"id":"6250","name":"李宇春"}]},{"name":"轮播","list":[{"id":"6251","name":"李宇春演唱会"}]},{"name":"轮播","list":[{"id":"6252","name":"李小璐"}]},{"name":"轮播","list":[{"id":"6253","name":"李小萌"}]},{"name":"轮播","list":[{"id":"6254","name":"李崇霄"}]},{"name":"轮播","list":[{"id":"6255","name":"李念"}]},';
    		$json .= '{"name":"轮播","list":[{"id":"6256","name":"李承铉"}]},{"name":"轮播","list":[{"id":"6257","name":"李易峰"}]},{"name":"轮播","list":[{"id":"6258","name":"李晨"}]},{"name":"轮播","list":[{"id":"6259","name":"李维嘉"}]},{"name":"轮播","list":[{"id":"6260","name":"李艺彤"}]},{"name":"轮播","list":[{"id":"6261","name":"李霄云"}]},{"name":"轮播","list":[{"id":"6262","name":"杜江"}]},{"name":"轮播","list":[{"id":"6263","name":"杜海涛"}]},{"name":"轮播","list":[{"id":"6264","name":"杜淳"}]},{"name":"轮播","list":[{"id":"6265","name":"来自未来的史密特"}]},{"name":"轮播","list":[{"id":"6266","name":"杨幂"}]},{"name":"轮播","list":[{"id":"6267","name":"杨文军"}]},{"name":"轮播","list":[{"id":"6268","name":"杨澜"}]},{"name":"轮播","list":[{"id":"6269","name":"杨烁"}]},{"name":"轮播","list":[{"id":"6270","name":"极品新娘"}]},{"name":"轮播","list":[{"id":"6271","name":"极限挑战"}]},{"name":"轮播","list":[{"id":"6272","name":"极限运动"}]},{"name":"轮播","list":[{"id":"6273","name":"林一峰"}]},{"name":"轮播","list":[{"id":"6274","name":"林申"}]},{"name":"轮播","list":[{"id":"6275","name":"果味VC"}]},{"name":"轮播","list":[{"id":"6276","name":"柳岩"}]},{"name":"轮播","list":[{"id":"6277","name":"査可欣"}]},{"name":"轮播","list":[{"id":"6278","name":"格莱美音乐奖"}]},{"name":"轮播","list":[{"id":"6279","name":"档案"}]},{"name":"轮播","list":[{"id":"6280","name":"梁丹妮"}]},{"name":"轮播","list":[{"id":"6281","name":"樱桃小丸子"}]},{"name":"轮播","list":[{"id":"6282","name":"欢乐喜剧人"}]},{"name":"轮播","list":[{"id":"6283","name":"欧冠"}]},{"name":"轮播","list":[{"id":"6284","name":"欧洲足球"}]},{"name":"轮播","list":[{"id":"6285","name":"欧美卡通"}]},{"name":"轮播","list":[{"id":"6286","name":"欧美摇滚"}]},{"name":"轮播","list":[{"id":"6288","name":"步步惊心"}]},{"name":"轮播","list":[{"id":"6289","name":"武侠剧场"}]},{"name":"轮播","list":[{"id":"6290","name":"武媚娘传奇"}]},{"name":"轮播","list":[{"id":"6291","name":"武林外传"}]},{"name":"轮播","list":[{"id":"6292","name":"段奕宏"}]},{"name":"轮播","list":[{"id":"6293","name":"殷旭"}]},{"name":"轮播","list":[{"id":"6294","name":"毛俊杰"}]},{"name":"轮播","list":[{"id":"6295","name":"毛宁"}]},{"name":"轮播","list":[{"id":"6296","name":"毛晓彤"}]},{"name":"轮播","list":[{"id":"6297","name":"民谣音乐"}]},{"name":"轮播","list":[{"id":"6298","name":"江苏肯帝亚"}]},{"name":"轮播","list":[{"id":"6299","name":"汪峰"}]},{"name":"轮播","list":[{"id":"6300","name":"汽车"}]},{"name":"轮播","list":[{"id":"6301","name":"沈傲君"}]},{"name":"轮播","list":[{"id":"6302","name":"沈泰"}]},{"name":"轮播","list":[{"id":"6303","name":"沈腾"}]},{"name":"轮播","list":[{"id":"6304","name":"沙溢"}]},{"name":"轮播","list":[{"id":"6305","name":"河南足球"}]},{"name":"轮播","list":[{"id":"6306","name":"法甲"}]},{"name":"轮播","list":[{"id":"6307","name":"海清"}]},{"name":"轮播","list":[{"id":"6308","name":"液氧罐头"}]},{"name":"轮播","list":[{"id":"6309","name":"港台女歌手"}]},{"name":"轮播","list":[{"id":"6310","name":"港台男歌手"}]},{"name":"轮播","list":[{"id":"6311","name":"满仓进城"}]},{"name":"轮播","list":[{"id":"6312","name":"演唱会"}]},{"name":"轮播","list":[{"id":"6313","name":"潘之琳"}]},{"name":"轮播","list":[{"id":"6314","name":"潘粤明"}]},{"name":"轮播","list":[{"id":"6316","name":"炉石传说"}]},{"name":"轮播","list":[{"id":"6319","name":"熊出没"}]},{"name":"轮播","list":[{"id":"6320","name":"熊小米"}]},{"name":"轮播","list":[{"id":"6321","name":"爱乐奇"}]},{"name":"轮播","list":[{"id":"6322","name":"爵士音乐"}]},{"name":"轮播","list":[{"id":"6323","name":"爸爸去哪儿第二季"}]},{"name":"轮播","list":[{"id":"6324","name":"牛莉"}]},{"name":"轮播","list":[{"id":"6325","name":"特工卡特"}]},{"name":"轮播","list":[{"id":"6326","name":"狂野自然"}]},{"name":"轮播","list":[{"id":"6327","name":"狐仙"}]},{"name":"轮播","list":[{"id":"6328","name":"猪猪侠"}]},{"name":"轮播","list":[{"id":"6329","name":"猫和老鼠"}]},{"name":"轮播","list":[{"id":"6330","name":"王丽坤"}]},{"name":"轮播","list":[{"id":"6331","name":"王伟"}]},{"name":"轮播","list":[{"id":"6332","name":"王伟光"}]},{"name":"轮播","list":[{"id":"6333","name":"王千源"}]},{"name":"轮播","list":[{"id":"6334","name":"王宝强"}]},{"name":"轮播","list":[{"id":"6335","name":"王珞丹"}]},{"name":"轮播","list":[{"id":"6336","name":"王者归来发布会"}]},{"name":"轮播","list":[{"id":"6337","name":"王雷"}]},{"name":"轮播","list":[{"id":"6339","name":"琅琊榜"}]},{"name":"轮播","list":[{"id":"6340","name":"甄嬛传"}]},{"name":"轮播","list":[{"id":"6341","name":"生活"}]},{"name":"轮播","list":[{"id":"6342","name":"田原"}]},{"name":"轮播","list":[{"id":"6343","name":"田朴珺"}]},{"name":"轮播","list":[{"id":"6344","name":"电子竞技"}]},{"name":"轮播","list":[{"id":"6345","name":"电子音乐"}]},{"name":"轮播","list":[{"id":"6346","name":"电影"}]},{"name":"轮播","list":[{"id":"6347","name":"电影片花"}]},{"name":"轮播","list":[{"id":"6349","name":"电视剧"}]},{"name":"轮播","list":[{"id":"6351","name":"痛仰"}]},{"name":"轮播","list":[{"id":"6352","name":"白凯南"}]},{"name":"轮播","list":[{"id":"6353","name":"白百何"}]},{"name":"轮播","list":[{"id":"6354","name":"神犬小七"}]},{"name":"轮播","list":[{"id":"6355","name":"神话剧场"}]},{"name":"轮播","list":[{"id":"6356","name":"福建浔兴 "}]},{"name":"轮播","list":[{"id":"6358","name":"科教•纪录片"}]},{"name":"轮播","list":[{"id":"6359","name":"秦岚"}]},{"name":"轮播","list":[{"id":"6360","name":"程璧"}]},';
    		$json .= '{"name":"轮播","list":[{"id":"6361","name":"章子怡"}]},{"name":"轮播","list":[{"id":"6362","name":"童瑶"}]},{"name":"轮播","list":[{"id":"6363","name":"笑傲江湖"}]},{"name":"轮播","list":[{"id":"6364","name":"篮球"}]},{"name":"轮播","list":[{"id":"6365","name":"粤语音乐"}]},{"name":"轮播","list":[{"id":"6366","name":"粤语频道"}]},{"name":"轮播","list":[{"id":"6367","name":"红高粱"}]},{"name":"轮播","list":[{"id":"6368","name":"纪录历史"}]},{"name":"轮播","list":[{"id":"6371","name":"综合"}]},{"name":"轮播","list":[{"id":"6372","name":"综艺"}]},{"name":"轮播","list":[{"id":"6374","name":"网球"}]},{"name":"轮播","list":[{"id":"6375","name":"罗大佑"}]},{"name":"轮播","list":[{"id":"6376","name":"罗晋"}]},{"name":"轮播","list":[{"id":"6377","name":"罪案烧脑"}]},{"name":"轮播","list":[{"id":"6378","name":"美克美家"}]},{"name":"轮播","list":[{"id":"6380","name":"美容"}]},{"name":"轮播","list":[{"id":"6383","name":"美食"}]},{"name":"轮播","list":[{"id":"6384","name":"美食解码"}]},{"name":"轮播","list":[{"id":"6385","name":"羽毛球"}]},{"name":"轮播","list":[{"id":"6386","name":"羽泉"}]},{"name":"轮播","list":[{"id":"6387","name":"翁虹"}]},{"name":"轮播","list":[{"id":"6388","name":"老农民"}]},{"name":"轮播","list":[{"id":"6389","name":"聊斋新编"}]},{"name":"轮播","list":[{"id":"6391","name":"胡兵"}]},{"name":"轮播","list":[{"id":"6392","name":"胡夏"}]},{"name":"轮播","list":[{"id":"6393","name":"胡彦斌"}]},{"name":"轮播","list":[{"id":"6394","name":"胡歌"}]},{"name":"轮播","list":[{"id":"6395","name":"脱单剧场"}]},{"name":"轮播","list":[{"id":"6396","name":"自制剧"}]},{"name":"轮播","list":[{"id":"6397","name":"自行车"}]},{"name":"轮播","list":[{"id":"6398","name":"舌尖上的中国"}]},{"name":"轮播","list":[{"id":"6399","name":"艾东"}]},{"name":"轮播","list":[{"id":"6400","name":"艾美奖"}]},{"name":"轮播","list":[{"id":"6401","name":"芈月传"}]},{"name":"轮播","list":[{"id":"6402","name":"芭莎TV"}]},{"name":"轮播","list":[{"id":"6403","name":"花样姐姐"}]},{"name":"轮播","list":[{"id":"6404","name":"花样爷爷"}]},{"name":"轮播","list":[{"id":"6405","name":"苏芒"}]},{"name":"轮播","list":[{"id":"6406","name":"苏阳"}]},{"name":"轮播","list":[{"id":"6407","name":"英超"}]},{"name":"轮播","list":[{"id":"6408","name":"英雄联盟"}]},{"name":"轮播","list":[{"id":"6409","name":"莫小棋"}]},{"name":"轮播","list":[{"id":"6410","name":"萨顶顶"}]},{"name":"轮播","list":[{"id":"6411","name":"董璇"}]},{"name":"轮播","list":[{"id":"6412","name":"蒋欣"}]},{"name":"轮播","list":[{"id":"6413","name":"蒙面歌王"}]},{"name":"轮播","list":[{"id":"6414","name":"蔡少芬"}]},{"name":"轮播","list":[{"id":"6415","name":"薛佳凝"}]},{"name":"轮播","list":[{"id":"6416","name":"薛凯琪"}]},{"name":"轮播","list":[{"id":"6417","name":"虎妈猫爸"}]},{"name":"轮播","list":[{"id":"6419","name":"蜡笔小新"}]},{"name":"轮播","list":[{"id":"6420","name":"裸婚时代"}]},{"name":"轮播","list":[{"id":"6421","name":"西游记"}]},{"name":"轮播","list":[{"id":"6422","name":"西甲"}]},{"name":"轮播","list":[{"id":"6423","name":"许晴"}]},{"name":"轮播","list":[{"id":"6424","name":"调皮王妃"}]},{"name":"轮播","list":[{"id":"6425","name":"谭晶"}]},{"name":"轮播","list":[{"id":"6426","name":"贝瓦儿歌"}]},{"name":"轮播","list":[{"id":"6427","name":"贾乃亮"}]},{"name":"轮播","list":[{"id":"6428","name":"贾青"}]},{"name":"轮播","list":[{"id":"6429","name":"赛车"}]},{"name":"轮播","list":[{"id":"6430","name":"赵丽颖"}]},{"name":"轮播","list":[{"id":"6431","name":"赵咏华"}]},{"name":"轮播","list":[{"id":"6432","name":"赵嘉敏"}]},{"name":"轮播","list":[{"id":"6433","name":"赵子琪"}]},{"name":"轮播","list":[{"id":"6434","name":"赵宝刚"}]},{"name":"轮播","list":[{"id":"6435","name":"赵立新"}]},{"name":"轮播","list":[{"id":"6436","name":"赵粤"}]},{"name":"轮播","list":[{"id":"6437","name":"超级指南"}]},{"name":"轮播","list":[{"id":"6438","name":"超级英雄"}]},{"name":"轮播","list":[{"id":"6439","name":"足球"}]},{"name":"轮播","list":[{"id":"6440","name":"跑步"}]},{"name":"轮播","list":[{"id":"6441","name":"跟播"}]},{"name":"轮播","list":[{"id":"6443","name":"还珠格格"}]},{"name":"轮播","list":[{"id":"6445","name":"邓紫棋"}]},{"name":"轮播","list":[{"id":"6446","name":"邓紫棋北京演唱会"}]},{"name":"轮播","list":[{"id":"6447","name":"邓超"}]},{"name":"轮播","list":[{"id":"6448","name":"邱欣怡"}]},{"name":"轮播","list":[{"id":"6449","name":"郎朗"}]},{"name":"轮播","list":[{"id":"6450","name":"郑元畅"}]},{"name":"轮播","list":[{"id":"6451","name":"郑晓龙"}]},{"name":"轮播","list":[{"id":"6452","name":"郑钧"}]},{"name":"轮播","list":[{"id":"6453","name":"郝云"}]},{"name":"轮播","list":[{"id":"6454","name":"郭品超"}]},{"name":"轮播","list":[{"id":"6455","name":"郭德纲"}]},{"name":"轮播","list":[{"id":"6456","name":"郭晓冬"}]},{"name":"轮播","list":[{"id":"6457","name":"郭涛"}]},{"name":"轮播","list":[{"id":"6458","name":"都市剧场"}]},{"name":"轮播","list":[{"id":"6459","name":"酷爸俏妈"}]},{"name":"轮播","list":[{"id":"6460","name":"重播之王"}]},{"name":"轮播","list":[{"id":"6461","name":"金宝贝"}]},{"name":"轮播","list":[{"id":"6462","name":"金强频道"}]},{"name":"轮播","list":[{"id":"6463","name":"金晨"}]},{"name":"轮播","list":[{"id":"6464","name":"金铭"}]},{"name":"轮播","list":[{"id":"6465","name":"钟汉良"}]},{"name":"轮播","list":[{"id":"6466","name":"钟立风"}]},{"name":"轮播","list":[{"id":"6467","name":"锦绣缘华丽冒险"}]},{"name":"轮播","list":[{"id":"6468","name":"陆川"}]},{"name":"轮播","list":[{"id":"6469","name":"陆毅"}]},{"name":"轮播","list":[{"id":"6470","name":"陈乔恩"}]},{"name":"轮播","list":[{"id":"6471","name":"陈创"}]},{"name":"轮播","list":[{"id":"6472","name":"陈奕迅"}]},{"name":"轮播","list":[{"id":"6473","name":"陈妍希"}]},{"name":"轮播","list":[{"id":"6474","name":"陈小春"}]},{"name":"轮播","list":[{"id":"6475","name":"陈思诚"}]},{"name":"轮播","list":[{"id":"6476","name":"陈数"}]},{"name":"轮播","list":[{"id":"6477","name":"陈晓"}]},{"name":"轮播","list":[{"id":"6478","name":"陈梦露"}]},{"name":"轮播","list":[{"id":"6479","name":"陈楚生"}]},{"name":"轮播","list":[{"id":"6480","name":"陈紫函"}]},{"name":"轮播","list":[{"id":"6481","name":"陈赫"}]},{"name":"轮播","list":[{"id":"6482","name":"隋唐英雄"}]},{"name":"轮播","list":[{"id":"6483","name":"霍思燕"}]},{"name":"轮播","list":[{"id":"6485","name":"非常完美"}]},{"name":"轮播","list":[{"id":"6486","name":"非诚勿扰"}]},{"name":"轮播","list":[{"id":"6490","name":"韩庚"}]},{"name":"轮播","list":[{"id":"6491","name":"韩磊"}]},{"name":"轮播","list":[{"id":"6492","name":"韩红"}]},{"name":"轮播","list":[{"id":"6493","name":"音乐"}]},{"name":"轮播","list":[{"id":"6494","name":"音乐节"}]},{"name":"轮播","list":[{"id":"6496","name":"颖儿"}]},{"name":"轮播","list":[{"id":"6497","name":"风尚"}]},{"name":"轮播","list":[{"id":"6498","name":"香港电影"}]},{"name":"轮播","list":[{"id":"6499","name":"马丽"}]},{"name":"轮播","list":[{"id":"6500","name":"马伊琍"}]},{"name":"轮播","list":[{"id":"6501","name":"马天宇"}]},{"name":"轮播","list":[{"id":"6502","name":"马条"}]},{"name":"轮播","list":[{"id":"6503","name":"马艳丽"}]},{"name":"轮播","list":[{"id":"6504","name":"马苏"}]},{"name":"轮播","list":[{"id":"6505","name":"高云翔"}]},{"name":"轮播","list":[{"id":"6507","name":"高尔夫"}]},';
    		$json .= '{"name":"轮播","list":[{"id":"6508","name":"高尔夫大满贯"}]},{"name":"轮播","list":[{"id":"6509","name":"高尔夫欧巡赛"}]},{"name":"轮播","list":[{"id":"6510","name":"高尔夫美巡赛"}]},{"name":"轮播","list":[{"id":"6511","name":"高露"}]},{"name":"轮播","list":[{"id":"6512","name":"魏楠"}]},{"name":"轮播","list":[{"id":"6513","name":"鹿鼎记"}]},{"name":"轮播","list":[{"id":"6514","name":"黄义达"}]},{"name":"轮播","list":[{"id":"6515","name":"黄婷婷"}]},{"name":"轮播","list":[{"id":"6516","name":"黄小蕾"}]},{"name":"轮播","list":[{"id":"6517","name":"黄斌"}]},{"name":"轮播","list":[{"id":"6518","name":"黄晓明"}]},{"name":"轮播","list":[{"id":"6519","name":"黄百鸣"}]},{"name":"轮播","list":[{"id":"6520","name":"黄磊"}]},{"name":"轮播","list":[{"id":"6521","name":"黄绮珊"}]},{"name":"轮播","list":[{"id":"6522","name":"黄轩"}]},{"name":"轮播","list":[{"id":"6523","name":"黑子的篮球"}]},{"name":"轮播","list":[{"id":"6524","name":"龙门镖局"}]}]';

		$dataArr = json_decode($json,true);
		$addOption = array();
		$isName = [];
		foreach ($dataArr as $key => $value) {
			if (!in_array($value['name'], $isName)) {
				$isName[] = $value['name'];
				$option = array(
					'name'=>$value['name'],
					'channel_id'=>null,
					'parent_id'=>0
				);
				$parent[$value['name']] = $liveChannelName->add($option);
			}
			$addOption[] = array(
				'channel_id'=>$value['list'][0]['id'],
				'name'=>$value['list'][0]['name'],
				'parent_id'=>$parent[$value['name']]
			);
		}
		$liveChannelName->addAll($addOption);
    	}

    	//-------------------------------------
    	/**
    	 * [getDownloadRulesLists 获取接口列表]
    	 * get /Monitoring/home/Monitoring/getInterfaceLists?page=x&pageSize=x&name=x
    	 *
    	 *
    	 * @return [type] [description]
    	 */
    	public function getInterfaceLists()
    	{
    		$get = I('get.');
    		$res = D('InterfaceGroupItem','Monitoring')->getInterfaceLists($get);
    		result(true,$res);
    	}
    	/**
    	 * [addInterfaceWarningRules 添加接口告警规则]
    	 *post /Monitoring/home/Monitoring/addInterfaceWarningRules
    	 *
    	 * {
    	 * 	"projectId":"接口id"
    	 * 	"period":"统计周期" //1分钟，2分钟，5分钟 不能小于1分钟
    	 *           "method":"统计方法"  //总数 count/百分比 percent
    	 *           "compare":"比较方法"  //大于 >/小于 <
    	 *           "warningGroups":["1","2"]  //告警组ID
    	 *           "value":"数值"
    	 * }
    	 */
    	public function addInterfaceWarningRules()
    	{
    		$put = I('put.');
    		if (empty($put['warningGroups']) || !is_array($put['warningGroups']) ) {
    			result('param');
    		}
    		$put['warningGroups'] = array_map('intval', $put['warningGroups']);
    		$put['warningGroups'] = array_unique($put['warningGroups']);
    		$idSql = implode(',',$put['warningGroups']);

    		$warningGroupCount = D('WarningGroup','Monitoring')->getCountForIdSql($idSql);
    		if ($warningGroupCount != count($put['warningGroups'])) {
    			result('告警组不存在');
    		}
    		$rulesId = D('InterfaceWarningRules','Monitoring')->addWarningRules($put);
    		if ($rulesId) {
    			D('InterfaceWarningRulesObject','Monitoring')->addWarningRulesObjectArr($rulesId,$put['warningGroups']);
    		}
    		result();
    	}
    	/**
    	 * [modifyInterfaceWarningRules 修改接口告警规则]
    	 *post /Monitoring/home/Monitoring/modifyInterfaceWarningRules
    	 *
    	 * {
    	 * 	"id":"告警规则id",
    	 * 	"projectId":"接口id"  // 状态码code/下载状态download
    	 * 	"period":"统计周期" //1分钟，2分钟，5分钟 不能小于1分钟
    	 *           "method":"统计方法"  //总数 count/百分比 percent
    	 *           "compare":"比较方法"  //大于 >/小于 <
    	 *           "warningGroups":["1","2"]  //告警组ID
    	 *           "value":"数值"
    	 * }
    	 */
    	public function modifyInterfaceWarningRules()
    	{
    		$put = I('put.');
    		if (empty($put['warningGroups']) || !is_array($put['warningGroups']) ) {
    			result('param');
    		}
    		$put['warningGroups'] = array_map('intval', $put['warningGroups']);
    		$put['warningGroups'] = array_unique($put['warningGroups']);
    		$idSql = implode(',',$put['warningGroups']);

    		$warningGroupCount = D('WarningGroup','Monitoring')->getCountForIdSql($idSql);
    		if ($warningGroupCount != count($put['warningGroups'])) {
    			result('告警组不存在');
    		}
    		$rulesId = D('InterfaceWarningRules','Monitoring')->modifyWarningRules($put);
    		if ($rulesId) {
    			D('InterfaceWarningRulesObject','Monitoring')->modifyWarningRulesObjectArr($rulesId,$put['warningGroups']);
    		}
    		result();
    	}
    	/**
    	 * [deleteInterfaceWarningRules 删除接口警告规则组]
    	 * post /Monitoring/home/Monitoring/deleteInterfaceWarningRules
    	 * ["1","2"]
    	 * @return [type] [description]
    	 */
    	public function deleteInterfaceWarningRules()
    	{
    		$put = I('put.');
    		D('InterfaceWarningRules','Monitoring')->deleteWarningRulesForIdArr($put);
    		result();
    	}
    	/**
    	 * [interfaceWarningRulesLists 接口警告规则组列表]
    	 * get /Monitoring/home/Monitoring/interfaceWarningRulesLists?name=x&page=x&pageSize=x
    	 *
    	 * @return [type] [description]
    	 */
    	public function interfaceWarningRulesLists()
    	{
    		$get = I('get.');
    		$res = D('InterfaceWarningRules','Monitoring')->warningRulesLists($get);
    		result(true,$res);
    	}


    	/**
    	 * [addDownloadWarningRules 添加下载告警规则]
    	 *post /Monitoring/home/Monitoring/addDownloadWarningRules
    	 *
    	 * {
    	 * 	"projectId":"接口id"
    	 * 	"period":"统计周期" //1分钟，2分钟，5分钟 不能小于1分钟
    	 *           "method":"统计方法"  //总数 count/百分比 percent
    	 *           "compare":"比较方法"  //大于 >/小于 <
    	 *           "warningGroups":["1","2"]  //告警组ID
    	 *           "value":"数值"
    	 * }
    	 */
    	public function addDownloadWarningRules()
    	{
    		$put = I('put.');
    		if (empty($put['warningGroups']) || !is_array($put['warningGroups']) ) {
    			result('param');
    		}
    		$put['warningGroups'] = array_map('intval', $put['warningGroups']);
    		$put['warningGroups'] = array_unique($put['warningGroups']);
    		$idSql = implode(',',$put['warningGroups']);

    		$warningGroupCount = D('WarningGroup','Monitoring')->getCountForIdSql($idSql);
    		if ($warningGroupCount != count($put['warningGroups'])) {
    			result('告警组不存在');
    		}
    		$rulesId = D('DownloadWarningRules','Monitoring')->addWarningRules($put);
    		if ($rulesId) {
    			D('DownloadWarningRulesObject','Monitoring')->addWarningRulesObjectArr($rulesId,$put['warningGroups']);
    		}
    		result();
    	}
    	/**
    	 * [modifyDownloadWarningRules 修改下载告警规则]
    	 *post /Monitoring/home/Monitoring/modifyDownloadWarningRules
    	 *
    	 * {
    	 * 	"id":"告警规则id",
    	 * 	"projectId":"接口id"  // 状态码code/下载状态download
    	 * 	"period":"统计周期" //1分钟，2分钟，5分钟 不能小于1分钟
    	 *           "method":"统计方法"  //总数 count/百分比 percent
    	 *           "compare":"比较方法"  //大于 >/小于 <
    	 *           "warningGroups":["1","2"]  //告警组ID
    	 *           "value":"数值"
    	 * }
    	 */
    	public function modifyDownloadWarningRules()
    	{
    		$put = I('put.');
    		if (empty($put['warningGroups']) || !is_array($put['warningGroups']) ) {
    			result('param');
    		}
    		$put['warningGroups'] = array_map('intval', $put['warningGroups']);
    		$put['warningGroups'] = array_unique($put['warningGroups']);
    		$idSql = implode(',',$put['warningGroups']);

    		$warningGroupCount = D('WarningGroup','Monitoring')->getCountForIdSql($idSql);
    		if ($warningGroupCount != count($put['warningGroups'])) {
    			result('告警组不存在');
    		}
    		$rulesId = D('DownloadWarningRules','Monitoring')->modifyWarningRules($put);
    		if ($rulesId) {
    			D('DownloadWarningRulesObject','Monitoring')->modifyWarningRulesObjectArr($rulesId,$put['warningGroups']);
    		}
    		result();
    	}
    	/**
    	 * [deleteDownloadWarningRules 删除下载警告规则组]
    	 * post /Monitoring/home/Monitoring/deleteDownloadWarningRules
    	 * ["1","2"]
    	 * @return [type] [description]
    	 */
    	public function deleteDownloadWarningRules()
    	{
    		$put = I('put.');
    		D('DownloadWarningRules','Monitoring')->deleteWarningRulesForIdArr($put);
    		result();
    	}
    	/**
    	 * [downloadWarningRulesLists 下载警告规则组列表]
    	 * get /Monitoring/home/Monitoring/downloadWarningRulesLists?name=x&page=x&pageSize=x
    	 *
    	 * @return [type] [description]
    	 */
    	public function downloadWarningRulesLists()
    	{
    		$get = I('get.');
    		$res = D('DownloadWarningRules','Monitoring')->warningRulesLists($get);
    		result(true,$res);
    	}

    	//-------------------------------------
    	/**
    	 * [checkDownloadWarningRules 检查下载告警]
    	 * /Monitoring/home/Monitoring/checkDownloadWarningRules
    	 * @return [type] [description]
    	 */
    	public function checkDownloadWarningRules()
    	{

    		//获取告警规则
    		//实例化
    		$downloadWarningRules = D('DownloadWarningRules','Monitoring');
    		$downloadWarningRulesHistory = D('DownloadWarningRulesHistory','Monitoring');
    		$downloadHistory = D('DownloadHistory','Monitoring');
    		$this->checkWarningRules($downloadWarningRules,$downloadWarningRulesHistory,$downloadHistory,'下载');
    		result() ;
    	}
    	/**
    	 * [checkCodeWarningRules 检查状态码告警]
    	 * /Monitoring/home/Monitoring/checkCodeWarningRules
    	 * @param  string $value [description]
    	 * @return [type]        [description]
    	 */
    	public function checkCodeWarningRules()
    	{
    		//获取告警规则
    		$interfaceWarningRules = D('InterfaceWarningRules','Monitoring');
    		$interfaceWarningRulesHistory = D('InterfaceWarningRulesHistory','Monitoring');
    		$interfaceHistory = D('InterfaceHistory','Monitoring');
    		$this->checkWarningRules($interfaceWarningRules,$interfaceWarningRulesHistory,$interfaceHistory,'接口');
    		result() ;
    	}
    	public function checkWarningRules($warningRules,$warningRulesHistory,$warningHistory,$msg)
    	{
    		//加载发邮件类
		vendor('PHPMailer.Mail#class');

    		$warningRulesArr = $warningRules->getAllWarningRulesLists();

         		if (empty($warningRulesArr['extra'])) {
         			result();
         			die;
         		}
		foreach ($warningRulesArr['extra'] as  $value) {
     			//获取历史记录
     			$get['endTime'] = strtotime(date("YmdHi",time())) ;
     			// 获取下载历史记录
	    		// $get['endTime'] = strtotime('2016-04-02 01:49:00') ;

	    		$get['startTime'] = $get['endTime'] - $value['period'] *60;

	    		$get['rulesWhere'] = $value['rulesWhere'];

	    		// $value['code'] = '200-403';

			$arr = explode('-', $value['code']);
			$arr = array_map('intval', $arr);
			if (count($arr) !==2) {
				//数组不等于2
				continue;
			}
			if (array_search(0, $arr)) {
				//有无效数字
				continue;
			}
			$get['startCode'] = $arr[0];
			$get['endCode'] = $arr[1];
			$get['method'] = $value['method'];

			// $get['method'] = 'percent';

			/*if (substr($value['code'], 1,2)  == 'xx') {
				if ($value['method'] == 'count') {
		    			$get['codetypeXX'] = $value['code'];
				}
			}else{
				if ($value['method'] == 'count') {
		    			$get['codetype'] = $value['code'];
				}
			}*/

	    		$res = $warningHistory->getArrForCheckRules($get);

	    		if (empty($res)) {
	    			continue;
	    		}

    			$amount = 0;
    			$downloadHistoryDate = array();
    			foreach ($res as $k => $v) {
    				$amount += $v['num'];
    				$downloadHistoryDate[ $v['codetype'] ][] =  $v;
    			}
			if (empty($downloadHistoryDate[$value['code']])) {
				continue;
			}
			foreach ($downloadHistoryDate[$value['code']] as $k => $v) {

				$isBool = false;
				$report = '';
				//判断正常的还是已发出告警的
				if ($value['status'] == 0) {
					$report = '发生告警';
					//判断是总数还是百分比
					if ($value['method'] == 'count') {
						$num = $v['num'];
						if ($value['compare'] == '>') {
							if ($num > $value['value']) {
								$isBool = true;
							}
						}elseif ($value['compare'] == '<') {
							if ($num < $value['value']) {

								$isBool = true;
							}
						}

					}elseif ($value['method'] == 'percent') {

						$num = $v['num']/$amount*100;

						if ($value['compare'] == '>') {
							if ( $num > $value['value']) {
								$isBool = true;
							}
						}elseif ($value['compare'] == '<') {
							if ( $num < $value['value']) {

								$isBool = true;
							}
						}
					}
				}elseif ($value['status'] == 1) {
					$report = '恢复正常';
					//判断是总数还是百分比
					if ($value['method'] == 'count') {
						$num = $v['num'];


						if ($value['compare'] == '>') {

							if ($num <= $value['value']) {
								$isBool = true;

								// $this->sendEmail($value,$v['num']);
							}

						}elseif ($value['compare'] == '<') {
							if ($num >= $value['value']) {

								$isBool = true;
							}
						}

					}elseif ($value['method'] == 'percent') {
						$num = $v['num']/$amount*100;
						if ($value['compare'] == '>') {
							if (  $num <= $value['value']) {

								$isBool = true;
							}
						}elseif ($value['compare'] == '<') {
							if ( $num >= $value['value']) {

								$isBool = true;
							}
						}
					}
				}
				if ($isBool) {
					//发送邮件
					$msg = $this->sendEmail($warningRules,$warningRulesHistory,$value,$num,$msg,$report,$get);
					if (!empty($msg)) {
						result($msg);
					}
				}
			}
         		}

    	}
    	public function sendEmail($warningRules,$warningRulesHistory,$rules, $value,$msg = '下载',$report = '',$get)
    	{


    		/*var_dump($rules);
    		die;*/
    		set_time_limit(30);
    		// vendor('PHPMailer.Mail#class');

    		if ($rules['compare'] == '>') {
    			$compare = '大于';
    		}elseif ($rules['compare'] == '<') {
    			$compare = '小于';
    		}

    		if ($rules['method'] == 'count') {
    			$method = '总数';
    		}elseif ($rules['method'] == 'percent') {
    			$method = '百分比';
    		}
    		$rulesContent ='详细信息：你的监控项【状态码'.$rules['code'].$method.'在'.$rules['period'].'分钟内'.$compare.$rules['value'].'】' . $report;
    		$time = time();

    		$content = $report.'时间：'.date('Y-m-d H:i:s',$time).'<br />';

		if ($msg == '下载') {
    			$content .= '域名：'  . $rules['rulesWhere'] .'<br />';
    		}elseif ($msg == '接口') {
    			$content .= '接口：'  . $rules['rulesWhere'] .'<br />';
    		}

    		$content .= $rulesContent .'<br />';
    		$content .= '当前值：'.$value;
    		$title = $msg.'监控-'.$rules['rulesName']."-".$report;


    		$winXinContent = $report .'时间：'.date('Y-m-d H:i:s',$time)."\n";
    		if ($msg == '下载') {
    			$winXinContent .= '域名：'  . $rules['rulesWhere'] ."\n";
    		}elseif ($msg == '接口') {
    			$winXinContent .= '接口：'  . $rules['rulesWhere'] ."\n";
    		}

    		$winXinContent .= $rulesContent ."\n";
    		$winXinContent .= '当前值：'.$value;

    		$winXinUrl = C('WEI_XIN_HOST_ADDR'). "Monitoring/Home/WeiXin/clickMenu?startTime=" . ($get['endTime']- 60*30) . "&endTime=" . $get['endTime'] . "&interval=1";

    		if ($msg == '下载') {
    			$winXinUrl .= '&url='  . $rules['rulesWhere'] .'&type=downloadChart&desc='.$rules['rulesName'];
    		}elseif ($msg == '接口') {
    			$winXinUrl .= '&interface='  . $rules['rulesWhere'] .'&type=interfaceChart&desc='.$rules['rulesGroupName'].'-'.$rules['rulesName'];
    		}

    		$winXin = [['description'=>$winXinContent,'title'=>$title,'url'=>$winXinUrl]];

    		// trace('判断EMail组是否存在','检查下载告警规则');
    		$wenXinObject = A('WeiXin');
    		// $wenXinObject =  new \Home\Controller\WenXinController();
    		$wenXinRes = $wenXinObject->sendTextMsg('news',$winXin);
    		$msg = '';
    		if ($wenXinRes['errcode'] !== 0) {
    			$msg .= '发送微信告警失败';
    		}
    		if (!empty($rules['emails'])) {
    			$email  = \Mail::getInstance(C('MAIL_CONFIG'));

    			foreach ($rules['emails'] as $key => $value) {
    				// trace('判断EMail是否为空','检查下载告警规则');

    				if (!empty($value)) {
    					$toEmailArr[] = $value;
    				}else{
    					// trace('判断EMail为空','检查下载告警规则');
    				}
    			}
    			if (!empty($toEmailArr)) {
    				$res = $email->send($toEmailArr, $title, $content);
    			}

    			if ($res === true) {

				// trace('发送告警Email成功：'.$value,'检查下载告警规则');
				$option['id'] = $rules['id'];
				if ($rules['status'] ==1) {
					$option['status']=0;
				}elseif ($rules['status'] ==0) {
					$option['status']=1;
				}

				// trace('修改告警状态','检查下载告警规则');
				$warningRules->modifyWarningRulesStatus($option);
				$option['time'] = $time;
				$option['rules_id'] = $rules['id'];
				if (!empty($option['id'])) {
					unset($option['id']);
				}
				$warningRulesHistory->addWarningRulesHistory($option);
			}else{
				$msg .= '发送Email告警失败';
				// trace('发送告警Email失败：'.$res,'检查下载告警规则');
			}
    		}else{
    			// trace('判断EMail组不存在','检查下载告警规则');
    		}
    		return $msg;
    	}

    	public function getDownloadWarningRules()
    	{
    		$get = I('get.');
    		$res = D('DownloadWarningRules','Monitoring')->getWarningRules($get);
    		$res['result'] = 'ok';
    		echo json_encode($res,JSON_HEX_TAG);
    		die;
    	}
    	public function getInterfaceWarningRules()
    	{
    		$get = I('get.');
    		$res = D('InterfaceWarningRules','Monitoring')->getWarningRules($get);
    		$res['result'] = 'ok';
    		echo json_encode($res,JSON_HEX_TAG);
    		die;
    	}
}