//@ sourceURL=desktop.desktop.js
var myData = {};
var desktopData = {};
var desktopAction = '';
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

/*检查对齐变量*/
var SOLT_OFFSET = 20;
var soltLeftData = [];
var soltTopData = [];

var soltRightData = [];
var soltBottomData = [];
$(function() {
    myData.checkedLists = [];   //存储check选中项

    myData.clearSelect = null;  //控制时候清空选择块的样式
    desktopData.nav = null;
    desktopData.logo = null;
    desktopData.timebar = null;
    desktopData.weather = null;
    desktopData.timeWeather = null;
    desktopData.sn = null;
    desktopData.attachment = null;
    desktopData.quickEntry = null;
    desktopData.quickList = null;
    desktopData.quickEntryThreeState = null;
    desktopData.quickEntryTwoState = null;
    desktopData.shortCutConfig = null;
    desktopData.image = null;
    desktopData.appConfig = {};
    desktopData.style = null;
    desktopData.animation = '';
    desktopData.enlargeVal = '';//jatai2016-03-10
    desktopData.quickEntrySlot = {
        "style": "SIMPLE",
        "animation": "SIMPLE",
        "globalItems": []
    };
    desktopData.screens = [];
    desktopData.screens.push({
        'blocks': []
    }); //为无导航单屏桌面预设
    myData.styleKey = 0;

    AjaxGet('/desktop/desktopGroupLists', function(data) {
        createDGroup(data);
        trHover('#dGroupTable');
    });

    trclick('#dGroupTable', function(obj, e) {
        myData.groupId = obj.data('id');
        myData.groupName = obj.data('name');
        myData.groupDesc = obj.data('desc');

        var tar = e.target;
        if( tar.className.indexOf('align-justify') != -1){
            enterDesktopLists();
        }
    });

    $('#dGroupTable').on('dblclick', 'tbody > tr', function(){
        enterDesktopLists();
    });

    listenSingleCheckBox('#myTable', function(e){
        var tar = e.target;
        var obj = $(tar).parents('tr').find('td:eq(0)');
        myData.desktopId = obj.data('id');
        myData.desktopName = obj.data('name');
    });

    trclick('#quickTable', function(obj, e) {
        myData.keyCode = obj.data('id');
        myData.keyVal = obj.data('key-val');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-list') != -1){
            createKeyVal(myData.keyVal);
            $('#keyValModal').modal('show');
        }
    });

    listenchoose();
    listenScreenRadio();
    listenSlotsInfo();
    listenBlocksMove();
    listenBlockDrag();
    listenMyPage();
    listenfile();
    listenFile('#logoModal');
    listenFile('#quickListModal');
    blocksFilter();
    listenInputPic('#quickListModal');
    selectTimeWeather();

    $('#quickListItem').sortable();
    $('#screenSelect').show();
});

listenToolbar('edit', editDGroup, '#dGroupTable');
listenToolbar('add', addDGroup, '#dGroupTable');
listenToolbar('del', delDGroup, '#dGroupTable');

listenToolbar('back', backTable);
listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('del', delTableInfo);
listenToolbar('move', moveTableInfo);
listenToolbar('release', releaseTableInfo);
listenToolbar('record', recordTableInfo);
listenToolbar('copy', copyDesktopSlots);

function enterDesktopLists(){
    $('#groupContent').hide();
    $('.breadcrumb').append('<li class="active">'+myData.groupName+'</li>');
    AjaxGet('/desktop/desktopLists?page=1&pageSize=' + pageSize + '&groupId=' + myData.groupId, function(data) {
        createElem(data, 1);
        trHover('#myTable');
        myData.checkedLists = [];
        $('#desktopContent').show();
    });
}

function addDGroup(){
    $('#dGroupName').val('');
    $('#dGroupDesc').val('');
    $('#dGroupModal h4').text('新增');
    $('#dGroupModal').modal('show');
}

function editDGroup(){
    if (myData.groupId) {
        $('#dGroupName').val(myData.groupName);
        $('#dGroupDesc').val(myData.groupDesc);
        $('#dGroupModal h4').text('修改');
        $('#dGroupModal').modal('show');
    }else{
        alert('请选择组！');
    }
}

function delDGroup(){
    if (myData.groupId) {
        if (confirm('确定删除？')) {
            AjaxGet('/desktop/deleteDesktopGroup?id=' + myData.groupId, function() {
                updateDGroup();
            });
        }
    }else{
        alert('请选择组！');
    }
}

function updateDGroup(){
    AjaxGet('/desktop/desktopGroupLists', function(data) {
        createDGroup(data);
        myData.groupId = null;
    });
}

function editTableInfo() {
    if (myData.checkedLists.length === 1) {
        var obj = $('.checkSelected td:eq(0)');
        myData.desktopId = obj.data('id');
        myData.desktopName = obj.data('name');
        $('#desktopContent').hide();
        desktopAction = 'edit';
        clearDesktopInfo();
        AjaxGet('/desktop/desktopLists?id=' + myData.desktopId, function(data) {
            setDesktopInfo(data);
        });
    } else {
        alert('请选择一个桌面！');
    }
}

function addTableInfo() {
    desktopAction = 'add';
    AjaxGet('/desktop/desktopLists', function(data) {
        selectDesktop(data);
        $('#newDesktopName').val('');
        $('#copyModal').modal('show');
    });
}

function delTableInfo() {
    if (myData.checkedLists.length > 0) {
        AjaxPost('/desktop/getDesktopNameForIdArr', myData.checkedLists, function(nameData){
            var con = "";
            for(var i = 0, len = nameData.extra.length; i < len; i++){
                con += nameData.extra[i] + '\n';
            }
            if (confirm('确定删除名称为：\n'+ con +'的桌面？')) {
                var obj = $('.checkSelected td:eq(0)');
                myData.desktopId = obj.data('id');
                myData.desktopName = obj.data('name');
                var filter = $('#myTable_filter input').val() || '';
                AjaxPost('/desktop/deleteDesktop', {"idLists": myData.checkedLists}, function() {
                    updateTable(currentPage, filter);
                    myData.checkedLists = [];
                });
            }
        });
    } else {
        alert('请选择桌面！');
    }
}

function moveTableInfo(){
    if (myData.checkedLists.length > 0) {
        AjaxGet('/desktop/desktopGroupLists', function(data){
            selectMove(data);
            $('#mGroupModal').modal('show');
        });
    } else {
        alert('请选择桌面！');
    }
}

function backTable(){
    $('#desktopContent').hide();
    $('#groupContent').show();
    $('.breadcrumb').find('li:last').remove();
    myData.desktopName = null;
    myData.desktopId = null;
    myData.checkedLists = [];
}

function releaseTableInfo(str) {
    if (myData.desktopName) {
        AjaxGet('/group/nameLists', function(data){
            selectGroup(data);
            $('#chooseType > input:eq(0)').trigger('click');
            $('#releaseModal').modal('show');
        });
    } else {
        alert('请选择桌面');
        return false;
    }
    if(str){
        myData.alone = true;
    }else{
        myData.alone = false;
    }
}

function recordTableInfo () {
    if (myData.checkedLists.length === 1) {
        var obj = $('.checkSelected td:eq(0)');
        myData.desktopId = obj.data('id');
        myData.desktopName = obj.data('name');
        AjaxGet('/desktop/desktopVersionLists?desktopName=' + myData.desktopName, function(data) {
            createRecord(data);
            trHover('#recordTable');
            $('#recordModal').modal('show');
        });
    }else{
        alert('请选择一个桌面！');
    }
}

$('#releaseDesktop').on('click', function(){
    releaseTableInfo(true);
});

//发布
$('#subRelease').on('click', function(){
    var type = $('#chooseType input:checked').val();
    var checkeds = $('#myTable tbody tr td input:checked');
    var len = checkeds.length;
    var url = '';
    var data = {};
    if(type === 'group'){
        var groupId = $('#group').val();
        data.groupId = groupId;
    }
    data.type = type;
    if(myData.checkedLists.length && !myData.alone){
        data.desktopIDList = myData.checkedLists;
    }else{
        data.desktopIDList = [myData.desktopId];
    }
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
                $('#releaseModal').modal('hide');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            hideLoading();
            ajaxError(XMLHttpRequest, textStatus, errorThrown);
        }
    });
});

//内测与公开变化事件
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

//创建桌面组下拉框
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

function updateTable(page, name) {
    var url = '';
    if (name) {
        url = '/desktop/desktopLists?name=' + name + '&page=' + page + '&pageSize=' + pageSize + '&groupId=' + myData.groupId;
    } else {
        url = '/desktop/desktopLists?page=' + page + '&pageSize=' + pageSize + '&groupId=' + myData.groupId;
    }
    AjaxGet(url, function(data) {
        createElem(data, page);
    });
}

