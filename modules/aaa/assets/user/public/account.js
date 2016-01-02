$(document).ready(function() {
    $('#accountform-ischangedpass').on('ifChecked', function(event){
        $('#changePasswordForm').slideDown();
    });
    $('#accountform-ischangedpass').on('ifUnchecked', function(event){
        $('#changePasswordForm').slideUp();
    });
});
