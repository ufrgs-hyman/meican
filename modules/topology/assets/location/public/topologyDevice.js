/**
 * @copyright Copyright (c) 2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

$(document).ready(function() {
// ====================================================================================================
// Get Network List
// =================================================================================================
	$('#locationDomain').change(function() {
		domainId = (this).value;
		
		$.ajax({
	        type: 'POST',
	        url: '/topology/network/getNetworkByDomain',
	        dataType: 'json',
	        data: {
	        	id: domainId
	        },
	        success: function(data){
	        	var select;
	        	var arraySize = Object.keys(data).length; //Length of JSON Object
	        	
	        	if(arraySize == 0) {
	        		alert("This domain doesn't have any network");
	        		
	        		$('#Location_network_id').find('option').remove().end();
	        		
	        		$('#Location_network_id').attr('disabled', 'disabled');
	        	}
	        	else {
		        	select = '';
		        	
		        	$.each(data, function(index, name){
		        		select += '<option value="'+index+'">'+name+'</option>';
		        	});
		        	
		        	$('#Location_network_id').find('option').remove().end().append(select);
		        	$('#Location_network_id').removeAttr('disabled');
	        	}	        	
	        }
	    });	
	});
});