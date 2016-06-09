//@ sourceURL=desktop.quickEntrySlot.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function() {
	myData.checkedLists = [];   //存储check选中项
	AjaxGet('/desktop/quickEntrySlotGroupLists', function(data) {
        createGroup(data);
        trHover('#groupTable');
    });
	trclick('#groupTable', function(obj, e) {
        myData.groupId = obj.data('id');
        myData.groupName = obj.data('name');
        myData.groupDesc = obj.data('desc');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//版本列表
            //AjaxGet('/desktop/quickEntrySlotLists?page=1&pageSize=' + pageSize+'&groupId='+myData.groupId, function(data){
            AjaxGet('/desktop/quickEntrySlotLists?page=1&pageSize=' + pageSize+'&groupId='+myData.groupId, function(data){

                $('#fristTable').hide();
                $('#secondTable').show();
                myData.checkedLists = [];
                createQuickSlot(data, 1);
				trHover('#quickSlotTable');
                $('.breadcrumb').append('<li class="active">'+myData.groupName+'</li>');
            });
            return false;
        }
    });

    /*AjaxGet('/desktop/quickEntrySlotLists?page=1&pageSize=' + pageSize, function(data){
        createQuickSlot(data, 1);
		trHover('#quickSlotTable');
    });*/

	listenSingleCheckBox('#quickSlotTable', function(e){
		var tar = e.target;
		var obj = $(tar).parents('tr').find('td:eq(0)');
        myData.slotId = obj.data('id');
		myData.slotApp = obj.data('app');
		myData.slotUri = obj.data('uri');
		myData.slotType = obj.data('type');

		if (tar.className.indexOf('glyphicon-list img-app') != -1) {
			createBind(myData.slotApp); //绑定应用
			$('#bindModal').modal('show');
			return false;
		}
		if (tar.className.indexOf('glyphicon-list img-type') != -1) {
			createJump(myData.slotType); //跳转信息
			$('#jumpModal').modal('show');
			return false;
		}
		if (tar.className.indexOf('glyphicon-list img-uri') != -1) {
			createUri(myData.slotUri); //链接
			$('#uriModal').modal('show');
			return false;
		}
		if (tar.className.indexOf('glyphicon-picture') != -1) {
			window.open($(tar).data('src')); //图片
			return false;
		}
    });

     listenSingleCheckBox('#desktopTable');

	listenfile();
	listenchoose();
	listenPic('#quickSlotTable');
	listenMyPage('quickSlotTable');

});

 listenToolbar('edit', editGroupInfo, '#groupTable');
 listenToolbar('add', addGroupInfo, '#groupTable');
 listenToolbar('del', delGroupInfo, '#groupTable');

listenToolbar('back', backTable, '#quickSlotTable');
listenToolbar('edit', editTableInfo, '#quickSlotTable');
listenToolbar('add', addTableInfo, '#quickSlotTable');
listenToolbar('move', moveTableInfo,'#quickSlotTable');

listenToolbar('del', delTableInfo, '#quickSlotTable');
// listenToolbar('copy', copyTableInfo, '#quickSlotTable');
// listenToolbar('release', releaseTableInfo, '#quickSlotTable');

 function addGroupInfo(){
     $('#groupName').val('');
     $('#groupDesc').val('');
     $('#groupModal h4').text('添加');
     $('#groupModal').modal('show');

}

function editGroupInfo(){
     if (myData.groupId) {
         $('#groupName').val(myData.groupName);
         $('#groupDesc').val(myData.groupDesc);
         $('#groupModal h4').text('修改');
         $('#groupModal').modal('show');
     } else {
         alert('请选择组！');
     }
 }

 function delGroupInfo(){
 	if (myData.groupId) {
         if (confirm('确定删除？')) {
             AjaxGet('/desktop/deleteQuickEntrySlotGroup?id=' + myData.groupId, function() {
                 updateGroup();
             });
         }
     } else {
         alert('请选择组！');
     }
 }

 function updateGroup(){
 	AjaxGet('/desktop/quickEntrySlotGroupLists', function(data){
         createGroup(data);
         myData.groupId = null;
     });
}

