<?php

	namespace Home\Desktop;
	class SlotTagImgDesktop extends \Think\Model
	{
		protected $tableName = 'slot_tag_img';
		protected $connection = 'DB_DESKTOP';

		public function addSlotTagImg($post)
		{
			if ( empty($post['name'])) {
				result('param');
			}

			if ($res = $this->getOneForName($post['name'])) {
				result('图片组图片名存在');
			}

			require_once '../Base/function/Form.class.php';

			$form = new \form();
			$formData = $form->getFormFile();

			require_once '../Base/Ossupclass/OssBase.class.php';
			$base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);

			if (empty($formData)) {
				result('请上传图片');
			}
			$res = $base->uploadFile($formData);

			foreach ($res as $key => $value) {
				if (empty($value['oss'])) {
					result('OSS返回数据出错');
				}
				list($width, $height) = getimagesize($formData[$key]['filepath']);
				$options = array(
					'name'=>$post['name'],
					'path'=>$value['oss'],
					'width'=>intval($width),
					'height'=>intval($height),
				);
			}

			if (!empty($options)) {
				$this->add($options);
			}
			return ;

		}
		public function getOneForName($name)
		{
			return $this->where("name='%s'",array($name))->select();
		}
		public function deleteSlotTagImg($put)
		{
			if (empty($put) || !is_array($put)) {
				result('param');
			}
			foreach ($put as  $value) {
				$arr[] = (int)$value;
			}
			$arr = array_unique($arr);
			if (!empty($arr)) {
				$sqlId = implode( ',',$arr);
				if ($res = $this->where("id IN (".$sqlId.")")->select()) {
					$this->where("id IN (".$sqlId.")")->delete();
				}
			}
			return ;
		}

		public function slotTagImgLists($get)
		{

			$where = '';
			if (!empty($get['name'])) {
				$where = " `name` LIKE  '%".$get['name']."%' ";
			}
			$field = 'id,name,concat("'.C('DOWNLOAD_IMG_PREFIX_HOST').'",path) as download,width,height';
			if (!empty($get['page'])&&$get['page_size']) {
				$get['page_size'] = (int)$get['page_size'];
				$get['page'] = (int)$get['page'];
				$get['page'] = $get['page']*$get['page_size']-$get['page_size'];
				$res['extra'] = $this->field($field)->where($where)->limit($get['page'],$get['page_size'])->select();
			}else{
				$res['extra'] = $this->field($field)->where($where)->select();
			}
			$res['count'] =  $this->where($where)->count();
			if (empty($res['extra'])) {
				$res['count'] = 0;
				$res['extra'] = [];
			}
			return $res;
		}
		public function getOneForGroupId($groupId)
		{
			return $this->where("group_id = %d",array($groupId))->find();
		}
	}