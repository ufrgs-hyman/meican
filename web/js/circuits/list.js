$(document).ready(function() {
	$("[name=toggler]").click(function(){
        $('.toHide').hide();
        $("#div_table_"+$(this).val()).show();
	});
});