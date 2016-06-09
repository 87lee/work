//@ sourceURL=live.setAuth.js

// 直播系统_添加直播授权
//     	 * post /Live/addLiveAuth
//     	 * {
//     	 * 	"model":"型号",
//     	 * 	"num":"数量"
//     	 * }
// 直播系统_修改直播授权设置
//     	 * post /Live/modifyLiveAuth
//     	 * {
//     	 * 	"id":"直播授权ID",
//     	 * 	"model":"厂商",
//     	 * 	"num":"数量"
//     	 * }
// 直播系统_删除直播授权设置
//     	 * post /Live/deleteLiveAuth
//     	 * ["id1","id2"]
// 直播系统_直播授权列表
//     	 * get /Live/liveAuthLists?page=x&pageSize=x&name=x

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/Live/liveAuthLists?page=1&pageSize=' + pageSize, function(data) {
        createAuth(data, 1);
        trHover('#authTable');
    });

    trclick('#authTable', function(obj, e) {
        myData.authId = obj.data('id');
        myData.amount = obj.data('amount');
        myData.time = obj.data('time');
        myData.model = obj.data('model');
        myData.vendorID = obj.data('vendorID');
    });

    listenchoose();
    listenMyPage('authTable', currentPage);
});

listenToolbar('add', addAuth, '#authTable');
listenToolbar('edit', editAuth, '#authTable');
listenToolbar('del', delAuth, '#authTable');
listenToolbar('watch', watchAuth, '#authTable');

function addAuth() {
	$('#authModal h4').text('新增');
	$('#authID').parent().hide();
	$('#authID').val('');
	$('#authModel').val('');
	$('#authSum').val('');
    $('#authVendorID').val('');
	$('#authModal').modal('show');
}

function editAuth() {
	$('#authModal h4').text('修改');
	$('#authID').parent().show();
	$('#authID').val(myData.authId);
	$('#authModel').val(myData.model);
	$('#authSum').val(myData.amount);
    $('#authVendorID').val(myData.vendorID);
	$('#authModal').modal('show');
}

function delAuth() {
    if( confirm('确定删除？') ){
        AjaxPost('/Live/deleteLiveAuth', [myData.authId], function() {
            var filter = $('#authTable_filter input').val() || '';
            updateTable(currentPage, filter);
        });
    }
}

function watchAuth() {
	
}

function updateTable(page, name){
    AjaxGet('/Live/liveAuthLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createAuth(data, page);
        myData.authId = null;
    });
}

$('#chooseModel > input').on('click', function(){   //全型号和自定义
    $(this).prop('checked');
    var val = $(this).val();
    if(val == "ALL"){
        $('#modelName').val(val);
        $('#modelName').parent().hide();
    }else{
        $('#modelName').val('');
        $('#modelName').parent().show();
    }
});

$('#chooseType > input').on('change', function(){     //内测、灰度、公开
    var $this = $(this);
    $this.prop('checked', true);
    var val = $this.val();
    if(val == "group"){
        $('#group').parent().show();
        $('#countNum').parent().hide();
    }else if(val == "AB"){
        $('#countNum').parent().show();
        $('#group').parent().hide();
    }else if(val == "ALL"){
        $('#group').parent().hide();
        $('#countNum').parent().hide();
    }
});

//创建内测包下拉框
function selectGroup(data, $obj){
    var arr = data.groups;
    var con = '';
    var $select = $obj || $('#group');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
    }
    $select.html(con);
}

$('#subAuth').on('click', function(){
	if ($('#authModel').val() === '') {
		alert('请输入型号');
		return;
	}
	if ($('#authSum').val() === '') {
		alert('请输入数量');
		return;
	}
    if ($('#authVendorID').val() === '') {
        alert('请输入vendorID');
        return;
    }
	if ($('#authModal h4').text() === '新增') {
    	var data = {
    		"model": $('#authModel').val(),
    		"amount": $('#authSum').val(),
            "vendorID": $('#authVendorID').val()
    	};
    	AjaxPost('/Live/addLiveAuth', data, function() {
    		var filter = $('#authTable_filter input').val() || '';
    		updateTable(currentPage, filter);
    	});
	}else{
		var data = {
			"id": myData.authId,
    		"model": $('#authModel').val(),
    		"amount": $('#authSum').val(),
            "vendorID": $('#authVendorID').val()
    	};
    	AjaxPost('/Live/modifyLiveAuth', data, function() {
    		var filter = $('#authTable_filter input').val() || '';
    		updateTable(currentPage, filter);
    	});
	}
    $('#authModal').modal('hide');
});

$('#subEdit').on('click', function(){
    var title = $('#editModal h4').text();

    var filter = $('#startupTable_filter input').val() || '';
    var data = {"id": myData.authId};

    if(title === '内测'){
        data.groupId = $('#customGroup').val();
        data.type = 'group';
    }else if(title === '灰度'){
        var countNum = $('#customCountNum').val();
        if(countNum == ' ' || !countNum){
            alert('请输入灰度数量');
            return;
        }else if(/\D/.test(countNum)){
            alert('灰度数量只能为数字');
            return;
        }
        data.AB = countNum;
        data.type = 'AB';
    }else if(title === '公开'){
        data.type = 'ALL';
    }

    AjaxPost('/Live/modifyLiveStartupPic', data, function(){
        updateTable(currentPage, filter);
        $('#editModal').modal('hide');
    });
});

//创建发布列表
function createAuth(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.model, arr.vendorID, arr.amount,arr.num , arr.time]);
    }
    $('#authTable').dataTable({
        "lengthChange": false,
        "autoWidth": false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "stateSave": false,
        "data": dataArr,
        "order": [
            [0, "desc"]
        ],
        "columnDefs": [
            {'title':'id','width':'8%', 'targets':0},
            {'title':'型号','width':'10%', 'targets':1},
            {'title':'vendorID','width':'10%', 'targets':2},
            {'title':'预授权数量','width':'10%', 'targets':3},
            {'title':'已授权数量','width':'10%', 'targets':4},
            {'title':'时间','width':'7%', 'targets':5}
        ],
        "createdRow": function( nRow, aData, idx ){
            $('td:eq(0)', nRow).data({
                "amount": aData[3],
                "id": aData[0],
                "model": aData[1],
                "time": aData[5],
                "vendorID": aData[2],
                "num": aData[4]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'authTable');
    initToolBar('#authTable', [
        myConfig.addBtn,
        myConfig.editBtn,
        myConfig.delBtn,
        /*'<a class="btn my-btn btn-primary watchBtn" href="javascript:">&nbsp;查看</a>'*/
    ]);
}
