//@ sourceURL=user.admin.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //用户当前的页面
var toolbar = [];
var tmp = [];

$(function () {
	initTopMenu();
	myData.checkedLists = [];   //存储check选中项
	hidePower();

	AjaxGet('/Customer/home/user/userLists?page=1&pageSize=' + pageSize, function(data){
        createUser(data, 1);
    });

	listenSingleCheckBox('#adminTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.userId = obj.data('id');
		myData.userName = obj.data('name');
		myData.permission = obj.data('permission');
		myData.group_id = obj.data('group_id');
		myData.email = obj.data('email');
		myData.note = obj.data('note');
		myData.nickName = obj.data('nickName');
    });

    listenSingleCheckBox('#uGroupTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.titleUG = obj.data('title');
		myData.idUG = obj.data('id');
		myData.rulesUG = obj.data('rules');
    });

    listenSingleCheckBox('#uRuleTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.ruleName = obj.data('name');
		myData.ruleId = obj.data('id');
		myData.ruleTitle = obj.data('title');
		myData.ruleTitle = obj.data('title');
		myData.rulePid = 0;
		myData.ruleStatus = obj.data('status');
    });
    listenSingleCheckBox('#uChildrenRuleTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.ruleChildrenName = obj.data('name');
		myData.ruleChildrenId = obj.data('id');
		myData.ruleChildrenTitle = obj.data('title');
		myData.ruleChildrenTitle = obj.data('title');
		myData.ruleChildrenPid = obj.data('pid');
		myData.ruleChildrenStatus = obj.data('status');
    });
    listenMyPage('adminTable', currentPage, updateUser);
    listenMyPage('uGroupTable', currentPage, updateUGroup, {'title':''}, {'title':'组名'});
    listenMyPage('uRuleTable', currentPage, updateRules);
    listenMyPage('uChildrenRuleTable', currentPage, updateChildrenRules);

    listenTab(function(str){
    	if(str === '用户管理'){
    		myData.checkedLists = [];
    		currentPage = 1;
    		$('#adminTable_filter input').val('');
    		updateUser(currentPage,{'name':''});
    		$('.tab-list').hide();
    		$('.tab-list:eq(0)').show();
    	}else if(str === '用户组管理'){
    		myData.checkedLists = [];
    		currentPage = 1;
    		$('#uGroupTable_filter input').val('');
    		updateUGroup(currentPage,{'name':''});
    		$('.tab-list').hide();
    		$('.tab-list:eq(1)').show();
    	}else if(str === '权限管理'){
    		myData.checkedLists = [];
    		currentPage = 1;
    		$('#uRuleTable_filter input').val('');
    		updateRules(currentPage,{'name':''});
    		$('.tab-list').hide();
    		$('.tab-list:eq(2)').show();
    		$('#sum').show();
    		$('#sub').hide();
    	}

    });

});


listenToolbar('user', addUser, '#adminTable');
listenToolbar('pwd', editPwd, '#adminTable');
listenToolbar('edit', editInfo, '#adminTable');
listenToolbar('del', delUser, '#adminTable');
listenToolbar('addUG', addUG, '#uGroupTable');
listenToolbar('editUG', editUG, '#uGroupTable');
listenToolbar('delUG', delUG, '#uGroupTable');
listenToolbar('addRule', addRule, '#uRuleTable');
listenToolbar('editRule', editRule, '#uRuleTable');
listenToolbar('delRule', delRule, '#uRuleTable');
listenToolbar('addSubRule', addSubRule, '#uChildrenRuleTable');
listenToolbar('editSubRule', editSubRule, '#uChildrenRuleTable');
listenToolbar('delSubRule', delSubRule, '#uChildrenRuleTable');
listenToolbar('back', backRuleTable, '#uChildrenRuleTable');

$('#backUser').on('click', function() {
	$('#breadcrumb').show();
	$('#page-content').show();
	$('#headTab').hide();
	$('#createUser').hide();
});

