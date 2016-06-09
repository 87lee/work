/*全局变量*/
var myConfig = {
    webUrl: 'http://' + window.location.host,
    logOutUrl: '/Pages/Console/index.html',
    addBtn: '<a class="btn my-btn btn-success addBtn" href="javascript:"><i class="glyphicon glyphicon-plus icon-white"></i>&nbsp;新增</a>',
    editBtn: '<a class="btn my-btn btn-info editBtn" href="javascript:"><i class="glyphicon glyphicon-edit icon-white"></i>&nbsp;修改</a>',
    delBtn: '<a class="btn my-btn btn-danger delBtn" href="javascript:"><i class="glyphicon glyphicon-trash icon-white"></i>&nbsp;删除</a>',
    backBtn: '<a class="btn my-btn btn-primary backBtn" href="javascript:"><i class="glyphicon glyphicon-arrow-left icon-white"></i>&nbsp;返回</a>',
    releaseBtn: '<a class="btn my-btn btn-primary releaseBtn" href="javascript:"><i class="glyphicon glyphicon-share icon-white"></i>&nbsp;发布</a>',
    uploadBtn: '<a class="btn my-btn btn-primary uploadBtn" href="javascript:"><i class="glyphicon glyphicon-upload icon-white"></i>&nbsp;上传</a>',
    underBtn: '<a class="btn my-btn btn-danger underBtn" href="javascript:"><i class="fa fa-cloud-download icon-white"></i>&nbsp;下架</a>',
    trObj: [], //缓存table 行点击对象
    moduel: null //缓存当前用户设置模块
};

function getTitle(){
    var host = window.location.host;
    if(host === '192.168.1.199:180'){
        return '本地';
    }else if(host === '115.28.175.220:180'){
        return '国内';
    }else if(host === '47.88.1.137:180'){
        return '海外';
    }
}

/*检查下一级权限*/
function checkNextPer(arr, str) {
    for (var i = 0, len = arr.length; i < len; i++) {
        if (arr[i] == str) {
            return true;
        }
    }
    return false;
}

/*降序显示*/
function descSort(data, param) {
    var len = data.length;
    param = param || "version";
    return data.sort(function(obj1, obj2){
        return  Number(obj2[param]) - Number(obj1[param]);
    });
}

/*升序显示*/
function ascSort(data, param) {
    var len = data.length;
    param = param || "version";
    return data.sort(function(obj1, obj2){
        return  Number(obj1[param]) - Number(obj2[param]);
    });
}

/*组文字颜色*/
function getTypeColor(type) {
    var str = '';
    if (/[\u4e00-\u9fa5]/g.test(type)) {
        switch (type) {
            case '内测':
                str = '#f70';
                break;
            case '灰度':
                str = '#999';
                break;
            case '公开':
                str = '#0a3';
                break;
        }
    } else {
        switch (type) {
            case 'group':
                str = '#f70';
                break;
            case 'AB':
                str = '#999';
                break;
            case 'ALL':
                str = '#0a3';
                break;
        }
    }
    return str;
}

/*组类型*/
function getTypeHtml(type) {
    var str = '';
    if (/[\u4e00-\u9fa5]/g.test(type)) {
        switch (type) {
            case '内测':
                str = 'group';
                break;
            case '灰度':
                str = 'AB';
                break;
            case '公开':
                str = 'ALL';
                break;
        }
    } else {
        switch (type) {
            case 'group':
                str = '内测';
                break;
            case 'AB':
                str = '灰度';
                break;
            case 'ALL':
                str = '公开';
                break;
        }
    }
    return str;
}

/*监听radio控件*/
function listenchoose() {
    $('.form-group > span.lbl').on('click', function() {
        $(this).prev('input').trigger('click');
    });
}

/*监听file控件*/
function listenfile() {
    $('.fileBtn').on('click', function() {
        $(this).siblings('.fileHide').trigger('click');
    });
    $('.fileHide').on('change', function() {
        var $this = $(this);
        $this.siblings('.fileShow').val($this.val());
    });
}

