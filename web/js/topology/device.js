$(document).ready(function() {
	$.getJSON(baseUrl + "/topology/device/get-networks-id", 
		function(data) {
			var first = true;
		   	$.each(data, function(key, val) {
		   		if(selected_network != null && selected_network != val){
		    		$("#collapsable".concat(val)).slideUp();
	    			$("#collapseExpand".concat(val)).attr("src", baseUrl+"/images/plus.gif");
	    		}
	    		else{
	    			if(first == false){
		    			$("#collapsable".concat(val)).slideUp();
	    			    $("#collapseExpand".concat(val)).attr("src", baseUrl+"/images/plus.gif");
		   			}
		   			else first = false;
	    		}
	    		
		   		document.getElementById("collapseExpand".concat(val)).addEventListener("click", function(){
	    			if ($("#collapsable".concat(val)).css("display") == "none") {
	    			    $("#collapsable".concat(val)).slideDown();
	    			    $("#collapseExpand".concat(val)).attr("src", baseUrl+"/images/minus.gif" );
	    			} else {
	    			    $("#collapsable".concat(val)).slideUp();
	    			    $("#collapseExpand".concat(val)).attr("src", baseUrl+"/images/plus.gif");
	    			}
		   		});
		   	});
		}
	);
});

function submitDeleteForm() {
	$("#device-form").submit();
}