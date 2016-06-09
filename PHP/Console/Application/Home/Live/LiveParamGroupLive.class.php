<?php
	namespace Home\Live;
	use Think\Model;
	class LiveParamGroupLive extends Model
	{
		protected $tableName = 'live_param_group';
		protected $connection = 'DB_LIVE';
		/*public function __construct()
    		{
		           parent::__construct();
		          isLogin();
    		}*/
    		public function addGroup($group/*,$start,$end*/)
    		{

    			if (!$res = $this->where("`group`='%s'",array($group))->find()) {
			              $options = array(
	                                            	'group'=>$group,
	                                        );
	                                        if ($this->add($options)) {
	                                        	$options = array(
	                                        		'version'=>$group,
	                                        		'json'=>'{}',
                                        		);
	                                        	D('LiveParamGroupPublish','Live')->publishLiveParam($options);
	                                                	result();
	                                        }else{
	                                                result('unknown');
	                                        }

    			}else{
    				result('参数组已存在');
    			}
    		}
    		public function groupLists()
    		{
    			$res['extra'] = $this->select();
    			if ($res['extra']) {
    				result(true,$res);
    			}else{
    				$res['extra'] = array();
    				result(true,$res);
    			}
    		}
    		public function deleteGroup($id)
    		{
    			$res = $this->find($id);
    			if ($res) {
    				if (D('LiveParamGroupInfo','Live')->where('`group_id`=%d',array($id))->find()) {
    					result('此参数组下存在内容');
    				}else{
    					if ($this->delete($id)) {
						D('LiveParamGroupPublish','Live')->deletePublishLiveForVersion($res['group']);
                                                          		result();
                                                    	}
    				}
    			}else{
    				result('参数组不存在');
    			}
    		}
    		public function getOneForId($id)
    		{
    			return $this->find($id);
    		}
                    	/*public function publishLiveParam($action,$group)
                    	{
                    		switch ($action) {
                                    case 'add':
                                        $data['event'] = 'add';

                                        break;
                                    case 'delete':
                            		$data['event'] = 'delete';

                                        break;
                                    default:
                                        result('操作不正确');
                                        break;
                                }
                                $data['version'] = $group;
                                echo $data = json_encode($data);
                                die;
                                $res = postUrl(LIVE_PARAM_ACCESS_ADDR . 'access/publish/liveParam',$data);

                                if ($res === false) {
                                    result('发布地址出错');
                                }else{
                                    $res = json_decode($res,true);
                                    if ($res['result'] == 'ok') {
                                       return true;
                                    }else{
                                        if (isset($res['reason'])) {
                                            result($res['reason']);
                                        }else{
                                            result('unknown');
                                        }
                                    }
                                }
                    	}*/
    		/*public function modifyGroup($id,$group)
    		{
    			$res = $this->find($id);
    			if ($res) {
                                    	$res = $this->where("`id`!=%d and `group`='%s'",array($id,$group))->find();
	                        	if (!$res) {
		                            	$options = array(
			                                'group'=>$group
		                        	);
	                            		$res = $this->where('`id`=%d',array($id))->save($options);
	                        		if ($res) {
                                                            	result();
                                                    	}else{
                                                            	result('unknown');
                                                    	}
	                        	}else{
	                           		 result('参数组已存在');
	                        	}
    			}else{
    				result('参数组不存在');
    			}
    		}*/

	}
