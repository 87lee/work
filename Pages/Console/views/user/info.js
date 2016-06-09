//@ sourceURL=user.info.js
var myData = {};
$(function () {
    AjaxGet('/user/lists', function(data){  //创建用户信息表格
        $('#myTable').css({visibility:'visible'});
        createElem(data.content);
        trHover('#myTable');
    });

    AjaxGet('/module/lists', function(data){    //创建模块列表树
        $('#tree1').ace_tree({
            dataSource: initModule(data),
            multiSelect: true,
            cacheItems: true,
            'open-icon' : 'ace-icon tree-minus',
            'close-icon' : 'ace-icon tree-plus',
            'selectable' : true,
            'selected-icon' : 'ace-icon fa fa-check',
            'unselected-icon' : 'ace-icon fa fa-times',
            loadingHTML : '<div class="tree-loading"><i class="ace-icon fa fa-refresh fa-spin blue"></i></div>'
        });

        $('#tree1').tree('discloseAll');
    });

    trclick('#myTable', function(obj, e){   //监听表格1的事件
        myData.name = obj.data('name');
        myData.id = obj.data('id');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-list') != -1){
            AjaxGet('/module/lists?user_id=' + myData.id, function(data){
                var modules = data.modules;
                $('#tree1').tree('deselectAll');
                if(modules){
                    for(var i = 0, len = modules.length; i < len; i++){
                        var module = modules[i];
                        var module_id = module.id;
                        var sub_modules = module.sub_modules;
                        for(var j = 0, l = sub_modules.length; j < l; j++){
                            var sub_module = sub_modules[j];
                            var sub_id = sub_module.sub_id;
                            checkModule(module_id, sub_id);
                        }
                    }
                }
                $('#myModal2').modal('show');
            });
        }
    });
});

function checkModule(pId, cId){ //显示用户已有的模块
    var tree = $('#tree1');
    var parent = tree.find('li.tree-branch').not('[data-template="treebranch"]');
    for(var i = 0, len = parent.length; i < len; i++){
        var obj = $(parent[i]);
        if(pId == obj.data("id")){
            var children = obj.find('li.tree-item');
            for(var j = 0, l = children.length; j < l; j++){
                var children_obj = $(children[j]);
                if(cId == children_obj.data("id")){
                    children_obj.find('.tree-item-name').trigger('click');
                }
            }
        }
    }
}

function initModule(data){  //把获取的数据生成tree的数据格式
    var arr = data.modules;
    var tree_data = {};
    for(var i = 0, len = arr.length; i < len; i++){
        var module_name = arr[i].module_name;
        var module = arr[i].module;
        var sub_modules = arr[i].sub_modules;
        var module_id = arr[i].id;
        if(sub_modules && sub_modules.length > 0){
            tree_data[module] = {"text": module_name, "type": "folder"};
            tree_data[module]['attr'] = {"data-id": module_id, "data-module": module};
            var children = {};
            for(var j = 0, l = sub_modules.length; j < l; j++){
                var sub_name = sub_modules[j].sub_module_name;
                var sub = sub_modules[j].sub_module;
                var sub_id = sub_modules[j].sub_id;
                children[sub] = {"text": sub_name, type: "item"};
                children[sub]['attr'] = {"data-id": sub_id, "data-sub": sub};
            }
            tree_data[module]['additionalParameters'] = {
                "children" : children
            };
        }
    }

    return treeCallbak(tree_data);
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
            AjaxGet('/user/delete?id=' + myData.id, function(){
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
        AjaxPost('/user/add', data, function(){
            $('#myModal').modal('hide');
            refreshTable();
        }, true);
    }else if(title == '修改'){
        data = {"user": name, "new": pwd};
        AjaxPost('/user/passwd', data, function(){
            $('#myModal').modal('hide');
            refreshTable();
        }, true);
    }
});

$('#subModule').click(function() {
    var data = {};
    data.modules = [];
    var tree = $('#tree1');
    var parent = tree.find('li.tree-branch').not('[data-template="treebranch"]');
    for(var i = 0, len = parent.length; i < len; i++){
        var obj = $(parent[i]);
        var children = obj.find('li.tree-item').filter('.tree-selected');
        var name = obj.find('.tree-branch-header').find('.tree-label').html();
        var sub_modules = [];
        for(var j = 0, l = children.length; j < l; j++){
            var children_obj = $(children[j]);
            var children_name = children_obj.find('.tree-item-name').find('.tree-label').html();
            var sub = {"sub_id": children_obj.data("id"), "sub_module": children_obj.data("sub"), "sub_module_name": children_name};
            sub_modules.push(sub);
        }
        if(sub_modules && sub_modules.length > 0){
            var module = {"id": obj.data("id"), "module": obj.data("module"), "module_name": name, "sub_modules": sub_modules};
            data.modules.push(module);
        }
    }

    AjaxPost('/module/auth?user_id=' + myData.id, data, function(){
        $('#myModal2').modal('hide');
    });
});

function refreshTable(){
    AjaxGet('/user/lists', function(data){
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