/*监听input图片*/
function listenInputPic(obj) {
    obj = obj || '#myTable';
    $(obj).on('mouseenter', '.fileShow', function() {
        var src = $(this).val();
        var left = $(this).position().left;
        var color = '#fff';
        $(obj + ' .my-image').attr('src', 'img/loading.gif').css({
            'opacity': 1,
            'zIndex': 1,
            'left': (left - 32 - 50) + 'px',
            'background-color': color
        });
        preImage(src, function(error) {
            if (error) { //图片加载失败，则显示默认图片
                src = 'img/error.png';
                this.width = 125;
                color = '#6cf';
            }
            $(obj + ' .my-image').attr('src', src).css({
                'left': (left - this.width - 50) + 'px',
                'background-color': color
            });
        });
    }).on('mouseleave', '.fileShow', function() {
        $(obj + ' .my-image').attr('src', '').css({
            'opacity': 0,
            'zIndex': -1,
        });
    });
}

/*监听图片*/
function listenPic(obj) {
    obj = obj || '#myTable';
    $(obj).on('mouseenter', '.glyphicon-picture', function() {
        var src = $(this).data('src');
        var left = $(this).position().left;
        var color = '#fff';
        $('.my-image').attr('src', 'img/loading.gif').css({
            'opacity': 1,
            'zIndex': 1,
            'left': (left - 32 - 50) + 'px',
            'background-color': color
        });
        preImage(src, function(error) {
            if (error) { //图片加载失败，则显示默认图片
                src = 'img/error.png';
                this.width = 125;
            }
            $('.my-image').attr('src', src).css({
                'left': (left - this.width - 50) + 'px',
            });
            $('.pagination > li.active > a').css('zIndex', '1');
        });
    }).on('mouseleave', '.glyphicon-picture', function() {
        $('.my-image').attr('src', '').css({
            'opacity': 0,
            'zIndex': -1,
        });
        $('.pagination > li.active > a').css('zIndex', '2');
    });
}

/*图片预加载*/
function preImage(url, callback) {
    var img = new Image(); //创建一个Image对象，实现图片的预下载
    img.src = url;
    if (img.complete) { // 如果图片已经存在于浏览器缓存，直接调用回调函数
        callback.call(img);
        return; // 直接返回，不用再处理onload事件
    }
    img.onload = function() { //图片下载完毕时异步调用callback函数
        callback.call(img); //将回调函数的this替换为Image对象
        img = null;
    };
    img.onerror = function() { //加载错误
        callback(true);
        console.log('加载错误');
        img = null;
    };
}

/*多张图预加载*/
function preMoreImage(aUrl, callback) {
    var totalImages = aUrl.length;
    var loadedImages = 0;
    showLoading();
    var addImages = function(){
        loadedImages++;
        if (loadedImages === totalImages) {
            hideLoading();
            callback();
        }
    };
    aUrl.forEach(function(src) {
        var img = new Image(); //创建一个Image对象，实现图片的预下载
        img.src = src;
        if (img.complete) {
            addImages();
        }
        img.onload = addImages();
        img.onerror = addImages();
    });
}

/*dataTable 默认配置*/
function myDataTable(id, options) {
    options = $.extend({
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 15,
        "destroy": true,
        "stateSave": true,
        "dom": '<"toolbar">frtip',
        "language": {
            "lengthMenu": "每页显示 _MENU_ 条记录",
            "info": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
            "infoEmpty": "没有数据",
            "infoFiltered": "(从 _MAX_ 条数据中检索)",
            "paginate": {
                "first": "首页",
                "previous": "上一页",
                "next": "下一页",
                "last": "尾页"
            },
            "zeroRecords": "没有检索到数据",
            "processing": "程序繁忙，请再次尝试",
            "search": "",
            "searchPlaceholder": "搜索"
        }
    }, options || {});
    return $(id).dataTable(options);
}

//配合服务端自定义分页
function updatePagination(len, page, allCount, table) {
    var beginCount = (page - 1) * pageSize + 1;
    var endCount = beginCount + len - 1;
    var info = '';
    table = table || 'myTable';
    if (len === 0) {
        info = '<div class="dataTables_info" id='+table+'_info role="status" aria-live="polite">没有数据</div>';
    } else {
        info = '<div class="dataTables_info" id='+table+'_info role="status" aria-live="polite">从 ' + beginCount + ' 到 ' + endCount + ' /共 ' + allCount + ' 条数据</div>';
    }
    var pagination = '<div class="dataTables_paginate paging_simple_numbers" id='+table+'_paginate><ul class="pagination">';
    var pageCount = Math.ceil(allCount / pageSize);
    pagination += getPageStr(pageCount, page, table);
    $('#'+table+'_wrapper').append(info).append(pagination);
}

