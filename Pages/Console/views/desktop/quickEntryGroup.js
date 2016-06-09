//@ sourceURL=desktop.quickEntryGroup.js
var myData = {};
var shiftKeyNum = 10;
$(function() {
    updateNavcreen();

    listenPic($('#add_pos2'));
    listenfile();
});

//更新导航
function updateNavcreen() {
    myData.groupID = null;
    myData.NavName = null;
    AjaxGet('/desktop/getQuickEntryGroupLists', selectNav);
}

//生成导航下拉框选项
function selectNav(data) {
    var arr = data.extra;
    var con = '<option value="请选择快捷入口组">请选择快捷入口组</option>';
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
    myData.groupID = $(this).val();
    myData.NavName = $(this).find("option:selected").text();

    if (myData.groupID === '请选择快捷入口组') {
        $("#editNav").hide();
        $("#delQuickEntryGroup").hide();
        $("#addQuickEntry").hide();
        $('#editIcon').hide();
        $("#delQuickEntry").hide();
    } else {
        $("#editNav").show();
        $("#delQuickEntryGroup").show();
        $("#addQuickEntry").show();
        $('#editIcon').show();
        $("#delQuickEntry").show();
    }
    myData.mList = [];
    if (myData.groupID === '请选择快捷入口组') {
        
    }else{
        AjaxGet('/desktop/getQuickEntryGroupLists?id=' + myData.groupID, updateScreen);
    }
});

function updateScreen(data) { //导航变化时更新屏
    if (data.extra.length === 1) {
        var arr = data.extra.mList || data.extra[0].mList;
        var title = data.extra.name || '';
        var con = '';
        $('#screenWarp ul li').html('');
        for (var i = 0; i < arr.length; i++) { //创建icon
            myData.mList.push(arr[i]);
            //判断展现方式
            if (arr[i].layout === 'vertical') {
                for (var j = 0; j < arr[i].extra.length; j++) {
                    con = con + createQEGIcons(arr[i].extra[j], arr[i].distance, j);
                }
            }else{
                for (var j = 0; j < arr[i].extra.length; j++) {
                    con += createIcons(arr[i].extra[j], arr[i].distance, j);
                }
            }
            //判断伸展方向
            var obj = $();
            obj = $('<div id="' + i + '" class="screen-nav" style="left: ' + arr[i].x + 'px;' + 'top: ' + arr[i].y + 'px;' + 'width: auto;display: inline-block;position: absolute;"></div>');
            obj.append(con);

            if (arr[i].layout === 'vertical') {
                obj.children().css('margin-bottom', arr[i].distance + 'px');
                obj.children().addClass('row');
                obj.children().css('margin-left', '0px');
                obj.children().css('margin-right', '0px');
            }else{
                obj.children().css('margin-right', arr[i].distance + 'px');
            }
            $('#screenWarp ul li').append(obj);
            con = '';
        }
    }else{
        $('#screenWarp ul li').html('');
    }


    listenQuickEntry('#screenWarp ul li');
    listenfocus();
}

//新建组
$('#addGroup').on('click', function() {
    $('#navList').find('input:checked').prop('checked', false);
    $('#myModal').modal('show');
});

//提交新导航数据
$('#subNewNav').on('click', function() {
    var name = $('#groupName').val();
    if (name == ' ' || !name) {
        alert('请输入导航名称');
        return;
    }
    var $obj = $('#navList').find('input:checked').parent().parent();
    var navData = $obj.data('nav');
    if (navData) {
        navData.name = name;
    }else{
        navData = {
            'name': name,
            'mList': []
        };
    }

    AjaxPost('/desktop/addQuickEntryGroup', navData, function() {
        updateNavcreen();
        myData.newNavName = name;
        $('#myModal').modal('hide');
    });
});

