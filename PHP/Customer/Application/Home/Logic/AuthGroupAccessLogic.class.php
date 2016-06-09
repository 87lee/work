<?php

namespace Home\Logic;

use Think\Model;

/**
 * 用户组对应表逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月7日
 * @version   1.0
 */
class AuthGroupAccessLogic extends Model
{

    /**
     * 根据用户id获取用户权限
     * 
     * 
     * @param array $uid
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月7日
     */
    public function getAuthAccessByUids(array $uids)
    {
        if (! empty($uids)) {
            return $this->alias('aga')
                ->join('LEFT JOIN tb_auth_group ag ON aga.group_id=ag.id')
                ->field('aga.uid,ag.id AS group_id,ag.title AS group_name')
                ->where(array('aga.uid' => array('in', $uids)))
                ->index('uid')
                ->select();
        }
        return array();
    }
}