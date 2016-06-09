//@ sourceURL=silent.silent.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/Silent/appGroupLists?page=1&pageSize='+pageSize, function(data) {
        createElem(data, 1);
        trHover('#groupTable');
    });

    trclick('#groupTable', function(obj, e) {
        myData.groupId = obj.data('id');
        myData.groupName = obj.data('name');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//版本列表
            AjaxGet('/Silent/appGroupItemLists?groupId='+ myData.groupId, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createApp(data);
                trHover('#appTable');
                $('.breadcrumb').append('<li class="active">'+myData.groupName+'</li>');
            });
            return false;
        }
    });

    trclick('#appTable', function(obj, e) {
        myData.appId = obj.data('id');
        myData.appName = obj.data('appName');
        myData.version = obj.data('version');
        myData.url = obj.data('url');
        myData.weight = obj.data('weight');
        myData.action = obj.data('action');
    });

    listenMyPage('groupTable', currentPage);
    listenchoose();
});

listenToolbar('add', addTableInfo, '#groupTable');
listenToolbar('del', delTableInfo, '#groupTable');
listenToolbar('edit', editTableInfo, '#groupTable');
listenToolbar('back', backTable, '#appTable');
listenToolbar('add', addTableInfo2, '#appTable');
listenToolbar('del', delTableInfo2, '#appTable');
listenToolbar('edit', editTableInfo2, '#appTable');

function addTableInfo(){
    AjaxGet('/Silent/appGroupLists', function(data){
        selectCopy(data);
        $('#copySelect').parent().show();
        $('#groupName').val('');
        $('#groupModal h4').text('添加');
        $('#groupModal').modal('show');
    });
}

function editTableInfo(){
    if (myData.groupId) {
        $('#copySelect').parent().hide();
        $('#groupName').val(myData.groupName);
        $('#groupModal h4').text('修改');
        $('#groupModal').modal('show');
    } else {
        alert('请选择组！');
    }
}

function delTableInfo(){
	if (myData.groupId) {
        if (confirm('确定删除？')) {
            var filter = $('#groupTable_filter input').val() || '';
            AjaxGet('/Silent/deleteAppGroup?id=' + myData.groupId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择组！');
    }
}

function updateTable(page, name){
    name = name || '';
	AjaxGet('/Silent/appGroupLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
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
    AjaxGet('/App/app3rdAndAppUpdateLists', function(data){
        selectApp(data);
    });
    $('#appAction input:eq(0)').trigger('click');
    $('#appModal h4').text('添加');
    $('#appModal').modal('show');
}

function editTableInfo2(){
    if (myData.appId) {
        AjaxGet('/App/app3rdAndAppUpdateLists', function(data){
            selectApp(data, {
                "appName": myData.appName,
                "version": myData.version,
                "url": myData.url
            });
            if(myData.action === '激活'){
                $('#appAction input:eq(2)').trigger('click');
            }else if(myData.action === '安装'){
                $('#appAction input:eq(0)').trigger('click');
            }else if(myData.action === '卸载'){
                $('#appAction input:eq(1)').trigger('click');
            }
            $('#appWeight').val(myData.weight === '--' ? '' : myData.weight);
            $('#appModal h4').text('修改');
            $('#appModal').modal('show');
        });
    } else {
        alert('请选择应用！');
    }
}

function delTableInfo2(){
    if (myData.appId) {
        if (confirm('确定删除？')) {
            AjaxGet('/Silent/deleteAppItemGroup?id=' + myData.appId, function() {
                updateTable2();
            });
        }
    } else {
        alert('请选择应用！');
    }
}

function updateTable2(){
    AjaxGet('/Silent/appGroupItemLists?groupId='+ myData.groupId, function(data){
        createApp(data);
        myData.appId = null;
    });
}

function selectCopy(data){
    var arr = data.extra;
    var con = '<option value="请选择组模板">请选择组模板</option>';
    var $select = $('#copySelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }

    $select.html(con);
}

function selectApp(data, app){
    var arr = data.extra;
    var con = '<option value="请选择应用名称">请选择应用名称</option>';
    var $select = $('#appName');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].appName + '">' + arr[i].appName + '</option>';
        $select.data('_' +arr[i].appName, arr[i].pkgName);
    }
    if(app && app.appName){
        $select.html(con).val(app.appName).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change', app);
    }else{
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change');
    }
}

$('#appName').on('change', function(e, app){
    var $this = $(this);
    var val = $this.val();
    if(val === '请选择应用名称'){
        $('#appVersion').html('<option value="请选择版本">请选择版本</option>').trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change');
        return false;
    }
    AjaxGet('/App/app3rdVersionAndAppUpdateVersionLists?name=' + val, function(data){
        selectVersion(data, app);
    });
});

function selectVersion(data, app){
    var arr = data.extra;
    var con = '<option value="请选择版本">请选择版本</option>';
    var $select = $('#appVersion');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].versionCode + '">' + arr[i].versionCode + '</option>';
        $select.data('_' + arr[i].versionCode, arr[i]);
    }
    if(app && app.version){
        $select.html(con).val(app.version).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change', app);
    }else{
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change');
    }
}

