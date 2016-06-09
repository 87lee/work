<?php

namespace Home\Model;

/**
 * 邮件队列模型
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月11日
 * @version   1.0
 */
class MailModel extends \Think\Model
{

    /**
     * 发送邮件
     * 
     * 
     * @param unknown $num
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月11日
     */
    public function send($num = 10)
    {
        $mailList = $this->where(array('status' => 0))
            ->field('mail_id,mail_to,subject,body')
            ->where('fail_num <=5 ')
            ->order('priority DESC,mail_id DESC')
            ->limit($num)
            ->select();
        
        if (! empty($mailList)) {
            import('Vendor.PHPMailer.Mail');
            $mailer = \Mail::getInstance(C('MAIL_CONFIG'));
            $failMailIds = array();
            
            foreach ($mailList as $k => $v) {
                $result = $mailer->send($v['mail_to'], htmlspecialchars_decode($v['subject']), htmlspecialchars_decode($v['body']));
                if (true === $result) {
                    //发送成功，删除
                    $this->where(array('mail_id' => $v['mail_id']))->delete();
                } else {
                    //记录失败id
                    $failMailIds[] = $v['mail_id'];
                    $this->where(array('mail_id' => $v['mail_id']))->setField('last_fail_msg', $result);
                }
            }
            
            if (! empty($failMailIds)) {
                $this->where(array('mail_id' => array('IN', $failMailIds)))->setInc('fail_num');
            }
            
            return array('total_count' => count($mailList), 'error_cout' => count($failMailIds));
        } else {
            return false;
        }
    }

    /**
     * 清理7天前的邮件队列和发送失败超过5次的记录
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月11日
     */
    public function clean()
    {
        $where['_complex'] = array('add_time' => array('lt', time() - 3 * 24 * 3600), 'fail_num' => array('gt', 5), '_logic' => 'OR');
        $this->where($where)->delete();
    }
}