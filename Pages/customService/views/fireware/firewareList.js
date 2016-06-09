//@ sourceURL=fireware.firewareList.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var passPage = 1; //通过当前的页面
var nopassPage = 1; //未通过当前的页面
var currentPage = 1; //当前的页面
var toolbar = [];

var orderTable = {
	"venid": 'vendorID',
    "pform": '平台',
    "brand": '品牌',
    "firmv": '固件版本',
    "cust": '客户',
    "puber": '发布者',
    "time": '发布时间'
};
var orderTablePform = {
    "pform": '平台'
};
var orderTableVenid = {
    "venid": 'vendorID'
};
var orderTableBrand = {
    "brand": '品牌'
};

$(function(){
	initTopMenu();

	listenInput();
	myData.checkedLists = [];
	AjaxGet('/Customer/Home/FirmwarePublish/getFirmwarePublish?page=1&pageSize=' + pageSize, function(data){
		data.sort = 'time-desc';
        createFirewareList(data, 1);
        $('#main-content').css('visibility', 'visible');
    });

    listenSingleCheckBox('#firewareListTable');
    listenSingleCheckBox('#vendorIdListTable');
    listenSingleCheckBox('#platformListTable');
    listenSingleCheckBox('#brandListTable');
    listenOrder('#firewareListTable', orderTable, updateFirewareTable);
    listenOrder('#platformListTable', orderTablePform, updateTable);
    listenOrder('#vendorIdListTable', orderTableVenid, updateTable);
    listenOrder('#brandListTable', orderTableBrand, updateTable);

    checkMoz();

	listenPage('firewareListTable', passPage, updateFirewareTable);
	listenMyPage('platformListTable', nopassPage, updateTable, {'pform':''}, orderTablePform);
	listenMyPage('vendorIdListTable', currentPage, updateTable, {'venid':''}, orderTableVenid);
	listenMyPage('brandListTable', currentPage, updateTable, {'brand':''}, orderTableBrand);
	$('#breadcrumb').css('min-width', '1090px');
	listenTab(function(str){
		$('.dataTables_filter input').val('');
    	if(str === '固件列表'){
    		$('#breadcrumb').css('min-width', '1090px');
    		currentPage = 1;
    		updateFirewareTable(currentPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(0)').show();
    	}else if(str === '平台列表'){
    		$('#breadcrumb').css('min-width', '');
    		currentPage = 1;
			updateTable(currentPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(1)').show();
    	}else if(str === 'vendorID列表'){
    		$('#breadcrumb').css('min-width', '');
    		currentPage = 1;
			updateTable(currentPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(2)').show();
    	}else if(str === '品牌列表'){
    		$('#breadcrumb').css('min-width', '');
    		currentPage = 1;
			updateTable(currentPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(3)').show();
    	}
    });

	listenTableAction('#firewareListTable');
	listenTableAction('#platformListTable');
	listenTableAction('#vendorIdListTable');
	listenTableAction('#brandListTable');

	$("#publishTime").datetimepicker({
		minView: "day",
		format: 'yyyy-mm-dd hh:00',
		language: 'zh-CN',
		autoclose: true
	}).val('');
	$('#breadcrumb span:eq(0)').trigger('click');
});


$('#subPublish').on('click', function (){
	var vendorId = $('#vendorID').val();
	var platform = $('#platform').val();
	var fireware = $('#fireware').val();
	var md5 = $('#md5').val();
	var customer = $('#customer').val();
	var brand = $('#brand').val();
	var downloadLink = $('#downloadLink').val();
	var pwd = $('#pwd').val();
	var versionDes = $('#versionDes').val();
	var filter = {
		"searchVendorID": $('#firewareListTable_filter input:eq(0)').val() || '',
		"searchPlatform": $('#firewareListTable_filter input:eq(1)').val() || '',
		"searchFireware": $('#firewareListTable_filter input:eq(2)').val() || '',
		"searchCustom": $('#firewareListTable_filter input:eq(3)').val() || '',
		"searchTime": $('#firewareListTable_filter input:eq(4)').val() || ''
	};
	var filter = $('#firewareListTable_filter input').val() || '';
	var linkTest = /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/ ;
	var md5Test = /\b(.{32})\b/ ;
	var pwdTest = /\b(.{4})\b/ ;

	if (vendorId === '' || vendorId === '请选择vendorID') {
		$('#publishModal .error-info').text('请选择vendorID!');
		return;
	}
	if (platform === '' || platform === '请选择平台') {
		$('#publishModal .error-info').text('请选择平台!');
		return;
	}
	if (fireware === '' || !fireware) {
		$('#publishModal .error-info').text('请输入固件!');
		return;
	}
	if (md5 === '' || !md5) {
		$('#publishModal .error-info').text('请输入MD5!');
		return;
	}
	if (!md5Test.test(md5)) {
		$('#publishModal .error-info').text('请输入正确的MD5!');
		return;
	}
	if (customer === '' || customer === '请选择客户') {
		$('#publishModal .error-info').text('请选择客户!');
		return;
	}
	if (brand === ''|| platform === '请选择品牌') {
		$('#publishModal .error-info').text('请选择品牌!');
		return;
	}
	if (downloadLink === '' || !downloadLink) {
		$('#publishModal .error-info').text('请输入下载链接!');
		return;
	}
	if (!linkTest.test(downloadLink)) {
		$('#publishModal .error-info').text('请输入正确的下载链接!');
		return;
	}
	if (pwd === '' || !pwd) {
		$('#publishModal .error-info').text('请输入下载密码!');
		return;
	}
	if (!pwdTest.test(pwd)) {
		$('#publishModal .error-info').text('请输入4位的下载密码!');
		return;
	}
	if (versionDes === '' || !versionDes) {
		$('#publishModal .error-info').text('请输入版本描述!');
		return;
	}

	var data = {
		"VendorId": vendorId,
		"PlatForm": platform,
		"FirmwareVer": fireware,
		"VersionDesc": versionDes,
		"Md5": md5,
		"Customer": customer,
		"brand": brand,
		"Path": downloadLink,
		"Passwd": pwd
	};

	var tmp = setFirmwareSort();

    AjaxKeyPost('/Customer/Home/FirmwarePublish/publish', data, function(data){
    	resultMsg(data, '发布固件成功', '#publishModal');
    	updateFirewareTable(currentPage, tmp);
    });
});



$('#subPlatform').on('click', function () {
	var platformName = $('#platformName').val();
	var platformNote = $('#platformNote').val();
	var filter = $('#platformListTable_filter input').val() || '';

	if (platformName === '' || !platformName) {
		alert('请输入平台名!');
		return;
	}

	var data = {
		"platform": platformName,
		"note": platformNote
	};
    AjaxKeyPost('/Customer/Home/Platform/add', data, function(data){

    	resultMsg(data, '平台录入成功', '#platformModal');
    	updateTable(currentPage, {"pform": filter});
    });
});

$('#subVendorID').on('click', function () {
	var vendorId = $('#newVendorID').val();
	var vendorIdNote = $('#newVendorIDNote').val();
	var filter = $('#vendorIdListTable_filter input').val() || '';

	if (vendorId === '' || !vendorId) {
		alert('请输入vendorID!');
		return;
	}

	var data = {
		"vendor_id": vendorId,
		"note": vendorIdNote
	};
    AjaxKeyPost('/Customer/Home/Vendorid/add', data, function(data){

    	resultMsg(data, 'VendorID录入成功', '#vendorIDModal');
    	updateTable(currentPage, {"venid": filter});
    });
});

$('#subBrand').on('click', function () {
	var brandName = $('#brandName').val();
	var bcustomer = $('#bcustomer').val();
	var brandNote = $('#brandNote').val();
	var filter = $('#brandListTable_filter input').val() || '';

	if (brandName === '' || !brandName) {
		alert('请输入品牌名!');
		return;
	}

	var data = {
		"brand_name": brandName,
		"customer": bcustomer,
		"remark":brandNote
	};
    AjaxKeyPost('/Customer/Home/Brands/addBrand', data, function(data){

    	resultMsg(data, '品牌录入成功', '#brandModal');
    	updateTable(currentPage, {"brand": filter});
    });
});

function updateTable(page, val, order){
	
	if (order === 1) {
		currentPage = 1;
		order = 0;
	}
	if (!val) {
		val = {
			'name': '',
			'sort': '',
		};

	}else{
		if (!val.name) {
			val.name = '';
		}
		if (!val.sort) {
			val.sort = '';
		}
	}
	var url = '';
	var type = $('#breadcrumb > span.active').text();
	var table = '';
	if(type === '平台列表'){
		url = '/Customer/Home/Platform/getPlatform?name='+val['name']+'&sort='+ val['sort'] +'&page='+ page +'&pageSize='+pageSize;
		AjaxGet(url, function(data){
			myData.checkedLists = [];
			//判断是否删除的是最后一条并设置当前页
			noneDataCB(data, page, url);
			myData.tmpData.sort = val.sort;

			createPlatformList(myData.tmpData, currentPage);
			myData.tmpData = {};
		});
	}else if(type === 'vendorID列表'){
		url = '/Customer/Home/Vendorid/getVendorid?name='+val['name']+'&sort='+ val['sort']+'&page='+ page +'&pageSize='+pageSize;
		AjaxGet(url, function(data){
			myData.checkedLists = [];
			//判断是否删除的是最后一条并设置当前页
			noneDataCB(data, page, url);
			myData.tmpData.sort = val.sort;

			createVendorIdListTable(myData.tmpData, currentPage);
			myData.tmpData = {};
		});
	}else if(type === '品牌列表'){
		url = '/Customer/Home/Brands/getBrand?name='+val['name']+'&sort='+ val['sort']+'&page='+ page +'&pageSize='+pageSize;
		AjaxGet(url, function(data){
			myData.checkedLists = [];
			//判断是否删除的是最后一条并设置当前页
			noneDataCB(data, page, url);
			myData.tmpData.sort = val.sort;

			createBrandListTable(myData.tmpData, currentPage);
			myData.tmpData = {};
		});
	}
}

function noneDataCB(data, page, url) {
	if (data.retval.list.length === 0 && page > 1) {
		page--;
		var strs = url.split(/(?:&page=)\d+/);
		url = strs[0] + '&page=' + page + strs[1];
		JqGet(url, function(data) {
			noneDataCB(data, page, url);
		});
	}else{
		currentPage = page;
		myData.tmpData = data;
	}
}

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
        		"customer": '',
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
    	if (!val.customer) {
    		val.customer = '';
    	}
    	if (!val.time) {
    		val.time = '';
    	}
    	if (!val.sort) {
    		val.sort = '';
    	}
    }
	url = '/Customer/Home/FirmwarePublish/getFirmwarePublish?venid='+val['vendorID']+'&pform='+val['platform']+'&firmv='+val['fireware']+'&cust='+val['customer']+'&time='+val['time']+'&sort='+val['sort']+'&page='+ page +'&pageSize='+pageSize;
	AjaxGet(url, function(data){
		data.sort = val.sort;
		myData.checkedLists = [];
		createFirewareList(data, page);
	});
}



function listenTableAction(id){
	listenToolbar('publish', publishFireware, id);
	listenToolbar('del', delFireware, id);
	listenToolbar('addPlatform', addPlatform, id);
	listenToolbar('addVendorID', addVendorID, id);
	listenToolbar('addBrand', addBrand, id);
	listenToolbar('delPlatform', delPlatform, id);
	listenToolbar('delVendorID', delVendorId, id);
	listenToolbar('delBrand', delBrand, id);
	listenToolbar('desc', descFirmware, id);
	listenToolbar('say', sayFirmware, id);
}

function publishFireware(){
	addFirewareList();
	$('#fireware').val('');
	$('#md5').val('');
	$('#downloadLink').val('');
	$('#pwd').val('');
	$('#versionDes').val('');
	$('#publishModal .error-info').text('');
	$('#publishModal').modal('show');
}

function addPlatform(){
	$('#platformName').val('');
	$('#platformNote').val('');
	$('#platformModal .error-info').text('');
	$('#platformModal').modal('show');
}

function addVendorID(){
	$('#newVendorID').val('');
	$('#newVendorIDNote').val('');
	$('#vendorIDModal .error-info').text('');
	$('#vendorIDModal').modal('show');
}

function addBrand(){
	AjaxGet('/Customer/Home/User/customerList', selectBCustomer, true);
	$('#brandName').val('');
	$('#brandNote').val('');
	$('#brandModal .error-info').text('');
	$('#brandModal').modal('show');
}


function delFireware(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			tmp = setFirmwareSort();
			AjaxKeyPost('/Customer/Home/FirmwarePublish/delete', {ids:myData.checkedLists}, function (data) {
				resultMsg(data, '删除成功');
				updateFirewareTable(currentPage, tmp);
				return;
			});
		}
	}else{
		alert('请选择固件！');
		return;
	}
}

