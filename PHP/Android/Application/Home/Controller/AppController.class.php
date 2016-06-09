<?php

namespace Home\Controller;

class AppController extends HomeBaseController
{

    public function _empty()
    {
        result('请填写正确地址');
    }

    /**
     * Android前端发布系统_APP_发布APP模块
     * post /Android/Home/App/publishApp
     *表单上传 type=file name = apkFile   上传文件为.apk
     * type=test,value = {"versionDesc":"版本备注"},name=extra
     * @return [type] [description]
     */
    public function publishApp()
    	{
    		set_time_limit(10*60);
    		try {
    		    $put = I('post.extra');
    		    if (empty($put)) {
    		        $put = '';
    		    }
    		    $options['versionDesc'] = $put;
    		    $publishId = D('AppPublish')->publishApp($options);
    		    result();
    		} catch (\Exception $e) {
    		    result($e->getMessage());
    		}
    		
    	}

    /**
     * Android前端发布系统_APP_修改用户APP权限
     * post /Android/Home/App/modifyAppAdmin
     * {
     * 	"id":"用户模块权限ID"
     * 	"name":"模块名"
     *  	"operator":"用户名",
     *  	"note":"备注"
     *
     *  }
     * @return [type] [description]
     */
    /*public function modifyAppAdmin()
     {
     $put = I('put.');
     D('AppAdmin')->modifyAppAdmin($put);
     result();
     }*/
    /**
     * Android前端发布系统_APP_删除用户APP
     * post /Android/Home/App/deletePublishApp
     * ["1","2"]
     * @return [type] [description]
     */
    public function deletePublishApp()
    {
        $put = I('put.');
        
        D('AppPublish')->deletePublishApp($put);
        
        result();
    }

    /**
     * Android前端发布系统_APP_APP模块列表
     * get /Android/Home/App/publishAppLists?id=x
     * get /Android/Home/App/publishAppLists?page=x&pageSize=x
     * get /Android/Home/App/publishAppLists?page=x&pageSize=x&name=x
     * @return [type] [description]
     */
    public function publishAppLists()
    {
        $get = I('get.');
        $res = D('AppPublish')->publishAppLists($get);
        result(true, $res);
    }

    /**
     * Android前端发布系统_APP_APP依赖跳转
     * get /Android/Home/Base/relyAppLists?module=依赖的模块名&versionName=依赖的模块版本名&pkg_name=依赖的模块包名
     * @return [type] [description]
     */
    public function relyAppLists()
    {
        $get = I('get.');
        $res = D('AppPublish')->relyAppLists($get);
        
        result(true, $res);
    }

    /**
     * Android前端发布系统_APP_APP模块修改状态
     * post /Android/Home/App/mofidyPassTest
     * {
     * 	'id':"发布APPID",
     * 	"passTest":"false/true"
     * }
     * @return [type] [description]
     */
    public function mofidyPassTest()
    {
        $put = I('put.');
        D('AppPublish')->mofidyPassTest($put);
        result();
    }

    /**
     * Android前端发布系统_APP_APP模块添加评论
     * post /Android/Home/App/addAppComment
     *
     *{
     *	"appId":"发布模块的ID",
     *	"content":"评论内容"
     *}
     *
     * @return [type] [description]
     */
    public function addAppComment()
    {
        $put = I('put.', '', 'htmlspecialchars');
        $id = D('AppComment')->addAppComment($put);
        result();
    }

    /**
     * Android前端发布系统_APP_用户APP删除评论
     * get /Android/Home/App/deleteAppComment?appId=x
     * @return [type] [description]
     */
    public function deleteAppComment()
    {
        $get = I('get.');
        D('AppComment')->deleteAppComment($get);
        result();
    }

    /**
     * Android前端发布系统_APP_用户APP评论列表
     * get /Android/Home/App/AppCommentLists?appId=发布id
     * get /Android/Home/App/AppCommentLists?appId=发布id&page=x&pageSize=x
     * @return [type] [description]
     */
    public function appCommentLists()
    {
        $get = I('get.');
        $res = D('AppComment')->appCommentLists($get);
        result(true, $res);
    }

    /**
     * Android前端发布系统_APP_APP发布规则
     * post /Android/Home/App/addAppPublishRule
     * {
     * 	"column":"规则字段",   //不为空
     * 	"operator":"算术 '+', '-', '*', '/', '%'"  //可为空
     * 	"param":"算术第二值",      //可为空   当operator 不为空时，不为空
     * 	"condition":"判断符号",   '==', '!=', '>=', '<=', '>', '<' " //不为空
     * 	"value":"判断值",     //不为空
     * 	"note":"备注"		//可为空
     * }
     * @return [type] [description]
     */
    public function addAppPublishRule()
    {
        $put = I('put.', '', '');
        D('AppPublishRule')->addAppPublishRule($put);
        result();
    }

    /**
     * Android前端发布系统_APP_修改应用发布规则
     * post /Android/Home/App/modifyAppPublishRule
     * {
     * 	"id","发布规则ID",
     * 	"column":"规则字段",   //不为空
     * 	"operator":"算术 '+', '-', '*', '/', '%'"  //可为空
     * 	"param":"算术第二值",      //可为空   当operator 不为空时，不为空
     * 	"condition":"判断符号",   '==', '!=', '>=', '<=', '>', '<' " //不为空
     * 	"value":"判断值",     //不为空
     * 	"note":"备注"		//可为空
     * }
     * @return [type] [description]
     */
    public function modifyAppPublishRule()
    {
        $put = I('put.', '', '');
        D('AppPublishRule')->modifyAppPublishRule($put);
        result();
    }

