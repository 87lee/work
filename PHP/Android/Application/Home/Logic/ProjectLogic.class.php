<?php

namespace Home\Logic;

use Think\Model;

/**
 * 项目逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月25日
 * @version   1.0
 */
class ProjectLogic extends Model
{

    /**
     * 新增项目
     * 
     * 
     * @param unknown $data
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function addProject($data)
    {
        if (empty($data['app_id']) || $data['app_id'] <= 0) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        } else {
            //判断项目是否存在
            if ($this->where(array('app_id' => $data['app_id'], 'is_del' => 0))->find()) {
                throw new \LogicException('项目已存在，请更换', C('BAD_REQUEST'));
            }
            
            //根据app_id获取应用名和包名
            $appName = M('App')->field('app,name')
                ->where(array('id' => $data['app_id']))
                ->find();
            if (empty($appName)) {
                throw new \LogicException('项目对应的应用不存在', C('BAD_REQUEST'));
            } else {
                $data['p_name'] = $appName['app'];
                $data['pkg_name'] = $appName['name'];
                $data['user'] = session('androidIsLogin.user');
                $data['add_time'] = time();
                if (false === $this->create($data)) {
                    throw new \LogicException('数据库生成数据失败', C('UNKNOWN_ERROR'));
                } else {
                    return $this->add();
                }
            }
        }
    }

    /**
     * 修改项目
     * 
     * 
     * @param unknown $data
     * @throws \LogicException
     * @return boolean|Ambigous <boolean, unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function editProject($data)
    {
        if (empty($data['p_id']) || $data['p_id'] <= 0) {
            throw new \LogicException('请选择一个项目', C('BAD_REQUEST'));
        } else {
            if (empty($data['remark'])) {
                return true;
            } else {
                return $this->where(array('p_id' => (int) $data['p_id']))->save(array('remark' => $data['remark']));
            }
        }
    }

    /**
     * 根据项目id删除项目
     * 
     * 
     * @param array $ids
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function delProjectByIds($ids)
    {
        if (empty($ids) || ! is_array($ids)) {
            throw new \LogicException('请选择一个项目', C('BAD_REQUEST'));
        } else {
            //判断项目下是否有关联用例
            $useCase = $this->alias('p')
                ->join('INNER JOIN tb_use_case uc ON p.p_id = uc.project_id')
                ->where(array('p.is_del' => 0, 'uc.is_del' => 0, 'p.p_id' => array('IN', $ids)))
                ->find();
            if (! empty($useCase)) {
                throw new \LogicException('请先删除项目下的测试用例', C('FORBIDDEN'));
            } else {
                return $this->where(array('p_id' => array('IN', $ids)))->setField('is_del', 1);
            }
        }
    }

    /**
     * 获取项目列表
     * 
     * 
     * @param unknown $data
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function getProject($data)
    {
        $isPage = isset($data['page']) && isset($data['pageSize']);
        list ($search, $sort) = $this->getSortRulesAndSearch($data);
        
        $projectMod = M('Project');
        if ($isPage) {
            $page = (int) $data['page'];
            $pageSize = (int) $data['pageSize'];
            $count = $projectMod->alias('p')
                ->where($search)
                ->count();
            $list = $projectMod->alias('p')
                ->join('LEFT JOIN tb_user u ON p.user=u.user')
                ->field('p.*,u.name')
                ->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $projectMod->alias('p')
                ->join('LEFT JOIN tb_user u ON p.user=u.user')
                ->field('p.*,u.name')
                ->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取搜索条件和排序条件
     * 
     * 
     * @param unknown $data
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    protected function getSortRulesAndSearch($data)
    {
        $where = array('p.is_del' => 0);
        $sort = 'p.p_id DESC';
        if (! empty($data)) {
            $map = array('p_id', 'p_name');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = 'p.' . $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['p.p_name'] = array('like', '%' . $data['name'] . '%');
        }
        return array($where, $sort);
    }

    /**
     * 获取项目下拉框数据
     * 
     * 
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function getProjectSelect()
    {
        return $this->field('p_id,p_name')
            ->where(array('is_del' => 0))
            ->select();
    }
}