function addUser(){
	AjaxGet('/Customer/Home/Group/authGroupList', function(data) {
		selectPower(data);
	});
	$('#newName').val('');
	$('#userPwd').val('');
	$('#newNickName').val('');
	$('#resetUserPwd').val('');
	$('#newEmail').val('');
	$('#newNote').val('');
	$('#createUser #errorInfo').text('');
	// $('#userModal').modal('show');
	$('#breadcrumb').hide();
	$('#page-content').hide();
	$('#headTab').show();
	$('#createUser').show();
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

function editInfo(){
	if(myData.checkedLists.length === 1){
		AjaxGet('/Customer/Home/Group/authGroupList', function(data) {
			selectEditPower(data,obj.data('group_id'));
		});
		 var obj = $('.checkSelected td:eq(0)');
		 myData.userId = obj.data('id');
		 myData.userName = obj.data('name');
		 myData.group_id = obj.data('group_id');
		 myData.email = obj.data('email');
		 myData.note = obj.data('note');
		 myData.nickName = obj.data('nickName');
    	$('#editName').val(myData.userName);
		$('#editPower').val(myData.group_id);
		$('#editNickName').val(myData.nickName);
		$('#editEmail').val(myData.email);
		$('#editNote').val(myData.note);
		$('#editModal').modal('show');
	}else{
		alert('请选择一个用户！');
		return;
	}
}

function delUser(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#adminTable_filter input').val() || '';
			AjaxPost('/Customer/Home/user/deleteUser', myData.checkedLists, function () {
				updateUser(currentPage, {'name':filter});
				return;
			});
		}
	}else{
		alert('请选择用户！');
		return;
	}
}

function addUG() {
	$('#tree').parent().hide();
	$('#UGModal h4').html('');
	$('#UGModal h4').html('创建用户组');
	$('#userGroup').val('');
	$('#UGModal .error-info').text('');
	$('#UGModal').modal('show');
}

function editUG() {
	if(myData.checkedLists.length === 1){
		var mData = checkedListsData('#uGroupTable', myData.checkedLists);
		myData.titleUG = mData.title;
		myData.idUG = mData.id;
		myData.rulesUG = mData.rules;
		$('#tree').parent().show();
		$('#userGroup').val(myData.titleUG);
		AjaxGet('/Customer/Home/Group/authRuleTree', function(data) {
			myData.treeArr = [];
			$('#tree').treeview({
				data: initModule(data),
				selectedColor: '#000000',
				selectedBackColor: '#ffffff',
				showIcon: false,
          		showCheckbox: true,
          		onNodeChecked: function(event, node) {
            		myData.treeArr.push(Number(data.retval[node.nodeId].id));
            		tmp = [];
            		var arr = findChild(node);
            		tmp = [];
            		for (var i = 0; i < arr.length; i++) {
            			$('#tree').treeview('checkNode', [ arr[i], { silent: true } ]);
            			myData.treeArr.push(Number(data.retval[arr[i]].id));
            		}
            		tmp = [];
            		arr = findParent(node, data);
            		tmp = [];
            		for (var j = 0; j < arr.length; j++) {
            			$('#tree').treeview('checkNode', [ arr[j], { silent: true } ]);
            			myData.treeArr.push(Number(data.retval[arr[j]].id));
            		}
          		},
          		onNodeUnchecked: function (event, node) {
          			tmp = [];
            		var arr = findChild(node);
            		tmp = [];
            		arr.push(node.nodeId);
            		for (var j = 0; j < arr.length; j++) {
            			for (var i = 0; i < myData.treeArr.length; i++) {
            				if (myData.treeArr[i] === Number(data.retval[arr[j]].id)) {
            					myData.treeArr.splice(i, 1);
            					$('#tree').treeview('uncheckNode', [ arr[j], { silent: true } ]);
            				}
            			}
            		}
          		}
			});
			$('#tree').treeview('collapseAll', { silent: true });
			$('#tree').treeview('uncheckAll', { silent: true });
			if (myData.rulesUG) {
				var nums = myData.rulesUG.split(',');
				for (var i = 0; i < data.retval.length; i++) {
					for (var j = 0; j < nums.length; j++) {
						if(data.retval[i].id === nums[j]){
							$('#tree').treeview('checkNode', [ i, { silent: true } ]);
							myData.treeArr.push(Number(data.retval[i].id));
						}
					}
				}
			}
		});
		$('#UGModal h4').html('修改用户组');
		$('#UGModal').modal('show');
	}else{
		alert('请选择一个用户！');
		return;
	}
}

function delUG() {
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#uGroupTable_filter input').val() || '';
			AjaxKeyPost('/Customer/Home/Group/delAuthGroup', {'ids':myData.checkedLists}, function (data) {
				resultMsg(data, '用户组删除成功', '#UGModal');
				updateUGroup(currentPage, {'name':filter});
				return;
			});
		}
	}else{
		alert('请选择用户组！');
		return;
	}
}

