//@ sourceURL=live.livePublish.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/Live/publishLiveAdLists?page=1&pageSize=' + pageSize, function(data) {
        createAdPublish(data, 1);
        trHover('#adPublishTable');
    });

    trclick('#adPublishTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.type = obj.data('type');
        myData.groupId = obj.data('sn');
        myData.info = obj.data('info');
        myData.AB = obj.data('AB');
        myData.groupName = obj.data('name');
        // myData.channelList = obj.data('channelList');

        $('.groupBtn').attr('disabled', false);
        $('.ABBtn').attr('disabled', false);
        $('.ALLBtn').attr('disabled', false);

        if(myData.type === 'AB'){
            $('.groupBtn').attr('disabled', true);
        }else if(myData.type === 'ALL'){
            $('.groupBtn').attr('disabled', true);
            $('.ABBtn').attr('disabled', true);
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

        if(tar.className.indexOf('glyphicon-list ad-list') != -1){
            createAdDetail(myData.info);
            $('#fristTable').hide();
            $('#secondTable').show();
            $('.breadcrumb').append('<li class="active">'+myData.groupName+'</li>');
            return;
        }
    });

    trclick('#adDetailTable', function(obj, e) {
        var tar = e.target;
        myData.channelList = obj.data('channelList');
        if(tar.className.indexOf('glyphicon-list channel-list') != -1){
            createChannel();
            trHover('#channelTable');
            $('#channelModal').modal('show');
            return;
        }
    });

    listenchoose();
    listenMyPage('adPublishTable', currentPage);
});

listenToolbar('under', underTableInfo, '#adPublishTable');
listenToolbar('release', releaseTableInfo, '#adPublishTable');
listenToolbar('group', releaseInfo, '#adPublishTable');
listenToolbar('AB', releaseInfo, '#adPublishTable');
listenToolbar('ALL', releaseInfo, '#adPublishTable');

// listenToolbar('watch', watchInfo, '#adDetailTable');
listenToolbar('back', backInfo, '#adDetailTable');

