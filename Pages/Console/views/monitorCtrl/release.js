//@ sourceURL=monitorCtrl.release.js
var myData = {};
$(function () {
	AjaxGet('/monitorCtrl/cmdLists', cmdSelect);
    releaseOpt();
	AjaxGet('/monitorCtrl/PublishLists', function(data){
        createElem(data.extra);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.id = obj.data('id');
        myData.model = obj.data('model');
        myData.vendorID = obj.data('vendorID');
        myData.type = obj.data('type');

        var tar = e.target;
	    if(tar.className.indexOf('glyphicon-list') != -1){
            releaseSN('/monitorCtrl/PublishLists?id=');
	        return;
	    }
    });

    listenchoose();
});

$('#cmdType > input').change(function(){
    $(this).prop('checked');
    var val = $(this).val();
    if(val == "shell"){
        $('#shell').parent().show();
        $('#upload').parent().hide();
        $('#shell').trigger('change');
    }else if(val == "upload"){
        $('#upload').parent().show();
        $('#shell').parent().hide();
        $('#upload').trigger('change');
    }
});

function cmdSelect(data){
    var arr = data.extra;
    var con1 = '';
    var con2 = '';
    var $select1 = $('#shell');
    var $select2 = $('#upload');

    for( var i=0; i<arr.length; i++ ){
        if(arr[i].cmd_type === 'shell'){
            con1 += '<option value="'+arr[i].id+'">'+arr[i].shell+'</option>';
            $select1.data('ver'+arr[i].id, arr[i].time);
        }else if(arr[i].cmd_type === 'upload'){
            con2 += '<option value="'+arr[i].id+'">'+arr[i].upload_file+'</option>';
            $select2.data('ver'+arr[i].id, arr[i].time);
        }
    }
    $select1.html(con1).trigger('change');
    $select2.html(con2);
}

$('#shell').change(function(){
    var val = $(this).val();
    var time = $(this).data('ver'+val);
    var $verTime = $('#verTime');
    $verTime.val(time);
});

$('#upload').change(function(){
    var val = $(this).val();
    var time = $(this).data('ver'+val);
    var $verTime = $('#verTime');
    $verTime.val(time);
});

function updateTable(){
    AjaxGet('/monitorCtrl/PublishLists', function(data){
        createElem(data.extra);
        myData.id = null;
    });
}

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.target.model, arr.target.vendorID, arr.target.type, null, arr.content.cmd_type, arr.content.shell, arr.content.upload_file, arr.time]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "order": [[8, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'型号','width':'10%', 'targets':1},
            {'title':'VendorID','width':'10%', 'targets':2},
            {'title':'类型','width':'10%', 'targets':3},
            {'title':'设备列表','width':'10%', 'targets':4},
            {'title':'命令类型','width':'10%', 'targets':5},
            {'title':'shell命令','width':'10%', 'targets':6},
            {'title':'文件路径','width':'10%', 'targets':7},
            {'title':'时间','width':'12%', 'targets':8}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTypeColor(3, nRow, aData[3]);
            tableTdIcon(4, nRow, 'list');
            if(!aData[6])
                $('td:eq(6)', nRow).html('--');
            if(!aData[7])
                $('td:eq(7)', nRow).html('--');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "model":aData[1],
                "vendorID":aData[2],
                "type":aData[3]
            });
        }
    });
    initToolBar('#myTable', [myConfig.releaseBtn, myConfig.underBtn]);
}

releaseTool('/monitorCtrl/deletePublish?id=');

$('#subVersion').click(function(){
	var model = $('#model').val();
	var vendorID = $('#vendorID').val() || 'none';
	var type = $('#chooseType').find('input:checked').val();
    var cmdType = $('#cmdType').find('input:checked').val();
    var cmd_id = '';

    if(model == ' ' || !model){
    	alert('请输入型号');
    	return;
    }
    if(cmdType === 'shell'){
        cmd_id = $('#shell').val();
    }else if(cmdType === 'upload'){
        cmd_id = $('#upload').val();
    }
    var data = {"model": model, "vendorID": vendorID, "type": type, "cmd_id": cmd_id};
	if(type == 'group') {
        var group = $('#group').val();
        data.group = group;
    } else if (type == 'AB') {
        var countNum = $('#countNum').val();
        if(countNum == ' ' || !countNum){
	    	alert('请输入灰度数量');
	    	return;
	    }else if(/\D/.test(countNum)){
	        alert('灰度数量只能为数字');
	        return;
	    }
	    data.AB = countNum;
    }

    AjaxPost('/monitorCtrl/addPublish', data, function(){
    	$('#myModal').modal('hide');
    	updateTable();
    });
});