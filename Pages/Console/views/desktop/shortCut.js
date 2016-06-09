//@ sourceURL=desktop.shortCut.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
var shortCutNPage = 1; //版本当前的页面

$(function () {
	AjaxGet('/desktop/shortCutsLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.groupId = obj.data('id');
        myData.groupName = obj.data('name');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//快捷键列表
            AjaxGet('/desktop/shortCutsLists?id='+ myData.groupId, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createShortCutTable(data);
                trHover('#shortCutTable');
                $('.breadcrumb').append('<li class="active">'+myData.groupName+'</li>');
            });
            return false;
        }
    });

    trclick('#shortCutTable', function(obj, e) {
        myData.shortCutId = obj.data('id');
        myData.keyVal = obj.data('key-val');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-list') != -1){
            createKeyVal(myData.keyVal);
            $('#keyValModal').modal('show');
        }
    });

    listenSingleCheckBox('#releaseTable');

    listenMyPage();
    listenchoose();
    listenfile();
    listenPic();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);
listenToolbar('release', releaseTableInfo);

listenToolbar('edit', editTableInfo2, '#shortCutTable');
listenToolbar('add', addTableInfo2, '#shortCutTable');
listenToolbar('del', delTableInfo2, '#shortCutTable');
listenToolbar('back', backTable, '#shortCutTable');

function addTableInfo() {
    $('#groupName').val('');
    $('#groupModal').modal('show');
    $('#groupModal h4').text('添加');
}

function editTableInfo() {
	if (myData.groupId) {
        $('#groupName').val(myData.groupName);
	    $('#groupModal').modal('show');
	    $('#groupModal h4').text('修改');
	} else {
		alert('请选择组！');
	}
}

function delTableInfo() {
    if (myData.groupId) {
        if (confirm('确定删除？')) {
        	var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/desktop/deleteShortCuts?id=' + myData.groupId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择组！');
    }
}

function releaseTableInfo(){
    if (myData.groupId) {
        AjaxWhen([
            AjaxGet('/group/nameLists', selectGroup, true),
            AjaxGet('/desktop/desktopGroupLists', selectDGroup, true),
            AjaxGet('/desktop/desktopLists', createDesktop, true)
        ], function(){
            $('#chooseType > input:eq(0)').trigger('click');
            $('#desktopModal').modal('show');
        });
    } else {
        alert('请选择内容！');
    }
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/desktop/shortCutsLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.groupId = null;
    });
}

function addTableInfo2(){
    clearQuickData();
    $('#quickKeyModal h4').text('添加');
    AjaxGet('/desktop/actionAppLists', function(data){
        selectApp(data);
        $('#quickKeyModal').modal('show');
    });
}

function editTableInfo2(){
    if (myData.shortCutId) {
        clearQuickData();
        $('#quickKeyModal h4').text('修改');
    	AjaxGet('/desktop/actionAppLists', function(data){
        	selectApp(data);
            setQuickData();
            $('#quickKeyModal').modal('show');
    	});
    } else {
        alert('请选择快捷键！');
    }
}

function delTableInfo2(){
    if(myData.shortCutId){
        if (confirm('确定删除？')) {
            var filter = $('#shortCutTable_filter input').val() || '';
            AjaxGet('/desktop/deleteShortCutsItems?id=' + myData.shortCutId, function() {
                updateTable2();
            });
        }
    }else{
        alert('请选择版本！');
        return false;
    }
}

function updateTable2(){
    AjaxGet('/desktop/shortCutsLists?id=' + myData.groupId, function(data){
        createShortCutTable(data);
        myData.shortCutId = null;
    });
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.shortCutId = null;
}

//清空快捷键对话框
function clearQuickData(){
    $('#keyCodeQuick').val('请选择键值');
    $('#uriValQuick').val('');
    $('#sidValQuick').val('');
    $('#jumpTypeQuick input:eq(0)').trigger('click');
}

