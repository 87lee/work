//@ sourceURL=desktop.attachment.js
var myData = {};
var shiftKeyNum = 10;
$(function() {
    updateAttachmentScreen();

    listenPic($('#add_pos2'));

    listenchoose();
});

//更新附件栏
function updateAttachmentScreen() {
    myData.attachmentID = null;
    myData.attachmentName = null;
    AjaxGet('/desktop/attachmentLists', selectAttachment);
}

//生成附件栏下拉框选项
function selectAttachment(data) {
    var arr = data.extra;
    var con = '<option value="空附件栏">空附件栏</option>';
    var $select = $('#attachmentSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '" >' + arr[i].name + '</option>';
    }
    if (!myData.newAttachmentName) {
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        $('#screenSelect').show();
    } else {
        var option = $select.html(con).find('option').filter('[data-name="' + myData.newAttachmentName + '"]');
        option.prop("selected", true);
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        $('#screenSelect').show();
        myData.newAttachmentName = null;
    }
    createAttachmentList(arr);
}

$('#attachmentSelect').on('change', function() { //监听选择附件栏事件
    myData.attachmentID = $(this).val();
    myData.attachmentName = $(this).find("option:selected").text();

    myData.extra = [];
    if (myData.attachmentID === '空附件栏') {
        $("#editAttachment").hide();
        $("#delAttachment").hide();
        $("#addIcon").hide();
        $("#editIcon").hide();
        $("#delIcon").hide();
        $("#moveIconTop").hide();
        $("#moveIconBottom").hide();
    } else {
        $("#editAttachment").show();
        $("#delAttachment").show();
        $("#addIcon").show();
        $("#editIcon").show();
        $("#delIcon").show();
        $("#moveIconTop").show();
        $("#moveIconBottom").show();
    }
    AjaxGet('/desktop/attachmentLists?id=' + myData.attachmentID, updateScreen);
});

function updateScreen(data) { //附件栏变化时更新屏
    var arr = data.extra.extra;
    var len = arr ? arr.length : 0;
    var title = data.extra.name || '';
    var con = '';
    for (var i = 0; i < len; i++) { //创建icon
        var tempExtra = {
            "name": arr[i].name,
            "normalPath": arr[i].normalPath,
            "forcusPath": arr[i].forcusPath,
            "radiusTopLeft": arr[i].radiusTopLeft,
            "radiusTopRight": arr[i].radiusTopRight,
            "radiusBottomLeft": arr[i].radiusBottomLeft,
            "radiusBottomRight": arr[i].radiusBottomRight,
            "type": arr[i].type
        };
        if (arr[i].type === 'APP') {
            tempExtra.appName = arr[i].appName;
            tempExtra.pkgName = arr[i].pkgName;
        } else if (arr[i].type === 'COMPONENT') {
            tempExtra.appName = arr[i].appName;
            tempExtra.detailName = arr[i].detailName;
            tempExtra.extraData = arr[i].extraData;
            tempExtra.clsName = arr[i].clsName;
            tempExtra.component = arr[i].component;
        } else if (arr[i].type === 'ACTION') {
            tempExtra.appName = arr[i].appName;
            tempExtra.detailName = arr[i].detailName;
            tempExtra.extraData = arr[i].extraData;
            tempExtra.action = arr[i].action;
        } else if (arr[i].type === 'URI') {
            tempExtra.extraData = arr[i].extraData;
            tempExtra.uri = arr[i].uri;
        }
        myData.extra.push(tempExtra);
        con += createIconsAttachment(myData.extra[i], data.extra.interval, i);
    }

    if (con !== '') {
        $('#attachmentMargin').val(data.extra.interval);
        refreshNavPos({
            "left": data.extra.x,
            "top": data.extra.y
        });
        $('#attachmentWarp').html('').css({
            "left": data.extra.x + 'px',
            "top": data.extra.y + 'px'
        }).append(con);
    } else {
        $('#attachmentMargin').val('');
        $('#attachmentLeft').val('');
        $('#attachmentTop').val('');
        $('#attachmentWarp').html('');
    }
}

