$(document).ready(function() {
	$('#group-type').change(function () {
	    $('.listPermissions').hide();
	    $('#'+$(this).val()).show();
	})

});