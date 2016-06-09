/*全局变量*/
var hostUrl='http://192.168.1.199:180/Customer/Home/Download/download?';
var myConfig = {
    webUrl: 'http://' + window.location.host,
    logOutUrl: '/Pages/customService/index.html',
    addBtn: '<a class="btn my-btn addBtn" href="javascript:"><i class="iconfont icon-51"></i>&nbsp;新增</a>',
    editBtn: '<a class="btn my-btn editBtn" href="javascript:"><i class="fa fa-pencil-square-o"></i>&nbsp;修改</a>',
    delBtn: '<a class="btn my-btn delBtn" href="javascript:"><i class="iconfont icon-shanchu"></i>&nbsp;删除</a>',
    backBtn: '<a class="btn my-btn backBtn" href="javascript:"><i class="fa "></i>&nbsp;返回</a>',
    releaseBtn: '<a class="btn my-btn releaseBtn" href="javascript:"><i class="fa "></i>&nbsp;发布</a>',
    uploadBtn: '<a class="btn my-btn uploadBtn" href="javascript:"><i class="fa "></i>&nbsp;上传</a>',
    underBtn: '<a class="btn my-btn underBtn" href="javascript:"><i class="fa fa-cloud-download icon-white"></i>&nbsp;下架</a>',
    trObj: [] //缓存table 行点击对象
};

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

/*监听tab控件*/
function listenTab(fn){
    $('#main-content').on('click', '.breadcrumb > span', function(){
        var $this = $(this);
        $('#breadcrumb span').removeClass('active');
        $this.addClass('active');
        $('.checkSelected').removeClass('checkSelected').find('input[type="checkBox"]').prop('checked', false);
        fn($this.text());
    });
}

