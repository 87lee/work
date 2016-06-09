$(function() {
	AjaxGet('/Android/Home/App/createReport', function(data) {
		for (var i in data.retval.app) {
			var dom = '';
			dom = '<table id="' + data.retval.app[i].name.split('.').join('') + 'Table" class="table table-bordered table-hover dataTable no-footer"></table>';
			var obj = $(dom);
			
			$('#totalTable').append(obj);
			createAppList(data.retval.app[i], $('#' + data.retval.app[i].name.split('.').join('') + 'Table').attr('id'));
		}
		$('#totalTable').append('<table id="testTotalTable" class="table table-bordered table-hover dataTable no-footer" role="grid"></table>');
		createTotalList(data.retval.total);
	});
	AjaxGet('/Android/Home/App/getAppName', function(data) {
		selectAppName(data);
	});

	$("#startTime").datetimepicker({
		minView: "month",
		format: 'yyyy-mm-dd',
		language: 'zh-CN',
		autoclose: true
	}).val('');

	$("#endTime").datetimepicker({
		minView: "month",
		format: 'yyyy-mm-dd',
		language: 'zh-CN',
		autoclose: true
	}).val('');
});

function createAppList(data, keyTable){
	var dataArr = [];
    if (data.records) {
    	var len = data.records.length;
    	if (len > 10) {
    		len = 10;
    	}
    	for( var i=0; i < len; i++ ) {
        	var arr = data.records[i];
        	dataArr.push([formatDate(arr.modify_time), arr.status_after, arr.modifyer, arr.version_code]);
    	}
    }
    myDataTable('#' + keyTable, {
        "data": dataArr,
        "order": [[0, "asc"]],
		"columnDefs": [
            {'title':'时间','width':'25%', 'targets':0},
            {'title':'动作','width':'25%', 'targets':1},
            {'title':'用户','width':'25%', 'targets':2},
            {'title':'版本号','width':'25%', 'targets':3}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			if(aData[1] === 'false'){
				$('td:eq(1)', nRow).html('未通过').css('color', 'red');
			}else if(aData[1] === 'true'){
				$('td:eq(1)', nRow).html('通过').css('color', '#00cc00');
			}else if(aData[1] === 'test'){
				$('td:eq(1)', nRow).html('待测试').css('color', '#ffcc00');
			}else if(aData[1] === 'regress'){
				$('td:eq(1)', nRow).html('回归通过').css('color', '#00cc00');
			}else if(aData[1] === 'back'){
				$('td:eq(1)', nRow).html('打回').css('color', 'red');
			}
			$('td:eq(0)', nRow).data({
				"id": aData[0]
			});
		}
	});

	var toolbar = [];

	toolbar.push('<div class="appName">' + data.app + '</div>');
	toolbar.push('<div class="appDev">开发人员：' + data.developer + '</div>');
	
  	initToolBar('#' + keyTable, toolbar);
  	if (data.records.length > 10) {
  		$('#' + keyTable + ' tbody').append('<tr role="row" class="odd"><td colspan="4"><div class="checkMore">查看更多</div></td></tr>');
  		$('#' + keyTable + ' tbody' + ' .checkMore').on('click', function() {
			AjaxGet('/Android/Home/App/getAppStatusRecord?pkg_name='+data.name+'&s_time='+$('#startTime').val()+'&e_time='+$('#endTime').val(), function(cbData) {
				$('#startTime').parent().siblings().hide();
				$('#endTime').parent().show();
				$('#startTime').attr('disabled', 'true');
				$('#endTime').attr('disabled', 'true');
				$('#totalTable').hide();
				$('#appName').val(data.name);
				$('#appName').hide();
				$('#generalTable').show();
				cbData.developer = data.developer;
				cbData.app = data.app;
				createGTable(cbData, 1, 'generalTable');
			});
		});
  	}
  	if (dataArr.length > 0) {
  		$('#' + keyTable + ' tbody').append('<tr role="row" class="odd"><td>统计</td><td colspan="3" style="text-align: center;">发起测试：' + data.statistics.test + '次，未通过：' + data.statistics.not_pass + '次，回归：' + data.statistics.regress + '次，一次通过：' + data.statistics.one_pass + '次，打回：' + data.statistics.back + '次</td></tr>');
  	}
}

