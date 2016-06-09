<?php 
	namespace Home\Model;
	use Think\Model;
	class ModuleModel extends Model 
	{
		protected $tableName = 'module'; 
		protected $_validate = array(     
			array('module','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一  
		);
		

	    /**
	     * 添加模块
	     * 地址 post /Modules/add  
	     * postdata {"module":"xxx", "module_name":"xxxx"}
	     * 
	     */
		public function addModules($module,$module_name)
		{
			if (empty($module) || empty($module_name) ) {
				result('param');
			}

			if ($this->create(array('module'=>$module,'module_name'=>$module_name))) {
				$this->add();
				result();
			}else{
				result('您添加的模块已存在');
			}
			
		}
		/**
		 * 删除模块
		 * @param  [string] $id     模块ID
		 * @param  [string] $sub_id 子模块ID
		 * @return [json]  
		 */
		public function delModules($id,$sub_id = '')
		{	
			if ($id) {
				if ($sub_id != '') {
					$sub_module = D('ModuleSub');
					$sub_module->delSubModule($sub_id);
				}else{
					$response = $this->find($id);

					if ($response) {
							$response = M('module_sub')->where("`module_id` = '" . $id ."'")->select();
							if ($response) {
								result('该模块下存在子模块');
							}else{
								$this->delete($id);
								result();
							}
					}else{
						result('该模块不存在');
					}
				}
				
			}else{
				result('param');
			}

			
		}
		/**
		 *	获取模块列表
		 * @param  string $user_id 用户ID  有用户ID获取用户ID  没有就获取全部
		 * @return json
		 */
		public function getModules($user_id='')
		{	
			$module = M('module');
			$sub_module =M('module_sub');
			if($user_id!=''){
				$user = M('User')->find($user_id);
				if ($user['root'] == 1 ) {
		        			$modules = $module->select();
		        			if ($modules) {
		        			
			        			foreach ($modules as $key => $value) {
			        				$arr = $value;
			        				if ($sub_module->field('`id` AS sub_id,`sub_module`,`sub_module_name`')->where("`module_id`=". $value['id'] )->select()) {
			        					$arr['sub_modules'] = $sub_module->field('`id` AS sub_id,`sub_module`,`sub_module_name`')->where("`module_id`=". $value['id'] )->select();
			            			
			        				}else{
			        					$arr['sub_modules'] = array();
			        				}

			        				$options[]=$arr;
			        			}
		        			}else{
		        				$options = array();
		        			}
        		
    				}else{

    					$user_auth = M('user_auth');
            				$module_group = $user_auth->field('module_id')->where("`user_id` = " . $user_id)->group('module_id')->select();
            		
            				if ($module_group) {
            			
						$model = M();
	            		
		            			foreach ($module_group as $key => $value) {

			            			$arr = $module->find($value['module_id']);
			            			$response = $model->query("SELECT `id` AS sub_id,`sub_module`,`sub_module_name` FROM `tb_module_sub` WHERE `module_id`=". $value['module_id'] . " AND `id` IN( SELECT `sub_module_id` FROM `tb_user_auth` WHERE `user_id`=".$user_id . " AND module_id= ". $value['module_id'] .  ")");
			            			if ($response) {
			            				$arr['sub_modules'] =$response;
			            			}else{
			            				$arr['sub_modules'] = array();
			            			}
			            			
			            			$options[]=$arr;
			            		}
	            			}else{
	            				$options = array();
	            			}
            		
				}
        		
    				$data['modules']=$options;

			}else{
    				$modules = $module->select();

    				if ($modules) {
    					foreach ($modules as $key => $value) {
	    					$arr = $value;

	    					$response = $sub_module->field('`id` AS sub_id,`sub_module`,`sub_module_name`')->where("`module_id`=". $value['id'] )->select();
		        				if ($response) {
		        					$arr['sub_modules'] = $response;
		        				}else{
		        					$arr['sub_modules'] = array();
		        				}
	    				
	    					$data['modules'][]=$arr;
	    				}
    				}else{
    					$data['modules']=array();
    				}	
    			}
		
			return $data;
		}
	}
?>