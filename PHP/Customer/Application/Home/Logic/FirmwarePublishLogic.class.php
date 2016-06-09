<?php

namespace Home\Logic;

use Think\Model;

/**
 * 发布固件逻辑
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class FirmwarePublishLogic extends Model
{

    /**
     * 发布固件
     * 
     * 
     * @param array $data
     * @return array 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function publish(array $data)
    {
        $return = array('code' => C('SUCCESS'), 'msg' => '', 'retval' => array());
        
        if (! empty($data)) {
            $validKey = array(
                'VendorId', 
                'PlatForm', 
                'FirmwareVer', 
                'VersionDesc', 
                'Md5', 
                'Customer', 
                'Path', 
                'Passwd', 
                'Brand', 
                'type_name', 
                'plate_name');
            $key = array_keys($data);
            if (array_diff($validKey, $key)) {
                $return['code'] = C('BAD_REQUEST');
                $return['msg'] = '请求参数有误';
            } else {
                $data['Publisher'] = session('customerIsLogin.user');
                //生成固件唯一性判断字符串
                //customer+brand_id+vendor_id+platform+firmware_ver+md5+path
                $data['unique_md5_str'] = md5(
                    $data['Customer'] . $data['Brand'] . $data['VendorId'] . $data['PlatForm'] . $data['FirmwareVer'] . $data['Md5'] .
                         $data['Path']);
                
                $firmwarePublishMod = D('FirmwarePublish');
                if ($firmwarePublishMod->where(array('unique_md5_str' => $data['unique_md5_str']))->find()) {
                    //唯一性判断
                    $return['msg'] = '固件已经存在';
                } else {
                    $data['pub_time'] = time();
                    $firmwarePublishMod->create($data);
                    $result = $firmwarePublishMod->add();
                    $return['msg'] = $result !== false ? '发布固件成功' : '发布固件失败';
                }
            }
        } else {
            $return['code'] = C('BAD_REQUEST');
            $return['msg'] = '请求参数有误';
        }
        unset($data);
        return $return;
    }

    /**
     * 按id删除固件发布信息
     * 
     * 
     * @param array $ids
     * @return array
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function deleteByIds(array $ids)
    {
        $return = array('code' => C('SUCCESS'), 'msg' => '', 'retval' => array());
        $ids = array_map('intval', $ids);
        
        if (empty($ids)) {
            $return['code'] = C('BAD_REQUEST');
            $return['msg'] = '请求参数有误';
        } else {
            C('READ_DATA_MAP', false);
            $logRecords = D('FirmwarePublish')->where(array('id' => array('in', $ids)))->select();
            $result = D('FirmwarePublish')->where(array('id' => array('in', $ids)))->delete();
            if ($result !== false) {
                M('FirmwarePublishHistory')->addAll($logRecords);
                $return['msg'] = '删除成功';
            } else {
                $return['msg'] = '删除失败';
            }
        }
        return $return;
    }

    /**
     * 获取固件发布列表
     * 
     * 
     * @param array $param
     * @return array
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function getFirmwarePublish($param)
    {
        $id = isset($param['id']) ? (int) $param['id'] : 0;
        $firwarePublishMod = D('FirmwarePublish');
        if (0 < $id) {
            return $firwarePublishMod->alias('fp')
                ->join('LEFT JOIN ' . C('DB_PREFIX') . 'user AS u ON fp.publisher=u.user LEFT JOIN tb_brands b ON fp.brand_id=b.id')
                ->where(array('fp.id' => $id))
                ->field('fp.*,u.name,b.brand_name')
                ->find();
        }
        
        //获取页码、每页显示数、排序规则、搜索条件
        $page = isset($param['page']) ? (int) $param['page'] : 1;
        $pageSize = isset($param['pageSize']) ? (int) $param['pageSize'] : 20;
        list ($search, $sort) = $this->getSortRulesAndSearch($param);
        //获取数据不要求字段映射，动态改变
        C('READ_DATA_MAP', false);
        
        $count = $firwarePublishMod->alias('fp')
            ->join('LEFT JOIN ' . C('DB_PREFIX') . 'user AS u ON fp.publisher=u.user LEFT JOIN tb_brands b ON fp.brand_id=b.id')
            ->where($search)
            ->count();
        $list = $firwarePublishMod->alias('fp')
            ->join('LEFT JOIN ' . C('DB_PREFIX') . 'user AS u ON fp.publisher=u.user LEFT JOIN tb_brands b ON fp.brand_id=b.id')
            ->field('fp.*,u.name,b.brand_name')
            ->where($search)
            ->order($sort)
            ->limit(($page - 1) * $pageSize . ',' . $pageSize)
            ->select();
        if (! empty($list)) {
            $customer = array_column($list, 'customer');
            $userList = M('User')->where(array('user' => array('in', $customer)))
                ->field('user,name')
                ->index('user')
                ->select();
            foreach ($list as $k => $v) {
                $list[$k]['customer_name'] = isset($userList[$v['customer']]) ? $userList[$v['customer']]['name'] : '';
            }
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => ceil($count / $pageSize), 'list' => $list);
    }

    /**
     * 获取排序规则和搜索条件
     * 
     * 
     * @param array $data
     * @return string
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月18日
     */
    protected function getSortRulesAndSearch(array $data)
    {
        //排序字段 vendor_id,platform,firmware_ver,customer,publisher,pub_time
        $where = array();
        $sort = 'id DESC';
        if (! empty($data)) {
            $map = array(
                'venid' => 'fp.vendor_id', 
                'pform' => 'fp.platform', 
                'firmv' => 'fp.firmware_ver', 
                'cust' => 'fp.customer', 
                'puber' => 'fp.publisher', 
                'time' => 'fp.pub_time', 
                'typename' => 'fp.type_name', 
                'plate' => 'fp.plate_name', 
                'brand' => 'b.brand_name');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (key_exists($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = $map[$sortArr[0]] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['brand_id']) && $where['fp.brand_id'] = (int) $data['brand_id'];
            ! empty($data['platform']) && $where['fp.platform'] = $data['platform'];
            //客户搜索
            if (! empty($data['customer'])) {
                $where['fp.customer'] = $data['customer'];
            } else {
                ! empty($data['cust']) && $where['fp.customer'] = array('like', '%' . $data['cust'] . '%');
            }
            ! empty($data['venid']) && $where['fp.vendor_id'] = array('like', '%' . $data['venid'] . '%');
            ! empty($data['pform']) && $where['fp.platform'] = array('like', '%' . $data['pform'] . '%');
            ! empty($data['firmv']) && $where['fp.firmware_ver'] = array('like', '%' . $data['firmv'] . '%');
            ! empty($data['typename']) && $where['fp.type_name'] = array('like', '%' . $data['typename'] . '%');
            ! empty($data['plate']) && $where['fp.plate_name'] = array('like', '%' . $data['plate'] . '%');
            ! empty($data['brand']) && $where['b.brand_name'] = array('like', '%' . $data['brand'] . '%');
            if (! empty($data['time'])) {
                $timeStamp = strtotime($data['time'] . ':00:00');
                $where['fp.pub_time'] = array(array('gt', $timeStamp), array('lt', $timeStamp + 3600));
            }
        }
        return array($where, $sort);
    }

    /**
     * 固件评论
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月8日
     */
    public function firmComment(array $data)
    {
        if (! empty($data['firm_id']) && ! empty($data['content'])) {
            if (M('FirmwarePublish')->find((int) $data['firm_id'])) {
                return M('FirmwareComment')->add(
                    array(
                        'publish_id' => $data['firm_id'], 
                        'content' => $data['content'], 
                        'time' => time(), 
                        'user' => session('customerIsLogin.user')));
            } else {
                throw new \LogicException('固件不存在', C('BAD_REQUEST'));
            }
        } else {
            throw new \LogicException('没有选择固件或评论为空', C('BAD_REQUEST'));
        }
    }

    /**
     * 获取固件列表
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月8日
     */
    public function getCommetList(array $data)
    {
        if (! empty($data['firm_id']) && (int) $data['firm_id'] > 0) {
            if (! empty($data['page']) && ! empty($data['pageSize'])) {
                //分页
                $res['extra'] = M('FirmwareComment')->where(array('publish_id' => (int) $data['firm_id']))
                    ->order('time desc')
                    ->limit(($data['page'] - 1) * $data['pageSize'], $data['pageSize'])
                    ->select();
            } else {
                //获取全部
                $res['extra'] = M('FirmwareComment')->where(array('publish_id' => (int) $data['firm_id']))
                    ->order('time desc')
                    ->select();
            }
            $res['count'] = M('FirmwareComment')->where(array('publish_id' => (int) $data['firm_id']))->count();
            if (! empty($res['extra'])) {
                //获取昵称
                $userNames = array_column($res['extra'], 'user');
                $nickNames = D('User')->getUserByUser($userNames, true);
                foreach ($res['extra'] as $k => $v) {
                    $res['extra'][$k]['user'] = isset($nickNames[$v['user']]) ? $nickNames[$v['user']]['name'] : '';
                }
            }
            return $res;
        } else {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        }
    }

    /**
     * 根据评论id删除评论
     * 
     * 
     * @param mix $ids
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月8日
     */
    public function delCommetByIds($ids)
    {
        $ids = is_array($ids) ? $ids : array((int) $ids);
        if (! empty($ids)) {
            return M('FirmwareComment')->where(array('id' => array('in', $ids)))->delete();
        } else {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        }
    }
}
