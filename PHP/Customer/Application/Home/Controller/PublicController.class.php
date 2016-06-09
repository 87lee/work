<?php

namespace Home\Controller;

/**
 * 模板公用头部、尾部
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月21日
 * @version   1.0
 */
class PublicController extends HomeBaseController
{

    /**
     * 模板公用头部
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月21日
     */
    public function header()
    {
        $this->display();
    }

    /**
     * 模板公用尾部
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月21日
     */
    public function footer()
    {
        $this->display();
    }
}