function addTableInfo() {
	clearTableInfo();
	$('#versionCodeQuick').val("请选择绑定应用版本");
	$('#appUrlQuick').val("请选择绑定应用路径");
	AjaxWhen([
        AjaxGet('/desktop/actionAppLists', selectApp, true),
		// AjaxGet('/desktop/layoutTypeLists', selectLayout, true),
		AjaxGet('/App/apkLists', selectApk, true)
    ], function(){
        $('#quickSlotModal').find('h4').html('新增');
		$('#quickSlotModal').modal('show');
    });
}
function moveTableInfo(){
    if (myData.checkedLists.length > 0) {
        AjaxGet('/desktop/quickEntrySlotGroupLists', function(data){
            selectMove(data);
            $('#mGroupModal').modal('show');
        });
    } else {
        alert('请选择坑位！');
    }
}
function selectMove(data) {
    var arr = data.extra;
    var con = '<option value="请选择组">请选择组</option>';
    var $select = $('#mGroupName');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }
    $select.html(con);
}
//移动坑位事件
$('#subMGroup').on('click', function(){
    var groupId = $('#mGroupName').val();

    if(groupId === '请选择组' || !groupId){
        alert('请选择组！');
        return;
    }

    AjaxPost('/desktop/getQuickEntrySlotId', myData.checkedLists, function(idData){
        var con = "";
        for(var i = 0, len = idData.extra.length; i < len; i++){
            con += idData.extra[i] + '\n';
        }
        if (confirm('确定移动id为：\n'+ con +'的坑位？')) {
            var filter = $('#quickSlotTable_filter input').val() || '';
            AjaxPost('/desktop/moveQuickEntrySlot', {"ids":myData.checkedLists, "groupId":groupId}, function() {
                alert('移动成功');
                myData.checkedLists = [];
                updateTable(currentPage, filter);
                $('#mGroupModal').modal('hide');
                return false;
            });
        }
    });

    return false;
});
function editTableInfo() {
	if (myData.checkedLists.length === 1) {
		var obj = $('.checkSelected td:eq(0)');
        myData.slotId = obj.data('id');
		clearTableInfo();
		AjaxWhen([
	        AjaxGet('/desktop/actionAppLists', selectApp, true),
			// AjaxGet('/desktop/layoutTypeLists', selectLayout, true),
			AjaxGet('/App/apkLists', selectApk, true)
	    ], function(){
	        $('#quickSlotModal').find('h4').html('修改');
			AjaxGet('/desktop/quickEntrySlotLists?id=' + myData.slotId, function(data) {
				data = data.extra;
				$('#slotIdQuick').val(data.slotId);
				$('#soltTitleQuick').val(data.title);
				// if(data.disconnectEnable == 'false'){
				// 	$('#disconnectType input:eq(1)').trigger('click');
				// }else if(data.disconnectEnable == 'true'){
				// 	$('#disconnectType input:eq(0)').trigger('click');
				// }
				// if(data.isModifySource == 'false'){
				// 	$('#isModifySource input:eq(1)').trigger('click');
				// }else if(data.isModifySource == 'true'){
				// 	$('#isModifySource input:eq(0)').trigger('click');
				// }
				// if (data.dataSource === 'yunos') {
				// 	$('#editTypeQuick input:eq(1)').trigger('click');
				// 	$('#dataType > input:eq(0)').trigger('click');
				// 	$('#quickSlotModal').modal('show');
				// 	return false;
				// }

				// if(data.dataSource === 'linkin'){
				// 	$('#dataType > input:eq(1)').trigger('click');
				// }else if(data.dataSource === 'linkinOnly'){
				// 	$('#dataType > input:eq(2)').trigger('click');
				// }

				$('#quickFileShow1').val(data.focusedDrawable);
				$('#quickFileShow2').val(data.normalDrawable);
				if(data.isEditable == 'false'){
					$('#editTypeQuick input:eq(1)').trigger('click');
				}else if(data.isEditable == 'true'){
					$('#editTypeQuick input:eq(0)').trigger('click');
				}

				if (data.actionType === 'URI') {
					$('#jumpTypeQuick > input:eq(1)').trigger('click');
					$('#uriNameQuick').val(data.uri.uriName);
					$('#uriValQuick').val(data.uri.uri);
				} else {
					$('#jumpTypeQuick > input:eq(0)').trigger('click');
					var actionData = data.action || data.component || data.app || data.scheme;
					var $jumpApp = $('#jumpAppQuick');
					var optionA = $jumpApp.find('option').filter('[data-name="' + actionData.appName + '"]');
					optionA.prop("selected", true);
					$jumpApp.trigger("chosen:updated.chosen").chosen({
						allow_single_deselect: true,
						width: "70%"
					});

					if (data.actionType !== 'APP') {
						$jumpApp.trigger('change', actionData.detailName);
					} else {
						$jumpApp.trigger('change');
					}
				}

				if (data.bindApp) {
					setTimeout(function(){
						$('#appNameQuick').val(data.bindApp.appName).trigger("chosen:updated.chosen").trigger('change', data.bindApp);
					}, 300);
					$('#appTypeQuick > input:eq(0)').trigger('click');
				} else {
					$('#appTypeQuick > input:eq(1)').trigger('click');
				}

				// $('#layoutType').val(data.layout).trigger('change');
				// if (data.layout === 'VIDEO') {
				// 	setChosenVal(data.videos);
				// }
				$('#quickSlotModal').modal('show');
			});
	    });
	} else {
		alert('请选择一个坑位');
		return false;
	}
}

// function copyTableInfo () {
// 	if (myData.checkedLists.length > 0) {
//         AjaxGet('/desktop/operationSlotGroupLists', function(data){
//         	selectCopy(data);
//         	$('#copyModal').modal('show');
//         });
//     } else {
//         alert('请选择坑位');
//     }
// }

function delTableInfo() {
	if (myData.checkedLists.length > 0) {
		var obj = $('.checkSelected td:eq(0)');
        myData.slotId = obj.data('id');
        AjaxPost('/desktop/getQuickEntrySlotId', myData.checkedLists, function(checkedData){
        	var arr = checkedData.extra;
        	var con = '';
        	for(var i = 0, len = arr.length; i < len; i++){
        		con += arr[i] + '\n';
        	}
        	if (confirm('确定删除ID为：\n'+ con +'的坑位？')) {
				var filter = $('#quickSlotTable_filter input').val() || '';
				AjaxPost('/desktop/deleteQuickEntrySlot', myData.checkedLists, function(data) {
					updateTable(currentPage, filter);
					myData.checkedLists = [];
					if(data.reason){
						alert(data.reason);
					}
				});
			}
        });
	} else {
		alert('请选择坑位');
		return false;
	}
}

