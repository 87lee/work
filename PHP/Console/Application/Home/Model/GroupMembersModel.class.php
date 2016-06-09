<?php
namespace Home\Model;
use Think\Model;
class GroupMembersModel extends Model
{
	protected $tableName = 'group_members';
    		/**
 		* 组成员
	     	* @param [type] $group_id [组ID]
	     	* @param [type] $sn       [组设备号]
	     	* @param [type] $desc     [设备号描述]
	     	*/
    	public function addGroupMember($put)
    	{
    		if (empty($put['group_id'])) {
    			result('param');
    		}
    		if(!D('group')->getValOneForId($put['group_id'])){
  			result('组不存在');
		}
      		if (!empty($_FILES['mac']) ) {
			$fileStr = file_get_contents($_FILES['mac']['tmp_name']);
			$arr = explode("\n", $fileStr);
			$time = time();
			foreach ($arr as $key => $value) {
				if (!empty($value)) {
					$mac = strtolower(trim($value));
					$macArr[] = $mac;
				}
			}
			if (!empty($macArr)) {
				$macSql = "'".implode("','", $macArr)."'";
				$res = $this->getArrForMacSqlGroupId($macSql,$put['group_id']);

				if (!empty($res)) {
					foreach ($res as $key => $value) {
						$selectMac[] = $value['sn'];;
					}
				}
				if (!empty($selectMac)) {
					$macArr = array_diff($macArr,$selectMac);
				}
				foreach ($macArr as $value) {
					$options[] = array(
						'sn'=>$value,
						'desc'=>'',
						'group_id'=>$put['group_id'],
					);
				}
				if (!empty($options)) {
					// var_dump($options);die;
					$this->addAll($options);
				}

			}
		}elseif (!empty($put['sn'])) {

	        		$put['sn'] = strtolower(trim($put['sn']));

	        		if ($this->getOneForSnGroupId($put['sn'],$put['group_id'])) {
	            			result('成员已存在');
	        		}

			$options = array(
					'group_id'=>$put['group_id'],
					'sn'=>$put['sn'],
					'desc'=>empty($put['desc'])?'':$put['desc']
			);
			$this->add($options);
	      	}else{
	            		result('param');
	      	}
	      	return ;
    	}
    	/**
     	* 删除成员
     	* @param [string] $id [删除成员ID]
     	*/
    	public function delGroupMember($put)
    	{

		if (!empty($put)&&is_array($put)) {

			foreach ($put as  $value) {
				$deleteIdArr[] = intval($value);
			}
			$deleteIdArr = array_unique($deleteIdArr);
			$idSql = implode(',', $deleteIdArr);

			if ($this->getOneForIdSql($idSql)) {
				return $this->where("id IN (".$idSql.")")->delete();
			}

		}else{
			result('param');
		}
    	}
    	public function getOneForIdSql($idSql)
    	{
    		return $this->where('id IN (%s)',array($idSql))->find();
    	}

    	public function memberLists($group_id)
    	{
    		$response = array();
      		if ($group_id) {
        			$response = $this->field("`id` AS member_id,sn,desc")->where("`group_id`=".$group_id)->select();
        			if (!$response) {
          				$response = array();
        			}

      		}
      		return $response;
    	}
    	public function getValArrForGroupId($groupId)
    	{
    		return $this->where("`group_id`=%d",array($groupId))->select();
    	}
    	public function getOneForSnGroupId($sn,$groupId)
    	{
    		return $this->where("sn = '%s' and `group_id`=%d",array($sn,$groupId))->find();
    	}
    	public function getArrForMacSqlGroupId($macSql,$groupId)
    	{
    		return $this->where("sn IN (".$macSql.") and `group_id`=%d",array($groupId))->select();
    	}
}
?>