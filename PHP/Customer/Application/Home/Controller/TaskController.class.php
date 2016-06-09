<?php

namespace Home\Controller;

use Think\Controller;
use Think\Log;

/**
 * 任务控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月11日
 * @version   1.0
 */
class TaskController extends Controller
{

    /**
     * 发送邮件
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月11日
     */
    public function sendMail()
    {
        $logPath = C('LOG_PATH') . '/email/sendMail_' . date('Ymd') . '.log';
        $mailMod = D('Mail');
        
        //整点清理7天前的邮件队列和发送失败超过5次的记录
        if (0 == (int) date('i')) {
            $mailMod->clean();
        }
        
        $result = $mailMod->send(20);
        if($result !== false){
            Log::write("定时发送邮件任务结果：\n" . var_export($result, true), '', '', $logPath);
        }
    }

    /**
     * 查看邮件定时任务日志记录
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月11日
     */
    public function sendLog()
    {
        $data = ! empty($_GET['name']) ? $_GET['name'] : date('Ymd');
        $logPath = C('LOG_PATH') . '/email/sendMail_' . $data . '.log';
        if (file_exists($logPath) && is_readable($logPath)) {
            echo '<pre>', file_get_contents(C('LOG_PATH') . '/email/sendMail_' . $data . '.log'), '</pre>';
        } else {
            echo '文件不存在或者不可读';
        }
    }
}