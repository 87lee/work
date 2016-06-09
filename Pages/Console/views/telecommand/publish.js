//@ sourceURL=telecommand.publish.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
$(function () {
	AjaxGet('/desktop/cmdLists?page=1&pageSize=' + pageSize, function(data){
        createElem(data, 1);
        trHover('#cmdTable');
    });

    trclick('#cmdTable', function(obj, e) {
        myData.cmdId = obj.data('id');
        myData.cmdList = obj.data('cmd');
        myData.cmdDesktopId = obj.data('desktopId');
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

        if(tar.className.indexOf('glyphicon-list cmd-list') != -1){
            $('#cmdListTable').val(myData.cmdList.join('\r\n'));
            $('#cmdListModal').modal('show');
            return;
        }
    });

    listenchoose();

    listenMyPage('cmdTable', currentPage);
});

listenToolbar('release', releaseTableInfo, '#cmdTable');
listenToolbar('under', underTableInfo, '#cmdTable');
listenToolbar('watch', watchTable, '#cmdTable');

function releaseTableInfo(){
    clearTableInfo();
    if(myData.cmdId){
        $('#desktopId').val(myData.cmdDesktopId);
        $('#chooseType input:eq(1)').trigger('click');
        $('#cmdTages').val(myData.cmdList.join('\r\n'));
    }
    AjaxGet('/group/nameLists', function(data){
        selectGroup(data);
        AjaxGet('/desktop/cmdLineLists', function(cmdData){
            selectCmd(cmdData);
            $('#cmdModal').modal('show');
        });
    });
}

function underTableInfo(){
    if(myData.cmdId){
        if (confirm('确定下架？')) {
            var filter = $('#cmdTable_filter input').val() || '';
            AjaxGet('/desktop/deleteCmd?id=' + myData.cmdId, function(){
                updateTable(currentPage, filter);
            });
        }
    }else{
        alert('请选择内容！');
        return false;
    }
}

function watchTable(){
    if (!myData.cmdId) {
        alert('请选择内容！');
        return false;
    }
    $('#infoWhite').val(myData.info.white.join(';'));
    $('#infoBlack').val(myData.info.black.join(';'));
    $('#infoModal').modal('show');
}

function clearTableInfo(){
    $('#desktopId').val('');
    $('#cmdTages').val('');
    $('#vendorId').val('');
    $('#chooseType input:eq(0)').trigger('click');
    $('#chooseRange input:eq(0)').trigger('click');
}

function updateTable(page, name){
    AjaxGet('/desktop/cmdLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.cmdId = null;
    });
}

function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    var range = {"all": "全部", "jbk": "越狱后", "unjbk": "越狱前"};
    var info = [];
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.model, arr.vendorID, arr.type, arr.pub_range, arr.groupId || '--', formatDate(arr.version), arr.cmd]);
        info.push({
            "white": arr.whiteList || [],
            "black": arr.blackList || []
        });
    }
    $('#cmdTable').dataTable({
        "lengthChange": false,
        "autoWidth": false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "data": dataArr,
        "order": [[6, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'8%', 'targets':0},
            {'title':'型号','width':'10%', 'targets':1},
            {'title':'vendorID','width':'10%', 'targets':2},
            {'title':'类型','width':'8%', 'targets':3},
            {'title':'发布范围','width':'8%', 'targets':4},
            {'title':'设备列表','width':'10%', 'targets':5},
            {'title':'版本','width':'15%', 'targets':6},
            {'title':'命令列表','width':'10%', 'targets':7}
        ],
        "createdRow": function( nRow, aData, idx ){
        	tableTypeColor(3, nRow, aData[3]);
            if(aData[3] == 'group'){
                tableTdIcon(5, nRow, 'list sn-list');
            }else{
                tableTdNull(5, nRow);
            }
            $('td:eq(4)', nRow).html(range[aData[4]]);
            tableTdIcon(7, nRow, 'list cmd-list');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "desktopId": aData[1],
                "cmd": aData[7],
                "sn": aData[5],
                "info": info[idx]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'cmdTable');
    initToolBar('#cmdTable', [myConfig.releaseBtn, myConfig.underBtn, '<a class="btn my-btn btn-primary watchBtn" href="javascript:">&nbsp;查看</a>']);
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

function createCmd(data){
    var dataArr = [];
    var len = data.length || 0;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr]);
    }
    myDataTable('#cmdListTable', {
        "data": dataArr,
        "pageLength": 10,
        "columnDefs": [
            {'title':'命令', 'width':'100%', 'targets':0},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).css({
                "word-break": "break-all"
            });
        }
    });
}

//创建内测包下拉框
function selectGroup(data){
    var arr = data.groups;
    var con = '';
    var $select = $('#group');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
    }
    $select.html(con);
}

//内测与公开变化事件
$('#chooseType > input').on('click', function(){
    var $this = $(this);
    $this.prop('checked', true);
    var val = $this.val();
    if(val === 'group'){
        $('#group').parent().show();
    }else if(val === 'ALL'){
        $('#group').parent().hide();
    }
});

function selectCmd(data){
    var arr = data.extra;
    var con = '<option value="请选择命令">请选择命令</option>';
    var $select = $('#cmdGroup');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].id+'">'+arr[i].name+'</option>';
        $select.data('_'+arr[i].id, arr[i].cmd);
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}

$('#cmdGroup').on('change', function(){
    var $this = $(this);
    var id = $this.val();
    if(id === '请选择命令'){
        $('#cmdTages').val('');
        return false;
    }
    var val = $this.data('_'+id);
    $('#cmdTages').val(val.join('\r\n'));
});

$('#subCmd').click(function(){
	var type = $('#chooseType input:checked').val();
    var range = $('#chooseRange input:checked').val();
    var desktopID = $('#desktopId').val();
    var vendorId = $('#vendorId').val() || 'none';
    var cmdTages = $('#cmdTages').val();

    var versionWhite = $('#versionWhite').val().split(';');
    var versionBlack = $('#versionBlack').val().split(';');
    var filter = $('#cmdTable_filter input').val() || '';

    var data = {};

    if(desktopID == ' ' || !desktopID){
        alert('请输入型号');
        return false;
    }

    if(type === 'group'){
        var groupId = $('#group').val();
        data.groupId = groupId;
    }

    if(cmdTages == ' ' || !cmdTages){
        alert('请输入命令');
        return false;
    }
    var cmd = cmdTages.split(/[\r\n]/);
    var cmdNow = [];

    for(var i = 0, len = cmd.length; i < len; i++){
        var val = cmd[i].trim();
        if(val == ' ' || !val){
            continue;
        }
        cmdNow.push(val);
    }

    data.type = type;
    data.model = desktopID;
    data.vendorID = vendorId;
    data.cmd = cmdNow;
    data.pub_range = range;
    data.whiteList = filterBlankLine(versionWhite);
    data.blackList = filterBlankLine(versionBlack);

    AjaxPost('/desktop/addCmd', data, function(){
        updateTable(currentPage, filter);
        $('#cmdModal').modal('hide');
    });
});