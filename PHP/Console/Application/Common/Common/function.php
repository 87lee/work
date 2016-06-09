<?php
	function deleteUrl($url)
        {
            $timeout = 30; //CURL请求超时时间

            $connecttimeout = 30; //CURL连接超时时间

            $sslVerifypeer = FALSE; //cURL将终止从服务端进行验证

            //CURL初始化
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerifypeer);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_URL, $url);
            $content = curl_exec($ch);
            $status = curl_getinfo($ch);
            curl_close($ch);
            if(intval($status["http_code"]) == 200)
            {
                return $content;

            }else
            {

                return false;
            }
        }
        function getUrl($url)
        {
            $timeout = 30; //CURL请求超时时间

            $connecttimeout = 30; //CURL连接超时时间

            $sslVerifypeer = FALSE; //cURL将终止从服务端进行验证

            //CURL初始化
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerifypeer);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_URL, $url);
            $content = curl_exec($ch);
            $status = curl_getinfo($ch);
            // dump($content);
            curl_close($ch);
            if(intval($status["http_code"]) == 200)
            {
                return $content;
            }else
            {

                return false;
            }
        }



        function postUrl($url,$file)
        {
            $timeout = 30; //CURL请求超时时间

            $connecttimeout = 30; //CURL连接超时时间

            $sslVerifypeer = FALSE; //cURL将终止从服务端进行验证
            $header = array(
             'Content-Type: application/json; charset=utf-8',
              'Content-Length: ' . strlen($file)
            );
            //CURL初始化
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerifypeer);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_URL, $url);
            $content = curl_exec($ch);
            $status = curl_getinfo($ch);
            curl_close($ch);
            if(intval($status["http_code"]) == 200)
            {
                return $content;

            }else
            {

                return false;
            }
        }
        /**
         * post Content-Type: application/x-www-form-urlencoded; charset=utf-8 类型数据
         * $data = 'rand=93w9&gmsfhm=440804198908110558&sfzxm=440800';
         * @param  [type] $url  [post地址]
         * @param  [type] $file [post 数据]
         * @return [type]       [description]
         */
        function postFormUrl($url,$file)
        {
            $timeout = 30; //CURL请求超时时间

            $connecttimeout = 30; //CURL连接超时时间

            $sslVerifypeer = FALSE; //cURL将终止从服务端进行验证
            $header = array(
             'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
              'Content-Length: ' . strlen($file)
            );
            //CURL初始化
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerifypeer);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_URL, $url);
            $content = curl_exec($ch);
            $status = curl_getinfo($ch);
            curl_close($ch);
            if(intval($status["http_code"]) == 200)
            {
                return $content;

            }else
            {

                return false;
            }


        }
 	function cbc_decontent($content)
            {
            		$content = str_replace("\r", "", $content);
		$content = str_replace("\n", "", $content);
		$content = str_replace(" ", "", $content);

		$key = 'YZV3141592653589';
		$vi = 'PLANCKH413566743';

		$model =MCRYPT_MODE_CBC;
		$cipher  = MCRYPT_RIJNDAEL_128;



		$content = mcrypt_decrypt($cipher,$key,base64_decode($content),$model,$vi);

		if ($content[strlen($content) - 1] !== '}') {
			$content = rtrim($content,$content[strlen($content) - 1]);
		}

		return  $content;
            }

            function cbc_encontent($content)
            {
            		//加密参数
		$encryptKey = 'YZV3141592653589';
		$encryptVi = 'PLANCKH413566743';
		$encryptModel = MCRYPT_MODE_CBC;
		$encryptCipher  = MCRYPT_RIJNDAEL_128;

		$block = mcrypt_get_block_size($encryptCipher,$encryptModel);
		$num = $block - strlen($content)%$block;
		$content .= str_repeat(chr($num),$num);

		$content = mcrypt_encrypt($encryptCipher,$encryptKey,$content,$encryptModel,$encryptVi);
		$content = base64_encode($content);
		return $content;
            }