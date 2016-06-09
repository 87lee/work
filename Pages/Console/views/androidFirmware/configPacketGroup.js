//@ sourceURL=androidFirmware.configPacketGroup.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {

	AjaxGet('/androidFirmware/configGroupLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.configId = obj.data('id');
        myData.configName = obj.data('name');
    });

    listenMyPage();
    listenfile();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);

function addTableInfo() {
    $('#configName').val('');
    $('#propShow').val('');
    $('#zipShow').val('');
    $('#propHide').val('');
    $('#zipHide').val('');

    $('#mp4Show').val('');
    $('#confShow').val('');
    $('#mp4Hide').val('');
    $('#confHide').val('');
    $('#configModal h4').text('添加');
    $('#configModal').modal('show');
}

function editTableInfo() {
	if (myData.configId) {
        $('#configName').val(myData.configName);
        $('#propShow').val('');
        $('#zipShow').val('');
        $('#propHide').val('');
        $('#zipHide').val('');

        $('#mp4Show').val('');
        $('#confShow').val('');
        $('#mp4Hide').val('');
        $('#confHide').val('');

    	$('#configModal h4').text('修改');
	    $('#configModal').modal('show');
	} else {
		alert('请选择包！');
	}
}

function delTableInfo() {
    if (myData.configId) {
        if (confirm('确定删除？')) {
        	var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/androidFirmware/deleteConfigGroup?id=' + myData.configId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择包！');
    }
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/androidFirmware/configGroupLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.configId = null;
    });
}

$('#subConfig').on('click', function(){
	var name = $('#configName').val();
	var title = $('#configModal h4').text();
	var filter = $('#myTable_filter input').val() || '';
    var data = new FormData();

	if(name == ' ' || !name){
		alert('请输入包名称');
		return false;
	}
    var extraData = {"name": name};

    var fileObj1 = document.getElementById("propHide").files[0];
    var fileVal1 = $("#propShow").val();
    var fileObj2 = document.getElementById("zipHide").files[0];
    var fileVal2 = $("#zipShow").val();
    var fileObj3 = document.getElementById("mp4Hide").files[0];
    var fileVal3 = $("#mp4Show").val();
    var fileObj4 = document.getElementById("confHide").files[0];
    var fileVal4 = $("#confShow").val();

    if(fileVal1 != ' ' && fileVal1.indexOf('http') == -1){
        data.append("prop", fileObj1);
    }
    if(fileVal2 != ' ' && fileVal2.indexOf('http') == -1){
        data.append("zip", fileObj2);
    }
    if(fileVal3 != ' ' && fileVal3.indexOf('http') == -1){
        data.append("mp4", fileObj3);
    }
    if(fileVal4 != ' ' && fileVal4.indexOf('http') == -1){
        data.append("conf", fileObj4);
    }

    if(title === '添加'){
    	data.append("extra", JSON.stringify(extraData));
    	AjaxFile('/androidFirmware/addConfigGroup', data, function(){
			$('#configModal').modal('hide');
	        updateTable(currentPage, filter);
		});
    }else if(title === '修改'){
    	extraData.id = myData.configId;
    	data.append("extra", JSON.stringify(extraData));
    	AjaxFile('/androidFirmware/modifyConfigGroup', data, function(){
			$('#configModal').modal('hide');
	        updateTable(currentPage, filter);
		});
    }
});

//创建包列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.md5, arr.path,arr.desktopId || '--']);
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
        "columnDefs": [
        {
            'title': 'ID',
            'width': '8%',
            'targets': 0
        },{
            'title': '包名称',
            'width': '16%',
            'targets': 1
        },{
            'title': 'MD5值',
            'width': '16%',
            'targets': 2
        },{
            'title': '链接',
            'width': '10%',
            'targets': 3
        },{
            'title': '桌面ID',
            'width': '10%',
            'targets': 4
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            if (aData[3]) {
                tableTdDownload(3, nRow, aData[3]);
            }else{
                tableTdNull(3, nRow);
            }
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