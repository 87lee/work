<?php
return array(
    'subject' => 'VSOONTECH - 问题单回复通知', 
    'content' => '
   			<div style="padding: 30px">
				<div style="margin: 6px 0 60px 0;">
					<p>您好, <font color="blue">{<$nickName>}</font> :</p>
                    <p>您的问题单:</p>  
                    <p style="text-indent:2em"><font color="gray">{<$askContent>}</font></p>
                    <p>我们已经回复，您可以<a href="{<$loginUrl>}">登录</a>，点击【我的提问】查看详情。</p>
					<br/>
					<p style="color:gray;font-size:12px">此为系统邮件，请勿回复</p>
				</div>
			</div>');
?>