//修改导航
$('#editNav').on('click', function() {
    if (myData.groupID === '请选择快捷入口组') {
        alert('当前为空快捷入口组');
        return false;
    }
    for (var i = 0; i < $('#screenWarp ul li').children().length; i++) {
        //if (myData.mList[i].direction === 'right') {
            var l = Number($('#screenWarp ul li').children(':eq(' + i + ')').css('left').split('px')[0]);
            var t = Number($('#screenWarp ul li').children(':eq(' + i + ')').css('top').split('px')[0]);
        // }else{
        //     var l = Number($('#screenWarp ul li').children(':eq(' + i + ')').css('left').split('px')[0]);
        //     var t = Number($('#screenWarp ul li').children(':eq(' + i + ')').css('top').split('px')[0]);
        //     l = l + Number($('#screenWarp ul li').children(':eq(' + i + ')').css('width').split('px')[0]);
        // }
        if ($('#screenWarp ul li').children(':eq(' + i + ')').children(':eq(0)').css('margin-bottom') === '0px') {
            myData.mList[i].distance = $('#screenWarp ul li').children(':eq(' + i + ')').children(':eq(0)').css('margin-right').split('px')[0];
        }else{
            myData.mList[i].distance = $('#screenWarp ul li').children(':eq(' + i + ')').children(':eq(0)').css('margin-bottom').split('px')[0];
        }
        myData.mList[i].x = l;
        myData.mList[i].y = t;
    }
    data = {
        "id": myData.groupID,
        "name": myData.NavName,
        "mList": myData.mList
    };
    AjaxPost('/desktop/modifyQuickEntryGroup', data, function() {
        alert('修改成功');
        AjaxGet('/desktop/getQuickEntryGroupLists', function(data) {
            createNavList(data.extra);
        });
        return;
    });
});

//删除导航
$('#delQuickEntryGroup').on('click', function() {
    if (confirm('确定删除？')) {
        AjaxGet('/desktop/deleteQuickEntryGroup?id=' + myData.groupID, function() {
            alert('删除成功');
            updateNavcreen();
        });
    }
    return false;
});

function listenfocus() {
    $('#screenWarp ul li a').on('click', function() {
        if ($(this).parent().hasClass('row')) {
            var idStr = $(this).parent().parent().get(0).id;
        }else{
            var idStr = $(this).parent().get(0).id;
        }
        var nChildNum = 0;
        var oChildNum = 0;
        if ($(this).parent().hasClass('row')) {
            for (var i = 0; i < $(this).parent().parent().children().length; i++) {
                if ($(this).parent().parent().children(':eq(' + i + ')').children('a').hasClass('active')){
                    oChildNum = i;
                }
            }
            nChildNum = $(this).parent().index();
        }else{
            nChildNum = $(this).index();
            oChildNum = $(this).parent().children('.active').index();
        }

        var oUrl = myData.mList[Number(idStr)].extra[oChildNum].normalPath;
        if ($(this).parent().hasClass('row')) {
            $(this).parent().parent().children(':eq(' + oChildNum.toString() + ')').children('a').children('img').attr('src', oUrl);
            $(this).parent().parent().children(':eq(' + oChildNum.toString() + ')').children('a').removeClass('active');
        }else{
            $(this).parent().children('.active').children('img').attr('src', oUrl);
            $(this).parent().children('.active').removeClass('active');
        }
        
        $(this).addClass('active');
        var nUrl = myData.mList[Number(idStr)].extra[nChildNum].forcusPath;
        $(this).children('img').attr('src', nUrl);
    });
}

