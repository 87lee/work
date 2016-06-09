//@ sourceURL=user.application.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //应用列表当前的页面
var appPage = 1; //应用当前的页面
var rulePage = 1; //规则当前的页面

$(function () {
	myData.checkedLists = [];   //应用列表存储check选中项
	myData.checkedItems = [];	//应用存储check选中项
	myData.checkedRules = [];	//应用存储check选中项

	AjaxGet('/Android/home/user/appLists?page=1&pageSize=' + pageSize, function(data){
        createApp(data, 1);
    });

    listenSingleCheckBox('#uAppTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.appId = obj.data('id');
    }, true);

    listenSingleCheckBox('#uAppListTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.appListId = obj.data('id');
    });

    listenSinglecheckBox('#uRuleTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.ruleId = obj.data('id');
    });

    checkMoz();
    listenMyPage('uAppTable', appPage, updateApp);
    listenMyPage('uAppListTable', currentPage, updateAppList);
    listenMyPage('uRuleTable', rulePage, updateRule);

    listenTab(function(str){
    	if(str === '应用列表'){
    		appPage = 1;
    		$('#uAppTable_filter input').val('');
    		updateApp(appPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(0)').show();
    	}else if(str === '应用管理列表'){
    		currentPage = 1;
    		$('#uAppListTable_filter input').val('');
    		updateAppList(currentPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(1)').show();
    	}else if(str === '发布规则'){
    		$('#uRuleTable tbody tr').css('background', '');
    		rulePage = 1;
    		updateRule(rulePage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(2)').show();
    	}
    });

    selectChosen($('#userAction'));
    selectChosen($('#ruleOperator'));
    selectChosen($('#ruleCondition'));
    selectChosen($('#dependencies'));

    $('#attrName').on('keyup',function() {
    	console.log('input');
    	if ($(this).val()) {
    		$('#attrValue').parent().show();
    		$('#attrPoint').parent().show();
    	}else{
    		$('#attrValue').parent().hide();
    		$('#attrPoint').parent().hide();
    	}
    });
});

listenToolbar('add', addApp, '#uAppTable');
listenToolbar('del', delApp, '#uAppTable');

listenToolbar('add', addAppList, '#uAppListTable');
listenToolbar('del', delAppList, '#uAppListTable');

listenToolbar('add', addRule, '#uRuleTable');
listenToolbar('edit', editRule, '#uRuleTable');
listenToolbar('del', delRule, '#uRuleTable');

function addApp(){
	$('#newAppName').val('');
	$('#newRemark').val('');
	$('#newPkgName').val('');
	$('#uAppModal .error-info').text('');
	$('#uAppModal').modal('show');
}

function delApp(){
	if(myData.checkedItems.length){
		if (confirm('确定删除？')) {
			var filter = $('#uAppTable_filter input').val() || '';
			AjaxPost('/Android/home/user/deleteApp', myData.checkedItems, function () {
				updateApp(appPage, filter);
				return;
			});
		}
	}else{
		alert('请选择应用！');
		return;
	}
}

function updateApp(page, name){
	name = name || '';
	AjaxGet('/Android/home/user/appLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedItems = [];
		myData.appId = null;
		createApp(data, page);
	});
}

function addAppList(){
	AjaxWhen([
        AjaxGet('/Android/home/user/testerLists', selectUser, true),
        AjaxGet('/Android/home/user/appLists', selectApp, true)
    ], function(){
		$('#remark').val('');
		$('#uAppListModal .error-info').text('');
		$('#uAppListModal').modal('show');
    });
}

function delAppList(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#uAppListTable_filter input').val() || '';
			AjaxPost('/Android/home/user/deleteAppAdmin', myData.checkedLists, function () {
				updateAppList(currentPage, filter);
				return;
			});
		}
	}else{
		alert('请选择应用！');
		return;
	}
}