function addRule() {
	$('#ruleModal h4').html('新增');
	$('#preMenu').parent().hide();
	$('#ruleCss').parent().hide();
	$('#ruleSort').parent().hide();
	$('#ruleName').val('');
	$('#ruleTitle').val('');
	$('#ruleModal').modal('show');
}

function editRule() {
	if (myData.checkedLists.length === 1) {
		$('#ruleModal h4').html('修改');
		var obj = $('.checkSelected td:eq(0)');
		$('#preMenu').parent().hide();
		$('#ruleCss').parent().hide();
		$('#ruleSort').parent().hide();
		myData.ruleName = obj.data('name');
		myData.ruleId = obj.data('id');
		myData.ruleTitle = obj.data('title');
		myData.ruleTitle = obj.data('title');
		myData.rulePid = 0;
		myData.ruleStatus = obj.data('status');
		myData.ruleAuthType = obj.data('auth_type');
		myData.ruleIcon = obj.data('icon');

		$('#ruleName').val(myData.ruleName);
		$('#ruleTitle').val(myData.ruleTitle);
		$('#ruleStatus').val(myData.ruleStatus);
		$('#ruleAuthType').val(myData.ruleAuthType);
		$('#ruleIcon').val(myData.ruleIcon);
		$('#ruleModal').modal('show');
	}else{
		alert('请选择一个权限节点！');
	}
}

function delRule() {
	if (myData.checkedLists.length) {
		if (confirm('确定删除？')) {
			var filter = $('#uGroupTable_filter input').val() || '';
			AjaxKeyPost('/Customer/Home/Group/delAuthRule', {'ids':myData.checkedLists}, function(data) {
				resultMsg(data, '权限规则删除成功', '#ruleModal');
    			updateRules(currentPage, {'name':filter});
			});
		}
	}else{
		alert('请选择权限节点！');
	}
}

function backRuleTable() {
	$('#sub').hide();
	updateRules(1);
	$('#sum').show();
}

function addSubRule() {
	$('#ruleModal h4').html('新增子权限');
	AjaxGet('/Customer/Home/Group/authRuleList?pid=' + myData.childrenPid, function(data) {
		selectPreMenu(data, myData.childrenPid);
	});
	$('#ruleName').val('');
	$('#ruleTitle').val('');
	$('#ruleCss').val('');
	$('#ruleSort').val('');
	$('#ruleModal .form-group').show();
	$('#ruleModal').modal('show');
}

function editSubRule() {
	if (myData.checkedLists.length === 1) {
		$('#ruleModal h4').html('修改子权限');
		$('#ruleModal .form-group').show();
		var obj = $('#uChildrenRuleTable .checkSelected td:eq(0)');
		AjaxGet('/Customer/Home/Group/authRuleList?pid=' + myData.childrenPid, function(data) {
			selectPreMenu(data, myData.childrenPid,obj.data('pid'));
		});
		myData.ruleChildrenName = obj.data('name');
		myData.ruleChildrenId = obj.data('id');
		myData.ruleChildrenTitle = obj.data('title');
		myData.ruleChildrenStatus = obj.data('status');
		myData.ruleChildrenPreMenu = obj.data('pid');
		myData.ruleChildrenCss = obj.data('css');
		myData.ruleChildrenSort = obj.data('sort');
		myData.ruleChildrenAuthType = obj.data('auth_type');
		myData.ruleIcon = obj.data('icon');

		$('#ruleName').val(myData.ruleChildrenName);
		$('#ruleTitle').val(myData.ruleChildrenTitle);
		$('#ruleStatus').val(myData.ruleChildrenStatus);
		$('#preMenu').val();
		$('#ruleCss').val(myData.ruleChildrenCss);
		$('#ruleSort').val(myData.ruleChildrenSort);
		$('#ruleAuthType').val(myData.ruleChildrenAuthType);
		$('#ruleIcon').val(myData.ruleIcon);
		$('#ruleModal').modal('show');
	}else{
		alert('请选择一个权限节点！');
	}
}

function delSubRule() {
	if (myData.checkedLists.length) {
		if (confirm('确定删除？')) {
			var filter = $('#uGroupTable_filter input').val() || '';
			AjaxKeyPost('/Customer/Home/Group/delAuthRule', {'ids':myData.checkedLists}, function(data) {
				resultMsg(data, '权限规则删除成功', '#ruleModal');
    			updateChildrenRules(currentPage, {'name':filter});
			});
		}
	}else{
		alert('请选择权限节点！');
	}
}