//创建新建组数据
function createNavList(arr) {

    $('#navList').html('');
    for (var i = 0, len = arr.length; i < len; i++) {
        var $div = $('<div class="screen"></div>');
        if (!arr[i].mList) 
            continue;
        for (var k = 0; k < arr[i].mList.length; k++) {

            var left = parseInt(arr[i].mList[k].x) / 4;
            var top = parseInt(arr[i].mList[k].y) / 4;
            var margin = arr[i].mList[k].distance / 4;
            var layout = arr[i].mList[k].layout;
            
            var $navList = $('<div style="line-height: 0;left: ' + left + 'px; top: ' + top + 'px;text-align: left;position: absolute;"></div>');
            if (layout === 'vertical') {
                for (var j = 0, l = arr[i].mList[k].extra.length; j < l; j++) {
                    var icon = arr[i].mList[k].extra[j];
                    if (j === 0) {
                        $navList.append('<img src=' + icon.forcusPath + ' tar="0" style="margin-bottom: ' + margin + 'px;">');
                    } else {
                        $navList.append('<img src=' + icon.normalPath + ' tar="0" style="margin-bottom: ' + margin + 'px;">');
                    }
                }
            }else{
                for (var j = 0, l = arr[i].mList[k].extra.length; j < l; j++) {
                    var icon = arr[i].mList[k].extra[j];
                    if (j === 0) {
                        $navList.append('<img src=' + icon.forcusPath + ' tar="0" style="margin-right: ' + margin + 'px;">');
                    } else {
                        $navList.append('<img src=' + icon.normalPath + ' tar="0" style="margin-right: ' + margin + 'px;">');
                    }
                }
            }
            $navList.children().load(function() {
                $(this).css('width', (this.width/4).toString()+'px');
            });
            $div.append($navList);
        }
        var tmp = arr[i];
        delete tmp.id;
        $div.data("nav", tmp);
        $div.append('<div class="radioBox"><input name="screenType" type="radio" class="ace" value="true"><span class="lbl">&nbsp;' + arr[i].name + '</span>&emsp;</div></div>');
        $('#navList').append($div);
    }
}

//添加控件事件
$('#addQuickEntry').on('click', function() {
    $('#quickEntryDirection').trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%",
        disable_search: true
    });
    $('#quickEntryLayout').trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%",
        disable_search: true
    });
    AjaxGet('/desktop/quickEntryLists', function(data) {
        createQuickList(data.extra);
    });
    $('#quickEntryName').val('');
    $('#quickEntryX').val('');
    $('#quickEntryY').val('');
    $('#quickEntryDistance').val('');
    $('#fnId').val('请选择功能ID');
    $('#quickEntryModal').modal('show');
});

$('#editIcon').on('click', function() {
    if ($('#screenWarp .selectHandle').length === 1) {
        clearIconModal('修改控件');
    }else{
        alert('请选择一个快捷入口');
        return;
    }
});

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

//删除控件事件
$('#delQuickEntry').on('click', function() {
    if (confirm('确定删除？')) {
        var delMlist = Number($('.selectHandle').attr('id'));
        myData.mList.splice(delMlist,1);
        $('#screenWarp ul li div:eq(' + delMlist + ')').remove();
    }
});

$('#quickEntryDirection').on('change',function() {
if ($(this).val() === 'left') {
   if ($('#quickEntryX').val() === '' || $('#quickEntryY').val() === '') {
        alert('请先输入横纵坐标！');
        $('#quickEntryDirection').html('<option value="right">右</option><option value="left">左</option>');
        $('#quickEntryDirection').trigger("chosen:updated.chosen").chosen({
            allow_single_deselect: true,
            width: "70%"
        });
   }else{
        if ($('#quickList').find('input:checked').length <= 0) {
            alert('请先选择快捷入口!');
            $('#quickEntryDirection').html('<option value="right">右</option><option value="left">左</option>');
            $('#quickEntryDirection').trigger("chosen:updated.chosen").chosen({
                allow_single_deselect: true,
                width: "70%"
            });
        }else{
            var len = $('#quickList').find('input:checked').parent().siblings().children().length;
            var sum = 0;
            for (var i = 0; i < len; i++) {
                sum += $('#quickList').find('input:checked').parent().siblings().children(':eq('+i+')').width();
            }
            if ($('#quickEntryDistance').val() === '') {
                alert('请先输入间隔！');
                $('#quickEntryDirection').html('<option value="right">右</option><option value="left">左</option>');
                $('#quickEntryDirection').trigger("chosen:updated.chosen").chosen({
                    allow_single_deselect: true,
                    width: "70%"
                });
            }else{
                sum += Number($('#quickEntryDistance').val());
                sum = Number($('#quickEntryX').val()) - sum*4;
                $('#quickEntryX').val(sum);
            }
        }
   }
}
});

