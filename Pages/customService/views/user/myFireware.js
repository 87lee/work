//@ sourceURL=user.myFireware.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var passPage = 1; //通过当前的页面
var nopassPage = 1; //未通过当前的页面
var currentPage = 1; //当前的页面

var orderTable = {
	"venid": 'vendorID',
    "pform": '平台',
    "firmv": '固件版本',
    "puber": '发布者',
    "time": '发布时间'
};

$(function(){
	initTopMenu(); 
    
	myData.checkedLists = [];
	AjaxGet('/Customer/Home/FirmwarePublish/getFirmwarePublish?page=1&pageSize=' + pageSize + '&cust=' + window.localStorage.getItem("CUSTOM_PERMISSION_USERNAME"), function(data){
		data.sort = 'time-desc';
        createFirewareList(data, 1);
        $('#main-content').css('visibility', 'visible');
        copyBtn(data);
    });

    listenSingleCheckBox('#firewareListTable');
    listenOrder('#firewareListTable', orderTable, updateFirewareTable);

    checkMoz();

	listenPage('firewareListTable', passPage, updateFirewareTable);
	$('#breadcrumb').css('min-width', '1090px');
	// listenTab(function(str){
	// 	$('.dataTables_filter input').val('');
 //    	if(str === '固件列表'){
 //    		$('#breadcrumb').css('min-width', '1090px');
 //    		passPage = 1;
 //    		updateFirewareTable(passPage);
 //    		$('.tab-list').hide();
 //    		$('.tab-list:eq(0)').show();
 //    	}else if(str === '平台列表'){
 //    		$('#breadcrumb').css('min-width', '');
 //    		nopassPage = 1;
	// 		updateTable(nopassPage);
 //    		$('.tab-list').hide();
 //    		$('.tab-list:eq(1)').show();
 //    	}else if(str === 'vendorID列表'){
 //    		$('#breadcrumb').css('min-width', '');
 //    		currentPage = 1;
	// 		updateTable(currentPage);
 //    		$('.tab-list').hide();
 //    		$('.tab-list:eq(2)').show();
 //    	}
 //    });

	listenTableAction('#firewareListTable');

	$("#publishTime").datetimepicker({
		minView: "day",
		format: 'yyyy-mm-dd hh:00',
		language: 'zh-CN',
		autoclose: true
	}).val('');
   
});

function updateFirewareTable(page, val, order){
	if (order === 1) {
		currentPage = 1;
		order = 0;
	}
	var url = '';
	var type = $('#breadcrumb > span.active').text();
	if (!val) {
    	val = {
        		"vendorID": '',
        		"platform": '',
        		"fireware": '',
        		"time": '',
        		"sort": ''
        	};
    }else{
    	if (!val.vendorID) {
    		val.vendorID = '';
    	}
    	if (!val.platform) {
    		val.platform = '';
    	}
    	if (!val.fireware) {
    		val.fireware = '';
    	}
    	if (!val.time) {
    		val.time = '';
    	}
    	if (!val.sort) {
    		val.sort = '';
    	}
    }
	url = '/Customer/Home/FirmwarePublish/getFirmwarePublish?venid='+val['vendorID']+'&pform='+val['platform']+'&firmv='+val['fireware']+'&cust='+window.localStorage.getItem("CUSTOM_PERMISSION_USERNAME")+'&time='+val['time']+'&sort='+val['sort']+'&page='+ page +'&pageSize='+pageSize;
	AjaxGet(url, function(data){
		data.sort = val.sort;
		myData.checkedLists = [];
		createFirewareList(data, page);
	});
}



function listenTableAction(id){
	listenToolbarRow('desc', descFirmware, id);
    listenToolbarRow('say', sayFirmware, id);
}

