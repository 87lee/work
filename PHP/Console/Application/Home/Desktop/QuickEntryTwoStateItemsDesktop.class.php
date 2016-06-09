<?php
	namespace Home\Desktop;
	class QuickEntryTwoStateItemsDesktop extends \Think\Model
	{
		protected $tableName = 'quick_entry_two_state_items';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'focusedNormalDrawable' =>'focused_normal_drawable',
			'focusedActiveDrawable' =>'focused_active_drawable',
			'normalDrawable' =>'normal_drawable',
			'activeDrawable' =>'active_drawable',
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
			$resSubField = "`name`,`action_info`,`event_type`,`x`,`y`,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',`focused_normal_drawable`) as focusedNormalDrawable,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',`focused_active_drawable`) as focusedActiveDrawable,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',`normal_drawable`) as normalDrawable,";
			$resSubField .="concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',`active_drawable`) as activeDrawable";

			if (!empty($id)) {

				$res['extra'] = D('QuickEntryTwoState','Desktop')->getValOneForId($id);

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
				$res['extra'] = D('QuickEntryTwoState','Desktop')->getValALL();
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