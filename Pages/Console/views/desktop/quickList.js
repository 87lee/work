//@ sourceURL=desktop.quickList.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/desktop/quickLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.quickListId = obj.data('id');
        myData.quickListName = obj.data('name');
    });

    listenSingleCheckBox('#releaseTable');

    listenMyPage();
    listenTableChoose();
    listenFile('#quickListModal');
    listenInputPic('#quickListModal');

    $('#quickListItem').sortable();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);
listenToolbar('release', releaseTableInfo);

function addTableInfo() {
    $('#quickListName').val('');
    $('#quickListItem').html('');
    AjaxGet('/App/apkLists', function(data){
        myData.appNameSelect = data.extra;
        $('#quickListModal h4').text('添加');
        $('#quickListModal').modal('show');
    });
}

function editTableInfo() {
	if (myData.quickListId) {
		$('#quickListName').val(myData.quickListName);
        $('#quickListItem').html('');
        AjaxGet('/App/apkLists', function(data){
            myData.appNameSelect = data.extra;
            AjaxGet('/desktop/quickLists?id=' + myData.quickListId, function(data){
                var con = '';
                var arr = data.extra;
                for(var i = 0, len = arr.length; i < len; i++){
                    con = getQuickListHtml();
                    $('#quickListItem').append($(con));
                    var l = $('.delQuickList').length;
                    selectAppName($('.quick-appName:eq('+ (l-1) +')'), arr[i]);
                }
                $('#quickListItem').sortable( "refresh" );
                $('#quickListModal h4').text('修改');
                $('#quickListModal').modal('show');
            });
        });
	} else {
		alert('请选择内容！');
	}
}

function delTableInfo() {
    if (myData.quickListId) {
        if (confirm('确定删除？')) {
        	var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/desktop/deleteQuickLists?id=' + myData.quickListId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function releaseTableInfo(){
    if (myData.quickListId) {
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
    AjaxGet('/desktop/quickLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.quickListId = null;
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

$('#subQuickList').on('click', function(){
	var name = $('#quickListName').val();
	var filter = $('#myTable_filter input').val() || '';
	var title = $('#quickListModal h4').text();

	if(name == ' ' || !name){
		alert('请输入底部快捷栏名称');
		return false;
	}
	var extra = [];
    var i = 0;
    var quickList = $('#quickListModal .quickList');
    var len = quickList.length;
    var picData = new FormData();
    for(i = 0; i < len; i++){
        var $quickList = $(quickList[i]);
        var appName = $quickList.find('.quick-appName').val();
        var appNameData = $quickList.find('.quick-appName').data('_' + appName);

        if(!appNameData){
            alert('请选择位置'+ (i+1) + '的应用名称');
            return false;
        }

        var pkgName = appNameData.pkgName;
        var appIcon = appNameData.icon;
        var versionCode = $quickList.find('.quick-version').val();
        var apkUrl = $quickList.find('.quick-url').val();
        var appTitle = $quickList.find('.quick-title').val() || '';
        var data = {};

        if(!appName || !versionCode || !apkUrl){
            alert('位置'+ (i+1) + '的信息不完整');
            return false;
        }
        if(appIcon === ''){
            alert('位置'+ (i+1) + '的应用没有图标，请在第三方应用上传');
            return false;
        }


        data.index = i;
        data.title = appTitle;
        data.appName = appName;
        data.pkgName = pkgName;
        data.apkUrl = apkUrl;
        data.versionCode = versionCode;
        data.appIcon = appIcon;

        extra.push(data);
    }


    var subUnder = function(){
		var data = {
			"name": name,
			"extra": extra
		};
		if(title === '添加'){
			AjaxPost('/desktop/addQuickLists', data, function(){
				$('#quickListModal').modal('hide');
		        updateTable(currentPage, filter);
			});
		}else if(title === '修改'){
			data.id = myData.quickListId;
			AjaxPost('/desktop/modifyQuickLists', data, function(){
				$('#quickListModal').modal('hide');
		        updateTable(currentPage, filter);
			});
		}
	};

    subUnder();
});

$('#addQuickList').on('click', function(){
    var con = getQuickListHtml();
    $('#quickListItem').append($(con));
    var len = $('.delQuickList').length;
    selectAppName($('.quick-appName:eq('+ (len-1) +')'));
    $('#quickListItem').sortable( "refresh" );
});

$('#quickListModal').on('click', '.delQuickList', function(){
    $(this).parent().remove();
    var quickList = $('.delQuickList');
    for(var i = 0, len = quickList.length; i < len; i++){
        $(quickList[i]).siblings('label').text('位置' + (i + 1) + ':');
    }
});

$('#quickListItem').on('sortstop', function( event, ui ) {
    var labels = $('#quickListItem label');
    for(var i = labels.length; i--;){
        $(labels[i]).text('位置'+ (i+1) +'：');
    }
});

//生成快捷栏控件
function getQuickListHtml(){
    var len = $('.delQuickList').length;
    return  '<div class="form-group quickList" style="position: relative;">'+
                '<label for="">位置' + (len + 1) + ':</label>'+
                '<input type="text" class="form-control quick-title" placeholder="选填" style="width: 21%;position: relative;top: 1px;">&emsp;'+
                '<select class="chosen-select form-control quick-appName" data-placeholder="请选择应用名称" style="width: 21%;">'+
                    '<option value="请选择应用名称">请选择应用名称</option>'+
                '</select>&emsp;'+
                '<select class="chosen-select form-control quick-version" data-placeholder="请选择应用版本" style="width: 21%;position: relative;top: 2px;">'+
                    '<option value="请选择应用版本">请选择应用版本</option>'+
                '</select>&emsp;'+
                '<select class="chosen-select form-control quick-url" data-placeholder="请选择应用路径" style="width: 21%;position: relative;top: 2px;">'+
                    '<option value="请选择应用路径">请选择应用路径</option>'+
                '</select>&emsp;'+
                '<button type="button" class="close delQuickList" style="position: absolute;top: 9px;right: 0;font-size: 20px;border-radius: 50%;border: 1px solid #0E0D0D;width: 22px;">×</button>'+
            '</div>';
}

function selectAppName($select, bindApp){
    var arr = myData.appNameSelect || [];
    var con = '<option value="请选择应用名称">请选择应用名称</option>';
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].appName + '">' + arr[i].appName + '</option>';
        $select.data('_' + arr[i].appName, {
            "icon": arr[i].icon,
            "pkgName": arr[i].pkgName
        });
    }
    if(bindApp && bindApp.appName){
        $select.html(con).val(bindApp.appName).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "21%"
        }).trigger('change', bindApp);
        $select.siblings('.quick-title').val(bindApp.title);
    }else{
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "21%"
        }).trigger('change');
    }
}