//设置快捷键对话框数据
function setQuickData(){
    AjaxGet('/desktop/shortCutsItemsLists?id='+ myData.shortCutId, function(data){
    	$('#keyCodeQuick').val(data.extra.keyCode);
	    if(data.extra.type === 'URI'){
	        $('#jumpTypeQuick input:eq(1)').trigger('click');
	        $('#uriValQuick').val(data.extra.uri);
	    }else if(data.extra.type === 'SCREEN'){
	        $('#jumpTypeQuick input:eq(2)').trigger('click');
	        $('#sidValQuick').val(data.extra.sid);
	    }else{
	        $('#jumpTypeQuick input:eq(0)').trigger('click');
	        $('#jumpType > input:eq(0)').trigger('click');
	        var $jumpApp = $('#jumpAppQuick');
	        var optionA = $jumpApp.find('option').filter('[data-name="' + data.extra.appName + '"]');
	        optionA.prop("selected", true);
	        $jumpApp.trigger("chosen:updated.chosen").chosen({
	            allow_single_deselect: true,
	            width: "70%"
	        });

	        if (data.extra.type !== 'APP') {
	            $jumpApp.trigger('change', data.extra.detailName);
	        } else {
	            $jumpApp.trigger('change');
	        }
	    }
    });
}

//内测与公开变化事件
$('#chooseType > input').on('click', function(){
    var $this = $(this);
    $this.prop('checked', true);
    var val = $this.val();
    if(val === 'group'){
        $('#group').parent().show();
    }else if(val === 'ALL'){
        $('#group').parent().hide();
    }
});

//创建内测组下拉框
function selectGroup(data){
    var arr = data.groups;
    var con = '';
    var $select = $('#group');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
    }
    $select.html(con);
}

//创建桌面组下拉框
function selectDGroup(data){
    var arr = data.extra;
    var con = '<option value="不分组">不分组</option>';
    var $select = $('#dGroup');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].name+'</option>';
    }
    $select.html(con);
}

$('#dGroup').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    var url = '/desktop/desktopLists';
    if(val !== '不分组'){
        url += '?groupId=' + val;
    }
    AjaxGet(url, createDesktop);
});

//发布桌面
$('#subDesktop').on('click', function(){
    var type = $('#chooseType input:checked').val();
    var checkBoxs = $('.checkSelected input');
    var desktopIDList = [];
    var len = checkBoxs.length;
    if(len <= 0){
        alert('请选择桌面');
        return false;
    }
    for(var i = 0; i < len; i++){
        var $td = $(checkBoxs[i]).parents('td');
        desktopIDList.push($td.data('id'));
    }
    var url = '';
    var data = {};
    if(type === 'group'){
        var groupId = $('#group').val();
        data.groupId = groupId;
    }
    data.type = type;
    data.desktopIDList = desktopIDList;
    data.shortCutsId = myData.groupId;
    $.ajax({
        url: '/desktop/shortCutsPublishDesktop',
        beforeSend: function() {
            showLoading();
        },
        type: 'post',
        data: JSON.stringify(data),
        dataType: 'json',
        success: function(data) {
            hideLoading();
            if (data.result == "fail") {
                if (data.reason == "登录超时，请重新登录" || data.reason == "未登录，请重新登录") {
                    window.location.href = myConfig.logOutUrl;
                }
                var con = '';
                var obj = null;
                for (var i = 0, len = data.failList.length; i < len; i++) {
                    obj = data.failList[i];
                    con += obj.desktopName + obj.reason + '\n';
                }
                alert(con);
                return false;
            } else {
                alert('发布成功');
                $('#desktopModal').modal('hide');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            hideLoading();
            ajaxError(XMLHttpRequest, textStatus, errorThrown);
        }
    });
});

//提交快捷键组
$('#subGroup').on('click', function(){
	var name = $('#groupName').val();
	var filter = $('#myTable_filter input').val() || '';
	var title = $('#groupModal h4').text();

	if(name == ' ' || !name){
		alert('请输入组名称');
		return false;
	}

    var data = {"name": name};
    if(title === '添加'){
    	AjaxPost('/desktop/addShortCuts', data, function(){
			$('#groupModal').modal('hide');
	        updateTable(currentPage, filter);
		});
    }else if(title === '修改'){
    	data.id = myData.groupId;
    	AjaxPost('/desktop/modifyShortCuts', data, function(){
			$('#groupModal').modal('hide');
	        updateTable(currentPage, filter);
		});
    }
});

//创建组列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, null]);
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
            'title': '名称',
            'width': '16%',
            'targets': 1
        },{
            'title': '快捷键列表',
            'width': '10%',
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
    updatePagination(len, page, data.count);
    initToolBar('#myTable', [myConfig.addBtn, myConfig.editBtn, myConfig.delBtn, myConfig.releaseBtn]);
}

//创建跳转应用
function selectApp(data) {
    var arr = data.extra;
    var con = '<option value="请选择跳转应用">请选择跳转应用</option>';
    var $select = $('#jumpAppQuick');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].appName + '" >' + arr[i].appName + '</option>';
        $select.data('_' + arr[i].id, arr[i]);
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    }).trigger('change');
}

