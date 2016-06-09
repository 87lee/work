//@ sourceURL=androidFirmware.configPacketRelease.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/androidFirmware/publishConfigGroupLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.ReleaseName = obj.data('name');
        myData.ReleaseVendorId = obj.data('vendorId');
        myData.snId = obj.data('sn');
        myData.desc = obj.data('desc');

        var tar = e.target;
        if(tar.className.indexOf('glyphicon-list') != -1){
            AjaxGet('/group/memberLists?group_id=' + myData.snId, function(data){
                createSN(data);
                trHover('#macTable');
                $('#macModal').modal('show');
            });
            return;
        }
    });

    listenMyPage();
    listenchoose();
});

listenToolbar('under', underTableInfo);
listenToolbar('release', releaseTableInfo);
listenToolbar('edit', editTableInfo);

function underTableInfo() {
    if (myData.ReleaseId) {
        if (confirm('确定下架？')) {
        	var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/androidFirmware/deletePublishConfigGroup?id=' + myData.ReleaseId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function releaseTableInfo(){
    if (myData.ReleaseId) {
        var model = myData.ReleaseName === '默认' ? 'Default' : myData.ReleaseName;
        var vendorID = myData.ReleaseVendorId === '默认' ? '0' : myData.ReleaseVendorId;
        $('#modelName').val(model);
        $('#vendorId').val(vendorID);
        if(model === 'Default' && vendorID === '0'){
            $('#modelType input:eq(0)').trigger('click');
        }else{
            $('#modelType input:eq(1)').trigger('click');
        }
    }else{
        $('#modelName').val('');
        $('#vendorId').val('');
        $('#modelType input:eq(1)').trigger('click');
    }
    $('#chooseType input:eq(1)').trigger('click');
    AjaxWhen([
        AjaxGet('/group/nameLists', selectGroup, true),
        AjaxGet('/androidFirmware/configGroupLists', selectGroupList, true)
    ], function(){
        $('#releaseModal').modal('show');
    });
}

function editTableInfo(){
    if (myData.ReleaseId) {
        $('#descModal').modal('show');
        $('#descInfo').val(myData.desc === '--' ? '' : myData.desc);
    }else {
        alert('请选择内容！');
    }
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/androidFirmware/publishConfigGroupLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.ReleaseId = null;
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

//创建配置包下拉框
function selectGroupList(data){
    var arr = data.extra;
    var con = '';
    var $select = $('#configGroup');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}

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

$('#modelType input').on('click', function(){
    var $this = $(this);
    var val = $this.val();
    $this.prop('checked', true);
    if(val === 'false'){
        $('#modelName').parent().show();
        $('#vendorId').parent().show();
    }else if(val === 'true'){
        $('#modelName').parent().hide();
        $('#vendorId').parent().hide();
    }
});

$('#subRelease').on('click', function(){
    var type = $('#chooseType input:checked').val();
    var model = $('#modelName').val();
    var vendorID = $('#vendorId').val();
    var configGroupId = $('#configGroup').val();
    var modelType = $('#modelType input:checked').val();
    var desc = $('#desc').val();
    var filter = $('#myTable_filter input').val() || '';
    var data = {};

    if(modelType === 'false'){
        if(model == ' ' || !model){
            alert('请输入型号');
            return false;
        }
        if(vendorID == ' ' || !vendorID){
            alert('请输入vendorID');
            return false;
        }
    }else if(modelType === 'true'){
        model = 'Default';
        vendorID = '0';
    }

    if(configGroupId == '请选择配置包' || !configGroupId){
        alert('请选择配置包');
        return false;
    }
    if(type === 'group'){
        var groupId = $('#group').val();
        data.groupId = groupId;
    }
    data.type = type;
    data.model = model;
    data.vendorID = vendorID;
    data.configGroupId = configGroupId;
    data.desc = desc;

    AjaxPost('/androidFirmware/publishConfigGroup', data, function(){
        updateTable(currentPage, filter);
        $('#releaseModal').modal('hide');
    });

});

//修改备注
$('#subEdit').on('click', function(){
    var desc = $('#descInfo').val();
    var filter = $('#myTable_filter input').val() || '';

    AjaxPost('/androidFirmware/modifyPublishConfigGroupDesc', {
        "id": myData.ReleaseId,
        "desc": desc
    }, function(){
        updateTable(currentPage, filter);
        $('#descModal').modal('hide');
    });
});

//创建发布列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        var model = arr.model === 'Default' ? '默认' : arr.model;
        var vendorID = arr.vendorID === '0' ? '默认' : arr.vendorID;
        dataArr.push([arr.id, model, vendorID, arr.name, arr.type, formatDate(arr.version), arr.groupId || '--', arr.url, arr.desktopId || '--', arr.desc || '--']);
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
            [5, "desc"]
        ],
        "columnDefs": [
            {'title':'ID','width':'8%', 'targets':0},
            {'title':'型号','width':'12%', 'targets':1},
            {'title':'vendorID','width':'12%', 'targets':2},
            {'title':'配置包名','width':'10%', 'targets':3},
            {'title':'类型','width':'8%', 'targets':4},
            {'title':'版本','width':'15%', 'targets':5},
            {'title':'设备列表','width':'10%', 'targets':6},
            {'title':'链接','width':'8%', 'targets':7},
            {'title':'桌面ID','width':'8%', 'targets':8},
            {'title':'备注','width':'8%', 'targets':9}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTypeColor(4, nRow, aData[4]);
            if(aData[4] == 'group'){
                tableTdIcon(6, nRow, 'list');
            }else{
                tableTdNull(6, nRow);
            }
            tableTdDownload(7, nRow, aData[7]);
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "name":aData[1],
                "vendorId":aData[2],
                "sn":aData[6],
                "desc":aData[8]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable', [myConfig.releaseBtn, myConfig.editBtn, myConfig.underBtn]);
}

function createSN(data){
    var dataArr = [];
    var len = data.members.length || 0;
    for( var i=0; i<len; i++ ) {
        var arr = data.members[i];
        dataArr.push([arr.sn, arr.desc]);
    }
    myDataTable('#macTable', {
        "data": dataArr,
        "pageLength": 10,
        "columnDefs": [
            {'title':'Mac', 'width':'40%', 'targets':0},
            {'title':'desc', 'width':'60%', 'targets':1}
        ]
    });
    $('#macTable_filter').prepend('<h4 style="position: absolute;margin: 6px 0;">'+ (data.group_name || '') +'</h4>');
}