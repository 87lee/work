//@ sourceURL=desktop.recycle.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
    myData.checkedLists = [];   //存储check选中项
	AjaxGet('/desktop/recycleLists?page=1&pageSize=' + pageSize, function(data) {
        createRecycle(data, 1);
        trHover('#recycleTable');
    });

    listenSingleCheckBox('#recycleTable', function(e){
        var tar = e.target;
        var obj = $(tar).parents('tr').find('td:eq(0)');
        myData.recycleId = obj.data('id');
    });

    listenMyPage('recycleTable');
});

listenToolbar('reply', replyTableInfo, '#recycleTable');
listenToolbar('del', delTableInfo, '#recycleTable');

function replyTableInfo() {
    actionTableInfo('/desktop/reductionDesktop');
}

function delTableInfo() {
    actionTableInfo('/desktop/deleteRecycleDesktop');
}

function actionTableInfo(url){
    if (myData.checkedLists.length > 0) {
        AjaxPost('/desktop/getDesktopNameForIdArr', myData.checkedLists, function(nameData){
            var con = "";
            for(var i = 0, len = nameData.extra.length; i < len; i++){
                con += nameData.extra[i] + '\n';
            }
            if (confirm('确定还原名称为：\n'+ con +'的桌面？')) {
                var filter = $('#recycleTable_filter input').val() || '';
                AjaxPost(url, myData.checkedLists, function() {
                    updateTable(currentPage, filter);
                    myData.checkedLists = [];
                });
            }
        });
    } else {
        alert('请选择桌面！');
    }
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/desktop/recycleLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createRecycle(data, page);
        myData.recycleId = null;
    });
}

//创建桌面列表
function createRecycle(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.layoutUpdateTme, arr.updateTime, arr.createTime, arr.desc, arr.user || '--']);
    }
    $('#recycleTable').dataTable({
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
            [2, "desc"]
        ],
        "columnDefs": [
        {
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '6%',
            'targets': 0,
            "orderable": false
        },
        {
            'title': '桌面名称',
            'width': '15%',
            'targets': 1
        },{
            'title': '布局更新时间',
            'width': '12%',
            'targets': 2
        },{
            'title': '全局更新时间',
            'width': '12%',
            'targets': 3
        },{
            'title': '创建时间',
            'width': '12%',
            'targets': 4
        },{
            'title': '备注',
            'width': '15%',
            'targets': 5
        },{
            'title': '最后修改者',
            'width': '10%',
            'targets': 6
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'recycleTable');
    initToolBar('#recycleTable', [
        '<a class="btn my-btn btn-primary replyBtn" href="javascript:"><i class="fa fa-reply-all icon-white"></i>&nbsp;还原</a>',
        myConfig.delBtn
    ]);

    listenCheckBox('#recycleTable');
    updateChecked('#recycleTable');
}