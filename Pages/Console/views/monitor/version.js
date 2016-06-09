//@ sourceURL=monitor.version.js
var myData = {};
$(function () {
	AjaxGet('/monitor/versionLists', function(data){
        createElem(data.content);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.id = obj.data('id');
        myData.desc = obj.data('desc');
        myData.path = obj.data('path');

        var tar = e.target;
        showTableDetail({
            "obj": obj,
            "tar": tar,
            "fn": function(row, tr){
                row.child(showDesc(myData.desc)).show();
                tar.className = 'glyphicon glyphicon-minus icon-black my-icon';
                tr.addClass('shown');
            }
        });
    });

    $('#myTable').on('click', 'a', function(ev){
        var e = ev || event;
        var tar = e.target;
        window.open(tar.href);
        return false;
    });

    listenfile();
});

function updateTable(){
    AjaxGet('/monitor/versionLists', function(data){
        createElem(data.content);
        myData.id = null;
    });
}

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.version, arr.md5, arr.download, arr.desc, arr.time]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "order": [[5, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'8%', 'targets':0},
            {'title':'版本','width':'8%', 'targets':1},
            {'title':'MD5','width':'20%', 'targets':2},
            {'title':'下载','width':'8%', 'targets':3},
            {'title':'版本描述','width':'8%', 'targets':4},
            {'title':'时间','width':'15%', 'targets':5},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
        	var upload = '否';
            tableTdDownload(3, nRow, aData[3]);
            tableTdIcon(4, nRow, 'plus');

            $('td:eq(0)', nRow).data({
                "id":aData[1],
                "path":aData[3],
                "desc":aData[4]
            });
        }
    });
    initToolBar('#myTable', [myConfig.addBtn, myConfig.delBtn]);
}

listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function addTableInfo(){
    $('#version').val("");
    $('#desc').val("");
    $('#myModal').find('h4').html('新增');
    $('#myModal').modal('show');
    $('#fileShow').val("");
    $('#fileHide').val("");
}

function delTableInfo(){
    if(myData.id){
        if( confirm('确定删除？') ){
            AjaxGet('/monitor/deleteVersion?id=' + myData.id, updateTable);
        }
    }else{
        alert('请选择版本！');
    }
}

$('#subVersion').click(function() {
    var version = $('#version').val();
    var desc = $('#desc').val();
    var data = new FormData();

    if(version == ' ' || !version){
        alert('请输入版本');
        return;
    }

    if(/\D/.test(version)){
        alert('版本只能为数字');
        return;
    }

    if(desc == ' ' || !desc){
        alert('请输入描述');
        return;
    }

    var fileObj = document.getElementById("fileHide").files[0];
    var fileVal = $("#fileShow").val();

    if(fileVal != ' ' && fileVal.indexOf('http') == -1){
        data.append("fileName", fileObj);
    }
    if(fileVal == ' ' || !fileVal){
        alert('请选择要提交的文件');
        return;
    }
    var extra_data = '{"version":"'+version+'", "desc":"'+desc+'"}';
    data.append("extra_data", extra_data);

    AjaxFile('/monitor/addVersion', data, function(){
        $('#myModal').modal('hide');
        updateTable();
    });
});