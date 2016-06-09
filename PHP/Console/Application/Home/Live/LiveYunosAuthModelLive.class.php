<?php
	namespace Home\Live;
	class LiveYunosAuthModelLive extends \Think\Model
	{
		protected $tableName = 'yunos_auth_model';
		protected $connection = 'DB_LIVE_AUTH';
		protected $_map = array(
			'vendorID' =>'vendor_id',
			/*'groupId'  =>'group_id',
			'isSkip'  =>'is_skip',
			'showTime'  =>'show_time',
			'AB'  =>'ab',
			'whiteList'=>'white_list',
			'blackList'=>'black_list',*/
		);
		/*protected $_validate = array(
			array('model','','该型号已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addLiveAuth($put)
		{
			if ( empty($put['model']) ) {
				result('请添加型号');
			}
			if (!isset($put['amount'])) {
				result('请添加数量');
			}
			if (empty($put['vendorID'])) {
				result('请添加vendorID');
			}
			$put['amount'] = intval($put['amount']);
			if (isset($put['id'])) {
				unset($put['id']);
			}

			if ($this->getOneForModelVenderId($put['model'],$put['vendorID'])) {
				result('型号与vendorID已存在');
			}
			if ($this->create($put)) {
				$this->add();
				D('LiveYunosAuthHistory',"Live")->addLiveAuthHistory($put,'add');
			}else{
				result($this->getError());
			}
		}
		public function getOneForModelVenderId($model,$vendorID)
		{
			return $this->where("model = '%s' and vendor_id = '%s'",array($model,$vendorID))->find();
		}
		public function getOneForModelVenderIdNoId($model,$vendorID,$id)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and id != %d",array($model,$vendorID,$id))->find();
		}
		public function modifyLiveAuth($put)
		{
			if ( empty($put['model']) ) {
				result('请输入型号');
			}
			if (!isset($put['amount'])) {
				result('请输入数量');
			}
			if (empty($put['vendorID'])) {
				result('请添加vendorID');
			}
			$put['amount'] = intval($put['amount']);

			if ($this->find($put['id'])) {
				if ($this->getOneForModelVenderIdNoId($put['model'],$put['vendorID'],$put['id'])) {
					result('型号与vendorID已经存在！');
				}
				$this->create($put,2);
				$this->save();
				D('LiveYunosAuthHistory',"Live")->addLiveAuthHistory($put,'modify');
			}else{
				if (isset($put['id'])) {
					unset($put['id']);
				}
				if ($this->create($put,1)) {
					$this->add();
					D('LiveYunosAuthHistory',"Live")->addLiveAuthHistory($put,'add');
				}else{
					result($this->getError());
				}
			}
			return;
		}
		public function getOneForNoIdModel($id,$model)
		{
			return $this->where("id != %d and model = '%s'",array($id,$model))->find();
		}
		public function deleteLiveAuth($put)
		{
			if (empty($put) || !is_array($put)) {

				result('param');
			}
			foreach ($put as  $value) {
				$arr[] = (int)$value;
			}
			$arr = array_unique($arr);
			if (!empty($arr)) {
				$sqlId = implode(',',$arr);
				if ($res = $this->getArrForIdSql($sqlId) ) {
					foreach ($res as $key => $value) {
						D('LiveYunosAuthHistory',"Live")->addLiveAuthHistory($value,'delete');
					}
					return $this->where("id IN (".$sqlId.")")->delete();
				}
			}
			return ;
		}
		public function getArrForIdSql($sqlId)
		{
			return $this->where("id IN (".$sqlId.")")->select();
		}
		public function liveAuthLists($get)
		{
			if (!empty($get['id'])) {
				$res['extra'] = $this->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
					$modelSql = "'".$res['extra']['model']."'";
				}

			}else{
				$where = "";
				if (!empty($get['name'])){
					$where = "   `model` like '%".$get['name']."%' ";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->where($where)->select();
				}

				// $modelSql = $this->field('model')->where($where)->select(false);
				$modelSql = '';
				if (!empty($res['extra'])) {
					foreach ($res['extra'] as $key => $value) {
						$modelSql .= "'".$value['model']."',";
					}
					$modelSql = trim($modelSql,',');
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}else{
				$modelNums = D('LiveYunosAuthNum','Live')->getNumForModelSql($modelSql);

				$modelNumArr = array();
				foreach ($modelNums as  $value) {
					$modelNumArr[$value['model']][$value['vendorID']] = $value['num'];
				}
				if (!empty($res['extra'][0])) {
					foreach ($res['extra'] as $key => $value) {
						if (!empty($modelNumArr[$value['model']][$value['vendorID']])) {
							$res['extra'][$key]['num'] = $modelNumArr[$value['model']][$value['vendorID']];
						}else{
							$res['extra'][$key]['num'] = '0';
						}
					}
				}else{
					if (!empty($modelNumArr[$res['extra']['model']][$res['extra']['vendorID']])) {
						$res['extra']['num'] = $modelNumArr[$res['extra']['model']][$res['extra']['vendorID']];
					}else{
						$res['extra']['num'] = '0';
					}
				}
			}
			return $res;
		}

		/*
		public function getOneForModelVenderIDType($model,$vendorID,$type)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and type = '%s'",array($model,$vendorID,$type))->find();
		}
		public function getOneForModelVenderIDTypeNotID($model,$vendorID,$type,$id)
		{
			return $this->where("model = '%s' and vendor_id = '%s' and type = '%s' and id !=%d",array($model,$vendorID,$type,$id))->find();
		}

		public function userActionLists($get)
		{
			if (empty($get['id'])) {
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->limit($get['page'],$get['pageSize'])->where("`user` like '%".$get['name']."%' or `interface` like  '%".$get['name']."%' ")->order("time desc")->select();
						// echo $this->getLastSql();
					}else{
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->where("`user` like '%".$get['name']."%' or `interface` like  '%".$get['name']."%' ")->order("time desc")->select();
					}
					$res['count'] = $this->where("`user` like '%".$get['name']."%'  or `interface` like  '%".$get['name']."%' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->limit($get['page'],$get['pageSize'])->order("time desc")->select();
					}else{
						$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->order("time desc")->select();
					}
					$res['count'] = $this->count();
				}

			}else{
				$res['extra'] = $this->field('id,user,interface,FROM_UNIXTIME(time,"%Y-%m-%d %H:%i:%s") as time')->find($get['id']);
				$res['count'] =1;

			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = 0;
			}
			return $res;
		}*/
	}