function releaseTableInfo() {
	if (myData.slotId) {
		AjaxWhen([
            AjaxGet('/group/nameLists', selectGroup, true),
	        AjaxGet('/desktop/getOperationSlot?id=' + myData.slotId, createDesktop, true)
        ], function(){
            trHover('#desktopTable');
	        $('#chooseType > input:eq(0)').trigger('click');
	        $('#desktopTable_wrapper').css('padding', '0 45px');
			$('#desktopModal').modal('show');
        });
	} else {
		alert('请选择坑位');
		return false;
	}
}

function updateTable(page, name) {
	var url = '';
	if (name) {
		url = '/desktop/quickEntrySlotLists?name=' + name + '&page=' + page + '&pageSize=' + pageSize+'&groupId='+myData.groupId;
	} else {
		url = '/desktop/quickEntrySlotLists?page=' + page + '&pageSize=' + pageSize+'&groupId='+myData.groupId;
	}
	AjaxGet(url, function(data) {
		createQuickSlot(data, page);
		myData.slotId = null;
	});
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.slotId = null;
}

// function setChosenVal(videoVal) { //修改时根据videoVal，初始化chosen的值
// 	videoVal.forEach(function(elem) {
// 		myData.videoLists.push([elem.url, elem.duration]);
// 		$('#videoList').append('<div><label title="' + elem.url + '--' + elem.duration + '">' + elem.url + '--' + elem.duration + '</label><button type="button" class="close">×</button></div>');
// 	});
// }

function clearTableInfo() {
	$('#slotIdQuick').val("");
	$('#soltTitleQuick').val("");
	$('#quickFileShow1').val("");
	$('#quickFileShow2').val("");
	$('#quickFileHide1').val("");
	$('#quickFileHide2').val("");
	$('#appNameQuick').val("请选择绑定应用名称");

	// $('#dataType > input:eq(1)').trigger('click');
	$('#editTypeQuick input:eq(1)').trigger('click');

	$('#jumpTypeQuick > input:eq(0)').trigger('click');
	// $('#layoutType').val('请选择布局类型').trigger('change');

	$('#uriNameQuick').val("");
	$('#uriValQuick').val("");
	// $('#videoList').html("");
	// $('#videoUrl').val("");
	// $('#videoDuration').val("");
	// myData.videoLists = [];

	$('#versionCodeQuick').html('<option value="请选择绑定应用版本">请选择绑定应用版本</option>');
	$('#appUrlQuick').html('<option value="请选择绑定应用路径">请选择绑定应用路径</option>');
}

function  selectCopy (data) {
	var arr = data.extra;
	var con = '<option value="请选择组">请选择组</option>';
	var $select = $('#copyGroup');
	var len = arr.length;
	for (var i = 0; i < len; i++) {
		con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
	}
	$select.html(con);
}

//创建跳转应用下拉框
function selectApp(data) {
	var arr = data.extra;
	var con = '<option value="请选择跳转应用">请选择跳转应用</option>';
	var $select = $('#jumpAppQuick');
	var len = arr.length;
	for (var i = 0; i < len; i++) {
		con += '<option value="' + arr[i].id + '" data-name="' + arr[i].appName + '" >' + arr[i].appName + '</option>';
		$select.data('_' + arr[i].id, arr[i]);
	}
	$select.html(con).trigger("chosen:updated.chosen").chosen({
		allow_single_deselect: true,
		width: "70%"
	}).trigger('change');
}

//根据跳转应用变化创建跳转详情页
$('#jumpAppQuick').on('change', function(e, name) {
	var id = $(this).val();
	var $select = $('#jumpDetailQuick');
	var $appName = $('#appNameQuick');
	var appVal = $(this).find('option:checked').text();
	if (id === '请选择跳转应用') {
		$select.html('<option value="请选择跳转详情页">请选择跳转详情页</option>').trigger("chosen:updated.chosen").chosen({
			allow_single_deselect: true,
			width: "70%"
		});
		$appName.val('请选择绑定应用名称').trigger("chosen:updated.chosen").chosen({
			allow_single_deselect: true,
			width: "70%"
		}).trigger('change');
		return false;
	}
	$appName.val(appVal).trigger("chosen:updated.chosen").chosen({
			allow_single_deselect: true,
			width: "70%"
		}).trigger('change');
	AjaxGet('/desktop/actionAppLists?id=' + id, function(data) {
		var arr = data.extra.extraData;
		var con = '<option value="请选择跳转详情页">请选择跳转详情页</option>';
		var len = arr.length;
		for (var i = 0; i < len; i++) {
			con += '<option value="' + arr[i].id + '" data-name="' + arr[i].detailName + '" >' + arr[i].detailName + '</option>';
			$select.data('_' + arr[i].id, arr[i]);
		}
		$select.html(con);
		if (name) {
			var option = $select.find('option').filter('[data-name="' + name + '"]');
			option.prop("selected", true);
		}
		$select.trigger("chosen:updated.chosen").chosen({
			allow_single_deselect: true,
			width: "70%"
		});
	});
});

//创建布局类型下拉框
// function selectLayout(data){
// 	var arr = data.extra;
// 	var con = '<option value="请选择布局类型">请选择布局类型</option>';
// 	var $select = $('#layoutType');
// 	var len = arr.length;
// 	for (var i = 0; i < len; i++) {
// 		con += '<option value="' + arr[i].type + '">' + arr[i].name + '</option>';
// 	}
// 	$select.html(con);
// }

