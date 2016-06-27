/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Diego Pittol
 */

$(document).ready(function() {
	document.getElementById("button_save").addEventListener('click', function() {
		document.getElementById('workflow_editor').contentWindow['editor'].saveModule();
	});
	
	document.getElementById("button_cancel").addEventListener('click', function() {
		window.location="../workflow/index";
	});
});