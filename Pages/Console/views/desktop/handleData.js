//@ sourceURL=desktop.handleData.js
var myData = {};
var pageSize = 15;  //自定义分页，每页显示的数据量
var currentPage = 1;    //当前的页面
$(function () {
	 myData.checkedLists = []; 
    AjaxGet('/desktop/actionAppLists?page=1&pageSize='+pageSize, function(data){
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.targetId = obj.data('id');
        myData.targetName = obj.data('name');
        myData.targetPkgName = obj.data('pkgName');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-list') != -1){
            AjaxGet('/desktop/actionAppLists?id=' + myData.targetId, function(data){
                $('.my-content').hide();
                $('.my-content-detail').show();
                createDetail(data);
                trHover('#detailTable');
                $('.breadcrumb').append('<li class="active">'+myData.targetName+'</li>');
            });
        }
    });

    trclick('#detailTable', function(obj, e){
        myData.KeyVal = obj.data('key-val');
        myData.detailId = obj.data('id');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-list') != -1){
            createKeyVal(myData.KeyVal);
            $('#keyValModal').modal('show');
        }
    });

    listenMyPage();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);
listenToolbar('copy', copyTable);


listenToolbar('edit', editTableInfo2, '#detailTable' );
listenToolbar('add', addTableInfo2, '#detailTable');
listenToolbar('del', delTableInfo2, '#detailTable');
listenToolbar('back', backTable, '#detailTable');


//选择要复制的名称
function copyTable(){
	if (myData.targetId) {
		AjaxWhen([
            AjaxGet('/desktop/actionAppLists', selectCopyActionApp, true),
        ], function(){
            $('#copyActionAppModal').modal('show');
        });
	}else{
		alert('请选择一条信息');
	}
}
//生成名称列表
function selectCopyActionApp(data){
	
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName]);
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
            'title': '名称',
            'width': '70%',
            'targets': 1
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>').data({
                "id": aData[0]
            });
        }
    });
    $('#releaseTable_filter label').css('right', '125px');
}
//提交复制
$('#subCopyActionApp').on('click',function(){

	var id = myData.targetId;
	var copyActionIDList = [];
	var checkeds = $('#releaseTable tbody tr td input:checked');
	var len = checkeds.length;
	if (len === 0) {
		alert('请选择要复制到的名称');
		return false;
	}
	for (var i = 0; i < len; i++) {
		var $td = $(checkeds[i]).parents('td');
		copyActionIDList.push($td.data('id') + '');
	}
	AjaxPost('/desktop/copyActionApp', {"fromId":id,"toIds":copyActionIDList }, function() {
                alert('复制成功');
				$('#copyActionAppModal').modal('hide');
            });


})
function setChosenVal(keyVal){  //修改时根据keyValue，初始化chosen的值
    var con = [];
    keyVal.forEach(function(elem){
        var $con = $(getExtraHtml(elem.key, elem.value));
        if(elem.type){//设置类型
            $con.find('.myType').val(elem.type);
        }
        con.push($con);
    });
    $('#detailModal .my-form').append(con);
}

function editTableInfo(){
    if(myData.targetId){
        AjaxGet('/App/apkLists', function(data){
            selectApk(data, myData.targetPkgName);
            $('#appModal').find('h4').html('修改');
            $('#appModal').modal('show');
        });
    }else{
        alert('请选择数据！');
    }
}

function editTableInfo2(){
    if(myData.detailId){
        clearTableInfo2();
        AjaxGet('/desktop/actionAppDetailLists?id=' + myData.detailId, function(data){
            $('#detailName').val(data.extra.detailName);
            $('#handleType').val(data.extra.actionType).trigger('change');
            if(data.extra.actionType === 'ACTION'){
                $('#actionVal').val(data.extra.action);
            }else if(data.extra.actionType === 'COMPONENT'){
                $('#clsNameVal').val(data.extra.clsName);
                $('#componentVal').val(data.extra.component);
            }else if(data.extra.actionType === 'SCHEME'){
                $('#clsUriVal').val(data.extra.uri);
                $('#actionVal').val(data.extra.action);
            }
            setChosenVal(data.extra.extraData);
            $('#detailModal').find('h4').html('修改');
            $('#detailModal').modal('show');
        });
    }else{
        alert('请选择详情页！');
    }
}

function clearTableInfo2(){
    $('#detailName').val('');
    $('#clsNameVal').val('');
    $('#clsUriVal').val('');
    $('#componentVal').val('');
    $('#actionVal').val('');
    $('#addkeyVal').parent().nextAll('.form-group').remove();
}

function addTableInfo(){
    AjaxGet('/App/apkLists', function(data){
        selectApk(data);
        $('#appModal').find('h4').html('新增');
        $('#appModal').modal('show');
    });
}

function addTableInfo2(){
    clearTableInfo2();
    $('#handleType').val('请选择ACTION类型').trigger('change');
    $('#detailModal').find('h4').html('新增');
    $('#detailModal').modal('show');
}

