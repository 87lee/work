//@ sourceURL=user.admin.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //用户当前的页面
var loginPage = 1; //登录列表当前的页面

$(function () {
	myData.checkedLists = [];   //存储check选中项
	myData.startTime = '';
	myData.endTime = '';
	myData.ip = '';
	myData.name = '';

	AjaxGet('/Android/home/user/userLists?page=1&pageSize=' + pageSize, function(data){
        createUser(data, 1);
    });

	listenSingleCheckBox('#adminTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.userId = obj.data('id');
		myData.userName = obj.data('name');
		myData.userNick = obj.data('nick');
		myData.userEmail = obj.data('email');
		myData.tourist = obj.data('tourist');
		myData.tester = obj.data('tester');
		myData.publisher = obj.data('publisher');
		myData.admin = obj.data('admin');
    });

    listenMyPage('loginTable', loginPage, updateLogin);
    listenMyPage('adminTable', currentPage, updateUser);

    if(window.localStorage.getItem("ANDROID_PERMISSION_USERNAME") !== 'root'){
    	$('#aUserPower .admin').prop('disabled', true);
    	$('#newPower .admin').prop('disabled', true);
    }

    listenTab(function(str){
    	if(str === '用户列表'){
    		currentPage = 1;
    		$('#adminTable_filter input').val('');
    		updateUser(currentPage);
    		$('.tab-list:eq(1)').hide();
    		$('.tab-list:eq(0)').show();
    	}else if(str === '用户登录管理'){
    		loginPage = 1;
    		myData.startTime = '';
			myData.endTime = '';
			myData.ip = '';
			myData.name = '';
			$('.search-box input').val('');
			updateLogin(loginPage);
    		$('.tab-list:eq(0)').hide();
    		$('.tab-list:eq(1)').show();
    	}
    });

    $("#startTime").datetimepicker({
		format: 'yyyy-mm-dd hh:ii',
		language: 'zh-CN',
		autoclose: true
	}).val('');
	$("#endTime").datetimepicker({
		format: 'yyyy-mm-dd hh:ii',
		language: 'zh-CN',
		autoclose: true
	}).val('');
});

listenToolbar('user', addUser, '#adminTable');
listenToolbar('pwd', editPwd, '#adminTable');
listenToolbar('edit', editPower, '#adminTable');
listenToolbar('del', delUser, '#adminTable');

function addUser(){
	$('#newName').val('');
	$('#userPwd').val('');
	$('#newNickName').val('');
	$('#resetUserPwd').val('');
	$('#newEmail').val('');
	$('#aUserPower input').prop('checked', false);
	$('#aUserPower input:eq(0)').prop('checked', true);
	$('#userModal .error-info').text('');
	$('#userModal').modal('show');
}

function editPwd(){
	if(myData.checkedLists.length === 1){
		var obj = $('.checkSelected td:eq(0)');
		myData.userName = obj.data('name');
		$('#userName').val(myData.userName);
		$('#newPwd').val('');
		$('#resetPwd').val('');
		$('#pwdModal .error-info').text('');
		$('#pwdModal').modal('show');
	}else{
		alert('请选择一个用户！');
		return;
	}
}

function editPower(){
	if(myData.checkedLists.length === 1){
		var mData = checkedListsData('#adminTable', myData.checkedLists);
		myData.userNick = mData.nick;
		myData.userEmail = mData.email;
		var obj = $('.checkSelected td:eq(0)');
		myData.userId = obj.data('id');
		myData.userName = obj.data('name');
		myData.tourist = obj.data('tourist');
		myData.tester = obj.data('tester');
		myData.publisher = obj.data('publisher');
		myData.admin = obj.data('admin');
		$('#powerName').val(myData.userName);
		$('#resetNick').val(myData.userNick);
		$('#resetEmail').val(myData.userEmail);
		$('#newPower input').prop('checked', false);
		setPower('newPower', 'tourist', myData.tourist);
		setPower('newPower', 'tester', myData.tester);
		setPower('newPower', 'publisher', myData.publisher);
		setPower('newPower', 'admin', myData.admin);
		// $('#newPower input:eq(0)').prop('checked', true);
		$('#powerModal .error-info').text('');
		$('#powerModal').modal('show');
	}else{
		alert('请选择一个用户！');
		return;
	}
}

