//@ sourceURL=live.listPublish.js

var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
    getNameId();
    // myData.checkedLists = [];
	AjaxGet('/Live/listPublishLists?page=1&pageSize=' + pageSize, function(data) {
        createElem(data, 1);
        trHover('#myTable');
    });

    trclick('#myTable', function (obj, e) {
        myData.groupId = obj.data('groupId');
        myData.type = obj.data('type');
        myData.releaseId = obj.data('id');
        myData.AB = obj.data('AB');
        myData.name = obj.data('name');
        myData.model = obj.data('model');
        myData.vendorID = obj.data('vendorID');

        $('.groupBtn').attr('disabled', false);
        $('.ABBtn').attr('disabled', false);
        $('.allBtn').attr('disabled', false);

        if(myData.type === 'AB'){
            $('.groupBtn').attr('disabled', true);
        }else if(myData.type === 'ALL'){
            $('.groupBtn').attr('disabled', true);
            $('.ABBtn').attr('disabled', true);
            $('.allBtn').attr('disabled', true);
        }

        var tar = e.target;
        if(tar.className.indexOf('glyphicon-list sn-list') != -1){
             AjaxGet('/group/memberLists?group_id=' + myData.groupId, function(data){
                createSN(data);
                trHover('#macTable');
                $('#macModal').modal('show');
            });
            return;
        }
    });

    listenchoose();
    listenMyPage();

    getGroupId();
});


listenToolbar('under', underTableInfo);
listenToolbar('release', releaseTableInfo);
listenToolbar('group', releaseInfo);
listenToolbar('AB', releaseInfo);
listenToolbar('all', releaseInfo);

//获取groupid
function getGroupId() {
    $.get('/group/nameLists', function (data) {
        data = JSON.parse(data);
        var arr = data.groups;
        var con = '';
        var $select = $('#groupid');
        for( var i=0; i<arr.length; i++ ){
            con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
        }
        $select.html(con);
    });
}

//获取groupid
function getNameId() {
    $.get('/Live/getListName', function (data) {
        data = JSON.parse(data);
        var arr = data.extra;
        myData.nameIdList = arr;
        var con = '';
        var $select = $('#name');
        for( var i=0; i<arr.length; i++ ){
            con += '<option value="'+arr[i].nameId+'">'+arr[i].name+'</option>';
        }
        $select.html(con);
    });
}