//移动桌面事件
$('#subMGroup').on('click', function(){
    var groupId = $('#mGroupName').val();

    if(groupId === '请选择组' || !groupId){
        alert('请选择组！');
        return;
    }

    AjaxPost('/desktop/getDesktopNameForIdArr', myData.checkedLists, function(nameData){
        var con = "";
        for(var i = 0, len = nameData.extra.length; i < len; i++){
            con += nameData.extra[i] + '\n';
        }
        if (confirm('确定移动名称为：\n'+ con +'的桌面？')) {
            var filter = $('#myTable_filter input').val() || '';
            AjaxPost('/desktop/moveDesktop', {"idLists": myData.checkedLists, "groupId": groupId}, function() {
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

//返回桌面列表
$('#myLayout .my-close').on('click', function() {
    var tds = $('#myTable tbody tr').find('td:eq(0)');
    $('#myLayout').hide();
    $('#desktopContent').show();
    for(var i = 0, len = tds.length; i < len; i++){
        $td = $(tds[i]);
        if($td.text() == myData.desktopId){
            $td.trigger('click');
            return false;
        }
    }
});

//进入桌面控件布局
$('#handleScreen').on('click', function() {
    $('#screenBtn').hide();
    $('#screenHandleBtn').show();
});

//退出桌面控件布局
$('#backSrceen').on('click', function() {
    $('#screenHandleBtn').hide();
    $('#screenBtn').show();
});

//进入桌面数据布局
$('#setDesktopDate').on('click', function() {
    $('#myLayout').hide();
    updateDesktopDate();
    $('#myDataLayout').show();
});

//进入桌面数据布局更新块位置
function updateDesktopDate() {
    var screens = $('#screenDataWarp ul li');
    desktopData.screens.forEach(function(elem, idx) {
        if (elem.blocks.length > 0) {
            var $screen = $(screens[idx]);
            elem.blocks.forEach(function(e, i) {
                var bg = '#' + e.bg.slice(-6);
                var opacity = '0';
                if(e.bg.length > 7){
                    opacity = parseInt(e.bg.slice(1, 3), 16) / 255;
                }
                var $block = $screen.find('.screen-block:eq(' + i + ')');
                $block.css({
                    "left": e.x + 'px',
                    "top": e.y + 'px',
                    "width": e.w + 'px',
                    "height": e.h + 'px',
                    "background-color": bg.getRgbColor(opacity+'')
                }).attr({
                    "data-yw": e.yw,
                    "data-yh": e.yh
                });
                if(e.operation === 'true'){
                    var x = (Number(e.w) - 48 - 7) + 'px';
                    $block.find('.block-data').css({ //运营坑位
                        'background-position': x + ' 7px'
                    });
                }
            });
        }
    });
    if(desktopData.nav){
        $('#navDataWarp').css({
            "left": desktopData.nav.x + 'px',
            "top": desktopData.nav.y + 'px'
        });
    }
}

//返回桌面
$('#backDesktop').on('click', function() {
    $('#myDataLayout').hide();
    $('#myLayout').show();
});

//清空桌面信息
function clearDesktopInfo() {
    desktopData.nav = null;
    desktopData.logo = null;
    desktopData.timebar = null;
    desktopData.weather = null;
    desktopData.timeWeather = null;
    desktopData.sn = null;
    desktopData.attachment = null;
    desktopData.quickEntry = null;
    desktopData.quickList = null;
    desktopData.quickEntryThreeState = null;
    desktopData.quickEntryTwoState = null;
    desktopData.shortCutConfig = null;
    desktopData.image = null;
    desktopData.appConfig = {};
    desktopData.style = null;
    desktopData.animation = '';
    desktopData.enlargeVal= '';//jatai2016-03-10
    desktopData.screens = [];
    desktopData.quickEntrySlot = {
        "style": "SIMPLE",
        "animation": "SIMPLE",
        "globalItems": []
    };
    desktopData.screens.push({
        'blocks': []
    });

    $('#desktopName').val('');
    $('#screenTitle').text('');
    $('#screenSlots').text('');
    $('#navWarp').html('');
    $('#navDataWarp').html('');
    $('#logoWarp').html('');
    $('#timerWarp').html('');
    $('#weatherWarp').html('');
    $('#timeWeatherWarp').html('');
    $('#attachmentWarp').html('');
    $('#quickWarp').html('');
    $('#threeWarp').html('');
    $('#twoWarp').html('');
    $('#snWarp').html('');
    $('#quickSlotWarp').html('');
    $('.screenWarp').css({
        "background-image": 'none',
        "background-size": "100% 100%"
    });
    setOpacity();
    $('#screenWarp ul').html('<li></li>');
    $('#screenDataWarp ul').html('<li></li>');
    $('#screenSelect [name="handleisable"]:eq(1)').trigger('click');
    $('#screenSelect [name="gridisable"]:eq(1)').trigger('click');
}

//修改桌面时设置屏信息
function setDesktopInfo(data) {
    $('#desktopName').val(data.extra.name);
    $('#desktopDesc').val(data.extra.desc);
    if(data.extra.appConfig) {  //配置信息
        desktopData.appConfig = data.extra.appConfig || {};
    }
    if(data.extra.style){  //风格信息
        desktopData.style = data.extra.style;
    }

    if(data.extra.animation){  //动画信息
        desktopData.animation = data.extra.animation;
    }
    if(data.extra.enlargeVal){  //放大倍数//jatai2016-03-10
        desktopData.enlargeVal = data.extra.enlargeVal;
    }
    if (data.extra.nav) { //导航栏
        var navData = data.extra.nav;
        desktopData.nav = {
            "isShowIndicator": navData.isShowIndicator,
            "style": navData.style,
            "x": navData.x,
            "y": navData.y,
            "interval": navData.interval,
            "extraData": navData.extraData
        };
        createNav(desktopData.nav);
    }
    if (data.extra.attachment) { //附件栏
        var attachmentData = data.extra.attachment;

        desktopData.attachment = {
            "name": attachmentData.name,
            "isShowIndicator": attachmentData.isShowIndicator,
            "style": attachmentData.style,
            "x": attachmentData.x,
            "y": attachmentData.y,
            "interval": attachmentData.interval,
            "extraData": attachmentData.extraData
        };

        createAttachment(desktopData.attachment);
    }
    if (data.extra.quickEntry) { //快捷入口
        var quickData = data.extra.quickEntry;

        desktopData.quickEntry = {
            "name": quickData.name,
            "isShowIndicator": quickData.isShowIndicator,
            "style": quickData.style,
            "extraData": quickData.extraData
        };

        createQuick(data.extra.quickEntry);
    }
    if (data.extra.quickEntryTwoState) { //两态快捷入口
        var twoData = data.extra.quickEntryTwoState;

        desktopData.quickEntryTwoState = {
            "name": twoData.name,
            "isShowIndicator": twoData.isShowIndicator,
            "style": twoData.style,
            "extraData": twoData.extraData,
            "x": twoData.x,
            "y": twoData.y
        };

        createTwo(data.extra.quickEntryTwoState);
    }
    if (data.extra.quickEntryThreeState) { //三态快捷入口
        var threeData = data.extra.quickEntryThreeState;

        desktopData.quickEntryThreeState = {
            "name": threeData.name,
            "isShowIndicator": threeData.isShowIndicator,
            "style": threeData.style,
            "extraData": threeData.extraData,
            "x": threeData.x,
            "y": threeData.y
        };

        createThree(data.extra.quickEntryThreeState);
    }

    if (data.extra.quickEntryGroup) { //快捷入口组
        var QEGData = data.extra.quickEntryGroup;

        desktopData.quickEntryGroup = {
            "name": QEGData.name,
            "mList": QEGData.mList
        };

        createQuickEntryGroup(data.extra.quickEntryGroup);
    }

    if(data.extra.logo) { //LOGO
        var logoData = data.extra.logo;
        desktopData.logo = {
            "isShowIndicator": logoData.isShowIndicator,
            "style": logoData.style,
            "intervalTime": logoData.intervalTime,
            "x": logoData.x,
            "y": logoData.y,
            "logoLists": logoData.logoLists
        };
        $('#logoWarp').html('<img src="' +logoData.logoLists[0] + '" >').css({
            "left": logoData.x + 'px',
            "top": logoData.y + 'px'
        });
    }

    if(data.extra.quickList) {//底部快捷栏
        desktopData.quickList = data.extra.quickList;
    }

    if(data.extra.shortCutConfig){//快捷键
        desktopData.shortCutConfig = data.extra.shortCutConfig;
    }

    if(data.extra.image){//壁纸
        desktopData.image = data.extra.image;
        $('.screenWarp').css({
            "background-image": 'url('+ desktopData.image +')',
            "background-size": "100% 100%"
        });
    }

    if (data.extra.timebar) { //时间
        desktopData.timebar = {
            "timeFormat": data.extra.timebar.timeFormat,
            "isShowIndicator": data.extra.timebar.isShowIndicator,
            "style": data.extra.timebar.style,
            "x": data.extra.timebar.x,
            "y": data.extra.timebar.y
        };
        if(!data.extra.timeWeather){
            var timerShowStyle = getTimerShow(data.extra.timebar.timeFormat);
            $('#timerWarp').html('<div class="handle-font" style="color: #fff;font-size: 18px;line-height: 40px;height: 40px;font-family: 微软雅黑;">'+ timerShowStyle +'</div>').css({
                "top": data.extra.timebar.y + 'px',
                "left": data.extra.timebar.x + 'px',
                "width": timerShowStyle.gblen() * 9 + 15
            });
        }
    }
    if (data.extra.weather) { //天气
        desktopData.weather = {
            "isShowIndicator": data.extra.weather.isShowIndicator,
            "isShowCity": data.extra.weather.isShowCity,
            "isShowTemperature": data.extra.weather.isShowTemperature,
            "isShowDesc": data.extra.weather.isShowDesc,
            "isShowIcon": data.extra.weather.isShowIcon,
            "style": data.extra.weather.style,
            "x": data.extra.weather.x,
            "y": data.extra.weather.y
        };

        if(!data.extra.timeWeather){
            var con = '';
            var width = 24;

            if (data.extra.weather.isShowCity === 'true') {
                con += '广州 ';
                width += 41;
            }
            con += '今 ';
            if (data.extra.weather.isShowTemperature === 'true') {
                con += '25℃ - 34℃';
                width += 97;
            }

            $('#weatherWarp').html('<span class="handle-font" style="color: #fff;font-size: 18px;line-height: 40px;height: 40px;font-family: 微软雅黑;display: block;float:left;width: ' + width + 'px;">' + con + '</span>').css({
                "top": data.extra.weather.y + 'px',
                "left": data.extra.weather.x + 'px',
                "width": width + 50
            });

            if (data.extra.weather.isShowIcon === 'true') {
                $('#weatherWarp').append('<span class="handle-font" style="display: block;float: left;background-image: url(img/icon_weather.png);background-size: 40px 40px;width: 40px;height: 40px;"></span>');
            } else if (data.extra.weather.isShowIcon === 'false') {
                $('#weatherWarp').css('width', width).find('span:eq(1)').remove();
            }
        }
    }

    if(data.extra.timeWeather){//时间天气
        desktopData.timeWeather = {
            "x": data.extra.timeWeather.x,
            "y": data.extra.timeWeather.y,
            "style": data.extra.timeWeather.style
        };

        var width = "auto";

        $('#timeWeatherWarp').html('').css({
            "top": data.extra.timeWeather.y + 'px',
            "left": data.extra.timeWeather.x + 'px',
            "width": width
        });

        $('#timeWeatherWarp').append('<span class="handle-font" style="display: block;float: left;"><img src="img/timeWeather/' + data.extra.timeWeather.style +'.png"></span>');

    }

    if (data.extra.sn) { //SN
        desktopData.sn = {
            "prefixInfo": data.extra.sn.prefixInfo,
            "systemProperty": data.extra.sn.systemProperty,
            "ipmacroProperty": data.extra.sn.ipmacroProperty,
            "isShowIndicator": data.extra.sn.isShowIndicator,
            "style": data.extra.sn.style,
            "x": data.extra.sn.x,
            "y": data.extra.sn.y
        };
        $('#snWarp').html('<div class="handle-font" style="color: #fff;font-size: 18px;line-height: 40px;height: 40px;font-family: 微软雅黑;">'+ data.extra.sn.prefixInfo +'1234567890</div>').css({
            "top": data.extra.sn.y + 'px',
            "left": data.extra.sn.x + 'px'
        });
    }

    if(data.extra.quickEntrySlot){   //全局快捷坑位
        desktopData.quickEntrySlot = data.extra.quickEntrySlot;
        desktopData.quickEntrySlot.globalItems.forEach(function(elem){
            $('#quickSlotWarp').append(createBlock(elem, true, elem.slotId, true, true));
        });
    }

    if (data.extra.messageConfig) {
        desktopData.messageConfig = data.extra.messageConfig;
        $('#msgWarp').html('<div class="handle-font" style="color: #fff;line-height: 40px;height: 40px;font-family: 微软雅黑;">消息控件</div>').css({
            "top":  data.extra.messageConfig.y+'px',
            "left": data.extra.messageConfig.x+'px',
            "font-size": data.extra.messageConfig.fontSize+'px',
            "width": data.extra.messageConfig.width+'px'

        });
    }

    if (data.extra.screens.length) { //屏
        var $screenWarp = $('#screenWarp ul');
        var $screenDataWarp = $('#screenDataWarp ul');
        data.extra.screens.forEach(function(elem) {
            var $li = $('<li></li>');
            var $liData = $('<li></li>');
            var idx = Number(elem.index) - 1;
            elem.blocks.forEach(function(e, i) {
                delete elem.blocks[i].id;
                delete elem.blocks[i].screenId;
                delete elem.blocks[i].action;
                $li.append(createBlock(elem.blocks[i], false, elem.blocks[i].slotId));
                $liData.append(createBlock(elem.blocks[i], true, elem.blocks[i].slotId, true, true));
            });
            $screenWarp.find('li:eq(' + idx + ')').replaceWith($li);
            $screenDataWarp.find('li:eq(' + idx + ')').replaceWith($liData);
            desktopData.screens[idx].blocks = elem.blocks;
            desktopData.screens[idx].slotGroupId = elem.slotGroupId;
            desktopData.screens[idx].itemStyle = elem.itemStyle;

        });
        $('#screenWarp ul li').hide().eq(0).show();
        $('#screenDataWarp ul li').hide().eq(0).show();
        myData.slots = desktopData.screens[0].blocks;
        if (desktopData.screens[0].blocks) {
            $('#screenSlots').text(desktopData.screens[0].blocks.length);
        } else {
            $('#screenSlots').text('');
        }
        $('#screenTitle').text('(' + 1 + '/' + desktopData.screens.length + ')');
    }else{
        desktopData.screens[0].slotGroupId = '请选择组';
        myData.slots = desktopData.screens[0].blocks;
    }
    AjaxGet('/desktop/operationSlotGroupLists', function(groupData){
        selectSlotGroup(groupData, desktopData.screens[0].slotGroupId);
        $('#myLayout').show();
    });
}

//设置LOGO信息
function setLogoInfo(id) {
    AjaxGet('/desktop/desktopLogoFileLists?desktopId=' + id, function(obj) {
        if (obj.extra.length === 0) {
            desktopData.logo = null;
            return false;
        }
        desktopData.logo = {
            "isShowIndicator": obj.extra.isShowIndicator,
            "style": obj.extra.style,
            "intervalTime": obj.extra.intervalTime,
            "x": obj.extra.x,
            "y": obj.extra.y,
            "logoLists": obj.extra.logoLists
        };
        $('#logoWarp').html('<img src="' + obj.extra.logoLists[0] + '" >').css({
            "left": obj.extra.x + 'px',
            "top": obj.extra.y + 'px'
        });
    });
}

//设置导航事件
$('#setNav').on('click', function() {
    AjaxGet('/desktop/navLists', function(data) {
        selectNav(data);
        $('#navModal').modal('show');
    });
});

//设置附件栏事件
$('#setAttachment').on('click', function() {
    AjaxGet('/desktop/attachmentLists', function(data) {
        selectAttachment(data);
        $('#attachmentModal').modal('show');
    });
});

//设置快捷入口事件
$('#setQuick').on('click', function() {
    AjaxGet('/desktop/quickEntryLists', function(data) {
        selectQuick(data);
        $('#quickModal').modal('show');
    });
});

//设置两态快捷入口事件
$('#setTwo').on('click', function() {
    AjaxGet('/desktop/quickEntryTwoStateLists', function(data) {
        selectTwo(data);
        $('#twoModal').modal('show');
    });
});

//设置三态快捷入口事件
$('#setThree').on('click', function() {
    AjaxGet('/desktop/quickEntryThreeStateLists', function(data) {
        selectThree(data);
        $('#threeModal').modal('show');
    });
});

//设置快捷入组口事件
$('#setQuickEntryGroup').on('click', function() {
    AjaxGet('/desktop/getQuickEntryGroupLists', function(data) {
        selectQEG(data);
        if (desktopData.quickEntryGroup) {
            $('#QEGSelect').parent().show();
            $('#QEGInfo').parent().show();
        }else{
            $('#QEGSelect').parent().hide();
            $('#QEGInfo').parent().hide();
        }
        $('#quickEntryGroupModal').modal('show');
    });
});

//设置壁纸事件
$('#setWallpaper').on('click', function() {
    $('#wallpaperHide').val('');
    if(desktopData.image){
        $('#paperShow input:eq(0)').trigger('click');
        $('#wallpaperShow').val(desktopData.image);
    }else{
        $('#paperShow input:eq(1)').trigger('click');
        $('#wallpaperShow').val('');
    }
    $('#wallpaperModal').modal('show');
});

//设置LOGO事件
$('#setLogo').on('click', function() {
    $('#addLogo').parent().nextAll().remove();
    var $logoLists = [];
    if (desktopData.logo) {
        $('#logoTime').val(desktopData.logo.intervalTime);
        if (desktopData.logo.isShowIndicator == 'true') {
            $('#logoTitle input:eq(0)').trigger('click');
        } else {
            $('#logoTitle input:eq(1)').trigger('click');
        }
        $('#logoShow input:eq(0)').prop('checked', true);
        var logoLists = desktopData.logo.logoLists;
        var len = logoLists.length;
        for(var i = 0; i < len; i++){
            var $logo = $(getLogoHtml());
            var sss = $logo.find('.fileShow');
            $logo.find('.fileShow').val(logoLists[i]);
            $logoLists.push($logo);
        }
    } else {
        $('#logoTime').val('');
        $('#logoTitle input:eq(1)').trigger('click');
        $('#logoShow input:eq(1)').prop('checked', true);
        $logoLists.push($(getLogoHtml()));
    }
    $('#logoModal .my-form').append($logoLists);
    $('#logoShow input:checked').trigger('click');
    $('#logoModal').modal('show');
});

//设置时间事件
$('#setTimebar').on('click', function() {
    if (desktopData.timebar) {
        $('#timerFormat').val(desktopData.timebar.timeFormat);
        $('#timerShow input:eq(0)').trigger('click');
        if (desktopData.timebar.isShowIndicator === 'true') {
            $('#timerTitle input:eq(0)').trigger('click');
        } else {
            $('#timerTitle input:eq(1)').trigger('click');
        }
    } else {
        $('#timerShow input:eq(1)').trigger('click');
    }
    $('#timerModal').modal('show');
});

//设置天气事件
$('#setWeather').on('click', function() {
    if (desktopData.weather) {
        $('#weatherShow input:eq(0)').trigger('click');
        if (desktopData.weather.isShowIndicator === 'true') {
            $('#weatherTitle input:eq(0)').trigger('click');
        } else {
            $('#weatherTitle input:eq(1)').trigger('click');
        }
        if (desktopData.weather.isShowCity === 'true') {
            $('#weatherCity input:eq(0)').trigger('click');
        } else {
            $('#weatherCity input:eq(1)').trigger('click');
        }
        if (desktopData.weather.isShowTemperature === 'true') {
            $('#weatherTemperature input:eq(0)').trigger('click');
        } else {
            $('#weatherTemperature input:eq(1)').trigger('click');
        }
        if (desktopData.weather.isShowDesc === 'true') {
            $('#weatherDesc input:eq(0)').trigger('click');
        } else {
            $('#weatherDesc input:eq(1)').trigger('click');
        }
        if (desktopData.weather.isShowIcon === 'true') {
            $('#weatherIcon input:eq(0)').trigger('click');
        } else {
            $('#weatherIcon input:eq(1)').trigger('click');
        }
    } else {
        $('#weatherShow input:eq(1)').trigger('click');
    }
    $('#weatherModal').modal('show');
});

//设置时间天气
$('#setTimeWeather').on('click', function() {
    $('#pic').hide();
    $("#pic").css("padding-top", "0px");
    $('#pic').children().remove();
    if(desktopData.timeWeather){
        $('#timeWeatherShow input:eq(0)').trigger('click');
        $('#timeWeatherStyle').val(parseInt(desktopData.timeWeather.style));
    }else{
        $('#timeWeatherShow input:eq(1)').trigger('click');
    }

    if (desktopData.timebar) {
        $('#timerFormat').val(desktopData.timebar.timeFormat);
        $('#timerShow input:eq(0)').trigger('click');
        if (desktopData.timebar.isShowIndicator === 'true') {
            $('#timerTitle input:eq(0)').trigger('click');
        } else {
            $('#timerTitle input:eq(1)').trigger('click');
        }
    } else {
        $('#timerShow input:eq(1)').trigger('click');
    }

    if (desktopData.weather) {
        $('#weatherShow input:eq(0)').trigger('click');
        if (desktopData.weather.isShowIndicator === 'true') {
            $('#weatherTitle input:eq(0)').trigger('click');
        } else {
            $('#weatherTitle input:eq(1)').trigger('click');
        }
        if (desktopData.weather.isShowCity === 'true') {
            $('#weatherCity input:eq(0)').trigger('click');
        } else {
            $('#weatherCity input:eq(1)').trigger('click');
        }
        if (desktopData.weather.isShowTemperature === 'true') {
            $('#weatherTemperature input:eq(0)').trigger('click');
        } else {
            $('#weatherTemperature input:eq(1)').trigger('click');
        }
        if (desktopData.weather.isShowDesc === 'true') {
            $('#weatherDesc input:eq(0)').trigger('click');
        } else {
            $('#weatherDesc input:eq(1)').trigger('click');
        }
        if (desktopData.weather.isShowIcon === 'true') {
            $('#weatherIcon input:eq(0)').trigger('click');
        } else {
            $('#weatherIcon input:eq(1)').trigger('click');
        }
    } else {
        $('#weatherShow input:eq(1)').trigger('click');
    }

    $('#timeWeatherModal').modal('show');
});

//设置SN事件
$('#setSN').on('click', function() {
    if (desktopData.sn) {
        $('#snShow input:eq(0)').trigger('click');
        if (desktopData.sn.isShowIndicator === 'true') {
            $('#snTitle input:eq(0)').trigger('click');
        } else {
            $('#snTitle input:eq(1)').trigger('click');
        }
        $('#snPrefix').val(desktopData.sn.prefixInfo);
    } else {
        $('#snPrefix').val('');
        $('#snShow input:eq(1)').trigger('click');
    }
    $('#snModal').modal('show');
});

//设置焦点跳转
$('#setFocusId').on('click', function() {
    var $selectBlock = $('.selectBlock');
    var $handleSelected = $('.selectHandle');

    var sL = $selectBlock.length;
    var hL = $handleSelected.length;
    var i = 0, j = 0, type = '';
    var title = '';
    if(sL !== 1 && hL !== 1){
        alert('请选择一个块或一个控件');
        return false;
    }
    //缓存控件存放跳转ID的对象以便操作
    if(sL){
        i = getScreenIdx();
        j = $selectBlock.index();
        myData.focusData = desktopData.screens[i].blocks[j];
        title = '设置坑位'+ myData.focusData.slotId +'焦点跳转';
    }else if(hL){
        type = $handleSelected.get(0).id;
        if(type === 'navWarp'){
            j = $handleSelected.find('.active').index();
            myData.focusData = desktopData.nav.extraData[j];
            title = '设置导航位置'+ (j) +'焦点跳转';
        }else if(type === 'attachmentWarp'){
            j = $handleSelected.find('.active').index();
            myData.focusData = desktopData.attachment.extraData[j];
            title = '设置附件栏位置'+ (j) +'焦点跳转';
        }else{
            j = $handleSelected.index();
            type = $handleSelected.parent().get(0).id;
            if(type === 'quickWarp'){
                myData.focusData = desktopData.quickEntry.extraData[j];
                title = '设置快捷入口'+ myData.focusData.index +'焦点跳转';
            }else if(type === 'twoWarp'){
                myData.focusData = desktopData.quickEntryTwoState.extraData[j];
                title = '设置二态快捷入口'+ myData.focusData.index +'焦点跳转';
            }else if(type === 'threeWarp'){
                myData.focusData = desktopData.quickEntryThreeState.extraData[j];
                title = '设置三态快捷入口'+ myData.focusData.index +'焦点跳转';
            }else if(type === 'quickEntryGroupWarp'){
                var k = 0;
                if ($('#quickEntryGroupWarp #' + j.toString() + ' .row').length > 0) {
                    k = $('#quickEntryGroupWarp #' + j.toString() + ' .active').parent().index();
                }else{
                    k = $('#quickEntryGroupWarp #' + j.toString() + ' .active').index();
                }
                myData.focusData = desktopData.quickEntryGroup.mList[j].extra[k];
                title = '设置快捷入口组'+ myData.focusData.index +'焦点跳转';
            }else if(type === 'quickSlotWarp'){
                myData.focusData = desktopData.quickEntrySlot.globalItems[j];
                title = '设置全局快捷坑位'+ myData.focusData.slotId +'焦点跳转';
            }else{
                alert('该控件不支持焦点跳转！');
                return false;
            }
        }
    }else{
        j = $('#navWarp .active').index();
        myData.focusData = desktopData.nav.extraData[j];
        title = '设置导航位置'+ (j) +'焦点跳转';
    }

    myData.clearSelect = true;
    selectSlotsID.call($('.focus-id'));
    $('#focusIdShow input:eq(0)').trigger('click');

    $('#nextFocusUpId').val(myData.focusData.nextFocusUpId || '请选择ID');
    $('#nextFocusDownId').val(myData.focusData.nextFocusDownId || '请选择ID');
    $('#nextFocusLeftId').val(myData.focusData.nextFocusLeftId || '请选择ID');
    $('#nextFocusRightId').val(myData.focusData.nextFocusRightId || '请选择ID');

    $('#focusIdModal').modal('show').find('h4').text(title);
    return false;
});

//设置云宽高
$('#setCloudWH').on('click', function() {
    var $selectBlock = $('.selectBlock');
    var len = $selectBlock.length;
    if(len > 1){
        alert('请选择一个块');
        return false;
    }
    if (!myData.slot || len <= 0) {
        alert('请选择块');
        return;
    }
    if($selectBlock.hasClass('quick-block')){
        alert('快捷坑位不能设置云宽高');
        return;
    }
    myData.clearSelect = true;
    $('#cloudBatch input:eq(1)').trigger('click');
    $('#cloudWidth').val($selectBlock.attr('data-yw'));
    $('#cloudHeight').val($selectBlock.attr('data-yh'));
    $('#cloudModal').modal('show');
    return false;
});

//批量修改云宽高事件
$('#cloudBatch input').on('click', function(){
    var $this = $(this);
    var val = $this.val();
    $this.prop('checked', true);
    if(val === 'true'){
        $('.cloud-alone').hide();
        $('#cloudAll').parent().show();
    }else if(val === 'false'){
        $('.cloud-alone').show();
        $('#cloudAll').parent().hide();
    }
});

//设置风格信息
$('#setStyle').on('click', function(){
    if(desktopData.style){
        $('#displayQuantity').val(desktopData.style.displayQuantity);
        $('#slotAngle').val(desktopData.style.slotAngle);
        $('#areaX').val(desktopData.style.x || '');
        $('#areaY').val(desktopData.style.y || '');
        $('#areaW').val(desktopData.style.width || '');
        $('#areaH').val(desktopData.style.height || '');
        if(desktopData.style.isCircle === 'true'){
            $('#isCircle input:eq(0)').prop('checked', true);
        }else{
            $('#isCircle input:eq(1)').prop('checked', true);
        }
        $('#styleType').val(desktopData.style.name).trigger('change');
    }else{
        $('#displayQuantity').val('');
        $('#slotAngle').val('');
        $('#isCircle input:eq(0)').prop('checked', true);
        $('#styleType').val('default').trigger('change');
    }
    $('#animationType').val(desktopData.animation);
    //jatai2016-03-10
    var val= desktopData.animation;

    if(val=='enlarge'||val=='enlarge_rotate'){
        $('#enlargeVal').val(desktopData.enlargeVal);
        $('.animationType').show();
    }else{
        $('#enlargeVal').val('1.00');
        $('.animationType').hide();
    }

    $('#styleModal').modal('show');
});
//设置屏风格信息
$('#setScreenStyle').on('click', function(){
    var i=$('#navWarp a[class=active]').index();
        if(i==-1){i=0}
        if(desktopData.screens[i].itemStyle){
            $('#screenDisplayQuantity').val(desktopData.screens[i].itemStyle.displayQuantity);
            $('#screenSlotAngle').val(desktopData.screens[i].itemStyle.slotAngle);
            $('#screenAreaX').val(desktopData.screens[i].itemStyle.x || '');
            $('#screenAreaY').val(desktopData.screens[i].itemStyle.y || '');
            $('#screenAreaW').val(desktopData.screens[i].itemStyle.width || '');
            $('#screenAreaH').val(desktopData.screens[i].itemStyle.height || '');
            if(desktopData.screens[i].itemStyle.isCircle === 'true'){
                $('#screenIsCircle input:eq(0)').prop('checked', true);
            }else{
                $('#screenIsCircle input:eq(1)').prop('checked', true);
            }
            if(desktopData.screens[i].itemStyle.name==='coverflow'){
                $('#screenStyleType').val('coverflow').trigger('change');
            }else{
                $('#screenStyleType').val('default').trigger('change');
            }

        }else{
            $('#screenStyleType').val('default').trigger('change');
            $('#screenDisplayQuantity').val('');
            $('#screenSlotAngle').val('');
            $('#screenAreaX').val('');
            $('#screenAreaY').val('');
            $('#screenAreaW').val('');
            $('#screenAreaH').val('');
            $('#screenIsCircle input:eq(0)').prop('checked', true);
            $('#screenStyleType').val('default').trigger('change');
        }

    $('#screenStyleModal').modal('show');
});
//设置配置信息
$('#setConfigure').on('click', function(){
    selectSlotsID.call($('#firstSlotId'));
    var data = desktopData.appConfig;
    if(data.isDisposeNavLeft == 'true'){
        $('#isDisposeNavLeft input:eq(0)').trigger('click');
    }else if(data.isDisposeNavLeft == 'false'){
        $('#isDisposeNavLeft input:eq(1)').trigger('click');
    }
    if(data.isDisposeNavRight == 'true'){
        $('#isDisposeNavRight input:eq(0)').trigger('click');
    }else if(data.isDisposeNavRight == 'false'){
        $('#isDisposeNavRight input:eq(1)').trigger('click');
    }
    if(data.isCreateNavBottomLine == 'true'){
        $('#isCreateNavBottomLine input:eq(0)').trigger('click');
    }else if(data.isCreateNavBottomLine == 'false'){
        $('#isCreateNavBottomLine input:eq(1)').trigger('click');
    }
    if(data.isSkipYunOSCheck == 'true'){
        $('#isSkipYunOSCheck input:eq(0)').trigger('click');
    }else if(data.isSkipYunOSCheck == 'false'){
        $('#isSkipYunOSCheck input:eq(1)').trigger('click');
    }
    if(data.isSkipYunOSReport == 'true'){
        $('#isSkipYunOSReport input:eq(0)').trigger('click');
    }else if(data.isSkipYunOSReport == 'false'){
        $('#isSkipYunOSReport input:eq(1)').trigger('click');
    }
    if(data.isAllowReplaceWallpaper == 'true'){
        $('#isAllowReplaceWallpaper input:eq(0)').trigger('click');
    }else if(data.isAllowReplaceWallpaper == 'false'){
        $('#isAllowReplaceWallpaper input:eq(1)').trigger('click');
    }
    if(data.isAllowSlotEmpty == 'true'){
        $('#isAllowSlotEmpty input:eq(0)').trigger('click');
    }else if(data.isAllowSlotEmpty == 'false'){
        $('#isAllowSlotEmpty input:eq(1)').trigger('click');
    }
    $('#slotCornerRadius').val(data.slotCornerRadius);
    $('#focusTheta').val(data.focusTheta);
    $('#focusImageShow').val(data.focusImage);
    $('#focusImageHide').val('');
    $('#focusStyle').val(data.focusStyle);
    $('#setBlur').val(data.isBlurEnabled);

    $('#firstSlotId').val(data.firstSlotId || '请选择ID');

    $('#configureModal').modal('show');
});

//设置底部快捷栏
$('#setQuickList').on('click', function(){
    AjaxGet('/App/apkLists', function(data){
        myData.appNameSelect = data.extra;
        AjaxGet('/desktop/quickLists', function(data){
            selectQuickList(data);
            $('#quickListModal').modal('show');
        });
    });
});

//设置快捷键
$('#setQuickKey').on('click', function(){
    var data = desktopData.shortCutConfig || [];
    AjaxGet('/desktop/shortCutsLists', function(shortCutsData){
        sleectShortCut(shortCutsData);
        createQuickTable(data);
        trHover('#quickTable');
        $('#myLayout').hide();
        $('#quickContent').show();
    });
});

//生成焦点ID下拉框选项
function selectSlotsID(){

    var arr = $('#screenWarp .common-block span');
    var con = '<option value="请选择ID">请选择ID</option>';
    var $select = this;
    var len = arr.length;
    var i = 0;
    for (i = 0; i < len; i++) {
        var elem = $(arr[i]).text();
        con += '<option value="' + elem + '">' + elem + '（坑位）</option>';
    }
   //
    var quickarr = $('#screenWarp .quick-block span');
    var quicklen = quickarr.length;

    for (k = 0; k < quicklen; k++) {
        var quickelem = $(quickarr[k]).text();
        con += '<option value="' + quickelem + '">' + quickelem + '（快捷坑位）</option>';
    }

    var globalarr = $('#quickSlotWarp .quick-slot-block .block-data span');
    var globallen=globalarr.length;
    for(var j=0;j<globallen;j++){
        var globalelem=$(globalarr[j]).text();
        con += '<option value="' + globalelem + '">' + globalelem + '（快捷坑位）</option>';
    }
    len = $('#navWarp a').length;
    for (i = 0; i < len; i++) {
        con += '<option value="' + i + '">' + i + '（导航）</option>';
    }    con += desktopData.quickEntry && getSlotsID(desktopData.quickEntry.extraData, '快捷入口');
    con += desktopData.quickEntryTwoState && getSlotsID(desktopData.quickEntryTwoState.extraData, '两态快捷入口');
    con += desktopData.quickEntryThreeState && getSlotsID(desktopData.quickEntryThreeState.extraData, '三态快捷入口');
    con += desktopData.attachment && getSlotsID(desktopData.attachment.extraData, '附件栏');

    $select.html(con);
}

function getSlotsID(arr, type){
    var len = arr.length;
    var con = '';
    for (var i = 0; i < len; i++) {
        var elem = arr[i].index;
        con += '<option value="' + elem + '">' + elem + '（'+ type +'）</option>';
    }
    return con;
}

//控件是否可移动事件
$('#screenSelect').on('click', '[name="handleisable"]', function() {
    var val = $(this).val();
    $(this).prop('checked', true);
    if (val == 'true') {
        HANDLE_IS_ABLE = true;
    } else if (val == 'false') {
        HANDLE_IS_ABLE = false;
    }
});

//检查对齐
$('#checkOffset').on('click', function() {
    $('.selectBlock').removeClass('selectBlock');
    var con = getSlotsPos();
    if (con) {
        if (confirm(con + '\n----确定自动对齐？')) {
            setSlotOffset();
        }
    } else {
        alert('坑位已对齐');
    }
});

//导航显示事件
$('#navShow input').on('click', function() {
    var $this = $(this);

    isShowHandle($this);
    if ($this.val() === 'true') {
        $('#navSelect').trigger('change');
        $('#navInfo').parent().show();
    } else {
        $("#navInfo").parent().hide();
    }
});

//附件栏显示事件
$('#attachmentShow input').on('click', function() {
    var $this = $(this);
    isShowHandle($this);
    if ($this.val() === 'true') {
        $('#attachmentSelect').trigger('change');
        $('#attachmentInfo').parent().show();
    } else {
        $("#attachmentInfo").parent().hide();
    }
});

//快捷入口显示事件
$('#quickShow input').on('click', function() {
    var $this = $(this);
    isShowHandle($this);
    if ($this.val() === 'true') {
        $('#quickSelect').trigger('change');
        $('#quickInfo').parent().show();
    } else {
        $("#quickInfo").parent().hide();
    }
});

//两态快捷入口显示事件
$('#twoShow input').on('click', function() {
    var $this = $(this);
    isShowHandle($this);
    if ($this.val() === 'true') {
        $('#twoSelect').trigger('change');
        $('#twoInfo').parent().show();
    } else {
        $("#twoInfo").parent().hide();
    }
});

//三态快捷入口显示事件
$('#threeShow input').on('click', function() {
    var $this = $(this);
    isShowHandle($this);
    if ($this.val() === 'true') {
        $('#threeSelect').trigger('change');
        $('#threeInfo').parent().show();
    } else {
        $("#threeInfo").parent().hide();
    }
});

//壁纸显示事件
$('#paperShow input').on('click', function() {
    var $this = $(this);
    isShowHandle($this);
});

//LOGO显示事件
$('#logoShow input').on('click', function() {
    isShowHandle($(this));
});

//时间显示事件
$('#timerShow input').on('click', function() {
    isShowHandle($(this));
});

//天气显示事件
$('#weatherShow input').on('click', function() {
    isShowHandle($(this));
});

//新时间天气显示事件
$('#timeWeatherShow input').on('click', function() {
    isShowHandle($(this));
});

//SN显示事件
$('#snShow input').on('click', function() {
    isShowHandle($(this));
});

//底部快捷栏显示事件
$('#quickListShow input').on('click', function() {
    isShowHandle($(this));
});

//焦点跳转开启事件
$('#focusIdShow input').on('click', function() {
    isShowHandle($(this));
});

//控件显示函数
function isShowHandle($this) {
    $this.prop('checked', true);
    if ($this.val() === 'true') {
        $this.parent().siblings().show();
    } else {
        $this.parent().siblings().hide();
    }
}

//新建桌面事件
$('#subCopy').on('click', function() {
    $('#desktopContent').hide();
    var desktopId = $('#copySelect').val();
    var desktopName = $('#newDesktopName').val();
    var newDesktopDesc = $('#newDesktopDesc').val() || '';
    if (desktopName == ' ' || !desktopName) {
        alert('请输入桌面名称');
        return;
    }
    clearDesktopInfo();
    desktopAction = 'add';
    if (desktopId === '空模板') {
        $('#myLayout').show();
        $('#desktopName').val(desktopName);
        $('#desktopDesc').val(newDesktopDesc);
        myData.desktopName = desktopName;
        myData.slots = desktopData.screens[0].blocks;
        desktopData.appConfig = {
            "isDisposeNavLeft": "false",
            "isDisposeNavRight": "false",
            "isCreateNavBottomLine": "false",
            "slotCornerRadius": "0",
            "focusTheta": "45",
            "isSkipYunOSReport": "false",
            "isAllowReplaceWallpaper": "true",
            "isAllowSlotEmpty": "false",
            "isSkipYunOSCheck": "false",
            "firstSlotId": "   ",
            "focusImage": "",
            "focusStyle": "slide",
            "isBlurEnabled": "true"
        };
        desktopData.animation = '';
        desktopData.enlargeVal = '';//jatai2016-03-10
        AjaxGet('/desktop/operationSlotGroupLists', function(groupData){
            selectSlotGroup(groupData, desktopData.screens[0].slotGroupId = '请选择组');
            $('#subAllData').trigger('click', true);
            $('#slotCornerRadius').val('0');
            $('#focusTheta').val('45');
            $('#myLayout').show();
        });
    } else {
        showLoading();
        AjaxGet('/desktop/desktopLists?id=' + desktopId, function(data) {
            setDesktopInfo(data);
            $('#desktopName').val(desktopName);
            $('#desktopDesc').val(newDesktopDesc);
            myData.desktopName = desktopName;
            $('#subAllData').trigger('click', true);
        }, true);
    }
    $('#copyModal').modal('hide');
});

//新建屏事件
$('#addScreen').on('click', function() {
    showScreenModal('添加屏');
});

//修改屏事件
$('#editScreen').on('click', function() {
    showScreenModal('修改屏');
});

//删除屏事件
$('#delScreen').on('click', function() {
    if (confirm('确定删除屏？')) {
        var idx = getScreenIdx();
        $('#screenWarp ul li:eq(' + idx + ')').html('');
        $('#screenDataWarp ul li:eq(' + idx + ')').html('');
        myData.slots = [];
        desktopData.screens[idx].blocks = [];
        $('#screenSlots').text('');
        alert('屏删除成功');
    }
});

//添加块
$('#addBlock').on('click', function() {
    if (!desktopData.screens[0].blocks) {
        alert('请先添加屏');
        return;
    }
    AjaxGet('/desktop/blockLists', function(data) {
        createBlockList(data); //创建块模板列表
        $('#myBlocks_filter input').val('').trigger('keyup');
        $('#blockModal').modal('show');
    });
});

//删除块
$('#delBlock').on('click', function() {
    var blockSelect = $('.selectBlock');
    var quickSelect = $('#quickSlotWarp .selectHandle');
    var $obj = null;
    if (blockSelect.length === 0 && quickSelect.length === 0) {
        alert('请选择块');
        return;
    }

    if (confirm('确定删除块？')) {
        var num = 0;
        if(blockSelect.length){
            while($('.selectBlock').length){
                $obj = $('.selectBlock:eq(0)');
                delSelectBlocks($obj);
                num++;
            }

            var $screenSlots = $('#screenSlots');
            $screenSlots.text(Number($screenSlots.text()) - num);
            clearSelectBlock();
        }else if(quickSelect.length){
            while($('#quickSlotWarp .selectHandle').length){
                $obj = $('#quickSlotWarp .selectHandle:eq(0)');
                delSelectQuicks($obj);
                num++;
            }
            refreshSlotPos({
                "x": "",
                "y": "",
                "w": "",
                "h": "",
                "bg": ""
            });
        }
    }
});

//修改非全局块ID事件
$('.screenWarp').on('click', 'ul li div span', function() {
    var $this = $(this);
    var $input = $this.siblings('input');
    var val = $this.text();
    $this.hide();
    $this.attr('placeholder', val);
    $input.val(val);
    $input.show().focus().select();
});

//修改全局快捷坑位块ID事件
$('#quickSlotWarp').on('click', '.block-data span', function() {
    var $this = $(this);
    var $input = $this.siblings('input');
    var val = $this.text();
    $this.hide();
    $this.attr('placeholder', val);
    $input.val(val);
    $input.show().focus().select();
});

//非全局块ID失去焦点
$('.screenWarp').on('blur', 'ul li div input', function() {
    var $this = $(this);
    var $span = $this.siblings('span');
    var val = $this.val();
    var blockIdx = $this.parent().parent().index();
    var $block = null;
    if (val == ' ' || !val) {
        val = $this.attr('placeholder');
    }
    if (/\D/.test(val)) {
        alert('ID只能为数字');
        val = $this.attr('placeholder');
    }
    $this.attr('placeholder', val);
    $this.hide();
    $span.show().text(val);
    var idx = getScreenIdx();
    $('#screenWarp ul li:eq(' + idx + ')').find('.screen-block:eq(' + blockIdx + ') span').text(val);
    $('#screenDataWarp ul li:eq(' + idx + ')').find('.screen-block:eq(' + blockIdx + ') span').text(val);
    desktopData.screens[idx].blocks[blockIdx].slotId = val;
    if (desktopData.screens[idx].blocks[blockIdx].operationId) {//块为运营坑位时重新获取数据
        desktopData.screens[idx].blocks[blockIdx].operationId = val;
        $block = $('#screenDataWarp ul li:eq(' + idx + ')').find('.screen-block:eq(' + blockIdx + ')');
        updateBlockDate($block, desktopData.screens[idx].blocks[blockIdx], true);
    }else if(desktopData.screens[idx].blocks[blockIdx].type === 'quickEntry'){
        $block = $('#screenDataWarp ul li:eq(' + idx + ')').find('.screen-block:eq(' + blockIdx + ')');
        updateBlockDate($block, desktopData.screens[idx].blocks[blockIdx], true);
    }
});

//全局快捷坑位块ID失去焦点
$('#quickSlotWarp').on('blur', '.block-data input', function() {
    var $this = $(this);
    var $span = $this.siblings('span');
    var val = $this.val();
    var blockIdx = $this.parent().parent().index();
    var $block = null;
    if (val == ' ' || !val) {
        val = $this.attr('placeholder');
    }
    if (/\D/.test(val)) {
        alert('ID只能为数字');
        val = $this.attr('placeholder');
    }
    $this.attr('placeholder', val);
    $this.hide();
    $span.show().text(val);

    desktopData.quickEntrySlot.globalItems[blockIdx].slotId = val;
    $block = $('#quickSlotWarp .screen-block:eq(' + blockIdx + ')');
    updateBlockDate($block, desktopData.quickEntrySlot.globalItems[blockIdx], true);
});

//修改普通块数据事件
$('#screenDataWarp').on('click', '.common-block .block-data i', function() {
    var title = $(this).siblings('span').text();
    myData.dataIdx = $(this).parent().parent().index();
    myData.dataTitle = title;
    $('#dataModal').modal('show');
    AjaxWhen([
        AjaxGet('/desktop/actionAppLists', selectApp, true),
        AjaxGet('/desktop/layoutTypeLists', selectLayout, true),
        AjaxGet('/App/apkLists', selectApk, true)
    ], function(){
        $('#dataModal h4').text(title);
        setBlockData();
    });
});

//修改快捷块数据事件
$('#screenDataWarp').on('click', '.quick-block .block-data i', function() {
    var title = $(this).siblings('span').text();
    myData.dataIdx = $(this).parent().parent().index();
    myData.dataTitle = title;
    $('#quickSlotModal').modal('show');
    AjaxWhen([
        AjaxGet('/desktop/actionAppLists', function(data){
            selectApp(data, $('#jumpAppQuickEntry'));
        }, true),
        AjaxGet('/App/apkLists', function(data){
            selectApk(data, $('#appName'));
        }, true)
    ], function(){
        $('#quickSlotModal h4').text(title);
        setQuickBlockData();
    });
});

//修改普通块时清空数据
function clearBlockData() {
    $('#editType input:eq(1)').trigger('click');
    $('#jumpType input:eq(0)').trigger('click');
    $('#slotType input:eq(1)').trigger('click');
    $('#dataType input:eq(1)').trigger('click');
    $('#disconnectType input:eq(1)').trigger('click');

    $('#layoutType').val('请选择显示效果').trigger('change');

    $('#pkgName').val("");
    $('#versionCode').val("请选择绑定应用版本");
    $('#appUrl').val("请选择绑定应用路径");

    $('#fileShow2').val("");
    $('#fileHide2').val("");
    $('.picType').hide();

    $('#uriVal').val("");
    $('#videoList').html('');
    $('#videoUrl').val("");
    $('#videoDuration').val("");
    myData.videoLists = [];
}

//修改快捷块时清空数据
function clearQuickBlockData() {
    $('#slotIdQuickEntry').val("");
    $('#soltTitleQuickEntry').val("");
    $('#quickFileShow1').val("");
    $('#quickFileShow2').val("");
    $('#quickFileHide1').val("");
    $('#quickFileHide2').val("");
    $('#appNameQuickEntry').val("请选择绑定应用名称");

    $('#editTypeQuickEntry input:eq(1)').trigger('click');

    $('#jumpTypeQuickEntry > input:eq(0)').trigger('click');

    $('#uriValQuickEntry').val("");

    $('#versionCodeQuickEntry').val("请选择绑定应用版本");
    $('#appUrlQuickEntry').val("请选择绑定应用路径");
}

//在块数据中创建跳转应用
function selectApp(data, $obj) {
    var arr = data.extra;
    var con = '<option value="请选择跳转应用">请选择跳转应用</option>';
    var $select = $obj || $('#jumpApp');
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

//快捷块中根据跳转应用变化创建跳转详情页
$('#jumpAppQuickEntry').on('change', function(e, name) {
    var id = $(this).val();
    var $select = $('#jumpDetailQuickEntry');
    var $appName = $('#appNameQuickEntry');
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

//跳转应用变化时显示跳转详情页
$('#jumpApp').on('change', function(e, name) {
    var id = $(this).val();
    var $select = $('#jumpDetail');
    var $appName = $('#appName');
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

//生成详情页下拉框
function selectDetail($select, id, name) {
    AjaxGet('/desktop/actionAppLists?id=' + id, function(data) {
        var arr = data.extra.extraData;
        var con = '<option value="请选择跳转详情页">请选择跳转详情页</option>';
        var len = arr.length;
        for (var i = 0; i < len; i++) {
            con += '<option value="' + arr[i].id + '" data-name="' + arr[i].detailName + '" >' + arr[i].detailName + '</option>';
            $select.data('_' + arr[i].id, arr[i]);
        }
        $select.html(con).parent().show();
        if (name) {
            var option = $select.find('option').filter('[data-name="' + name + '"]');
            option.prop("selected", true);
        }
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
    });
}

//创建显示效果下拉框
function selectLayout(data){
    var arr = data.extra;
    var con = '<option value="请选择显示效果">请选择显示效果</option>';
    var $select = $('#layoutType');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].type + '">' + arr[i].name + '</option>';
    }
    $select.html(con);
}

//创建布局类型下拉框
function selectLayout(data){
    var arr = data.extra;
    var con = '<option value="请选择显示效果">请选择显示效果</option>';
    var $select = $('#layoutType');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].type + '">' + arr[i].name + '</option>';
    }
    $select.html(con);
}

//创建绑定应用下拉框
function selectApk(data, $obj){
    var arr = data.extra;
    var con = '<option value="请选择绑定应用名称">请选择绑定应用名称</option>';
    var $select = $obj || $('#appName');
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

$('#appName').on('change', function(e, bindApp){
    bindAppType(e, bindApp, $('#versionCode'));
});

function bindAppType(e, bindApp, $obj){
    var val = $(e.target).val();
    if(val === '请选择绑定应用名称'){
        return false;
    }
    if(!val && bindApp){
        alert('绑定应用' + bindApp.appName + '不存在！');
        return false;
    }
    AjaxGet('/App/apkVersionLists?appName=' + val, function(data){
        selectVersion(data, bindApp, $obj);
    });
}

$('#appNameQuick').on('change', function(e, bindApp){
    bindAppType(e, bindApp, $('#versionCodeQuickEntry'));
});

function selectVersion(data, bindApp) {
    var arr = data.extra;
    var con = '<option value="请选择绑定应用版本">请选择绑定应用版本</option>';
    var $select = $('#versionCode');
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

$('#versionCode').on('change', function(e, url){
    var val = $('#versionCode option:checked').text();
    if(val === '请选择绑定应用版本'){
        $('#appUrl').html('<option value="请选择绑定应用路径">请选择绑定应用路径</option>');
        return false;
    }
    selectUrl($(this).data('_' + val), url);
});

$('#versionCodeQuickEntry').on('change', function(e, url){
    var val = $('#versionCodeQuickEntry option:checked').text();
    if(val === '请选择绑定应用版本'){
        $('#appUrlQuickEntry').html('<option value="请选择绑定应用路径">请选择绑定应用路径</option>');
        return false;
    }
    selectUrl($(this).data('_' + val), url, $('#appUrlQuickEntry'));
});

function selectUrl(data, url, $obj){
    var con = '';
    var $select = $obj || $('#appUrl');
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

//块数据是否为云事件
$('#dataType input').on('click', function() {
    var $this = $(this);
    $this.prop('checked', true);
    if ($this.val() === 'yunos') {
        $('.linkinType').hide();
        $('#editType').hide();
        $('#soltTitle').parent().hide();
    } else{
        $('#jumpType').show().find('input:checked').trigger('click');
        $('#appType').show().find('input:checked').trigger('click');
        $('#layoutType').trigger('change').parent().show();
        $('#soltTitle').parent().show();
        $('#editType').show();
    }
});

//是否为可替换变化
$('#editType > input').on('click', function() {
    var $this = $(this);
    var val = $this.val();
    var layoutType = $('#layoutType').val();
    $this.prop('checked', true);
    if (val === 'false' && (layoutType === 'APP' || layoutType === 'APP_CENTER_IMG_BOTTOM_TEXT')) {
        $('#appType input:eq(0)').trigger('click');
    }
});

//块数据是否为运营坑位
$('#slotType input').on('click', function() {
    var $this = $(this);
    $this.prop('checked', true);
    if ($this.val() === 'true') {
        $('#jumpType').hide();
        $('#layoutType').parent().hide();
        $('#soltTitle').parent().hide();
        $('.operationType').hide();
        $('#appType').hide();
        $('.appType').hide();
        $('#fileShow2').parent().hide();
        $('#dataType').hide();
        $('#editType').hide();
        $('#disconnectType').hide();
    } else if ($this.val() === 'false') {
        $('#editType').show();
        $('#disconnectType').show();
        $('#dataType').show().find('input:checked').trigger('click');
    }
});

//跳转信息类型变化
$('#jumpType > input').on('click', function() {
    var $this = $(this);
    var val = $this.val();
    $this.prop('checked', true);
    if (val === 'APP') {
        $('#uriVal').parent().hide();
        var $jumpApp = $('#jumpApp');
        $jumpApp.parent().show();
        $('#jumpDetail').parent().show();
        $('#appType').show();
    } else if (val === 'URI') {
        $('#handleType').parent().hide();
        $('#jumpApp').parent().hide();
        $('#jumpDetail').parent().hide();
        $('#uriVal').parent().show();
        $('#appType input:eq(1)').trigger('click');
        $('#appType').hide();
    }
});

//是否为绑定应用
$('#appType > input').on('click', function() {
    var $this = $(this);
    var val = $this.val();
    var layoutType = $('#layoutType').val();
    $this.prop('checked', true);
    if (val === 'true') {
        $('.appType').show();
    } else if (val === 'false') {
        if($('#editType input:checked').val() === 'false' && (layoutType === 'APP' || layoutType === 'APP_CENTER_IMG_BOTTOM_TEXT')){
            $('#appType input:eq(0)').trigger('click');
        }else{
            $('.appType').hide();
        }
    }
});

//显示效果变化
$('#layoutType').on('change', function() {
    var $this = $(this);
    var val = $this.val();
    var editType = $('#editType input:checked').val();
    if (val === 'VIDEO') {
        $('#addVideo').show();
    } else {
        $('#addVideo').hide();
    }
    if(editType === 'false' && (val === 'APP' || val === 'APP_CENTER_IMG_BOTTOM_TEXT')){
        $('#appType input:eq(0)').trigger('click');
    }
    var $select = $('#jumpDetail');
    if(val === 'APP' || val === 'APP_CENTER_IMG_BOTTOM_TEXT' || val === '请选择显示效果'){
        $('.picType').hide();
    }else{
        $select.parent().show();
        $('.picType').show();
    }
    $('#jumpType input:checked').trigger('click');
});

//添加坑位视频事件
$('#addVideo a').on('click', function() {
    $('#videoListModal').modal('show');
    $('#dataModal').modal('hide');
});

//从坑位视频返回
$('#videoListModal .my-back').on('click', function() {
    $('#dataModal').modal('show');
    $('#videoListModal').modal('hide');
});

//添加坑位视频
$('#subVideoList').on('click', function() {
    var url = $('#videoUrl').val();
    var duration = $('#videoDuration').val();

    if (url == ' ' || !url) {
        alert('请输入视频链接');
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

//删除坑位视频
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

//在块数据中创建ACTION名称
function selectActionName() {
    var $this = $(this);
    var val = $this.val();
    var $select = $('#actionName');
    $('#layoutType').trigger('change');
    if (val === '请选择ACTION类型') {
        $select.parent().hide();
        return false;
    } else {
        $select.parent().show();
    }
    var arr = $this.data('_' + val).value;
    var con = '';
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
        $select.data('_' + arr[i].id, arr[i]);
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}

//在块数据中是否显示图片
function showActionPic() {
    var $this = $(this);
    var val = $this.val();
    $('.data-layout').hide();
    if (val === 'IMAGE') {
        $('#actionPic').trigger('change').parent().show();
        $('#actionReload').show();
    } else if (val === 'VIDEO') {
        $('#videoUrl').parent().show();
        $('#videoTime').parent().show();
    }
}

//图片变化更新预览图
$('#actionPic').on('change', function() {
    var src = $(this).val();
    $('#actionReload').html('<label for="actionReload">预览图:</label>&nbsp;<i class="glyphicon glyphicon-picture icon-black my-icon" data-src=' + src + '></i>');
});

//在块数据中创建图片
function selectActionPic() {
    var $this = $(this);
    var val = $this.val();

    var $select = $('#actionPic');
    if (val === '请选择ACTION名称') {
        return;
    }

    var data = $this.data('_' + val);
    var con = '<option value="' + data.pic1 + '">pic1</option>' +
        '<option value="' + data.pic2 + '">pic2</option>' +
        '<option value="' + data.pic3 + '">pic3</option>';
    $select.html(con).trigger('change');
}

//弹出屏的模态框
function showScreenModal(type) {
    AjaxGet('/desktop/fragmentLists', function(data) {
        createScreenList(data.extra); //生成屏列表
        var $screenModal = $('#screenModal');
        $screenModal.find('h4').html(type);
        $screenModal.modal('show');
    });
}

//生成导航下拉框选项
function selectNav(data) {
    var arr = data.extra;
    var con = '<option value="请选择导航">请选择导航</option>';
    var $select = $('#navSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }
    if (desktopData.nav) {
        $('#navShow input:eq(0)').trigger('click');
        if (desktopData.nav.isShowIndicator === 'true') {
            $('#navTitle input:eq(0)').trigger('click');
        } else {
            $('#navTitle input:eq(1)').trigger('click');
        }
        $('#navStyle').val(desktopData.nav.style);
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change');
    } else {
        $('#navShow input:eq(1)').trigger('click');
        $('#navTitle input:eq(1)').trigger('click');
        $('#navStyle').val('SIMPLE');
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        }).trigger('change');
    }
}

//生成附加栏下拉框选项
function selectAttachment(data) {
    var arr = data.extra;
    var con = '<option value="请选择附件栏">请选择附件栏</option>';
    var $select = $('#attachmentSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '">' + arr[i].name + '</option>';
    }
    if (desktopData.attachment) {
        if (desktopData.attachment.isShowIndicator === 'true') {
            $('#attachmentTitle input:eq(0)').trigger('click');
        } else {
            $('#attachmentTitle input:eq(1)').trigger('click');
        }
        if(desktopData.attachment.name){
            $select.html(con).find('option[data-name="'+ desktopData.attachment.name +'"]').prop('selected', true);
            $select.data('custom-name', true);
        }else{
            $select.html(con);
            $select.data('custom-name', null);
        }
        $('#attachmentShow input:eq(0)').trigger('click');
    } else {
        $('#attachmentTitle input:eq(1)').trigger('click');
        $select.html(con);
        $('#attachmentShow input:eq(1)').trigger('click');
    }
}

//生成底部快捷栏下拉框选项
function selectQuickList(data, name) {
    var arr = data.extra;
    var con = '<option value="请选择底部快捷栏">请选择底部快捷栏</option>';
    var $select = $('#quickListSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '">' + arr[i].name + '</option>';
    }
    if (desktopData.quickList) {
        $('#quickListShow input:eq(0)').trigger('click');
    } else {
        $('#quickListShow input:eq(1)').trigger('click');
    }
    if(name){
        $select.html(con).find('option[data-name="'+ name +'"]').prop('selected', true);
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "440px"
        });
    }else{
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "440px"
        }).trigger('change');
    }
}

//生成快捷入口下拉框选项
function selectQuick(data) {
    var arr = data.extra;
    var con = '<option value="请选择快捷入口">请选择快捷入口</option>';
    var $select = $('#quickSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '">' + arr[i].name + '</option>';
    }
    if (desktopData.quickEntry) {
        if (desktopData.quickEntry.isShowIndicator === 'true') {
            $('#quickTitle input:eq(0)').trigger('click');
        } else {
            $('#quickTitle input:eq(1)').trigger('click');
        }
        if(desktopData.quickEntry.name){
            $select.html(con).find('option[data-name="'+ desktopData.quickEntry.name +'"]').prop('selected', true);
            $select.trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            }).data('custom-name', true);
        }else{
            $select.html(con).trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            });
            $select.data('custom-name', null);
        }
        $('#quickShow input:eq(0)').trigger('click');
    } else {
        $('#quickTitle input:eq(1)').trigger('click');
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
        $('#quickShow input:eq(1)').trigger('click');
    }
}

//生成快捷入口组下拉框选项
function selectQEG(data) {
    var arr = data.extra;
    var con = '<option value="请选择快捷入口组">请选择快捷入口组</option>';
    var $select = $('#QEGSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '">' + arr[i].name + '</option>';
    }
    if (desktopData.quickEntryGroup) {
        if(desktopData.quickEntryGroup.name){
            $select.html(con).find('option[data-name="'+ desktopData.quickEntryGroup.name +'"]').prop('selected', true);
            $select.trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            }).data('custom-name', true);
        }else{
            $select.html(con).trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            });
            $select.data('custom-name', null);
        }
//        $('#QEGSelect[data-name='+desktopData.quickEntryGroup.name+']').trigger('click');
        $('#QEGShow input:eq(0)').trigger('click');
    } else {
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
        $('#QEGShow input:eq(1)').trigger('click');
    }
}

$('#QEGShow').on('click', function() {
    if ($('#QEGShow').find('input:checked').val() === 'false') {
        $(this).siblings().hide();
    }else{
        $(this).siblings().show();
    }
});

//生成三态快捷入口下拉框选项
function selectThree(data) {
    var arr = data.extra;
    var con = '<option value="请选择三态快捷入口">请选择三态快捷入口</option>';
    var $select = $('#threeSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '">' + arr[i].name + '</option>';
    }
    if (desktopData.quickEntryThreeState) {
        if (desktopData.quickEntryThreeState.isShowIndicator === 'true') {
            $('#threeTitle input:eq(0)').trigger('click');
        } else {
            $('#threeTitle input:eq(1)').trigger('click');
        }
        if(desktopData.quickEntryThreeState.name){
            $select.html(con).find('option[data-name="'+ desktopData.quickEntryThreeState.name +'"]').prop('selected', true);
            $select.trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            }).data('custom-name', true);
        }else{
            $select.html(con).trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            });
            $select.data('custom-name', null);
        }
        $('#threeShow input:eq(0)').trigger('click');
    } else {
        $('#threeTitle input:eq(1)').trigger('click');
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
        $('#threeShow input:eq(1)').trigger('click');
    }
}

