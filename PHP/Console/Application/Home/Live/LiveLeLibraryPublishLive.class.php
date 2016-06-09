<?php
	namespace Home\Live;
	use Think\Model;
	class LiveLeLibraryPublishLive extends Model
	{
		protected $tableName = 'live_letv_lib_publish';
		protected $connection = 'DB_LIVE';
		/*public function __construct()
    		{
		           parent::__construct();
		          isLogin();
    		}*/
    		public function publishLeLibrary($put)
    		{
    			if (empty($put['version']) || empty($put['json'])) {
    				result('param');
    			}
    			if ($res = $this->getOneForVersion($put['version'])) {
    				$options = array(
    					'json'=>$put['json'],
    					'time'=>time(),
				);
    				$this->where("id = %d",array($res['id']))->save($options);
    			}else{
    				$options = array(
    					'version'=>$put['version'],
    					'json'=>$put['json'],
    					'time'=>time(),
				);
    				$this->add($options);
    			}
    			return;
    		}
    		public function getOneForVersion($version)
    		{
    			return $this->where("version ='%s'",array($version))->find();
    		}
    		public function deletePublishLiveForVersion($version)
    		{
    			if ($res = $this->getOneForVersion($version)) {
    				$this->where("id =%d",array($res['id']))->delete();
    			}
    			return;
    		}
	}
