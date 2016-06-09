//@ sourceURL=otaUpdate.publish.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/ota/publishModelVersionLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.info = obj.data('info');
        myData.type = obj.data('type');
        myData.forceUpdate = obj.data('forceUpdate');
        myData.model = obj.data('model');
        myData.vendorId = obj.data('vendorId');
        myData.versionCode = obj.data('versionCode');
        myData.fileID = obj.data('fileId');
        myData.groupId = obj.data('groupId');
        myData.AB = obj.data('AB');
        myData.fake = obj.data('fake');

        $('.groupBtn').attr('disabled', false);
        $('.ABBtn').attr('disabled', false);
        $('.underBtn').attr('disabled', false);

        if(myData.type === 'AB'){
            $('.groupBtn').attr('disabled', true);
        }else if(myData.type === 'ALL'){
            $('.groupBtn').attr('disabled', true);
            $('.ABBtn').attr('disabled', true);
            $('.underBtn').attr('disabled', true);
        }

        var tar = e.target;
        if(tar.className.indexOf('glyphicon-list sn-list') != -1){
            AjaxGet('/group/memberLists?group_id=' + myData.groupId, function(data){
                createSN(data);
                trHover('#macTable');
                $('#macModal').modal('show');
            });
            return;
        }

        if (tar.className.indexOf('glyphicon-list info-list') != -1) {//版本列表
            $('#infoDesc').val(myData.info.desc.join(';'));
            $('#infoWhite').val(myData.info.white.join(';'));
            $('#infoBlack').val(myData.info.black.join(';'));
            $('#infoModal').modal('show');
            return false;
        }
    });

    listenchoose();
    listenMyPage();
});

listenToolbar('under', underTableInfo);
listenToolbar('release', releaseTableInfo);
listenToolbar('group', releaseInfo);
listenToolbar('AB', releaseInfo);
listenToolbar('watch', watchTable);

function underTableInfo() {
    if (myData.ReleaseId) {
        if (confirm('确定下架？')) {
            var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/ota/deletePublishModelVersion?id=' + myData.ReleaseId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function releaseTableInfo(){
    AjaxWhen([
        AjaxGet('/group/nameLists', selectGroup, true),
        AjaxGet('/ota/modelLists', selectModelList, true)
    ], function(){
        $('#fileId').val('');
        $('#versionDesc').val('');
        $('#fileLength').val('');
        $('#versionWhite').val('');
        $('#versionBlack').val('');
        $('#chooseType input:eq(0)').trigger('click');
        $('#releaseModal').modal('show');
    });
}

function releaseInfo(){
    if (!myData.ReleaseId) {
        alert('请选择内容！');
        return false;
    }
    var $this = $(this);
    var str = $this.text().trim();
    if(str === '内测'){
        AjaxGet('/group/nameLists', function(data){
            selectGroup(data, $('#customGroup'));
            $('#customGroup').val(myData.groupId);
            $('#groupModal').modal('show');
        });
    }else if(str === '灰度'){
        $('#customCountNum').val(myData.AB == '--' ? '' : myData.AB);
        $('#ABModal').modal('show');
    }
}

function watchTable(){
    if (!myData.ReleaseId) {
        alert('请选择内容！');
        return false;
    }
    $('#infoDesc').val(myData.info.desc.join(';'));
    $('#infoWhite').val(myData.info.white.join(';'));
    $('#infoBlack').val(myData.info.black.join(';'));
    $('#infoModal').modal('show');
}

function updateTable(page, name){
    AjaxGet('/ota/publishModelVersionLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.ReleaseId = null;
    });
}

function releaseAll(data, fn){
    AjaxPost('/ota/modifyPublishModelVersion', data, function(){
        var filter = $('#myTable_filter input').val() || '';
        updateTable(currentPage, filter);
        fn();
    });
}

$('#chooseType > input').on('change', function(){     //内测、灰度、公开
    var $this = $(this);
    $this.prop('checked', true);
    var val = $this.val();
    if(val == "group"){
        $('#group').parent().show();
        $('#countNum').parent().hide();
    }else if(val == "AB"){
        $('#countNum').parent().show();
        $('#group').parent().hide();
    }else if(val == "ALL"){
        $('#group').parent().hide();
        $('#countNum').parent().hide();
    }
});

//创建配置包下拉框
function selectModelList(data){
    var arr = filterSameDate(data.extra);
    var con = '<option value="请选择型号">请选择型号</option>';
    var $select = $('#modelName');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].model+'">'+arr[i].model+'</option>';
        $select.data('_' + arr[i].model, arr[i]);
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    }).trigger('change');
}

