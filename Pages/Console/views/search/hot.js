//@ sourceURL=search.hot.js
var myData = {};
var check = 0;
$(function () {
    AjaxGet('/search/hotConfigLists', function(data){	//初始化组table
        createElem(data.extra);
        trHover('#myTable');
    });
    //参数列表按钮
    $('#hotWords .toolbar').html(myConfig.backBtn + myConfig.addBtn + myConfig.editBtn + myConfig.delBtn);

    trclick('#myTable', function(obj, e){		//组table点击事件
        myData.versionId = obj.data('id');
        myData.publish = obj.data('publish');
        myData.words = obj.data('words');
        var tar = e.target;
        if( tar.className.indexOf('glyphicon-align-justify') != -1){
        	$('#myTable_wrapper').hide();
        	$('#hotWords').show();
        	$('.breadcrumb').append('<li class="active">'+myData.publish+'</li>');
            createHotWord(myData.words);
        }
    });

    listenchoose();
    $("#versionName").datetimepicker({
        minView: "day",
        format: 'yyyy-mm-dd hh:00',
        language: 'zh-CN',
        autoclose: true
    }).val('');
});

listenToolbar('add', addTableInfo);		//组添加事件
listenToolbar('del', delTableInfo);		//组删除事件
listenToolbar('edit', editTableInfo);     //组修改事件
$('#hotWords .toolbar').on('click', 'a', function(){	//参数列表事件
	var idx = $(this).index();
	if(idx === 0){		//返回
		$('#myTable_wrapper').show();
		$('#hotWords').hide();
		$('.breadcrumb').find('li:last').remove();
	}else if(idx === 1){		//新增
        addHotWord();
	}else if(idx === 2){		//修改
        editHotWord();
	}else if(idx === 3){		//删除
        delHotWord();
        
	}
});

function addTableInfo(){
    $('#myModal h4').html('新增');
    $('#versionName').val("");
    $('#myModal').modal('show');
}

function editTableInfo() {
    if(myData.versionId){
        $('#myModal h4').html('修改');
        $('#versionName').val(myData.publish);
        $('#myModal').modal('show');
    }else{
        alert('请选择参数组！');
    }
}

function delTableInfo(){
    if(myData.versionId){
        if( confirm('确定删除？') ){
            AjaxGet('/search/deleteHotConfig?id=' + myData.versionId, function(){
            	updateTable();
            });
        }
    }else{
        alert('请选择参数组！');
    }
}

function updateTable(){
	AjaxGet('/search/hotConfigLists', function(data){
	    createElem(data.extra);
	    myData.versionId = null;
	});
}

function selectHotWord(data){
    var arr = data.extra;
    var con = '';
    var $select = $('#hotWord');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].name+'">'+arr[i].name+'</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "70%"
    });
}

function addHotWord(){
    $('#myModal2 h4').html('新增');
    AjaxGet('/search/getHotName', function(data) {
        selectHotWord(data);
        $('#hotWord').on('change', function() {
            if($(this).val().length > 6){
                $('#hotWord .chosen-choices li:last a').trigger('click');
                
            }
        });
        $('#hotWord_chosen .chosen-drop').prepend('<div class="chosen-search"><input class="custom-search" type="text" autocomplete="off" style="width: 100%;"></div>');
        $('#hotWord_chosen .chosen-search input').on('change', function() {
            $('#hotWord').html('');
            AjaxGet('/search/getHotName?name='+$('#hotWord_chosen .chosen-search input').val(), function(newData){
                if (newData.extra.length <= 0) {
                    alert('未能取得相关热词数据！');
                    return;
                }
                selectHotWord(newData);
            });
        });
    });
    $('#myModal2').modal('show');
}

function editHotWord(){
    $('#myModal2 h4').html('修改');
    $('#hotWord1').val('');
    $('#hotWord2').val('');
    $('#hotWord3').val('');
    $('#hotWord4').val('');
    $('#hotWord5').val('');
    $('#hotWord6').val('');
    for (var i = 0; i < $('#myGroup li.active span').length; i++) {
        if ($('#myGroup li.active span:eq(' + i + ')').text() != '') {
            $('#hotWord' + (i+1).toString() + '').val($('#myGroup li.active span:eq(' + i + ')').text());
        }
    }
    $('#myModal2').modal('show');
}


function delHotWord(){
	if(myData.keyNum >= 0){
        if( confirm('确定删除？') ){
            $('#myGroup li:eq(' + myData.keyNum + ')').remove();
            myData.words.splice(myData.keyNum, 1);
            var data = {
                "id": myData.versionId,
                "publish": myData.publish,
                "words": myData.words
            };
            AjaxPost('/search/modifyHotConfig', data, function(){
                updateHotWord();
            });
        }
    }else{
        alert('请选择参数！');
    }
}

