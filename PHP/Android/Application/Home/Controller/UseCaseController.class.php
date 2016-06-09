<?php

namespace Home\Controller;

/**
 * 测试用例控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月25日
 * @version   1.0
 */
class UseCaseController extends HomeBaseController
{

    /**
     * 新增项目
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function addProject()
    {
        //$_POST = array('app_id' => 16, 'remark' => '我是备注');
        $this->isPost();
        try {
            $projectId = D('Project', 'Logic')->addProject(I('post.'));
            if ($projectId > 0) {
                json_echo(C('SUCCESS'), '新增项目成功', array('id' => $projectId));
            } else {
                json_echo(C('UNKNOWN_ERROR'), '新增项目失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 删除项目
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function delProject()
    {
        //$_POST = array('p_ids' => [2,99]);
        $this->isPost();
        try {
            $res = D('Project', 'Logic')->delProjectByIds(I('post.p_ids'));
            if ($res !== false) {
                json_echo(C('SUCCESS'), '项目删除成功');
            } else {
                json_echo(C('UNKNOWN_ERROR'), '项目删除失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 修改项目
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function editProject()
    {
        //$_POST = array('p_id' => 2, 'remark' => '我是测试修改项目。。。。。。。。。');
        $this->isPost();
        try {
            $res = D('Project', 'Logic')->editProject(I('post.'));
            if ($res !== false) {
                json_echo(C('SUCCESS'), '项目修改成功');
            } else {
                json_echo(C('UNKNOWN_ERROR'), '项目修改失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取项目列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function getProject()
    {
        try {
            json_echo(C('SUCCESS'), '获取项目列表成功', D('Project', 'Logic')->getProject(I('get.')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取项目下拉框数据
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function getProjectSelect()
    {
        try {
            json_echo(C('SUCCESS'), '获取项目列表成功', D('Project', 'Logic')->getProjectSelect());
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 新增用例
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function addUseCase()
    {
        /* $_POST = array(
         'project_id' => '3',
         'number' => 'test-record-03',
         'pre_condition' => '前置条件',
         'module' => '功能模块',
         'steps' => '操作步骤',
         'expect_result' => '登录成功',
         'remark'=>'这是备注'); */
        $this->isPost();
        try {
            $useCaseId = D('UseCase', 'Logic')->addUseCase(I('post.'));
            if ($useCaseId > 0) {
                json_echo(C('SUCCESS'), '新增用例成功', array('id' => $useCaseId));
            } else {
                json_echo(C('UNKNOWN_ERROR'), '新增用例失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 删除用例
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function delUseCase()
    {
        //$_POST=array('ids'=>[14,15]);
        $this->isPost();
        try {
            $res = D('UseCase', 'Logic')->delUseCaseByIds(I('post.ids'));
            if (false !== $res) {
                json_echo(C('SUCCESS'), '删除用例成功');
            } else {
                json_echo(C('UNKNOWN_ERROR'), '删除用例失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 修改用例
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function editUseCase()
    {
        /* $_POST=array(
         'id'=>15,
         'pre_condition'=>'前置条件update2',
         'module' => '功能模块2', 
         'steps' => '操作步骤不能为空update2', 
         'expect_result' => '预期结果不能为空update2'
         ); */
        $this->isPost();
        try {
            $res = D('UseCase', 'Logic')->editUseCase(I('post.'));
            if (false !== $res) {
                json_echo(C('SUCCESS'), '修改用例成功');
            } else {
                json_echo(C('UNKNOWN_ERROR'), '修改用例失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 执行用例
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function execUseCase()
    {
        /* $_POST=array(
         'id'=>15,
         'real_result'=>'实际结果',
         'status' => '1',
         ); */
        $this->isPost();
        try {
            $res = D('UseCase', 'Logic')->execUseCase(I('post.'));
            if (false !== $res) {
                json_echo(C('SUCCESS'), '修改用例成功');
            } else {
                json_echo(C('UNKNOWN_ERROR'), '修改用例失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取测试用例列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function getUseCase()
    {
        try {
            $res = D('UseCase', 'Logic')->getUseCase(I('get.'));
            if (false !== $res) {
                json_echo(C('SUCCESS'), '获取用例成功', $res);
            } else {
                json_echo(C('UNKNOWN_ERROR'), '获取用例失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 用例管理首页统计
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月27日
     */
    public function manageIndex()
    {
        try {
            $res = D('UseCase', 'Logic')->manageIndex();
            if (false !== $res) {
                json_echo(C('SUCCESS'), '用例管理首页统计成功', $res);
            } else {
                json_echo(C('UNKNOWN_ERROR'), '用例管理首页统计失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 执行用例首页统计
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月27日
     */
    public function execIndex()
    {
        try {
            $res = D('UseCase', 'Logic')->execIndex();
            if (false !== $res) {
                json_echo(C('SUCCESS'), '执行用例首页统计成功', $res);
            } else {
                json_echo(C('UNKNOWN_ERROR'), '执行用例首页统计失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 重置用例实际结果为空、测试用例状态为未测试
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月13日
     */
    public function resetUseCase()
    {
        /* $_POST=array(
         'ids'=>[63,64],
         'project_id'=>10
         ); */
        $this->isPost();
        try {
            json_echo(C('SUCCESS'), '重置成功', D('UseCase', 'Logic')->resetUseCase(I('post.ids'), I('post.project_id')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
}