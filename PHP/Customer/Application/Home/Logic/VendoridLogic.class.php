<?php

namespace Home\Logic;

use Think\Model;

/**
 * VendorID逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class VendoridLogic extends Model
{

    /**
     * VendorID录入
     * 
     * 
     * @param array $data
     * @return array 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function inputVendorid(array $data)
    {
        if (! key_exists('vendor_id', $data)) {
            return array('code' => C('BAD_REQUEST'), 'msg' => '参数有误', 'retval' => array());
        }
        
        $vendoridMod = D('Vendorid');
        $vendoridMod->create($data);
        $validError = $vendoridMod->getError();
        if ($validError) {
            return array('code' => C('BAD_REQUEST'), 'msg' => $validError, 'retval' => array());
        } else {
            $result = $vendoridMod->add();
            if ($result !== false) {
                return array('code' => C('SUCCESS'), 'msg' => 'VendorID录入成功', 'retval' => array('id' => $result));
            }
            return array('code' => C('SUCCESS'), 'msg' => 'VendorID录入失败', 'retval' => array());
        }
    }

    /**
     * 根据id删除VendorID记录
     * 
     * 
     * @param array $ids
     * @return array
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function deleteByIds(array $ids)
    {
        $ids = array_map('intval', $ids);
        if (empty($ids)) {
            return array('code' => C('BAD_REQUEST'), 'msg' => '参数有误', 'retval' => array());
        }
        
        $result = D('Vendorid')->where(array('id' => array('in', $ids)))->delete();
        return array('code' => C('SUCCESS'), 'msg' => $result !== false ? '删除成功' : '删除失败', 'retval' => array());
    }

    /**
     * 获取vendorid
     * 
     * 
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月17日
     */
    public function getVendorid($param)
    {
        $isPage = isset($param['page']) && isset($param['pageSize']);
        $page = (int) $param['page'];
        $pageSize = (int) $param['pageSize'];
        list ($search, $sort) = $this->getSortRulesAndSearch($param);
        
        $count = D('Vendorid')->where($search)->count();
        if ($isPage) {
            $list = D('Vendorid')->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = D('Vendorid')->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取搜索条件和排序规则
     * 
     * 
     * @param array $data
     * @return array 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月21日
     */
    public function getSortRulesAndSearch(array $data)
    {
        $where = array();
        $sort = 'id DESC';
        if (! empty($data)) {
            $map = array('id' => 'id', 'venid' => 'vendor_id');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (key_exists($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = $map[$sortArr[0]] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['vendor_id|note'] = array('like', '%' . $data['name'] . '%');
        }
        return array($where, $sort);
    }

    /**
     * 修改vendorid
     * 
     * 
     * @param unknown $id
     * @param unknown $remark
     * @throws \LogicException
     * @return Ambigous <boolean, unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editVendorid($id, $remark)
    {
        if ($id <= 0) {
            throw new \LogicException('请选择vendorid', C('BAD_REQUEST'));
        } else {
            if (! is_null($remark)) {
                return M('Vendorid')->where(array('id' => $id))->setField('note', $remark);
            } else {
                throw new \LogicException('参数有误', C('BAD_REQUEST'));
            }
        }
    }
}