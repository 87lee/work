<?php

	namespace Home\Resources;
	class OssFileResources extends \Think\Model
	{
		protected $tableName = 'oss_file';
		protected $connection = 'DB_RESOURCES';
		public function uploadImage($post)
		{
			if (empty($post['group_id']) || empty($post['name'])) {
				result('param');
			}
			if (!D('OssImageGroup','Resources')->getOneForId($post['group_id'])) {
				result('图片组不存在');
			}

			if ($res = $this->getOneForNameGroupId($post['name'],$post['group_id'])) {
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
				if (empty($value['download']) || empty($value['oss'])) {
					result('OSS返回数据出错');
				}
				$options[] = array(
					'group_id'=>$post['group_id'],
					'name'=>$post['name'],
					'download'=>$value['download'],
					'path'=>$value['oss'],
					'md5'=>$formData[$key]['md5_file']
				);
			}

			if (!empty($options)) {
				$this->addAll($options);
			}
			return ;

		}
		public function getOneForNameGroupId($name,$groupId)
		{
			return $this->where("name='%s' and group_id = %d",array($name,$groupId))->select();
		}
		public function deleteImage($get)
		{
			if (empty($get['id'])) {
				result('param');
			}
			if (!$res = $this->find($get)) {
				result('图片不存在');
			}
				//删除OSS上面的图片
				// require_once '../Base/Ossupclass/OssBase.class.php';

				// $base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);

				// $res = $base->deleteFile($res['path']);
			return $this->delete($id);



		}

		public function imageLists($get)
		{


			$where = '';
			if (!empty($get['name'])) {
				$where = " `name` LIKE  '%".$get['name']."%' ";
			}elseif (!empty($get['group'])) {
				$get['group'] = (int)$get['group'];
				$where = "`group_id`={$get['group']}";
			}
			$field = 'id,name,concat("'.C('DOWNLOAD_IMG_PREFIX_HOST').'",path) as download';
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