//创建绑定应用下拉框
function selectApk(data){
	var arr = data.extra;
	var con = '<option value="请选择绑定应用名称">请选择绑定应用名称</option>';
	var $select = $('#appNameQuick');
	var len = arr.length;
	for (var i = 0; i < len; i++) {
		con += '<option value="' + arr[i].appName + '">' + arr[i].appName + '</option>';
		$select.data('_' + arr[i].appName, {
			"icon": arr[i].icon,
			"pkgName": arr[i].pkgName
		});
	}
	$select.html(con).trigger("chosen:updated.chosen").chosen({
		allow_single_deselect: true,
		width: "70%"
	});
}

$('#appNameQuick').on('change', function(e, bindApp){
	var val = $(this).val();
	if(val === '请选择绑定应用名称'){
		return false;
	}
	if(!val && bindApp){
		alert('绑定应用' + bindApp.appName + '不存在！');
		return false;
	}
	AjaxGet('/App/apkVersionLists?appName=' + val, function(data){
		selectVersion(data, bindApp);
	});
});

function selectVersion(data, bindApp) {
	var arr = data.extra;
	var con = '<option value="请选择绑定应用版本">请选择绑定应用版本</option>';
	var $select = $('#versionCodeQuick');
	var len = arr.length;
	for (var i = 0; i < len; i++) {
		con += '<option value="' + arr[i].versionCode + '">' + arr[i].versionCode + '</option>';
		$select.data('_' + arr[i].versionCode, {
			"path": arr[i].path,
			"path3rd": arr[i].path3rd
		});
	}
	if(bindApp && bindApp.url){
		$select.html(con).val(bindApp.versionCode).trigger('change', bindApp.url);
	}else{
		$select.html(con).trigger('change');
	}
}

$('#versionCodeQuick').on('change', function(e, url){
	var val = $('#versionCodeQuick option:checked').text();
	if(val === '请选择绑定应用版本'){
		$('#appUrlQuick').html('<option value="请选择绑定应用路径">请选择绑定应用路径</option>');
		return false;
	}
	selectUrl($(this).data('_' + val), url);
});

function selectUrl(data, url){
	var con = '';
	var $select = $('#appUrlQuick');
	if(!data){
		alert('绑定应用版本不存在！');
		return false;
	}
	if(data.path3rd){
		con += '<option value="'+ data.path3rd +'">外链</option>';
	}
	if(data.path){
		con += '<option value="'+ data.path +'">链接</option>';
	}
	if(url){
		$select.html(con).val(url);
	}else{
		$select.html(con);
	}
}

//是否为云数据变化
// $('#dataType > input').on('click', function() {
// 	var $this = $(this);
// 	var val = $this.val();
// 	$this.prop('checked', true);
// 	if (val === 'yunos') {
// 		$('.linkinType').hide();
// 		$('#editTypeQuick').hide();
// 	} else{
// 		$('.linkinType').show();
// 		$('#editTypeQuick').show();
// 		$('#jumpTypeQuick input:checked').trigger('click');
// 		$('#appTypeQuick input:checked').trigger('click');
// 		$('#layoutType').trigger('change');
// 	}
// });

//是否为可替换变化
$('#editTypeQuick > input').on('click', function() {
	var $this = $(this);
	var val = $this.val();
	// var layoutType = $('#layoutType').val();
	$this.prop('checked', true);
	if (val === 'false') {
		$('#appTypeQuick input:eq(0)').trigger('click');
	}else{
		$('#appTypeQuick input:checked').trigger('click');
	}
});

//跳转类型变化事件
$('#jumpTypeQuick > input').on('click', function() {
	var $this = $(this);
	var val = $this.val();
	$this.prop('checked', true);
	if (val === 'APP') {
		$('#uriNameQuick').parent().hide();
		$('#uriValQuick').parent().hide();
		$('#jumpAppQuick').parent().show();
		$('#jumpDetailQuick').parent().show();
	} else if (val === 'URI') {
		$('#handleType').parent().hide();
		$('#jumpAppQuick').parent().hide();
		$('#jumpDetailQuick').parent().hide();
		$('#uriNameQuick').parent().show();
		$('#uriValQuick').parent().show();
	}
});

//是否绑定应用变化
$('#appTypeQuick > input').on('click', function() {
	var $this = $(this);
	var val = $this.val();
	// var layoutType = $('#layoutType').val();
	$this.prop('checked', true);
	if (val === 'true') {
		$('.appTypeQuick').show();
	} else if (val === 'false') {
		var jumpTypeQuick = $('#jumpTypeQuick input:checked').val();
		if($('#editTypeQuick input:checked').val() === 'false' && jumpTypeQuick === 'APP'){
			$('#appTypeQuick input:eq(0)').trigger('click');
		}else{
			$('.appTypeQuick').hide();
		}
	}
});

// //布局类型变化事件
// $('#layoutType').on('change', function() {
// 	var $this = $(this);
// 	var val = $this.val();
// 	var editType = $('#editTypeQuick input:checked').val();
// 	if (val === 'VIDEO') {
// 		$('#addVideo').show();
// 	} else {
// 		$('#addVideo').hide();
// 	}
// 	if(editType === 'false' && (val === 'APP' || val === 'APP_CENTER_IMG_BOTTOM_TEXT')){
// 		$('#appTypeQuick input:eq(0)').trigger('click');
// 	}
// 	var $select = $('#jumpDetailQuick');
// 	if(val === 'APP' || val === 'APP_CENTER_IMG_BOTTOM_TEXT' || val === '请选择布局类型'){
// 		$select.parent().hide();
// 		$select.html('<option value="请选择跳转详情页">请选择跳转详情页</option>').trigger("chosen:updated.chosen").chosen({
// 			allow_single_deselect: true,
// 			width: "70%"
// 		});
// 		$('.picType').hide();
// 	}else{
// 		$select.parent().show();
// 		$('.picType').show();
// 	}
// 	$('#jumpTypeQuick input:checked').trigger('click');
// });

