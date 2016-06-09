//@ sourceURL=monitor.release.js
var myData = {};
$(function () {
    releaseOpt({"version": "/monitor/versionLists"});
	AjaxGet('/monitor/PublishLists', function(data){
        createElem(data.extra);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.id = obj.data('id');
        myData.model = obj.data('model');
        myData.vendorID = obj.data('vendorID');
        myData.type = obj.data('type');
        myData.desc = obj.data('desc');
        myData.download = obj.data('download');

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

	    if(tar.className.indexOf('glyphicon-list') != -1){
            releaseSN('/monitor/PublishLists?id=');
	        return;
	    }
    });

    $('#myTable').on('click', 'a', function(ev){
        var e = ev || event;
        var tar = e.target;
        window.open(tar.href);
        return false;
    });

    listenchoose();
});

function updateTable(){
    AjaxGet('/monitor/PublishLists', function(data){
        createElem(data.extra);
        myData.id = null;
    });
}

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.target.model, arr.target.vendorID, arr.content.version, arr.target.type, null, arr.content.desc, arr.content.download, arr.time]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "order": [[8, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'8%', 'targets':0},
            {'title':'型号','width':'8%', 'targets':1},
            {'title':'VendorID','width':'8%', 'targets':2},
            {'title':'版本','width':'10%', 'targets':3},
            {'title':'类型','width':'8%', 'targets':4},
            {'title':'设备列表','width':'8%', 'targets':5},
            {'title':'描述','width':'8%', 'targets':6},
            {'title':'下载','width':'8%', 'targets':7},
            {'title':'时间','width':'12%', 'targets':8}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
        	tableTypeColor(4, nRow, aData[4]);
        	tableTdIcon(5, nRow, 'list');
        	tableTdIcon(6, nRow, 'plus');
            tableTdDownload(7, nRow, aData[7]);
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "model":aData[1],
                "vendorID":aData[2],
                "type":aData[4],
                "desc":aData[6],
                "download":aData[7]
            });
        }
    });
    initToolBar('#myTable', [myConfig.releaseBtn, myConfig.underBtn]);
}

releaseTool('/monitor/deletePublish?id=');

$('#subVersion').click(function(){
	var model = $('#model').val();
	var vendorID = $('#vendorID').val() || 'none';
	var version = $('#version').val();
	var type = $('#chooseType').find('input:checked').val();

    if(model == ' ' || !model){
    	alert('请输入型号');
    	return;
    }
    var data = {"model": model, "vendorID": vendorID, "type": type, "version": version};
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

    AjaxPost('/monitor/addPublish', data, function(){
    	$('#myModal').modal('hide');
    	updateTable();
    });
});