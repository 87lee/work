<?php
	namespace Home\Desktop;
	class MacBlackListDesktop extends \Think\Model
	{
		protected $tableName = 'mac_black_list';
		protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		protected $_validate = array(
			array('app_name','','跳转信息应用名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function addDesktopMacBlack($put)
		{
			if (!empty($_FILES['mac']) ) {
				$fileStr = file_get_contents($_FILES['mac']['tmp_name']);
				$arr = explode("\n", $fileStr);
				$time = time();
				foreach ($arr as $key => $value) {
					if (!empty($value)) {
						$mac = strtolower(trim($value));
						$macArr[] = $mac;
					}
				}
				if (!empty($macArr)) {
					$macSql = "'".implode("','", $macArr)."'";
					$res = $this->getArrForMacSql($macSql);

					if (!empty($res)) {
						foreach ($res as $key => $value) {
							$selectMac[] = $value['mac'];;
						}
					}
					if (!empty($selectMac)) {
						$macArr = array_diff($macArr,$selectMac);
					}
					foreach ($macArr as $value) {
						$options[] = array(
							'mac'=>$value,
							'desc'=>'',
							'time'=>$time,
						);
					}
					return $this->addAll($options);
				}
			}elseif (!empty($put['mac'])) {

				if (!$this->getOneForMac($put['mac'])) {
					$options = array(
						'mac'=>strtolower(trim($put['mac'])),
						'desc'=>isset($put['desc'])?$put['desc']:'',
						'time'=>time(),
					);
					return $this->add($options);
				}else{
					result('mac已存在');
				}

			}else{
				result('param');
			}
		}
		public function getArrForMacSql($macSql)
		{
			return $this->field('mac')->where("mac IN (".$macSql.")")->select();
		}
		public function getOneForMac($macSql)
		{
			return $this->field('mac')->where("mac = '%s' ",array($macSql))->find();
		}
		public function getOneForMacNotID($mac,$id)
		{
			return $this->field('mac')->where("mac = '%s' and id !=%d ",array($mac,$id))->find();
		}
		public function deleteDesktopMacBlack($put)
		{
			if (empty($put) || !is_array($put)) {
				result('param');
			}
			foreach ($put as  $value) {
				$arr[] = (int)$value;
			}
			$arr = array_unique($arr);
			if (!empty($arr)) {
				$sqlId = implode($arr, ',');
				if ($res = $this->where("id IN (".$sqlId.")")->select()) {
					return $this->where("id IN (".$sqlId.")")->delete();
				}

			}

		}

		public function desktopMacBlackLists($get)
		{

			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}elseif (!empty($get['name'])) {
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `mac` like '%".$get['name']."%'")->select();
					// echo $this->getLastSql();
				}else{
					$res['extra'] = $this->where(" `mac` like '%".$get['name']."%' ")->select();
				}
				$res['count'] = $this->where(" `mac` like '%".$get['name']."%'")->count();
			}else{
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit( $get['page'],$get['pageSize'] )->select();
				}else{
					$res['extra'] = $this->select();
				}
				$res['count'] = $this->count();
			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}

		public function modifyDesktopMacBlack($put)
		{
			if (!empty($put['id'])&&!empty($put['mac'])) {
				if ($res = $this->find($put['id'])) {

					if (!$this->getOneForMacNotID($put['mac'],$put['id'])) {
						$options = array(
							'mac'=>strtolower(trim($put['mac'])),
							'desc'=>isset($put['desc'])?$put['desc']:$res['desc'],
							'time'=>time(),
						);
						return $this->where("id = %d",array($put['id']))->save($options);
					}else{
						result('mac已存在');
					}
				}else{
					result('mac不存在');
				}
			}else{
				result('param');
			}


		}
		public function getValOneForId($id)
		{
			return $this->find($id);
		}
	}