function updateAppList(page, name){
	name = name || '';
	AjaxGet('/Android/home/user/appAdminLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedLists = [];
		myData.appListId = null;
		createAppLists(data, page);
	});
}

function addRule(){
	AjaxWhen([
        AjaxGet('/Android/home/user/appLists', selectRuleApp, true),
    ], function(){

	$('#ruleColumn').val('');
	selectChosen($('#ruleOperator').val(''));
	$('#ruleParam').val('');
	selectChosen($('#ruleCondition').val('=='));
	$('#ruleValue').val('');
	$('#ruleNote').val('');
	$('#attrName').val('');
	$('#attrValue').val('');
	$('#attrValue').parent().hide();
	$('#attrPoint').parent().hide();
	$('#attrPoint').val('');
	$('#uRuleModal .error-info').text('');
	$('#uRuleModal h4').text('新增发布规则');
	$('#uRuleModal').modal('show');

	});
}

function editRule(){
	if(myData.checkedRules.length === 1){
    	AjaxGet('/Android/home/user/appLists', function(data) {
			var arr = data.extra;
    		var con = '';
    		var $select = $('#specifiedApp');
    		myData.ruleSpecApp = obj.data('specifiedApp');
    		for( var i=0; i<arr.length; i++ ){
    			
    			for (var j in myData.ruleSpecApp) {
    				if(myData.ruleSpecApp[j] === arr[i].name){
    					con += '<option selected value="'+arr[i].name+'">'+arr[i].app+'</option>';
    				}else{
    					con += '<option value="'+arr[i].name+'">'+arr[i].app+'</option>';
    				}
    			}
    		}
    		$select.html(con).trigger("chosen:updated.chosen").chosen({
        		allow_single_deselect: true,
        		width: "463px"
    		});
		});
		$('#uRuleModal .error-info').text('');
		var obj = $('.checkSelected td:eq(0)');
		myData.ruleId = obj.data('id');
		myData.ruleColumn = obj.data('column');
		myData.ruleOperator = obj.data('operator');
		myData.ruleParam = obj.data('param');
		myData.ruleCondition = obj.data('condition');
		myData.ruleValue = obj.data('value');
		myData.ruleNote = obj.data('note');
		myData.ruleDep = obj.data('dependencies');
		if (obj.data('attrName') === '--') {
			myData.attrName = '';
			myData.attrValue = '';
			myData.attrNode = '';
		}else{
			myData.attrName = obj.data('attrName');
			myData.attrValue = obj.data('attrValue');
			myData.attrNode = obj.data('attrNode');
		}
		if (myData.attrName == '' || !myData.attrName) {
			$('#attrName').val('');
			$('#attrValue').val('');
			$('#attrPoint').val('');
			$('#attrValue').parent().hide();
			$('#attrPoint').parent().hide();
		}else{
			$('#attrName').val(myData.attrName);
			$('#attrValue').val(myData.attrValue);
			$('#attrPoint').val(myData.attrNode);
			$('#attrValue').parent().show();
			$('#attrPoint').parent().show();
		}
		$('#ruleColumn').val(myData.ruleColumn);
		selectChosen($('#ruleOperator').val(myData.ruleOperator));
		$('#ruleParam').val(myData.ruleParam);
		selectChosen($('#ruleCondition').val(myData.ruleCondition));
		$('#ruleValue').val(myData.ruleValue);
		$('#ruleNote').val(myData.ruleNote);
		$('#dependencies').val(myData.ruleDep);
		$('#uRuleModal h4').text('修改发布规则');
		$('#uRuleModal').modal('show');
	}else{
		alert('请选择一种规则！');
		return;
	}

}

function delRule(){
	if(myData.checkedRules.length){
		if (confirm('确定删除？')) {
			var filter = $('#uRuleTable_filter input').val() || '';
			AjaxPost('/Android/Home/App/deleteAppPublishRule', myData.checkedRules, function () {
				updateRule(rulePage, filter);
				return;
			});
		}
	}else{
		alert('请选择规则！');
		return;
	}
}

