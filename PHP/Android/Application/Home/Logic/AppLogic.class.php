<?php

namespace Home\Logic;

use Think\Model;

/**
 * app逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月20日
 * @version   1.0
 */
class AppLogic extends Model
{

    /**
     * 生成月报
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月20日
     */
    public function createReport($data)
    {
        $sTime = empty($data['s_time']) ? '' : strtotime($data['s_time']);
        $eTime = empty($data['e_time']) ? '' : strtotime($data['e_time'] . ' 23:59:59');
        $reportInfo = $finalStatic = $otherTester = array();
        
        //获取应用名和开发人员
        $where = empty($data['app_name']) ? array('aa.operation' => 'publish') : array(
            'aa.operation' => 'publish', 
            'a.name' => $data['app_name']);
        $appBaseInfo = $this->alias('a')
            ->join('LEFT JOIN tb_app_admin aa ON a.name=aa.name LEFT JOIN tb_user u ON aa.operator=u.user')
            ->field('aa.*,a.app,u.name AS developer')
            ->where($where)
            ->select();
        
        if (! empty($appBaseInfo)) {
            foreach ($appBaseInfo as $k => $v) {
                empty($reportInfo[$v['name']]) && $reportInfo[$v['name']] = array(
                    'app' => $v['app'], 
                    'name' => $v['name'], 
                    'statistics' => array(), 
                    'records' => array());
                $reportInfo[$v['name']]['developer'] = isset($reportInfo[$v['name']]['developer']) ? $reportInfo[$v['name']]['developer'] .
                     '、' . $v['developer'] : $v['developer'];
            }
            
            //获取应用操作记录,分组获取2条记录
            $pkgNames = array_unique(array_column($appBaseInfo, 'name'));
            $where2 = 'asmr.pkg_name IN (' . implode(',', 
                array_map(function ($v)
                {
                    return "'" . $v . "'";
                }, $pkgNames)) . ')';
            
            $subWhere = '';
            if (! empty($sTime)) {
                $where2 .= ' AND asmr.modify_time >=' . $sTime;
                $subWhere .= ' AND asmr2.modify_time >=' . $sTime;
            }
            if (! empty($eTime)) {
                $where2 .= ' AND asmr.modify_time <=' . $eTime;
                $subWhere .= ' AND asmr2.modify_time <=' . $eTime;
            }
            $where2 = $where2 .
                 ' AND 11 > ( SELECT count(1) FROM tb_app_status_modify_record asmr2 INNER JOIN tb_app_publish ap2 ON ( ap2.pkg_name = asmr2.pkg_name AND ap2.version_code = asmr2.version_code ) WHERE asmr2.pkg_name = asmr.pkg_name AND asmr2.id < asmr.id ' .
                 $subWhere . ')';
            
            $modifyRecord = M('AppStatusModifyRecord')->alias('asmr')
                ->join(
                'LEFT JOIN tb_user u ON asmr.modified_by=u.user INNER JOIN tb_app_publish ap ON (asmr.pkg_name=ap.pkg_name AND asmr.version_code=ap.version_code)')
                ->field('asmr.*,u.name AS modifyer')
                ->where($where2)
                ->order('modify_time ASC,pkg_name DESC')
                ->select();
            
            if (! empty($modifyRecord)) {
                foreach ($modifyRecord as $k2 => $v2) {
                    //统计各个应用的数据
                    $reportInfo[$v2['pkg_name']]['records'][] = $v2;
                }
                
                //获取每个应用的统计数据
                $pkgStatics = D('AppStatusModifyRecord', 'Logic')->getPkgNameStatics($pkgNames, $sTime, $eTime);
                foreach ($reportInfo as $pkg => $info) {
                    $reportInfo[$pkg]['statistics']['test'] = isset($pkgStatics['test'][$pkg]) ? $pkgStatics['test'][$pkg]['count'] : 0;
                    $reportInfo[$pkg]['statistics']['not_pass'] = isset($pkgStatics['not_pass'][$pkg]) ? $pkgStatics['not_pass'][$pkg]['count'] : 0;
                    $reportInfo[$pkg]['statistics']['regress'] = isset($pkgStatics['regress'][$pkg]) ? $pkgStatics['regress'][$pkg]['count'] : 0;
                    $reportInfo[$pkg]['statistics']['one_pass'] = isset($pkgStatics['one_pass'][$pkg]) ? $pkgStatics['one_pass'][$pkg]['count'] : 0;
                    $reportInfo[$pkg]['statistics']['back'] = isset($pkgStatics['back'][$pkg]) ? $pkgStatics['back'][$pkg]['count'] : 0;
                }
                //获取总的统计数据
                $finalStatic = D('AppStatusModifyRecord', 'Logic')->getSumRecordStatics($pkgNames, $sTime, $eTime);
                //获取其他没有参与测试的用户统计
                $otherTester = $this->getOtherTesterStatic(array_keys($finalStatic));
                
                //重置数组key??
                unset($data, $sTime, $eTime, $where, $where2, $appBaseInfo, $modifyRecord);
            }
        }
        return array('app' => $reportInfo, 'total' => array_merge($finalStatic, $otherTester));
    }

