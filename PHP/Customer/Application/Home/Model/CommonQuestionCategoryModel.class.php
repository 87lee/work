<?php

namespace Home\Model;

/**
 * 常见问题分类模型
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月21日
 * @version   1.0
 */
class CommonQuestionCategoryModel extends \Think\Model
{

    /**
     * 根据分类ids获取分类信息
     * 
     * 
     * @param array $ids
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月25日
     */
    public function getCateInfoByIds(array $ids)
    {
        return $this->where(array('cate_id' => array('in', $ids)))
            ->field('cate_id,cate_name')
            ->index('cate_id')
            ->select();
    }
}