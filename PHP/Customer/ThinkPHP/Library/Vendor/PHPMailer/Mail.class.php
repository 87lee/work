<?php

/**
 * 邮件发送类
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月30日
 * @version   1.0
 */
require_once 'class.phpmailer.php';

class Mail
{

    private $mailer = null;

    private $conf = array();

    protected function __construct(array $config)
    {
        $this->conf = $config;
        $this->mailer = new \PHPMailer(true);
        $this->setMailerConfig();
    }

    /**
     * 获取单例
     * 
     * 
     * @param array $config
     * @return Ambigous <>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public static function getInstance(array $config)
    {
        static $instance = array();
        $key = md5(var_export($config, true));
        if (! isset($instance[$key])) {
            $instance[$key] = new self($config);
        }
        return $instance[$key];
    }

    /**
     * 设置邮件配置信息
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月31日
     */
    private function setMailerConfig()
    {
        $mail = $this->mailer;
        $mail->IsSMTP();
        $mailConfig = $this->conf;
        
        $mail->CharSet = $mailConfig['CHATSET']; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = $mailConfig['SMTP_AUTH']; //开启认证
        $mail->Port = $mailConfig['PORT'];
        $mail->Host = $mailConfig['MAIL_HOST'];
        $mail->Username = $mailConfig['MAIL_USERNAME'];
        $mail->Password = $mailConfig['MAIL_PASSWORD'];
        //$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could  not execute: /var/qmail/bin/sendmail ”的错误提示
        $mail->AddReplyTo($mailConfig['MAIL_USERNAME'], $mailConfig['MAIL_FROM_NAME']); //回复地址
        $mail->From = $mailConfig['MAIL_USERNAME'];
        $mail->FromName = $mailConfig['MAIL_FROM_NAME'];
        $mail->WordWrap = $mailConfig['WORD_WRAP'];
        $mail->IsHTML(true);
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
    public function send($mailTo, $subject, $content)
    {
        try {
            if (! is_array($mailTo) && preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $mailTo) !== 1) {
                throw new \Exception('邮箱格式不正确', 400);
            } else {
                set_time_limit(30);
                $mail = $this->mailer;
                if (is_array($mailTo)) {
                    foreach ($mailTo as $m) {
                        $mail->AddAddress($m);
                    }
                } else {
                    $mail->AddAddress($mailTo);
                }
                $mail->Subject = $subject;
                $mail->Body = $content;
                //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
                //$mail->AddAttachment("f:/test.png");  //附件
                $res = $mail->Send();
                $this->clear();
                return $res;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 清理相关参数
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月12日
     */
    protected function clear()
    {
        $mail = $this->mailer;
        $mail->clearAddresses();
        $mail->clearAllRecipients();
        $mail->clearAttachments();
        $mail->clearBCCs();
        $mail->clearCCs();
        $mail->clearCustomHeaders();
        $mail->clearReplyTos();
    }
}
