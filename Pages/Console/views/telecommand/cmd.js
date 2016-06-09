//@ sourceURL=telecommand.CMD.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
$(function () {
	AjaxGet('/desktop/cmdLineLists?page=1&pageSize=' + pageSize, function(data){
        createElem(data, 1);
        trHover('#cmdGroupTable');
    });

    trclick('#cmdGroupTable', function(obj, e) {
        myData.cmdId = obj.data('id');
        myData.cmdName = obj.data('name');
        myData.cmdList = obj.data('cmd');

        var tar = e.target;

        if(tar.className.indexOf('glyphicon-list cmd-list') != -1){
            $('#cmdListTable').val(myData.cmdList.join('\r\n'));
            $('#cmdListModal').modal('show');
            return;
        }
    });

    listenMyPage('cmdGroupTable', currentPage);
});

listenToolbar('add', addTableInfo, '#cmdGroupTable');
listenToolbar('edit', editTableInfo, '#cmdGroupTable');
listenToolbar('del', delTableInfo, '#cmdGroupTable');

function addTableInfo(){
	$('#cmdName').val('');
	$('#cmdTages').val('');
	$('#cmdModal h4').text('添加');
	$('#cmdModal').modal('show');
}

function editTableInfo(){
	if(myData.cmdId){
		$('#cmdName').val(myData.cmdName);
		$('#cmdTages').val(myData.cmdList.join('\r\n'));
		$('#cmdModal h4').text('修改');
		$('#cmdModal').modal('show');
	}else{
		alert('请选择命令！');
	}
}

function delTableInfo(){
    if(myData.cmdId){
        if (confirm('确定删除？')) {
        	var filter = $('#cmdGroupTable_filter input').val() || '';
            AjaxGet('/desktop/deleteCmdLine?id=' + myData.cmdId, function(){
                updateTable(currentPage, filter);
            });
        }
    }else{
        alert('请选择命令！');
        return false;
    }
}

function clearTableInfo(){
    $('#desktopId').val('');
    $('#cmdTages').val('');
    $('#vendorId').val('');
    $('#chooseType input:eq(0)').trigger('click');
    $('#chooseRange input:eq(0)').trigger('click');
}

function updateTable(page, name){
    AjaxGet('/desktop/cmdLineLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.cmdId = null;
    });
}

function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.cmd]);
    }
    $('#cmdGroupTable').dataTable({
        "lengthChange": false,
        "autoWidth": false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "data": dataArr,
        "order": [[1, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'8%', 'targets':0},
            {'title':'命令名称','width':'40%', 'targets':1},
            {'title':'命令列表','width':'40%', 'targets':2}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(2, nRow, 'list cmd-list');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
                "cmd": aData[2]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'cmdGroupTable');
    initToolBar('#cmdGroupTable');
}

$('#subCmd').click(function(){
    var name = $('#cmdName').val();
    var cmdTages = $('#cmdTages').val();
    var title = $('#cmdModal h4').text();
    var filter = $('#cmdGroupTable_filter input').val() || '';
    var data = {};

    if(name == ' ' || !name){
        alert('请输入命令名称');
        return false;
    }

    if(cmdTages == ' ' || !cmdTages){
        alert('请输入命令');
        return false;
    }
    var cmd = cmdTages.split(/[\r\n]/);
    var cmdNow = [];

    for(var i = 0, len = cmd.length; i < len; i++){
        var val = cmd[i].trim();
        if(val == ' ' || !val){
            continue;
        }
        cmdNow.push(val);
    }
    data = {"name": name, "cmd": cmdNow};
    if(title === '添加'){
    	AjaxPost('/desktop/addCmdLine', data, function(){
	        updateTable(currentPage, filter);
	        $('#cmdModal').modal('hide');
	    });
    }else if(title === '修改'){
    	data.id = myData.cmdId;
    	AjaxPost('/desktop/modifyCmdLine', data, function(){
	        updateTable(currentPage, filter);
	        $('#cmdModal').modal('hide');
	    });
    }
});