//生成两态快捷入口下拉框选项
function selectTwo(data) {
    var arr = data.extra;
    var con = '<option value="请选择两态快捷入口">请选择两态快捷入口</option>';
    var $select = $('#twoSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '">' + arr[i].name + '</option>';
    }
    if (desktopData.quickEntryTwoState) {
        if (desktopData.quickEntryTwoState.isShowIndicator === 'true') {
            $('#twoTitle input:eq(0)').trigger('click');
        } else {
            $('#twoTitle input:eq(1)').trigger('click');
        }
        if(desktopData.quickEntryTwoState.name){
            $select.html(con).find('option[data-name="'+ desktopData.quickEntryTwoState.name +'"]').prop('selected', true);
            $select.trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            }).data('custom-name', true);
        }else{
            $select.html(con).trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            });
            $select.data('custom-name', null);
        }
        $('#twoShow input:eq(0)').trigger('click');
    } else {
        $('#twoTitle input:eq(1)').trigger('click');
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
        $('#twoShow input:eq(1)').trigger('click');
    }
}

//生成桌面下拉框选项
function selectDesktop(data) {
    var arr = data.extra;
    var con = '<option value="空模板">空模板</option>';
    var $select = $('#copySelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }

    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}

//生成运营坑位组下拉框
function selectSlotGroup(data, id){
    var arr = data.extra;
    var con = '<option value="请选择组">请选择组</option>';
    var $select = $('#slotGroup');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }
    $select.html(con).val(id);
}

//导航变化事件
$('#navSelect').on('change', function() {
    var NavId = $(this).val();

    if(!NavId){
        return false;
    }
    if (NavId === '请选择导航') {
        $("#navInfo").parent().hide();
        if (desktopData.nav) {
            $('#navMargin').val(desktopData.nav.interval);
        } else {
            $('#navMargin').val('');
        }
    } else {
        AjaxGet('/desktop/navLists?id=' + NavId, updateNavInfo);
    }
});

//附件栏变化事件
$('#attachmentSelect').on('change', function() {
    var $this = $(this);
    var AttachmentId = $this.val();
    if($this.data('custom-name')){
        AttachmentId = '请选择附件栏';
        $this.data('custom-name', null);
    }

    $('#attachmentModal .custom-index').remove();
    if (desktopData.attachment && AttachmentId === '请选择附件栏') {
        var data = {"extra": desktopData.attachment};
        updateAttachmentInfo(data);
    } else {
        if (AttachmentId === '请选择附件栏') {
            $("#attachmentInfo").parent().hide();
            $('#attachmentMargin').val('');
        } else {
            AjaxGet('/desktop/attachmentLists?id=' + AttachmentId, updateAttachmentInfo);
        }
    }
});

//底部快捷栏变化事件
$('#quickListSelect').on('change', function() {
    var QuickListId = $(this).val();
    $('#quickListItem').html('');
    if (desktopData.quickList && QuickListId === '请选择底部快捷栏') {
        var data = {"extra": desktopData.quickList};
        updateQuickListInfo(data);
    } else {
        if (QuickListId === '请选择底部快捷栏') {
            return false;
        } else {
            AjaxGet('/desktop/quickLists?id=' + QuickListId, updateQuickListInfo);
        }
    }
});

//快捷入口变化事件
$('#quickSelect').on('change', function() {
    var $this = $(this);
    var QuickId = $this.val();
    if($this.data('custom-name')){
        QuickId = '请选择快捷入口';
        $this.data('custom-name', null);
    }

    $('#quickModal .custom-index').remove();
    if (desktopData.quickEntry && QuickId === '请选择快捷入口') {
        var data = {"extra": desktopData.quickEntry};
        updateQuickInfo(data);
    } else {
        if (QuickId === '请选择快捷入口') {
            $("#quickInfo").parent().hide();
        } else {
            AjaxGet('/desktop/quickEntryLists?id=' + QuickId, updateQuickInfo);
        }
    }
});

//两态快捷入口变化事件
$('#twoSelect').on('change', function() {
    var $this = $(this);
    var TwoId = $this.val();
    if($this.data('custom-name')){
        TwoId = '请选择两态快捷入口';
        $this.data('custom-name', null);
    }

    $('#twoModal .custom-index').remove();
    if (desktopData.quickEntryTwoState && TwoId === '请选择两态快捷入口') {
        var data = {"extra": desktopData.quickEntryTwoState};
        updateTwoInfo(data);
    } else {
        if (TwoId === '请选择两态快捷入口') {
            $("#twoInfo").parent().hide();
        } else {
            AjaxGet('/desktop/quickEntryTwoStateLists?id=' + TwoId, updateTwoInfo);
        }
    }
});

//三态快捷入口变化事件
$('#QEGSelect').on('change', function() {
    var $this = $(this);
    var QEGId = $this.val();
    $('#quickEntryGroupModal .modal-body').css('overflow', 'auto');
    // if($this.data('custom-name')){
    //     QEGId = '请选择快捷入口组';
    //     $this.data('custom-name', null);
    // }

    $('#quickEntryGroupModal .custom-index').remove();
    if (desktopData.quickEntryGroup && QEGId === '请选择快捷入口组') {
        var data = {"extra": [desktopData.quickEntryGroup]};
//        updateThreeInfo(data);
        updateQEGInfo(data);
    } else {
        if (QEGId === '请选择快捷入口组') {
            $("#QEGInfo").parent().hide();
        } else {
            AjaxGet('/desktop/getQuickEntryGroupLists?id=' + QEGId, updateQEGInfo);
        }
    }
});

//三态快捷入口变化事件
$('#threeSelect').on('change', function() {
    var $this = $(this);
    var ThreeId = $this.val();
    if($this.data('custom-name')){
        ThreeId = '请选择三态快捷入口';
        $this.data('custom-name', null);
    }

    $('#threeModal .custom-index').remove();
    if (desktopData.quickEntryThreeState && ThreeId === '请选择三态快捷入口') {
        var data = {"extra": desktopData.quickEntryThreeState};
        updateThreeInfo(data);
    } else {
        if (ThreeId === '请选择三态快捷入口') {
            $("#threeInfo").parent().hide();
        } else {
            AjaxGet('/desktop/quickEntryThreeStateLists?id=' + ThreeId, updateThreeInfo);
        }
    }
});

//更新导航预览图
function updateNavInfo(data) {
    var arr = data.extra;
    var left = parseInt(arr.x) / 4;
    var top = parseInt(arr.y) / 4;
    var margin = arr.interval / 4;
    var $navList = $('<div style="left: ' + left + 'px; top: ' + top + 'px;position: absolute;"></div>');
    for (var j = 0, l = arr.extra.length; j < l; j++) {
        var icon = arr.extra[j];
        delete arr.extra[j].id;
        delete arr.extra[j].navId;
        if (j === 0) {
            setIconWidth($navList, icon.forcusPath, margin);
        } else {
            setIconWidth($navList, icon.normalPath, margin);
        }
    }
    $('#navMargin').val(arr.interval);
    $("#navInfo").html('').append($navList).data("nav", arr).parent().show();
}

//更新附件栏预览图
function updateAttachmentInfo(data) {
    var arr = data.extra;
    var left = parseInt(arr.x) / 4;
    var top = parseInt(arr.y) / 4;
    var margin = arr.interval / 4;
    var $attachmentList = $('<div style="left: ' + left + 'px; top: ' + top + 'px;position: absolute;"></div>');
    var customIdx = '';
    var l = arr.extra ? arr.extra.length : arr.extraData.length;
    var icons = arr.extra || arr.extraData;
    for (var j = 0; j < l; j++) {
        var icon = icons[j];
        delete icons[j].id;
        if (j === 0) {
            setIconHeight($attachmentList, icon.forcusPath, margin, true);
        } else {
            setIconHeight($attachmentList, icon.normalPath, margin);
        }
        var index = icon.index || '';
        customIdx +=    '<div class="form-group custom-index">'+
                            '<label>控件'+ (j + 1) +':</label>'+
                            '<input type="text" class="form-control" placeholder="请设置ID（位置从上到下）" value="'+ index +'">'+
                        '</div>';
    }
    $('#attachmentMargin').val(arr.interval);
    $("#attachmentInfo").html('').append($attachmentList).data('attachment', arr).parent().show().before(customIdx);
}

//更新底部快捷栏信息
function updateQuickListInfo(data) {
    var con = '';
    var arr = data.extra;
    for(var i = 0, len = arr.length; i < len; i++){
        con = getQuickListHtml(arr[i]);
        $('#quickListItem').append(con);
        var l = $('.delQuickList').length;
        selectListAppName($('.quick-appName:eq('+ (l-1) +')'), arr[i]);
    }
    $('#quickListItem').sortable( "refresh" );
}

//更新快捷入口预览图
function updateQuickInfo(data) {
    var arr = data.extra;
    var margin = arr.interval / 4;
    var $quickList = $('<div style="width: 100%; top: 100%;text-align: left;"></div>');
    var customIdx = '';
    var l = arr.extra ? arr.extra.length : arr.extraData.length;
    var icons = arr.extra || arr.extraData;
    for (var j = 0; j < l; j++) {
        var icon = icons[j];
        delete icons[j].id;
        var left = parseInt(icons[j].itemX) / 4;
        var top = parseInt(icons[j].itemY) / 4;
        if (j === 0) {
            setIconQuick($quickList, icon.forcusPath, left, top);
        } else {
            setIconQuick($quickList, icon.normalPath, left, top);
        }
        var index = icon.index || '';
        customIdx +=    '<div class="form-group custom-index">'+
                            '<label>'+ icons[j].name +':</label>'+
                            '<input type="text" class="form-control" placeholder="请设置ID（位置按添加顺序）" value="'+ index +'">'+
                        '</div>';
    }
    $("#quickInfo").html('').append($quickList).data('quick', arr).parent().show().before(customIdx);
}

//更新两态快捷入口预览图
function updateTwoInfo(data) {
    var arr = data.extra;
    var margin = arr.interval / 4;
    var $twoList = $('<div style="width: 100%; top: 100%;text-align: left;"></div>');
    var customIdx = '';
    var l = arr.extra ? arr.extra.length : arr.extraData.length;
    var icons = arr.extra || arr.extraData;
    for (var j = 0; j < l; j++) {
        var icon = icons[j];
        delete icons[j].id;
        var left = parseInt(icons[j].x) / 4;
        var top = parseInt(icons[j].y) / 4;
        if (j === 0) {
            setIconQuick($twoList, icon.focusedActiveDrawable, left, top);
        } else {
            setIconQuick($twoList, icon.activeDrawable, left, top);
        }
        var index = icon.index || '';
        customIdx +=    '<div class="form-group custom-index">'+
                            '<label>'+ icons[j].name +':</label>'+
                            '<input type="text" class="form-control" placeholder="请设置ID（位置从左到右）" value="'+ index +'">'+
                        '</div>';
    }
    $("#twoInfo").html('').append($twoList).data('two', arr).parent().show().before(customIdx);
}

//更新三态快捷入口预览图
function updateQEGInfo(data) {
    var arr = data.extra.mList || data.extra[0].mList;
    var $QEGList = $('<div style="width: 100%; top: 100%;text-align: left;"></div>');
    var customIdx = '';
    for (var k = 0; k < arr.length; k++) {

            var left = parseInt(arr[k].x) / 4;
            var top = parseInt(arr[k].y) / 4;
            var margin = arr[k].distance / 4;
            var layout = arr[k].layout;
            var $navList = $('<div style="line-height: 0;left: ' + left + 'px; top: ' + top + 'px;text-align: left;position: absolute;"></div>');
            if (layout === 'vertical') {
                for (var j = 0, l = arr[k].extra.length; j < l; j++) {
                    var icon = arr[k].extra[j];
                    if (j === 0) {
                        $navList.append('<img src=' + icon.forcusPath + ' tar="0" style="margin-bottom: ' + margin + 'px;"><br>');
                    } else {
                        $navList.append('<img src=' + icon.normalPath + ' tar="0" style="margin-bottom: ' + margin + 'px;"><br>');
                    }

                    var index = arr[k].extra.index || '';
                    customIdx +=    '<div class="form-group custom-index">'+
                                    '<label>'+ arr[k].extra[j].name +':</label>'+
                                    '<input type="text" class="form-control" placeholder="请设置ID（位置从左到右）" value="'+ index +'">'+
                                    '</div>';

                }
            }else{
                for (var j = 0, l = arr[k].extra.length; j < l; j++) {
                    var icon = arr[k].extra[j];
                    if (j === 0) {
                        $navList.append('<img src=' + icon.forcusPath + ' tar="0" style="margin-right: ' + margin + 'px;">');
                    } else {
                        $navList.append('<img src=' + icon.normalPath + ' tar="0" style="margin-right: ' + margin + 'px;">');
                    }

                    var index = arr[k].extra.index || '';
                    customIdx +=    '<div class="form-group custom-index">'+
                                    '<label>'+ arr[k].extra[j].name +':</label>'+
                                    '<input type="text" class="form-control" placeholder="请设置ID（位置从左到右）" value="'+ index +'">'+
                                    '</div>';

                }
            }
            // if (arr[k].direction === 'left') {
            //     var len = $navList.children('img').length;
            //     var count = 0;
            //     $navList.children().load(function() {
            //         var l = Number( $(this).parent().css('left').split('px')[0] );
            //         var w = (this.width/4 + margin);
            //         var maxW = 0;
            //         for (var i = 0; i < $(this).parent().children().length; i++) {
            //             var childW = $(this).parent().children(':eq(' + i + ')').width()/4;
            //             if (childW >= maxW) {
            //                 maxW = childW;
            //             }
            //             if ((this.width/4) >= maxW) {
            //                 maxW = this.width/4;
            //             }
            //         }
            //         count++;
            //         if (count === len) {
            //             $(this).parent().css('left', ( Number(l) - ((Number(maxW) + Number(margin))*4) ).toString() + 'px' );
            //         }
            //         $(this).css('width', (this.width/4).toString()+'px');
            //     });
            // }else{
                $navList.children().load(function() {
                    $(this).css('width', (this.width/4).toString()+'px');
                });
            //}
            $QEGList.append($navList);

        }
    var QEGdata = data.extra || data.extra[0];
    $("#QEGInfo").html('').append($QEGList).data('QEG', QEGdata).parent().show().before(customIdx);
}

//更新三态快捷入口预览图
function updateThreeInfo(data) {
    var arr = data.extra;
    var margin = arr.interval / 4;
    var $threeList = $('<div style="width: 100%; top: 100%;text-align: left;"></div>');
    var customIdx = '';
    var l = arr.extra ? arr.extra.length : arr.extraData.length;
    var icons = arr.extra || arr.extraData;
    for (var j = 0; j < l; j++) {
        var icon = icons[j];
        delete icons[j].id;
        var left = parseInt(icons[j].x) / 4;
        var top = parseInt(icons[j].y) / 4;
        if (j === 0) {
            setIconQuick($threeList, icon.focusedDrawableA, left, top);
        } else {
            setIconQuick($threeList, icon.drawableA, left, top);
        }
        var index = icon.index || '';
        customIdx +=    '<div class="form-group custom-index">'+
                            '<label>'+ icons[j].name +':</label>'+
                            '<input type="text" class="form-control" placeholder="请设置ID（位置从左到右）" value="'+ index +'">'+
                        '</div>';
    }
    $("#threeInfo").html('').append($threeList).data('three', arr).parent().show().before(customIdx);
}

//导航提交事件
$('#subNav').on('click', function() {
    var navId = $('#navSelect').val();
    var isShowIndicator = $('#navTitle').find('input:checked').val();
    var style = $('#navStyle').val();
    var navShow = $('#navShow').find('input:checked').val();
    var margin = $('#navMargin').val();

    if (navShow === 'false') {
        var screenNum = 0;
        var property = [];
        var slotGroupId = '请选择组';
        desktopData.screens.forEach(function(elem) {
            if (elem.blocks.length > 0) {
                screenNum++;
                property = myData.slots = elem.blocks;
                slotGroupId = elem.slotGroupId;
            }
        });
        if (screenNum > 1) {
            alert('屏的数量大于1，不能去除导航');
            return;
        }
        desktopData.screens.length = 1;
        desktopData.nav = null;
        $('#navWarp').html('');
        $('#navDataWarp').html('');
        $('#screenWarp ul').html('<li></li>');
        $('#screenDataWarp ul').html('<li></li>');

        var idx = 0;
        var len = property.length;
        var $screenWarp = $('#screenWarp ul');
        var $screenDataWarp = $('#screenDataWarp ul');
        var $li = $('<li></li>');
        var $liData = $('<li></li>');
        for (var i = 0; i < len; i++) { //创建块
            var blockId = 0;
            if (property[i].slotId) {
                blockId = property[i].slotId;
            } else {
                blockId = createBlockId(idx + 1, i + 1);
            }
            $li.append(createBlock(property[i], false, blockId));
            $liData.append(createBlock(property[i], true, blockId, true, true));
        }

        $screenWarp.find('li:eq(' + idx + ')').replaceWith($li);
        $screenDataWarp.find('li:eq(' + idx + ')').replaceWith($liData);
        desktopData.screens[idx].blocks = property;
        desktopData.screens[idx].slotGroupId = slotGroupId;
        $('#screenWarp ul li').hide().eq(idx).show(); //隐藏其他屏，显示新增屏
        $('#screenDataWarp ul li').hide().eq(idx).show();

        var $screenSlots = $('#screenSlots');
        $screenSlots.text(len);
        $('#screenTitle').text('(1/1)');

        $('#navModal').modal('hide');
    } else {
        if (navId === '请选择导航' && !desktopData.nav) {
            alert('请选择导航');
            return;
        }
        if (margin == ' ' || !margin) {
            alert('请输入间隔');
            return;
        }

        if(isNaN(Number(margin))){
            alert('间隔只能为数字');
            return;
        }

        var navData = $("#navInfo").data("nav");
        if (!navData && desktopData.nav) {
            navData = {};
            navData.extra = desktopData.nav.extraData;
        }
        if (desktopData.nav) {
            desktopData.nav = {
                "isShowIndicator": isShowIndicator,
                "style": style,
                "x": desktopData.nav.x,
                "y": desktopData.nav.y,
                "interval": margin,
                "extraData": navData.extra
            };
        } else {
            desktopData.nav = {
                "isShowIndicator": isShowIndicator,
                "style": style,
                "x": navData.x,
                "y": navData.y,
                "interval": margin,
                "extraData": navData.extra
            };
        }

        createNav2(desktopData.nav);
        $('#navModal').modal('hide');
    }
});

