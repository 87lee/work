<?php 
	namespace Home\Model;
	use Think\Model;
	class LiveParamGroupInfoModel extends Model 
	{
		protected $tableName = 'live_param_group_info'; 
		
		/*public function __construct()  
    		{  
		           parent::__construct();
		          isLogin(); 
    		}*/
    		public function addGroupParam($groupId,$param,$value,$type,$desc,$default)
    		{
    			$res = M('LiveParamGroup')->find($groupId);
    			if ($res) {
    				// $this->where('`param=%s`',array($param))->find();
    				if (!$this->where("`param`='%s' and `group_id`=%d",array($param,$groupId))->find()) {
    					/*if ($default ==='true') {
    						$options = array(
		                                            	'default'=>'false'
		                               		);
		                                        	$res = $this->where("`group_id`=%d and `default`='true'",array($groupId))->save($options);
    					}*/
    					$options = array(
	    					'group_id'=>$groupId,
	    					'desc'=>$desc,
	    					'param'=>$param,
	    					'value'=>$value,
	    					'type'=>$type,
	    					'default'=>$default
					);
                        
                                                    $this->publishLiveParam('add',$options);
	    				if ($this->add($options)) {
	    					result();
	    				}else{
	    					result('unknown');
	    				}
    				}else{
    					result('参数组参数已存在');
    				}
    				
    			}else{
    				result('参数组不存在');
    			}
    		}
    		public function groupParamLists($groupId = '')
    		{
    			if ($groupId == '') {
    				result('param');
    				$res['extra'] = $this->where()->select();
	    			if ($res) {
	    				result(true,$res);
	    			}else{
	    				$res['extra'] = array();
	    				result(true,$res);
	    			}
    			}else{
    				$group['extra'] = $this->field('type')->where('`group_id`=%d',array($groupId))->group('type')->select();
	    			if ($group) {
	    				foreach ($group['extra'] as $key => $value) {
	    					$group['extra'][$key]['params'] = $this->field('id,param,value,desc,default')->where("`group_id`=%d and `type`='%s'",array($groupId,$value['type']))->select();
	    					if (empty($group['extra'][$key]['params'])) {
	    						$group['extra'][$key]['params'] = array();
	    					}
	    				}
	    				result(true,$group);
	    			}else{
	    				$group['extra'] = array();
	    				result(true,$group);
	    			}
    			}
    		}
    		public function deleteGroupParam($id)
    		{
    			$res = $this->find($id);
    			if ($res) {
    				$this->publishLiveParam('delete',$res);
				if ($this->delete($id)) {
                                                  	result();
                                        	}else{
                                                	result('unknown');
                                        	}
    				
    			}else{
    				result('参数组参数不存在');
    			}
    		}
    		public function modifyGroupParam($id,$value,$default)
    		{
    			$res = $this->find($id);
    			$group_id = $res['group_id'];
    			if ($res) {
    				if ($default === 'true') {
    					//发布
    					$res['value']=$value;
    					$this->publishLiveParam('modify',$res);

	                                        	/*$options = array(
	                                            	'default'=>'false'
	                               		);
	                                        	$res = $this->where("`group_id`=%d and `default`='true'",array($group_id))->save($options);
	                                        	*/
	                                        	$options = array(
	                                            	'default'=>'true',
	                                            	'value'=>$value
	                               		);
	                                }elseif ($default === 'false') {
	                                	//发布
	                                	$res['value']=$value;
                                           	$this->publishLiveParam('modify',$res);

	                                    	$options = array(
	                                            	'default'=>'false',
	                                            	'value'=>$value
	                               		);
	                                }else{
	                                    	result('param');
	                                }
    				$res = $this->where("`group_id`=%d and `id`=%d",array($group_id,$id))->save($options);
				if ($res) {
                                                  	result();
                                        	}else{
                                                	result('unknown');
                                        	}
    				
    			}else{
    				result('参数组参数不存在');
    			}
    		}

                     public function publishLiveParam($action,$options)
                     {
                                switch ($action) {
                                    case 'add':
                                        $data['params'] = $this->field('param,value')->where("`group_id`=%d",array($options['group_id']))->select();
                                        $group = D('LiveParamGroup')->field('group')->find($options['group_id']);
                                        $data['version'] = $group['group'];
                                        $arr = array(
                                            'param'=>$options['param'],
                                            'value'=>$options['value']
                                        );
                                        if (!empty($data['params'])) {
                                            array_unshift($data['params'],$arr);
                                        }else{
                                            $data['params'] = array($arr);
                                        }
                                        break;
                                    case 'modify':
                                        $data['params'] = $this->field('param,value')->where("`group_id`=%d and `id`!=%d",array($options['group_id'],$options['id']))->select();
                                        $group = D('LiveParamGroup')->field('group')->find($options['group_id']);
                                        $data['version'] = $group['group'];
                                        $arr = array(
                                            'param'=>$options['param'],
                                            'value'=>$options['value']
                                        );
                                        if (!empty($data['params'])) {
                                            array_unshift($data['params'],$arr);
                                        }else{
                                            $data['params'] = array($arr);
                                        }
                                        break;
                                    case 'delete':
                                        $data['params'] = $this->field('param,value')->where("`group_id`=%d and `id`!=%d",array($options['group_id'],$options['id']))->select();
                                        $group = D('LiveParamGroup')->field('group')->find($options['group_id']);
                                        $data['version'] = $group['group'];
                                        if (empty($data['params'])) {
                                            $data['params'] = array();
                                        }
                                        break;
                                    default:
                                        result('操作不正确');
                                        break;
                                }
                                $data['event'] = 'modify';
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
	}
