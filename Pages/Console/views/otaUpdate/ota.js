//@ sourceURL=otaUpdate.ota.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
var versionNPage = 1; //版本当前的页面

$(function () {
    myData.checkedLists = [];   //存储check选中项

	AjaxGet('/ota/modelLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#pkgTable');
    });

    trclick('#pkgTable', function(obj, e) {
        myData.pkgId = obj.data('id');
        myData.model = obj.data('model');
        myData.vendorId = obj.data('vendorId');
        myData.desc = obj.data('desc');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//版本列表
            AjaxGet('/ota/modelLists?modelID='+ myData.pkgId +'&page=1&pageSize=' + pageSize, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createVersion(data, 1);
                versionNPage = 1;
                trHover('#versionTable');
                myData.checkedLists = [];
                $('#versionTable input[type="checkbox"]').prop('checked', false);
                $('.checkSelected').removeClass('checkSelected');
                $('.breadcrumb').append('<li class="active">'+myData.model+'</li>');
            });
            return false;
        }
    });

    listenSingleCheckBox('#versionTable');

    listenMyPage('pkgTable', currentPage);
    listenVersionPage('versionTable');
});

listenToolbar('edit', editTableInfo, '#pkgTable');
listenToolbar('add', addTableInfo, '#pkgTable');
listenToolbar('del', delTableInfo, '#pkgTable');
listenToolbar('ware', wareTableInfo, '#pkgTable');

listenToolbar('edit', editTableInfo2, '#versionTable');
listenToolbar('add', addTableInfo2, '#versionTable');
listenToolbar('del', delTableInfo2, '#versionTable');
listenToolbar('back', backTable, '#versionTable');

function addTableInfo() {
    $('#modelName').val('');
    $('#vendorId').val('');
    $('#pkgDesc').val('');
    $('#pkgModal h4').text('添加型号');
    $('#pkgModal').modal('show');
}

function editTableInfo() {
	if (myData.pkgId) {
        $('#modelName').val(myData.model);
        $('#vendorId').val(myData.vendorId);
        $('#pkgDesc').val(myData.desc);
        $('#pkgModal h4').text('修改');
	    $('#pkgModal').modal('show');
	} else {
		alert('请选择OTA包！');
	}
}