//附件栏提交事件
$('#subAttachment').on('click', function() {
    var attachmentId = $('#attachmentSelect').val();
    var isShowIndicator = $('#attachmentTitle').find('input:checked').val();
    var style = $('#attachmentStyle').val();
    var attachmentShow = $('#attachmentShow').find('input:checked').val();
    var margin = $('#attachmentMargin').val();
    var customIdx = $('#attachmentModal .custom-index input');

    if (attachmentShow === 'false') {
        $('#attachmentWarp').html('');
        desktopData.attachment = null;
        $('#attachmentModal').modal('hide');
    } else {
        if (attachmentId === '请选择附件栏' && !desktopData.attachment) {
            alert('请选择附件栏');
            return;
        }
        if (margin == ' ' || !margin) {
            alert('请输入间隔');
            return;
        }

        if(isNaN(Number(margin))){
            alert('间隔只能为数字');
            return;
        }

        var data = $("#attachmentInfo").data('attachment');
        if (desktopData.attachment && !data) {
            data = {};
            data.extra = desktopData.attachment.extraData;
        }
        for(var i = 0, len = customIdx.length; i < len; i++){
            var $customIdx = $(customIdx[i]);
            var val = $customIdx.val();
            if (val == ' ' || !val) {
                alert('请设置控件'+ (i+1) +'的ID');
                return;
            }
            if (/\D/.test(val)) {
                alert('ID只能为数字');
                return;
            }
            if(!data.extra){
                data.extra = data.extraData;
            }
            data.extra[i].index = val;
        }
        if (desktopData.attachment) {
            desktopData.attachment = {
                "name": data.name || desktopData.attachment.name,
                "isShowIndicator": isShowIndicator,
                "style": style,
                "x": desktopData.attachment.x,
                "y": desktopData.attachment.y,
                "interval": margin,
                "extraData": data.extra
            };
        } else {
            desktopData.attachment = {
                "name": data.name || desktopData.attachment.name,
                "isShowIndicator": isShowIndicator,
                "style": style,
                "x": data.x,
                "y": data.y,
                "interval": margin,
                "extraData": data.extra
            };
        }

        createAttachment(desktopData.attachment);

        $('#attachmentModal').modal('hide');
    }
});

//快捷入口提交事件
$('#subQuick').on('click', function() {
    var quickId = $('#quickSelect').val();
    var isShowIndicator = $('#quickTitle').find('input:checked').val();
    var style = $('#quickStyle').val();
    var quickShow = $('#quickShow').find('input:checked').val();
    var customIdx = $('#quickModal .custom-index input');

    if (quickShow === 'false') {
        $('#quickWarp').html('');
        desktopData.quickEntry = null;
        $('#quickModal').modal('hide');
    } else {
        if (quickId === '请选择快捷入口' && !desktopData.quickEntry) {
            alert('请选择快捷入口');
            return;
        }

        var data = $("#quickInfo").data('quick');
        if (desktopData.quickEntry && !data) {
            data = {};
            data.extra = desktopData.quickEntry.extraData;
        }
        data.extra = data.extra || data.extraData;
        for(var i = 0, len = customIdx.length; i < len; i++){
            var $customIdx = $(customIdx[i]);
            var val = $customIdx.val();
            if (val == ' ' || !val) {
                alert('请控件设置'+ (i+1) +'的ID');
                return;
            }
            if (/\D/.test(val)) {
                alert('ID只能为数字');
                return;
            }
            data.extra[i].index = val;
        }

        desktopData.quickEntry = {
            "name": data.name || desktopData.quickEntry.name,
            "isShowIndicator": isShowIndicator,
            "style": style,
            "extraData": data.extra
        };

        createQuick(desktopData.quickEntry);

        $('#quickModal').modal('hide');
    }
});

//两态快捷入口提交事件
$('#subTwo').on('click', function() {
    var twoId = $('#twoSelect').val();
    var isShowIndicator = $('#twoTitle').find('input:checked').val();
    var style = $('#twoStyle').val();
    var twoShow = $('#twoShow').find('input:checked').val();
    var customIdx = $('#twoModal .custom-index input');

    if (twoShow === 'false') {
        $('#twoWarp').html('');
        desktopData.quickEntryTwoState = null;
        $('#twoModal').modal('hide');
    } else {
        if (twoId === '请选择两态快捷入口' && !desktopData.quickEntryTwoState) {
            alert('请选择两态快捷入口');
            return;
        }
        var data = $("#twoInfo").data('two');
        if (!data && desktopData.quickEntryTwoState) {
            data = {};
            data.extra = desktopData.quickEntryTwoState.extraData;
        }

        data.extra = ascSort(data.extra || data.extraData, 'itemX');

        for(var i = 0, len = customIdx.length; i < len; i++){
            var $customIdx = $(customIdx[i]);
            var val = $customIdx.val();
            if (val == ' ' || !val) {
                alert('请控件设置'+ (i+1) +'的ID');
                return;
            }
            if (/\D/.test(val)) {
                alert('ID只能为数字');
                return;
            }
            data.extra[i].index = val;
        }

        desktopData.quickEntryTwoState = {
            "name": data.name || desktopData.quickEntryTwoState.name,
            "isShowIndicator": isShowIndicator,
            "style": style,
            "x": "0",
            "y": "0",
            "extraData": data.extra
        };

        createTwo(desktopData.quickEntryTwoState);

        $('#twoModal').modal('hide');
    }
});

//三态快捷入口提交事件
$('#subThree').on('click', function() {
    var threeId = $('#threeSelect').val();
    var isShowIndicator = $('#threeTitle').find('input:checked').val();
    var style = $('#threeStyle').val();
    var threeShow = $('#threeShow').find('input:checked').val();
    var customIdx = $('#threeModal .custom-index input');

    if (threeShow === 'false') {
        $('#threeWarp').html('');
        desktopData.quickEntryThreeState = null;
        $('#threeModal').modal('hide');
    } else {
        if (threeId === '请选择三态快捷入口' && !desktopData.quickEntryThreeState) {
            alert('请选择三态快捷入口');
            return;
        }
        var data = $("#threeInfo").data('three');
        if (!data && desktopData.quickEntryThreeState) {
            data = {};
            data.extra = desktopData.quickEntryThreeState.extraData;
        }

        data.extra = ascSort(data.extra || data.extraData, 'itemX');

        for(var i = 0, len = customIdx.length; i < len; i++){
            var $customIdx = $(customIdx[i]);
            var val = $customIdx.val();
            if (val == ' ' || !val) {
                alert('请控件设置'+ (i+1) +'的ID');
                return;
            }
            if (/\D/.test(val)) {
                alert('ID只能为数字');
                return;
            }
            data.extra[i].index = val;
        }


        desktopData.quickEntryThreeState = {
            "name": data.name || desktopData.quickEntryThreeState.name,
            "isShowIndicator": isShowIndicator,
            "style": style,
            "x": "0",
            "y": "0",
            "extraData": data.extra
        };

        createThree(desktopData.quickEntryThreeState);

        $('#threeModal').modal('hide');
    }
});

//快捷入口组提交事件
$('#subQEG').on('click', function() {
    var QEGId = $('#QEGSelect').val();
    var QEGShow = $('#QEGShow').find('input:checked').val();
    var customIdx = $('#quickEntryGroupModal .custom-index input');
    if (QEGShow === 'false') {
        $('#QEGWarp').html('');
        desktopData.quickEntryGroup = null;
        $('#quickEntryGroupWarp').html('');
        $('#quickEntryGroupModal').modal('hide');
    } else {
        if (QEGId === '请选择快捷入口组' && !desktopData.quickEntryGroup) {
            alert('请选择快捷入口组');
            return;
        }
        var data = $("#QEGInfo").data('QEG');
        if (!data && desktopData.quickEntryGroup) {
            data = {};
            data.mList = desktopData.quickEntryGroup.mList;
        }
        if (data[0]) {
            var subData = data[0].mList;
        }else{
            var subData = data.mList;
        }
        for (var k = 0; k < customIdx.length;) {
            for (var i = 0; i < subData.length; i++) {
                for (var j = 0; j < subData[i].extra.length; j++) {
                    subData[i].extra[j].index = $(customIdx[k]).val();
                    k++;
                }
            }
        }

        if (data[0]) {
            data = data[0];
        }
        data.mList = subData;

        desktopData.quickEntryGroup = {
            "name": data.name || desktopData.quickEntryGroup.name,
            "mList": data.mList
        };

        createQuickEntryGroup(desktopData.quickEntryGroup);

        $('#quickEntryGroupModal').modal('hide');
    }
});

//壁纸提交事件
$('#subWallpaper').on('click', function(){
    var fileObj = document.getElementById("wallpaperHide").files[0];
    var fileVal = $("#wallpaperShow").val();
    var paperShow = $('#paperShow input:checked').val();
    var data = new FormData();
    var uploadFile = false;

    if(paperShow == 'false'){
        $('.screenWarp').css({
            "background-image": 'none',
            "background-size": "100% 100%"
        });
        desktopData.image = null;
        $('#wallpaperModal').modal('hide');
    }else{
        if (fileVal != ' ' && fileVal.indexOf('http') == -1 && fileVal) {
            data.append("image", fileObj);
            uploadFile = true;
        }

        if (fileVal.indexOf('http') != -1) {
            $('.screenWarp').css({
                "background-image": 'url('+ fileVal +')',
                "background-size": "100% 100%"
            });
            desktopData.image = fileVal;
        }
        if(uploadFile){
            data.append('additional', 'wallpaper');
            AjaxFile('/desktop/updataImage', data, function(imgData) {
                $('.screenWarp').css({
                    "background-image": 'url('+ imgData.image +')',
                    "background-size": "100% 100%"
                });
                desktopData.image = imgData.image;
                $('#wallpaperModal').modal('hide');
            });
        }else{
            $('#wallpaperModal').modal('hide');
        }
    }
});

//风格变化事件
$('#styleType').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    $this.parent().siblings('.coverflow').hide().filter('.'+val).show();
    $('#animationType').parent().show();
});
//屏风格变化事件
$('#screenStyleType').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    $this.parent().siblings().hide().filter('.'+val).show();
});
//放大变化事件//jatai2016-03-10
$('#animationType').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    if(val =='enlarge'||val == 'enlarge_rotate'){
        $this.parent().siblings('.animationType').show();
        if(desktopData.enlargeVal == null){
            $('#enlargeVal').val('1.00');
        }else{
            $('#enlargeVal').val(desktopData.enlargeVal);
        }
        //$('#enlargeVal').val(desktopData.enlargeVal)
    }else{
        $(this).parent().siblings('.animationType').hide();
        $('#enlargeVal').val('1.00');
    }
});

//风格信息提交事件
$('#subStyle').on('click', function(){
    var styleType = $('#styleType').val();
    var itemStyle = $('#itemStyle').val();
    var animationType = $('#animationType').val();
    var displayQuantity = $('#displayQuantity').val();
    var slotAngle = $('#slotAngle').val();
    var isCircle = $('#isCircle input:checked').val();
    var areaX = $('#areaX').val();
    var areaY = $('#areaY').val();
    var areaW = $('#areaW').val();
    var areaH = $('#areaH').val();
    var amType = $('#animationType').val();
    var enlargeVal = $('#enlargeVal').val();

    if(styleType === 'default'){
        desktopData.style = null;
    }else if(styleType === 'coverflow'){
        if(displayQuantity == ' ' || !displayQuantity){
            alert('请输入显示数量！');
            return false;
        }
        if(/\D/.test(displayQuantity)){
            alert('显示数量只能为数字！');
            return false;
        }
        if(slotAngle == ' ' || !slotAngle){
            alert('请输入偏转角度！');
            return false;
        }
        if(/\D/.test(slotAngle)){
            alert('偏转角度只能为数字！');
            return false;
        }
        if(areaX == ' ' || !areaX){
            alert('请输入X坐标');
            return false;
        }
        if(/\D/.test(areaX)){
            alert('X坐标只能为数字！');
            return false;
        }
        if(areaY == ' ' || !areaY){
            alert('请输入Y坐标！');
            return false;
        }
        if(/\D/.test(areaY)){
            alert('Y坐标只能为数字！');
            return false;
        }
        if(areaW == ' ' || !areaW){
            alert('请输入宽！');
            return false;
        }
        if(/\D/.test(areaW)){
            alert('宽只能为数字！');
            return false;
        }
        if(areaH == ' ' || !areaH){
            alert('请输入高！');
            return false;
        }
        if(/\D/.test(areaH)){
            alert('高只能为数字！');
            return false;
        }

        desktopData.style = {
            "name":"coverflow",
            "displayQuantity": displayQuantity,
            "slotAngle": slotAngle,
            "isCircle": isCircle,
            "x": areaX,
            "y": areaY,
            "width": areaW,
            "height": areaH
        };
    }

    //jatai2016-03-10
    if(amType=='enlarge'||amType=='enlarge_rotate'){
        if(!/^[1-9].\d*$/.test(enlargeVal) && !/^[1-9]\d*$/.test(enlargeVal)){
            alert('请输入大于等于1的数！');
            return false;
        }
        enlargeVal = enlargeVal;
    }else{
        enlargeVal = null;
    }
    desktopData.animation = animationType;
    desktopData.enlargeVal=enlargeVal;

    $('#styleModal').modal('hide');
});
//屏风格信息提交事件
$('#subScreenStyle').on('click', function(){
    var i=$('#navWarp a[class=active]').index();
    if(i==-1){i=0;}
    var screenStyleType = $('#screenStyleType').val();
    var screenDisplayQuantity = $('#screenDisplayQuantity').val();
    var screenSlotAngle = $('#screenSlotAngle').val();
    var screenIsCircle = $('#screenIsCircle input:checked').val();
    var screenAreaX = $('#screenAreaX').val();
    var screenAreaY = $('#screenAreaY').val();
    var screenAreaW = $('#screenAreaW').val();
    var screenAreaH = $('#screenAreaH').val();


    if(screenStyleType === 'default'){
        desktopData.screens[i].itemStyle = null;
    }else if(screenStyleType==='coverflow'){
        if(screenDisplayQuantity == ' ' || !screenDisplayQuantity){
            alert('请输入显示数量！');
            return false;
        }
        if(/\D/.test(screenDisplayQuantity)){
            alert('显示数量只能为数字！');
            return false;
        }
        if(screenSlotAngle == ' ' || !screenSlotAngle){
            alert('请输入偏转角度！');
            return false;
        }
        if(/\D/.test(screenSlotAngle)){
            alert('偏转角度只能为数字！');
            return false;
        }
        if(screenAreaX == ' ' || !screenAreaX){
            alert('请输入X坐标');
            return false;
        }
        if(/\D/.test(screenAreaX)){
            alert('X坐标只能为数字！');
            return false;
        }
        if(screenAreaY == ' ' || !screenAreaY){
            alert('请输入Y坐标！');
            return false;
        }
        if(/\D/.test(screenAreaY)){
            alert('Y坐标只能为数字！');
            return false;
        }
        if(screenAreaW == ' ' || !screenAreaW){
            alert('请输入宽！');
            return false;
        }
        if(/\D/.test(screenAreaW)){
            alert('宽只能为数字！');
            return false;
        }
        if(screenAreaH == ' ' || !screenAreaH){
            alert('请输入高！');
            return false;
        }
        if(/\D/.test(screenAreaH)){
            alert('高只能为数字！');
            return false;
        }

        desktopData.screens[i].itemStyle = {
            "name":screenStyleType,
            "displayQuantity": screenDisplayQuantity,
            "slotAngle": screenSlotAngle,
            "isCircle": screenIsCircle,
            "x": screenAreaX,
            "y": screenAreaY,
            "width": screenAreaW,
            "height": screenAreaH
        };

    }


    $('#screenStyleModal').modal('hide');
});
//配置信息提交事件
$('#subConfigure').on('click', function(){
    var isDisposeNavLeft = $('#isDisposeNavLeft input:checked').val();
    var isDisposeNavRight = $('#isDisposeNavRight input:checked').val();
    var isCreateNavBottomLine = $('#isCreateNavBottomLine input:checked').val();
    var isSkipYunOSCheck = $('#isSkipYunOSCheck input:checked').val();
    var isSkipYunOSReport = $('#isSkipYunOSReport input:checked').val();
    var isAllowReplaceWallpaper = $('#isAllowReplaceWallpaper input:checked').val();
    var isAllowSlotEmpty = $('#isAllowSlotEmpty input:checked').val();
    var slotCornerRadius = $('#slotCornerRadius').val() || '0';
    var focusTheta = $('#focusTheta').val() || '45';
    var firstSlotId = $('#firstSlotId').val();
    var focusStyle = $('#focusStyle').val();
    var setBlur = $('#setBlur').val();

    var fileObj = document.getElementById("focusImageHide").files[0];
    var fileVal = $("#focusImageShow").val();
    var data = new FormData();
    var uploadFile = false;

    if(/\D/.test(slotCornerRadius)){
        alert('圆角值只能为数字！');
        return false;
    }

    if(/\D/.test(focusTheta)){
        alert('焦点角度只能为数字！');
        return false;
    }

    if(firstSlotId == '请选择ID' || !firstSlotId){
        alert('请选择ID');
        return false;
    }

    if (fileVal != ' ' && fileVal.indexOf('http') == -1 && fileVal) {
        data.append("image", fileObj);
        uploadFile = true;
    }
    if(uploadFile){
        data.append('additional', 'slot');
        AjaxFile('/desktop/updataImage', data, function(imgData) {
            desktopData.appConfig = {
                "isDisposeNavLeft": isDisposeNavLeft,
                "isDisposeNavRight": isDisposeNavRight,
                "isCreateNavBottomLine": isCreateNavBottomLine,
                "slotCornerRadius": slotCornerRadius,
                "focusTheta": focusTheta,
                "isSkipYunOSReport": isSkipYunOSReport,
                "isAllowReplaceWallpaper": isAllowReplaceWallpaper,
                "isAllowSlotEmpty": isAllowSlotEmpty,
                "isSkipYunOSCheck": isSkipYunOSCheck,
                "firstSlotId": firstSlotId,
                "focusStyle": focusStyle,
                "focusImage": imgData.image,
                "isBlurEnabled": setBlur
            };

            $('.screen-block').css('border-radius', slotCornerRadius + 'px');
            $('.block-data').css('border-radius', slotCornerRadius + 'px');
            $('#configureModal').modal('hide');
        });
    }else{
        desktopData.appConfig = {
            "isDisposeNavLeft": isDisposeNavLeft,
            "isDisposeNavRight": isDisposeNavRight,
            "isCreateNavBottomLine": isCreateNavBottomLine,
            "slotCornerRadius": slotCornerRadius,
            "focusTheta": focusTheta,
            "isSkipYunOSReport": isSkipYunOSReport,
            "isAllowReplaceWallpaper": isAllowReplaceWallpaper,
            "isAllowSlotEmpty": isAllowSlotEmpty,
            "isSkipYunOSCheck": isSkipYunOSCheck,
            "firstSlotId": firstSlotId,
            "focusStyle": focusStyle,
            "focusImage": fileVal,
            "isBlurEnabled": setBlur
        };

        $('.screen-block').css('border-radius', slotCornerRadius + 'px');
        $('.block-data').css('border-radius', slotCornerRadius + 'px');
        $('#configureModal').modal('hide');
    }
});

//底部快捷栏保存事件
$('#saveQuickList').on('click', function(){
    var type = $('#quickListShow input:checked').val();
    if(type === 'false'){
        alert('请设置底部快捷栏');
        return false;
    }
    var name = prompt("请输入模板名称", "");
    if(name === null){
        return false;
    }

    if(!name || name == ' '){
        alert('请输入模板名称');
        return false;
    }

    var quickLists = $('.quickList');
    var len = quickLists.length;
    desktopData.quickList = [];
    for(var i = 0; i < len; i++){
        var $quickList = $(quickLists[i]);
        var appName = $quickList.find('.quick-appName').val();
        var appNameData = $quickList.find('.quick-appName').data('_' + appName);

        if(!appNameData){
            alert('请选择位置'+ (i+1) + '的应用名称');
            return false;
        }

        var pkgName = appNameData.pkgName;
        var appIcon = appNameData.icon;
        var versionCode = $quickList.find('.quick-version').val();
        var apkUrl = $quickList.find('.quick-url').val();
        var appTitle = $quickList.find('.quick-title').val() || '';
        var data = {};

        if(!appName || !versionCode || !apkUrl){
            alert('位置'+ (i+1) + '的信息不完整');
            return false;
        }
        if(appIcon === ''){
            alert('位置'+ (i+1) + '的应用没有图标，请在第三方应用上传');
            return false;
        }


        data.index = i;
        data.title = appTitle;
        data.appName = appName;
        data.pkgName = pkgName;
        data.apkUrl = apkUrl;
        data.versionCode = versionCode;
        data.appIcon = appIcon;

        desktopData.quickList.push(data);

    }

    var subUnder = function(){
        data = {
            "name": name,
            "extra": desktopData.quickList
        };
        AjaxPost('/desktop/addQuickLists', data, function(){
            alert('保存成功');
            AjaxGet('/desktop/quickLists', function(data){
                selectQuickList(data, name);
            });
            return false;
        });
    };

    subUnder();
});

//底部快捷栏提交事件
$('#subQuickList').on('click', function(){
    var quickListShow = $('#quickListShow').find('input:checked').val();
    if (quickListShow === 'false') {
        desktopData.quickList = null;
        $('#quickListModal').modal('hide');
    } else {
        var quickLists = $('.quickList');
        var len = quickLists.length;
        desktopData.quickList = [];
        for(var i = 0; i < len; i++){
            var $quickList = $(quickLists[i]);
            var appName = $quickList.find('.quick-appName').val();
            var appNameData = $quickList.find('.quick-appName').data('_' + appName);

            if(!appNameData){
                alert('请选择位置'+ (i+1) + '的应用名称');
                return false;
            }

            var pkgName = appNameData.pkgName;
            var appIcon = appNameData.icon;
            var versionCode = $quickList.find('.quick-version').val();
            var apkUrl = $quickList.find('.quick-url').val();
            var appTitle = $quickList.find('.quick-title').val() || '';
            var data = {};

            if(!appName || !versionCode || !apkUrl){
                alert('位置'+ (i+1) + '的信息不完整');
                return false;
            }
            if(appIcon === ''){
                alert('位置'+ (i+1) + '的应用没有图标，请在第三方应用上传');
                return false;
            }


            data.index = i;
            data.title = appTitle;
            data.appName = appName;
            data.pkgName = pkgName;
            data.apkUrl = apkUrl;
            data.versionCode = versionCode;
            data.appIcon = appIcon;

            desktopData.quickList.push(data);

        }

        $('#quickListModal').modal('hide');
    }
});

$('#quickListItem').on('sortstop', function( event, ui ) {
    var labels = $('#quickListItem label');
    for(var i = labels.length; i--;){
        $(labels[i]).text('位置'+ (i+1) +'：');
    }
});

$('#addQuickList').on('click', function(){
    var con = getQuickListHtml();
    $('#quickListItem').append(con).sortable( "refresh" );
    var len = $('.delQuickList').length;
    selectListAppName($('.quick-appName:eq('+ (len-1) +')'));
});

$('#quickListModal').on('click', '.delQuickList', function(){
    $(this).parent().remove();
    var quickList = $('.delQuickList');
    for(var i = 0, len = quickList.length; i < len; i++){
        $(quickList[i]).siblings('label').text('位置' + (i + 1) + '：');
    }
});

//生成快捷栏控件
function getQuickListHtml(){
    var len = $('.delQuickList').length;
    return  '<div class="form-group quickList" style="position: relative;">'+
                '<label for="">位置' + (len + 1) + ':</label>'+
                '<input type="text" class="form-control quick-title" placeholder="选填" style="width: 21%;position: relative;top: 1px;">&emsp;'+
                '<select class="chosen-select form-control quick-appName" data-placeholder="请选择应用名称" style="width: 21%;">'+
                    '<option value="请选择应用名称">请选择应用名称</option>'+
                '</select>&emsp;'+
                '<select class="chosen-select form-control quick-version" data-placeholder="请选择应用版本" style="width: 21%;position: relative;top: 2px;">'+
                    '<option value="请选择应用版本">请选择应用版本</option>'+
                '</select>&emsp;'+
                '<select class="chosen-select form-control quick-url" data-placeholder="请选择应用路径" style="width: 21%;position: relative;top: 2px;">'+
                    '<option value="请选择应用路径">请选择应用路径</option>'+
                '</select>&emsp;'+
                '<button type="button" class="close delQuickList" style="position: absolute;top: 9px;right: 0;font-size: 20px;border-radius: 50%;border: 1px solid #0E0D0D;width: 22px;">×</button>'+
            '</div>';
}

function selectListAppName($select, bindApp){
    var arr = myData.appNameSelect || [];
    var con = '<option value="请选择应用名称">请选择应用名称</option>';
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].appName + '">' + arr[i].appName + '</option>';
        $select.data('_' + arr[i].appName, {
            "icon": arr[i].icon,
            "pkgName": arr[i].pkgName
        });
    }
    if(bindApp && bindApp.appName){
        $select.html(con).val(bindApp.appName).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "21%"
        }).trigger('change', bindApp);
        $select.siblings('.quick-title').val(bindApp.title);
    }else{
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "21%"
        }).trigger('change');
    }
}

function selectListVersion($select, data, bindApp){
    var arr = data.extra;
    var con = '<option value="请选择应用版本">请选择应用版本</option>';
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].versionCode + '">' + arr[i].versionCode + '</option>';
        $select.data('_' + arr[i].versionCode, {
            "path": arr[i].path,
            "path3rd": arr[i].path3rd
        });
    }
    if(bindApp && bindApp.apkUrl){
        $select.html(con).val(bindApp.versionCode).trigger('change', bindApp);
    }else{
        $select.html(con).trigger('change');
    }
}

function selectListUrl($select, data, bindApp){
    var con = '<option value="请选择应用路径" title="请选择应用路径">请选择应用路径</option>';
    var url = '';
    if(data.path3rd){
        if(data.path3rd.length > 42){
            url = data.path3rd.substr(0, 42) + '...';
        }else{
            url = data.path3rd;
        }
        con += '<option value="'+ data.path3rd +'" title="'+ data.path3rd +'">'+ url +'</option>';
    }
    if(data.path){
        if(data.path.length > 42){
            url = data.path.substr(0, 42) + '...';
        }else{
            url = data.path;
        }
        con += '<option value="'+ data.path +'" title="'+ data.path +'">'+ url +'</option>';
    }
    if(bindApp && bindApp.apkUrl){
        $select.html(con).val(bindApp.apkUrl).trigger('change', bindApp);
    }else{
        $select.html(con).trigger('change');
    }
}

$('#quickListModal').on('change', '.quick-appName', function(e, bindApp){
    var $this = $(this);
    var val = $this.val();
    if(val && val !== '请选择应用名称'){
        AjaxGet('/App/apkVersionLists?appName=' + val, function(data){
            selectListVersion($this.siblings('.quick-version'), data, bindApp);
        });
        return false;
    }
});

$('#quickListModal').on('change', '.quick-version', function(e, bindApp){
    var $this = $(this);
    var val = $this.val();
    var $url = $this.siblings('.quick-url');
    if(val === '请选择应用版本'){
        $url.val('请选择应用路径').trigger('change');
        return false;
    }
    selectListUrl($url, $(this).data('_' + val), bindApp);
});

$('#quickListModal').on('change', '.quick-url', function(e, bindApp){
    this.title = this.options[this.selectedIndex].value;
});

//创建附件栏
function createAttachment(data) {
    var con = '';
    var len = data.extraData.length;
    for (var i = 0; i < len; i++) {
        con += createIconsAttachment(data.extraData[i], data.interval, i);
    }
    if (len < 1) {
        $('#attachmentWarp').html('');
    } else {
        $('#attachmentWarp').html('').append(con).css({
            "left": data.x + 'px',
            "top": data.y + 'px'
        });
        if (getScreenIdx() === 0) {
            $('#attachmentWarp').show();
        } else {
            $('#attachmentWarp').hide();
        }
    }
}

//创建快捷入口
function createQuick(data) {
    var con = [];
    var len = data.extraData.length;
    for (var i = 0; i < len; i++) {
        var $con = $(createIcons(data.extraData[i], 0, i));
        $con.css({
            "position": "absolute",
            "left": Number(data.extraData[i].itemX),
            "top": Number(data.extraData[i].itemY)
        });
        con.push($con);
    }
    if (len < 1) {
        $('#quickWarp').html('');
    } else {
        $('#quickWarp').html('').append(con);
        $('#quickWarp a').draggable({disabled: true});
        $('#quickWarp').show();
    }
}

//创建两态快捷入口
function createTwo(data) {
    var con = [];
    var len = data.extraData.length;
    for (var i = 0; i < len; i++) {
        var $con = $(createIconsTwo(data.extraData[i], i));
        $con.css({
            "position": "absolute",
            "left": Number(data.extraData[i].x),
            "top": Number(data.extraData[i].y)
        });
        con.push($con);
    }
    if (len < 1) {
        $('#twoWarp').html('');
    } else {
        $('#twoWarp').html('').append(con);
        $('#twoWarp a').draggable({disabled: true});
        $('#twoWarp').show();
    }
}

//创建三态快捷入口
function createThree(data) {
    var con = [];
    var len = data.extraData.length;
    for (var i = 0; i < len; i++) {
        var $con = $(createIconsThree(data.extraData[i], i));
        $con.css({
            "position": "absolute",
            "left": Number(data.extraData[i].x),
            "top": Number(data.extraData[i].y)
        });
        con.push($con);
    }
    if (len < 1) {
        $('#threeWarp').html('');
    } else {
        $('#threeWarp').html('').append(con);
        $('#threeWarp a').draggable({disabled: true});
        $('#threeWarp').show();
    }
}

