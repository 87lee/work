//@ sourceURL=search.sort.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/search/getProgramLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.sortId = obj.data('id');
        myData.tmpRecommend = obj.data('recommend');
    });

    listenMyPage();
    listenchoose();
});

listenToolbar('edit', editTableInfo);

function editTableInfo() {
	if (myData.sortId) {
        $('#recommend').val(myData.tmpRecommend);
	    $('#editRecommend').modal('show');
	} else {
		alert('请选择搜索排序！');
	}
}


function updateTable(page, name, type){
    name = name || '';
    AjaxGet('/search/getProgramLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.sortId = null;
    });
}



$('#subRecommend').on('click', function(){
    var recommend = $('#recommend').val();
    if (!(/^\d{1,3}$/.test(recommend)) || parseInt(recommend) > 100) {
        alert('请输入0-100的推荐度!');
        return;
    }
    var data = {
        "id": myData.sortId,
        "recommend": recommend
    };
    console.log(data);
    var filter = $('#myTable_filter input').val() || '';
    AjaxPost('/search/modifyProgramSort', data, function(){
        $('#editRecommend').modal('hide');
        updateTable(currentPage, filter);
    });
});

//创建应用列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.type, arr.name, arr.recommend]);
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
            [3, "desc"]
        ],
        "columnDefs": [
        {
            'title': 'ID',
            'width': '8%',
            'targets': 0
        },{
            'title': '分类',
            'width': '16%',
            'targets': 1
        },{
            'title': '名称',
            'width': '16%',
            'targets': 2
        },{
            'title': '推荐度',
            'width': '16%',
            'targets': 3
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[2],
                "recommend": aData[3],
                "type": aData[1]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable', ['<a class="btn my-btn btn-info editBtn" href="javascript:"><i class="glyphicon glyphicon-edit icon-white"></i>&nbsp;修改排序</a>']);
}