//@ sourceURL=desktop.handle.js
var myData = {};
$(function () {
    AjaxGet('/desktop/actionTypeLists', function(data){
        createElem(data.extra);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.handleId = obj.data('id');
    }, 1);
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function editTableInfo(){
    if(myData.handleId){
        $('#groupName').val(myData.handleName);
        $('#myModal').find('h4').html('修改');
        $('#myModal').modal('show');
    }else{
        alert('请选择ACTION类型！');
    }
}

function addTableInfo(){
    $('#handleName').val("");
    $('#myModal').find('h4').html('新增');
    $('#myModal').modal('show');
}

function delTableInfo(){
    if(myData.handleId){
        if( confirm('确定删除？') ){
            AjaxGet('/desktop/deleteActionType?id=' + myData.handleId, function(){
                updateTable();
            });
        }
    }else{
        alert('请选择ACTION类型！');
    }
}

function updateTable(){
    AjaxGet('/desktop/actionTypeLists', function(data){
        createElem(data.extra);
        myData.handleId = null;
    });
}

$('#subHandle').click(function() {
    var handleName = $('#handleName').val();
    var data = {};

    if(handleName == ' ' || ! handleName){
        alert('请输入ACTION类型！');
        return;
    }
    var title = $('#myModal').find('h4').html();
    if(title == '新增'){
        data = {"action": $.trim(handleName)};
        AjaxPost('/desktop/addActionType', data, function(){
            $('#myModal').modal('hide');
            updateTable();
        });
    }else if(title == '修改'){
        data = {"group_name": $.trim(groupName), "group_id": myData.handleId};
        AjaxPost('/group/modifyName', data, function(){
            $('#myModal').modal('hide');
            updateTable();
        });
    }
});

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.action]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'ACTION类型','width':'25%', 'targets':1},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id":aData[0],
            });
        }
    });
    initToolBar('#myTable', [myConfig.addBtn, myConfig.delBtn]);
}