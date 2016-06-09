<?php

namespace Home\Model;

/**
 * 用例模型
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月6日
 * @version   1.0
 */
class UseCaseModel extends \Think\Model
{

    /**
     * 未测试
     * @var unknown
     */
    const NOT_TEST = 0;

    /**
     * 通过
     * @var unknown
     */
    const HAS_PASS = 1;

    /**
     * 未通过
     * @var unknown
     */
    const NOT_PASS = 2;

    /**
     * 不适用
     * @var unknown
     */
    const NOT_SUITABLE = 3;
}