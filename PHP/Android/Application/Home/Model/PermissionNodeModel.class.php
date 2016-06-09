<?php
	namespace Home\Model;
	class PermissionNodeModel extends \Think\Model
	{
		protected $tableName = 'permission_node';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'nodeName' =>'node_name',
			'parentId'  =>'parent_id',
		);
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/

		public function addPermission($put)
		{
			if (empty($put['node']) || empty($put['nodeName'])) {
				result('param');
			}
			if (!empty($put['parentId'])) {

				if (!$this->getOneForId($put['parentId'])) {
					result('此权限父节点不存在');
				}
				if ($this->getOneForParentIdNode($put['parentId'],$put['node'])) {
					result('此权限已存在');
				}
			}else{
				if ($this->getOneForNodeAndParentIdZ($put['node'])) {
					result('此权限已存在');
				}
			}
			$options = array(
				'node'=>$put['node'],
				'node_name'=>$put['nodeName'],
				'parent_id'=>isset($put['parentId'])?intval($put['parentId']):0,
				"note"=>isset($put['note'])?$put['note']:''
			);
			return $this->add($options);
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getOneForNodeAndParentIdZ($node)
		{
			return $this->where("node = '%s' and parent_id = 0",array($node))->find();
		}
		public function getOneForParentIdNode($parentId,$node)
		{
			return $this->where("parent_id = %d and  node = '%s' ",array($parent_id,$node))->find();
		}
		public function getOneForNode($node)
		{
			return $this->where("node = '%s'",array($node))->find();
		}
		public function deletePermission($get)
		{

		}
		public function permissionLists($get)
		{
			if (!empty($get['parentId'])) {
				$where = 'parent_id = ' . intval($get['parentId']);
			}else{
				$where = 'parent_id = 0';
			}
			if( !empty($get['name'])){
				$where .= "  AND  (node like '%" .$get['name']."%' or  (node_name like '%" .$get['name']."%' )";
			}
			if (!empty($get['page'])&&!empty($get['pageSize'])) {
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$res['extra'] = $this->limit( $get['page'],$get['pageSize'] )->where($where)->select();
			}else{
				$res['extra'] = $this->where($where)->select();
			}
			$res['count'] = $this->where($where)->count();

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';
			}
			return $res;
		}
	}