    /**
     * 获取其他没参与测试的用户初始化数据
     * 
     * 
     * @param array $tester  排除这些用户
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月29日
     */
    public function getOtherTesterStatic($tester)
    {
        $where = empty($tester) ? array('tester' => 'true') : array('user' => array('notin', $tester), 'tester' => 'true');
        return M('User')->where($where)
            ->field('DISTINCT user,name,0 AS pass,0 AS not_pass,0 AS regress,0 AS back')
            ->index('user')
            ->select();
    }

    /**
     * 获取app和对应包名
     * 
     * 
     * @param unknown $name 检索字段
     * @param unknown $field
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月12日
     */
    public function getAppName($name, $field)
    {
        $field = empty($field) ? 'app,name' : $field;
        $where = empty($name) ? true : array('app' => array('LIKE', '%' . $name . '%'));
        return M('App')->field($field)
            ->where($where)
            ->order('id DESC')
            ->select();
    }

    /**
     * 发布渠道包
     * 
     * 
     * @param unknown $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function publishChannel($data)
    {
        $user = session('androidIsLogin');
        //1.判断是否有上传文件
        $errorCode = $_FILES['channel_apk']['error'];
        $uploadError = array(
            UPLOAD_ERR_INI_SIZE => '上传的文件超过了服务器配置的最大值', 
            UPLOAD_ERR_FORM_SIZE => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值', 
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传', 
            UPLOAD_ERR_NO_FILE => '没有文件被上传', 
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹', 
            UPLOAD_ERR_CANT_WRITE => '文件写入失败');
        if ($errorCode > 0) {
            throw new \LogicException(isset($uploadError[$errorCode]) ? $uploadError[$errorCode] : '未知错误', C('BAD_REQUEST'));
        } else {
            //判断关联的发布appid和备注是否存在
            $channelName = $data['channel_name'];
            $appPublishId = $data['app_id'];
            $desc = $data['desc'];
            if ($appPublishId <= 0) {
                throw new \LogicException('请选择关联app', C('BAD_REQUEST'));
            } elseif (empty($channelName)) {
                throw new \LogicException('请填写渠道包名称', C('BAD_REQUEST'));
            } elseif (empty($desc)) {
                throw new \LogicException('请填写版本描述', C('BAD_REQUEST'));
            }
            
            $apkTmpPath = $_FILES['channel_apk']['tmp_name'];
            //2.解压
            $zip = new \ZipArchive();
            $zipRes = $zip->open($apkTmpPath);
            if ($zipRes !== true) {
                $zipError = array(
                    $zip::ER_EXISTS => "文件已经存在", 
                    $zip::ER_INCONS => "Zip归档文件不一致", 
                    $zip::ER_MEMORY => "分配内存失败", 
                    $zip::ER_NOENT => "没有这样的文件", 
                    $zip::ER_NOZIP => "不是一个zip归档", 
                    $zip::ER_OPEN => "无法打开文件", 
                    $zip::ER_READ => "读取错误");
                $zip->close();
                throw new \LogicException(isset($zipError[$zipRes]) ? '解压出错:' . $zipError[$zipRes] : '解压出现未知错误，错误码' . $zipRes, C(
                    'BAD_REQUEST'));
            }
            
            //3.读取appinfo.xml
            $appInfoXml = $zip->getFromName('assets/appinfo.xml');
            if (empty($appInfoXml)) {
                $zip->close();
                throw new \LogicException('上传失败，丢失文件assets/appinfo.xml', C('BAD_REQUEST'));
            } else {
                $appInfoArr = simplexml_load_string($appInfoXml);
                if (empty($appInfoArr)) {
                    throw new \LogicException('文件assets/appinfo.xml内容有误，解析失败', C('BAD_REQUEST'));
                }
                $appInfoArr = json_decode(json_encode((array) $appInfoArr, JSON_UNESCAPED_UNICODE), true);
            }
            
            //4.检测必填参数
            if (empty($appInfoArr['package_name']) || empty($appInfoArr['version_name']) || empty($appInfoArr['channel_id']) ||
                 empty($appInfoArr['version_code']) || empty($appInfoArr['min_sdk']) || empty($appInfoArr['@attributes']['name']) ||
                 empty($appInfoArr['git_branch']) || empty($appInfoArr['git_commit_id']) || empty($appInfoArr['system_app'])) {
                $zip->close();
                throw new \LogicException('文件assets/appinfo.xml节点缺失', C('BAD_REQUEST'));
            }
            //获取应用包名
            //渠道包获取包名直接读取appinfo.xml
            /* $appObj = new \Org\ApkParser();
            $appObj->open($apkTmpPath);
            $pkgName = $appObj->getPackage();
            if (empty($pkgName)) {
                throw new \LogicException('获取安卓包名失败', C('BAD_REQUEST'));
            } else {
                $appInfoArr['package_name'] = $pkgName;
            } */
            
