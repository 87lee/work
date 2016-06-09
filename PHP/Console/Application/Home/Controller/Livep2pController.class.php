<?php
	namespace Home\Controller;
	use Think\Controller;
	class Livep2pController extends Controller {
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

	    	public function getOnlineSearch()
	    	{
	    		$get = I('get.');
    			switch ($get['param']) {
    				case 'chid':
    					if (!empty($get['chid'])) {
			    			$res = getUrl( C('LIVE_P2P_ONLINE_SEARCH_HOST').'FileClientCnt?p={"ChId":"'.$get['chid'].'"}' );
			    		}else{
			    			$res = getUrl( C('LIVE_P2P_ONLINE_SEARCH_HOST').'FileClientCnt?p={}' );
			    		}
    					break;
    				case 'info':
			    		$res = getUrl(C('LIVE_P2P_ONLINE_SEARCH_HOST').'PeerInfo?p={"id":"'.$get['id'] . '"}');

    					break;
    				default:
    					$res = getUrl(C('LIVE_P2P_ONLINE_SEARCH_HOST').'AllClientCnt?p=');
    					break;
    			}
	    		if (empty(trim($res))) {
	    			result('远程获取数据出错');
	    		}
	    		// echo cbc_decontent($res);
			echo $res;
	    		// result(true,$res);
	    	}
	}
