<?php

	namespace Home\Model;
	use Think\Model;
	class GroupModel extends Model
	{
		protected $tableName = 'group';
		protected $_validate = array(
			array('group_name','','帐号名称已经存在！',0,'unique',3), // 在新增的时候验证name字段是否唯一

		);
		/*public function __construct()
    {
          parent::__construct();
          isLogin();
    }*/
    /**
     * 新增组名
     * @param [string] $name [增加组名]
     */
    public function addGroup($name)
    {

    	if (!empty($name)) {
    		if ($this->create(array('group_name'=>$name))) {
    			   $this->add();
             result();
    		}else{
            result('组已存在');
    		}
    	}else{
    		    result('param');
    	}

    }
    /**
     * 删除组名
     * @param [string] $id [删除组的ID]
     */
    public function delGroup($id)
    {

      if (!empty($id)) {

        if ($this->find($id)) {

            if (M('group_members')->where("`group_id`=".$id)->find()) {

                result('组内有成员存在');
            }else{

              	if (D('AppUpdatePublish','App')->getOneForGroupId($id)) {
                		result('应用升级正在使用此组');
            		}
            		if (D('MoreAppPublishWhiteBlack','App')->getOneForGroupId($id)) {
                		result('应用黑白名单发布正在使用此组');
            		}
            		if (D('DesktopVersionPublish','Desktop')->getOneForGroupId($id)) {
                		result('桌面发布正在使用此组');
            		}
            		if (D('LiveListPublish','Live')->getOneForGroupId($id)) {
                		result('直播列表发布正在使用此组');
            		}
            		if (D('PublishLiveAd','Live')->getOneForGroupId($id)) {
                		result('直播广告发布正在使用此组');
            		}
            		if (D('LiveBootad','Live')->getOneForGroupId($id)) {
                		result('直播开机画面正在使用此组');
            		}
            		if (D('ModelVersionPublish','Ota')->getOneForGroupId($id)) {
                		result('OTA发布正在使用此组');
            		}
            		if (D('PublishSilent','Silent')->getOneForGroupId($id)) {
                		result('静默发布正在使用此组');
            		}

            		$this->delete($id);
            		result();
              }

        }else{
             result('组不存在');
        }
      }else{
            result('param');
      }

    }
    /**
     * 修改组名
     * @param  [int] $group_id   [组ID]
     * @param  [string] $group_name [组名字]
     * @return [type]             [description]
     */
    public function editName($group_id,$group_name)
    {
      if (!empty($group_id)&&!empty($group_name)) {
        if ($this->find($group_id)) {

          if ($this->create(array('id'=>$group_id,'group_name'=>$group_name))) {

            if ($this->save()) {
              result();
            }else{
              result('unknown');
            }
          }else{
            result('组名存在');
          }
        }else{
          result('组不存在');
        }
      }else{
        result('param');
      }

    }

    public function nameLists($id = null)
    {
        	if ($id!=null) {
          		$response = $this->field("`id` AS group_id,`group_name`")->find($id);
          		if (!$response) {
          			$response = array();
        		}
      	}else{
          		$response = $this->field("`id` AS group_id,`group_name`")->select();
        		if (!$response) {
          			$response = array();
        		}
    	}
    	return $response;
      }
      public function getValOneForId($id)
      {
      	return $this->find($id);
      }
}
?>