            //5.判断和关联的app版本号是否一致
            $app = M('appPublish')->alias('a')
                ->join('tb_app app ON a.pkg_name=app.name')
                ->field('a.pkg_name,a.version_code,a.version_name,app.app')
                ->where(array('a.id' => $appPublishId))
                ->find();
            if (empty($app)) {
                throw new \LogicException('公版app包不存在', C('BAD_REQUEST'));
            } else {
                //渠道包版本号和关联app版本号一致
                if ($app['version_code'] != $appInfoArr['version_code'] || $app['version_name'] != $appInfoArr['version_name']) {
                    throw new \LogicException('渠道包版本号必须与关联的公版app版本号一致', C('BAD_REQUEST'));
                }
                //同一版本号&同一渠道号记录只能有一条
                $isExists = M('AppChannelPublish')->where(
                    array(
                        'app_publish_id' => $appPublishId, 
                        'version_code' => $appInfoArr['version_code'], 
                        'channel_id' => $appInfoArr['channel_id']))->find();
                if ($isExists) {
                    throw new \LogicException('渠道包已经存在', C('BAD_REQUEST'));
                }
            }
            
            //6.检测权限
            // 超级管理员、系统管理员可直接发布所有渠道包，开发用户仅能发布指定给自己的APP所关联的渠道包，其他用户不能发布
            if (! isAdmin(false)) {
                $isDeveloper = M('AppAdmin')->where(
                    array('operation' => 'publish', 'name' => $app['pkg_name'], 'operator' => $user['user']))->find();
                if (empty($isDeveloper)) {
                    throw new \LogicException('未指定该应用，不能发布该应用的渠道包', C('UNAUTHORIZED'));
                }
            }
            
            //7.上传
            //上传至oss存储
            $formData = array(
                "apkFile" => array(
                    "extension" => "apk", 
                    "md5_file" => $appInfoArr['package_name'] . "/" . $appInfoArr['@attributes']['name'] . '_' . $appInfoArr['version_code'] .
                         '_' . $appInfoArr['channel_id'] . '_' . md5_file($apkTmpPath), 
                        "filepath" => $apkTmpPath, 
                        "size" => filesize($apkTmpPath)));
            require_once '../Base/Ossupclass/OssBase.class.php';
            $base = new \base(C('OSS_ACCESS_ID'), C('OSS_ACCESS_KEY'), C('OSS_ENDPOINT'), C('OSS_BUCKET'));
            $res = $base->coveUploadFile($formData);
            $appInfoArr['path'] = $res['apkFile']['oss'];
            
