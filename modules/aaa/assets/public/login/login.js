$(document).ready(function() {
	$("#loginform-login").focus();

	$("#cafe-button").click(function() {
		window.location.href = "https://" + window.location.href.split("/")[2] + "/secure";
		return false;
	});
});