function delPlatform(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#platformListTable_filter input').val() || '';
			AjaxKeyPost('/Customer/Home/Platform/delete', {ids:myData.checkedLists}, function (data) {
				resultMsg(data, '删除成功');
				updateTable(currentPage, {"pform": filter});
				return;
			});
		}
	}else{
		alert('请选择固件！');
		return;
	}
}

function delVendorId(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#vendorIdListTable_filter input').val() || '';
			AjaxKeyPost('/Customer/Home/Vendorid/delete', {ids:myData.checkedLists}, function (data) {
				resultMsg(data, '删除成功');
				updateTable(currentPage, {"venid": filter});
				return;
			});
		}
	}else{
		alert('请选择固件！');
		return;
	}
}

function delBrand(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#brandListTable_filter input').val() || '';
			AjaxKeyPost('/Customer/Home/Brands/delBrand', {ids:myData.checkedLists}, function (data) {
				resultMsg(data, '删除成功');
				updateTable(currentPage, {"brand": filter});
				return;
			});
		}
	}else{
		alert('请选择固件！');
		return;
	}
}

function createFirewareList(data, page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        var urlStr = '<a style="color:#0000ff;" target="_blank" href="' + arr.path + '">'+arr.path+'</a>'
        dataArr.push([ arr.id, arr.customer_name, arr.brand_name, arr.vendor_id, arr.platform, arr.firmware_ver, arr.md5, arr.name, formatDate(arr.pub_time), urlStr, arr.passwd]);
    }
    myDataTable('#firewareListTable', {
        "data": dataArr,
        "order": [[6, "desc"]],
		"ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'客户','width':'11%', 'targets':1},
            {'title':'品牌','width':'7%', 'targets':2},
            {'title':'vendorID','width':'7%', 'targets':3},
            {'title':'平台','width':'9%', 'targets':4},
            {'title':'固件版本','width':'17%', 'targets':5},
            {'title':'MD5','width':'8%', 'targets':6},      
            {'title':'发布者','width':'10%', 'targets':7},
            {'title':'发布时间','width':'15%', 'targets':8},
            {'title':'下载链接','width':'8%', 'targets':9},
            {'title':'密码','width':'5%', 'targets':10}
        ],
		"createdRow": function(nRow, aData, aIdx) {

			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"cust": aData[1],
				"brand": aData[2],				
				"venid": aData[3],
        		"pform": aData[4],
        		"firmv": aData[5],
        		"puber": aData[6],
        		"time": aData[7]
			});

		}
	});

	$('#firewareListTable th:eq(1)').css('min-width', '160px');
	$('#firewareListTable th:eq(2)').css('min-width', '70px');
	$('#firewareListTable th:eq(3)').css('min-width', '100px');
	$('#firewareListTable th:eq(5)').css('min-width', '100px');
	$('#firewareListTable th:eq(6)').css('min-width', '100px');
	$('#firewareListTable th:eq(7)').css('min-width', '100px');
	$('#firewareListTable th:eq(8)').css('min-width', '180px');

	toolbar = [];

	initToolBtn(myData.data.retval, '固件列表');
  	initToolBar('#firewareListTable', toolbar);
  	$('#firewareListTable_wrapper .toolbar').css('margin-bottom', '55px');
  	if (toolbar.length <= 0) {
		$('#firewareListTable_filter').css({
			"position": "inherit",
			"margin-bottom": "10px",
			"margin-top": "0px"
		});
		$('.toolbar').remove();
	}
	updatePagination(len, page, data.retval.count, 'firewareListTable');
	listenCheckBox('#firewareListTable');
    	updateChecked('#firewareListTable');

    var keyList = {
	"venid": '1',
        	"pform": '2',
        	"firmv": '3',
        	"cust": '5',
        	"puber": '6',
        	"time": '7'
    };
    orderTab('#firewareListTable', data, keyList);
}

