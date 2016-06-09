//@ sourceURL=appManager.blackRelease.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/App/publishBlacklistAppLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.releaseId = obj.data('id');
        myData.desktopId = obj.data('desktopId');
        myData.groupId = obj.data('groupId');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-align-justify') != -1){
            AjaxGet('/App/publishBlacklistAppLists?id='+ myData.releaseId, function(data){
                createBlack(data);
                trHover('#blackTable');
                $('#blackModal').modal('show');
            });
        }
    });

    listenMyPage();
});

listenToolbar('release', releaseTableInfo);
listenToolbar('under', underTableInfo);

function releaseTableInfo() {
    $('#desktopId').val('');
    AjaxGet('/App/blacklistLists', function(data){
        selectGroup(data);
        $('#releaseModal h4').text('发布');
        $('#releaseModal').modal('show');
    });
}

function underTableInfo() {
    if (myData.releaseId) {
        if (confirm('确定下架？')) {
        	var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/App/deletePublishBlacklistApp?id=' + myData.releaseId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/App/publishBlacklistAppLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.releaseId = null;
    });
}

//创建组下拉框
function selectGroup(data, groupId) {
    var arr = data.extra;
    var con = '<option value="请选择组">请选择组</option>';
    var $select = $('#groupSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }
    if(groupId){
        $select.html(con).val(groupId).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
    }else{
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
    }
}

$('#subRelease').on('click', function(){
	var desktopId = $('#desktopId').val();
	var groupSelect = $('#groupSelect').val();
	var filter = $('#myTable_filter input').val() || '';

	if(groupSelect == '请选择组' || !groupSelect){
		alert('请选择组');
		return false;
	}
	if(desktopId == ' ' || !desktopId){
		alert('请输入桌面ID');
		return false;
	}

	var data = {
		"blacklistId": groupSelect,
		"desktopid": desktopId
	};

	AjaxPost('/App/addPublishBlacklistApp', data, function(){
		$('#releaseModal').modal('hide');
        updateTable(currentPage, filter);
	});
});

//创建桌面列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.desktopid + '--' + arr.id, arr.blacklistId]);
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
            [0, "desc"]
        ],
        "columnDefs": [
        {
            'title': '桌面ID',
            'width': '16%',
            'targets': 0
        },{
            'title': '黑名单列表',
            'width': '16%',
            'targets': 1
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            var temp = aData[0].split('--');
            tableTdIcon(1, nRow, 'align-justify');
            $('td:eq(0)', nRow).html(temp[0]);
            $('td:eq(0)', nRow).data({
                "id": temp[1],
                "desktopId": temp[0],
                "groupId": aData[1],
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable', [
        myConfig.releaseBtn,
        myConfig.underBtn
    ]);
}

//创建更多应用黑名单列表
function createBlack(data) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.appName, arr.pkgName]);
    }
    myDataTable('#blackTable', {
        "data": dataArr,
        "order": [
            [0, "desc"]
        ],
        "columnDefs": [
        {
            'title': '应用名称',
            'width': '16%',
            'targets': 0
        },
        {
            'title': '包名',
            'width': '16%',
            'targets': 1
        }]
    });
}