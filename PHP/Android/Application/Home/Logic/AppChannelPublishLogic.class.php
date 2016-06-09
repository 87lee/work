<?php

namespace Home\Logic;

use Think\Model;

/**
 * 渠道包逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月18日
 * @version   1.0
 */
class AppChannelPublishLogic extends Model
{

    /**
     * 获取渠道包、支持分页和检索
     * 
     * 
     * @param array $data
     * @return array
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function getChannelApp($data)
    {
        $isPage = isset($data['page']) && isset($data['pageSize']);
        list ($search, $sort) = $this->getSortRulesAndSearch($data);
        
        $appChannelMod = M('AppChannelPublish');
        if ($data['id'] > 0) {
            return $appChannelMod->alias('acp')
                ->join('LEFT JOIN tb_user u ON acp.publisher=u.user')
                ->field('acp.*,u.name AS publisher_name')
                ->where(array('acp.id' => $data['id']))
                ->select();
        }
        
        $count = $appChannelMod->alias('acp')
            ->join('LEFT JOIN tb_user u ON acp.publisher=u.user')
            ->where($search)
            ->count();
        if ($isPage) {
            $page = (int) $data['page'];
            $pageSize = (int) $data['pageSize'];
            $list = $appChannelMod->alias('acp')
                ->join('LEFT JOIN tb_user u ON acp.publisher=u.user')
                ->field('acp.*,u.name AS publisher_name')
                ->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $appChannelMod->alias('acp')
                ->join('LEFT JOIN tb_user u ON acp.publisher=u.user')
                ->field('acp.*,u.name AS publisher_name')
                ->where($search)
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
     * @return multitype:string multitype:multitype:string  number  
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    protected function getSortRulesAndSearch($data)
    {
        $where = array();
        $sort = '';
        if (! empty($data)) {
            //排序：名称、包名、渠道号、发布人、发布时间，要支持排序
            $map = array('channel_name', 'pkg_name', 'channel_id', 'user', 'pub_time');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    if ($sortArr[0] == 'user') {
                        $sort = 'u.name' . ' ' . $sortArr[1];
                    } else {
                        $sort = 'acp.' . $sortArr[0] . ' ' . $sortArr[1];
                    }
                }
            }
            
            //检索字段:名称、包名、渠道号、发布人
            if (! empty($data['name'])) {
                $where['acp.channel_name|acp.pkg_name|acp.channel_id|u.name'] = array('like', '%' . $data['name'] . '%');
            } else {
                ! empty($data['channel_name']) && $where['acp.channel_name'] = array('like', '%' . $data['channel_name'] . '%');
                ! empty($data['pkg_name']) && $where['acp.pkg_name'] = array('like', '%' . $data['pkg_name'] . '%');
                ! empty($data['channel_id']) && $where['acp.channel_id'] = array('like', '%' . $data['channel_id'] . '%');
                ! empty($data['publisher']) && $where['u.name'] = array('like', '%' . $data['publisher'] . '%');
            }
            ! empty($data['app_id']) && $where['acp.app_publish_id'] = (int) $data['app_id'];
        }
        return array($where, $sort);
    }

    /**
     * 根据id下载渠道包
     * 
     * 
     * @param unknown $id
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月31日
     */
    public function download($id)
    {
        if ($id <= 0) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        } else {
            $path = $this->where(array('id' => $id))->getField('path');
            if (empty($path)) {
                throw new \LogicException('渠道包下载地址不存在', C('BAD_REQUEST'));
            } else {
                $path = C("DOWNLOAD_APK_PREFIX_ADDR") . $path;
                header("Content-Type: application/force-download");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=" . basename($path));
                readfile($path);
            }
        }
    }
}
    
