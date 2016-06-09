//@ sourceURL=live.liveGroup.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
    myData.channelList = [];        //缓存频道id
	AjaxGet('/Live/liveAdGroupLists?page=1&pageSize='+pageSize, function(data) {
        createGroup(data, 1);
        trHover('#lGroupTable');
    });

    trclick('#lGroupTable', function(obj, e) {
        myData.groupId = obj.data('id');
        myData.groupName = obj.data('name');
        myData.groupDesc = obj.data('desc');

        var tar = e.target;
        if (tar.className.indexOf('glyphicon-list') != -1) {//版本列表
            updateAdScreen(true);
            selectTime();
            return false;
        }
    });

    // trclick('#adTable', function(obj, e) {
    //     myData.appId = obj.data('id');
    //     myData.appName = obj.data('appName');
    //     myData.version = obj.data('version');
    //     myData.url = obj.data('url');
    //     myData.weight = obj.data('weight');
    //     myData.action = obj.data('action');
    // });

    listenMyPage('lGroupTable', currentPage);
    listenchoose();
    listenPosXY();
});

//返回广告组
$('#secondTable .my-close').on('click', function() {
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    $('#adWarp').css({
        'position': 'absolute',
        'cursor': 'move',
        'width': 0 + 'px',
        'height': 0 + 'px',
        'background': '#86c64e'
    });
});

//更新广告
function updateAdScreen(type) {
    myData.adId = null;
    myData.adName = null;
    AjaxGet('/Live/liveAdLists?groupId='+ myData.groupId, function(data){
        $('#fristTable').hide();
        selectAd(data);
        if(type){
            $('#secondTable').show();
            $('.breadcrumb').append('<li class="active">'+myData.groupName+'</li>');
        }
    });
}

//生成广告下拉框选项
function selectAd(data) {
    var arr = data.extra;
    var con = '<option value="空广告">空广告</option>';
    var $select = $('#adSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '" >' + arr[i].name + '</option>';
    }
    if (!myData.newAdName) {
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        $('#screenSelect').show();
    } else {
        var option = $select.html(con).find('option').filter('[data-name="' + myData.newAdName + '"]');
        option.prop("selected", true);
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        myData.newAdName = null;
        $('#screenSelect').show();
    }
    // createAdList(arr);
}

$('#adSelect').on('change', function() { //监听选择导航事件
    myData.adId = $(this).val();
    myData.adName = $(this).find("option:selected").text();

    if (myData.adId === '空广告') {
        $("#editAd").hide();
        $("#delAd").hide();
        $("#addAdInfo").hide();
        $("#editChannel").hide();
    } else {
        $("#editAd").show();
        $("#delAd").show();
        $("#addAdInfo").show();
        $("#editChannel").show();
    }
    myData.extra = [];
    myData.channelList = [];
    AjaxGet('/Live/liveAdLists?id=' + myData.adId, updateScreen);
});

function selectTime($select){
    var hours = '',
        minSec = '',
        i = 0,
        num = '';
    for( i = 0; i < 24; i++ ) {
        num = addZero(i);
        hours += '<option value="'+num+'">'+num+'</option>';
    }
    for( i = 0; i < 60; i++ ) {
        num = addZero(i);
        minSec += '<option value="'+num+'">'+num+'</option>';
    }

    $('#startTime').find('select:eq(0)').html( hours );
    $('#endTime').find('select:eq(0)').html( hours );
    $('#startTime').find('select').not(':first').html( minSec );
    $('#endTime').find('select').not(':first').html( minSec );
}

/*数字补0*/
function addZero( num ){
    num = num < 10 ? '0' + num : '' + num;
    return num;
}

function updateScreen(data) {   //广告变化时更新屏
    var ad = myData.extra = data.extra;
    var $ad = $('#adWarp');
    if(data.count !== '0'){
        if(ad.screenSize === '1280'){
            $('#screenSelect input[name="screenSize"]:eq(0)').prop('checked', true);
        }else if(ad.screenSize === '1920'){
            $('#screenSelect input[name="screenSize"]:eq(1)').prop('checked', true);
        }
        var size = getAdSize();
        $ad.css({
            'position': 'absolute',
            'cursor': 'move',
            'width': size.width + 'px',
            'height': size.height + 'px',
            'background': '#86c64e',
            'top': ad.y + 'px',
            'left': ad.x + 'px'
        });
        refreshAdPos({"left": ad.x, "top": ad.y});
    }else{
        $ad.css({
            'position': 'absolute',
            'cursor': 'move',
            'width': 0 + 'px',
            'height': 0 + 'px',
            'background': '#86c64e'
        });
        refreshAdPos({"left": 0, "top": 0});
    }
}