function underTableInfo() {
    if (myData.ReleaseId) {
        if (confirm('确定下架？')) {
            var filter = $('#adPublishTable_filter input').val() || '';
            AjaxPost('/Live/deletePublishLiveAd?id', [myData.ReleaseId], function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择广告！');
    }
}

function releaseTableInfo(){
    AjaxGet('/group/nameLists', function(data){
        selectGroup(data);
        AjaxGet('/Live/liveAdGroupLists', function(data){
            selectAdGroup(data);
            $('#modelName').val('');
            $('#vendorID').val('');
            $('#chooseModel input:eq(1)').trigger('click');
            $('#chooseType input:eq(0)').trigger('click');
            $('#releaseModal').modal('show');
        });
    });
}

function releaseInfo(){
    if (!myData.ReleaseId) {
        alert('请选择内容！');
        return false;
    }
    var $this = $(this);
    var str = $this.text().trim();
    $('#editModal h4').text(str);
    if(str === '内测'){
        AjaxGet('/group/nameLists', function(data){
            selectGroup(data, $('#customGroup'));
            $('#customGroup').val(myData.groupId);
            $('.edit-group').show();
            $('.edit-count').hide();
        });
    }else if(str === '灰度'){
        $('#customCountNum').val(myData.AB == '--' ? '' : myData.AB);
        $('.edit-group').hide();
        $('.edit-count').show();
    }else{
        var filter = $('#adPublishTable_filter input').val() || '';
        AjaxPost('/Live/modifyPublishLiveAd', {"id": myData.ReleaseId, "type": "ALL"}, function(){
            updateTable(currentPage, filter);
            $('#editModal').modal('hide');
        });
        return;
    }
    $('#editModal').modal('show');
}

function watchInfo(){
    if (!myData.channelList) {
        alert('请选择广告！');
        return false;
    }
    createChannel();
    trHover('#channelTable');
    $('#channelModal').modal('show');
}

function backInfo(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.channelList = null;
}

function updateTable(page, name){
    AjaxGet('/Live/publishLiveAdLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createAdPublish(data, page);
        myData.ReleaseId = null;
    });
}

$('#chooseModel > input').on('click', function(){   //全型号和自定义
    $(this).prop('checked');
    var val = $(this).val();
    if(val == "ALL"){
        $('#modelName').val(val);
        $('#modelName').parent().hide();
    }else{
        $('#modelName').val('');
        $('#modelName').parent().show();
    }
});

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

//创建广告组下拉框
function selectAdGroup(data){
    var arr = data.extra;
    var con = '<option value="请选择广告组">请选择广告组</option>';
    var $select = $('#adGroup');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}

// $('#adGroup').on('change', function(){
//     var $this = $(this);
//     var val = $this.val();
//     var con = '<option value="请选择广告">请选择广告</option>';
//     var $select = $('#adDetail');
//     AjaxGet('/Live/liveAdLists?groupId=' + val, function(data){
//         var arr = data.extra;
//         for( var i=0; i<arr.length; i++ ){
//             con += '<option value="'+arr[i].id+'">'+arr[i].name+'</option>';
//         }
//         $select.html(con).trigger("chosen:updated.chosen").chosen({
//             allow_single_deselect: true,
//             width: "70%"
//         }).trigger('change');
//     });
// });

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

$('#subRelease').on('click', function(){
    var type = $('#chooseType input:checked').val();
    var modelType = $('#chooseModel input:checked').val();
    var modelName = $('#modelName').val();
    var vendorId = $('#vendorID').val() || 'none';


    var adGroup = $('#adGroup').val();
    // var adDetail = $('#adDetail').val();
    var filter = $('#adPublishTable_filter input').val() || '';
    var data = {};


    if(modelType === 'All'){
        modelName = modelType;
    }else{
        if(modelName == '请输入型号' || !modelName){
            alert('请输入型号');
            return false;
        }
    }
    if(adGroup == '选择广告组' || !adGroup){
        alert('选择广告组');
        return false;
    }
    // if(adDetail == '选择广告' || !adDetail){
    //     alert('选择广告');
    //     return false;
    // }

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

    $.extend(data, {
        "adGroupId": adGroup,
        "model": modelName,
        "vendorID": vendorId,
        "type": type
    });

    AjaxPost('/Live/publishLiveAd', data, function(){
        updateTable(currentPage, filter);
        $('#releaseModal').modal('hide');
    });
});

$('#subEdit').on('click', function(){
    var title = $('#editModal h4').text();

    var filter = $('#adPublishTable_filter input').val() || '';
    var data = {"id": myData.ReleaseId};

    if(title === '内测'){
        data.groupId = $('#customGroup').val();
        data.type = 'group';
    }else if(title === '灰度'){
        var countNum = $('#customCountNum').val();
        if(countNum == ' ' || !countNum){
            alert('请输入灰度数量');
            return;
        }else if(/\D/.test(countNum)){
            alert('灰度数量只能为数字');
            return;
        }
        data.AB = countNum;
        data.type = 'AB';
    }else if(title === '公开'){
        data.type = 'ALL';
    }

    AjaxPost('/Live/modifyPublishLiveAd', data, function(){
        updateTable(currentPage, filter);
        $('#editModal').modal('hide');
    });
});

//创建发布列表
function createAdPublish(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    var info = [];
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.model + '-' + arr.id, arr.vendorID, arr.name, arr.type, arr.groupId || '--', arr.AB || '--', '--', formatDate(arr.version), formatDate(arr.updateTime)]);
        info.push(arr.adList.ad);
    }
    $('#adPublishTable').dataTable({
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
            [7, "desc"]
        ],
        "columnDefs": [
            {'title':'型号','width':'12%', 'targets':0},
            {'title':'vendorID','width':'15%', 'targets':1},
            {'title':'广告组名称','width':'15%', 'targets':2},
            {'title':'类型','width':'7%', 'targets':3},
            {'title':'设备列表','width':'7%', 'targets':4},
            {'title':'灰度','width':'7%', 'targets':5},
            {'title':'广告列表','width':'7%', 'targets':6},
            {'title':'版本','width':'10%', 'targets':7},
            {'title':'发布时间','width':'10%', 'targets':8}
        ],
        "createdRow": function( nRow, aData, idx ){
            var temp = aData[0].split('-');
            tableTypeColor(3, nRow, aData[3]);
            if(aData[3] == 'group'){
                tableTdIcon(4, nRow, 'list sn-list');
            }else{
                tableTdNull(4, nRow);
            }
            tableTdIcon(6, nRow, 'list ad-list');

            $('td:eq(0)', nRow).data({
                "id": temp[1],
                "type": aData[3],
                "sn": aData[4],
                "AB": aData[5],
                "name": aData[2],
                "info": info[idx]
            }).html(temp[0]);
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'adPublishTable');
    initToolBar('#adPublishTable', [
        myConfig.releaseBtn,
        myConfig.underBtn,
        '<a class="btn my-btn btn-primary groupBtn" href="javascript:">&nbsp;内测</a>',
        '<a class="btn my-btn btn-primary ABBtn" href="javascript:">&nbsp;灰度</a>',
        '<a class="btn my-btn btn-primary ALLBtn" href="javascript:">&nbsp;公开</a>'
    ]);
}

