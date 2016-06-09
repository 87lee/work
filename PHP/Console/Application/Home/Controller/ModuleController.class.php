<?php
namespace Home\Controller;
use Think\Controller;

class ModuleController extends Controller {
    public function __construct()
            {
                    parent::__construct();
                    isLogin();

            }

    /**
     * 首页 安全页
     *
     */

     public function index()
        {
           /* $modules = D('Modules');
            $modules->getAllModules();*/
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
     * 模块管理_添加模块
     * 地址 post /Modules/add
     *
     */
    public function add(){
        $module = I('put.module');
        $module_name = I('put.module_name');
        $modules = D('Module');

        $modules->addModules($module,$module_name);
    }

    /**
     * 模块管理_删除模块
     * @param 传入参数 {"id":""}
     * @return [type] [description]
     */

    public function delete()
    {
        $id = I('get.id');
        $sub_id = I('get.sub_id');
        $modules = D('Module');
        if (!empty($sub_id)&&!empty($id)) {
            $modules->delModules($id,$sub_id);
        }elseif(!empty($id)){
            $modules->delModules($id);
        }else{
            $data['result'] = 'fail';
            $data['reason'] = '参数有误';
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 模块管理_添加子模块
     * @param 传入参数 {"module_id":1, "sub_module":"xxx", "sub_module_name":"xxxx"}
     * @return [type] [description]
     */

    public function sub()
    {
        $modules = D('ModuleSub');
        $module_id= I('put.module_id');
        $sub_module= I('put.sub_module');
        $sub_module_name= I('put.sub_module_name');
        if (!empty($module_id)&&!empty($sub_module)&&!empty($sub_module_name)) {
            $modules->addSubModules($module_id,$sub_module,$sub_module_name);
        }else{
            $data['result'] = 'fail';
            $data['reason'] = '参数有误';
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
    }
    /**
     * 模块管理_设置用户模块权限
     *
     */
    public function auth()
    {
        $user_id = I('get.user_id');
        $modules = I('put.');

        $user_auth = D('UserAuth');

        if (!empty($user_id)&&!empty($modules)) {
            $user_auth ->auth($user_id,$modules);
        }else{

            $data['result'] = 'fail';
            $data['reason'] = '参数有误';
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 模块管理_所有模块列表
     * @return
     */
    public function lists()
    {
        $user_id = I('get.user_id');
        if ($user_id) {
            $modules = D('Module');
            $res = $modules->getModules($user_id);
        }else{
            $modules = D('Module');
            $res = $modules->getModules();
        }
        result(true,$res);
    }
    /**
     * 模块管理_查出自定义所有模块列表
     * @return [type] [description]
     */
    public function customModeulLists()
    {
        	$user = session('is_login');
        	$user_id = intval($user['uid']);
        	if (!empty($user_id)) {
            	$res = D('UserAuthCustom')->getModules($user_id);
        	}else{
        		$res['modules'] = array();
        	}
        	result(true,$res);
    }
    /**
     * 模块管理_用户模块自定义
     *
     */
    public function authCustom()
    {
        	$user = session('is_login');
	$user_id = intval($user['uid']);
        	$modules = I('put.');
	if (!empty($modules)&&!empty($user_id)) {
            	D('UserAuthCustom')->authCustom($user_id,$modules);
            		result();
        	}else{
            		result('param');
        	}
    }
    /**
     * 模块管理_用户模块自定义同步用户模块
     *
     */
    public function syncCustom()
    {
    	if (!D('UserAuthCustom')->select()) {
    		$res = D('UserAuth')->field('user_id,module_id')->group('module_id,user_id')->order("user_id")->select();
        		D('UserAuthCustom')->addAll($res);
        		result();
    	}else{
    		result('已同步');
    	}
    }
}