//生成详情页下拉框
function selectDetail($select, id, name) {
    AjaxGet('/desktop/actionAppLists?id=' + id, function(data) {
        var arr = data.extra.extraData;
        var con = '<option value="请选择跳转详情页">请选择跳转详情页</option>';
        var len = arr.length;
        for (var i = 0; i < len; i++) {
            con += '<option value="' + arr[i].id + '" data-name="' + arr[i].detailName + '" >' + arr[i].detailName + '</option>';
            $select.data('_' + arr[i].id, arr[i]);
        }
        $select.html(con).parent().show();
        if (name) {
            var option = $select.find('option').filter('[data-name="' + name + '"]');
            option.prop("selected", true);
        }
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
    });
}

//跳转应用变化时显示跳转详情页
$('#jumpApp').on('change', function(e, name) {
    var id = $(this).val();
    var $select = $('#jumpDetailQuick');
    if (id === '请选择跳转应用') {
        $select.parent().hide();
        $select.html('<option value="请选择跳转详情页">请选择跳转详情页</option>');
        return false;
    }

    selectDetail($select, id, name);
});

//跳转类型变化事件
$('#jumpTypeQuick input').on('click', function(){
    var $this = $(this);
    var val = $this.val();
    $this.prop('checked', true);
    $('.quick-key').hide();
    if(val === 'APP'){
        var $jumpApp = $('#jumpAppQuick');
        $jumpApp.parent().show();
        if($jumpApp.val() !== '请选择跳转应用'){
            $('#jumpDetailQuick').parent().show();
        }
    }else if(val === 'URI'){
        $('#uriValQuick').parent().show();
    }else if(val === 'SCREEN'){
        $('#sidValQuick').parent().show();
    }
});

//跳转应用变化时显示跳转详情页
$('#jumpAppQuick').on('change', function(e, name) {
    var id = $(this).val();
    var $select = $('#jumpDetailQuick');
    if (id === '请选择跳转应用') {
        $select.parent().hide();
        $select.html('<option value="请选择跳转详情页">请选择跳转详情页</option>');
        return false;
    }

    selectDetail($select, id, name);
});

//提交快捷键成员事件
$('#subQuickKey').on('click', function(){
    var keyCode = $('#keyCodeQuick').val();
    var type = $('#jumpTypeQuick input:checked').val();
    var title = $('#quickKeyModal h4').text();
    var data = {};



    if(type === 'URI'){
        var uri = $('#uriValQuick').val();
        if (uri == ' ' || !uri) {
            alert('请输入uri');
            return false;
        }
        data.uri = uri;
    }else if(type === 'SCREEN'){
        var sid = $('#sidValQuick').val();
        if (sid == ' ' || !sid) {
            alert('请输入sid');
            return false;
        }
        data.sid = sid;
    }else if(type === 'APP'){
        var $jumpApp = $('#jumpAppQuick');
        if ($jumpApp.val() == '请选择跳转应用') {
            alert('请选择跳转应用');
            return false;
        }
        var appData = $jumpApp.data('_' + $jumpApp.val());
        var $jumpDetail = $('#jumpDetailQuick');
        if ($jumpDetail.val() == '请选择跳转详情页') {
            type = 'APP';
            data.pkgName = appData.pkgName;
            data.appName = appData.appName;
        } else {
            var detailDate = $jumpDetail.data('_' + $jumpDetail.val());
           //修改
            data = detailDate;
            type = detailDate.actionType;
            data.appName = appData.appName;
            /*data.extraData = detailDate.extraData;
            type = detailDate.actionType;
            data.detailName = detailDate.detailName;
            if (detailDate.actionType === 'ACTION') {
                data.action = detailDate.action;
            } else if (detailDate.actionType === 'COMPONENT') {
                data.component = detailDate.component;
                data.clsName = detailDate.clsName;
            }*/

        }
    }

     if (keyCode == '请选择键值' || !keyCode) {
        alert('请选择键值');
        return false;
    }
    data.keyCode = keyCode;
    data.type = type;
    var i = 0, len = 0;

    if(title === '添加'){
    	data.shortId = myData.groupId;
        AjaxPost('/desktop/addShortCutsItems', data, function(){
        	$('#quickKeyModal').modal('hide');
	        updateTable2();
        });
    }else if(title === '修改'){
        data.id = myData.shortCutId;
        AjaxPost('/desktop/modifyShortCutsItems', data, function(){
        	$('#quickKeyModal').modal('hide');
	        updateTable2();
        });
    }
});


