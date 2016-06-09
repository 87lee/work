<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{

    public function index()
    {
        $this->display('login');
    }

    public function _empty()
    {
        result('请填写正确地址');
    }
    
    public function main(){
        $this->display();
    }
    public function welcome(){
        echo '欢迎进入客服系统';
    }
}
