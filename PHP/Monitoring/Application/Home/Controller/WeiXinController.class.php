<?php
namespace Home\Controller;
use Think\Controller;

class WeiXinController extends Controller {

    	/**
     	* 访问不存在的地址
     	*
     	*/
     	protected $rulesPageAddr = 'Pages/monitor/mobile/html/';
    	public function index(){        //根据当前控制器名来判断要执行那个城市的操作

    	}
    	public function __construct()
    	{
	        	parent::__construct();
	       	$this->getAccessToken();
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


    	public function getAccessToken()
    	{
    		$weixinToken = D('WeixinToken','Monitoring');
    		$res = $weixinToken ->getOne();
    		$bool = false;

    		if ($res && !empty(session('wenXinTokey'))) {

    			if ( ( ( $res['time']+$res['expires'] - 180) - time()) <= 0 ) {
    				$bool = true;
    			}
    		}else{
    			$bool = true;
    		}

    		if($bool){
    			$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=". C('WIN_XIN_CORP_ID') ."&corpsecret=".C('WIN_XIN_CORP_SECRET');
	    		$json = getUrl($url);
	    		// $json = '{"access_token":"a3OGYjvW-KxYOThoAansVXgm_SDb7qL9AeQxP4sM-Siryy_q0frq9B3vmThrV8BK","expires_in":7200}';
	    		$arr = json_decode($json,true);
	    		$weixinToken->addToken($arr);
	    		session('wenXinTokey',$arr['access_token']);
    		}

    	}

    	public function sendTextMsg($type,$content)
    	{

    		$url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=' . session('wenXinTokey');
		$str = '{"touser": "@all",
			"msgtype": "'.$type.'",
			"agentid": 0,';
		if ($type == 'text') {
			$str .= 	'"text": {
				"content": "'.$content.'";
			}';
		}elseif ($type == 'news') {
			$str .= '"news": {
       				"articles":[';
			$articles = '';
       			foreach ($content as  $value) {
       				$articles .= '{';
       				$articlesAttr = isset($value['title'])?'"title": "' . $value['title'].'",':'';
       				$articlesAttr .= isset($value['description'])?'"description": "'.$value['description'].'",':'';
       				$articlesAttr .= isset($value['url'])?'"url": "'.$value['url'].'",':'';
       				$articlesAttr .= isset($value['picurl'])?'"picurl": "'.$value['picurl'].'",':'';
               			$articles .= trim($articlesAttr,',');
           				$articles .= '},';
       			}

       			$str .= trim($articles,',');
       			$str .= 	']}';
		}
		$str .= '}';

		$res = postUrl($url,$str);
		$res = json_decode($res,true);
		// return '11';
		return $res;

    	}

    	public function getAppInfo()
    	{
    		$url = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get?access_token='.session('wenXinTokey').'&agentid=0';
    		$res = getUrl($url);
    		echo $res;
    	}
    	public function setApp()
    	{
    		$url = 'https://qyapi.weixin.qq.com/cgi-bin/agent/set?access_token='.session('wenXinTokey');

		$str = '{
				"agentid": "1",
				"report_location_flag": "0",
				"logo_mediaid": "xxxxx",
				"name": "测试应用",
				"description": "这是一个测试添加新应用",
				"redirect_domain": "http://www.qq.com",
				"isreportuser":0,
				"isreportenter":0,
				"home_url":"http://www.qq.com"
			}';
		$res = postUrl($url,$str);
		$res = json_decode($res,true);
		if ($res['errcode'] !== 0 ) {
			result($res['errmsg']);
		}
		result();
    	}
    	public function getMenu()
    	{
    		$url = 'https://qyapi.weixin.qq.com/cgi-bin/menu/get?access_token='.session('wenXinTokey').'&agentid=0';
    		$res = getUrl($url);
    		var_dump($res);
    	}
    	public function setMenu()
    	{

    		$url = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token='.session('wenXinTokey').'&agentid=0';
    		$json = '{
		   "button":[
		       {
		           "name":"告警查询",
		           "sub_button":[
		               {
		                   "type":"view",
		                   "name":"未解除告警",
		                   "url":"'.C('WEI_XIN_HOST_ADDR') . 'Monitoring/Home/WeiXin/clickMenu?type=warningList"
		               }
		           ]
		       },
		       {
		           "name":"监控查询",
		           "sub_button":[
		               {
		                   "type":"view",
		                   "name":"接口查询",
		                   "url":"'.C('WEI_XIN_HOST_ADDR') . 'Monitoring/Home/WeiXin/clickMenu?type=interface"
		               },
		               {
		                   "type":"view",
		                   "name":"下载查询",
		                   "url":"'.C('WEI_XIN_HOST_ADDR') . 'Monitoring/Home/WeiXin/clickMenu?type=download"
		               }
		           ]
		      }
		   ]
		}';
		/*echo strlen('"url":"http://'.$_SERVER['HTTP_HOST'] . '/Monitoring/Home/WeiXin/clickMenu/"');
		die;*/
    		$res = postUrl($url,$json);
    		echo ($res);
    	}

    	public function loginAuth($redirectUri= '')
    	{
    		$address = trim(__SELF__,'/');

    		$arr = explode('/', $address);

    		unset($arr[count($arr)-1]);
    		$redirectUri =C('WEI_XIN_HOST_ADDR') . implode('/', $arr) . '/getUserInfo';


    		$redirectUri = urlencode($redirectUri);
    		echo $url = 'https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id=' . C('WIN_XIN_CORP_ID') . '&redirect_uri= ' . $redirectUri . '&state=x1xx&usertype=member';
    	}



    	public function clickMenu()
    	{
    		$get = I('get.');
    		$where = '';

    		if (!empty($get['type'])) {
    			$where .= $get['type'].'.html';
    			$state['type'] = $get['type'].'.html';
    		}
    		if (!empty($get['startTime'])&&!empty($get['endTime'])) {
    			$where .= '#startTime='.$get['startTime'] .'&endTime='.$get['endTime'].'&interval=1';
    			$state['startTime'] = $get['startTime'];
    		}

    		if (!empty($get['url'])) {
    			$state['url'] = $get['url'];
    			$where .= '&url=' .$get['url'];
    		}elseif (!empty($get['interface'])) {
    			$state['interface'] = $get['interface'];
    			$where .= '&interface=' .$get['interface'];
    		}
    		if (!empty($get['desc'])) {
    			$state['desc'] = $get['desc'];
    			$where .= '&desc = ' .$get['desc'];
    		}

    		$this->rulesPageAddr .= $where;
    		// session('weixinLogin',null);

    		if (empty(session('weixinLogin'))) {
    			$where = json_encode($state,JSON_UNESCAPED_UNICODE);

    			header('Location:' . $this->oauthCode(base64_encode($where)));
    		}else{
    			header('Location:'.C('WEI_XIN_HOST_ADDR') .$this->rulesPageAddr);
    		}
    	}

    	public function oauthCode($msg='')
    	{

    		$url =  'https://open.weixin.qq.com/connect/oauth2/authorize';
    		$url .=  '?appid='.C('WIN_XIN_CORP_ID');

    		/*$address = trim(__SELF__,'/');

    		$arr = explode('/', $address);

    		unset($arr[count($arr)-1]);
    		var_dump(__CONTROLLER__);
    		die;*/

    		// $url .= '&redirect_uri='. 'http://'.$_SERVER['HTTP_HOST'] . __CONTROLLER__. '/getUserInfo';

    		$url .= '&redirect_uri='.urlencode( C('WEI_XIN_HOST_ADDR') . __CONTROLLER__ . '/getUserInfo');

    		return $url .= '&response_type=code&scope=snsapi_base&state='.$msg.'#wechat_redirect';
    		die;
    	}
    	public function getUserInfo()
    	{

    		$get = I('get.');

    		$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=' . session('wenXinTokey'). '&code=' . $get['code'];
    		$res = getUrl($url);

    		$res = json_decode($res,true);
    		if (!empty($res['UserId'])) {
    			session('weixinLogin',array('user'=>$res['UserId'],'name'=>$res['UserId']));
    			$state = base64_decode($get['state']);
    			$state = json_decode($state,true);
    			$where = '';

    			if (!empty($state['type'])) {
	    			$where .= $state['type'];
	    		}

	    		if (!empty($state['startTime'])) {
	    			$where .= '#startTime='.$state['startTime'] .'&endTime='.($state['startTime'] + 30*60).'&interval=1';
	    		}

	    		if (!empty($state['url'])) {
	    			$where .= '&url=' .$state['url'];
	    		}elseif (!empty($state['interface'])) {
	    			$where .= '&interface=' .$state['interface'];
	    		}
	    		if (!empty($state['desc'])) {
	    			$where .= '&desc = ' .$state['desc'];
	    		}

    			$where = !empty($where)?$where:'';

    			header('location:'.C('WEI_XIN_HOST_ADDR') . $this->rulesPageAddr. $where);
    		}else{
    			if (!empty($res['errmsg'])) {
    				echo $res['errmsg'];
    			}

    		}

    	}
    	/*public function openUrl()
    	{

    	}*/
    	public function callbackWinXin()
    	{
    		$get = I('get.');
    		vendor('WinXinPHP.WXBizMsgCrypt');

    		// 假设企业号在公众平台上设置的参数如下
		/*$encodingAesKey = "jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C";
		$token = "QDG6eK";
		$corpId = "wx5823bf96d3bd56c7";


		$sVerifyMsgSig = "5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3";
		$sVerifyTimeStamp = "1409659589";
		$sVerifyNonce = "263014780";
		$sVerifyEchoStr = "P9nAzCzyDtyTWESHep1vC5X9xho/qYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp+4RPcs8TgAE7OaBO+FZXvnaqQ==";
		*/


		//验证地址
		$corpId = C('WIN_XIN_CORP_ID');

		$encodingAesKey = "Mv9WqaBP9EHAYkecEWbIGtG9NrdoKPWBOjRRT62EtVl";  //jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C
		$token = "cR9PM6Ov6w45LLSUpJQG1ZUI73";

		$sVerifyMsgSig = $get['msg_signature'];
		$sVerifyTimeStamp = $get['timestamp'];
		$sVerifyNonce = $get['nonce'];
		$sVerifyEchoStr = $get['echostr'];

		// 需要返回的明文

		$sEchoStr = "";
		$wxcpt = new \WXBizMsgCrypt($token, $encodingAesKey, $corpId);


		$errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
		if ($errCode == 0) {
			// 验证URL成功，将sEchoStr返回
			// HttpUtils.SetResponce($sEchoStr);
			echo $sEchoStr;
			die;
		}


		/*//对用户回复的消息解密
		// $sReqMsgSig = HttpUtils.ParseUrl("msg_signature");
		$sReqMsgSig = "477715d11cdb4164915debcba66cb864d751f3e6";
		// $sReqTimeStamp = HttpUtils.ParseUrl("timestamp");
		$sReqTimeStamp = "1409659813";
		// $sReqNonce = HttpUtils.ParseUrl("nonce");
		$sReqNonce = "1372623149";

		// post请求的密文数据
		// $sReqData = HttpUtils.PostData();
		$sReqData = "<xml>";
		$sReqData .= "<ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName>";
		$sReqData .= "<Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt>";
		$sReqData .= "<AgentID><![CDATA[218]]></AgentID>";
		$sReqData .= "</xml>";*/


		$sReqMsgSig = $get['msg_signature'];
		$sReqTimeStamp = $get['timestamp'];
		$sReqNonce = $get['nonce'];
		$sReqData = $get['echostr'];


		$sMsg = "";  // 解析之后的明文
		$errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
		if ($errCode == 0) {
			// 解密成功，sMsg即为xml格式的明文
			// TODO: 对明文的处理
			// For example:
			$xml = new \DOMDocument();
			$xml->loadXML($sMsg);
			/*$xmlObject = simplexml_load_string($sMsg);

			echo "<pre>";
			// var_dump($xml);
			$arr = json_decode(json_encode((array)$xmlObject),true);
			// var_dump($arr['Content']);*/


			$content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;
			// print("content: " . $content . "\n\n");
		} else {
			print("ERR: " . $errCode . "\n\n");
			//exit(-1);
		}



		//企业回复用户消息的加密
		// 需要发送的明文
		/*$sRespData = "<xml>";

		$sRespData .= "<ToUserName><![CDATA[mycreate]]></ToUserName>";
		$sRespData .= "<FromUserName><![CDATA[wx5823bf96d3bd56c7]]></FromUserName>";

		$sRespData .= "<CreateTime>1348831860</CreateTime>";
		$sRespData .= "<MsgType><![CDATA[text]]></MsgType>";
		$sRespData .= "<Content><![CDATA[this is a test]]></Content>";
		$sRespData .= "<MsgId>1234567890123456</MsgId>";
		$sRespData .= "<AgentID>128</AgentID>";
		$sRespData .= "</xml>";*/



		if ($content == 'app') {
			$sRespData = "<xml>";
			$sRespData .= "<ToUserName><![CDATA[".$corpId."]]></ToUserName>";
			$sRespData .= "<FromUserName><![CDATA[".$corpId."]]></FromUserName>";
			$sRespData .= "<CreateTime>".time()."</CreateTime>";
			$sRespData .= "<MsgType><![CDATA[text]]></MsgType>";
			$sRespData .= "<Content><![CDATA[应用APP]]></Content>";
			$sRespData .= "<MsgId>1</MsgId>";
			$sRespData .= "<AgentID>0</AgentID>";
			$sRespData .= "</xml>";
		}


		$sEncryptMsg = ""; //xml格式的密文
		$errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);

		if ($errCode == 0) {
			echo $sEncryptMsg;
			// TODO:
			// 加密成功，企业需要将加密之后的sEncryptMsg返回
			// HttpUtils.SetResponce($sEncryptMsg);  //回复加密之后的密文
			// echo $sEncryptMsg;
		} else {
			print("ERR: " . $errCode . "\n\n");
			// exit(-1);
		}
    	}
}