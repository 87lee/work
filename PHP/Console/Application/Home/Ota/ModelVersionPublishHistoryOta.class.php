<?php 
	namespace Home\Ota;
	class ModelVersionPublishHistoryOta extends \Think\Model
	{
		protected $tableName = 'model_version_publish_history';
		protected $connection = 'DB_OTA'; 
		protected $_map = array(       
			'fileID'  =>'file_id',   
			'vendorID'  =>'vendor_id',   
			'whiteList' =>'white_list',          
			'blackList'  =>'black_list', 
			'groupId'  =>'group_id', 
			'AB'  =>'ab', 
			'forceUpdate'  =>'force_update', 
		);
		
		public function addModelVersionPublishHistory($options)
		{
			$this->create($options);
			$this->add();
		}

		
	}