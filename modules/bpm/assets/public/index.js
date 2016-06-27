/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Diego Pittol
 */

$(document).ready(function() {
	$(".btn-update").click(function() {
		var id = $(this).attr("id");
		$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id,
			function(data) {
				if(!data){
					window.location.href = baseUrl + "/bpm/workflow/update?id="+id;
				}
			}
		);
	});
	
	$(".btn-delete").click(function() {
		var id = $(this).attr("id");
		$("#delete-workflow-modal").modal('show');
		
		$("#delete-workflow-modal").on("click", "#cancel-btn", function (){
            $("#delete-workflow-modal").modal("hide");
            return false;
        });
		
		$("#delete-workflow-modal").on("click", "#delete-btn", function (){
			$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
	    		function(data) {
					if(!data){
						$.ajax({
							type: "GET",
							url: baseUrl + "/bpm/workflow/delete",
							data: "id=".concat(id),
							cache: false,
							success:function() {
								$("#delete-workflow-modal").modal("hide");
							}
						});
					}
					else {
						$("#delete-workflow-modal").modal("hide");
						$("#disable-message").html(data);
						$("#disable-workflow-modal").modal('show');

						$("#disable-workflow-modal").off("click");

						$("#disable-workflow-modal").on("click", "#cancel-btn", function (){
				            $("#disable-workflow-modal").modal("hide");
				            return false;
				        });
						
						$("#disable-workflow-modal").on("click", "#confirm-btn", function (){
							$("#disable-workflow-modal").modal("hide");
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
	});
	
	
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
									$("#"+data+'.btn-update').attr('disabled', false).change();
									$("#toggle-"+data).prop('checked', false).change();
									$.ajax({
										type: "GET",
										url: baseUrl + "/bpm/workflow/active",
										data: "id=".concat(id),
										cache: false,
										success: function(){
											$("#"+id+'.btn-update').attr("disabled", true).change();
										}
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
								success:function(){
									$("#"+id+'.btn-update').attr("disabled", true).change();
								}
							});
						}
					});	
				}
			});	
    	}
    	else {
    		$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, function(data) {
				if(data){
					$("#disable-message").html(data);
					$("#disable-workflow-modal").modal('show');
					
					$("#disable-workflow-modal").off("click");
					
					$("#disable-workflow-modal").on("click", "#cancel-btn", function (){
			            $("#disable-workflow-modal").modal("hide");
			            return false;
			        });
					
					$("#disable-workflow-modal").on("click", "#confirm-btn", function (){
						$("#disable-workflow-modal").modal("hide");
						$.ajax({
							type: "GET",
							url: baseUrl + "/bpm/workflow/disable",
							data: "id=".concat(id),
							cache: false,
							success:function(){
								$("#toggle-"+id).prop('checked', false).change();
								$("#"+id+'.btn-update').attr('disabled', false).change();
							}
						}); 
					});

					$("#disable-workflow-modal").on('hidden.bs.modal', function (e) {
						$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, function(data){
							if(data) $("#toggle-"+id).prop('checked', true).change();
						})
					});
				}
			});
    	}
    });
})