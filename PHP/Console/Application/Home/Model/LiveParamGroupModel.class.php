<?php 
	namespace Home\Model;
	use Think\Model;
	class LiveParamGroupModel extends Model 
	{
		protected $tableName = 'live_param_group'; 
		
		/*public function __construct()  
    		{  
		           parent::__construct();
		          isLogin(); 
    		}*/
    		public function addGroup($group/*,$start,$end*/)
    		{
    			$res = $this->where("`group`='%s'",array($group))->find();
    			if (!$res) {
			              $options = array(
	                                            'group'=>$group,
	                                            
	                                        );
	                                        if ($this->add($options)) {
	                                        		$this->publishLiveParam('add',$group);
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
    				if (M('LiveParamGroupInfo')->where('`group_id`=%d',array($id))->find()) {
    					result('此参数组下存在内容');
    				}else{
    					if ($this->delete($id)) {
    						
    						$this->publishLiveParam('delete',$res['group']);
                                                          	result();
                                                    	}else{
                                                                result('unknown');
                                                    	}
    				}
    			}else{
    				result('参数组不存在');
    			}
    		}
                    	public function publishLiveParam($action,$group)
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
                                $data = json_encode($data);
                                
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
                    	}
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
