$(function(){
    var user = $.cookie('android-username');
    var pass = $.cookie('android-passwd');
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
                $.cookie('android-username', user, {expires:30});
                $.cookie('android-passwd', passwd, {expires:30});
            }else{
                $.cookie('android-username', null);
                $.cookie('android-passwd', null);
            }
            var data = {"user": user, "passwd": passwd};
            AjaxPost('/Android/home/user/login', data, function(data){
                window.localStorage.setItem("ANDROID_PERMISSION_USEPOWER", getPowerStr(data.auths));
                window.localStorage.setItem("ANDROID_PERMISSION_USERNAME", user);
                window.localStorage.setItem("ANDROID_PERMISSION_USERNICKNAME", data.name);
                window.localStorage.setItem("ANDROID_PERMISSION_MOFIDYPASSWROK", data.isMofidyPasswd);
                window.location.href = 'welcome.html?_=' + Date.now();
            });
        }
    });

    $(document).keyup(function(e){
        if( e.keyCode == 13 ) $('#loginBtn').trigger('click');
    });
});

function getPowerStr(data){
    var str = {
        "tourist": "普通游客",
        "tester": "测试用户",
        "publisher": "发布用户",
        "admin": "系统管理员"
    };
    var power = '';
    for(var i = 0, len = data.length; i < len; i++){
        power += str[data[i]] + '、';
    }
    return power.slice(0, power.length -1);
}

$('.login-remember').on('click', function(){
    console.log('xxxx');
});