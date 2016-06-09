$(function () {
    //清空消息圆点
    $('#msg').on('click', function() {
        $('#msgNum').css({
            "background-color": "",
            "position": "",
            "top": "",
            "left": "",
            "width": "",
            "line-height": "",
            "border-radius": "",
            "padding-left": ""
            });
            $('#msgNum').html(''); 
    });
    /*设置用户名*/
    initUsername();
    /*隐藏模块*/
    hideModular();
    /*监听退出登录*/
    listenLogout();
    /*监听菜单缩放*/
    listenMenu();

	$('#firstModular').on('click', '.nav-list > li', function(e, type){
		var $this = $(this);
		var idx = $this.index();
        if(editPass() === 'false'){
            return;
        }
        $('#firstModular > .nav-list > li > a').removeClass('active');
        $('.sidebar-two li a.active').removeClass('active');
        $this.find('> a').addClass('active');
        if(!type && $this.find('> a').attr('href') === window.location.hash){
            listenUrl();
        }
	});

	$('.sidebar-two').on('click', 'li', function(e, type){
		var $this = $(this);
		var idx = $this.index();
        if(editPass() === 'false'){
            return;
        }
        $('.sidebar-two li a').removeClass('active');
        $this.find('a').addClass('active');
        if(!type && $this.find('a').attr('href') === window.location.hash){
            listenUrl();
        }
	});

	listenUrl();
    placeholder_IE10();
    resizeWindow();
});

$(window).
off('hashchange').
on('hashchange', function(e){
    listenUrl();
});

function initUsername(){
    var name = window.localStorage.getItem("ANDROID_PERMISSION_USERNICKNAME");
    if(name){
        $('#user-info').html(name).attr('title', name);
        $('.ace-nav').show();
    }else{
        window.location.href = myConfig.logOutUrl;
    }
}

function hideModular(){
    var power = window.localStorage.getItem("ANDROID_PERMISSION_USEPOWER");
    if(power.indexOf('系统管理员') === -1){
        $('.nav a[href="#user/admin"]').parent().remove();
        $('.nav a[href="#user/modular"]').parent().remove();
        $('.nav a[href="#user/application"]').parent().remove();
    }

    if(power.indexOf('发布用户') === -1){
        $('a[href="#base/version"]').parent().remove();
    }
    $('.sidebar-one').show();
}

/*退出登录*/
function listenLogout(){
    $('#logOut').click(function(){
        var url = '/Android/home/user/logout';
        if( confirm('确定要退出吗') ){
            $.ajax({
                url : myConfig.webUrl + url,
                type : 'post',
                success: function(data){
                    window.localStorage.PERMISSION = "";
                    window.location.href = myConfig.logOutUrl;
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    ajaxError(XMLHttpRequest, textStatus, errorThrown);
                }
            });
        }
    });
}

/*监听url变化*/
function listenUrl(){
    var hash = window.location.hash;
    var url = hash.replace(/#/, "views/");
    var module = hash && hash.split('#')[1].split('/')[0];      //当前要显示的模块
    var $obj = null;

    if(editPass() === 'false' && url !== 'views/user/personal'){
        $obj = $('#firstModular a').filter('[href="#user/personal"]');     //匹配需要点击的菜单
    }else{
        $obj = $('#firstModular a').filter('[href="'+ hash +'"]');     //匹配需要点击的菜单
    }
    if(!$obj.length){
    	$obj = $('#firstModular a').filter('[href="#app/version"]');
    }

    if($obj.length === 0){
        window.location.href = myConfig.logOutUrl;
    }else{
    	$obj.trigger('click', true);
        refreshModule($obj.attr('href').replace(/#/, "views/"));     //读取当前模块信息并显示
    }

}

/*获取数据显示模块内容*/
function refreshModule(url){
    showLoading();
    $('#main-content').css('visibility', 'hidden');
    $('#main-content').load(url + '.html?_=' + Date.now(), function(data, status){
        var h = $(window).height() - 50;
        $('.main-content').css('height', h);
        loadScript(url);
    });
}

function loadScript(url){
    $.getScript(url + '.js', function(){
        hideLoading();
        $('#main-content').css('visibility', 'visible');
    });
}

function resizeWindow(){
    $(window).on('resize', function(){
        var h = $(window).height() - 50;
        $('.main-content').css('height', h);
    });
}

function listenMenu(){
    $('#sidebar-collapse > i').on('click', function(){
        var $this = $(this);
        $('.sidebar-one').toggleClass('menu-min');
        if($this.hasClass('fa-angle-double-left')){
            $this.get(0).className = 'fa fa-angle-double-right';
        }else{
            $this.get(0).className = 'fa fa-angle-double-left';
        }
    });
}

function editPass(){
    return window.localStorage.getItem("ANDROID_PERMISSION_MOFIDYPASSWROK");
}