//监听自定义分页
function listenMyPage(table, nPage) {
    table = table || 'myTable';
    currentPage = nPage || currentPage;
    $('.my-content').on('click', '#'+ table +'_paginate ul li a', function() {
        var val = $(this).text();
        var active = $(this).parent().hasClass('active');
        var page = Number(val);
        var filter = $('#'+ table +'_filter input').val() || '';
        if(active){
            return false;
        }

        if (val === '上一页' && !$(this).parent().hasClass('disabled')) {
            currentPage--;
            updateTable(currentPage, filter, table);
        } else if (val === '下一页' && !$(this).parent().hasClass('disabled')) {
            currentPage++;
            updateTable(currentPage, filter, table);
        } else if (!isNaN(page)) {
            currentPage = page;
            updateTable(currentPage, filter, table);
        }
        return false;
    });
    $('.my-content').on('keyup', '#'+ table +'_filter input', function() {
        var val = $(this).val();
        currentPage = 1;
        updateTable(currentPage, val, table);
        return false;
    });
}

//count 总页数 pageindex 当前页数
function getPageStr(count, pageindex, table) {
    var a = [];
    var i = 0;
    var cls = '';
    var lastCount = '<li class="paginate_button disabled" aria-controls='+ table +' tabindex="0"><a href="#">…</a></li>' +
        '<li class="paginate_button" aria-controls='+ table +' tabindex="0"><a href="#">' + count + '</a></li>';
    var firstCount = '<li class="paginate_button" aria-controls='+ table +' tabindex="0"><a href="#">1</a></li>' +
        '<li class="paginate_button disabled" aria-controls='+ table +' tabindex="0"><a href="#">…</a></li>';
    if(count < pageindex){
        count = pageindex;
    }
    //总页数少于5 全部显示,大于5 显示前3 后3 中间3 其余....
    if (pageindex == 1 || count === 0) {
        cls = 'paginate_button previous disabled';
    } else {
        cls = 'paginate_button previous';
    }
    a[a.length] = '<li class="' + cls + '" aria-controls='+ table +' tabindex="0" id='+ table +'"_previous"><a href="#">上一页</a></li>';
    if (count <= 5) { //总页数小于5
        for (i = 1; i <= count; i++) {
            setPageList();
        }
    } else { //总页数大于5页
        if (pageindex <= 3) {
            for (i = 1; i <= 4; i++) {
                setPageList();
            }
            a[a.length] = lastCount;
        } else if (pageindex >= count - 3) {
            a[a.length] = firstCount;
            for (i = count - 4; i <= count; i++) {
                setPageList();
            }
        } else { //当前页在中间部分
            a[a.length] = firstCount;
            for (i = pageindex - 1; i <= pageindex + 1; i++) {
                setPageList();
            }
            a[a.length] = lastCount;
        }
    }
    if (pageindex == count || count === 0) {
        cls = 'paginate_button next disabled';
    } else {
        cls = 'paginate_button next';
    }
    a[a.length] = '<li class="' + cls + '" aria-controls='+ table +' tabindex="0" id='+ table +'"_next"><a href="#">下一页</a></li></ul></div>';

    function setPageList() {
        cls = '';
        if (pageindex == i) {
            cls = 'paginate_button active';
        } else {
            cls = 'paginate_button';
        }
        a[a.length] = '<li class="' + cls + '" aria-controls='+ table +' tabindex="0"><a href="#">' + i + '</a></li>';
    }
    return a.join("");
}

/*table详细信息*/
function showTableDetail(opt) {
    opt = $.extend({
        "id": "#myTable"
    }, opt);
    if (opt.tar.className.indexOf('glyphicon-plus') != -1 || opt.tar.className.indexOf('glyphicon-minus') != -1) {
        var $oi = $(opt.id).find('.glyphicon-minus');
        var tr = opt.obj.closest('tr');
        var table = $(opt.id).DataTable();
        var row = table.row(tr);
        var sss = $oi.eq(0);
        if ($oi.length !== 0 && $oi.get(0) != opt.tar) {
            $oi.get(0).className = 'glyphicon glyphicon-plus icon-black my-icon';
            var oTr = $oi.eq(0).closest('tr');
            var oRow = table.row(oTr);
            oRow.child.hide();
            opt.fn(row, tr);
            return;
        }

        if (row.child.isShown()) {
            row.child.hide();
            opt.tar.className = 'glyphicon glyphicon-plus icon-black my-icon';
            tr.removeClass('shown');
        } else {
            opt.fn(row, tr);
        }
    }
}

