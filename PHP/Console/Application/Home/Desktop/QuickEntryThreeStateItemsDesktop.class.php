<?php
	namespace Home\Desktop;
	class QuickEntryThreeStateItemsDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_three_state_items';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'eventA' =>'event_a',
			'eventB' =>'event_b',
			'eventC' =>'event_c',
			'drawableA' =>'drawable_a',
			'drawableB' =>'drawable_b',
			'drawableC' =>'drawable_c',
			'focusedDrawableA' =>'focused_drawable_a',
			'focusedDrawableB' =>'focused_drawable_b',
			'focusedDrawableC' =>'focused_drawable_c',
			'quickId'  =>'quick_id', // 把表单中的actionId映射到数据表的action_id字段
			'actionInfo'  =>'action_info', // 把表单中的actionId映射到数据表的action_id字段
			'eventType'  =>'event_type', // 把表单中的actionId映射到数据表的action_id字段
		);
		public function addQuickEntryItems($quickEntrytId,$options)
		{
			foreach ($options as $key => $value) {
				if (!empty($value)) {
					$value['quick_id'] = $quickEntrytId;
					$value['quickId'] = $quickEntrytId;
					$res[] = $this->create($value);
				}
			}
			$this->addAll($res);
		}
		public function deleteQuickEntryItems($id)
		{
			if ($this->where("`quick_id`=%d",array($id))->find()) {
				$this->where("`quick_id`=%d",array($id))->delete();
			}

			return true;
		}

		public function quickEntryLists($id =null)
		{
			$resSubField = "`name`,`action_info`,`event_type`,`x`,`y`,`event_a`,`event_b`,`event_c`,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',drawable_a) as drawableA,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',drawable_b) as drawableB,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',drawable_c) as drawableC,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."', 	focused_drawable_a) as focusedDrawableA,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."', 	focused_drawable_b) as focusedDrawableB,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."', 	focused_drawable_c) as focusedDrawableC";

			if (!empty($id)) {

				$res['extra'] = D('QuickEntryThreeState','Desktop')->getValOneForId($id);

				if (!empty($res['extra'])) {

					$resSub = $this->field($resSubField)->where("`quick_id`=%d",array($id))->select();

					if ($resSub) {
						foreach ($resSub as $key => $value) {
							$actionInfo = json_decode($value['actionInfo'],true);
							unset($value['actionInfo']);
							$data[$key] = array_merge($value,checkActionApp($actionInfo));
						}
						$res['extra']['extra'] = $data;
					}else{
						$res['extra']['extra']= array();
					}
				}else{
					$res['extra'] = array();
				}
			}else{
				$res['extra'] = D('QuickEntryThreeState','Desktop')->getValALL();
				if (empty($res['extra'])) {
					$res['extra'] = array();
				}else{

					foreach ($res['extra'] as $k => $v) {

						$resSub = $this->field($resSubField)->where("`quick_id`=%d",array($v['id']))->select();

						if ($resSub) {
							foreach ($resSub as $key => $value) {
								$actionInfo = json_decode($value['actionInfo'],true);
								unset($value['actionInfo']);
								$data[$key] = array_merge($value,checkActionApp($actionInfo));
							}
							$res['extra'][$k]['extra'] = $data;
						}else{
							$res['extra'][$k]['extra'] = array();
						}
					}
				}
			}
			return $res;
		}
		public function modifyQuickEntryItems($quickEntrytId,$options)
		{
			if ($this->where("`quick_id`=%d",array($quickEntrytId))->find()) {
				$this->where("`quick_id`=%d",array($quickEntrytId))->delete();
			}

			foreach ($options as $key => $value) {
				if (!empty($value)) {
					$value['quick_id'] = $quickEntrytId;
					$value['quickId'] = $quickEntrytId;
					$res[] = $this->create($value);
				}
			}
			if (!empty($res)) {
				$this->addAll($res);
			}

		}
	}