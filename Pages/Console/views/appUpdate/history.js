//@ sourceURL=appUpdate.history.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/App/appUpdateHistoryLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.info = obj.data('info');
        myData.snId = obj.data('sn');

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

listenToolbar('watch', watchTable);

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
    $('#infoFake').val(getTrueOrFalse(myData.info.fake));
    $('#infoModal').modal('show');
}

function updateTable(page, name){
    AjaxGet('/App/appUpdateHistoryLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.ReleaseId = null;
    });
}

//创建发布列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    var info = [];
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.model, arr.appName, arr.vendorid, arr.type, arr.AB || '--', arr.groupId || '--', arr.versionCode, arr.reason, arr.user, arr.time]);
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
            [10, "desc"]
        ],
        "columnDefs": [
            {'title':'ID','width':'5%', 'targets':0},
            {'title':'型号','width':'10%', 'targets':1},
            {'title':'应用名称','width':'10%', 'targets':2},
            {'title':'vendorID','width':'8%', 'targets':3},
            {'title':'类型','width':'5%', 'targets':4},
            {'title':'灰度','width':'5%', 'targets':5},
            {'title':'设备列表','width':'5%', 'targets':6},
            {'title':'版本','width':'8%', 'targets':7},
            {'title':'操作原因','width':'8%', 'targets':8},
            {'title':'操作用户','width':'8%', 'targets':9},
            {'title':'操作时间','width':'10%', 'targets':10}
        ],
        "createdRow": function( nRow, aData, idx ){
            tableTypeColor(4, nRow, aData[4]);
            if(aData[4] == 'group'){
                tableTdIcon(6, nRow, 'list sn-list');
            }else{
                tableTdNull(6, nRow);
            }

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "sn": aData[6],
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
    $('#macTable_filter').prepend('<h4 style="position: absolute;margin: 6px 0;">'+ data.group_name +'</h4>');
}

function getTrueOrFalse(str){
    return str === 'false' ? '否' : '是';
}