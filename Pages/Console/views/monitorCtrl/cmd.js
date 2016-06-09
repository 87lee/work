//@ sourceURL=monitorCtrl.cmd.js
var myData = {};
$(function () {
	AjaxGet('/monitorCtrl/cmdLists', function(data){
        createElem(data.extra);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.id = obj.data('id');
    });

    listenchoose();
});

function updateTable(){
    AjaxGet('/monitorCtrl/cmdLists', function(data){
        createElem(data.extra);
        myData.id = null;
    });
}

$('#cmdType > input').change(function(){
	$(this).prop('checked');
	var val = $(this).val();
    if(val == "shell"){
        $('#shell').parent().show();
        $('#upload').parent().hide();
    }else if(val == "upload"){
        $('#upload').parent().show();
        $('#shell').parent().hide();
    }
});

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.cmd_type, arr.shell, arr.upload_file, arr.time]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "order": [[4, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'8%', 'targets':0},
            {'title':'命令类型','width':'8%', 'targets':1},
            {'title':'shell命令','width':'10%', 'targets':2},
            {'title':'文件路径','width':'10%', 'targets':3},
            {'title':'时间','width':'15%', 'targets':4},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
			if(!aData[2])
				$('td:eq(2)', nRow).html('--');
            if(!aData[3])
            	$('td:eq(3)', nRow).html('--');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
            });
        }
    });
    initToolBar('#myTable', [myConfig.addBtn, myConfig.delBtn]);
}

listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function addTableInfo(){
    $('#shell').val("");
    $('#upload').val("");
    $('#myModal').find('h4').html('新增');
    $('#myModal').modal('show');
}

function delTableInfo(){
    if(myData.id){
        if( confirm('确定删除？') ){
            AjaxGet('/monitorCtrl/deleteCmd?id=' + myData.id, updateTable);
        }
    }else{
        alert('请选择版本！');
    }
}

$('#subCmd').click(function() {
	var type = $('#cmdType').find('input:checked').val();
    var data = {};

    if(type === 'shell'){
    	var shell = $('#shell').val();
    	data = {'cmd_type': type, 'shell': shell};
    }else if(type === 'upload'){
    	var upload = $('#upload').val();
    	data = {'cmd_type': type, 'upload_file': upload};
    }

    AjaxPost('/monitorCtrl/addCmd', data, function(){
        $('#myModal').modal('hide');
        updateTable();
    });
});