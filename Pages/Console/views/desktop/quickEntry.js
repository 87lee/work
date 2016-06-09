//@ sourceURL=desktop.quickEntry.js
var myData = {};
var shiftKeyNum = 10;
$(function() {
    updateQuickScreen();

    listenPic($('#add_pos2'));

    listenchoose();
});

//更新快捷入口
function updateQuickScreen() {
    myData.quickID = null;
    myData.quickName = null;
    AjaxGet('/desktop/quickEntryLists', selectQuick);
}

//生成快捷入口下拉框选项
function selectQuick(data) {
    var arr = data.extra;
    var con = '<option value="空快捷入口">空快捷入口</option>';
    var $select = $('#quickSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '" >' + arr[i].name + '</option>';
    }
    if (!myData.newQuickName) {
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        $('#screenSelect').show();
    } else {
        var option = $select.html(con).find('option').filter('[data-name="' + myData.newQuickName + '"]');
        option.prop("selected", true);
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        $('#screenSelect').show();
        myData.newQuickName = null;
    }
    createQuickList(arr);
}

$('#quickSelect').on('change', function() { //监听选择快捷入口事件
    myData.quickID = $(this).val();
    myData.quickName = $(this).find("option:selected").text();

    myData.extra = [];
    if (myData.quickID === '空快捷入口') {
        $("#editQuick").hide();
        $("#delQuick").hide();
        $("#addIcon").hide();
        $("#editIcon").hide();
        $("#delIcon").hide();
    } else {
        $("#editQuick").show();
        $("#delQuick").show();
        $("#addIcon").show();
        $("#editIcon").show();
        $("#delIcon").show();
    }
    AjaxGet('/desktop/quickEntryLists?id=' + myData.quickID, updateScreen);
});

function updateScreen(data) { //快捷入口变化时更新屏
    var arr = data.extra.extra;
    var len = arr ? arr.length : 0;
    var title = data.extra.name || '';
    var con = [];
    for (var i = 0; i < len; i++) { //创建icon
        delete arr[i].id;
        myData.extra.push(arr[i]);
        var $con = $(createIcons(arr[i], 0, i));
        $con.css({
            "position": "absolute",
            "left": Number(arr[i].itemX),
            "top": Number(arr[i].itemY)
        });
        con.push($con);
    }

    if (con.length > 0) {
        $('#quickWarp').html('').append(con);
    } else {
        $('#quickWarp').html('');
    }
    $('#quickLeft').val('');
    $('#quickTop').val('');
}

$('#jumpType > input').on('click', function(){
    var $this = $(this);
    var val = $this.val();

    if(val === 'APP'){
        $('.app-type').show();
        $('.linkin-type').hide();
        if($('#jumpApp').val() === '请选择跳转应用'){
            $('#jumpDetail').parent().hide();
        }
    }else if(val === 'URI'){
        $('.app-type').hide();
        $('.linkin-type').show();
    }
});

//新建快捷入口
$('#addQuick').on('click', function() {
    $('#quickList').find('input:checked').prop('checked', false);
    $('#myModal').modal('show');
});

//提交新快捷入口数据
$('#subNewQuick').on('click', function() {
    var name = $('#quickName').val();
    if (name == ' ' || !name) {
        alert('请输入快捷入口名称');
        return;
    }
    var $obj = $('#quickList').find('input:checked').parent().parent();
    var quickData = $obj.data('quick');
    var extra = [];
    var x = '0';
    var y = '0';
    var interval = '0';
    if (quickData) {
        var len = quickData.extra.length;
        for (var i = 0; i < len; i++) {
            delete quickData.extra[i].id;
            extra.push(quickData.extra[i]);
        }
    }
    var data = {
        "name": name,
        "extra": extra
    };
    AjaxPost('/desktop/addQuickEntry', data, function() {
        updateQuickScreen();
        myData.newQuickName = name;
        $('#myModal').modal('hide');
    });
});

//修改快捷入口
$('#editQuick').on('click', function() {
    if (myData.quickID === '空快捷入口') {
        alert('当前为空快捷入口');
        return false;
    }
    data = {
        "id": myData.quickID,
        "name": myData.quickName,
        "extra": myData.extra
    };

    AjaxPost('/desktop/modifyQuickEntry', data, function() {
        alert('修改成功');
        AjaxGet('/desktop/quickEntryLists', function(data) {
            createQuickList(data.extra);
        });
        return;
    });
});

