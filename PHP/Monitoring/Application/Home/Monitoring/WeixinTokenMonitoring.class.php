<?php
	namespace Home\Monitoring;
	class WeixinTokenMonitoring extends \Think\Model
	{
		protected $tableName = 'weixin_token';
		protected $connection = 'DB_MONITORING';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/


		public function addToken($put)
		{
			if (!empty($put['access_token']) && !empty($put['expires_in'])) {
				$options = array(
					'token'=>$put['access_token'],
					'expires'=>$put['expires_in'],
					'time'=>time(),
				);

				if ($res = $this->getOne()) {
					$this->where('id = %d',array($res['id']))->save($options);

				}else{
					$this->add($options);
				}
			}

			return ;
		}
		public function getOne()
		{
			return $this->find();
		}
	}