function updateRule(page, name){
	name = name || '';
	AjaxGet('/Android/Home/App/appPublishRuleLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedRules = [];
		myData.ruleId = null;
		createRule(data, page);
	});
}

$('#userAction').on('change', function(){
	var $this = $(this);
	var str = $this.val();
	var url = '';

	if(str === 'test'){
		url = '/Android/home/user/testerLists';
	}else if(str === 'publish'){
		url = '/Android/home/user/publisherLists';
	}

	AjaxGet(url, function(data){
		selectUser(data);
	});
});

function selectChosen($select){
    $select.trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        disable_search: true,
        width: "463px"
    });
}

function selectUser(data){
	var arr = data.extra;
    var con = '<option value="请选择用户">请选择用户</option>';
    var $select = $('#userName');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].name+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "463px"
    });
}

function selectApp(data){
	var arr = data.extra;
    var con = '<option value="请选择应用">请选择应用</option>';
    var $select = $('#newApp');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].name+'">'+arr[i].app+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "463px"
    });
}

$('#subNewApp').on('click', function(){
	var newApp = $('#newAppName').val();
	var newPkg = $('#newPkgName').val();
	var remark = $('#newRemark').val();
	var filter = $('#uAppTable_filter input').val() || '';
	var $errorInfo = $('#uAppModal .error-info');
	var data = {};

	if(newApp == ' ' || !newApp.trim()){
		$errorInfo.text('请输入应用名！');
		return;
	}
	if(newPkg == ' ' || !newPkg.trim()){
		$errorInfo.text('请输入包名！');
		return;
	}
	data = {
		"app": newApp,
		"name": newPkg,
		"note": remark
	};

	AjaxPost('/Android/home/user/addApp', data, function(){
		$('#uAppModal').modal('hide');
		updateApp(appPage, filter);
		return;
	}, $errorInfo);
});

$('#subApp').on('click', function(){
	var userName = $('#userName').val();
	var userAction = $('#userAction').val();
	var newPkg = $('#newApp').val();
	var newApp = $('#newApp option:selected').text();
	var remark = $('#remark').val();
	var filter = $('#uAppListTable_filter input').val() || '';
	var $errorInfo = $('#uAppListModal .error-info');
	var data = {};

	if(userName == '请选择用户' || !userName){
		$errorInfo.text('请选择用户！');
		return;
	}

	if(newPkg == '请选择应用' || !newPkg){
		$errorInfo.text('请选择应用！');
		return;
	}

	data = {
		"app": newApp,
		"name": newPkg,
		"operator": userName,
		"operation": userAction,
		"notes": remark
	};

	AjaxPost('/Android/home/user/addAppAdmin', data, function(){
		$('#uAppListModal').modal('hide');
		updateAppList(currentPage, filter);
		return;
	}, $errorInfo);
});