// function initModule(data){  //把获取的数据生成tree的数据格式
//     var arr = data.retval;
//     var tree_data = [];
//     for(var i = 0, len = arr.length; i < len; i++){
//         var nodes = {};
//         nodes.text = arr[i].title;
//         var children = [];
//         for (var j = 0; j < arr[i].children.length; j++) {
//             children.push({text:arr[i].children[j].title,icon: "glyphicon glyphicon-unchecked",selectedIcon: "glyphicon glyphicon-check"});
//         }
//         nodes.nodes = children;
//         tree_data.push(nodes);
//     }
//     console.log(tree_data);
//     return tree_data;
// }

function initModule(data) {
    var arr = data.retval;
    var tree_data = [];
    for (var i = 0; i < arr.length; i++) {
        var nodes = {};
        nodes.text = arr[i].title;
        var children = [];
        if (arr[i].lvl === 1) {
            tree_data.push({text:arr[i].title});
        }else if (arr[i].lvl === 2) {
        	if (tree_data[tree_data.length-1].nodes) {
        		tree_data[tree_data.length-1].nodes.push({text:arr[i].title,icon: "glyphicon glyphicon-unchecked",selectedIcon: "glyphicon glyphicon-check"});
        	}else{
        		tree_data[tree_data.length-1].nodes = [];
        		tree_data[tree_data.length-1].nodes.push({text:arr[i].title,icon: "glyphicon glyphicon-unchecked",selectedIcon: "glyphicon glyphicon-check"});
        	}
        }else if (arr[i].lvl === 3) {
        	if (tree_data[tree_data.length-1].nodes[tree_data[tree_data.length-1].nodes.length-1].nodes) {
        		tree_data[tree_data.length-1].nodes[tree_data[tree_data.length-1].nodes.length-1].nodes.push({text:arr[i].title,icon: "glyphicon glyphicon-unchecked",selectedIcon: "glyphicon glyphicon-check"});
        	}else{
        		tree_data[tree_data.length-1].nodes[tree_data[tree_data.length-1].nodes.length-1].nodes = [];
        		tree_data[tree_data.length-1].nodes[tree_data[tree_data.length-1].nodes.length-1].nodes.push({text:arr[i].title,icon: "glyphicon glyphicon-unchecked",selectedIcon: "glyphicon glyphicon-check"});
        	}
        }
    }
    return tree_data;
}

$('#subUG').on('click', function() {
	var filter = $('#uGroupTable_filter input').val() || '';
	if ($('#UGModal h4').html() === '创建用户组') {
		AjaxKeyPost('/Customer/Home/Group/addAuthGroup', {'title':$('#userGroup').val()}, function(data){
    		resultMsg(data, '用户组添加成功', '#UGModal');
    		updateUGroup(currentPage, {'name':filter});
    	});
	}else if ($('#UGModal h4').html() === '修改用户组') {
		var data ={
			'title': $('#userGroup').val(),
			'id': myData.idUG,
			'rules': myData.treeArr
		};
		AjaxKeyPost('/Customer/Home/Group/editAuthGroup', data, function(data) {
			resultMsg(data, '用户组修改成功', '#UGModal');
    		updateUGroup(currentPage, {'name':filter});
			myData.treeArr = [];
		});
		
	}
});

