//@ sourceURL=appManager.app.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/app/appGroupLists?page=1&pageSize='+pageSize, function(data) {
        createElem(data, 1);
        trHover('#groupTable');
    });

    trclick('#groupTable', function(obj, e) {
        myData.groupId = obj.data('id');
        myData.groupName = obj.data('name');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//版本列表
            AjaxGet('/app/appGroupMemberLists?groupId='+ myData.groupId, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createList(data);
                trHover('#listTable');
                $('.breadcrumb').append('<li class="active">'+myData.groupName+'</li>');
            });
            return false;
        }
    });

    trclick('#listTable', function(obj, e) {
        myData.appId = obj.data('id');
        myData.appName = obj.data('appName');
        myData.pkgName = obj.data('pkgName');
    });

    listenMyPage('groupTable', currentPage);
});

listenToolbar('add', addTableInfo, '#groupTable');
listenToolbar('del', delTableInfo, '#groupTable');
listenToolbar('edit', editTableInfo, '#groupTable');
listenToolbar('back', backTable, '#listTable');
listenToolbar('add', addTableInfo2, '#listTable');
listenToolbar('del', delTableInfo2, '#listTable');
listenToolbar('edit', editTableInfo2, '#listTable');

function addTableInfo(){
	$('#groupName').val('');
    $('#groupModal h4').text('添加');
    $('#groupModal').modal('show');
}

function editTableInfo(){
    if (myData.groupId) {
        $('#groupName').val(myData.groupName);
        $('#groupModal h4').text('修改');
        $('#groupModal').modal('show');
    } else {
        alert('请选择应用组！');
    }
}

function delTableInfo(){
	if (myData.groupId) {
        if (confirm('确定删除？')) {
            var filter = $('#groupTable_filter input').val() || '';
            AjaxGet('/app/deleteAppGroup?id=' + myData.groupId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择应用组！');
    }
}

function updateTable(page, name){
    name = name || '';
	AjaxGet('/app/appGroupLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.groupId = null;
    });
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.appId = null;
}

function addTableInfo2(){
    $('#appName').val('');
    $('#pkgName').val('');
    $('#listModal h4').text('添加');
    $('#listModal').modal('show');
}

function editTableInfo2(){
    if (myData.appId) {
        $('#appName').val(myData.appName);
        $('#pkgName').val(myData.pkgName);
        $('#listModal h4').text('修改');
        $('#listModal').modal('show');
    } else {
        alert('请选择应用！');
    }
}

function delTableInfo2(){
    if (myData.appId) {
        if (confirm('确定删除？')) {
            AjaxGet('/app/deleteAppGroupMember?id=' + myData.appId, function() {
                updateTable2();
            });
        }
    } else {
        alert('请选择应用！');
    }
}

function updateTable2(){
    AjaxGet('/app/appGroupMemberLists?groupId='+ myData.groupId, function(data){
        createList(data);
        myData.appId = null;
    });
}

$('#subGroup').on('click', function(){
    var groupName = $('#groupName').val();
    var title = $('#groupModal h4').text();
    var filter = $('#groupTable_filter input').val() || '';
    var data = {};

    if(groupName == ' ' || !groupName){
        alert('请输入应用组名称');
        return false;
    }
    data = {"name": groupName};

    if(title === '添加'){
    	AjaxPost('/app/addAppGroup', data, function(){
            $('#groupModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        data.id = myData.groupId;
        AjaxPost('/App/modifyAppGroup', data, function(){
            $('#groupModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

//创建应用组
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, null]);
    }
    $('#groupTable').dataTable({
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
            [0, "desc"]
        ],
        "columnDefs": [
        {
            'title': 'ID',
            'width': '25%',
            'targets': 0
        },{
            'title': '应用组名称',
            'width': '50%',
            'targets': 1
        },{
            'title': '应用列表',
            'width': '25%',
            'targets': 2
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            tableTdIcon(2, nRow, 'list');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'groupTable');
    initToolBar('#groupTable');
}


$('#subList').on('click', function(){
    var appName = $('#appName').val();
    var pkgName = $('#pkgName').val();
    var title = $('#listModal h4').text();
    var filter = $('#listTable_filter input').val() || '';
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
        data.groupId = myData.groupId;
        AjaxPost('/app/addAppGroupMember', data, function(){
            $('#listModal').modal('hide');
            updateTable2();
        });
    }else if(title === '修改'){
        data.id = myData.appId;
        AjaxPost('/App/modifyAppGroupMember', data, function(){
            $('#listModal').modal('hide');
            updateTable2();
        });
    }
});

//创建列表
function createList(data){
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName, arr.pkgName]);
    }
    myDataTable('#listTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'应用名称','width':'20%', 'targets':1},
            {'title':'应用包名','width':'35%', 'targets':2}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "appName": aData[1],
                "pkgName": aData[2]
            });
        }
    });
    initToolBar('#listTable', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}