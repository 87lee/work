<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 数据修复、每个方法一般只执行一次
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年6月1日
 * @version   1.0
 */
class FixController extends Controller
{

    public function fixUserAuthData()
    {
        if (S('Fix-fixUserAuthData')) {
            echo '数据已经初始化';
            return;
        }
        
        $user = M('User')->select();
        $groups = C('SPECIAL_GROUP');
        
        $initGroupAccess = [];
        foreach ($user as $v) {
            if ($v['user'] == 'root') {
                //超级管理员
                $initGroupAccess[] = array('uid' => $v['id'], 'group_id' => $groups['ROOT']);
            } else {
                //其他用户
                // tester 、publisher 、admin
                if ($v['tester'] == 'true') {
                    $initGroupAccess[] = array('uid' => $v['id'], 'group_id' => $groups['TESTER']);
                }
                if ($v['publisher'] == 'true') {
                    $initGroupAccess[] = array('uid' => $v['id'], 'group_id' => $groups['PUBLISHER']);
                }
                if ($v['admin'] == 'true') {
                    $initGroupAccess[] = array('uid' => $v['id'], 'group_id' => $groups['ADMIN']);
                }
            }
        }
        if (false !== M('auth_group_access')->addAll($initGroupAccess)) {
            S('Fix-fixUserAuthData', true, 24 * 3600);
            echo '初始化权限数据成功';
        } else {
            echo '失败';
        }
        exit();
    }
}
