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
	    	
			$("#dialog").dialog();
		
			$("#dialog").dialog("open");
			
			$("#dialog").dialog({
				dialogClass: "no-close",
				buttons: [
		          {
		        	  text: tt("Cancel"),
				      click: function() {
				    	  window.location="../workflow/index";
				      }
		          },
				    
		          {
		        	  text: "Ok",
				      click: function() {
				    	  var params = "?domainTop=".concat(selectBox.options[selectBox.selectedIndex].value);
				    	  $("#dialog").dialog("close");
				    	  window.location="../workflow/create".concat(params);
				      }
		          }
		       ]
			});
		} else {
			var params = "?domainTop=".concat(selectBox.options[selectBox.selectedIndex].value);
	  	  	$("#dialog").dialog("close");
	  	  	window.location="../workflow/create".concat(params);
		}
	});
});