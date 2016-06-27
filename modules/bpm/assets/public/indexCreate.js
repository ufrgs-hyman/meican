/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Diego Pittol
 */

$(document).ready(function() {
	
	var selectBox = document.getElementById("selectDomain");

	$.getJSON(baseUrl + "/bpm/workflow/get-user-domains", 
		function(data) {
		    var i;
		    for(i=selectBox.options.length-1;i>=0;i--){
		    	selectBox.remove(i);
		    }
		    
		    $.each(data, function(key, val) {
		    	var newOption = document.createElement('option');
	            newOption.text = val;
	            newOption.value = key;
	            
	            // For standard browsers
	            try { selectBox.add(newOption, null); }
	            // For Microsoft Internet Explorer and other non-standard browsers.
	            catch (ex) { selectBox.add(newOption); }
		    });
		    
		    if(selectBox.options.length > 1){
		    	
		    	$("#dialog").modal({
		            backdrop: 'static',
		            keyboard: false
		        });
		    	
		    	$('#button_ok').click(function(){
		    		var params = "?domainTop=".concat(selectBox.options[selectBox.selectedIndex].value);
		    		$('#dialog').modal('hide');
			    	window.location="../workflow/create".concat(params);
		    	});
		    	
		    	$('#button_cancel').click(function(){
		    		window.location="../workflow/index";
		    	});
			} else if(selectBox.options.length == 1){
				var params = "?domainTop=".concat(selectBox.options[selectBox.selectedIndex].value);
		  	  	window.location="../workflow/create".concat(params);
			} else {
				window.location="../workflow/index";
			}
		}
	);
});