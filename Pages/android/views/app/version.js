//@ sourceURL=app.version.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var passPage = 1; //通过当前的页面
var nopassPage = 1; //未通过当前的页面
var currentPage = 1; //当前的页面
var commentPage = 1;

$(function () {
	myData.checkedLists = [];   //存储check选中项
	var data = {};

	var power = window.localStorage.getItem("ANDROID_PERMISSION_USEPOWER");
	if(power.indexOf('发布用户') !== -1){
		$('#publishBtn').show();
	}

	AjaxGet('/Android/Home/App/publishAppLists?passTest=true&page=1&pageSize=' + pageSize, function(data){
        createVersion(data, 1, 'pVersionTable');
        $('#main-content').css('visibility', 'visible');
    });

    listenSingleCheckBox('#pVersionTable');
    listenSingleCheckBox('#nVersionTable');
    listenSingleCheckBox('#aVersionTable');
    listenSingleCheckBox('#commentTable');

    $('#detailTable').on('click', 'tbody tr', function(ev) {
    	var e = ev || event;
    	var tar = e.target;
    	var obj = $(this).children().eq(0);
    	myData.detail = obj.data('detail');

        if( tar.className.indexOf('detail-btn') != -1){
            AjaxGet('/Android/Home/Base/relyModuleLists?' + myData.detail, function(data){
            	if(!data.extra.relyModule){
            		alert('此版本模块未发布！');
            		return false;
            	}
            	$('#detailModal h4').text('模块详情');
				createDetail(data, false);
				createRely(data);
			});
        }
        return false;
    });


    checkMoz();
	listenfile();
	listenMyPage('pVersionTable', passPage, updateVersion);
	listenMyPage('nVersionTable', nopassPage, updateVersion);
	listenMyPage('aVersionTable', currentPage, updateVersion);
	listenMyPage('commentTable', commentPage, updateVersion);

	selectTest();

	listenTab(function(str){
		$('.dataTables_filter input').val('');
    	if(str === '最新通过'){
    		passPage = 1;
    		updateVersion(passPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(0)').show();
    	}else if(str === '最新待测试'){
    		nopassPage = 1;
			updateVersion(nopassPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(1)').show();
    	}else if(str === '全部'){
    		currentPage = 1;
			updateVersion(currentPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(2)').show();
    	}else{
    		commentPage = 1;
			updateVersion(commentPage);
    		$('.tab-list').hide();
    		$('.tab-list:eq(3)').show();
    	}
    });

    listenTableAction('#pVersionTable');
    listenTableAction('#nVersionTable');
    listenTableAction('#aVersionTable');
    listenTableAction('#commentTable');

    AjaxGet('/Android/Home/App/commentUnreadCount', function(data) {
        if(data.count > 0){
        $('#msgNum').css({
            "background-color": "#ff0000",
            "position": "absolute",
            "top": "9px",
            "left": "100px",
            "width": "15px",
            "line-height": "1.2",
            "border-radius": "15px",
            "padding-left": "4px"
        });
        $('#msgNum').html(data.count.toString());
        }else{
            $('#msgNum').css({
            "background-color": "",
            "position": "",
            "top": "",
            "left": "",
            "width": "",
            "line-height": "",
            "border-radius": "",
            "padding-left": ""
            });
            $('#msgNum').html('');
        }
    });

});

$('#publishBtn').on('click', publishApp);

function listenTableAction(id){
	// listenToolbar('publish', publishApp, '#aVersionTable');
	listenToolbar('del', delApp, id);
	listenToolbar('detail', detailApp, id);
	// listenToolbar('rely', relyApp, id);
	listenToolbar('signature', signatureApp, id);
	listenToolbar('say', sayApp, id);
	listenToolbar('state', stateApp, id);
	listenToolbar('desc', descApp, id);
}

function publishApp(){
	$('#fileHide').val('');
	$('#fileShow').val('');
	$('#remark').val('');
	$('#publishModal .error-info').text('');
	$('#publishModal').modal('show');
}

function delApp(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = getFilter();
			AjaxPost('/Android/Home/App/deletePublishApp', myData.checkedLists, function () {
				updateVersion(currentPage, filter);
				return;
			});
		}
	}else{
		alert('请选择APP！');
		return;
	}
}

function getFilter(){
	var str = $('#breadcrumb span.active').text();
	var filter = '';
	if(str === '最新通过'){
		filter = $('#pVersionTable_filter input').val() || '';
	}else if(str === '最新待测试'){
		filter = $('#nVersionTable_filter input').val() || '';
	}else if(str === '全部'){
		filter = $('#aVersionTable_filter input').val() || '';
	}else{
		filter = $('#commentTable_filter input').val() || '';
	}
	return filter;
}

function detailApp(){
	if(myData.checkedLists.length === 1){
		myData.versionId = $('.checkSelected td:eq(0)').data('id');
		AjaxGet('/Android/Home/App/publishAppLists?id=' + myData.versionId, function(data){
			$('#detailModal h4').text('app详情');
			createDetail(data, true);
			createRely(data);
			$('#detailModal').modal('show');
		});
	}else{
		alert('请选择一个APP！');
		return;
	}
}

function descApp(){
	if(myData.checkedLists.length === 1){
		myData.versionId = $('.checkSelected td:eq(0)').data('id');
		AjaxGet('/Android/Home/App/publishAppLists?id=' + myData.versionId, function(data){
			$('#versionRemark').children().remove();
//			$('#versionRemark').val(data.extra.versionDesc);
			var arr = data.extra.versionDesc.split('\n');
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
		alert('请选择一个APP！');
		return;
	}
}

function signatureApp(){
	if(myData.checkedLists.length === 1){
		myData.versionId = $('.checkSelected td:eq(0)').data('id');
		AjaxGet('/Android/Home/App/publishAppLists?id=' + myData.versionId, function(data){
			createSignature(data.extra.signature);
			$('#signatureModal').modal('show');
		});
	}else{
		alert('请选择一个APP！');
		return;
	}
}

function sayApp(){
	if(myData.checkedLists.length === 1){
		$('#sayModal .error-info').text('');
		myData.versionId = $('.checkSelected td:eq(0)').data('id');
		var filter = getFilter();
		AjaxGet('/Android/Home/App/AppCommentLists?appId=' + myData.versionId, function(data){
			createSay(data);
			$('#newSay').val('');
			$('#sayModal').modal('show');
			var buf = [];
			buf[0] = myData.versionId;
			AjaxPost('/Android/Home/App/deleteCommentUnread', buf, function(data) {
				$('#sayModal').on('hide.bs.modal', function() {
					updateVersion(commentPage, filter);
					return;
				});
			});
		});
	}else{
		alert('请选择一个APP！');
		return;
	}
}

function stateApp(){
	if(myData.checkedLists.length === 1){
		var $obj = $('.checkSelected td:eq(0)');
		myData.versionId = $obj.data('id');
		myData.passTest = $obj.data('passTime');
		$('#testState').children().remove();
		AjaxGet('/Android/home/user/me', function(data) {
			if(data.extra.tester == "true"){
				$('#testState').append('<option value="true">通过</option><option value="false">未通过</option><option value="regress">回归通过</option>');
			}
			if (data.extra.publisher == "true") {
				$('#testState').append('<option value="back">打回</option>');
			}
			$('#testState').trigger("chosen:updated.chosen").chosen({
	        	allow_single_deselect: true,
	        	disable_search: true,
	        	width: "463px"
	    	});
	    	$('#stateModal .error-info').text('');
			$('#stateModal').modal('show');
		});
	}else{
		alert('请选择一个APP！');
		return;
	}
}

function updateVersion(page, name){
	name = name || '';
	var url = '';
	var type = $('#breadcrumb > span.active').text();
	var table = '';
	if(type === '最新通过'){
		url = '/Android/Home/App/publishAppLists?passTest=true&name='+name+'&page='+ page +'&pageSize='+pageSize;
		table = 'pVersionTable';
	}else if(type === '最新待测试'){
		url = '/Android/Home/App/publishAppLists?passTest=test&name='+name+'&page='+ page +'&pageSize='+pageSize;
		table = 'nVersionTable';
	}else if(type === '全部'){
		url = '/Android/Home/App/publishAppLists?name='+name+'&page='+ page +'&pageSize='+pageSize;
		table = 'aVersionTable';
	}else{
		url = '/Android/Home/App/publishAppLists?name='+name+'&page='+ page +'&pageSize='+pageSize+'&unread=true';
		table = 'commentTable';
	}
	AjaxGet(url, function(data){
		myData.checkedLists = [];
		myData.versionId = null;
		createVersion(data, page, table);
	});
}

function selectTest(){
    var $select = $('#testState');
    $select.trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        disable_search: true,
        width: "463px"
    });
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

	var data = {"appId": myData.versionId, "content": newSay};
	AjaxPost('/Android/Home/App/addAppComment', data, function(){
		AjaxGet('/Android/Home/App/AppCommentLists?appId=' + myData.versionId, function(data){
			createSay(data);
			$('#newSay').val('');
			return;
		});
	}, $errorInfo);

});

