//@ sourceURL=desktop.screen.js
var myData = {};
var desktopData = {};
/*检查对齐变量*/
var SOLT_OFFSET = 20;
var soltLeftData = [];
var soltTopData = [];
var soltRightData = [];
var soltBottomData = [];
$(function () {
	desktopData.screens = [];
	updateSelectScreen();

	listenchoose();
	listenScreenRadio();
	listenSlotsInfo();
	listenBlocksMove();
	listenBlockDrag();
	blocksFilter();
});

function updateSelectScreen(){
	AjaxGet('/desktop/fragmentLists', selectScreen);	//创建屏列表下拉框

	AjaxGet('/desktop/blockLists', createBlockList);	//创建块模板列表
	myData.screenId = null;
	myData.screenName = null;
	myData.slots = null;
	myData.slot = null;
}

function selectScreen(data){	//生成屏列表选项
    var arr = data.extra;
    var con = '<option value="空屏">空屏</option>';
    var $select = $('#screen');
    var len = arr.length;
    for( var i=0; i<len; i++ ){
        con += '<option value="'+ arr[i].id +'" data-name="'+ arr[i].name +'">'+ arr[i].name +'</option>';
    }
    if(!myData.newScreenName){
    	$select.html(con).trigger("chosen:updated.chosen").chosen({
	        allow_single_deselect: true,
	        width: "170px"
	    }).trigger('change');
        $('#screenSelect').show();
    }else{
    	var option = $select.html(con).find('option').filter('[data-name="'+myData.newScreenName+'"]');
    	option.prop("selected", true);
    	$select.trigger("chosen:updated.chosen").chosen({
	        allow_single_deselect: true,
	        width: "170px"
	    }).trigger('change');
    	myData.newScreenName = null;
        $('#screenSelect').show();
    }
    createScreenList(arr);
}

$('#screen').on('change', function(){	//监听选择屏事件
	myData.screenId = $(this).val();
	myData.screenName = $(this).find("option:selected").text();

	if(myData.screenId === '空屏'){
		$("#editScreen").hide();
		$("#delScreen").hide();
		$("#addBlock").hide();
		$("#delBlock").hide();
		$("#checkOffset").hide();
	}else{
		$("#editScreen").show();
		$("#delScreen").show();
		$("#addBlock").show();
		$("#delBlock").show();
		$("#checkOffset").show();
	}
	AjaxGet('/desktop/fragmentLists?id=' + myData.screenId, updateScreen);
});

function updateScreen(data){	//屏变化时更新屏
	$('.screen-block').remove();		//清空所有块
	var arr = data.extra.property || [];
	var len = arr.length;
	var title = data.extra.name || '';
    desktopData.screens[0] = {"blocks": []};
	for(var i = 0; i < len; i++){		//创建块
		$('#screenWarp ul li').append(createBlock(arr[i]));
        desktopData.screens[0].blocks.push({
            "x": arr[i].x,
            "y": arr[i].y,
            "w": arr[i].w,
            "h": arr[i].h,
            "bg": arr[i].bg,
            "yh": arr[i].yh,
            "yw": arr[i].yw,
            "type": arr[i].type
        });
	}
    myData.slots = desktopData.screens[0].blocks;
	$('#screenTitle').text(title);
	$('#screenSlots').text(len);
    setOpacity();
}

//新建屏
$('#addScreen').on('click', function(){
	$('#screenList').find('input:checked').prop('checked', false);
	$('#screenModal').modal('show');
});

//修改屏
$('#editScreen').on('click', function(){
	var data = {};
	var slot = {};
	var blocks = $('#screenWarp .screen-block');
	var property = [];
	if(myData.screenId === '空屏'){
		alert('当前为空屏');
		return false;
	}
	$('.selectBlock').removeClass("selectBlock");
	// for(var i = 0, len = blocks.length; i < len; i++){
	// 	var $block = $(blocks[i]);
 //        var bg = $block.getBackgroundColor();
 //        var opacity = bg.a / 100;
 //        var opacity16 = opacity ? (Math.round(opacity * 255)).toString(16) : '00';
	// 	slot = {
	// 		"x": parseInt($block.css('left')),
	// 		"y": parseInt($block.css('top')),
	// 		"w": $block.width(),
	// 		"h": $block.height(),
	// 		"yh": $block.data('yh'),
	// 		"yw": $block.data('yw'),
	// 		"bg": '#'+ opacity16 + bg.rgb.slice(-6)
	// 	};
	// 	property.push(slot);
	// }
	data = {"id": myData.screenId, "name": myData.screenName, "property": desktopData.screens[0].blocks};
	AjaxPost('/desktop/modifyFragment', data, function(){
		alert('修改成功');
		AjaxGet('/desktop/fragmentLists', function(data){//创建屏模板列表
			createScreenList(data.extra);
		});
		return ;
	});
	return false;
});

//删除屏
$('#delScreen').on('click', function(){
	if(confirm('确定删除屏？')){
		AjaxGet('/desktop/deleteFragment?id=' + myData.screenId, function(){
			alert('删除成功');
			updateSelectScreen();
		});
	}
	return false;
});

//新建屏提交
$('#subNewScreen').on('click', function(){
	var screenName = $('#screenName').val();
	if(screenName == ' ' || ! screenName){
        alert('请输入屏名称');
        return;
    }
    var $obj = $('#screenList').find('input:checked').parent().parent();
    var property = $obj.data('property') || [];
    var data = {"name": screenName, "property": property};

    AjaxPost('/desktop/addFragment', data, function(){
    	updateSelectScreen();
    	myData.newScreenName = screenName;
    	$('#screenModal').modal('hide');
    });
});

