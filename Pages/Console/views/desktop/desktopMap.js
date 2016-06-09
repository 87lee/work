//@ sourceURL=desktop.desktopMap.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
    myData.checkedLists = [];   //存储check选中项
	AjaxGet('/desktop/desktopMapLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    listenSingleCheckBox('#myTable');

    listenMyPage();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function addTableInfo() {
    $('#mapDesktopId').val('');
    $('#mapId').val('');
    $('#vendorId').val('');
    $('#model').val('');
    $('#mapModal h4').text('添加');
    $('#mapModal').modal('show');
}

function editTableInfo() {
	if (myData.checkedLists.length === 1) {
        var obj = $('.checkSelected td:eq(0)');
        myData.mapDesktopId = obj.data('mapDesktopId');
        myData.mapId = obj.data('mapId');
        myData.id = obj.data('id');
        myData.mapVendorId = obj.data('vendorId');
        myData.mapModel = obj.data('model');
		$('#mapDesktopId').val(myData.mapDesktopId);
	    $('#mapId').val(myData.mapId);
        $('#vendorId').val(myData.mapVendorId);
        $('#model').val(myData.mapModel);
	    $('#mapModal h4').text('修改');
	    $('#mapModal').modal('show');
	} else {
		alert('请只选择一个内容！');
	}
}

function delTableInfo() {
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

function updateTable(page, name){
    name = name || '';
    AjaxGet('/desktop/desktopMapLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.id = null;
    });
}

$('#subMap').on('click', function(){
	var desktop2 = $('#mapDesktopId').val() || '';
	var desktop3 = $('#mapId').val();
    var vendorID = $('#vendorId').val() || '';
    var model = $('#model').val() || '';
	var title = $('#mapModal h4').text();
	var filter = $('#myTable_filter input').val() || '';

	if(desktop3 == ' ' || !desktop3){
		alert('请输入映射ID');
		return false;
	}

	var data = {
		"desktop2": desktop2,
		"desktop3": desktop3,
        "vendorID": vendorID,
        "model": model
	};

	if(title === '添加'){
		AjaxPost('/desktop/addDesktopMap', data, function(){
			$('#mapModal').modal('hide');
            updateTable(currentPage, filter);
		});
	}else if(title === '修改'){
		data.id = myData.id;
		AjaxPost('/desktop/modifyDesktopMap', data, function(){
			$('#mapModal').modal('hide');
            updateTable(currentPage, filter);
		});
	}
});

//创建桌面列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.desktop2, arr.model, arr.vendorID, arr.desktop3, arr.createTime]);
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
            [4, "desc"]
        ],
        "columnDefs": [
        {
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '8%',
            'targets': 0,
            "orderable": false
        },{
            'title': '桌面ID',
            'width': '19%',
            'targets': 1
        },{
            'title': '型号',
            'width': '19%',
            'targets': 2
        },{
            'title': 'vendorID',
            'width': '19%',
            'targets': 3
        },{
            'title': '映射桌面ID',
            'width': '19%',
            'targets': 4
        },{
            'title': '创建时间',
            'width': '16%',
            'targets': 5
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            if(aData[1] === '' || !aData[1]){
                $('td:eq(1)', nRow).html('--');
            }
            if(aData[2] === '' || !aData[2]){
                $('td:eq(2)', nRow).html('--');
            }
            if(aData[3] === '' || !aData[3]){
                $('td:eq(3)', nRow).html('--');
            }
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "mapDesktopId": aData[1],
                "model": aData[2],
                "mapId": aData[4],
                "vendorId": aData[3]
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable');

    listenCheckBox();
    updateChecked();
}