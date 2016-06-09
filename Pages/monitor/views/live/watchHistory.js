//@ sourceURL=live.watchHistory.js
var myData = {};
Highcharts.setOptions({
    global: {
        timezoneOffset: -8 * 60  // +8 时区修正方法
    },
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

$(function () {
    myData.endTime = myData.startTime = moment().valueOf();

    // AjaxGet('/Monitoring/home/Monitoring/getInterface', function(data){
    //     selectPorject(data);
    //     $('#page-content').show();
    // });
    AjaxGet('/Monitoring/Home/Domain/domainLists', function(data){
        $('#page-content').show();
    });
    $('#reportrange2 span').html(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
    $('#reportrange2').daterangepicker({}, function(start, end, label) {
        $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        myData.startTime = start.valueOf();
        myData.endTime = end.valueOf();
    });
});

$('.my-choose').on('click', 'li a', function(){
    var $obj = $(this).parent();
    var idx = $obj.index();
    $('.my-choose li').removeClass('active');
    $obj.addClass('active');
    selectParitcle(idx);
    return false;
});

$('#chartBox').on('click', 'a', function(){
    var $this = $(this);
    $('#chartBox a').removeClass('active');
    $this.addClass('active');
    $('#getChartData').trigger('click');
    return false;
});

$('#getChartData').on('click', function(){
    $('#error').hide();
    var idx = $('.my-choose li.active').index();
    var startTime = 0;
    var endTime = 0;

    var now = new Date();
    var nowTime = now.getTime();
    var nowDay = Date.parse(now.toLocaleDateString());

    switch(idx){
        case 0: 
            startTime = nowTime - 30 * 60000;
            endTime = nowTime;
            break;   //30分钟
        case 1: 
            startTime = nowTime - 3600000;
            endTime = nowTime;
            break;   //1小时
        case 2: 
            startTime = nowTime - 6 * 3600000;
            endTime = nowTime;
            break;      //6小时
        case 3: 
            startTime = nowDay;
            endTime = nowTime;
            break;      //今天
        case 4: 
            startTime = nowDay - 24 * 3600000;
            endTime = nowDay;
            break;     //昨天
        case 5: 
            startTime = nowDay - 6 * 24 * 3600000;
            endTime = nowTime;
            break;     //近7天
        case 6: 
            startTime = nowDay - 14 * 24 * 3600000;
            endTime = nowTime;
            break;     //近15天
        case 7: 
            startTime = nowDay - 29 * 24 * 3600000;
            endTime = nowTime;
            break;     //近30天
        case 8: 
            startTime = myData.startTime;
            endTime = myData.endTime;
            break;     //自定义
    }

    var interval = $('#timeSelect').val();
    var channelSelect = $('#channelSelect').val();

    if(interval === '请选择粒度' || !interval){
        alert('请选择粒度');
        return;
    }

    time = {
        "startTime": startTime,
        "interval": Number(interval),
        "zoom": endTime - startTime
    };
    if(endTime <= startTime){
        alert('请选择正确的时间！');
        return;
    }

    AjaxGet('/Monitoring/home/Monitoring/getLivewatchHistory?startTime='+ (0 | (startTime / 60000)) * 60 +'&endTime='+ (0 | (endTime / 60000)) * 60 +'&interval='+ interval + '&channel=' + channelSelect, function(cbData){
        if(cbData.extra.data){
            createChart(cbData, time);
            createTable(cbData);
        }else{
            $('#monitorChart').html('');
            $('#tableBox').hide();
            $('#error').show();
            $('#error').html('<div align="center" style="font-size:20px;padding-top: 200px;color: #777777;">没有数据！</div>');
//            alert('没有数据！');
        }
        return;
    });
});

function selectParitcle(idx){
    var con = '';
    var $select = $('#timeSelect');
    switch(idx){
        case 0:    //30分钟
        case 1:    //1小时
        case 2:
            con += '<option value="1">1分钟</option><option value="5">5分钟</option><option value="10">10分钟</option>';
            break;      //6小时
        case 3:         //今天
        case 4:
            con += '<option value="5">5分钟</option><option value="10">10分钟</option><option value="30">30分钟</option>';
            break;     //昨天
        case 5:
            con += '<option value="30">30分钟</option><option value="60">1小时</option><option value="180">3小时</option>';
            break;     //近7天
        case 6:
            con += '<option value="60">1小时</option><option value="180">3小时</option><option value="300">5小时</option>';
            break;     //近15天
        case 7:
            con += '<option value="180">3小时</option><option value="300">5小时</option><option value="720">12小时</option>';
            break;     //近30天
        case 8:
            con += '<option value="30">30分钟</option><option value="60">1小时</option><option value="180">3小时</option><option value="300">5小时</option><option value="720">12小时</option>';
            break;     //自定义
    }
    $select.html(con);
    return false;
}

function createChart(adata, time){
    

    var tmp = adata.extra;
    adata.extra = [];
    adata.extra.push(tmp);
    
    var len = adata.extra.length;
    var dataArr = {};
    var startTimes = {};
    console.log(len);
    for(var i = 0; i < len; i++){
        var temp = adata.extra[i].data;
        //startTimes[data.extra[i].type] = data.extra[i].startTime;
        startTimes[$('#channelSelect').val()] = adata.extra[i].startTime;
        dataArr[$('#channelSelect').val()] = [];
        for(var j = 0, l = temp.length; j < l; j++){
            dataArr[$('#channelSelect').val()].push(Number(temp[j]));
        }
    }
    console.log(dataArr);
    var seriesArr = [];

    for(var p in dataArr){
        seriesArr.push({
            name: p,
            pointInterval: 60 * 1000 * time.interval,
            pointStart: startTimes[p] * 1000,
            data: dataArr[p]
        });
    }
    console.log(seriesArr);
    $('#monitorChart').highcharts({
        chart: {
            zoomType: 'x',
            spacingRight: 20
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        xAxis: {
            type: 'datetime',
            maxZoom: time.zoom, // 显示区间
            title: {
                text: null
            }
        },
        yAxis: {
            title: {
                text: null
            }
        },
        credits: {
            enabled:false // 禁用版权信息
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                lineWidth: 1,
                marker: {
                    enabled: false
                },
                shadow: false,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: seriesArr
    });
}

function createTable(data){
    var dataArr = [];
    var len = data.extra.length;
    var count = 0;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        //dataArr.push([arr.type, arr.countData, null]);
        dataArr.push([arr.countData]);
        count += Number(arr.countData);
    }
    myDataTable('#chartTable', {
        "paging": false,
        "searching": false,
        "info": false,
        "data": dataArr,
        "order": [[0, "desc"]],
        "columnDefs": [
//            {'title':'返回码种类','width':'30%', 'targets':0},
            {'title':'数量','width':'30%', 'targets':0}
        ],
        "createdRow": function(nRow, aData, iDataIndex) {
            //var percent = Number(aData[1]) / count * 100;
            //$('td:eq(2)', nRow).html(percent.toFixed(2) + '%');
        }
    });
    $('#tableBox').show();
}