$('#addVideo a').on('click', function() { //添加视频列表事件
	$('#videoListModal').modal('show');
	$('#quickSlotModal').modal('hide');
});

$('#videoListModal .my-back').on('click', function() {
	$('#quickSlotModal').modal('show');
	$('#videoListModal').modal('hide');
});

//添加视频列表
$('#subVideoList').on('click', function() {
	var url = $('#videoUrl').val();
	var duration = $('#videoDuration').val();

	if (url == ' ' || !url) {
		alert('请输入路径');
		return;
	}
	if (duration == ' ' || !duration) {
		alert('请输入持续时间');
		return;
	}
	if (/\D/.test(duration)) {
		alert('持续时间只能为数字');
		return;
	}

	for (var i = 0, len = myData.videoLists.length; i < len; i++) {
		var elem = myData.videoLists[i];
		if (elem[0] === url) {
			alert('该路径已存在');
			return;
		}
	}

	myData.videoLists.push([url, duration]);
	$('#videoList').append('<div><label title="' + url + '--' + duration + '">' + url + '--' + duration + '</label><button type="button" class="close">×</button></div>');
});

//删除视频列表
$('.my-listVal').on('click', '.close', function() {
	$this = $(this);
	var elem = $this.siblings('label').text().split('--');
	var url = elem[0];
	var duration = elem[1];
	myData.videoLists.forEach(function(e, i) {
		if (e[0] === url) {
			myData.videoLists.splice(i, 1);
			return;
		}
	});
	$this.parent().remove();
});

//复制坑位
$('#subCopy').on('click', function(){
	var copyGroup = $('#copyGroup').val();
	if(copyGroup === '请选择组' || !copyGroup){
		alert('请选择组');
		return false;
	}

	AjaxPost('/desktop/getOperationSlotId', myData.checkedLists, function(checkedData){
    	var arr = checkedData.extra;
    	var con = '';
    	for(var i = 0, len = arr.length; i < len; i++){
    		con += arr[i] + '\n';
    	}
    	if (confirm('确定复制ID为：\n'+ con +'的坑位？')) {
			AjaxPost('/desktop/copyOperationSlot', {"slotGroupId": copyGroup, "idLists": myData.checkedLists}, function () {
				alert('复制成功');
				$('#copyModal').modal('hide');
				return false;
			});
		}
    });

    return false;
});

