//@ sourceURL=live.checkLiveList.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
    $('#page-content .my-content').hide();
	AjaxGet('/Live/getListName', function(data) {
        selectListName(data);
    });

    trclick('#channelTypeTable', function (obj, e) {
        myData.id = obj.data('id');
    });

    listenchoose();

});



function selectListName(data){
    var arr = data.extra;
    var con = '<option value="请选择分类">请选择分类</option>';
    var $select = $('#channelType');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].nameId + '">' + arr[i].name + '</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "150px"
    });
    $('#page-content .my-content').show();
}

$('#getChannelData').on('click', function(){
    $('#channelTable').parent().hide();
    $('#channelTable_wrapper').parent().hide();
    $('#channelTypeTable_wrapper').show();
    $('#channelTypeTable').show();
    var channelType = $('#channelType').val();
    if(channelType === '请选择分类'){
        alert('请选择分类');
        return;
    }

    AjaxGet('/Live/getListType?nameId=' + channelType, createChannelTable);
});




function createChannelTable(data){
    var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.typeName, arr.pinyin, arr.urlSimp, arr.url, arr.typeId, arr.isRecommend, formatDate(arr.version, true), arr.id]);
    }
    myDataTable('#channelTypeTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'名称','width':'13%', 'targets':0},
            {'title':'拼音','width':'18%', 'targets':1},
            {'title':'新类型','width':'8%', 'targets':2},
            {'title':'旧类型','width':'8%', 'targets':3},
            {'title':'类型ID','width':'25%', 'targets':4},
            {'title':'是否推荐','width':'8%', 'targets':5},
            {'title':'版本','width':'12%', 'targets':6},
            {'title':'频道列表','width':'8%', 'targets':7}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id": aData[7]
            });
            tableTdDownload(2, nRow, aData[2]);
            tableTdDownload(3, nRow, aData[3]);
            if(aData[5] === 'true'){
                $('td:eq(5)', nRow).html('是');
            }else{
                $('td:eq(5)', nRow).html('否');
            }
            $('td:eq(7)', nRow).html('<i class="glyphicon glyphicon-list icon-black my-icon chan" data-per="memberManager"></i>');
        }
    });
    $('#channelTypeTable .chan').parent().on('click', function() {
    myData.Id = $(this).parent().children('td:eq(0)').data('id');
    AjaxGet('/Live/getListTypeChannel?id=' + myData.Id, function(data) {
        $('#channelTypeTable_wrapper').hide();
        $('#channelTypeTable').hide();
        $('#channelTable').parent().show();
        $('#channelTable_wrapper').parent().show();
        $('#page-content .my-choose').hide();
        createTable(data);
    });
    });
}

function createTable(data){
    var dataArr = [];
    var len = data.channelLists.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.channelLists[i];
        dataArr.push([arr.number, arr.name, arr.pinyin, arr.id, arr.playLists]);
    }
    myDataTable('#channelTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'排序','width':'20%', 'targets':0},
            {'title':'频道名称','width':'20%', 'targets':1},
            {'title':'拼音','width':'20%', 'targets':2},
            {'title':'频道ID','width':'20%', 'targets':3},
            {'title':'源','width':'20%', 'targets':4}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(4)', nRow).data({
                "playLists": aData[4]
            });
            $('td:eq(4)', nRow).html('<i class="glyphicon glyphicon-list icon-black my-icon biu" data-per="memberManager"></i>');
        }
    });
    initToolBar('#channelTable', [
        '<a class="btn my-btn btn-primary backBtn" id="changeTable" href="javascript:">&nbsp;返回</a>'
    ]);
    $('#changeTable').on('click', function() {
        $('#channelTable').parent().hide();
        $('#channelTable_wrapper').parent().hide();
        $('#channelTypeTable_wrapper').show();
        $('#channelTypeTable').show();
        $('#page-content .my-choose').show();
    });
    $('#channelTable .biu').parent().on('click', function() {
        sourceTable($(this).data('playLists'));
        $('#sourceModal').modal('show');
    });
}

function sourceTable(data){
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.stbPlayUrl, arr.type]);
    }
    myDataTable('#sourceTable', {
        "data": dataArr,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "columnDefs": [
            {'title':'播放链接','width':'20%', 'targets':0},
            {'title':'类型','width':'20%', 'targets':1}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
        }
    });
    $('#sourceTable_wrapper').css({
        'width': '1000px',
        'margin-left': '67px'
    });
}