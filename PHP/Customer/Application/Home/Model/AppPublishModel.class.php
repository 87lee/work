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

			set_time_limit(0);
			$filename = $_FILES['apkFile']['tmp_name'];
			$zip = new \ZipArchive;
			if ($zip->open($filename) === TRUE) {
			      	$xml = $zip->getFromName('assets/appinfo.xml');
			      	if (!empty($xml)) {
			      		$xmlArr = simplexml_load_string($xml);
			      	}else{
			      		$zip->close();
			      		result('上传失败，丢失文件assets/appinfo.xml');
			      	}

			      	if (!empty($xmlArr)) {

			      		$xmlArr = (array)$xmlArr;

			     		if (empty($put['versionDesc'])) {
						$xmlArr['versionDesc'] = '';
					}else{
						$xmlArr['versionDesc'] = $put['versionDesc'];
					}
					if (empty($xmlArr['package_name']) || empty($xmlArr['version_name']) || empty($xmlArr['version_code']) || empty($xmlArr['channel_id']) || empty($xmlArr['min_sdk']) || empty($xmlArr['@attributes']['name']) || empty($xmlArr['git_branch']) || empty($xmlArr['git_commit_id']) || empty($xmlArr['system_app'])) {
						$zip->close();
						result('上传失败，app模块内容出错');
					}else{
						$isAppPublish = D('AppAdmin')->isAppPublish($xmlArr['package_name']);
						if ($isAppPublish) {
							if (!empty($xmlArr['dependencies'])) {
								$dependencies = (array)$xmlArr['dependencies'];
								if (!empty($dependencies['dependency'])) {
									if (is_array($dependencies['dependency'])) {
										foreach ($dependencies['dependency'] as $key => $value) {
											$dependency[] = (array)$value;
										}
									}else{
										$dependency[] = (array)$dependencies['dependency'];
									}
								}
							}
							if (!empty($dependency)) {
								foreach ($dependency as $key => $value) {
									if ( !empty($value['@attributes']['name']) && !empty($value['package_name']) && !empty($value['version_name']) )  {
										$dependencyItem[] = array(
											'module'=>$value['@attributes']['name'],
											'version_name'=>$value['version_name'],
											'pkg_name'=>$value['package_name']
										);
									}
								}
							}
							//保存app
							$config = array(
								// 'maxSize'    =>    3*1024*1024,
								'savePath'   =>    '',
								'rootPath'   =>    './Apk/',
								'saveName'   =>    array('uniqid',''),
								'exts'       =>    array('apk'),
								'autoSub'    =>    true,
								'subName'    =>    array('date','Ymd'),
							);
							$upload = new \Think\Upload($config);// 实例化上传类
							$info   =   $upload->uploadOne($_FILES['apkFile']);

							if(!$info) {
								// 上传错误提示错误信息
								$zip->close();
								result($upload->getError());
							}else{
								// 上传成功 获取上传文件信息
								$xmlArr['path'] = 'Apk/'.$info['savepath'].$info['savename'];
							}
							//是否混淆
							$commandPath = 'Apk/jadx/bin/jadx';
							$savename = explode( '.',$info['savename'] );
							$commandSavePath = 'Apk/jadx/bin/'.$savename[0];
							$commandFilePath = 'Apk/'.$info['savepath'].$info['savename'];
							$commandBool = exec($commandPath.' -d '.$commandSavePath.' ' . $commandFilePath );

							$xmlFilePath = $commandSavePath . '/com/linkin/proguard/Mark.java';

							if (file_exists($xmlFilePath)) {
								if (file_exists($commandSavePath)) {
									removeDir($commandSavePath);

								}
								if (file_exists($xmlArr['path'])) {
									removeDir($xmlArr['path']);
								}
								result('上传失败，apk未混淆！');
							}else{
								$xmlArr['mixed'] = 'true';
							}
						      	//签名信息
						      	$signature = $zip->extractTo($commandSavePath,'META-INF/CERT.RSA');
							// $commandPath = '/usr/lib/jvm/jdk7/bin/keytool -printcert -file ';
							$commandPath = 'keytool -printcert -file ';
							$commandFilePath = $commandSavePath.'/META-INF/CERT.RSA';
							// echo $commandPath.$commandFilePath;
							exec($commandPath.$commandFilePath ,$signatureMsg );

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
								if (file_exists($xmlArr['path'])) {
									removeDir($xmlArr['path']);
								}
								result('上传失败，没有签名信息！');
							}

							if (file_exists($commandSavePath)) {
								removeDir($commandSavePath);
							}
							$xmlArr['signature'] = !empty($signatureArr)?json_encode($signatureArr,JSON_UNESCAPED_UNICODE):'[]';

							if ($res = $this->where(' version_code = "%s" AND version_name = "%s"  AND pkg_name = "%s" ' ,array($xmlArr['version_code'],$xmlArr['version_name'],$xmlArr['package_name']))->find()) {
								if (file_exists($xmlArr['path'])) {
									removeDir($xmlArr['path']);
								}
								result("上传失败，版本已存在！");
							}
							$user = session('androidIsLogin');
							$options = array(
								"name"=>$xmlArr['@attributes']['name'],
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
								"pass_test"=>'false',
								"pub_time"=>time(),
								"path"=>$xmlArr['path'],
								"git_branch"=>$xmlArr['git_branch'],
								"rely_module"=>!empty($dependencyItem)?json_encode($dependencyItem,JSON_UNESCAPED_UNICODE):'[]',
							);
							$zip->close();
							return $this->add($options);

						}else{
							$zip->close();
							result('auth');
						}
					}
			     	}else{
			     		$zip->close();
			     		result('上传失败，assets/appinfo.xml内容出错');
			     	}
			}else{
		     		result('上传失败，打开上传文件出错');
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
						if ($res = $this->where("id IN (".$sqlId.")")->select()) {
							D("AppComment")->deleteCommentArrForId($sqlId);
							return $this->where("id IN (".$sqlId.")")->delete();
						}

					}else{
						result('auth');
					}
				}


			}else{
				result('param');
			}
		}

		/*public function modifyAppAdmin($put)
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
			if (!empty($put['id'])&&($put['passTest'] == 'false' || $put['passTest'] == 'true')) {
				if ($res = $this->find($put['id'])) {
					$isAppTester = D('AppAdmin')->isAppTester($res['pkgName']);
					if ($isAppTester) {
						$options = array(
							'pass_test'=>$put['passTest']
						);
						return $this->where('id = %d',array($put['id']))->save($options);
					}else{
						result('auth');
					}

				}else{
					result('应用不存在');
				}

			}else{
				result('param');
			}
		}
		public function publishAppLists($get)
		{
			$field = "`id`,`name`,`version_code` AS versionCode,`version_name` AS versionName,`version_desc` AS versionDesc,`pkg_name` AS pkgName,`git_commit_id` AS gitCommitId,`channel_id` AS channelId,`system_app` AS systemApp,`mixed`,`signature`,`min_sdk` AS  minSdk,`publisher`,`pub_time` AS pubTime,`pass_test` AS passTest,`git_branch` AS gitBranch,`rely_module` AS relyModule,concat('".LOCALHOST_PATH."',path) as path";
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
					$UserName = D('User')->getAllNameForUserName($get['name']);
					$where = " `name` like '%".$get['name']."%' or  `pkg_name` like '%".$get['name']."%' or publisher IN (".$UserName.")";
				}
				if ($get['passTest'] == 'true' || $get['passTest'] == 'false') {
					$table = $this->order('version_code desc')->select(false);
					$group = 'pkg_name';
					if (!empty($where)) {
						$where .= " AND pass_test = '".$get['passTest']."'";
					}else{
						$where = " pass_test = '".$get['passTest']."'";
					}
				}

				if (!empty($get['page'])&&!empty($get['pageSize'])) {
					$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
					if (!empty($table)) {
						$module = M();
						$res['extra'] = $module->query("SELECT ".$field." FROM (". $table .") AS a WHERE ".$where." GROUP BY ".$group." LIMIT {$get['page']},{$get['pageSize']} ");
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
		public function getValOneForIdToComment($id)
		{
			return $this->find($id);

		}
	}