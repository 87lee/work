//@ sourceURL=interfaceTable.interfaceTable.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
    AjaxGet('/Monitoring/Home/Interface/groupLists?page=1&pageSize='+pageSize, function(data) {
        createElem(data, 1);
        trHover('#groupTable');
    });

    trclick('#groupTable', function(obj, e) {
        myData.groupId = obj.data('id');
        myData.groupName = obj.data('name');
        myData.groupDesc = obj.data('desc');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//版本列表
            AjaxGet('/Monitoring/Home/Interface/groupItemLists?groupId='+ myData.groupId, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createInterface(data);
                trHover('#interfaceTable');
                $('.breadcrumb').append('<li class="active">'+myData.groupName+'</li>');
            });
            return false;
        }
    });

    trclick('#interfaceTable', function(obj, e) {
        myData.interfaceId = obj.data('id');
        myData.interfaceName = obj.data('name');
        myData.interfaceDesc = obj.data('desc');
        myData.groupId = obj.data('groupId');
        myData.interface = obj.data('interface');
    });

    listenMyPage('groupTable', currentPage);
    listenchoose();
});


 listenToolbar('add', addTableGroup, '#groupTable');
 listenToolbar('del', delTableGroup, '#groupTable');
 listenToolbar('edit', editTableGroup, '#groupTable');
 listenToolbar('back', backTable, '#interfaceTable');
 listenToolbar('add', addTableInterface, '#interfaceTable');
 listenToolbar('del', delTableInterface, '#interfaceTable');
 listenToolbar('edit', editTableInterface, '#interfaceTable');

function addTableGroup() {
        $('#groupDesc').val('');
        $('#groupName').val('');
        $('#groupModal h4').text('添加');
        $('#groupModal').modal('show');
}

function editTableGroup(){
    if (myData.groupId) {
        $('#groupName').val(myData.groupName);
        $('#groupDesc').val(myData.groupDesc);
        $('#groupModal h4').text('修改');
        $('#groupModal').modal('show');
    } else {
        alert('请选择组！');
    }
}

function delTableGroup(){
    if (myData.groupId) {
        if (confirm('确定删除？')) {
            var filter = $('#groupTable_filter input').val() || '';
            AjaxGet('/Monitoring/Home/Interface/deleteGroup?id=' + myData.groupId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择组！');
    }
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/Monitoring/Home/Interface/groupLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.groupId = null;
    });
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.interfaceId = null;
}

function addTableInterface(){
    $('#interfaceName').val('');
    $('#infPath').val('');
    $('#interfaceDesc').val('');
    $('#interfaceModal h4').text('添加');
    $('#interfaceModal').modal('show');
}

function editTableInterface(){
    if (myData.interfaceId) {
        $('#interfaceName').val(myData.interfaceName);
        $('#infPath').val(myData.interface);
        $('#interfaceDesc').val(myData.interfaceDesc);
        // AjaxGet('/App/app3rdAndAppUpdateLists', function(data){
        //     selectApp(data, {
        //         "appName": myData.appName,
        //         "version": myData.version,
        //         "url": myData.url
        //     });
        //     if(myData.action === '激活'){
        //         $('#appAction input:eq(2)').trigger('click');
        //     }else if(myData.action === '安装'){
        //         $('#appAction input:eq(0)').trigger('click');
        //     }else if(myData.action === '卸载'){
        //         $('#appAction input:eq(1)').trigger('click');
        //     }
        //     $('#appWeight').val(myData.weight === '--' ? '' : myData.weight);
        //     $('#interfaceModal h4').text('修改');
        //     $('#interfaceModal').modal('show');
        // });
        $('#interfaceModal h4').text('修改');
        $('#interfaceModal').modal('show');
    } else {
        alert('请选择接口！');
    }
}

function delTableInterface(){
    if (myData.interfaceId) {
        if (confirm('确定删除？')) {
            var arr = [];
            arr.push(myData.interfaceId);
            AjaxPost('/Monitoring/Home/Interface/deleteGroupItem', arr, function() {
                updateTable2();
            });
        }
    } else {
        alert('请选择接口！');
    }
}

function updateTable2(){
    AjaxGet('/Monitoring/Home/Interface/groupItemLists?groupId='+ myData.groupId, function(data){
        createInterface(data);
        myData.interfaceId = null;
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
    var groupDesc = $('#groupDesc').val();
    var title = $('#groupModal h4').text();
    var filter = $('#groupTable_filter input').val() || '';
    var data = {};

    if(groupName == ' ' || !groupName){
        alert('请输入组名称');
        return false;
    }
    data = {"name": groupName};

    if(title === '添加'){
        data.desc = groupDesc;
        AjaxPost('/Monitoring/home/Interface/addGroup', data, function(){
            $('#groupModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        data.id = myData.groupId;
        data.desc = groupDesc;
        AjaxPost('/Monitoring/home/Interface/modifyGroup', data, function(){
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
        dataArr.push([arr.id, arr.name, arr.desc, null]);
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
            'width': '25%',
            'targets': 1
        },{
            'title': '描述',
            'width': '25%',
            'targets': 2
        },{
            'title': '接口列表',
            'width': '25%',
            'targets': 3
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            tableTdIcon(3, nRow, 'list');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
                "desc": aData[2]
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


$('#subInterface').on('click', function(){
    var interfaceName = $('#interfaceName').val();
    var interface = $('#infPath').val();
    var interfaceDesc = $('#interfaceDesc').val();

    var title = $('#interfaceModal h4').text();
    var filter = $('#interfaceTable_filter input').val() || '';
    var data = {};

    if(interfaceName == ' ' || !interfaceName){
        alert('请输入接口名称');
        return false;
    }
    if(interface == ' ' || !interface){
        alert('请输入接口');
        return false;
    }
    data = {
        "name": interfaceName,
        "interface": interface,
        "desc": interfaceDesc
    };
    

    if(title === '添加'){
        data.groupId = myData.groupId;
        AjaxPost('/Monitoring/home/Interface/addGroupItem', data, function(){
            $('#interfaceModal').modal('hide');
            updateTable2();
        });
    }else if(title === '修改'){
        data.id = myData.interfaceId;
        AjaxPost('/Monitoring/home/Interface/modifyGroupItem', data, function(){
            $('#interfaceModal').modal('hide');
            updateTable2();
        });
    }
});

//创建列表
function createInterface(data){
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.interface, arr.desc || '--', arr.groupId]);
    }
    myDataTable('#interfaceTable', {
        "data": dataArr,
        "order": [
            [0, "desc"]
        ],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'名称','width':'15%', 'targets':1},
            {'title':'接口','width':'18%', 'targets':2},
            {'title':'备注','width':'18%', 'targets':3}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
                "interface": aData[2],
                "desc": aData[3],
                "groupId": aData[4]
            });
        }
    });
    initToolBar('#interfaceTable', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}