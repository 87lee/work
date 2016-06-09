//@ sourceURL=desktop.nav.js
var myData = {};
var shiftKeyNum = 10;
$(function() {
    updateNavcreen();

    listenPic($('#add_pos2'));
    listenfile();
});

//更新导航
function updateNavcreen() {
    myData.NavId = null;
    myData.NavName = null;
    AjaxGet('/desktop/navLists', selectNav);
}

//生成导航下拉框选项
function selectNav(data) {
    var arr = data.extra;
    var con = '<option value="空导航">空导航</option>';
    var $select = $('#navSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '" >' + arr[i].name + '</option>';
    }
    if (!myData.newNavName) {
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        $('#screenSelect').show();
    } else {
        var option = $select.html(con).find('option').filter('[data-name="' + myData.newNavName + '"]');
        option.prop("selected", true);
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        myData.newNavName = null;
        $('#screenSelect').show();
    }
    createNavList(arr);
}

$('#navSelect').on('change', function() { //监听选择导航事件
    myData.NavId = $(this).val();
    myData.NavName = $(this).find("option:selected").text();

    if (myData.NavId === '空导航') {
        $("#editNav").hide();
        $("#delNav").hide();
        $("#addIcon").hide();
        $("#editIcon").hide();
        $("#delIcon").hide();
        $('#moveIconFront').hide();
        $('#moveIconBehind').hide();
    } else {
        $("#editNav").show();
        $("#delNav").show();
        $("#addIcon").show();
        $("#editIcon").show();
        $("#delIcon").show();
        $('#moveIconFront').show();
        $('#moveIconBehind').show();
    }
    myData.extra = [];
    AjaxGet('/desktop/navLists?id=' + myData.NavId, updateScreen);
});

function updateScreen(data) { //导航变化时更新屏
    var arr = data.extra.extra;
    var len = arr ? arr.length : 0;
    var title = data.extra.name || '';
    var con = '';
    for (var i = 0; i < len; i++) { //创建icon
        myData.extra.push({
            "normalPath": arr[i].normalPath,
            "forcusPath": arr[i].forcusPath,
            "functionId": arr[i].functionId,
            "currentDrawable": arr[i].currentDrawable
        });
        con += createIcons(myData.extra[i], data.extra.interval, i);
    }
    $('#navWarp').html('').css({
        "left": data.extra.x + 'px',
        "top": data.extra.y + 'px',
        "width": 'auto'
    }).append(con);

    if (con !== '') {
        $('#navMargin').val(data.extra.interval);
        refreshNavPos({
            "left": data.extra.x,
            "top": data.extra.y
        });
    } else {
        $('#navMargin').val('');
        $('#navLeft').val('');
        $('#navTop').val('');
    }
}

//新建导航
$('#addNav').on('click', function() {
    $('#navList').find('input:checked').prop('checked', false);
    $('#myModal').modal('show');
});

//提交新导航数据
$('#subNewNav').on('click', function() {
    var name = $('#navName').val();
    if (name == ' ' || !name) {
        alert('请输入导航名称');
        return;
    }
    var $obj = $('#navList').find('input:checked').parent().parent();
    var navData = $obj.data('nav');
    var iconIds = [];
    var x = '0';
    var y = '0';
    var interval = '0';
    if (navData) {
        var len = navData.extra.length;
        for (var i = 0; i < len; i++) {
            iconIds.push({
                "normalPath": navData.extra[i].normalPath,
                "forcusPath": navData.extra[i].forcusPath,
                "functionId": navData.extra[i].functionId,
                "currentDrawable": navData.extra[i].currentDrawable
            });
        }
        x = navData.x;
        y = navData.y;
        interval = navData.interval;
    }
    var data = {
        "name": name,
        "x": x,
        "y": y,
        "interval": interval,
        "extra": iconIds
    };
    AjaxPost('/desktop/addNav', data, function() {
        updateNavcreen();
        myData.newNavName = name;
        $('#myModal').modal('hide');
    });
});