$('#subRule').on('click', function(){
	var ruleColumn = $('#ruleColumn').val();
	var ruleOperator = $('#ruleOperator').val();
	var ruleParam = $('#ruleParam').val() || '';
	var ruleCondition = $('#ruleCondition').val();
	var ruleValue = $('#ruleValue').val();
	var ruleNote = $('#ruleNote').val();
	var title = $('#uRuleModal h4').text();
	var dep = $('#dependencies').val();
	var specApp = $('#specifiedApp').val();
	var attrNode = $('#attrPoint').val();
	var attrValue = $('#attrValue').val();
	var attrName = $('#attrName').val();

	console.log($('#attrPoint').val());

	var filter = $('#uRuleTable_filter input').val() || '';
	var $errorInfo = $('#uRuleModal .error-info');
	var data = {};

	if(ruleColumn == ' ' || !ruleColumn){
		$errorInfo.text('请输入字段！');
		return;
	}

	if(ruleOperator && (ruleParam == ' ' || !ruleParam)){
		$errorInfo.text('运算不为空必须输入参数！');
		return;
	}

	if(ruleCondition == ' ' || !ruleCondition){
		$errorInfo.text('请输入条件！');
		return;
	}

	if(ruleValue == ' ' || !ruleValue){
		$errorInfo.text('请输入取值！');
		return;
	}

	if (attrName) {
		if (attrValue == ' ' || !attrValue) {
			$errorInfo.text('请输入属性值！');
		}
		if (attrNode == ' ' || !attrNode) {
			$errorInfo.text('请输入属性节点！');
		}
	}

	data = {
		"column": ruleColumn,
		"operator": ruleOperator,
		"param": ruleParam,
		"condition": ruleCondition,
		"value": ruleValue,
		"note": ruleNote,
		"specifiedApp": specApp,
		"attrName": attrName,
		"attrValue": attrValue,
		"attrNode": attrNode
	};
	if(title === '新增发布规则'){
		AjaxPost('/Android/Home/App/addAppPublishRule', data, function(){
			$('#uRuleModal').modal('hide');
			updateRule(rulePage, filter);
			return;
		}, $errorInfo);
	}else if(title === '修改发布规则'){
		data.id = myData.ruleId;
		AjaxPost('/Android/Home/App/modifyAppPublishRule', data, function(){
			$('#uRuleModal').modal('hide');
			updateRule(rulePage, filter);
			return;
		}, $errorInfo);
	}
});

function createAppLists(data, page){
	var dataArr = [];
    var len = data.extra.length;
    var operation = {
    	"test": "测试",
    	"publish" : "发布"
    };
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.app, arr.name, arr.operator, operation[arr.operation], arr.note]);
    }
    myDataTable('#uAppListTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'应用名','width':'20%', 'targets':1},
            {'title':'包名','width':'20%', 'targets':2},
            {'title':'维护者','width':'15%', 'targets':3},
            {'title':'操作','width':'10%', 'targets':4},
            {'title':'备注','width':'30%', 'targets':5}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0]
			});
		}
	});

	initToolBar('#uAppListTable', [
		myConfig.addBtn,
		myConfig.delBtn
	]);

	updatePagination(len, page, data.count, 'uAppListTable');
	listenCheckBox('#uAppListTable');
    updateChecked('#uAppListTable');
}

function createApp(data, page){
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.app, arr.name, arr.note]);
    }
    myDataTable('#uAppTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'应用名','width':'30%', 'targets':1},
            {'title':'包名','width':'30%', 'targets':2},
            {'title':'备注','width':'35%', 'targets':3}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0]
			});
		}
	});

	initToolBar('#uAppTable', [
		myConfig.addBtn,
		myConfig.delBtn
	]);

	updatePagination(len, page, data.count, 'uAppTable');
	listenCheckBox('#uAppTable', true);
    updateChecked('#uAppTable', true);
}

function createRule(data, page){
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.column, arr.operator, arr.param, arr.condition, arr.value, arr.note, arr.attrName || '--', arr.attrValue || '--', arr.attrNode || '--', arr.specifiedApp]);
    }
    myDataTable('#uRuleTable', {
        "data": dataArr,
        "order": [[1, "asc"]],
		"columnDefs": [
			{'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'字段','width':'10%', 'targets':1},
            {'title':'运算','width':'10%', 'targets':2},
            {'title':'参数','width':'10%', 'targets':3},
            {'title':'条件','width':'10%', 'targets':4},
            {'title':'取值','width':'10%', 'targets':5},
            {'title':'备注','width':'14%', 'targets':6},
            {'title':'属性名','width':'7%', 'targets':7},
            {'title':'属性值','width':'7%', 'targets':8},
            {'title':'属性节点','width':'9%', 'targets':9},
            {'title':'指定的应用','width':'10%', 'targets':10}
        ],
		"createdRow": function(nRow, aData, idx) {
			if(aData[2] === '' || !aData[2]){
                $('td:eq(2)', nRow).html('--');
            }
            if(aData[3] === '' || !aData[3]){
                $('td:eq(3)', nRow).html('--');
            }
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"column": aData[1],
				"operator": aData[2],
				"param": aData[3],
				"condition": aData[4],
				"value": aData[5],
				"note": aData[6],
				"attrName": aData[7],
				"attrValue": aData[8],
				"attrNode": aData[9],
				"specifiedApp": aData[10],
			}).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
		}
	});

	initToolBar('#uRuleTable', [
		myConfig.addBtn,
		myConfig.editBtn,
		myConfig.delBtn
	]);

	updatePagination(len, page, data.count, 'uRuleTable');
	listencheckBox('#uRuleTable');
    updatechecked('#uRuleTable');
}

