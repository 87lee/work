//@ sourceURL=live.setAuth.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/Live/liveAuthHistoryLists?page=1&pageSize=' + pageSize, function(data) {
        		createAuthLog(data, 1);
    	});
    	listenMyPage('authLogTable', currentPage,'updateTable');
});

function updateTable(page, name){
    AjaxGet('/Live/liveAuthHistoryLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createAuthLog(data, page);
    });
}

//创建发布列表
function createAuthLog(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        if (arr.action == 'add') {
        	arr.action = '添加';
        }else if (arr.action == 'modify') {
        	arr.action = '修改';
        }else if (arr.action == 'delete') {
        	arr.action = '删除';
        }
        dataArr.push([arr.id, arr.model, arr.vendorID, arr.amount,arr.action , arr.authorizer , arr.time]);
    }
    $('#authLogTable').dataTable({
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
            [0, "desc"]
        ],
        "columnDefs": [
            {'title':'id','width':'8%', 'targets':0},
            {'title':'型号','width':'10%', 'targets':1},
            {'title':'vendorID','width':'10%', 'targets':2},
            {'title':'预授权数量','width':'10%', 'targets':3},
            {'title':'操作','width':'10%', 'targets':4},
            {'title':'操作人','width':'7%', 'targets':5},
            {'title':'时间','width':'7%', 'targets':6}
        ],
        "createdRow": function( nRow, aData, idx ){
            $('td:eq(0)', nRow).data({
                "amount": aData[3],
                "id": aData[0],
                "model": aData[1],
                "time": aData[6],
                "vendorID": aData[2]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'authLogTable');

}
