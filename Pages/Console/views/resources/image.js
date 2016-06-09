//@ sourceURL=resources.image.js
var myData = {};
var pageSize = 15;  //自定义分页，每页显示的数据量
var currentPage = 1;    //当前的页面
$(function () {
    AjaxGet('/resources/imageGroupLists', selectGroup);

    AjaxGet('/resources/imageLists?page=1&pageSize='+pageSize, function(data){
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.picId = obj.data('id');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-picture') != -1){
            window.open($(tar).data('src'));
        }
    });

    listenfile();

    listenPic();

    listenMyPage();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function editTableInfo(){
    if(myData.picId){
        $('#groupName').val(myData.groupName);
        $('#myModal').find('h4').html('修改');
        $('#myModal').modal('show');
    }else{
        alert('请选择内测组！');
    }
}

function addTableInfo(){
    $('#groupName').val("");
    $('#picName').val("");
    $('#fileShow').val("");
    $('#myModal').find('h4').html('新增');
    $('#myModal').modal('show');
}

function delTableInfo(){
    if(myData.picId){
        if( confirm('确定删除？') ){
            var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/resources/deleteImage?id=' + myData.picId, function(){
                updateTable(currentPage, filter);
            });
        }
    }else{
        alert('请选择内测组！');
    }
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/resources/imageLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.picId = null;
    });
}

function selectGroup(data){
    var arr = data.extra;
    var con = '';
    var $select = $('#groupId');
    var len = arr.length;
    for( var i=0; i<len; i++ ){
        con += '<option value="'+ arr[i].id +'">'+ arr[i].group +'</option>';
    }
    $select.html(con).trigger("change");
}


$('#subGroup').click(function() {
    var groupId = $('#groupId').val();
    var picName = $('#picName').val();
    var filter = $('#myTable_filter input').val() || '';
    var data = new FormData();

    if(groupId == ' ' || ! groupId){
        alert('请选择组');
        return;
    }
    if(picName == ' ' || ! picName){
        alert('请输入图片名称');
        return;
    }
    var fileObj = document.getElementById("fileHide").files[0];
    var fileVal = $("#fileShow").val();

    if(fileVal != ' ' && fileVal.indexOf('http') == -1){
        data.append("fileName", fileObj);
    }
    if(fileVal == ' ' || !fileVal){
        alert('请选择要上传的图片');
        return;
    }
    var extra_data = '{"group_id":"'+groupId+'", "name":"'+picName+'"}';
    data.append("extra_data", extra_data);

    AjaxFile('/resources/uploadImage', data, function(){
        $('#myModal').modal('hide');
        updateTable(currentPage, filter);
    });
});

function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.download]);
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
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'图片名称','width':'25%', 'targets':1},
            {'title':'查看','width':'10%', 'targets':2}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(2)', nRow).html('<i class="glyphicon glyphicon-picture icon-black my-icon" data-src="'+ aData[2] +'"></i>').addClass('center');
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