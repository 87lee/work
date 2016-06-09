<?php 

	namespace Home\Model;
	use Think\Model;
	class ServerConfigModel extends Model 
	{
		protected $tableName = 'server_config'; 
		protected $_validate = array(     
			array('name','','帐号名称已经存在！',0,'unique',3), // 在新增的时候验证name字段是否唯一
      
		);
		
	    	/**
	     	* 新增服务参数
	     	* @param [string] $name [增加组名]
	     	*/
	    	public function addServerConf($name,$value)
	    	{		
	     
	    		$options = array(
	    			'name'=>$name,
	    			'value'=>$value,
	    			'update_time'=>time()
			);
	    		if ($this->create($options)) {
			   	$this->add();
			   	$this->publishServerConfig();
	         			result();
	    		}else{
	            		result('名字已存在');
	    		}
	    	}
	    	/**
	     	* 删除服务参数
	     	* @param [string] $id [删除组的ID]
	     	*/
	    	public function deleteServerConf($id)
	    	{   
			if ($this->find($id)) {
		            	$this->delete($id);
		                	$this->publishServerConfig();
		                	result();
		        	}else{
		             	result('服务参数不存在');
		        	}
	    	}
	    	public function serverConfLists()
	    	{
	    		$res['extra'] = $this->field('id,name,value,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") as updateTime')->select();
	    		if (!$res['extra']) {
	    			$res['extra'] = array();
	    		}
	    		result(true,$res);
	    	}
    		/**
	     	* 修改服务参数
	     	* @param [string] $name [增加组名]
	     	*/
	    	public function modifyServerConf($id,$name,$value)
	    	{	
	    		$res = $this->where("`name`='%s' and `id` !=%d",array($name,$id))->find();		     
	    		$options = array(
	    			'name'=>$name,
	    			'value'=>$value,
	    			'update_time'=>time()
			);
	    		if (!$res) {
			   	$this->where("`id`=%d",array($id))->save($options);
			   	$this->publishServerConfig();
	         			result();
	    		}else{
	            			result('名字已存在');
	    		}
	    	}
	    	/**
	    	 * 发布服务参数
	    	 * @return [type] [description]
	    	 */
   		public function publishServerConfig()
		{	
			$data = $this->field('name,value')->select();
			$data1 = array();
			foreach ($data as $key => $value) {
				$data1[$value['name']] = $value['value'];
			}
			$data = json_encode($data1,JSON_UNESCAPED_UNICODE);
			$res = postUrl(SERVER_CONFIG_ACCESS_ADDR . 'serverConf',$data);
			if (!$res) {
				result('发布地址发错');
			}
			$res = json_decode($res,true);
			if ($res['result'] !='ok') {
				result('添加成功，发布失败:'.$res['reason']);
			}else{
				return true;
			}
		}
	}
?>