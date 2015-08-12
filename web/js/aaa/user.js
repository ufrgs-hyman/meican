$(document).ready(function() {
    $('#accountform-ischangedpass').click(function() {
    	$('#changePasswordForm').slideToggle();
    });
});

function submitDeleteForm() {
	$("#user-form").submit();
}