$('#subPublish').on('click', function(){
	var fileObj = document.getElementById("fileHide").files[0];
    var fileVal = $("#fileShow").val();
    var filter = getFilter();
    var remark = $('#remark').val();
    var $errorInfo = $('#publishModal .error-info');
    var data = new FormData();

    if(fileVal != ' ' && fileVal.indexOf('http') == -1){
        data.append("apkFile", fileObj);
    }
    if(fileVal == ' ' || !fileVal){
        $errorInfo.text('请选择要上传的APK文件');
        return;
    }
    if(remark == ' ' || !remark){
        $errorInfo.text('请输入版本描述');
        return;
    }
    if(remark.gblen() < 20){
    	$errorInfo.text('版本描述不少于20字节');
        return;
    }


    var extra = remark;
    data.append("extra", extra);

    AjaxFile('/Android/Home/App/publishApp', data, function(){
        $('#publishModal').modal('hide');
        updateVersion(currentPage, filter);
    }, $errorInfo);

});

$('#subState').on('click', function(){
	var state = $('#testState').val();
	var data = {"id": myData.versionId, "passTest": state};
	var $errorInfo = $('#stateModal .error-info');
	var filter = getFilter();

	AjaxPost('/Android/Home/App/mofidyPassTest', data, function(){
		$('#stateModal').modal('hide');
		updateVersion(currentPage, filter);
		return;
	}, $errorInfo);
});

