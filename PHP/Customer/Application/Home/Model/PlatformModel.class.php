<?php

namespace Home\Model;

/**
 * 平台模型
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class PlatformModel extends \Think\Model
{
    //验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
    protected $_validate = array(array('platform', 'require', '平台名不能为空'), array('platform', '', '该平台名已经存在！', 0, 'unique', 1));
}