function createPlatformList(data, page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        dataArr.push([arr.id, arr.platform, arr.note]);
    }
    myDataTable('#platformListTable', {
        "data": dataArr,
        "order": [[2, "desc"]],
        "ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'平台','width':'48%', 'targets':1},
            {'title':'备注','width':'48%', 'targets':2}
        ],
		"createdRow": function(nRow, aData, aIdx) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');

			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"pform": aData[1]
			});
		}
	});

	toolbar = [];

	initToolBtn(myData.data.retval, '平台列表');
  	initToolBar('#platformListTable', toolbar);
	updatePagination(len, page, data.retval.count, 'platformListTable');
	listenCheckBox('#platformListTable');
    updateChecked('#platformListTable');
    var keyList = {
        "pform": '1'
    };
    orderTab('#platformListTable', data, keyList);
}

function createVendorIdListTable(data, page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        dataArr.push([arr.id, arr.vendor_id, arr.note]);
    }
    myDataTable('#vendorIdListTable', {
        "data": dataArr,
        "order": [[2, "desc"]],
        "ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'vendorID','width':'48%', 'targets':1},
            {'title':'备注','width':'48%', 'targets':2}
        ],
		"createdRow": function(nRow, aData, aIdx) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');

			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"venid": aData[1]
			});
		}
	});

	toolbar = [];

	initToolBtn(myData.data.retval, 'vendorID列表');
  	initToolBar('#vendorIdListTable', toolbar);
	updatePagination(len, page, data.retval.count, 'vendorIdListTable');
	listenCheckBox('#vendorIdListTable');
    	updateChecked('#vendorIdListTable');
    var keyList = {
        "venid": '1'
    };
    orderTab('#vendorIdListTable', data, keyList);
}