function createQuickEntryGroup(data) { //新建快捷入口组
        var arr = data.mList;
        var len = arr ? arr.length : 0;
        var con = '';
        myData.QEGPics = [];
        $('#quickEntryGroupWarp').html('');
        for (var i = 0; i < len; i++) { //创建icon
            //判断展现方式
            myData.QEGPics[i] = [];
            if (arr[i].layout === 'vertical') {
                for (var j = 0; j < arr[i].extra.length; j++) {
                    myData.QEGPics[i].push({'forcusPath': arr[i].extra[j].forcusPath, 'normalPath': arr[i].extra[j].normalPath});
                    con = con + createQEGIcons(arr[i].extra[j], arr[i].distance, j);
                }
            }else{
                for (var j = 0; j < arr[i].extra.length; j++) {
                    myData.QEGPics[i].push({'forcusPath': arr[i].extra[j].forcusPath, 'normalPath': arr[i].extra[j].normalPath});
                    con += createIcons(arr[i].extra[j], arr[i].distance, j);
                }
            }
            //判断伸展方向
            var obj = $();
            obj = $('<div id="' + i + '" class="screen-nav" style="left: ' + arr[i].x + 'px;' + 'top: ' + arr[i].y + 'px;' + 'width: auto;display: inline-block;position: absolute;"></div>');
            obj.append(con);

            if (arr[i].layout === 'vertical') {
                obj.children().css('margin-bottom', arr[i].distance + 'px');
            }else{
                obj.children().css('margin-right', arr[i].distance + 'px');
            }
            $('#quickEntryGroupWarp').append(obj);
            con = '';
        }
}

//LOGO提交事件
$('#subLogo').on('click', function() {
    var isShowIndicator = $('#logoTitle').find('input:checked').val();
    var style = $('#logoStyle').val();
    var logoShow = $('#logoShow').find('input:checked').val();
    var intervalTime = $('#logoTime').val();
    var data = new FormData();
    var logoData = {};
    var logoLists = [];

    if (logoShow === 'false') {
        $('#logoWarp').html('');
        $('#logoModal').modal('hide');
        desktopData.logo = null;
    } else {
        if (intervalTime == ' ' || !intervalTime) {
            alert('请输入展示间隔（秒）');
            return;
        }
        if (/\D/.test(intervalTime)) {
            alert('展示间隔（秒）只能为数字');
            return;
        }

        var fileObjs = $('#logoModal .fileHide');
        var fileVals = $('#logoModal .fileShow');
        var uploadFile = false;

        for(var i = 0, len = fileObjs.length; i < len; i++){
            var fileObj = fileObjs[i].files[0];
            var fileVal = fileVals[i].value;
            if (fileVal == ' ' || !fileVal) {
                alert('请选择要上传的第'+ (i + 1) +'个LOGO');
                return false;
            }
            if (fileVal != ' ' && fileVal.indexOf('http') == -1 && fileVal) {
                data.append("logoFile" + (i + 1), fileObj);
                uploadFile = true;
            }
            logoLists.push('');
            if (fileVal.indexOf('http') != -1) {
                logoLists[i] = fileVal;
            }
        }

        var uploadLogo = function (){
            if (desktopData.logo) {
                desktopData.logo = {
                    "isShowIndicator": isShowIndicator,
                    "style": style,
                    "intervalTime": intervalTime,
                    "x": desktopData.logo.x,
                    "y": desktopData.logo.y,
                    "logoLists": logoLists
                };
            } else {
                desktopData.logo = {
                    "isShowIndicator": isShowIndicator,
                    "style": style,
                    "intervalTime": intervalTime,
                    "x": "10",
                    "y": "10",
                    "logoLists": logoLists
                };
            }
            $('#logoWarp').html('<img src="' + logoLists[0] + '" >').css({
                "left": desktopData.logo.x + 'px',
                "top": desktopData.logo.y + 'px'
            });
            $('#logoModal').modal('hide');
        };

        if(uploadFile){
            AjaxFile('/desktop/updataImage', data, function(imgData) {
                for(var p in imgData){
                    if(p !== 'result'){
                        var idx = parseInt(p.replace('logoFile', ''));
                        logoLists[idx - 1] = imgData[p];
                    }
                }
                uploadLogo();
            });
        }else{
            uploadLogo();
        }
    }
});

//时间提交事件
$('#subTimer').on('click', function() {
    var timeFormat = $('#timerFormat').val();
    var isShowIndicator = $('#timerTitle').find('input:checked').val();
    var style = $('#timerStyle').val();
    var timerShow = $('#timerShow').find('input:checked').val();

    if (timerShow === 'false') {
        $('#timerWarp').html('');
        desktopData.timebar = null;
    } else {
        if (timeFormat == ' ' || !timeFormat) {
            alert('请输入时间显示格式');
            return;
        }
        var x = 600;
        var y = 20;
        if (desktopData.timebar) {
            x = desktopData.timebar.x;
            y = desktopData.timebar.y;
        }

        desktopData.timebar = {
            "timeFormat": timeFormat,
            "isShowIndicator": isShowIndicator,
            "style": style,
            "x": x,
            "y": y
        };
        var timerShowStyle = getTimerShow(timeFormat);
        $('#timerWarp').html('<div class="handle-font" style="color: #fff;font-size: 18px;line-height: 40px;height: 40px;font-family: 微软雅黑;">'+ timerShowStyle +'</div>').css({
            "top": y,
            "left": x,
            "width": timerShowStyle.gblen() * 9 + 15
        });
    }

    $('#timerModal').modal('hide');
});

function getTimerShow(str){
    return str.replace(/yyyy/, "2015").replace(/MM/, "11").replace(/dd/, "20").replace(/EEEE/, "星期五").replace(/EEE/, "周五").replace(/HH/, "16").replace(/hh/, "04").replace(/mm/, "37").replace(/aa/, "下午");
}

//天气提交事件
$('#subWeather').on('click', function() {
    var isShowIndicator = $('#timerTitle').find('input:checked').val();
    var isShowCity = $('#weatherCity').find('input:checked').val();
    var isShowTemperature = $('#weatherTemperature').find('input:checked').val();
    var isShowDesc = $('#weatherDesc').find('input:checked').val();
    var isShowIcon = $('#weatherIcon').find('input:checked').val();
    var style = $('#weatherStyle').val();
    var weatherShow = $('#weatherShow').find('input:checked').val();

    if (weatherShow === 'false') {
        $('#weatherWarp').html('');
        desktopData.weather = null;
    } else {
        var con = '';
        var width = 24;
        if (isShowCity === 'true') {
            con += '广州 ';
            width += 41;
        }
        con += '今 ';
        if (isShowTemperature === 'true') {
            con += '25℃ - 34℃';
            width += 97;
        }
        var x = 800;
        var y = 20;
        if (desktopData.weather) {
            x = desktopData.weather.x;
            y = desktopData.weather.y;
        }

        $('#weatherWarp').html('<span class="handle-font" style="color: #fff;font-size: 18px;line-height: 40px;height: 40px;font-family: 微软雅黑;display: block;float:left;width: ' + width + 'px;">' + con + '</span>').css({
            "top": y,
            "left": x,
            "width": width + 50
        });

        if (isShowIcon === 'true') {
            $('#weatherWarp').append('<span class="handle-font" style="display: block;float: left;background-image: url(img/icon_weather.png);background-size: 40px 40px;width: 40px;height: 40px;"></span>');
        } else if (isShowIcon === 'false') {
            $('#weatherWarp').css('width', width).find('span:eq(1)').remove();
        }

        desktopData.weather = {
            "isShowIndicator": isShowIndicator,
            "isShowCity": isShowCity,
            "isShowTemperature": isShowTemperature,
            "isShowDesc": isShowDesc,
            "isShowIcon": isShowIcon,
            "style": style,
            "x": x,
            "y": y
        };
    }

    $('#weatherModal').modal('hide');
});

//时间天气提交时间
$('#subTimeWeather').on('click', function() {
    //新时间天气
    var timeWeatherX = 400;
    var timeWeatherY = 20;
    var timeWeatherStyle = $('#timeWeatherStyle').val();
    var timeWeatherWidth = "auto";
    var timeWeatherShow = $('#timeWeatherShow').find('input:checked').val();

    if (timeWeatherShow === 'false') {
        $('#timeWeatherWarp').html('');
        desktopData.timeWeather = null;
    } else {
        if(desktopData.timeWeather){
            timeWeatherX = desktopData.timeWeather.x;
            timeWeatherY = desktopData.timeWeather.y;
        }

        desktopData.timeWeather = {
            "x": timeWeatherX,
            "y": timeWeatherY,
            "style": timeWeatherStyle
        };

        $('#timeWeatherWarp').html('').css({
            "top": timeWeatherY,
            "left": timeWeatherX,
            "width": timeWeatherWidth
        });

        $('#timeWeatherWarp').append('<span class="handle-font" style="display: block;float: left;"><img src="img/timeWeather/' + timeWeatherStyle.toString() +'.png"></span>');
    }

    //时间
    var timeFormat = $('#timerFormat').val();
    var isShowIndicatorA = $('#timerTitle').find('input:checked').val();
    var styleA = $('#timerStyle').val();
    var timerShow = $('#timerShow').find('input:checked').val();

    if (timerShow === 'false') {
        $('#timerWarp').html('');
        desktopData.timebar = null;
    } else {
        if (timeFormat == ' ' || !timeFormat) {
            alert('请输入时间显示格式');
            return false;
        }
        var xA = 600;
        var yA = 20;
        if (desktopData.timebar) {
            xA = desktopData.timebar.x;
            yA = desktopData.timebar.y;
        }

        desktopData.timebar = {
            "timeFormat": timeFormat,
            "isShowIndicator": isShowIndicatorA,
            "style": styleA,
            "x": xA,
            "y": yA
        };
        var timerShowStyle = getTimerShow(timeFormat);
        $('#timerWarp').html('<div class="handle-font" style="color: #fff;font-size: 18px;line-height: 40px;height: 40px;font-family: 微软雅黑;">'+ timerShowStyle +'</div>').css({
            "top": yA,
            "left": xA,
            "width": timerShowStyle.gblen() * 9 + 15
        });
    }

    //天气
    var isShowIndicator = $('#timerTitle').find('input:checked').val();
    var isShowCity = $('#weatherCity').find('input:checked').val();
    var isShowTemperature = $('#weatherTemperature').find('input:checked').val();
    var isShowDesc = $('#weatherDesc').find('input:checked').val();
    var isShowIcon = $('#weatherIcon').find('input:checked').val();
    var style = $('#weatherStyle').val();
    var weatherShow = $('#weatherShow').find('input:checked').val();

    if (weatherShow === 'false') {
        $('#weatherWarp').html('');
        desktopData.weather = null;
    } else {
        var con = '';
        var width = 24;
        if (isShowCity === 'true') {
            con += '广州 ';
            width += 41;
        }
        con += '今 ';
        if (isShowTemperature === 'true') {
            con += '25℃ - 34℃';
            width += 97;
        }
        var x = 800;
        var y = 20;
        if (desktopData.weather) {
            x = desktopData.weather.x;
            y = desktopData.weather.y;
        }

        $('#weatherWarp').html('<span class="handle-font" style="color: #fff;font-size: 18px;line-height: 40px;height: 40px;font-family: 微软雅黑;display: block;float:left;width: ' + width + 'px;">' + con + '</span>').css({
            "top": y,
            "left": x,
            "width": width + 50
        });

        if (isShowIcon === 'true') {
            $('#weatherWarp').append('<span class="handle-font" style="display: block;float: left;background-image: url(img/icon_weather.png);background-size: 40px 40px;width: 40px;height: 40px;"></span>');
        } else if (isShowIcon === 'false') {
            $('#weatherWarp').css('width', width).find('span:eq(1)').remove();
        }

        desktopData.weather = {
            "isShowIndicator": isShowIndicator,
            "isShowCity": isShowCity,
            "isShowTemperature": isShowTemperature,
            "isShowDesc": isShowDesc,
            "isShowIcon": isShowIcon,
            "style": style,
            "x": x,
            "y": y
        };
    }

    $('#timeWeatherModal').modal('hide');
});

//SN提交事件
$('#subSN').on('click', function() {
    var isShowIndicator = $('#snTitle').find('input:checked').val();
    var style = $('#snStyle').val();
    var snShow = $('#snShow').find('input:checked').val();
    var prefixInfo = $('#snPrefix').val() || 'SN：';

    if (snShow === 'false') {
        $('#snWarp').html('');
        desktopData.sn = null;
    } else {
        var x = 600;
        var y = 20;
        if (desktopData.sn) {
            x = desktopData.sn.x;
            y = desktopData.sn.y;
        }

        desktopData.sn = {
            "prefixInfo": prefixInfo,
            "systemProperty": "",
            "ipmacroProperty": "SN",
            "isShowIndicator": isShowIndicator,
            "style": style,
            "x": x,
            "y": y
        };
        $('#snWarp').html('<div class="handle-font" style="color: #fff;font-size: 18px;line-height: 40px;height: 40px;font-family: 微软雅黑;">'+ prefixInfo +'1234567890</div>').css({
            "top": y,
            "left": x
        });
    }

    $('#snModal').modal('hide');
});

//等比例缩放提交事件
$('#subScale').on('click', function(){
    var scaleALl = $('#scaleAll input:checked').val();
    var width = $('#scaleWidth').val();
    var height = $('#scaleHeight').val();
    if(/\D/.test(width) || /\D/.test(height)){
        alert('宽高比例只能为数字！');
        return;
    }
    var selectBlocks = null;
    var i = 0;
    if(scaleALl === 'true'){
        var screenLists = $('#screenWarp li');
        for(i = screenLists.length; i--;){
            selectBlocks = $(screenLists[i]).find('.screen-block');
            updateScale(i, selectBlocks, width, height);
        }
    }else{
        selectBlocks = $('.selectBlock');
        updateScale(getScreenIdx(), selectBlocks, width, height);
    }
    myData.clearSelect = null;
    $('#scaleModal').modal('hide');
    return false;
});

//等比例缩放选中块
function updateScale(idx, selectBlocks, width, height){
    var len = selectBlocks.length,
        w = 0,
        h = 0,
        j = 0,
        position;
    for(var i = len; i--;){
        $selectBlock = $(selectBlocks[i]);
        j = $selectBlock.index();
        w = 0 | $selectBlock.width() * width / 100;
        h = 0 | $selectBlock.height() * height / 100;
        l = 0 | parseInt($selectBlock.css('left')) * width / 100;
        t = 0 | parseInt($selectBlock.css('top')) * height / 100;
        $selectBlock.css({
            "width": w,
            "height": h,
            "left": l,
            "top": t
        });
        desktopData.screens[idx].blocks[j].w = w + '';
        desktopData.screens[idx].blocks[j].h = h + '';
        desktopData.screens[idx].blocks[j].x = l + '';
        desktopData.screens[idx].blocks[j].y = t + '';
    }

    if(len === 1){
        var bg = selectBlocks.getBackgroundColor();
        myData.slot = {
            "x": parseInt(selectBlocks.css('left')),
            "y": parseInt(selectBlocks.css('top')),
            "w": selectBlocks.width(),
            "h": selectBlocks.height(),
            "bg": bg.rgb,
            "yh": selectBlocks.attr('data-yh'),
            "yw": selectBlocks.attr('data-yw')
        };
        refreshSlotPos(myData.slot);
    }
}

$('#scaleModal').on('click', '[data-dismiss="modal"]', function(){
    myData.clearSelect = null;
});

//保持纵横比宽高事件
$('#scaleModal').on('keyup', 'input[type="text"]', function(e){
    var based = $('#scaleBased input:checked').val();
    if(based === 'false'){
        return true;
    }
    var $this = $(this);
    var val = $this.val();
    $('#scaleWidth').val(val);
    $('#scaleHeight').val(val);
    return true;
});

//焦点跳转提交事件
$('#subFocusId').on('click', function() {
    var isShow = $('#focusIdShow input:checked').val();
    myData.focusData.nextFocusUpId = null;
    myData.focusData.nextFocusDownId = null;
    myData.focusData.nextFocusLeftId = null;
    myData.focusData.nextFocusRightId = null;
    if(isShow === 'true'){
        var nextFocusUpId = $('#nextFocusUpId').val();
        var nextFocusDownId = $('#nextFocusDownId').val();
        var nextFocusLeftId = $('#nextFocusLeftId').val();
        var nextFocusRightId = $('#nextFocusRightId').val();

        if(nextFocusUpId !== '请选择ID'){
            myData.focusData.nextFocusUpId = nextFocusUpId;
        }
        if(nextFocusDownId !== '请选择ID'){
            myData.focusData.nextFocusDownId = nextFocusDownId;
        }
        if(nextFocusLeftId !== '请选择ID'){
            myData.focusData.nextFocusLeftId = nextFocusLeftId;
        }
        if(nextFocusRightId !== '请选择ID'){
            myData.focusData.nextFocusRightId = nextFocusRightId;
        }
    }

    myData.clearSelect = null;
    myData.focusData = null;
    $('#focusIdModal').modal('hide');
    return false;
});

$('#focusIdModal').on('click', '[data-dismiss="modal"]', function(){
    myData.clearSelect = null;
    myData.focusData = null;
});

//修改云宽高提交事件
$('#subCloud').on('click', function() {
    var width = $('#cloudWidth').val();
    var height = $('#cloudHeight').val();
    var type = $('#cloudBatch input:checked').val();
    if(type === 'false'){
        if (width == ' ' || !width) {
            alert('请输入云宽');
            return false;
        }
        if (height == ' ' || !height) {
            alert('请输入云高');
            return false;
        }
        if (/\D/.test(width) || /\D/.test(height)) {
            alert('云宽高只能为数字');
            return false;
        }
        var $selectBlock = $('.selectBlock');
        var idx = getScreenIdx();
        $selectBlock.attr('data-yw', width);
        $selectBlock.attr('data-yh', height);
        desktopData.screens[idx].blocks[myData.slotIdex].yw = width;
        desktopData.screens[idx].blocks[myData.slotIdex].yh = height;
    }else if(type === 'true'){
        var standard = $('#cloudAll').val();
        if(standard === '请选择规范' || !standard){
            alert('请选择规范');
            return false;
        }else{
            cloudStandard(standard);
        }
    }
    myData.clearSelect = null;
    $('#cloudModal').modal('hide');
    return false;
});

$('#cloudModal').on('click', '[data-dismiss="modal"]', function(){
    myData.clearSelect = null;
});

function cloudStandard(type){
    var screens = $('#screenWarp li');
    var len = desktopData.screens.length;
    var l = 0;
    var con = '';
    for(var i = 0; i < len; i++){
        l = desktopData.screens[i].blocks.length;
        for(var j = 0; j < l; j++){
            if(type === 'XM11' && desktopData.screens[i].blocks[j].type === 'common'){
                con += standardXM11(i, j);
            }
        }
    }
    if(con){
        alert(con.substring(0, con.length - 1) + '坑位宽高不符合此规范');
    }
}

function standardXM11(i, j) {
    var screens = $('#screenWarp li');
    var h = Number(desktopData.screens[i].blocks[j].h) * 1.5;
    var w = Number(desktopData.screens[i].blocks[j].w) * 1.5;
    var $block = $('#screenWarp li:eq('+ i +') .screen-block:eq('+ j +')' );
    var yh = '';
    var yw = '';
    var con = '';
    if(w >= 306 && w <= 396){
        if(h >= 408 && h <= 528){//竖图
            yh = '480';
            yw = '360';
        }else if(h >= 199 && h <= 258){//小横图
            yh = '234';
            yw = '360';
        }
    }else if(w >= 620 && w <= 805 && h >= 405 && h <= 526){//大横图
        yh = '480';
        yw = '732';
    }

    if(yh && yw){
        desktopData.screens[i].blocks[j].yh = yh;
        desktopData.screens[i].blocks[j].yw = yw;
        $block.attr({
            "data-yh": yh,
            "data-yw": yw,
        });
    }else{
        con = desktopData.screens[i].blocks[j].slotId + '、';
    }
    return con;
}

//屏提交事件
$('#subNewScreen').on('click', function() {
    var title = $('#screenModal h4').text();
    var idx = getScreenIdx();
    var navLen = $('#navWarp a').length;
    var screenLen = 0;
    desktopData.screens.forEach(function(elem) {
        if (elem.blocks.length > 0) {
            screenLen++;
        }
    });
    if (title === '添加屏') {
        if ((navLen === 0 && screenLen > 0) || (navLen !== 0 && navLen === screenLen)) {
            alert('屏和导航不匹配，请修改导航');
            return;
        }
    }
    var $obj = $('#screenList').find('input:checked').parent().parent();
    var property = $obj.data('property') || [];
    var len = property.length;
    var $screenWarp = $('#screenWarp ul');
    var $screenDataWarp = $('#screenDataWarp ul');
    var $li = $('<li></li>');
    var $liData = $('<li></li>');
    for (var i = 0; i < len; i++) { //创建块
        var blockId = createBlockId(idx + 1, i + 1);
        property[i].slotId = blockId;
        $.extend(property[i], {
            "title": "",
            "isEditable": "false",
            "dataSource": "yunos",
            "layout": "APP",
            "operation": "true",
            "operationId": blockId,
            "slotId": blockId
        });
        $li.append(createBlock(property[i], false, blockId));
        $liData.append(createBlock(property[i], true, blockId, true));
    }
    if (title === '添加屏') {
        if (desktopData.screens[idx].blocks.length > 0) {
            alert('该屏已添加，请选择修改');
            return;
        }
        $screenWarp.find('li:eq(' + idx + ')').replaceWith($li);
        $screenDataWarp.find('li:eq(' + idx + ')').replaceWith($liData);
        desktopData.screens[idx].blocks = property;
        $('#screenWarp ul li').hide().eq(idx).show(); //隐藏其他屏，显示新增屏
        $('#screenDataWarp ul li').hide().eq(idx).show();
    } else if (title === '修改屏') {
        if (desktopData.screens[idx].blocks.length === 0) {
            alert('该屏为空，请选择添加');
            return;
        }

        $screenWarp.find('li:eq(' + idx + ')').replaceWith($li);
        $screenDataWarp.find('li:eq(' + idx + ')').replaceWith($liData);
        desktopData.screens[idx].blocks = property;
    }
    myData.slots = property;
    var $screenSlots = $('#screenSlots');
    $screenSlots.text(len);
    $('#screenTitle').text('(' + (idx + 1) + '/' + desktopData.screens.length + ')');
    $('#screenModal').modal('hide');
});

//在桌面上创建导航
function createNav(data) {
    var arr = data.extraData;
    var len = arr ? arr.length : 0;
    var con = '';
    var lists = 0;
    desktopData.screens.forEach(function(elem) {
        if (elem.blocks.length > 0) {
            lists++;
        }
    });
    var less = len - lists;
    var li = '';
    if (less < 0) {
        alert('屏的数量超过导航数量,请删除屏');
        return;
    }
    myData.navPics = [];
    for (var i = 0; i < len; i++) { //创建icon
        con += createIcons(arr[i], data.interval, i);
        myData.navPics.push({
            "normal": arr[i].normalPath,
            "forcus": arr[i].forcusPath
        });
        if (i !== 0) {
            desktopData.screens.push({
                'blocks': [],
                'slotGroupId': '请选择组'
            });
        }
    }
    for (var j = 0; j < less; j++) {
        li += '<li></li>';
    }
    myData.slots = desktopData.screens[0].blocks;
    if (desktopData.screens[0].blocks) {
        $('#screenSlots').text(desktopData.screens[0].blocks.length);
    } else {
        $('#screenSlots').text('');
    }
    $('#screenTitle').text('(' + 1 + '/' + desktopData.screens.length + ')');

    $('.screenWarp ul').html('').append(li);
    $('.screen-nav').html('').css({
        "left": data.x + 'px',
        "top": data.y + 'px'
    }).append(con);
}

//在桌面上创建导航2
function createNav2(data) {
    var arr = data.extraData;
    var len = arr ? arr.length : 0;
    var con = '';
    var lists = 0;
    var tempArr = desktopData.screens;
    desktopData.screens = [];
    tempArr.forEach(function(elem) {
        if (elem.blocks.length > 0) {
            desktopData.screens.push({
                'blocks': elem.blocks,
                'slotGroupId': elem.slotGroupId
            });
            lists++;
        }
    });

    if (lists === 0) {
        desktopData.screens.push({
            'blocks': [],
            'slotGroupId': '请选择组'
        });
    }

    var less = len - lists;
    var li = '';
    if (less < 0) {
        alert('屏的数量超过导航数量,请删除屏');
        return;
    }

    myData.navPics = [];
    for (var i = 0; i < len; i++) { //创建icon
        con += createIcons(arr[i], data.interval, i);
        myData.navPics.push({
            "normal": arr[i].normalPath,
            "forcus": arr[i].forcusPath
        });
        if (!desktopData.screens[i]) {
            desktopData.screens.push({
                'blocks': [],
                'slotGroupId': '请选择组'
            });
        }
    }

    var $screenWarp = $('#screenWarp ul');
    var $screenDataWarp = $('#screenDataWarp ul');
    for (var j = 0; j < len; j++) {
        li += '<li></li>';
    }
    $('.screenWarp ul').html('').append(li);
    desktopData.screens.forEach(function(elem, idx) {
        var $li = $('<li></li>');
        var $liData = $('<li></li>');
        if (elem.blocks.length > 0) {
            elem.blocks.forEach(function(e, i) {
                var blockId = '';
                if (elem.blocks[i].slotId) {
                    blockId = elem.blocks[i].slotId;
                } else {
                    blockId = createBlockId(idx + 1, i + 1);
                }
                $li.append(createBlock(elem.blocks[i], false, blockId));
                $liData.append(createBlock(elem.blocks[i], true, blockId, true, true));
            });
        }

        $screenWarp.find('li:eq(' + idx + ')').replaceWith($li);
        $screenDataWarp.find('li:eq(' + idx + ')').replaceWith($liData);
    });
    $('#screenWarp ul li').hide().eq(0).show();
    $('#screenDataWarp ul li').hide().eq(0).show();
    myData.slots = desktopData.screens[0].blocks;
    if (desktopData.screens[0].blocks) {
        $('#screenSlots').text(desktopData.screens[0].blocks.length);
    } else {
        $('#screenSlots').text('');
    }
    $('#screenTitle').text('(' + 1 + '/' + desktopData.screens.length + ')');

    $('.screen-nav').html('').css({
        "left": data.x + 'px',
        "top": data.y + 'px'
    }).append(con);
}

//修改时设置普通块的数据
function setBlockData() {
    var screenId = getScreenIdx();
    var blockData = desktopData.screens[screenId].blocks[myData.dataIdx];
    var dataSource = blockData.dataSource;
    var isEditable = blockData.isEditable;
    var disconnectEnable = blockData.disconnectEnable;
    var title = blockData.title;
    var layout = blockData.layout;

    clearBlockData();
    if (!isEditable) {
        return;
    } else if (isEditable === 'true') {
        $('#editType input:eq(0)').trigger('click');
    } else if (isEditable === 'false') {
        $('#editType input:eq(1)').trigger('click');
    }

    if(disconnectEnable == 'false'){
        $('#disconnectType input:eq(1)').trigger('click');
    }else if(disconnectEnable == 'true'){
        $('#disconnectType input:eq(0)').trigger('click');
    }

    if (blockData.operation === 'true') {
        $('#dataType input:eq(1)').trigger('click');
        $('#slotType input:eq(0)').trigger('click');
        $('#soltTitle').val('');
    } else if (blockData.operation === 'false') {
        $('#soltTitle').val(title);
        $('#slotType input:eq(1)').trigger('click');
    }

    if (dataSource === 'yunos') {
        $('#dataType input:eq(0)').trigger('click');
    } else {
        if(dataSource === 'linkin'){
            $('#dataType > input:eq(1)').trigger('click');
        }else if(dataSource === 'linkinOnly'){
            $('#dataType > input:eq(2)').trigger('click');
        }
        $('#fileShow2').val(blockData.pic);

        if (blockData.actionData && blockData.actionData.type === 'URI') {
            $('#jumpType > input:eq(1)').trigger('click');
            $('#uriVal').val(blockData.actionData.uri);
        } else if(blockData.actionData){
            $('#handleType').val(blockData.actionData.type);
            $('#jumpType > input:eq(0)').trigger('click');
            var appData = blockData.actionData.appName;
            var $jumpApp = $('#jumpApp');
            var optionA = $jumpApp.find('option').filter('[data-name="' + appData + '"]');
            optionA.prop("selected", true);
            $jumpApp.trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            });

            if (blockData.actionData && blockData.actionData.type !== 'APP') {
                var actionData = blockData.actionData.detailName;
                $jumpApp.trigger('change', actionData);
            } else {
                $jumpApp.trigger('change');
            }
        }
        if (blockData.actionData && blockData.actionData.appInfo) {
            $('#appType input:eq(0)').trigger('click');

            $('#appName').val(blockData.actionData.appName).trigger("chosen:updated.chosen").trigger('change', {
                "appName": blockData.actionData.appName,
                "pkgName": blockData.actionData.appInfo.pkgName,
                "versionCode": blockData.actionData.appInfo.versionCode,
                "url": blockData.actionData.appInfo.path
            });

        } else {
            $('#appType input:eq(1)').trigger('click');
        }
        $('#layoutType').val(blockData.layout).trigger('change');
        if (blockData.layout === 'VIDEO') {
            setChosenVal(blockData.videos);
        }
    }
    if(blockData.operation==="true"){
        $('#slotType input:eq(0)').trigger('click');

    }
}

