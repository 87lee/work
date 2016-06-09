$(function () {
    /*设置用户名*/
    initUsername();
    /*监听退出登录*/
    listenLogout();
    /*获取模块列表数据*/
    loadNavList();
    /*监听菜单缩放*/
    listenMenu();

    var title = getTitle();
    document.title = title + '系统';
    $('.navbar-brand').html(title + '系统');
});

$(window).
off('hashchange').
on('hashchange', function(e){
    listenUrl();
});

function initUsername(){
    var user = window.localStorage.getItem("PERMISSION_BACK_USERNAME");
    if(user){
        $('#user-info').html(user);
        $('.ace-nav').show();
    }else{
        window.location.href = myConfig.logOutUrl;
    }
}

function loadNavList(){
    var content = '';
    var power = '';
    $.ajax({
        url: '../monitor/navList.json',
    })
    .done(function(data) {
        for(var i = 0, len = data.length; i < len; i++){
            content += createNavListHtml(data[i]);
        }
        $('.nav-list').hide().append(content);
        /*调用权限设置函数*/
        permission();
        /*获取当前用户所有模块*/
        var arr = [];
        var modules = $('.nav-list > li');
        myConfig.allModules = [];
        myConfig.allModulesList = [];
        for(i = modules.length; i--;){
            arr.push(modules[i].className);
        }
        for(i = data.length; i--;){
            if(arr.indexOf(data[i].module) !== -1){
                power += createPowerListHtml(data[i]);
                myConfig.allModules.push(data[i]);
                myConfig.allModulesList.push(data[i].module);
            }
        }
        $('#powerList').html(power);
        /*调用当前用户已设置模块*/
        AjaxGet('/Monitoring/home/module/customModeulLists', function(data){
            myConfig.module = data.modules;
            currentModuel();
            /*监听导航栏*/
            listenNav();
            /*监听url*/
            listenUrl();
        });
    });
}

function createPowerListHtml(data){//创建权限模块
    var li = {
        "first":function(){
            return  '<li>'+
                        '<a class="dropdown-toggle" href="#'+data.url+'" data-url="views/'+data.url+'">'+
                            '<i class="menu-icon '+data.icon+'"></i>&emsp;'+data.name+
                        '</a>'+
                    '</li>';
        },
        "second":function(){
            return  '<li>'+
                        '<a class="dropdown-toggle" data-module="'+data.module+'">'+
                            '<i class="menu-icon '+data.icon+'"></i>&emsp;'+data.name+
                        '</a>'+
                    '</li>';
        }
    };
    return li[data.type]();
}

function createNavListHtml(data){//创建父菜单
    var li = {
        "first":function(){
            return  '<li class="'+data.module+'">'+
                        '<a href="#'+data.url+'" class="ajax-link" data-url="views/'+data.url+'">'+
                            '<i class="menu-icon '+data.icon+'"></i>'+
                            '<span class="menu-text"> '+data.name+' </span>'+
                        '</a>'+
                        '<b class="arrow"></b>'+
                    '</li>';
        },
        "second":function(){
            var content =   '<li class="'+data.module+'">'+
                                '<a href="#'+data.module+'" class="dropdown-toggle" data-toggle="collapse" >'+
                                    '<i class="menu-icon '+data.icon+'"></i>'+
                                    '<span class="menu-text"> '+data.name+' </span>'+
                                    '<b class="arrow fa fa-angle-down"></b>'+
                                '</a>'+
                                '<b class="arrow"></b>'+
                                '<ul class="submenu collapse" id="'+data.module+'">';
            return content += getChildModule(data);
        }
    };
    
    return li[data.type]();
}

function getChildModule(data){//创建子菜单
    var content = '';
    for(var i = 0, len = data.children.length; i < len; i++){
        var child = data.children[i];
        if(child.children){
            content += '<li class="'+data.module+'-'+child.module+' three">'+
                                '<a href="javascript;" class="dropdown-toggle">'+
                                    '<i class="menu-icon fa fa-caret-right"></i>'+
                                    '<span class="menu-text"> '+child.name+' </span>'+
                                    '<b class="arrow fa fa-angle-down"></b>'+
                                '</a>'+
                                '<b class="arrow"></b>'+
                                '<ul class="submenu">';
            content += getChildModule(child);
        }else{
            content +=  '<li class="'+data.module+'-'+child.module+'">'+
                            '<a href="#'+child.url+'" class="ajax-link" data-url="views/'+child.url+'">'+
                                child.name+
                            '</a>'+
                            '<b class="arrow"></b>'+
                        '</li>';
        }
    }
    return content += '</ul></li>';
}