function createBrandListTable(data, page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        //console.log(arr);
        dataArr.push([arr.id, arr.brand_name, arr.customer, arr.brandNote]);
    }
    myDataTable('#brandListTable', {
        "data": dataArr,
        "order": [[2, "desc"]],
        "ordering": false,
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'品牌','width':'20%', 'targets':1},
            {'title':'客户','width':'20%', 'targets':2},
            {'title':'备注','width':'60%', 'targets':3}
        ],
		"createdRow": function(nRow, aData, aIdx) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');

			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"brand": aData[1]
			});
		}
	});

	toolbar = [];
	//console.log(myData.data.retval);
	initToolBtn(myData.data.retval, '品牌列表');
  	initToolBar('#brandListTable', toolbar);
	updatePagination(len, page, data.retval.count, 'brandListTable');
	listenCheckBox('#brandListTable');
    	updateChecked('#brandListTable');
    var keyList = {
        "brand": '1'
    };
    orderTab('#brandListTable', data, keyList);
}

function addFirewareList(){
	AjaxWhen([
        AjaxGet('/Customer/Home/Vendorid/getVendorid', selectVendorID, true),
        AjaxGet('/Customer/Home/Platform/getPlatform', selectPlatform, true),
        AjaxGet('/Customer/Home/User/customerList', selectCustomer, true),
        AjaxGet('/Customer/Home/Brands/getBrand', selectBrand, true)
    ], function(){
//		$('#remark').val('');
		$('#publishModal .error-info').text('');
		$('#publishModal').modal('show');
    });
}

