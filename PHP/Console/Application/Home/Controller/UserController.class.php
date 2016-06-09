<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {


    public function __construct()
            {
                    parent::__construct();
                    isLogin();
            }

    /**
     * 首页 安全页
     *
     */

    public function index(){

    }
    /**
	 	* 访问不存在的地址
	 	* @param  [type] $name [description]
	 	* @return [type]       [description]
	 	*/
	public function _empty($name){        //把所有城市的操作解析到city方法
		echo '{"result":"fail","reason":"当前地址不存在"}';
	}
    /**
     * 退出登陆
     */
    public function logout()
    {
        $user = D('User');
        $user->logout();
    }

    /**
     * 登陆用户
     * post {"user":"admin","passwd":"admin"}
     * @return json
     */

    public function login(){
        $user = D('User');
        $name = I('put.user');
        $psw = I('put.passwd');
        $user->login($name,$psw);
    }

    /**
     * 增加用户
     * {"user":"xxx", "passwd":"xxxx"}
     * @return json
     */

    public function add()
    {
        $name = I('put.user');
        $passwd= I('put.passwd');
        $user = D('User');
        $user->addUser($name,$passwd);
    }

    /**
     * 删除用户
     * GET/user/delete?id=x
     * @return json
     */
    public function delete()
    {
        $name = I('get.id');
        $user = D('User');
        $user->delUser($name);
    }
    /**
     * 修改用户密码
     * @return json
     */
    public function passwd()
    {

        $name = I('put.user');
        $old_psw = I('put.old');
        $new_psw = I('put.new');
        $user = D('User');
        $user->editPasswd($name,$old_psw,$new_psw);
    }
    /**
     * 获取所有用户列表
     * @return json
     */
    public function lists()
    {
        $user = D('User');
        $id = I('get.id');
        if ($id) {
            $user->getAllUser($id);
        }else{
            $user->getAllUser();
        }

    }


}