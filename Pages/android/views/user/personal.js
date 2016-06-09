//@ sourceURL=user.personal.js
var myData = {};
var pageSize = 12; //自定义分页，每页显示的数据量
var modularPage = 1; //模块当前的页面
var appPage = 1; //应用当前的页面

$(function () {
	AjaxGet('/Android/home/user/currentModuleAdminLists', function(data){
		createModular(data, 1);
	});

	AjaxGet('/Android/home/user/currentAppAdminLists', function(data){
		createApp(data, 1);
	});

	updateUserInfo();

	// $('#userName').text(window.localStorage.getItem("ANDROID_PERMISSION_USERNAME"));
	$('#userPower').text(window.localStorage.getItem("ANDROID_PERMISSION_USEPOWER"));
	// $('#userNickName').text(window.localStorage.getItem("ANDROID_PERMISSION_USERNICKNAME"));

	if(window.localStorage.getItem("ANDROID_PERMISSION_MOFIDYPASSWROK") === 'false'){
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

function updateUserInfo(){
	AjaxGet('/Android/home/user/me', function(data){
		$('#userName').text(data.extra.user);
		$('#userNickName').text(data.extra.name);
		$('#userNickEmail').text(data.extra.email);
		$('#user-info').html(data.extra.name).attr('title', data.extra.name);
	});
}

function updateModular(page, name){
	name = name || '';
	AjaxGet('/Android/home/user/currentModuleAdminLists', function(data){
		createModular(data, page);
	});
}

function updateApp(page, name){
	name = name || '';
	AjaxGet('/Android/home/user/currentAppAdminLists', function(data){
		createApp(data, page);
	});
}

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
	$('#nickName').val($('#userNickName').text());
	$('#editEmail').val($('#userNickEmail').text());
	$('#nickModal .error-info').text('');
	$('#nickModal').modal('show');
});

//提交新密码
$('#subAPwd').on('click', function(){
	var oldPwd = $('#oldPwd').val();
	var newPwd = $('#newPwd').val();
	var resetPwd = $('#resetPwd').val();
	var regExp = new RegExp(/^[a-zA-Z0-9~!#@$%^&*()]{6,20}$/);
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

	AjaxPost('/Android/home/user/modifyPasswd', data, function () {
		$('#aPwdModal').modal('hide');
		window.location.href = myConfig.logOutUrl;
		return ;
	}, $errorInfo);
});

//提交新昵称
$('#subNick').on('click', function(){
	var name = $('#nickName').val();
	var email = $('#editEmail').val();
	var regExp = new RegExp(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/);
	var $errorInfo = $('#nickModal .error-info');

	if(name == ' ' || !name){
		$errorInfo.text('请输入昵称！');
		return;
	}

	if(!regExp.test(email)){
		$errorInfo.text('你的邮箱不符合规则！');
		return;
	}

	AjaxPost('/Android/home/user/modifyUserName', {"name": name, "email": email}, function(){
		updateUserInfo();
		$('#nickModal').modal('hide');
	}, $errorInfo);
});

function createModular(data, page){
	var dataArr = [];
    var len = data.extra.length;
    var operation = {
    	"publish": "维护"
    };
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.name, operation[arr.operation], arr.note]);
    }
    if(!dataArr.length){
    	$('#pModularTable').parent().parent().hide();
    	return false;
    }
    $('#pModularTable').parent().parent().show();
	myDataTable('#pModularTable', {
		"data": dataArr,
		"order": [
			[0, "desc"]
		],
		"stateSave": false,
		"columnDefs": [{
			'title': '模块',
			'width': '30%',
			'targets': 0
		},{
			'title': '操作',
			'width': '10%',
			'targets': 1
		},{
			'title': '备注',
			'width': '60%',
			'targets': 2
		}]
	});
}

function createApp(data, page){
	var dataArr = [];
    var len = data.extra.length;
    var operation = {
    	"test": "测试",
    	"publish" : "发布"
    };
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.name, operation[arr.operation], arr.note]);
    }
    if(!dataArr.length){
    	$('#pAppTable').parent().parent().hide();
    	return false;
    }
    $('#pAppTable').parent().parent().show();
	myDataTable('#pAppTable', {
		"data": dataArr,
		"order": [
			[0, "desc"]
		],
		"stateSave": false,
		"columnDefs": [{
			'title': '应用',
			'width': '30%',
			'targets': 0
		},{
			'title': '操作',
			'width': '10%',
			'targets': 1
		},{
			'title': '备注',
			'width': '60%',
			'targets': 2
		}]
	});
}