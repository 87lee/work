<?php
	namespace Home\Live;
	use Think\Model;
	class LiveParamListLive extends Model
	{
		protected $tableName = 'live_param_list';
		protected $connection = 'DB_LIVE';
		/*public function __construct()
    		{

    		}*/
    		public function addParam($typeId,$param,$desc)
    		{
    			$res = D('LiveParamType','Live')->find($typeId);
    			if ($res) {
    				// $this->where('`param=%s`',array($param))->find();
    				if (!$this->where("`param`='%s' and `type_id`=%d",array($param,$typeId))->find()) {
    					$options = array(
	    					'type_id'=>$typeId,
	    					'desc'=>$desc,
	    					'param'=>$param
					);
	    				if ($this->add($options)) {
	    					result();
	    				}else{
	    					result('unknown');
	    				}
    				}else{
    					result('参数已存在');
    				}

    			}else{
    				result('参数类型不存在');
    			}
    		}
    		public function paramLists($typeId = ' ')
    		{
    			if ($typeId == ' ') {

				$type['extra'] = D('LiveParamType','Live')->select();
				// $data['extra'] = array();
                                                    if (!empty($type['extra'])) {
                                                         foreach ($type['extra'] as $key => $value) {
                                                            $type['extra'][$key]['params'] = $this->where("`type_id`=%d",array($value['id']))->select();
                                                            unset($type['extra'][$key]['id']);
                                                            if (empty($type['extra'][$key]['params'])) {
                                                                $type['extra'][$key]['params'] = array();
                                                            }
                                                            // $data['extra'][]=$type[$key];
                                                            // unset($type[$key]);
                                                        }
                                                     }else{
                                                     	$type['extra'] = array();
                                                     }


				result(true,$type);

    			}else{
    				$res['extra'] = $this->where("`type_id`=%d",array($typeId))->select();
    				if (!empty($res['extra'])) {
    					result(true,$res);
    				}else{
    					$res['extra'] = array();
    					result(true,$res);
    				}
    			}

    		}
    		public function deleteParam($id)
    		{

    			if ($this->find($id)) {
    				if (D('LiveParamOptions','Live')->where('`param_id`=%d',array($id))->find()) {
    					result('此参数下存在值');
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
                    public function modifyParam($id,$param,$desc)
                    {
                		$res = $this->find($id);
    			if ($res) {
    				$res = $this->where("`id`!=%d and `param`='%s' and `type_id`=%d",array($id,$param,$res['type_id']))->find();
    				if (!$res) {
    					$options = array(
	    					'param'=>$param,
	    					'desc'=>$desc
					);
	    				$res = $this->where('`id`=%d',array($id))->save($options);
					if ($res) {
	                                                  	result();
	                                        	}else{
	                                                	result('unknown');
	                                        	}
    				}else{
    					result('参数已存在');
    				}

    			}else{
    				result('参数不存在');
    			}

                    }

	}
