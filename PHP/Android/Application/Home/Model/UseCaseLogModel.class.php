<?php

namespace Home\Model;

/**
 * 测试用例操作记录模型
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月5日
 * @version   1.0
 */
class UseCaseLogModel extends \Think\Model
{

    /**
     * 新增
     * @var unknown
     */
    const IS_ADD = 1;

    /**
     * 更新
     * @var unknown
     */
    const IS_UPDATE = 2;

    /**
     * 删除
     * @var unknown
     */
    const IS_DELETE = 3;

    /**
     * 重置
     * @var unknown
     */
    const IS_RESET = 4;
}