function delTableInfo(){
    if (myData.targetId) {
        var str = '';
        AjaxGet('/desktop/actionAppLists?id='+ myData.targetId, function(data){
            if(data.extra.extraData.length){
                str = '子信息存在，确定删除？';
            }else{
                str = '确定删除？';
            }
            if (confirm(str)) {
                var filter = $('#myTable_filter input').val() || '';
                AjaxGet('/desktop/deleteActionApp?id=' + myData.targetId, function(){
                    updateTable(currentPage, filter);
                });
            }
        });
    } else {
        alert('请选择数据！');
    }
}

function delTableInfo2(){
    if(myData.detailId){
        if( confirm('确定删除？') ){
            AjaxGet('/desktop/deleteActionAppDetail?id=' + myData.detailId, function(){
                updateDetail();
            });
        }
    }else{
        alert('请选择详情页！');
    }
}

function backTable(){
    $('.my-content-detail').hide();
    $('.my-content').show();
    $('.breadcrumb').find('li:last').remove();
    myData.detailId = null;
}

function updateTable(page, name){
    var url = '';
    if(name){
        url = '/desktop/actionAppLists?name='+name+'&page='+ page +'&pageSize='+pageSize;
    }else{
        url = '/desktop/actionAppLists?page='+ page +'&pageSize='+pageSize;
    }
    AjaxGet(url, function(data){
        createElem(data, page);
        myData.targetId = null;
    });
}

function updateDetail(){
    AjaxGet('/desktop/actionAppLists?id=' + myData.targetId, function(data){
        createDetail(data);
        trHover('#detailTable');
        myData.detailId = null;
    });
}

$('#handleType').on('change', updateHandleType);

function updateHandleType(){    //ACTION类型事件
    $('.handle').hide();
    myData.handleType = $(this).val();
    myData.KeyvalLists = [];
    if(myData.handleType === 'ACTION'){
        $('.handle-action').show();
    }else if(myData.handleType === 'COMPONENT'){
        $('.handle-component').show();
    }else if(myData.handleType === 'SCHEME'){
        $('.handle-uri').show();
        $('.handle-action').show();
    }
}

$('#subKeyVal').on('click', function() {
    var key = $('#myKey').val();
    var val = $('#myVal').val();

    if(key == ' ' || !key){
        alert('请输入KEY');
        return ;
    }
    if(val == ' ' || !val){
        alert('请输入VAL');
        return ;
    }

    for(var i = 0, len = myData.KeyvalLists.length; i < len; i++){
        var elem = myData.KeyvalLists[i];
        if(elem[0] === key && elem[1] === val){
            alert('该附加数据已存在');
            return ;
        }
    }
    myData.KeyvalLists.push([key, val]);
    $('#keyValList').append('<div><label title="'+ key +'='+ val +'">'+ key +'&emsp;&emsp;=&emsp;&emsp;'+ val +'</label><button type="button" class="close">×</button></div>');
});

//删除KEY-VAL
$('.my-listVal').on('click', '.close', function(){
    $this = $(this);
    var elem = $this.siblings('label').text().split('--');
    var key = elem[0];
    var val = elem[1];
    myData.KeyvalLists.forEach(function(e, i){
        if(e[0] === key && e[1] === val){
            myData.KeyvalLists.splice(i, 1);
            return;
        }
    });
    $this.parent().remove();
});

$('#subDetail').on('click', function() {
    var detailName = $('#detailName').val();

    var handleType = myData.handleType;
    var extraData = [];
    var title = $('#detailModal').find('h4').html();

    if(detailName == ' ' || ! detailName){
        alert('请输入详情页名称');
        return false;
    }

    if(myData.handleType === '请选择ACTION类型'){
        alert('请选择ACTION类型');
        return false;
    }

    var keyLists = $('#detailModal .myKey');
    var valLists = $('#detailModal .myVal');
    var typeLists = $('#detailModal .myType');
    var len = valLists.length;
    for(var i = 0; i < len; i++){
        var key = $(keyLists[i]).val();
        var val = $(valLists[i]).val();
        var type = $(typeLists[i]).val();
        var temp = {};
        if(key == ' ' || !key){
            alert('请输入KEY');
            return false;
        }
        if(val == ' ' || !val){
            alert('请输入VALUE');
            return false;
        }
        if(type !== '请选择数据类型'){
            temp.type = type;
        }
        temp.key = key;
        temp.value = val;

        extraData.push(temp);
    }

    var data = {"actionAppId": myData.targetId, "detailName": detailName, "actionType": handleType, "extraData": extraData};

    if(myData.handleType === 'ACTION'){
        var action = $('#actionVal').val();
        if(action == ' ' || !action){
            alert('请输入action');
            return false;
        }
        data.action = action;
    }else if(myData.handleType === 'COMPONENT'){
        var component = $('#componentVal').val();
        var clsName = $('#clsNameVal').val();
        if(component == ' ' || ! component){
            alert('请输入包名');
            return false;
        }
        if(clsName == ' ' || ! clsName){
            alert('请输入clsName');
            return false;
        }
        data.component = component;
        data.clsName = clsName;
    }else if(myData.handleType === 'SCHEME'){
        var clsUri = $('#clsUriVal').val();
        var action = $('#actionVal').val();
        if(clsUri == ' ' || !clsUri){
            alert('请输入uri');
            return false;
        }
        data.uri = clsUri;
        data.action = action;
    }

    if(title == '新增'){
        AjaxPost('/desktop/addActionAppDetail', data, function(){
            $('#detailModal').modal('hide');
            updateDetail();
        });
    }else if(title == '修改'){
        data.id = myData.detailId;
        AjaxPost('/desktop/modifyActionAppDetail', data, function(){
            $('#detailModal').modal('hide');
            updateDetail();
        });
    }
});