//为导航添加、修改控件
$('#subQuickEntry').on('click', function() {
    var quickEntryName = $('#quickEntryName').val();
    var quickEntryX = $('#quickEntryX').val();
    var quickEntryY = $('#quickEntryY').val();
    var quickList = $('#quickList').find('input:checked').parent().parent();
    var quickListData = quickList.data('quick');
    var quickEntryDirection = $('#quickEntryDirection').val();
    var quickEntryDistance = $('#quickEntryDistance').val();
    var quickEntryLayout = $('#quickEntryLayout').val();
    var pattern = /-?[1-9]\d*/ ;

    if (quickEntryName == ' ' || !quickEntryName) {
        alert('请输入快捷入口名称');
        return;
    }

    if (quickEntryX == ' ' || !quickEntryX) {
        alert('请输入横坐标');
        return;
    }

    if (Number(quickEntryX) < 0) {
        alert('请输入不小于0的横坐标！');
        return;
    }

    if (!pattern.test(quickEntryX)) {
        alert('请输入正确的横坐标');
        return;
    }

    if (quickEntryY == ' ' || !quickEntryY) {
        alert('请输入纵坐标');
        return;
    }

    if (!pattern.test(quickEntryY)) {
        alert('请输入正确的纵坐标');
        return;
    }

    if (quickEntryDistance == ' ' || !quickEntryDistance) {
        alert('请输入间隔');
        return;
    }

    if (!quickListData) {
        alert('请选择快捷入口！');
        return;
    }else{
        delete quickListData.id;
        delete quickListData.name;

    }

    var data = {
        'x': quickEntryX,
        'y': quickEntryY,
        'direction': quickEntryDirection,
        'distance': quickEntryDistance,
        'layout': quickEntryLayout,
        'name': quickEntryName,
        'extra': quickListData.extra
    };

    myData.mList.push(data);

    if (data.extra.length > 0) {
        var con = '';
        //判断展现方式
        if (data.layout === 'vertical') {
            for (var j = 0; j < data.extra.length; j++) {
                con = con + createQEGIcons(data.extra[j], data.distance, j);
            }
            var obj = $();
            obj = $('<div id="' + (myData.mList.length-1).toString() + '" class="screen-nav" style="left: ' + data.x + 'px;' + 'top: ' + data.y + 'px;' + 'width: auto;display: inline-block;position: absolute;"></div>');
            obj.append(con);
            obj.children('a').css('margin-right', '0px');
        }else{
            for (var j = 0; j < data.extra.length; j++) {
                con += createIcons(data.extra[j], data.distance, j);
            }
            var obj = $();
            obj = $('<div id="' + (myData.mList.length-1).toString() + '" class="screen-nav" style="left: ' + data.x + 'px;' + 'top: ' + data.y + 'px;' + 'width: auto;display: inline-block;position: absolute;"></div>');
            obj.append(con);
        }

        // if (arr[i].layout === 'vertical') {
        //         obj.children().css('margin-bottom', arr[i].distance + 'px');
        //         obj.children().addClass('row');
        //         obj.children().css('margin-left', '0px');
        //         obj.children().css('margin-right', '0px');
        //     }else{
        //         obj.children().css('margin-right', arr[i].distance + 'px');
        //     }
        
        $('#screenWarp ul li').append(obj);
    }

    $('#quickEntryModal').modal('hide');

    listenQuickEntry('#screenWarp ul li');
});



//生成预览图片
loadPreIMG();

//导航点击事件
listenHandleSwitch('#nav');

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