//table 更新checkbox
function updatechecked(table){
    table = table || '#myTable';
    var checkBoxs = $(table + ' tbody tr input');
    for(var j = 0, l = checkBoxs.length; j < l; j++){
        var $checkBox = $(checkBoxs[j]);
        var $td = $checkBox.parents('td');
        var $tr = $checkBox.parents('tr');
        var id = $td.data('id');
        if($.inArray(id, myData.checkedRules) !== -1){
            $tr.addClass('checkSelected');
            $checkBox.prop('checked', true);
        }
    }
    if($(table + ' tbody tr').not(".checkSelected").length > 0){
        $(table + ' thead tr input').prop('checked', false);
    }else{
        $(table + ' thead tr input').prop('checked', true);
    }
}

//table 监听checkbox
function listencheckBox(table) {
    table = table || '#myTable';
    $(table).off('click', 'tbody tr input').on('click', 'tbody tr input', function(ev) {
        var e = ev || event;
        var $this = $(this);
        var $tr = $this.parents('tr');
        var id = $this.parents('td').data('id');

        if ($this.prop('checked')) {
            $tr.addClass('checkSelected');
            myData.checkedRules.push(id);
        } else {
            $tr.removeClass('checkSelected');
            myData.checkedRules.remove(id);
        }
    });

    $(table).off('click', 'thead tr input').on('click', 'thead tr input', function(ev) {
        var e = ev || event;
        var $this = $(this);
        var inputs = $(table + ' tbody tr input');
        var i = 0, id = null;

        if ($this.prop('checked')) {
            $(table + ' tbody tr').addClass('checkSelected');
            inputs.prop('checked', true);
            for(i = inputs.length; i--;){
                id = $(inputs[i]).parents('td').data('id');
                if($.inArray(id, myData.checkedRules) === -1){
                    myData.checkedRules.push(id);
                }
            }
        } else {
            $(table + ' tbody tr').removeClass('checkSelected');
            inputs.prop('checked', false);
            for(i = inputs.length; i--;){
                id = $(inputs[i]).parents('td').data('id');
                if($.inArray(id, myData.checkedRules) !== -1){
                    myData.checkedRules.remove(id);
                }
            }
        }
    });
}

//监听 checkBox 单选
function listenSinglecheckBox(id, fn){
    $(id).on('click', 'tbody tr', function(ev) {
        var e = ev || event;
        var obj = $(this).find('input');
        var tagName = e.target.tagName.toLowerCase();
        var idx = $(e.target).index();
        if(tagName === 'td' && idx ===0){
            obj.trigger('click');
            return;
        }
        if(tagName !== 'input' && tagName !== 'span'){
            myData.checkedRules = [];
            $(id + ' input').prop('checked', false);
            $(id + ' tbody tr').removeClass('checkSelected');
            obj.trigger('click');
        }
        fn && fn(e);
    });
}

function selectRuleApp(data){
	var arr = data.extra;
    var con = '';
    var $select = $('#specifiedApp');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].name+'">'+arr[i].app+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "463px"
    });
}