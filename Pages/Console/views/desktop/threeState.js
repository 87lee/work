//@ sourceURL=desktop.threeState.js
var myData = {};
var shiftKeyNum = 10;
$(function() {
    updateThreeScreen();

    listenPic($('#add_pos2'));

    listenchoose();

    loadPreIMG2('#iconList1', 1);
    loadPreIMG2('#iconList2', 2);
    loadPreIMG2('#iconList3', 3);
});

//更新三态快捷入口
function updateThreeScreen() {
    myData.threeID = null;
    myData.threeName = null;
    AjaxGet('/desktop/quickEntryThreeStateLists', selectThree);
}

//生成三态快捷入口下拉框选项
function selectThree(data) {
    var arr = data.extra;
    var con = '<option value="空三态快捷入口">空三态快捷入口</option>';
    var $select = $('#threeSelect');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '" data-name="' + arr[i].name + '" >' + arr[i].name + '</option>';
    }
    if (!myData.newThreeName) {
        $select.html(con).trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        $('#screenSelect').show();
    } else {
        var option = $select.html(con).find('option').filter('[data-name="' + myData.newThreeName + '"]');
        option.prop("selected", true);
        $select.trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "170px"
        }).trigger('change');
        $('#screenSelect').show();
        myData.newThreeName = null;
    }
    createThreeList(arr);
}

$('#threeSelect').on('change', function() { //监听选择三态快捷入口事件
    myData.threeID = $(this).val();
    myData.threeName = $(this).find("option:selected").text();

    myData.extra = [];
    if (myData.threeID === '空三态快捷入口') {
        $("#editThree").hide();
        $("#delThree").hide();
        $("#addIcon").hide();
        $("#editIcon").hide();
        $("#delIcon").hide();
    } else {
        $("#editThree").show();
        $("#delThree").show();
        $("#addIcon").show();
        $("#editIcon").show();
        $("#delIcon").show();
    }
    AjaxGet('/desktop/quickEntryThreeStateLists?id=' + myData.threeID, updateScreen);
});

function updateScreen(data) { //三态快捷入口变化时更新屏
    var arr = data.extra.extra;
    var len = arr ? arr.length : 0;
    var title = data.extra.name || '';
    var con = [];
    for (var i = 0; i < len; i++) { //创建icon
        delete arr[i].id;
        myData.extra.push(arr[i]);
        var $con = $(createIconsThree(arr[i], i));
        $con.draggable({
                containment: '#pageWrap',
                disabled: false
        }).css({
            "position": "absolute",
            "left": Number(arr[i].x),
            "top": Number(arr[i].y)
        });
        con.push($con);
    }

    if (con.length > 0) {
        $('#threeWarp').html('').append(con);
    } else {
        $('#threeWarp').html('');
    }
    $('#threeLeft').val('');
    $('#threeTop').val('');
}

//新建三态快捷入口
$('#addThree').on('click', function() {
    $('#threeList').find('input:checked').prop('checked', false);
    $('#myModal').modal('show');
});

//提交新三态快捷入口数据
$('#subNewThree').on('click', function() {
    var name = $('#threeName').val();
    if (name == ' ' || !name) {
        alert('请输入三态快捷入口名称');
        return;
    }
    var $obj = $('#threeList').find('input:checked').parent().parent();
    var threeData = $obj.data('three');
    var extra = [];
    var x = '0';
    var y = '0';
    var interval = '0';
    if (threeData) {
        var len = threeData.extra.length;
        for (var i = 0; i < len; i++) {
            delete threeData.extra[i].id;
            extra.push(threeData.extra[i]);
        }
    }
    var data = {
        "name": name,
        "x": 0,
        "y": 0,
        "extra": extra
    };
    AjaxPost('/desktop/addQuickEntryThreeState', data, function() {
        updateThreeScreen();
        myData.newThreeName = name;
        $('#myModal').modal('hide');
    });
});

//修改三态快捷入口
$('#editThree').on('click', function() {
    if (myData.threeID === '空三态快捷入口') {
        alert('当前为空三态快捷入口');
        return false;
    }
    data = {
        "id": myData.threeID,
        "x": "0",
        "y": "0",
        "name": myData.threeName,
        "extra": myData.extra
    };

    AjaxPost('/desktop/modifyQuickEntryThreeState', data, function() {
        alert('修改成功');
        AjaxGet('/desktop/quickEntryThreeStateLists', function(data) {
            createThreeList(data.extra);
        });
        return;
    });
});

//删除三态快捷入口
$('#delThree').on('click', function() {
    if (confirm('确定删除？')) {
        AjaxGet('/desktop/deleteQuickEntryThreeState?id=' + myData.threeID, function() {
            alert('删除成功');
            updateThreeScreen();
        });
    }
    return false;
});