//修改导航
$('#editNav').on('click', function() {
    if (myData.NavId === '空导航') {
        alert('当前为空导航');
        return false;
    }
    data = {
        "id": myData.NavId,
        "name": myData.NavName,
        "x": $('#navLeft').val() || 0,
        "y": $('#navTop').val() || 0,
        "interval": $('#navMargin').val() || 0,
        extra: myData.extra
    };
    AjaxPost('/desktop/modifyNav', data, function() {
        alert('修改成功');
        AjaxGet('/desktop/navLists', function(data) {
            createNavList(data.extra);
        });
        return;
    });
});

//删除导航
$('#delNav').on('click', function() {
    if (confirm('确定删除？')) {
        AjaxGet('/desktop/deleteNav?id=' + myData.NavId, function() {
            alert('删除成功');
            updateNavcreen();
        });
    }
    return false;
});

//创建新建导航数据
function createNavList(arr) {
    $('#navList').html('');
    for (var i = 0, len = arr.length; i < len; i++) {
        var left = parseInt(arr[i].x) / 4;
        var top = parseInt(arr[i].y) / 4;
        var margin = arr[i].interval / 4;
        var $div = $('<div class="screen"></div>');
        var $navList = $('<div style="left: ' + left + 'px; top: ' + top + 'px;text-align: left;position: absolute;"></div>');
        for (var j = 0, l = arr[i].extra.length; j < l; j++) {
            var icon = arr[i].extra[j];
            if (j === 0) {
                setIconWidth($navList, icon.forcusPath, margin);
            } else {
                setIconWidth($navList, icon.normalPath, margin);
            }
        }
        $div.append($navList);
        $div.append('<div class="radioBox"><input name="screenType" type="radio" class="ace" value="true"><span class="lbl">&nbsp;' + arr[i].name + '</span>&emsp;</div></div>');
        $div.data("nav", arr[i]);
        $('#navList').append($div);
    }
}

//添加控件事件
$('#addIcon').on('click', function() {
    $('#iconModal h4').text('添加控件');
    $('#picPath').val('');
    $('#fileBtn').val('');
    $('#fnId').val('请选择功能ID');
    AjaxGet('/desktop/iconLists', function(data) {
        setIconInfo('添加控件', data);
    });
});

//修改控件事件
$('#editIcon').on('click', function() {
    var $now = $('#navWarp a.active');
    var len = $now.length;
    if (len === 0) {
        alert('请选择控件');
        return false;
    }
    $('#fnId').val(myData.extra[$now.index()].functionId);
    $('#picPath').val(myData.extra[$now.index()].currentDrawable);
    $('#iconModal h4').text('修改控件');
    AjaxGet('/desktop/iconLists', function(data) {
        setIconInfo('修改控件', data);
    });
});

//删除控件事件
$('#delIcon').on('click', function() {
    if (confirm('确定删除？')) {
        var $icon = $('#navWarp a.active');
        var idx = $icon.index();
        $icon.remove();
        var $frist = $('#navWarp a:eq(0)');
        myData.extra.splice(idx, 1);
        if ($frist.length > 0) {
            $frist.addClass('active').find('img').attr('src', myData.extra[0].forcusPath);
        }
    }
});

//向前移控件事件
$('#moveIconFront').on('click', function() {
    var $icon = $('#navWarp a.active');
    var idx = $icon.index();
    var nIdx = idx - 1;
    if (idx === 0) {
        alert('不能向前移');
        return false;
    }
    var tempData = myData.extra[idx];
    myData.extra.splice(idx, 1);
    myData.extra.splice(nIdx, 0, tempData);
    $('#navWarp a:eq(' + nIdx + ')').before($('#navWarp a:eq(' + idx + ')'));
});

//向后移控件事件
$('#moveIconBehind').on('click', function() {
    var $icon = $('#navWarp a.active');
    var idx = $icon.index();
    var nIdx = idx + 1;
    var len = $('#navWarp a').length;
    if (nIdx === len) {
        alert('不能向后移');
        return false;
    }
    var tempData = myData.extra[idx];
    myData.extra.splice(idx, 1);
    myData.extra.splice(nIdx, 0, tempData);
    $('#navWarp a:eq(' + nIdx + ')').after($('#navWarp a:eq(' + idx + ')'));
});