function createSay(data){
	var username = window.localStorage.getItem("ANDROID_PERMISSION_USERNICKNAME");
	var power = window.localStorage.getItem("ANDROID_PERMISSION_USEPOWER");
	var len = data.extra.length;
	var con = '';
	for(var i = 0; i < len; i++){
		con += sayHtml(data.extra[i]);
	}
	$('#sayList').html(con);
	$('#sayList .say-user').filter('[data-user='+ username +']').css('color', '#2196f3');
	if(power.indexOf('系统管理员') === -1){
		$('#sayList .say-info .iconfont').remove();
	}
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

$('#sayList').on('click', '.say-info i', function(){
	if (confirm('确定删除？')) {
		var id = $(this).data('id');
		AjaxGet('/Android/Home/App/deleteAppComment?id=' + id, function () {
			AjaxGet('/Android/Home/App/AppCommentLists?appId=' + myData.versionId, function(data){
				createSay(data);
				return;
			});
		});
	}
});

function createDetail(data, type){
	if(type){
		$('#passTest').val(data.extra.passTest);
	    $('#channelId').val(data.extra.channelId);
	    $('#versionCode').val(data.extra.versionCode);
	    $('#systemApp').val(data.extra.systemApp === 'true' ? '是' : '否');
	    $('#mixed').val(data.extra.mixed === 'true' ? '是' : '否');
	    $('.type-app').show();
	    $('.type-base').hide();
	}else{
		$('#dPublisher').val(data.extra.publisher);
		$('#dPubTime').val(data.extra.pubTime);
		$('.type-base').show();
		$('.type-app').hide();
	}

    $('#commitId').val(data.extra.gitCommitId);
	$('#gitBranch').val(data.extra.gitBranch);
    // $('#versionRemark').val(data.extra.versionDesc);
    $('#lowestVersion').val(data.extra.minSdk);
}

function createSignature(data){
	$('#owner').val(data.owner);
	$('#issuer').val(data.issuer);
	$('#serialNumber').val(data.serialNumber);
	$('#validFrom').val(data.validFrom);

	var certificateFingerprints = '';
	var num = 0;
	for(var p in data.certificateFingerprints){
		num++;
		if(num !== 3){
			certificateFingerprints += p + ' : ' + data.certificateFingerprints[p] + '\n';
		}else{
			var temp = data.certificateFingerprints[p];
			certificateFingerprints += p + ' : ' + temp.slice(0, 70) + '\n' + temp.slice(70);
		}
	}
	$('#certificateFingerprints').val(certificateFingerprints);
}

function createRely(data){
	var dataArr = [];
    var len = data.extra.relyModule.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra.relyModule[i];
        var detail = 'module='+ arr.module +'&versionName='+ arr.version_name +'&pkgName=' + arr.pkg_name;
        dataArr.push([arr.module, arr.version_name, arr.pkg_name, detail]);
    }
    myDataTable('#detailTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'模块','width':'25%', 'targets':0},
            {'title':'版本名称','width':'15%', 'targets':1},
            {'title':'包名','width':'45%', 'targets':2},
            {'title':'查看','width':'15%', 'targets':3}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(3)', nRow).html('<a class="my-btn detail-btn" style="border: 0;cursor: pointer;"><i class="iconfont icon-bjibenxinxi"></i> 详情</a>');
			$('td:eq(0)', nRow).data({
				"detail": aData[3]
			});
		}
	});
}

