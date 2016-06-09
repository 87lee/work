//@ sourceURL=desktop.icon.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
$(function() {
    AjaxGet('/desktop/iconLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.widgetId = obj.data('id');
        myData.widgetName = obj.data('name');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-picture') != -1) {
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

function editTableInfo() {
    if (myData.widgetId) {
        $('#widget').val(myData.widgetName);
        $('#fileShow1').val("");
        $('#fileShow2').val("");
        $('#fileHide1').val("");
        $('#fileHide2').val("");
        $('#myModal').find('h4').html('修改');
        $('#myModal').modal('show');
    } else {
        alert('请选择控件！');
    }
}

function addTableInfo() {
    $('#widget').val("");
    $('#fileShow1').val("");
    $('#fileShow2').val("");
    $('#fileHide1').val("");
    $('#fileHide2').val("");
    $('#myModal').find('h4').html('添加');
    $('#myModal').modal('show');
}

function delTableInfo() {
    if (myData.widgetId) {
        if (confirm('确定删除？')) {
            var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/desktop/deleteIcon?id=' + myData.widgetId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择控件！');
    }
}

function updateTable(page, name) {
    var url = '';
    if (name) {
        url = '/desktop/iconLists?name=' + name + '&page=' + page + '&pageSize=' + pageSize;
    } else {
        url = '/desktop/iconLists?page=' + page + '&pageSize=' + pageSize;
    }
    AjaxGet(url, function(data) {
        createElem(data, page);
        myData.widgetId = null;
    });
}

$('#subIcon').click(function() {
    var widget = $('#widget').val();
    var picNormal = $('#picNormal').val();
    var picForcus = $('#picForcus').val();
    var filter = $('#myTable_filter input').val() || '';
    var title = $('#myModal h4').text();
    var data = new FormData();

    if (widget == ' ' || !widget) {
        alert('请输入控件名称');
        return;
    }

    var fileObj1 = document.getElementById("fileHide1").files[0];
    var fileVal1 = $("#fileShow1").val();
    var fileObj2 = document.getElementById("fileHide2").files[0];
    var fileVal2 = $("#fileShow2").val();

    if(title === '添加'){
        if (fileVal1 == ' ' || !fileVal1) {
            alert('请上传正常状态图片');
            return;
        }
        if (fileVal2 == ' ' || !fileVal2) {
            alert('请上传焦点状态图片');
            return;
        }
    }

    if (fileVal1 != ' ' && fileVal1.indexOf('http') == -1 && fileVal1) {
        data.append("normalFile", fileObj1);
    }
    if (fileVal2 != ' ' && fileVal2.indexOf('http') == -1 && fileVal2) {
        data.append("forcusFile", fileObj2);
    }
    var extraData = {};
    if(title === '添加'){
        extraData = {
            "name": widget
        };
        data.append("extraData", JSON.stringify(extraData));
        AjaxFile('/desktop/addIcon', data, function() {
            $('#myModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        extraData = {
            "name": widget,
            "id": myData.widgetId
        };
        data.append("extraData", JSON.stringify(extraData));
        AjaxFile('/desktop/modifyIcon', data, function() {
            $('#myModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.normalPath, arr.forcusPath]);
    }
    $('#myTable').dataTable({
        "lengthChange": false,
        "autoWidth": false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "data": dataArr,
        "order": [
            [1, "desc"]
        ],
        "columnDefs": [{
            'title': 'ID',
            'width': '10%',
            'targets': 0
        }, {
            'title': '控件名称',
            'width': '15%',
            'targets': 1
        }, {
            'title': '正常状态图片路径',
            'width': '15%',
            'targets': 2
        }, {
            'title': '焦点状态图片路径',
            'width': '15%',
            'targets': 3
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(2)', nRow).html('<i class="glyphicon glyphicon-picture icon-black my-icon" data-src="' + aData[2] + '"></i>').addClass('center');
            $('td:eq(3)', nRow).html('<i class="glyphicon glyphicon-picture icon-black my-icon" data-src="' + aData[3] + '"></i>').addClass('center');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable');
}