function underTableInfo() {
    $('#modelType').siblings().show();
    $('#modelType').show();
    $('#AB').parent().hide();
    $('#id').parent().hide();
    if (myData.releaseId) {
        if (confirm('确定下架？')) {
            var filter = $('#myTable_filter input').val() || '';
            AjaxPost('/Live/deleteListPublish', [myData.releaseId], function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择内容！');
    }
}

function releaseTableInfo(){
    $('#modelType').siblings().show();
    $('#modelType').show();
    $('#AB').parent().hide();
    $('#id').parent().hide();
    $('#id').val('');
    $('#vendorID').val('none');
    $('#modelType input:eq(0)').trigger('click');
    $('#chooseModel input:eq(0)').trigger('click');
    $('#mapModal').modal('show');
}

function releaseInfo(){
    if (!myData.releaseId) {
        alert('请选择内容！');
        return false;
    }
    var $this = $(this);
    var str = $this.text().trim();
    $('#editModal h4').text(str);
    if(str === '内测'){
        AjaxGet('/group/nameLists', function(data){
            selectGroup(data, $('#customGroup'));
            $('#customGroup').val(myData.groupId);
            $('.edit-group').show();
            $('.edit-count').hide();
        });
    }else if(str === '灰度'){
        $('#customCountNum').val(myData.AB == '--' ? '' : myData.AB);
        $('.edit-group').hide();
        $('.edit-count').show();
    }else{
        var filter = $('#myTable_filter input').val() || '';
        AjaxPost('/Live/modifyListPublish', {
            "type": 'ALL',
            "id": myData.releaseId
        }, function(){
            updateTable(currentPage, filter);
            $('#editModal').modal('hide');
        });
        return;
    }
    $('#editModal').modal('show');
}

//创建内测包下拉框
function selectGroup(data, $obj){
    var arr = data.groups;
    var con = '';
    var $select = $obj || $('#group');
    for( var i=0; i<arr.length; i++ ){
        con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
    }
    $select.html(con);
}

function updateTable(page, name){
    name = name || '';
    $.get('/Live/listPublishLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        data = JSON.parse(data);
        createElem(data, page);
        myData.releaseId = null;
    });
}

$('#subLive').on('click', function(){
	var id = $('#id').val();
	var vendorID = $('#vendorID').val();
    var name = $('#name').val();
    var type = $('#modelType input:checked').val();
    var AB = $('#AB').val();
    var groupId = $('#groupid').val();
	var filter = $('#myTable_filter input').val() || '';
    var data = {};

	if(id == ' ' || !id){
		alert('请输入型号ID');
		return false;
	}
	if(vendorID == ' ' || !vendorID){
		alert('请输入vendorID');
		return false;
	}
    if(name == ' ' || !name){
        alert('请输入名称');
        return false;
    }
    if (type === 'AB') {
        if(AB == ' ' || !AB || AB == '--'){
            alert('请输入灰度');
            return false;
        }
    }

    if(type === "group"){
    	data = {
    		"model": id,
    		"vendorID": vendorID,
            "nameId": name,
            "type": type,
            "groupId": groupId
    	};
    }else if(type === "ALL"){
        data = {
            "model": id,
            "vendorID": vendorID,
            "nameId": name,
            "type": type
        };
    }else if(type === "AB"){
        data = {
            "model": id,
            "vendorID": vendorID,
            "nameId": name,
            "type": type,
            "AB": AB
        };
    }

	AjaxPost('/Live/addListPublish', data, function(){
        $('#id').val('ALL');
        $('#modelType input:eq(0)').trigger('click');
		$('#mapModal').modal('hide');
        updateTable(currentPage, filter);
	});
});

$('#subEdit').on('click', function(){
    var title = $('#editModal h4').text();

    var filter = $('#myTable_filter input').val() || '';
    var data = {"id": myData.releaseId};

    if(title === '内测'){
        data.groupId = $('#customGroup').val();
        data.type = 'group';
    }else if(title === '灰度'){
        var countNum = $('#customCountNum').val();
        if(countNum == ' ' || !countNum){
            alert('请输入灰度数量');
            return;
        }else if(/\D/.test(countNum)){
            alert('灰度数量只能为数字');
            return;
        }
        data.AB = countNum;
        data.type = 'AB';
    }

    AjaxPost('/Live/modifyListPublish', data, function(){
        updateTable(currentPage, filter);
        $('#editModal').modal('hide');
    });
});

//内测/公开/灰度变化事件
$('#modelType > input').on('click', function(){
    var $this = $(this);
    $this.prop('checked', true);
    var val = $this.val();
    if(val === 'group'){
        $('#groupid').parent().show();
        $('#AB').parent().hide();
    }else if(val === 'ALL'){
        $('#groupid').parent().hide();
        $('#AB').parent().hide();
    }else if(val === 'AB'){
        $('#groupid').parent().hide();
        $('#AB').parent().show();
    }
});

$('#chooseModel > input').on('click', function(){   //全型号和自定义
    $(this).prop('checked');
    var val = $(this).val();
    if(val == "ALL"){
        $('#id').val(val);
        $('#id').parent().hide();
    }else{
        $('#id').val('');
        $('#id').parent().show();
    }
});

//创建桌面列表
function createElem(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.model + '-' + arr.id, arr.vendorID, arr.name, arr.type, arr.AB || '--', arr.groupId || '--', formatDate(arr.time)]);
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
        "order": [[6, "desc"]],
        "columnDefs": [
            {'title':'型号','width':'13%', 'targets':0},
            {'title':'vendorID','width':'13%', 'targets':1},
            {'title':'名称','width':'13%', 'targets':2},
            {'title':'类型','width':'13%', 'targets':3},
            {'title':'灰度','width':'13%', 'targets':4},
            {'title':'设备列表','width':'13%', 'targets':5},
            {'title':'发布时间','width':'13%', 'targets':6}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            var temp = aData[0].split('-');

            tableTypeColor(3, nRow, aData[3]);
            if(aData[3] == 'group'){
                tableTdIcon(5, nRow, 'list sn-list');
            }else{
                tableTdNull(5, nRow);
            }
            $('td:eq(0)', nRow).data({
                "id": temp[1],
                "model": temp[0],
                "vendorID": aData[1],
                "name": aData[2],
                "type": aData[3],
                "AB": aData[4],
                "groupId": aData[5]
            }).html(temp[0]);
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count);
    initToolBar('#myTable', [
        myConfig.releaseBtn,
        myConfig.underBtn,
        '<a class="btn my-btn btn-primary groupBtn" href="javascript:">&nbsp;内测</a>',
        '<a class="btn my-btn btn-primary ABBtn" href="javascript:">&nbsp;灰度</a>',
        '<a class="btn my-btn btn-primary allBtn" href="javascript:">&nbsp;公开</a>'
    ]);

    // listenCheckBox();
    // updateChecked();
}

function createSN(data){
    var dataArr = [];
    var len = data.members.length || 0;
    for( var i=0; i<len; i++ ) {
        var arr = data.members[i];
        dataArr.push([arr.sn, arr.desc]);
    }
    myDataTable('#macTable', {
        "data": dataArr,
        "pageLength": 10,
        "columnDefs": [
            {'title':'Mac', 'width':'40%', 'targets':0},
            {'title':'desc', 'width':'60%', 'targets':1}
        ]
    });
    $('#macTable_filter').prepend('<h4 style="position: absolute;margin: 6px 0;">'+ (data.group_name || '') +'</h4>');
}