//添加块
$('#addBlock').on('click', function(){
	$('#myBlocks_filter input').val('').trigger('keyup');
	$('#blockModal').modal('show');
});

//删除块
$('#delBlock').on('click', function(){
    if ($('.selectBlock').length <= 0) {
        alert('请选择块');
        return;
    }

    if (confirm('确定删除块？')) {
        var num = 0;
        while($('.selectBlock').length){
            var $obj = $('.selectBlock:eq(0)');
            delSelectBlocks($obj);
            num++;
        }

        var $screenSlots = $('#screenSlots');
        $screenSlots.text(Number($screenSlots.text()) - num);
        clearSelectBlock();
    }
});

//检查对齐
$('#checkOffset').on('click', function(){
    $('.selectBlock').removeClass('selectBlock');
    var con = getSlotsPos();
    if(con){
        if( confirm(con + '\n----确定自动对齐？') ){
            setSlotOffset();
        }
    }else{
        alert('坑位已对齐');
    }
});

function getSlotsPos(){
    var con = '';
    soltLeftData = [];
    soltTopData = [];
    soltRightData = [];
    soltBottomData = [];
    var screens = desktopData.screens;
    screens.forEach(function(elem, i){
        var aLeft = [];
        var aTop = [];
        var aRight = [];
        var aBottom = [];
        elem.blocks.forEach(function(e){
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
    if(con){
        return con;
    }else{
        return false;
    }
}

//检查对齐左
function checkSlotLeft(arr, idx){
    var min = Math.min.apply(null, arr);
    soltLeftData[idx] = {
        "standard": min,
        "lists": []
    };
    var con = '';
    for(var i = 0, len = arr.length; i < len; i++){
        if(arr[i] > min && arr[i] < (min + SOLT_OFFSET)){
            con += '第' + (idx+1) +'屏最左边一列没对齐\n';
            soltLeftData[idx].lists.push(i);
        }
    }
    return con;
}

//检查对齐上
function checkSlotTop(arr, idx){
    var min = Math.min.apply(null, arr);
    soltTopData[idx] = {
        "standard": min,
        "lists": []
    };
    var con = '';
    for(var i = 0, len = arr.length; i < len; i++){
        if(arr[i] > min && arr[i] < (min + SOLT_OFFSET)){
            con = '第' + (idx+1) +'屏最上面一行没对齐\n';
            soltTopData[idx].lists.push(i);
        }
    }
    return con;
}

//检查对齐右
function checkSlotRight(arr, idx){
    var max = Math.max.apply(null, arr);
    soltRightData[idx] = {
        "standard": max,
        "lists": []
    };
    var con = '';
    for(var i = 0, len = arr.length; i < len; i++){
        if(arr[i] < max && arr[i] > (max - SOLT_OFFSET)){
            con = '第' + (idx+1) +'屏最右边一列没对齐\n';
            soltRightData[idx].lists.push(i);
        }
    }
    return con;
}

//检查对齐下
function checkSlotBottom(arr, idx){
    var max = Math.max.apply(null, arr);
    soltBottomData[idx] = {
        "standard": max,
        "lists": []
    };
    var con = '';
    for(var i = 0, len = arr.length; i < len; i++){
        if(arr[i] < max && arr[i] > (max - SOLT_OFFSET)){
            con = '第' + (idx+1) +'屏最下面一行没对齐\n';
            soltBottomData[idx].lists.push(i);
        }
    }
    return con;
}

//设置对齐
function setSlotOffset(){
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
    for(i = 0; i < len; i++){
        slots = $(screens[i]).find('.screen-block');
        l = soltLeftData[i].lists.length;
        for(j = 0; j < l; j++){
            idx = soltLeftData[i].lists[j];
            $(slots[idx]).css('left', soltLeftData[i].standard);
            desktopData.screens[i].blocks[idx].x = soltLeftData[i].standard;
        }
    }
    for(i = 0; i < len; i++){
        slots = $(screens[i]).find('.screen-block');
        l = soltTopData[i].lists.length;
        for(j = 0; j < l; j++){
            idx = soltTopData[i].lists[j];
            $(slots[idx]).css('top', soltTopData[i].standard);
            desktopData.screens[i].blocks[idx].y = soltTopData[i].standard;
        }
    }
    for(i = 0; i < len; i++){
        slots = $(screens[i]).find('.screen-block');
        l = soltRightData[i].lists.length;
        for(j = 0; j < l; j++){
            idx = soltRightData[i].lists[j];
            $slot = $(slots[idx]);
            val = soltRightData[i].standard - $slot.width();
            $slot.css('left', val);
            desktopData.screens[i].blocks[idx].x = val;
        }
    }
    for(i = 0; i < len; i++){
        slots = $(screens[i]).find('.screen-block');
        l = soltBottomData[i].lists.length;
        for(j = 0; j < l; j++){
            idx = soltBottomData[i].lists[j];
            $slot = $(slots[idx]);
            val = soltBottomData[i].standard - $slot.height();
            $slot.css('top', val);
            desktopData.screens[i].blocks[idx].y = val;
        }
    }
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

    $('#setAlignment').css('transform', 'scale('+ 100 / val +')');
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
        position = $selectBlock.position();
        l = 0 | position.left * width / 100;
        t = 0 | position.top * height / 100;
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