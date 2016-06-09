<?php
/**
 * 一些上传的配置
 */
return array(
    //问题单附件配置
    'QUESTION_ATTACH' => array(
        //阿里云oss存储
        'OSS' => array(
            'maxSize' => 5 * 1024 * 1024, 
            'exts' => array('jpg','jpeg','png','log', 'txt', 'doc','docx', 'xlsx','xls', 'zip', 'rar')), 
        //本地存储
        'LOCAL' => array(
            'maxSize' => 5 * 1024 * 1024, 
            'rootPath' => '../../download/', 
            'savePath' => 'Upload/Question/', 
            'saveName' => array(), 
            'exts' => array('jpg','jpeg','png','log', 'txt', 'doc','docx', 'xlsx', 'xls', 'zip', 'rar'), 
            'autoSub' => true, 
            'subName' => array('upload_sub_name', 'question'))));