function setQuickBlockData(){
    var screenId = getScreenIdx();
    var blockData = desktopData.screens[screenId].blocks[myData.dataIdx];

    var isEditable = blockData.isEditable;
    var title = blockData.title;
    var layout = blockData.layout;

    clearQuickBlockData();

    if(!blockData.actionData){
        return;
    }

    if (!isEditable) {
        return;
    } else if (isEditable === 'true') {
        $('#editTypeQuick input:eq(0)').trigger('click');
    } else if (isEditable === 'false') {
        $('#editTypeQuick input:eq(1)').trigger('click');
    }

    if (blockData.actionData.type === 'URI') {
        $('#jumpTypeQuick > input:eq(1)').trigger('click');
        // $('#uriNameQuick').val(data.uri.uriName);
        $('#uriValQuick').val(blockData.actionData.uri);
    } else {
        $('#jumpTypeQuick > input:eq(0)').trigger('click');
        var $jumpApp = $('#jumpAppQuick');
        var optionA = $jumpApp.find('option').filter('[data-name="' + blockData.actionData.appName + '"]');
        optionA.prop("selected", true);
        $jumpApp.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });

        if (blockData.actionData.type !== 'APP') {
            $jumpApp.trigger('change', blockData.actionData.detailName);
        } else {
            $jumpApp.trigger('change');
        }
    }

    if (blockData.actionData.appInfo) {
        setTimeout(function(){
            $('#appNameQuick').val(blockData.actionData.appName).trigger("chosen:updated.chosen").trigger('change', {
                "appName": blockData.actionData.appName,
                "pkgName": blockData.actionData.appInfo.pkgName,
                "versionCode": blockData.actionData.appInfo.versionCode,
                "url": blockData.actionData.appInfo.path
            });
        }, 300);
        $('#appTypeQuickEntry > input:eq(0)').trigger('click');
    } else {
        $('#appTypeQuickEntry > input:eq(1)').trigger('click');
    }
}

//修改时根据videoVal，初始化chosen的值
function setChosenVal(videoVal) {
    videoVal.forEach(function(elem) {
        myData.videoLists.push([elem.url, elem.duration]);
        $('#videoList').append('<div><label title="' + elem.url + '--' + elem.duration + '">' + elem.url + '--' + elem.duration + '</label><button type="button" class="close">×</button></div>');
    });
}

//设置普通块的显示效果
function setBlockDataShow ($block, data) {
    var title = '';
    if (data.dataSource === 'yunos') {
        $block.css({ //云
            'background-image': 'url(img/icon_yunos.png)',
            'background-position': '50%',
            'background-repeat': 'no-repeat',
            'background-size': 'initial'
        });
        return false;
    }
    if (data.pic && data.dataSource !== 'yunos' && (data.layout !== 'APP' && data.layout !== 'APP_CENTER_IMG_BOTTOM_TEXT')) {
        $block.css({ //加载中
            'background-image': 'url(img/loading.gif)',
            'background-size': '32px 32px',
            'background-repeat': 'no-repeat',
            'background-position': 'center',
        });
        preImage(data.pic, function(){
            $block.css({ //图片或视频
                'background-image': 'url(' + data.pic + ')',
                'background-size': '100% 100%'
            });
        });
        return false;
    }

    if (data.pic && data.dataSource !== 'yunos' && data.layout === 'APP') {
        $block.css({ //加载中
            'background-image': 'url(img/loading.gif)',
            'background-size': '32px 32px',
            'background-repeat': 'no-repeat',
            'background-position': 'center',
        });
        preImage(data.pic, function(){
            var size = '96px 96px';
            if(this.width < 96){
                size = this.width + 'px ' + this.height + 'px';
            }
            $block.css({ //应用
                'background-image': 'url(' + data.pic + ')',
                'background-size': size
            });
        });
        title = data.title || '';
        $block.append('<div class="block-title" style="top: 5%;">' + title + '</div>');
        return false;
    }

    if (data.pic && data.dataSource !== 'yunos' && data.layout === 'APP_CENTER_IMG_BOTTOM_TEXT') {
        $block.css({ //加载中
            'background-image': 'url(img/loading.gif)',
            'background-size': '32px 32px',
            'background-repeat': 'no-repeat',
            'background-position': 'center',
        });
        preImage(data.pic, function(){
            var size = '96px 96px';
            if(this.width < 96){
                size = this.width + 'px ' + this.height + 'px';
            }
            $block.css({ //应用底部文本
                'background-image': 'url(' + data.pic + ')',
                'background-size': size
            });
        });
        title = data.title || '';
        $block.append('<div class="block-title" style="top: 5%;">' + title + '</div>');
        return false;
    }

    $block.css({ //其他
        'background-image': 'initial',
        'background-size': 'initial',
        'background-repeat': 'no-repeat',
        'background-position': 'center',
    });
}

//设置全局快捷块的显示效果
function setGlobalQuickBlockDataShow ($block, data) {
    var title = '';
    if (data.normalDrawable) {
        $block.css({ //加载中
            'background-image': 'url(img/loading.gif)',
            'background-size': '32px 32px',
            'background-repeat': 'no-repeat',
            'background-position': 'center',
        });
        preImage(data.normalDrawable, function(){
            $block.css({ //图片或视频
                'background-image': 'url(' + data.normalDrawable + ')',
                'background-size': '100% 100%'
            });
        });
        title = data.title || '';
        $block.append('<div class="block-title" style="top: 5%;">' + title + '</div>');
        return false;
    }

    $block.css({ //其他
        'background-image': 'initial',
        'background-size': 'initial',
        'background-repeat': 'no-repeat',
        'background-position': 'center',
    });
}

//设置快捷块的显示效果
function setQuickBlockDataShow ($block, data) {
    var title = '';
    if (data.normalDrawable) {
        $block.css({ //加载中
            'background-image': 'url(img/loading.gif)',
            'background-size': '32px 32px',
            'background-repeat': 'no-repeat',
            'background-position': 'center',
        });
        preImage(data.normalDrawable, function(){
            $block.css({ //图片或视频
                'background-image': 'url(' + data.normalDrawable + ')',
                'background-size': '100% 100%'
            });
        });
        title = data.title || '';
        $block.append('<div class="block-title" style="top: 5%;">' + title + '</div>');
        return false;
    }

    $block.css({ //其他
        'background-image': 'initial',
        'background-size': 'initial',
        'background-repeat': 'no-repeat',
        'background-position': 'center',
    });
}

//更新块的数据
function updateBlockDate($block, data, req) {
    if(data.type === 'common'){
        updateCommonBlockDate($block, data, req);
    }else if(data.type === 'quickEntry'){
        updateQuickBlockDate($block, data, req);
    }else if(data.type === 'globalQuickEntry'){
        updateGlobalQuickBlockDate($block, data, req);
    }
}

function updateGlobalQuickBlockDate($block, data, req){
    $block.find('.block-title').remove();
    $block.removeClass('block-relation-quick-slot');
    $block.find('.fa-pencil-square-o').remove();
    if(!req){
        setGlobalQuickBlockDataShow($block, data);
        return false;
    }
    return AjaxGet('/desktop/quickEntrySlotLists?slotID='+ data.slotId, function(slotData){
        if(slotData.count != '0'){
            setGlobalQuickBlockDataShow($block, slotData.extra);
        }else{
            $block.addClass('block-relation-quick-slot');
            $block.css({ //关联失败
                'background-image': 'url(img/icon_operation_bad.png)',
                'background-size': '60%',
                'background-repeat': 'no-repeat',
                'background-position': 'center',
            });
        }
    });
}

function updateQuickBlockDate($block, data, req){
    $block.find('.block-title').remove();
    $block.removeClass('block-relation-quick');
    $block.find('.fa-pencil-square-o').remove();
    if(!req){
        setQuickBlockDataShow($block, data);
        return false;
    }
    return AjaxGet('/desktop/quickEntrySlotLists?slotID='+ data.slotId, function(slotData){
        if(slotData.count != '0'){
            setQuickBlockDataShow($block, slotData.extra);
        }else{
            $block.addClass('block-relation-quick');
            $block.css({ //关联失败
                'background-image': 'url(img/icon_operation_bad.png)',
                'background-size': '60%',
                'background-repeat': 'no-repeat',
                'background-position': 'center',
            });
        }
    });
}

function updateCommonBlockDate($block, data, req){
    $block.find('.block-title').remove();
    $block.removeClass('block-relation');
    if (data.operation === 'true') {
        var x = (Number(data.w) - 48 - 7) + 'px';
        $block.find('.block-data').css({ //运营坑位
            'background-image': 'url(img/icon_operation.png)',
            'background-position': x + ' 7px',
            'background-repeat': 'no-repeat',
            'background-size': '48px 25px'
        });
        if(!req){
            setBlockDataShow($block, data);
            return false;
        }
        return AjaxGet('/desktop/operationSlotLists?slotID='+ data.slotId + '&slotGroupId=' + $('#slotGroup').val(), function(slotData){
            if(slotData.count != '0'){
                var w = 0 | data.w;
                var h = 0 | data.h;
                if(w == h) {
                    slotData.extra.pic = slotData.extra.pic1;
                }else if(w  > h) {
                    slotData.extra.pic = slotData.extra.pic2;
                }else if(w < h) {
                    slotData.extra.pic = slotData.extra.pic3;
                }
                data.pic=slotData.extra.pic;

                data.title=slotData.extra.title;
               // data.slotId=slotData.extra.slotId;
                data.isEditable=slotData.extra.isEditable;
                data.dataSource=slotData.extra.dataSource;
                data.layout=slotData.extra.layout;
                data.disconnectEnable=slotData.extra.disconnectEnable;
                data.operationId=slotData.extra.slotID;

                if(slotData.extra.actionType==="ACTION"){
                    data.actionData=slotData.extra.action;
                    data.actionData.type=slotData.extra.actionType;

                }else if(slotData.extra.actionType==="URL"){
                    data.actionData.uri=slotData.extra.uri.uriName;
                    data.actionData.type=slotData.extra.actionType;

                }else if(slotData.extra.actionType==="COMPONENT"){
                    data.actionData=slotData.extra.component;
                    data.actionData.type=slotData.extra.actionType;

                }else if(slotData.extra.actionType==="APP"){
                    data.actionData={};
                    if(slotData.extra.bindApp){
                        data.actionData.appInfo={
                            "path":slotData.extra.bindApp.url,
                            "pkgName":slotData.extra.bindApp.pkgName,
                            "versionCode":slotData.extra.bindApp.versionCode
                        }
                    }
                    data.actionData.pkgName=slotData.extra.app.pkgName;
                    data.actionData.appName=slotData.extra.app.appName;
                    data.actionData.type=slotData.extra.actionType;
                }

                setBlockDataShow($block, slotData.extra);
            }else{
                $block.addClass('block-relation');
                $block.css({ //关联失败
                    'background-image': 'url(img/icon_operation_bad.png)',
                    'background-size': '60%',
                    'background-repeat': 'no-repeat',
                    'background-position': 'center',
                });
            }
        });
    }else{
        $block.find('.block-data').css({ //非运营坑位
            'background-image': 'initial',
            'background-size': 'initial',
            'background-repeat': 'no-repeat',
            'background-position': 'center'
        });
        setBlockDataShow($block, data);
        return false;
    }
}

//块数据提交
$('#subData').on('click', function() {
    var screenId = getScreenIdx();
    var blockData = desktopData.screens[screenId].blocks[myData.dataIdx];
    var isEditable = $('#editType input:checked').val();
    var dataSource = $('#dataType input:checked').val();
    var disconnectType = $('#disconnectType > input:checked').val();

    var slotId = myData.dataTitle;
    var title = $('#soltTitle').val();

    var jumpType = $('#jumpType > input:checked').val();
    var slotType = $('#slotType > input:checked').val();

    var layoutType = $('#layoutType').val();
    var operationId = null;

    var data = {};
    var appInfo = {};
    var picData = new FormData();

    if (slotType === 'false' && dataSource === 'yunos') {
        layoutType = 'APP';
        title = '';
        data = {
            "title": title,
            "slotId": slotId,
            "isEditable": isEditable,
            "dataSource": dataSource,
            "disconnectEnable": disconnectType,
            "layout": layoutType,
            "operation": slotType
        };
    } else {
        data = {
            "title": title,
            "slotId": slotId,
            "isEditable": isEditable,
            "dataSource": dataSource,
            "disconnectEnable": disconnectType,
            "layout": layoutType,
            "operation": slotType
        };

        if (slotType === 'true') {
            data.layout = 'APP';
            data.operationId = slotId;
        } else if (slotType === 'false') {
            if (layoutType === 'VIDEO') {
                var len = myData.videoLists.length;
                var videos = [];
                if (len === 0) {
                    alert('请添加坑位视频');
                    return false;
                }
                for (var i = 0; i < len; i++) {
                    var elem = myData.videoLists[i];
                    videos.push({
                        "url": elem[0],
                        "duration": elem[1]
                    });
                }
                data.videos = videos;
            }

            if(layoutType === '请选择显示效果'){
                alert('请选择显示效果');
                return false;
            }else if(layoutType !== 'APP' && layoutType !== 'APP_CENTER_IMG_BOTTOM_TEXT'){
                var fileObj2 = document.getElementById("fileHide2").files[0];
                var fileVal2 = $("#fileShow2").val();
                if (fileVal2 == ' ' || !fileVal2) {
                    alert('请选择要上传的图片');
                    return false;
                }

                if (fileVal2 != ' ' && fileVal2.indexOf('http') == -1 && fileVal2) {
                    picData.append("pic", fileObj2);
                }

                if (fileVal2.indexOf('http') != -1) {
                    data.pic = fileVal2;
                }
            }

            data.actionData = {};
            var appName = '';
            var appNameData = '';
            var pkgName = '';
            var icon = '';
            if(jumpType != 'URI'){
                appName = $('#appName').val();
                appNameData = $('#appName').data('_' + appName);
                pkgName = appNameData.pkgName;
                icon = appNameData.icon;
            }

            if(layoutType === 'APP' || layoutType === 'APP_CENTER_IMG_BOTTOM_TEXT'){
                if(icon === ''){
                    alert('该应用没有图标，请在第三方应用上传');
                    return false;
                }
                data.pic = icon;
            }

            var appType = $('#appType > input:checked').val();
            if (appType === 'true') {
                var versionCode = $('#versionCode').val();
                var appUrl = $('#appUrl').val();

                if (versionCode == '请选择绑定应用版本' || !versionCode) {
                    alert('请选择绑定应用版本');
                    return false;
                }
                if (appUrl == '请选择绑定应用路径' || !appUrl) {
                    alert('请选择绑定应用路径');
                    return false;
                }
                data.actionData.appInfo = {
                    "path": appUrl,
                    "pkgName": pkgName,
                    "versionCode": versionCode
                };
            }

            if (layoutType == '请选择显示效果' || !layoutType) {
                alert('请选择显示效果');
                return false;
            }
            data.layout = layoutType;

            if (jumpType === 'URI') {       //跳转uri
                data.actionData.type = jumpType;
                data.actionData.uri = $('#uriVal').val();
                if (data.actionData.uri == ' ' || !data.actionData.uri) {
                    alert('请输入跳转链接');
                    return false;
                }
            } else {
                var $jumpApp = $('#jumpApp');
                if ($jumpApp.val() == '请选择跳转应用') {
                    alert('请选择跳转应用');
                    return false;
                }
                var appData = $jumpApp.data('_' + $jumpApp.val());
                var $jumpDetail = $('#jumpDetail');
                if ($jumpDetail.val() == '请选择跳转详情页') {      //跳转到app
                    data.actionData.type = 'APP';
                    data.actionData.pkgName = appData.pkgName;
                    data.actionData.appName = appData.appName;
                } else {
                    var detailDate = $jumpDetail.data('_' + $jumpDetail.val());
                    //detailDate.actionData.appInfo = data.actionData.appInfo;
                    var tmpData = data.actionData.appInfo;
                    data.actionData = detailDate;
                    data.actionData.appInfo = tmpData;
                    data.actionData.appName = appData.appName;
                    data.actionData.pkgName = appData.pkgName;
                    data.actionData.type = detailDate.actionType;
                    // data.actionData.pkgName = appData.pkgName;
                    // data.actionData.appName = appData.appName;
                    // var detailDate = $jumpDetail.data('_' + $jumpDetail.val());
                    // data.actionData.extraData = detailDate.extraData;
                    // data.actionData.type = detailDate.actionType;
                    // data.actionData.detailName = detailDate.detailName;
                    // if (detailDate.actionType === 'ACTION') {       //跳转到action
                    //     data.actionData.action = detailDate.action;
                    // } else if (detailDate.actionType === 'COMPONENT') {     //跳转到component
                    //     data.actionData.component = detailDate.component;
                    //     data.actionData.clsName = detailDate.clsName;
                    // }
                }
            }
        }
    }

    blockData = {
        "slotId": blockData.slotId,
        "w": blockData.w,
        "h": blockData.h,
        "yh": blockData.yh,
        "yw": blockData.yw,
        "bg": blockData.bg,
        "x": blockData.x,
        "y": blockData.y,
        "type": 'common'
    };

    if (dataSource !== 'yunos' && slotType !== 'true' && !data.pic) {
        picData.append('additional', 'slot');
        AjaxFile('/desktop/updataImage', picData, function(imgData) {
            data.pic = imgData.pic;
            $.extend(blockData, data);
            desktopData.screens[screenId].blocks[myData.dataIdx] = blockData;
            var $block = $('#screenDataWarp ul li:eq(' + screenId + ')').find('.screen-block:eq(' + myData.dataIdx + ')');
            updateBlockDate($block, blockData, true);
        });
    } else {
        $.extend(blockData, data);
        desktopData.screens[screenId].blocks[myData.dataIdx] = blockData;
        var $block = $('#screenDataWarp ul li:eq(' + screenId + ')').find('.screen-block:eq(' + myData.dataIdx + ')');
        updateBlockDate($block, blockData, true);
    }

    $('#dataModal').modal('hide');
});

//提交所有数据
$('#subAllData').on('click', function(e, newDesktop) {
    var name = $('#desktopName').val();
    var desc = $('#desktopDesc').val();
    var screenLists = [];
    var blockIds = [];
    var handleIds = [];
    var handles = [];
    var relation = $('.block-relation');    //关联失败的运营坑位
    var relationQuick = $('.block-relation-quick');     //关联失败的快捷坑位
    var relationQuickSlot = $('.block-relation-quick-slot');     //关联失败的全局快捷坑位
    var filter = $('#myTable_filter input').val() || '';
    var i = 0, j = 0;
    var len = 0, l = 0;
    var id = '';

    if (name == ' ' || !name) {
        alert('请输入桌面名称');
        return false;
    }
    var con = '';
    for(j = 0, l = relation.length; j < l; j++){
        var $relation = $(relation[j]);
        id = $relation.find('span').text();
        con += '没有坑位ID为' + id + '的运营坑位\n';
    }
    if(con){
        alert(con);
        return false;
    }

    con = '';
    for(j = 0, l = relationQuick.length; j < l; j++){
        var $relationQuick = $(relationQuick[j]);
        id = $relationQuick.find('span').text();
        con += '没有坑位ID为' + id + '的快捷坑位\n';
    }
    if(con){
        alert(con);
        return false;
    }

    con = '';
    for(j = 0, l = relationQuickSlot.length; j < l; j++){
        var $relationQuickSlot = $(relationQuickSlot[j]);
        id = $relationQuickSlot.find('span').text();
        con += '没有坑位ID为' + id + '的全局快捷坑位\n';
    }
    if(con){
        alert(con);
        return false;
    }

    var data = {
        "name": name,
        "desc": desc
    };

    if(desktopData.appConfig.firstSlotId ){//桌面基础设置
        data.appConfig = {};
        $.extend(data.appConfig, desktopData.appConfig);
    }

    if (desktopData.nav) {//导航
        data.nav = desktopData.nav;
        data.appConfig && (data.appConfig.isCreateNavigation = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateNavigation = 'false');
    }
    if (desktopData.attachment) {//附件栏
        data.attachment = desktopData.attachment;
        blockIds = getHandleId(data.attachment, blockIds);
        data.appConfig && (data.appConfig.isCreateSlotAttachment = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateSlotAttachment = 'false');
    }
    if (desktopData.quickEntry) {//快捷入口
        data.quickEntry = desktopData.quickEntry;
        blockIds = getHandleId(data.quickEntry, blockIds);
        data.appConfig && (data.appConfig.isCreateQuickEntry = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateQuickEntry = 'false');
    }
    if (desktopData.quickEntryTwoState) {//两态态快捷入口
        data.quickEntryTwoState = desktopData.quickEntryTwoState;
        blockIds = getHandleId(data.quickEntryTwoState, blockIds);
        data.appConfig && (data.appConfig.isCreateUsbWidget = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateUsbWidget = 'false');
    }
    if (desktopData.quickEntryThreeState) {//三态快捷入口
        data.quickEntryThreeState = desktopData.quickEntryThreeState;
        blockIds = getHandleId(data.quickEntryThreeState, blockIds);
        data.appConfig && (data.appConfig.isCreateNetworkWidget = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateNetworkWidget = 'false');
    }
    if (desktopData.quickEntryGroup) {
        data.quickEntryGroup = desktopData.quickEntryGroup;
        for (var i = 0; i < data.quickEntryGroup.length; i++) {
            for (var j = 0; j < data.quickEntryGroup[i].extra.length; j++) {
                blockIds.push(data.quickEntryGroup[i].extra[j].index);
            }
        }
    }
    if(desktopData.timeWeather){//时间天气
        data.timeWeather = desktopData.timeWeather;
        data.appConfig && (data.appConfig.isCreateTimeWeather = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateTimeWeather = 'false');
    }
    if (desktopData.timebar) {//时间（新天气弃置这个字段）
        data.timebar = desktopData.timebar;
        data.appConfig && (data.appConfig.isCreateTimeWidget = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateTimeWidget = 'false');
    }
    if (desktopData.weather) {//天气
        data.weather = desktopData.weather;
        data.appConfig && (data.appConfig.isCreateWeatherWidget = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateWeatherWidget = 'false');
    }
    if (desktopData.sn) {//SN
        data.sn = desktopData.sn;
        data.appConfig && (data.appConfig.isCreateSnWidget = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateSnWidget = 'false');
    }
    if (desktopData.quickList) {//底部快捷栏
        data.quickList = desktopData.quickList;
        data.appConfig && (data.appConfig.isCreateQuickList = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateQuickList = 'false');
    }
    if(desktopData.shortCutConfig && desktopData.shortCutConfig.length > 0){//快捷键
        data.shortCutConfig = desktopData.shortCutConfig;
    }
    if (desktopData.logo) {//LOGO
        data.logo = desktopData.logo;
        data.appConfig && (data.appConfig.isCreateLogoWidget = 'true');
    }else{
        data.appConfig && (data.appConfig.isCreateLogoWidget = 'false');
    }

    if(desktopData.image){
        data.image = desktopData.image;
    }

    if(desktopData.style){
        data.style = desktopData.style;
    }

    if(desktopData.messageConfig){//消息控件
        data.messageConfig = desktopData.messageConfig;
        data.messageConfig.x = Number($('#msgWarp').css('left').split('px')[0]);
        data.messageConfig.y = Number($('#msgWarp').css('top').split('px')[0]);
    }

    if(desktopData.animation){  //动画效果
        data.animation = desktopData.animation;
    }

    if(desktopData.enlargeVal){ //jatai2016-03-10   放大倍数
        data.enlargeVal=desktopData.enlargeVal;
    }

    desktopData.screens.forEach(function(elem) {
        if (elem.blocks.length > 0) {
            if(elem.itemStyle){
                screenLists.push({
                    'blocks': elem.blocks,
                    'slotGroupId': elem.slotGroupId,
                    'itemStyle':elem.itemStyle
                });
            }else{
                screenLists.push({
                    'blocks': elem.blocks,
                    'slotGroupId': elem.slotGroupId,
                    'itemStyle':null
                });
            }
        }

    });
    if (screenLists.length > 0) {
        data.screens = screenLists;
        screenLists.forEach(function(e) {
            e.blocks.forEach(function(elem) {
                blockIds.push(elem.slotId);
            });
        });

        // 检查坑位是否与当前屏匹配
        if(desktopData.nav){
            for(i = 0, len = screenLists.length; i < len; i++){
                var fnId = desktopData.nav.extraData[i].functionId;
                for(j = 0, l = screenLists[i].blocks.length; j < l; j++){
                    var elem = screenLists[i].blocks[j];
                    if(elem.slotId.charAt(0) != fnId && elem.type === 'common'){
                        alert('坑位'+ elem.slotId +'和第' + (i+1) + '屏功能编号不一致');
                        return ;
                    }
                }
            }
        }

        blockIds = blockIds.sort();//检查坑位ID是否重复
        for (i = 0, len = blockIds.length; i < len; i++) {
            if (blockIds[i] == blockIds[i + 1]) {
                alert("ID" + blockIds[i] + "重复");
                return;
            }
        }
        blockIds = null;
    }

    if(data.slotGroupId){
        data.slotGroupId = desktopData.screens.slotGroupId;
    }

    if($('.quick-block').length || $('#quickSlotWarp .screen-block').length){
        data.quickEntrySlot = desktopData.quickEntrySlot;
    }
    setBlurNum(data);
    if (desktopAction === 'edit') {
        data.id = myData.desktopId;
        AjaxPost('/desktop/modifyDesktop', data, function() {
            updateTable(currentPage, filter);
            alert('修改桌面成功');
        });
    } else if (desktopAction === 'add') {
        data.groupId = myData.groupId;
        AjaxPost('/desktop/addDesktop', data, function(obj) {
            updateTable(currentPage, filter);
            myData.desktopId = obj.id;
            alert('新增桌面成功');
            desktopAction = 'edit';
            if(newDesktop){
                myData.slots = desktopData.screens[0].blocks;
            }
        });
    }
});

function setBlurNum(data) {
    if (data.screens) {
        for (var i = 0; i < data.screens.length; i++) {
            if (data.screens[i].blocks) {
                for (var j = 0; j < data.screens[i].blocks.length; j++) {
                    if (data.screens[i].blocks[j].bg.length === 8) {
                        data.screens[i].blocks[j].bg = '#0' + data.screens[i].blocks[j].bg.split('#')[1];
                    }
                }
            }
        }
    }
}

//获取控件ID
function getHandleId(arr, handleIds){
    for(var i = 0, len = arr.extraData.length; i < len; i++){
        handleIds.push(arr.extraData[i].index);
    }
    return handleIds;
}

//桌面组提交事件
$('#subDGroup').on('click', function(){
    var name = $('#dGroupName').val();
    var desc = $('#dGroupDesc').val();
    var title = $('#dGroupModal h4').text();
    var data = {};

    if(name == ' ' || !name){
        alert('请输入组名称！');
        return false;
    }

    data = {"name": name, "desc": desc};

    if(title === '新增'){
        AjaxPost('/desktop/addDesktopGroup', data, function(){
            updateDGroup();
            $('#dGroupModal').modal('hide');
        });
    }else if(title === '修改'){
        data.id = myData.groupId;
        AjaxPost('/desktop/modifyDesktopGroup', data, function(){
            updateDGroup();
            $('#dGroupModal').modal('hide');
        });
    }


});

//创建桌面组
function createDGroup(data){
    var dataArr = [];
    var len = data.extra.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.desc, null]);
    }
    myDataTable('#dGroupTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'组名称','width':'35%', 'targets':1},
            {'title':'备注','width':'20%', 'targets':2},
            {'title':'桌面列表','width':'10%', 'targets':3}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(3, nRow, 'align-justify');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "name":aData[1],
                "desc":aData[2]
            });
        }
    });
    initToolBar('#dGroupTable');
}

//创建桌面列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name, arr.layoutUpdateTme, arr.updateTime, arr.createTime, arr.desc, arr.user || '--']);
    }
    $('#myTable').dataTable({
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
            [2, "desc"]
        ],
        "columnDefs": [
        {
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '6%',
            'targets': 0,
            "orderable": false
        },
        {
            'title': '桌面名称',
            'width': '15%',
            'targets': 1
        },{
            'title': '布局更新时间',
            'width': '12%',
            'targets': 2
        },{
            'title': '全局更新时间',
            'width': '12%',
            'targets': 3
        },{
            'title': '创建时间',
            'width': '12%',
            'targets': 4
        },{
            'title': '备注',
            'width': '15%',
            'targets': 5
        },{
            'title': '最后修改者',
            'width': '10%',
            'targets': 6
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).data({
                "id": aData[0],
                "name": aData[1],
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable', [
        myConfig.backBtn,
        myConfig.addBtn,
        myConfig.editBtn,
        myConfig.delBtn,
        '<a class="btn my-btn btn-primary moveBtn" href="javascript:"><i class="fa fa-cut icon-white"></i>&nbsp;移动</a>',
        '<a class="btn my-btn btn-primary recordBtn" href="javascript:"><i class="fa fa-search icon-white"></i>&nbsp;生成记录</a>',
        myConfig.releaseBtn,
'<a class="btn my-btn btn-primary copyBtn" href="javascript:"><i class="fa fa-copy icon-white"></i>&nbsp;复制桌面布局</a>'
    ]);

    listenCheckBox();
    updateChecked();
}

//创建布局列表
function createRecord(data) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([formatDate(arr.layoutVersion), formatDate(arr.version), arr.layoutPath, arr.sourcePath, arr.time]);
    }
    myDataTable('#recordTable', {
        "data": dataArr,
        "searching": false,
        "pageLength": 12,
        "order": [
            [4, "desc"]
        ],
        "columnDefs": [{
            'title': '布局版本',
            'width': '12%',
            'targets': 0
        }, {
            'title': '资源包版本',
            'width': '12%',
            'targets': 1
        }, {
            'title': '布局',
            'width': '15%',
            'targets': 2
        }, {
            'title': '资源包',
            'width': '15%',
            'targets': 3
        }, {
            'title': '生成时间',
            'width': '12%',
            'targets': 4
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            tableTdDownload(2, nRow, aData[2]);
            tableTdDownload(3, nRow, aData[3]);
        }
    });
}

//LOGO拖拽事件
listenSingleDrag("#logoWarp", 'LOGO信息：');

//导航点击事件
$('#navWarp').on('click', 'a', function() {
    if (HANDLE_IS_ABLE) {
        return false;
    }
    var $now = $(this);
    if ($now.hasClass('active')) {
        return false;
    }
    switchNav($now);
    return false;
});
//导航拖拽事件
listenSingleDrag("#navWarp", '导航信息：');

