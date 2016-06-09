<?php

namespace Home\Logic;

use Think\Model;

/**
 * 权限规则逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月28日
 * @version   1.0
 */
class AuthRuleLogic extends Model
{

    /**
     * 添加规则
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function addAuthRule(array $data)
    {
        if (empty($data['name']) || empty($data['title'])) {
            throw new \LogicException('规则名或规则中文描述不能为空', C('BAD_REQUEST'));
        }
        
        if (! isset($data['pid']) || $data['pid'] < 0) {
            throw new \LogicException('请选择上级权限节点', C('BAD_REQUEST'));
        }
        
        $authRuleMod = M('AuthRule');
        
        //判断一下菜单等级
        $data['level'] = $this->getLevelByPid($data['pid']);
        if ($authRuleMod->where(array('name' => $data['name'],'pid'=>$data['pid']))->find()) {
            throw new \LogicException('该规则名已经存在，请修改', C('BAD_REQUEST'));
        }
        $data['add_time'] = time();
        if (! $authRuleMod->create($data)) {
            throw new \LogicException($authRuleMod->getError(), C('BAD_REQUEST'));
        }
        return $authRuleMod->add();
    }

    /**
     * 编辑权限规则
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function editAuthRule(array $data)
    {
        if (empty($data['id'])) {
            throw new \LogicException('请选择权限规则再进行修改', C('BAD_REQUEST'));
        }
        
        if (empty($data['name']) || empty($data['title'])) {
            throw new \LogicException('规则名或规则中文描述不能为空', C('BAD_REQUEST'));
        }
        
        if (! isset($data['pid']) || $data['pid'] < 0) {
            throw new \LogicException('请选择上级权限节点', C('BAD_REQUEST'));
        }
        $data['level'] = $this->getLevelByPid($data['pid']);
        
        $authRuleMod = M('AuthRule');
        if (! $authRuleMod->create($data)) {
            throw new \LogicException($authRuleMod->getError(), C('BAD_REQUEST'));
        }
        return $authRuleMod->save();
    }

    /**
     * 删除权限规则
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function delAuthRule(array $data)
    {
        if (empty($data['ids']) || ! is_array($data['ids'])) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        }
        return M('AuthRule')->where(array('id' => array('in', $data['ids'])))->delete();
    }

    /**
     * 获取权限规则
     * 
     * 
     * @param array $data
     * @return multitype:number unknown 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function authRuleList(array $data)
    {
        $isPage = ! empty($data['page']) && ! empty($data['pageSize']);
        $page = (int) $data['page'];
        $pageSize = (int) $data['pageSize'];
        list ($search, $sort) = $this->getSortRulesAndSearch($data);
        
        $authRuleMod = M('AuthRule');
        $count = $authRuleMod->alias('ar')
            ->join('LEFT JOIN tb_auth_rule ar2 ON ar.pid=ar2.id')
            ->where($search)
            ->count();
        if ($isPage) {
            $list = $authRuleMod->alias('ar')
                ->join('LEFT JOIN tb_auth_rule ar2 ON ar.pid=ar2.id')
                ->field('ar.*,ar2.pid AS ppid,ar2.name AS pid_name')
                ->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $authRuleMod->alias('ar')
                ->join('LEFT JOIN tb_auth_rule ar2 ON ar.pid=ar2.id')
                ->field('ar.*,ar2.pid AS ppid,ar2.name AS pid_name')
                ->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取排序规则和搜索条件
     * 
     * 
     * @param array $data
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function getSortRulesAndSearch(array $data)
    {
        $where = array();
        $sort = 'ar.id desc';
        
        if (! empty($data)) {
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortFields = array('id');
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $sortFields) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = 'ar.' . $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            if (! empty($data['name'])) {
                $where['_complex'][] = array(
                    'ar.name' => array('like', '%' . $data['name'] . '%'), 
                    'ar.title' => array('like', '%' . $data['name'] . '%'), 
                    '_logic' => 'OR');
            }
            isset($data['ar.status']) && $where['status'] = (int) $data['status'];
            if (isset($data['pid'])) {
                if (isset($data['all'])) {
                    $where['_complex'][] = array('ar.pid' => (int) $data['pid'], 'ar2.pid' => (int) $data['pid'], '_logic' => 'OR');
                } else {
                    $where['ar.pid'] = (int) $data['pid'];
                }
            }
        }
        return array($where, $sort);
    }

    /**
     * 获取树状权限规则
     * 
     * 
     * @return multitype:unknown 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月29日
     */
    public function authRuleTree()
    {
        $res = M('AuthRule')->where(['status' => 1])->select();
        return $this->getRuleTreeData($res,' |----　');
    }

    /**
     * 生成树状结构
     *
     *
     * @param unknown $rules
     * @param string $htmlDivce
     * @param number $pid
     * @param number $level
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月29日
     */
    public function getRuleTreeData($rules, $htmlDivce = '—— ', $pid = 0, $level = 0, $leftpin = 0)
    {
        /* $arr = [];
        foreach ($rules as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['level'] = $level + 1;
                $pid == 0 && $v['children'] = $this->getRuleTreeData($rules, $v['id'], $level + 1);
                $arr[] = $v;
            }
        }
        return $arr; */

        $arr = [];
        foreach ($rules as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['lvl'] = $level + 1;
                $v['leftpin'] = $leftpin + 0; //左边距
                $v['lefthtml'] = str_repeat($htmlDivce, $level);
                //$v['children'] = $this->getRuleTreeData($rules, $htmlDivce, $v['id'], $level + 1, $leftpin + 20);
                $arr[] = $v;
                $arr = array_merge($arr, $this->getRuleTreeData($rules, $htmlDivce, $v['id'], $level + 1, $leftpin + 20));
            }
        }
        return $arr;
        
    }

    /**
     * 根据上级规则id获取下一级规则
     * 
     * 
     * @param array $pids
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月11日
     */
    public function getRulesByPids(array $pids)
    {
        if (! empty($pids) && is_array($pids)) {
            $ruleLists = M('AuthRule')->where(array('status' => 1, 'pid' => array('in', $pids)))
                ->order('sort desc')
                ->select();
            if (! empty($ruleLists)) {
                $rule = array();
                foreach ($ruleLists as $k => $v) {
                    $rule[$v['pid']][] = $v;
                }
                return $rule;
            } else {
                return array();
            }
        } else {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        }
    }

    /**
     * 根据父节点获取菜单level
     * 
     * 
     * @param unknown $pid
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月13日
     */
    public function getLevelByPid($pid)
    {
        if ($pid > 0) {
            $pidLevel = M('AuthRule')->where(array('id' => $pid))->getField('level');
            if ($pidLevel > 0) {
                return $pidLevel + 1;
            } else {
                throw new \LogicException('选择的上级节点有误', C('BAD_REQUEST'));
            }
        } else {
            return 1;
        }
    }
}