            //上传到本地，判断是否混淆、反编译，然后删除本地apk
            $config = array(
                'savePath' => '', 
                'rootPath' => C('LOCALHOST_PATH_ADDR') . 'temp/', 
                'exts' => array('apk'), 
                'autoSub' => false, 
                'replace' => true, 
                'saveName' => $appInfoArr['@attributes']['name'] . '_' . $appInfoArr['version_code']);
            
            $upload = new \Think\Upload($config); // 实例化上传类
            $info = $upload->uploadOne($_FILES['channel_apk']);
            if (! $info) {
                $zip->close();
                throw new \LogicException($upload->getError(), C('BAD_REQUEST'));
            }
            
            //是否混淆
            $commandPath = 'Apk/jadx/bin/jadx';
            $savename = explode('.', $appInfoArr['@attributes']['name']);
            $commandSavePath = C('LOCALHOST_PATH_ADDR') . 'temp/' . $savename[0];
            
            if (file_exists($commandSavePath)) {
                removeDir($commandSavePath);
            }
            
            $commandFilePath = C('LOCALHOST_PATH_ADDR') . 'temp/' . $info['savename'];
            
            if (! file_exists($commandFilePath)) {
                throw new \LogicException('上传失败，apk反编译文件不存在！', C('BAD_REQUEST'));
            }
            exec($commandPath . ' -d ' . $commandSavePath . ' ' . $commandFilePath);
            
            //删除本地APK
            if (file_exists($commandFilePath)) {
                unlink($commandFilePath);
            }
            //判断是否反编译
            $xmlFilePath = $commandSavePath . '/com/linkin/proguard/Mark.java';
            if (file_exists($xmlFilePath)) {
                if (file_exists($commandSavePath)) {
                    removeDir($commandSavePath);
                }
                $zip->close();
                throw new \LogicException('上传失败，apk未混淆！', C('BAD_REQUEST'));
            } else {
                $appInfoArr['mixed'] = 'true';
            }
            //签名信息
            $zip->extractTo($commandSavePath, 'META-INF/CERT.RSA');
            $zip->close();
            $commandPath = 'keytool -printcert -file ';
            $commandFilePath = $commandSavePath . '/META-INF/CERT.RSA';
            
            if (! file_exists($commandFilePath)) {
                if (file_exists($commandSavePath)) {
                    removeDir($commandSavePath);
                }
                throw new \LogicException('上传失败，签名信息文件不存在！', C('BAD_REQUEST'));
            }
            exec($commandPath . "'" . $commandFilePath . "'", $signatureMsg, $returnVal);
            if (! empty($signatureMsg)) {
                foreach ($signatureMsg as $key => $value) {
                    if (strstr($value, 'Owner: ')) {
                        $signatureArr['owner'] = trim(str_replace('Owner:', '', $value));
                    } elseif (strstr($value, 'Issuer:')) {
                        $signatureArr['issuer'] = trim(str_replace('Issuer:', '', $value));
                    } elseif (strstr($value, 'Serial number:')) {
                        $signatureArr['serialNumber'] = trim(str_replace('Serial number:', '', $value));
                    } elseif (strstr($value, 'Valid from:')) {
                        $signatureArr['validFrom'] = trim(str_replace('Valid from:', '', $value));
                    } elseif (strstr($value, 'MD5:')) {
                        $signatureArr['certificateFingerprints']['md5'] = trim(str_replace('MD5:', '', $value));
                    } elseif (strstr($value, 'SHA1:')) {
                        $signatureArr['certificateFingerprints']['sha1'] = trim(str_replace('SHA1:', '', $value));
                    } elseif (strstr($value, 'SHA256:')) {
                        $signatureArr['certificateFingerprints']['sha256'] = trim(str_replace('SHA256:', '', $value));
                    }
                }
            } else {
                
                if (file_exists($commandSavePath)) {
                    removeDir($commandSavePath);
                }
                throw new \LogicException('上传失败，读取签名信息失败！', C('BAD_REQUEST'));
            }
            if (empty($signatureArr)) {
                if (file_exists($commandSavePath)) {
                    removeDir($commandSavePath);
                }
                throw new \LogicException('上传失败，没有签名信息！', C('BAD_REQUEST'));
            }
            if (file_exists($commandSavePath)) {
                removeDir($commandSavePath);
            }
            $appInfoArr['signature'] = ! empty($signatureArr) ? json_encode($signatureArr, JSON_UNESCAPED_UNICODE) : '[]';
            