function selectVendorID(data){
	var arr = data.retval.list;
    var con = '<option value="请选择vendorID">请选择vendorID</option>';
    var $select = $('#vendorID');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].vendor_id+'">'+arr[i].vendor_id+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "715px"
    });
}

function selectPlatform(data){
	var arr = data.retval.list;
    var con = '<option value="请选择平台">请选择平台</option>';
    var $select = $('#platform');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].platform+'">'+arr[i].platform+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "715px"
    });
}

function selectCustomer(data){
	var arr = data.retval;
    var con = '<option value="请选择客户">请选择客户</option>';
    var $select = $('#customer');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].user+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "715px"
    });
}

function selectBCustomer(data){
	//console.log(data);
	var arr = data.retval;
    var con = '<option value="请选择客户">请选择客户</option>';
    var $select = $('#bcustomer');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].user+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "633px"
    });
}

function selectBrand(data){
	console.log(data);
	var arr = data.retval;
    var con = '<option value="请选择品牌">请选择品牌</option>';
    var $select = $('#brand');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].user+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "715px"
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
        	"customer": $('#'+ table +'_filter input:eq(3)').val(),
        	"time": $('#'+ table +'_filter input:eq(4)').val().split(':')[0],
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
        		"customer": '',
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
			$('#descModal').modal('show');
		});
	}else{
		alert('请选择一个固件版本！');
		return;
	}
}

