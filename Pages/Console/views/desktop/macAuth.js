//@ sourceURL=desktop.macAuth.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
    myData.checkedLists = [];   //存储check选中项

	AjaxGet('/desktop/desktopMacBlackLists?page=1&pageSize=' + pageSize, function(data) {
        createMac(data, 1);
        trHover('#macTable');
    });

    listenSingleCheckBox('#macTable');
    listenMyPage('macTable', currentPage);
    listenchoose();
    listenfile();
    $('.mac-name').autotab();
});

listenToolbar('edit', editTableInfo, '#macTable');
listenToolbar('add', addTableInfo, '#macTable');
listenToolbar('del', delTableInfo, '#macTable');

function addTableInfo(){
	$('.mac-name').val('');
	$('#desc').val('');
	$('#importType input:eq(0)').trigger('click');
	$('#importType').show();
    $('#macModal h4').text('添加');
    $('#macModal').modal('show');
}

function editTableInfo(){
	if(myData.checkedLists.length === 1){
		var obj = $('.checkSelected td:eq(0)');
        myData.macId = obj.data('id');
        myData.macItems = obj.data('mac');
        myData.desc = obj.data('desc');
		var macObjs = $('.mac-name');
		var macItems = myData.macItems.split(':');
		for(var i = 0, len = macItems.length; i < len; i++){
			$(macObjs[i]).val(macItems[i]);
		}
		$('#desc').val(myData.desc);
		$('#importType input:eq(0)').trigger('click');
		$('#importType').hide();
	    $('#macModal h4').text('修改');
	    $('#macModal').modal('show');
	}else{
		alert('请选择一个mac！');
		return;
	}
}

function delTableInfo() {
    if (myData.checkedLists.length > 0) {
        if (confirm('确定删除？')) {
            var filter = $('#myTable_filter input').val() || '';
            AjaxPost('/desktop/deleteDesktopMacBlack', myData.checkedLists, function() {
                updateTable(currentPage, filter);
                myData.checkedLists = [];
            });
        }
    } else {
        alert('请选择mac！');
    }
}

function updateTable(page, name){
	name = name || '';
	AjaxGet('/desktop/desktopMacBlackLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedLists = [];
		myData.macId = null;
		createMac(data, page);
	});
}


$('#importType input').on('click', function(){
	var $this = $(this);
	var val = $this.val();
	$('#importType').siblings('.form-group').hide();
	if(val === 'true'){
		$('#importType').siblings('.import-true').show();
	}else if(val === 'false'){
		$('#importType').siblings('.import-false').show();
	}
});

$('#subMac').on('click', function(){
	var importType = $('#importType input:checked').val();
	var data = new FormData();
	var fileObj = document.getElementById("fileHide").files[0];
    var fileVal = $("#fileShow").val();
    var macName = $('.mac-name');
    var filter = $('#macTable_filter input').val() || '';
    var title = $('#macModal h4').text();
	if(importType === 'true'){
		if(fileVal != ' ' && fileVal.indexOf('http') == -1){
	        data.append("mac", fileObj);
	    }
	    if(fileVal == ' ' || !fileVal){
	        alert('请选择要上传的文件');
	        return;
	    }
	    AjaxFile('/desktop/addDesktopMacBlack', data, function(){
	        $('#macModal').modal('hide');
	        updateTable(currentPage, filter);
	    });
	}else if(importType === 'false'){
		var mac = [];
        for(var i = macName.length; i--;){
            var $mac = $(macName[i]);
            var val = $mac.val();
            if(val == ' ' || !val || val.length != 2){
                alert('Mac格式不正确');
                return;
            }
            mac.unshift(val);
        }
        var desc = $('#desc').val() || '';
        data = {"mac": mac.join(':'), "desc": desc};
        if(title === '添加'){
        	AjaxPost('/desktop/addDesktopMacBlack', data, function(){
		        $('#macModal').modal('hide');
		        updateTable(currentPage, filter);
		    });
        }else if(title === '修改'){
        	data.id = myData.macId;
        	AjaxPost('/desktop/modifyDesktopMacBlack', data, function(){
		        $('#macModal').modal('hide');
		        updateTable(currentPage, filter);
		    });
        }
	}
});

//创建应用列表
function createMac(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.mac, arr.desc, formatDate(arr.time)]);
    }
    $('#macTable').dataTable({
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
            [3, "desc"]
        ],
        "columnDefs": [
        {
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '8%',
            'targets': 0,
            "orderable": false
        },{
            'title': 'mac',
            'width': '40%',
            'targets': 1
        },{
            'title': '描述',
            'width': '40%',
            'targets': 2
        },{
            'title': '时间',
            'width': '12%',
            'targets': 3
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "mac": aData[1],
                "desc": aData[2]
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'macTable');
    initToolBar('#macTable');
    listenCheckBox('#macTable');
    updateChecked('#macTable');
}