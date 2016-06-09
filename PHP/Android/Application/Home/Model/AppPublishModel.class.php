<?php
	namespace Home\Model;
	class AppPublishModel extends \Think\Model
	{
		protected $tableName = 'app_publish';
		// protected $connection = 'DB_DESKTOP';
		protected $_map = array(
			'versionName' =>'version_name',
			'versionCode' =>'version_code',
			'pkgName'  =>'pkg_name',
			'versionDesc'  =>'version_desc',
			'gitCommitId'  =>'git_commit_id',
			'channelId'  =>'channel_id',
			'systemApp' =>'system_app',
			'minSdk'  =>'min_sdk',
			'pubTime'  =>'pub_time',
			'gitBranch' =>'git_branch',
			'passTest'  =>'pass_test',
			'relyModule'  =>'rely_module',
		);
		/*protected $_validate = array(
			array('name','','用户名已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);*/
		/**
		 * [publishApp description]
		 * @param  [type] $xmlData [description]
		 * @param  [type] $put     [description]
		 * @return [type]          [description]
		 */
		public function publishApp($put)
		{

			//set_time_limit(0);

			$filename = $_FILES['apkFile']['tmp_name'];

			if (!file_exists($filename)) {
				result('上传失败，没有上传apk文件');
			}

			//测试上传appinfo.xml
			// $xmlArr = simplexml_load_file($filename);
			$zip = new \ZipArchive;
			if ($zipRes = $zip->open($filename) !== TRUE) {
				$zip->close();
				switch($zipRes){
				            case \ZipArchive::ER_EXISTS:
				                $ErrMsg = "文件已经存在";
				                break;
				            case \ZipArchive::ER_INCONS:
				                $ErrMsg = "Zip归档文件不一致";
				                break;

				            case \ZipArchive::ER_MEMORY:
				                $ErrMsg = "分配内存失败";
				                break;

				            case \ZipArchive::ER_NOENT:
				                $ErrMsg = "没有这样的文件";
				                break;

				            case \ZipArchive::ER_NOZIP:
				                $ErrMsg = "不是一个zip归档";
				                break;

				            case \ZipArchive::ER_OPEN:
				                $ErrMsg = "无法打开文件";
				                break;

				            case \ZipArchive::ER_READ:
				                $ErrMsg = "读取错误";
				                break;

				            case \ZipArchive::ER_SEEK:
				                $ErrMsg = "搜索错误";
				                break;

				            default:
				                $ErrMsg = "未知原因".$zipRes;
				                break;
				}
				$zip->close();
		     		result('上传失败，解压文件：'.$ErrMsg);
		     	}

		      	$xml = $zip->getFromName('assets/appinfo.xml');

		      	if (!empty($xml)) {
		      		$xmlArr = simplexml_load_string($xml);
		      	}else{
		      		$zip->close();
		      		result('上传失败，丢失文件assets/appinfo.xml');
		      	}
		      	if (empty($xmlArr)) {
		      		$zip->close();
		     		result('上传失败，assets/appinfo.xml内容出错');
		     	}
		     	$xmlArr = json_decode( json_encode( (array)$xmlArr,JSON_UNESCAPED_UNICODE), true);
	     		if (empty($put['versionDesc'])) {
				$xmlArr['versionDesc'] = '';
			}else{
				$xmlArr['versionDesc'] = $put['versionDesc'];
			}
			if (empty($xmlArr['package_name']) || empty($xmlArr['version_name']) || empty($xmlArr['channel_id'])  || empty($xmlArr['version_code']) || empty($xmlArr['min_sdk']) || empty($xmlArr['@attributes']['name']) || empty($xmlArr['git_branch']) || empty($xmlArr['git_commit_id']) || empty($xmlArr['system_app'])) {
				$zip->close();
				result('上传失败，app模块appinfo.xml节点缺失');
			}
			$appObj  = new \Org\ApkParser();
			$appObj->open($filename);
			$pkgName = $appObj->getPackage();    // 应用包名
			if (empty($pkgName)) {
				$zip->close();
				result('上传失败，获取安卓包名失败');
			}

			//检测应用是否存在
			$app=M('App')->where(array('name'=>$pkgName))->find();
			if(empty($app)){
				$zip->close();
			    result("应用未录入，请先录入应用");
			}

			if ($res = $this->where(' version_code = "%s" AND version_name = "%s"  AND pkg_name = "%s" ' ,array($xmlArr['version_code'],$xmlArr['version_name'],$pkgName))->find()) {
			    $zip->close();
			    result("上传失败，版本已存在！");
			}

			//检测pkgName版本是否处于待测试状态、处于待测试，禁止上传
			$isTesting=M('AppPublish')->field('pass_test,version_code')->where(array('pkg_name'=>$pkgName))->order('id DESC')->find();
			if(!empty($isTesting) && $isTesting['pass_test'] == 'test'){
				$zip->close();
			    	result('上传失败，该应用存在待测试版本，请勿上传新版本');
			}

			$xmlArr['package_name'] = $pkgName;
			//查看权限
			if(!isAdmin(false)){
			    $isAppPublish = D('AppAdmin')->isAppPublish($xmlArr['package_name']);
			    if (empty($isAppPublish)) {
			        $zip->close();
			        result('auth');
			    }
			}
			
			//检查子节点内容
			if (!empty($xmlArr['dependencies']['dependency'])) {
				foreach ($xmlArr['dependencies']['dependency'] as $key => $value) {
					if ( !empty($value['@attributes']['name']) && !empty($value['package_name']) && !empty($value['version_name']) )  {
						$dependencyItem[] = array(
							'module'=>$value['@attributes']['name'],
							'version_name'=>$value['version_name'],
							'pkg_name'=>$value['package_name']
						);
					}
				}
			}
			//上传oss
			$formData = array(
				"apkFile" => array(
			    		"extension" =>"apk",
					"md5_file" => $xmlArr['package_name'] ."/".$xmlArr['@attributes']['name'].'_'.$xmlArr['version_code'] .'_'.$xmlArr['channel_id'] .'_'.md5_file($filename) ,
			    		"filepath" => $filename,
				    	"size" => filesize($filename)
			  	)
			);

			require_once '../Base/Ossupclass/OssBase.class.php';
			$base  = new \base(C('OSS_ACCESS_ID'),C('OSS_ACCESS_KEY'),C('OSS_ENDPOINT'),C('OSS_BUCKET'));
			$res = $base->coveUploadFile($formData);
			$xmlArr['path'] = $res['apkFile']['oss'];

			//保存本地app
			$config = array(
				// 'maxSize'    =>    3*1024*1024,
				'savePath'   =>    '',
				'rootPath'   =>    C('LOCALHOST_PATH_ADDR') . 'temp/',
				'exts'       =>    array('apk'),
				'autoSub'    =>    false,
				'replace'=>true,
				'saveName'=> $xmlArr['@attributes']['name'].'_'.$xmlArr['version_code'],
			);

			$upload = new \Think\Upload($config);// 实例化上传类
			$info   =   $upload->uploadOne($_FILES['apkFile']);

			if(!$info) {
				// 上传错误提示错误信息
				$zip->close();
				result($upload->getError());
			}

			//是否混淆
			$commandPath = 'Apk/jadx/bin/jadx';
			$savename = explode( '.',$xmlArr['@attributes']['name'] );
			$commandSavePath = C('LOCALHOST_PATH_ADDR') . 'temp/'.$savename[0];

			if (file_exists($commandSavePath)) {
				removeDir($commandSavePath);
			}

			$commandFilePath = C('LOCALHOST_PATH_ADDR') . 'temp/'.$info['savename'];

			if (!file_exists($commandFilePath)) {
				$zip->close();
				result('上传失败，apk反编译文件不存在！');
			}

			$commandBool = exec($commandPath.' -d '.$commandSavePath.' ' . $commandFilePath );

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
				result('上传失败，apk未混淆！');
			}else{
				$xmlArr['mixed'] = 'true';
			}

			/*

			$getAndroidManifest = file_get_contents($commandSavePath.'/AndroidManifest.xml');
			$searchAndroid = 'xmlns:"http://schemas.android.com/apk/res/android"';
			$getAndroidManifest = str_replace($searchAndroid, '', $getAndroidManifest);
			$getAndroidManifest = @simplexml_load_string($getAndroidManifest);
			 $getAndroidManifest = json_decode(json_encode($getAndroidManifest),TRUE);
			 $linkinChannel = false;
			 if (!empty($getAndroidManifest['application']['meta-data'])) {
			 	foreach ($getAndroidManifest['application']['meta-data'] as  $value) {
			 		if ($value['@attributes']['name'] == 'LINKIN_CHANNEL') {
			 			$linkinChannel = true;
			 			if (isset($value['@attributes']['value'])) {
			 				$xmlArr['channel_id'] = $value['@attributes']['value'];
			 			}else{
			 				result('上传失败，AndroidManifest.xml文件没有获取渠道号！');
			 			}
			 		}
			 	}
			 }

			if (!$linkinChannel) {
			 	result('上传失败，AndroidManifest.xml文件没有获取渠道号！');
			}

			 */

		      	//签名信息
		      	$zip->extractTo($commandSavePath,'META-INF/CERT.RSA');
		      	$zip->close();
			// $commandPath = '/usr/lib/jvm/jdk7/bin/keytool -printcert -file ';
			$commandPath = 'keytool -printcert -file ';
			$commandFilePath = $commandSavePath.'/META-INF/CERT.RSA';

			if (!file_exists($commandFilePath)) {
				if (file_exists($commandSavePath)) {
					removeDir($commandSavePath);
				}
				result('上传失败，签名信息文件不存在！');
			}


			exec($commandPath."'".$commandFilePath."'" ,$signatureMsg ,$returnVal);
			/*exec('keytool -help' ,$signatureMsg ,$returnVal);

			var_dump($signatureMsg);
			var_dump($returnVal);
			die;*/
			if (!empty($signatureMsg)) {
				foreach ($signatureMsg as $key => $value) {
					if (strstr($value,'Owner: ')) {
						$signatureArr['owner'] =trim(str_replace('Owner:','',$value)) ;
					}elseif (strstr($value,'Issuer:')) {
						$signatureArr['issuer'] =trim(str_replace('Issuer:','',$value)) ;
					}elseif (strstr($value,'Serial number:')) {
						$signatureArr['serialNumber'] =trim(str_replace('Serial number:','',$value)) ;
					}elseif (strstr($value,'Valid from:')) {
						$signatureArr['validFrom'] =trim(str_replace('Valid from:','',$value)) ;
					}elseif (strstr($value,'MD5:')) {
						$signatureArr['certificateFingerprints']['md5'] =trim(str_replace('MD5:','',$value)) ;
					}elseif (strstr($value,'SHA1:')) {
						$signatureArr['certificateFingerprints']['sha1'] = trim(str_replace('SHA1:','',$value));
					}elseif (strstr($value,'SHA256:')) {
						$signatureArr['certificateFingerprints']['sha256'] = trim(str_replace('SHA256:','',$value)) ;
					}
				}
			}else{
				if (file_exists($commandSavePath)) {
					removeDir($commandSavePath);
				}
				result('上传失败，读取签名信息失败！');
			}

			if (empty($signatureArr)) {
				if (file_exists($commandSavePath)) {
					removeDir($commandSavePath);
				}
				result('上传失败，没有签名信息！'.$signatureMsg[0]);
			}
			if (file_exists($commandSavePath)) {
				removeDir($commandSavePath);
			}
			$xmlArr['signature'] = !empty($signatureArr)?json_encode($signatureArr,JSON_UNESCAPED_UNICODE):'[]';

			//判断发布规则
			$publishRule = D('AppPublishRule')->getAll();
			if (!empty($publishRule)) {

				foreach ($publishRule as $key => $value) {

					//是否限制APP
					$specifiedApp = json_decode($value['specifiedApp']);
					if (!empty($specifiedApp)) {
						$IsSpecifiedApp = true;
						foreach ($specifiedApp as  $v) {
							if ($v == $xmlArr['package_name']) {
								$IsSpecifiedApp = false;
								break;
							}
						}
						if ($IsSpecifiedApp) {
							continue;
						}
					}

					//是否为子节点设置规则
					if (!empty($value['attrNode'])) {

						$attrNode = explode('.', $value['attrNode']);
						if (!empty($attrNode)) {
							$checkNode = $xmlArr;
							foreach ($attrNode as  $v) {
								$dependency =  $v;
								if (isset($checkNode[$v])) {
									$checkNode = $checkNode[$v];
								}else{
									result('上传失败，配置文件没有'.$v.'子节点！');
								}
							}
						}

						/*if (empty($xmlArr['dependencies']['dependency'])) {
							result('上传失败，配置文件没有dependencies子节点！');
						}*/

						if (!empty($checkNode[0]) ) {
							foreach ($checkNode as $k => $v) {
								if (!empty($value['attrName'])) {
									if ($v['@attributes'][$value['attrName']] != $value['attrValue']) {
										continue;
									}
								}
								if (empty($v[$value['column']])) {
									result('上传失败，配置文件没有'.$dependency.'子节点'.$value['column'].'字段！');
								}
								$this->checkAppPublishRule($v,$value,$value['attrNode'] . '.base');
							}
						}else{
							if (!empty($value['attrName'])) {
								if ($checkNode['@attributes'][$value['attrName']] != $value['attrValue']) {
									continue;
								}
							}
							if (empty($checkNode[$value['column']])) {
								result('上传失败，配置文件没有'.$dependency.'子节点'.$value['column'].'字段！');
							}
							$this->checkAppPublishRule($checkNode,$value,$value['attrNode'] . '.base');
						}

					}else{
						if (!empty($value['attrName'])) {
							if ($xmlArr['@attributes'][$value['attrName']] != $value['attrValue']) {
								continue;
							}
						}
						if (empty($xmlArr[$value['column']])) {
							result('上传失败，配置文件没有'.$value['column'].'字段！');
						}

						$this->checkAppPublishRule($xmlArr,$value,'apk');
					}

				}
			}
			$user = session('androidIsLogin');

			$options = array(
				//"name"=>$xmlArr['@attributes']['name'],
			        "name"=>$app['app'],
				"version_code"=>$xmlArr['version_code'],
				"version_name"=>$xmlArr['version_name'],
				"version_desc"=>$xmlArr['versionDesc'],
				"pkg_name"=>$xmlArr['package_name'],
				"git_commit_id"=>$xmlArr['git_commit_id'],
				"channel_id"=>$xmlArr['channel_id'],
				"system_app"=>$xmlArr['system_app'],
				"mixed"=>$xmlArr['mixed'],
				"signature"=>!empty($xmlArr['signature'])?$xmlArr['signature']:'[]',
				"min_sdk"=>$xmlArr['min_sdk'],
				"publisher"=>$user['user'],
				"pass_test"=>'test',
				"pub_time"=>time(),
				"path"=>$xmlArr['path'],
				"git_branch"=>$xmlArr['git_branch'],
				"rely_module"=>!empty($dependencyItem)?json_encode($dependencyItem,JSON_UNESCAPED_UNICODE):'[]',
			);

			if ($id = $this->add($options)) {
				$statusOptions = array(
					"pkgName"=>$xmlArr['package_name'],
					"versionCode"=>$xmlArr['version_code'],
					"versionName"=>$xmlArr['version_name'],
					"passTest"=>'',
					"publisher"=>$user['user'],
				);
				//记录修改状态
				D('AppStatusModifyRecord')->addStatusRecord($statusOptions,'test');
			}

			return $id;
		}
		public function checkAppPublishRule($xmlArr,$value,$dependency)
		{
			$msg = '上传失败，'. $dependency .'的字段'.$value['column'].'不符合发布规则！';

			if (!empty($value['operator'])) {
				switch ($value['condition']) {
					case '==':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr[$value['column']]) + floatval($value['param']) != floatval($value['value'])) {
									result($msg);
								}
								break;
							case '-':
								if (floatval($xmlArr[$value['column']]) - floatval($value['param']) != floatval($value['value'])) {
									result($msg);
								}
								break;
							case '*':
								if (floatval($xmlArr[$value['column']]) * floatval($value['param']) != floatval($value['value'])) {
									result($msg);
								}
								break;
							case '/':
								if (floatval($xmlArr[$value['column']]) / floatval($value['param']) != floatval($value['value'])) {
									result($msg);
								}
								break;
							case '%':

								if (floatval($xmlArr[$value['column']]) % floatval($value['param']) != floatval($value['value'])) {
									result($msg);
								}
								break;
							default:
								result($msg);
								break;
						}
						break;
					case '!=':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr[$value['column']]) + floatval($value['param']) == floatval($value['value'])) {
									result($msg);
								}
								break;
							case '-':
								if (floatval($xmlArr[$value['column']]) - floatval($value['param']) == floatval($value['value'])) {
									result($msg);
								}
								break;
							case '*':
								if (floatval($xmlArr[$value['column']]) * floatval($value['param']) == floatval($value['value'])) {
									result($msg);
								}
								break;
							case '/':
								if (floatval($xmlArr[$value['column']]) / floatval($value['param']) == floatval($value['value'])) {
									result($msg);
								}
								break;
							case '%':
								if (floatval($xmlArr[$value['column']]) % floatval($value['param']) == floatval($value['value'])) {
									result($msg);
								}
								break;
							default:
								result($msg);
								break;
						}
						break;
					case '>=':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr[$value['column']]) + floatval($value['param']) < floatval($value['value'])) {
									result($msg);
								}
								break;
							case '-':
								if (floatval($xmlArr[$value['column']]) - floatval($value['param'])  <  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '*':
								if (floatval($xmlArr[$value['column']]) * floatval($value['param'])  <  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '/':
								if (floatval($xmlArr[$value['column']]) / floatval($value['param'])  <  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '%':
								if (floatval($xmlArr[$value['column']]) % floatval($value['param']) < floatval($value['value'])) {
									result($msg);
								}
								break;
							default:
								result($msg);
								break;
						}
						break;
					case '<=':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr[$value['column']]) + floatval($value['param']) > floatval($value['value'])) {
									result($msg);
								}
								break;
							case '-':
								if (floatval($xmlArr[$value['column']]) - floatval($value['param'])  >  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '*':
								if (floatval($xmlArr[$value['column']]) * floatval($value['param'])  >  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '/':
								if (floatval($xmlArr[$value['column']]) / floatval($value['param'])  >  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '%':
								if (floatval($xmlArr[$value['column']]) % floatval($value['param'])   > floatval($value['value'])) {
									result($msg);
								}
								break;
							default:
								result($msg);
								break;
						}
						break;
					case '>':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr[$value['column']]) + floatval($value['param']) <= floatval($value['value'])) {
									result($msg);
								}
								break;
							case '-':
								if (floatval($xmlArr[$value['column']]) - floatval($value['param'])  <=  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '*':
								if (floatval($xmlArr[$value['column']]) * floatval($value['param'])  <=  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '/':
								if (floatval($xmlArr[$value['column']]) / floatval($value['param'])  <=  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '%':
								if (floatval($xmlArr[$value['column']]) % floatval($value['param']) <= floatval($value['value'])) {
									result($msg);
								}
								break;
							default:
								result($msg);
								break;
						}
						break;
					case '<':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr[$value['column']]) + floatval($value['param']) >= floatval($value['value'])) {
									result($msg);
								}
								break;
							case '-':
								if (floatval($xmlArr[$value['column']]) - floatval($value['param'])  >= floatval($value['value'])) {
									result($msg);
								}
								break;
							case '*':
								if (floatval($xmlArr[$value['column']]) * floatval($value['param'])  >=  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '/':
								if (floatval($xmlArr[$value['column']]) / floatval($value['param'])  >=  floatval($value['value'])) {
									result($msg);
								}
								break;
							case '%':
								if (floatval($xmlArr[$value['column']]) % floatval($value['param']) >= floatval($value['value'])) {
									result($msg);
								}
								break;
							default:
								result($msg);
								break;
						}
						break;
					default:
						result($msg);
						break;
				}
			}else{
				switch ($value['condition']) {
					case '==':
						if ($xmlArr[$value['column']] != $value['value']) {
							result($msg);
						}
						break;
					case '!=':

						if ($xmlArr[$value['column']] == $value['value']) {
							result($msg);
						}
						break;
					case '>=':
						if ($xmlArr[$value['column']] < $value['value']) {
							result($msg);
						}
						break;
					case '<=':
						if ($xmlArr[$value['column']] > $value['value']) {
							result($msg);
						}
						break;
					case '>':
						if ($xmlArr[$value['column']] <= $value['value']) {
							result($msg);
						}
						break;
					case '<':
						if ($xmlArr[$value['column']] >= $value['value']) {
							result($msg);
						}

						break;
					default:
						result($msg);
						break;
				}
			}
		}
		/**
		 * 检查APP自身属性发布规则
		 * @param  [type] $xmlArr [description]
		 * @param  [type] $value  [description]
		 * @return [type]         [description]
		 */
		public function checkAppAttributesPublishRule($xmlArr,$value)
		{
			if (!empty($value['operator'])) {
				switch ($value['condition']) {
					case '==':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr['@attributes'][$value['column']]) + floatval($value['param']) != floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '-':
								if (floatval($xmlArr['@attributes'][$value['column']]) - floatval($value['param']) != floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '*':
								if (floatval($xmlArr['@attributes'][$value['column']]) * floatval($value['param']) != floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '/':
								if (floatval($xmlArr['@attributes'][$value['column']]) / floatval($value['param']) != floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '%':

								if (floatval($xmlArr['@attributes'][$value['column']]) % floatval($value['param']) != floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							default:
								result('上传失败，字段属性'.$value['@attributes']['column'].'不符合发布规则！');
								break;
						}
						break;
					case '!=':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr['@attributes'][$value['column']]) + floatval($value['param']) == floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '-':
								if (floatval($xmlArr['@attributes'][$value['column']]) - floatval($value['param']) == floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '*':
								if (floatval($xmlArr['@attributes'][$value['column']]) * floatval($value['param']) == floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '/':
								if (floatval($xmlArr['@attributes'][$value['column']]) / floatval($value['param']) == floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '%':
								if (floatval($xmlArr['@attributes'][$value['column']]) % floatval($value['param']) == floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							default:
								result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								break;
						}
						break;
					case '>=':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr['@attributes'][$value['column']]) + floatval($value['param']) < floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '-':
								if (floatval($xmlArr['@attributes'][$value['column']]) - floatval($value['param'])  <  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '*':
								if (floatval($xmlArr['@attributes'][$value['column']]) * floatval($value['param'])  <  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '/':
								if (floatval($xmlArr['@attributes'][$value['column']]) / floatval($value['param'])  <  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '%':
								if (floatval($xmlArr['@attributes'][$value['column']]) % floatval($value['param']) < floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							default:
								result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								break;
						}
						break;
					case '<=':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr['@attributes'][$value['column']]) + floatval($value['param']) > floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '-':
								if (floatval($xmlArr['@attributes'][$value['column']]) - floatval($value['param'])  >  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '*':
								if (floatval($xmlArr['@attributes'][$value['column']]) * floatval($value['param'])  >  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '/':
								if (floatval($xmlArr['@attributes'][$value['column']]) / floatval($value['param'])  >  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '%':
								if (floatval($xmlArr['@attributes'][$value['column']]) % floatval($value['param'])   > floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							default:
								result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								break;
						}
						break;
					case '>':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr['@attributes'][$value['column']]) + floatval($value['param']) <= floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '-':
								if (floatval($xmlArr['@attributes'][$value['column']]) - floatval($value['param'])  <=  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '*':
								if (floatval($xmlArr['@attributes'][$value['column']]) * floatval($value['param'])  <=  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '/':
								if (floatval($xmlArr['@attributes'][$value['column']]) / floatval($value['param'])  <=  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '%':
								if (floatval($xmlArr['@attributes'][$value['column']]) % floatval($value['param']) <= floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							default:
								result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								break;
						}
						break;
					case '<':
						switch ($value['operator']) {
							case '+':
								if (floatval($xmlArr['@attributes'][$value['column']]) + floatval($value['param']) >= floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '-':
								if (floatval($xmlArr['@attributes'][$value['column']]) - floatval($value['param'])  >= floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '*':
								if (floatval($xmlArr['@attributes'][$value['column']]) * floatval($value['param'])  >=  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '/':
								if (floatval($xmlArr['@attributes'][$value['column']]) / floatval($value['param'])  >=  floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							case '%':
								if (floatval($xmlArr['@attributes'][$value['column']]) % floatval($value['param']) >= floatval($value['value'])) {
									result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								}
								break;
							default:
								result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
								break;
						}
						break;
					default:
						result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
						break;
				}
			}else{
				switch ($value['condition']) {
					case '==':
						if ($xmlArr['@attributes'][$value['column']] != $value['value']) {
							result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
						}
						break;
					case '!=':

						if ($xmlArr['@attributes'][$value['column']] == $value['value']) {
							result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
						}
						break;
					case '>=':
						if ($xmlArr['@attributes'][$value['column']] < $value['value']) {
							result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
						}
						break;
					case '<=':
						if ($xmlArr['@attributes'][$value['column']] > $value['value']) {
							result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
						}
						break;
					case '>':
						if ($xmlArr['@attributes'][$value['column']] <= $value['value']) {
							result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
						}
						break;
					case '<':
						if ($xmlArr['@attributes'][$value['column']] >= $value['value']) {
							result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
						}

						break;
					default:
						result('上传失败，字段属性'.$value['column'].'不符合发布规则！');
						break;
				}
			}
		}
		public function deletePublishApp($put)
		{
			if (!empty($put) && is_array($put)) {
				foreach ($put as  $value) {
					$arr[] = (int)$value;
				}
				$arr = array_unique($arr);
				if (!empty($arr)) {
					$sqlId = implode($arr, ',');
					if (isAdmin()) {
					    $user = session('androidIsLogin');
					    
					    if ($res = $this->field('*,concat(pkg_name,version_code) AS pkg_version_code')->where("id IN (".$sqlId.")")->select()) {
					        if(false !== $this->where("id IN (".$sqlId.")")->delete()){
					            foreach ($res as  $value) {
					                //添加删除人和删除时间
					                $value['delete_user']=$user['user'];
					                $value['delete_time']=time();
					                D("AppPublishHistory")->addAppPublishHistory($value);
					            }
					            D("AppComment")->deleteCommentArrForId($sqlId);
					            
					            //删除应用的状态修改记录
					            $pkgVersionCode=array_column($res, 'pkg_version_code');
					            D('AppStatusModifyRecord')->where(array('CONCAT(pkg_name,version_code)'=>array('IN',$pkgVersionCode)))->delete();
					            
					            //删除应用的渠道包到历史表
					            $oldList = M('AppChannelPublish')->field("*,'" . $user['user'] . "' AS delete_user," . time() . " AS delete_time")
					            ->where(array('app_publish_id' => array('IN', $arr)))
					            ->select();
					            if (!empty($oldList) && false !== M('AppChannelPublish')->where(array('app_publish_id' => array('IN', $arr)))->delete()) {
					                M('AppChannelPublishHistory')->addAll($oldList);
					            }
					            return true;
					        }else{
					            result('删除失败');
					        }
					    }
					    
					}else{
						result('auth');
					}
				}
			}else{
				result('param');
			}
		}

		/*public function modifyAppAdmin($put)addStatusRecord
		{

			if (isAdmin()) {
				if (!empty($put['id']) && !empty($put['name']) && !empty($put['operator'])) {
					if ($this->find($put['id'])) {
						if ($this->where("name = '%s'  and  operator = '%s' and id !=%d",array($user['name'],$put['operator'],$put['id']))->find()) {
							$options = array(
								'name' => $put['name'],
								'operator' => $put['operator'],
							);
							if (!empty($put['note'])) {
								$options['note'] = $put['note'];
							}
							$this->where("id = %d",array($user['id']))->save($options);

						}else{
							result('该用户已有该权限');
						}
					}else{
						result('数据不存在');
					}
				}else{
					result('param');
				}
			}else{
				result('auth');
			}
		}*/
		
		public function mofidyPassTest($put)
		{
        if (! empty($put['id']) && isset($put['passTest'])) {
            $id = (int) $put['id'];
            $passTest = $put['passTest'];
            if ($id <= 0) {
                result('param');
            } else {
                $res = $this->find($id);
                if (empty($res)) {
                    result('应用不存在');
                }
                
                //规则：1、管理员：通过、不通过、打回  2、测试用户，必须是app管理员的所属测试，只能通过、不通过 3、产品用户：打回
                $status = $this->getAppModifyPassTest();
                if (! array_key_exists($passTest, $status)) {
                    result('app状态参数有误');
                } else {
                    //测试用户需要判断是否是属于app应用的测试管理
                    if (! isAdmin(false) && array_key_exists('true', $status) && array_key_exists('false', $status)) {
                        $isAuth = D('AppAdmin')->isAppTester($res['pkgName']);
                        if (! $isAuth) {
                            result('未指定该应用，不可修改状态');
                        }
                    }
                    
                    //判断是通过还是回归通过，规则：上一版本未通过，则回归通过
                    if (in_array($passTest, array('true', 'regress'))) {
                        $preRecord = $this->field('id,pass_test')
                            ->where(array('pkg_name' => $res['pkgName'], 'id' => array('lt', $res['id'])))
                            ->order('id DESC')
                            ->find();
                        if (! empty($preRecord) && $preRecord['passTest'] == 'false') {
                            $put['passTest'] = 'regress';
                        } else {
                            $put['passTest'] = 'true';
                        }
                    }
                    
                    //判断不通过、打回不能再修改状态  & 通过或回归通过之后，不能再改为不通过
                    if (in_array($res['passTest'], array('false', 'back'))) {
                        result('状态已终止，不可修改');
                    } elseif (in_array($res['passTest'], array('true', 'regress')) && $put['passTest'] == 'false') {
                        result('测试已完成，不可修改测试状态');
                    }
                    
                    $options = array('pass_test' => $put['passTest']);
                    if ($this->where('id = %d', array($id))->save($options)) {
                        //记录修改状态
                        D('AppStatusModifyRecord')->addStatusRecord($res, $put['passTest']);
                    }
                }
            }
        } else {
            result('param');
        }
        return;
    }
		public function publishAppLists($get)
		{

			$field = "`id`,`name`,`version_code` AS versionCode,`version_name` AS versionName,`version_desc` AS versionDesc,`pkg_name` AS pkgName,`git_commit_id` AS gitCommitId,`channel_id` AS channelId,`system_app` AS systemApp,`mixed`,`signature`,`min_sdk` AS  minSdk,`publisher`,`pub_time` AS pubTime,`pass_test` AS passTest,`git_branch` AS gitBranch,`rely_module` AS relyModule,concat('". U("App/download") ."?id=',id) as path";
			if (!empty($get['id'])) {
				$res['extra'] = $this->field($field)->find($get['id']);
				if (!empty($res['extra'])) {
					$res['count'] = 1;
					// $res['extra']['rely'] =D('AppRely')->appRelyListsForId($res['extra']['id']);
				}
				// concat('".LOCALHOST_PATH."',normal_path) as normalPath
			}else{
				$where = '';
				$group = '';

				$table = '';
				if (!empty($get['name'])) {
					$UserNameSql = D('User')->getAllNameForUserName($get['name']);

					$appNameSql = D('App')->getNameSqlForNameApp($get['name']);

					$where = " ( `name` like '%".$get['name']."%' or  `pkg_name` like '%".$get['name']."%' or publisher IN (".$UserNameSql.")  or  pkg_name IN (".$appNameSql.") )";
				}

				if ($get['passTest'] == 'true' || $get['passTest'] == 'false' || $get['passTest'] == 'test') {
					$table = $this->order('version_code desc')->select(false);
					$group = 'pkg_name';
					if (!empty($where)) {
						$where .= " AND ";
					}
					if ($get['passTest'] == 'true') {
						$where .= " ( pass_test = 'true'  or  pass_test = 'regress' )";
					}else{
						$where .= " pass_test = '".$get['passTest']."' " ;
					}
				}elseif (!empty($get['unread'])) {
					if (!empty($where)) {
						$where .= " AND ";
					}
					$group = '';
					$where .= '( id IN ('.D('AppCommentUnread')->getCurrentCommentPublishIdSql().'))';

				}

				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					if (!empty($table)) {
						$module = M();
						 $sql = "SELECT ".$field." FROM (". $table .") AS a WHERE ".$where." GROUP BY ".$group." LIMIT {$get['page']},{$get['pageSize']} ";
						$res['extra'] = $module->query($sql);
					}else{
						$res['extra'] = $this->field($field)->where($where)->limit($get['page'],$get['pageSize'])->group($group)->select();
					}
				}else{
					if (!empty($table)) {
						$module = M();
						$res['extra'] = $module->query("SELECT ".$field." FROM (". $table .") AS a WHERE ".$where." GROUP BY ".$group);
					}else{
						$res['extra'] = $this->field($field)->where($where)->group($group)->select();

					}
				}
				if (!empty($table)) {
					$field = "";
					$res['count'] = count($res['extra']);
				}else{
					$res['count'] = $this->where($where)->group($group)->count();

				}

			}

			if (empty($res['extra'])) {
				$res['extra'] = array();
				$res['count'] = isset($res['count'])?$res['count']:'0';

			}else{

				$UserArr = D('User')->getAllNameUser();

				foreach ($UserArr as  $value) {
					$nameArr[$value['user']] = $value['name'];
				}

				$appArr = D('App')->getAll();
				if (!empty($appArr)) {
					foreach ($appArr as  $value) {

						$appData[ $value['name'] ] = $value['app'];
					}
				}

				/*if (empty($res['extra'][0])) {
					$jsonArr = json_decode($res['extra']['relyModule'],true);
					$res['extra']['relyModule'] = !empty($jsonArr)?$jsonArr:array();
					if (!empty($nameArr[$res['extra']['publisher']])) {
						$res['extra']['publisher'] = $nameArr[$res['extra']['publisher']];
					}
				}else{
					foreach ($res['extra'] as $key => $value) {
						$jsonArr = json_decode($value['relyModule'],true);
						$res['extra'][$key]['relyModule'] = !empty($jsonArr)?$jsonArr:array();

						if (!empty($nameArr[$value['publisher']])) {
							$res['extra'][$key]['publisher'] = $nameArr[$value['publisher']];
						}
					}
				}*/
				if (empty($res['extra'][0])) {
					$res['extra']['signature'] =   json_decode( $res['extra']['signature'],true );
					$res['extra']['relyModule'] = json_decode($res['extra']['relyModule'],true);
					if (empty($res['extra']['relyModule'])) {
						$res['extra']['relyModule'] = array();
					}

					if (!empty($appData[$res['extra']['pkgName']])) {
						$res['extra']['name'] = $appData[$res['extra']['pkgName']];
					}
					if (!empty($nameArr[$res['extra']['publisher']])) {
						$res['extra']['publisher'] = $nameArr[$res['extra']['publisher']];
					}
				}else{
					foreach ($res['extra'] as $key => $value) {
						$res['extra'][$key]['signature'] =   json_decode( $value['signature'],true );
						$res['extra'][$key]['relyModule'] = json_decode($value['relyModule'],true);
						if (empty($res['extra'][$key]['relyModule'])) {
							$res['extra'][$key]['relyModule'] = array();
						}
						if (!empty($nameArr[$value['publisher']])) {
							$res['extra'][$key]['publisher'] = $nameArr[$value['publisher']];
						}
						if (!empty($appData[$value['pkgName']])) {
							$res['extra'][$key]['name'] = $appData[$value['pkgName']];
						}

					}
				}
			}

			return $res;
		}

		public function relyAppLists($get)
		{
			if (!empty($get['module'])&&!empty($get['versionName'])&&!empty($get['pkgName'])) {
				$res['extra'] = $this->where("name = '%s' and version_name = '%s'  and pkg_name = '%s'",array($get['module'],$get['versionName'],$get['pkgName']))->find();

			}
			if (empty($res['extra'])) {
				$res['extra'] = new \stdClass();
			}else{
				$res['extra']['signature'] =   json_decode( $res['extra']['signature'],true );
				$res['extra']['relyModule'] = json_decode($res['extra']['relyModule'],true);
				if (empty($res['extra']['relyModule'])) {
					$res['extra']['relyModule'] = array();
				}
			}
			return $res;
		}
		public function getValOneForId($id)
		{
			if (isPOrT()) {
				return $this->find($id);
			}
		}
		public function getOneForId($id)
		{
			return $this->find($id);
		}
		public function getValOneForIdToComment($id)
		{
			return $this->find($id);

		}
		public function getArrIdPkgNameInId($id)
		{
			return $this->field('id,pkg_name,version_name')->where("id IN (%s)",array($id))->select();
		}
		public function download($get)
		{
			if (!empty($get['id'])) {
				if ($res = $this->find($get['id'])) {
					return C("DOWNLOAD_APK_PREFIX_ADDR") . $res['path'];
				}
			}
			return;
		}

    /**
     * 根据用户组获取修改状态列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月26日
     */
    public function getAppModifyPassTest()
    {
        $passTest = array();
        $authPassTest = array(
            'isAdmin' => array('true' => '通过', 'false' => '未通过', 'back' => '打回'), 
            'isTester' => array('true' => '通过', 'false' => '未通过'), 
            'isProduct' => array('back' => '打回'));
        foreach ($authPassTest as $k => $v) {
            if ($k(false)) {
                $passTest = array_merge($passTest, $v);
            }
        }
        return $passTest;
    }
	}