//删除快捷入口
$('#delQuick').on('click', function() {
    if (confirm('确定删除？')) {
        AjaxGet('/desktop/deleteQuickEntry?id=' + myData.quickID, function() {
            alert('删除成功');
            updateQuickScreen();
        });
    }
    return false;
});

//创建新建快捷入口数据
function createQuickList(arr) {
    $('#quickList').html('');
    for (var i = 0, len = arr.length; i < len; i++) {
        var $div = $('<div class="screen"></div>');
        var $quickList = $('<div style="width: 100%; top: 100%;text-align: left;"></div>');
        for (var j = 0, l = arr[i].extra.length; j < l; j++) {
            var icon = arr[i].extra[j];
            var left = parseInt(arr[i].extra[j].itemX) / 4;
            var top = parseInt(arr[i].extra[j].itemY) / 4;
            if (j === 0) {
                setIconQuick($quickList, icon.forcusPath, left, top);
            } else {
                setIconQuick($quickList, icon.normalPath, left, top);
            }
        }
        $div.append($quickList);
        $div.append('<div class="radioBox"><input name="screenType" type="radio" class="ace" value="true"><span class="lbl">&nbsp;' + arr[i].name + '</span>&emsp;</div></div>');
        $div.data("quick", arr[i]);
        $('#quickList').append($div);
    }
}

//添加控件事件
$('#addIcon').on('click', function() {
    clearIconModal('添加控件');
    $('#iconModal').modal('show');
});

//修改控件事件
$('#editIcon').on('click', function() {
    var len = $('#quickWarp a.selectHandle').length;
    if (len !== 1) {
        alert('请选择一个控件');
        return false;
    }
    clearIconModal('修改控件');
    $('#iconModal').modal('show');
});

function clearIconModal(type) {
    AjaxGet('/desktop/actionAppLists', selectApp);
    AjaxGet('/desktop/iconLists', function(handleData) {
        $('#iconModal h4').text(type);
        setIconInfo(type, handleData);
    });
}

//删除控件事件
$('#delIcon').on('click', function() {
    var $icon = $('#quickWarp a.selectHandle');
    if($icon.length === 0){
        alert('请选择控件');
        return false;
    }
    if (confirm('确定删除？')) {
        var idx = $icon.index();
        $icon.remove();
        var $frist = $('#quickWarp a:eq(0)');
        myData.extra.splice(idx, 1);
        if ($frist.length > 0) {
            $frist.addClass('active').find('img').attr('src', myData.extra[0].forcusPath);
        }
    }
});


//为快捷入口添加、修改控件
$('#subIcon').on('click', function() {
    var name = $('#iconName').val();
    var itemX = $('#iconPosX').val();
    var itemY = $('#iconPosY').val();
    var iconId = $('#iconList').val();
    var title = $('#iconModal h4').text();
    var jumpType = $('#jumpType input:checked').val();
    var data = {};

    if (name == ' ' || !name) {
        alert('请输入控件名称');
        return;
    }
    if (itemX == ' ' || !itemX) {
        alert('请输入坐标X');
        return;
    }
    if (itemY == ' ' || !itemY) {
        alert('请输入坐标Y');
        return;
    }

    if(jumpType === 'APP'){
        var $jumpApp = $('#jumpApp');
        if ($jumpApp.val() == '请选择跳转应用') {
            alert('请选择跳转应用');
            return false;
        }
        var $jumpDetail = $('#jumpDetail');
        var appData = $jumpApp.data('_' + $jumpApp.val());
        if ($jumpDetail.val() == '请选择跳转详情页') {
            data.type = 'APP';
            data.appName = appData.appName;
            data.pkgName = appData.pkgName;
        } else {
            var detailDate = $jumpDetail.data('_' + $jumpDetail.val());
            data = detailDate;
            data.type = detailDate.actionType;
            data.appName = appData.appName;
            // data.detailName = detailDate.detailName;
            // data.extraData = detailDate.extraData;
            // if (detailDate.actionType === 'ACTION') {
            //     data.action = detailDate.action;
            // } else if (detailDate.actionType === 'COMPONENT') {
            //     data.component = detailDate.component;
            //     data.clsName = detailDate.clsName;
            // }
        }
    }else if(jumpType === 'URI'){
        var uri = $('#uriVal').val();
        if(uri.indexOf('http://') === -1 && uri.indexOf('https://') === -1){
            alert('请输入正确的连接！');
            return false;
        }
        data.uri = uri;
        data.type = 'URI';
    }



    if (iconId == '请选择控件' || !iconId) {
        alert('请选择控件');
        return;
    }
    var iconI = $('#reloadPic i');
    var normalPath = $(iconI[0]).data('src');
    var forcusPath = $(iconI[1]).data('src');

    var idx = $('#quickWarp a.active').index();

    $.extend(data, {
        "name": name,
        "itemX": itemX,
        "itemY": itemY,
        "normalPath": normalPath,
        "forcusPath": forcusPath
    });

    if (title === '添加控件') {
        myData.extra.splice(idx + 1, 0, data);
    } else if (title === '修改控件') {
        myData.extra[idx] = data;
    }

    var con = [];
    var len = myData.extra.length;
    for (var i = 0; i < len; i++) {
        var $con = $(createIcons(myData.extra[i], 0, i));
        $con.css({
            "position": "absolute",
            "left": Number(myData.extra[i].itemX),
            "top": Number(myData.extra[i].itemY)
        });
        con.push($con);
    }

    $('#quickWarp').html('').append(con);
    $('#iconModal').modal('hide');
});

