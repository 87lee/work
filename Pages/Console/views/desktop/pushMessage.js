//@ sourceURL=desktop.pushMessage.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
    myData.checkedLists = [];   //存储check选中项
    var name='';
	AjaxGet('/desktop/pushMessageLists?name=' + name + '&page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    listenSingleCheckBox('#myTable');

    listenMyPage();
});

listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function addTableInfo() {
    console.log(myData);
    $('#pushId').val('');
    $('#pushId').val('');
    $('#vendorId').val('');
    $('#model').val('');
    $('#pushModal h4').text('新增');    
    clearTableInfo();
    if(myData.model){
        $('#pushId').val(myData.pushId);
        $('#chooseType input:eq(1)').trigger('click');
    }
    AjaxGet('/group/nameLists', function(data){
        selectGroup(data);
        $('#pushModal').modal('show');
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



function delTableInfo() {
    console.log(myData)
    if (myData.checkedLists.length > 0) {
        if (confirm('确定删除？')) {
        	var filter = $('#myTable_filter input').val() || '';
            var data = {"desktopMaps": myData.checkedLists};
            AjaxPost('/desktop/deleteDesktopMap', data, function() {
                myData.checkedLists = [];
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function clearTableInfo(){
    $('#pushId').val('');
    $('#vendorId').val('');
    $('#chooseType input:eq(0)').trigger('click');
    $('#chooseRange input:eq(0)').trigger('click');
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/desktop/pushMessageLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.id = null;
    });
}

$('#subPush').click(function(){
    var type = $('#chooseType input:checked').val();
    var range = $('#chooseRange input:checked').val();
    var pushID = $('#pushId').val();
    var playTime = $('#playTime').val();
    var playCount = $('#playCount').val();
    var msg = $('#msg').val();
    var vendorId = $('#vendorId').val() || 'none';

    var versionWhite = $('#versionWhite').val().split(';');
    var versionBlack = $('#versionBlack').val().split(';');
    var filter = $('#myTable_filter input').val() || '';

    var data = {};

    if(pushID == ' ' || !pushID){
        alert('请输入型号');
        return false;
    }

    if(type === 'group'){
        var groupId = $('#group').val();
        data.groupId = groupId;
    }


    data.type = type;
    data.model = pushID;
    data.vendorID = vendorId;
    data.playCount = playCount;
    data.playTime = playTime;
    data.msg = msg;
    data.pub_range = range;
    data.whiteList = filterBlankLine(versionWhite);
    data.blackList = filterBlankLine(versionBlack);
    //console.log(data);
    AjaxPost('/desktop/addPushMessage', data, function(){
        updateTable(currentPage, filter);
        $('#pushModal').modal('hide');
    });
});

//创建消息推送列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        console.log(arr.whiteList[0]);
        dataArr.push([arr.id, arr.model, arr.type, formatDate(arr.version), arr.pub_range, arr.msg, arr.groupId || '--', arr.vendorID, arr.playTime, arr.playCount]);
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
        "data": dataArr,
        "order": [
            [1, "desc"]
        ],
        "columnDefs": [
        {
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '1%',
            'targets': 0,
            "orderable": false
        },{
            'title': '型号',
            'width': '9%',
            'targets': 1
        },{
            'title': '类型',
            'width': '9%',
            'targets': 2
        },{
            'title': '资源包版本',
            'width': '9%',
            'targets': 3
        },{
            'title': '发布范围',
            'width': '9%',
            'targets': 4
        },{
            'title': '消息内容',
            'width': '26%',
            'targets': 5
        },{
            'title': '内测组ID',
            'width': '8%',
            'targets': 6
        },{
            'title': 'vendorID',
            'width': '8%',
            'targets': 7
        },{
            'title': '播放时长',
            'width': '8%',
            'targets': 8
        },{
            'title': '播放次数',
            'width': '8%',
            'targets': 9
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "model": aData[1],
                "type": aData[2],
                "version": aData[3],
                "pub_range": aData[4],
                "msg": aData[5],
                "groupId": aData[6],
                "vendorID": aData[7],
                "playTime": aData[8],
                "playCount": aData[9]
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable',[myConfig.addBtn,myConfig.delBtn]);

    listenCheckBox();
    updateChecked();
}