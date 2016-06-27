/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 * @author Diego Pittol
 */

$(document).ready(function() {
	$(".add-system-btn").click(function() {
		var id = $(this).attr("id");
	    $.ajax({
	        url: baseUrl + "/aaa/role/create-role-system?id="+id,
	        success: function(response){
	            $('#add-role-system-form-wrapper').html(response);
	            $("#add-role-system-modal").modal("show");
	            $("#add-role-system-modal").on("click", "#close-btn", function (){
	                $("#add-role-system-modal").modal("hide");
	                return false;
	            });
	        }
	    });
	    return false;
	});
});

$("#add-role-system-modal").on("click", "#save-role-btn", function (){
    $("#add-role-system-form").submit();
    return false;
});

$("#role-system-grid").on("click", '.btn-edit',  function() {
    roleId = $(this).attr('id');
    $.ajax({
        url: baseUrl + "/aaa/role/update-role-system?id=" + roleId,
        success: function(response){
            $('#edit-role-system-form-wrapper').html(response);
            $("#edit-role-system-modal").modal("show");
            $("#edit-role-system-modal").on("click", "#close-btn", function (){
                $("#edit-role-system-modal").modal("hide");
                return false;
            });
        }
    });
    return false;
});

$("#error-modal").on("click", "#close-btn", function (){
    $("#error-modal").modal("hide");
    return false;
});

$("#delete-system-role").click(function() {
    if($('.deleteSystem:checked').length > 0) { 
        $("#delete-role-system-modal").modal("show");
    }
    else {
        $("#error-modal-domain").modal("show");
    }
    return false;
});

$("#delete-role-system-modal").on("click", "#close-btn", function (){
    $("#delete-role-system-modal").modal("hide");
    return false;
});

$("#edit-role-system-modal").on("click", "#save-role-btn", function (){
    $("#edit-role-system-form").submit();
    return false;
});

$("#delete-role-system-modal").on("click", "#delete-role-btn", function (){
    $("#delete-role-system-modal").modal("hide");
    $("#system-role-form").submit();
    return false;
});