$(function(){
	setSize();
	var url=window.location.href;
	var key=url.substring(url.indexOf('key=')+4,url.length);
	if(key.length <=0){
		window.location.href = myConfig.logOutUrl;
		return false;
	}
	
	$('#updatePassBtn').on('click',function(){
		var pass=$('#new-pass').val();
		var re_pass=$('#repeat-new-pass').val();
		var regExp = new RegExp(/^[a-zA-Z0-9~!#@$%^&*()]{8,20}$/);
		var errorMsg='';
		if(pass.length < 8 || pass.length > 20){
			$('.error-info').text('密码格式8~20数字、字母组成').show();
		}else if(pass !== re_pass){
			$('.error-info').text('确认密码不正确').show();
		}else if(!regExp.test(pass)){
			$('.error-info').text('密码格式不正确').show();
		}else{
			AjaxKeyPost('/Customer/Home/User/userResetPwd',{'key':key,'pwd':pass,'re_pwd':re_pass},function(res){
				res=JSON.parse(res);
				if(res.code != 200){
					$('.error-info').text(res.msg).show();
				}else{
					window.location.href = myConfig.logOutUrl;
				}
			});
		}
		return false;
		
	});
	window.onresize = setSize;
});

function setSize() {
    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    var height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;    
    $('#backImg').css({'width':width, 'height':height});
    var tmpH = (height/2)-165;
    $('#back').css("margin-top", tmpH.toString() + "px");
    $('#logo').css('left', (width/2 - 271) + 'px');
    $('#logo').css('top', (tmpH - 99) + 'px');
}
