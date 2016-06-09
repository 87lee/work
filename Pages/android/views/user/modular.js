//@ sourceURL=user.modular.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
var modularPage = 1; //模块当前的页面

$(function () {
	myData.checkedLists = [];   //模块列表存储check选中项
	myData.checkedItems = [];	//模块存储check选中项

	AjaxGet('/Android/home/user/ModuleLists?page=1&pageSize=' + pageSize, function(data){
        createModular(data, 1);
    });

    listenSingleCheckBox('#uModularTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.modularId = obj.data('id');
    }, true);

    listenSingleCheckBox('#uModularListTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
		myData.modularListId = obj.data('id');
    });

    checkMoz();
    listenMyPage('uModularTable', modularPage, updateModular);
    listenMyPage('uModularListTable', currentPage, updateModularList);

    listenTab(function(str){
    	if(str === '模块列表'){
    		modularPage = 1;
    		$('#uModularTable_filter input').val('');
    		updateModular(modularPage);
    		$('.tab-list:eq(1)').hide();
    		$('.tab-list:eq(0)').show();
    	}else if(str === '模块管理列表'){
    		currentPage = 1;
    		$('#uModularListTable_filter input').val('');
    		updateModularList(currentPage);
    		$('.tab-list:eq(0)').hide();
    		$('.tab-list:eq(1)').show();
    	}
    });

    selectAction();
});

listenToolbar('add', addModular, '#uModularTable');
listenToolbar('del', delModular, '#uModularTable');

listenToolbar('add', addModularList, '#uModularListTable');
listenToolbar('del', delModularList, '#uModularListTable');

function addModular(){
	$('#newModularName').val('');
	$('#newRemark').val('');
	$('#modularModal .error-info').text('');
	$('#modularModal').modal('show');
}

function delModular(){
	if(myData.checkedItems.length){
		if (confirm('确定删除？')) {
			var filter = $('#uModularTable_filter input').val() || '';
			AjaxPost('/Android/home/user/deleteModule', myData.checkedItems, function () {
				updateModular(modularPage, filter);
				return;
			});
		}
	}else{
		alert('请选择模块！');
		return;
	}
}

function updateModular(page, name){
	name = name || '';
	AjaxGet('/Android/home/user/ModuleLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedItems = [];
		myData.modularId = null;
		createModular(data, page);
	});
}


function addModularList(){
	AjaxWhen([
        AjaxGet('/Android/home/user/publisherLists', selectUser, true),
        AjaxGet('/Android/home/user/ModuleLists', selectModular, true)
    ], function(){
		$('#remark').val('');
		$('#modularListModal .error-info').text('');
		$('#modularListModal').modal('show');
    });
}

function delModularList(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#uModularListTable_filter input').val() || '';
			AjaxPost('/Android/home/user/deleteModuleAdmin', myData.checkedLists, function () {
				updateModularList(currentPage, filter);
				return;
			});
		}
	}else{
		alert('请选择模块！');
		return;
	}
}

function updateModularList(page, name){
	name = name || '';
	AjaxGet('/Android/home/user/moduleAdminLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedLists = [];
		myData.modularListId = null;
		createModularLists(data, page);
	});
}

function selectAction(){
    var $select = $('#userAction');
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

function selectModular(data){
	var arr = data.extra;
    var con = '<option value="请选择模块">请选择模块</option>';
    var $select = $('#newModular');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].name+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "463px"
    });
}

$('#subNewModular').on('click', function(){
	var newModular = $('#newModularName').val();
	var remark = $('#newRemark').val();
	var filter = $('#uModularTable_filter input').val() || '';
	var $errorInfo = $('#modularModal .error-info');
	var data = {};

	if(!newModular.trim()){
		$errorInfo.text('请输入模块名！');
		return;
	}

	data = {
		"name": newModular,
		"note": remark
	};

	AjaxPost('/Android/home/user/addModule', data, function(){
		$('#modularModal').modal('hide');
		updateModular(modularPage, filter);
		return;
	}, $errorInfo);
});

$('#subModular').on('click', function(){
	var userName = $('#userName').val();
	var userAction = $('#userAction').val();
	var newModular = $('#newModular').val();
	var remark = $('#remark').val();
	var filter = $('#uModularListTable_filter input').val() || '';
	var $errorInfo = $('#modularListModal .error-info');
	var data = {};

	if(userName == '请选择用户' || !userName){
		$errorInfo.text('请选择用户！');
		return;
	}

	if(newModular == '请选择模块' || !newModular){
		$errorInfo.text('请选择模块！');
		return;
	}

	data = {
		"names": newModular,
		"operator": userName,
		"operation": userAction,
		"notes": remark
	};

	AjaxPost('/Android/home/user/addModuleAdmin', data, function(){
		$('#modularListModal').modal('hide');
		updateModularList(currentPage, filter);
		return;
	}, $errorInfo);
});

function createModular(data, page){
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.note]);
    }
    myDataTable('#uModularTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'模块','width':'40%', 'targets':1},
            {'title':'备注','width':'55%', 'targets':2}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0]
			});
		}
	});

	initToolBar('#uModularTable', [
		myConfig.addBtn,
		myConfig.delBtn
	]);

	updatePagination(len, page, data.count, 'uModularTable');
	listenCheckBox('#uModularTable', true);
    updateChecked('#uModularTable', true);
}

function createModularLists(data, page){
	var dataArr = [];
    var len = data.extra.length;
    var operation = {
    	"publish": "维护"
    };
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.operator, operation[arr.operation], arr.note]);
    }
    myDataTable('#uModularListTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'模块','width':'20%', 'targets':1},
            {'title':'维护者','width':'20%', 'targets':2},
            {'title':'操作','width':'20%', 'targets':3},
            {'title':'备注','width':'35%', 'targets':4}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0]
			});
		}
	});

	initToolBar('#uModularListTable', [
		myConfig.addBtn,
		myConfig.delBtn
	]);

	updatePagination(len, page, data.count, 'uModularListTable');
	listenCheckBox('#uModularListTable');
    updateChecked('#uModularListTable');
}