//@ sourceURL=user.info.js
var myData = {};
var treeData = {};
$(function () {
    AjaxGet('/Monitoring/home/user/lists', function(data){  //创建用户信息表格
        $('#myTable').css({visibility:'visible'});
        createElem(data.content);
        trHover('#myTable');
    });

    AjaxGet('/Monitoring/home/module/lists', function(data){    //创建模块列表树
        treeData = data;
        $('#tree').treeview({
            data: initModule(data),
            multiSelect: true,
            selectedBackColor: '#ffffff',
            selectedColor: '#000000'
        });
        $('#tree').treeview('collapseAll', { silent: true });
    });

    function treeCheckAll(id) {
        for (var i = 0; i < $(id + ' .list-group li span[class*=check]').parent().length; i++) {
            var num = $(id + ' .list-group li span[class*=check]:eq('+ i +')').parent().attr('data-nodeid');
            $(id).treeview('selectNode', [ Number(num), { silent: true } ]);
       }
    }

    function treeUnCheckAll(id) {
        for (var i = 0; i < $(id + ' .list-group li span[class*=check]').parent().length; i++) {
            var num = $(id + ' .list-group li span[class*=check]:eq('+ i +')').parent().attr('data-nodeid');
            $(id).treeview('unselectNode', [ Number(num), { silent: true } ]);
       }
    }

    trclick('#myTable', function(obj, e){   //监听表格1的事件
        myData.name = obj.data('name');
        myData.id = obj.data('id');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-list') != -1){
            AjaxGet('/Monitoring/home/module/lists?user_id=' + myData.id, function(data){
                var modules = data.modules;
                treeUnCheckAll('#tree');
                $('#tree').treeview('expandAll', { levels: 2, silent: true });
                if(modules){
                    for(var i = 0, len = modules.length; i < len; i++){
                         var module = modules[i];
                         var sub_modules = module.sub_modules;
                        for(var j = 0, l = sub_modules.length; j < l; j++){
                            checkModule('#tree', sub_modules[j].sub_module_name);
                        }
                    }
                }
                $('#myModal2').modal('show');
            });
        }
    });
});

function checkModule(id, name){ //显示用户已有的模块
    for (var i = 0; i < $(id + ' .list-group li span[class*=check]').parent().length; i++) {
        if ($(id + ' .list-group li span[class*=check]:eq('+ i +')').parent().text() === name) {
            var num = $(id + ' .list-group li span[class*=check]:eq('+ i +')').parent().attr('data-nodeid');
            $(id).treeview('selectNode', [ Number(num), { silent: true } ]);
        }
    }
}

function initModule(data){  //把获取的数据生成tree的数据格式
    var arr = data.modules;
    var tree_data = [];
    for(var i = 0, len = arr.length; i < len; i++){
        var nodes = {};
        nodes.text = arr[i]. module_name;
        var children = [];
        for (var j = 0; j < arr[i].sub_modules.length; j++) {
            children.push({text:arr[i].sub_modules[j].sub_module_name,icon: "glyphicon glyphicon-unchecked",selectedIcon: "glyphicon glyphicon-check"});
        }
        nodes.nodes = children;
        tree_data.push(nodes);
    }
    return tree_data;
}

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function editTableInfo(){
    if(myData.name){
        $('#name').val(myData.name);
        $('#pwd').val("");
        $('#myModal').find('h4').html('修改');
        $('#name').prop('disabled', true);
        $('#myModal').modal('show');
    }else{
        alert('请选择用户！');
    }
}

function addTableInfo(){
    $('#name').val("");
    $('#pwd').val("");
    $('#myModal').find('h4').html('新增');
    $('#name').prop('disabled', false);
    $('#myModal').modal('show');
}

function delTableInfo(){
    if(myData.id){
        if( confirm('确定删除？') ){
            AjaxGet('/Monitoring/home/user/delete?id=' + myData.id, function(){
                myData.id = null;
                refreshTable();
            });
        }
    }else{
        alert('请选择用户！');
    }
}


$('#subUser').click(function() {
    var name = $('#name').val();
    var pwd = $('#pwd').val();
    var data = {};

    if(name == ' ' || !name){
        alert('请输入用户名');
        return;
    }
    if(pwd == ' ' || ! pwd){
        alert('请输入密码');
        return;
    }

    var title = $('#myModal').find('h4').html();
    if(title == '新增'){
        data = {"user": name, "passwd": pwd};
        AjaxPost('/Monitoring/home/user/add', data, function(){
            $('#myModal').modal('hide');
            refreshTable();
        }, true);
    }else if(title == '修改'){
        data = {"user": name, "new": pwd};
        AjaxPost('/Monitoring/home/user/passwd', data, function(){
            $('#myModal').modal('hide');
            refreshTable();
        }, true);
    }
});

$('#subModule').click(function() {
    var data = {};
    data.modules = [];
    for (var i = 0; i < treeData.modules.length; i++) {
        //treeData第一层
        var module = {};
        module.id = treeData.modules[i].id;
        module.module = treeData.modules[i].module;
        module.module_name = treeData.modules[i].module_name;
        module.sub_modules =[];
        for (var j = 0; j < treeData.modules[i].sub_modules.length; j++) {
            //treeData子层
            var sub_module = {};
            sub_module.sub_id = treeData.modules[i].sub_modules[j].sub_id;
            sub_module.sub_module = treeData.modules[i].sub_modules[j].sub_module;
            sub_module.sub_module_name = treeData.modules[i].sub_modules[j].sub_module_name;
            for (var k = 0; k < $('#tree .list-group li span[class*=check]').parent().length; k++) {
                //在tree里面搜索
                if ($('#tree .list-group li span[class*=-check]:eq('+k+')').parent().text() === treeData.modules[i].sub_modules[j].sub_module_name) {
                    module.sub_modules.push(sub_module);
                }
            }
        }
        if (module.sub_modules.length > 0) {
            data.modules.push(module);
        }
    }
    AjaxPost('/Monitoring/home/module/auth?user_id=' + myData.id, data, function(){
        $('#myModal2').modal('hide');
    });
});

function refreshTable(){
    AjaxGet('/Monitoring/home/user/lists', function(data){
        createElem(data.content);
    });
}

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.user, null]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'用户名','width':'20%', 'targets':1},
            {'title':'模块列表','width':'10%', 'targets':2},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(2, nRow, 'list');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "name":aData[1]
            });
        }
    });
    initToolBar('#myTable');
}