$('#subRule').on('click', function() {
	if ($('#ruleModal h4').html() === '新增') {
		var ruleName = $('#ruleName').val();
		var ruleTitle = $('#ruleTitle').val();
		var ruleStatus = $('#ruleStatus').val();
		var ruleAuthType = $('#ruleAuthType').val();
		var ruleAuthIcon = $('#ruleIcon').val();
		if(ruleName == ' ' || !ruleName){
			$errorInfo.text('请输入规则名！');
			return;
		}
		if(ruleTitle == ' ' || !ruleTitle){
			$errorInfo.text('请输入规则描述！');
			return;
		}
		var pid = 0;
		var data = {
			"name": ruleName,
			"title": ruleTitle,
			"pid": pid,
			"status": ruleStatus,
			"auth_type":ruleAuthType,
			"icon":ruleAuthIcon
		};
		AjaxKeyPost('/Customer/Home/Group/addAuthRule', data, function(data) {
			var filter = $('#uRuleTable_filter input').val() || '';
			resultMsg(data, '权限规则添加成功', '#ruleModal');
    		updateRules(currentPage, {'name':filter});
		});
	}else if ($('#ruleModal h4').html() === '修改') {
		var obj = $('#uRuleTable .checkSelected td:eq(0)');
		myData.ruleId = obj.data('id');
		var ruleName = $('#ruleName').val();
		var ruleTitle = $('#ruleTitle').val();
		var ruleStatus = $('#ruleStatus').val();
		var ruleAuthType = $('#ruleAuthType').val();
		var ruleAuthIcon = $('#ruleIcon').val();
		if(ruleName == ' ' || !ruleName){
			$errorInfo.text('请输入规则名！');
			return;
		}
		if(ruleTitle == ' ' || !ruleTitle){
			$errorInfo.text('请输入规则描述！');
			return;
		}
		var pid = 0;
		var data = {
			"id": myData.ruleId,
			"name": ruleName,
			"title": ruleTitle,
			"pid": pid,
			"status": ruleStatus,
			"auth_type":ruleAuthType,
			"icon":ruleAuthIcon
		};
		AjaxKeyPost('/Customer/Home/Group/editAuthRule', data, function(data) {
			var filter = $('#uRuleTable_filter input').val() || '';
			resultMsg(data, '权限规则修改成功', '#ruleModal');
    		updateRules(currentPage, {'name':filter});
		});
	}else if ($('#ruleModal h4').html() === '新增子权限') {
		var ruleName = $('#ruleName').val();
		var ruleTitle = $('#ruleTitle').val();
		var ruleStatus = $('#ruleStatus').val();
		var rulePreMenu = $('#preMenu').val();
		var ruleCss = $('#ruleCss').val();
		var ruleSort = $('#ruleSort').val();
		var ruleAuthType = $('#ruleAuthType').val();
		var ruleAuthIcon = $('#ruleIcon').val();
		
		if(ruleName == ' ' || !ruleName){
			$errorInfo.text('请输入规则名！');
			return;
		}
		if(ruleTitle == ' ' || !ruleTitle){
			$errorInfo.text('请输入规则描述！');
			return;
		}
		var data = {
			"name": ruleName,
			"title": ruleTitle,
			"pid": rulePreMenu,
			"status": ruleStatus,
			"css": ruleCss,
			"sort": ruleSort,
			"auth_type":ruleAuthType,
			"icon":ruleAuthIcon
		};
		AjaxKeyPost('/Customer/Home/Group/addAuthRule', data, function(data) {
			var filter = $('#uChildrenRuleTable_filter input').val() || '';
			resultMsg(data, '权限规则添加成功', '#ruleModal');
    		updateChildrenRules(currentPage, {'name':filter});
		});
	}else if ($('#ruleModal h4').html() === '修改子权限') {
		var obj = $('#uChildrenRuleTable .checkSelected td:eq(0)');
		myData.ruleId = obj.data('id');
		var ruleName = $('#ruleName').val();
		var ruleTitle = $('#ruleTitle').val();
		var ruleStatus = $('#ruleStatus').val();
		var ruleAuthType = $('#ruleAuthType').val();
		var ruleAuthIcon = $('#ruleIcon').val();

		var rulePreMenu = $('#preMenu').val();
		var ruleCss = $('#ruleCss').val();
		var ruleSort = $('#ruleSort').val();
		if(ruleName == ' ' || !ruleName){
			$errorInfo.text('请输入规则名！');
			return;
		}
		if(ruleTitle == ' ' || !ruleTitle){
			$errorInfo.text('请输入规则描述！');
			return;
		}
		var pid = myData.childrenPid;
		var data = {
			"id": myData.ruleId,
			"name": ruleName,
			"title": ruleTitle,
			"pid": rulePreMenu,
			"status": ruleStatus,
			"css": ruleCss,
			"sort": ruleSort,
			"auth_type":ruleAuthType,
			"icon":ruleAuthIcon
		};
		// console.log(data.id);
		// return;
		AjaxKeyPost('/Customer/Home/Group/editAuthRule', data, function(data) {
			var filter = $('#uChildrenRuleTable_filter input').val() || '';
			resultMsg(data, '权限规则修改成功', '#ruleModal');
    		updateChildrenRules(currentPage, {'name':filter});
		});
	}
});

