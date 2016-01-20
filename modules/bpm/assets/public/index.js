// TOGGLE BUTTONS EVENT (ENABLE and DISABLE)
// ==============================
$(document).ready(function() {
	$('.toggle-event-class').change(function() {
    	var id = $(this).val();
    	if($(this).prop('checked')){
    		$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, function(data) {
				if(!data){
					$.getJSON(baseUrl + "/bpm/workflow/has-other-active?id="+id, function(data) {
						if(data!=-1){
							$.ajax({
								type: "GET",
								url: baseUrl + "/bpm/workflow/disable",
								data: "id=".concat(data),
								cache: false,
								success:function() {
									$("#toggle-"+data).prop('checked', false).change();
									$.ajax({
										type: "GET",
										url: baseUrl + "/bpm/workflow/active",
										data: "id=".concat(id),
										cache: false,
									}); 
								}
							});
						}
						else{
							$.ajax({
								type: "GET",
								url: baseUrl + "/bpm/workflow/active",
								data: "id=".concat(id),
								cache: false,
							});
						}
					});	
				}
			});	
    	}
    	else {
    		$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, function(data) {
				if(data){
					$("#message").html(tt("This Workflow is enabled for domain ")+data+tt(". This domain will not have an enabled workflow. Do you confirm?"));
					$("#dialog").modal('show');
					
					$("#ok-btn").show();
					$("#close-btn").show();
					$("#delete-btn").hide();
					$("#delete-btn").off("click");
					
					$("#ok-btn").off("click").click(function(){
						$("#dialog").modal('hide');
						$.ajax({
							type: "GET",
							url: baseUrl + "/bpm/workflow/disable",
							data: "id=".concat(id),
							cache: false,
						}); 
					});

					$("#close-btn").off("click").click(function(){
						$("#dialog").modal('hide');
					});
					
					$("#dialog").on('hidden.bs.modal', function (e) {
						$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, function(data){
							if(data) $("#toggle-"+id).prop('checked', true).change();
						})
					});
				}
			});
    	}
    });
})

// DELETE WORKFLOW
// ==============================
function deleteWorkflow(id){
	$("#message").html(tt("Delete this Workflow?"));
	$("#dialog").modal('show');
	
	$("#ok-btn").hide();
	$("#ok-btn").off("click");
	$("#close-btn").show();
	$("#delete-btn").show();
	
	$("#delete-btn").click(function(){
		$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
    		function(data) {
				if(!data){
					$.ajax({
						type: "GET",
						url: baseUrl + "/bpm/workflow/delete",
						data: "id=".concat(id),
						cache: false,
					});
				}
				else {
					$("#message").html(tt("This Workflow is enabled for domain ")+data+tt(". This domain will not have an enabled workflow. Do you confirm?"));
					$("#dialog").modal('show');
					
					$("#ok-btn").hide();
					$("#ok-btn").off("click");
					$("#close-btn").show();
					$("#delete-btn").show();
					
					$("#delete-btn").off("click").click(function(){
						$("#dialog").modal('hide');
			    		$.ajax({
			    			type: "GET",
			    			url: baseUrl + "/bpm/workflow/delete",
			    			data: "id=".concat(id),
			    			cache: false,
			    		});	
					});
				}
			}
		);
	});
}

// UPDATE
//==============================
function update(id){
	$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
		function(data) {
			if(!data){
				window.location="../workflow/update?id="+id;
			}
			else {
				$("#message").html(tt("Only disabled Workflows can be edited."));
				$("#dialog").modal('show');
				
				$("#ok-btn").hide();
				$("#close-btn").show();
				$("#delete-btn").hide();
			}
		}
	);
}