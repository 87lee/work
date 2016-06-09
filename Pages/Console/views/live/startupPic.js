//@ sourceURL=live.startupPic.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/Live/liveStartupPicLists?page=1&pageSize=' + pageSize, function(data) {
        createStartup(data, 1);
        trHover('#startupTable');
    });

    trclick('#startupTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.type = obj.data('type');
        myData.groupId = obj.data('sn');
        myData.info = obj.data('info');
        myData.AB = obj.data('AB');
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
    });

    listenchoose();
    listenfile();
    listenPic('#startupTable');
    listenMyPage('startupTable', currentPage);
});

listenToolbar('under', underTableInfo, '#startupTable');
listenToolbar('release', releaseTableInfo, '#startupTable');
listenToolbar('group', releaseInfo, '#startupTable');
listenToolbar('AB', releaseInfo, '#startupTable');
listenToolbar('ALL', releaseInfo, '#startupTable');
listenToolbar('watch', watchInfo, '#startupTable');
// listenToolbar('watch', watchInfo, '#startupTable');

function underTableInfo() {
    if (myData.ReleaseId) {
        if (confirm('确定下架？')) {
            var filter = $('#startupTable_filter input').val() || '';
            AjaxPost('/Live/deleteLiveStartupPic?id', [myData.ReleaseId], function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择广告！');
    }
}

function watchInfo() {
    if (myData.ReleaseId) {
        for (var i = 0; i < $('tbody tr').length; i++) {
            if ($('tbody tr:eq('+i+') td:eq(0)').data('id') === myData.ReleaseId){
                $('#infoWhite').val($('tbody tr:eq('+i+') td:eq(0)').data('whiteList'));
                $('#infoBlack').val($('tbody tr:eq('+i+') td:eq(0)').data('blackList'));
            }
        }
        $('#infoModal').modal('show');
    } else {
        alert('请选择开机画面！');
    }
}

function releaseTableInfo(){
    AjaxGet('/group/nameLists', function(data){
        selectGroup(data);
        $('#modelName').val('');
        $('#vendorID').val('');
        $('#startName').val('');
        $('#showTime').val('');
        $('#startShow').val('');
        $('#startHide').val('');
        $('#startBlack').val('');
        $('#startWhite').val('');

        $('#chooseModel input:eq(1)').trigger('click');
        $('#chooseType input:eq(0)').trigger('click');
        $('#chooseSkip input:eq(0)').trigger('click');
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
        var filter = $('#startupTable_filter input').val() || '';
        AjaxPost('/Live/modifyLiveStartupPic', {"id": myData.ReleaseId, "type": "ALL"}, function(){
            updateTable(currentPage, filter);
            $('#editModal').modal('hide');
        });
        return;
    }
    $('#editModal').modal('show');
}

function updateTable(page, name){
    AjaxGet('/Live/liveStartupPicLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createStartup(data, page);
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
    var startName = $('#startName').val() || '';
    var isSkip = $('#chooseSkip input:checked').val();
    var showTime = $('#showTime').val() || '';
    var fileObj = document.getElementById("startHide").files[0];
    var fileVal = $("#startShow").val();
    var startWhite = $('#startWhite').val();
    var startBlack = $('#startBlack').val();

    var filter = $('#startupTable_filter input').val() || '';
    var data = new FormData();


    if(modelType === 'All'){
        modelName = modelType;
    }else{
        if(modelName == '请输入型号' || !modelName){
            alert('请输入型号');
            return false;
        }
    }
    data.append('model', modelName);
    data.append('name', startName);

    if (fileVal == ' ' || !fileVal) {
        alert('请选择要上传的图片');
        return false;
    }
    if(fileVal != ' ' && fileVal.indexOf('http') == -1 && fileVal) {
        data.append("img", fileObj);
    }
    data.append('isSkip', isSkip);

    if(showTime == ' ' || !showTime){
        alert('请输入展示次数');
        return false;
    }
    if (startWhite != '' && /(\d+-\d+;|\d+;)*(\d+-\d+|\d+)$/.test(startWhite) === false) {
        alert('请输入正确的白名单');
        return false;
    }
    if (startBlack != '' && /(\d+-\d+;|\d+;)*(\d+-\d+|\d+)$/.test(startBlack) === false) {
        alert('请输入正确的黑名单');
        return false;
    }
    if(/\D/.test(showTime)){
        alert('展示次数只能为数字');
        return false;
    }
    data.append('showTime', showTime);
    data.append('vendorID', vendorId);
    data.append('type', type);
    data.append('blackList', startBlack);
    data.append('whiteList', startWhite);

    if(type == 'group') {
        var group = $('#group').val();
        data.append('groupId', group);
    } else if (type == 'AB') {
        var countNum = $('#countNum').val();
        if(countNum == ' ' || !countNum){
            alert('请输入灰度数量');
            return;
        }else if(/\D/.test(countNum)){
            alert('灰度数量只能为数字');
            return;
        }
        data.append('AB', countNum);
    }
    AjaxFile('/Live/addLiveStartupPic', data, function(){
        updateTable(currentPage, filter);
        $('#releaseModal').modal('hide');
    });
});

$('#subEdit').on('click', function(){
    var title = $('#editModal h4').text();

    var filter = $('#startupTable_filter input').val() || '';
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

    AjaxPost('/Live/modifyLiveStartupPic', data, function(){
        updateTable(currentPage, filter);
        $('#editModal').modal('hide');
    });
});

//创建发布列表
function createStartup(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.model + '-' + arr.id, arr.vendorID, arr.name, arr.type, arr.groupId || '--', arr.AB || '--', arr.isSkip, arr.showTime, arr.url, formatDate(arr.version), arr.blackList, arr.whiteList]);
    }
    $('#startupTable').dataTable({
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
            [9, "desc"]
        ],
        "columnDefs": [
            {'title':'型号','width':'8%', 'targets':0},
            {'title':'vendorID','width':'10%', 'targets':1},
            {'title':'名称','width':'10%', 'targets':2},
            {'title':'类型','width':'7%', 'targets':3},
            {'title':'设备列表','width':'7%', 'targets':4},
            {'title':'灰度','width':'7%', 'targets':5},
            {'title':'是否跳过','width':'7%', 'targets':6},
            {'title':'展示次数','width':'7%', 'targets':7},
            {'title':'图片','width':'7%', 'targets':8},
            {'title':'发布时间','width':'8%', 'targets':9}
        ],
        "createdRow": function( nRow, aData, idx ){
            var temp = aData[0].split('-');
            $('td:eq(0)', nRow).html(temp[0]);
            tableTypeColor(3, nRow, aData[3]);
            if(aData[3] == 'group'){
                tableTdIcon(4, nRow, 'list sn-list');
            }else{
                tableTdNull(4, nRow);
            }
            if(aData[6] == 'false'){
                $('td:eq(6)', nRow).html('否');
            }else{
                $('td:eq(6)', nRow).html('是');
            }
            if (aData[8] !== '--') {
                $('td:eq(8)', nRow).html('<i class="glyphicon glyphicon-picture icon-black my-icon" data-src="' + aData[8] + '"></i>').addClass('center');
            }else{
                tableTdNull(8, nRow);
            }

            $('td:eq(0)', nRow).data({
                "id": temp[1],
                "type": aData[3],
                "sn": aData[4],
                "AB": aData[5],
                "whiteList": aData[11],
                "blackList": aData[10]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'startupTable');
    initToolBar('#startupTable', [
        myConfig.releaseBtn,
        myConfig.underBtn,
        '<a class="btn my-btn btn-primary groupBtn" href="javascript:">&nbsp;内测</a>',
        '<a class="btn my-btn btn-primary ABBtn" href="javascript:">&nbsp;灰度</a>',
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