function createFirewareList(data, page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        var brand = 'a';
        var urlStr = '<a style="color:#0000ff;" target="_blank" href="' + arr.path + '">点击下载</a>'
        dataArr.push([arr.id, brand, arr.vendor_id, arr.platform, arr.firmware_ver, arr.md5, formatDate(arr.pub_time), urlStr, arr.passwd, '']);
    }
    myDataTable('#firewareListTable', {
        "data": dataArr,
        "order": [[6, "desc"]],
		"ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'品牌','width':'8%', 'targets':1},
            {'title':'vendorID','width':'8%', 'targets':2},
            {'title':'平台','width':'16%', 'targets':3},
            {'title':'固件版本','width':'10%', 'targets':4},
            {'title':'MD5','width':'12%', 'targets':5},            
            {'title':'发布时间','width':'15%', 'targets':6},
            {'title':'下载链接','width':'8%', 'targets':7},
            {'title':'密码','width':'5%', 'targets':8},
            {'title':'版本描述和评论','width':'32%', 'targets':9}
        ],
		"createdRow": function(nRow, aData, aIdx) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"venid": aData[1],
        		"pform": aData[2],
        		"firmv": aData[3],
        		"puber": aData[6],
        		"time": aData[7]
			});

		}
	});

	$('#firewareListTable th:eq(1)').css('min-width', '100px');
	$('#firewareListTable th:eq(2)').css('min-width', '70px');
    $('#firewareListTable th:eq(5)').css('min-width', '100px');
	$('#firewareListTable th:eq(6)').css('min-width', '180px');
	$('#firewareListTable th:eq(7)').css('min-width', '100px');
    $('#firewareListTable th:eq(9)').css('min-width', '280px');

    toolbar = [];
	initToolBtn(myData.data.retval, '我的固件');
  	//initToolBar('#firewareListTable', toolbar);
    initToolBarRow('#firewareListTable td:last-child', toolbar);
    $('.tab-list').css("padding-top","6px");
    $('.copyBtn').css("width","50px");
    $('.dataTables_filter').css("margin-top","6px");


	var power = window.localStorage.getItem("CUSTOM_PERMISSION_USEPOWER");

	/*toolbar.push('<a class="btn my-btn descBtn" href="javascript:"><i class="iconfont"></i>&nbsp;版本描述</a>');
	toolbar.push('<a class="btn my-btn sayBtn" href="javascript:"><i class="iconfont"></i>&nbsp;评论</a>');
  	initToolBar('#firewareListTable', toolbar);*/
  	$('#firewareListTable_wrapper .toolbar').css('margin-bottom', '55px');
	updatePagination(len, page, data.retval.count, 'firewareListTable');
	listenCheckBox('#firewareListTable');
    	updateChecked('#firewareListTable');

    var keyList = {
	       "venid": '1',
        	"pform": '2',
        	"firmv": '3',
        	"puber": '5',
        	"time": '6'
    };
    orderTab('#firewareListTable', data, keyList);
    $(".copyBtn").on("copy", function(e) {
            alert(222);
          var data = [];
          var a = $(this).parent().eq(1).val();
          e.clipboardData.clearData();
          e.clipboardData.setData("text/plain", "Direct binding - FOO");
          e.clipboardData.setData("text/html", "<b>Direct binding - FOO</b>");
          e.preventDefault();
        });
}

function listenPage(table, nPage, fn) {
	var tmp = {};
    table = table || 'myTable';
    currentPage = nPage || currentPage;
    $('#page-content').on('click', '#'+ table +'_paginate ul li a', function() {
    	tmp = setFirmwareSort();
        var val = $(this).text();
        var active = $(this).parent().hasClass('active');
        var page = Number(val);
        var filter = $('#'+ table +'_filter input').val() || '';
        if(active){
            return false;
        }
        if (val === '上一页' && !$(this).parent().hasClass('disabled')) {
            currentPage--;
            fn(currentPage, tmp);
        } else if (val === '下一页' && !$(this).parent().hasClass('disabled')) {
            currentPage++;
            fn(currentPage, tmp);
        } else if (!isNaN(page)) {
            currentPage = page;
            fn(currentPage, tmp);
        }
        return false;
    });
    $('#page-content').on('click', '#'+ table +'_filter button', function() {
        var val = {
        	"vendorID": $('#'+ table +'_filter input:eq(0)').val(),
        	"platform": $('#'+ table +'_filter input:eq(1)').val(),
        	"fireware": $('#'+ table +'_filter input:eq(2)').val(),
        	"time": $('#'+ table +'_filter input:eq(3)').val().split(':')[0],
        };
        currentPage = 1;
        fn(currentPage, val);
        return false;
    });
}

