<?php
return array(
    'subject' => 'VSOONTECH - 客户服务系统重置密码', 
    'content' => '
   			<div style="padding: 30px">
				<div style="margin: 6px 0 60px 0;">
					<p>我们收到您的重置密码请求，如果确认是您本人操作，请点击重置密码。</p>
					<p>
						<a href="{<$url>}" target="_blank" style="text-decoration:none"><span style="background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;">重置密码</span></a>
					</p>
					<br/>
					<p style="color:gray;font-size:12px">如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复</p>
					<p style="color:gray;font-size:12px">该验证邮件有效期为{<$expire>}分钟，超时请重新发送邮件。</p>
				</div>
			</div>');
?>
