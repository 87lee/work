//@ sourceURL=live.authedLog.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面
$(function() {
	listenMyPage('PowerTable', currentPage);

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

	// $.post('/Monitoring/home/Report/getUsersAuth?page=1&pageSize=15', function(data) {
	// 	console.log(data);
	// });
	
});

function updateTable(page){
	AjaxPost('/Live/getLiveAuthDetail?page='+ page +'&pageSize=' + pageSize, myData.pData, function(data){
		createPowerTable(data, page);
		return;
	});
}

$('#getBackData').on('click', function(){
	var model = $('#model').val();
	var mac = $('#mac').val();
	var vendorID = $('#vendorID').val();
	var wifi = $('#wifi').val();
	var startTime = $('#startTime').val();
	var endTime = $('#endTime').val();
	var channel = $('#channel').val();

	if(Date.parse(endTime) <= Date.parse(startTime)){
		alert('请选择正确的时间！');
		return;
	}

	myData.pData = {
		"model": model,
		"mac": mac,
		"vendorID": vendorID,
		"wifi": wifi,
		"channel": channel,
		"startTime": startTime,
		"endTime": endTime
	};

	updateTable(1);
});

function createPowerTable(data, page) {
	var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.model, arr.vendorID, arr.channel, arr.mac, arr.wifi, arr.uuid, arr.yunosDevice, arr.yunosChip, arr.yunosModel, arr.yunosVendor, arr.time]);
    }
    myDataTable('#PowerTable', {
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
        "columnDefs": [
            {'title':'model','width':'9%', 'targets':0},
            {'title':'vendorID','width':'9%', 'targets':1},
            {'title':'channel','width':'9%', 'targets':2},
            {'title':'mac','width':'9%', 'targets':3},
            {'title':'wifi','width':'9%', 'targets':4},
            {'title':'uuid','width':'9%', 'targets':5},
            {'title':'yunosDevice','width':'9%', 'targets':6},
            {'title':'yunosChip','width':'9%', 'targets':7},
            {'title':'yunosModel','width':'9%', 'targets':8},
            {'title':'yunosVendor','width':'10%', 'targets':9},
            {'title':'授权时间','width':'9%', 'targets':10}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "uuid":aData[5],
                "mac":aData[3]
            });
        },
        "language": {
			"zeroRecords": "没有检索到数据",
			"infoEmpty": "没有数据"
		}
    });
    updatePagination(len, page, data.count, 'PowerTable');
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