function selectVersion($select, data, bindApp){
    var arr = data.extra;
    var con = '<option value="请选择应用版本">请选择应用版本</option>';
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].versionCode + '">' + arr[i].versionCode + '</option>';
        $select.data('_' + arr[i].versionCode, {
            "path": arr[i].path,
            "path3rd": arr[i].path3rd
        });
    }
    if(bindApp && bindApp.apkUrl){
        $select.html(con).val(bindApp.versionCode).trigger('change', bindApp);
    }else{
        $select.html(con).trigger('change');
    }
}

function selectUrl($select, data, bindApp){
    var con = '<option value="请选择应用路径" title="请选择应用路径">请选择应用路径</option>';
    var url = '';
    if(data.path3rd){
        if(data.path3rd.length > 42){
            url = data.path3rd.substr(0, 42) + '...';
        }else{
            url = data.path3rd;
        }
        con += '<option value="'+ data.path3rd +'" title="'+ data.path3rd +'">'+ url +'</option>';
    }
    if(data.path){
        if(data.path.length > 42){
            url = data.path.substr(0, 42) + '...';
        }else{
            url = data.path;
        }
        con += '<option value="'+ data.path +'" title="'+ data.path +'">'+ url +'</option>';
    }
    if(bindApp && bindApp.apkUrl){
        $select.html(con).val(bindApp.apkUrl).trigger('change', bindApp);
    }else{
        $select.html(con).trigger('change');
    }
}

$('#quickListModal').on('change', '.quick-appName', function(e, bindApp){
    var $this = $(this);
    var val = $this.val();
    if(val && val !== '请选择应用名称'){
        AjaxGet('/App/apkVersionLists?appName=' + val, function(data){
            selectVersion($this.siblings('.quick-version'), data, bindApp);
        });
        return false;
    }
});

$('#quickListModal').on('change', '.quick-version', function(e, bindApp){
    var $this = $(this);
    var val = $this.val();
    var $url = $this.siblings('.quick-url');
    if(val === '请选择应用版本'){
        $url.val('请选择应用路径').trigger('change');
        return false;
    }
    selectUrl($url, $(this).data('_' + val), bindApp);
});

$('#quickListModal').on('change', '.quick-url', function(e, bindApp){
    var $this = $(this);
    this.title = this.options[this.selectedIndex].value;
});

//创建桌面列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name]);
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
            [0, "desc"]
        ],
        "columnDefs": [
        {
            'title': 'ID',
            'width': '16%',
            'targets': 0
        },{
            'title': '名称',
            'width': '16%',
            'targets': 1
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
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
    data.quickListId = myData.quickListId;
    $.ajax({
        url: '/desktop/quickListsPublishDesktop',
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

function updateChecked(){
    var checkBoxs = $('#releaseTable tbody tr input');
    for(var j = 0, l = checkBoxs.length; j < l; j++){
        var $checkBox = $(checkBoxs[j]);
        var $td = $checkBox.parents('td');
        var $tr = $checkBox.parents('tr');
        var id = $td.data('id');
        if($.inArray(id, myData.checkedLists) !== -1){
            $tr.addClass('checkSelected');
            $checkBox.prop('checked', true);
        }
    }
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

//监听文件上传按钮
function listenFile(id) {
    $(id).on('click', '.fileBtn', function() {
        $(this).siblings('.fileHide').trigger('click');
    });
    $(id).on('change', '.fileHide', function() {
        var $this = $(this);
        $this.siblings('.fileShow').val($this.val());
    });
}

//表格数据中的radio监听事件
function listenTableChoose() {
    $('#chooseType').on('click', 'span.lbl', function() {
        $(this).prev('input').trigger('click');
    });
}