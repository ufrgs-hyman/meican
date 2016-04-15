$(document).ready(function() {
	
	if(selected_domain) $(window).scrollTop($('#box-dom-'+selected_domain).offset().top);
	
	$(".btn-delete").click(function() {
		var domainId = $(this).attr("value");
	    if($('.deleteUrn'+domainId+':checked').length > 0) { 
	    	$("#delete-port-modal").modal("show");
	    	
	    	$('#delete-port-btn').off("click");
            
            $("#delete-port-modal").on("click", "#delete-port-btn", function (){
        		$.ajax({
        	          url: $("#port-grid-form-"+domainId).attr('action'),
        	          type: 'post',
        	          data: $("#port-grid-form-"+domainId).serialize(),
        	          success: function (data) {
        	        	  if(data==true) window.location="../port/index?id="+domainId;
        	        	  else if(data!=null){
        	        		  $("#delete-port-modal").modal("hide");
        	        		  $("#message").html(data);
        	        		  $("#dialog").modal("show");
        	        	  }
        	        	  else $("#delete-port-modal").modal("hide");
        	          }
        	     });
        	});
	    	
	    	$("#delete-port-modal").on("click", "#cancel-btn", function (){
                $("#delete-port-modal").modal("hide");
                return false;
            });
	    }
	    else {
	        $("#error-modal").modal("show");
	    }
	    return false;
	});
	
	$(".btn-edit").click(function() {
		var portId = $(this).attr("id");
	    $.ajax({
	        url: baseUrl + "/topology/port/update?id=" + portId,
	        success: function(response){
	            $('#edit-port-form-wrapper').html(response);
	            $("#edit-port-modal").modal("show");
	            
	            $('#save-edit-port-btn').off("click");
	            
	            $("#edit-port-modal").on("click", "#save-edit-port-btn", function (){
	        		$.ajax({
	        	          url: $("#edit-port-form").attr('action'),
	        	          type: 'post',
	        	          data: $("#edit-port-form").serialize(),
	        	          success: function (data) {
	        	        	  if(data) $('#edit-port-form-wrapper').html(data);
	        	        	  else{
	        	        		  $.ajax({
	        	        			  url: baseUrl + "/topology/port/get-domain-id?id=" + portId,
	        	        			  success: function(response){
	        	        				  window.location="../port/index?id="+response;
	        	        			  }
		        	        	  });
	        	        	  }
	        	          }
	        	     });
	        	});
	            
	            $("#edit-port-modal").on("click", "#cancel-btn", function (){
	                $("#edit-port-modal").modal("hide");
	                return false;
	            });
	        }
	    });
	    return false;
	});
	
	$(".btn-add").click(function() {
		var domainId = $(this).attr("value");
	    $.ajax({
	        url: baseUrl + "/topology/port/create?id=" + domainId,
	        success: function(response){
	            $('#add-port-form-wrapper').html(response);
	            $("#add-port-modal").modal("show");
	            
	            $('#save-port-btn').off("click");
	            
	            $("#add-port-modal").on("click", "#save-port-btn", function (){
	        		$.ajax({
	        	          url: $("#add-port-form").attr('action'),
	        	          type: 'post',
	        	          data: $("#add-port-form").serialize(),
	        	          success: function (data) {
	        	        	  if(data) $('#add-port-form-wrapper').html(data);
	        	        	  else {
	        	        		  window.location="../port/index?id="+domainId;
	        	        	  }
	        	          }
	        	     });
	        	});
	            
	            $("#add-port-modal").on("click", "#cancel-btn", function (){
	                $("#add-port-modal").modal("hide");
	                return false;
	            });
	        }
	    });
	    return false;
	});
});