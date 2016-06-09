<?php
	namespace Home\App;
	class Link3rdApp extends \Think\Model
	{
		protected $tableName = '3rd_link';
		protected $connection = 'DB_APP';
		protected $randArr = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		protected $_map = array(
			'urlId' =>'url_id',
		);
		public function add3rdLink($put)
		{
			if (!empty($put['name'])&&!empty($put['url'])) {
				$options  = array(
					'name' => $put['name'],
					'url' => $put['url'],
					'desc' => !empty($put['desc'])?$put['desc']:"",
					'url_id' => $this->getRand(),
				);

				return $this->add($options);
			}else{
				result('param');
			}
		}
		public function getRand()
		{
			$arr = array_rand($this->randArr,4);
			$urlId =$this->randArr[$arr[0]].$this->randArr[$arr[1]].$this->randArr[$arr[2]].$this->randArr[$arr[3]];

			if ( $this->where("url_id = '%s'",array($urlId))->find() ) {
				$this->getRand();
			}else{
				return $urlId;
			}
		}

		public function delete3rdLink($get)
		{
			$res = $this->find($get['id']);
			if ($res) {
				$this->delete($get['id']);
				return true;
			}else{
				result('该第三方链接不存在');
			}
		}
		public function app3rdLinkLists($get)
		{

			if (!empty($get['id'])) {
				$res['extra'] = $this->field("`id`,concat('".C('LINK_REDIRECT_PREFIX')."',`url_id`) as `urlId`,`name`,`url`,`desc`")->find($get['id']);
				if (!empty($res['extra'])) {
					$res['count'] = 1;
				}

			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field("`id`,concat('".C('LINK_REDIRECT_PREFIX')."',`url_id`) as `urlId`,`name`,`url`,`desc`")->limit($get['page'],$get['pageSize'])->where(" `name` like '%".$get['name']."%'")->select();

				}else{
					$res['extra'] = $this->field("`id`,concat('".C('LINK_REDIRECT_PREFIX')."',`url_id`) as `urlId`,`name`,`url`,`desc`")->where(" `name` like '%".$get['name']."%'  ")->select();
				}
				$res['count'] = $this->where(" `name` like '%".$get['name']."%'")->count();
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field("`id`,concat('".C('LINK_REDIRECT_PREFIX')."',`url_id`) as `urlId`,`name`,`url`,`desc`")->limit($get['page'],$get['pageSize'])->select();
				}else{
					$res['extra'] = $this->field("`id`,concat('".C('LINK_REDIRECT_PREFIX')."',`url_id`) as `urlId`,`name`,`url`,`desc`")->select();
				}
				$res['count'] = $this->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		public function modify3rdLink($put)
		{
			$res = $this->find($put['id']);
			if (!$res) {
				result('该第三方链接不存在');
			}

			if ($put['name']) {
				$options['name']=$put['name'];
			}
			if ($put['url']) {
				$options['url']=$put['url'];
			}
			if ($put['desc']) {
				$options['desc']=$put['desc'];
			}
			if (!empty($options)) {
				if ($this->create($options)) {
					return $this->where("`id`=%d",array($put['id']))->save();
				}
			}
		}

	}