function delTableInfo() {
    if (myData.pkgId) {
        if (confirm('确定删除？')) {
        	var filter = $('#pkgTable_filter input').val() || '';
            AjaxGet('/ota/deleteModel?id=' + myData.pkgId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择OTA包！');
    }
}

function wareTableInfo(){
	if (myData.pkgId) {
		addTableInfo2();
		$('#versionModal h4').text('添加固件');
	} else {
        alert('请选择OTA包！');
    }
}

function updateTable(page, name, type){
    name = name || '';
    if(type === 'versionTable'){
        AjaxGet('/ota/modelLists?modelID='+ myData.pkgId +'&name='+name+'&page='+ page +'&pageSize=' + pageSize, function(data){
            createVersion(data, page);
            myData.versionId = null;
        });
    }else{
        AjaxGet('/ota/modelLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
            createElem(data, page);
            myData.pkgId = null;
        });
    }
}

function addTableInfo2(){
    $('#fileId').val('');
    $('#versionCode').val('');
    $('#versionDesc').val('');
    $('#versionLength').val('');
    $('#versionWhite').val('');
    $('#versionBlack').val('');
    $('#versionModal h4').text('添加');
    $('#versionModal').modal('show');
}

function editTableInfo2(){
    if (myData.checkedLists.length === 1) {
        var obj = $('.checkSelected td:eq(0)');
        myData.versionId = obj.data('id');
        myData.fileId = obj.data('fileId');
        myData.version = obj.data('version');
        myData.length = obj.data('length');
        myData.desc = obj.data('desc');
        myData.white = obj.data('white');
        myData.black = obj.data('black');
        $('#fileId').val(myData.fileId);
        $('#versionCode').val(myData.version);
        $('#versionLength').val(myData.length);
        $('#versionDesc').val(myData.desc);
        $('#versionWhite').val(myData.white);
        $('#versionBlack').val(myData.black);
        $('#versionModal h4').text('修改');
        $('#versionModal').modal('show');
    }else{
        alert('请选择一个固件！');
        return false;
    }
}

function delTableInfo2(){
    if(myData.checkedLists.length){
        if (confirm('确定删除？')) {
            var filter = $('#versionTable_filter input').val() || '';
            AjaxPost('/ota/deleteModelVersion', myData.checkedLists, function() {
                myData.checkedLists = [];
                updateTable(versionNPage, filter, 'versionTable');
            });
        }
    }else{
        alert('请选择固件！');
        return false;
    }
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.versionId = null;
}

$('#subPkg').on('click', function(){
	var model = $('#modelName').val();
	//var length = $('#modelLength').val();
    var vendorID = $('#vendorId').val() || 'none';
    var desc = $('#pkgDesc').val() || '';
	var filter = $('#pkgTable_filter input').val() || '';
    var title = $('#pkgModal h4').text();

	if(model == ' ' || !model){
		alert('请输入型号');
		return false;
	}
	/*if(length == ' ' || !length){
		alert('请输入长度');
		return false;
	}
    if(/\D/.test(length)){
        alert('长度只能为数字');
        return false;
    } */
	var data = {"model": model, "vendorID": vendorID, "desc": desc};

    if(title === '添加型号'){
        AjaxPost('/ota/addModel', data, function(){
            $('#pkgModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if( title === '修改'){
        data.id = myData.pkgId;
        AjaxPost('/ota/modifyModel', data, function(){
            $('#pkgModal').modal('hide');
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
        dataArr.push([arr.id, arr.model, arr.vendorID, arr.desc, null]);
    }
    $('#pkgTable').dataTable({
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
            'title': '型号',
            'width': '16%',
            'targets': 1
        },{
            'title': 'vendorID',
            'width': '10%',
            'targets': 2
        },{
            'title': '描述',
            'width': '16%',
            'targets': 3
        },{
            'title': '固件列表',
            'width': '10%',
            'targets': 4
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            tableTdIcon(4, nRow, 'list');
            $('td:eq(3)', nRow).css('word-break', 'break-all');

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "model": aData[1],
                "vendorId": aData[2],
                "desc": aData[3]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'pkgTable');
    initToolBar('#pkgTable', [
    	'<a class="btn my-btn btn-success addBtn" href="javascript:"><i class="glyphicon glyphicon-plus icon-white"></i>&nbsp;新增型号</a>',
    	'<a class="btn my-btn btn-success wareBtn" href="javascript:"><i class="glyphicon glyphicon-plus icon-white"></i>&nbsp;新增固件</a>',
    	myConfig.editBtn,
    	myConfig.delBtn
    ]);
}

$('#subVersion').on('click', function(){
    var fileID = $('#fileId').val();
    var version = $('#versionCode').val();
    var length = $('#versionLength').val();
    var desc = $('#versionDesc').val().split(';');
    var whiteList = $('#versionWhite').val().split(';');
    var blackList = $('#versionBlack').val().split(';');
    var filter = $('#versionTable_filter input').val() || '';
    var title = $('#versionModal h4').text();

    if(fileID == ' ' || !fileID){
        alert('请输入fileID');
        return false;
    }
    if(version == ' ' || !version){
        alert('请输入版本');
        return false;
    }
    if(/\D/.test(version)){
        alert('版本只能为数字');
        return false;
    }
    if(length == ' ' || !length){
        alert('请输入长度');
        return false;
    }
    if(/\D/.test(length)){
        alert('长度只能为数字');
        return false;
    }
	var data = {
        "fileID": fileID,
        "version": version,
		"length":length,
        "desc": filterBlankLine(desc),
        "whiteList": filterBlankLine(whiteList),
        "blackList": filterBlankLine(blackList)
    };

    if(title === '添加' || title === '添加固件'){
        data.modelID = myData.pkgId;
        AjaxPost('/ota/addModelVersion', data, function(){
            $('#versionModal').modal('hide');
            updateTable(versionNPage, filter, 'versionTable');
        });
    }else if(title === '修改'){
        data.id = myData.versionId;
        AjaxPost('/ota/modifyModelVersion', data, function(){
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
        dataArr.push([arr.id, arr.fileID, arr.version,arr.length, arr.whiteList.join(';'), arr.blackList.join(';'), arr.desc.join(';')]);
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
            'title': 'fileID',
            'width': '12%',
            'targets': 1
        },{
            'title': '版本',
            'width': '12%',
            'targets': 2
        },{
            'title': '长度',
            'width': '13%',
            'targets': 3
        },
		{
            'title': '白名单',
            'width': '22%',
            'targets': 4
        },{
            'title': '黑名单',
            'width': '25%',
            'targets': 5
        },{
            'title': '描述',
            'width': '20%',
            'targets': 6
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            //$('td:eq(3)', nRow).html(formatDate(aData[3]));
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "fileId": aData[1],
                "version": aData[2],
                "length": aData[3],
                "white": aData[4],
                "black": aData[5],
				"desc": aData[6]

            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');

            $('td:eq(3)', nRow).css('word-break', 'break-all');
            $('td:eq(4)', nRow).css('word-break', 'break-all');
            $('td:eq(5)', nRow).css('word-break', 'break-all');
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