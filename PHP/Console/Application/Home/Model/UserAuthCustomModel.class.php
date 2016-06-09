<?php
    	namespace Home\Model;
    	use Think\Model;
    	class UserAuthCustomModel extends Model
    	{
        		protected $tableName = 'user_auth_custom';

        		protected $_validate = array(
            		/*array('user','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
            		array('passwd','checkPwd','密码格式不正确',0,'function') // 自定义函数验证密码格式   */
        		);

	        	/**
	         	* 用户模块自定义
	         	* @param  [string] $user_id [用户ID]
	         	* @param  [array] $options [全部模块权限]
	         	* @return [json]
	         	*/
	        	public function authCustom($user_id,$options)
	        	{

	            	if (!empty($user_id)&&!empty($options['modules'])&&is_array($options['modules'])) {
		                	$user = M('user')->find($user_id);
	                		if ($user) {

	                                           $newArr = array();
	                                           $modulesArr = array();
	                                           $subModulesArr = array();
	                              		if (isset($options['modules'])) {
	                                    		foreach ($options['modules'] as  $value) {
	                                          			$modulesArr[] = $value;
	                                    		}
	                                    		$modulesArr = array_unique($modulesArr);
	                                    		$modulesStr = implode('","',$modulesArr);
	                                    		$modulesStr = '"'.trim($modulesStr,',').'"';
	                                    		$modulesGetArr = M('module')->field('id,module')->where("`module` IN (".$modulesStr.")")->select();
	                                    		$modulesSqlArr = array();
	                                    		foreach ($modulesGetArr as  $value) {
	                                    			$modulesSqlArr[$value['module']] = $value['id'];
	                                    		}
	                                    		if (count($modulesArr) != count($modulesGetArr)) {
	                                    			result('模块不存在');
	                                    		}else{
	                                    			foreach ($modulesArr as $value) {
	                                    				$newArr[] = array(
	                                    					'module_id'=>$modulesSqlArr[$value],
	                                    					'user_id'=>$user_id
                                    					);
	                                    			}
	                                    		}
	                              		}else{
	                                    		result('param');
	                              		}

	                        		$this->deleteUserModuleCustom($user_id);
	                        		$this->addUserModuleCustomArr($newArr);

		                	}else{
		                              	result('用户不存在');
		                	}
	            	}else{
	                                result('param');
	               	}
	        	}
	        	public function getModules($userID)
	        	{
	        		$moduleSql = $this->field('module_id')->where("`user_id`=".$userID)->group('module_id')->select(false);
        			$module = M('module')->field('id,module')->where("`id` IN (".$moduleSql.")")->select();
        			$moduleArr = array();
        			if (!empty($module)) {
        				foreach ($module as $value) {
        					$moduleArr[$value['id']] = $value['module'];
        				}
        			}
	        		$data['modules'] = array();

	        		$moduleSqlArr = $this->field('module_id')->where("`user_id`=".$userID)->order('id')->select();

	        		if (!empty($moduleSqlArr)) {
	        			foreach ($moduleSqlArr as $value) {
	        				if ($value['module_id'] == '1') {
	        					continue;
	        				}
	        				$data['modules'][] = $moduleArr[$value['module_id']];
	        			}
	        		}
	        		return $data;

	        	}
	        	public function deleteSubModule($userID,$moduleID)
	        	{
	        		if ($this->where("`module_id`=".$moduleID . " AND `user_id`=".$userID)->find()) {
	        			$this->where("`module_id`=".$moduleID . " AND `user_id`=".$userID)->delete();
	        		}
	        	}
	        	public function addUserModuleCustomArr($optionsArr)
	        	{
	        		return $this->addAll($optionsArr);
	        	}
	        	public function deleteUserModuleCustom($userID)
	        	{
	        		return $this->where("`user_id` = " . $userID)->delete();
	        	}
	        	public function addUserModuleCustom($userID,$moduleID)
	        	{
	        		if (!$this->where("`module_id`=".$moduleID . "  AND `user_id`=".$userID)->find()) {
	        			$options = array(
	        				'module_id'=>$moduleID,
	        				'user_id'=>$userID,
        				);
	        			$this->add($options);
	        		}
	        		return;
	        	}
    	}
?>