//添加广告
$('#addAd').on('click', function(){
    $('#adName').val('');
    $('#adWidth').val('');
    $('#adHeight').val('');
    $('#adPosX').val('');
    $('#adPoxY').val('');
    $('#adModal').modal('show');
});

//删除广告
$('#delAd').on('click', function(){
    if (confirm('确定删除？')) {
        AjaxPost('/Live/deleteLiveAd', [myData.adId], function() {
            alert('删除成功');
            updateAdScreen();
        });
    }
    return false;
});

//展示信息
$('#addAdInfo').on('click', function(){
    var startTime = myData.extra.startTime.split(':');
    var endTime = myData.extra.endTime.split(':');
    $('#startTime select:eq(0)').val(startTime[0]);
    $('#startTime select:eq(1)').val(startTime[1]);
    $('#startTime select:eq(2)').val(startTime[2]);
    $('#endTime select:eq(0)').val(endTime[0]);
    $('#endTime select:eq(1)').val(endTime[1]);
    $('#endTime select:eq(2)').val(endTime[2]);
    $('#adUrl').val(myData.extra.url || '');
    $('#interval').val(myData.extra.interval || '');
    $('#duration').val(myData.extra.duration || '');
    $('#maxShowTimes').val(myData.extra.maxShowTimes || '');
    $('#adInfoModal').modal('show');
});

//频道列表
$('#editChannel').on('click', function(){
    AjaxGet('/Live/getChannelType', function(data){
        $('#channeled').html('');
        createChannel(myData.extra.channelList);
        selectChannelList(data);
        $('#channelModal').modal('show');
    });
});

//生成频道列表下拉框选项
function selectChannelList(data) {
    var arr = data.extra;
    var con = '<option value="请选择频道列表">请选择频道列表</option>';
    var $select = $('#channelListSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].nameId + '">' + arr[i].name + '</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    }).trigger('change');
}

//频道列表变化事件
$('#channelListSelect').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    AjaxGet('/Live/getChannelList?nameId=' + val, selectChannel);
});

