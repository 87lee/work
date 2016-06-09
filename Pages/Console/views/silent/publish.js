//@ sourceURL=silent.publish.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/Silent/publishSilentLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.type = obj.data('type');
        myData.groupId = obj.data('sn');
        myData.info = obj.data('info');
        myData.AB = obj.data('AB');
        myData.range = obj.data('range');

        $('.groupBtn').attr('disabled', false);
        $('.ABBtn').attr('disabled', false);
        $('.ALLBtn').attr('disabled', false);

        if(myData.type === 'AB'){
            $('.groupBtn').attr('disabled', true);
        }else if(myData.type === 'ALL'){
            $('.groupBtn').attr('disabled', true);
            $('.ABBtn').attr('disabled', true);
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
    });

    listenchoose();
    listenMyPage();
});

listenToolbar('under', underTableInfo);
listenToolbar('release', releaseTableInfo);
listenToolbar('group', releaseInfo);
listenToolbar('AB', releaseInfo);
listenToolbar('ALL', releaseInfo);
listenToolbar('watch', watchInfo);

function underTableInfo() {
    if (myData.ReleaseId) {
        if (confirm('确定下架？')) {
            var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/Silent/deletePublishSilent?id=' + myData.ReleaseId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function releaseTableInfo(){
    AjaxGet('/group/nameLists', function(data){
        selectGroup(data);
        AjaxGet('/Silent/appGroupLists', function(data){
            selectSilent(data);
            selectActive();
            $('#modelName').val('');
            $('#vendorID').val('');
            $('#idle').val('');
            $('#duration').val('');
            $('#chooseModel input:eq(1)').trigger('click');
            $('#chooseType input:eq(0)').trigger('click');
            $('#chooseRange input:eq(2)').trigger('click');
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
        $('.edit-group').hide();
        $('.edit-count').hide();
    }
    if(myData.range === '越狱'){
        $('#customRange input:eq(0)').trigger('click');
    }else if(myData.range === '非越狱'){
        $('#customRange input:eq(1)').trigger('click');
    }else if(myData.range === '全部'){
        $('#customRange input:eq(2)').trigger('click');
    }
    $('#editModal').modal('show');
}

function watchInfo(){
    if (!myData.ReleaseId) {
        alert('请选择内容！');
        return false;
    }
    createWatch();
    $('#watchModal').modal('show');
}

function updateTable(page, name){
    AjaxGet('/Silent/publishSilentLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
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

$('#silentGroup').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    var $active = $('.active-type');
    $active.hide();
    if(val !== '请选择静默组'){
        AjaxGet('/Silent/appGroupItemLists?groupId=' + val, function(data){
            var arr = data.extra;
            for(var i = arr.length; i--;){
                if(arr[i].action === 'active'){
                    $active.show();
                    break;
                }
            }
        });
    }
});

//创建静默列表下拉框
function selectSilent(data){
    var arr = data.extra;
    var con = '<option value="请选择静默组">请选择静默组</option>';
    var $select = $('#silentGroup');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    }).trigger('change');
}

function selectActive(){
    var con24 = '';
    var con60 = '';
    var $start = $('.startActive');
    var $end = $('.endActive');
    var i = 0;
    for( i=0; i< 24; i++ ){
        i = i < 10 ? ('0' + i) : i;
        con24 += '<option value="'+ i +'">'+ i +'</option>';
    }
    for( i=0; i< 60; i++ ){
        i = i < 10 ? ('0' + i) : i;
        con60 += '<option value="'+ i +'">'+ i +'</option>';
    }
    $('.startActive:eq(0)').html(con24);
    $('.startActive:eq(1)').html(con60);
    $('.startActive:eq(2)').html(con60);
    $('.endActive:eq(0)').html(con24);
    $('.endActive:eq(1)').html(con60);
    $('.endActive:eq(2)').html(con60);
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

$('#subRelease').on('click', function(){
    var type = $('#chooseType input:checked').val();
    var modelType = $('#chooseModel input:checked').val();
    var modelName = $('#modelName').val();
    var vendorId = $('#vendorID').val() || 'none';
    var range = $('#chooseRange input:checked').val();
    var startActive = $('.startActive');
    var endActive = $('.endActive');
    var idle = $('#idle').val();
    var duration = $('#duration').val();
    var silentGroup = $('#silentGroup').val();

    var filter = $('#myTable_filter input').val() || '';
    var data = {};


    if(modelType === 'All'){
        modelName = modelType;
    }else{
        if(modelName == '请输入型号' || !modelName){
            alert('请输入型号');
            return false;
        }
    }
    if(silentGroup == '选择静默组' || !silentGroup){
        alert('选择静默组');
        return false;
    }

    if($('.active-type').is(':visible')){
        if(idle == ' ' || !idle || duration == ' ' || !duration) {
            alert('空闲激活时间或持续激活时间不能为空');
            return false;
        }
        if(/\D/.test(idle) || /\D/.test(duration)) {
            alert('空闲激活时间和持续激活时间必须为数字');
            return false;
        }
        if(idle < 10 || duration < 10){
            alert('空闲激活时间和持续激活时间必须为大于或等于10');
            return false;
        }

        data.startActive = startActive.eq(0).val() + ':' + startActive.eq(1).val() + ':' + startActive.eq(2).val();
        data.endActive = endActive.eq(0).val() + ':' + endActive.eq(1).val() + ':' + endActive.eq(2).val();
        data.idle = idle;
        data.duration = duration;
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

    $.extend(data, {
        "silentGroupId": silentGroup,
        "model": modelName,
        "vendorID": vendorId,
        "type": type,
        "pubRange": range
    });

    AjaxPost('/Silent/publishSilent', data, function(){
        updateTable(currentPage, filter);
        $('#releaseModal').modal('hide');
    });
});

$('#subEdit').on('click', function(){
    var range = $('#customRange input:checked').val();
    var title = $('#editModal h4').text();

    var filter = $('#myTable_filter input').val() || '';
    var data = {"id": myData.ReleaseId, "pubRange": range};

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

    AjaxPost('/Silent/modifyPublishSilent', data, function(){
        updateTable(currentPage, filter);
        $('#editModal').modal('hide');
    });
});

//创建发布列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    var info = [];
    var range = {
        "jbk": "越狱",
        "unjbk": "非越狱",
        "all": "全部"
    };
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.model, arr.vendorID, arr.groupName, range[arr.pubRange], arr.type, arr.groupId || '--', arr.AB || '--', formatDate(arr.version)]);
        info.push({
            "startActive": arr.startActive,
            "endActive": arr.endActive,
            "idle": arr.idle,
            "duration": arr.duration,
            "groupContent": arr.groupContent
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
            [6, "desc"]
        ],
        "columnDefs": [
            {'title':'ID','width':'5%', 'targets':0},
            {'title':'型号','width':'15%', 'targets':1},
            {'title':'vendorID','width':'15%', 'targets':2},
            {'title':'静默组名称','width':'15%', 'targets':3},
            {'title':'发布范围','width':'8%', 'targets':4},
            {'title':'类型','width':'8%', 'targets':5},
            {'title':'设备列表','width':'8%', 'targets':6},
            {'title':'灰度','width':'8%', 'targets':7},
            {'title':'版本','width':'15%', 'targets':8}
        ],
        "createdRow": function( nRow, aData, idx ){
            tableTypeColor(5, nRow, aData[5]);
            if(aData[5] == 'group'){
                tableTdIcon(6, nRow, 'list sn-list');
            }else{
                tableTdNull(6, nRow);
            }

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "range": aData[4],
                "type": aData[5],
                "sn": aData[6],
                "AB": aData[7],
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
        myConfig.releaseBtn,
        myConfig.underBtn,
        '<a class="btn my-btn btn-primary groupBtn" href="javascript:">&nbsp;内测</a>',
        '<a class="btn my-btn btn-primary ABBtn" href="javascript:">&nbsp;灰度</a>',
        '<a class="btn my-btn btn-primary ALLBtn" href="javascript:">&nbsp;公开</a>',
        '<a class="btn my-btn btn-primary watchBtn" href="javascript:">&nbsp;查看</a>'
    ]);
}

function createWatch(){
    var dataArr = [];
    var len = myData.info.groupContent.length;
    var action = {
        "active": "激活",
        "remove": "卸载",
        "install": "安装"
    };
    $('#boxTimeInfo .timeInfo:eq(0)').text(myData.info.startActive);
    $('#boxTimeInfo .timeInfo:eq(1)').text(myData.info.endActive);
    $('#boxTimeInfo .timeInfo:eq(2)').text(myData.info.idle);
    $('#boxTimeInfo .timeInfo:eq(3)').text(myData.info.duration);
    for (var i = 0; i < len; i++) {
        var arr = myData.info.groupContent[i];
        dataArr.push([arr.appName, arr.pkgName, action[arr.action], arr.weight || '--', arr.versionName || '--', arr.versionCode || '--', arr.download || '--']);
    }
    myDataTable('#infoTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'应用名称','width':'15%', 'targets':0},
            {'title':'包名','width':'18%', 'targets':1},
            {'title':'行为','width':'8%', 'targets':2},
            {'title':'权重','width':'8%', 'targets':3},
            {'title':'版本名称','width':'15%', 'targets':4},
            {'title':'版本','width':'15%', 'targets':5},
            {'title':'下载','width':'8%', 'targets':6}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            if(aData[6] !== '--'){
                tableTdDownload(6, nRow, aData[6]);
            }
        }
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