/*更新当前显示模块*/
function currentModuel(){
    var $navList = $('.nav-list');
    var lists = $navList.find(' > li');
    var arr = myConfig.module;
    var nowModuel = [];
    lists.hide();
    for(i = 0, len = arr.length; i < len; i++){     //缓存可见模块并显示在容器开始
        nowModuel.push(lists.filter('.'+arr[i]).show());
    }
    $navList.prepend(nowModuel).show();

    var hideModules = $('.nav-list > li').filter(':hidden');
    var content = '';
    for(i = hideModules.length; i--;){  //新建不可见模块
        var idx = myConfig.allModulesList.indexOf(hideModules[i].className.replace(' open', '').replace(' active', ''));
        if(idx > -1){
            content += createNavListHtml(myConfig.allModules[idx]);
        }
    }
    hideModules.remove();   //移除过期不可见模块
    $navList.append(content);   //插入新不可见模块
    $navList.find('.submenu li').show();
    $('#sidebar').show();
}

/*读取登录权限并设置导航是否可见*/
function permission(){
    myConfig.permission = [];   //缓存用户权限
    myConfig.moduleList = [];   //缓存有效模块地址
    var $moduleList = $('.ajax-link').closest('li');    //子模块列表
    var $toggleList = $('#sidebar .dropdown-toggle').closest('li');  //父模块列表
    if(window.localStorage.PERMISSION_BACK){
        var per = $.parseJSON(window.localStorage.PERMISSION_BACK);
        myConfig.permission = per;
        /*权限部分显示*/
        $.each(per, function(idx, elem){
            var module = elem.module;
            var sub = elem.sub_modules;
            var i = 0;
            if(module == 'user'){
                for(i = 0, len = sub.length; i < len; i++){
                    if(sub[i].sub_module == "add" || sub[i].sub_module == "delete"){
                        $moduleList.filter('.user-add-del').show();
                    }else if(sub[i].sub_module == "edit"){
                        $moduleList.filter('.user-edit').show();
                    }
                }
            }else{
                $moduleList.filter('.' + module).show();
                for(i = 0, len = sub.length; i < len; i++){
                    $moduleList.filter('.' + module + '-' + sub[i].sub_module).show();
                    var three = $('.submenu > li.three');
                    if(three.length !== 0){
                        three.filter('.' + module + '-' + sub[i].sub_module).show();
                    }
                }
            }
        });
    }

    $moduleList.each(function(idx, elem){           //根据权限对模块进一步操作
        if($(elem).css('display') == 'none'){
            $(elem).remove();
        }else{
            myConfig.moduleList.push($(elem).find('>a').data('url'));
        }
    });

    removeChildModule();
}

function removeChildModule(){
    var menulist = $('.submenu');   //子模块不存在则移除父模块
    for(var i = 0, len = menulist.length; i < len; i++){
        var obj = $(menulist[i]);
        var oParent = obj.parent();
        if(obj.children().length === 0){
            oParent.remove();
        }else if(oParent.hasClass('three') && oParent.css('display') == 'none'){
            oParent.remove();
            removeChildModule();
        }else{
            oParent.show();
        }
    }
}

/*备用函数，用来细分权限*/
function checkDetailPer(obj, module){
    var per = obj.data('per');
    for(var i = 0, len = myConfig.permission.length; i < len; i++){
        if(myConfig.permission[i].module == module){
            var sub = myConfig.permission[i].sub_modules;
            for(var j = 0, l = sub.length; j < l; j++){
                if(sub[j].sub_module == per){
                    return true;
                }
            }
        }
    }
    return false;
}

