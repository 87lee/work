//@ sourceURL=user.pwd.js
$(function () {
    $('#submit').click(function(){
        var oldPwd = $('#oldPwd').val();
        var newPwd = $('#newPwd').val();
        if(oldPwd == ' ' || !oldPwd){
            alert('请输入旧密码');
            return;
        }
        if(newPwd == ' ' || !newPwd){
            alert('请输入新密码');
            return;
        }
        var user = window.localStorage.getItem("PERMISSION_BACK_USERNAME");
        var data = {"user": user, "old": oldPwd, "new": newPwd};
        AjaxPost('/Monitoring/home/user/passwd', data, function(){
            alert('修改成功');
            return;
        });
    });
});