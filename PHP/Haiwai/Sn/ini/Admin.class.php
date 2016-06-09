<?php 
    /**
  * 
  */

  class Admin
  {

    private $timeout = 30; //CURL请求超时时间

    private $connecttimeout = 30; //CURL连接超时时间

    private $sslVerifypeer = FALSE; //cURL将终止从服务端进行验证
    
    public $redis; //redis

    private $get_sn_host = GET_SN_HOST;//获取sn接口的域名
    private $get_youtobe_host = GET_YOUTUBE_HOST;//获取youtube接口的域名
    
    function __construct()
    {
      $this->redis = new redis();
      if (!$this->redis->connect('127.0.0.1','6379')) {
            die('连接redis失败');
        }
        if (!$this->redis->exists('sn_lists')) {
            $this->syn();
        };
    }
    /**
     * 获取所有套餐
     * @return json
     */
    private function _getPlanLis()
    {
      return $response = $this->_getUrl( $this->get_sn_host . 'channel/interface/getPlanList.htm');
      
    }
    /**
     * 获取sn列表及对应的套餐，有效期信息。
     * @return json
     */
    private function _getSnList($page=1,$pageSize=100)
    {
      return $response = $this->_getUrl( $this->get_sn_host . 'channel/interface/getSnList.htm?page='.$page.'&pageSize='.$pageSize);
      
    }
    /**
     * 获取直播列表最新版本号
     * @return json
     */
    private function _getChannelListVersion()
    {
      return $response = $this->_getUrl( $this->get_sn_host . 'channel/interface/getChannelListVersion.htm');
      
    }
    /**
     * 获取直播列表内容
     * @return json
     */
    private function _getChannelList()
    {
      return $response = $this->_getUrl( $this->get_sn_host . 'channel/interface/getChannelList.htm');
      
    }
    /**
     * 获取youtube分类
     * @return json
     */
    private function _getYoutobeType()
    {
      return $response = $this->_getUrl( $this->get_youtobe_host . 'channel/stb/getYoutbeType.htm');
      
    }
    /**
     * 根据youtube分类获取列表
     * @return json
     */
    private function _getYoutubeByType($typeid,$page=1,$pagesize=20)
    {
      return $response = $this->_getUrl( $this->get_youtobe_host . 'channel/stb/getYoutubeByType.htm?typeId=' . $typeid . '&page=' . $page . '&pageSize=' . $pagesize);
      
    }
    /**
     * 同步sn列表、同步SN对应的套餐信息、同步套餐详细信息到redis
     */
    public function syn()
    {

        //同步sn列表、套餐详细数据到redis
        for ($i=1; $i > 0 ; $i++) { 
            //获取sn列表及对应的套餐，有效期信息
            $getSnList = $this->_getSnList($i,100);
            $getSnList = json_decode($getSnList);
            $getSnList = $getSnList->snLists;
            if (empty($getSnList)) {
                break;
            }
            foreach ($getSnList as $key => $value) {
              $planLists = json_encode($value->planLists,JSON_UNESCAPED_UNICODE);
              $sn = $value->sn;
              $this->redis->sadd('sn_lists',$sn);//sn列表
              $this->redis->set('sn_plan_'.$sn,$planLists);//sn对应套餐信息
              $this->redis->set('sn_balance_' . $sn,$value->balance);//sn对应余额信息

            }
        }

        //同步所有套餐数据到redis
        $PlanLis = $this -> _getPlanLis();
        $PlanLis = json_decode($PlanLis);
        $PlanLis = $PlanLis->planLists;
        
        foreach ($PlanLis as $key => $value) {
             
           $planList = json_encode($value,JSON_UNESCAPED_UNICODE);
            $this->redis->set('plan_detail_'.$value->id,$planList);//套餐详细信息
        }

        //同步直播列表内容数据到redis
        $ChannelList = $this->_getChannelList();
        $this->redis->set('channel_list',$ChannelList);

        //同步youtube分类
        $youtobe_type = $this->_getYoutobeType();

        //同步youtube分类
        $youtobe_type = $this->_getYoutobeType(); 
        $this->redis->set('youtobe_type',$youtobe_type);

        //同步youtube分类列表
        $youtobe_type = json_decode($youtobe_type,true);

        foreach ($youtobe_type as $key => $value) {
          $data = array();
          //根据youtube分类获取列表
          for ($j=1; $j > 0 ; $j++) { 
              
              $youtobe_type_list = $this->_getYoutubeByType($value['id'],$j,20);
              $youtobe_type_list = json_decode($youtobe_type_list);

              $youtobe_type_list = $youtobe_type_list->list;
              
              
              if (empty($youtobe_type_list)) {
                  break;
              }

              $data = array_merge($data,$youtobe_type_list);
          }

          $data = json_encode($data,JSON_UNESCAPED_UNICODE);

          $this->redis->set('youtobe_type_list_'.$value['id'],$data);
        }

    }

    
    /**
     * [addSn 增加sn]
     * @param [int] $id      [增加sn_id]
     * @param string $plan    [原有套餐]
     * @param string $balance [原有余额]
     */
    public function addSn($id,$plan='[]',$balance='0')
    {
        $this->redis->sadd('sn_lists',$id);
        $this->redis->set('sn_plan_'.$id,'[]');//sn对应套餐信息
        $this->redis->set('sn_balance_'.$id,'0');//sn对应余额信息
    }
    public function delSn($id)
    {
        $this->redis->srem('sn_lists',$id);
        $this->redis->del('sn_plan_'.$id);//sn对应套餐信息
        $this->redis->del('sn_balance_'.$id);//sn对应余额信息

    }
    /**
     * [editSn 修改sn]
     * @param  [string] $del_id [删除的sn_id]
     * @param  [string] $add_id [增加的sn_id]
     * @return [int]         
     */
    public function editSn($del_id,$add_id)
    {

      if ($this->redis->sismember('sn_lists',$del_id)&&!$this->redis->sismember('sn_lists',$add_id)) {           
            
            $plan = $this->redis->get('sn_plan_'.$del_id);
            $balance = $this->redis->get('sn_plan_'.$add_id);
            $this->addSn($add_id,$plan,$balance);
            $this->delSn($del_id);
            return  3;//1、添加的账户存在 2、删除的账户不存在 3、修改成功
        }elseif ( $this->redis->sismember('sn_lists',$add_id) ) {
            return 1;
        }elseif(!$this->redis->sismember('sn_lists',$del_id)) {
            return 2;
        }
    }

    public function addSnBalance($id,$val=0)
    {
        $res = $this->redis->get('sn_balance_'.$id);
        $res += $val;
        $this->redis->set('sn_balance_'.$id,$res);
    }
    public function delSnBalance($id,$val=0)
    {
        $res = $this->redis->get('sn_balance_'.$id);
        $res -= $val;
        $this->redis->set('sn_balance_'.$id,$res);
    }
    /**
     * 发送 HTTP GET Request
     * @param   String  $url    要发送到的URL地址
     * @param   Array   $param  参数数组
     * @return  Source OR False 接口返回值或false
     */
    private  function _getUrl($url, $param = array()) 
    {
        if(!empty($param)) 
        {
            $query = http_build_query($param);
            $url = $url . '?' . $query;
        }
        
        //CURL初始化
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerifypeer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_URL, $url);
        $content = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        if(intval($status["http_code"]) == 200) 
        {
            return $content;

        }else
        {
            
            return false;
        }
    } 
  }
?>