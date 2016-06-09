<?php
	namespace Home\Live;
	use Think\Model;
	class LiveParamGroupInfoLive extends Model
	{
		protected $tableName = 'live_param_group_info';
		protected $connection = 'DB_LIVE';
		/*public function __construct()
    		{
		           parent::__construct();
		          isLogin();
    		}*/
    		public function addGroupParam($groupId,$param,$value,$type,$desc,$default)
    		{

    			if (!$res = D('LiveParamGroup','Live')->find($groupId)) {
    				result('参数组不存在');
    			}
			// $this->where('`param=%s`',array($param))->find();
			if ($this->where("`param`='%s' and `group_id`=%d",array($param,$groupId))->find()) {
				result('参数组参数已存在');
			}

			$options = array(
				'group_id'=>$groupId,
				'desc'=>$desc,
				'param'=>$param,
				'value'=>$value,
				'type'=>$type,
				'default'=>$default
			);
			if ($this->add($options)) {
				$this->publishLiveParamForGroupId($groupId);
			}
			result();

    		}

    		public function publishLiveParamForGroupId($groupId)
    		{
    			$groupId = (int)$groupId;
    			$liveParamGroup = D('LiveParamGroup','Live')->getOneForId($groupId);
    			$paramArr = $this->getAllParamValueForGroupId($groupId);
    			if (!empty($paramArr)) {
    				$paramData = array();
    				foreach ($paramArr as  $value) {
    					$paramData[$value['param']] = $value['value'];
    				}
    				if (!empty($paramData)) {
    					$paramJson = json_encode($paramData,JSON_UNESCAPED_UNICODE);
    				}else{
    					$paramJson = '{}';
    				}

    			}else{
    				$paramJson = '{}';
    			}

			$options = array(
                                    		'version'=>$liveParamGroup['group'],
                                    		'json'=>$paramJson,
                        		);
                                    	D('LiveParamGroupPublish','Live')->publishLiveParam($options);
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

				if ($this->delete($id)) {
					$this->publishLiveParamForGroupId($res['group_id']);
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

    			if (!$res) {
    				result('参数组参数不存在');
    			}
    			$group_id = $res['group_id'];

			if ($default === 'true') {
				//发布
				$res['value']=$value;

                                        	$options = array(
                                            		'default'=>'true',
                                            		'value'=>$value
                               		);
                                	}elseif ($default === 'false') {
	                                	//发布
	                                	$res['value']=$value;
	                                    	$options = array(
	                                            	'default'=>'false',
	                                            	'value'=>$value
                               		);
                                	}else{
                                    		result('param');
                                	}

			$this->where("`group_id`=%d and `id`=%d",array($group_id,$id))->save($options);
			$this->publishLiveParamForGroupId($group_id);
                                       result();
    		}
    		public function getAllParamValueForGroupId($groupId)
    		{
    			return $this->field('param,value')->where("`group_id`=%d",array($groupId))->select();
    		}
	}
