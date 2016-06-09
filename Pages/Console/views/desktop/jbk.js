//@ sourceURL=desktop.jbk.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/desktop/jbkLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e) {
        myData.jbkId = obj.data('id');
        myData.jbkVendorid = obj.data('vendorid');
        myData.jbkPasswd = obj.data('passwd');
        myData.jbkDesc = obj.data('desc');
    });

    listenMyPage();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);
listenToolbar('state', stateTableInfo);

function addTableInfo() {
    $('#jbkVendorId').val('');
    $('#jbkPasswd').val('');
    $('#jbkDesc').val('');
    $('#jbkModal h4').text('添加');
    $('#jbkModal').modal('show');
}

function editTableInfo() {
	if (myData.jbkId) {
		$('#jbkVendorId').val(myData.jbkVendorid);
	    $('#jbkPasswd').val(myData.jbkPasswd);
	    $('#jbkDesc').val(myData.jbkDesc);
	    $('#jbkModal h4').text('修改');
	    $('#jbkModal').modal('show');
	} else {
		alert('请选择内容！');
	}
}

function delTableInfo() {
    if (myData.jbkId) {
        if (confirm('确定删除？')) {
        	var filter = $('#myTable_filter input').val() || '';
            AjaxGet('/desktop/deleteJbk?id=' + myData.jbkId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function stateTableInfo() {
    $('#stateMac').val('');
    $('#myMac i').remove();
    $('#stateModal').modal('show');
}

function updateTable(page, name){
    name = name || '';
    AjaxGet('/desktop/jbkLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createElem(data, page);
        myData.jbkId = null;
    });
}

$('#subState').on('click', function(){
    var stateMac = $('#stateMac').val();

    if(stateMac == ' ' || !stateMac){
        alert('请输入mac地址');
        return false;
    }

    $('#myMac i').remove();

    AjaxGet('/desktop/jbkLists?mac=' + stateMac.toUpperCase(), function (data) {
        if(!data.extra.jailbreak){
            $('#myMac').append('<i class="ace-icon glyphicon glyphicon-remove red">未越狱</i>');
        }else{
            $('#myMac').append('<i class="ace-icon glyphicon glyphicon-ok green">已越狱</i>');
        }
    });
});

$('#subJbk').on('click', function(){
	var vendorid = $('#jbkVendorId').val();
	var passwd = $('#jbkPasswd').val();
	var desc = $('#jbkDesc').val() || '';
	var title = $('#jbkModal h4').text();
	var filter = $('#myTable_filter input').val() || '';

	if(vendorid == ' ' || !vendorid){
		alert('请输入客户ID');
		return false;
	}
	if(passwd == ' ' || !passwd){
		alert('请输入密码');
		return false;
	}


	var data = {
		"vendorid": vendorid,
		"passwd": passwd,
		"desc": desc
	};

	if(title === '添加'){
		AjaxPost('/desktop/addJbk', data, function(){
			$('#jbkModal').modal('hide');
            updateTable(currentPage, filter);
		});
	}else if(title === '修改'){
		data.id = myData.jbkId;
		AjaxPost('/desktop/modifyJbk', data, function(){
			$('#jbkModal').modal('hide');
            updateTable(currentPage, filter);
		});
	}
});

//创建桌面列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.vendorid, arr.passwd, arr.desc + '--' + arr.id]);
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
            [0, "desc"]
        ],
        "columnDefs": [
        {
            'title': '客户ID',
            'width': '15%',
            'targets': 0
        },{
            'title': '密码',
            'width': '16%',
            'targets': 1
        },{
            'title': '描述',
            'width': '16%',
            'targets': 2
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            var temp = aData[2].split('--');
            $('td:eq(2)', nRow).html(temp[0]);
            $('td:eq(0)', nRow).data({
                "id": temp[1],
                "vendorid": aData[0],
                "passwd": aData[1],
                "desc": temp[0],
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable', [
        myConfig.addBtn,
        myConfig.editBtn,
        myConfig.delBtn,
        '<a class="btn my-btn btn-primary stateBtn" href="javascript:"><i class="fa fa-search icon-white"></i>&nbsp;查询越狱状态</a>'
    ]);
}