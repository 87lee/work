<?php

namespace Home\Model;

/**
 * Vendorid模型
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class VendoridModel extends \Think\Model
{

    protected $_validate = array(array('vendor_id', 'require', 'vendor_id不能为空'), array('vendor_id', '', '该vendor_id已经存在！', 0, 'unique', 1));
}