//设置控件数据
function setIconInfo(type, handleData, data) {
    var arr = handleData.extra;
    var con = '<option value="请选择控件">请选择控件</option>';
    var $select = $('#iconList');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
        $select.data('_' + arr[i].name, arr[i]);
    }

    if (type === '修改控件') {
        var $icon = $('#quickWarp a.selectHandle');
        var idx = $icon.index();
        var iconData = myData.extra[idx];

        $('#iconName').val(iconData.name);
        $('#iconPosX').val(iconData.itemX);
        $('#iconPosY').val(iconData.itemY);
        var $jumpApp = $('#jumpApp');
        var optionA = $jumpApp.find('option').filter('[data-name="' + iconData.appName + '"]');
        optionA.prop("selected", true);
        $jumpApp.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });

        if (iconData.type === 'APP') {
            $jumpApp.trigger('change');
        } else if(iconData.type !== 'URI'){
            $jumpApp.trigger('change', iconData.detailName);
        }

        if(iconData.type === 'URI'){
            $('#uriVal').val(iconData.uri);
            $('#jumpType input:eq(1)').trigger('click');
        }else{
            $('#jumpType input:eq(0)').trigger('click');
        }
    } else if (type === '添加控件') {
        $('#jumpType input:eq(0)').trigger('click');
        $('#iconName').val('');
        $('#iconPosX').val('');
        $('#iconPosY').val('');
    }
    $select.html(con).val('请选择控件').trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    }).trigger('change');

    $('#iconModal').modal('show');
}

