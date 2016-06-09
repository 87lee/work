<?php
	namespace Home\Live;
	use Think\Model;
	class LiveLeLibraryInfoLive extends Model
	{
		protected $tableName = 'live_letv_lib_info';
		protected $connection = 'DB_LIVE';
		/*public function __construct()
    		{
		           parent::__construct();
		          isLogin();
    		}*/
    		public function addLeLibraryParam($put)
    		{
    			if (empty($put['groupId']) || !isset($put['param']) || !isset($put['value']) || !isset($put['type']) || !isset($put['desc']) || !isset($put['default'])) {
    				result('param');
    			}

    			$liveLeLibrary = D('LiveLeLibrary','Live');
    			if (!$res = $liveLeLibrary->getOneForId($put['groupId'])) {
    				result('参数组不存在');
    			}
			// $this->where('`param=%s`',array($param))->find();
			if ($this->getOneForParamGroupId($put['param'],$put['groupId'])) {
				result('参数组参数已存在');
			}

			$options = array(
				'group_id'=>$put['groupId'],
				'desc'=>$put['desc'],
				'param'=>$put['param'],
				'value'=>$put['value'],
				'type'=>$put['type'],
				'default'=>$put['default']
			);
			if ($this->add($options)) {
				$this->publishLiveParamForGroupId($put['groupId']);
			}
			return ;

    		}
    		public function getOneForParamGroupId($param,$groupId)
    		{
    			return $this->where("`param`='%s' and `group_id`=%d",array($param,$groupId))->find();
    		}
    		public function saveLeLibraryParamPublish($put)
    		{
    			if (empty($put['groupId']) || !isset($put['items']) || !is_array($put['items'])) {
    				result('param');
    			}

    			$liveLeLibrary = D('LiveLeLibrary','Live');
    			if (!$res = $liveLeLibrary->getOneForId($put['groupId'])) {
    				result('参数组不存在');
    			}
    			//删除数据
			$this->deleteArrForGroupId($put['groupId']);
			$params = [];

    			if (!empty($put['items'])) {

    				foreach ($put['items'] as $key => $value) {
					if ( !isset($value['param']) || !isset($value['value']) || !isset($value['type']) || !isset($value['desc']) || !isset($value['default'])) {


		    				continue;
		    			}
		    			if (in_array($value['param'], $params)) {
		    				continue;
		    			}
		    			$params[] = $value['param'];

		    			$addOptions[] = array(
		    				'group_id'=>$put['groupId'],
						'desc'=>$value['desc'],
						'param'=>$value['param'],
						'value'=>$value['value'],
						'type'=>$value['type'],
						'default'=>$value['default']
	    				);

				}


				/*$paramArr = $this->getArrForGroupId($put['groupId']);

				$paramArrCount = count($paramArr) ;

				$putItemsCount = count($put['items']) ;


				$difference =$paramArrCount - $putItemsCount;

				if ($difference > 0) {
					for ($i=0; $i < $difference; $i++) {
						//需要删除多余数据
						$deleteArr[] = $paramArr[$paramArrCount-1-$i]['id'];
						unset($paramArr[$paramArrCount-1-$i]);
					}
				}

				$params = [];
				foreach ($put['items'] as $key => $value) {
					if ( !isset($value['param']) || !isset($value['value']) || !isset($value['type']) || !isset($value['desc']) || !isset($value['default'])) {

						if (!empty($paramArr[$key])) {
							$deleteArr[] = $paramArr[$key]['id'];
						}
		    				continue;
		    			}
		    			if (in_array($value['param'], $params)) {
		    				if (!empty($paramArr[$key])) {
							$deleteArr[] = $paramArr[$key]['id'];
						}
		    				continue;
		    			}
		    			$params[] = $value['param'];
		    			$options = array(
		    				'group_id'=>$put['groupId'],
						'desc'=>$value['desc'],
						'param'=>$value['param'],
						'value'=>$value['value'],
						'type'=>$value['type'],
						'default'=>$value['default']
	    				);



					if (!empty($paramArr[$key])) {
						$options['id'] = $paramArr[$key]['id'];
						$modifyOptions[] = $options;
						//修改数据
						// $this->where('id = %d',array($paramArr[$key]['id']))->save($options);
					}else{
						$addOptions[] =  $options;
					}
				}
				//删除数据
				if (!empty($deleteArr)) {
					$deleteArr = array_unique($deleteArr);
					$idSql = implode(',', $deleteArr);
					$this->where('id IN ('.$idSql.')')->delete();
				}

				//修改数据
				if (!empty($modifyOptions)) {

					// $this->getOneForParamGroupId($put['param'],$put['groupId']);
					$this->save($modifyOptions);
				}*/

				//添加数据
				if (!empty($addOptions)) {
					$this->addAll($addOptions);
				}

    			}

    			// var_dump($params);

    			$this->publishLiveParamForGroupId($put['groupId']);
    			return ;

    		}

    		public function deleteArrForGroupId($groupId)
    		{
    			if ($this->where('group_id = %d' ,array($groupId))->find()) {
    				$this->where('group_id = %d' ,array($groupId))->delete();
    			}
    			return ;
    		}
    		public function getArrForGroupId($groupId)
    		{
    			return $this->where('group_id = %d' ,array($groupId))->select();
    		}
    		public function publishLiveParamForGroupId($groupId)
    		{
    			$groupId = (int)$groupId;

    			$liveParamGroup = D('LiveLeLibrary','Live')->getOneForId( $groupId);
    			if (empty($liveParamGroup)) {
    				result('版本不存在');
    			}
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
                                    	D('LiveLeLibraryPublish','Live')->publishLeLibrary($options);
    		}

    		public function leLibraryParamLists($get)
    		{
    			if (!empty($get['groupId'])) {
    				$where = '`group_id`=' . intval($get['groupId']);

    				$res['extra'] = $this->field('type')->where($where)->group('type')->select();

    				if (!empty($res['extra'])) {
	    				foreach ($res['extra'] as $key => $value) {
	    					$res['extra'][$key]['params'] = $this->field('id,param,value,desc,default')->where("`group_id`=%d and `type`='%s'",array($get['groupId'],$value['type']))->select();
	    					if (empty($res['extra'][$key]['params'])) {
	    						$res['extra'][$key]['params'] = array();
	    					}
	    				}

	    			}
	    			/*if (!empty($get['name'])) {
	    				$where = "group like '%".$get['name']."%'";
	    			}

	    			if (!empty($get['page']) && !empty($get['pageSize'])) {
	    				$get['page'] = ($get['page'] - 1 )*$get['pageSize'];
	    				$res['extra'] = $this->where($where)->limit($get['page'],$get['pageSize'])->select();
	    			}else{
	    				$res['extra'] = $this->where($where)->select();
	    			}
				*/
	    			if (empty($res['extra'])) {
	    				$res['extra'] = array();
	    			}
	    			return $res;
    			}
    		}
    		public function deleteLeLibraryParam($get)
    		{
    			if (!empty($get['id'])) {

	    			if (!$res = $this->find($get['id'])) {
	    				result('参数组参数不存在');
	    			}
				if ($this->delete($get['id'])) {
					// $this->publishLiveParamForGroupId($res['group_id']);
                                        	}
    			}
    			return ;

    		}
    		public function modifyLeLibraryParam($get)
    		{

    			if (empty($get['id']) || !isset($get['value']) || !isset($get['default'])) {
    				result('param');
    			}

    			if (!$res = $this->find($get['id'])) {
    				result('参数组参数不存在');
    			}

    			$group_id = $res['group_id'];

			if ($get['default'] === 'true') {
				//发布
				$res['value']=$get['value'];

                                        	$options = array(
                                            		'default'=>'true',
                                            		'value'=>$get['value']
                               		);
                                	}elseif ($get['default'] === 'false') {
	                                	//发布
	                                	$res['value']=$get['value'];
	                                    	$options = array(
	                                            	'default'=>'false',
	                                            	'value'=>$get['value']
                               		);
                                	}else{
                                    		result('param');
                                	}

			$this->where("`group_id`=%d and `id`=%d",array($group_id,$get['id']))->save($options);
			// $this->publishLiveParamForGroupId($group_id);
                                      return ;
    		}
    		public function getAllParamValueForGroupId($groupId)
    		{
    			return $this->field('param,value')->where("`group_id`=%d",array($groupId))->select();
    		}
	}
