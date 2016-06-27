/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

$(document).ready(function() {
	$('#group-type').change(function () {
	    $('.listPermissions').hide();
	    $('#'+$(this).val()).show();
	})

});