<?php

namespace Home\Controller;

/**
 * 用户组控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月28日
 * @version   1.0
 */
class GroupController extends HomeBaseController
{

    /**
     * 用户组添加
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月25日
     */
    public function addAuthGroup()
    {
        //$_POST = ['title' => '管理员zzz'];
        $this->isPost();
        try {
            $res = D('AuthGroup', 'Logic')->addAuthGroup(I('post.'));
            if ($res > 0) {
                json_echo(C('SUCCESS'), '用户组添加成功', array('id' => $res));
            }
            json_echo(C('SUCCESS'), '用户组添加失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 用户组编辑
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月25日
     */
    public function editAuthGroup()
    {
        //$_POST = ['title' => '管理员10', 'id' => 2,'rules'=>[]];
        $this->isPost();
        try {
            $res = D('AuthGroup', 'Logic')->editAuthGroup(I('post.'));
            if ($res !== false) {
                json_echo(C('SUCCESS'), '用户组修改成功');
            }
            json_echo(C('SUCCESS'), '用户组修改失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 用户组删除
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月25日
     */
    public function delAuthGroup()
    {
        //$_POST = ['ids' => [10, 11]];
        $this->isPost();
        try {
            $res = D('AuthGroup', 'Logic')->delAuthGroup(I('post.'));
            if ($res !== false) {
                json_echo(C('SUCCESS'), '用户组删除成功');
            }
            json_echo(C('SUCCESS'), '用户组删除失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 用户组列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月25日
     */
    public function authGroupList()
    {
        try {
            json_echo(C('SUCCESS'), '获取成功', D('AuthGroup', 'Logic')->authGroupList(I('get.')));
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), '获取失败');
        }
    }
    
    /**
     * 新增用户获取可分配角色列表
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    public function groupOption(){
        try {
            json_echo(C('SUCCESS'), '获取成功', D('AuthGroup', 'Logic')->groupOption());
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), '获取失败');
        }
    }

    /**
     * 添加权限规则
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function addAuthRule()
    {
        //$_POST = array('pid' => 0, 'name' => 'Index/index', 'title' => '首页', 'status' => '1', 'sort' => '255');
        $this->isPost();
        try {
            $res = D('AuthRule', 'Logic')->addAuthRule(I('post.'));
            if ($res > 0) {
                json_echo(C('SUCCESS'), '权限规则添加成功', array('id' => $res));
            }
            json_echo(C('SUCCESS'), '权限规则添加失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 编辑权限规则
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function editAuthRule()
    {
        //$_POST = array('pid' => 0, 'name' => 'Index/index222', 'title' => '首页', 'status' => '1', 'sort' => '255', 'id' => 1);
        $this->isPost();
        try {
            $res = D('AuthRule', 'Logic')->editAuthRule(I('post.'));
            if ($res !== false) {
                json_echo(C('SUCCESS'), '权限规则修改成功');
            }
            json_echo(C('SUCCESS'), '权限规则修改失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 删除权限规则
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function delAuthRule()
    {
        //$_POST = ['ids' => [13, 19]];
        $this->isPost();
        try {
            $res = D('AuthRule', 'Logic')->delAuthRule(I('post.'));
            if ($res !== false) {
                json_echo(C('SUCCESS'), '权限规则删除成功');
            }
            json_echo(C('SUCCESS'), '权限规则删除失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取权限规则
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function authRuleList()
    {
        try {
            json_echo(C('SUCCESS'), '获取成功', D('AuthRule', 'Logic')->authRuleList(I('get.')));
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), '获取失败');
        }
    }

    /**
     * 返回树状数据结构
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月29日
     */
    public function authRuleTree()
    {
        try {
            json_echo(C('SUCCESS'), '获取成功', D('AuthRule', 'Logic')->authRuleTree());
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), '获取失败');
        }
    }
}
