//@ sourceURL=desktop.public.js

var shiftKeyNum = 10;	//shift键偏移量
var ABS_VALUE = 30;	//检查对齐偏移量
var HANDLE_IS_ABLE = false; //设置控件是否可移动变量

//在省略图导航上添加ICON
function setIconWidth(obj, src, margin) {
	preImage(src, function() {
		obj.append('<img src=' + src + ' tar="0" style="width: ' + this.width / 4 + 'px;margin-right: ' + margin + 'px;">');
	});
}

//在省略图快捷入口上添加ICON
function setIconQuick(obj, src, left, top) {
	preImage(src, function() {
		obj.append('<img src=' + src + ' tar="0" style="width: ' + this.width / 4 + 'px;left: ' + left + 'px;top: ' + top + 'px;position: absolute;">');
	});
}

//在省略图附件栏上添加ICON
function setIconHeight(obj, src, margin, type) {
	preImage(src, function() {
		obj.append('<img src=' + src + ' tar="0" style="width: ' + this.width / 4 + 'px;margin-bottom: ' + margin + 'px;">');
		if (type) {
			obj.width(this.width / 4);
		}
	});
}

//在屏幕上创建icon--导航
function createIcons(data, margin, i) {
	if (i === 0) {
		return '<a href="javascript:" class="active" style="display: block;float: left;margin-right:' + margin + 'px;"><img src=' + data.forcusPath + '></a>';
	} else {
		return '<a href="javascript:" style="display: block;float: left;margin-right:' + margin + 'px;"><img src=' + data.normalPath + '></a>';
	}
}

//快捷入口组横向专用
function createQEGIcons(data, margin, i) {
    if (i === 0) {
        return '<div class="row" style="margin-right:0px;margin-left:0px"><a href="javascript:" class="active" style="display: block;float: left;margin-bottom:' + margin + 'px;"><img src=' + data.forcusPath + '></a></div>';
    } else {
        return '<div class="row" style="margin-right:0px;margin-left:0px"><a href="javascript:" style="display: block;float: left;margin-bottom:' + margin + 'px;"><img src=' + data.normalPath + '></a></div>';
    }
}

//在屏幕上创建icon--两态控件
function createIconsTwo(data, i) {
	if (i === 0) {
		return '<a href="javascript;" class="active" style="display: block;float: left;"><img src=' + data.focusedActiveDrawable + '></a>';
	} else {
		return '<a href="javascript;" style="display: block;float: left;"><img src=' + data.activeDrawable + '></a>';
	}
}

//在屏幕上创建icon--三态控件
function createIconsThree(data, i) {
	if (i === 0) {
		return '<a href="javascript;" class="active" style="display: block;float: left;"><img src=' + data.focusedDrawableA + '></a>';
	} else {
		return '<a href="javascript;" style="display: block;float: left;"><img src=' + data.drawableA + '></a>';
	}
}

//在屏幕上创建icon--附件栏
function createIconsAttachment(data, margin, i) {
	var radiusTopLeft = data.radiusTopLeft + 'px';
	var radiusTopRight = data.radiusTopRight + 'px';
	var radiusBottomLeft = data.radiusBottomLeft + 'px';
	var radiusBottomRight = data.radiusBottomRight + 'px';
	if (i === 0) {
		return '<a href="javascript;" class="active" style="display: block;margin-bottom:' + margin + 'px;border-top-left-radius: ' + radiusTopLeft + ';border-top-right-radius: ' + radiusTopRight + ';border-bottom-right-radius: ' + radiusBottomLeft + ';  border-bottom-left-radius: ' + radiusBottomRight + ';"><img src=' + data.forcusPath + '></a>';
	} else {
		return '<a href="javascript;" style="display: block;margin-bottom:' + margin + 'px;border-top-left-radius: ' + radiusTopLeft + ';border-top-right-radius: ' + radiusTopRight + ';border-bottom-right-radius: ' + radiusBottomLeft + ';  border-bottom-left-radius: ' + radiusBottomRight + ';"><img src=' + data.normalPath + '></a>';
	}
}

//创建新建屏数据
function createScreenList(arr) {
	$('#screenList').html('');
	for (var i = 0, len = arr.length; i < len; i++) {
		var s = arr[i];
		var $div = $('<div class="screen"></div>');
		var slots = s.property;
		for (var j = 0, l = slots.length; j < l; j++) {
			var slot = slots[j];
			var $item = $('<div class="item"></div>');
			var bg = '#'+ slot.bg.slice(-6);
			$item.width(slot.w / 4);
			$item.height(slot.h / 4);
			$item.css("left", slot.x / 4);
			$item.css("top", slot.y / 4);
			$item.css('background-color', 'rgba(0, 0, 0, 0)');
			$item.css('box-shadow', ' 0 0 0 1px '+ bg +' inset');
			$div.append($item);
		}
		$div.append('<div class="radioBox"><input name="screenType" type="radio" class="ace" value="true"><span class="lbl">&nbsp;' + s.name + '</span>&emsp;</div></div>');
		$div.data("property", s.property);
		$('#screenList').append($div);
	}
}

