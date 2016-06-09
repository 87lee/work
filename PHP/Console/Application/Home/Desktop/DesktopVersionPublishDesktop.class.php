<?php
	namespace Home\Desktop;
	class DesktopVersionPublishDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_version_publish';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'AB'=>'gray',
			'groupId'=>'group_id',
			'sourcePath' =>'source_path',
			'layoutPath'  =>'layout_path',
			'layoutVersion'  =>'layout_version',
		);
		public function addDesktopVersionPublish($options,$error = false)
		{
			$desktop = D('Desktop','Desktop')->getDesktopForName($options['model']);
			if ($desktop) {
				$res = $this->where("`model`='%s' and `type`='%s'",array($options['model'],$options['type']))->find();
				if ($res) {
					$this->where("`model`='%s' and `type`='%s'",array($options['model'],$options['type']))->delete();
				}
				if ( $options['type']=='group' || $options['type']=='AB' || $options['type']=='ALL' ) {
					if ($options['type']=='group') {
						$res = D('Group')->getValOneForId($options['groupId']);
						if (!$res) {
							if ($error) {
								return $result = array('reason'=>  '组不存在');
							}else{
								result('组不存在');
							}
						}
					}
					$this->create($options);
					$desktopSourceId = $this->add();
					if ($desktopSourceId) {
						return $desktopSourceId;
					}else{
						if ($error) {
							return $result = array('reason'=>  '添加失败');
						}else{
							result('添加失败');
						}

					}
				}else{
					if ($error) {
						return $result = array('reason'=>  '类型出错');
					}else{
						result('类型出错');
					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=>  '桌面不存在');
				}else{
					result('桌面不存在');
				}

			}
		}

		public function deleteDesktopVersionPublish($id)
		{
			$res = $this->find($id);
			if ($res) {
				$this->delete($id);
				return true;
			}else{
				result('发布桌面版本不存在');
			}
		}

		public function desktopSlotsFileLists($get)
		{
			$noField = "md5";
			if (empty($get['id'])) {

				if (!empty($get['type'])) {
					if (!empty($get['name'])) {
						if (!empty($get['page'])&&!empty($get['pageSize'])) {
							$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
							$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where(" `type` = '".$get['type']."' and (`model` like '%".$get['name']."%' or `version` like  '%".$get['name']."%') ")->order('time desc')->select();
						}else{
							$res['extra'] = $this->field($noField,true)->where(" `type` = '".$get['type']."' and (`model` like '%".$get['name']."%' or `version` like  '%".$get['name']."%') ")->order('time desc')->select();
						}
						$res['count'] = $this->where(" `type` = '".$get['type']."' and (`model` like '%".$get['name']."%' or `version` like  '%".$get['name']."%') ")->count();
					}else{
						if (!empty($get['page'])&&!empty($get['pageSize'])) {
							$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
							$res['extra'] = $this->field($noField,true)->limit($get['page'],$get['pageSize'])->where("`type` = '%s'",array($get['type']))->order('time desc')->select();
						}else{
							$res['extra'] = $this->field($noField,true)->where("`type` = '%s'",array($get['type']))->order('time desc')->select();
						}
						$res['count'] = $this->where("`type` = '%s'",array($get['type']))->count();
					}

				} else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->field($noField,true)->limit($get['page'],$get['pageSize'])->order('time desc')->select();
					}else{
						$res['extra'] = $this->field($noField,true)->order('time desc')->select();
					}
					$res['count'] = $this->count();
				}
			}else{
				$res['extra'] = $this->field($noField,true)->find($get['id']);
				$res['count'] = 1;
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = 0;
				}
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}

			return $res;
		}
		public function getValAllForModelForType($model,$type)
		{
			return $this->where("`model`='%s' and `type`='%s'",array($model,$type))->select();
		}
		public function getValAllForIdArr($sqlArr)
		{
			return $this->where("`id` IN (".$sqlArr.")")->select();
		}
		public function getValOneForModelForType($model,$type)
		{
			return $this->where("`model`='%s' and `type`='%s'",array($model,$type))->find();
		}
		public function getDesktopPublishNameArrForIdArr($put)
		{
			$res['extra'] = array();
			$sqlStr = '';
			$arr = array();
			if ( !empty($put) && is_array($put) ) {

				foreach ($put as $key => $value) {
					$value = intval($value);
					if ($value != 0) {
						$sqlStr .= $value . ',';
					}
				}
				$sqlStr = trim($sqlStr,',');
				$response = $this->field("id,model")->where("id IN (".$sqlStr.")")->select();

				if (!empty($response)) {
					foreach ($response as $key => $value) {
						$arr[$value['id']] = $value['model'];
					}
				}
				foreach ($put as $key => $value) {

					$value = intval($value);
					if (!empty($arr[$value])) {
						$res['extra'][]= $arr[$value];
					}else{
						$res['extra'][] = new \stdClass();
					}
				}
			}

			return $res;
		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
		public function getSqlModelForTypeAll()
		{
			return $this->field('model')->where("type = 'ALL'")->select(false);
		}
	}