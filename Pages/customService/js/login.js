$(function(){
    setSize();
    var user = $.cookie('custom-username');
    var pass = $.cookie('custom-passwd');
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
                $.cookie('custom-username', user, {expires:30});
                $.cookie('custom-passwd', passwd, {expires:30});
            }else{
                $.cookie('custom-username', null);
                $.cookie('custom-passwd', null);
            }
            var data = {"user": user, "passwd": passwd};
            AjaxPost('/Customer/home/user/login', data, function(data){
                console.log(data);
                window.localStorage.setItem("CUSTOM_PERMISSION_USEPOWER", getPowerStr(data));
                window.localStorage.setItem("CUSTOM_PERMISSION_USERNAME", user);
                window.localStorage.setItem("CUSTOM_PERMISSION_USERNICKNAME", data.name);
                window.localStorage.setItem("CUSTOM_PERMISSION_MOFIDYPASSWROK", data.modifyPasswd);
                console.log(data.id);
                window.localStorage.setItem("CUSTOM_PERMISSION_ID", data.id);
                window.location.href = 'welcome.html?_=' + Date.now();
            });
        }
    });

    $(document).keyup(function(e){
        if( e.keyCode == 13 ) $('#loginBtn').trigger('click');
    });

    window.onresize = setSize;
});

function setSize() {
    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    var height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;    
    $('#backImg').css({'width':width, 'height':height});
    var tmpH = (height/2)-165;
    $('#back').css("margin-top", tmpH.toString() + "px");
    $('#logo').css('left', (width/2 - 271) + 'px');
    $('#logo').css('top', (tmpH - 99) + 'px');
}

function getPowerStr(data){
    var str = {
        "root": "超级管理员",
        "admin": "客服管理员",
        "online": "在线客服",
        "normal": "普通客服",
        "customer": "客户"
    };
    var power = '';
    power += str[data.permission] + '、';
    return power.slice(0, power.length -1);
}

$('.login-remember').on('click', function(){
    console.log('xxxx');
});