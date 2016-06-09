//@ sourceURL=live.group.js
var myData = {};
$(function () {

	AjaxGet('/liveParam/typeLists', selectType);	//初始化类型选项

    AjaxGet('/liveParam/groupLists', function(data){	//初始化组table
        createElem(data.extra);
        trHover('#myTable');
    });
    //参数列表按钮
    $('#myWidget .toolbar').html(myConfig.backBtn + myConfig.addBtn + myConfig.editBtn + myConfig.delBtn);

    trclick('#myTable', function(obj, e){		//组table点击事件
        myData.groupId = obj.data('id');
        myData.group = obj.data('group');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-align-justify') != -1){
        	$('#myTable_wrapper').hide();
        	$('#myWidget').show();
        	$('.breadcrumb').append('<li class="active">'+myData.group+'</li>');
        	AjaxGet('/liveParam/groupParamLists?group_id=' + myData.groupId, function(data){
		        createWidget(data.extra);
		    });
        }
    });

    listenchoose();
});

listenToolbar('add', addTableInfo);		//组添加事件
listenToolbar('del', delTableInfo);		//组删除事件
$('#myWidget .toolbar').on('click', 'a', function(){	//参数列表事件
	var idx = $(this).index();
	if(idx === 0){		//返回
		$('#myTable_wrapper').show();
		$('#myWidget').hide();
		$('.breadcrumb').find('li:last').remove();
	}else if(idx === 1){		//新增
		addWidgetInfo();
	}else if(idx === 2){		//修改
		editWidgetInfo();
	}else if(idx === 3){		//删除
		delWidgetInfo();
	}
});

function addTableInfo(){
    $('#groupName').val("");
    $('#myModal').modal('show');
}

function delTableInfo(){
    if(myData.groupId){
        if( confirm('确定删除？') ){
            AjaxGet('/liveParam/deleteGroup?group_id=' + myData.groupId, function(){
            	updateTable();
            });
        }
    }else{
        alert('请选择参数组！');
    }
}

function updateTable(){
	AjaxGet('/liveParam/groupLists', function(data){
	    createElem(data.extra);
	    myData.groupId = null;
	});
}

function addWidgetInfo(){
    $('#myModal2').modal('show');
}

function editWidgetInfo(){
	if(myData.paramId){
		AjaxGet('/liveParam/optionLists?param=' + myData.paramName, selectEditName);
	}else{
		alert('请选择参数！');
	}
}

function selectEditName(data){		//参数选项变化
	var arr = data.extra;
    var con = '';
    var $select = $('#c_editName');
    var len = arr.length;
    for( var i=0; i<len; i++ ){
        if(arr[i].default === 'true'){
            con += '<option value="'+ arr[i].value +'" data-default="true" style="color: red;">'+ arr[i].value +'</option>';
        }else{
            con += '<option value="'+ arr[i].value +'" data-default="false" style="color: #858585;">'+ arr[i].value +'</option>';
        }
    }
    $select.html(con).val(myData.editName).trigger('change');
    if(!$select.val()){
    	$select.get(0).selectIndex = 0;
    	$('#chooseCustom > input').eq(0).trigger('click');
    }else{
    	$('#chooseCustom > input').eq(1).trigger('click');
    }
    $('#editName').val(myData.editName);
	$('#myModal3').modal('show');
}

$('#c_editName').on('change', updateSelectColor);

function delWidgetInfo(){
	if(myData.paramId){
        if( confirm('确定删除？') ){
            AjaxGet('/liveParam/deleteGroupParam?id=' + myData.paramId, function(){
                updateWidget();
            });
        }
    }else{
        alert('请选择参数！');
    }
}

function updateWidget(){
	AjaxGet('/liveParam/groupParamLists?group_id=' + myData.groupId, function(data){
        createWidget(data.extra);
        myData.paramId = null;
    });
}

$('#subGroup').click(function() {		//组添加提交
    var groupName = $('#groupName').val();
    var data = {};

    if(groupName == ' ' || ! groupName){
        alert('请输入版本');
        return;
    }
    if(/\D/.test(groupName)){
    	alert('版本只能为数字');
        return;
    }
    data =  {"group": groupName};
    AjaxPost('/liveParam/addGroup', data, function(){
        $('#myModal').modal('hide');
        updateTable();
    });
});

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.group, null]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'版本','width':'25%', 'targets':1},
            {'title':'编辑','width':'10%', 'targets':2},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(2, nRow, 'align-justify');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "group":aData[1]
            });
        }
    });
    initToolBar('#myTable', [myConfig.addBtn, myConfig.delBtn]);
}

$('#myGroup').on('click', '> li', function(){	//高亮事件
	var $this = $(this);
	if($this.hasClass('widget-list-title')){
		return false;
	}
    $('#myGroup > li').removeClass('active');
    $this.addClass('active');
    myData.paramId = $this.data('id');
    myData.editName = $this.find('span').eq(1).text();
    myData.paramName = $this.find('span').eq(0).text();
});

function createWidget(data){
	typeArr = getByType(data);
	var con = '';
	var $group = $('#myGroup');
	var title = '<div style="position: relative;font-weight: 700;">'+
	    			'<span style="width: 33.3333%;">参数</span>'+
	    			'<span style="width: 33.3333%;">参数值</span>'+
	    			'<span style="width: 33.3333%;">描述</span>'+
	    		'</div>';
	for(var p in typeArr){
		var arr = typeArr[p];
		var len = arr.length;
		con += '<li class="widget-list-title">'+ p +'</li>';
		for(var i =0; i < len; i++){
			for(var j = 0; j < arr[i].length; j++){
				con += createGroup(arr[i][j]);
			}
		}
	}
	$group.html('').append(con);
	if($group.children().length < 18){
		$('#myWidget .widget-header').html('').append(title);
	}else{
		$('#myWidget .widget-header').html('').append(title).css('paddingRight', 16);
	}
}

