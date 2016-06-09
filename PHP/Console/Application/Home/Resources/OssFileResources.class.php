<?php

	namespace Home\Resources;
	class OssFileResources extends \Think\Model
	{
		protected $tableName = 'oss_file';
		protected $connection = 'DB_RESOURCES';

		public function uploadFile($post)
		{
			if (empty($post['groupId']) || empty($post['name'])) {
				result('param');
			}
			if (!D('OssFileGroup','Resources')->getOneForId($post['groupId'])) {
				result('资源组不存在');
			}

			if ($res = $this->getOneForNameGroupId($post['name'],$post['groupId'])) {
				result('图片组文件名存在');
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
					'group_id'=>$post['groupId'],
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
		public function deleteFile($get)
		{
			if (empty($get['id'])) {
				result('param');
			}
			if (!$res = $this->find($get['id'])) {
				result('文件不存在');
			}
				//删除OSS上面的图片
				// require_once '../Base/Ossupclass/OssBase.class.php';

				// $base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);

				// $res = $base->deleteFile($res['path']);
			return $this->delete($get['id']);

		}

		public function fileLists($get)
		{

			$where = '';

			if (!empty($get['groupId'])) {
				$get['groupId'] = (int)$get['groupId'];

				$where = "  `group_id`=".$get['groupId'];

				if (!empty($get['name'])) {
					$where .= " and `name` LIKE  '%".$get['name']."%' ";
				}
			}

			$field = 'id,name,concat("'.C('DOWNLOAD_PREFIX_ADDR').'",path) as download';
			if (!empty($get['page'])&&$get['pageSize']) {

				$get['pageSize'] = (int)$get['pageSize'];
				$get['page'] = (int)$get['page'];
				$get['page'] = $get['page']*$get['pageSize']-$get['pageSize'];

				$res['extra'] = $this->field($field)->where($where)->limit($get['page'],$get['pageSize'])->select();
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