//@ sourceURL=resources.group.js
var myData = {};
$(function () {
    AjaxGet('/resources/imageGroupLists', function(data){
        createElem(data.extra);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.groupId = obj.data('id');
        myData.groupName = obj.data('groupName');
    });
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function editTableInfo(){
    if(myData.groupId){
        $('#groupName').val(myData.groupName);
        $('#myModal').find('h4').html('修改');
        $('#myModal').modal('show');
    }else{
        alert('请选择内测组！');
    }
}

function addTableInfo(){
    $('#groupName').val("");
    $('#myModal').find('h4').html('新增');
    $('#myModal').modal('show');
}

function delTableInfo(){
    if(myData.groupId){
        if( confirm('确定删除？') ){
            AjaxGet('/resources/deleteImageGroup?id=' + myData.groupId, function(){
                updateTable();
            });
        }
    }else{
        alert('请选择组！');
    }
}

function updateTable(){
    AjaxGet('/resources/imageGroupLists', function(data){
        createElem(data.extra);
        myData.groupId = null;
    });
}

$('#subGroup').click(function() {
    var groupName = $('#groupName').val();
    var data = {};

    if(groupName == ' ' || ! groupName){
        alert('请输入内测组名称');
        return;
    }
    var title = $('#myModal').find('h4').html();
    if(title == '新增'){
        data = {"group": $.trim(groupName)};
        AjaxPost('/resources/addImageGroup', data, function(){
            $('#myModal').modal('hide');
            updateTable();
        });
    }else if(title == '修改'){
        data = {"group_name": $.trim(groupName), "group_id": myData.groupId};
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
        dataArr.push([arr.id, arr.group, null]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'组名称','width':'25%', 'targets':1}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id":aData[0]
            });
        }
    });
    initToolBar('#myTable', [myConfig.addBtn, myConfig.delBtn]);
}