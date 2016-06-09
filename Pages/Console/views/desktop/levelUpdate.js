//@ sourceURL=desktop.levelUpdate.js
function reloadUpdate(url){
	AjaxGet(url, function(data){
        createElem(data);
        trHover('#myTable');
    });

    listenSingleCheckBox('#myTable', function(e){
    	var tar = e.target;
        if(tar.className.indexOf('glyphicon-list') != -1){
            myData.snId = $(tar).parents('tr').find('td:eq(0)').data('sn');
            AjaxGet('/group/memberLists?group_id=' + myData.snId, function(data){
                createSN(data);
                trHover('#macTable');
                $('#macModal').modal('show');
            });
        }
    });

    listenchoose();
}

function createElem(data) {
    var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        var layoutVersion = arr.layoutVersion ? formatDate(arr.layoutVersion) : '--';
        dataArr.push([arr.id, arr.model, arr.type, layoutVersion, formatDate(arr.version), arr.groupId || '--', arr.layoutPath, arr.sourcePath, formatDate(arr.time)]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "stateSave": false,
        "order": [[8, "desc"]],
        "columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'8%', 'targets':0, "orderable": false},
            {'title':'桌面名称','width':'15%', 'targets':1},
            {'title':'类型','width':'8%', 'targets':2},
            {'title':'布局版本','width':'15%', 'targets':3},
            {'title':'资源包版本','width':'15%', 'targets':4},
            {'title':'设备列表','width':'10%', 'targets':5},
            {'title':'布局','width':'8%', 'targets':6},
            {'title':'资源包','width':'8%', 'targets':7},
            {'title':'发布时间','width':'15%', 'targets':8},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTypeColor(2, nRow, aData[2]);
            if(aData[2] == 'group'){
            	tableTdIcon(5, nRow, 'list');
            }else{
            	tableTdNull(5, nRow);
            }
            tableTdDownload(6, nRow, aData[6]);
            tableTdDownload(7, nRow, aData[7]);
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "sn":aData[5]
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
        }
    });
    initToolBar('#myTable', [myConfig.releaseBtn, myConfig.underBtn]);
    listenCheckBox();
    updateChecked();
}

function createSN(data){
    var dataArr = [];
    var len = data.members.length || 0;
    for( var i=0; i<len; i++ ) {
        var arr = data.members[i];
        dataArr.push([arr.sn, arr.desc]);
    }
    myDataTable('#macTable', {
        "data": dataArr,
        "pageLength": 10,
        "columnDefs": [
            {'title':'Mac', 'width':'40%', 'targets':0},
            {'title':'desc', 'width':'60%', 'targets':1}
        ]
    });
    $('#macTable_filter').prepend('<h4 style="position: absolute;margin: 6px 0;">'+ (data.group_name || '') +'</h4>');
}

function commonUpdate(addUrl, getUrl, delUrl, verUrl){
	listenToolbar('release', releaseTableInfo);
	listenToolbar('under', delTableInfo);

	function updateTable(){
	    AjaxGet(getUrl, function(data){
	        createElem(data);
	        myData.id = null;
	    });
	}

	function releaseTableInfo(){
	    $('#countNum').val("");
	    $('#group').val("");
	    $('#versionList').parent().hide();
	    $('#versionLayout').val("").parent().hide();
	    AjaxWhen([
            AjaxGet('/group/nameLists', selectGroup, true),
            AjaxGet('/desktop/desktopLists', selectDesktop, true)
        ], function(){
            $('#myModal').modal('show');
        });
	}

	function delTableInfo(){
	    if (myData.checkedLists.length > 0) {
	        if( confirm('确定下架？') ){
	        	$.ajax({
			        url: delUrl,
			        beforeSend: function() {
			            showLoading();
			        },
			        type: 'post',
			        data: JSON.stringify(myData.checkedLists),
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
			                alert('删除成功');
			                $('#releaseModal').modal('hide');
			            }
			            updateTable();
	            		myData.checkedLists = [];
			        },
			        error: function(XMLHttpRequest, textStatus, errorThrown) {
			            hideLoading();
			            ajaxError(XMLHttpRequest, textStatus, errorThrown);
			        }
			    });
	        }
	    }else{
	        alert('请选择版本！');
	    }
	}

	//创建内存组下拉框
	function selectGroup(data){
	    var arr = data.groups;
	    var con = '';
	    var $select = $('#group');
	    for( var i=0; i<arr.length; i++ ){
	        con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
	    }
	    $select.html(con);
	}

	//创建桌面下拉框
	function selectDesktop(data){
	    var arr = data.extra;
	    var con = '<option value="请选择桌面">请选择桌面</option>';
	    var $select = $('#desktopList');
	    var len = arr.length;
	    for( var i=0; i<len; i++ ){
	        con += '<option value="'+ arr[i].name +'">'+ arr[i].name +'</option>';
	    }
	    $select.html(con).trigger("chosen:updated.chosen").chosen({
	        allow_single_deselect: true,
	        width: "70%"
	    });
	}

	$('#desktopList').on('change', function(){
	    var name = $(this).val();
	    if(name === '请选择桌面'){
	        $('#versionList').parent().hide();
	        $('#versionLayout').parent().hide();
	        return false;
	    }
	    $('#versionList').parent().show();
	    $('#versionLayout').parent().show();
	    AjaxGet(verUrl + '?desktopName=' + name, selectVersion);
	});

	function selectVersion(data){
	    var arr = descSort(data.extra);
	    var con = '';
	    var $select = $('#versionList');
	    var len = arr.length;
	    for( var i=0; i<len; i++ ){
	        con += '<option value="'+ arr[i].id +'">'+ formatDate(arr[i].version) +'</option>';
	        $('#versionList').data('_'+arr[i].id, formatDate(arr[i].layoutVersion));
	    }
	    $select.html(con).trigger("chosen:updated.chosen").chosen({
	        allow_single_deselect: true,
	        width: "70%"
	    }).trigger('change');
	}

	$('#versionList').on('change', function(){
		var $this = $(this);
		var id = $this.val();
		$('#versionLayout').val($this.data('_'+id));
	});


	$('#chooseType > input').on('click', function(){     //内测、灰度、公开
	    $(this).prop('checked');
	    var val = $(this).val();
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

	$('#subVersion').click(function() {
	    var type = $('#chooseType').find('input:checked').val();
	    var desktopId = $('#desktopList').val();
	    var versionId = $('#versionList').val();
	    if(desktopId === '请选择桌面' || !desktopId){
	        alert('请选择桌面');
	        return ;
	    }
	    if(versionId === '请选择版本' || !versionId){
	        alert('请选择版本');
	        return ;
	    }

	    var data = {"id": versionId, "type": type};
	    if(type == 'group') {
	        var group = $('#group').val();
	        data.groupId = group;
	    } else if (type == 'AB') {
	        var countNum = $('#countNum').val();
	        if(countNum == ' ' || !countNum){
	            alert('请输入灰度数量');
	            return;
	        }else if(/\D/.test(countNum)){
	            alert('灰度数量只能为数字');
	            return;
	        }
	        data.AB = countNum;
	    }

	    AjaxPost(addUrl, data, function(){
	        $('#myModal').modal('hide');
	        updateTable();
	    });
	});
}