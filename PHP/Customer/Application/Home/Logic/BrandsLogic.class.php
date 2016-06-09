<?php

namespace Home\Logic;

use Think\Model;

/**
 * 品牌逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月26日
 * @version   1.0
 */
class BrandsLogic extends Model
{

    /**
     * 新增品牌
     * 
     * 
     * @param array $data
     * @return int
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function addBrand($data)
    {
        if (empty($data['brand_name'])) {
            throw new \LogicException('品牌名不能为空', C('BAD_REQUEST'));
        } elseif (empty($data['customer'])) {
            throw new \LogicException('请选择客户', C('BAD_REQUEST'));
        } else {
            $brandMod = M('Brands');
            //判断用户是否存在
            if (! M('User')->where(array('user' => $data['customer']))->find()) {
                throw new \LogicException('客户不存在，请重新选择', C('BAD_REQUEST'));
            }
            
            //判断客户+品牌是否唯一
            if ($brandMod->where(array('brand_name' => $data['brand_name'], 'customer' => $data['customer']))->find()) {
                throw new \LogicException('该品牌客户已经存在，请更改', C('BAD_REQUEST'));
            } else {
                $data['admin_user'] = session('customerIsLogin.user');
                $data['add_time'] = time();
                if (false === $brandMod->create($data)) {
                    throw new \LogicException('数据库创建数据失败', C('INTERNAL_SERVER_ERROR'));
                } else {
                    return $brandMod->add();
                }
            }
        }
    }

    /**
     * 删除品牌
     * 
     * 
     * @param unknown $ids
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function delBrandByIds($ids)
    {
        if (empty($ids) || ! is_array($ids)) {
            throw new \LogicException('请选择品牌', C('BAD_REQUEST'));
        } else {
            return M('Brands')->where(array('id' => array('IN', $ids)))->delete();
        }
    }

    /**
     * 修改品牌
     * 
     * 
     * @param unknown $id
     * @param unknown $remark
     * @throws \LogicException
     * @return Ambigous <boolean, unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editBrand($id, $remark)
    {
        if ($id <= 0) {
            throw new \LogicException('请选择品牌', C('BAD_REQUEST'));
        } else {
            if (! is_null($remark)) {
                return M('Brands')->where(array('id' => $id))->setField('remark', $remark);
            } else {
                throw new \LogicException('参数有误', C('BAD_REQUEST'));
            }
        }
    }

    /**
     * 获取品牌
     * 
     * 
     * @param unknown $data
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function getBrand($data)
    {
        $isPage = isset($data['page']) && isset($data['pageSize']);
        list ($search, $sort) = $this->getSortRulesAndSearch($data);
        
        $BrandMod = M('Brands');
        $count = $BrandMod->alias('b')
            ->join('LEFT JOIN tb_user u ON b.customer=u.user')
            ->where($search)
            ->count();
        if ($isPage) {
            $page = (int) $data['page'];
            $pageSize = (int) $data['pageSize'];
            $list = $BrandMod->alias('b')
                ->join('LEFT JOIN tb_user u ON b.customer=u.user')
                ->field('b.*,u.name AS customer_name')
                ->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $BrandMod->alias('b')
                ->join('LEFT JOIN tb_user u ON b.customer=u.user')
                ->field('b.*,u.name AS customer_name')
                ->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取检索条件和排序条件
     * 
     * 
     * @param unknown $data
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    protected function getSortRulesAndSearch($data)
    {
        $where = array();
        $sort = 'b.id DESC';
        if (! empty($data)) {
            $map = array('id', 'brand_name', 'customer');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = 'b.' . $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['customer']) && $where['u.user'] = $data['customer'];
            ! empty($data['name']) && $where['b.brand_name'] = array('like', '%' . $data['name'] . '%');
        }
        return array($where, $sort);
    }

    /**
     * 根据用户名获取品牌
     * 
     * 
     * @param unknown $user
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function getBrandByUser($user)
    {
        if (empty($user)) {
            throw new \LogicException('用户名不存在', C('BAD_REQUEST'));
        } else {
            return M('Brands')->where(array('customer' => $user))
                ->order('id DESC')
                ->select();
        }
    }

    /**
     * 根据品牌，获取固件。进而获取固件所属的平台
     * 
     * 
     * @param unknown $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月12日
     */
    public function getPlatform($data)
    {
        $isPage = isset($data['page']) && isset($data['pageSize']);
        list ($search, $sort) = $this->getPlatformSortRulesAndSearch($data);
        
        C('READ_DATA_MAP', false);
        $count = D('FirmwarePublish')->where($search)->count('DISTINCT platform');
        if ($isPage) {
            $page = (int) $data['page'];
            $pageSize = (int) $data['pageSize'];
            $list = D('FirmwarePublish')->field('platform')
                ->where($search)
                ->group('platform')
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = D('FirmwarePublish')->field('platform')
                ->where($search)
                ->group('platform')
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取检索条件和排序规则
     * 
     * 
     * @param unknown $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月12日
     */
    public function getPlatformSortRulesAndSearch($data)
    {
        $where = array();
        $sort = 'id DESC';
        if (! empty($data)) {
            $map = array('platform');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['brand_id']) && $where['brand_id'] = (int) $data['brand_id'];
            ! empty($data['name']) && $where['platform'] = array('like', '%' . $data['name'] . '%');
        }
        return array($where, $sort);
    }
}