$('#checkAll').on('click', function() {
	if ($(this).text() === '全选') {
		$('#tree').treeview('checkAll', { silent: true });
		$(this).text('全不选');
	}else{
		$('#tree').treeview('uncheckAll', { silent: true });
		$(this).text('全选');
	}
});

function updateUser(page, val){
	val = val || '';
	AjaxGet('/Customer/home/user/userLists?name='+val.name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedLists = [];
		myData.userId = null;
		createUser(data, page);
	});
}

function setPower(id, user, type){
	if(type === 'false'){
		$('#'+ id +' .' + user).prop('checked', false);
	}else{
		$('#'+ id +' .' + user).prop('checked', true);
	}
}

//创建用户
$('#subUser').on('click', function(){
	var newName = $('#newName').val();
	var userPwd = $('#userPwd').val();
	var newNickName = $('#newNickName').val();
	var resetUserPwd = $('#resetUserPwd').val();
	var permission = $('#power').val();
	var email = $('#newEmail').val();
	var note = $('#newNote').val();
	var filter = $('#adminTable_filter input').val() || '';
	var regExp = new RegExp(/^[a-zA-Z][\w+]{0,64}$/);
	var regExp2 = new RegExp(/^[a-zA-Z0-9~!#@$%^&*()]{8,20}$/);
	var pattern = new RegExp(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/);
	var $errorInfo = $('#createUser #errorInfo');
	var data = {};

	if(!regExp.test(newName)){
		$errorInfo.text('你的用户名不符合规则！');
		return;
	}
	if(newNickName == ' ' || !newNickName){
		$errorInfo.text('请输入昵称！');
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
	if (!pattern.test(email)) {
		$errorInfo.text('你的邮箱不符合规则！');
		return;
	}
	data = {
		"user": newName,
		"name": newNickName,
		"passwd": userPwd,
		"permission": permission,
		"email": email,
		"note": note
	};

	AjaxPost('/Customer/home/user/addUser', data, function () {
		$('#userModal').modal('hide');
		updateUser(currentPage, {'name':filter});
		return;
	}, $errorInfo);
	$('#breadcrumb').show();
	$('#page-content').show();
	$('#headTab').hide();
	$('#createUser').hide();
});

//提交新密码
$('#subPwd').on('click', function(){
	var userName = $('#userName').val();
	var newPwd = $('#newPwd').val();
	var resetPwd = $('#resetPwd').val();
	var regExp = new RegExp(/^[a-zA-Z0-9~!#@$%^&*()]{8,20}$/);
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

	AjaxPost('/Customer/home/user/modifyPasswd', data, function () {
		$('#pwdModal').modal('hide');
		if(myData.userName === window.localStorage.getItem("CUSTOM_PERMISSION_USERNAME")){
			window.location.href = myConfig.logOutUrl;
		}
		return ;
	}, $errorInfo);
});

//修改信息
$('#subEdit').on('click', function(){
	var editPower = $('#editPower').val();
	var editEmail = $('#editEmail').val();
	var editNote = $('#editNote').val();
	var editNickName = $('#editNickName').val();
	var pattern = /\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/,
	    filter = $('#adminTable_filter input').val() || '';
	var $errorInfo = $('#editModal .error-info');

	if(newNickName == ' ' || !newNickName){
		$errorInfo.text('请输入昵称！');
		return;
	}

	if (!pattern.test(editEmail)) {
		$errorInfo.text('你的邮箱不符合规则！');
		return;
	}

	var data = {
		"user": myData.userName,
		"email": editEmail,
		"name": editNickName,
		"note": editNote,
		"permission": editPower
	};

	AjaxPost('/Customer/home/user/modifyUserInfo', data, function(){
		$('#editModal').modal('hide');
		updateUser(currentPage, {'name':filter});
		return;
	});
});

function createUser(data, page){
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.user, arr.name, arr.group_name, arr.email, arr.note, arr.permission, arr.group_id]);
    }
    myDataTable('#adminTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
        "ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'用户名','width':'20%', 'targets':1},
            {'title':'昵称','width':'20%', 'targets':2},
            {'title':'权限','width':'15%', 'targets':3},
            {'title':'邮箱','width':'15%', 'targets':4},
            {'title':'备注','width':'15%', 'targets':5},
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"name": aData[1],
				"nickName": aData[2],
				"permission": aData[6],
				"email": aData[4],
				"note": aData[5],
				"group_id": aData[7]
			});
		}
	});
	toolbar = [];
	initToolBtn(myData.data.retval, '用户管理');
	initToolBar('#adminTable', toolbar);
	updatePagination(len, page, data.count, 'adminTable');
	listenCheckBox('#adminTable');
    updateChecked('#adminTable');
}

