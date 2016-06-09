$(function(){
    var user = $.cookie('username');
    var pass = $.cookie('passwd');
    if( user != 'undefind' ){
        $('#login-name').val(user);
        $('#login-pass').val(pass);
    }

    $('#loginBtn').click(function(){
        var user = $('#login-name').val();
        var passwd = $('#login-pass').val();
        if(user == ' ' || !user){
            alert('请输入用户名');
        }else if(passwd == ' ' || !passwd){
            alert('请输入密码');
        }else{
            var check = $('#remember').attr('checked');
            if( check == 'checked' ){
                $.cookie('username', user, {expires:30});
                $.cookie('passwd', passwd, {expires:30});
            }else{
                $.cookie('username', null);
                $.cookie('passwd', null);
            }
            var data = {"user": user, "passwd": passwd};
            AjaxPost('/user/login', data, function(data){
                var str = JSON.stringify(data.modules);
                window.localStorage.setItem("PERMISSION", str);
                window.localStorage.setItem("PERMISSION_USERNAME", user);
                window.location.href = 'welcome.html?_=' + Date.now();
            });
        }
    });

    $(document).keyup(function(e){
        if( e.keyCode == 13 ) $('#loginBtn').trigger('click');
    });

    var title = getTitle();
    document.title = title + '运营管理系统';
    $('.login-header h2').html('欢迎来到' + title + '运营管理系统');
});