//提交坑位
$('#subQuickSlot').on('click', function() {
	var slotID = $('#slotIdQuick').val();
	var soltTitle = $('#soltTitleQuick').val();
	var appName = $('#appNameQuick').val();
	var versionCode = $('#versionCodeQuick').val();
	var appUrl = $('#appUrlQuick').val();
	var jumpType = $('#jumpTypeQuick > input:checked').val();
	var appType = $('#appTypeQuick > input:checked').val();
	var editType = $('#editTypeQuick > input:checked').val();
	// var disconnectType = $('#disconnectType > input:checked').val();
	// var isModifySource = $('#isModifySource input:checked').val();
	// var layoutType = $('#layoutType').val();
	var data = {};
	var picData = new FormData();
	var title = $('#quickSlotModal').find('h4').html();
	var filter = $('#quickSlotTable_filter input').val() || '';

	if (slotID == ' ' || !slotID) {
		alert('请输入坑位ID');
		return false;
	}
	if (/\D/.test(slotID)){
		alert('坑位ID只能为数字');
		return false;
	}

	if(editType === 'false' && appType === 'false' && jumpType === 'APP'){
		alert('不可替换类型必须绑定应用');
		return false;
	}
	data = {
		"slotId": slotID,
		"title": soltTitle,
		"isEditable": editType,
		"groupId": myData.groupId
	};

	var fileObj1 = document.getElementById("quickFileHide1").files[0];
	var fileVal1 = $("#quickFileShow1").val();
	var fileObj2 = document.getElementById("quickFileHide2").files[0];
	var fileVal2 = $("#quickFileShow2").val();

	if (fileVal1 == ' ' || !fileVal1) {
		alert('请选择要上传焦点图片');
		return;
	}
	if (fileVal2 == ' ' || !fileVal2) {
		alert('请选择要上传正常图片');
		return;
	}

	if (fileVal1 != ' ' && fileVal1.indexOf('http') == -1 && fileVal1) {
		picData.append("pic1", fileObj1);
	}
	if (fileVal2 != ' ' && fileVal2.indexOf('http') == -1 && fileVal2) {
		picData.append("pic2", fileObj2);
	}

	if (fileVal1.indexOf('http') != -1) {
		data.focusedDrawable = fileVal1;
	}
	if (fileVal2.indexOf('http') != -1) {
		data.normalDrawable = fileVal2;
	}

	if (jumpType === 'URI') {
		data.uri = {};
		data.uri.uriName = $('#uriNameQuick').val();
		data.uri.uri = $('#uriValQuick').val();
		data.actionType = jumpType;
		if (data.uri.uriName == ' ' || !data.uri.uriName) {
			alert('请输入URI名称');
			return false;
		}
		if (data.uri.uri == ' ' || !data.uri.uri) {
			alert('请输入URI值');
			return false;
		}
	} else {
		var $jumpApp = $('#jumpAppQuick');
		if ($jumpApp.val() == '请选择跳转应用') {
			alert('请选择跳转应用');
			return false;
		}
		var $jumpDetail = $('#jumpDetailQuick');
		var appData = $jumpApp.data('_' + $jumpApp.val());
		if ($jumpDetail.val() == '请选择跳转详情页' || $jumpDetail.parent().is(':hidden')) {
			data.actionType = 'APP';
			data.app = {
				"appName": appData.appName,
				"pkgName": appData.pkgName
			};
		} else {
			var detailDate = $jumpDetail.data('_' + $jumpDetail.val());
			data.actionType = detailDate.actionType;
			if (detailDate.actionType === 'ACTION') {
				data.action = {
					"appName": appData.appName,
					"detailName": detailDate.detailName,
					"action": detailDate.action,
					"extraData": detailDate.extraData
				};
			} else if (detailDate.actionType === 'COMPONENT') {
				data.component = {
					"appName": appData.appName,
					"detailName": detailDate.detailName,
					"component": detailDate.component,
					"clsName": detailDate.clsName,
					"extraData": detailDate.extraData
				};
			}else if(detailDate.actionType === 'SCHEME'){
				data.scheme = {
					"appName": appData.appName,
					"detailName": detailDate.detailName,
					"uri": detailDate.uri,
					"action": detailDate.action,
					"extraData": detailDate.extraData
				};
			}
		}
	}
    var appNameData = '';
    var pkgName = '';
    var icon = '';
    if(appType === 'true'){
        appName = $('#appNameQuick').val();
        appNameData = $('#appNameQuick').data('_' + appName);
        pkgName = appNameData.pkgName;
        icon = appNameData.icon;
    }

	// if(layoutType === 'APP' || layoutType === 'APP_CENTER_IMG_BOTTOM_TEXT'){
	// 	if(icon === ''){
	// 		alert('该应用没有图标，请在第三方应用上传');
	// 		return false;
	// 	}
	// 	data.pic1 = data.pic2 = data.pic3 = icon;
	// }

	if (appType === 'true') {
		if (versionCode == '请选择绑定应用版本') {
			alert('请选择绑定应用版本');
			return false;
		}
		if (appUrl == '请选择绑定应用路径') {
			alert('请选择绑定应用路径');
			return false;
		}

		data.bindApp = {
			"appName": appName,
			"pkgName": pkgName,
			"versionCode": versionCode,
			"url": appUrl,
			"autoInstall": 'true'
		};
	}
	if (title === '新增') {
		// if (data.pic1 && data.pic2 && data.pic3) {
		// 	AjaxPost('/desktop/addQuickEntrySlot', data, function() {
		// 		$('#quickSlotModal').modal('hide');
		// 		updateTable(currentPage, filter);
		// 	});
		// 	return false;
		// }
		picData.append('additional', 'slot');
		AjaxFile('/desktop/updataImage', picData, function(imgData) {
			data.focusedDrawable = imgData.pic1;
			data.normalDrawable = imgData.pic2;
			AjaxPost('/desktop/addQuickEntrySlot', data, function() {
				$('#quickSlotModal').modal('hide');
				updateTable(currentPage, filter);
			});
		});
	} else if (title === '修改') {
		data.id = myData.slotId;
		// data.isModifySource = isModifySource;
		if (data.focusedDrawable && data.normalDrawable) {
			AjaxPost('/desktop/modifyQuickEntrySlot', data, function() {
				$('#quickSlotModal').modal('hide');
				updateTable(currentPage, filter);
			});
			return false;
		}
		picData.append('additional', 'slot');
		AjaxFile('/desktop/updataImage', picData, function(imgData) {
			if (!data.focusedDrawable) {
				data.focusedDrawable = imgData.pic1;
			}
			if (!data.normalDrawable) {
				data.normalDrawable = imgData.pic2;
			}
			AjaxPost('/desktop/modifyQuickEntrySlot', data, function() {
				$('#quickSlotModal').modal('hide');
				updateTable(currentPage, filter);
			});
		});
	}
});