//创建新建块列表
function createBlockList(data) {
	var arr = data.extra;
	$('#blockList').html('');
	for (var i = 0, len = arr.length; i < len; i++) {
		var slot = arr[i];
		slot.bg = '#33ACFF';
		slot.x = 30;
		slot.y = 30;
		var $div = $('<div class="item"><div class="item-block" style="background-color:rgba(0, 0, 0, 0);box-shadow: 0 0 0 1px '+ slot.bg +' inset;width:' + slot.w + 'px;height:' + slot.h + 'px;cursor:pointer;text-align:center; color:#fff; line-height:' + slot.h + 'px">' + slot.w + '*' + slot.h + '</div><div class="ace-spinner middle touch-spinner" style="width: 100px;margin: 10px auto 0;display: block;"><div class="input-group"><div class="spinbox-buttons input-group-btn"><button type="button" class="btn spinbox-down btn-xs btn-danger"><i class=" ace-icon ace-icon fa fa-minus smaller-75"></i></button></div><input type="text" class="input-mini spinbox-input form-control" value="1"><div class="spinbox-buttons input-group-btn"><button type="button" class="btn spinbox-up btn-xs btn-success"><i class=" ace-icon ace-icon fa fa-plus smaller-75"></i></button></div></div></div></div>');
		delete slot.id;
		delete slot.name;
		$div.data("slot", slot);
		$('#blockList').append($div);
	}
}

//监听块列表中块的点击事件
$('#blockList').on('click', '.item-block', function() {
	var $parent = $(this).parent();
	var num = $parent.find('input').val();
	var slot = $parent.data("slot");
	var idx = getScreenIdx();
	var $screen = $('#screenWarp ul li:eq(' + idx + ')');
	var count = $screen.find('.screen-block').length;
	var type = $('#blockType input:checked').val();
	var i = 0, data = {}, blockId = null;

	if(num == ' ' || !num){
		alert('请输入数量');
		return false;
	}
	if (/\D/.test(num)) {
        alert('数量只能为数字');
        return false;
    }

    if(type === 'globalQuickEntry'){	//全局快捷坑位
    	count = $('#quickSlotWarp .screen-block').length;
    	for(i = 0; i < num; i++){
	    	data = {};
	    	$.extend(data, slot, {"type": type});
			blockId = createBlockId(idx + 1, count + i + 1);
			delete data.yh;
			delete data.yw;
			data.slotId = blockId;
			$('#quickSlotWarp').append(createBlock(data, true, blockId, true));
			desktopData.quickEntrySlot.globalItems.push(data);
	    }
    }else{		//非全局坑位
    	for(i = 0; i < num; i++){
	    	data = {};
	    	$.extend(data, slot, {"type": type});
			if($('#screenDataWarp').length > 0){
				blockId = createBlockId(idx + 1, count + i + 1);
				if(type === 'common'){
					$.extend(data, {
			            "title": "",
			            "isEditable": "false",
			            "dataSource": "yunos",
			            "layout": "APP",
			            "operation": "true",
			            "operationId": blockId,
			            "slotId": blockId
			        });
				}else if(type === 'quickEntry'){
					delete data.yh;
					delete data.yw;
					data.slotId = blockId;
				}
				$screen.append(createBlock(data, false, blockId));
				$('#screenDataWarp ul li:eq(' + idx + ')').append(createBlock(data, true, blockId, true));
			}else{
				$screen.append(createBlock(data));
			}
			myData.slots.push(data);
	    }
	    var $screenSlots = $('#screenSlots');
		$screenSlots.text(Number($screenSlots.text()) + Number(num));
    }
	$('#blockModal').modal('hide');
	return false;
});

//监听块数量
$('#blockList').on('click', '.item button', function(){
	var $this = $(this);
	var type = $this.hasClass('spinbox-down') ? true : false;
	var $input = $this.parent().siblings('input');
	var val = $input.val();
	var count = 0;
	if (/\D/.test(val)) {
        count = 1;
    }else{
    	var temp = type ? -1 : 1;
    	count = Number(val) + temp;
    }

    if(count < 0){
    	count = 0;
    }
    $input.val(count);
});

//生成新的块
function createBlock(slot, data, id, val, noReq) {
	var con = '';
	var bg = '#' + slot.bg.slice(-6);
	var opacity = '0.2';
	if(slot.bg.length > 7){
		opacity = parseInt(slot.bg.slice(1, 3), 16) / 255;
	}else{
		slot.bg = '#33' + slot.bg.slice(-6);
	}
	var slotCornerRadius = desktopData.appConfig ? desktopData.appConfig.slotCornerRadius : '0';
	var slotType = 'common-block';
	var slotQuick = '';
	if(slot.type === 'common'){
		slotType = 'common-block';
	}else if(slot.type === 'quickEntry'){
		slotType = 'quick-block';
		slotQuick = '<div class="icon-quick"></div>';
	}else if(slot.type === 'globalQuickEntry'){
		slotType = 'quick-slot-block';
		slotQuick = '<div class="icon-quick-slot"></div>';
		slotCornerRadius = 0;
	}
	var block = $('<div class="screen-block '+ slotType +'" tabindex="0" data-yw="' + slot.yw + '" data-yh="' + slot.yh + '">'+ slotQuick +'</div>');
	block.css({
		"left": slot.x + 'px',
		"top": slot.y + 'px',
		"width": slot.w,
		"height": slot.h,
		"border-radius": slotCornerRadius + 'px',
		"background-color": bg.getRgbColor(opacity+''),
		"box-shadow": '0 0 0 1px '+ bg +' inset',
		"position": "absolute"
	});
	if (arguments.length === 1) {
		return block;
	}
	if (!data) {
		con = '<div class="block-id">' +
			'<span>' + id + '</span>' +
			'<input type="text" value="' + id + '" placeholder="' + id + '">' +
			'</div>';
		block.append(con);
	} else {
		con = '<div class="block-data" style="border-radius: '+ desktopData.appConfig.slotCornerRadius+'px">' +
			'<span>' + id + '</span>' +
			'<input type="text" value="' + id + '" placeholder="' + id + '">' +
			'<i class="ace-icon fa fa-pencil-square-o bigger-300 white"></i>' +
			'</div>';
		block.append(con);
		if (val) {
			if(noReq){//更新块绑定的数据
				updateBlockDate(block, slot);
			}else{
				updateBlockDate(block, slot, true);
			}
		}
	}
	return block;
}

