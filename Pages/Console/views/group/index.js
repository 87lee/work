//@ sourceURL=group.js
var myData = {};
$(function () {
	myData.checkedLists = [];
    AjaxGet('/group/nameLists', function(data){
        createElem(data.groups);
        trHover('#myTable');
    });

    trclick('#myTable', function(obj, e){
        myData.parentId = myData.id = obj.data('id');
        myData.cn_name = obj.data('cn_name');

        var tar = e.target;
        if( tar.className.indexOf('glyphicon-align-justify') != -1){
            if(!checkDetailPer($(tar), 'group')){
                alert('权限不够');
                return;
            }
            AjaxGet('/group/memberLists?group_id=' + myData.id, function(data){
                $('#fristTable').hide();
                $('#secondTable').show();
                createElem2(data.members);
                //trHover('#myTable2');
                $('.breadcrumb').append('<li class="active">'+myData.cn_name+'</li>');
            });
        }
    });

   /*trclick('#myTable2', function(obj, e){
        myData.id2 = obj.data('id');
        myData.cn_name2 = obj.data('cn_name');
        myData.name2 = obj.data('name');
		
    });*/
	listenSingleCheckBox('#myTable2');

    listenchoose();
    listenfile();
    $('.mac-name').autotab();
});

listenToolbar('edit', editTableInfo);
listenToolbar('add', addTableInfo);
listenToolbar('mac', macTableInfo);
listenToolbar('del', delTableInfo);

listenToolbar('add', addTableInfo2, '#myTable2');
listenToolbar('del', delTableInfo2, '#myTable2');
listenToolbar('import', importInfo, '#myTable2');
listenToolbar('back', backTable, '#myTable2');

function editTableInfo(){
    if(myData.id){
        $('#cn_name').val(myData.cn_name);
        $('#myModal').find('h4').html('修改');
        $('#myModal').modal('show');
    }else{
        alert('请选择内测组！');
    }
}

function addTableInfo(){
    $('#cn_name').val("");
    $('#myModal').find('h4').html('添加组');
    $('#myModal').modal('show');
}

function macTableInfo(){
    if(myData.id){
        addTableInfo2();
        $('#myModal2 h4').text('添加设备');
    }else{
        alert('请选择内测组！');
    }
}

function addTableInfo2(){
    $('.mac-name').val("");
    $('#desc_name').val("");
    $('#sn_name').val("");
    $('#myModal2 h4').text('添加');
    $('#myModal2').modal('show');
    $('#deviceType > input:eq(0)').trigger('click');
}

function importInfo(){
    $('#fileShow').val();
    $('#fileHide').val();
    $('#importModal').modal('show');
}

function delTableInfo(){
    if(myData.id){
        if( confirm('确定删除？') ){
            AjaxGet('/group/deleteName?group_id=' + myData.id, function(){
                AjaxGet('/group/nameLists', function(data){
                    createElem(data.groups);
                    myData.id = null;
                });
            });
        }
    }else{
        alert('请选择内测组！');
    }
}

function delTableInfo2(){
	console.log(myData.checkedLists);
	console.log(myData.parentId);
    if(myData.checkedLists.length){
        if( confirm('确定删除？') ){
			AjaxPost('/group/deleteMember',myData.checkedLists,function(){
				AjaxGet('/group/memberLists?group_id=' + myData.parentId, function(data){
                    createElem2(data.members);
                });
			});
			
            /*AjaxGet('/group/deleteMember?member_id=' + myData.id2, function(){
                AjaxGet('/group/memberLists?group_id=' + myData.parentId, function(data){
                    createElem2(data.members);
                    myData.id2 = null;
                });
            });*/
        }
    }else{
        alert('请选择内测组！');
    }
}

function backTable(){
    $('#secondTable').hide();
    $('#fristTable').show();
    $('.breadcrumb').find('li:last').remove();
    myData.id2 = null;
}

$('#importType input').on('click', function(){
    var $this = $(this);
    var val = $this.val();
    $('#importType').siblings('.form-group').hide();
    if(val === 'true'){
        $('#importType').siblings('.import-true').show();
    }else if(val === 'false'){
        $('#importType').siblings('.import-false').show();
    }
});

$('#deviceType > input').on('click', function(){
    var $this = $(this);
    $this.prop('checked', true);
    if($this.val() === 'mac'){
        $('.mac-name').parent().show();
        $('#sn_name').parent().hide();
    }else if($this.val() === 'sn'){
        $('.mac-name').parent().hide();
        $('#sn_name').parent().show();
    }
});