$('#modelName').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    if(val === '请选择型号'){
        $('#vendorId').html('<option value="请选择vendorID">请选择vendorID</option>').trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change');
        return false;
    }
    selectVendorId($this.data('_' + val));
});

function selectVendorId(data){
    var arr = data.vendorID;
    var con = '<option value="请选择vendorID">请选择vendorID</option>';
    var $select = $('#vendorId');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+data.id[i]+'">'+arr[i]+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    }).trigger('change');
}

$('#vendorId').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    if(val === '请选择vendorID'){
        $('#versionCode').html('<option value="请选择版本">请选择版本</option>').trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
        return false;
    }
    AjaxGet('/ota/modelLists?modelID=' + val, selectVersionCode);
});

function selectVersionCode(data){
    var arr = descSort(data.extra);
    var con = '<option value="请选择版本">请选择版本</option>';
    var $select = $('#versionCode');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].version+'</option>';
        $select.data('_'+arr[i].id, arr[i]);
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}

$('#versionCode').on('change', function(){
    var $this = $(this);
    var data = $this.data('_'+$this.val());
    $('#fileId').val(data.fileID);
    $('#fileLength').val(data.length);
    $('#versionDesc').val(data.desc.join(';'));
    $('#versionWhite').val(data.whiteList.join(';'));
    $('#versionBlack').val(data.blackList.join(';'));

});

function filterSameDate(arr){
    var temp = [];
    var data = [];
    for(var i = 0, len = arr.length; i < len; i++){
        var idx = $.inArray(arr[i].model, temp);
        if(idx === -1){
            temp.push(arr[i].model);
            data.push({"model": arr[i].model, "vendorID": [arr[i].vendorID], "id": [arr[i].id]});
        }else{
            data[idx].vendorID.push(arr[i].vendorID);
            data[idx].id.push(arr[i].id);
        }
    }
    temp = null;
    return data;
}

//创建内测包下拉框
function selectGroup(data, $obj){
    var arr = data.groups;
    var con = '';
    var $select = $obj || $('#group');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
    }
    $select.html(con);
}

$('#subGroup').on('click', function(){
    var customGroup = $('#customGroup').val();
    releaseAll({
        "id": myData.ReleaseId,
        "type": "group",
        "groupId": customGroup,
        "forceUpdate": myData.forceUpdate,
        "fake": myData.fake,
        "model": myData.model,
        "vendorID": myData.vendorId,
        "version": myData.versionCode,
        "fileID": myData.fileID,
        "desc": myData.info.desc,
        "whiteList": myData.info.white,
        "blackList": myData.info.black
    }, function(){
        $('#groupModal').modal('hide');
    });
});

$('#subAB').on('click', function(){
    var countNum = $('#customCountNum').val();
    if(countNum == ' ' || !countNum){
        alert('请输入灰度数量');
        return;
    }else if(/\D/.test(countNum)){
        alert('灰度数量只能为数字');
        return;
    }
    releaseAll({
        "id": myData.ReleaseId,
        "type": "AB",
        "AB": countNum,
        "forceUpdate": myData.forceUpdate,
        "fake": myData.fake,
        "model": myData.model,
        "vendorID": myData.vendorId,
        "version": myData.versionCode,
        "fileID": myData.fileID,
        "desc": myData.info.desc,
        "whiteList": myData.info.white,
        "blackList": myData.info.black
    }, function(){
        $('#ABModal').modal('hide');
    });
});

