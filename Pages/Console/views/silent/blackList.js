//@ sourceURL=silent.blackList.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/Silent/blackListLists?page=1&pageSize='+pageSize, function(data) {
        createElem(data, 1);
        trHover('#blackTable');
    });

    trclick('#blackTable', function(obj, e) {
        myData.blackId = obj.data('id');
        myData.modelName = obj.data('model');
        myData.vendorId = obj.data('vendorId');
    });

    listenMyPage('blackTable', currentPage);
});

listenToolbar('add', addTableInfo, '#blackTable');
listenToolbar('del', delTableInfo, '#blackTable');
listenToolbar('edit', editTableInfo, '#blackTable');

function addTableInfo(){
	$('#modelName').val('');
    $('#vendorId').val('');
    $('#blackModal h4').text('添加');
    $('#blackModal').modal('show');
}

function editTableInfo(){
    if (myData.blackId) {
        $('#modelName').val(myData.modelName);
        $('#vendorId').val(myData.vendorId);
        $('#blackModal h4').text('修改');
        $('#blackModal').modal('show');
    } else {
        alert('请选择黑名单！');
    }
}

function delTableInfo(){
	if (myData.blackId) {
        if (confirm('确定删除？')) {
            var filter = $('#blackTable_filter input').val() || '';
            AjaxGet('/Silent/deleteBlackList?id=' + myData.blackId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择黑名单！');
    }
}

function updateTable(page, name){
    name = name || '';
	AjaxGet('/Silent/blackListLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.blackId = null;
    });
}

$('#subBlack').on('click', function(){
    var modelName = $('#modelName').val();
    var vendorId = $('#vendorId').val() || 'none';
    var title = $('#blackModal h4').text();
    var filter = $('#blackTable_filter input').val() || '';
    var data = {};

    if(modelName == ' ' || !modelName){
        alert('请输入型号');
        return false;
    }
    data = {"model": modelName, "vendorID": vendorId};

    if(title === '添加'){
    	AjaxPost('/Silent/addBlackList', data, function(){
            $('#blackModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        data.id = myData.blackId;
        AjaxPost('/Silent/modifyBlackList', data, function(){
            $('#blackModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

//创建应用组
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.model, arr.vendorID]);
    }
    $('#blackTable').dataTable({
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
            'title': 'ID',
            'width': '25%',
            'targets': 0
        },{
            'title': '型号',
            'width': '40%',
            'targets': 1
        },{
            'title': 'vendorID',
            'width': '35%',
            'targets': 2
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "model": aData[1],
                "vendorId": aData[2]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'blackTable');
    initToolBar('#blackTable');
}