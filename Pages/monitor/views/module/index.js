//@ sourceURL=module.js
var myData = {};
var pageSize = 15;
$(function () {
    AjaxGet('/Monitoring/home/module/lists', function(data){
        createElem(data.modules);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.parentId = myData.id = obj.data('id');
        myData.cn_name = obj.data('cn_name');
        myData.name = obj.data('name');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-align-justify') != -1){
            AjaxGet('/Monitoring/home/module/lists', function(data){
                $('#myTable_wrapper').hide();
                var sub_modules = getSubModules(data.modules);
                createElem2(sub_modules);
                trHover('#myTable2');
                $('.breadcrumb').append('<li class="active">'+myData.cn_name+'</li>');
            });
        }
    });

    trclick('#myTable2', function(obj, e){
        myData.id2 = obj.data('id');
        myData.cn_name2 = obj.data('cn_name');
        myData.name2 = obj.data('name');
    });
});

listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

listenToolbar('add', addTableInfo2, '#myTable2');
listenToolbar('del', delTableInfo2, '#myTable2');
listenToolbar('back', backTable, '#myTable2');

function addTableInfo(){
    $('#name').val("");
    $('#cn_name').val("");
    $('#myModal').find('h4').html('新增');
    $('#myModal').modal('show');
}

function addTableInfo2(){
    $('#name').val("");
    $('#cn_name').val("");
    $('#myModal').find('h4').html('新增子模块');
    $('#myModal').modal('show');
}

function delTableInfo(){
    if(myData.id){
        if( confirm('确定删除？') ){
            AjaxGet('/Monitoring/home/module/delete?id=' + myData.id, function(){
                AjaxGet('/Monitoring/home/module/lists', function(data){
                    createElem(data.modules);
                    myData.id = null;
                });
            });
        }
    }else{
        alert('请选择模块！');
    }
}

function delTableInfo2(){
    if(myData.id2){
        if( confirm('确定删除？') ){
            AjaxGet('/Monitoring/home/module/delete?id=' + myData.parentId + '&sub_id=' + myData.id2, function(){
                AjaxGet('/Monitoring/home/module/lists', function(data){
                    var sub_modules = getSubModules(data.modules);
                    createElem2(sub_modules);
                    myData.id2 = null;
                });
            });
        }
    }else{
        alert('请选择模块！');
    }
}

function backTable(){
    $('#myTable2_wrapper').hide();
    $('#myTable_wrapper').show();
    $('.breadcrumb').find('li:last').remove();
    myData.id2 = null;
}

$('#subModule').click(function() {
    var name = $('#name').val();
    var cn_name = $('#cn_name').val();
    var data = {};

    if(name == ' ' || !name){
        alert('请输入模块字段');
        return;
    }
    if(cn_name == ' ' || ! cn_name){
        alert('请输入模块名');
        return;
    }
    var title = $('#myModal').find('h4').html();
    if(title == '新增'){
        data = {"module": name, "module_name": cn_name};
        AjaxPost('/Monitoring/home/module/add', data, function(){
            $('#myModal').modal('hide');
            AjaxGet('/Monitoring/home/module/lists', function(data){
                createElem(data.modules);
            });
        }, true);
    }else if(title == '新增子模块'){
        data = {"module_id": myData.parentId, "sub_module": name, "sub_module_name": cn_name};
        AjaxPost('/Monitoring/home/module/sub/add', data, function(){
            $('#myModal').modal('hide');
            AjaxGet('/Monitoring/home/module/lists', function(data){
                var sub_modules = getSubModules(data.modules);
                createElem2(sub_modules);
            });
        }, true);
    }
});

function getSubModules(arr){
    for(var i = 0, len = arr.length; i < len; i++){
        if(arr[i].id === myData.parentId){
            return arr[i].sub_modules;
        }
    }
}

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.module_name, arr.module, arr.sub_modules]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'模块名称','width':'35%', 'targets':1},
            {'title':'模块ID','width':'20%', 'targets':2},
            {'title':'子模块','width':'15%', 'targets':3},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(3, nRow, 'align-justify');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "cn_name":aData[1],
                "name":aData[2]
            });
        }
    });
    initToolBar('#myTable', [myConfig.addBtn, myConfig.delBtn]);

//    $('#myTable_wrapper .dataTables_info').remove();
}

function createElem2(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.sub_id, arr.sub_module_name, arr.sub_module]);
    }
    myDataTable('#myTable2', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'模块名称','width':'35%', 'targets':1},
            {'title':'模块ID','width':'20%', 'targets':2},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "cn_name":aData[1],
                "name":aData[2]
            });
        }
    });
    initToolBar('#myTable2', [myConfig.backBtn, myConfig.addBtn, myConfig.delBtn]);
}