function update(id){
	$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
		function(data) {
			if(!data){
				window.location="../workflow/update?id="+id;
			}
			else {
				$("#message").html(tt("Only disabled Workflows can be edited."));
				$("#dialog").modal('show');
			}
		}
	);
}