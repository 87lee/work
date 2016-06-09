//@ sourceURL=appUpdate.app.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
var versionNPage = 1; //版本当前的页面

$(function () {
    myData.checkedLists = [];   //存储check选中项

	AjaxGet('/App/appUpdateLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#appTable');
    });

    trclick('#appTable', function(obj, e) {
        myData.appId = obj.data('id');
        myData.appName = obj.data('appName');
        myData.pkgName = obj.data('pkgName');
        myData.channel = obj.data('channel');
        myData.desc = obj.data('desc');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//版本列表
            AjaxGet('/App/appUpdateLists?id='+ myData.appId +'&page=1&pageSize=' + pageSize, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createVersion(data, 1);
                versionNPage = 1;
                trHover('#versionTable');
                myData.checkedLists = [];
                $('#versionTable input[type="checkbox"]').prop('checked', false);
                $('.checkSelected').removeClass('checkSelected');
                $('.breadcrumb').append('<li class="active">'+myData.appName+'</li>');
            });
            return false;
        }
    });

    listenSingleCheckBox('#versionTable');

    listenMyPage('appTable', currentPage);
    listenVersionPage('versionTable');
    listenfile();
});

listenToolbar('edit', editTableInfo, '#appTable');
listenToolbar('add', addTableInfo, '#appTable');
listenToolbar('del', delTableInfo, '#appTable');
listenToolbar('ware', wareTableInfo, '#appTable');

listenToolbar('add', addTableInfo2, '#versionTable');
listenToolbar('del', delTableInfo2, '#versionTable');
listenToolbar('back', backTable, '#versionTable');

function addTableInfo() {
    $('#appName').val('');
    $('#pkgName').val('');
    $('#channel').val('');
    $('#appDesc').val('');
    $('#appModal h4').text('添加应用');
    $('#appModal').modal('show');
}

function editTableInfo() {
	if (myData.appId) {
        $('#appName').val(myData.appName);
        $('#pkgName').val(myData.pkgName);
        $('#channel').val(myData.channel);
        $('#appDesc').val(myData.desc);
        $('#appModal h4').text('修改');
	    $('#appModal').modal('show');
	} else {
		alert('请选择应用');
	}
}

function delTableInfo() {
    if (myData.appId) {
        if (confirm('确定删除？')) {
        	var filter = $('#appTable_filter input').val() || '';
            AjaxGet('/App/deleteAppUpdate?id=' + myData.appId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择应用');
    }
}

function wareTableInfo(){
	if (myData.appId) {
		addTableInfo2();
		$('#versionModal h4').text('添加版本');
	} else {
        alert('请选择应用');
    }
}

function updateTable(page, name, type){
    name = name || '';
    if(type === 'versionTable'){
        AjaxGet('/App/appUpdateLists?id='+ myData.appId +'&name='+name+'&page='+ page +'&pageSize=' + pageSize, function(data){
            createVersion(data, page);
            myData.versionId = null;
        });
    }else{
        AjaxGet('/App/appUpdateLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
            createElem(data, page);
            myData.appId = null;
        });
    }
}

function addTableInfo2(){
    $('#appShow').val('');
    $('#appHide').val('');
    $('#versionDesc').val('');
    $('#versionModal h4').text('添加');
    $('#versionModal').modal('show');
}

function delTableInfo2(){
    if(myData.checkedLists.length){
        if (confirm('确定删除？')) {
            var filter = $('#versionTable_filter input').val() || '';
            AjaxPost('/App/deleteAppUpdateVersion', myData.checkedLists, function() {
                myData.checkedLists = [];
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

$('#subApp').on('click', function(){
	var appName = $('#appName').val();
    var pkgName = $('#pkgName').val();
    var channel = $('#channel').val();
    var desc = $('#appDesc').val() || '';
	var filter = $('#appTable_filter input').val() || '';
    var title = $('#appModal h4').text();

	if(appName == ' ' || !appName){
		alert('请输入应用名称');
		return false;
	}

    if(pkgName == ' ' || !pkgName){
        alert('请输入包名');
        return false;
    }

    if(channel == ' ' || !channel){
        alert('请输入渠道');
        return false;
    }

    var data = {"appName": appName, "pkgName": pkgName, "channel": channel, "desc": desc};

    if(title === '添加应用'){
        AjaxPost('/App/addAppUpdate', data, function(){
            $('#appModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if( title === '修改'){
        data.id = myData.appId;
        AjaxPost('/App/modifyAppUpdate', data, function(){
            $('#appModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

//创建应用列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName, arr.pkgName, arr.channel, arr.desc, null]);
    }
    $('#appTable').dataTable({
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
            'title': '渠道',
            'width': '16%',
            'targets': 3
        },{
            'title': '描述',
            'width': '16%',
            'targets': 4
        },{
            'title': '版本列表',
            'width': '8%',
            'targets': 5
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            tableTdIcon(5, nRow, 'list');
            $('td:eq(4)', nRow).css('word-break', 'break-all');

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "appName": aData[1],
                "pkgName": aData[2],
                "channel": aData[3],
                "desc": aData[4]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'appTable');
    initToolBar('#appTable', [
    	'<a class="btn my-btn btn-success addBtn" href="javascript:"><i class="glyphicon glyphicon-plus icon-white"></i>&nbsp;新增应用</a>',
    	'<a class="btn my-btn btn-success wareBtn" href="javascript:"><i class="glyphicon glyphicon-plus icon-white"></i>&nbsp;新增版本</a>',
    	myConfig.editBtn,
    	myConfig.delBtn
    ]);
}

$('#subVersion').on('click', function(){
    var desc = $('#versionDesc').val().split(';');

    var filter = $('#versionTable_filter input').val() || '';
    var title = $('#versionModal h4').text();
    var data = new FormData();

    var fileObj1 = document.getElementById("appHide").files[0];
    var fileVal1 = $("#appShow").val();

    if(fileVal1 != ' ' && fileVal1.indexOf('http') == -1){
        data.append("appFile", fileObj1);
    }
    if(fileVal1 == ' ' || !fileVal1){
        alert('请上传应用文件');
        return false;
    }

    var extraData = {
        "appId": myData.appId,
        "desc": filterBlankLine(desc)
    };

    if(title === '添加' || title === '添加版本'){
        data.append("extra", JSON.stringify(extraData));
        AjaxFile('/App/addAppUpdateVersion', data, function(){
            $('#versionModal').modal('hide');
            updateTable(versionNPage, filter, 'versionTable');
        });
    }
});

//创建版本列表
function createVersion(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.versionName, arr.versionCode, arr.desc.join(';'), arr.path]);
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
            [1, "desc"]
        ],
        "columnDefs": [
        {
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '6%',
            'targets': 0,
            "orderable": false
        },{
            'title': '版本名称',
            'width': '12%',
            'targets': 1
        },{
            'title': '版本',
            'width': '12%',
            'targets': 2
        },{
            'title': '描述',
            'width': '20%',
            'targets': 3
        },{
            'title': '下载',
            'width': '10%',
            'targets': 4
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).data({
                "id": aData[0]
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');

            $('td:eq(3)', nRow).css('word-break', 'break-all');
            tableTdDownload(4, nRow, aData[4]);
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
        myConfig.delBtn
    ]);

    listenCheckBox('#versionTable');
    updateChecked('#versionTable');
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