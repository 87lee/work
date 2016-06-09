//@ sourceURL=appUpdate.publish.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/App/publishAppUpdateVersionLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.info = obj.data('info');
        myData.type = obj.data('type');
        myData.snId = obj.data('sn');
        myData.AB = obj.data('AB');

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
listenToolbar('release', releaseTableInfo);
listenToolbar('group', releaseInfo);
listenToolbar('AB', releaseInfo);
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

function releaseTableInfo(){
    AjaxWhen([
        AjaxGet('/group/nameLists', selectGroup, true),
        AjaxGet('/App/appUpdateLists', selectApp, true)
    ], function(){
        $('#modelName').val('');
        $('#vendorID').val('');
        $('#miniForceUpdateVersion').val('');
        $('#miniUpdateVersion').val('');
        $('#countNum').val('');
        $('#versionDesc').val('');
        $('#versionWhite').val('');
        $('#versionBlack').val('');
        $('#chooseModel input:eq(1)').trigger('click');
        $('#chooseUmeng input:eq(1)').trigger('click');
        $('#showTips input:eq(1)').trigger('click');
        $('#chooseFake input:eq(0)').trigger('click');
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
    $('.edit-type').hide();
    $('#editModal h4').text(str);

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

    if(str === '内测'){
        $('#customGroup').parent().show();
        AjaxGet('/group/nameLists', function(data){
            selectGroup(data, $('#customGroup'));
            $('#customGroup').val(myData.snId);
            $('#editModal').modal('show');
        });
    }else if(str === '灰度'){
        $('#customCountNum').val(myData.AB == '--' ? '' : myData.AB).parent().show();
        $('#editModal').modal('show');
    }else if(str === '公开'){
        $('#editModal').modal('show');
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
    $('#infoForced').val(myData.info.miniForceUpdateVersion);
    $('#infoPrompt').val(myData.info.miniUpdateVersion);
    $('#infoUmeng').val(getTrueOrFalse(myData.info.umeng));
    $('#infoShowTips').val(getTrueOrFalse(myData.info.showTips));
    $('#infoFake').val(getTrueOrFalse(myData.info.fake));
    $('#infoModal').modal('show');
}

function updateTable(page, name){
    AjaxGet('/App/publishAppUpdateVersionLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
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

//创建应用列表下拉框
function selectApp(data){
    var arr = data.extra;
    var con = '<option value="请选择应用名称">请选择应用名称</option>';
    var $select = $('#appName');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].appName+'</option>';
        $select.data('_'+arr[i].id, {
            "appName": arr[i].appName,
            "pkgName": arr[i].pkgName,
            "channel": arr[i].channel
        });
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    }).trigger('change');
}

$('#appName').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    if(val === '请选择应用名称'){
        $('#versionCode').html('<option value="请选择版本">请选择版本</option>').trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change');
        return false;
    }
    AjaxGet('/App/appUpdateLists?id=' + val, selectVersionCode);
});

function selectVersionCode(data){
    var arr = descSort(data.extra, 'versionCode');
    var con = '<option value="请选择版本">请选择版本</option>';
    var $select = $('#versionCode');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].versionName+'</option>';
        $select.data('_'+arr[i].id, {
            "versionCode": arr[i].versionCode,
            "versionName": arr[i].versionName,
            "path": arr[i].path,
            "md5": arr[i].md5
        });
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
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

$('#subRelease').on('click', function(){
    var type = $('#chooseType input:checked').val();
    var umengType = $('#chooseUmeng input:checked').val();
    var showTipsType = 'true' || $('#showTips input:checked').val();
    var fakeType = $('#chooseFake input:checked').val();
    var modelType = $('#chooseModel input:checked').val();
    var modelName = $('#modelName').val();
    var vendorId = $('#vendorID').val() || 'none';
    var versionCode = $('#versionCode').val();
    var appName = $('#appName').val();

    var versionDesc = $('#versionDesc').val().split(';');
    var versionWhite = $('#versionWhite').val().split(';');
    var versionBlack = $('#versionBlack').val().split(';');

    var miniForceUpdateVersion = $('#miniForceUpdateVersion').val();
    var miniUpdateVersion = $('#miniUpdateVersion').val();

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
    if(vendorId == '请选择vendorID' || !vendorId){
        alert('请选择vendorID');
        return false;
    }
    if(appName == '请选择应用名称' || !appName){
        alert('请选择应用名称');
        return false;
    }
    if(versionCode == '请选择版本' || !versionCode){
        alert('请选择版本');
        return false;
    }
    if(miniForceUpdateVersion == '请输入强制版本' || !miniForceUpdateVersion){
        alert('请输入强制版本');
        return false;
    }else if(/\D/.test(miniForceUpdateVersion)){
        alert('强制版本只能为数字');
        return false;
    }
    if(miniUpdateVersion == '请输入提示版本' || !miniUpdateVersion){
        alert('请输入提示版本');
        return false;
    }else if(/\D/.test(miniUpdateVersion)){
        alert('提示版本只能为数字');
        return false;
    }
    if(Number(miniUpdateVersion) <= Number(miniForceUpdateVersion)){
        alert('提示版本要大于强制版本');
        return false;
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

    if(type !== 'group' && fakeType === 'true'){
        alert('灰度与公开不能选择假包！');
        return;
    }
    var appData = $('#appName').data('_' + appName);
    var versionData = $('#versionCode').data('_' + versionCode);
    $.extend(data, {
        "appName": appData.appName,
        "pkgName": appData.pkgName,
        "channel": appData.channel,
        "versionCode": versionData.versionCode,
        "path": versionData.path,
        "versionName": versionData.versionName,
        "md5": versionData.md5,
        "model": modelName,
        "vendorid": vendorId,
        "type": type,
        "desc": filterBlankLine(versionDesc),
        "whiteList": filterBlankLine(versionWhite),
        "blackList": filterBlankLine(versionBlack),
        "umeng": umengType,
        "showTips": showTipsType,
        "fake": fakeType,
        "miniForceUpdateVersion": miniForceUpdateVersion,
        "miniUpdateVersion": miniUpdateVersion
    });

    AjaxPost('/App/publishAppUpdateVersion', data, function(){
        updateTable(currentPage, filter);
        $('#releaseModal').modal('hide');
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
            {'title':'型号','width':'8%', 'targets':5},
            {'title':'vendorID','width':'8%', 'targets':6},
            {'title':'类型','width':'5%', 'targets':7},
            {'title':'灰度','width':'5%', 'targets':8},
            {'title':'设备列表','width':'5%', 'targets':9},
            {'title':'下载','width':'5%', 'targets':10},
            {'title':'发布时间','width':'10%', 'targets':11}
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