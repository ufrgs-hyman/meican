$(document).ready(function() {
    console.log('holaacc');
    $('#userform-ischangedpass').on('ifChecked', function(event){
        console.log('click');
        $('#changePasswordForm').slideDown();
    });
    $('#userform-ischangedpass').on('ifUnchecked', function(event){
        $('#changePasswordForm').slideUp();
    });
});