function delUser(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#adminTable_filter input').val() || '';
			AjaxPost('/Android/home/user/deleteUser', myData.checkedLists, function () {
				updateUser(currentPage, filter);
				return;
			});
		}
	}else{
		alert('请选择用户！');
		return;
	}
}

function updateUser(page, name){
	name = name || '';
	AjaxGet('/Android/home/user/userLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedLists = [];
		myData.userId = null;
		createUser(data, page);
	});
}

function updateLogin(page, name){
	name = name || '';
	AjaxGet('/Android/home/user/userLoginLists?name='+ myData.name +'&page='+ page +'&pageSize='+ pageSize + '&endTime=' + myData.endTime + '&startTime=' + myData.startTime + '&ip=' + myData.ip, function(data){
		createLogin(data, page);
	});
}

function setPower(id, user, type){
	if(type === 'false'){
		$('#'+ id +' .' + user).prop('checked', false);
	}else{
		$('#'+ id +' .' + user).prop('checked', true);
	}
}

$('#searchBtn').on('click', function(){
	var name = $('#searchName').val();
	var startTime = $('#startTime').val();
	var endTime = $('#endTime').val();
	var ip = $('#ip').val();


	if(Date.parse(endTime) <= Date.parse(startTime)){
		alert('请选择正确的时间！');
		return;
	}
	loginPage = 1;
	myData.startTime = startTime;
	myData.endTime = endTime;
	myData.ip = ip;
	myData.name = name;

	updateLogin(loginPage, name);
});

