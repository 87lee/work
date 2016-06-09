//@ sourceURL=appManager.blackGroup.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/App/blacklistLists', function(data) {
        createBlack(data, 1);
        trHover('#blackTable');
    });

    trclick('#blackTable', function(obj, e) {
        myData.blackId = obj.data('id');
        myData.blackName = obj.data('name');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-align-justify') != -1){
            AjaxGet('/App/blacklistAppLists?blacklistId='+ myData.blackId +'&page=1&pageSize=' + pageSize, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createElem(data, 1);
                trHover('#myTable');
                $('.breadcrumb').append('<li class="active">'+myData.blackName+'</li>');
            });
        }
    });

    trclick('#myTable', function(obj, e) {
        myData.appId = obj.data('id');
        myData.pkgName = obj.data('pkgName');
        myData.appName = obj.data('appName');
    });

    listenMyPage();
});

listenToolbar('edit', editTableInfo2, '#blackTable');
listenToolbar('add', addTableInfo2, '#blackTable');
listenToolbar('del', delTableInfo2, '#blackTable');

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);
listenToolbar('back', backTable);

function addTableInfo2() {
    $('#blackGroup').val('');
    $('#blackModal h4').text('添加');
    $('#blackModal').modal('show');
}

function editTableInfo2() {
    if (myData.blackId) {
        $('#blackGroup').val(myData.blackName);
        $('#blackModal h4').text('修改');
        $('#blackModal').modal('show');
    } else {
        alert('请选择黑名单组！');
    }
}

function delTableInfo2() {
    if (myData.blackId) {
        if (confirm('确定删除？')) {
            AjaxGet('/App/deleteBlacklist?id=' + myData.blackId, function() {
                updateTable2();
            });
        }
    } else {
        alert('请选择黑名单组！');
    }
}

function updateTable2(){
    AjaxGet('/App/blacklistLists', function(data){
        createBlack(data);
        myData.blackId = null;
    });
}

function addTableInfo() {
    $('#appName').val('');
    $('#pkgName').val('');
    $('#appModal h4').text('添加');
    $('#appModal').modal('show');
}

function editTableInfo() {
	if (myData.appId) {
        $('#appName').val(myData.appName);
        $('#pkgName').val(myData.pkgName);
        $('#appModal h4').text('修改');
	    $('#appModal').modal('show');
	} else {
		alert('请选择应用！');
	}
}

function delTableInfo() {
    if (myData.appId) {
        if (confirm('确定删除？')) {
        	var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/App/deleteBlacklistApp?id=' + myData.appId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择应用！');
    }
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/App/blacklistAppLists?blacklistId='+ myData.blackId +'&name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.appId = null;
    });
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.appId = null;
}

$('#subBlack').on('click', function(){
	var blackGroup = $('#blackGroup').val();
    var title = $('#blackModal h4').text();
    var data = {};

	if(blackGroup == ' ' || !blackGroup){
		alert('请输入黑名单组名称');
		return false;
	}

    data = {"name": blackGroup};
    if(title === '添加'){
        AjaxPost('/App/addBlacklist', data, function(){
            $('#blackModal').modal('hide');
            updateTable2();
        });
    }else if(title === '修改'){
        data.id = myData.blackId;
        AjaxPost('/App/modifyBlacklist', data, function(){
            $('#blackModal').modal('hide');
            updateTable2();
        });
    }
});

$('#subApp').on('click', function(){
    var appName = $('#appName').val();
    var pkgName = $('#pkgName').val();
    var title = $('#appModal h4').text();
    var filter = $('#myTable_filter input').val() || '';
    var data = {};

    if(appName == ' ' || !appName){
        alert('请输入应用名称');
        return false;
    }
    if(pkgName == ' ' || !pkgName){
        alert('请输入包名');
        return false;
    }
    data = {"appName": appName, "pkgName": pkgName};
    if(title === '添加'){
        data.blacklistId = myData.blackId;
        AjaxPost('/App/addBlacklistApp', data, function(){
            $('#appModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        data.id = myData.appId;
        AjaxPost('/App/modifyBlacklistApp', data, function(){
            $('#appModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

//创建更多应用黑名单组列表
function createBlack(data) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, null]);
    }
    myDataTable('#blackTable', {
        "data": dataArr,
        "order": [
            [1, "desc"]
        ],
        "columnDefs": [
        {
            'title': 'ID',
            'width': '8%',
            'targets': 0
        },{
            'title': '黑名单组',
            'width': '16%',
            'targets': 1
        },{
            'title': '黑名单应用',
            'width': '16%',
            'targets': 2
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            tableTdIcon(2, nRow, 'align-justify');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1]
            });
        }
    });
    initToolBar('#blackTable');
}

//创建更多应用黑名单列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName, arr.pkgName]);
    }
    $('#myTable').dataTable({
        "lengthChange": false,
        "autoWidth": false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "data": dataArr,
        "order": [
            [1, "desc"]
        ],
        "columnDefs": [
        {
            'title': 'ID',
            'width': '8%',
            'targets': 0
        },{
            'title': '应用名称',
            'width': '16%',
            'targets': 1
        },{
            'title': '包名',
            'width': '16%',
            'targets': 2
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "appName": aData[1],
                "pkgName": aData[2]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}