//创建跳转应用下拉框
function selectApp(data) {
    var arr = data.extra;
    var con = '<option value="请选择跳转应用">请选择跳转应用</option>';
    var $select = $('#jumpApp');
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
$('#jumpApp').on('change', function(e, name) {
    var id = $(this).val();
    var $select = $('#jumpDetail');
    if (id === '请选择跳转应用') {
        $select.parent().hide();
        $select.html('<option value="请选择跳转详情页">请选择跳转详情页</option>');
        return false;
    }
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
});

//生成预览图片
loadPreIMG();

//快捷入口点击事件
listenHandleSwitch('#quick');


//快捷入口拖拽事件
$("#screenWarp")
.on("mousedown", "a", function(ev) {
    var oDiv = this;
    var e = ev||event;
    var $this = $(this);
    //按Ctrl，则判断为多选
    if(e.ctrlKey){
        $this.toggleClass("selectHandle");
        refreshNavPos({
            "left": "",
            "top": ""
        });
    }
    //没按Ctrl，且点击的块不是多选的块之一，则判断为单选
    if(!e.ctrlKey && !$this.hasClass('selectHandle')){
        $('.selectHandle').removeClass("selectHandle");
        refreshNavPos({
            "left": parseInt($this.css('left')),
            "top": parseInt($this.css('top'))
        });
        $this.addClass("selectHandle");
    }
    var scale = 100 / $('#desktopProportion').val();    //获取显示比例
    disX=e.clientX * scale -oDiv.offsetLeft;
    disY=e.clientY * scale -oDiv.offsetTop;
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
        var nowHandle = myData.extra[nowIdx];
        var offsetX = l -  Number(myData.extra[nowIdx].itemX);
        var offsetY = t -  Number(myData.extra[nowIdx].itemY);

        var len = selectHandles.length;
        for(var i = 0; i < len; i++){
            var $selectHandle = $(selectHandles[i]);
            var idx = $selectHandle.index();
            myData.extra[idx].itemX = Number(myData.extra[idx].itemX) + offsetX;
            myData.extra[idx].itemY = Number(myData.extra[idx].itemY) + offsetY;
            $selectHandle.css({
                "left": Number(myData.extra[idx].itemX),
                "top": Number(myData.extra[idx].itemY)
            });
        }

        var dom = $('#screenWarp li').get(0);
        dom.scrollLeft = l;
        dom.scrollTop = t;
        if(len > 1){
            refreshNavPos({
                "left": "",
                "top": ""
            });
        }else{
            refreshNavPos({
                "left": myData.extra[nowIdx].itemX,
                "top": myData.extra[nowIdx].itemY
            });
        }
    });
    $(document).off('mouseup').on('mouseup', function(ev){
        var e = ev || event;
        var cls = e.target.className;
        $(document).off('mousemove');
        if (cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;
        $(document).off('mouseup');
    });
    return false;
});

$('#quickLeft').on('change', function() { //快捷入口Left改变
    var $icon = $('#quickWarp a.active');
    var idx = $icon.index();
    var left = parseInt(this.value.trim(), 10);
    myData.extra[idx].itemX = left + '';
    $icon.css('left', left);
});

$('#quickTop').on('change', function() { //快捷入口Top改变
    var $icon = $('#quickWarp a.active');
    var idx = $icon.index();
    var top = parseInt(this.value.trim(), 10);
    myData.extra[idx].itemY = top + '';
    $icon.css('top', top);
});

//键盘改变选中控件
$(document).off('keydown.quick-keydown').on('keydown.quick-keydown', function(e) {
    var sel = $('.selectHandle');
    if(sel.length === 0){
        return true;
    }
    if (e.keyCode === 46) {
        $('#delIcon').trigger('click');
        return false;
    }
    var offset = e.shiftKey ? shiftKeyNum : 1;
    if (e.keyCode === 38) {
        setNavXY("y", -offset);
    } else if (e.keyCode === 40) {
        setNavXY("y", offset);
    } else if (e.keyCode === 37) {
        setNavXY("x", -offset);
    } else if (e.keyCode === 39) {
        setNavXY("x", offset);
    } else{
        return true;
    }
    return false;
});

//更新选中控件位置
function setNavXY(type, num) {
    var sel = $('.selectHandle');
    var handle = null;
    for(var i = 0; i < sel.length; i++){
        var $handle = $(sel[i]);
        var idx = $handle.index();
        var extra = myData.extra[idx];
        if (type == "y") {
            extra.itemY = Number(extra.itemY) + num;
            $handle.css('top', extra.itemY);
            $('#quickTop').val(extra.itemY);
        } else if (type == 'x') {
            extra.itemX = Number(extra.itemX) + num;
            $handle.css('left', extra.itemX);
            $('#quickLeft').val(extra.itemX);
        } else if (type == 'X') {
            extra.itemX = num;
            $handle.css('left', extra.itemX);
            $('#quickLeft').val(extra.itemX);
        } else if (type == 'Y') {
            extra.itemY = num;
            $handle.css('top', extra.itemY);
            $('#quickTop').val(extra.itemY);
        }
    }
}

//刷新快捷入口的位置
function refreshNavPos(pos) {
    $('#quickLeft').val(parseInt(pos.left) || 0);
    $('#quickTop').val(parseInt(pos.top) || 0);
}

//新建屏radio事件
$('#quickList').on('click', '.screen .radioBox > span.lbl', function() {
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

//对齐事件
$('#setQuickAlignment').on('click', 'a', function(ev){
    var e = ev || event;
    setHandleAlignment(e.target.text);
    $('#setQuickAlignment').css({
        "display": 'none'
    });
    return false;
});

//对齐
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
        data.push(myData.extra[idx]);
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