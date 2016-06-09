<?php

namespace Home\Controller;

use Think\Controller;

class TestController extends Controller
{
    /**
     * 测试
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function index(){
        $this->assign('get',$_GET);
        $this->assign('test','测试文件。。。。');
        $this->display();
    }
}