/*dataTable 客户端默认配置*/
function mDataTable(id, options) {
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

/*dataTable 服务器默认配置*/
function myDataTable(id, options) {
    options = $.extend({
        "lengthChange": false,
        "autoWidth": false,
        "destroy": true,
        "paging": false,
        "searching": false,
        "pageLength": 1,
        "info": false,
        "dom": '<"toolbar">frtip',
        "language": {
            "zeroRecords": "没有检索到数据",
            "infoEmpty": "没有数据"
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
    $('#'+table).siblings("#"+table+'_paginate').remove();
    if (len === 0) {
        info = '<div class="dataTables_info" id='+table+'_info role="status" aria-live="polite">没有数据</div>';
    } else {
        info = '<div class="dataTables_info" id='+table+'_info role="status" aria-live="polite">第 ' + beginCount + ' 到 ' + endCount + ' 条，共 ' + allCount + ' 条数据</div>';
    }

    var pagination = '<div class="dataTables_paginate paging_simple_numbers" id='+table+'_paginate>'+ info +'<ul class="pagination">';
    var pageCount = Math.ceil(allCount / pageSize);
    pagination += getPageStr(pageCount, page, table);
    $(pagination).insertAfter('#'+table);
    // $(info).insertAfter('#'+table);
}

//监听自定义分页
function listenMyPage(table, nPage, fn, tmp, otable) {
    table = table || 'myTable';
    currentPage = nPage || currentPage;

    var order = {};
    $('#page-content').on('click', '#'+ table +'_paginate ul li a', function() {
        order = setTableSort('#'+table, tmp, otable);
        var val = $(this).text();
        var active = $(this).parent().hasClass('active');
        var page = Number(val);
        var filter = $('#'+ table +'_filter input').val() || '';
        if(active){
            return false;
        }

        if (val === '上一页' && !$(this).parent().hasClass('disabled')) {
            currentPage--;
            
            fn(currentPage, {"name":filter}, table);
            
        } else if (val === '下一页' && !$(this).parent().hasClass('disabled')) {
            currentPage++;
            
            fn(currentPage, {"name":filter}, table);
            
        } else if (!isNaN(page)) {
            currentPage = page;

           
            fn(currentPage, {"name": filter}, table);
        }

        return false;
    });

    $('#page-content').on('keyup', '#'+ table +'_filter input', function() {

        var val = $(this).val();
        order.name = val;
        currentPage = 1;

        fn(currentPage, order, table);

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

/*table中td生成√或--*/
function tableTdNull(idx, nRow, type) {
    if(type === 'true'){
        $('td:eq(' + idx + ')', nRow).html('√');
    }else{
        $('td:eq(' + idx + ')', nRow).html('--');
    }
}

/*table 工具条*/
function initToolBar(id, options) {
    var toolbar = "";
    toolbar = options ? options.join('') : myConfig.addBtn + myConfig.editBtn + myConfig.delBtn;
    $(id + "_wrapper div.toolbar").html(toolbar).css('marginBottom', '15px');
    if(navigator.userAgent.toUpperCase().indexOf("FIREFOX") !== -1){
        $(id + '_wrapper div.toolbar .my-btn').addClass('my-btn-firefox');
    }
}

function initToolBarRow(id, options) {
    var toolbar = "";
    var copy ='<a class="btn my-btn copyBtn" href="javascript:"><i class="iconfont icon-copy"></i>&nbsp;复制</a>';
    toolbar = options ? options.join('') : myConfig.addBtn + myConfig.editBtn + myConfig.delBtn;
    $(id).html(toolbar+copy);
    if(navigator.userAgent.toUpperCase().indexOf("FIREFOX") !== -1){
        $(id + ' .my-btn').addClass('my-btn-firefox');
    }
}

/*table 工具栏绑定*/
function listenToolbar(type, fn, table) {
    var wrapper = table || '#myTable';
    $('#page-content').on('click', wrapper + '_wrapper .toolbar .' + type + 'Btn', fn);
}

function listenToolbarRow(type, fn, table) {
    var wrapper = table || '#myTable';
    $('#page-content').on('click', wrapper + ' tr td .' + type + 'Btn', fn);
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
            background: '#ccfbfb'
        });

        var obj = $this.children().eq(0);
        if (fn)
            fn(obj, ev);
        return false;
    });
}

//table 更新checkbox
function updateChecked(table, type){
    table = table || '#myTable';
    var checkBoxs = $(table + ' tbody tr input');
    for(var j = 0, l = checkBoxs.length; j < l; j++){
        var $checkBox = $(checkBoxs[j]);
        var $td = $checkBox.parents('td');
        var $tr = $checkBox.parents('tr');
        var id = $td.data('id');
        if(type){
            if($.inArray(id, myData.checkedItems) !== -1){
                $tr.addClass('checkSelected');
                $checkBox.prop('checked', true);
            }
        }else{
            if($.inArray(id, myData.checkedLists) !== -1){
                $tr.addClass('checkSelected');
                $checkBox.prop('checked', true);
            }
        }
    }
    if($(table + ' tbody tr').not(".checkSelected").length > 0){
        $(table + ' thead tr input').prop('checked', false);
    }else{
        $(table + ' thead tr input').prop('checked', true);
    }
}

//table 监听checkbox
function listenCheckBox(table, type) {
    table = table || '#myTable';
    $(table).off('click', 'tbody tr input').on('click', 'tbody tr input', function(ev) {
        var e = ev || event;
        var $this = $(this);
        var $tr = $this.parents('tr');
        var id = $this.parents('td').data('id');

        if ($this.prop('checked')) {
            $tr.addClass('checkSelected');
            if(type){
                myData.checkedItems.push(id);
            }else{
                myData.checkedLists.push(id);
            }
        } else {
            $tr.removeClass('checkSelected');
            if(type){
                myData.checkedItems.remove(id);
            }else{
                myData.checkedLists.remove(id);
            }
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
                if(type){
                    if($.inArray(id, myData.checkedItems) === -1){
                        myData.checkedItems.push(id);
                    }
                }else{
                    if($.inArray(id, myData.checkedLists) === -1){
                        myData.checkedLists.push(id);
                    }
                }
            }
        } else {
            $(table + ' tbody tr').removeClass('checkSelected');
            inputs.prop('checked', false);
            for(i = inputs.length; i--;){
                id = $(inputs[i]).parents('td').data('id');
                if(type){
                    if($.inArray(id, myData.checkedItems) !== -1){
                        myData.checkedItems.remove(id);
                    }
                }else{
                    if($.inArray(id, myData.checkedLists) !== -1){
                        myData.checkedLists.remove(id);
                    }
                }
            }
        }
    });
}

//监听 checkBox 单选
function listenSingleCheckBox(id, fn, type){
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
            if(type){
                myData.checkedItems = [];
            }else{
                myData.checkedLists = [];
            }

            $(id + ' input').prop('checked', false);
            $(id + ' tbody tr').removeClass('checkSelected');
            obj.trigger('click');
        }
        fn && fn(e);
    });
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

