//@ sourceURL=live.liveFeedback.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
$(function () {
	listenMyPage('searchTable', currentPage);

	$("#startTime").datetimepicker({
		format: 'yyyy-mm-dd hh:ii',
		language: 'zh-CN',
		autoclose: true
	}).val(getNowDate(true));
	$("#endTime").datetimepicker({
		format: 'yyyy-mm-dd hh:ii',
		language: 'zh-CN',
		autoclose: true
	}).val('');
});

function updateTable(page){
	AjaxPost('/Monitoring/home/Report/getUsersReport?page='+ page +'&pageSize=' + pageSize, myData.pData, function(data){
		createBack(data, page);
		return;
	});
}

$('#getBackData').on('click', function(){
	var model = $('#model').val();
	var sn = $('#sn').val();
	var mac = $('#mac').val();
	var startVersion = $('#startVersion').val();
	var endVersion = $('#endVersion').val();
	var bEvent = $('#bEvent').val();
	var channel = $('#channel').val();
	var startTime = $('#startTime').val();
	var endTime = $('#endTime').val();
	var p2pId = $('#p2pId').val();
	var g3Desc = $('#g3Desc').val();
	var g3Region = $('#g3Region').val();

	if(Date.parse(endTime) <= Date.parse(startTime)){
		alert('请选择正确的时间！');
		return;
	}

	myData.pData = {
		"model": model,
		"sn": sn,
		"mac": mac,
		"startVersion": startVersion,
		"endVersion": endVersion,
		"event": bEvent,
		"channel": channel,
		"startTime": startTime,
		"endTime": endTime,
		"p2pId": p2pId,
		"g3Desc": g3Desc,
		"g3Region": g3Region
	};

	updateTable(1);
});

function createBack(data, page){
	var dataArr = [];
	var len = data.extra.length;
	for (var i = 0; i < len; i++) {
		var arr = data.extra[i];
		dataArr.push([arr.model, arr.sn, arr.mac, arr.version, arr.event, arr.channel, arr.p2pId, arr.g3Desc, arr.g3Region, arr.json, arr.time]);
	}
	$('#searchTable').dataTable({
		"lengthChange": false,
		"autoWidth": false,
		"destroy": true,
		"paging": false,
		"searching": false,
		"pageLength": 1,
		"info": false,
		"scrollX": true,
		"dom": '<"toolbar">frtip',
		"data": dataArr,
		"order": [
			[10, 'desc']
		],
		"columnDefs": [
		{
			'title': 'model',
			'width': '10%',
			'targets': 0
		},
		{
			'title': 'sn',
			'width': '8%',
			'targets': 1
		},
		{
			'title': 'mac',
			'width': '8%',
			'targets': 2
		},
		{
			'title': 'version',
			'width': '6%',
			'targets': 3
		},{
			'title': 'event',
			'width': '8%',
			'targets': 4
		},{
			'title': 'channel',
			'width': '8%',
			'targets': 5
		},{
			'title': 'p2pId',
			'width': '7%',
			'targets': 6
		},{
			'title': 'g3Desc',
			'width': '12%',
			'targets': 7
		},{
			'title': 'g3Region',
			'width': '8%',
			'targets': 8
		},{
			'title': 'url',
			'width': '5%',
			'targets': 9
		},{
			'title': 'time',
			'width': '10%',
			'targets': 10
		}],
		"createdRow": function(nRow, aData, iDataIndex) {
			tableTdDownload(9, nRow, '/Pages/monitor/report/report.htm?id=' + aData[9]);
		},
		"language": {
			"zeroRecords": "没有检索到数据",
			"infoEmpty": "没有数据"
		}
	});
	updatePagination(len, page, data.count, 'searchTable');
}

function getNowDate(begin) {
    var now = new Date();
    var month = (now.getMonth() + 1);
    var day = now.getDate();
    var hour = now.getHours();
    var minute = now.getMinutes();

    month = month < 10 ? '0' + month : month;
    day = day < 10 ? '0' + day : day;
    if(begin){
    	hour = '00';
    	minute = '00';
    }else{
    	hour = hour < 10 ? '0' + hour : hour;
    	minute = minute < 10 ? '0' + minute : minute;
    }
    return now.getFullYear() + "-" +
        month + "-" +
        day + " " +
        hour + ":" +
        minute;
}