function createGroup(data){		//参数列表模板
    var color = '';
    if(data.default === 'true'){
        color = 'red';
    }else{
        color = '#858585';
    }
    return  '<li data-id="'+data.id+'">'+
    			'<span style="width: 33.3333%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" title="'+ data.param +'">'+data.param+'</span>'+
    			'<span style="width: 33.3333%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;color:'+ color +';" title="'+ data.value +'">'+data.value+'</span>'+
    			'<span style="width: 33.3333%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" title="'+ data.desc +'">'+data.desc+'</span>'+
    		'</li>';
}

function getByType(data){		//根据类型重组数据
	var len = data.length;
	var typeArr = {};
	for(var i = 0; i < len; i++){
		if(!typeArr.hasOwnProperty(data[i].type)){
			typeArr[data[i].type] = [];
		}
		typeArr[data[i].type].push(data[i].params);
	}
	return typeArr;
}

function selectType (data) {
	var arr = data.extra;
    var con = '';
    var $select = $('#typeName');
    var len = arr.length;
    for( var i=0; i<len; i++ ){
        con += '<option value="'+ arr[i].id +'">'+ arr[i].type +'</option>';
    }
    $select.html(con).trigger("change");
}

$('#typeName').on('change', function(){		//类型改变事件
	var id = $(this).val();
	AjaxGet('/liveParam/paramLists?type_id=' + id, selectParam);
});

function selectParam(data){		//参数选项变化
	var arr = data.extra;
    var con = '';
    var $select = $('#paramName');
    var len = arr.length;
    for( var i=0; i<len; i++ ){
        con += '<option value="'+ arr[i].id +'">'+ arr[i].param +'</option>';
        $select.data('desc' + arr[i].id, arr[i].desc);
    }
    $select.html(con).trigger("change");
}

$('#paramName').on('change', function(){	//参数改变事件
	var id = $(this).val();
	var desc = $(this).data('desc' + id);
	AjaxGet('/liveParam/optionLists?param_id=' + id, selectAdvance);
	$('#descName').val(desc);
});

function selectAdvance(data){		//预选值变化
	var arr = data.extra;
    var con = '';
    var $select = $('#advanceName');
    var len = arr.length;
    for( var i=0; i<len; i++ ){
        if(arr[i].default === 'true'){
            con += '<option value="'+ arr[i].id +'" data-default="true" style="color: red;">'+ arr[i].value +'</option>';
        }else{
            con += '<option value="'+ arr[i].id +'" data-default="false" style="color: #858585;">'+ arr[i].value +'</option>';
        }
    }
    $select.html(con).trigger('change');
}

$('#advanceName').on('change', updateSelectColor);

function updateSelectColor(){
    var $this = $(this)
    var idx = $this.get(0).selectedIndex;
    var $option = $this.find('option:eq('+ idx +')');
    var color = $option.data('default');
    if(color === true){
        $this.css('color', 'rgb(255, 0, 0)');
        $this.data('default', 'true');
    }else{
        $this.css('color', '#858585');
        $this.data('default', 'false');
    }
}

$('#subParam').click(function() {		//参数列表添加提交
    var typeName = $('#typeName').find('option:selected').text();
    var paramName = $('#paramName').find('option:selected').text();
    var advanceName = $('#advanceName').find('option:selected').text();
    var descName = $('#descName').val();
    var data = {};

    if(typeName == ' ' || ! typeName){
        alert('请选择类型');
        return;
    }
    if(paramName == ' ' || ! paramName){
        alert('请选择参数');
        return;
    }
    if(advanceName == ' ' || ! advanceName){
        alert('请选择参数值');
        return;
    }
    var Default = $('#advanceName').data('default');
    data = {"group_id": myData.groupId, "param": paramName, "value": advanceName, "type": typeName, "desc": descName, "default": Default};
    AjaxPost('/liveParam/addGroupParam', data, function(){
        $('#myModal2').modal('hide');
        updateWidget();
    });
});

$('#editParam').click(function() {		//参数值修改
    var custom = $('#chooseCustom').find('input:checked').val();
    var editName = custom == 'false' ? $('#c_editName').val() : $('#editName').val();
    var Default = ''
    if(editName == ' ' || ! editName){
        alert('输入参数值');
        return;
    }
    if(custom == 'false'){
        var color = $('#c_editName').data('default');
        if(color){
            Default = 'true';
        }else{
            Default = 'false';
        }
    }else{
        Default = 'false';
    }
    AjaxGet('/liveParam/modifyGroupParam?id='+ myData.paramId +'&value=' + encodeURIComponent(editName) +'&default='+Default, function (){
    	$('#myModal3').modal('hide');
    	updateWidget();
    });
});

$('#chooseCustom > input').change(function(){   //自定义预选值事件
    $(this).prop('checked');
    var val = $(this).val();
    if(val == "false"){
        $('#editName').parent().hide();
        $('#c_editName').parent().show();
    }else{
        $('#editName').parent().show();
        $('#c_editName').parent().hide();
    }
});