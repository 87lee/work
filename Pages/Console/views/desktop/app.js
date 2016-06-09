//@ sourceURL=desktop.app.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
// var spreadPage = 1; //当前的页面
Highcharts.setOptions({
    lang: {
        loading: '加载中...',
        months: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月','九月',  '十月','十一月', '十二月'],
        shortMonths: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月','九月',  '十月','十一月', '十二月'],
        weekdays: ['星期日',  '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
        decimalPoint: '.',
        numericSymbols: ['k', 'M', 'G', 'T', 'P', 'E'], // SI prefixes used in axis labels
        resetZoom: '重 置',
        resetZoomTitle: '重置为 1:1',
        thousandsSep: ' '
    }
});

$(function() {
	myData.checkedLists = [];   //存储check选中项
    myData.jbkType = 'unjbk';

	AjaxGet('/desktop/attentionAppLists?page=1&pageSize=' + pageSize, function(data) {
        createAppChart(data, 1);
        trHover('#appChartTable');
    });

	listenSingleCheckBox('#appChartTable');

	listenMyPage('appChartTable');
	// listenSpreadPage();

	$('#main-content').css('visibility', 'visible');
});

listenToolbar('edit', editTableInfo, '#appChartTable');
listenToolbar('add', addTableInfo, '#appChartTable');
listenToolbar('del', delTableInfo, '#appChartTable');
listenToolbar('bar', barChart, '#appChartTable');
listenToolbar('spread', spreadTable, '#appChartTable');

listenToolbar('back', backTable, '#spreadTable');

function addTableInfo() {
	AjaxGet('/App/app3rdAndAppUpdateLists', function(data){
		selectApp(data);
		$('#appModal h4').html('添加');
		$('#appModal').modal('show');
	});
}

function editTableInfo() {
	if (myData.checkedLists.length === 1) {
		var obj = $('.checkSelected td:eq(0)');
        myData.appId = obj.data('id');
        myData.appName = obj.data('name');
        AjaxGet('/App/app3rdAndAppUpdateLists', function(data){
			selectApp(data, myData.appName);
			$('#appModal h4').html('修改');
			$('#appModal').modal('show');
		});
	} else {
		alert('请选择一个应用');
		return false;
	}
}

function delTableInfo() {
	if (myData.checkedLists.length > 0) {
    	if (confirm('确定删除？')) {
			var filter = $('#appChartTable_filter input').val() || '';
			AjaxPost('/desktop/deleteAttentionApp', myData.checkedLists, function(data) {
				updateTable(currentPage, filter);
				myData.checkedLists = [];
				if(data.reason){
					alert(data.reason);
				}
			});
		}
	} else {
		alert('请选择应用');
		return false;
	}
}

function updateTable(page, name) {
	name = name || '';
	AjaxGet('/desktop/attentionAppLists?name=' + name + '&page=' + page + '&pageSize=' + pageSize, function(data) {
		myData.checkedLists = [];
		createAppChart(data, page);
		myData.appId = null;
	});
}

function barChart(){
	AjaxGet('/desktop/desktopAttentionAppLists', function(data){
		createChart(data.extra);
        setTimeout(function(){
            $('#appBarModal').modal('show');
        }, 500);
	});
}

function spreadTable(){
	// spreadPage = 1;
	AjaxGet('/desktop/isDesktopAttentionAppLists?name=' + myData.jbkType, function(data){
        if(!data.appLists.length){
            alert('请先添加应用！');
            return;
        }
        $('#tableBox1').hide();
		$('#tableBox2').show();
        $('#tableBox2').html('<table id="spreadTable" class="table my-table table-bordered table-hover dataTable no-footer"></table>');
        createSpread(data, 1);
		trHover('#spreadTable');
        $('.breadcrumb').append('<li class="active">桌面分布</li>');
    });
}

function updateSpread() {
	AjaxGet('/desktop/isDesktopAttentionAppLists?name=' + myData.jbkType, function(data) {
		createSpread(data);
	});
}

function backTable(){
	$('#tableBox2').hide();
    $('#tableBox1').show();
    $('.breadcrumb').find('li:last').remove();
}

function createChart(data){
	var categories = [];
	var seriesData = [];
	data.sort(function(obj1, obj2){
        return Number(obj2.count) - Number(obj1.count);
    });
	for(var i = 0, len = data.length; i < len; i++){
		categories.push(data[i].appName);
		seriesData.push(data[i].count);
	}
    setTimeout(function(){
        var width = $('#appBarModal .modal-body').width();
        $('#appBarChart').css('width', width).highcharts({
            chart: {
                type: 'bar',

            },
            title: {
                text: null
            },
            subtitle: {
                text: null
            },
            xAxis: {
                categories: categories,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: null,
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            legend: {
                enabled : false
            },
            credits: {
                enabled: false
            },
            series: [{
                name: '占量总数',
                data: seriesData
            }]
        });
        $('#appBarModal').modal('show');
    }, 1000);
}

function selectApp(data, name){
	var arr = data.extra;
    var con = '<option value="请选择应用">请选择应用</option>';
    var $select = $('#appName');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].appName+'">'+arr[i].appName+'</option>';
        $select.data('_' + arr[i].appName, arr[i].pkgName);
    }
    if(name){
    	$select.html(con).val(name).trigger("chosen:updated.chosen").chosen({
	        allow_single_deselect: true,
	        width: "70%"
	    });
    }else{
    	$select.html(con).trigger("chosen:updated.chosen").chosen({
	        allow_single_deselect: true,
	        width: "70%"
	    });
    }
}