//生成频道下拉框选项
function selectChannel(data) {
    var arr = data.extra;
    var con = '<option value="请选择频道">请选择频道</option>';
    var $select = $('#channelSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}

/*创建已选频道html*/
function createChannel(data){
    var i, len = 0;
    var stred = [];
    for(i = 0, len = data.length; i < len; i++){
        var channeled = data[i];
        stred.push($('<label class="channel-set">'+ channeled.name +'<div class="channel-set-button">×</div></label>').data('channel', channeled));
    }
    $('#channeled').append(stred);
}

//添加频道
$('#addChannel').on('click', function(){
    var id =  $('#channelSelect').val();
    var name =  $('#channelSelect option:selected').text();
    if(id === '请选择频道'){
        alert('请选择频道');
        return;
    }
    var data = {"id": id, "name": name};
    if($.inArray(id, myData.channelList) === -1){
        myData.channelList.push(id);
        myData.extra.channelList.push(data);
    }else{
        alert('该频道已存在');
        return;
    }
    createChannel([data]);
});

/*删除频道*/
$('#channelModal').on('click', '.channel-set-button', function(){
    var $this = $(this);
    var $obj = $this.parent();
    var idx = $obj.index();
    myData.channelList.splice(idx, 1);
    myData.extra.channelList.splice(idx, 1);
    $this.parent().remove();
});

//修改广告
$('#editAd').on('click', function() {
    if (myData.AdId === '空广告') {
        alert('当前为空广告');
        return false;
    }
    if(!myData.extra.url){
        alert('请设置展示信息');
        return;
    }
    if(!myData.extra.channelList.length){
        alert('请添加频道列表');
        return;
    }

    myData.extra.x = parseInt($('#adWarp').css('left'));
    myData.extra.y = parseInt($('#adWarp').css('top'));
    AjaxPost('/Live/modifyLiveAd', myData.extra, function() {
        alert('修改成功');
        return;
    });
});

//缓存展示信息
$('#subAdInfo').on('click', function(){
    var url = $('#adUrl').val();
    var interval = $('#interval').val();
    var duration = $('#duration').val();
    var maxShowTimes = $('#maxShowTimes').val();
    var $startSec = $('#startTime').find('select');
    var $endSec = $('#endTime').find('select');
    var startTime = $startSec.eq(0).val()+':'+$startSec.eq(1).val()+':'+$startSec.eq(2).val();
    var endTime = $endSec.eq(0).val()+':'+$endSec.eq(1).val()+':'+$endSec.eq(2).val();

    if(url == ' ' || !url){
        alert('请输入url');
        return;
    }
    if(url.indexOf('http://') === -1 && url.indexOf('https://') === -1){
        alert('请输入正确的url');
        return;
    }
    if(interval == ' ' || !interval){
        alert('请输入展示间隔');
        return;
    }
    if(!/^(-)?\d+$/.test(interval)){
        alert('展示间隔只能为数字');
        return;
    }
    if(duration == ' ' || !duration){
        alert('请输入展示持续时间');
        return;
    }
    if(!/^(-)?\d+$/.test(duration)){
        alert('展示持续时间只能为数字');
        return;
    }
    if(maxShowTimes == ' ' || !maxShowTimes){
        alert('请输入最大展示次数');
        return;
    }
    if(!/^(-)?\d+$/.test(maxShowTimes)){
        alert('最大展示次数只能为数字');
        return;
    }

    $.extend(myData.extra, {
        "url": url,
        "interval": interval,
        "duration": duration,
        "maxShowTimes": maxShowTimes,
        "startTime": startTime,
        "endTime": endTime
    });

    $('#adInfoModal').modal('hide');
});

//提交新广告
$('#subAd').on('click', function(){
    var name = $('#adName').val();
    var width = $('#adWidth').val();
    var height = $('#adHeight').val();
    var posX = $('#adPosX').val();
    var posY = $('#adPoxY').val();
    var screenSize = $('#adScreenSize input:checked').val();
    var data = {};

    if(name == ' ' || !name){
        alert('请输入广告名称');
        return;
    }
    if(width == ' ' || !width){
        alert('请输入广告宽');
        return;
    }
    if(!/^\d+$/.test(width)){
        alert('广告宽只能为数字');
        return;
    }
    if(height == ' ' || !height){
        alert('请输入广告高');
        return;
    }
    if(!/^\d+$/.test(height)){
        alert('广告高只能为数字');
        return;
    }
    if(posX == ' ' || !posX){
        alert('请输入广告X坐标');
        return;
    }
    if(!/^(-)?\d+$/.test(posX)){
        alert('广告X坐标只能为数字');
        return;
    }
    if(posY == ' ' || !posY){
        alert('请输入广告Y坐标');
        return;
    }
    if(!/^(-)?\d+$/.test(posY)){
        alert('广告Y坐标只能为数字');
        return;
    }

    data = {
        "groupId": myData.groupId,
        "name": name,
        "width": width,
        "height": height,
        "screenSize": screenSize,
        "x": posX,
        "y": posY
    };

    AjaxPost('/Live/addLiveAd', data, function() {
        myData.newAdName = name;
        updateAdScreen();
        $('#adModal').modal('hide');
    });

});

//导航拖拽事件
listenAdDrag("#adWarp", refreshAdPos);

//屏幕尺寸切换事件
$('#screenSelect input[name="screenSize"]').on('click', function(){
    var $this = $(this);
    myData.extra.screenSize = $this.val();
    $('#adWarp').css(getAdSize());
});

function getAdSize(){
    var screenX = 1920;
    var screenY = 1080;
    if(myData.extra.screenSize === '1920'){
        screenX = 1280;
        screenY = 720;
    }
    var width = 0 | (parseInt(myData.extra.width) * screenX / 1920);
    var height = 0 | (parseInt(myData.extra.height) * screenY / 1080);
    return {"width": width, "height": height};
}

//刷新导航的位置
function refreshAdPos(pos) {
    $('#slotLeft').val(parseInt(pos.left) || 0);
    $('#slotTop').val(parseInt(pos.top) || 0);
}

listenToolbar('add', addTableInfo, '#lGroupTable');
listenToolbar('del', delTableInfo, '#lGroupTable');
listenToolbar('edit', editTableInfo, '#lGroupTable');
// listenToolbar('back', backTable, '#adTable');

function addTableInfo(){
    $('#groupName').val('');
    $('#groupDesc').val('');
    $('#lGroupModal h4').text('添加');
    $('#lGroupModal').modal('show');
}

function editTableInfo(){
    if (myData.groupId) {
        $('#groupName').val(myData.groupName);
        $('#groupDesc').val(myData.groupDesc);
        $('#lGroupModal h4').text('修改');
        $('#lGroupModal').modal('show');
    } else {
        alert('请选择组！');
    }
}

function delTableInfo(){
	if (myData.groupId) {
        if (confirm('确定删除？')) {
            var filter = $('#lGroupTable_filter input').val() || '';
            AjaxGet('/Live/deleteLiveAdGroup?id=' + myData.groupId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择组！');
    }
}

function updateTable(page, name){
    name = name || '';
	AjaxGet('/Live/liveAdGroupLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createGroup(data, page);
        myData.groupId = null;
    });
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.appId = null;
}

$('#subGroup').on('click', function(){
    var groupName = $('#groupName').val();
    var groupDesc = $('#groupDesc').val() || '';
    var title = $('#lGroupModal h4').text();
    var filter = $('#lGroupTable_filter input').val() || '';
    var data = {};

    if(groupName == ' ' || !groupName){
        alert('请输入组名称');
        return false;
    }

    data = {"name": groupName, "desc": groupDesc};

    if(title === '添加'){
    	AjaxPost('/Live/addLiveAdGroup', data, function(){
            $('#lGroupModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        data.id = myData.groupId;
        AjaxPost('/Live/modifyLiveAdGroup', data, function(){
            $('#lGroupModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

//创建广告组
function createGroup(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.desc, null]);
    }
    $('#lGroupTable').dataTable({
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
            [0, "desc"]
        ],
        "columnDefs": [
        {
            'title': 'ID',
            'width': '15%',
            'targets': 0
        },{
            'title': '组名称',
            'width': '30%',
            'targets': 1
        },{
            'title': '备注',
            'width': '30%',
            'targets': 2
        },{
            'title': '广告列表',
            'width': '25%',
            'targets': 3
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            tableTdIcon(3, nRow, 'list');
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
                "desc": aData[2]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'lGroupTable');
    initToolBar('#lGroupTable');
}

$('#screenSelect').on('click', 'span.lbl', function(){
    $(this).prev('input').trigger('click');
});

//显示比例
$('#desktopProportion').on('change', function(){
    var $this = $(this);
    var val = parseInt($this.val());
    var scale = val / 100;

    var offsetWidth = 1280 - 1280 * scale;
    var offsetHeight = 720 - 720 * scale;
    $('#pageWrap').css({
        "transform": "scale("+ scale +")",
        "top": 45 - offsetHeight / 2,
        "left" : 130 - offsetWidth / 2
    });

    var $rightDiv = $('#slotInfo').parent();
    $rightDiv.css('left', 1410 - offsetWidth);

    var $layout = $('#myLayout');
    $layout.width(1610 - offsetWidth);

    if(val === 60){
        $('#screenSelect').css('left', 0);
    }else{
        $('#screenSelect').css('left', '130px');
    }
});

function listenAdDrag(id, fn) {
    //控件拖拽前事件
    $(id)
    .on("mousedown", function(ev) {
        var oDiv = this;
        var e = ev||event;
        var $this = $(this);
        var scale = 100 / $('#desktopProportion').val();    //获取显示比例
        disX=e.clientX * scale -oDiv.offsetLeft;
        disY=e.clientY * scale -oDiv.offsetTop;
        fn({
            "left": parseInt($this.css('left')),
            "top": parseInt($this.css('top'))
        });
        $(document).off('mousemove').on('mousemove', function(ev){
            var e=ev||event;
            var l=parseInt(e.clientX * scale -disX);
            var t=parseInt(e.clientY * scale -disY);
            if(l<0)
            {
                l=0;
            }
            if(t<0)
            {
                t=0;
            }

            $this.css('left', l);
            $this.css('top', t);
            fn({
                "left": l,
                "top": t
            });
            var dom = $('#screenWarp li').get(0);
            dom.scrollLeft = l;
            dom.scrollTop = t;
        });
        $(document).off('mouseup').on('mouseup', function(ev){
            var e = ev || event;
            var cls = e.target.className;
            if (myData.clearSelect) {
                return false;
            }
            $(document).off('mousemove');
            if (cls.indexOf('scale-set') !== -1 || cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;
            $(document).off('mouseup');
        });
        return false;
    });
}

function listenPosXY(){
    $('#slotLeft').on('change', function() { //块Left改变
        var $ad = $('#adWarp');
        var left = parseInt(this.value.trim(), 10);
        $ad.css('left', left);
        return false;
    });

    $('#slotTop').on('change', function() { //块Top改变
        var $ad = $('#adWarp');
        var top = parseInt(this.value.trim(), 10);
        $ad.css('top', top);
        return false;
    });
}