function setFirmwareSort() {
	var tmp = {
        		"vendorID": '',
        		"platform": '',
        		"fireware": '',
        		"time": '',
        		"sort": ''
        	};

    var asc = $('#firewareListTable thead tr th[class$=_asc]');
    var desc = $('#firewareListTable thead tr th[class$=_desc]');
    for (var i in orderTable) {
    	if (orderTable[i] === asc.html()) {
    		tmp.sort = i + '-asc';
    	}else if(orderTable[i] === desc.html()){
    		tmp.sort = i + '-desc';
    	}
    }
    return tmp;
}

function descFirmware(){
	if(myData.checkedLists.length === 1){
		myData.versionId = $('.checkSelected td:eq(0)').data('id');
		AjaxGet('/Customer/Home/FirmwarePublish/getFirmwarePublish?id=' + myData.versionId, function(data){
			$('#versionRemark').children().remove();
//			$('#versionRemark').val(data.extra.versionDesc);
			var arr = data.retval.VersionDesc.split('\n');
			for (var i = 0; i < arr.length; i++) {
				if (arr[i] == '\r'){

					$('#versionRemark').append('<div style="width: 85%;border-bottom: 1px dashed #F2F2F2;line-height:2;" align="left"><br></div>');
				}else if(arr[i] == ''){

				}else{
					$('#versionRemark').append('<div style="width: 85%;border-bottom: 1px dashed #F2F2F2;line-height:2;" align="left">' + arr[i] + '</div>');
				}
			}
			console.log($('#descModal').html());
			$('#descModal').modal('show');
		});
	}else{
		alert('请选择一个固件版本！');
		return;
	}
}

function sayFirmware() {
    console.log('??');
    if(myData.checkedLists.length === 1){

        $('#sayModal .error-info').text('');
        myData.versionId = $('.checkSelected td:eq(0)').data('id');
        AjaxGet('/Customer/Home/FirmwarePublish/getCommetList?firm_id=' + myData.versionId, function(data){
            createSay(data);
            $('#newSay').val('');
            $('#sayModal').modal('show');
            var buf = [];
            buf[0] = myData.versionId;
        });

    }else{
        alert('请选择一个APP！');
        return;
    }
}

$('#subSay').on('click', function(){
    var newSay = $('#newSay').val();
    var $errorInfo = $('#sayModal .error-info');
    var $titleInfo = $('#sayList .say-title');
    if(newSay == ' ' || !newSay){
        if($titleInfo.length){
            $errorInfo.css('left', '50px');
        }else{
            $errorInfo.css('left', '61px');
        }
        $errorInfo.text('请输入评论！');
        return;
    }

    var data = {"firm_id": myData.versionId, "content": newSay};
    AjaxKeyPost('/Customer/Home/FirmwarePublish/firmComment', data, function(){
        AjaxGet('/Customer/Home/FirmwarePublish/getCommetList?firm_id=' + myData.versionId, function(data){
            createSay(data);
            $('#newSay').val('');
            return;
        });
    }, $errorInfo);

});

function createSay(data){
    var username = window.localStorage.getItem("CUSTOM_PERMISSION_USERNICKNAME");
//    var power = window.localStorage.getItem("CUSTOM_PERMISSION_USEPOWER");
    var len = data.extra.length;
    var con = '';
    for(var i = 0; i < len; i++){
        con += sayHtml(data.extra[i]);
    }
    $('#sayList').html(con);
    $('#sayList .say-user').filter('[data-user='+ username +']').css('color', '#2196f3');
//    $('#sayList .say-info .iconfont').remove();
}

function sayHtml(data){
    var con = data.content.split('\n');
    var span = '';
    for(var i = 0, len = con.length; i < len; i++){
        span += '<span>'+ con[i] +'</span>';
    }
    return '<li class="clearfix">'+
                '<div class="say-title clearfix">'+
                    '<div class="say-user" data-user='+ data.user +'>'+ data.user +'</div>'+
                    '<div class="say-time">'+ formatDate(data.time) +'</div>'+
                '</div>'+
                '<div class="say-info">'+
                    '<span>'+ span +'</span>'
                '</div>'+
            '</li>';
}

$('#sayList').on('click', '.say-info i', function(){
    if (confirm('确定删除？')) {
        var id = $(this).data('id');
        AjaxKeyPost('/Customer/Home/FirmwarePublish/delCommet', {'id':id}, function () {
            AjaxGet('/Customer/Home/FirmwarePublish/getCommetList?firm_id=' + myData.versionId, function(data){
                createSay(data);
                return;
            });
        });
    }
});
