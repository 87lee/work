<?php
	namespace Home\Desktop;
	class DesktopJbkPasswdDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_jbk_passwd';
		protected $connection = 'DB_DESKTOP';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		protected $_validate = array(
			array('vendorid','','客户名已存在',0,'unique',3), // 在新增的时候验证name字段是否唯一
		);
		public function addJbk($vendorid,$passwd,$desc)
		{
			$options = array(
				'vendorid'=>$vendorid,
				'passwd'=>$passwd,
				'desc'=>$desc,
			);

			if ($this->create($options)) {
				$this->add();
			}else{
				result($this->getError());
			}
			/*$data = array(
				'vendorID'=>$vendorid,
				'passwd'=>$passwd,
				'event'=>'add'
			);
			$data = json_encode($data,JSON_UNESCAPED_UNICODE);
			if ($this->create($options)) {

				$json = postUrl(DESKTOP3_JBK_PASSWD_ACCESS_ADDR.'jbkPasswdPub',$data);
				// var_dump($json);
				if ($json) {
					$json = json_decode($json,true);
					if ($json['result']=='ok') {
						$this->add();
					}else{
						result($json['reason']);
					}
				}else{
					result('发布地址出错');
				}

			}else{
				result($this->getError());
			}*/

		}
		public function modifyJbk($id,$vendorid,$passwd,$desc)
		{
			$options = array(
				'vendorid'=>$vendorid,
				'passwd'=>$passwd,
				'desc'=>$desc,
			);
			if (!$this->where("`vendorid`='%s' and `id` != %d",array($vendorid,$id))->find()) {
				$this->where("`id`=%d",array($id))->save($options);
			}else{
				result($this->getError());
			}

			/*$data = array(
				'vendorID'=>$vendorid,
				'passwd'=>$passwd,
				'event'=>'modify'
			);
			$data = json_encode($data,JSON_UNESCAPED_UNICODE);
			if (!$this->where("`vendorid`='%s' and `id` != %d",array($vendorid,$id))->find()) {
				$json = postUrl(DESKTOP3_JBK_PASSWD_ACCESS_ADDR.'jbkPasswdPub',$data);
				if ($json) {
					$json = json_decode($json,true);
					if ($json['result']=='ok') {
						$this->where("`id`=%d",array($id))->save($options);
					}else{
						result($json['reason']);
					}
				}else{
					result('发布地址出错');
				}

			}else{
				result($this->getError());
			}*/

		}
		public function deleteJbk($id)
		{	$res = $this->find($id);
			if (!$res) {
				result('客户不存在');
			}
			$this->where("`id`=%d",array($id))->delete();

			/*$data = array(
				'vendorID'=>$res['vendorid'],
				'passwd'=>$res['passwd'],
				'event'=>'delete'
			);
			$data = json_encode($data,JSON_UNESCAPED_UNICODE);
			$json = postUrl(DESKTOP3_JBK_PASSWD_ACCESS_ADDR.'jbkPasswdPub',$data);
			if ($json) {
				$json = json_decode($json,true);

				if ($json['result']=='ok') {

					$this->where("`id`=%d",array($id))->delete();
				}else{
					if (!empty($json['reason'])) {
						result($json['reason']);
					}else{
						result('发布地址返回数据出错');
					}

				}
			}else{
				result('发布地址出错');
			}*/

		}

		public function jbkLists($id = null,$name= null ,$page=null,$pageSize=null,$mac=null)
		{
			if ($id ===null) {
				if ($name != null) {
					if (!empty($page)&&!empty($pageSize)) {
						$page = $page*$pageSize - $pageSize;
						$res['extra'] = $this->limit($page,$pageSize)->where("`vendorid` like '%".$name."%' or `desc` like  '%".$name."%'")->select();
					}else{
						$res['extra'] = $this->where("`vendorid` like '%".$name."%' or `desc` like  '%".$name."%'")->select();
					}
					$res['count'] = $this->where("`vendorid` like '%".$name."%' or `desc` like  '%".$name."%'")->count();
				}elseif ($mac != null) {
					$json = getUrl(C('DESKTOP3_JBK_PASSWD_ACCESS_ADDR').'jailbreakCheck?mac='.$mac);
					if ($json) {
						$json = json_decode($json,true);

						if ($json['result']=='ok') {
							$res['extra'] = $json['extra'];
						}else{
							result('发布地址返回数据出错');
						}
					}else{
						result('发布地址出错');
					}
				}else{
					if (!empty($page)&&!empty($pageSize)) {
						$page = $page*$pageSize - $pageSize;
						$res['extra'] = $this->limit($page,$pageSize)->select();
					}else{
						$res['extra'] = $this->select();
					}
					$res['count'] = $this->count();
				}
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}
			}else{
				$res['extra'] = $this->find($id);
				$res['count'] = 1;
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = 0;
				}
			}
			result(true,$res);
		}


	}