<?php 
	namespace Home\Model;
	use Think\Model;
	class ModuleSubModel extends Model 
	{
		protected $tableName = 'module_sub'; 
		
		protected $_validate = array(     
			// array('sub_module','','帐号名称已经存在！',0,'unique',1),
		);
		
	    /**
	     * 添加子模块
	     * post {"module_id":1, "sub_module":"xxx", "sub_module_name":"xxxx"}
	     * @param [string] $module_id       模块ID
	     * @param [string] $sub_module      子模块英文名
	     * @param [string] $sub_module_name 子模块中文名
	     */
		public function addSubModules($module_id,$sub_module,$sub_module_name)
		{
			if (!empty($module_id)&&!empty($sub_module)&&!empty($sub_module_name)) {

				$response = $this->where("`module_id` ='" .$module_id . "' AND `sub_module`='" . $sub_module ."'")->find();
				
				if (!$response){

					$options = array(
						'module_id'=>$module_id,
						'sub_module'=>$sub_module,
						'sub_module_name'=>$sub_module_name
					);
					
					if (!M('module')->find($module_id)) {
							result('功能模块不存在');
						}else{
							if ($this->add($options)) {
								result();
								
							}else{
								result('unknown');
							}
						}
					
				}else{
					result('您添加的子模块已存在');
				}
				
			}else{
				result('param');
			}
		}
		/**
		 * 删除子模块
		 * @param  [string] $id 子模块ID
		 * @return [json]    
		 */
		public function delSubModule($id)
		{
			if (!empty($id)) {
				$response = $this->find($id) ;
				if($response){
					
					if (M('UserAuth')->where("`sub_module_id`=".$id)->find()) {
						result('有用户拥用该子模块');
					}else{
						$this->delete($id);
						result();
					}
					
				}else{
					result('子模块不存在');
				}
			}else{
				result('param');
			}
			
		}
	}
?>