/*展示table描述的详情信息*/
function showDesc(data) {
    var arr = data;
    var content = '<div style="text-align: center;max-height: 180px;overflow-y: auto;">';
    for (var i = 0, len = arr.length; i < len; i++) {
        content += '<li>' + arr[i] + '</li>';
    }
    return content += '</div>';
}

/*table中td生成icon*/
function tableTdIcon(idx, nRow, type) {
    $('td:eq(' + idx + ')', nRow).html('<i class="glyphicon glyphicon-' + type + ' icon-black my-icon" data-per="memberManager"></i>').addClass('center');
}
/*table中td生成--*/
function tableTdNull(idx, nRow) {
    $('td:eq(' + idx + ')', nRow).html('--').addClass('center');
}
/*table中td生成下载链接*/
function tableTdDownload(idx, nRow, data) {
	$('td:eq(' + idx + ')', nRow).html('<a class="glyphicon glyphicon-arrow-down icon-black my-icon" href="'+ data +'" target="_blank" style="color: black;"></a>').addClass('center');
}
/*table中类型字体*/
function tableTypeColor(idx, nRow, data) {
    $('td:eq(' + idx + ')', nRow).html(getTypeHtml(data)).css({
        color: getTypeColor(data)
    });
}
/*table 工具条*/
function initToolBar(id, options) {
    var toolbar = "";
    toolbar = options ? options.join('') : myConfig.addBtn + myConfig.editBtn + myConfig.delBtn;
    $(id + "_wrapper div.toolbar").html(toolbar).css('marginBottom', '10px');
}

/*table 工具栏绑定*/
function listenToolbar(type, fn, table) {
    var wrapper = table || '#myTable';
    $('#page-content').on('click', wrapper + '_wrapper .toolbar .' + type + 'Btn', fn);
}

/*table行点击*/
function trclick(id, fn) {
    $(id).on('click', 'tbody tr', function(ev) {
        var flag = false;
        var $this = $(this);
        myConfig.trObj.forEach(function(e, i, arr) {
            if (e.id === id) {
                flag = true;
                myConfig.trObj[i].obj = $this;
                return false;
            }
        });
        if (!flag) {
            myConfig.trObj.push({
                "id": id,
                "obj": $this
            });
        }

        var $tr = $(id).find('tbody').find('tr').filter('[role="row"]');
        $tr.css({
            background: '#fff'
        });
        $this.filter('[role="row"]').css({
            background: '#E4EFC9'
        });

        var obj = $this.children().eq(0);
        if (fn)
            fn(obj, ev);
    });
}

/*table行悬停*/
function trHover(id) {
    var obj = null;
    $(id).on('mouseover', 'tbody tr', function() {
        myConfig.trObj.forEach(function(e) {
            if (e.id === id) {
                obj = e.obj;
            }
        });
        $(this).filter('[role="row"]').css({
            background: '#efefef'
        });
        if (obj) obj.filter('[role="row"]').css({
            background: '#E4EFC9'
        });
    });

    $(id).delegate('tbody tr', 'mouseout', function() {
        myConfig.trObj.forEach(function(e) {
            if (e.id === id) {
                obj = e.obj;
            }
        });
        $(this).filter('[role="row"]').css({
            background: '#fff'
        });
        if (obj) obj.filter('[role="row"]').css({
            background: '#E4EFC9'
        });
    });
}

//table 更新checkbox
function updateChecked(table){
    table = table || '#myTable';
    var checkBoxs = $(table + ' tbody tr input');
    for(var j = 0, l = checkBoxs.length; j < l; j++){
        var $checkBox = $(checkBoxs[j]);
        var $td = $checkBox.parents('td');
        var $tr = $checkBox.parents('tr');
        var id = $td.data('id');
        if($.inArray(id, myData.checkedLists) !== -1){
            $tr.addClass('checkSelected');
            $checkBox.prop('checked', true);
        }
    }
    if($(table + ' tbody tr').not(".checkSelected").length > 0){
        $(table + ' thead tr input').prop('checked', false);
    }else{
        $(table + ' thead tr input').prop('checked', true);
    }
}

