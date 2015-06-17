$(document).ready(function() {
	
/* ====================================================================================================
	Create IDC URL
	=================================================================================================*/
    
	$("input[name='Domain[oscars_protocol]']").click(function() {
		var protocol = $(this).val().toLowerCase();
		var oscarsIP = $('#Domain_oscars_ip').val().toLowerCase();
		
		var url = protocol+'://'+oscarsIP+':8080/axis2/services/OSCARS';

		$('#idcUrl').html(url);
   });
	
	$('#Domain_oscars_ip').keyup(function() {
		if($("input[name='Domain[oscars_protocol]']").is(':checked')) { 
			var protocol = $("input[name='Domain[oscars_protocol]']:checked").val().toLowerCase();
			var oscarsIP = $(this).val().toLowerCase();
			
			var url = protocol+'://'+oscarsIP+':8080/axis2/services/OSCARS';
			
			$('#idcUrl').html(url);
		}
	});
});