//阻止附件栏点击事件
$('#attachmentWarp').on('click', 'a', function() {
    $old = $('#attachmentWarp .active');
    $now = $(this);
    if($old === $now){
        return false;
    }
    oIdx = $old.index();
    nIdx = $now.index();
    $old.removeClass('active').find('img').attr('src', desktopData.attachment.extraData[oIdx].normalPath);
    $now.addClass('active').find('img').attr('src', desktopData.attachment.extraData[nIdx].forcusPath);
    return false;
});
//附件栏拖拽事件
listenSingleDrag("#attachmentWarp", '附件栏信息：');

//阻止快捷入口点击事件
$('#quickWarp').on('click', 'a', function() {
    return false;
});
//快捷入口拖拽事件
listenQuickDrag("#quickWarp", '快捷入口信息：');

//阻止两态快捷入口点击事件
$('#twoWarp').on('click', 'a', function() {
    return false;
});
//两态快捷入口拖拽事件
listenMultipleDrag("#twoWarp", '两态快捷信息：');

//阻止三态快捷入口点击事件
$('#threeWarp').on('click', 'a', function() {
    return false;
});
//三态快捷入口拖拽事件
listenMultipleDrag("#threeWarp", '三态快捷信息：');

//快捷入口组拖拽事件
listenGroupMultipleDrag("#quickEntryGroupWarp", '快捷入口组信息：');

//快捷入口点击事件
$('#quickEntryGroupWarp').on('click', 'a', function() {
    if (HANDLE_IS_ABLE) {
        return false;
    }
    var $now = $(this);
    if ($now.hasClass('active')) {
        return false;
    }
    switchquickEntry($now);
    return false;
});

//消息控件拖拽事件
listenSingleDrag("#msgWarp", '消息控件信息：');

//时间拖拽事件
listenSingleDrag("#timerWarp", '时间信息：');

//天气拖拽事件
listenSingleDrag("#weatherWarp", '天气信息：');

//时间天气拖拽事件
listenSingleDrag("#timeWeatherWarp", '时间天气信息：');

//SN拖拽事件
listenSingleDrag("#snWarp", 'SN信息：');

//全局快捷坑位拖拽事件
listenGobalSlotDrag("#quickSlotWarp", '全局快捷坑位信息：');

//布局数据导航点击事件
$('#navDataWarp').on('click', 'a', function() {
    var $nowData = $(this);
    if ($nowData.hasClass('active')) {
        return false;
    }
    switchNavData($nowData);
    return false;
});

//布局数据导航换图
function switchNavData($nowData) {
    var $oldData = $('#navDataWarp a').filter('.active').removeClass('active');
    var oIdx = $oldData.index();
    var nIdx = $nowData.index();
    $nowData.addClass('active');
    var $now = $('#navWarp a:eq(' + nIdx + ')').addClass('active');
    var $old = $('#navWarp a:eq(' + oIdx + ')').removeClass('active');

    $old.find('img').attr('src', myData.navPics[oIdx].normal);
    $now.find('img').attr('src', myData.navPics[nIdx].forcus);
    $oldData.find('img').attr('src', myData.navPics[oIdx].normal);
    $nowData.find('img').attr('src', myData.navPics[nIdx].forcus);

    clearSelectBlock();
    if (desktopData.screens[nIdx]) {
        myData.slot = null;
        desktopData.screens[oIdx].blocks = myData.slots;
        myData.slots = desktopData.screens[nIdx].blocks;
    }
    switchScreen(oIdx, nIdx);
    var len = $('#screenWarp ul li:eq(' + nIdx + ')').find('.screen-block').length;
    $('#screenSlots').text(len);
    $('#screenTitle').text('(' + (nIdx + 1) + '/' + desktopData.screens.length + ')');
}

//导航换图
function switchNav($now) {
    var $old = $('#navWarp a').filter('.active').removeClass('active');
    var oIdx = $old.index();
    var nIdx = $now.index();
    $now.addClass('active');
    var $nowData = $('#navDataWarp a:eq(' + nIdx + ')').addClass('active');
    var $oldData = $('#navDataWarp a:eq(' + oIdx + ')').removeClass('active');

    $old.find('img').attr('src', myData.navPics[oIdx].normal);
    $now.find('img').attr('src', myData.navPics[nIdx].forcus);
    $oldData.find('img').attr('src', myData.navPics[oIdx].normal);
    $nowData.find('img').attr('src', myData.navPics[nIdx].forcus);

    clearSelectBlock();
    if (desktopData.screens[nIdx]) {
        myData.slot = null;
        desktopData.screens[oIdx].blocks = myData.slots;
        myData.slots = desktopData.screens[nIdx].blocks;
    }
    switchScreen(oIdx, nIdx);
    var len = $('#screenWarp ul li:eq(' + nIdx + ')').find('.screen-block').length;
    $('#screenSlots').text(len);
    $('#screenTitle').text('(' + (nIdx + 1) + '/' + desktopData.screens.length + ')');
}

function switchquickEntry($now) {
    var $old = $now.parent().children().filter('.active').removeClass('active');
    if ($now.siblings('br').length > 0) { 
        var oIdx = Math.round($old.index()/2);
        var nIdx = Math.round($now.index()/2);
        
    }else{
        var oIdx = $old.index();
        var nIdx = $now.index();
    }
    $now.addClass('active');
    // var $nowData = $now.parent().children(':eq(' + nIdx + ')').addClass('active');
    // var $oldData = $now.parent().children(':eq(' + oIdx + ')').removeClass('active');

    var parentId = Number($now.parent().get(0).id);
    $old.find('img').attr('src', myData.QEGPics[parentId][oIdx].normalPath);
    $now.find('img').attr('src', myData.QEGPics[parentId][nIdx].forcusPath);
    // $oldData.find('img').attr('src', myData.QEGPics[parentId][oIdx].normalPath);
    // $nowData.find('img').attr('src', myData.QEGPics[parentId][nIdx].forcusPath);

//     clearSelectBlock();
//     if (desktopData.screens[nIdx]) {
//         myData.slot = null;
//         desktopData.screens[oIdx].blocks = myData.slots;
//         myData.slots = desktopData.screens[nIdx].blocks;
//     }
// //    switchScreen(oIdx, nIdx);
//     var len = $('#screenWarp ul li:eq(' + nIdx + ')').find('.screen-block').length;
//     $('#screenSlots').text(len);
//     $('#screenTitle').text('(' + (nIdx + 1) + '/' + desktopData.screens.length + ')');
}

//切换屏
function switchScreen(oIdx, nIdx) {
    var $lists = $("#screenWarp ul li");
    var $listDatas = $("#screenDataWarp ul li");
    var n = 0;
    var o = 0;
    if (nIdx > oIdx) {
        n = 1280;
        o = -1280;
    } else {
        n = -1280;
        o = 1280;
    }

    if (nIdx !== 0) {
        $('#attachmentWarp').hide();
    }
    if(!desktopData.screens[nIdx].slotGroupId){
        desktopData.screens[nIdx].slotGroupId = '请选择组';
    }
    $('#slotGroup').val(desktopData.screens[nIdx].slotGroupId);

    $lists.eq(nIdx).css({
        'left': n,
        'display': 'block'
    }).animate({
        'left': 0,
        'opacity': 1
    }, 300, function() {
        if (nIdx === 0) {
            $('#attachmentWarp').show();
        }
    });
    $lists.eq(oIdx).css({
        'left': 0,
        'display': 'block'
    }).animate({
        'left': o,
        'opacity': 0
    }, 300);

    $listDatas.eq(nIdx).css({
        'left': n,
        'display': 'block'
    }).animate({
        'left': 0,
        'opacity': 1
    }, 300);
    $listDatas.eq(oIdx).css({
        'left': 0,
        'display': 'block'
    }).animate({
        'left': o,
        'opacity': 0
    }, 300);
}

//检查对齐
function getSlotsPos() {
    var con = '';
    soltLeftData = [];
    soltTopData = [];
    soltRightData = [];
    soltBottomData = [];
    var screens = desktopData.screens;
    screens.forEach(function(elem, i) {
        var aLeft = [];
        var aTop = [];
        var aRight = [];
        var aBottom = [];
        elem.blocks.forEach(function(e) {
            var left = parseInt(e.x);
            var top = parseInt(e.y);
            var right = parseInt(e.w) + left;
            var bottom = parseInt(e.h) + top;
            aLeft.push(left);
            aTop.push(top);
            aRight.push(right);
            aBottom.push(bottom);
        });
        con += checkSlotLeft(aLeft, i);
        con += checkSlotTop(aTop, i);
        con += checkSlotRight(aRight, i);
        con += checkSlotBottom(aBottom, i);
    });
    if (con) {
        return con;
    } else {
        return false;
    }
}

//检查对齐左
function checkSlotLeft(arr, idx) {
    var min = Math.min.apply(null, arr);
    soltLeftData[idx] = {
        "standard": min,
        "lists": []
    };
    var con = '';
    for (var i = 0, len = arr.length; i < len; i++) {
        if (arr[i] > min && arr[i] < (min + SOLT_OFFSET)) {
            con += '第' + (idx + 1) + '屏最左边一列没对齐\n';
            soltLeftData[idx].lists.push(i);
        }
    }
    return con;
}

//检查对齐上
function checkSlotTop(arr, idx) {
    var min = Math.min.apply(null, arr);
    soltTopData[idx] = {
        "standard": min,
        "lists": []
    };
    var con = '';
    for (var i = 0, len = arr.length; i < len; i++) {
        if (arr[i] > min && arr[i] < (min + SOLT_OFFSET)) {
            con = '第' + (idx + 1) + '屏最上面一行没对齐\n';
            soltTopData[idx].lists.push(i);
        }
    }
    return con;
}

//检查对齐右
function checkSlotRight(arr, idx) {
    var max = Math.max.apply(null, arr);
    soltRightData[idx] = {
        "standard": max,
        "lists": []
    };
    var con = '';
    for (var i = 0, len = arr.length; i < len; i++) {
        if (arr[i] < max && arr[i] > (max - SOLT_OFFSET)) {
            con = '第' + (idx + 1) + '屏最右边一列没对齐\n';
            soltRightData[idx].lists.push(i);
        }
    }
    return con;
}

//检查对齐下
function checkSlotBottom(arr, idx) {
    var max = Math.max.apply(null, arr);
    soltBottomData[idx] = {
        "standard": max,
        "lists": []
    };
    var con = '';
    for (var i = 0, len = arr.length; i < len; i++) {
        if (arr[i] < max && arr[i] > (max - SOLT_OFFSET)) {
            con = '第' + (idx + 1) + '屏最下面一行没对齐\n';
            soltBottomData[idx].lists.push(i);
        }
    }
    return con;
}

//设置对齐
function setSlotOffset() {
    var screens = $('#screenWarp > ul > li');
    $('.selectBlock').removeClass('selectBlock');
    var i = 0,
        len = screens.length,
        slots = null,
        l = 0,
        j = 0,
        idx = 0,
        $slot = null,
        val = 0;
    for (i = 0; i < len; i++) {
        slots = $(screens[i]).find('.screen-block');
        l = soltLeftData[i].lists.length;
        for (j = 0; j < l; j++) {
            idx = soltLeftData[i].lists[j];
            $(slots[idx]).css('left', soltLeftData[i].standard);
            desktopData.screens[i].blocks[idx].x = soltLeftData[i].standard;
        }
    }
    for (i = 0; i < len; i++) {
        slots = $(screens[i]).find('.screen-block');
        l = soltTopData[i].lists.length;
        for (j = 0; j < l; j++) {
            idx = soltTopData[i].lists[j];
            $(slots[idx]).css('top', soltTopData[i].standard);
            desktopData.screens[i].blocks[idx].y = soltTopData[i].standard;
        }
    }
    for (i = 0; i < len; i++) {
        slots = $(screens[i]).find('.screen-block');
        l = soltRightData[i].lists.length;
        for (j = 0; j < l; j++) {
            idx = soltRightData[i].lists[j];
            $slot = $(slots[idx]);
            val = soltRightData[i].standard - $slot.width();
            $slot.css('left', val);
            desktopData.screens[i].blocks[idx].x = val;
        }
    }
    for (i = 0; i < len; i++) {
        slots = $(screens[i]).find('.screen-block');
        l = soltBottomData[i].lists.length;
        for (j = 0; j < l; j++) {
            idx = soltBottomData[i].lists[j];
            $slot = $(slots[idx]);
            val = soltBottomData[i].standard - $slot.height();
            $slot.css('top', val);
            desktopData.screens[i].blocks[idx].y = val;
        }
    }
}

$('#addLogo').on('click', function(){
    var con = getLogoHtml();
    $('#logoModal .my-form').append(con);
});

$('#logoModal').on('click', '.delLogo', function(){
    if($('.delLogo').length === 1){
        alert('请至少上传一个LOGO');
        return false;
    }
    $(this).parent().remove();
});

//生成上传LOGO控件
function getLogoHtml(){
    return  '<div class="form-group" style="position: relative;">'+
                '<label for="desc">&nbsp;</label>'+
                '<input type="text" class="form-control fileShow" placeholder="请上传LOGO"  readonly="readonly" style="width: 65%;margin-left: 3px;">'+
                '<input type="button" class="fileBtn button" value="浏览" style="width:15%;position: absolute;right: 94px;top: 5px;">'+
                '<input type="file" accept="image/*" style="width:0px;height: 0;" class="fileHide" style="width: 65%;">'+
                '<button type="button" class="close delLogo" style="position: absolute;top: 6px;right: 48px;font-size: 20px;border-radius: 50%;border: 1px solid #0E0D0D;width: 22px;">×</button>'+
            '</div>';
}

//监听文件上传按钮
function listenFile(id) {
    $(id).on('click', '.fileBtn', function() {
        $(this).siblings('.fileHide').trigger('click');
    });
    $(id).on('change', '.fileHide', function() {
        var $this = $(this);
        $this.siblings('.fileShow').val($this.val());
    });
}

//创建快捷键表格
function createQuickTable(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        var action = arr.action || '--';
        var component = arr.component || '--';
        var clsName = arr.clsName || '--';
        var detailName = arr.detailName || '--';
        var appName = arr.appName || '--';
        var uri = arr.uri || '--';
        var sid = arr.sid || '--';
        var extraData = arr.extraData || '--';
        var keyCode = {"131": "F1","132": "F2","133": "F3","134": "F4","135": "F5","136": "F6","137": "F7","138": "F8","176": "Setting"}[arr.keyCode] || arr.keyCode;
        dataArr.push([keyCode + '-' + arr.keyCode, arr.type, appName, detailName, uri, sid, extraData]);
    }
    myDataTable('#quickTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'键值','width':'8%', 'targets':0},
            {'title':'类型','width':'10%', 'targets':1},
            {'title':'应用名称','width':'12%', 'targets':2},
            {'title':'详情页名称','width':'12%', 'targets':3},
            {'title':'uri','width':'18%', 'targets':4},
            {'title':'sid','width':'8%', 'targets':5},
            {'title':'附加数据','width':'8%', 'targets':6},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            var keyCode = aData[0].split('-');
            $('td:eq(0)', nRow).html(keyCode[0]);
            if(aData[6] !== '--'){
                tableTdIcon(6, nRow, 'list');
            }
            $('td:eq(0)', nRow).data({
                "id": keyCode[1],
                "key-val": aData[6]
            });
        }
    });
    initToolBar('#quickTable', [
        myConfig.backBtn,
        myConfig.addBtn,
        myConfig.editBtn,
        myConfig.delBtn,
        '<a class="btn my-btn btn-primary saveBtn" href="javascript:"><i class="fa fa-save icon-white"></i>&nbsp;保存为模板</a>'
    ]);

    myData.keyCode = null;
}

listenToolbar('edit', editQuickKey, '#quickTable');
listenToolbar('add', addQuickKey, '#quickTable');
listenToolbar('del', delQuickKey, '#quickTable');
listenToolbar('back', backQuickKey, '#quickTable');
listenToolbar('save', saveQuickKey, '#quickTable');

function sleectShortCut(data, name){
    var arr = data.extra;
    var con = '<option value="空模板">空模板</option>';
    var $select = $('#shortCutSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '" >' + arr[i].name + '</option>';
    }
    if(name){
        $select.html(con);
        $select.find('option[data-name="'+ name +'"]').prop('selected', true);
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "200px"
        });
    }else{
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "200px"
        });
    }
}

$('#shortCutSelect').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    if(val === '空模板'){
        desktopData.shortCutConfig = [];
        createQuickTable(desktopData.shortCutConfig);
    }else{
        AjaxGet('/desktop/shortCutsLists?id=' + val, function(data){
            data.extra.forEach(function(e, i){
                delete data.extra[i].id;
            });
            desktopData.shortCutConfig = data.extra;
            createQuickTable(data.extra);
        });
    }
});

function editQuickKey(){
    if (myData.keyCode) {
        clearQuickData();
        $('#quickKeyModal h4').text('修改');
        AjaxGet('/desktop/actionAppLists', function(data){
            selectApp(data, $('#jumpAppQuick'), $('#jumpDetailQuick'));
            setQuickData();
            $('#quickKeyModal').modal('show');
        });
    } else {
        alert('请选择快捷键！');
    }
}

function addQuickKey(){
    clearQuickData();
    $('#quickKeyModal h4').text('添加');
    AjaxGet('/desktop/actionAppLists', function(data){
        selectApp(data, $('#jumpAppQuick'), $('#jumpDetailQuick'));
        $('#quickKeyModal').modal('show');
    });
}

function delQuickKey(){
    if (myData.keyCode) {
        if (confirm('确定删除？')) {
            for(var i = 0, len = desktopData.shortCutConfig.length; i < len; i++){
                if(desktopData.shortCutConfig[i].keyCode == myData.keyCode){
                    desktopData.shortCutConfig.splice(i, 1);
                    break;
                }
            }
            createQuickTable(desktopData.shortCutConfig);
        }
    } else {
        alert('请选择快捷键！');
    }
}

//保存为模板
function saveQuickKey(){
    var name = prompt("请输入模板名称", "");
    if(name === null){
        return false;
    }

    if(!name || name == ' '){
        alert('请输入模板名称');
        return false;
    }
    var data = {"name": name, "extra": desktopData.shortCutConfig};
    AjaxPost('/desktop/addAllShortCutsItems', data, function(){
        alert('保存成功');
        createQuickTable(desktopData.shortCutConfig);
        AjaxGet('/desktop/shortCutsLists', function(shortCutsData){
            sleectShortCut(shortCutsData, name);
        });
    });
}

//返回桌面配置
function backQuickKey(){
    $('#quickContent').hide();
    $('#myLayout').show();
}

//清空快捷键对话框
function clearQuickData(){
    $('#keyCodeQuick').val('请选择键值');
    $('#uriValQuick').val('');
    $('#sidValQuick').val('');
    $('#jumpTypeQuick input:eq(0)').trigger('click');
}

//设置快捷键对话框数据
function setQuickData(){
    var data = {};
    for(var i = 0, len = desktopData.shortCutConfig.length; i < len; i++){
        if(desktopData.shortCutConfig[i].keyCode == myData.keyCode){
            data = desktopData.shortCutConfig[i];
        }
    }

    $('#keyCodeQuick').val(data.keyCode);
    if(data.type === 'URI'){
        $('#jumpTypeQuick input:eq(1)').trigger('click');
        $('#uriValQuick').val(data.uri);
    }else if(data.type === 'SCREEN'){
        $('#jumpTypeQuick input:eq(2)').trigger('click');
        $('#sidValQuick').val(data.sid);
    }else{
        $('#jumpTypeQuick input:eq(0)').trigger('click');
        $('#jumpType > input:eq(0)').trigger('click');
        var $jumpApp = $('#jumpAppQuick');
        var optionA = $jumpApp.find('option').filter('[data-name="' + data.appName + '"]');
        optionA.prop("selected", true);
        $jumpApp.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });

        if (data.type !== 'APP') {
            $jumpApp.trigger('change', data.detailName);
        } else {
            $jumpApp.trigger('change');
        }
    }
}

//跳转类型变化事件
$('#jumpTypeQuick input').on('click', function(){
    var $this = $(this);
    var val = $this.val();
    $this.prop('checked', true);
    $('.quick-key').hide();
    if(val === 'APP'){
        var $jumpApp = $('#jumpAppQuick');
        $jumpApp.parent().show();
        if($jumpApp.val() !== '请选择跳转应用'){
            $('#jumpDetailQuick').parent().show();
        }
    }else if(val === 'URI'){
        $('#uriValQuick').parent().show();
    }else if(val === 'SCREEN'){
        $('#sidValQuick').parent().show();
    }
});

//跳转应用变化时显示跳转详情页
$('#jumpAppQuick').on('change', function(e, name) {
    var id = $(this).val();
    var $select = $('#jumpDetailQuick');
    if (id === '请选择跳转应用') {
        $select.parent().hide();
        $select.html('<option value="请选择跳转详情页">请选择跳转详情页</option>');
        return false;
    }

    selectDetail($select, id, name);
});

//提交快捷键事件
$('#subQuickKey').on('click', function(){
    var keyCode = $('#keyCodeQuick').val();
    var type = $('#jumpTypeQuick input:checked').val();
    var title = $('#quickKeyModal h4').text();
    var data = {};



    if(type === 'URI'){
        var uri = $('#uriValQuick').val();
        if (uri == ' ' || !uri) {
            alert('请输入uri');
            return false;
        }
        data.uri = uri;
    }else if(type === 'SCREEN'){
        var sid = $('#sidValQuick').val();
        if (sid == ' ' || !sid) {
            alert('请输入sid');
            return false;
        }
        data.sid = sid;
    }else if(type === 'APP'){
        var $jumpApp = $('#jumpAppQuick');
        if ($jumpApp.val() == '请选择跳转应用') {
            alert('请选择跳转应用');
            return false;
        }
        var appData = $jumpApp.data('_' + $jumpApp.val());
        var $jumpDetail = $('#jumpDetailQuick');
        if ($jumpDetail.val() == '请选择跳转详情页') {
            type = 'APP';
            data.pkgName = appData.pkgName;
            data.appName = appData.appName;
        } else {
            //修改 2016.4.19
            var detailDate = $jumpDetail.data('_' + $jumpDetail.val());
            data = detailDate;
            type = detailDate.actionType;
            data.appName = appData.appName;

            /*data.appName = appData.appName;
            var detailDate = $jumpDetail.data('_' + $jumpDetail.val());
            data.extraData = detailDate.extraData;
            type = detailDate.actionType;
            data.detailName = detailDate.detailName;
            if (detailDate.actionType === 'ACTION') {
                data.action = detailDate.action;
            } else if (detailDate.actionType === 'COMPONENT') {
                data.component = detailDate.component;
                data.clsName = detailDate.clsName;
            }*/
        }
    }

    if (keyCode == '请选择键值' || !keyCode) {
        alert('请选择键值');
        return false;
    }

    data.keyCode = keyCode;

    data.type = type;
    var i = 0, len = 0;

    if(title === '添加'){
        if(!desktopData.shortCutConfig){
            desktopData.shortCutConfig = [];
        }
        len = desktopData.shortCutConfig.length;
        for(; i < len; i++){
            if(desktopData.shortCutConfig[i].keyCode == keyCode){
                alert('该快捷键已存在');
                return false;
            }
        }
        desktopData.shortCutConfig.push(data);
    }else if(title === '修改'){
        len = desktopData.shortCutConfig.length;
        for(; i < len; i++){
            if(desktopData.shortCutConfig[i].keyCode == myData.keyCode){
                desktopData.shortCutConfig[i] = data;
                break;
            }
        }
    }

    createQuickTable(desktopData.shortCutConfig);
    $('#quickKeyModal').modal('hide');
});

//创建附件数据列表
function createKeyVal(data){
    $('#jumpExtra').nextAll().remove();
    var len = data.length;
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
        var arr = data[i];
        var myType = '&nbsp;';
        if(arr.type){
            myType = type[arr.type];
        }
        con +=  '<div class="form-group" style="margin-bottom: 2px;">' +
                    '<label></label>' +
                    '<span style="display: inline-block;width: 20%;padding: 7px 10px;border: 1px solid #ccc;word-wrap: break-word;">' + arr.key + '</span>' +
                    '&nbsp;=&nbsp;' +
                    '<span style="display: inline-block;width: 20%;padding: 7px 10px;border: 1px solid #ccc;word-wrap: break-word;">' + arr.value + '</span>' +
                    '&nbsp;&nbsp;' +
                    '<span style="display: inline-block;width: 20%;padding: 7px 10px;border: 1px solid #ccc;word-wrap: break-word;">' + myType + '</span>' +
                '</div>';
    }
    $('#jumpExtra').after(con);
}

//限制绑定应用
$('#dataModal .form-group > span.lbl').on('click', function() {
    var val = $('#appType input:checked').val();
    var layoutType = $('#layoutType').val();
    if (val === 'false') {
        if($('#editType input:checked').val() === 'false' && (layoutType === 'APP' || layoutType === 'APP_CENTER_IMG_BOTTOM_TEXT')){
            $('#appType input:eq(0)').trigger('click');
        }else{
            $('.appType').hide();
        }
        return false;
    }
    $(this).prev('input').trigger('click');
});

