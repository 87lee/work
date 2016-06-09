//@ sourceURL=appManager.appManager3rd.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
var versionNPage = 1; //版本当前的页面

$(function () {
	AjaxGet('/App/apkLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.apkId = obj.data('id');
        myData.pgkName = obj.data('pgkName');
        myData.appName = obj.data('appName');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//版本列表
            AjaxGet('/App/apkVersionLists?id='+ myData.apkId +'&page=1&pageSize=' + pageSize, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createVersion(data, 1);
                trHover('#versionTable');
                $('.breadcrumb').append('<li class="active">'+myData.appName+'</li>');
            });
            return false;
        }

        if (tar.className.indexOf('glyphicon-picture') != -1) {//图片
            window.open($(tar).data('src'));
            return false;
        }
    });

    trclick('#versionTable', function(obj, e) {
        myData.versionId = obj.data('id');
        myData.path3rd = obj.data('path3rd');
    });

    listenMyPage();
    listenVersionPage('versionTable');
    listenchoose();
    listenfile();
    listenPic();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

listenToolbar('edit', editTableInfo2, '#versionTable');
listenToolbar('add', addTableInfo2, '#versionTable');
listenToolbar('del', delTableInfo2, '#versionTable');
listenToolbar('back', backTable, '#versionTable');

function addTableInfo() {
    $('#appName').val('');
    $('#pkgName').val('');
    $('#versionCode').val('');
    $('#path3rd').val('');
    $('#iconShow').val('');
    $('#iconHide').val('');
    $('#apkShow').val('');
    $('#apkHide').val('');
    $('#path3rdShow input:eq(1)').trigger('click');
    $('#apkModal').modal('show');
}

function editTableInfo() {
	if (myData.apkId) {
        $('#editApkName').val(myData.appName);
        $('#editShow').val('');
        $('#editHide').val('');
	    $('#editApkModal').modal('show');
	} else {
		alert('请选择应用！');
	}
}

function delTableInfo() {
    if (myData.apkId) {
        var str = '';
        AjaxGet('/App/apkVersionLists?id='+ myData.apkId, function(data){
            if(data.count == 0){
                str = '确定删除？';
            }else{
                str = '该应用存在现有版本，确定删除？';
            }
            if (confirm(str)) {
                var filter = $('#myTable_filter input').val() || '';
                AjaxGet('/App/deleteApk?id=' + myData.apkId, function() {
                    updateTable(currentPage, filter);
                });
            }
        });
    } else {
        alert('请选择应用！');
    }
}

function updateTable(page, name, type){
    name = name || '';
    if(type === 'versionTable'){
        AjaxGet('/App/apkVersionLists?id='+ myData.apkId +'&name='+name+'&page=1&pageSize=' + pageSize, function(data){
            createVersion(data, page);
            myData.versionId = null;
        });
    }else{
        AjaxGet('/App/apkLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
            createElem(data, page);
            myData.apkId = null;
        });
    }
}

function addTableInfo2(){
    $('#versionCodeV').val('');
    $('#path3rdV').val('');
    $('#apkShowV').val('');
    $('#apkHideV').val('');
    $('#path3rdShowV input:eq(1)').trigger('click');
    $('#versionModal').modal('show');
}

function editTableInfo2(){
    if(myData.versionId){
        $('#editPath3rdV').val(myData.path3rd);
        $('#editVersionModal').modal('show');
    }else{
        alert('请选择版本！');
        return false;
    }
}

function delTableInfo2(){
    if(myData.versionId){
        if (confirm('确定删除？')) {
            var filter = $('#versionTable_filter input').val() || '';
            AjaxGet('/App/deleteApkVersion?id=' + myData.versionId, function() {
                updateTable(versionNPage, filter, 'versionTable');
            });
        }
    }else{
        alert('请选择版本！');
        return false;
    }
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.versionId = null;
}

$('#path3rdShow input').on('click', function(){
    var $this = $(this);
    var val = $this.val();
    $this.prop('checked', true);
    if(val === 'true'){
        $('.apk-type3rd').show();
        $('.apk-type').hide();
    }else if(val === 'false'){
        $('.apk-type3rd').hide();
        $('.apk-type').show();
    }
});

$('#path3rdShowV input').on('click', function(){
    var $this = $(this);
    var val = $this.val();
    $this.prop('checked', true);
    if(val === 'true'){
        $('.version-type3rd').show();
        $('.version-type').hide();
    }else if(val === 'false'){
        $('.version-type3rd').hide();
        $('.version-type').show();
    }
});

$('#subApk').on('click', function(){
    var type = $('#path3rdShow input:checked').val();
	var appName = $('#appName').val();
	var filter = $('#myTable_filter input').val() || '';
    var iconObj = document.getElementById("iconHide").files[0];
    var iconVal = $("#iconShow").val();

    var data = new FormData();

	if(appName == ' ' || !appName){
		alert('请输入应用名称');
		return false;
	}

    if(iconVal != ' ' && iconVal.indexOf('http') == -1){
        data.append("icon", iconObj);
    }
    if(iconVal == ' ' || !iconVal){
        alert('请选择要上传的图标');
        return false;
    }

    var extraData = {"appName": appName};
	if(type === 'false'){
        var fileObj = document.getElementById("apkHide").files[0];
        var fileVal = $("#apkShow").val();

        if(fileVal != ' ' && fileVal.indexOf('http') == -1){
            data.append("apkFile", fileObj);
        }
        if(fileVal == ' ' || !fileVal){
            alert('请选择要上传的应用');
            return false;
        }
    }else if(type === 'true'){
        var pkgName = $('#pkgName').val();

        if(pkgName == ' ' || !pkgName){
            alert('请输入包名');
            return false;
        }
        extraData.pkgName = pkgName;
    }

    data.append("extraData", JSON.stringify(extraData));

	AjaxFile('/App/addNewApk', data, function(){
		$('#apkModal').modal('hide');
        updateTable(currentPage, filter);
	});
});

