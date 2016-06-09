//@ sourceURL=user.personal.js
var myData = {};
var pageSize = 12; //自定义分页，每页显示的数据量
var modularPage = 1; //模块当前的页面
var appPage = 1; //应用当前的页面

$(function () {
	AjaxGet('/Customer/home/user/me', function(data) {
		$('#userName').text(data.user);

		$('#userPower').text(data.group_name);
		$('#userNickName').text(data.name);
		$('#userEmail').text(data.email);
	});

	if(window.localStorage.getItem("CUSTOM_PERMISSION_MOFIDYPASSWROK") === 'false'){
		alert('请修改初始密码！');
		$('#subAPwd').siblings().remove();
		$('#aPwdModal').modal({
			show: true
		});

		$('#aPwdModal').on('hide.bs.modal', function (e) {
		  	return false;
		});
	}
});

//修改密码
$('#page-content').on('click', '.pwdBtn', function(){
	$('#oldPwd').val('');
	$('#newPwd').val('');
	$('#resetPwd').val('');
	$('#aPwdModal .error-info').text('');
	$('#aPwdModal').modal('show');
});



//修改昵称
$('#page-content').on('click', '.nickBtn', function(){
	$('#Email').val($('#userEmail').text());
	$('#nickName').val($('#userNickName').text());
	$('#nickModal .error-info').text('');
	$('#nickModal').modal('show');
});


//提交新密码
$('#subAPwd').on('click', function(){
	var oldPwd = $('#oldPwd').val();
	var newPwd = $('#newPwd').val();
	var resetPwd = $('#resetPwd').val();
	var regExp = new RegExp(/^[a-zA-Z0-9~!#@$%^&*()]{8,20}$/);
	var $errorInfo = $('#aPwdModal .error-info');
	var data = {};

	if(oldPwd == ' ' || !oldPwd){
		$errorInfo.text('请输入旧密码！');
		return;
	}
	if(!regExp.test(newPwd)){
		$errorInfo.text('你的新密码不符合规则！');
		return;
	}
	if(newPwd !== resetPwd){
		$errorInfo.text('确认密码与新密码不一致！');
		return;
	}
	if(oldPwd === newPwd){
		$errorInfo.text('新旧密码不能相同！');
		return;
	}
	data = {"newPasswd": newPwd, "oldPasswd": oldPwd};

	AjaxPost('/Customer/home/user/modifyPasswd', data, function () {
		$('#aPwdModal').modal('hide');
		window.location.href = myConfig.logOutUrl;
		return ;
	}, $errorInfo);
});

//提交新昵称
$('#subNick').on('click', function(){
	var name = $('#nickName').val();
	var email = $('#Email').val();
	var pattern = /\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/,
	 	$errorInfo = $('#nickModal .error-info');

	if(name == ' ' || !name){
		$errorInfo.text('请输入昵称！');
		return;
	}
	if(email == ' ' || !email){
		$errorInfo.text('请输入邮箱！');
		return;
	}
	if (!pattern.test(email)) {
		$errorInfo.text('你的邮箱不符合规则！');
		return;
	}

	AjaxPost('/Customer/home/user/modifyUserName', {"name": name}, function(){
		$('#user-info').html(name).attr('title', name);
		window.localStorage.setItem("CUSTOM_PERMISSION_USERNICKNAME", name);
		$('#userNickName').text(name);
	}, $errorInfo);

	AjaxPost('/Customer/home/user/modifyUserEmail', {"email": email}, function(){
		$('#userEmail').text(email);
		$('#nickModal').modal('hide');
	}, $errorInfo);
});

//提交邮箱
// $('#subEmail').on('click', function(){
// 	var email = $('#Email').val();
// 	var pattern = /\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/,
// 	    $errorInfo = $('#emailModal .error-info');

// 	if(email == ' ' || !email){
// 		$errorInfo.text('请输入邮箱！');
// 		return;
// 	}

// 	if (!pattern.test(email)) {
// 		$errorInfo.text('你的邮箱不符合规则！');
// 		return;
// 	}

// 	AjaxPost('/Customer/home/user/modifyUserEmail', {"email": email}, function(){
// 		$('#userEmail').text(email);
// 		$('#emailModal').modal('hide');
// 	}, $errorInfo);
// });

