$(document).ready(function() {
	document.getElementById("selectNetwork").disabled = true;
	document.getElementById("selectDomain").addEventListener("change", function() {
		var domainName = this.options[this.selectedIndex].text;
		if(domainName != "select"){
		    $.getJSON(baseUrl + "/topology/device/get-networks-by-domain?domainName="+domainName, 
		    		function(data) {
		        	    if(data.length>0){
		        	    	var options = '';
		        	    	document.getElementById("selectNetwork").disabled = false;
		        	    	$.each(data, function (key, data) {
		        	    		options += '<option value="'+data.id+'">' + data.name + '</option>';
		        	    	})
		        	    	$('#selectNetwork').html(options);
		        	    } else{
		        	    	document.getElementById("selectNetwork").disabled = true;
		        	    	$('#selectNetwork').html('<option></option>');
		        	    }
			        }
	        );
		} else {
			document.getElementById("selectNetwork").disabled = true;
	    	$('#selectNetwork').html('<option></option>');
		}
	});
});

function dev_changeDomain(elem) {
    $("#dev_network").attr("disabled","disabled");
    if (elem.value != -1) {
        var networks = null;
        for (var i in domains) {
            if (domains[i].id == elem.value) {
                networks = domains[i].networks;
                break;
            }
        }
        $("#dev_network").fillSelectBox(networks).removeAttr("disabled").slideDown();
    }
}

function updateNetworks(){
	$.ajax({
		type: "POST",
		url: "device/delete",
		data: "id=".concat(element),
		cache: false,
		success: function(html) {
			$.pjax.defaults.timeout = false;//IMPORTANT
			$.pjax.reload({container:'#pjaxContainer'});
			selectNum=0;
			$('#delete_button').hide();
			$('.selectionCheckbox').click(deleteControl);
		}
	});
}