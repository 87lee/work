<?php 
	namespace Home\Model;
	use Think\Model;
	class MonitorCmdManagerModel extends Model 
	{
		protected $tableName = 'monitor_cmd_manager'; 
		
		
	    	public function addCmd($cmd)
		{
			if (!empty($cmd['cmd_type'])) {
				switch ($cmd['cmd_type']) {
					case 'shell':
						if (!empty($cmd['shell'])) {
							$options =  array(
								'cmd_type' => 'shell', 
								'shell' =>$cmd['shell'],
								'upload_file' =>''
							);
							
							if ($this->add($options)) {
								result();
							}else{
								result('unknown');
							}
						}else{
							result('param');
						}
						break;
					case 'upload':
						if (!empty($cmd['upload_file'])) {
							$options =  array(
								'cmd_type' => 'upload', 
								'shell' =>'',
								'upload_file' =>$cmd['upload_file']
							);
							
							if ($this->add($options)) {
								result();
							}else{
								result('unknown');
							}
						}else{
							result('param');
						}
						break;
					default:
						result('param');
						break;
				}
			}else{
				result('param');
			}
		}
		public function deleteCmd($id)
		{
			$cmd = $this->find($id);
			if (!empty($cmd)) {
				
				if (M('monitor_cmd_report')->where('cmd_id=%d',array($id))->find()) {
					result('此命令有反馈查询');
				}elseif (M('monitor_cmd_publish')->where('cmd_id=%d',array($id))->find()) {
					result('此命令已发布');
				}else{
					$this->delete($id);
					result();
				}
			}else{
				result('param');
			}
		}
    
	}