function createGTable(data, page, table) {
	var dataArr = [];
    var len = data.retval.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval[i];
        dataArr.push([formatDate(arr.modify_time), arr.status_after, arr.modifyer, arr.version_code]);
    }
    myDataTable('#' + table, {
        "data": dataArr,
        "order": [[0, "asc"]],
		"columnDefs": [
            {'title':'时间','width':'13%', 'targets':0},
            {'title':'动作','width':'13%', 'targets':1},
            {'title':'用户','width':'8%', 'targets':2},
            {'title':'版本号','width':'8%', 'targets':3}
        ],
		"createdRow": function(nRow, aData, aIdx) {
			if(aData[1] === 'false'){
				$('td:eq(1)', nRow).html('未通过').css('color', 'red');
			}else if(aData[1] === 'true'){
				$('td:eq(1)', nRow).html('通过').css('color', '#00cc00');
			}else if(aData[1] === 'test'){
				$('td:eq(1)', nRow).html('待测试').css('color', '#ffcc00');
			}else if(aData[1] === 'regress'){
				$('td:eq(1)', nRow).html('回归通过').css('color', '#00cc00');
			}else if(aData[1] === 'back'){
				$('td:eq(1)', nRow).html('打回').css('color', 'red');
			}
			$('td:eq(0)', nRow).data({
				"modify_time": aData[0]
			});
		}
	});

	var toolbar = [];
	toolbar.push('<div class="appName">' + data.app + '</div>');
	toolbar.push('<div class="appDev">开发人员：' + data.developer + '</div>');
  	initToolBar('#' + table, toolbar);
}

function createTotalList(data) {
	var dataArr = [];
	if (data.length != 0) {
		for (var i in data) {
    		dataArr.push([data[i].name, data[i].pass, data[i].not_pass, data[i].regress, data[i].back]);
    	}
    }
    myDataTable('#testTotalTable', {
        "data": dataArr,
        "order": [[0, "desc"]],
		"columnDefs": [
            {'title':'用户','width':'20%', 'targets':0},
            {'title':'通过','width':'20%', 'targets':1},
            {'title':'不通过','width':'20%', 'targets':2},
            {'title':'回归通过','width':'20%', 'targets':3},
            {'title':'打回','width':'20%', 'targets':4}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).data({
				"name": aData[0]
			});
		}
	});

	var toolbar = [];

	toolbar.push('<div class="appName">测试汇总</div>');
	
  	initToolBar('#testTotalTable', toolbar);
}

function selectAppName(data){
	var arr = data.retval;
    var con = '<option value="">请选择应用</option>';
    var $select = $('#appName');
    for( var i=0; i < arr.length; i++ ){
        con += '<option value="'+arr[i].name+'">'+arr[i].app+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "200px"
    });
}

$('#getMonthReport').on('click', function() {
	var startTime = $('#startTime').val();
	var endTime = $('#endTime').val();
	var appName = $('#appName').val();
	AjaxGet('/Android/Home/App/createReport?s_time=' + startTime + '&e_time=' + endTime + '&app_name=' + appName, function(data) {
		$('#totalTable').children().remove();
		for (var i in data.retval.app) {
			var dom = '';
			dom = '<table id="' + data.retval.app[i].name.split('.').join('') + 'Table" class="table table-bordered table-hover dataTable no-footer"></table>';
			var obj = $(dom);
			$('#totalTable').append(obj);
			createAppList(data.retval.app[i], $('#' + data.retval.app[i].name.split('.').join('') + 'Table').attr('id'));
		}
		$('#totalTable').append('<table id="testTotalTable" class="table table-bordered table-hover dataTable no-footer" role="grid"></table>');
		createTotalList(data.retval.total);
	});
});