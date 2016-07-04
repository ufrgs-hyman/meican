/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

$(document).ready(function() {
	$('#group-type').change(function () {
	    $('.table').hide();
	    $('#'+$(this).val()).show();
	})

});