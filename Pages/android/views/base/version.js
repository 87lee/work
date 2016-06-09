//@ sourceURL=base.version.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	myData.checkedLists = [];   //存储check选中项
	var data = {};

	AjaxGet('/Android/Home/Base/publishModuleLists?page=1&pageSize=' + pageSize, function(data){
        createVersion(data, 1);
    });

    listenSingleCheckBox('#bVersionTable', function(e){
    });

    $('#bDetailTable').on('click', 'tbody tr', function(ev) {
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
				createDetail(data);
				createRely(data);
			});
        }
        return false;
    });

    checkMoz();
	listenfile();
	listenMyPage('bVersionTable', currentPage, updateVersion);
});

listenToolbar('publish', publishXml, '#bVersionTable');
listenToolbar('del', delXml, '#bVersionTable');
listenToolbar('detail', detailXml, '#bVersionTable');
listenToolbar('say', sayXml, '#bVersionTable');

function publishXml(){
	$('#fileHide').val('');
	$('#fileShow').val('');
	$('#remark').val('');
	$('#publishModal .error-info').text('');
	$('#publishModal').modal('show');
}

function delXml(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			var filter = $('#bVersionTable_filter input').val() || '';
			AjaxPost('/Android/Home/Base/deletePublishModule', myData.checkedLists, function () {
				updateVersion(currentPage, filter);
				return;
			});
		}
	}else{
		alert('请选择模块！');
		return;
	}
}

function detailXml(){
	if(myData.checkedLists.length === 1){
		myData.versionId = $('.checkSelected td:eq(0)').data('id');
		AjaxGet('/Android/Home/Base/publishModuleLists?id=' + myData.versionId, function(data){
			createDetail(data);
			createRely(data);
			$('#detailModal').modal('show');
		});
	}else{
		alert('请选择一个模块！');
		return;
	}
}

function sayXml(){
	if(myData.checkedLists.length === 1){
		$('#sayModal .error-info').text('');
		myData.versionId = $('.checkSelected td:eq(0)').data('id');
		AjaxGet('/Android/Home/Base/moduleCommentLists?moduleId=' + myData.versionId, function(data){
			createSay(data);
			$('#newSay').val('');
			$('#sayModal').modal('show');
		});
	}else{
		alert('请选择一个模块！');
		return;
	}
}

function updateVersion(page, name){
	name = name || '';
	AjaxGet('/Android/Home/Base/publishModuleLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
		myData.checkedLists = [];
		myData.versionId = null;
		createVersion(data, page);
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

	var data = {"moduleId": myData.versionId, "content": newSay};
	AjaxPost('/Android/Home/Base/addModuleComment', data, function(){
		AjaxGet('/Android/Home/Base/moduleCommentLists?moduleId=' + myData.versionId, function(data){
			createSay(data);
			$('#newSay').val('');
			return;
		});
	}, $errorInfo);
});

$('#subPublish').on('click', function(){
	var fileObj = document.getElementById("fileHide").files[0];
    var fileVal = $("#fileShow").val();
    var filter = $('#uAppTable_filter input').val() || '';
    var remark = $('#remark').val() || '';
    var $errorInfo = $('#publishModal .error-info');
    var data = new FormData();

    if(fileVal != ' ' && fileVal.indexOf('http') == -1){
        data.append("name", fileObj);
    }
    if(fileVal == ' ' || !fileVal){
        $errorInfo.text('请选择要上传的xml文件');
        return;
    }

    var extra = remark;
    data.append("extra", extra);

    AjaxFile('/Android/Home/Base/publishModule', data, function(){
        $('#publishModal').modal('hide');
        updateVersion(currentPage, filter);
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
		AjaxGet('/Android/Home/Base/deleteModuleComment?id=' + id, function () {
			AjaxGet('/Android/Home/Base/moduleCommentLists?moduleId=' + myData.versionId, function(data){
				createSay(data);
				return;
			});
		});
	}
});

function createDetail(data){
    $('#versionRemark').val(data.extra.versionDesc);
    $('#lowestVersion').val(data.extra.minSdk);
    $('#commitId').val(data.extra.gitCommitId);
}

function createRely(data){
	var dataArr = [];
    var len = data.extra.relyModule.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra.relyModule[i];
        var detail = 'module='+ arr.module +'&versionName='+ arr.version_name +'&pkgName=' + arr.pkg_name;
        dataArr.push([arr.module, arr.version_name, arr.pkg_name, detail]);
    }
    myDataTable('#bDetailTable', {
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

function createVersion(data, page){
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.pkgName, arr.versionName, arr.gitBranch, arr.publisher, formatDate(arr.pubTime)]);
    }
    myDataTable('#bVersionTable', {
        "data": dataArr,
        "order": [[6, "desc"]],
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'3%', 'targets':0, "orderable": false},
            {'title':'模块','width':'15%', 'targets':1},
            {'title':'包名','width':'20%', 'targets':2},
            {'title':'版本名称','width':'8%', 'targets':3},
            // {'title':'git commit id','width':'12%', 'targets':4},
            {'title':'git 分支','width':'8%', 'targets':4},
            {'title':'发布者','width':'8%', 'targets':5},
            {'title':'发布时间','width':'12%', 'targets':6}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0]
			});
		}
	});

	var toolbar = [];

	var power = window.localStorage.getItem("ANDROID_PERMISSION_USEPOWER");
	if(power.indexOf('发布用户') !== -1){
		toolbar.push('<a class="btn my-btn publishBtn" href="javascript:"><i class="iconfont icon-release"></i>&nbsp;发布</a>');
	}
	if(power.indexOf('系统管理员') !== -1){
		toolbar.push(myConfig.delBtn);
	}
	toolbar.push('<a class="btn my-btn detailBtn" href="javascript:"><i class="iconfont icon-bjibenxinxi"></i>&nbsp;详情</a>');
	toolbar.push('<a class="btn my-btn sayBtn" href="javascript:"><i class="iconfont icon-pinglun"></i>&nbsp;评论</a>');
  	initToolBar('#bVersionTable', toolbar);
	updatePagination(len, page, data.count, 'bVersionTable');
	listenCheckBox('#bVersionTable');
    updateChecked('#bVersionTable');
}