$('#subGroup').click(function() {
    var cn_name = $('#cn_name').val();
    var data = {};

    if(cn_name == ' ' || ! cn_name){
        alert('请输入内测组名称');
        return;
    }
    var title = $('#myModal').find('h4').html();
    if(title == '添加组'){
        data = {"group_name": $.trim(cn_name)};
        AjaxPost('/group/Addname', data, function(){
            $('#myModal').modal('hide');
            AjaxGet('/group/nameLists', function(data){
                createElem(data.groups);
            });
        });
    }else if(title == '修改'){
        data = {"group_name": $.trim(cn_name), "group_id": myData.id};
        AjaxPost('/group/modifyName', data, function(){
            $('#myModal').modal('hide');
            AjaxGet('/group/nameLists', function(data){
                createElem(data.groups);
            });
        });
    }
});

$('#subMac').click(function() {
    var type = $('#deviceType > input:checked').val();
    var mac_name = $('.mac-name');
    var sn_name = $('#sn_name').val();
    var desc_name = $('#desc_name').val();
    var data = {};
    var title = $('#myModal2').find('h4').html();
    var sn = '';

    if(type === 'mac'){
        var mac = [];
        for(var i = mac_name.length; i--;){
            var $mac = $(mac_name[i]);
            var val = $mac.val();
            if(val == ' ' || !val || val.length != 2){
                alert('Mac格式不正确');
                return;
            }
            mac.unshift(val);
        }
        sn = mac.join(':');
    }else if(type === 'sn'){
        if(!sn_name || sn_name == ' '){
            alert('请输入sn');
            return;
        }
        sn = sn_name;
    }

    data = {"group_id": myData.parentId, "sn": sn, "desc": desc_name};
    AjaxPost('/group/addMember', data, function(){
        $('#myModal2').modal('hide');
        AjaxGet('/group/memberLists?group_id=' + myData.parentId, function(data){
            createElem2(data.members);
        });
    });
});

$('#subImport').click(function() {
    var data = new FormData();
    var fileObj = document.getElementById("fileHide").files[0];
    var fileVal = $("#fileShow").val();
    if(fileVal != ' ' && fileVal.indexOf('http') == -1){
        data.append("mac", fileObj);
    }
    if(fileVal == ' ' || !fileVal){
        alert('请选择要上传的文件');
        return;
    }
    data.append("group_id", myData.parentId);
    AjaxFile('/group/addMember', data, function(){
        $('#importModal').modal('hide');
        AjaxGet('/group/memberLists?group_id=' + myData.parentId, function(data){
            createElem2(data.members);
        });
    });
    return false;
});


function createElem(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push([arr.group_id, arr.group_name, null]);
    }
    myDataTable('#myTable', {
        "data": dataArr,
        "columnDefs": [
            {'title':'ID','width':'10%', 'targets':0},
            {'title':'组名称','width':'25%', 'targets':1},
            {'title':'组成员列表','width':'10%', 'targets':2}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            tableTdIcon(2, nRow, 'align-justify');
            $('td:eq(0)', nRow).data({
                "id":aData[0],
                "cn_name":aData[1]
            });
        }
    });
    initToolBar('#myTable', [
        '<a class="btn my-btn btn-success addBtn" href="javascript:"><i class="glyphicon glyphicon-plus icon-white"></i>&nbsp;新增组</a>',
        '<a class="btn my-btn btn-success macBtn" href="javascript:"><i class="glyphicon glyphicon-plus icon-white"></i>&nbsp;新增设备</a>',
        myConfig.editBtn,
        myConfig.delBtn
    ]);
}

function createElem2(data) {
    var dataArr = [];
    var len = data.length;
    for( var i=0; i<len; i++ ) {
        var arr = data[i];
        dataArr.push(['', arr.member_id, arr.sn, arr.desc]);
    }
    myDataTable('#myTable2', {
        "data": dataArr,
        "columnDefs": [
		{
            'title': '<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>',
            'width': '5%',
            'targets': 0,
            "orderable": false
        },
            {'title':'ID','width':'10%', 'targets':1},
            {'title':'Mac','width':'20%', 'targets':2},
            {'title':'描述','width':'30%', 'targets':3}
        ],
        "createdRow": function( nRow, aData, iDataIndex ){
            $('td:eq(0)', nRow).data({
                "id":aData[1],
            }).html('<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
        }
    });
    initToolBar('#myTable2', [myConfig.backBtn, myConfig.addBtn, '<a class="btn my-btn btn-success importBtn" href="javascript:"><i class="glyphicon glyphicon-plus icon-white"></i>&nbsp;批量导入</a>', myConfig.delBtn]);
	listenCheckBox('#myTable2');
    updateChecked('#myTable2');
}