//为导航添加、修改控件
$('#subIcon').on('click', function() {
    var iconId = $('#iconList').val();
    var fnId = $('#fnId').val();
    var title = $('#iconModal h4').text();

    if (title === '添加控件') {
        if (iconId == '请选择控件' || !iconId) {
            alert('请选择控件');
            return;
        }
    }
    if (fnId == '请选择功能ID' || !fnId) {
        alert('请选择功能ID');
        return;
    }
    var idx = $('#navWarp a.active').index();
    var iconI = $('#reloadPic i');
    var normalPath = $(iconI[0]).data('src') || myData.extra[idx].normalPath;
    var forcusPath = $(iconI[1]).data('src') || myData.extra[idx].forcusPath;
    var currentDrawable  = $('#picPath').val();
    var iconSrc = {
        "normalPath": normalPath,
        "forcusPath": forcusPath,
        "functionId": fnId,
        "currentDrawable": currentDrawable
    };

    if (title === '添加控件') {
        myData.extra.splice(idx + 1, 0, iconSrc);

        var picData = new FormData();
        var fileObj1 = document.getElementById("fileBtn").files[0];
        var fileVal1 = $("#picPath").val();
        if (fileVal1 != ' ' && fileVal1.indexOf('http') == -1 && fileVal1) {
            picData.append("pic1", fileObj1);
        }
        picData.append('additional', 'slot');
        if (fileVal1) {
            AjaxFile('/desktop/updataImage', picData, function(imgData) {
                myData.extra[idx+1].currentDrawable = imgData.pic1;
            });
        }
    } else if (title === '修改控件') {
        myData.extra[idx] = iconSrc;

        var picData = new FormData();
        var fileObj1 = document.getElementById("fileBtn").files[0];
        var fileVal1 = $("#picPath").val();
        if (fileVal1 != ' ' && fileVal1.indexOf('http') == -1 && fileVal1) {
            picData.append("pic1", fileObj1);
        }
        picData.append('additional', 'slot');
        if (fileVal1) {
            AjaxFile('/desktop/updataImage', picData, function(imgData) {
                myData.extra[idx].currentDrawable = imgData.pic1;
            });
        }
    }


    var con = '';
    var margin = $('#navMargin').val() || 0;
    var len = myData.extra.length;
    for (var i = 0; i < len; i++) {
        con += createIcons(myData.extra[i], margin, i);
    }
    $('#navMargin').val(margin);
    $('#navWarp').html('').append(con);
    $('#iconModal').modal('hide');
});

//设置控件数据
function setIconInfo(type, data) {
    var arr = data.extra;
    var con = '<option value="请选择控件">请选择控件</option>';
    var $select = $('#iconList');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
        $select.data('_' + arr[i].name, arr[i]);
    }

    $select.html(con).val('请选择控件').trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    }).trigger('change');

    $('#iconModal').modal('show');
}

//生成预览图片
loadPreIMG();

//导航点击事件
listenHandleSwitch('#nav');
//导航拖拽事件
listenHandleDrag("#navWarp", refreshNavPos);

$('#navLeft').on('change', function() { //导航Left改变
    var left = parseInt(this.value.trim(), 10);
    $('#navWarp').css('left', left);
});

$('#navTop').on('change', function() { //导航Top改变
    var top = parseInt(this.value.trim(), 10);
    $('#navWarp').css('top', top);
});

$('#navMargin').on('change', function() { //导航间隔改变
    var margin = parseInt(this.value.trim(), 10);
    $('#navWarp a').css('margin-right', margin);
});

$(document).off('keydown').on('keydown', function(e) { //键盘改变导航
    var offset = e.shiftKey ? shiftKeyNum : 1;
    if ($('#navWarp a').length !== 0) {
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

//设置导航位置
function setNavXY(type, num) {
    var $navWarp = $('#navWarp');
    if (type === 'x') {
        var x = parseInt($navWarp.css('left')) + num;
        $navWarp.css('left', x);
        $('#navLeft').val(x);
    } else {
        var y = parseInt($navWarp.css('top')) + num;
        $navWarp.css('top', y);
        $('#navTop').val(y);
    }
}

//刷新导航的位置
function refreshNavPos(pos) {
    $('#navLeft').val(parseInt(pos.left) || 0);
    $('#navTop').val(parseInt(pos.top) || 0);
}

//新建屏radio事件
$('#navList').on('click', '.screen .radioBox > span.lbl', function() {
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