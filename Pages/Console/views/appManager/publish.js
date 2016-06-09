//@ sourceURL=appManager.publish.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/App/appWhiteBlackLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.ReleaseId = obj.data('id');
        myData.type = obj.data('type');
        myData.snId = obj.data('sn');
        myData.whiteList = obj.data('white');
        myData.blackList = obj.data('black');

        $('.groupBtn').attr('disabled', false);
        $('.ALLBtn').attr('disabled', false);

        if(myData.type === 'ALL'){
            $('.ALLBtn').attr('disabled', true);
            $('.groupBtn').attr('disabled', true);
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
        if(tar.className.indexOf('glyphicon-list white-list') != -1){
            createInfo(myData.whiteList);
            trHover('#infoTable');
            $('#infoModal h4').text('白名单列表');
            $('#infoModal').modal('show');
            return;
        }
        if(tar.className.indexOf('glyphicon-list black-list') != -1){
            createInfo(myData.blackList);
            trHover('#infoTable');
            $('#infoModal h4').text('黑名单列表');
            $('#infoModal').modal('show');
            return;
        }
    });

    listenchoose();
    listenMyPage();
});

listenToolbar('under', underTableInfo);
listenToolbar('release', releaseTableInfo);
listenToolbar('group', releaseInfo);
listenToolbar('ALL', releaseInfo);

function underTableInfo() {
    if (myData.ReleaseId) {
        if (confirm('确定下架？')) {
            var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/App/deleteAppWhiteBlack?id=' + myData.ReleaseId, function() {
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
        AjaxGet('/app/appGroupLists', function(appData){
            selectApp(appData, ['#whiteGroup', '#blackGroup']);
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
    $('.edit-type').hide();
    $('#editModal h4').text(str);

    AjaxGet('/app/appGroupLists', function(appData){
        selectApp(appData, ['#customWhite', '#customBlack']);
        if(str === '内测'){
            $('#customGroup').parent().show();
            AjaxGet('/group/nameLists', function(data){
                selectGroup(data, $('#customGroup'));
                $('#customGroup').val(myData.snId);
                $('#editModal').modal('show');
            });
        }else if(str === '公开'){
            $('#editModal').modal('show');
        }
    });
}

function updateTable(page, name){
    AjaxGet('/App/appWhiteBlackLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
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
function selectApp(data, aID){
    var arr = data.extra;
    var con = '<option value="请选择应用组">请选择应用组</option>';
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].name+'</option>';
    }
    for(var j = aID.length; j--;){
        var $select = $(aID[j]);
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change');
    }
}

$('#whiteGroup').on('change', appGroupChange);
$('#blackGroup').on('change', appGroupChange);
$('#customWhite').on('change', appGroupChange);
$('#customBlack').on('change', appGroupChange);

function appGroupChange(){
    var $this = $(this);
    var val = $this.val();
    if(val !== '请选择应用组'){
    	AjaxGet('/app/appGroupMemberLists?groupId=' + val, function(data){
    		$this.data('extra', data.extra);
    	});
    }else{
        $this.data('extra', []);
    }
    return false;
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
    var customGroup = $('#customGroup').val();
    var $white = $('#customWhite');
    var $black = $('#customBlack');
    var filter = $('#myTable_filter input').val() || '';
    var data = {};

    if(title === '内测'){
        data.type = 'group';
        data.groupId = customGroup;
    }else if(title === '公开'){
        data.type = 'ALL';
    }

    $.extend(data, {
        "id": myData.ReleaseId,
        "whiteList": $white.data('extra') || [],
        "blackList": $black.data('extra') || []
    });

    AjaxPost('/App/modifyAppWhiteBlack', data, function(){
        updateTable(currentPage, filter);
        $('#editModal').modal('hide');
    });
});

$('#subRelease').on('click', function(){
    var type = $('#chooseType input:checked').val();
    var modelType = $('#chooseModel input:checked').val();
    var modelName = $('#modelName').val();
    var vendorId = $('#vendorID').val() || 'none';
    var $white = $('#whiteGroup');
    var $black = $('#blackGroup');

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
        "model": modelName,
        "vendorID": vendorId,
        "type": type,
        "whiteList": $white.data('extra') || [],
        "blackList": $black.data('extra') || []
    });

    AjaxPost('/App/publishAppWhiteBlack', data, function(){
        updateTable(currentPage, filter);
        $('#releaseModal').modal('hide');
    });
});

//创建发布列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.model, arr.vendorID, arr.type, arr.groupId || '--', arr.whiteList, arr.blackList, arr.time]);
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
            [7, "desc"]
        ],
        "columnDefs": [
            {'title':'ID','width':'5%', 'targets':0},
            {'title':'型号','width':'10%', 'targets':1},
            {'title':'vendorID','width':'8%', 'targets':2},
            {'title':'类型','width':'5%', 'targets':3},
            {'title':'设备列表','width':'5%', 'targets':4},
            {'title':'白名单列表','width':'5%', 'targets':5},
            {'title':'黑名单列表','width':'5%', 'targets':6},
            {'title':'发布时间','width':'8%', 'targets':7}
        ],
        "createdRow": function( nRow, aData, idx ){
            tableTypeColor(3, nRow, aData[3]);
            if(aData[3] == 'group'){
                tableTdIcon(4, nRow, 'list sn-list');
            }else{
                tableTdNull(4, nRow);
            }
            if(aData[5].length){
                tableTdIcon(5, nRow, 'list white-list');
            }else{
                tableTdNull(5, nRow);
            }
            if(aData[6].length){
                tableTdIcon(6, nRow, 'list black-list');
            }else{
                tableTdNull(6, nRow);
            }

            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "type": aData[3],
                "sn": aData[4],
                "white": aData[5],
                "black": aData[6]
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
        '<a class="btn my-btn btn-primary ALLBtn" href="javascript:">&nbsp;公开</a>'
    ]);
}

function createInfo(data){
    var dataArr = [];
    var len = data.length;
    for (var i = 0; i < len; i++) {
        var arr = data[i];
        dataArr.push([arr.appName, arr.pkgName]);
    }
    myDataTable('#infoTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'应用名称','width':'50%', 'targets':0},
            {'title':'应用包名','width':'50%', 'targets':1}
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