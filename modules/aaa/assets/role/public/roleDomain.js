/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 * @author Diego Pittol
 */

$(document).ready(function(){
	$(".add-domain-btn").click(function() {
		var id = $(this).attr("id");
	    $.ajax({
	        url: baseUrl + "/aaa/role/create-role-domain?id="+id,
	        success: function(response){
	            $('#add-role-domain-form-wrapper').html(response);
	            $("#add-role-domain-modal").modal("show");
	            $("#add-role-domain-modal").on("click", "#close-btn", function (){
	                $("#add-role-domain-modal").modal("hide");
	                return false;
	            });
	        }
	    });
	    return false;
	});
	
	$("#add-role-domain-modal").on('shown.bs.modal', function(){
		getGroups();
	});
	
	$("#add-role-domain-modal").on("change", "#userdomainrole-domain", function (){
		getGroups();
	});
	
	$("#edit-role-domain-modal").on("change", "#userdomainrole-domain", function (){
		getGroups();
	});

});

$("#add-role-domain-modal").on("click", "#save-role-btn", function (){
    $("#add-role-domain-form").submit();
    return false;
});

$("#role-domain-grid").on("click", '.btn-edit',  function() {
    roleId = $(this).attr('id');
    $.ajax({
        url: baseUrl + "/aaa/role/update-role-domain?id=" + roleId,
        success: function(response){
            $('#edit-role-domain-form-wrapper').html(response);
            $("#edit-role-domain-modal").modal("show");
            $("#edit-role-domain-modal").on("click", "#close-btn", function (){
                $("#edit-role-domain-modal").modal("hide");
                return false;
            });
        }
    });
    return false;
});

$("#edit-role-domain-modal").on("click", "#save-role-btn", function (){
    $("#edit-role-domain-form").submit();
    return false;
});

$("#delete-domain-role").click(function() {
    if($('.deleteDomain:checked').length > 0) { 
        $("#delete-role-domain-modal").modal("show");
    }
    else {
        $("#error-modal-domain").modal("show");
    }
    return false;
});

$("#error-modal-domain").on("click", "#close-btn", function (){
    $("#error-modal-domain").modal("hide");
    return false;
});

$("#delete-role-domain-modal").on("click", "#close-btn", function (){
    $("#delete-role-domain-modal").modal("hide");
    return false;
});

$("#delete-role-domain-modal").on("click", "#delete-role-btn", function (){
    $("#delete-role-domain-modal").modal("hide");
    $("#domain-role-form").submit();
    return false;
});

function getGroups(){
	var domain = $("#userdomainrole-domain option:selected" ).val();
	$.getJSON(baseUrl + "/aaa/role/get-groups-by-domain-name?name="+domain, 
		function(data) {
			//console.log(data);
		   	var selectBox = document.getElementById("userdomainrole-_grouprolename");
		   	
		   	var length = selectBox.options.length;
		   	for (i = 0; i < length; i++) selectBox.options[0] = null;
		   	
		   	$.each(data, function(key, val) {
		       	var newOption = document.createElement('option');
		       	newOption.text = val;
		        newOption.value = key;
				
				// For standard browsers
	            try {
	            	selectBox.add(newOption, null);
	            }
	            // For Microsoft Internet Explorer and other non-standard browsers.
	            catch (ex) {
	            	selectBox.add(newOption);
	            }
	    	});
		}
	);
}