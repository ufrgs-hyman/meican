$(document).ready(function() {
	document.getElementById("button_save").addEventListener('click', function() {
		document.getElementById('workflow_editor').contentWindow['editor'].saveModule();
	});
	
	document.getElementById("button_cancel").addEventListener('click', function() {
		window.location="../workflow/index";
	});
});