//@ sourceURL=desktop.block.js
var myData = {};
var pageSize = 15;  //自定义分页，每页显示的数据量
var currentPage = 1;    //当前的页面
$(function () {
    AjaxGet('/desktop/blockLists?page=1&pageSize='+pageSize, function(data){
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.blockId = obj.data('id');
        myData.blockName = obj.data('groupName');
    });

    listenMyPage();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function editTableInfo(){
    if(myData.blockId){
        $('#groupName').val(myData.blockName);
        $('#myModal').find('h4').html('修改');
        $('#myModal').modal('show');
    }else{
        alert('请选择块！');
    }
}

function addTableInfo(){
    $('#realW').val('');
    $('#realH').val('');
    $('#cloudW').val('');
    $('#cloudH').val('');
    $('#myModal').find('h4').html('新增');
    $('#myModal').modal('show');
}

function delTableInfo(){
    if(myData.blockId){
        if( confirm('确定删除？') ){
            var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/desktop/deleteBlock?id=' + myData.blockId, function(){
                updateTable(currentPage, filter);
            });
        }
    }else{
        alert('请选择块！');
    }
}

function updateTable(page, name){
    var url = '';
    if(name){
        url = '/desktop/blockLists?name='+name+'&page='+ page +'&pageSize='+pageSize;
    }else{
        url = '/desktop/blockLists?page='+ page +'&pageSize='+pageSize;
    }
    AjaxGet(url, function(data){
        createElem(data, page);
        myData.blockId = null;
    });
}

$('#subBlock').click(function() {
    var realW = $('#realW').val();
    var realH = $('#realH').val();
    var cloudW = $('#cloudW').val();
    var cloudH = $('#cloudH').val();
    var filter = $('#myTable_filter input').val() || '';
    var data = {};

    if(realW == ' ' || !realW || realH == ' ' || !realH){
        alert('请输入宽、高');
        return;
    }
    if(/\D/.test(realW) || /\D/.test(realH)){
        alert('宽、高只能为数字');
        return;
    }
    if(cloudW == ' ' || !cloudW || cloudH == ' ' || !cloudH){
        alert('请输入云宽、云高');
        return;
    }
    if(/\D/.test(cloudW) || /\D/.test(cloudH)){
        alert('云宽、云高只能为数字');
        return;
    }
    var title = $('#myModal').find('h4').html();
    if(title == '新增'){
        data = {"name": realW + '*' + realH, "w": $.trim(realW), "h": $.trim(realH), "yw": $.trim(cloudW), "yh": $.trim(cloudH)};
        AjaxPost('/desktop/addBlock', data, function(){
            $('#myModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title == '修改'){
        data = {"group_name": $.trim(groupName), "group_id": myData.blockId};
        AjaxPost('/group/modifyName', data, function(){
            $('#myModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.w, arr.h, arr.yw, arr.yh]);
    }
    $('#myTable').dataTable({
        "lengthChange": false,
        "autoWidth":false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "data": dataArr,
        "order": [[1, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'块名称','width':'15%', 'targets':1},
            {'title':'宽','width':'15%', 'targets':2},
            {'title':'高','width':'15%', 'targets':3},
            {'title':'云宽','width':'15%', 'targets':4},
            {'title':'云高','width':'15%', 'targets':5},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id":aData[0],
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable', [myConfig.addBtn, myConfig.delBtn]);
}