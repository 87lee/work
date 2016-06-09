//@ sourceURL=appUpdate.authentication.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/App/publishAppUpdateVersionLists?page=1&type=authentication&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.info = obj.data('info');
        myData.type = obj.data('type');
        myData.snId = obj.data('sn');
        myData.AB = obj.data('AB');

        $('.ALLBtn').attr('disabled', false);

        if(myData.type === 'ALL'){
            $('.ALLBtn').attr('disabled', true);
        }

        var tar = e.target;
        if(tar.className.indexOf('glyphicon-list sn-list') != -1){
             AjaxGet('/group/memberLists?group_id=' + myData.snId, function(data){
                createSN(data);
                trHover('#macTable');
                $('#macModal').modal('show');
            });
            return;
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
            AjaxGet('/App/deletePublishAppUpdateVersion?id=' + myData.ReleaseId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function releaseInfo(){
    if (!myData.ReleaseId) {
        alert('请选择内容！');
        return false;
    }
    $('.edit-type').hide();

    $('#editDesc').val(myData.info.desc.join(';'));
    $('#editWhite').val(myData.info.white.join(';'));
    $('#editBlack').val(myData.info.black.join(';'));
    $('#editForced').val(myData.info.miniForceUpdateVersion);
    $('#editPrompt').val(myData.info.miniUpdateVersion);
    if(myData.info.umeng === 'false'){
        $('#editUmeng input:eq(0)').trigger('click');
    }else{
        $('#editUmeng input:eq(1)').trigger('click');
    }
    if(myData.info.showTips === 'false'){
        $('#editShowTips input:eq(0)').trigger('click');
    }else{
        $('#editShowTips input:eq(1)').trigger('click');
    }
    if(myData.info.fake === 'false'){
        $('#editFake input:eq(0)').trigger('click');
    }else{
        $('#editFake input:eq(1)').trigger('click');
    }

    $('#editModal').modal('show');
}

function watchTable(){
    if (!myData.ReleaseId) {
        alert('请选择内容！');
        return false;
    }
    $('#infoDesc').val(myData.info.desc.join(';'));
    $('#infoWhite').val(myData.info.white.join(';'));
    $('#infoBlack').val(myData.info.black.join(';'));
    $('#infoForced').val(myData.info.miniForceUpdateVersion);
    $('#infoPrompt').val(myData.info.miniUpdateVersion);
    $('#infoUmeng').val(getTrueOrFalse(myData.info.umeng));
    $('#infoShowTips').val(getTrueOrFalse(myData.info.showTips));
    $('#infoFake').val(getTrueOrFalse(myData.info.fake));
    $('#infoModal').modal('show');
}

function updateTable(page, name){
    AjaxGet('/App/publishAppUpdateVersionLists?name='+name+'&type=authentication&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.ReleaseId = null;
    });
}

$('#subEdit').on('click', function(){
    var title = $('#editModal h4').text();
    var countNum = $('#customCountNum').val();
    var customGroup = $('#customGroup').val();

    var umengType = $('#editUmeng input:checked').val();
    var showTipsType = 'true' || $('#editShowTips input:checked').val();
    var fakeType = $('#editFake input:checked').val();

    var versionDesc = $('#editDesc').val().split(';');
    var versionWhite = $('#editWhite').val().split(';');
    var versionBlack = $('#editBlack').val().split(';');

    var miniForceUpdateVersion = $('#editForced').val();
    var miniUpdateVersion = $('#editPrompt').val();
    var filter = $('#myTable_filter input').val() || '';
    var data = {};

    if(title === '内测'){
        data.type = 'group';
        data.groupId = customGroup;
    }else if(title === '灰度'){
        if(countNum == ' ' || !countNum){
            alert('请输入灰度数量');
            return;
        }else if(/\D/.test(countNum)){
            alert('灰度数量只能为数字');
            return;
        }
        data.type = 'AB';
        data.AB = countNum;
    }else if(title === '公开'){
        data.type = 'ALL';
    }

    if(title !== '内测' && fakeType === 'true'){
        alert('灰度与公开不能选择假包！');
        return;
    }

    $.extend(data, {
        "id": myData.ReleaseId,
        "desc": filterBlankLine(versionDesc),
        "whiteList": filterBlankLine(versionWhite),
        "blackList": filterBlankLine(versionBlack),
        "umeng": umengType,
        "showTips": showTipsType,
        "fake": fakeType,
        "miniForceUpdateVersion": miniForceUpdateVersion,
        "miniUpdateVersion": miniUpdateVersion
    });

    AjaxPost('/App/modifyPublishAppUpdateVersion', data, function(){
        updateTable(currentPage, filter);
        $('#editModal').modal('hide');
    });
});

//创建发布列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    var info = [];
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName, arr.pkgName, arr.channel, arr.versionCode, arr.model, arr.vendorid, arr.type, arr.AB || '--', arr.groupId || '--', arr.path, arr.time]);
        info.push({
            "miniForceUpdateVersion": arr.miniForceUpdateVersion,
            "miniUpdateVersion": arr.miniUpdateVersion,
            "umeng": arr.umeng,
            "fake": arr.fake,
            "showTips": arr.showTips,
            "desc": arr.desc,
            "white": arr.whiteList,
            "black": arr.blackList
        });
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
            [11, "desc"]
        ],
        "columnDefs": [
            {'title':'ID','width':'5%', 'targets':0},
            {'title':'应用名称','width':'10%', 'targets':1},
            {'title':'包名','width':'10%', 'targets':2},
            {'title':'渠道','width':'8%', 'targets':3},
            {'title':'版本','width':'8%', 'targets':4},
            {'title':'型号','width':'10%', 'targets':5},
            {'title':'vendorID','width':'8%', 'targets':6},
            {'title':'类型','width':'5%', 'targets':7},
            {'title':'灰度','width':'5%', 'targets':8},
            {'title':'设备列表','width':'5%', 'targets':9},
            {'title':'下载','width':'5%', 'targets':10},
            {'title':'发布时间','width':'8%', 'targets':11}
        ],
        "createdRow": function( nRow, aData, idx ){
            tableTypeColor(7, nRow, aData[7]);
            if(aData[7] == 'group'){
                tableTdIcon(9, nRow, 'list sn-list');
            }else{
                tableTdNull(9, nRow);
            }
            tableTdDownload(10, nRow, aData[10]);

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "type": aData[7],
                "AB": aData[8],
                "sn": aData[9],
                "info": info[idx]
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