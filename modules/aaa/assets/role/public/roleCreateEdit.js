$(document).ready(function() {
	
});

$("#delete-role-grid-btn").click(function() {
    if($(':checkbox:checked').length > 0) { 
        $("#delete-role-modal").modal("show");
    }
    else {
        $("#error-modal").modal("show");
    }
    return false;
});

$("#add-role-grid-btn").click(function() {
    $.ajax({
        url: baseUrl + "/aaa/role/create?id=1",
        success: function(response){
            $('#add-role-form-wrapper').html(response);
            $("#add-role-modal").modal("show");
            $("#add-role-modal").on("click", "#close-btn", function (){
                $("#add-role-modal").modal("hide");
                return false;
            });
            
            monitorGroupType();
        }
    });
    return false;
});

$("#role-grid").on("click",'img.edit-role-grid-btn',  function() {
    roleId = $(this).parent().parent().parent().attr('data-key');
    $.ajax({
        url: baseUrl + "/aaa/role/update?id=" + roleId,
        success: function(response){
            $('#edit-role-form-wrapper').html(response);
            $("#edit-role-modal").modal("show");
            $("#edit-role-modal").on("click", "#close-btn", function (){
                $("#edit-role-modal").modal("hide");
                return false;
            });
            
            monitorGroupType();
        }
    });
    return false;
});

$("#error-modal").on("click", "#close-btn", function (){
    $("#error-modal").modal("hide");
    return false;
});

$("#delete-role-modal").on("click", "#close-btn", function (){
    $("#delete-role-modal").modal("hide");
    return false;
});

$("#add-role-modal").on("click", "#save-role-btn", function (){
    $("#add-role-form").submit();
    return false;
});

$("#edit-role-modal").on("click", "#save-role-btn", function (){
    $("#edit-role-form").submit();
    return false;
});

$("#delete-role-btn").click(function (){
    submitDeleteForm();
});

function submitDeleteForm() {
    $("#role-grid-form").submit();
}

function monitorGroupType(){
	var value = $("#userdomainrole-_grouprolename option:selected" ).val();
    if(systemGroups.indexOf(value) != -1){
		$("#userdomainrole-domain option:first-child").attr("selected", true);
		$('#userdomainrole-domain').attr('disabled', 'disabled');
	}
	else $('#userdomainrole-domain').removeAttr('disabled');
	
	$("#userdomainrole-_grouprolename").on("change", function (){
		var value = $( "#userdomainrole-_grouprolename option:selected" ).val();
		if(systemGroups.indexOf(value) != -1){
			$("#userdomainrole-domain option:first-child").attr("selected", true);
			$('#userdomainrole-domain').attr('disabled', 'disabled');
		}
		else $('#userdomainrole-domain').removeAttr('disabled');
    });
}