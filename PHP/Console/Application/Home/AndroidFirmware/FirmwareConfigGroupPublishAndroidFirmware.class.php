<?php
	namespace Home\AndroidFirmware;
	class FirmwareConfigGroupPublishAndroidFirmware extends \Think\Model
	{
		protected $tableName = 'firmware_config_group_publish';
		protected $connection = 'DB_ANDROID_FIRMWARE';
		protected $_map = array(
			'vendorID' =>'vendorid',
			'groupId'  =>'group_id',
			'desktopId'  =>'desktop_id',
			'configGroupId'=>'config_group_id'
		);

		public function publishConfigGroup($put)
		{
			if (empty($put['model']) || !isset($put['vendorID']) || !isset($put['configGroupId']) || !( $put['type'] == 'group' || $put['type'] =='ALL')) {
				result('param');
			}
			if ($put['type'] == 'group') {
				if (empty($put['groupId'])) {
					result('param');
				}
				$group = D('Group')->find($put['groupId']);
				if (!$group) {
					result('组不存在，请选择正确组');
				}
			}else{
				if (isset($put['groupId'])) {
					unset($put['groupId']);
				}
			}

			$configGroup = D('FirmwareConfigGroup','AndroidFirmware')->getOneForId($put['configGroupId']);
			if (!$configGroup) {
				result('配置包不存在');
			}
			$res = $this->where("`model` = '%s' and `vendorid` = '%s' and `type`= '%s'" ,array($put['model'],$put['vendorID'],$put['type']))->find();
			if ($res) {
				$this->deletePublishConfigGroup($res['id']);
			}
			$time = time();
			$options = array(
				'model'=>$put['model'],
				'vendorID'=>$put['vendorID'],
				'configGroupId'=>$put['configGroupId'],
				'groupId'=>($put['type'] == 'group'?$put['groupId']:0),
				'type'=>$put['type'],
				'url'=>$configGroup['url'],
				'version'=>$time,
				'md5'=>$configGroup['md5'],
				'desktop_id'=>$configGroup['desktopId'],
			);
			$this->create($options);
			$id = $this->add();
			return true;
		}

		public function modifyPublishConfigGroupDesc($put)
		{
			if (empty($put['id']) || empty($put['desc'])) {

				result('param');
			}
			if (!$this->find($put['id'])) {
				result('该配置包没有发布');
			}
			$options = array(
				'desc'=>$put['desc']
			);
			$this->where("id = %d",array($put['id']))->save($options);

		}
		public function deletePublishConfigGroupMysql($id)
		{
			if ($this->find($id)) {
				$this->delete($id);
			}
			return true;
		}

		public function deletePublishConfigGroup($id)
		{
			if (!empty($id)) {
				if ($this->find($id)) {
					$this->delete($id);
				}
			}
			return true;
		}

		public function publishConfigGroupLists($id = null,$name= null ,$page=null,$pageSize=null)
		{

			if ($id ===null) {
				$firmwareConfigGroup = D('FirmwareConfigGroup','AndroidFirmware')->getConfigGroupLists();
				$firmwareConfigGroupArr = array();
				foreach ($firmwareConfigGroup as  $value) {
					$firmwareConfigGroupArr[$value['id']] =$value['name'];
				}
				$where = '';
				$order = "version desc";

				if ($name != null) {
					$where = "`model` like '%".$name."%' or `vendorid` like '%".$name."%' or desktop_id like '%".$name."%'";
				}
				if (!empty($page)&&!empty($pageSize)) {
					$page = $page*$pageSize - $pageSize;
					$res['extra'] = $this->limit($page,$pageSize)->where($where)->order($order)->select();
				}else{
					$res['extra'] = $this->where($where)->order($order)->select();
				}
				$res['count'] = $this->where($where)->count();
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}else{
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['name'] = !empty($firmwareConfigGroupArr[$value['configGroupId']])?$firmwareConfigGroupArr[$value['configGroupId']]:'';
						unset($res['extra'][$key]['configGroupId']);
					}
				}
			}else{
				$res['extra'] = $this->find($id);
				if (empty($res['extra'])) {
					$res['extra'] = array();
				}else{
					$firmwareConfigGroup = D('FirmwareConfigGroup','AndroidFirmware')->getConfigGroupLists($res['extra']['configGroupId']);

					$res['extra']['name'] = !empty($firmwareConfigGroup)?$firmwareConfigGroup['name']:'';

					unset($res['extra']['configGroupId']);
				}
			}
			return $res;
		}

	}