//创建新建三态快捷入口数据
function createThreeList(arr) {
    $('#threeList').html('');
    for (var i = 0, len = arr.length; i < len; i++) {
        var $div = $('<div class="screen"></div>');
        var $threeList = $('<div style="width: 100%; top: 100%;text-align: left;"></div>');
        for (var j = 0, l = arr[i].extra.length; j < l; j++) {
            var icon = arr[i].extra[j];
            var left = parseInt(arr[i].extra[j].x) / 4;
            var top = parseInt(arr[i].extra[j].y) / 4;
            if (j === 0) {
                setIconQuick($threeList, icon.focusedDrawableA, left, top);
            } else {
                setIconQuick($threeList, icon.drawableA, left, top);
            }
        }
        $div.append($threeList);
        $div.append('<div class="radioBox"><input name="screenType" type="radio" class="ace" value="true"><span class="lbl">&nbsp;' + arr[i].name + '</span>&emsp;</div></div>');
        $div.data("three", arr[i]);
        $('#threeList').append($div);
    }
}

//添加控件事件
$('#addIcon').on('click', function() {
    clearIconModal('添加控件');
});

//修改控件事件
$('#editIcon').on('click', function() {
    var len = $('#threeWarp a.active').length;
    if (len === 0) {
        alert('请选择控件');
        return false;
    }
    clearIconModal('修改控件');
});

function clearIconModal(type) {
    AjaxWhen([
        AjaxGet('/desktop/actionAppLists', selectApp, true),
        AjaxGet('/desktop/iconLists', function(handleData) {
            $('#iconModal h4').text(type);
            setIconInfo('#iconList1', type, handleData);
            setIconInfo('#iconList2', type, handleData);
            setIconInfo('#iconList3', type, handleData);
        }, true)
    ], function(){
        $('#iconModal').modal('show');
    });
}

//删除控件事件
$('#delIcon').on('click', function() {
    if (confirm('确定删除？')) {
        var $icon = $('#threeWarp a.active');
        var idx = $icon.index();
        $icon.remove();
        var $frist = $('#threeWarp a:eq(0)');
        myData.extra.splice(idx, 1);
        if ($frist.length > 0) {
            $frist.addClass('active').find('img').attr('src', myData.extra[0].forcusPath);
        }
    }
});

//为三态快捷入口添加、修改控件
$('#subIcon').on('click', function() {
    var name = $('#iconName').val();
    var itemX = $('#iconPosX').val();
    var itemY = $('#iconPosY').val();
    var iconId1 = $('#iconList1').val();
    var iconId2 = $('#iconList2').val();
    var iconId3 = $('#iconList3').val();
    var eventTpye = $('#eventTpye').val();
    var title = $('#iconModal h4').text();
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
    if (eventTpye == '请选择事件类型' || !eventTpye) {
        alert('请选择事件类型');
        return;
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
        // data.detailName = detailDate.detailName;
        // data.extraData = detailDate.extraData;
        // if (detailDate.actionType === 'ACTION') {
        //     data.action = detailDate.action;
        // } else if (detailDate.actionType === 'COMPONENT') {
        //     data.component = detailDate.component;
        //     data.clsName = detailDate.clsName;
        // }
    }

    if (iconId1 == '请选择控件' || !iconId1) {
        alert('请选择状态1控件');
        return false;
    }
    if (iconId2 == '请选择控件' || !iconId2) {
        alert('请选择状态2控件');
        return false;
    }
    if (iconId3 == '请选择控件' || !iconId3) {
        alert('请选择状态3控件');
        return false;
    }
    var iconI1 = $('#reloadPic1 i');
    var normalPath1 = $(iconI1[0]).data('src');
    var forcusPath1 = $(iconI1[1]).data('src');

    var iconI2 = $('#reloadPic2 i');
    var normalPath2 = $(iconI2[0]).data('src');
    var forcusPath2 = $(iconI2[1]).data('src');

    var iconI3 = $('#reloadPic3 i');
    var normalPath3 = $(iconI3[0]).data('src');
    var forcusPath3 = $(iconI3[1]).data('src');

    var idx = $('#threeWarp a.active').index();

    $.extend(data, {
        "name": name,
        "x": itemX,
        "y": itemY,
        "drawableA": normalPath1,
        "drawableB": normalPath2,
        "drawableC": normalPath3,
        "focusedDrawableA": forcusPath1,
        "focusedDrawableB": forcusPath2,
        "focusedDrawableC": forcusPath3,
        "eventType": "NETWORK",
        "eventA": "WifiConnectedEvent",
        "eventB": "EthernetConnectedEvent",
        "eventC": "NetworkDisconnectedEvent",
    });

    if (title === '添加控件') {
        myData.extra.splice(idx + 1, 0, data);
    } else if (title === '修改控件') {
        myData.extra[idx] = data;
    }

    var con = [];
    var len = myData.extra.length;
    for (var i = 0; i < len; i++) {
        var $con = $(createIconsThree(myData.extra[i], i));
        $con.draggable({
                containment: '#pageWrap',
                disabled: false
        }).css({
            "position": "absolute",
            "left": Number(myData.extra[i].x),
            "top": Number(myData.extra[i].y)
        });
        con.push($con);
    }
    $('#threeWarp').html('').append(con);
    $('#iconModal').modal('hide');
});