//新建附件栏
$('#addAttachment').on('click', function() {
    $('#attachmentList').find('input:checked').prop('checked', false);
    $('#myModal').modal('show');
});

//提交新附件栏数据
$('#subNewAttachment').on('click', function() {
    var name = $('#attachmentName').val();
    if (name == ' ' || !name) {
        alert('请输入附件栏名称');
        return;
    }
    var $obj = $('#attachmentList').find('input:checked').parent().parent();
    var attachmentData = $obj.data('attachment');
    var extra = [];
    var x = '0';
    var y = '0';
    var interval = '0';
    if (attachmentData) {
        var len = attachmentData.extra.length;
        for (var i = 0; i < len; i++) {
            delete attachmentData.extra[i].id;
            extra.push(attachmentData.extra[i]);
        }
        x = attachmentData.x;
        y = attachmentData.y;
        interval = attachmentData.interval;
    }
    var data = {
        "name": name,
        "x": x,
        "y": y,
        "interval": interval,
        "extra": extra
    };
    AjaxPost('/desktop/addAttachment', data, function() {
        updateAttachmentScreen();
        myData.newAttachmentName = name;
        $('#myModal').modal('hide');
    });
});

//修改附件栏
$('#editAttachment').on('click', function() {
    if (myData.attachmentID === '空附件栏') {
        alert('当前为空附件栏');
        return false;
    }
    data = {
        "id": myData.attachmentID,
        "name": myData.attachmentName,
        "x": $('#attachmentLeft').val() || 0,
        "y": $('#attachmentTop').val() || 0,
        "interval": $('#attachmentMargin').val() || 0,
        "extra": myData.extra
    };

    AjaxPost('/desktop/modifyAttachment', data, function() {
        alert('修改成功');
        AjaxGet('/desktop/attachmentLists', function(data) {
            createAttachmentList(data.extra);
        });
        return;
    });
});

//删除附件栏
$('#delAttachment').on('click', function() {
    if (confirm('确定删除？')) {
        AjaxGet('/desktop/deleteAttachment?id=' + myData.attachmentID, function() {
            alert('删除成功');
            updateAttachmentScreen();
        });
    }
    return false;
});

//创建新建附件栏数据
function createAttachmentList(arr) {
    $('#attachmentList').html('');
    for (var i = 0, len = arr.length; i < len; i++) {
        var left = parseInt(arr[i].x) / 4;
        var top = parseInt(arr[i].y) / 4;
        var margin = arr[i].interval / 4;
        var $div = $('<div class="screen"></div>');
        var $attachmentList = $('<div style="left: ' + left + 'px; top: ' + top + 'px;text-align: left;position: absolute;"></div>');
        for (var j = 0, l = arr[i].extra.length; j < l; j++) {
            var icon = arr[i].extra[j];
            if (j === 0) {
                setIconHeight($attachmentList, icon.forcusPath, margin, true);
            } else {
                setIconHeight($attachmentList, icon.normalPath, margin);
            }
        }
        $div.append($attachmentList);
        $div.append('<div class="radioBox"><input name="screenType" type="radio" class="ace" value="true"><span class="lbl">&nbsp;' + arr[i].name + '</span>&emsp;</div></div>');
        $div.data("attachment", arr[i]);
        $('#attachmentList').append($div);
    }
}

//添加控件事件
$('#addIcon').on('click', function() {
    clearIconModal('添加控件');
    $('#iconModal').modal('show');
});

