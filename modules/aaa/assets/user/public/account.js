$(document).ready(function() {
    $('#userform-ischangedpass').on('ifChecked', function(event){
        $('#changePasswordForm').slideDown();
    });
    $('#userform-ischangedpass').on('ifUnchecked', function(event){
        $('#changePasswordForm').slideUp();
    });
});
