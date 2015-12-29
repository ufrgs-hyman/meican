$(document).ready(function() {
	var value = $( "#userdomainrole-_grouprolename option:selected" ).val();
	if(systemGroups.indexOf(value) != -1){
		$("#userdomainrole-domain option:first-child").attr("selected", true);
		$('#domain-select').attr('disabled', 'disabled');
	}
	else $('#domain-select').removeAttr('disabled');
	
	$('#userdomainrole-_grouprolename').change(function () {
		var value = $( "#userdomainrole-_grouprolename option:selected" ).val();
		if(systemGroups.indexOf(value) != -1){
			$("#userdomainrole-domain option:first-child").attr("selected", true);
			$('#domain-select').attr('disabled', 'disabled');
		}
		else $('#domain-select').removeAttr('disabled');
	})
});