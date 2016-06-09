//@ sourceURL=download.url.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	 myData.checkedLists = [];   //存储check选中项
	 var data = {};

	 AjaxGet('/Monitoring/Home/Domain/domainLists?page=1&pageSize=' + pageSize, function(data){
         createDomain(data, 1);
     });

     listenSingleCheckBox('#domainTable', function(e){
     });

   //  $('#bDetailTable').on('click', 'tbody tr', function(ev) {
   //  	var e = ev || event;
   //  	var tar = e.target;
   //  	var obj = $(this).children().eq(0);
   //  	myData.detail = obj.data('detail');

   //      if( tar.className.indexOf('detail-btn') != -1){
   //          AjaxGet('/Android/Home/Base/relyModuleLists?' + myData.detail, function(data){
   //          	if(!data.extra.relyModule){
   //          		alert('此版本模块未发布！');
   //          		return false;
   //          	}
			// 	createDetail(data);
			// 	createRely(data);
			// });
   //      }
   //      return false;
   //  });

 //    checkMoz();
	// listenfile();
	listenMyPage('domainTable', currentPage);
});

listenToolbar('add', addDomain, '#domainTable');
listenToolbar('del', delDomain, '#domainTable');
listenToolbar('edit', editDomain, '#domainTable');
// listenToolbar('say', sayXml, '#bVersionTable');

function addDomain(){
	$('#domainName').val('');
	$('#domainUrl').val('');
	$('#domainDesc').val('');
	$('#domainModal h4').html('新增');
	$('#domainModal .error-info').text('');
	$('#domainModal').modal('show');
}

function delDomain(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#domainTable_filter input').val() || '';
			AjaxPost('/Monitoring/Home/Domain/deleteDomain', myData.checkedLists, function () {
				updateTable(currentPage, filter);
				return;
			});
		}
	}else{
		alert('请选择模块！');
		return;
	}
}

function editDomain(){
	if(myData.checkedLists.length === 1){
		var obj = $('.checkSelected td:eq(0)');
		myData.domainId = obj.data('id');
		myData.domainName = obj.data('name');
		myData.domainUrl = obj.data('url');
		myData.domainDesc = obj.data('desc');
		$('#domainName').val(myData.domainName);
		$('#domainUrl').val(myData.domainUrl);
		$('#domainDesc').val(myData.domainDesc);
		$('#domainModal h4').html('修改');
		$('#domainModal .error-info').text('');
		$('#domainModal').modal('show');
	}else{
		alert('请选择一个模块！');
		return;
	}
}

function updateTable(page, name){
	name = name || '';
	AjaxGet('/Monitoring/Home/Domain/domainLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedLists = [];
		myData.domainId = null;
		createDomain(data, page);
	});
}

$('#subDomain').on('click', function(){
    var domainName = $("#domainName").val();
    var domainUrl = $('#domainUrl').val();
    var domainDesc = $('#domainDesc').val();

    var filter = $('#domainTable_filter input').val() || '';
    var $errorInfo = $('#domainModal .error-info');
    var data = {
    	'name': domainName,
    	'url': domainUrl,
    	'desc': domainDesc
    };

    if(domainName == ' ' || !domainName){
        $errorInfo.text('请输入名称');
        return;
    }
    if(domainUrl == ' ' || !domainUrl){
        $errorInfo.text('请输入Url');
        return;
    }

    if ($('#domainModal h4').html() === '新增') {
    	AjaxPostWithError('/Monitoring/home/Domain/addDomain', data, function(data) {
    		$('#domainModal').modal('hide');
        	updateTable(currentPage, filter);
    	}, $errorInfo);
    }
    if ($('#domainModal h4').html() === '修改') {
    	data.id = myData.domainId;
    	AjaxPostWithError('/Monitoring/home/Domain/modifyDomain', data, function(data) {
    		$('#domainModal').modal('hide');
        	updateTable(currentPage, filter);
    	}, $errorInfo);
    }

});

// function createDomain(data, page){
// 	var dataArr = [];
//     var len = data.extra.length;
//     for( var i=0; i<len; i++ ) {
//         var arr = data.extra[i];
//         dataArr.push([arr.id, arr.name, arr.url, arr.desc]);
//     }
//     myDataTable('#domainTable', {
//         "data": dataArr,
//         "order": [[1, "desc"]],
// 		"columnDefs": [
//             {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
//             {'title':'名称','width':'15%', 'targets':1},
//             {'title':'url','width':'20%', 'targets':2}
//         ],
// 		"createdRow": function(nRow, aData, iDataIndex) {
// 			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
// 			$('td:eq(0)', nRow).data({
// 				"id": aData[0]
// 			});
// 		}
// 	});

// 	var toolbar = [];

// 	// var power = window.localStorage.getItem("ANDROID_PERMISSION_USEPOWER");
// 	// if(power.indexOf('发布用户') !== -1){
// 	// 	toolbar.push('<a class="btn my-btn publishBtn" href="javascript:"><i class="iconfont icon-release"></i>&nbsp;发布</a>');
// 	// }
// 	// if(power.indexOf('系统管理员') !== -1){
// 	// 	toolbar.push(myConfig.delBtn);
// 	// }
// 	toolbar.push('<a class="btn my-btn detailBtn" href="javascript:"><i class="iconfont icon-bjibenxinxi"></i>&nbsp;详情</a>');
// 	toolbar.push('<a class="btn my-btn sayBtn" href="javascript:"><i class="iconfont icon-pinglun"></i>&nbsp;评论</a>');
//   	initToolBar('#domainTable', toolbar);
// 	updatePagination(len, page, data.count, 'domainTable');
// 	listenCheckBox('#domainTable');
//     updateChecked('#domainTable');
// }

function createDomain(data, page) {
	var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.url, arr.desc]);
    }
    $('#domainTable').dataTable({
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
        	{'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'名称','width':'15%', 'targets':1},
            {'title':'url','width':'20%', 'targets':2},
            {'title':'备注','width':'20%', 'targets':3}

        ],
        "createdRow": function(nRow, aData, iDataIndex) {
//            tableTdIcon(3, nRow, 'list');
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
                "url": aData[2],
                "desc": aData[3]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'domainTable');
    listenCheckBox('#domainTable');
    updateChecked('#domainTable');
    initToolBar('#domainTable');
}