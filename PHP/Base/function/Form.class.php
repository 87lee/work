<?php 
/**
* 
*/
class form 
{
	
	public function getFormFile()
	{
		if ($_FILES) {
			
			foreach ($_FILES as $key => $value) {
				$data[$key]['extension'] = explode('.', $value['name']);
				if (count($data[$key]['extension']) ===1) {
					$data[$key]['extension']='';
				}else{
					$data[$key]['extension']=end($data[$key]['extension']);
				}
				/* 设置发生错误时的提示信息 */
				$error = array(
					1=>'您上传的文件文件大小超出了服务器的空间大小',
					2=>'您上传的文件大小超出浏览器限制',
					3=>'出现异常，文件上传不完整',
					4=>'请选择您需要上传的文件',
					5=>'你上传的文件类型服务器临时文件夹丢失  ',
					6=>'你上传的文件写入到临时文件夹出错',
				);
				if ($value['error'] > 0) {
					result($error[$value['error']]);
				}
				$data[$key]['md5_file']=md5_file($value['tmp_name']);
				$data[$key]['filepath']=$value['tmp_name'];
				$data[$key]['size']=$value['size'];
				
			}
			return $data;
		}else{
			return false;
		}
	}
	public function getFileNameForMd5()
	{
		if ($_FILES) {
			$data = $_FILES;
			foreach ($_FILES as $key => $value) {
				$extension = explode('.', $value['name']);
				if (count($extension) ===1) {
					$extension='';
				}else{
					$extension = end($extension);
				}
				/* 设置发生错误时的提示信息 */
				$error = array(
					1=>'您上传的文件文件大小超出了服务器的空间大小',
					2=>'您上传的文件大小超出浏览器限制',
					3=>'出现异常，文件上传不完整',
					4=>'请选择您需要上传的文件',
					5=>'你上传的文件类型服务器临时文件夹丢失  ',
					6=>'你上传的文件写入到临时文件夹出错',
				);
				if ($value['error'] > 0) {
					result($error[$value['error']]);
				}
				$data[$key]['name']=md5_file($value['tmp_name']).'.'.$extension;
				
			}
			return $data;
		}else{
			return false;
		}
	}
}
 ?>