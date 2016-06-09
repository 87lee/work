//@ sourceURL=desktop.layoutType.js
var myData = {};
$(function () {
	AjaxGet('/desktop/layoutTypeLists', function(data){
        createLayout(data);
        trHover('#layoutTable');
    });

    trclick('#layoutTable', function(obj, e){
        myData.layoutId = obj.data('id');
        myData.layoutName = obj.data('name');
        myData.layoutType = obj.data('type');

    });
});

listenToolbar('edit', editTableInfo, '#layoutTable');
listenToolbar('add', addTableInfo, '#layoutTable');
listenToolbar('del', delTableInfo, '#layoutTable');

function addTableInfo(){
    $('#layoutName').val('');
    $('#layoutType').val('');
    $('#layoutModal h4').text('添加');
    $('#layoutModal').modal('show');
}

function editTableInfo(){
    if(myData.layoutId){
        $('#layoutName').val(myData.layoutName);
        $('#layoutType').val(myData.layoutType);
        $('#layoutModal h4').text('修改');
        $('#layoutModal').modal('show');
    }else{
        alert('请选择布局类型！');
    }
}

function delTableInfo(){
    if(myData.layoutId){
        if( confirm('确定删除？') ){
            AjaxGet('/desktop/deleteLayoutType?id=' + myData.layoutId, function(){
                updateTable();
            });
        }
    }else{
        alert('请选择布局类型！');
    }
}

function updateTable(){
    AjaxGet('/desktop/layoutTypeLists', function(data){
        createLayout(data);
        myData.layoutId = null;
    });
}

$('#subLayout').on('click', function(){
    var name = $('#layoutName').val();
    var type = $('#layoutType').val();
    var title = $('#layoutModal h4').text();

    if(name == ' ' || !name){
        alert('请输入名称');
        return false;
    }
    if(type == ' ' || !type){
        alert('请输入类型');
        return false;
    }

    var data = {"name": name, "type": type};
    var url = '';

    if(title === '添加'){
        url = '/desktop/addLayoutType';
    }else if(title === '修改'){
        url = '/desktop/modifyLayoutType';
        data.id = myData.layoutId;
    }
    AjaxPost(url, data, function(){
        $('#layoutModal').modal('hide');
        updateTable();
    });
});

function createLayout(data){
    var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.type]);
    }
    myDataTable('#layoutTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'名称','width':'25%', 'targets':1},
            {'title':'类型','width':'10%', 'targets':2},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "name":aData[1],
                "type":aData[2],
            });
        }
    });
    initToolBar('#layoutTable');
}