//修改控件事件
$('#editIcon').on('click', function() {
    var len = $('#attachmentWarp a.active').length;
    if (len === 0) {
        alert('请选择控件');
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
    if (confirm('确定删除？')) {
        var $icon = $('#attachmentWarp a.active');
        var idx = $icon.index();
        $icon.remove();
        var $frist = $('#attachmentWarp a:eq(0)');
        myData.extra.splice(idx, 1);
        if ($frist.length > 0) {
            $frist.addClass('active').find('img').attr('src', myData.extra[0].forcusPath);
        }
    }
});

//向上移控件事件
$('#moveIconTop').on('click', function() {
    var $icon = $('#attachmentWarp a.active');
    var idx = $icon.index();
    var nIdx = idx - 1;
    if (idx === 0) {
        alert('不能向上移');
        return false;
    }
    var tempData = myData.extra[idx];
    myData.extra.splice(idx, 1);
    myData.extra.splice(nIdx, 0, tempData);
    $('#attachmentWarp a:eq(' + nIdx + ')').before($('#attachmentWarp a:eq(' + idx + ')'));
});

//向下移控件事件
$('#moveIconBottom').on('click', function() {
    var $icon = $('#attachmentWarp a.active');
    var idx = $icon.index();
    var nIdx = idx + 1;
    var len = $('#attachmentWarp a').length;
    if (nIdx === len) {
        alert('不能向下移');
        return false;
    }
    var tempData = myData.extra[idx];
    myData.extra.splice(idx, 1);
    myData.extra.splice(nIdx, 0, tempData);
    $('#attachmentWarp a:eq(' + nIdx + ')').after($('#attachmentWarp a:eq(' + idx + ')'));
});


//为附件栏添加、修改控件
$('#subIcon').on('click', function() {
    var name = $('#iconName').val();
    var iconId = $('#iconList').val();
    var radiusTopLeft = $('#radiusTopLeft').val();
    var radiusTopRight = $('#radiusTopRight').val();
    var radiusBottomLeft = $('#radiusBottomLeft').val();
    var radiusBottomRight = $('#radiusBottomRight').val();
    var title = $('#iconModal h4').text();
    var data = {};

    if (name == ' ' || !name) {
        alert('请输入控件名称');
        return false;
    }

    if(radiusTopLeft == ' ' || !radiusTopLeft){
        alert('请输入左上角圆角值');
        return false;
    }
    if(/\D/.test(radiusTopLeft) && radiusTopLeft >= 0 && radiusTopLeft <= 90){
        alert('左上角圆角值出错');
        return false;
    }
    if(radiusTopRight == ' ' || !radiusTopRight){
        alert('请输入右上角圆角值');
        return false;
    }
    if(/\D/.test(radiusTopRight) && radiusTopRight >= 0 && radiusTopRight <= 90){
        alert('右上角圆角值出错');
        return false;
    }
    if(radiusBottomLeft == ' ' || !radiusBottomLeft){
        alert('请输入左下角圆角值');
        return false;
    }
    if(/\D/.test(radiusBottomLeft) && radiusBottomLeft >= 0 && radiusBottomLeft <= 90){
        alert('左下角圆角值出错');
        return false;
    }
    if(radiusBottomRight == ' ' || !radiusBottomRight){
        alert('请输入右下角圆角值');
        return false;
    }
    if(/\D/.test(radiusBottomRight) && radiusBottomRight >= 0 && radiusBottomRight <= 90){
        alert('右下角圆角值出错');
        return false;
    }

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
        // data.type = detailDate.actionType;
        // data.appName = appData.appName;
        // data.detailName = detailDate.detailName;
        // data.extraData = detailDate.extraData;
        // if (detailDate.actionType === 'ACTION') {
        //     data.action = detailDate.action;
        // } else if (detailDate.actionType === 'COMPONENT') {
        //     data.component = detailDate.component;
        //     data.clsName = detailDate.clsName;
        // }
    }

    if (iconId == '请选择控件' || !iconId) {
        alert('请选择控件');
        return;
    }
    var iconI = $('#reloadPic i');
    var normalPath = $(iconI[0]).data('src');
    var forcusPath = $(iconI[1]).data('src');

    var idx = $('#attachmentWarp a.active').index();

    $.extend(data, {
        "name": name,
        "normalPath": normalPath,
        "forcusPath": forcusPath,
        "radiusTopLeft": radiusTopLeft,
        "radiusTopRight": radiusTopRight,
        "radiusBottomLeft": radiusBottomLeft,
        "radiusBottomRight": radiusBottomRight,
    });

    if (title === '添加控件') {
        myData.extra.splice(idx + 1, 0, data);
    } else if (title === '修改控件') {
        myData.extra[idx] = data;
    }

    var con = '';
    var margin = $('#attachmentMargin').val() || 0;
    var len = myData.extra.length;
    for (var i = 0; i < len; i++) {
        con += createIconsAttachment(myData.extra[i], margin, i);
    }
    $('#attachmentMargin').val(margin);
    $('#attachmentWarp').html('').append(con);
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
        var $icon = $('#attachmentWarp a.active');
        var idx = $icon.index();
        var iconData = myData.extra[idx];
        $('#iconName').val(iconData.name);
        $('#radiusTopLeft').val(iconData.radiusTopLeft);
        $('#radiusTopRight').val(iconData.radiusTopRight);
        $('#radiusBottomLeft').val(iconData.radiusBottomLeft);
        $('#radiusBottomRight').val(iconData.radiusBottomRight);

        var $jumpApp = $('#jumpApp');
        var optionA = $jumpApp.find('option').filter('[data-name="' + iconData.appName + '"]');
        optionA.prop("selected", true);
        $jumpApp.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });

        if (iconData.type !== 'APP') {
            $jumpApp.trigger('change', iconData.detailName);
        } else {
            $jumpApp.trigger('change');
        }
    } else if (type === '添加控件') {
        $('#iconName').val('');
        $('#radiusTopLeft').val('');
        $('#radiusTopRight').val('');
        $('#radiusBottomLeft').val('');
        $('#radiusBottomRight').val('');
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

//附件栏点击事件
listenHandleSwitch('#attachment');

//附件栏拖拽事件
listenHandleDrag("#attachmentWarp", refreshNavPos);

$('#attachmentLeft').on('change', function() { //附件栏Left改变
    var left = parseInt(this.value.trim(), 10);
    $('#attachmentWarp').css('left', left);
});

$('#attachmentTop').on('change', function() { //附件栏Top改变
    var top = parseInt(this.value.trim(), 10);
    $('#attachmentWarp').css('top', top);
});

$('#attachmentMargin').on('change', function() { //附件栏间隔改变
    var margin = parseInt(this.value.trim(), 10);
    $('#attachmentWarp a').css('margin-bottom', margin);
});

$(document).off('keydown').on('keydown', function(e) { //键盘改变所有块
    var offset = e.shiftKey ? shiftKeyNum : 1;
    if ($('#attachmentWarp a').length !== 0) {
        if (e.keyCode === 38) {
            setNavXY("y", -offset);
        } else if (e.keyCode === 40) {
            setNavXY("y", offset);
        } else if (e.keyCode === 37) {
            setNavXY("x", -offset);
        } else if (e.keyCode === 39) {
            setNavXY("x", offset);
        }
        if (e.keyCode === 37 || e.keyCode === 38 || e.keyCode === 39 || e.keyCode === 40) {
            return false;
        }
    }
});

//设置附件栏位置
function setNavXY(type, num) {
    var $navWarp = $('#attachmentWarp');
    if (type === 'x') {
        var x = parseInt($navWarp.css('left')) + num;
        $navWarp.css('left', x);
        $('#attachmentLeft').val(x);
    } else {
        var y = parseInt($navWarp.css('top')) + num;
        $navWarp.css('top', y);
        $('#attachmentTop').val(y);
    }
}

//刷新附件栏的位置
function refreshNavPos(pos) {
    $('#attachmentLeft').val(parseInt(pos.left) || 0);
    $('#attachmentTop').val(parseInt(pos.top) || 0);
}

//新建屏radio事件
$('#attachmentList').on('click', '.screen .radioBox > span.lbl', function() {
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