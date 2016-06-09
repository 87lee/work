<?php
    	namespace Home\Model;
    	use Think\Model;
    	class UserAuthModel extends Model
    	{
        		protected $tableName = 'user_auth';

        		protected $_validate = array(
            		/*array('user','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
            		array('passwd','checkPwd','密码格式不正确',0,'function') // 自定义函数验证密码格式   */
        		);

	        	/**
	         	* 设置用户模块权限
	         	* @param  [string] $user_id [用户ID]
	         	* @param  [array] $options [全部模块权限]
	         	* @return [json]
	         	*/
	        	public function auth($user_id,$options)
	        	{
	            	if (!empty($user_id)&&!empty($options)&&is_array($options)) {
		                	$user = M('user')->find($user_id);
	                		if ($user) {
	                                           $old_arr =array();
	                                           $new_arr = array();
	                              		if (isset($options['modules'])) {
	                                    		foreach ($options['modules'] as  $value) {
	                                          			foreach ($value['sub_modules'] as $key => $val) {
	                                                			$new_arr[$value['id']][] = $val['sub_id'];
	                                          			}


	                                          			if (!empty($new_arr[$value['id']])) {
	                                          				$new_arr[$value['id']] = array_unique($new_arr[$value['id']]);
	                                          			}

	                                    		}
	                              		}else{
	                                    		result('param');
	                              		}

	                        		$module = M('module');
	                        		$module_group = $this->field('module_id')->where("`user_id` = " . $user_id)->group('module_id')->select();

	                    			$model = M();
	                              		if (!empty($module_group)) {
		                                    	foreach ($module_group as  $value) {
		                                          		$user_module = $this->field('sub_module_id AS sub_id')->where("`user_id`=".$user_id ." AND `module_id`=" .$value['module_id'])->select();

		                                          		foreach ($user_module as $key => $val) {
		                                                		$old_arr[$value['module_id']][] = $val['sub_id'];
		                                          		}
		                                    	}
	                              		}

		                              	if (!empty($old_arr)) {
		                                    	foreach ($old_arr as $key => $value) {

			                                          	if(empty($new_arr[$key])){
			                                                	$del_diff[$key][] = $value;
			                                          	}else{
								$arrayDiff = array_diff($value,$new_arr[$key]);
			                                                	if (!empty($arrayDiff)) {
			                                                		$del_diff[$key][]=  $arrayDiff;
			                                                	}

			                                          	}
		                                    	}
		                              	}else{
		                                    	$del_diff = array();
		                              	}

		                        	if (!empty($new_arr)) {
		                                    	foreach ($new_arr as $key => $value) {
			                                          	if (empty($old_arr[$key])) {
			                                                	$add_diff[$key][]= $value;
			                                          	}else{
			                                          		$arrayDiff = array_diff($value,$old_arr[$key]);
			                                                	if (!empty($arrayDiff)) {
			                                                		$add_diff[$key][]=  $arrayDiff;
			                                                	}
			                                          	}
		                                    	}
		                              	}else{
		                                    	$add_diff = array();
		                              	}
		                        	if (!empty($add_diff)) {

		                                    	foreach ($add_diff as $key => $value) {
		                                    		D('UserAuthCustom')->addUserModuleCustom($user_id,$key);
		                                          		foreach ($value as $val) {
			                                                	if (!empty($val)){
			                                                      	foreach ($val as $k => $v) {
			                                                            		$response = M('module_sub')->where("`module_id`=".$key . " AND `id`=".$v)->find();
			                                                            		if ($response) {
			                                                                  		$response = $this->where("`module_id`=".$key . " AND `sub_module_id`=".$v ." AND `user_id`=".$user_id)->find();
			                                                                  		if ($response) {
			                                                                        			result('功能不存在');
			                                                                  		}else{
			                                                                        			$options=array(
					                                                                              	'module_id'=>$key,
					                                                                              	'sub_module_id'=>$v,
					                                                                              	'user_id'=>$user_id
					                                                                        	);
					                                                                        	if (!$this->add($options)) {
					                                                                              	result('未知错误');
					                                                                        	}
			                                                                  		}
			                                                            		}else{
			                                                                  		result('功能不存在');
			                                                            		}
			                                                      	}
			                                                	}
		                                          		}
		                                    	}
		                              	}
		                        	if (!empty($del_diff)) {
		                                    	foreach ($del_diff as $key => $value) {
		                                          		foreach ($value as $val) {
		                                                		if (!empty($val)) {
		                                                      		foreach ($val as $k => $v) {
		                                                            			$response = $this->where("`module_id`=".$key . " AND `sub_module_id`=".$v ." AND `user_id`=".$user_id)->find();
		                                                            			if ($response) {
			                                                                  		if (!$this->where("`module_id`=".$key . " AND `sub_module_id`=".$v ." AND `user_id`=".$user_id)->delete()) {
			                                                                        			result('未知错误');
			                                                                  		}
			                                                            		}else{
			                                                                  		result('功能不存在');
			                                                            		}
		                                                  			}
			                                                	}

		                                          		}
		                                          		if (!$this->where("`module_id`=".$key . " AND `user_id`=".$user_id)->find()) {
		                                          			D('UserAuthCustom')->deleteSubModule($user_id,$key);
		                                          		}
		                                    	}
		                              	}

		                	}else{
		                              	result('用户不存在');
		                	}
	            	}else{
	                                result('param');
	               	}

                             	result();

	        	}

    	}
?>