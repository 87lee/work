<?php

namespace Home\Controller;

/**
 * 信息控制器【目前只支持邮件】
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月30日
 * @version   1.0
 */
class MessageController extends HomeBaseController
{

    /**
     * 发送信息
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public function send()
    {
        //邮箱
        $_POST = ['type' => 'email', 'mail_to' => '1353178739@qq.com', 'subject' => '这是标题', 'content' => '这是发送的内容'];
        $method = 'send' . ucwords(I('post.type', '^_^'));
        if (method_exists($this, $method)) {
            $this->$method(I('post.mail_to'), I('post.subject'), I('post.content'));
        }
    }

    /**
     * 发送邮件
     * 
     * 
     * @param unknown $mailTo
     * @param unknown $subject
     * @param unknown $content
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public function sendEmail($mailTo, $subject, $content)
    {
        try {
            //判断内容是否为空、邮箱格式是否正确
            if (preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $mailTo) !== 1) {
                json_echo(C('BAD_REQUEST'), '邮箱格式不正确');
            }
            
            if (empty($subject) || empty($content)) {
                json_echo(C('BAD_REQUEST'), '邮件标题和内容不能为空');
            }
            
            import('Vendor.PHPMailer.Mail');
            //\Mail::getInstance(C('MAIL_CONFIG'))->send($mailTo, $subject, $content);
            json_echo(C('SUCCESS'), '发送邮件成功');
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), '发送邮件失败');
        }
    }
}