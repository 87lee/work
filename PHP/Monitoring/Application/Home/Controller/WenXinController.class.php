<?php
namespace Home\Controller;
use Think\Controller;

class WenXinController extends Controller {

    	/**
     	* 访问不存在的地址
     	*
     	*/

    	public function index(){        //根据当前控制器名来判断要执行那个城市的操作

    	}
    	public function __construct()
    	{
	        	parent::__construct();
	       	$this->getAccessToken();
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
    		if ($res) {

    			if ( (time()-$res['time']-$res['expires']-180) <=0 ) {
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
    		}

    	}

    	public function sendTextMsg($type,$content)
    	{

    		/*echo strlen(session('wenXinTokey'));
    		die;*/

    		$url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=' . session('wenXinTokey');

    		/*{
		   "errcode": 0,
		   "errmsg": "ok",
		   "invaliduser": "UserID1",
		   "invalidparty":"PartyID1",
		   "invalidtag":"TagID1"
		}

		{
				   "touser": "@all",
				   "toparty": " PartyID1 | PartyID2 ",
				   "totag": " TagID1 | TagID2 ",
				   "msgtype": "text",
				   "agentid": 1,
				   "text": {
				       "content": "测试发送文本消息"
				   },
				   "safe":"0"
				}
		*/
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
    		/*{
		   "agentid": "5",
		   "report_location_flag": "0",
		   "logo_mediaid": "xxxxx",
		   "name": "NAME",
		   "description": "DESC",
		   "redirect_domain": "xxxxxx",
		   "isreportuser":0,
		   "isreportenter":0,
		   "home_url":"http://www.qq.com"
		}*/
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
		                   "url":"http://www.baidu.com/"
		               }
		           ]
		       },
		       {
		           "name":"监控查询",
		           "sub_button":[
		               {
		                   "type":"view",
		                   "name":"接口查询",
		                   "url":"'.$_SERVER['HTTP_HOST'] . "/Page/monitor/charts.html"  .'"
		               },
		               {
		                   "type":"view",
		                   "name":"下载查询",
		                   "url":"'.$_SERVER['HTTP_HOST'] . "/Page/monitor/charts.html"  .'"
		               }
		           ]
		      }
		   ]
		}';
    		$res = postUrl($url,$json);
    		var_dump($res);
    	}
    	public function checkLogin($redirectUri)
    	{
    		$redirectUri = urlencode($redirectUri);
    		$url = 'https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id=' . C('WIN_XIN_CORP_ID') . '&redirect_uri= ' . $redirectUri . '&state=xxxx&usertype=member';
    	}
    	public function oauthCode()
    	{
    		$url =  'https://open.weixin.qq.com/connect/oauth2/authorize';
    		$url .=  '?appid='.C('WIN_XIN_CORP_ID');

    		$arr = explode('/', __SELF__);
    		unset($arr[count($arr)-1]);

    		$url .= '&redirect_uri='.urlencode( $_SERVER['HTTP_HOST'] . implode('/', $arr) . '/getUserInfo');
    		$url .= '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
    		$test = getUrl($url);
    		var_dump($test);
    	}
    	public function getUserInfo()
    	{
    		$get = I('get.');
    		$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=' . C('WIN_XIN_CORP_ID'). '&code=' . $get['code'];
    		$test = getUrl($url);
    		var_dump($test);
    	}
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