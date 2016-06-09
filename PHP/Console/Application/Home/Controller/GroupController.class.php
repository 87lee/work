<?php
	namespace Home\Controller;
	use Think\Controller;

	class GroupController extends Controller {
		public function __construct()
	    	{
		        	parent::__construct();
		       	isLogin();

	    	}
		public function index()
		{

		}
		/**
	         	* 访问不存在的地址
	         	* @param  [type] $name [description]
	         	* @return [type]       [description]
	         	*/
	    	public function _empty($name){        //把所有城市的操作解析到city方法
	    		echo '{"result":"fail","reason":"当前地址不存在"}';
	    	}
		/**
		 * 内测组管理_新增组名
		 *	POST  /group/Addname
		 *	DATA:  {"group_name":"xx"}
		 */
		public function Addname()
		{
			$get = I('get.');
			$group_name = I('put.group_name');
			$group = D('Group');
			$group->addGroup($group_name);
		}
		/**
		 * 内测组管理_删除组
		 * GET /group/deleteName?group_id=1
		 * @return [type] [description]
		 */
		public function deleteName()
		{
			$group_id = I('get.group_id');
			D('Group')->delGroup($group_id);
		}
		/**
		 * 内测组管理_修改组名
		 * POST  /group/modifyName
		 * DATA:  {"group_name":"xx", "group_id":2}
		 * @return [type] [description]
		 */
		public function modifyName()
		{
			$group_id = I('put.group_id');
			$group_name = I('put.group_name');
			D('Group')->editName($group_id,$group_name);
		}
		/**
		 * 内测组管理_查询组列表
		 * GET /group/nameLists
		 * RETURN DATA:   {"result":"ok", "groups":[{"group_id":1, "group_name":"xxx"}]}
		 * @return [type] [description]
		 */
		public function nameLists()
		{
			$res['groups'] = D('Group')->nameLists();
			result(true,$res);
		}
		/**
		 * 内测组管理_添加组成员
		 *
		 * POST  /group/addMember
		 * DATA:  {"group_id": 1, "sn":"324342", "desc":"xxx"}
		 */
		public function addMember()
		{
			$put = I('put.');
			if (empty($put['group_id'])) {
				$put['group_id'] = I('post.group_id');
			}
			D('GroupMembers')->addGroupMember($put);
			result();
		}
		/**
		 * 内测组管理_删除组成员
		 *
		 * POST  /group/deleteMember
		 * ["1","2"]
		 *
		 *
		 *
		 */
		public function deleteMember()
		{
			$put = I('put.');

			D('GroupMembers')->delGroupMember($put);
			result();
		}

		/**
		 * 内测组管理_查询组成员列表
		 * GET /group/memberLists?group_id=1
		 * RETURN DATA:   {"result":"ok", "members":[{"member_id":1, "sn":"xxx", "desc":"xxxx"}]}
		 * @return [type] [description]
		 */
		public function memberLists()
		{
			$group_id = I('get.group_id');
			$data = D('Group')->nameLists($group_id);
			$data['members'] = D('GroupMembers')->memberLists($group_id);
			result(true,$data);
		}
	}
 ?>