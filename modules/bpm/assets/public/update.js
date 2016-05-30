$(document).ready(function() {
	document.getElementById("button_save").addEventListener('click', function() {
        $.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
            function(data) {
                if(!data){
                    document.getElementById('workflow_editor').contentWindow['editor'].updateModule(id);
                }
                else {
                    $("#message").html("Only disabled Workflows can be edited.");
                    $("#dialog").modal('show');
                }
            }
        );
	});
	
	document.getElementById("button_cancel").addEventListener('click', function() {
		window.location="../workflow/index";
	});
});