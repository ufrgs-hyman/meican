$(document).ready(function() {
/* ====================================================================================================
	Account
	=================================================================================================*/
    
    $('#accountform-ischangedpass').click(function() {
    	$('#changePasswordForm').slideToggle();
    });
    
});

function submitDeleteForm() {
	$("#user-form").submit();
}