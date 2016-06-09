<?php
	namespace Home\Desktop;
	class AttentionAppDesktop extends \Think\Model
	{
		protected $tableName = 'attention_app';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);
		protected $_validate = array(
			array('app_name','','跳转信息应用名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function addAttentionApp($put)
		{
			if (empty($put['appName']) || empty($put['pkgName']) ) {
				result('param');
			}
			$user = getUser();
			if (empty($user)) {
				result('login');
			}
			if ($this->getOneForAppNameAndpkgNameUser($put['pkgName'],$user['user'])) {
				result('应用已存在');
			}
			$options = array(
				'app_name'=>$put['appName'],
				'pkgname'=>$put['pkgName'],
				'user'=>$user['user'],
				'time'=>time(),
			);
			return $this->add($options);


		}
		public function getArrForMacSql($macSql)
		{
			return $this->where("mac IN (".$macSql.")")->select();
		}
		public function getOneForAppNameAndpkgNameUser($pkgName,$User)
		{
			return $this->where("pkgname = '%s' and user = '%s'  ",array($pkgName,$User))->find();
		}
		public function getOneForAppNameAndpkgNameUserNoId($pkgName,$User,$id)
		{
			return $this->where("pkgname = '%s' and user = '%s' and id !=%d",array($pkgName,$User,$id))->find();
		}
		public function getOneForMacNotID($mac,$id)
		{
			return $this->where("mac = '%s' and id !=%d ",array($mac,$id))->find();
		}
		public function deleteAttentionApp($put)
		{
			if (!empty($put) && is_array($put)) {
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
			}else{
				result('param');
			}
		}

		public function attentionAppLists($get)
		{
			$noField = 'user';
			$user = getUser();
			$where = "user  = '".$user['user']."'";
			if (!empty($get['id'])) {
				$res['extra'] = $this->field($noField,true)->where($where)->find($get['id']);
				if ($res['extra']) {
					$res['count'] = 1;
				}
			}else{
				if (!empty($get['name'])) {
					$where = " `app_name` like '%".$get['name']."%'";
				}
				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					$res['extra'] = $this->field($noField,true)->limit($get['page'],$get['pageSize'])->where($where)->select();
				}else{
					$res['extra'] = $this->field($noField,true)->where($where)->select();
				}
				$res['count'] = $this->where($where)->count();
			}
			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
		public function desktopAttentionAppLists()
		{

			/*
			联合查询

			 SELECT action_info FROM tb_desktop_block_info
				LEFT JOIN tb_desktop_blocks ON tb_desktop_block_info.block_id = tb_desktop_blocks.id
				LEFT JOIN tb_desktop_screens ON  tb_desktop_screens.id = tb_desktop_blocks.screen_id
				WHERE tb_desktop_screens.index IN (1,2) AND   tb_desktop_block_info.action_info != '{}' AND  tb_desktop_block_info.action_info != ''   AND   tb_desktop_block_info.operation = 'false'


			//现在用的SQL

			SELECT `action_info` FROM `tb_desktop_block_info` WHERE ( block_id IN (SELECT `id` FROM `tb_desktop_blocks` WHERE ( screen_id IN (SELECT `id` FROM `tb_desktop_screens` WHERE ( `index` IN (1,2) ) )  ) ) and operation = 'false' and  action_info != '{}' and  action_info != ''  )



			SELECT operation_action.pkgname FROM tb_operation_slot_action operation_action WHERE ( operation_action.operation_id IN (
				SELECT `id` FROM `tb_operation_slot` WHERE ( slot_group_id = 1 and slot_id IN (
				SELECT block_info.operation_id FROM tb_desktop_block_info block_info WHERE ( block_info.block_id IN (
				SELECT `id` FROM `tb_desktop_blocks` WHERE ( screen_id IN (
				SELECT `id` FROM `tb_desktop_screens` WHERE ( `index` IN (1,2) and slot_group_id = 1 ) )  ) ) and block_info.operation = 'true' and block_info.operation_id != 0   ) ) ) ) and operation_action.pkgname is not null)



			SELECT * FROM tb_desktop_screens
				LEFT JOIN tb_desktop_blocks ON tb_desktop_blocks.screen_id = tb_desktop_screens.id
				LEFT JOIN tb_desktop_block_info AS block_info ON block_info.block_id = tb_desktop_blocks.id
				LEFT JOIN tb_operation_slot ON tb_operation_slot.slot_id = block_info.operation_id and tb_desktop_screens.slot_group_id = tb_operation_slot.slot_group_id
				LEFT JOIN tb_operation_slot_action AS operation_action  ON operation_action.operation_id = tb_operation_slot.id
				WHERE tb_desktop_screens.index IN (1,2)  AND   block_info.operation = 'true' AND block_info.operation_id != 0 AND operation_action.pkgname IS NOT NULL

			56

			1,2,4,6,11,14,20

			13+2+1+0+0+0+1
			*/

			//查出非运营坑位SQL
			/*$sql =  "SELECT action_info FROM tb_desktop_block_info
					LEFT JOIN tb_desktop_blocks ON tb_desktop_block_info.block_id = tb_desktop_blocks.id
					LEFT JOIN tb_desktop_screens ON  tb_desktop_screens.id = tb_desktop_blocks.screen_id
					WHERE tb_desktop_screens.index IN (1,2)  AND   tb_desktop_block_info.operation = 'false'  AND   tb_desktop_block_info.action_info != '{}' AND  tb_desktop_block_info.action_info != ''"

			$Model = new Model() // 实例化一个model对象 没有对应任何数据表
			$desktopBlockIdFalse = $Model->query($sql);

			$count = 0;
			if ($desktopBlockIdFalse) {
				foreach ($desktopBlockIdFalse as $value) {
					$actionInfo = json_decode($value['actionInfo'],true);
					if ($actionInfo) {
						if ($actionInfo['pkgName'] ==$pkgName) {
							$count +=1;
						}
					}
				}
			}



			SELECT operation_action.pkgname FROM tb_operation_slot_action AS operation_action
					LEFT JOIN tb_operation_slot ON operation_action.operation_id = tb_operation_slot.id
					LEFT JOIN tb_desktop_block_info AS block_info ON block_info.operation_id = tb_operation_slot.slot_id
					LEFT JOIN tb_desktop_blocks ON block_info.block_id = tb_desktop_blocks.id
					LEFT JOIN tb_desktop_screens ON  tb_desktop_screens.id = tb_desktop_blocks.screen_id AND  tb_operation_slot.slot_group_id = tb_desktop_screens.slot_group_id
					WHERE tb_desktop_screens.index IN (1,2) AND   block_info.operation = 'true' AND block_info.operation_id != 0 AND block_info.operation_id = tb_operation_slot.slot_id AND tb_operation_slot.slot_group_id = tb_desktop_screens.slot_group_id AND operation_action.pkgname IS NOT NULL

			SELECT * FROM tb_desktop_screens
				LEFT JOIN tb_desktop_blocks ON tb_desktop_blocks.screen_id = tb_desktop_screens.id
				LEFT JOIN tb_desktop_block_info AS block_info ON block_info.block_id = tb_desktop_blocks.id
				LEFT JOIN tb_operation_slot ON tb_operation_slot.slot_id = block_info.operation_id and tb_desktop_screens.slot_group_id = tb_operation_slot.slot_group_id
				LEFT JOIN tb_operation_slot_action AS operation_action  ON operation_action.operation_id = tb_operation_slot.id
				WHERE tb_desktop_screens.index IN (1,2) AND   block_info.operation = 'true' AND block_info.operation_id != 0 AND operation_action.pkgname IS NOT NULL


			//查出运营坑位SQL
			$sql =  ""

			if ($arr) {
				foreach ($arr as  $value) {
					if ($value['pkgName'] ==$pkgName) {
						$count +=1;
					}
				}
			}*/


			$noField = 'user';
			$user = getUser();
			$where = "user  = '".$user['user']."'";
			$res['extra'] = $this->field($noField,true)->where($where)->select();
			$module = M();
			if ($res['extra']) {
				foreach ($res['extra'] as $key => $value) {
					$sql = "SELECT  distinct e.id, e.name FROM db_desktop.tb_operation_slot_action as a,db_desktop.tb_operation_slot as b, db_desktop.tb_desktop_screens as c,db_desktop.tb_desktop_blocks as d, db_desktop.tb_desktop as e, db_desktop.tb_desktop_block_info as f, db_desktop.tb_desktop_version_publish as g WHERE a.pkgname = '{$value['pkgName']}' and a.action_type != 'URI' and a.operation_id = b.id and b.slot_group_id = c.slot_group_id and d.slot_id and c.id = d.screen_id and d.slot_id = b.slot_id and f.block_id = d.id and f.operation = true and c.desktop_id = e.id and e.is_effective = true and (c.index = 1 or c.index = 2) AND e.name = g.model AND g.type = 'ALL'  ";

					$res['extra'][$key]['count'] = count($module->query($sql));
					// var_dump($module->query($sql));
					/*$res['extra'][$key]['count'] = 0;
					$res['extra'][$key]['count'] += D("DesktopScreens","Desktop")->getAttentionAppCount($value['pkgName']);
					$res['extra'][$key]['count'] += D("DesktopScreens","Desktop")->getAttentionAppOperationCount($value['pkgName']);*/
				}
			}else{
				$res['extra']= array();
			}
			// var_dump($res['extra']);
			// die;
			return $res;
		}
		public function isDesktopAttentionAppLists($get)
		{

			// SELECT `action_info` FROM `tb_desktop_block_info` WHERE ( block_id IN (SELECT `id` FROM `tb_desktop_blocks` WHERE ( screen_id IN (SELECT `id` FROM `tb_desktop_screens` WHERE ( `index` IN (1,2) and desktop_id = 45 ) )  ) ) and operation = 'false' and  action_info != '{}' and  action_info != ''  )
			// SELECT operation_action.pkgname FROM tb_operation_slot_action operation_action WHERE ( operation_action.operation_id IN (SELECT `id` FROM `tb_operation_slot` WHERE ( slot_group_id = 1 and slot_id IN (SELECT block_info.operation_id FROM tb_desktop_block_info block_info WHERE ( block_info.block_id IN (SELECT `id` FROM `tb_desktop_blocks` WHERE ( screen_id IN (SELECT `id` FROM `tb_desktop_screens` WHERE ( `index` IN (1,2) and slot_group_id = 1 and desktop_id = 45 ) )  ) ) and block_info.operation = 'true' and block_info.operation_id != 0   ) ) ) ) )


			$noField = 'user,id,time';
			$user = getUser();
			$where = "user  = '".$user['user']."' ";
			$res['appLists'] = $this->field($noField,true)->where($where)->select();
			$module = M();
			foreach ($res['appLists'] as $key => $value) {
				$sql = "SELECT  distinct e.id, e.name FROM db_desktop.tb_operation_slot_action as a,db_desktop.tb_operation_slot as b, db_desktop.tb_desktop_screens as c,db_desktop.tb_desktop_blocks as d, db_desktop.tb_desktop as e, db_desktop.tb_desktop_block_info as f WHERE a.pkgname = '{$value['pkgName']}' and a.action_type != 'URI' and a.operation_id = b.id and b.slot_group_id = c.slot_group_id and d.slot_id and c.id = d.screen_id and d.slot_id = b.slot_id and f.block_id = d.id and f.operation = true and c.desktop_id = e.id and e.is_effective = true and (c.index = 1 or c.index = 2)";
				$appLists[$value['pkgName']] = $module->query($sql);
			}

			if ($res['appLists']) {
				$desktop = D("Desktop","Desktop")->isDesktopAttentionAppLists($get,$res['appLists']);
				if (!empty($desktop)) {
					foreach ($desktop as $key => $value) {
						unset($desktop[$key]['id']);
						foreach ($res['appLists'] as $k => $v) {
							if (!empty($v['pkgName'])) {
								if (!empty($appLists[$v['pkgName']])) {
									foreach ($appLists[$v['pkgName']] as $row) {
										if ($row['name'] == $value['name']) {
											$desktop[$key]['appLists'][$k] = 'true';
											break;
										}else{
											$desktop[$key]['appLists'][$k] = 'false';
										}
									}

								}else{
									$desktop[$key]['appLists'][$k] = 'false';
								}

							}else{
								$desktop[$key]['appLists'][$k] = 'false';
							}
						}
					}
					/*$res['desktopCount'] = $desktop['count'];
					unset($desktop['count']);
					foreach ($desktop as $key => $value) {
						$res['desktopLists'][$key]['name'] = $value['name'];
						foreach ($res['appLists'] as $k => $v) {
							if (!empty($value['pkgName'])) {
								foreach ($value['pkgName'] as  $row) {

									if ($row == $v['pkgName']) {
										$res['desktopLists'][$key]['appLists'][$k] = 'true';
										break;
									}else{
										$res['desktopLists'][$key]['appLists'][$k] = 'false';
									}
								}
							}else{
								$res['desktopLists'][$key]['appLists'][$k] = 'false';
							}
						}
					}*/
					$res['desktopLists'] = $desktop;
				}else{
					$res['desktopLists']= array();
				}
			}else{
				$res['appLists']= array();
				$res['desktopLists']= array();
			}
			return $res;
		}
		public function modifyAttentionApp($put)
		{
			if (!empty($put['appName']) && !empty($put['pkgName']) && !empty($put['id']) ) {
				if ($this->find($put['id'])) {
					$user = getUser();
					if (!empty($user)) {
						if (!$this->getOneForAppNameAndpkgNameUserNoId($put['pkgName'],$user['user'],$put['id'])) {
							$options = array(
								'app_name'=>$put['appName'],
								'pkgname'=>$put['pkgName'],
								'time'=>time(),
							);
							return $this->where("id = %d",array($put['id']))->save($options);
						}else{
							result('应用已存在');
						}
					}
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