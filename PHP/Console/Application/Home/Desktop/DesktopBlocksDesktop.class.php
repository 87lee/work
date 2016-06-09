<?php
	namespace Home\Desktop;
	class DesktopBlocksDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_blocks';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'slotId' =>'slot_id', // 把表单中name映射到数据表的username字段
			'w'  =>'width', // 把表单中的mail映射到数据表的email字段
			'h'=>'height',
			'updateTime'=>'update_time',
			'screenId'=>'screen_id',
			'nextFocusLeftId'  =>'next_focus_left_id',
			'nextFocusRightId'=>'next_focus_right_id',
			'nextFocusUpId'=>'next_focus_up_id',
			'nextFocusDownId'=>'next_focus_down_id',
		);
		public function addDesktopBlocks($options)
		{
			$this->create($options);
			return $addId = $this->add();
		}
		public function deleteDesktopBlocks($desktopScreensSql)
		{
			if ( $this->where("`screen_id` IN (".$desktopScreensSql.")")->find()) {
				return $this->where("`screen_id` IN (".$desktopScreensSql.")")->delete();
			}
		}

		public function blocksLists($screensId,$slotGroupId,$index,$desktopId)
		{
			$response = $this->field('id,width,height, yw ,yh,x,y,bg,slot_id,next_focus_left_id,next_focus_right_id,next_focus_up_id,next_focus_down_id')->where("`screen_id` = %d",array($screensId))->select();

			/*{"type": "ACTION","extraData": [{ "key": "key","value": "value"}],"action": "this.is.action.name"}
			{"type": "APP","pkgName": "this.is.package.name","appInfo":{"path":"1231","pkgName":"111","versionCode":"2132"}}
			{"type": "APP","pkgName": "this.is.package.name"}
			{"type": "COMPONENT","extraData": [{ "key": "key","value": "value"}],"clsName": "this.is.package.name","component": "this.is.class.name"}
			{"type": "URI","uri": "this.is.uri"}*/
			if ($response) {
				foreach ($response as $key => $value) {
					$res = D('DesktopBlockInfo','Desktop')->blocksInfoLists($value['id'],$slotGroupId);
					if ($res) {
						$data[$key] = $res;
						if (!empty($data[$key]['pic1']) && !empty($data[$key]['pic2']) && !empty($data[$key]['pic3']) ) {
							if ($value['w'] == $value['h']) {
								$data[$key]['pic'] = $data[$key]['pic1'];
							}elseif ($value['w']  > $value['h']) {
								$data[$key]['pic'] = $data[$key]['pic2'];
							}elseif ($value['w'] < $value['h']) {
								$data[$key]['pic'] = $data[$key]['pic3'];
							}
							unset($data[$key]['pic1']);
							unset($data[$key]['pic2']);
							unset($data[$key]['pic3']);
						}
					}
					$data[$key]['type'] = 'common';
					$data[$key]['slotId'] = $value['slotId'];
					$data[$key]['w'] = $value['w'];
					$data[$key]['yw'] = $value['yw'];
					$data[$key]['h'] = $value['h'];
					$data[$key]['yh'] = $value['yh'];
					$data[$key]['bg'] = $value['bg'];
					$data[$key]['x'] = $value['x'];
					$data[$key]['y'] = $value['y'];
					$data[$key]['nextFocusLeftId'] = $value['nextFocusLeftId'];
					$data[$key]['nextFocusRightId'] = $value['nextFocusRightId'];
					$data[$key]['nextFocusUpId'] = $value['nextFocusUpId'];
					$data[$key]['nextFocusDownId'] = $value['nextFocusDownId'];

				}
			}else{
				$data = array();
			}
			if (isset($index)&&!empty($desktopId)) {
				$dataQuickEntry = D('DesktopDownloadQuickEntry','Desktop')->downloadQuickEntryLists($desktopId,($index-1));
			}
			if (!empty($data)&&!empty($dataQuickEntry)) {
				$data = array_merge($data,$dataQuickEntry);
			}elseif (!empty($dataQuickEntry)) {
				return $dataQuickEntry;
			}
			return $data;
		}

		public function createDesktopblocksLists($screensId,$error = false,$screens,$slotGroupId)
		{
			$data = $this->field('id ,`slot_id`,`width`,`height`,`bg`, `yw`,`yh`,`x`,`y`,`update_time`,next_focus_left_id,next_focus_right_id,next_focus_up_id,next_focus_down_id')->where("`screen_id` = %d",array($screensId))->select();

			if ($data) {
				foreach ($data as $key => $value) {
					$res = D('DesktopBlockInfo','Desktop')->createDesktopBlocksInfoLists($value['id'],$error,$value,$slotGroupId);
					if ($error) {
						if (empty($res['reason'])) {
							$data[$key] = $res;
						}else{
							return $result = array('reason'=> '第'.$screens['sid'].'屏'.$res['reason']);
						}
					}else{
						if (empty($res)) {
							result('该桌面第'.$screens['sid'].'屏'.$value['slotId'].'坑位参数出错,没有找到屏信息');
						}else{
							$data[$key] = $res;
						}
					}

					if (!empty($res['slotID'])) {
						$data[$key]['id'] = $res['slotID'];
						unset($data[$key]['slotID']);
					}else{
						$data[$key]['id'] = $value['slotId'];
						unset($data[$key]['slotId']);
					}
					if (!empty($data[$key]['pic1']) && !empty($data[$key]['pic2']) && !empty($data[$key]['pic3']) ) {
						if ($value['w'] == $value['h']) {
							$data[$key]['picUrl'] = $data[$key]['pic1'];
						}elseif ($value['w']  > $value['h']) {
							$data[$key]['picUrl'] = $data[$key]['pic2'];
						}elseif ($value['w'] < $value['h']) {
							$data[$key]['picUrl'] = $data[$key]['pic3'];
						}
						unset($data[$key]['pic1']);
						unset($data[$key]['pic2']);
						unset($data[$key]['pic3']);
					}
					$data[$key]['width'] = $value['w'];
					$data[$key]['yw'] = $value['yw'];
					$data[$key]['height'] = $value['h'];
					$data[$key]['yh'] = $value['yh'];
					$data[$key]['bg'] = $value['bg'];
					$data[$key]['x'] = $value['x'];
					$data[$key]['y'] = $value['y'];
					$data[$key]['updateTime'] = $value['updateTime'];

					if (!empty($value['nextFocusLeftId']) && $value['nextFocusLeftId'] != '') {
						$data[$key]['nextFocusLeftId'] = $value['nextFocusLeftId'];
					}
					if (!empty($value['nextFocusRightId']) && $value['nextFocusRightId'] != '') {
						$data[$key]['nextFocusRightId'] = $value['nextFocusRightId'];
					}
					if (!empty($value['nextFocusUpId']) && $value['nextFocusUpId'] != '') {
						$data[$key]['nextFocusUpId'] = $value['nextFocusUpId'];
					}
					if (!empty($value['nextFocusDownId']) && $value['nextFocusDownId'] != '') {
						$data[$key]['nextFocusDownId'] = $value['nextFocusDownId'];
					}
				}
			}else{
				$data = array();
			}

			/*else{
				if ($error) {
					return $result = array('reason'=> '没有找到第'.$screens['sid'].'屏坑位参数');
					// return false;
				}else{
					result('没有找到第'.$screens['sid'].'屏坑位参数');
				}

			}*/
			return $data;
		}
		public function modifyDesktopBlocks($desktopBlocksId,$options)
		{
			$this->create($options);
			$this->where("`id`=%d",array($desktopBlocksId))->save();
		}
		public function deleteDesktopBlockId($id)
		{
			if ( $this->where("`id` =%d",array($id))->find()) {
				return $this->where("`id` =%d",array($id))->delete();
			}

		}
		public function getScreensIdLists($blockIdSql,$slotGroupId)
		{
			$getScreenIdSql = $this->field("`screen_id`")->where("`id` IN (".$blockIdSql.")")->select(false);
			return D('DesktopScreens','Desktop')->getDesktopLists($getScreenIdSql,$slotGroupId);

		}
		public function modifyOperationSlot($blockIdSql,$modifySourceVersion,$slotGroupId)
		{
			$getScreenIdSql = $this->field("`screen_id`")->where("`id` IN (".$blockIdSql.")")->select(false);

			return D('DesktopScreens','Desktop')->modifyOperationSlot($getScreenIdSql,$modifySourceVersion,$slotGroupId);

		}
		public function modifyUpdateTime($blockIdSql,$desktopSlotUpdateTime)
		{
			if ($desktopSlotUpdateTime) {

				$options = array(
					'update_time'=>time()
				);
				$this->where("`id` IN (".$blockIdSql.")")->save($options);
			}

		}
		public function getIdAndSlotIdForDesktopScreensIdSql($desktopScreensIdSql)
		{
			return $this->field("id,slot_id")->where("`screen_id` IN (".$desktopScreensIdSql.")")->select();
		}
		public function getArrIdForDesktopScreensIdSql($desktopScreensIdSql)
		{
			return $this->field('id')->where("screen_id IN (".$desktopScreensIdSql.") ")->select();
		}
		public function getValOneForScreensId($screensId)
		{
			return $this->where("screen_id IN (".$screensId.") ")->find();
		}
		public function getSqlIdForDesktopScreensIdSql($desktopScreensIdSql)
		{
			return $this->field('id')->where("screen_id IN (".$desktopScreensIdSql.") ")->select(false);
		}
		public function getAttentionAppCount($getAttemtionAppCountScreensSql,$pkgName)
		{
			$desktopBlockIdSql = $this->field('id')->where("screen_id IN (".$getAttemtionAppCountScreensSql.") ")->select(false);
			return D("DesktopBlockInfo","Desktop")->getAttentionAppCount($desktopBlockIdSql,$pkgName);
		}
		public function getAttentionAppOperationCount($getAttemtionAppCountScreensSql,$pkgName)
		{
			$desktopBlockIdSql = $this->field('id')->where("screen_id IN (".$getAttemtionAppCountScreensSql.") ")->select(false);
			return D("DesktopBlockInfo","Desktop")->getAttentionAppOperationCount($desktopBlockIdSql,$pkgName);
		}
		public function getAttentionAppCountForDesktopID($getAttemtionAppCountScreensSql)
		{
			$desktopBlockIdSql = $this->field('id')->where("screen_id IN (".$getAttemtionAppCountScreensSql.") ")->select(false);
			return D("DesktopBlockInfo","Desktop")->getAttentionAppCountForDesktopID($desktopBlockIdSql);
		}
		public function getAttentionAppOperationCountForDesktopID($getAttemtionAppCountScreensSql)
		{
			$desktopBlockIdSql = $this->field('id')->where("screen_id IN (".$getAttemtionAppCountScreensSql.") ")->select(false);
			return D("DesktopBlockInfo","Desktop")->getAttentionAppOperationCountForDesktopID($desktopBlockIdSql);
		}
		public function getValOneForId($id)
		{
			return $this->find($id);
		}
		public function getArrForScreenId($screensId)
		{
			return $this->where('screen_id =%d',array($screensId))->select();
		}
	}