/*退出登录*/
function listenLogout(){
    $('#logOut').click(function(){
        var url = '/Monitoring/home/user/logout';
        if( confirm('确定要退出吗') ){
            $.ajax({
                url : myConfig.webUrl + url,
                type : 'post',
                success: function(data){
                    window.localStorage.PERMISSION_BACK = "";
                    window.location.href = myConfig.logOutUrl;
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    ajaxError(XMLHttpRequest, textStatus, errorThrown);
                }
            });
        }
    });
}

function listenNav(){
    $('#sidebar').on('click', '.nav-list li', function(e, type){
        var $this = $(this);
        var len = $this.find('ul').length;
        if(len !== 0) return;
        $('#sidebar').find('.nav-list li').removeClass('active');
        $this.addClass('active');
        if(!type && $this.find('a').attr('href') === window.location.hash){
            listenUrl();
        }
    });
}

/*监听url变化*/
function listenUrl(){
    var hash = window.location.hash;
    var url = hash.replace(/#/, "views/");
    var obj = null;
    var module = hash && hash.split('#')[1].split('/')[0];      //当前要显示的模块
    currentModuel();
    $('#sidebar .nav-list').children().children('ul').on('show.bs.collapse', function () {
        $(this).parent().children('a').children('b').removeClass('fa-angle-down').addClass('fa-angle-right');
        $(this).parent().children('a').css({'background-color':'#2199f2','color':'#ffffff'});
    });
    $('#sidebar .nav-list').children().children('ul').on('hide.bs.collapse', function(){
        $(this).parent().children('a').children('b').removeClass('fa-angle-right').addClass('fa-angle-down');
        $(this).parent().children('a').css({'background-color':'#434343','color':'#999999'});
    });
    obj = $('.ajax-link').filter('[data-url="'+ url +'"]');     //匹配需要点击的菜单
    if(obj.length){
        obj.parents('.'+ module).show();
    }else{      //匹配失败，则显示默认模块
        obj = $('#sidebar').find('.ajax-link').eq(0);
        if(obj.length === 0)
            obj = $('.user-edit').find('.ajax-link').eq(0);
    }

    var menu = obj.parent().parent();
    if(menu.hasClass('submenu') && !menu.hasClass('in')){     //选中模块为二级菜单
        var three = menu.parents('ul.submenu');
        if(three.length > 0 && !three.hasClass('in')){    //选中模块为三级菜单
            three.siblings('a').trigger('click');
            setTimeout(function(){
                menu.siblings('a').trigger('click');
            }, 400);
        }else{
            menu.siblings('a').trigger('click');
        }
    }
    if(obj.length === 0 && !window.localStorage.PERMISSION_BACK){
        window.location.href = myConfig.logOutUrl;
    }else{
        obj.trigger('click', true);
        $('#sidebar .nav-list li ul li a').css({'color':'#999999'});
        obj.css({'color':'#2199f2'});
        refreshModule(obj.data('url'));     //读取当前模块信息并显示
    }
}

/*获取数据显示模块内容*/
function refreshModule(url){
    showLoading();
    $('#main-content').css('visibility', 'hidden');
    var type = url.split('/')[2];
    $('#main-content').load(url + '.html?_=' + Date.now(), function(data, status){
        if("undefined" != typeof desktopData){  //有桌面缓存则清除
            desktopData = null;
        }
        if(type === 'release'){
            $.getScript('js/common.js', function(){
                loadScript(url);
            });
        }else{
            loadScript(url);
        }
    });
}

function loadScript(url){
    $.getScript(url + '.js', function(){
        hideLoading();
        $('#main-content').css('visibility', 'visible');
    });
}

/*功能模块点击事件*/
$('#powerList').on('click', 'li > a', function(){
    var $this = $(this);
    var module = $this.attr('data-module');
    var $module = null;
    var $parent = null;
    if(module){
        $module = $('.nav-list .'+ module +' > ul');
    }else{
        module = $this.attr('href').split('/')[0].slice(1);
        $module = $('.nav-list .'+ module +' a');
    }
    $parent = $module.parent();
    if($parent.is(':hidden')){      //不可见模块临时设置为可见
        $('.nav-list').append($parent.show());
    }
    if($module.hasClass('submenu')){    //非一级模块，手动触发url变化事件
        window.location.hash = $module.find('.ajax-link:eq(0)').attr('href');
    }
});

//自定义模块事件
$('#customSetting').on('click', function(){
    $('#settingModal').modal('show');
    var allModules = myConfig.allModules.slice();
    var nowModuel = myConfig.module.slice();
    var settingsed = [];
    var unsettings = [];
    var i, j, idx = 0;

    for(i = 0, len = nowModuel.length; i < len; i++){       //缓存已选区域模块信息
        idx = myConfig.allModulesList.indexOf(nowModuel[i]);
        if(idx > -1){
            settingsed.push(allModules[idx]);
        }
    }
    for(i = 0, len = myConfig.allModulesList.length; i < len; i++){     //缓存可选区域模块信息
        idx = nowModuel.indexOf(myConfig.allModulesList[i]);
        if(idx === -1){
            unsettings.push(allModules[i]);
        }
    }

    createCustomModule(settingsed, unsettings);     //创建自定义模块HTML

    $("#settingsed, #unsettings").sortable({       //开启关联区域排序
      connectWith: ".connectedSortable"
    }).disableSelection();
});

//提交自定义模块
$('#subSetting').on('click', function(){
    var settingsed = $('#settingsed > label');
    var data = {"modules": []};
    var content = [];
    $('.nav-list > li').hide();
    for(var i = 0, len = settingsed.length; i < len; i++){      //显示自定义的模块并缓存
        var module = $(settingsed[i]).data('module');
        data.modules.push(module.module);
        content.push($('.nav-list .' + module.module).show());
    }
    $('.nav-list').prepend(content);
    myConfig.module = data.modules;
    AjaxPost('/Monitoring/home/module/authCustom', data, function(){
        $('#settingModal').modal('hide');
    });
});

/*创建已选区域、未选区域html*/
function createCustomModule(settingsed, unsettings){
    var i, len = 0;
    var stred = [];
    var unstr = [];
    for(i = 0, len = settingsed.length; i < len; i++){
        var settinged = settingsed[i];
        stred.push($('<label class="custom-set"><i class="ace-icon '+ settinged.icon +'"></i>&ensp;'+ settinged.name +'<div class="custom-set-button">×</div></label>').data('module', settinged));
    }
    for(i = 0, len = unsettings.length; i < len; i++){
        var unsetting = unsettings[i];
        unstr.push($('<label class="custom-set"><i class="ace-icon '+ unsetting.icon +'"></i>&ensp;'+ unsetting.name +'<div class="custom-set-button">+</div></label>').data('module', unsetting));
    }
    $('#settingsed').html('').append(stred);
    $('#unsettings').html('').append(unstr);
}

/*移动到已选区域事件*/
$('#settingsed').on('sortreceive', function( event, ui ){
    console.log('settingsed');
    ui.item.find('.custom-set-button').text('×');
});
/*移动到可选区域事件*/
$('#unsettings').on('sortreceive', function( event, ui ){
    console.log('settings');
    ui.item.find('.custom-set-button').text('+');
});
/*自定义模块手动添加事件*/
$('#settingModal').on('click', '.custom-set-button', function(){
    console.log('modal');
    var $this = $(this);
    var str = $this.text();
    if(str === '×'){
        $('#unsettings').append($this.text('+').parent());
    }else if(str === '+'){
        $('#settingsed').append($this.text('×').parent());
    }
});

function listenMenu(){
    $('#sidebar-collapse > i').on('click', function(){
        var $this = $(this);
        $('.sidebar-one').toggleClass('menu-min');
        if($this.hasClass('fa-angle-double-left')){
            $('#sidebar ul li a b').hide();
            $('#sidebar ul li ul').hide();
            $this.get(0).className = 'fa fa-angle-double-right';
        }else{
            $('#sidebar ul li a b').show();
            $('#sidebar ul li ul').show();
            $this.get(0).className = 'fa fa-angle-double-left';
        }
    });
    $('#sidebar ul').on('mouseover', function() {
        if ($('.sidebar-one').hasClass('menu-min')) {
            $('.sidebar-one').removeClass('menu-min');
            $('#sidebar ul li a b').show();
            $('#sidebar-collapse > i').get(0).className = 'fa fa-angle-double-left';
            $('#sidebar ul li ul').show();
        }
    });
}