//table 监听checkbox
function listenCheckBox(table) {
    table = table || '#myTable';
    $(table).off('click', 'tbody tr input').on('click', 'tbody tr input', function(ev) {
        var e = ev || event;
        var $this = $(this);
        var $tr = $this.parents('tr');
        var id = $this.parents('td').data('id');

        if ($this.prop('checked')) {
            $tr.addClass('checkSelected');
            myData.checkedLists.push(id);
        } else {
            $tr.removeClass('checkSelected');
            myData.checkedLists.remove(id);
        }
    });

    $(table).off('click', 'thead tr input').on('click', 'thead tr input', function(ev) {
        var e = ev || event;
        var $this = $(this);
        var inputs = $(table + ' tbody tr input');
        var i = 0, id = null;

        if ($this.prop('checked')) {
            $(table + ' tbody tr').addClass('checkSelected');
            inputs.prop('checked', true);
            for(i = inputs.length; i--;){
                id = $(inputs[i]).parents('td').data('id');
                if($.inArray(id, myData.checkedLists) === -1){
                    myData.checkedLists.push(id);
                }
            }
        } else {
            $(table + ' tbody tr').removeClass('checkSelected');
            inputs.prop('checked', false);
            for(i = inputs.length; i--;){
                id = $(inputs[i]).parents('td').data('id');
                if($.inArray(id, myData.checkedLists) !== -1){
                    myData.checkedLists.remove(id);
                }
            }
        }
    });
}

//监听 checkBox 单选
function listenSingleCheckBox(id, fn){
    $(id).on('click', 'tbody tr', function(ev) {
        var e = ev || event;
        var obj = $(this).find('input');
        var tagName = e.target.tagName.toLowerCase();
        var idx = $(e.target).index();
        if(tagName === 'td' && idx ===0){
            obj.trigger('click');
            return;
        }
        if(tagName !== 'input' && tagName !== 'span'){
            myData.checkedLists = [];
            $(id + ' input').prop('checked', false);
            $(id + ' tbody tr').removeClass('checkSelected');
            obj.trigger('click');
        }
        fn && fn(e);
    });
}

/*tree的默认回调函数*/
function treeCallbak(tree_data) {
    return function(options, callback) {
        var $data = null;
        if (!("text" in options) && !("type" in options)) {
            $data = tree_data; //the root tree
            callback({
                data: $data
            });
            return;
        } else if ("type" in options && options.type == "folder") {
            if ("additionalParameters" in options && "children" in options.additionalParameters)
                $data = options.additionalParameters.children;
            else $data = {}; //no data
        }
        if ($data !== null) //this setTimeout is only for mimicking some random delay
            setTimeout(function() {
            callback({
                data: $data
            });
        }, parseInt(Math.random() * 500) + 200);
    };
}
//清除前后空格
function clearSpace(str) {
    return str.replace(/(^\s*)|(\s*$)/g, "");
}
//过滤空格和换行
function dataFilter(data) {
    for (var p in data)
        if (data.hasOwnProperty(p)) {
            data[p] = data[p].replace(/\ +/g, "");
            data[p] = data[p].replace(/[\r\n]/g, "");
        }
    return data;
}
/*全局ajax:get*/
function AjaxGet(url, cb, loading) {
    return $.ajax({
        url: myConfig.webUrl + url,
        dataType: 'json',
        beforeSend: function() {
            if(!loading)
                showLoading();
        }
    }).done(function(data){
        ajaxSuccess(data, cb);
    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
        ajaxError(XMLHttpRequest, textStatus, errorThrown);
    }).always(function(){
        if(!loading)
            hideLoading();
    });
}
/*ajax:when(多个get)*/
function AjaxWhen(arr, cb){
    showLoading();
    $.when.apply(null, arr).done(function(){
        cb(arguments);
    }).always(function(){
        hideLoading();
    });
}
//post文件上传
function AjaxFile(url, data, cb) {
    return $.ajax({
        url: myConfig.webUrl + url,
        data: data,
        contentType: false,
        processData: false,
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
            showLoading();
        }
    }).done(function(data){
        ajaxSuccess(data, cb);
    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
        ajaxError(XMLHttpRequest, textStatus, errorThrown);
    }).always(function(){
        hideLoading();
    });
}
/*全局ajax:post*/
function AjaxPost(url, data, cb, filter) {
    data = filter ? dataFilter(data) : data;
    return $.ajax({
        url: myConfig.webUrl + url,
        beforeSend: function() {
            showLoading();
        },
        type: 'post',
        data: JSON.stringify(data),
        dataType: 'json'
    }).done(function(data){
        ajaxSuccess(data, cb);
    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
        ajaxError(XMLHttpRequest, textStatus, errorThrown);
    }).always(function(){
        hideLoading();
    });
}