$('#appVersion').on('change', function(e, app){
    var $this = $(this);
    var val = $this.val();
    if(val === '请选择版本'){
        $('#appUrl').html('<option value="请选择路径">请选择路径</option>');
        return false;
    }
    selectUrl($this.data('_' + val), app);
});

function selectUrl(data, app){
    var con = '';
    var $select = $('#appUrl');
    if(data.path3rd){
        con += '<option value="'+ data.path3rd +'">外链</option>';
    }
    if(data.path){
        con += '<option value="'+ data.path +'">链接</option>';
    }
    if(app && app.url){
        $select.html(con).val(app.url);
    }else{
        $select.html(con);
    }
}

$('#appAction > input').on('click', function(){
    var $this = $(this);
    var val = $this.val();
    $this.prop('checked', true);
    $('.action-remove').show();
    if(val === 'active'){
        $('#appWeight').parent().show();
    }else{
        $('#appWeight').parent().hide();
        if(val === 'remove'){
            $('.action-remove').hide();
        }
    }
});

$('#subGroup').on('click', function(){
    var groupName = $('#groupName').val();
    var copyId = $('#copySelect').val();
    var title = $('#groupModal h4').text();
    var filter = $('#groupTable_filter input').val() || '';
    var data = {};

    if(groupName == ' ' || !groupName){
        alert('请输入组名称');
        return false;
    }
    data = {"name": groupName};

    if(title === '添加'){
        if(copyId !== '请选择组模板'){
            data.groupId = copyId;
        }
    	AjaxPost('/Silent/addAppGroup', data, function(){
            $('#groupModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        data.id = myData.groupId;
        AjaxPost('/Silent/modifyAppGroup', data, function(){
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
            'title': '组名称',
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


$('#subApp').on('click', function(){
    var appAction = $('#appAction input:checked').val();
    var appName = $('#appName').val();
    var appVersion = $('#appVersion').val();
    var appUrl = $('#appUrl').val();
    var appWeight = $('#appWeight').val();

    var title = $('#appModal h4').text();
    var filter = $('#appTable_filter input').val() || '';
    var data = {};

    if(appName == '请选择应用名称' || !appName){
        alert('请选择应用名称');
        return false;
    }
    if(appAction !== 'remove'){
        if(appVersion == '请选择版本' || !appVersion){
            alert('请选择版本');
            return false;
        }
        if(appUrl == '请选择路径' || !appUrl){
            alert('请选择路径');
            return false;
        }
        var appData = $('#appVersion').data('_' + appVersion);
        data = {
            "appName": appName,
            "pkgName": appData.pkgName,
            "versionName": appData.versionName,
            "versionCode": appData.versionCode,
            "action": appAction,
            "download": appUrl
        };
    }else{
        data = {
            "appName": appName,
            "pkgName": $('#appName').data('_' + appName),
            "action": appAction
        };
    }
    if(appAction === 'active'){
        if(appWeight == ' ' || !appWeight){
            alert('请输入权重');
            return false;
        }
        if(/\D/.test(appWeight) || (appWeight < 0 || appWeight > 100)){
            alert('非法权重');
            return false;
        }
        data.weight = appWeight;
    }

    if(title === '添加'){
        data.groupId = myData.groupId;
        AjaxPost('/Silent/addAppGroupItem', data, function(){
            $('#appModal').modal('hide');
            updateTable2();
        });
    }else if(title === '修改'){
        data.id = myData.appId;
        AjaxPost('/Silent/modifyAppGroupItem', data, function(){
            $('#appModal').modal('hide');
            updateTable2();
        });
    }
});

//创建列表
function createApp(data){
    var dataArr = [];
    var len = data.extra.length;
    var action = {
        "active": "激活",
        "remove": "卸载",
        "install": "安装"
    };
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName, arr.pkgName, action[arr.action], arr.weight || '--', arr.versionName || '--', arr.versionCode || '--', arr.download || '--']);
    }
    myDataTable('#appTable', {
        "data": dataArr,
        "order": [
            [6, "desc"]
        ],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'应用名称','width':'15%', 'targets':1},
            {'title':'包名','width':'18%', 'targets':2},
            {'title':'行为','width':'8%', 'targets':3},
            {'title':'权重','width':'8%', 'targets':4},
            {'title':'版本名称','width':'15%', 'targets':5},
            {'title':'版本','width':'15%', 'targets':6},
            {'title':'下载','width':'8%', 'targets':7}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            if(aData[7] !== '--'){
                tableTdDownload(7, nRow, aData[7]);
            }else{
                tableTdNull(7, nRow);
            }
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "appName": aData[1],
                "version": aData[6],
                "url": aData[7],
                "weight": aData[4],
                "action": aData[3]
            });
        }
    });
    initToolBar('#appTable', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}