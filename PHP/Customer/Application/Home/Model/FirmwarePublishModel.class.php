<?php

namespace Home\Model;

/**
 * 发布固件模型类
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class FirmwarePublishModel extends \Think\Model
{

    /**
     * 字段映射
     * @var unknown
     */
    protected $_map = array(
        'VendorId' => 'vendor_id', 
        'PlatForm' => 'platform', 
        'FirmwareVer' => 'firmware_ver', 
        'VersionDesc' => 'version_desc', 
        'Md5' => 'md5', 
        'Customer' => 'customer', 
        'Path' => 'path', 
        'Passwd' => 'passwd', 
        'Publisher' => 'publisher', 
        'Brand' => 'brand_id');

    /**
     * 通过id获取数据
     * 
     * 
     * @param mix $ids
     * @return array
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function getByIds($ids)
    {
        if (is_array($ids)) {
            return $this->where(array('id' => array('in', $ids)))->select();
        }
        return $this->find($ids);
    }
}