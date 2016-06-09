//@ sourceURL=serveParam.param.js
var myData = {};
$(function () {
    AjaxGet('/Server/serverConfLists', function(data){     //初始化类型table
        createElem(data.extra);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){       //类型table点击事件
        myData.serveId = obj.data('id');
        myData.serveName = obj.data('name');
        myData.serveVal = obj.data('val');
    });

    listenchoose();
});

listenToolbar('add', addTableInfo);     //类型table添加事件
listenToolbar('edit', editTableInfo);     //类型table修改事件
listenToolbar('del', delTableInfo);     //类型table删除事件


function addTableInfo(){
    $('#serveName').val("");
    $('#serveVal').val("");
    $('#myModal h4').text('新增');
    $('#myModal').modal('show');
}

function editTableInfo(){
    if(myData.serveId){
        $('#serveName').val(myData.serveName);
        $('#serveVal').val(myData.serveVal);
        $('#myModal h4').text('修改');
        $('#myModal').modal('show');
    }else{
        alert('请选择参数！');
    }
}

function delTableInfo(){
    if(myData.serveId){
        if( confirm('确定删除？') ){
            AjaxGet('/Server/deleteServerConf?id=' + myData.serveId, function(){
                updateTable();
            });
        }
    }else{
        alert('请选择参数！');
    }
}


function updateTable(){
    AjaxGet('/Server/serverConfLists', function(data){
        createElem(data.extra);
        myData.serveId = null;
    });
}

$('#subParam').click(function() {    //参数添加提交
    var serveName = $('#serveName').val();
    var serveVal = $('#serveVal').val();
    var title = $('#myModal h4').text();
    var data = {};

    if(serveName == ' ' || ! serveName){
        alert('请输入服务参数名称');
        return;
    }

    if(serveVal == ' ' || ! serveVal){
        alert('请输入服务参数值');
        return;
    }

    if(title == '新增'){
        data = {"name": $.trim(serveName), "value": $.trim(serveVal)};
        AjaxPost('/Server/addServerConf', data, function(){
            $('#myModal').modal('hide');
            updateTable();
        });
    }else if(title == '修改'){
        data = {"id": myData.serveId, "name": $.trim(serveName), "value": $.trim(serveVal)};
        AjaxPost('/Server/modifyServerConf', data, function(){
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
        dataArr.push([arr.id, arr.name, arr.value]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "order": [[1, "asc"]],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'服务参数名','width':'25%', 'targets':1},
            {'title':'服务参数值','width':'20%', 'targets':2},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "name":aData[1],
                "val":aData[2]
            });
        }
    });
    initToolBar('#myTable');
}