$('#subRelease').on('click', function(){
    var type = $('#chooseType input:checked').val();
    var forceUpdate = $('#chooseForceUpdate input:checked').val();
    var fake = $('#fake input:checked').val();
    var modelName = $('#modelName').val();
    var vendorId = $('#vendorId').val();
    var versionCode = $('#versionCode').val();
    var fileId = $('#fileId').val();
    var fileLength = $('#fileLength').val();
    var versionDesc = $('#versionDesc').val().split(';');
    var versionWhite = $('#versionWhite').val().split(';');
    var versionBlack = $('#versionBlack').val().split(';');
    var version = $('#versionCode').data('_'+versionCode).version;
    var filter = $('#myTable_filter input').val() || '';
    var data = {};


    if(modelName == '请选择型号' || !modelName){
        alert('请选择型号');
        return false;
    }
    if(vendorId == '请选择vendorID' || !vendorId){
        alert('请选择vendorID');
        return false;
    }
    if(versionCode == '请选择版本' || !versionCode){
        alert('请选择版本');
        return false;
    }
    if(fileLength == '请输入长度' || !fileLength){
        alert('请输入长度');
        return false;
    }
	if(/\D/.test(fileLength)){
            alert('长度只能为数字');
            return;
        }
	if(type == 'group') {
        var group = $('#group').val();
        data.groupId = group;
    } else if (type == 'AB') {
        var countNum = $('#countNum').val();
        if(countNum == ' ' || !countNum){
            alert('请输入灰度数量');
            return;
        }else if(/\D/.test(countNum)){
            alert('灰度数量只能为数字');
            return;
        }
        data.AB = countNum;
    }
    if(type !== 'group' && fake === 'true'){
        alert('灰度不能选择假包！');
        return;
    }
    data.type = type;
    data.forceUpdate = forceUpdate;
    data.fake = fake;
    data.model = modelName;
    data.vendorID = $('#vendorId option:selected').text();
    data.version = version;
    data.fileID = fileId;
    data.length = fileLength;
    data.desc = filterBlankLine(versionDesc);
    data.whiteList = filterBlankLine(versionWhite);
    data.blackList = filterBlankLine(versionBlack);

    AjaxPost('/ota/publishModelVersion', data, function(){
        updateTable(currentPage, filter);
        $('#releaseModal').modal('hide');
    });
});

//创建发布列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    var info = [];
    var groups = [];
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        var AB = arr.AB || '--';
        dataArr.push([arr.id, arr.model, arr.vendorID, arr.type, AB, '--', arr.version, arr.fileID, arr.forceUpdate, arr.fake, arr.time]);
        info.push({"desc": arr.desc, "white": arr.whiteList, "black": arr.blackList});
        groups.push(arr.groupId);
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
        "stateSave": false,
        "data": dataArr,
        "order": [
            [10, "desc"]
        ],
        "columnDefs": [
            {'title':'ID','width':'5%', 'targets':0},
            {'title':'型号','width':'7%', 'targets':1},
            {'title':'vendorID','width':'7%', 'targets':2},
            {'title':'类型','width':'5%', 'targets':3},
            {'title':'灰度','width':'8%', 'targets':4},
            {'title':'设备列表','width':'7%', 'targets':5},
            {'title':'版本','width':'10%', 'targets':6},
            {'title':'fileID','width':'8%', 'targets':7},
            {'title':'强制升级','width':'7%', 'targets':8},
            {'title':'假包','width':'7%', 'targets':9},
            {'title':'发布时间','width':'10%', 'targets':10}
        ],
        "createdRow": function( nRow, aData, idx ){
            tableTypeColor(3, nRow, aData[3]);
            if(aData[3] == 'group'){
                tableTdIcon(5, nRow, 'list sn-list');
            }else{
                tableTdNull(5, nRow);
            }
            if(!aData[4]){
                tableTdNull(4, nRow);
            }
           // $('td:eq(7)', nRow).html(aData[7]);
            if(aData[8] === 'false'){
                $('td:eq(8)', nRow).html('否');
            }else{
                $('td:eq(8)', nRow).html('是');
            }
            if(aData[9] === 'false'){
                $('td:eq(9)', nRow).html('否');
            }else{
                $('td:eq(9)', nRow).html('是');
            }

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "model": aData[1],
                "vendorId": aData[2],
                "versionCode": aData[6],
                "AB": aData[4],
                "type": aData[3],
                "fileId": aData[7],
                "forceUpdate": aData[8],
                "fake": aData[9],
                "info": info[idx],
                "groupId": groups[idx]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'myTable');
    initToolBar('#myTable', [
        myConfig.releaseBtn,
        myConfig.underBtn,
        '<a class="btn my-btn btn-primary groupBtn" href="javascript:">&nbsp;内测</a>',
        '<a class="btn my-btn btn-primary ABBtn" href="javascript:">&nbsp;灰度</a>',
        '<a class="btn my-btn btn-primary watchBtn" href="javascript:">&nbsp;查看</a>'
    ]);
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