//设置控件数据
function setIconInfo(id, type, handleData, data) {
    var arr = handleData.extra;
    var con = '<option value="请选择控件">请选择控件</option>';
    var $select = $(id);
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
        $select.data('_' + arr[i].name, arr[i]);
    }

    if (type === '修改控件') {
        var $icon = $('#threeWarp a.active');
        var idx = $icon.index();
        var iconData = myData.extra[idx];
        $('#iconName').val(iconData.name);
        $('#iconPosX').val(iconData.x);
        $('#iconPosY').val(iconData.y);
        $('#eventTpye').val(iconData.eventType);
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
        $('#iconPosX').val('');
        $('#iconPosY').val('');
        $('#eventTpye').val('请选择事件类型');
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

//三态快捷入口点击事件
//控件点击切换状态事件
function listenThreeSwitch(type) {
	$(type + 'Warp').on('click', 'a', function() {
		var $now = $(this);
		if ($now.hasClass('active')) {
			return false;
		}
		var $old = $(type + 'Warp a').filter('.active');
		var oIdx = $old.index();
		var nIdx = $now.index();
		preImage(myData.extra[nIdx].focusedDrawableA, function() {
			$now.addClass('active').find('img').attr('src', myData.extra[nIdx].focusedDrawableA);
			$old.removeClass('active').find('img').attr('src', myData.extra[oIdx].drawableA);
		});
		return false;
	});
}
listenThreeSwitch('#three');


//三态快捷入口拖拽事件
$("#threeWarp")
.on("mousedown", "a", function(ev) {
    var oDiv = this;
    var e = ev||event;
    var $this = $(this);
    var scale = 100 / $('#desktopProportion').val();
    disX=e.clientX * scale -oDiv.offsetLeft;
    disY=e.clientY * scale -oDiv.offsetTop;
    refreshNavPos({
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
        refreshNavPos({
            "left": l,
            "top": t
        });

        var idx = $this.index();
        myData.extra[idx].x = l + '';
        myData.extra[idx].y = t + '';

        var dom = $('#screenWarp li').get(0);
        dom.scrollLeft = l;
        dom.scrollTop = t;
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

$('#threeLeft').on('change', function() { //三态快捷入口Left改变
    var $icon = $('#threeWarp a.active');
    var idx = $icon.index();
    var left = parseInt(this.value.trim(), 10);
    myData.extra[idx].x = left + '';
    $icon.css('left', left);
});

$('#threeTop').on('change', function() { //三态快捷入口Top改变
    var $icon = $('#threeWarp a.active');
    var idx = $icon.index();
    var top = parseInt(this.value.trim(), 10);
    myData.extra[idx].y = top + '';
    $icon.css('top', top);
});

$(document).off('keydown').on('keydown', function(e) { //键盘改变所有块
    var offset = e.shiftKey ? shiftKeyNum : 1;
    if ($('#threeWarp a').length !== 0) {
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

//设置三态快捷入口位置
function setNavXY(type, num) {
    var $navWarp = $('#threeWarp');
    if (type === 'x') {
        var x = parseInt($navWarp.css('left')) + num;
        $navWarp.css('left', x);
        $('#threeLeft').val(x);
    } else {
        var y = parseInt($navWarp.css('top')) + num;
        $navWarp.css('top', y);
        $('#threeTop').val(y);
    }
}

//刷新三态快捷入口的位置
function refreshNavPos(pos) {
    $('#threeLeft').val(parseInt(pos.left) || 0);
    $('#threeTop').val(parseInt(pos.top) || 0);
}

//新建屏radio事件
$('#threeList').on('click', '.screen .radioBox > span.lbl', function() {
    $(this).prev('input').trigger('click');
});

//生成预览图片
function loadPreIMG2(id, i) {
	$(id).on('change', function() {
		var name = $(this).find('option:selected').text();
		if (name === '请选择控件') {
			$('#reloadPic' + i).html('');
			return false;
		}
		var data = $(id).data('_' + name);
		var con = '<label class="icon-name" data-id=' + data.id + '>' + name + ':</label>' +
			'<span>正常状态</span><i class="glyphicon glyphicon-picture icon-black my-icon" data-src=' + data.normalPath + '></i>' +
			'<span>焦点状态</span><i class="glyphicon glyphicon-picture icon-black my-icon" data-src=' + data.forcusPath + '></i>';
		$('#reloadPic' + i).html(con);
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