//监听导航组(日后有机会做成公用函数)
function listenHandleNavDrag(id, fn) {
    //控件拖拽前事件
    $(id)
    .on("mousedown", function(ev) {
        var oDiv = this;
        var e = ev||event;
        var $this = $(this);
        var scale = 100 / $('#desktopProportion').val();    //获取显示比例
        $('.selectHandle').removeClass('selectHandle');
        $(this).addClass('selectHandle');
        if ($('.selectHandle a:eq(0)').css('margin-right') === '0px') {
            var margin = $('.selectHandle a:eq(0)').css('margin-bottom').split('px');
            $('#navMargin').val(margin[0]);
        }else{
            var margin = $('.selectHandle a:eq(0)').css('margin-right').split('px');
            $('#navMargin').val(margin[0]);
        }

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

function listenQuickEntry(id) {
            //对快捷入口组成员进行监听
    listenHandleNavDrag(id + '> div', refreshNavPos);

    $('#navLeft').on('change', function() { //导航Left改变
        var left = parseInt(this.value.trim(), 10);
        $(id + ' div').css('left', left);
    });

    $('#navTop').on('change', function() { //导航Top改变
        var top = parseInt(this.value.trim(), 10);
        $(id + ' div').css('top', top);
    });

    $('#navMargin').on('change', function() { //导航间隔改变
        var margin = parseInt(this.value.trim(), 10);
        if ($(id + ' .selectHandle .row').length > 0) {
            $(id + ' .selectHandle a').css('margin-bottom', margin);
        }else{
            $(id + ' .selectHandle a').css('margin-right', margin);
        }
    });

    $(document).off('keydown').on('keydown', function(e) { //键盘改变导航
        var offset = e.shiftKey ? shiftKeyNum : 1;
        if ($(id + ' div a').length !== 0) {
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
}

function clearIconModal(type) {
    AjaxGet('/desktop/actionAppLists', selectApp);
    AjaxGet('/desktop/iconLists', function(handleData) {
        $('#iconModal h4').text(type);
        setIconInfo(type, handleData);
    });
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
        var $icon = $('#screenWarp .selectHandle');

        var idx = 0;
        if ($('#screenWarp .selectHandle .row').length > 0) {
            idx = $('#screenWarp .selectHandle .row a.active').parent().index();
        }else{
            idx = $('#screenWarp .selectHandle a.active').index();
        }
        var parentIdx = $icon.index();
        var iconData = myData.mList[parentIdx].extra[idx];
        $('#iconName').val(iconData.name);
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
    }/* else if (type === '添加控件') {
        $('#jumpType input:eq(0)').trigger('click');
        $('#iconName').val('');
        $('#iconPosX').val('');
        $('#iconPosY').val('');
    }*/
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

//为快捷入口添加、修改控件
$('#subIcon').on('click', function() {
    var name = $('#iconName').val();
    var iconId = $('#iconList').val();
    var title = $('#iconModal h4').text();
    var jumpType = $('#jumpType input:checked').val();
    var data = {};

    if (name == ' ' || !name) {
        alert('请输入控件名称');
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
            delete data.id;
            data.type = detailDate.actionType;
            delete data.actionType;
            data.appName = appData.appName;
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

    var idx = 0;
    if ($('#screenWarp ul li .selectHandle .row').length > 0) {
        idx = $('#screenWarp .selectHandle .row a.active').parent().index();
    }else{
        idx = $('#screenWarp .selectHandle a.active').index();
    }

    var parentIdx = $('#screenWarp .selectHandle').index();
    data.name = name;
    data.forcusPath = $('#reloadPic i:eq(1)').data('src');
    data.normalPath = $('#reloadPic i:eq(0)').data('src');
    /*if (title === '添加控件') {
        myData.mList.splice(idx + 1, 0, data);
    } else*/
     if (title === '修改控件') {
        myData.mList[parentIdx].extra[idx] = data;
    }

    var tmpData = {
        "extra": [{
            "id": myData.groupID,
            "name": myData.NavName,
            "mList": myData.mList
        }]
    }
    tmpD(tmpData);
    
    $('#iconModal').modal('hide');
});

function tmpD(data) {
    if (data.extra.length === 1) {
        var arr = data.extra.mList || data.extra[0].mList;
        var title = data.extra.name || '';
        var con = '';
        $('#screenWarp ul li').html('');
        for (var i = 0; i < arr.length; i++) { //创建icon
            //判断展现方式
            if (arr[i].layout === 'vertical') {
                for (var j = 0; j < arr[i].extra.length; j++) {
                    con = con + createQEGIcons(arr[i].extra[j], arr[i].distance, j);
                }
            }else{
                for (var j = 0; j < arr[i].extra.length; j++) {
                    con += createIcons(arr[i].extra[j], arr[i].direction, j);
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
            $('#screenWarp ul li').append(obj);
            con = '';
        }
    }else{
        $('#screenWarp ul li').html('');
    }

    listenQuickEntry('#screenWarp ul li');
    listenfocus();
}