function updateHotWord(){
	AjaxGet('/search/hotConfigLists?id=' + myData.versionId, function(data){
        createHotWord(data.extra.words);
        myData.keyNum = null;
    });
}

$('#subVersion').click(function() {		//组添加提交
    var versionName = $('#versionName').val();
    var data = {};

    if(versionName == ' ' || ! versionName){
        alert('请选择版本');
        return;
    }
    if(!(/^([0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:00)$/.test(versionName))){
    	alert('请选择正确的版本');
        return;
    }
    if ($('#myModal h4').html() === '新增') {
        data =  {
            "publish": versionName,
            "words":[]
        };
        AjaxPost('/search/addHotConfig', data, function(){
            $('#myModal').modal('hide');
            updateTable();
        });
    }else{
        data =  {
            "id": myData.versionId,
            "publish": versionName,
            "words":myData.words
        };
        AjaxPost('/search/modifyHotConfig', data, function(){
            $('#myModal').modal('hide');
            updateTable();
        });
    }
});

function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.id, arr.publish, arr.time, arr.words]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'发布时间','width':'25%', 'targets':1},
            {'title':'修改时间','width':'25%', 'targets':2},
            {'title':'热词','width':'10%', 'targets':3},
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(3, nRow, 'align-justify');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "publish":aData[1],
                "time":aData[2],
                "words":aData[3]
            });
        }
    });
    initToolBar('#myTable', [myConfig.addBtn, myConfig.editBtn, myConfig.delBtn]);
}

$('#myGroup').on('click', '> li', function(){	//高亮事件
	var $this = $(this);
	if($this.hasClass('widget-list-title')){
		return false;
	}
    $('#myGroup > li').removeClass('active');
    $this.addClass('active');
    myData.keyNum = $this.index();
});



function createHotWord(data){
	var con = '';
	var $group = $('#myGroup');
	var title = '<div style="position: relative;font-weight: 700;">'+
	    			'<span style="width: 16.6666%;">热词1</span>'+
	    			'<span style="width: 16.6666%;">热词2</span>'+
	    			'<span style="width: 16.6666%;">热词3</span>'+
                    '<span style="width: 16.6666%;">热词4</span>'+
                    '<span style="width: 16.6666%;">热词5</span>'+
                    '<span style="width: 16.6666%;">热词6</span>'+
	    		'</div>';
	for(var j = 0;j < data.length;j++){
		con += createGroup(data[j].keys);
	}
	$group.html('').append(con);
	if($group.children().length < 18){
		$('#hotWords .widget-header').html('').append(title);
	}else{
		$('#hotWords .widget-header').html('').append(title).css('paddingRight', 16);
	}
}

function createGroup(data){		//参数列表模板
    var str = '<li>';
    for (var i = 0; i < data.length; i++) {
        str += '<span style="height: 39px;width: 16.6666%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" title="'+ data[i] +'">'+data[i]+'</span>';
    }
    if (data.length < 6) {
        for (var i = 0; i < (6 - data.length); i++) {
            str += '<span style="height: 39px;width: 16.6666%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" title=""></span>';
        }
    }
    str += '</li>'
    return str;
}

$('#subHotWords').on('click', function() {
    var keys = {"keys": []};
    for (var i = 0; i < 6; i++) {
        if ($('#hotWord' + (i+1).toString()).val() === '') {
            alert('热词' + (i+1).toString() + '不能为空！');
            return;
        }else{
            keys.keys.push($('#hotWord' + (i+1).toString()).val());
        }
    }

    //验证热词组中是否有重复元素（返回不了值，待考究）
    keyOnly(keys.keys);
    if (check == 1) {
        check = 0;
        return;
    }
    for (var i = 0; i < 6; i++) {
        if ($('#hotWord' + (i+1).toString()).val() != '' ) {
            if (/[`~!@#\$%\^\&\*\(\)_\+<>\?:"\{\},\.\\\/;'\[\]]+/.test($('#hotWord' + (i+1).toString()).val())) {
                alert('请输入正确的热词！');
                return;
            }
        }
    }
    if($('#myModal2 h4').html() === '修改'){
        myData.words[$('#myGroup li.active').index()] = keys;
    }else{
        myData.words.push(keys);
    }
    var data = {
        "id": myData.versionId,
        "publish": myData.publish,
        "words": myData.words
    };
    AjaxPost('/search/modifyHotConfig', data, function() {
        updateHotWord();
        $('#myModal2').modal('hide');
    });
});

function keyOnly(arr){
    if (arr.length == 1) {
        return;
    }
    for (var i = 1; i < arr.length; i++) {
        if (arr[0] === arr[i]) {
            check = 1;
            alert('请输入不重复的热词！');
            return;
        }
    }
    keyOnly(arr.slice(1));
}