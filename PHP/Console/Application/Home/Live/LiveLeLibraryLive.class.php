<?php
	namespace Home\Live;
	use Think\Model;
	class LiveLeLibraryLive extends Model
	{
		protected $tableName = 'live_letv_lib';
		protected $connection = 'DB_LIVE';
		/*public function __construct()
    		{
		           parent::__construct();
		          isLogin();
    		}*/

    		public function addLeLibrary($put/*,$start,$end*/)
    		{
    			if (empty($put['group'])) {
    				result('param');
    			}

    			if ($res = $this->getOneForGroup($put['group'])) {
    				result('乐视配置组已存在');
    			}

		              $options = array(
                                            	'group'=>$put['group'],
                                        );

                                        if ($this->add($options)) {
                                        	$options = array(
                                        		'version'=>$put['group'],
                                        		'json'=>'{}',
                                    		);
                                        	D('LiveLeLibraryPublish','Live')->publishLeLibrary($options);
                                        }
                                        return ;
    		}
    		public function getOneForGroup($group)
    		{
    			return $this->where("`group`='%s'",array($group))->find();
    		}
    		public function leLibraryLists($get)
    		{
    			$where = '';
    			if (!empty($get['name'])) {
    				$where = "group like '%".$get['name']."%'";
    			}
    			if (!empty($get['page']) && !empty($get['pageSize'])) {
    				$get['page'] = ($get['page'] - 1 )*$get['pageSize'];
    				$res['extra'] = $this->where($where)->limit($get['page'],$get['pageSize'])->select();
    			}else{
    				$res['extra'] = $this->where($where)->select();
    			}

    			if (empty($res['extra'])) {
    				$res['extra'] = array();
    			}
    			return $res;
    		}
    		public function deleteLeLibrary($get)
    		{
    			if (!empty($get['id'])) {

    				if (!$res = $this->find($get['id'])) {
	    				result('该乐视配置不存在');
	    			}
    				if (D('LiveLeLibraryInfo','Live')->where('`group_id`=%d',array($get['id']))->find()) {
    					result('此乐视配置组下存在内容');
    				}
				if ($this->delete($get['id'])) {
					D('LiveLeLibraryPublish','Live')->deletePublishLiveForVersion($res['group']);
                                                	}
    			}
    			return ;

    		}
    		public function getOneForId($id)
    		{
    			return $this->find($id);
    		}

	}
