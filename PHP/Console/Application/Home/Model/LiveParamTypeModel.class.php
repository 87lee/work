<?php 
	namespace Home\Model;
	use Think\Model;
	class LiveParamTypeModel extends Model 
	{
		protected $tableName = 'live_param_type'; 
		
		/*public function __construct()  
    		{  
		          
    		}*/
    		public function addType($type)
    		{
    			$res = $this->where("`type`='%s'",array($type))->find();
    			if (!$res) {
		              $options = array(
                                            'type'=>$type
                                        );
                                        if ($this->add($options)) {

                                                result();
                                        }else{
                                                result('unknown');
                                        }	
    			}else{
    				result('参数类型已存在');
    			}
    		}
    		public function typeLists()
    		{
    			$res['extra'] = $this->where()->select();
    			if ($res) {
    				result(true,$res);
    			}else{
    				$res['extra'] = array();
    				result(true,$res);
    			}
    		}
    		public function deleteType($id)
    		{
    			
    			if ($this->find($id)) {
    				
    				if (M('LiveParamList')->where('`type_id`=%d',array($id))->find()) {
    					result('此参数类型下存在参数');
    				}else{
    					
                                                        if ($this->delete($id)) {
                                                          result();
                                                        }else{
                                                                result('unknown');
                                                        }
    				}
    			}else{
    				result('参数类型不存在');
    			}
    		}
                    public function modifyParamType($id,$type)
                    {

                		$res = $this->find($id);
    			if ($res) {
    				$res = $this->where("`id`!=%d and `type`='%s'",array($id,$type))->find();
    				if (!$res) {
    					$options = array(
	    					'type'=>$type,
					);
	    				$res = $this->where('`id`=%d',array($id))->save($options);
                                                     
					if ($res) {
	                                                  	result();
	                                        	}else{
	                                                	result('unknown');
	                                        	}
    				}else{
    					result('参数类型已存在');
    				}

    			}else{
    				result('参数类型不存在');
    			}
                        
                    }
	}
