<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    	public function index(){
    		// phpinfo();
    	}

   	public function _empty(){
    		result('请填写正确地址');
    	}
}