    /**
     * Android前端发布系统_APP_删除APP发布规则
     * post /Android/Home/App/deleteAppPublishRule
     * ["id","id"]
     *
     * @return [type] [description]
     */
    public function deleteAppPublishRule()
    {
        $put = I('put.');
        D('AppPublishRule')->deleteAppPublishRule($put);
        result();
    }

    /**
     * Android前端发布系统_APP_APP发布规则列表
     * get /Android/Home/App/appPublishRuleLists?page=x&pageSize=x
     * get /Android/Home/App/appPublishRuleLists?page=x&pageSize=x&name=x
     * @return [type] [description]
     */
    public function appPublishRuleLists()
    {
        $get = I('get.');
        $res = D('AppPublishRule')->appPublishRuleLists($get);
        result(true, $res);
    }

    /**
     * Android前端发布系统_APP_APP获取未读评论条数
     * get /Android/Home/App/commentUnreadCount
     * @return [type] [description]
     */
    public function commentUnreadCount()
    {
        $res = D('AppCommentUnread')->commentUnreadCount();
        result(true, $res);
    }

    /**
     * Android前端发布系统_APP_APP删除未读评论
     * get /Android/Home/App/deleteCommentUnread?appId=发布appid
     * @return [type] [description]
     */
    public function deleteCommentUnread()
    {
        $put = I('put.');
        D('AppCommentUnread')->deleteCommentUnread($put);
        result();
    }

    /**
     * Android前端发布系统_APP_APP未读评论列表
     * get /Android/Home/App/deleteCommentUnread?appId=发布appid
     * @return [type] [description]
     */
    public function commentUnreadLists()
    {
        $get = I('get.');
        $res = D('AppCommentUnread')->commentUnreadLists($get);
        result(true, $res);
    }

    /**
     * Android前端发布系统_APP_APP下载
     * get /Android/Home/App/download?appId=发布appid
     * @return [type] [description]
     */
    public function download()
    {
        $get = I('get.');
        $file = D('AppPublish')->download($get);
        
        if (! empty($file)) {
            header("Content-Type: application/force-download");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . basename($file));
            readfile($file);
        }
    }

    /**
     * 生成月报
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月20日
     */
    public function createReport()
    {
        //$_GET = array('s_time' => '2016-04-18', 'e_time' => '2016-04-18', 'app_name' => '');
        try {
            json_echo(C('SUCCESS'), '成功', D('App', 'Logic')->createReport(I('get.')));
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), $e->getMessage());
        }
    }

    /**
     * 获取应用对应名和包名
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月22日
     */
    public function getAppName()
    {
        try {
            json_echo(C('SUCCESS'), '成功', D('App', 'Logic')->getAppName());
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), $e->getMessage());
        }
    }

    /**
     * 获取应用状态修改记录
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月22日
     */
    public function getAppStatusRecord()
    {
        try {
            json_echo(C('SUCCESS'), '成功', 
                D('AppStatusModifyRecord', 'Logic')->getAppStatusRecordByApp(I('get.pkg_name'), I('get.s_time', 0), I('get.e_time', 0)));
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), $e->getMessage());
        }
    }

    /**
     * 获取应用下拉框
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月6日
     */
    public function appOption()
    {
        try {
            json_echo(C('SUCCESS'), '成功', D('App', 'Logic')->getAppName(I('get.name'), 'id,app,name'));
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), $e->getMessage());
        }
    }

    /**
     * 发布渠道包
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function publishChannelApp()
    {
        /* $_POST=array(
         'channel_apk'=>'file上传文件'
         'channel_name'=>'渠道包名称',
         'app_id'=>1,
         'desc'=>'版本描述'
         ); */
        ! IS_POST && json_echo(C('FORBIDDEN'), '请求方式有误');
        set_time_limit(10 * 60);
        try {
            json_echo(C('SUCCESS'), '渠道包发布成功', array('id' => D('App', 'Logic')->publishChannel(I('post.'))));
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), $e->getMessage());
        }
    }

    /**
     * 删除渠道包
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function delChannelApp()
    {
        //$_POST = array('ids' => [14, 15]);
        ! IS_POST && json_echo(C('FORBIDDEN'), '请求方式有误');
        try {
            $res = D('App', 'Logic')->delChannelApp(I('post.ids'));
            if (true === $res) {
                json_echo(C('SUCCESS'), '删除渠道包成功');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取渠道包
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function getChannelApp()
    {
        try {
            json_echo(C('SUCCESS'), '获取渠道包成功', D('AppChannelPublish', 'Logic')->getChannelApp(I('get.')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 根据用户所属组获取修改app状态下拉列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月26日
     */
    public function appModifyStatus()
    {
        try {
            json_echo(C('SUCCESS'), '获取成功', D('App', 'Logic')->appModifyStatus());
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 下载渠道包
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月31日
     */
    public function downloadChannel()
    {
        try {
            json_echo(C('SUCCESS'), '下载成功', D('AppChannelPublish', 'Logic')->download(I('get.id')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
}