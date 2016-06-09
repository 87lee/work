$(function() {
	setSize();
	$('.input-group>input').on('focus',function(){
		$(this).css('border','1px solid #cccccc');
		$('.error-info').text('');
	})
	
	$('#emailSub').on('click', function() {
		var inputObj=$('.input-group>input');
		var errorObj=$('.error-info');
		var email=$.trim(inputObj.val());
		var regExp=new RegExp(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/);
		var errorMsg='';
		
		//禁用输入框和提交按钮
		inputObj.val('').attr('readonly',"readonly");
		$(this).attr('disabled','disabled');
		
		
		if(email == ''){
			inputObj.css({'border':'1px solid red'});
			errorMsg='邮箱不能为空';
		}else if(!regExp.test(email)){
			inputObj.css({'border':'1px solid red'});
			errorMsg='邮箱格式不正确';
		}
		if(errorMsg !== ''){
			errorObj.text(errorMsg).show();
		}else{
			inputObj.css({'border':'1px solid #cccccc'});
			errorObj.text('邮件发送中...');
			AjaxKeyPost('/Customer/Home/User/sendFindPwdEmail',{'email':email},function(res){
				res=JSON.parse(res);
				if(res.code != 200){
					inputObj.css({'border':'1px solid red'});
					errorObj.text(res.msg).show();
				}else{
					errorObj.text(res.msg).show();
				}
			});
		}
		
		//恢复输入框和按钮
		inputObj.removeAttr('readonly');
		$(this).removeAttr('disabled');
		
		return false;
	});
	window.onresize = setSize;
});

function setSize() {
    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    var height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;    
    $('#backImg').css({'width':width, 'height':height});
    var tmpH = (height/2)-130;
    $('#back').css("margin-top", tmpH.toString() + "px");
    $('#logo').css('left', (width/2 - 271) + 'px');
    $('#logo').css('top', (tmpH - 99) + 'px');
}
