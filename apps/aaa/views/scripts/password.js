if (language == 'pt-BR') {
    $("#userInfo").css('margin-left', '37px');
} else {
    $("#userInfo").css('margin-left', '19px');
}

function showPasswdBox(){
    $("#changePassword").attr({
        disabled: 'disabled'
    });
    $('#tpassword').slideDown();
}