//生成块ID
function createBlockId(p, q) {
	var id = '';
	if (q > 9) {
		id = '' + p + q;
	} else {
		id = '' + p + 0 + q;
	}
	return id;
}

//监听块拖拽事件
function listenBlockDrag() {
	//块拖拽前事件
	$("#screenWarp")
	.on("mousedown", ".screen-block", function(ev) {
		var oDiv = this;
		var e = ev||event;
		var $this = $(this);
		var nowIdx = $this.index();
		//按Ctrl，则判断为多选
		if(e.ctrlKey){
			$this.toggleClass("selectBlock");
			refreshSlotPos({
				"x": "",
				"y": "",
				"w": "",
				"h": "",
				"bg": "",
				"yh": "",
				"yw": ""
			});
		}
		//没按Ctrl，且点击的块不是多选的块之一，则判断为单选
		if(!e.ctrlKey && !$this.hasClass('selectBlock')){
			$('.selectBlock').removeClass("selectBlock");
			$('#slotInfo h4').text('坑位信息：');
			myData.slotIdex = $this.index();
			var bg = $this.getBackgroundColor();
			myData.slot = {
				"x": parseInt($this.css('left')),
				"y": parseInt($this.css('top')),
				"w": $this.width(),
				"h": $this.height(),
				"bg": bg.rgb,
				"yh": $this.attr('data-yh'),
				"yw": $this.attr('data-yw')
			};
			$this.addClass("selectBlock");
			$('.selectBlock').focus();
			refreshSlotPos(myData.slot);
			setOpacity(bg.a);
		}
		var scale = 100 / $('#desktopProportion').val();	//获取显示比例
		disX=e.clientX * scale -oDiv.offsetLeft;
		disY=e.clientY * scale -oDiv.offsetTop;
		$(document).off('mousemove').on('mousemove', function(ev){
			var e = ev || event;
			var l = parseInt(e.clientX * scale -disX);
            var t = parseInt(e.clientY * scale -disY);
            var lArr = [];
			if(!$(oDiv).hasClass('selectBlock')){
				$(oDiv).addClass('selectBlock');
			}
			if(l<0)
			{
				l=0;
			}
			if(t<0)
			{
				t=0;
			}
			else if(t > 720-oDiv.offsetHeight)
			{
				t = 720-oDiv.offsetHeight;
			}

			var selectBlocks = $('.selectBlock');
			var nowIdx = $this.index();
			var nowSlot = myData.slots[nowIdx];
			var offsetX = l -  Number(myData.slots[nowIdx].x);
			var offsetY = t -  Number(myData.slots[nowIdx].y);

			var len = selectBlocks.length;
			for(var i = 0; i < len; i++){
				$selectBlock = $(selectBlocks[i]);
				var idx = $selectBlock.index();
				myData.slots[idx].x = Number(myData.slots[idx].x) + offsetX;
				myData.slots[idx].y = Number(myData.slots[idx].y) + offsetY;
				lArr.push(myData.slots[idx]);
				$selectBlock.css({
					"left": Number(myData.slots[idx].x),
					"top": Number(myData.slots[idx].y)
				});
			}
			lArr.sort(function(a, b){
				return b.x - a.x;
			});
			var dom = $('#screenWarp li:eq('+ getScreenIdx() +')').get(0);
			if(offsetX > 0 && (lArr[0].x + Number(lArr[0].w)) >= dom.scrollWidth){
				dom.scrollLeft = l;
			}
	        if(offsetY > 0){
	        	dom.scrollTop = t;
	        }

			if(len > 1){
				refreshSlotPos({
					"x": "",
					"y": "",
					"w": "",
					"h": "",
					"bg": ""
				});
			}else{
				refreshSlotPos(myData.slots[nowIdx]);
			}
		});
		$(document).off('mouseup').on('mouseup', function(ev){
			var e = ev || event;
			var cls = e.target.className;
			$(document).off('mousemove');
			if (myData.clearSelect) {
				return false;
			}
			if (cls.indexOf('scale-set') !== -1 || cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1 || cls.indexOf('ui-slider-handle') !== -1) return false;
			setOpacity();
			clearSelectBlock();
			$('#setAlignment').hide();
			$(document).off('mouseup');
		});
		return false;
	});
}

