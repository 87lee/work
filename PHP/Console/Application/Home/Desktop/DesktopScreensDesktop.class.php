<?php
	namespace Home\Desktop;
	class DesktopScreensDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_screens';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'desktopId'=>'desktop_id',
			'slotGroupId'=>'slot_group_id',
			'itemStyle'=>'item_style'
		);
		public function addDesktopScreens($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}
		public function deleteDesktopScreens($desktopId)
		{
			if ( $this->where("`desktop_id` IN (".$desktopId.")")->find()) {
				return $this->where("`desktop_id` IN (".$desktopId.")")->delete();
			}
		}
		public function screensLists($desktopId)
		{
			$screensLists = $this->field('id,index,slot_group_id,item_style')->where("`desktop_id` IN (".$desktopId.")")->select();
			if ($screensLists) {

				foreach ($screensLists as $key => $value) {
					if (!empty($value['itemStyle'])) {
						$screensLists[$key]['itemStyle'] = json_decode($value['itemStyle'],true);
					}else{
						$screensLists[$key]['itemStyle'] = new \stdClass();
					}
					$screensLists[$key]['blocks'] = D('DesktopBlocks','Desktop')->blocksLists($value['id'],$value['slotGroupId'],$value['index'],$desktopId);
				}
			}else{
				$screensLists = array();
			}
			return $screensLists;
		}
		public function createDesktopScreens($desktopId,$error = false)
		{
			$screensLists = $this->field('`id`,`index` as sid ,slot_group_id,item_style')->where("`desktop_id` IN (".$desktopId.")")->select();
			$screens = array();
			if ($screensLists) {
				foreach ($screensLists as $key => $value) {
					$screens[$key]['slots'] = D('DesktopBlocks','Desktop')->createDesktopblocksLists($value['id'],$error,$value,$value['slotGroupId']);
					$screens[$key]['sid'] = $value['sid']-1;
					if (!empty($value['itemStyle'])) {
						$screens[$key]['itemStyle'] = json_decode($value['itemStyle'],true);
					}else{
						unset($screens[$key]['itemStyle']);
					}
					if ($error) {
						if (!empty($screens[$key]['slots']['reason'])) {
							return $screens[$key]['slots'];
						}
					}else{
						if (!empty($screens[$key]['slots']['reason'])) {
							result('第'.$value['sid'].'屏'.$screens[$key]['slots']['reason']);
						}
					}
					if (empty($screens[$key]['slots'])) {
						$screens[$key]['slots'] = array();
					}
				}
			}else{

				/*if ($error) {
					return $result = array('reason'=> '没有屏存在');
					// return false;
				}else{
					result('没有屏存在');
				}*/
			}
			return $screens;

		}

		public function getDesktopLists($screenIdSql,$slotGroupId)
		{
			$getDesktopIdSql = $this->field("`desktop_id`")->where("`id` IN (".$screenIdSql.")  and slot_group_id = ".$slotGroupId)->select(false);
			return D('Desktop','Desktop')->getDesktopLists($getDesktopIdSql);

		}
		public function modifyOperationSlot($screenIdSql,$modifySourceVersion,$slotGroupId)
		{
			$getDesktopIdSql = $this->field("`desktop_id`")->where("`id` IN (".$screenIdSql.") and slot_group_id = ".$slotGroupId)->select(false);

			if ($modifySourceVersion) {
				D('Desktop','Desktop')->modifySourceVersion($getDesktopIdSql);
			}else{
				D('Desktop','Desktop')->modifyOperationSlot($getDesktopIdSql);
			}

		}
		public function getScreenIdSqlForDesktopId($desktopId)
		{
			return $this->field("id")->where("`desktop_id` = %d",array($desktopId))->select(false);
		}
		public function getSqlIdForDesktopId($desktopArrId)
		{
			return $this->field('id')->where("desktop_id IN (".$desktopArrId.") ")->select(false);
		}

		public function getSqlIdForSlotGroupId($SlotGroupId)
		{
			return $this->field('id')->where("slot_group_id IN (".$SlotGroupId.") ")->select(false);
		}
		public function getValOneForDesktopId($DesktopId)
		{
			return $this->where("`desktop_id`=%d",array( $DesktopId))->find();
		}
		public function getValOneForDesktopArrId($DesktopArrId)
		{
			return $this->where("`desktop_id` IN (".$DesktopArrId.")")->find();
		}
		public function getArrForDesktopId($DesktopId)
		{
			return $this->where("`desktop_id`=%d",array( $DesktopId))->select();
		}
		public function getAttentionAppCount($pkgName)
		{
			$getAttemtionAppCountScreensSql = $this->field("id")->where("`index` IN (1,2)")->select(false);

			return D("DesktopBlocks","Desktop")->getAttentionAppCount($getAttemtionAppCountScreensSql,$pkgName);
		}
		public function getAttentionAppOperationCount($pkgName)
		{
			$operationSlotGroup = D('OperationSlotGroup','Desktop')->getOperationSlotGroupAll();
			if ($operationSlotGroup) {
				$count = 0;
				foreach ($operationSlotGroup as $value) {
					$getAttemtionAppCountScreensSql = $this->field("id")->where("`index` IN (1,2) and slot_group_id = ".$value['id'])->select(false);
					$getoperationIdSql = D("DesktopBlocks","Desktop")->getAttentionAppOperationCount($getAttemtionAppCountScreensSql,$pkgName);
					$count += D('OperationSlot','Desktop')->getAttentionAppOperationCount($value['id'],$getoperationIdSql,$pkgName);
				}
				return $count ;
			}
		}
		public function getAttentionAppCountForDesktopID($desktopId)
		{
			$getAttemtionAppCountScreensSql = $this->field("id")->where("`index` IN (1,2) and desktop_id = ".$desktopId)->select(false);

			return D("DesktopBlocks","Desktop")->getAttentionAppCountForDesktopID($getAttemtionAppCountScreensSql);
		}
		public function getAttentionAppOperationCountForDesktopID($desktopId)
		{
			$operationSlotGroup = D('OperationSlotGroup','Desktop')->getOperationSlotGroupAll();
			$pkgNameArr = array();
			if ($operationSlotGroup) {

				foreach ($operationSlotGroup as $value) {
					$getAttemtionAppCountScreensSql = $this->field("id")->where("`index` IN (1,2) and slot_group_id = ".$value['id'] .' and desktop_id = '. $desktopId)->select(false);
					$getoperationIdSql = D("DesktopBlocks","Desktop")->getAttentionAppOperationCountForDesktopID($getAttemtionAppCountScreensSql);
					$pkgName = D('OperationSlot','Desktop')->getAttentionAppOperationCountForDesktopID($value['id'],$getoperationIdSql);
					if (!empty($pkgName)) {
						$pkgNameArr = array_merge($pkgNameArr,$pkgName);
					}
				}
			}
			return $pkgNameArr ;
		}
		/*public function modifyDesktopScreens($desktopId,$options,$desktopInfo=false)
		{

			$res = $this->where("`desktop_id`=%d",array($desktopId))->select();

			if (count($res) == count($options)) {

				foreach ($res as $key => $value) {
					$data = array(
						'index'=>$options[$key]['index']
					);
					$this->where("`id`=%d",array($value['id']))->save($data);

					$response = D('DesktopBlocks','Desktop')->where("`screen_id`=%d",array($value['id']))->select();

					if (count($response) == count($options[$key]['blocks'])) {

						foreach ($options[$key]['blocks'] as $k => $v) {

							$data = array(
								'width'=>$v['w'],
								'height'=>$v['h'],
								'yw'=>$v['yw'],
								'yh'=>$v['yh'],
								'bg'=>$v['bg'],
								'x'=>$v['x'],
								'y'=>$v['y'],
								'update_time'=>time()
							);
							D('DesktopBlocks','Desktop')->modifyDesktopBlocks($response[$k]['id'],$data);

							if ($desktopInfo) {

								$data = array(

									'title'=>isset($v['title'])?$v['title']:'',
									'slot_id'=>isset($v['slotId'])?$v['slotId']:'',
									'is_edit_able'=>$v['isEditAble'],
									'data_source'=>$v['detaSource'],
									'layout'=>$v['layout'],
									'action_id'=>$v['actionId']
								);

								$desktopBlockInfoId = D('DesktopBlockInfo','Desktop')->modifyDesktopBlockInfo($response[$k]['id'],$data);

								if ($v['layout'] == 'IMAGE') {
									$data = array(
										'pic'=> $v['pic']
									);
									D('DesktopBlockPic','Desktop')->modifyDesktopBlockPic($desktopBlockInfoId,$data);
								}elseif ($v['layout'] == 'VIDEO') {
									$data = array(
										'url'=> $v['url'],
										'duration' =>$v['duration']
									);
									D('DesktopBlockVideo','Desktop')->modifyDesktopBlockVideo($desktopBlockInfoId,$data);
								}
							}
						}
					}elseif (count($response) > count($options[$key]['blocks'])) {

						$modifyId = count($options[$key]['blocks']);
						foreach ($response as $k => $v) {

							if ($modifyId <= 0) {

								$desktopBlockInfo = D('DesktopBlockInfo','Desktop')->where("`block_id`=%d",array($v['id']))->find();
								if ($desktopBlockInfo) {
									if (D('DesktopBlockPic','Desktop')->where("`block_id`=%d",array($desktopBlockInfo['id']))->find()) {
										D('DesktopBlockPic','Desktop')->deleteDesktopBlockPic($desktopBlockInfo['id']);
									}elseif (D('DesktopBlockVideo','Desktop')->where("`block_id`=%d",array($desktopBlockInfo['id']))->find()) {
										D('DesktopBlockVideo','Desktop')->deleteDesktopBlockVideo($desktopBlockInfo['id']);
									}
									D('DesktopBlockInfo','Desktop')->deleteDesktopBlockInfo($v['id']);
								}else{
									D('DesktopBlocks','Desktop')->deleteDesktopBlockId($v['id']);
								}



							}else{
								$data = array(
									'width'=>$options[$key]['blocks'][$k]['w'],
									'height'=>$options[$key]['blocks'][$k]['h'],
									'yw'=>$options[$key]['blocks'][$k]['yw'],
									'yh'=>$options[$key]['blocks'][$k]['yh'],
									'bg'=>$options[$key]['blocks'][$k]['bg'],
									'x'=>$options[$key]['blocks'][$k]['x'],
									'y'=>$options[$key]['blocks'][$k]['y'],
									'update_time'=>time()
								);

								D('DesktopBlocks','Desktop')->modifyDesktopBlocks($v['id'],$data);

								if ($desktopInfo) {

									$data = array(

										'title'=>isset($options[$key]['blocks'][$k]['title'])?$options[$key]['blocks'][$k]['title']:'',
										'slot_id'=>isset($options[$key]['blocks'][$k]['slotId'])?$options[$key]['blocks'][$k]['slotId']:'',
										'is_edit_able'=>$options[$key]['blocks'][$k]['isEditAble'],
										'data_source'=>$options[$key]['blocks'][$k]['detaSource'],
										'layout'=>$options[$key]['blocks'][$k]['layout'],
										'action_id'=>$options[$key]['blocks'][$k]['actionId']
									);

									$desktopBlockInfoId = D('DesktopBlockInfo','Desktop')->modifyDesktopBlockInfo($v['id'],$data);

									if ($options[$key]['blocks'][$k]['layout'] == 'IMAGE') {
										$data = array(
											'pic'=> $options[$key]['blocks'][$k]['pic']
										);

										D('DesktopBlockPic','Desktop')->modifyDesktopBlockPic($desktopBlockInfoId,$data);

									}elseif ($options[$key]['blocks'][$k]['layout'] == 'VIDEO') {
										$data = array(
											'url'=> $options[$key]['blocks'][$k]['url'],
											'duration' =>$options[$key]['blocks'][$k]['duration']
										);
										D('DesktopBlockVideo','Desktop')->modifyDesktopBlockVideo($desktopBlockInfoId,$data);
									}
								}

							}
							$modifyId--;
						}elseif (count($response) < count($options[$key]['blocks'])) {

							$modifyId = 0;
							foreach ($response as $k => $v) {


								$data = array(
									'width'=>$options[$key]['blocks'][$k]['w'],
									'height'=>$options[$key]['blocks'][$k]['h'],
									'yw'=>$options[$key]['blocks'][$k]['yw'],
									'yh'=>$options[$key]['blocks'][$k]['yh'],
									'bg'=>$options[$key]['blocks'][$k]['bg'],
									'x'=>$options[$key]['blocks'][$k]['x'],
									'y'=>$options[$key]['blocks'][$k]['y'],
									'update_time'=>time()
								);

								D('DesktopBlocks','Desktop')->modifyDesktopBlocks($v['id'],$data);

								if ($desktopInfo) {

									$data = array(

										'title'=>isset($options[$key]['blocks'][$k]['title'])?$options[$key]['blocks'][$k]['title']:'',
										'slot_id'=>isset($options[$key]['blocks'][$k]['slotId'])?$options[$key]['blocks'][$k]['slotId']:'',
										'is_edit_able'=>$options[$key]['blocks'][$k]['isEditAble'],
										'data_source'=>$options[$key]['blocks'][$k]['detaSource'],
										'layout'=>$options[$key]['blocks'][$k]['layout'],
										'action_id'=>$options[$key]['blocks'][$k]['actionId']
									);

									$desktopBlockInfoId = D('DesktopBlockInfo','Desktop')->modifyDesktopBlockInfo($v['id'],$data);

									if ($options[$key]['blocks'][$k]['layout'] == 'IMAGE') {
										$data = array(
											'pic'=> $options[$key]['blocks'][$k]['pic']
										);

										D('DesktopBlockPic','Desktop')->modifyDesktopBlockPic($desktopBlockInfoId,$data);

									}elseif ($options[$key]['blocks'][$k]['layout'] == 'VIDEO') {
										$data = array(
											'url'=> $options[$key]['blocks'][$k]['url'],
											'duration' =>$options[$key]['blocks'][$k]['duration']
										);
										D('DesktopBlockVideo','Desktop')->modifyDesktopBlockVideo($desktopBlockInfoId,$data);
									}
								}



								$modifyId++;
							}

							for ($i=0; $i < $modifyId; $i++) {

								unset($options[$key]['blocks'][$i]);

							}

							$options[$key]['blocks'] = array_values($options[$key]['blocks']);

							foreach ($options[$key]['blocks'] as $k => $v) {

								$data = array(
									'screen_id'=>$value['id'],
									'width'=>$v['w'],
									'height'=>$v['h'],
									'yw'=>$v['yw'],
									'yh'=>$v['yh'],
									'bg'=>$v['bg'],
									'x'=>$v['x'],
									'y'=>$v['y'],
									'update_time'=>time()
								);

								$desktopBlocksId = D('DesktopBlocks','Desktop')->addDesktopBlocks($data);

								if ($desktopInfo) {

									$data = array(
										'block_id'=>$desktopBlocksId,
										'title'=>isset($v['title'])?$v['title']:'',
										'slot_id'=>isset($v['slotId'])?$v['slotId']:'',
										'is_edit_able'=>$v['isEditAble'],
										'data_source'=>$v['detaSource'],
										'layout'=>$v['layout'],
										'action_id'=>$v['actionId']
									);

									$desktopBlockInfoId = D('DesktopBlockInfo','Desktop')->addDesktopBlockInfo($data);
									if ($v['layout'] == 'IMAGE') {
										$data = array(
											'pic'=> $v['pic'],
											'block_id' =>$desktopBlockInfoId
										);
										D('DesktopBlockPic','Desktop')->addDesktopBlockPic($data);
									}elseif ($v['layout'] == 'VIDEO') {
										$data = array(
											'url'=> $v['url'],
											'block_id' =>$desktopBlockInfoId,
											'duration' =>$v['duration']
										);
										D('DesktopBlockVideo','Desktop')->addDesktopBlockVideo($data);
									}
								}
							}
						}
					}
				}
			}elseif (count($res) > count($options)) {

				$modifyIdTop = count($options);

				foreach ($res as $key => $value) {
					if ($modifyIdTop <= 0) {
						$this->where("`id`=%d",array($value['id']))->delete();
					}else{
						$this->where("`id`=%d",array($value['id']))->save($options[$key]);
					}
					$modifyIdTop--;
				}
			}elseif (count($res) < count($options)) {

				$modifyId = 0;
				foreach ($res as $key => $value) {
					$this->where("`id`=%d",array($value['id']))->save($options[$key]);
					$modifyId++;
				}
				for ($i=0; $i < $modifyId; $i++) {
					unset($options[$i]);
				}
				$options = array_values($options);

				$this->addAll($options);
			}
		}*/

	}