// function createWatch(){
//     var data = myData.info;
//     $('#adUrl').val(data.url);
//     $('#adWidth').val(data.width);
//     $('#adHeight').val(data.height);
//     $('#adPosX').val(data.x);
//     $('#adPoxY').val(data.y);
//     $('#startTime').val(data.startTime);
//     $('#endTime').val(data.endTime);
//     $('#interval').val(data.interval);
//     $('#duration').val(data.duration);
//     $('#maxShowTimes').val(data.maxShowTimes);
// }

function createAdDetail(data){
    var dataArr = [];
    var len = data.length;
    var channelList = [];
    for (var i = 0; i < len; i++) {
        var arr = data[i];
        dataArr.push([arr.name, arr.interval, arr.duration, arr.maxShowTimes, arr.startTime, arr.endTime, 'x:'+arr.posX+', y:'+arr.posY, arr.width + 'X' + arr.height, '--', arr.url]);
        channelList.push(arr.channelList);
    }
    myDataTable('#adDetailTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'广告名称','width':'10%', 'targets':0},
            {'title':'展示间隔','width':'8%', 'targets':1},
            {'title':'展示持续时间（秒）','width':'15%', 'targets':2},
            {'title':'最大展示次数','width':'15%', 'targets':3},
            {'title':'开始时间','width':'10%', 'targets':4},
            {'title':'结束时间','width':'10%', 'targets':5},
            {'title':'坐标','width':'8%', 'targets':6},
            {'title':'广告宽高','width':'8%', 'targets':7},
            {'title':'频道列表','width':'8%', 'targets':8},
            {'title':'广告页面','width':'8%', 'targets':9}
        ],
        "createdRow": function( nRow, aData, idx ){
            $('td:eq(9)', nRow).html('<a class="fa fa-file icon-black my-icon" href="'+ aData[9] +'" target="_blank" style="color: black;"></a>').addClass('center');
            tableTdIcon(8, nRow, 'list channel-list');

            $('td:eq(0)', nRow).data({
                "channelList": channelList[idx]
            });
        }
    });
    initToolBar('#adDetailTable', [
        myConfig.backBtn
    ]);
}

function createChannel(){
    var dataArr = [];
    var len = myData.channelList.length;
    for (var i = 0; i < len; i++) {
        var arr = myData.channelList[i];
        dataArr.push([arr]);
    }
    myDataTable('#channelTable', {
        "data": dataArr,
        "paging": false,
        "searching": false,
        "columnDefs": [
            {'title':'频道名称','width':'100%', 'targets':0}
        ]
    });
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