//单个控件拖拽
function listenSingleDrag(id, info){
    $(id)
    .on("mousedown", function(ev) {
        var oDiv = this;
        var e = ev||event;
        var $this = $(this);
        var scale = 100 / $('#desktopProportion').val();    //获取显示比例
        disX=e.clientX * scale -oDiv.offsetLeft;
        disY=e.clientY * scale -oDiv.offsetTop;
        $('.selectBlock').removeClass("selectBlock");
        $('.selectHandle').removeClass("selectHandle");
        if (!HANDLE_IS_ABLE) {
            return false;
        }
        $('#slotInfo h4').text(info);
        $this.addClass("selectHandle");
        selectedHandle();
        refreshSlotPos({
            "x": parseInt($this.css('left')),
            "y": parseInt($this.css('top')),
            "w": $this.width(),
            "h": $this.height(),
            "bg": ''
        });
        $(document).off('mousemove').on('mousemove', function(ev){
            var e=ev||event;
            var l=parseInt(e.clientX * scale -disX);
            var t=parseInt(e.clientY * scale -disY);
            if(l<0)
            {
                l=0;
            }else if(l > 1280-oDiv.offsetWidth){
                l = 1280-oDiv.offsetWidth;
            }
            if(t<0)
            {
                t=0;
            }
            else if(t > 720-oDiv.offsetHeight)
            {
                t = 720-oDiv.offsetHeight;
            }

            $this.css('left', l);
            $this.css('top', t);
            refreshSlotPos({
                "x": l,
                "y": t,
                "w": $this.width(),
                "h": $this.height(),
                "bg": ''
            });
            setHandleXY($this, 'x', l);
            setHandleXY($this, 'y', t);
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
            if (cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;
            $(document).off('mouseup');
        });
        return false;
    });
}

function listenMultipleDrag(id, info){
    $(id)
    .on("mousedown", "a", function(ev) {
        var oDiv = this;
        var e = ev||event;
        var $this = $(this);
        var scale = 100 / $('#desktopProportion').val();    //获取显示比例
        disX=e.clientX * scale -oDiv.offsetLeft;
        disY=e.clientY * scale -oDiv.offsetTop;
        $('.selectBlock').removeClass("selectBlock");
        $('.selectHandle').removeClass("selectHandle");
        if (!HANDLE_IS_ABLE) {
            return false;
        }
        $('#slotInfo h4').text(info);
        $this.addClass("selectHandle");
        selectedHandleQuick();
        refreshSlotPos({
            "x": parseInt($this.css('left')),
            "y": parseInt($this.css('top')),
            "w": $this.width(),
            "h": $this.height(),
            "bg": ''
        });
        $(document).off('mousemove').on('mousemove', function(ev){
            var e=ev||event;
            var l=parseInt(e.clientX * scale -disX);
            var t=parseInt(e.clientY * scale -disY);
            if(l<0)
            {
                l=0;
            }else if(l > 1280-oDiv.offsetWidth){
                l = 1280-oDiv.offsetWidth;
            }
            if(t<0)
            {
                t=0;
            }
            else if(t > 720-oDiv.offsetHeight)
            {
                t = 720-oDiv.offsetHeight;
            }

            $this.css('left', l);
            $this.css('top', t);
            refreshSlotPos({
                "x": l,
                "y": t,
                "w": $this.width(),
                "h": $this.height(),
                "bg": ''
            });
            setHandleXY($this, 'x', l);
            setHandleXY($this, 'y', t);
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
            if (cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;
            $(document).off('mouseup');
        });
        return false;
    });
}

function listenGroupMultipleDrag(id, info){
    $(id)
    .on("mousedown", "> div", function(ev) {
        var oDiv = this;
        var e = ev||event;
        var $this = $(this);
        var scale = 100 / $('#desktopProportion').val();    //获取显示比例
        disX=e.clientX * scale -oDiv.offsetLeft;
        disY=e.clientY * scale -oDiv.offsetTop;
        $('.selectBlock').removeClass("selectBlock");
        $('.selectHandle').removeClass("selectHandle");
        if (!HANDLE_IS_ABLE) {
            return false;
        }
        $('#slotInfo h4').text(info);
        $this.addClass("selectHandle");
        selectedHandleQuick();
        refreshSlotPos({
            "x": parseInt($this.css('left')),
            "y": parseInt($this.css('top')),
            "w": $this.width(),
            "h": $this.height(),
            "bg": ''
        });
        $(document).off('mousemove').on('mousemove', function(ev){
            var e=ev||event;
            var l=parseInt(e.clientX * scale -disX);
            var t=parseInt(e.clientY * scale -disY);
            if(l<0)
            {
                l=0;
            }else if(l > 1280-oDiv.offsetWidth){
                l = 1280-oDiv.offsetWidth;
            }
            if(t<0)
            {
                t=0;
            }
            else if(t > 720-oDiv.offsetHeight)
            {
                t = 720-oDiv.offsetHeight;
            }

            $this.css('left', l);
            $this.css('top', t);
            refreshSlotPos({
                "x": l,
                "y": t,
                "w": $this.width(),
                "h": $this.height(),
                "bg": ''
            });
            setHandleXY($this, 'x', l);
            setHandleXY($this, 'y', t);
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
            if (cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;
            $(document).off('mouseup');
        });
        return false;
    });
}

//全局快捷坑位拖拽事件
function listenGobalSlotDrag(id, info){
    $(id)
    .on("mousedown", ".screen-block", function(ev) {
        var oDiv = this;
        var e = ev||event;
        var $this = $(this);
        $('.selectBlock').removeClass("selectBlock");
        // if (!HANDLE_IS_ABLE) {
        //     return false;
        // }
        //按Ctrl，则判断为多选
        if(e.ctrlKey){
            var $selectHandles = $(".selectHandle");
            if($selectHandles.length && $selectHandles.parent().get(0).id !== 'quickSlotWarp'){
                $selectHandles.removeClass("selectHandle");
            }
            $this.toggleClass("selectHandle");
            refreshSlotPos({
                "x": "",
                "y": "",
                "w": "",
                "h": "",
                "bg": ''
            });
        }
        //没按Ctrl，且点击的块不是多选的块之一，则判断为单选
        if(!e.ctrlKey && !$this.hasClass('selectHandle')){
            $('.selectHandle').removeClass("selectHandle");
            var bg = $this.getBackgroundColor();
            refreshSlotPos({
                "x": parseInt($this.css('left')),
                "y": parseInt($this.css('top')),
                "w": $this.width(),
                "h": $this.height(),
                "bg": bg.rgb
            });
            $this.addClass("selectHandle");
            setOpacity(bg.a);
        }
        var scale = 100 / $('#desktopProportion').val();    //获取显示比例
        disX=e.clientX * scale -oDiv.offsetLeft;
        disY=e.clientY * scale -oDiv.offsetTop;
        $('#slotInfo h4').text(info);
        selectedSlotQuick();

        $(document).off('mousemove').on('mousemove', function(ev){
            var e=ev||event;
            var l=parseInt(e.clientX * scale -disX);
            var t=parseInt(e.clientY * scale -disY);
            if(!$(oDiv).hasClass('selectHandle')){
                $(oDiv).addClass('selectHandle');
            }
            if(l<0)
            {
                l=0;
            }else if(l > 1280-oDiv.offsetWidth){
                l = 1280-oDiv.offsetWidth;
            }
            if(t<0)
            {
                t=0;
            }
            else if(t > 720-oDiv.offsetHeight)
            {
                t = 720-oDiv.offsetHeight;
            }

            var selectHandles = $('.selectHandle');
            var nowIdx = $this.index();
            var nowHandle = desktopData.quickEntrySlot.globalItems[nowIdx];
            var offsetX = l -  Number(desktopData.quickEntrySlot.globalItems[nowIdx].x);

            var offsetY = t -  Number(desktopData.quickEntrySlot.globalItems[nowIdx].y);

            var len = selectHandles.length;
            for(var i = 0; i < len; i++){
                var $selectHandle = $(selectHandles[i]);
                var idx = $selectHandle.index();
                desktopData.quickEntrySlot.globalItems[idx].x = Number(desktopData.quickEntrySlot.globalItems[idx].x) + offsetX;
                desktopData.quickEntrySlot.globalItems[idx].y = Number(desktopData.quickEntrySlot.globalItems[idx].y) + offsetY;
                $selectHandle.css({
                    "left": Number(desktopData.quickEntrySlot.globalItems[idx].x),
                    "top": Number(desktopData.quickEntrySlot.globalItems[idx].y)
                });
            }

            var dom = $('#screenWarp li').get(0);
            dom.scrollLeft = l;
            dom.scrollTop = t;
            if(len > 1){
                refreshSlotPos({
                    "x": '',
                    "y": '',
                    "w": '',
                    "h": '',
                    "bg": ''
                });
            }else{
                refreshSlotPos(desktopData.quickEntrySlot.globalItems[nowIdx]);
            }
        });
        $(document).off('mouseup').on('mouseup', function(ev){
            var e = ev || event;
            var cls = e.target.className;
            if (myData.clearSelect) {
                return false;
            }
            $(document).off('mousemove');
            if (cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('block-data') !== -1 || cls.indexOf('block-title') !== -1 || cls.indexOf('icon-quick-slot') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;
            $(document).off('mouseup');
        });
        return false;
    });
}

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
    $('#pageDataWrap').css({
        "transform": "scale("+ scale +")",
        "top": 45 - offsetHeight / 2,
        "left" : 130 - offsetWidth / 2
    });

    var $rightDiv = $('#slotInfo').parent();
    $rightDiv.css('left', 1410 - offsetWidth);
    var $rightDataDiv = $('#backDesktop').parent().parent();
    $rightDataDiv.css({
        'left': 1410 - offsetWidth,
        'height': 700 - offsetWidth / 2 - 16
    });

    var $layout = $('#myLayout');
    $layout.width(1610 - offsetWidth);
    var $layoutData = $('#myDataLayout');
    $layoutData.width(1610 - offsetWidth);

    var $bottomDiv = $('#screenInfoWrap');
    $bottomDiv.css({
        "width": 1280 * scale,
        "bottom": offsetWidth / 1.8
    });

    if(val === 60){
        $('#screenSelect').css('left', 0);
    }else{
        $('#screenSelect').css('left', '130px');
    }

    if(val === 100){
        $('#screenSelect').siblings('.my-close').css('left', '-205px');
    }else if(val === 80){
        $('#screenSelect').siblings('.my-close').css('left', '-153px');
    }else if(val === 60){
        $('#screenSelect').siblings('.my-close').css('left', 0);
    }else if(val === 120){
        $('#screenSelect').siblings('.my-close').css('left', '-200px');
    }

    $('#setAlignment').css('transform', 'scale('+ 100 / val +')');
});

//切换运营坑位组
$('#slotGroup').on('change', function(){
    var $this = $(this);
    id = $this.val();
    var screenLists = $('#screenDataWarp li');
    var blockLists = null;
    var $block = null;
    var data = null;
    var idArr = [];
    var blockArr = [];
    var i = 0, j = 0;
    var idx = getScreenIdx();
    blockLists = $(screenLists[idx]).find('.screen-block');
    for(j = blockLists.length; j--;){
        $block = $(blockLists[j]);
        data = desktopData.screens[idx].blocks[j];
        if (data.operation === 'true'){
            desktopData.screens[idx].slotGroupId = id;
            idArr.push(data.slotId);
            blockArr.push($block);
        }
    }
    AjaxPost('/desktop/operationSlotArrLists', {"slotGroupId": id, "slotIDArr": idArr}, function(soltData){
        for(i = blockArr.length; i--;){
            $block = blockArr[i];
            data = soltData.extra[i];
            $block.removeClass('block-relation');
            $block.find('.block-title').remove();
            if(data.id){
                var w = 0 | $block.width();
                var h = 0 | $block.height();
                if(w == h) {
                    data.pic = data.pic1;
                }else if(w  > h) {
                    data.pic = data.pic2;
                }else if(w < h) {
                    data.pic = data.pic3;
                }
                setBlockDataShow($block, data);
            }else{
                $block.addClass('block-relation');
                $block.css({ //关联失败
                    'background-image': 'url(img/icon_operation_bad.png)',
                    'background-size': '60%',
                    'background-repeat': 'no-repeat',
                    'background-position': 'center',
                });
            }
        }
        idArr = blockArr = screenLists = blockLists = data = $block = null;
    });
});

$('#screenSelect').on('click', 'span.lbl', function(){
    $(this).prev('input').trigger('click');
});


//屏向前移事件
$('#setScreenFront').on('click', function() {
    var $icon = $('#navWarp a.active');
    var idx = $icon.index();
    var nIdx = idx - 1;
    if (idx === 0 || !$icon.length) {
        alert('不能向前移');
        return false;
    }
    var tempData = desktopData.screens[idx];
    myData.slots = desktopData.screens[idx].blocks;
    desktopData.screens.splice(idx, 1);
    desktopData.screens.splice(nIdx, 0, tempData);
    $('#navWarp a:eq(' + nIdx + ')').before($('#navWarp a:eq(' + idx + ')'));
    $('#navDataWarp a:eq(' + nIdx + ')').before($('#navDataWarp a:eq(' + idx + ')'));
    $('#screenWarp li:eq(' + nIdx + ')').before($('#screenWarp li:eq(' + idx + ')'));
    $('#screenDataWarp li:eq(' + nIdx + ')').before($('#screenDataWarp li:eq(' + idx + ')'));
});

//屏向后移事件
$('#setScreenBehind').on('click', function() {
    var $icon = $('#navWarp a.active');
    var idx = $icon.index();
    var nIdx = idx + 1;
    var len = $('#navWarp a').length;
    if (nIdx === len || !$icon.length) {
        alert('不能向后移');
        return false;
    }
    var tempData = desktopData.screens[idx];
    myData.slots = desktopData.screens[idx].blocks;
    desktopData.screens.splice(idx, 1);
    desktopData.screens.splice(nIdx, 0, tempData);
    $('#navWarp a:eq(' + nIdx + ')').after($('#navWarp a:eq(' + idx + ')'));
    $('#navDataWarp a:eq(' + nIdx + ')').after($('#navDataWarp a:eq(' + idx + ')'));
    $('#screenWarp li:eq(' + nIdx + ')').after($('#screenWarp li:eq(' + idx + ')'));
    $('#screenDataWarp li:eq(' + nIdx + ')').after($('#screenDataWarp li:eq(' + idx + ')'));
});

$('#slotGroup').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    desktopData.screens[getScreenIdx()].slotGroupId = val;
});

function listenQuickDrag(id, info){
    $(id)
    .on("mousedown", "a", function(ev) {
        var oDiv = this;
        var e = ev||event;
        var $this = $(this);
        $('.selectBlock').removeClass("selectBlock");
        if (!HANDLE_IS_ABLE) {
            return false;
        }
        //按Ctrl，则判断为多选
        if(e.ctrlKey){
            var $selectHandles = $(".selectHandle");
            if($selectHandles.length && $selectHandles.parent().get(0).id !== 'quickWarp'){
                $selectHandles.removeClass("selectHandle");
            }
            $this.toggleClass("selectHandle");
            refreshSlotPos({
                "x": "",
                "y": "",
                "w": "",
                "h": "",
                "bg": ''
            });
        }
        //没按Ctrl，且点击的块不是多选的块之一，则判断为单选
        if(!e.ctrlKey && !$this.hasClass('selectHandle')){
            $('.selectHandle').removeClass("selectHandle");
            refreshSlotPos({
                "x": parseInt($this.css('left')),
                "y": parseInt($this.css('top')),
                "w": $this.width(),
                "h": $this.height(),
                "bg": ''
            });
            $this.addClass("selectHandle");
        }
        var scale = 100 / $('#desktopProportion').val();    //获取显示比例
        disX=e.clientX * scale -oDiv.offsetLeft;
        disY=e.clientY * scale -oDiv.offsetTop;
        $('#slotInfo h4').text(info);
        selectedHandleQuick();

        $(document).off('mousemove').on('mousemove', function(ev){
            var e=ev||event;
            var l=parseInt(e.clientX * scale -disX);
            var t=parseInt(e.clientY * scale -disY);
            if(!$(oDiv).hasClass('selectHandle')){
                $(oDiv).addClass('selectHandle');
            }
            if(l<0)
            {
                l=0;
            }else if(l > 1280-oDiv.offsetWidth){
                l = 1280-oDiv.offsetWidth;
            }
            if(t<0)
            {
                t=0;
            }
            else if(t > 720-oDiv.offsetHeight)
            {
                t = 720-oDiv.offsetHeight;
            }

            var selectHandles = $('.selectHandle');
            var nowIdx = $this.index();
            var nowHandle = desktopData.quickEntry.extraData[nowIdx];
            var offsetX = l -  Number(desktopData.quickEntry.extraData[nowIdx].itemX);
            var offsetY = t -  Number(desktopData.quickEntry.extraData[nowIdx].itemY);

            var len = selectHandles.length;
            for(var i = 0; i < len; i++){
                var $selectHandle = $(selectHandles[i]);
                var idx = $selectHandle.index();
                desktopData.quickEntry.extraData[idx].itemX = Number(desktopData.quickEntry.extraData[idx].itemX) + offsetX;
                desktopData.quickEntry.extraData[idx].itemY = Number(desktopData.quickEntry.extraData[idx].itemY) + offsetY;
                $selectHandle.css({
                    "left": Number(desktopData.quickEntry.extraData[idx].itemX),
                    "top": Number(desktopData.quickEntry.extraData[idx].itemY)
                });
            }

            var dom = $('#screenWarp li').get(0);
            dom.scrollLeft = l;
            dom.scrollTop = t;
            if(len > 1){
                refreshSlotPos({
                "x": '',
                "y": '',
                "w": '',
                "h": '',
                "bg": ''
            });
            }else{
                refreshSlotPos({
                    "x": l,
                    "y": t,
                    "w": $this.width(),
                    "h": $this.height(),
                    "bg": ''
                });
            }
        });
        $(document).off('mouseup').on('mouseup', function(ev){
            var e = ev || event;
            var cls = e.target.className;
            if (myData.clearSelect) {
                return false;
            }
            $(document).off('mousemove');
            if (cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;
            $(document).off('mouseup');
        });
        return false;
    });
}

//对齐事件
$('#setQuickAlignment').on('click', 'a', function(ev){
    var e = ev || event;
    var type = {};
    if($('#quickSlotWarp .selectHandle').length){
        type = {"x": "x", "y": "y"};
        setQuickSlotAlignment(e.target.text, desktopData.quickEntrySlot.globalItems, type);

    }else{
        setHandleAlignment(e.target.text);
    }
    $('#setQuickAlignment').css({
        "display": 'none'
    });
    return false;
});

//全局快捷坑位对齐
function setQuickSlotAlignment(str, objArr, type){
    var selectBlocks = $('#quickSlotWarp .selectHandle');
    var len = selectBlocks.length;
    var data = [];
    var i = 0, j = 0;
    var $selectBlock = null;
    var idx = 0;
    for(i = 0; i < len; i++){
        $selectBlock = $(selectBlocks[i]);
        idx = $selectBlock.index();
        data.push(objArr[idx]);
    }

    if(str === '横向间隔对齐'){
        for (i = 0; i < len; i++) {
            for (j = i; j < len; j++) {
                if (Number(data[i][type.x]) > Number(data[j][type.x])) {//交换两个元素的位置
                    data[i] = [data[j], data[j] = data[i]][0];
                    selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
                }
            }
        }
        var offsetX = Number(data[1][type.x]) - (Number(data[0][type.x]) + $(selectBlocks[0]).width());
        for (i = 2; i < len; i++){
            data[i][type.x] = (Number(data[i-1][type.x]) + $(selectBlocks[i-1]).width()) + offsetX;
            $(selectBlocks[i]).css('left', data[i][type.x]);
        }
    }else if(str === '纵向间隔对齐'){
        for (i = 0; i < len; i++) {
            for (j = i; j < len; j++) {
                if (Number(data[i][type.y]) > Number(data[j][type.y])) {//交换两个元素的位置
                    data[i] = [data[j], data[j] = data[i]][0];
                    selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
                }
            }
        }
        var offsetY = Number(data[1][type.y]) - (Number(data[0][type.y]) + $(selectBlocks[0]).height());
        for (i = 2; i < len; i++){
            data[i][type.y] = (Number(data[i-1][type.y]) + $(selectBlocks[i-1]).height()) + offsetY;
            $(selectBlocks[i]).css('top', data[i][type.y]);
        }
    }else if(str === '左对齐'){
        data.sort(function(obj1, obj2){
            return Number(obj1[type.x]) - Number(obj2[type.x]);
        });
        var minLeft = Number(data[0][type.x]);
        for (i = len; i--;){
            data[i][type.x] = minLeft;
            $(selectBlocks[i]).css('left', data[i][type.x]);
        }
    }else if(str === '右对齐'){
        for (i = 0; i < len; i++) {
            for (j = i; j < len; j++) {
                if (Number(data[i][type.x]) + $(selectBlocks[i]).width() < Number(data[j][type.x]) + $(selectBlocks[j]).width()) {//交换两个元素的位置
                    data[i] = [data[j], data[j] = data[i]][0];
                    selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
                }
            }
        }
        var maxRight = Number(data[0][type.x]) + $(selectBlocks[0]).width();
        for (i = len; i--;){
            data[i][type.x] = maxRight - $(selectBlocks[i]).width();
            $(selectBlocks[i]).css('left', data[i][type.x]);
        }
    }else if(str === '上对齐'){
        data.sort(function(obj1, obj2){
            return Number(obj1[type.y]) - Number(obj2[type.y]);
        });
        var minTop = Number(data[0][type.y]);
        for (i = len; i--;){
            data[i][type.y] = minTop;
            $(selectBlocks[i]).css('top', data[i][type.y]);
        }
    }else if(str === '下对齐'){
        for (i = 0; i < len; i++) {
            for (j = i; j < len; j++) {
                if (Number(data[i][type.y]) + $(selectBlocks[i]).height() < Number(data[j][type.y]) + $(selectBlocks[j]).height()) {//交换两个元素的位置
                    data[i] = [data[j], data[j] = data[i]][0];
                    selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
                }
            }
        }
        var maxBottom = Number(data[0][type.y]) + $(selectBlocks[0]).height();
        for (i = len; i--;){
            data[i][type.y] = maxBottom - $(selectBlocks[i]).height();
            $(selectBlocks[i]).css('top', data[i][type.y]);
        }
    }
    return false;
}

//快捷入口对齐
function setHandleAlignment(str){
    var selectBlocks = $('.selectHandle');
    var len = selectBlocks.length;
    var data = [];
    var i = 0, j = 0;
    var $selectBlock = null;
    var idx = 0;
    for(i = 0; i < len; i++){
        $selectBlock = $(selectBlocks[i]);
        idx = $selectBlock.index();
        data.push(desktopData.quickEntry.extraData[idx]);
    }

    if(str === '横向间隔对齐'){
        for (i = 0; i < len; i++) {
            for (j = i; j < len; j++) {
                if (Number(data[i].itemX) > Number(data[j].itemX)) {//交换两个元素的位置
                    data[i] = [data[j], data[j] = data[i]][0];
                    selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
                }
            }
        }
        var offsetX = Number(data[1].itemX) - (Number(data[0].itemX) + $(selectBlocks[0]).width());
        for (i = 2; i < len; i++){
            data[i].itemX = (Number(data[i-1].itemX) + $(selectBlocks[i-1]).width()) + offsetX;
            $(selectBlocks[i]).css('left', data[i].itemX);
        }
    }else if(str === '纵向间隔对齐'){
        for (i = 0; i < len; i++) {
            for (j = i; j < len; j++) {
                if (Number(data[i].itemY) > Number(data[j].itemY)) {//交换两个元素的位置
                    data[i] = [data[j], data[j] = data[i]][0];
                    selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
                }
            }
        }
        var offsetY = Number(data[1].itemY) - (Number(data[0].itemY) + $(selectBlocks[0]).height());
        for (i = 2; i < len; i++){
            data[i].itemY = (Number(data[i-1].itemY) + $(selectBlocks[i-1]).height()) + offsetY;
            $(selectBlocks[i]).css('top', data[i].itemY);
        }
    }else if(str === '左对齐'){
        data.sort(function(obj1, obj2){
            return Number(obj1.itemX) - Number(obj2.itemX);
        });
        var minLeft = Number(data[0].itemX);
        for (i = len; i--;){
            data[i].itemX = minLeft;
            $(selectBlocks[i]).css('left', data[i].itemX);
        }
    }else if(str === '右对齐'){
        for (i = 0; i < len; i++) {
            for (j = i; j < len; j++) {
                if (Number(data[i].itemX) + $(selectBlocks[i]).width() < Number(data[j].itemX) + $(selectBlocks[j]).width()) {//交换两个元素的位置
                    data[i] = [data[j], data[j] = data[i]][0];
                    selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
                }
            }
        }
        var maxRight = Number(data[0].itemX) + $(selectBlocks[0]).width();
        for (i = len; i--;){
            data[i].itemX = maxRight - $(selectBlocks[i]).width();
            $(selectBlocks[i]).css('left', data[i].itemX);
        }
    }else if(str === '上对齐'){
        data.sort(function(obj1, obj2){
            return Number(obj1.itemY) - Number(obj2.itemY);
        });
        var minTop = Number(data[0].itemY);
        for (i = len; i--;){
            data[i].itemY = minTop;
            $(selectBlocks[i]).css('top', data[i].itemY);
        }
    }else if(str === '下对齐'){
        for (i = 0; i < len; i++) {
            for (j = i; j < len; j++) {
                if (Number(data[i].itemY) + $(selectBlocks[i]).height() < Number(data[j].itemY) + $(selectBlocks[j]).height()) {//交换两个元素的位置
                    data[i] = [data[j], data[j] = data[i]][0];
                    selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
                }
            }
        }
        var maxBottom = Number(data[0].itemY) + $(selectBlocks[0]).height();
        for (i = len; i--;){
            data[i].itemY = maxBottom - $(selectBlocks[i]).height();
            $(selectBlocks[i]).css('top', data[i].itemY);
        }
    }
    return false;
}

//事件天气下拉框
function selectTimeWeather(){
    $('#timeWeatherStyle').trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "60%"
    });
}

$('#timeWeather').on('mouseenter', '.chosen-results > li', function() {
    var $this = $(this);
    var src = 'img/timeWeather/' + $this.text() + '.png';
    var left = 850;
    var color = '#428bca';
    $('#timeWeatherModal .my-image').attr('src', 'img/loading.gif').css({
        'opacity': 1,
        'zIndex': 1041,
        'left': left + 'px',
        'top': '30px',
        'background-color': color
    });
    preImage(src, function(error) {
        if (error) { //图片加载失败，则显示默认图片
            src = 'img/error.png';
            this.width = 125;
        }
        $('#timeWeatherModal .my-image').attr('src', src).css({
            'left': left + 'px',
            'top': '30px',
        });
    });
}).on('mouseleave', '.chosen-results > li', function() {
    $('.my-image').attr('src', '').css({
        'opacity': 0,
        'zIndex': -1,
    });
});

$('#timeWeatherStyle').on('change', function () {
    myData.styleKey = 1;
    $('#pic').show();
    $('#pic').children().remove();
    $("#pic").css("padding-top", "130px");
    $("#pic").css("display", "block");
    $('#pic').append('<img style="background-color:#428bca;" src="img/timeWeather/' + $(this).val().toString() +'.png">');
});
//选择要复制的桌面
function copyDesktopSlots(){
    if (myData.checkedLists.length === 1) {
        AjaxWhen([
            AjaxGet('/desktop/desktopGroupLists', selectCopyDesktop, true),
            AjaxGet('/desktop/desktopLists', createCopyDesktop, true)
        ], function(){
            $('#copyDesktopModal').modal('show');
        });
    }else{
        alert('请选择一个桌面');
    }
}
//筛选桌面组
$('#copyDesktop').on('change', function(){
    var $this = $(this);
    var val = $this.val();
    var url = '/desktop/desktopLists';
    if(val !== '不分组'){
        url += '?groupId=' + val;
    }
    AjaxGet(url, createCopyDesktop);
});
//生成桌面列表
function createCopyDesktop(data){
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.id, arr.name]);
    }

    myDataTable('#releaseTable', {
        "data": dataArr,
        "paging": false,
        "stateSave": false,
        "order": [
            [1, "desc"]
        ],
        "columnDefs": [
        {
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '30%',
            'targets': 0,
            "orderable": false
        },
        {
            'title': '桌面名称',
            'width': '70%',
            'targets': 1
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            $('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>').data({
                "id": aData[0]
            });
        }
    });
    $('#releaseTable_filter label').css('right', '125px');
}
//生成桌面组
function selectCopyDesktop(data){
    var arr = data.extra;
    var con = '<option value="不分组">不分组</option>';
    var $select = $('#copyDesktop');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }

    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}





//提交桌面布局
$('#subCopyDesktop').on('click', function() {
    var id = myData.checkedLists[0];
    var copyDesktopIDList = [];
    var checkeds = $('#releaseTable tbody tr td input:checked');
    var len = checkeds.length;
    if (len === 0) {
        alert('请选择要复制到的桌面');
        return false;
    }
    for (var i = 0; i < len; i++) {
        var $td = $(checkeds[i]).parents('td');
        copyDesktopIDList.push($td.data('id') + '');
    }
    AjaxPost('/desktop/copyLayoutToDesktop', {"desktops":copyDesktopIDList, "copyLayoutDesktopId":id}, function() {
                alert('复制成功');
                $('#copyDesktopModal').modal('hide');
            });

});


$('#setMessageConfig').on('click', function() {
    if (desktopData.messageConfig) {
        $('#messageSwitch input:eq(0)').trigger('click');
        $('#messageConfigX').val($('#msgWarp').css('left').split('px')[0]);
        $('#messageConfigY').val($('#msgWarp').css('top').split('px')[0]);
        $('#messageConfigWidth').val($('#msgWarp').css('width').split('px')[0]);
        $('#messageConfigFontSize').val($('#msgWarp').css('font-size').split('px')[0]);
    }else{
        $('#messageConfigX').val('');
        $('#messageConfigY').val('');
        $('#messageConfigWidth').val('');
        $('#messageConfigFontSize').val('');
    }
    $('#messageSwitch').on('change', function() {
        //$('#logoShow input:eq(0)').prop('checked', true);
        console.log($('#messageSwitch').find('input:checked').val());
        if ($('#messageSwitch').find('input:checked').val() === 'false') {
            $('#messageConfigX').parent().hide();
            $('#messageConfigY').parent().hide();
            $('#messageConfigWidth').parent().hide();
            $('#messageConfigFontSize').parent().hide();
        }else{
            $('#messageConfigX').parent().show();
            $('#messageConfigY').parent().show();
            $('#messageConfigWidth').parent().show();
            $('#messageConfigFontSize').parent().show();
        }
    });
   $('#messageConfigModal').modal('show');     
});

$('#subMessageConfig').on('click', function() {
    var messageConfigX = $('#messageConfigX').val();
    var messageConfigY = $('#messageConfigY').val();
    var messageConfigWidth = $('#messageConfigWidth').val();
    var messageConfigFontSize = $('#messageConfigFontSize').val();
if ($('#messageSwitch').find('input:checked').val() === 'true') {
    if (!(/^\d+$/.test(messageConfigX))) {
        alert('请输入正确的横坐标！');
        return;
    }
    if (!(/^\d+$/.test(messageConfigY))) {
        alert('请输入正确的纵坐标！');
        return;
    }
    if (!(/^\d+$/.test(messageConfigWidth))) {
        alert('请输入正确的宽度！');
        return;
    }
    if (!(/^\d+$/.test(messageConfigFontSize))) {
        alert('请输入正确的字体大小！');
        return;
    }
    $('#msgWarp').html('');
    $('#msgWarp').html('<div class="handle-font" style="color: #fff;line-height: 40px;height: 40px;font-family: 微软雅黑;">消息控件</div>').css({
            "top":  messageConfigY+'px',
            "left": messageConfigX+'px',
            "font-size": messageConfigFontSize+'px',
            "width": messageConfigWidth+'px'

    });
    desktopData.messageConfig = {
        'x': parseInt(messageConfigX),
        'y': parseInt(messageConfigY),
        'width': parseInt(messageConfigWidth),
        'fontSize': parseInt(messageConfigFontSize)
    }
}else{
    desktopData.messageConfig = null;
    $('#msgWarp').html('');
}
    $('#messageConfigModal').modal('hide'); 
});



// 快捷坑位变化事件，防冲突重新一遍（待优化）
// //是否为可替换变化
// $('#editTypeQuickEntry > input').on('click', function() {
//     var $this = $(this);
//     var val = $this.val();
//     // var layoutType = $('#layoutType').val();
//     $this.prop('checked', true);
//     if (val === 'false') {
//         $('#appTypeQuickEntry input:eq(0)').trigger('click');
//     }else{
//         $('#appTypeQuickEntry input:checked').trigger('click');
//     }
// });

// //跳转类型变化事件
// $('#jumpTypeQuickEntry > input').on('click', function() {
//     var $this = $(this);
//     var val = $this.val();
//     $this.prop('checked', true);
//     if (val === 'APP') {
//         $('#uriNameQuickEntry').parent().hide();
//         $('#uriValQuickEntry').parent().hide();
//         $('#jumpAppQuickEntry').parent().show();
//         $('#jumpDetailQuickEntry').parent().show();
//     } else if (val === 'URI') {
//         $('#handleType').parent().hide();
//         $('#jumpAppQuickEntry').parent().hide();
//         $('#jumpDetailQuickEntry').parent().hide();
//         $('#uriNameQuickEntry').parent().show();
//         $('#uriValQuickEntry').parent().show();
//     }
// });

// //是否绑定应用变化
// $('#appTypeQuickEntry > input').on('click', function() {
//     var $this = $(this);
//     var val = $this.val();
//     // var layoutType = $('#layoutType').val();
//     $this.prop('checked', true);
//     if (val === 'true') {
//         $('.appTypeQuick').show();
//     } else if (val === 'false') {
//         if($('#editTypeQuickEntry input:checked').val() === 'false'){
//             $('#appTypeQuickEntry input:eq(0)').trigger('click');
//         }else{
//             $('.appTypeQuickEntry').hide();
//         }
//     }
// });

// $('#quickSlotModal .form-group > span.lbl').on('click', function() {
//     var val = $('#appTypeQuickEntry input:checked').val();
//     // var layoutType = $('#layoutType').val();
//     if (val === 'false') {
//         if($('#editTypeQuickEntry input:checked').val() === 'false'){
//             $('#appTypeQuickEntry input:eq(0)').trigger('click');
//         }else{
//             $('.appTypeQuick').hide();
//         }
//         return false;
//     }
//     $(this).prev('input').trigger('click');
// });
