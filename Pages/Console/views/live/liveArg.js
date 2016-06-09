//@ sourceURL=live.liveArg.js
var myData = {};
$(function () {
    AjaxGet('/liveParam/typeLists', function(data){     //初始化类型table
        createElem(data.extra);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){       //类型table点击事件
        myData.typeId = obj.data('id');
        myData.typeName = obj.data('typeName');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-align-justify') != -1){//初始化参数table
            AjaxGet('/liveParam/paramLists?type_id=' + myData.typeId, function(data){
                $('#myTable_wrapper').hide();
                createElem2(data.extra);
                trHover('#myTable2');
                $('.breadcrumb').append('<li class="active">'+myData.typeName+'</li>');
            });
        }
    });

    trclick('#myTable2', function(obj, e){      //参数table点击事件
        myData.paramId = obj.data('id');
        myData.paramName = obj.data('paramName');
        myData.paramDesc = obj.data('paramDesc');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-align-justify') != -1){//初始化预选值table
            AjaxGet('/liveParam/optionLists?param_id=' + myData.paramId, function(data){
                $('#myTable2_wrapper').hide();
                createElem3(data.extra);
                trHover('#myTable3');
                $('.breadcrumb').append('<li class="active">'+myData.paramName+'</li>');
            });
        }
    });

    trclick('#myTable3', function(obj, e){      //预选值table点击事件
        myData.advanceId = obj.data('id');
        myData.advanceName = obj.data('advanceName');
        myData.choose = obj.data('choose');
    });

    listenchoose();
});

listenToolbar('add', addTableInfo);     //类型table添加事件
listenToolbar('edit', editTableInfo);     //类型table修改事件
listenToolbar('del', delTableInfo);     //类型table删除事件

listenToolbar('add', addTableInfo2, '#myTable2');   //参数table添加事件
listenToolbar('edit', editTableInfo2, '#myTable2');   //参数table修改事件
listenToolbar('del', delTableInfo2, '#myTable2');   //参数table删除事件
listenToolbar('back', backTable, '#myTable2');      //返回类型table

listenToolbar('add', addTableInfo3, '#myTable3');   //预选值table添加事件
listenToolbar('edit', editTableInfo3, '#myTable3');   //预选值table修改事件
listenToolbar('del', delTableInfo3, '#myTable3');   //预选值table删除事件
listenToolbar('back', backTable2, '#myTable3');     //返回参数table


function addTableInfo(){
    $('#typeName').val("");
    $('#myModal h4').text('新增');
    $('#myModal').modal('show');
}

function addTableInfo2(){
    $('#paramName').val("");
    $('#paramDesc').val("");
    $('#myModal2 h4').text('新增');
    $('#myModal2').modal('show');
}

function addTableInfo3(){
    $('#advanceName').val("");
    $('#chooseDefault').hide();
    $('#myModal3 h4').text('新增');
    $('#myModal3').modal('show');
}

function editTableInfo(){
    if(myData.typeId){
        $('#typeName').val(myData.typeName);
        $('#myModal h4').text('修改');
        $('#myModal').modal('show');
    }else{
        alert('请选择类型！');
    }
}

function editTableInfo2(){
    if(myData.paramId){
        $('#paramName').val(myData.paramName);
        $('#paramDesc').val(myData.paramDesc);
        $('#myModal2 h4').text('修改');
        $('#myModal2').modal('show');
    }else{
        alert('请选择参数！');
    }
}

function editTableInfo3(){
    if(myData.advanceId){
        $('#advanceName').val(myData.advanceName);
        $('#chooseDefault').show();
        $('#myModal3 h4').text('修改');
        if(myData.choose === 'true'){
            $('#chooseDefault').find('input:eq(0)').trigger('click');
        }else{
            $('#chooseDefault').find('input:eq(1)').trigger('click');
        }
        $('#myModal3').modal('show');
    }else{
        alert('请选择预选值！');
    }
}

function delTableInfo(){
    if(myData.typeId){
        if( confirm('确定删除？') ){
            AjaxGet('/liveParam/deleteType?type_id=' + myData.typeId, function(){
                updateTable1();
            });
        }
    }else{
        alert('请选择类型！');
    }
}

function delTableInfo2(){
    if(myData.paramId){
        if( confirm('确定删除？') ){
            AjaxGet('/liveParam/deleteParam?param_id=' + myData.paramId, function(){
                updateTable2();
            });
        }
    }else{
        alert('请选择参数！');
    }
}

function delTableInfo3(){
    if(myData.advanceId){
        if( confirm('确定删除？') ){
            AjaxGet('/liveParam/deleteOption?option_id=' + myData.advanceId, function(){
                updateTable3();
            });
        }
    }else{
        alert('请选择预选值！');
    }
}

