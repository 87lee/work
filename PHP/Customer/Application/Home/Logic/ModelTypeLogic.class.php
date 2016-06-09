<?php

namespace Home\Logic;

use Think\Model;

/**
 * 型号逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月23日
 * @version   1.0
 */
class ModelTypeLogic extends Model
{

    /**
     * 添加型号
     * 
     * 
     * @param unknown $data
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function addModelType($data)
    {
        if (empty($data['type_name'])) {
            throw new \LogicException('请输入型号', C('BAD_REQUEST'));
        } else {
            //检查板型是否存在
            $modelTypeMod = M('ModelType');
            if ($modelTypeMod->where(array('type_name' => $data['type_name']))->find()) {
                throw new \LogicException('型号已存在', C('BAD_REQUEST'));
            } else {
                $addData = array(
                    'type_name' => $data['type_name'], 
                    'remark' => $data['remark'], 
                    'admin_user' => session('customerIsLogin.user'), 
                    'add_time' => time());
                return $modelTypeMod->add($addData);
            }
        }
    }

    /**
     * 根据id删除型号
     * 
     * 
     * @param unknown $ids
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function delModelTypeByIds($ids)
    {
        if (empty($ids) || ! is_array($ids)) {
            throw new \LogicException('请选择型号', C('BAD_REQUEST'));
        } else {
            return M('ModelType')->where(array('id' => array('IN', $ids)))->delete();
        }
    }

    /**
     * 修改型号备注
     * 
     * 
     * @param unknown $id
     * @param unknown $remark
     * @throws \LogicException
     * @return Ambigous <boolean, unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editModelType($id, $remark)
    {
        if ($id <= 0 || ! (M('ModelType')->find($id))) {
            throw new \LogicException('请选择板型', C('BAD_REQUEST'));
        } else {
            if (! is_null($remark)) {
                return M('ModelType')->where(array('id' => $id))->setField('remark', $remark);
            } else {
                throw new \LogicException('参数有误', C('BAD_REQUEST'));
            }
        }
    }

    /**
     * 获取型号列表
     * 
     * 
     * @param unknown $data
     * @return multitype:number unknown 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function modelTypeList($data, $field = true)
    {
        $isPage = isset($data['page']) && isset($data['pageSize']);
        list ($search, $sort) = $this->getSortRulesAndSearch($data);
        
        $plateMod = M('ModelType');
        $count = $plateMod->where($search)->count();
        if ($isPage) {
            $page = (int) $data['page'];
            $pageSize = (int) $data['pageSize'];
            $list = $plateMod->where($search)
                ->field($field)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $plateMod->where($search)
                ->field($field)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取分页条件和检索条件
     * 
     * 
     * @param unknown $data
     * @return array  
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    protected function getSortRulesAndSearch($data)
    {
        $where = array();
        $sort = 'id DESC';
        if (! empty($data)) {
            $map = array('id', 'type_name');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['type_name'] = array('like', '%' . $data['name'] . '%');
        }
        return array($where, $sort);
    }

    /**
     * 获取型号下拉框
     * 
     * 
     * @return array
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function modelTypeOption($name)
    {
        $data = ! empty($name) ? array('name' => $name) : array();
        $list = $this->modelTypeList($data, 'id,type_name');
        return $list['list'];
    }
}