//创建绑定应用下拉框
function selectApk(data, pkgName){
    var arr = data.extra;
    var con = '<option value="请选择应用名称">请选择应用名称</option>';
    var $select = $('#appName');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].pkgName + '">' + arr[i].appName + '</option>';
    }
    if(pkgName){
        $select.html(con).val(pkgName).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
    }else{
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
    }
}

$('#subApp').on('click', function(){
    var appName = $('#appName option:selected').text();
    var pkgName = $('#appName').val();
    var title = $('#appModal').find('h4').html();
    var filter = $('#myTable_filter input').val() || '';

    if(appName == ' ' || !appName){
        alert('请输入应用名称');
        return false;
    }
    var data = {"appName": appName, "pkgName": pkgName};
    if(title == '新增'){
        AjaxPost('/desktop/addActionApp', data, function(){
            $('#appModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title == '修改'){
        data.id = myData.targetId;
        AjaxPost('/desktop/modifyActionApp', data, function(){
            $('#appModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName, arr.pkgName, null]);
    }
    $('#myTable').dataTable({
        "lengthChange": false,
        "autoWidth":false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "data": dataArr,
        "order": [[1, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'名称','width':'15%', 'targets':1},
            {'title':'包名','width':'15%', 'targets':2},
            {'title':'详情页','width':'10%', 'targets':3},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(3, nRow, 'list');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
                'pkgName': aData[2],
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
	initToolBar('#detailTable', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);

    updatePagination(len, page, data.count);
    initToolBar('#myTable',[ myConfig.addBtn, myConfig.editBtn, myConfig.delBtn,'<a class="btn my-btn btn-primary copyBtn" href="javascript:"><i class="fa fa-copy icon-white"></i>&nbsp;复制</a>']);

		


}

function createDetail(data){
    var dataArr = [];
    var len = data.extra.extraData.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra.extraData[i];
        var action = arr.action || '--';
        var component = arr.component || '--';
        var clsName = arr.clsName || '--';
        dataArr.push([arr.id, arr.detailName, arr.actionType, action, component, clsName, arr.extraData]);
    }
    myDataTable('#detailTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'8%', 'targets':0},
            {'title':'详情页名称','width':'10%', 'targets':1},
            {'title':'类型','width':'12%', 'targets':2},
            {'title':'action','width':'18%', 'targets':3},
            {'title':'包名','width':'18%', 'targets':4},
            {'title':'clsName','width':'18%', 'targets':5},
            {'title':'附加数据','width':'8%', 'targets':6},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(6, nRow, 'list');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "key-val": aData[6]
            });
        }
    });
    initToolBar('#detailTable', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}

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

$('#addkeyVal').on('click', function(){
    var con = getExtraHtml();
    $('#detailModal .my-form').append(con);
});

$('#detailModal').on('click', '.delKeyVal', function(){
    $(this).parent().remove();
});

function getExtraHtml(key, value){
    key = key ? 'value="'+ key +'"' : '';
    value = value ? 'value='+ value : '';
    return  '<div class="form-group" style="position: relative;">'+
                '<label for="extraData">&nbsp;</label>'+
                '<input type="text" class="form-control myKey" placeholder="请输入KEY" style="width: 20%;margin-left: 2px;" '+ key +'>'+
                '&nbsp;=&nbsp;'+
                '<input type="text" class="form-control myVal" placeholder="请输入VALUE" style="width: 20%;" '+ value +'>'+
                '&nbsp;&nbsp;'+
                '<select class="chosen-select form-control myType" data-placeholder="请选择数据类型" style="width: 23%;position: relative;top: 1px;">'+
                    '<option value="请选择数据类型">请选择数据类型</option>'+
                    '<option value="int">整型</option>'+
                    '<option value="long">长整型</option>'+
                    '<option value="float">浮点型</option>'+
                    '<option value="double">双精度浮点型</option>'+
                    '<option value="boolean">布尔型</option>'+
                    '<option value="char">字符型</option>'+
                    '<option value="string">字符串型</option>'+
                '</select>'+
                '<button type="button" class="close delKeyVal" style="position: absolute;top: 6px;right: 48px;font-size: 20px;border-radius: 50%;border: 1px solid #0E0D0D;width: 22px;">×</button>'+
            '</div>';
}