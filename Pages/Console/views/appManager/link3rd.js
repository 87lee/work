//@ sourceURL=appManager.link3rd.js
var myData = {};
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //当前的页面

$(function () {
	AjaxGet('/App/app3rdLinkLists?page=1&pageSize='+pageSize, function(data) {
        createLink(data, 1);
        trHover('#linkTable');
    });

    trclick('#linkTable', function(obj, e) {
        myData.linkId = obj.data('id');
        myData.linkName = obj.data('name');
        myData.linkDesc = obj.data('desc');
        myData.linkReal = obj.data('link');
        myData.linkCopy = obj.data('copy');

        $('#copyContent').val(myData.linkCopy.split('-')[1]);
    });

    ZeroClipboard.config({swfPath: 'assets/swf/ZeroClipboard.swf'});
    listenMyPage('linkTable', currentPage);
});

listenToolbar('add', addTableInfo, '#linkTable');
listenToolbar('del', delTableInfo, '#linkTable');
listenToolbar('edit', editTableInfo, '#linkTable');
// listenToolbar('copy', copyTableInfo, '#linkTable');

function addTableInfo(){
	$('#linkName').val('');
    $('#linkReal').val('');
    $('#linkDesc').val('');
    $('#linkModal h4').text('添加');
    $('#linkModal').modal('show');
}

function editTableInfo(){
    if (myData.linkId) {
        $('#linkName').val(myData.linkName);
        $('#linkReal').val(myData.linkReal);
        $('#linkDesc').val(myData.linkDesc);
        $('#linkModal h4').text('修改');
        $('#linkModal').modal('show');
    } else {
        alert('请选择链接！');
    }
}

function delTableInfo(){
	if (myData.linkId) {
        if (confirm('确定删除？')) {
            var filter = $('#linkTable_filter input').val() || '';
            AjaxGet('/App/delete3rdLink?id=' + myData.linkId, function() {
                updateTable(currentPage, filter);
            });
        }
    } else {
        alert('请选择链接！');
    }
}

function copyTableInfo(){
    if (myData.linkId) {
        $('#copyContent').val(myData.linkCopy);
    } else {
        alert('请选择链接！');
    }
}

function updateTable(page, name){
    name = name || '';
	AjaxGet('/App/app3rdLinkLists?name='+name+'&page='+ page +'&pageSize='+pageSize, function(data){
        createLink(data, page);
        myData.linkId = null;
    });
}

$('#subLink').on('click', function(){
    var linkName = $('#linkName').val();
    var linkReal = $('#linkReal').val();
    var linkDesc = $('#linkDesc').val() || '';
    var title = $('#linkModal h4').text();
    var filter = $('#linkTable_filter input').val() || '';
    var data = {};

    if(linkName == ' ' || !linkName){
        alert('请输入名称');
        return false;
    }
    if(linkReal.indexOf('http://') === -1 && linkReal.indexOf('https://') === -1){
        alert('请输入重定向');
        return false;
    }

    data = {"name": linkName, "desc": linkDesc, "url": linkReal};

    if(title === '添加'){
    	AjaxPost('/App/add3rdLink', data, function(){
            $('#linkModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }else if(title === '修改'){
        data.id = myData.linkId;
        AjaxPost('/App/modify3rdLink', data, function(){
            $('#linkModal').modal('hide');
            updateTable(currentPage, filter);
        });
    }
});

//创建链接列表
function createLink(data, page) {
    var dataArr = [];
    var len = data.extra.length;
    for (var i = 0; i < len; i++) {
        var arr = data.extra[i];
        dataArr.push([arr.name, arr.id + '-' + arr.urlId, arr.url, arr.desc]);
    }
    $('#linkTable').dataTable({
        "lengthChange": false,
        "autoWidth": false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "data": dataArr,
        "order": [
            [0, "desc"]
        ],
        "columnDefs": [
        {
            'title': '名称',
            'width': '20%',
            'targets': 0
        },{
            'title': '链接',
            'width': '10%',
            'targets': 1
        },{
            'title': '重定向',
            'width': '10%',
            'targets': 2
        },{
            'title': '备注',
            'width': '20%',
            'targets': 3
        }],
        "createdRow": function(nRow, aData, iDataIndex) {
            var temp = aData[1].split('-');
            tableTdDownload(1, nRow, temp[1]);
            tableTdDownload(2, nRow, aData[2]);
            $('td:eq(0)', nRow).data({
                "id": temp[0],
                "name": aData[0],
                "copy": aData[1],
                "link": aData[2],
                "desc": aData[3]
            });
        },
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
        }
    });
    updatePagination(len, page, data.count, 'linkTable');
    initToolBar('#linkTable', [myConfig.addBtn, myConfig.editBtn, myConfig.delBtn,
        '<a id="copyLink" data-clipboard-target="copyContent" class="btn my-btn btn-primary copyBtn" href="javascript:"><i class="fa fa-copy icon-white"></i>&nbsp;复制链接</a>'
    ]);

    var clip = new ZeroClipboard(document.getElementById("copyLink"));

    clip.off('copy').on("copy", function (event) {
        if (myData.linkId) {
            $('#copyContent').val(myData.linkCopy.split('-')[1]);
            alert('复制成功！');
        } else {
            alert('请选择链接！');
        }
    });
}