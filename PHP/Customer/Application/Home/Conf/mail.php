<?php
return array(
    'MAIL_CONFIG' => array(
        'CHATSET' => 'UTF-8', 
        'SMTP_AUTH' => true, 
        'PORT' => '25',  //端口
        'MAIL_HOST' => 'smtp.qiye.163.com',  //SMTP服务器地址
        'MAIL_USERNAME' => 'service@ipmacro.com', 
        'MAIL_PASSWORD' => 'uxLVqwZjeqFTNcv@', 
        'MAIL_FROM' => 'service@ipmacro.com', 
        'MAIL_FROM_NAME' => 'VSOONTECH', 
        'WORD_WRAP' => 80), 
    
    //邮件发送优先级,值越大优先级越高
    'FIND_PASSWORD_PRIORITY' => 255, 
    'QUESTION_REPLY_PRIORITY' => 1);