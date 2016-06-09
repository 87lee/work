<?php 
	namespace Home\Desktop;
	class DesktopCmdHistoryDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_cmd_history';
		protected $connection = 'DB_DESKTOP'; 
		protected $_map = array(       
			'snList' =>'sn_list', 
			"vendorID"=>"vendorid",    
		);
		
		public function addCmdHistory($options,$reason='下架')
		{
			//添加到数据库
			$options['reason'] = $reason;
			$this->create($options);
			$id = $this->add();
		}


		
	}