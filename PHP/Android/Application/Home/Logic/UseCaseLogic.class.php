<?php

namespace Home\Logic;

use Think\Model;

/**
 * 用例逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月25日
 * @version   1.0
 */
class UseCaseLogic extends Model
{

    /**
     * 创建用例
     * 
     * 
     * @param unknown $data
     * @throws \LogicException
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function addUseCase($data)
    {
        if (! empty($data)) {
            $rules = array(
                array('project_id', 'require', '请选择项目', 1), 
                array('number', 'require', '请填写用例编号', 1), 
                /* array('pre_condition', 'require', '请填写用例前置条件', 1), 
                array('module', 'require', '请填写用例功能模块', 1),  */
                array('steps', 'require', '请填写用例操作步骤', 1), 
                array('expect_result', 'require', '请填写用例预期结果', 1));
            $data['last_update_user'] = session('androidIsLogin.user');
            $data['add_user'] = session('androidIsLogin.user');
            $data['last_update_time'] = time();
            $data['add_time'] = time();
            if (false === $this->validate($rules)->create($data)) {
                throw new \LogicException($this->getError(), C('UNKNOWN_ERROR'));
            } else {
                //判断用例编号唯一性
                if ($this->where(array('number' => $data['number'], 'is_del' => 0))->find()) {
                    throw new \LogicException('用例编号已经存在', C('BAD_REQUEST'));
                }
                $id = $this->add();
                if ($id > 0) {
                    //记录下修改日志
                    $useCaseLogMod = D('UseCaseLog');
                    D('UseCaseLog', 'Logic')->recordLog($id, $data, $useCaseLogMod::IS_ADD);
                }
                return $id;
            }
        } else {
            throw new \LogicException('请求参数有误', C('FORBIDDEN'));
        }
    }

    /**
     * 根据用例id删除用例
     * 
     * 
     * @param array $ids
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function delUseCaseByIds($ids)
    {
        if (empty($ids) || ! is_array($ids)) {
            throw new \LogicException('请求参数有误', C('BAD_REQUEST'));
        } else {
            //记录下修改日志
            $useCaseLogMod = D('UseCaseLog');
            D('UseCaseLog', 'Logic')->recordLog($ids, array(), $useCaseLogMod::IS_DELETE);
            return $this->where(array('id' => array('IN', $ids)))->setField('is_del', 1);
        }
    }

    /**
     * 修改用例
     * 
     * 
     * @param array $data
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function editUseCase($data)
    {
        if (empty($data['id']) || $data['id'] <= 0) {
            throw new \LogicException('请求参数有误', C('BAD_REQUEST'));
        } else {
            $id = (int) $data['id'];
            
            //只允许修改 用例编号、前置条件、功能模块、操作步骤、预期结果,备注
            //但前置条件和功能模块可以为空
            $editData = array();
            $map = array(
                'number' => array('require', '请填写用例编号'), 
                'pre_condition' => array('false'), 
                'module' => array('false'), 
                'remark' => array('false'), 
                'steps' => array('require', '请填写用例操作步骤'), 
                'expect_result' => array('require', '请填写用例预期结果'));
            foreach ($map as $k => $v) {
                if ($v[0] == 'require' && empty($data[$k])) {
                    unset($editData);
                    throw new \LogicException($v[1], C('BAD_REQUEST'));
                } elseif (isset($data[$k])) {
                    $editData[$k] = $data[$k];
                }
            }
            
            //判断用例编号唯一性
            if (isset($data['number']) && $this->where(array('number' => $data['number'], 'is_del' => 0, 'id' => array('neq', $id)))->find()) {
                throw new \LogicException('用例编号已经存在', C('BAD_REQUEST'));
            }
            
            $editData['last_update_user'] = session('androidIsLogin.user');
            $editData['last_update_time'] = time();
            //记录下修改日志
            $useCaseLogMod = D('UseCaseLog');
            D('UseCaseLog', 'Logic')->recordLog($id, $editData, $useCaseLogMod::IS_UPDATE);
            return $this->where(array('id' => $id))->save($editData);
        }
    }

    /**
     * 执行用例
     * 
     * 
     * @param unknown $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function execUseCase($data)
    {
        if (empty($data['id']) || $data['id'] <= 0) {
            throw new \LogicException('请求参数有误', C('BAD_REQUEST'));
        } else {
            $id = (int) $data['id'];
            //只允许修改用例执行状态
            $useCaseMod = D('UseCase');
            
            $editData = array();
            
            if (! empty($data['real_result']) || ! empty($data['status'])) {
                $editData = array('last_update_user' => session('androidIsLogin.user'), 'last_update_time' => time());
                if (! empty($data['real_result'])) {
                    $editData['real_result'] = $data['real_result'];
                }
                
                if (! empty($data['status'])) {
                    if (! in_array($data['status'], array($useCaseMod::HAS_PASS, $useCaseMod::NOT_PASS, $useCaseMod::NOT_SUITABLE))) {
                        throw new \LogicException('用例执行状态有误', C('BAD_REQUEST'));
                    } else {
                        $editData['status'] = $data['status'];
                    }
                }
                
                //记录下修改日志
                $useCaseLogMod = D('UseCaseLog');
                D('UseCaseLog', 'Logic')->recordLog($id, $editData, $useCaseLogMod::IS_UPDATE);
                return $useCaseMod->where(array('id' => $id))->save($editData);
            } else {
                throw new \LogicException('请求参数有误', C('BAD_REQUEST'));
            }
        }
    }

    /**
     * 获取用例列表
     * 
     * 
     * @param array $data
     * @return array
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function getUseCase($data)
    {
        $isPage = isset($data['page']) && isset($data['pageSize']);
        list ($search, $sort) = $this->getSortRulesAndSearch($data);
        
        $useCaseMod = M('UseCase');
        if ($isPage) {
            $page = (int) $data['page'];
            $pageSize = (int) $data['pageSize'];
            $count = $useCaseMod->alias('uc')
                ->where($search)
                ->count();
            $list = $useCaseMod->alias('uc')
                ->join('LEFT JOIN tb_user u ON uc.last_update_user=u.user')
                ->field('uc.*,u.name AS last_update_name')
                ->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $useCaseMod->alias('uc')
                ->join('LEFT JOIN tb_user u ON uc.last_update_user=u.user')
                ->field('uc.*,u.name AS last_update_name')
                ->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取排序规则和检索条件
     * 
     * 
     * @param array $data
     * @return array 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    protected function getSortRulesAndSearch($data)
    {
        $where = array('uc.is_del' => 0);
        $sort = 'uc.number ASC';
        if (! empty($data)) {
            //用例编号、用例执行状态、最后修改人、最后修改时间
            $map = array('number', 'status', 'last_update_user', 'last_update_time');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = 'uc.' . $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['uc.number|u.name'] = array('like', '%' . $data['name'] . '%');
            ! empty($data['project_id']) && $where['uc.project_id'] = (int) $data['project_id'];
            isset($data['status']) && $where['uc.status'] = (int) $data['status'];
        }
        return array($where, $sort);
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
        $projects = $this->execIndex();
        return array_values($projects);
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
        //获取所有未删除项目
        $projects = M('Project')->field('p_id AS project_id,p_name AS project_name,0 AS total,0 AS percent,0 AS exec_num')
            ->where(array('is_del' => 0))
            ->index('project_id')
            ->select();
        if (empty($projects)) {
            return array();
        } else {
            //获取项目相关用例统计
            $projectIds = array_column($projects, 'project_id');
            $useCaseList = M('UseCase')->field('project_id,status,count(id) AS total')
                ->where(array('is_del' => 0, 'project_id' => array('IN', $projectIds)))
                ->group('project_id,status')
                ->select();
            
            foreach ($useCaseList as $v) {
                $projects[$v['project_id']]['total'] += $v['total'];
                
                if (! empty($v['status']) && $v['status'] != 0) {
                    $projects[$v['project_id']]['exec_num'] += $v['total'];
                }
                //重新计算占比
                if ($projects[$v['project_id']]['total'] > 0) {
                    $projects[$v['project_id']]['percent'] = round(
                        ($projects[$v['project_id']]['exec_num'] / $projects[$v['project_id']]['total']), 2) * 100;
                }
            }
            
            return $projects;
        }
    }

    /**
     * 重置测试用例状态
     * 
     * 
     * @param unknown $ids
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月13日
     */
    public function resetUseCase($ids, $projectId)
    {
        if (empty($ids) && empty($projectId)) {
            throw new \LogicException('请求参数有误', C('BAD_REQUEST'));
        } else {
            //组合条件
            $where['is_del'] = 0;
            if (! empty($ids)) {
                $ids = array_unique($ids);
                $where['id'] = array('IN', $ids);
                $logItem = $ids;
            } else {
                $projectId > 0 && $where['project_id'] = (int) $projectId;
                $logItem = $projectId;
            }
            
            //重置
            $useCaseMod = D('UseCase');
            $resetData = array(
                'real_result' => '', 
                'status' => $useCaseMod::NOT_TEST, 
                'last_update_user' => session('androidIsLogin.user'), 
                'last_update_time' => time());
            if (false !== $this->where($where)->save($resetData)) {
                //记录下修改日志
                $useCaseLogMod = D('UseCaseLog');
                D('UseCaseLog', 'Logic')->recordLog($logItem, array(), $useCaseLogMod::IS_RESET);
                return true;
            } else {
                throw new \LogicException('重置出错', C('UNKNOWN_ERROR'));
            }
        }
    }
}
