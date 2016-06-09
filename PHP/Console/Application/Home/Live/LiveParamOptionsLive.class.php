<?php
	namespace Home\Live;
	use Think\Model;
	class LiveParamOptionsLive extends Model
	{
		protected $tableName = 'live_param_options';
		protected $connection = 'DB_LIVE';
		/*public function __construct()
    		{

    		}*/
    		public function addOption($paramId,$value)
    		{
    			$res = D('LiveParamList','Live')->find($paramId);
    			if ($res) {

    				if (!$this->where("`param_id`=%d  and  `value`='%s'",array($paramId,$value))->find()) {
    					$options = array(
	    					'param_id'=>$paramId,
	    					'value'=>$value
					);
    					if ($this->add($options)) {
	    					result();
	    				}else{
	    					result('unknown');
	    				}
    				}else{
    					result('此参数与值已存在');
    				}

    			}else{
    				result('此参数不存在');
    			}
    		}
    		public function optionLists($paramId,$param = false)
    		{
    			if ($paramId === false) {
    				$paramId = D('LiveParamList','Live')->field('id')->where("`param`='%s'",array($param))->select(false);
    			}
			$res['extra'] = $this->where("`param_id` IN (".$paramId.")")->select();
    			if (!empty($res['extra'])) {
    				result(true,$res);
    			}else{
    				$res['extra'] = array();
    				result(true,$res);
    			}
    		}
    		public function deleteOption($id)
    		{

    			if ($this->find($id)) {

				if ($this->delete($id)) {
                                                      	result();
                                                	}else{
                                                            result('unknown');
                                               	}
    			}else{
    				result('参数值不存在');
    			}
    		}
                    public function modifyParam($id,$value,$default)
                    {
                        	$res = $this->find($id);
                        	$param_id = $res['param_id'];
	                    if ($res) {
	                        	$res = $this->where("`id`!=%d and `param_id`=%d and `value`='%s'",array($id,$param_id,$value))->find();

	                        	if (!$res) {

	                        		if ($default === 'true') {
		                                        	$options = array(
		                                            	'default'=>'false'
		                               		);
		                                        	$res = $this->where("`param_id`=%d and `default`='true'",array($param_id))->save($options);
		                                        	// echo $this->getLastSql();
		                                        	$options = array(
		                                            	'default'=>'true',
		                                            	'value'=>$value
		                               		);
		                                        	$res = $this->where("`param_id`=%d and `id`=%d",array($param_id,$id))->save($options);

		                                        	if ($res) {
		                                        		result();
		                                        	}else{
		                                        		result('unknown');
		                                        	}
		                                }elseif ($default === 'false') {

		                                    	$options = array(
		                                            	'default'=>'false',
		                                            	'value'=>$value
		                               		);
		                                        	$res = $this->where("`id`=%d",array($id))->save($options);
	                                        		result();

		                                }else{
		                                    	result('param');
		                                }

	                        	}else{
	                           		 result('参数值已存在');
	                        	}


	                    	}else{
	                        result('参数值不存在');
	                    	}

                    }

	}
