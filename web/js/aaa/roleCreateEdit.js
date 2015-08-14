$(document).ready(function() {
	var value = $( "#userdomainrole-_grouprolename option:selected" ).val();
	if(systemGroups.indexOf(value) != -1){
		$("#userdomainrole-domain option:first-child").attr("selected", true);
		$('#domain-select').hide();
	}
	else $('#domain-select').show();
	
	$('#userdomainrole-_grouprolename').change(function () {
		var value = $( "#userdomainrole-_grouprolename option:selected" ).val();
		if(systemGroups.indexOf(value) != -1){
			$("#userdomainrole-domain option:first-child").attr("selected", true);
			$('#domain-select').hide();
		}
		else $('#domain-select').show();
	})
});