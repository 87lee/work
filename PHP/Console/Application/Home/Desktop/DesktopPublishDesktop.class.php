<?php
	namespace Home\Desktop;
	class DesktopPublishDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_publish';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'AB'=>'gray',
			'groupId'=>'group_id',

		);
		public function addDesktopPublish($options,$error = false)
		{
			$desktop = D('Desktop','Desktop')->where("`name`='%s'",array($options['model']))->find();
			if ($desktop) {
				$res = $this->where("`model`='%s' and `type`='%s'",array($options['model'],$options['type']))->find();
				if (!$res) {
					if ( $options['type']=='group' || $options['type']=='AB' || $options['type']=='ALL' ) {
						if ($options['type']=='group') {
							$res = D('Group')->find($options['groupId']);
							if (!$res) {
								if ($error) {
									return $result = array('reason'=>'组不存在');

								}else{
									result('组不存在');
								}
							}
						}
						$this->create($options);
						$desktopLayoutId = $this->add();
						if ($desktopLayoutId) {
							return $desktopLayoutId;
						}else{
							if ($error) {
								return $result = array('reason'=>'添加布局不成功');

							}else{
								result('unknown');
							}
						}
					}else{
						if ($error) {
							return $result = array('reason'=>'类型出错');
						}else{
							result('类型出错');
						}
					}

				}else{
					if ($error) {
						return $result = array('reason'=>'已发布');
					}else{
						result('已发布');
					}
				}
			}else{
				if ($error) {
					return $result = array('reason'=>'桌面不存在');
				}else{
					result('桌面不存在');
				}
			}
		}

		public function deleteDesktopPublish($id)
		{
			$res = $this->find($id);
			if ($res) {
				if ($this->delete($id)) {
					return true;
				}else{
					result('unknown');
				}
			}else{
				result('发布桌面不存在');
			}
		}



		public function desktopSlotsFileLists($get)
		{

			if (empty($get['id'])) {
				if (!empty($get['name'])) {
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->where("`model` like '%".$get['name']."%' or `vendorid` like  '%".$get['name']."%' ")->select();
					}else{
						$res['extra'] = $this->where("`model` like '%".$get['name']."%' or `vendorid` like  '%".$get['name']."%' ")->select();
					}
					$res['count'] = $this->where("`model` like '%".$get['name']."%' or `vendorid` like  '%".$get['name']."%' ")->count();
				}else{
					if (!empty($get['page'])&&!empty($get['pageSize'])) {
						$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
						$res['extra'] = $this->limit($get['page'],$get['pageSize'])->select();
					}else{
						$res['extra'] = $this->select();
					}
					$res['count'] = $this->count();
				}
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}else{
					foreach ($res['extra'] as $key => $value) {
						if (!empty($value['cmd'])) {
							$res['extra'][$key]['cmd'] = json_decode($value['cmd'],true);
						}
						if (!empty($value['blackList'])) {
							$res['extra'][$key]['blackList'] = json_decode($value['blackList'],true);
						}
						if (!empty($value['whiteList'])) {
							$res['extra'][$key]['whiteList'] = json_decode($value['whiteList'],true);
						}
					}
				}


			}else{


				$res['extra'] = $this->find($get['id']);
				$res['count'] = 1;
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = 0;
				}else{
					foreach ($res['extra'] as $value) {
						if (!empty($value['cmd'])) {
							$res['extra']['cmd'] = json_decode($value['cmd'],true);
						}
						if (!empty($value['blackList'])) {
							$res['extra']['blackList'] = json_decode($value['blackList'],true);
						}
						if (!empty($value['whiteList'])) {
							$res['extra']['whiteList'] = json_decode($value['whiteList'],true);
						}
					}
				}
			}


			return $res;
		}
	}