$('#editApk').on('click', function(){
    var appName = $('#editApkName').val();
    var filter = $('#myTable_filter input').val() || '';
    var iconObj = document.getElementById("editHide").files[0];
    var iconVal = $("#editShow").val();
    var data = new FormData();

    if(appName == ' ' || !appName){
        alert('请输入应用名称');
        return false;
    }

    if(iconVal != ' ' && iconVal.indexOf('http') == -1 && iconVal){
        data.append("icon", iconObj);
    }
    var extraData = {"appName": appName, "id": myData.apkId};
    data.append("extraData", JSON.stringify(extraData));

    AjaxFile('/App/modifyApk', data, function(){
        $('#editApkModal').modal('hide');
        updateTable(currentPage, filter);
    });
});

//创建应用列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName, arr.pkgName, arr.icon, null]);
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
        },{
            'title': '图标',
            'width': '10%',
            'targets': 3
        },{
            'title': '版本列表',
            'width': '10%',
            'targets': 4
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            if(aData[3] === ''){
                tableTdNull(3, nRow);
            }else{
                $('td:eq(3)', nRow).html('<i class="glyphicon glyphicon-picture icon-black my-icon" data-src="' + aData[3] + '"></i>').addClass('center');
            }
            tableTdIcon(4, nRow, 'list');

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "appName": aData[1],
                "pgkName": aData[2]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable');
}

$('#subVersion').on('click', function(){
    var type = $('#path3rdShowV input:checked').val();
    var filter = $('#versionTable_filter input').val() || '';

    var data = new FormData();

    var extraData = {"appId": myData.apkId};
    if(type === 'false'){
        var fileObj = document.getElementById("apkHideV").files[0];
        var fileVal = $("#apkShowV").val();

        if(fileVal != ' ' && fileVal.indexOf('http') == -1){
            data.append("apkFile", fileObj);
        }
        if(fileVal == ' ' || !fileVal){
            alert('请选择要上传的应用');
            return false;
        }
    }else if(type === 'true'){
        var versionName = $('#versionNameV').val();
        var versionCode = $('#versionCodeV').val();
        var path3rd = $('#path3rdV').val();

        if(versionName == ' ' || !versionName){
            alert('请输入版本名称');
            return false;
        }
        if(versionCode == ' ' || !versionCode){
            alert('请输入版本');
            return false;
        }
        if(/\D/.test(versionCode)){
            alert('版本只能为数字');
            return false;
        }
        if(path3rd == ' ' || !path3rd){
            alert('请输入外链');
            return false;
        }
        extraData.versionCode = versionCode;
        extraData.path3rd = path3rd;
        extraData.versionName = versionName;
    }

    data.append("extraData", JSON.stringify(extraData));

    AjaxFile('/App/addApk', data, function(){
        $('#versionModal').modal('hide');
        updateTable(versionNPage, filter, 'versionTable');
    });
});

$('#editVersion').on('click', function(){
    var path3rd = $('#editPath3rdV').val();
    var filter = $('#versionTable_filter input').val() || '';

    if(path3rd == ' ' || !path3rd){
        alert('请输入外链');
        return false;
    }

    var data = {"path3rd": path3rd, "id": myData.versionId};

    AjaxPost('/App/modifyApkVersion', data, function(){
        $('#editVersionModal').modal('hide');
        updateTable(versionNPage, filter, 'versionTable');
    });
});

//创建版本列表
function createVersion(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.versionName, arr.versionCode, arr.path, arr.path3rd]);
    }
    $('#versionTable').dataTable({
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
            [2, "desc"]
        ],
        "columnDefs": [
        {
            'title': 'ID',
            'width': '8%',
            'targets': 0
        },{
            'title': '版本名称',
            'width': '16%',
            'targets': 1
        },{
            'title': '版本',
            'width': '16%',
            'targets': 2
        },{
            'title': '链接',
            'width': '10%',
            'targets': 3
        },{
            'title': '外链',
            'width': '10%',
            'targets': 4
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            if (aData[3]) {
                tableTdDownload(3, nRow, aData[3]);
            }else{
                tableTdNull(3, nRow);
            }
            if (aData[4]) {
                tableTdDownload(4, nRow, aData[4]);
            }else{
                tableTdNull(4, nRow);
            }

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "path3rd": aData[4]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'versionTable');
    initToolBar('#versionTable', [
        myConfig.backBtn,
        myConfig.addBtn,
        myConfig.editBtn,
        myConfig.delBtn
    ]);
}

//监听自定义分页
function listenVersionPage(table) {
    $('.my-content').on('click', '#'+ table +'_paginate ul li a', function() {
        var val = $(this).text();
        var active = $(this).parent().hasClass('active');
        var page = Number(val);
        var filter = $('#'+ table +'_filter input').val() || '';
        if(active){
            return false;
        }

        if (val === '上一页' && !$(this).parent().hasClass('disabled')) {
            versionNPage--;
            updateTable(versionNPage, filter, table);
        } else if (val === '下一页' && !$(this).parent().hasClass('disabled')) {
            versionNPage++;
            updateTable(versionNPage, filter, table);
        } else if (!isNaN(page)) {
            versionNPage = page;
            updateTable(versionNPage, filter, table);
        }
        return false;
    });
    $('.my-content').on('keyup', '#'+ table +'_filter input', function() {
        var val = $(this).val();
        versionNPage = 1;
        updateTable(versionNPage, val, table);
        return false;
    });
}