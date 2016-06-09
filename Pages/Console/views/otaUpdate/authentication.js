//@ sourceURL=otaUpdate.authentication.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/ota/publishModelVersionLists?page=1&type=authentication&pageSize=' + pageSize, function(data) {
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

        $('.ALLBtn').attr('disabled', false);

        if(myData.type === 'ALL'){
            $('.ALLBtn').attr('disabled', true);
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
listenToolbar('ALL', releaseInfo);
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
    releaseAll({
        "id": myData.ReleaseId,
        "type": "ALL",
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
        $('#releaseModal').modal('hide');
    });
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
    AjaxGet('/ota/publishModelVersionLists?name='+name+'&type=authentication&page='+ page +'&pageSize='+pageSize, function(data){
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

//创建发布列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    var info = [];
    var groups = [];
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        var AB = arr.AB || '--';
        dataArr.push([arr.id, arr.model, arr.vendorID,  arr.type, AB, '--', arr.version, arr.fileID, arr.forceUpdate, arr.fake, arr.time]);
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
            {'title':'型号','width':'12%', 'targets':1},
            {'title':'vendorID','width':'6%', 'targets':2},
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
            //$('td:eq(7)', nRow).html(formatDate(aData[7]));
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
        myConfig.underBtn,
        '<a class="btn my-btn btn-primary ALLBtn" href="javascript:">&nbsp;公开</a>',
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