/*全局非异步ajax:get*/
function JqGet(url, cb, loading) {
    return $.ajax({
        url: myConfig.webUrl + url,
        async : false,
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
function AjaxFile(url, data, cb, $showError) {
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
        ajaxSuccess(data, cb, $showError);
    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
        ajaxError(XMLHttpRequest, textStatus, errorThrown);
    }).always(function(){
        hideLoading();
    });
}
/*全局ajax:post*/
function AjaxPost(url, data, cb, $showError) {
    return $.ajax({
        url: myConfig.webUrl + url,
        beforeSend: function() {
            showLoading();
        },
        type: 'post',
        data: JSON.stringify(data),
        dataType: 'json'
    }).done(function(data){
        ajaxSuccess(data, cb, $showError);
    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
        ajaxError(XMLHttpRequest, textStatus, errorThrown);
    }).always(function(){
        hideLoading();
    });
}

/*新post*/
function AjaxKeyPost(url, data, cb, $showError) {
    return $.ajax({
        url: myConfig.webUrl + url,
        beforeSend: function() {
            showLoading();
        },
        type: 'post',
        data: data,
        dataType: 'text'
    }).done(function(data){
        ajaxSuccess(data, cb, $showError);
    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
        console.log('error');
        ajaxError(XMLHttpRequest, textStatus, errorThrown);
    }).always(function(){
        hideLoading();
    });
}

function resultMsg(){
    //data, msg, id
    var args = arguments;
    console.log(arguments);
    console.log(args[1]);
    console.log(JSON.parse(args[0]).msg);
    console.log(JSON.parse(args[0]).code);
    if (JSON.parse(args[0]).code === 200 && JSON.parse(args[0]).msg === args[1]) {
            if (args[2]) {
                $(args[2]).modal('hide');
            }
        }else{
            console.log('fail');
            if (args[2]) {
                $(args[2] + ' .error-info').html('发布失败：' + JSON.parse(args[0]).msg);
            }else{
                alert('发布失败：' + JSON.parse(args[0]).msg);
            }
        }
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
function ajaxSuccess(data, callback, $showError) {
    /*console.log(typeof data);
    console.log(data.code);*/
    if (data.result == "fail") {
        if (data.reason == "登录超时，请重新登录" || data.reason == "未登录，请重新登录" || data.reason == "账号已在其它地方登录，请重新登录") {
            alert(data.reason);

            window.location.href = myConfig.logOutUrl;
        }else if(data.reason == "请修改初始密码"){
            if(window.location.hash !== '#user/personal'){
                $('#firstModular a').filter('[href="#user/personal"]');
            }
        }else if($showError){
            $showError.text(data.reason);
        }else{
            alert(data.reason);
        }
        return;
    }else if(data.code == 800 || data.code == 401){
        alert(data.msg);
        window.location.href = myConfig.logOutUrl;
    }else if(data.code == 801){
    	alert(data.msg);
    	return false;
    }else{
        if($showError){
            $showError.text('');
        }
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
function formatDate(obj) {
    var now = new Date(Number(obj) * 1000);
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

/**
 * 按字节截取字符
 * description : 按字节长度截取字符串,并添加后缀.
 * @param len 需要截取的长度,字符串长度不足返回本身;
 * @param start  开始位置;
 * @return 返回截取后的字符串;
 * @requires getLength;
 */
String.prototype.gbSubstr=function(start , len ){
	var tempStr=this;
	if( this.gblen() > len){
	  	var i=0;
	  	for(var z=start;z<len;z++){
	   		if(this.charCodeAt(z)>127 || this.charCodeAt(z)==94 ){
	    			i=i+2;
	   		}else{
	    			i=i+1;
	   		}

	   		if( i >= len){
	    			tempStr = tempStr.substr(start,(z + 1));
	    			break;
	   		}
	  	}
	  	return tempStr;
 	}else{
  		return this;
 	}
}

//兼容火狐textarea
function checkMoz(){
    if(navigator.userAgent.toUpperCase().indexOf("FIREFOX") !== -1){
        $('.my-btn').addClass('my-btn-firefox');
        $('.fileBtn').css('padding-bottom', '2px');
    }else if(isIE()){
        $('.my-form select.form-control').css('padding-left', '8px');
    }
    if(navigator.userAgent.toUpperCase().indexOf("CHROME") !== -1 || navigator.userAgent.toUpperCase().indexOf("SAFARI") !== -1){
        $('.label-group+textarea').css('marginBottom', '-4px');
    }
}

function isIE() { //ie?
    if (!!window.ActiveXObject || "ActiveXObject" in window)
        return true;
    else
        return false;
}

function placeholder_IE10(){
    $('#main-content').on('blur', 'textarea', function(){
        var $this = $(this);
        var val = $this.val();
        if(val === ''){
            $this.css('color', '#999');
        }else{
            $this.css('color', '#000');
        }
    }).on('keydown', 'textarea', function(){
        $(this).css('color', '#000');
    });
}
//datatable排序判断
function listenOrder(id, orderTable, fn, page){
    // for (var i = $(id+' thead tr th').length - 1; i >= 0; i--) {
    //  $(id+' thead tr th')[i]
    // }
        $(id).on('click', 'thead tr th', function(ev) {
            if ($(this).children().length === 0) {
                for (var i in orderTable) {
                    if (orderTable[i] === $(this).html()) {


                if($(this).attr('class') === 'sorting_desc'){
                    $(this).removeClass();
                    var val = {
                        "sort": ''
                    };
                    for (var key in $(id+' tbody td:eq(0)').data()) {
                        if (orderTable[key] === $(this).html()) {
                            val.sort = key + '-asc';
                        }
                    }
                    fn(1, val, 1);
                    $(this).addClass('sorting_asc');
                }else if ($(this).attr('class') === 'sorting_asc') {
                    $(this).removeClass();
                    var val = {
                        "sort": ''
                    };
                    for (var key in $(id+' tbody td:eq(0)').data()) {
                        if (orderTable[key] === $(this).html()) {
                            val.sort = key + '-desc';
                        }
                    }
                    fn(1, val, 1);
                    $(this).addClass('sorting_desc');
                }else{
                    $(this).removeClass();
                    var val = {
                        "sort": ''
                    };
                    for (var key in $(id+' tbody td:eq(0)').data()) {
                        if (orderTable[key] === $(this).html()) {
                            val.sort = key + '-desc';
                        }
                    }
                    fn(1, val, 1);
                    $(this).addClass('sorting_desc');
                }

                }
                }

            }
        });
}
//datatable升降序图标处理
function orderTab(table, data, keyList) {
    // if (data.sort === '' || !data.sort) {
    //     console.log('sorting1');
    //     $(table + ' thead tr th:eq(0)').siblings().removeClass();
    //     $(table + ' thead tr th:eq(0)').siblings().addClass('sorting');
    // }else{
        for (var i in keyList) {
            if (i + '-desc' === data.sort) {
                $(table + ' thead tr th:eq('+ keyList[i] +')').removeClass();
                $(table + ' thead tr th:eq('+ keyList[i] +')').addClass('sorting_desc');
            }else if (i + '-asc' === data.sort) {
                $(table + ' thead tr th:eq('+ keyList[i] +')').removeClass();
                $(table + ' thead tr th:eq('+ keyList[i] +')').addClass('sorting_asc');
            }else{
                $(table + ' thead tr th:eq('+ keyList[i] +')').removeClass();
                $(table + ' thead tr th:eq('+ keyList[i] +')').addClass('sorting');
            }
        }
    // }
}

//检测表单排序类型
function setTableSort(table, tmp, otable) {
    var asc = $(table+' thead tr th[class$=_asc]');
    var desc = $(table+' thead tr th[class$=_desc]');
    for (var i in otable) {
        if (otable[i] === asc.html()) {
            tmp.sort = i + '-asc';
        }else if(otable[i] === desc.html()){
            tmp.sort = i + '-desc';
        }
    }
    return tmp;
}

//从checkedLists单选数据组
function checkedListsData(id, checked) {
    for (var i = 0; i < $(id + ' tbody tr').length; i++) {
        if (checked[0] === $(id + ' tbody tr:eq('+i+')').children('td:eq(0)').data('id')) {
            return ($(id + ' tbody tr:eq('+i+')').children('td:eq(0)').data());
        }
    }
}

/*table中td生成icon*/
function tableTdIcon(idx, nRow, type) {
    $('td:eq(' + idx + ')', nRow).html('<i class="glyphicon glyphicon-' + type + ' icon-black my-icon" data-per="memberManager"></i>').addClass('center');
}

//获取权限初始化子页面菜单
function initTopMenu() {
    JqGet('/Customer/Home/Menu/getSecondMenu?uid=' + window.localStorage.getItem("CUSTOM_PERMISSION_ID") + '&pid=' + window.location.hash.split('#')[1], function(data) {
        myData.data = data;
        //console.log(data.retval);
        var breadcrumbs = '';
        var j = 0;
        for (var i in data.retval) {
            //子窗菜单栏
            if (j === 0) {
                breadcrumbs += '<span class="active">' + data.retval[i].title + '</span>'
                j++;
            }else{
                breadcrumbs += '<i></i><span>' + data.retval[i].title + '</span>'
            }
            
        }
        $('#breadcrumb').html(breadcrumbs);
    });
}

//获取权限生成子页菜单下的按键
function initToolBtn(data, title) {
    for (var i in data) {
        if (data[i].title === title) {
            
            if (data[i].children) {
                for (var j = 0; j < data[i].children.length; j++) {
                    toolbar.push('<a class="btn my-btn ' + data[i].children[j].css + '" href="javascript:"><i class="fa ' + data[i].children[j].icon + '"></i>&nbsp;' + data[i].children[j].title + '</a>');
                }
            }
        }
    }
}

//鼠标悬停复制
function copyBtn(data){
    /*$("#firewareListTable tr").mouseover(function(event){
        var X = event.pageX+10;
        var Y = event.pageY+10;
        $("body").append("<button type=button class=dynamicBtn style='width:100px;height:30px; position:absolute;top:"+Y+"px;left:"+X+"px; value=复制'>复制</button>");
    }).siblings().mouseout(function(){
       $(".dynamicBtn").remove();
    });
    $(".dynamicBtn").on('mouseover',function(){

    })*/
}

