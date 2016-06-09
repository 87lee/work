<?php

namespace Home\Logic;

use Think\Model;

/**
 * 平台逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class PlatformLogic extends Model
{

    /**
     * 录入平台
     * 
     * 
     * @param array $data
     * @return array 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function inputPlatform(array $data)
    {
        if (! key_exists('platform', $data)) {
            return array('code' => C('BAD_REQUEST'), 'msg' => '参数有误', 'retval' => array());
        }
        
        $platformMod = D('Platform');
        //判断平台是否存在
        if ($platformMod->where(array('platform' => $data['platform']))->find()) {
            return array('code' => C('SUCCESS'), 'msg' => '平台名已经存在，请更换', 'retval' => array());
        }
        $platformMod->create($data);
        $validError = $platformMod->getError();
        if ($validError) {
            return array('code' => C('BAD_REQUEST'), 'msg' => $validError, 'retval' => array());
        } else {
            $result = $platformMod->add();
            if ($result !== false) {
                return array('code' => C('SUCCESS'), 'msg' => '平台录入成功', 'retval' => array('id' => $result));
            }
            return array('code' => C('SUCCESS'), 'msg' => '平台录入失败', 'retval' => array());
        }
    }

    /**
     * 根据id删除记录
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
        
        $result = D('Platform')->where(array('id' => array('in', $ids)))->delete();
        return array('code' => C('SUCCESS'), 'msg' => $result !== false ? '删除成功' : '删除失败', 'retval' => array());
    }

    /**
     * 获取平台信息
     * 
     * 
     * @param unknown $param
     * @return multitype:number \Think\mixed 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月18日
     */
    public function getPlatform($param)
    {
        $isPage = isset($param['page']) && isset($param['pageSize']);
        $page = (int) $param['page'];
        $pageSize = (int) $param['pageSize'];
        list ($search, $sort) = $this->getSortRulesAndSearch($param);
        
        $count = D('Platform')->where($search)->count();
        if ($isPage) {
            $list = D('Platform')->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = D('Platform')->where($search)
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
            $map = array('id' => 'id', 'pform' => 'platform');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (key_exists($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = $map[$sortArr[0]] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['platform'] = array('like', '%' . $data['name'] . '%');
        }
        return array($where, $sort);
    }

    /**
     * 修改平台，只能修改备注
     * 
     * 
     * @param unknown $id
     * @param unknown $remark
     * @throws \LogicException
     * @return Ambigous <boolean, unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editPlatform($id, $remark)
    {
        if ($id <= 0) {
            throw new \LogicException('请选择平台', C('BAD_REQUEST'));
        } else {
            if (! is_null($remark)) {
                return M('Platform')->where(array('id' => $id))->setField('note', $remark);
            } else {
                throw new \LogicException('参数有误', C('BAD_REQUEST'));
            }
        }
    }
}