//创建坑位列表
function createQuickSlot(data, page) {
	var dataArr = [];
	var len = data.extra.length;
	for (var i = 0; i < len; i++) {
		var arr = data.extra[i];
		var type = arr.app || arr.action || arr.component || arr.scheme || '--';
		var appName = type.appName || '--';
		var detailName = type.detailName || '--';
		var uri = arr.uri || '--';
		var bindApp = arr.bindApp || '--';
		var pic1 = arr.focusedDrawable || '--';
		var pic2 = arr.normalDrawable || '--';
		dataArr.push([arr.id, arr.slotId, arr.title, pic1, pic2, appName, detailName, uri, bindApp, type]);
	}
	$('#quickSlotTable').dataTable({
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
			[1, 'asc']
		],
		"columnDefs": [
		{
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '6%',
            'targets': 0,
            "orderable": false
        },
		{
			'title': '坑位ID',
			'width': '6%',
			'targets': 1
		},
		{
			'title': '坑位标题',
			'width': '8%',
			'targets': 2
		},
		// {
		// 	'title': '是否可替换',
		// 	'width': '5%',
		// 	'targets': 3
		// },
		{
			'title': '焦点',
			'width': '4%',
			'targets': 3
		},{
			'title': '正常',
			'width': '4%',
			'targets': 4
		},{
			'title': '应用名称',
			'width': '10%',
			'targets': 5
		},{
			'title': '详情页',
			'width': '10%',
			'targets': 6
		},{
			'title': '链接',
			'width': '4%',
			'targets': 7
		},{
			'title': '绑定应用',
			'width': '4%',
			'targets': 8
		},{
			'title': '跳转信息',
			'width': '4%',
			'targets': 9
		}],
		"createdRow": function(nRow, aData, iDataIndex) {
			// if (aData[3] !== 'true') {
			// 	$('td:eq(3)', nRow).html('否').css('color', '#f70');
			// }else{
			// 	$('td:eq(3)', nRow).html('是').css('color', '#0a3');
			// }
			if (aData[3] !== '--') {
				$('td:eq(3)', nRow).html('<i class="glyphicon glyphicon-picture icon-black my-icon" data-src="' + aData[3] + '"></i>').addClass('center');
			}else{
				tableTdNull(3, nRow);
			}
			if (aData[4] !== '--') {
				$('td:eq(4)', nRow).html('<i class="glyphicon glyphicon-picture icon-black my-icon" data-src="' + aData[4] + '"></i>').addClass('center');
			}else{
				tableTdNull(4, nRow);
			}

			if (aData[8] !== '--') {
				tableTdIcon(8, nRow, 'list img-app');
			}else{
				tableTdNull(8, nRow);
			}
			if (aData[7] !== '--') {
				tableTdIcon(7, nRow, 'list img-uri');
			}else{
				tableTdNull(7, nRow);
			}
			if (aData[9] !== '--') {
				tableTdIcon(9, nRow, 'list img-type');
			}else{
				tableTdNull(9, nRow);
			}
			$('td:eq(1)', nRow).addClass('title-checked');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				'app': aData[8],
				'uri': aData[7],
				'type': aData[9],
			}).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
		},
		"language": {
			"zeroRecords": "没有检索到数据",
			"infoEmpty": "没有数据"
		}
	});
	updatePagination(len, page, data.count, 'quickSlotTable');
	initToolBar('#quickSlotTable', [
		myConfig.backBtn,
		myConfig.addBtn,
		myConfig.editBtn,
		myConfig.delBtn,
        '<a class="btn my-btn btn-primary moveBtn" href="javascript:"><i class="fa fa-cut icon-white"></i>&nbsp;移动</a>'

	]);

	listenCheckBox('#quickSlotTable');
    updateChecked('#quickSlotTable');
		//alert('test3')

}

//创建视频列表
function createVideo(data) {
	var dataArr = [];
	var len = data.length;
	for (var i = 0; i < len; i++) {
		var arr = data[i];
		dataArr.push([arr.url, arr.duration]);
	}
	myDataTable('#videoTable', {
		"data": dataArr,
		"columnDefs": [{
			'title': '视频路径',
			'width': '75%',
			'targets': 0
		}, {
			'title': '持续时间（秒）',
			'width': '25%',
			'targets': 1
		}, ]
	});
}

//创建绑定应用
function createBind(data) {
	var spans = $('#bindModal .bind-span');
	$(spans[0]).text(data.appName);
	$(spans[1]).text(data.pkgName);
	$(spans[2]).text(data.versionCode);
	$(spans[3]).text(data.url);
	// if (data.autoInstall === 'true') {
	// 	$(spans[4]).text('是');
	// } else if (data.autoInstall === 'false') {
	// 	$(spans[4]).text('否');
	// }
}

//创建连接
function createUri(data) {
	var spans = $('#uriModal .bind-span');
	$(spans[0]).text(data.uriName);
	$(spans[1]).text(data.uri);
}

//创建跳转信息
function createJump(data) {
	var spans = $('#jumpModal .bind-span');
	$(spans[0]).text(data.appName);
	if (data.pkgName) {
		$(spans[1]).text(data.pkgName).parent().show();
	} else {
		$(spans[1]).parent().hide();
	}
	if (data.detailName) {
		$(spans[2]).text(data.detailName).parent().show();
	} else {
		$(spans[2]).parent().hide();
	}
	if (data.action) {
		$(spans[3]).text(data.action).parent().show();
	} else {
		$(spans[3]).parent().hide();
	}
	if (data.component) {
		$(spans[4]).text(data.component).parent().show();
	} else {
		$(spans[4]).parent().hide();
	}
	if (data.clsName) {
		$(spans[5]).text(data.clsName).parent().show();
	} else {
		$(spans[5]).parent().hide();
	}
	if (data.uri) {
		$(spans[6]).text(data.uri).parent().show();
	} else {
		$(spans[6]).parent().hide();
	}

	$('#jumpExtra').nextAll().remove();
	if (!data.extraData) {
		return false;
	}

	var len = data.extraData.length;
	var con = '';
	var type = {
        "int": "整型",
        "long": "长整型",
        "float": "浮点型",
        "double": "双精度浮点型",
        "boolean": "布尔型",
        "char": "字符型",
        "string": "字符串型"
    };
	for (var i = 0; i < len; i++) {
		var arr = data.extraData[i];
		var myType = '&nbsp;';
        if(arr.type){
            myType = type[arr.type];
        }
		con +=  '<div class="form-group" style="margin-bottom: 2px;">' +
                    '<label></label>' +
                    '<span style="display: inline-block;width: 19%;padding: 7px 10px;border: 1px solid #ccc;">' + arr.key + '</span>' +
                    '&nbsp;=&nbsp;' +
                    '<span style="display: inline-block;width: 19%;padding: 7px 10px;border: 1px solid #ccc;margin-left: 0;">' + arr.value + '</span>' +
                    '&nbsp;&nbsp;' +
                    '<span style="display: inline-block;width: 19%;padding: 7px 10px;border: 1px solid #ccc;margin-left: 0;">' + myType + '</span>' +
                '</div>';
	}
	$('#jumpExtra').after(con);
}