function updateUGroup(page, val, order) {
	if (!val) {
		val = {
			'name': '',
			'sort': ''
		};

	}else{
		if (!val.name) {
			val.name = '';
		}
		if (!val.sort) {
			val.sort = '';
		}
	}
	name = name || '';
	AjaxGet('/Customer/Home/Group/authGroupList?name='+val['name']+'&sort='+val['sort']+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedLists = [];
		myData.idUG = null;
		createUGroup(data, currentPage);
	});
}

function updateRules(page, val, order) {
	val = val || '';
	if (typeof val  === "object" ) {
		var searchStr='&name='+val.name;
	}else{
		var searchStr='';
	}
	AjaxGet('/Customer/Home/Group/authRuleList?pid=0'+'&page='+ page +'&pageSize='+pageSize+searchStr, function(data) {
		myData.checkedLists = [];
		createRules(data,currentPage);
	});
}

function updateChildrenRules(page, val, order) {
	var val = val || '';
	if (typeof val  === "object" ) {
		var searchStr='&name='+val.name;
	}else{
		var searchStr='';
	}
	AjaxGet('/Customer/Home/Group/authRuleList?pid='+myData.childrenPid+'&page='+ page +'&pageSize='+pageSize+searchStr, function(data) {
		myData.checkedLists = [];
		createChildrenRules(data,currentPage);
	});
}

function createUGroup(data, page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        dataArr.push(['', arr.id, arr.title, arr.status, arr.rules, formatDate(arr.add_time)]);
    }
    myDataTable('#uGroupTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
        "ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'ID','width':'20%', 'targets':1},
            {'title':'组名','width':'20%', 'targets':2},
            {'title':'状态','width':'15%', 'targets':3},
            {'title':'权限','width':'15%', 'targets':4},
            {'title':'添加时间','width':'15%', 'targets':5},
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[1],
				"title": aData[2],
				"status": aData[3],
				"rules": aData[4],
				"add_time": aData[5]
			});
			$('td:eq(4)', nRow).html('<i class="glyphicon glyphicon-align-justify icon-black my-icon" data-per="memberManager"></i>');
		}
	});

    toolbar = [];
	initToolBtn(myData.data.retval, '用户组管理');
	initToolBar('#uGroupTable', toolbar);
	updatePagination(len, page, data.retval.count, 'uGroupTable');
	listenCheckBox('#uGroupTable');
    updateChecked('#uGroupTable');
}

function createRules(data, page) {
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        dataArr.push([arr.id, arr.name, arr.title, arr.status, arr.pid, arr.sort,arr.auth_type,arr.icon]);
    }
    myDataTable('#uRuleTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
        "ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'路径','width':'20%', 'targets':1},
            {'title':'名称','width':'20%', 'targets':2},
            {'title':'状态','width':'15%', 'targets':3},
            {'title':'子权限','width':'15%', 'targets':4},
            {'title':'排序','width':'15%', 'targets':5}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(4)', nRow).html('<label class="position-relative"><i class="glyphicon glyphicon-align-justify icon-black my-icon" data-per="memberManager"></i></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0], 
				"pid": aData[4],
				"name": aData[1],
				"title": aData[2],
				"status": aData[3],
				"sort": aData[5],
				"auth_type": aData[6],
				"icon": aData[7]
			});
		}
	});

    toolbar = [];
	initToolBtn(myData.data.retval, '权限管理');
	initToolBar('#uRuleTable', toolbar);
	updatePagination(len, page, data.retval.count, 'uRuleTable');
	listenCheckBox('#uRuleTable');
    updateChecked('#uRuleTable');
    $('.glyphicon-align-justify').on('click',function() {
    	var obj = $(this).parent().parent().parent().children(':eq(0)');
		AjaxGet('/Customer/Home/Group/authRuleList?pid='+obj.data('id')+'&page=1'+'&all=1', function(data) {
        	myData.checkedLists = [];
        	$('#sum').hide();
        	$('#sub').show();
        	updateRules(currentPage, {'name':''});
        	myData.childrenPid = obj.data('id');
        	createChildrenRules(data, 1);
        });
	});
}