/*全局ajax:jsonp*/
function AjaxJsonp(url, cb) {
    return $.ajax({
        url: myConfig.webUrl + url,
        dataType: "jsonp",
        beforeSend: function() {
            showLoading();
        }
    }).done(function(data){
        ajaxSuccess(data, cb);
    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
        ajaxError(XMLHttpRequest, textStatus, errorThrown);
    }).always(function(){
        hideLoading();
    });
}

/*ajax成功回调*/
function ajaxSuccess(data, callback) {
    if (data.result == "fail") {
        if (data.reason == "登录超时，请重新登录" || data.reason == "未登录，请重新登录") {
            window.location.href = myConfig.logOutUrl;
        }else if(data.reason == "系统维护中"){
            window.location.href = './maintain.html';
        }else{
            alert(data.reason);
        }
        return;
    } else {
        callback(data);
    }
}

/*ajax失败回调*/
function ajaxError(http, text, err, type) {
    alert('数据错误，网络状态码：' + http.status);
    console.log('ajax状态码:' + http.readyState);
    console.log('网络状态码:' + http.status);
    console.log('错误文本:' + text);
    if (parseInt(http.status) == 401) {
        window.location.href = myConfig.logOutUrl;
    }
}

/*显示loading*/
function showLoading() {
    var time = 200;
    var $loading = $('#loading');
    if(!$loading.is(':hidden')){
        return false;
    }
    var clientW = $(window).width();
    var clientH = $(window).height();
    var L = (clientW - $loading.width()) / 2;
    var T = (clientH - $loading.height()) / 2;
    $loading.show().css({
        top: T,
        left: L
    }).animate({
        opacity: 1
    }, time);
    $('#myShade').show();
}

/*隐藏loading*/
function hideLoading() {
    var time = 200;
    var $loading = $('#loading');
    if($loading.is(':hidden')){
        return false;
    }
    $loading.animate({
        opacity: 0
    }, time).hide();
    $('#myShade').hide();
}

//将秒数转换成时间格式
function formatDate(obj, msec) {
    var now = null;
    if(msec){
        now = new Date(Number(obj));
    }else{
        now = new Date(Number(obj) * 1000);
    }
    // return (now.toLocaleDateString() + ' ' + now.toTimeString().slice(0, 8)).replace(/\//g,'-');
    var month = (now.getMonth() + 1);
    var day = now.getDate();
    var hour = now.getHours();
    var minute = now.getMinutes();
    var second = now.getSeconds();

    month = month < 10 ? '0' + month : month;
    day = day < 10 ? '0' + day : day;
    hour = hour < 10 ? '0' + hour : hour;
    minute = minute < 10 ? '0' + minute : minute;
    second = second < 10 ? '0' + second : second;
    return now.getFullYear() + "-" +
        month + "-" +
        day + " " +
        hour + ":" +
        minute + ":" +
        second;
}

//删除数组元素
Array.prototype.remove = function(val) {
    var index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
};
//获取字符串长度(英文占1个字符，中文汉字占2个字符)
String.prototype.gblen = function() {
    var len = 0;
    for (var i=0; i<this.length; i++) {
        if (this.charCodeAt(i)>127 || this.charCodeAt(i)==94) {
            len += 2;
        } else {
            len ++;
        }
    }
    return len;
};
//所有input去除前后空格
$('#main-content').on('change', 'input', function(){
    var $this = $(this);
    if(this.type.toLowerCase() === 'file'){
        return;
    }
    $this.val($this.val().trim());
});
//显示“是”、“否”
function getTrueOrFalse(str){
    return str === 'false' ? '否' : '是';
}
//过滤空格行
function filterBlankLine(data){
    var arr = [];
    for(var i = 0, len = data.length; i < len; i++){
        var val = data[i].trim();
        if(val == ' ' || !val){
            continue;
        }
        arr.push(val);
    }
    data = null;
    return arr;
}