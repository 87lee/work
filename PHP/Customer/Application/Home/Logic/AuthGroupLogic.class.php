<?php

namespace Home\Logic;

use Think\Model;

/**
 * 用户组逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月28日
 * @version   1.0
 */
class AuthGroupLogic extends Model
{

    /**
     * 添加用户组
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function addAuthGroup(array $data)
    {
        if (empty($data['title'])) {
            throw new \LogicException('用户组名不能为空', C('BAD_REQUEST'));
        }
        $rules = array(array('title', 'require', '用户组名不能为空'), array('title', '', '该用户组名已经存在！', 0, 'unique', 1));
        $authGroupMod = M('AuthGroup');
        $data['add_time'] = time();
        if (! $authGroupMod->validate($rules)->create($data)) {
            throw new \LogicException($authGroupMod->getError(), C('BAD_REQUEST'));
        }
        return $authGroupMod->add();
    }

    /**
     * 修改用户组
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return Ambigous <boolean, unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function editAuthGroup(array $data)
    {
        if (empty($data['title']) || empty($data['id'])) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        }
        $rules = array(array('title', 'require', '用户组名不能为空'), array('title', '', '该用户组名已经存在！', 0, 'unique', 2));
        $authGroupMod = M('AuthGroup');
        isset($data['rules']) && $data['rules'] = implode(',', $data['rules']);
        if (! $authGroupMod->validate($rules)->create($data)) {
            throw new \LogicException($authGroupMod->getError(), C('BAD_REQUEST'));
        }
        return $authGroupMod->save();
    }

    /**
     * 删除用户组
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function delAuthGroup(array $data)
    {
        if (empty($data['ids']) || ! is_array($data['ids'])) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        }
        return M('AuthGroup')->where(array('id' => array('in', $data['ids'])))->delete();
    }

    /**
     * 获取组列表
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    public function authGroupList(array $data)
    {
        $isPage = ! empty($data['page']) && ! empty($data['pageSize']);
        $page = (int) $data['page'];
        $pageSize = (int) $data['pageSize'];
        list ($search, $sort) = $this->getSortRulesAndSearch($data);
        
        $authGroupMod = M('AuthGroup');
        $count = $authGroupMod->count();
        if ($isPage) {
            $list = $authGroupMod->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $authGroupMod->where($search)
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
        $where = array('status' => 1);
        $sort = 'id desc';
        
        if (! empty($data)) {
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortFields = array('id');
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $sortFields) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['title'] = array('like', '%' . $data['name'] . '%');
        }
        return array($where, $sort);
    }

    /**
     * 根据登录用户所属组获取组下拉框用于分配
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    public function groupOption()
    {
        $gid = session('customerIsLogin.group_id');
        if ($gid <= 0) {
            throw new \LogicException('非法登录', C('FORBIDDEN'));
        } else {
            $subGroupId = M('AuthGroup')->where(array('id' => $gid))->getField('sub_group');
            $subGroupId = trim($subGroupId, ',');
            if (! empty($subGroupId)) {
                return array(
                    'list' => M('AuthGroup')->field('id,title')
                        ->where('id IN (' . $subGroupId . ')')
                        ->select());
            } else {
                return array('list' => array());
            }
        }
    }
}