//页面监控
function listenInput() {
	$('#md5').on('keyup', function() {
		var md5Test = /\b(.{32})\b/ ;
		if (!md5Test.test($(this).val())) {
			$('#publishModal .error-info').text('请输入正确的MD5!');
		}else{
			$('#publishModal .error-info').text('');
		}
	});
	$('#downloadLink').on('keyup', function() {
		var linkTest = /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/ ;
		if (!linkTest.test($(this).val())) {
			$('#publishModal .error-info').text('请输入正确的下载地址!');
		}else{
			$('#publishModal .error-info').text('');
		}
	});
	$('#pwd').on('keyup', function() {
		var pwdTest = /\b(.{4})\b/ ;
		if (!pwdTest.test($(this).val())) {
			$('#publishModal .error-info').text('请输入正确的下载密码!');
		}else{
			$('#publishModal .error-info').text('');
		}
	});
}

//固件评论页面
function sayFirmware() {
    console.log(myData.checkedLists);
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
        alert('请选择一个固件！');
        return;
    }
}

//生成固件评论列表
function createSay(data){
    var username = window.localStorage.getItem("CUSTOM_PERMISSION_USERNICKNAME");
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
                    '<i class="iconfont icon-shanchu" data-id='+ data.id +'></i>'+ span +
                '</div>'+
            '</li>';
}

//提交固件评论
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

//删除固件评论
$('#sayList').on('click', '.say-info i', function(){
    if (confirm('确定删除？')) {
        var id = $(this).data('id');
        AjaxKeyPost('/Customer/Home/FirmwarePublish/delCommet', {'id':id}, function (res) {
        	res=JSON.parse(res)
        	if(res.code != undefined && res.code == 801){
        		alert(res.msg);
        	}else{
        		AjaxGet('/Customer/Home/FirmwarePublish/getCommetList?firm_id=' + myData.versionId, function(data){
                    createSay(data);
                    return;
                });
        	}
        });
    }
});