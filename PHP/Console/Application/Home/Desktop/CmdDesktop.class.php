<?php
	namespace Home\Desktop;
	class CmdDesktop extends \Think\Model
	{
		protected $tableName = 'cmd';
		protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'snList' =>'sn_list',
			"vendorID"=>"vendorid",
		);*/
		/*protected $_validate = array(
			array('name','','命令已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/
		public function addCmdLine($put)
		{
			//检查数据
			if (!empty($put['name']) && !empty($put['cmd'])&&is_array($put['cmd'])) {

				if ($this->where("`name`='%s'",array($put['name']))->find()) {
					result('命令已存在');
				}
				$put['cmd'] = array_values($put['cmd']);

				$jsonCmd = json_encode($put['cmd'],JSON_UNESCAPED_UNICODE);


				$time = time();
				$options = array(
					'name'=>$put['name'],
					'cmd'=>$jsonCmd,
				);

				//添加到数据库

				if ($this->create($options)) {
					return $id = $this->add();
				}else{
					result($this->getError());
				}


			}else{
				result('param');
			}
		}

		public function modifyCmdLine($put)
		{
			//检查数据
			if (!empty($put['name']) && !empty($put['id']) && !empty($put['cmd'])&&is_array($put['cmd'])) {

				if (!$this->find($put['id'])) {
					result('没有此命令');
				}
				if ($this->where("`name`='%s' and `id` != %d",array($put['name'],$put['id']))->find()) {

					result('命令已存在');
				}
				$put['cmd'] = array_values($put['cmd']);
				$jsonCmd = json_encode($put['cmd'],JSON_UNESCAPED_UNICODE);

				$time = time();
				$options = array(
					'name'=>$put['name'],
					'cmd'=>$jsonCmd,
				);

				//添加到数据库

				if ($this->create($options,2)) {
					return $id = $this->where("`id`=%d",array($put['id']))->save();
				}else{
					result($this->getError());
				}


			}else{
				result('param');
			}
		}
		public function deleteCmdLine($id)
		{
			$res = $this->find($id);
			if ($res) {
				$this->where("`id`=%d",array($id))->delete();
			}

		}

		public function cmdLineLists($get)
		{
			if (empty($get['id'])) {
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`name` like '%".$get['name']."%' ")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->where("`name` like '%".$get['name']."%'")->select();
					}
					$res['count'] = $this->where("`name` like '%".$get['name']."%' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $this->select();
					}
					$res['count'] = $this->count();
				}
				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['cmd'] = json_decode($value['cmd'],true);
					}
				}
			}else{
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['extra']['cmd'] = json_decode($res['extra']['cmd'],true);
					$res['count'] = 1;
				}

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;

		}


	}