<?php 
	namespace Home\Model;
	use Think\Model;
	class MonitorModel extends Model 
	{
		protected $tableName = 'monitor'; 
		protected $_validate = array(     
			array('version','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
      
                    );
                	
                    /**
                     * 新增版本
                     * @param [string] $name [增加组名]
                     */
                    public function addVersion($options)
                    {	
                        // dump($this->create($options));die();
                        if ($this->add($options)) {
                            result();
                        }else{
                            result('unknown');
                        }
                    }
                    /**
                     * 版本列表
                     * @return [type] [description]
                     */
                    public function versionLists( $version = null )
                    {
                        if ($version === null) {
                            $lists['content'] = $this->select();
                            foreach ($lists['content'] as $key => $value) {

                                $lists['content'][$key]['desc'] = json_decode($value['desc']);
                            }
                            result(true,$lists);
                        }else{
                            $lists['content'] = $this->where('version=%d',array($version))->find();
                            $lists['content']['desc'] = json_decode($lists['content']['desc']);
                            
                            result(true,$lists);
                        }
                    }
                    /**
                     * 删除版本
                     * @param  [type] $id [版本ID]
                     * @return [type]     [description]
                     */
                    public function deleteVersion($version)
                    {
                        $versionDetail = $this->where('version=%d',array($version))->find();
                        if (!$versionDetail) {
                            result('该版本不存在');
                        }
                        if ( D('MonitorPublish')->where('`version`="'.$versionDetail['id']  .'"')->find() ) {
                                result('此版本正在发布');
                        }
                        

                        require_once '../Base/Ossupclass/OssBase.class.php';

                        $base  = new \base(OSS_ACCESS_ID,OSS_ACCESS_KEY,OSS_ENDPOINT,OSS_BUCKET);
                        $res = $base->deleteFile($versionDetail['path']);
                        if ($res) {
                            $this->where('version=%d',array($version))->delete();
                            result();
                        }else{
                            result('param');
                        }

                       
                    }
                   
        
        
	}