function createChildrenRules(data, page) {
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        dataArr.push([arr.id, arr.name, arr.title, arr.status,arr.sort, arr.pid, arr.type, arr.css, arr.condition, arr.add_time,arr.auth_type,arr.icon]);
    }
    myDataTable('#uChildrenRuleTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
        "ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'路径','width':'20%', 'targets':1},
            {'title':'名称','width':'20%', 'targets':2},
            {'title':'状态','width':'15%', 'targets':3},
            {'title':'排序','width':'15%', 'targets':4}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"name": aData[1],
				"title": aData[2],
				"status": aData[3],
				"sort": aData[4],
				"pid": aData[5],
				"type": aData[6],
				"css": aData[7],
				"condition": aData[8],
				"add_time": aData[9],
				"auth_type": aData[10],
				"icon": aData[11]
			});
		}
	});

	initToolBar('#uChildrenRuleTable', [
		'<a class="btn my-btn backBtn" href="javascript:"><i class="iconfont icon-icon-user-add"></i>&nbsp;返回</a>',
		'<a class="btn my-btn addSubRuleBtn" href="javascript:"><i class="iconfont icon-icon-user-add"></i>&nbsp;新增</a>',
		'<a class="btn my-btn editSubRuleBtn" href="javascript:"><i class="iconfont icon-yonghufankui03"></i>&nbsp;修改</a>',
		'<a class="btn my-btn delSubRuleBtn" href="javascript:"><i class="iconfont icon-shanchu"></i>&nbsp;删除</a>'
	]);
	updatePagination(len, page, data.retval.count, 'uChildrenRuleTable');
	listenCheckBox('#uChildrenRuleTable');
    updateChecked('#uChildrenRuleTable');
}

function hidePower() {
    if (window.localStorage.getItem("CUSTOM_PERMISSION_USEPOWER") === '客服管理员') {
    	$('#editPower option:eq(3)').remove();
    	$('#power option:eq(3)').remove();
    }
}

function selectPower(data){
	var arr = data.retval.list;
    var con = '<option value="请选择vendorID">请选择用户权限</option>';
    var $select = $('#power');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].title+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        disable_search: true,
        width: "463px"
    });
}

function selectEditPower(data,selectGroupId){
	var arr = data.retval.list;
    var con = '<option value="请选择vendorID">请选择用户权限</option>';
    var $select = $('#editPower');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].title+'</option>';
    }
    
    $select.html(con).on('chosen:ready')
    if(selectGroupId !== undefined && selectGroupId !='' && selectGroupId > 0){
    	$select.val(selectGroupId)
    }
    $select.chosen({
        allow_single_deselect: true,
        disable_search: true,
        width: "463px"
    }).trigger("chosen:updated");
    
    /*$select.trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        disable_search: true,
        width: "463px"
    });*/
}

function selectPreMenu(data, presentPid,defaultSelect){
	var arr = data.retval.list;
    var con = '<option value="' + presentPid + '">/</option>';
    var $select = $('#preMenu');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].title+'</option>';
    }
    
    $select.html(con).on('chosen:ready')
    if(defaultSelect !== undefined && defaultSelect !='' && defaultSelect > 0){
    	$select.val(defaultSelect)
    }
    $select.chosen({
    	 allow_single_deselect: true,
         disable_search: true,
         width: "463px"
    }).trigger("chosen:updated");
   /* $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        disable_search: true,
        width: "463px"
    });*/
}

function findChild(node) {
    if (node.nodes) {
    	if (node.nodes.length > 0) {
        	for (var i = 0; i < node.nodes.length; i++) {
        		tmp.push(node.nodes[i].nodeId);
            	findChild(node.nodes[i]);
        	}
    	}
	}
    return tmp;
}

function findParent(node, data) {
	if (data.retval[node.nodeId].lvl > 1) {
        if (data.retval[node.nodeId].lvl === 2) {
            for (var i = node.nodeId; i >= 0; i--) {
            	if(data.retval[i].lvl === 1){
            		tmp.push(i);
            		break;
            	}
            }
        }else if (data.retval[node.nodeId].lvl === 3) {
        	for (var i = node.nodeId; i >= 0; i--) {
            	if(data.retval[i].lvl === 2){
            		tmp.push(i);
            		continue;
            	}
            	if(data.retval[i].lvl === 1){
            		tmp.push(i);
            		break;
            	}
            }
        }
    }
    return tmp;
}