//选中块样式隐藏事件
function selectedBlock() {
	$(document).off('click.selectedBlock').on('click.selectedBlock', function(e) {
		var cls = e.target.className;
		if (myData.clearSelect) {
			return false;
		}
		if (cls.indexOf('alignment-set') !== -1 || cls.indexOf('margin-set') !== -1 || cls.indexOf('screen-block') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;
		clearSelectBlock();
	});
}

//多选块右键菜单
$('#screenWarp').on('contextmenu', '.screen-block', function(ev){
	var e = ev || event;
	var oDiv = this;
	var len = $('.selectBlock').length;
	$('.selectHandle').removeClass('selectHandle');
	if(len <= 1){
		$('#setAlignment .alignment-set').hide();
	}else{
		$('#setAlignment .alignment-set').show();
	}
	var scrollLeft = $('#screenWarp li:eq('+ getScreenIdx()+')').scrollLeft();
	disX = oDiv.offsetLeft + e.offsetX - scrollLeft;
	disY = oDiv.offsetTop + e.offsetY;
	$('#setAlignment').css({
		"display": 'block',
		"left": disX,
		"top": disY - 100
	});
	$(document).off('click.block-contextmenu').on('click.block-contextmenu', function(ev){
		var e = ev || event;
		$('#setAlignment').css({
			"display": 'none'
		});
		$(document).off('click.block-contextmenu');
	});
	return false;
});

//多选快捷入口右键菜单
$('#quickWarp').on('contextmenu', 'a', function(ev){
	gobalHandleMenu(ev, this);
	return false;
});

//多选快捷坑位右键菜单
$('#quickSlotWarp').on('contextmenu', '.screen-block', function(ev){
	gobalHandleMenu(ev, this);
	return false;
});

//全局控件右键菜单
function gobalHandleMenu(ev, oDiv){
	var e = ev || event;
	var len = $('.selectHandle').length;
	$('.selectBlock').removeClass('selectBlock');
	if(len <= 1){
		return true;
	}
	disX = oDiv.offsetLeft + e.offsetX;
	disY = oDiv.offsetTop + e.offsetY;
	$('#setQuickAlignment').css({
		"display": 'block',
		"left": disX,
		"top": disY - 100
	});
	$(document).off('click.handle-contextmenu').on('click.handle-contextmenu', function(ev){
		var e = ev || event;
		$('#setQuickAlignment').css({
			"display": 'none'
		});
		$(document).off('click.handle-contextmenu');
	});
	return false;
}

//对齐事件
$('#setAlignment').on('click', 'a', function(ev){
	var e = ev || event;
	setSelectedAlignment(e.target.text);
	$('#setAlignment').css({
		"display": 'none'
	});
	return false;
});

//对齐
function setSelectedAlignment(str){
	var selectBlocks = $('.selectBlock');
	var len = selectBlocks.length;
	var data = [];
	var i = 0, j = 0;
	var $selectBlock = null;
	var idx = 0;
	for(i = 0; i < len; i++){
		$selectBlock = $(selectBlocks[i]);
		idx = $selectBlock.index();
		data.push(myData.slots[idx]);
	}

	if(str === '等比列缩放'){
		$('#scaleModal').modal('show');
		$('#scaleAll input:eq(1)').prop('checked', true);
		$('#scaleBased input:eq(0)').prop('checked', true);
		$('#scaleWidth').val('100');
		$('#scaleHeight').val('100');
		myData.clearSelect = true;
	}else if(str === '横向间隔对齐'){
		for (i = 0; i < len; i++) {
	        for (j = i; j < len; j++) {
	            if (Number(data[i].x) > Number(data[j].x)) {//交换两个元素的位置
	                data[i] = [data[j], data[j] = data[i]][0];
	                selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
	            }
	        }
	    }
	    var offsetX = Number(data[1].x) - (Number(data[0].x) + Number(data[0].w));
	    for (i = 2; i < len; i++){
	    	data[i].x = (Number(data[i-1].x) + Number(data[i-1].w)) + offsetX;
	    	$(selectBlocks[i]).css('left', data[i].x);
	    }
	}else if(str === '纵向间隔对齐'){
		for (i = 0; i < len; i++) {
	        for (j = i; j < len; j++) {
	            if (Number(data[i].y) > Number(data[j].y)) {//交换两个元素的位置
	                data[i] = [data[j], data[j] = data[i]][0];
		            selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
	            }
	        }
	    }
	    var offsetY = Number(data[1].y) - (Number(data[0].y) + Number(data[0].h));
	    for (i = 2; i < len; i++){
	    	data[i].y = (Number(data[i-1].y) + Number(data[i-1].h)) + offsetY;
	    	$(selectBlocks[i]).css('top', data[i].y);
	    }
	}else if(str === '左对齐'){
		data.sort(function(obj1, obj2){
			return Number(obj1.x) - Number(obj2.x);
		});
		var minLeft = Number(data[0].x);
		for (i = len; i--;){
	    	data[i].x = minLeft;
	    	$(selectBlocks[i]).css('left', data[i].x);
	    }
	}else if(str === '右对齐'){
		for (i = 0; i < len; i++) {
	        for (j = i; j < len; j++) {
	            if (Number(data[i].x) + Number(data[i].w) < Number(data[j].x) + Number(data[j].w)) {//交换两个元素的位置
	                data[i] = [data[j], data[j] = data[i]][0];
		            selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
	            }
	        }
	    }
		var maxRight = Number(data[0].x) + Number(data[0].w);
		for (i = len; i--;){
	    	data[i].x = maxRight - Number(data[i].w);
	    	$(selectBlocks[i]).css('left', data[i].x);
	    }
	}else if(str === '上对齐'){
		data.sort(function(obj1, obj2){
			return Number(obj1.y) - Number(obj2.y);
		});
		var minTop = Number(data[0].y);
		for (i = len; i--;){
	    	data[i].y = minTop;
	    	$(selectBlocks[i]).css('top', data[i].y);
	    }
	}else if(str === '下对齐'){
		for (i = 0; i < len; i++) {
	        for (j = i; j < len; j++) {
	            if (Number(data[i].y) + Number(data[i].h) < Number(data[j].y) + Number(data[j].h)) {//交换两个元素的位置
	                data[i] = [data[j], data[j] = data[i]][0];
		            selectBlocks[i] = [selectBlocks[j], selectBlocks[j] = selectBlocks[i]][0];
	            }
	        }
	    }
		var maxBottom = Number(data[0].y) + Number(data[0].h);
		for (i = len; i--;){
	    	data[i].y = maxBottom - Number(data[i].h);
	    	$(selectBlocks[i]).css('top', data[i].y);
	    }
	}
	return false;
}

//选中控件样式隐藏事件
function selectedHandle() {
	$(document).off('click.selectedHandle').on('click.selectedHandle', function(e) {
		var cls = e.target.className;
		if (myData.clearSelect) {
			return false;
		}
		if (e.target.tagName === "IMG" && $(e.target).parent().hasClass('selectHandle')) return false;
		if (e.target.tagName === "IMG" && $(e.target).parent().parent().hasClass('selectHandle')) return false;
		if (cls.indexOf('handle-font') !== -1 || cls.indexOf('selectHandle') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;

		$('.selectHandle').removeClass("selectHandle");
		$(document).off('click.selectedHandle');
		if($('.selectBlock').length <= 0){
			refreshSlotPos({
				"x": "",
				"y": "",
				"w": "",
				"h": "",
				"bg": ""
			});
		}
	});
}

//快捷入口选中控件样式隐藏事件
function selectedHandleQuick() {
	$(document).off('click.selectedHandle').on('click.selectedHandle', function(e) {
		var cls = e.target.className;
		if (myData.clearSelect) {
			return false;
		}
		if (e.target.tagName === "IMG") return false;
		if (cls.indexOf('handle-font') !== -1 || cls.indexOf('selectHandle') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;

		$('.selectHandle').removeClass("selectHandle");
		$(document).off('click.selectedHandle');
		if($('.selectBlock').length <= 0){
			refreshSlotPos({
				"x": "",
				"y": "",
				"w": "",
				"h": "",
				"bg": ""
			});
		}
	});
}

//全局快捷坑位选中控件样式隐藏事件
function selectedSlotQuick() {
	$(document).off('click.selectedHandle').on('click.selectedHandle', function(e) {
		var cls = e.target.className;
		if (myData.clearSelect) {
			return false;
		}
		if (cls.indexOf('handle-font') !== -1 || cls.indexOf('selectHandle') !== -1 || cls.indexOf('slot-data') !== -1 || cls.indexOf('block-data') !== -1 || cls.indexOf('block-title') !== -1 || cls.indexOf('icon-quick-slot') !== -1 || cls.indexOf('bigpicker') !== -1 || cls.indexOf('biglayout') !== -1) return false;

		$('.selectHandle').removeClass("selectHandle");
		$(document).off('click.selectedHandle');
		if($('.selectBlock').length <= 0){
			refreshSlotPos({
				"x": "",
				"y": "",
				"w": "",
				"h": "",
				"bg": ""
			});
		}
	});
}

//清除选中非全局块痕迹
function clearSelectBlock(del) {
	$('.selectBlock').removeClass("selectBlock");
	$(document).off('click.selectedBlock');
	myData.slot = null;
	myData.slotIdex = null;
	refreshSlotPos({
		"x": "",
		"y": "",
		"w": "",
		"h": "",
		"bg": ""
	});
}

//删除选中非全局块
function delSelectBlocks($obj){
	var idx = getScreenIdx();
	var i = $obj.index();
	var $dataBlock = $('#screenDataWarp ul li:eq(' + idx + ')').find('.screen-block:eq(' + i + ')');
	$obj.remove();
	$dataBlock.remove();
	myData.slots.splice(i, 1);
}

function delSelectQuicks($obj){
	var i = $obj.index();
	$obj.remove();
	desktopData.quickEntrySlot.globalItems.splice(i, 1);
}


//刷新块的位置
function refreshSlotPos(slot) {
	var l = slot.x ? parseInt(slot.x) : 0;
	var t = slot.y ? parseInt(slot.y) : 0;
	var w = slot.w ? parseInt(slot.w) : 0;
	var h = slot.h ? parseInt(slot.h) : 0;
	$('#slotLeft').val(l);
	$('#slotTop').val(t);
	$('#slotWidth').val(w);
	$('#slotHeight').val(h);
	$('#slotBgd').val(slot.bg ? '#'+slot.bg.slice(-6) : '');
}

//颜色以十六进制显示
$.fn.getBackgroundColor = function() {
	var rgb = $(this).css('background-color');
	var a = 100;
	if(rgb === 'transparent'){
		a = 0;
		rgb = $(this).css('box-shadow').split(' 0px')[0];
	}
	if (rgb >= 0) return rgb; //如果是一个hex值则直接返回
	else {
		rgb = rgb.match(/^rgba\((\d+),\s*(\d+),\s*(\d+),\s*(.*)\)$/) || rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		if(rgb[4]){
			a = Math.round(parseFloat(rgb[4]) * 100);
		}
		rgb = "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}
	return {"rgb": rgb, "a": a};
};

$.fn.changeBackgroundColor = function(a) {
	var rgb = $(this).css('backgroundColor');
	if(rgb === 'transparent'){
		rgb = $(this).css('box-shadow').split(' 0px')[0];
	}
	if (rgb >= 0) return rgb; //如果是一个hex值则直接返回
	else {
		rgb = rgb.match(/^rgba\((\d+),\s*(\d+),\s*(\d+),\s*(.*)\)$/) || rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);

		rgb = 'rgba('+ rgb[1] +', '+ rgb[2] +', '+ rgb[3] +', '+ a +')';
	}
	return $(this).css('backgroundColor', rgb);
};

//获取hex值
function hex(x) {
	return ("0" + parseInt(x).toString(16)).slice(-2);
}

//颜色以rgba显示
String.prototype.getRgbColor = function(a){
	var _this = this.slice(1);
	a = a || 1;
	return 'rgba('+ rgb(_this.slice(0, 2)) +', '+ rgb(_this.slice(2, 4)) +', '+ rgb(_this.slice(4)) +', '+ a +')' ;
};

function rgb(x){
	return parseInt(x, 16) + '';
}

//随机生成背景颜色
function getBgColor() {
	return "#" + ("00000" + ((Math.random() * 16777215 + 0.5) >> 0).toString(16)).slice(-6);
}

//新建屏radio事件
function listenScreenRadio() {
	$('#screenList').on('click', '.screen .radioBox > span.lbl', function() {
		$(this).prev('input').trigger('click');
	});
}

//监听坑位信息事件
function listenSlotsInfo() {
	$('#slotLeft').on('change', function() { //块Left改变
		var blockSelected = $('.selectBlock');
		var handleSelected = $('.selectHandle');
		var left = parseInt(this.value.trim(), 10);
		if (blockSelected.length > 0) {
			blockSelected.css('left', left);
			setSlotXY('X', left);
			for(var i = 0, len = blockSelected.length; i < len; i++){
				var idx = $(blockSelected[i]).index();
				myData.slots[idx].x = left + '';
			}
		} else if (handleSelected.length > 0) {
			handleSelected.css('left', left);
			setHandleXY(handleSelected, 'x', left);
		}
		return false;
	});

	$('#slotTop').on('change', function() { //块Top改变
		var blockSelected = $('.selectBlock');
		var handleSelected = $('.selectHandle');
		var top = parseInt(this.value.trim(), 10);
		if (blockSelected.length > 0) {
			blockSelected.css('top', top);
			setSlotXY('Y', top);
			for(var i = 0, len = blockSelected.length; i < len; i++){
				var idx = $(blockSelected[i]).index();
				myData.slots[idx].y = top + '';
			}
		} else if (handleSelected.length > 0) {
			handleSelected.css('top', top);
			setHandleXY(handleSelected, 'y', top);
		}
		return false;
	});

	$('#slotBgd').bigColorpicker(function(elem, color) { //生成颜色选择器
		elem.value = color;
		changeBlockColor();
	});

	$('#slotBgd').on('blur', changeBlockColor); //颜色输入框变化事件

	$('#slotWidth').on('change', function() { //块Width改变
		var blockSelected = $('.selectBlock');
		var width = parseInt(this.value.trim(), 10);
		if (blockSelected.length > 0) {
			blockSelected.css('width', width);
			for(var i = 0, len = blockSelected.length; i < len; i++){
				var idx = $(blockSelected[i]).index();
				myData.slots[idx].w = width + '';
			}
		}
		return false;
	});

	$('#slotHeight').on('change', function() { //块Left改变
		var blockSelected = $('.selectBlock');
		var height = parseInt(this.value.trim(), 10);
		if (blockSelected.length > 0) {
			blockSelected.css('height', height);
			for(var i = 0, len = blockSelected.length; i < len; i++){
				var idx = $(blockSelected[i]).index();
				myData.slots[idx].h = height + '';
			}
		}
		return false;
	});

	$('#slotInfo input').on('keyup', function(ev){
	    var e = ev || event;
	    if(ev.keyCode === 13){
	        $(this).trigger('blur');
	    }
	});
}

//设置控件位置
function setHandleXY(obj, type, val) {
	var id = obj.get(0).id;
	val = parseInt(val);
	if (id === 'logoWarp') {
		desktopData.logo[type] = val;
	} else if (id === 'navWarp') {
		desktopData.nav[type] = val;
	} else if (id === 'timerWarp') {
		desktopData.timebar[type] = val;
	} else if (id === 'weatherWarp') {
		desktopData.weather[type] = val;
	} else if (id === 'timeWeatherWarp') {
		desktopData.timeWeather[type] = val;
	} else if (id === 'snWarp') {
		desktopData.sn[type] = val;
	} else if (id === 'attachmentWarp') {
		desktopData.attachment[type] = val;
	} else {
		var idx = obj.index();
		var parent = obj.parent().get(0).id;
		if(type == 'x'){
			if(parent === 'quickWarp'){
				desktopData.quickEntry.extraData[idx].itemX = val;
			}else if(parent === 'threeWarp'){
				desktopData.quickEntryThreeState.extraData[idx].x = val;
			}else if(parent === 'twoWarp'){
				desktopData.quickEntryTwoState.extraData[idx].x = val;
			}else if(parent === 'quickSlotWarp'){
				desktopData.quickEntrySlot.globalItems[idx].x = val;
			}else if (parent === 'quickEntryGroupWarp') {
				desktopData.quickEntryGroup.mList[Number(id)].x = val;
			}
		}else if(type == 'y'){
			if(parent === 'quickWarp'){
				desktopData.quickEntry.extraData[idx].itemY = val;
			}else if(parent === 'threeWarp'){
				desktopData.quickEntryThreeState.extraData[idx].y = val;
			}else if(parent === 'twoWarp'){
				desktopData.quickEntryTwoState.extraData[idx].y = val;
			}else if(parent === 'quickSlotWarp'){
				desktopData.quickEntrySlot.globalItems[idx].y = val;
			}else if (parent === 'quickEntryGroupWarp') {
				desktopData.quickEntryGroup.mList[Number(id)].y = val;
			}
		}
	}
}

//监听按键操作块事件
function listenBlocksMove() {
	//键盘改变选中块
	$('#pageWrap').on('keydown', '.selectBlock', function(e) {
		if (e.keyCode === 46) {
			$('#delBlock').trigger('click');
			return false;
		}
		if (HANDLE_IS_ABLE) {
			return false;
		}
		notAllSelect(e);
		return false;
	});

	$(document).off('keydown').on('keydown', function(e) { //键盘改变所有块
		if (HANDLE_IS_ABLE) {
			return;
		}
		if(e.target.tagName.toUpperCase() === "INPUT"){	//当目标为文本框则取消事件
    		return;
    	}
		var len = $('.selectBlock').length;
		if(len > 0){
			notAllSelect(e);
		}else{
			var offset = e.shiftKey ? shiftKeyNum : 1;
			var idx = getScreenIdx();
			if ($('#screenWarp ul li:eq(' + idx + ') div').length !== 0) {
				if (e.keyCode === 38) {
					setScreenXY("y", -offset, idx);
				} else if (e.keyCode === 40) {
					setScreenXY("y", offset, idx);
				} else if (e.keyCode === 37) {
					setScreenXY("x", -offset, idx);
				} else if (e.keyCode === 39) {
					setScreenXY("x", offset, idx);
				}
				if (e.keyCode === 37 || e.keyCode === 38 || e.keyCode === 39 || e.keyCode === 40) {
					return false;
				}
			}
		}
	});

	//非全选块事件
	function notAllSelect(e){
		var offset = e.shiftKey ? shiftKeyNum : 1;
		if (e.keyCode === 38) {
			setSlotXY("y", -offset);
		} else if (e.keyCode === 40) {
			setSlotXY("y", offset);
		} else if (e.keyCode === 37) {
			setSlotXY("x", -offset);
		} else if (e.keyCode === 39) {
			setSlotXY("x", offset);
		}
	}
}

//更新选中块位置
function setSlotXY(type, num) {
	var sel = $('.selectBlock');
	var slot = null;
	var offsetX = 0;
	var offsetY = 0;
	for(var i = 0; i < sel.length; i++){
		var $block = $(sel[i]);
		var idx = $block.index();
		slot = myData.slots[idx];
		if (type == "y") {
			offsetY = Number(slot.y) + num;
			slot.y = offsetY >= 0 ? offsetY : 0;
			$block.css('top', slot.y);
			$('#slotTop').val(slot.y);
		} else if (type == 'x') {
			offsetX = Number(slot.x) + num;
			slot.x = offsetX >= 0 ? offsetX : 0;
			$block.css('left', slot.x);
			$('#slotLeft').val(slot.x);
		} else if (type == 'X') {
			slot.x = num;
			$block.css('left', slot.x);
			$('#slotLeft').val(slot.x);
		} else if (type == 'Y') {
			slot.y = num;
			$block.css('top', slot.y);
			$('#slotTop').val(slot.y);
		}
	}
}

//更新所有块位置
function setScreenXY(type, num, idx) {
	var offsetX = 0;
	var offsetY = 0;
	if (myData.slots) {
		var temp = [];
		for (var i = 0; i < myData.slots.length; i++) {
			var slot = myData.slots[i];
			var $block = $("#screenWarp ul li").eq(idx).children(".screen-block").eq(i);
			if (type == "x") {
				offsetX = Number(slot.x) + num;
				slot.x = offsetX >= 0 ? offsetX : 0;
				temp.push(slot.x);
				$block.css("left", slot.x);
			} else {
				offsetY = Number(slot.y) + num;
				slot.y = offsetY >= 0 ? offsetY : 0;
				temp.push(slot.y);
				$block.css("top", slot.y);
			}
			updateSlotsData(slot);
		}
		if(type == "x"){
			refreshSlotPos({
				"x": Math.min.apply(null, temp),
				"y": "",
				"w": "",
				"h": "",
				"bg": "",
				"yh": "",
				"yw": ""
			});
		}else{
			refreshSlotPos({
				"x": "",
				"y": Math.min.apply(null, temp),
				"w": "",
				"h": "",
				"bg": "",
				"yh": "",
				"yw": ""
			});
		}
	}
}

//更新myData.slots缓存数据
function updateSlotsData(slot) {
	// refreshSlotPos(slot);
	try {
		myData.slots[myData.slotIdex].x = slot.x + '';
		myData.slots[myData.slotIdex].y = slot.y + '';
	} catch (e) {
		console.log(e);
		console.log(slot.x.toString());
	}
}

//块颜色变化
function changeBlockColor() {
	var blockSelected = $('.selectBlock');
	var quickSelected = $('#quickSlotWarp .selectHandle');
	var i = 0, idx = 0, opacity = 0, opacity16 = 0, $block = null, color = '';
	if (blockSelected.length > 0) {
		color = $('#slotBgd').val();
        for(i = 0, len = blockSelected.length; i < len; i++){
            $block = $(blockSelected[i]);
            idx = $block.index();
            opacity = $block.getBackgroundColor().a / 100;
    		opacity16 = opacity ? (Math.round(opacity * 255)).toString(16) : '00';
            $block.css({
            	'backgroundColor': color,
            	'boxShadow': color + ' 0px 0px 0px 1px inset'
            }).changeBackgroundColor(opacity);
            myData.slots[idx].bg = '#'+ opacity16 + color.slice(-6);
        }
	}else if(quickSelected.length > 0){
		color = $('#slotBgd').val();
        for(i = 0, len = quickSelected.length; i < len; i++){
            $block = $(quickSelected[i]);
            idx = $block.index();
            opacity = $block.getBackgroundColor().a / 100;
    		opacity16 = opacity ? (Math.round(opacity * 255)).toString(16) : '00';
            $block.css({
            	'backgroundColor': color,
            	'boxShadow': color + ' 0px 0px 0px 1px inset'
            }).changeBackgroundColor(opacity);
            desktopData.quickEntrySlot.globalItems[idx].bg = '#'+ opacity16 + color.slice(-6);
        }
	}
}

//获取当前屏序号
function getScreenIdx() {
	var idx = $('#navWarp a.active').index();
	idx = idx === -1 ? 0 : idx;
	return idx;
}

//导航、快捷入口、附件栏，控件增删改事件
function handleIcon(id) {
	//添加控件事件
	$('#addIcon').on('click', function() {
		AjaxGet('/desktop/actionLists', function(data) {
			$('#iconModal h4').text('添加控件');
			selectActionType(data);
			AjaxGet('/desktop/iconLists', function(handleData) {
				setIconInfo('添加控件', handleData);
			});
		});
	});

	//修改控件事件
	$('#editIcon').on('click', function() {
		AjaxGet('/desktop/actionLists', function(data) {
			$('#iconModal h4').text('修改控件');
			selectActionType(data);
			AjaxGet('/desktop/iconLists', function(handleData) {
				setIconInfo('修改控件', handleData, data);
			});
		});
	});

	//删除控件事件
	$('#delIcon').on('click', function() {
		if (confirm('确定删除？')) {
			var $icon = $(id + 'Warp a.active');
			var idx = $icon.index();
			$icon.remove();
			var $frist = $(id + 'Warp a:eq(0)');
			if (id === '#nav') {
				myData.iconIds.splice(idx, 1);
			} else {
				myData.extra.splice(idx, 1);
			}
			myData.iconSrc.splice(idx, 1);
			if ($frist.length > 0) {
				$frist.addClass('active').find('img').attr('src', myData.iconSrc[0].forcusPath);
			}
		}
	});
}

//生成预览图片
function loadPreIMG() {
	$('#iconList').on('change', function() {
		var name = $(this).find('option:selected').text();
		if (name === '请选择控件') {
			$('#reloadPic').html('');
			return false;
		}
		var data = $('#iconList').data('_' + name);
		var con = '<label class="icon-name" data-id=' + data.id + '>' + name + ':</label>' +
			'<span>正常状态</span><i class="glyphicon glyphicon-picture icon-black my-icon" data-src=' + data.normalPath + '></i>' +
			'<span>焦点状态</span><i class="glyphicon glyphicon-picture icon-black my-icon" data-src=' + data.forcusPath + '></i>';
		$('#reloadPic').html(con);
		return false;
	});
}

//控件点击切换状态事件
function listenHandleSwitch(type) {
	$(type + 'Warp').on('click', 'a', function() {
		var $now = $(this);
		if ($now.hasClass('active')) {
			return false;
		}
		var $old = $(type + 'Warp a').filter('.active');
		var oIdx = $old.index();
		var nIdx = $now.index();
		preImage(myData.extra[nIdx].forcusPath, function() {
			$now.addClass('active').find('img').attr('src', myData.extra[nIdx].forcusPath);
			$old.removeClass('active').find('img').attr('src', myData.extra[oIdx].normalPath);
		});
		return false;
	});
}

function blocksFilter() {
	$('#myBlocks_filter').on('keyup', 'input', function() {
		var val = $(this).val().toLowerCase();
		var blocks = $('#blockList .item');
		for (var i = 0, len = blocks.length; i < len; i++) {
			var $block = $(blocks[i]);
			if ($block.text().toLowerCase().indexOf(val) != -1) {
				$block.show();
			} else {
				$block.hide();
			}
		}
		return false;
	});
}

function listenHandleDrag(id, fn) {
    //控件拖拽前事件
    $(id)
    .on("mousedown", function(ev) {
        var oDiv = this;
        var e = ev||event;
        var $this = $(this);
        var scale = 100 / $('#desktopProportion').val();	//获取显示比例
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

//设置块的透明度
function setOpacity(num){
    num = num || 0;
    var $obj = $('#slotOpacity');
    $obj.css({width:'90%', 'float':'left', margin:'15px 0'}).slider({
        value: num,
        range: "min",
        animate: true,
        slide: blockSlider
    });
    if(num === 0){
    	$obj.find('.tooltip').hide();
    }else{
    	$obj.find('.tooltip').show();
    	$obj.find('.tooltip-inner').text(num);
    }
}

function blockSlider(event, ui){
    if( !ui.handle.firstChild ) {
        $("<div class='tooltip top in slot-data' style='display:none;left:-5px;top:-31px;'><div class='tooltip-arrow slot-data'></div><div class='tooltip-inner slot-data' style='padding: 3px;'></div></div>")
        .prependTo(ui.handle);
    }
    $(ui.handle.firstChild).show().children().eq(1).text(ui.value);
    var blockSelected = $('.selectBlock');
    var quickSelected = $('#quickSlotWarp .selectHandle');
    var opacity = ui.value/100;
    var opacity16 = opacity ? (Math.round(opacity * 255)).toString(16) : '00';
    var i = 0, $block = null, idx = 0;
    if (blockSelected.length > 0) {
        for(i = 0, len = blockSelected.length; i < len; i++){
            $block = $(blockSelected[i]);
            idx = $block.index();
            $block.changeBackgroundColor(opacity);
            myData.slots[idx].bg = '#'+ opacity16 + myData.slots[idx].bg.slice(-6);
        }
    }else if(quickSelected.length > 0) {
    	for(i = 0, len = quickSelected.length; i < len; i++){
            $block = $(quickSelected[i]);
            idx = $block.index();
            $block.changeBackgroundColor(opacity);
            desktopData.quickEntrySlot.globalItems[idx].bg = '#'+ opacity16 + desktopData.quickEntrySlot.globalItems[idx].bg.slice(-6);
        }
    }
}