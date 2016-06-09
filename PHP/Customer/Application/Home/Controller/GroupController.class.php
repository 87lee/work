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
        //$_POST = ['title' => '管理员10', 'id' => 2,'rules'=>[1,2,3]];
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
     * 根据登录用户所属组获取组下拉框用于分配
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
        //$_POST = array('pid' => 0, 'name' => 'Index/index222', 'title' => '首页', 'status' => '1', 'sort' => '255', 'id' => 13);
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


    /**
     * 权限节点添加
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月25日
     */
    public function adminAuthAdd()
    {
        if (IS_POST) {
            //提交添加
            $res = M('AuthRule')->create(I('post.'));
            $res['add_time'] = time();
            json_echo('200', '成功', M('AuthRule')->add($res));
        } else {
            //添加操作
            $authRule = M('AuthRule');
            $res = $authRule->where(['status' => 1])->select();
            /* $this->assign('admin_rule', $this->getRuleTreeData($res, '|-- '));
             $this->display(); */
            json_echo('200', '成功', $this->getRuleTreeData($res, '|-- '));
        }
    }

    /**
     * 获取数据节点
     * 
     * 
     * @param unknown $rules
     * @param string $htmlDivce
     * @param number $pid
     * @param number $level
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月25日
     */
    public function getRuleTreeData($rules, $htmlDivce = '— ', $pid = 0, $level = 0, $leftpin = 0)
    {
        $arr = [];
        foreach ($rules as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['lvl'] = $level + 1;
                $v['leftpin'] = $leftpin + 0; //左边距
                $v['lefthtml'] = str_repeat($htmlDivce, $level);
                $arr[] = $v;
                $arr = array_merge($arr, $this->getRuleTreeData($rules, $htmlDivce, $v['id'], $level + 1, $leftpin + 20));
            }
        }
        return $arr;
    }
}