$('#subApp').on('click', function(){
    var appName = $('#appName').val();
    var title = $('#appModal h4').text();
    var filter = $('#appChartTable_filter input').val() || '';
    var data = {};

    if(appName == '请选择应用' || !appName){
        alert('请选择应用');
        return false;
    }
    var pkgName = $('#appName').data('_' + appName);
    data = {"appName": appName, "pkgName": pkgName};

    if(title === '添加'){
    	AjaxPost('/desktop/addAttentionApp', data, function(){
            $('#appModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        data.id = myData.appId;
        AjaxPost('/desktop/modifyAttentionApp', data, function(){
            $('#appModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

//创建APP列表
function createAppChart(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.appName, arr.pkgName]);
    }
    $('#appChartTable').dataTable({
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
            'width': '10%',
            'targets': 0,
            "orderable": false
        },{
            'title': '应用名称',
            'width': '45%',
            'targets': 1
        },{
            'title': '包名',
            'width': '45%',
            'targets': 2
        }],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1]
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
        },
		"language": {
			"zeroRecords": "没有检索到数据",
			"infoEmpty": "没有数据"
		}
    });
	updatePagination(len, page, data.count, 'appChartTable');
    initToolBar('#appChartTable', [
    	myConfig.addBtn,
    	myConfig.editBtn,
    	myConfig.delBtn,
    	'<a class="btn my-btn btn-primary barBtn" href="javascript:"><i class="fa fa-search icon-white"></i>&nbsp;数据统计</a>',
    	'<a class="btn my-btn btn-primary spreadBtn" href="javascript:"><i class="fa fa-search icon-white"></i>&nbsp;桌面分布</a>'
    ]);
    listenCheckBox('#appChartTable');
    updateChecked('#appChartTable');
}

//创建桌面分布列表
function createSpread(data, page) {
	var columnDefs = [{
            'title': '桌面名称',
            'width': '10%',
            'targets': 0
        }];
    var dataArr = [];
    var i = 0, j = 0;
    var len = data.appLists.length;
    var l = data.desktopLists.length;
    for(i = 0; i < len; i++){
    	columnDefs.push({
            'title': data.appLists[i].appName,
            'width': (0 | (90 / len)) + '%',
            'targets': i + 1
        });
    }

    for (i = 0; i < l; i++) {
        var arr = data.desktopLists[i];
        var temp = [arr.name];
        for(j = 0, len = arr.appLists.length; j < len; j++){
        	temp.push(arr.appLists[j] === 'false' ? '<i class="ace-icon glyphicon glyphicon-remove red"><i>' : '<i class="ace-icon glyphicon glyphicon-ok green"><i>');
        }
        dataArr.push(temp);
    }

    myDataTable('#spreadTable', {
        "paging": false,
		"data": dataArr,
        "destroy": true,
        "stateSave": false,
		"order": [
			[1, 'asc']
		],
        "columnDefs": columnDefs.slice(),
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id": aData[0]
            });
        }
    });
	// updatePagination(len, page, data.desktopCount, 'spreadTable');
    var jbkRadio = '';
    if(myData.jbkType === 'unjbk'){
        jbkRadio = '<div style="display: inline;position: relative;top: 3px;left: 15px;"><input name="jbkAble" type="radio" class="ace" value="unjbk" checked><span class="lbl">&nbsp;未越狱</span>&emsp;<input name="jbkAble" type="radio" class="ace" value="jbk"><span class="lbl">&nbsp;越狱</span></div>';
    }else if(myData.jbkType === 'jbk'){
        jbkRadio = '<div style="display: inline;position: relative;top: 3px;left: 15px;"><input name="jbkAble" type="radio" class="ace" value="unjbk"><span class="lbl">&nbsp;未越狱</span>&emsp;<input name="jbkAble" type="radio" class="ace" value="jbk" checked><span class="lbl">&nbsp;越狱</span></div>';
    }
    initToolBar('#spreadTable', [
    	myConfig.backBtn,
        jbkRadio
    ]);
}

$('#tableBox2').on('click', '.toolbar input', function(){
    myData.jbkType = $(this).val();
    updateSpread();
});

$('#tableBox2').on('click', '.toolbar span', function(){
    $(this).prev('input').trigger('click');
});

// function listenSpreadPage(table) {
//     table = table || 'spreadTable';
//     $('.my-content').on('click', '#'+ table +'_paginate ul li a', function() {
//         var val = $(this).text();
//         var active = $(this).parent().hasClass('active');
//         var page = Number(val);
//         var filter = $('#'+ table +'_filter input').val() || '';
//         if(active){
//             return false;
//         }

//         if (val === '上一页' && !$(this).parent().hasClass('disabled')) {
//             spreadPage--;
//             updateSpread(spreadPage, filter, table);
//         } else if (val === '下一页' && !$(this).parent().hasClass('disabled')) {
//             spreadPage++;
//             updateSpread(spreadPage, filter, table);
//         } else if (!isNaN(page)) {
//             spreadPage = page;
//             updateSpread(spreadPage, filter, table);
//         }
//         return false;
//     });
//     $('.my-content').on('keyup', '#'+ table +'_filter input', function() {
//         var val = $(this).val();
//         spreadPage = 1;
//         updateSpread(spreadPage, val, table);
//         return false;
//     });
// }