            //8.验证发布规则
            D('AppPublishRule', 'Logic')->checkRules($appInfoArr);
            
            //9.保存
            $channelData = array(
                "app_publish_id" => $appPublishId, 
                "channel_name" => $channelName, 
                "name" => $app['app'], 
                "version_code" => $appInfoArr['version_code'], 
                "version_name" => $appInfoArr['version_name'], 
                "version_desc" => $desc, 
                "pkg_name" => $appInfoArr['package_name'], 
                "git_commit_id" => $appInfoArr['git_commit_id'], 
                "channel_id" => $appInfoArr['channel_id'], 
                "system_app" => $appInfoArr['system_app'], 
                "mixed" => $appInfoArr['mixed'], 
                "signature" => $appInfoArr['signature'], 
                "min_sdk" => $appInfoArr['min_sdk'], 
                "publisher" => $user['user'], 
                "pub_time" => time(), 
                "path" => $appInfoArr['path'], 
                "git_branch" => $appInfoArr['git_branch']);
            return M('AppChannelPublish')->add($channelData);
        }
    }

    /**
     * 删除渠道包
     * 
     * 
     * @param unknown $ids
     * @throws \LogicException
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function delChannelApp($ids)
    {
        if (empty($ids) || ! is_array($ids)) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        } else {
            $appChannelPublishMod = M('AppChannelPublish');
            $authCheck = $this->checkDelChannelApp($ids);
            $user = session('androidIsLogin');
            $oldList = $appChannelPublishMod->field("*,'" . $user['user'] . "' AS delete_user," . time() . " AS delete_time")
                ->where(array('id' => array('IN', $ids)))
                ->select();
            if (!empty($oldList) && false !== $appChannelPublishMod->where(array('id' => array('IN', $ids)))->delete()) {
                M('AppChannelPublishHistory')->addAll($oldList);
                return true;
            } else {
                throw new \LogicException('删除渠道包失败', C('UNKNOWN_ERROR'));
            }
        }
    }

    /**
     * 检测是否有删除和发布渠道包的权限
     * 系统管理员可以删除和发布、开发用户只能删除和发布指派给自己的应用
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年6月1日
     */
    protected function checkDelChannelApp($ids)
    {
        if (empty($ids) || ! is_array($ids)) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        } else {
            $loginUser = session('androidIsLogin');
            if (isAdmin(false)) {
                return true;
            } else {
                //删除、$id为渠道包id数组
                $pkgNames = M('AppChannelPublish')->alias('acp')
                    ->join('tb_app_publish ap ON ap.id=acp.app_publish_id')
                    ->field('DISTINCT ap.pkg_name')
                    ->where(array('acp.id' => array('IN', $ids)))
                    ->select();
                if (empty($pkgNames)) {
                    throw new \LogicException('找不到渠道包所对应的公版APP包', C('BAD_REQUEST'));
                } else {
                    $pkgNames = array_column($pkgNames, 'pkg_name');
                    $appAdmin = M('AppAdmin')->field('DISTINCT name')
                        ->where(array('name' => array('IN', $pkgNames), 'operator' => $loginUser['user'], 'operation' => 'publish'))
                        ->getField('name', true);
                    if (count($pkgNames) == count($appAdmin)) {
                        return true;
                    } else {
                        throw new \LogicException('未指定该应用，不能删除该应用的渠道包', C('BAD_REQUEST'));
                    }
                }
            }
        }
    }

    /**
     * 获取app修改状态列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月26日
     */
    public function appModifyStatus()
    {
        return D('AppPublish')->getAppModifyPassTest();
    }
}
    