//创建发布桌面列表
function createDesktop(data) {
	var dataArr = [];
	var len = data.extra.length;
	for (var i = 0; i < len; i++) {
		var arr = data.extra[i];
		dataArr.push([arr.desktopID, arr.desktopName]);
	}
	myDataTable('#desktopTable', {
		"data": dataArr,
		"order": [
			[1, "desc"]
		],
		"paging": false,
		"pageLength": 1,
		"columnDefs": [{
			'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
			'width': '15%',
			'targets': 0,
			"orderable": false,
			'defaultContent': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>'
		},{
			'title': '桌面名称',
			'width': '50%',
			'targets': 1
		}, ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0]
			});
		}
	});
	listenCheckbox();
}

//监听发布桌面列表checkbox
function listenCheckbox() {
	$('#desktopTable').off('click', 'th input:checkbox').on('click', 'th input:checkbox', function() {
		var that = this;
		$(this).closest('table').find('tr > td:first-child input:checkbox')
			.each(function() {
				this.checked = that.checked;
				$(this).closest('tr').toggleClass('selected');
				if (that.checked) {
					$(this).closest('tr').addClass('checkSelected');
				} else {
					$(this).closest('tr').removeClass('checkSelected');
				}
			});
	});

	$('#desktopTable').off('click', 'tbody tr td input').on('click', 'tbody tr td input', function() {
		var $this = $(this);
		var $tr = $this.parents('tr');
		if ($this.prop('checked')) {
			$tr.addClass('checkSelected');
		} else {
			$tr.removeClass('checkSelected');
		}
	});
}

//发布选中桌面
$('#subDesktop').on('click', function() {
	var type = $('#chooseType input:checked').val();
	var title = $('#desktopModal h4').text();
	var desktopIDList = [];
	var checkeds = $('#desktopTable tbody tr td input:checked');
	var len = checkeds.length;
	var url = '';
	var data = {};
	if(type === 'group'){
		var groupId = $('#group').val();
		data.groupId = groupId;
	}
	data.type = type;
	if (len === 0) {
		alert('请选择桌面');
		return false;
	}
	for (var i = 0; i < len; i++) {
		var $td = $(checkeds[i]).parents('td');
		desktopIDList.push($td.data('id') + '');
	}
	data.desktopIDList = desktopIDList;
	$.ajax({
		url: '/desktop/autoPublishDesktop',
		beforeSend: function() {
			showLoading();
		},
		type: 'post',
		data: JSON.stringify(data),
		dataType: 'json',
		success: function(data) {
			hideLoading();
			if (data.result == "fail") {
				if (data.reason == "登录超时，请重新登录" || data.reason == "未登录，请重新登录") {
					window.location.href = myConfig.logOutUrl;
				}
				var con = '';
				var obj = null;
				for (var i = 0, len = data.failList.length; i < len; i++) {
					obj = data.failList[i];
					con += obj.desktopName + obj.reason + '\n';
				}
				alert(con);
				return false;
			} else {
				alert('发布成功');
				$('#desktopModal').modal('hide');
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			hideLoading();
			ajaxError(XMLHttpRequest, textStatus, errorThrown);
		}
	});
});

$('#chooseType > input').on('click', function(){
	var $this = $(this);
	$this.prop('checked', true);
	var val = $this.val();
	if(val === 'group'){
		$('#group').parent().show();
	}else if(val === 'ALL'){
		$('#group').parent().hide();
	}
});

//创建内测组下拉框
function selectGroup(data){
    var arr = data.groups;
    var con = '';
    var $select = $('#group');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
    }
    $select.html(con);
}

$('#quickSlotModal .form-group > span.lbl').on('click', function() {
	var val = $('#appTypeQuick input:checked').val();
	var jumpTypeQuick = $('#jumpTypeQuick input:checked').val();
	// var layoutType = $('#layoutType').val();
	if (val === 'false' && jumpTypeQuick === 'APP') {
		if($('#editTypeQuick input:checked').val() === 'false'){
			$('#appTypeQuick input:eq(0)').trigger('click');
		}else{
			$('.appTypeQuick').hide();
		}
		return false;
	}
    $(this).prev('input').trigger('click');
});

$('#subGroup').on('click', function(){
    var groupName = $('#groupName').val();
    var groupDesc = $('#groupDesc').val();

    var title = $('#groupModal h4').text();
    var filter = $('#groupTable_filter input').val() || '';
    var data = {};

    if(groupName == ' ' || !groupName){
        alert('请输入组名称');
        return false;
    }
    data = {"name": groupName,"desc":groupDesc};

    if(title === '添加'){
    	AjaxPost('/desktop/addQuickEntrySlotGroup', data, function(){
            $('#groupModal').modal('hide');
            updateGroup();
        });
    }else if(title === '修改'){
        data.id = myData.groupId;
        AjaxPost('/desktop/modifyQuickEntrySlotGroup', data, function(){
            $('#groupModal').modal('hide');
            updateGroup();
        });
    }
});

//创建坑位组
function createGroup(data) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name,arr.desc, null]);
    }
    myDataTable('#groupTable', {
        "data": dataArr,
        "columnDefs": [
        {
            'title': 'ID',
            'width': '25%',
            'targets': 0
        },{
            'title': '组名称',
            'width': '25%',
            'targets': 1
        },{
            'title': '描述',
            'width': '25%',
            'targets': 2
        },{
            'title': '快捷坑位列表',
            'width': '25%',
            'targets': 3
        }],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(3, nRow, 'list');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
				"desc": aData[2]
            });
        }
    });
    initToolBar('#groupTable', [myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}