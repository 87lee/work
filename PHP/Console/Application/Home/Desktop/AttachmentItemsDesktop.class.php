<?php
	namespace Home\Desktop;
	class AttachmentItemsDesktop extends \Think\Model
	{
		protected $tableName = 'attachment_items';
		protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'radiusTopLeft' =>'radius_top_left', // 把表单中name映射到数据表的username字段
			'radiusTopRight'  =>'radius_top_right', // 把表单中的mail映射到数据表的email字段
			'radiusBottomLeft' =>'radius_bottom_left', // 把表单中name映射到数据表的username字段
			'radiusBottomRight'  =>'radius_bottom_right', // 把表单中的radiusBottomRight映射到数据表的radius_bottom_right字段
			'actionInfo'  =>'action_info', // 把表单中的actionId映射到数据表的action_id字段
			'attacheId'  =>'attache_id', // 把表单中的actionId映射到数据表的action_id字段
			'forcusPath' =>'forcus_path', // 把表单中name映射到数据表的username字段
			'normalPath'  =>'normal_path', // 把表单中的mail映射到数据表的email字段
		);
		public function addAttachmentItems($attachmentId,$options)
		{
			foreach ($options as $key => $value) {
				if (!empty($value)) {
					$value['attache_id'] = $attachmentId;
					$value['attacheId'] = $attachmentId;
					$res[] = $this->create($value);
				}
			}
			if (!empty($res)) {
				$this->addAll($res);
			}
		}
		public function deleteAttachmentItems($id)
		{
			if ($this->where("`attache_id`=%d",array($id))->find()) {
				$this->where("`attache_id`=%d",array($id))->delete();
			}
			return true;

		}
		public function attachmentLists($id =null)
		{
			if (!empty($id)) {

				$res['extra'] = D('Attachment','Desktop')->getValOneForId($id);

				if (!empty($res['extra'])) {
					$resSub = $this->field("`name`,`radius_top_left`,`radius_top_right`,`radius_bottom_left`,`radius_bottom_right`,`action_info` as actionInfo,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath")->where("`attache_id`=%d",array($id))->select();

					/*if ($resSub) {
						$resSub = D('QuickEntryItems','Desktop')->actionAppLists($resSub);
						$res['extra']['extra'] = $resSub;
					}else{
						$res['extra']['extra']= array();
					}*/

					if ($resSub) {
						foreach ($resSub as $key => $value) {

							$actionInfo = json_decode($value['actionInfo'],true);
							unset($value['actionInfo']);
							$data[$key] = array_merge($value,checkActionApp($actionInfo));
						}

						$res['extra']['extra'] = $data;
					}else{
						$res['extra']['extra'] = array();
					}
				}else{
					$res['extra'] = array();
				}
			}else{
				$res['extra'] = D('Attachment','Desktop')->getValAll();
				if (empty($res['extra'])) {
					$res['extra'] = array();
				}else{
					foreach ($res['extra'] as $k => $v) {
						$resSub = $this->field("`name`,`radius_top_left`,`radius_top_right`,`radius_bottom_left`,`radius_bottom_right`,`action_info` as actionInfo,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',normal_path) as normalPath,concat('".C('DOWNLOAD_IMG_PREFIX_HOST')."',forcus_path) as forcusPath")->where("`attache_id`=%d",array($v['id']))->select();

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
		public function modifyAttachmentItems($attachmentId,$options)
		{
			if ($this->where("`attache_id`=%d",array($attachmentId))->find()) {
				$this->where("`attache_id`=%d",array($attachmentId))->delete();
			}

			foreach ($options as $key => $value) {
				if (!empty($value)) {
					$value['attache_id'] = $attachmentId;
					$value['attacheId'] = $attachmentId;
					$res[] = $this->create($value);
				}

			}
			if (!empty($res)) {
				$this->addAll($res);
			}

		}

	}