//创建用户
$('#subUser').on('click', function(){
	var newName = $('#newName').val();
	var userPwd = $('#userPwd').val();
	var newNickName = $('#newNickName').val();
	var newEmail = $('#newEmail').val();
	var resetUserPwd = $('#resetUserPwd').val();
	var tourist = $('#aUserPower .tourist').prop('checked');
	var tester = $('#aUserPower .tester').prop('checked');
	var publisher = $('#aUserPower .publisher').prop('checked');
	var admin = $('#aUserPower .admin').prop('checked');
	var filter = $('#adminTable_filter input').val() || '';
	var regExp = new RegExp(/^[a-zA-Z][\w+]{0,64}$/);
	var regExp2 = new RegExp(/^[a-zA-Z0-9~!#@$%^&*()]{6,20}$/);
	var regExp3 = new RegExp(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/);
	var $errorInfo = $('#userModal .error-info');
	var data = {};

	if(!regExp.test(newName)){
		$errorInfo.text('你的用户名不符合规则！');
		return;
	}
	if(newNickName == ' ' || !newNickName){
		$errorInfo.text('请输入昵称！');
		return;
	}
	if(!regExp3.test(newEmail)){
		$errorInfo.text('你的邮箱不符合规则！');
		return;
	}
	if(!regExp2.test(userPwd)){
		$errorInfo.text('你的密码不符合规则！');
		return;
	}
	if(userPwd !== resetUserPwd){
		$errorInfo.text('确认密码与密码不一致！');
		return;
	}
	data = {
		"user": newName,
		"name": newNickName,
		"passwd": userPwd,
		"email": newEmail,
		"admin": admin + '',
		"tourist": tourist + '',
		"tester": tester + '',
		"publisher": publisher + ''
	};

	AjaxPost('/Android/home/user/addUser', data, function () {
		$('#userModal').modal('hide');
		updateUser(currentPage, filter);
		return;
	}, $errorInfo);
});

//提交新密码
$('#subPwd').on('click', function(){
	var userName = $('#userName').val();
	var newPwd = $('#newPwd').val();
	var resetPwd = $('#resetPwd').val();
	var regExp = new RegExp(/^[a-zA-Z0-9~!#@$%^&*()]{6,20}$/);
	var $errorInfo = $('#pwdModal .error-info');
	var data = {};

	if(userName == ' ' || !userName){
		$errorInfo.text('请输入用户名！');
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
	data = {"user": userName, "passwd": newPwd};

	AjaxPost('/Android/home/user/modifyPasswd', data, function () {
		$('#pwdModal').modal('hide');
		if(myData.userName === window.localStorage.getItem("ANDROID_PERMISSION_USERNAME")){
			window.location.href = myConfig.logOutUrl;
		}
		return ;
	}, $errorInfo);
});

//修改权限
$('#subPower').on('click', function(){
	var $newPower = $('#newPower');
	var nick = $('#resetNick').val();
	var email = $('#resetEmail').val();
	var tourist = $newPower.find('.tourist').prop('checked');
	var tester = $newPower.find('.tester').prop('checked');
	var publisher = $newPower.find('.publisher').prop('checked');
	var admin = $newPower.find('.admin').prop('checked');
	var filter = $('#adminTable_filter input').val() || '';
	var regExp = new RegExp(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/);
	var $errorInfo = $('#powerModal .error-info');

	if(nick == ' ' || !nick){
		$errorInfo.text('请输入昵称！');
		return;
	}
	if(!regExp.test(email)){
		$errorInfo.text('你的邮箱不符合规则！');
		return;
	}

	var data = {
		"id": myData.userId,
		"name": nick,
		"email": email,
		"tourist": tourist + '',
		"tester": tester + '',
		"publisher": publisher + '',
		"admin": admin + ''
	};

	AjaxPost('/Android/home/user/modifyAuth', data, function(){
		$('#powerModal').modal('hide');
		updateUser(currentPage, filter);
		return;
	}, $errorInfo);
});

$('#newPower .power-list label:eq(3)').on('click', function(){
	var disabled = $(this).find('.admin').prop('disabled');
	if(disabled){
		var $errorInfo = $('#powerModal .error-info');
		$errorInfo.text('您的权限不够！');
	}
});

$('#aUserPower .power-list label:eq(3)').on('click', function(){
	var disabled = $(this).find('.admin').prop('disabled');
	if(disabled){
		var $errorInfo = $('#userModal .error-info');
		$errorInfo.text('您的权限不够！');
	}
});

function createUser(data, page){
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.user, arr.name, arr.email, arr.tourist, arr.tester, arr.publisher, arr.admin]);
    }
    myDataTable('#adminTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'用户名','width':'16%', 'targets':1},
            {'title':'昵称','width':'16%', 'targets':2},
            {'title':'邮箱','width':'17%', 'targets':3},
            {'title':'普通游客','width':'12%', 'targets':4},
            {'title':'测试用户','width':'12%', 'targets':5},
            {'title':'发布用户','width':'12%', 'targets':6},
            {'title':'系统管理员','width':'12%', 'targets':7},
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			tableTdNull(4, nRow, aData[4]);
			tableTdNull(5, nRow, aData[5]);
			tableTdNull(6, nRow, aData[6]);
			tableTdNull(7, nRow, aData[7]);
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"name": aData[1],
				"nick": aData[2],
				"email": aData[3],
				"tourist": aData[4],
				"tester": aData[5],
				"publisher": aData[6],
				"admin": aData[7]
			});
		}
	});

	initToolBar('#adminTable', [
		'<a class="btn my-btn userBtn" href="javascript:"><i class="iconfont icon-icon-user-add"></i>&nbsp;创建用户</a>',
		'<a class="btn my-btn pwdBtn" href="javascript:"><i class="iconfont icon-mima1"></i>&nbsp;密码重置</a>',
		'<a class="btn my-btn editBtn" href="javascript:"><i class="iconfont icon-yonghufankui03"></i>&nbsp;修改用户</a>',
		myConfig.delBtn
	]);
	updatePagination(len, page, data.count, 'adminTable');
	listenCheckBox('#adminTable');
    updateChecked('#adminTable');
}

function createLogin(data, page){
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        var logout = arr.logout !== '0' ? formatDate(arr.logout) : '--';
        dataArr.push([arr.user, formatDate(arr.login), logout, arr.ip]);
    }
    myDataTable('#loginTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'用户名','width':'20%', 'targets':0},
            {'title':'登录时间','width':'30%', 'targets':1},
            {'title':'退出时间','width':'30%', 'targets':2},
            {'title':'IP','width':'20%', 'targets':3}
        ]
	});

	updatePagination(len, page, data.count, 'loginTable');
}