//创建快捷键成员表格
function createShortCutTable(data) {
    var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        var action = arr.action || '--';
        var component = arr.component || '--';
        var clsName = arr.clsName || '--';
        var detailName = arr.detailName || '--';
        var appName = arr.appName || '--';
        var uri = arr.uri || '--';
        var sid = arr.sid || '--';
        var extraData = arr.extraData || '--';
        var keyCode = {"131": "F1","132": "F2","133": "F3","134": "F4","135": "F5","136": "F6","137": "F7","138": "F8","176": "Setting"}[arr.keyCode] || arr.keyCode;
        dataArr.push([keyCode + '-' + arr.keyCode, arr.type + '-' + arr.id, appName, detailName, uri, sid, extraData]);
    }
    myDataTable('#shortCutTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'键值','width':'8%', 'targets':0},
            {'title':'类型','width':'10%', 'targets':1},
            {'title':'应用名称','width':'12%', 'targets':2},
            {'title':'详情页名称','width':'12%', 'targets':3},
            {'title':'uri','width':'18%', 'targets':4},
            {'title':'sid','width':'8%', 'targets':5},
            {'title':'附加数据','width':'8%', 'targets':6},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
        	var keyCode = aData[0].split('-');
        	$('td:eq(0)', nRow).html(keyCode[0]);
        	var type = aData[1].split('-');
        	$('td:eq(1)', nRow).html(type[0]);
            if(aData[6] !== '--'){
                tableTdIcon(6, nRow, 'list');
            }
            $('td:eq(0)', nRow).data({
                "id": type[1],
                "key-val": aData[6]
            });
        }
    });
    initToolBar('#shortCutTable', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}

//创建附件数据列表
function createKeyVal(data){
    $('#jumpExtra').nextAll().remove();
    var len = data.length;
    var con = '';
    var type = {
        "int": "整型",
        "long": "长整型",
        "float": "浮点型",
        "double": "双精度浮点型",
        "boolean": "布尔型",
        "char": "字符型",
        "string": "字符串型"
    };
    for (var i = 0; i < len; i++) {
        var arr = data[i];
        var myType = '&nbsp;';
        if(arr.type){
            myType = type[arr.type];
        }
        con +=  '<div class="form-group" style="margin-bottom: 2px;">' +
                    '<label></label>' +
                    '<span style="display: inline-block;width: 20%;padding: 7px 10px;border: 1px solid #ccc;word-wrap: break-word;">' + arr.key + '</span>' +
                    '&nbsp;=&nbsp;' +
                    '<span style="display: inline-block;width: 20%;padding: 7px 10px;border: 1px solid #ccc;word-wrap: break-word;">' + arr.value + '</span>' +
                    '&nbsp;&nbsp;' +
                    '<span style="display: inline-block;width: 20%;padding: 7px 10px;border: 1px solid #ccc;word-wrap: break-word;">' + myType + '</span>' +
                '</div>';
    }
    $('#jumpExtra').after(con);
}

function createDesktop(data){
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name]);
    }

    myDataTable('#releaseTable', {
        "data": dataArr,
        "paging": false,
        "stateSave": false,
        "order": [
            [1, "desc"]
        ],
        "columnDefs": [
        {
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '30%',
            'targets': 0,
            "orderable": false
        },
        {
            'title': '桌面名称',
            'width': '70%',
            'targets': 1
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>').data({
                "id": aData[0]
            });
        }
    });

    listenCheckbox();
    $('#releaseTable_filter label').css('right', '125px');
}

//监听发布桌面列表checkbox
function listenCheckbox() {
    $('#releaseTable').off('click', 'tbody tr input').on('click', 'tbody tr input', function(ev) {
        var e = ev || event;
        var $this = $(this);
        var $tr = $this.parents('tr');
        var id = $this.parents('td').data('id');

        if ($this.prop('checked')) {
            $tr.addClass('checkSelected');
        } else {
            $tr.removeClass('checkSelected');
        }
    });

    $('#releaseTable').off('click', 'thead tr input').on('click', 'thead tr input', function(ev) {
        var e = ev || event;
        var $this = $(this);

        if ($this.prop('checked')) {
            $('#releaseTable tbody tr').addClass('checkSelected');
            $('#releaseTable tbody tr input').prop('checked', true);
        } else {
            $('#releaseTable tbody tr').removeClass('checkSelected');
            $('#releaseTable tbody tr input').prop('checked', false);
        }
    });
}