function updateTable1(){
    AjaxGet('/liveParam/typeLists', function(data){
        createElem(data.extra);
        myData.typeId = null;
    });
}

function updateTable2(){
    AjaxGet('/liveParam/paramLists?type_id=' + myData.typeId, function(data){
        createElem2(data.extra);
        myData.paramId = null;
    });
}

function updateTable3(){
    AjaxGet('/liveParam/optionLists?param_id=' + myData.paramId, function(data){
        createElem3(data.extra);
        myData.advanceId = null;
    });
}

function backTable(){
    $('#myTable2_wrapper').hide();
    $('#myTable_wrapper').show();
    $('.breadcrumb').find('li:last').remove();
    myData.paramId = null;
}

function backTable2(){
    $('#myTable3_wrapper').hide();
    $('#myTable2_wrapper').show();
    $('.breadcrumb').find('li:last').remove();
    myData.advanceId = null;
}

$('#subType').click(function() {    //类型添加提交
    var typeName = $('#typeName').val();
    var title = $('#myModal h4').text();
    var data = {};

    if(typeName == ' ' || ! typeName){
        alert('请输入类型名称');
        return;
    }

    if(title == '新增'){
        data = {"type": $.trim(typeName)};
        AjaxPost('/liveParam/addType', data, function(){
            $('#myModal').modal('hide');
            updateTable1();
        });
    }else if(title == '修改'){
        data = {"id": myData.typeId, "type": $.trim(typeName)};
        AjaxPost('/liveParam/modifyParamType', data, function(){
            $('#myModal').modal('hide');
            updateTable1();
        });
    }
});

$('#subParam').click(function() {       //参数添加提交
    var paramName = $('#paramName').val();
    var paramDesc = $('#paramDesc').val();
    var title = $('#myModal2 h4').text();
    var data = {};

    if(paramName == ' ' || !paramName){
        alert('请输入参数名称');
        return;
    }
    if(title == '新增'){
        data = {"type_id": myData.typeId, "param": $.trim(paramName), "desc": paramDesc};
        AjaxPost('/liveParam/addParam', data, function(){
            $('#myModal2').modal('hide');
            updateTable2();
        });
    }else if(title == '修改'){
        data = {"id": myData.paramId,"param": $.trim(paramName),"desc": paramDesc}
        AjaxPost('/liveParam/modifyParamType', data, function(){
            $('#myModal2').modal('hide');
            updateTable2();
        });
    }
});

$('#subAdvance').click(function() {     //预选值添加提交
    var advanceName = $('#advanceName').val();
    var title = $('#myModal3 h4').text();
    var data = {};

    if(advanceName == ' ' || !advanceName){
        alert('请输入预选值');
        return;
    }
    if(title == '新增'){
        data = {"param_id": myData.paramId, "value": $.trim(advanceName)};
        AjaxPost('/liveParam/addOption', data, function(){
            $('#myModal3').modal('hide');
            updateTable3();
        });
    }else if(title == '修改'){
        var Default = $('#chooseDefault').find('input:checked').val();
        if(Default == 'true' && $('#myTable3 td').filter('.red').length > 0){
            if( !confirm('该参数下已有预选值，确定替换？') ){
                return;
            }
        }
        data = {"id": myData.advanceId, "value": $.trim(advanceName), "default": Default};
        AjaxPost('/liveParam/modifyParamType', data, function(){
            $('#myModal3').modal('hide');
            updateTable3();
        });
    }
});

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.type, null]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "order": [[1, "asc"]],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'参数类型','width':'25%', 'targets':1},
            {'title':'参数列表','width':'10%', 'targets':2},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(2, nRow, 'align-justify');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "typeName":aData[1]
            });
        }
    });
    initToolBar('#myTable');
}

function createElem2(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.param, arr.desc, null]);
    }
    myDataTable('#myTable2', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'参数','width':'20%', 'targets':1},
            {'title':'描述','width':'35%', 'targets':2},
            {'title':'预选值列表','width':'15%', 'targets':3},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(3, nRow, 'align-justify');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "paramName": aData[1],
                "paramDesc": aData[2],
            });
        }
    });
    initToolBar('#myTable2', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}

function createElem3(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.value+'|'+arr.default]);
    }
    myDataTable('#myTable3', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'227px', 'targets':0},
            {'title':'参数预选值','width':'572px', 'targets':1},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            var paramData = aData[1].split('|');
            var value = paramData[0];
            var choose = paramData[1];
            if(choose === 'true'){
                $('td:eq(1)', nRow).addClass('red');
            }
            $('td:eq(1)', nRow).css({
                'wordWrap':'break-word',
                'maxWidth': '572px'
            });
            $('td:eq(1)', nRow).text(value);
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "advanceName": value,
                "choose": choose
            });
        }
    });
    initToolBar('#myTable3', [myConfig.backBtn, myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}