function createVersion(data, page, table){
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.pkgName, arr.versionName, arr.passTest, arr.publisher, formatDate(arr.pubTime), arr.path]);
    }
    table = table || 'aVersionTable';
    myDataTable('#' + table, {
        "data": dataArr,
        "order": [[6, "desc"]],
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'应用名','width':'13%', 'targets':1},
            {'title':'包名','width':'13%', 'targets':2},
            {'title':'版本名称','width':'8%', 'targets':3},
            {'title':'状态','width':'8%', 'targets':4},
            {'title':'发布者','width':'9%', 'targets':5},
            {'title':'发布时间','width':'12%', 'targets':6},
            {'title':'下载','width':'8%', 'targets':7}
        ],
		"createdRow": function(nRow, aData, aIdx) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(7)', nRow).html('<a href="'+ aData[7] +'" target="_blank" style="color: black;text-decoration: underline;">点击获取</a>').addClass('center');
			if(aData[4] === 'false'){
				$('td:eq(4)', nRow).html('未通过').css('color', 'red');
			}else if(aData[4] === 'true'){
				$('td:eq(4)', nRow).html('通过').css('color', '#00cc00');
			}else if(aData[4] === 'test'){
				$('td:eq(4)', nRow).html('待测试').css('color', '#ffcc00');
			}else if(aData[4] === 'regress'){
				$('td:eq(4)', nRow).html('回归通过').css('color', '#00cc00');
			}else if(aData[4] === 'back'){
				$('td:eq(4)', nRow).html('打回').css('color', 'red');
			}

			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"passTime": aData[4]
			});
		}
	});

	var toolbar = [];

	var power = window.localStorage.getItem("ANDROID_PERMISSION_USEPOWER");
	// if(power.indexOf('发布用户') !== -1){
	// 	toolbar.push('<a class="btn my-btn publishBtn" href="javascript:"><i class="iconfont icon-release"></i>&nbsp;发布</a>');
	// }
	if(power.indexOf('系统管理员') !== -1){
		toolbar.push(myConfig.delBtn);
	}
	toolbar.push('<a class="btn my-btn detailBtn" href="javascript:"><i class="iconfont icon-bjibenxinxi"></i>&nbsp;详情</a>');
	toolbar.push('<a class="btn my-btn signatureBtn" href="javascript:"><i class="iconfont icon-geonesign3"></i>&nbsp;签名信息</a>');
	toolbar.push('<a class="btn my-btn descBtn" href="javascript:"><i class="iconfont icon-xiangmujibenxinxi"></i>&nbsp;版本描述</a>');
	if(power.indexOf('测试用户') !== -1 || power.indexOf('发布用户') !== -1){
		toolbar.push('<a class="btn my-btn stateBtn" href="javascript:"><i class="iconfont icon-yonghufankui03"></i>&nbsp;修改状态</a>');
	}
	toolbar.push('<a class="btn my-btn sayBtn" href="javascript:"><i class="iconfont icon-pinglun"></i>&nbsp;评论</a>');
  	initToolBar('#' + table, toolbar);

	updatePagination(len, page, data.count, table);
	listenCheckBox('#' + table);
    updateChecked('#' + table);
    // AjaxGet('/Android/Home/App/publishAppLists?unread=true', function(data) {
    // 	if(data.count > 0){
    // 		console.log('nonono');
    // 	$('#msgNum').css({
    // 		"background-color": "#ff0000",
    // 		"border-radius": "10px",
    // 		"color": "#ffffff",
    // 		"line-height": "15px",
    // 		"top": "5px",
    // 		"padding-left": "4px",
    // 		"padding-right": "4px",
    // 		"display": "inline-block",
    // 		"position": "absolute",
    // 		"z-index": "999"
    // 	});
    // 	$('#msgNum').html(data.count.toString());
    // 	}else{
    // 		$('#msgNum').css({
    // 		"background-color": "",
    // 		"border-radius": "",
    // 		"color": "",
    // 		"line-height": "",
    // 		"left": "",
    // 		"top": "",
    // 		"padding-left": "",
    // 		"padding-right": "",
    // 		"z-index": "999"
    // 	});
    // 		$('#msgNum').html('');
    // 	}
    // });
}