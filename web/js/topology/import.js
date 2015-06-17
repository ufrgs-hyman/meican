$(document).ready(function() {
    $("#loading-dialog").attr("title", "Loading");
    $("#loading-dialog").html("<br>Waiting a response from the discovery service...<br><br>" + 
    		'<div style="text-align: center;"><img src="' + baseUrl + '/images/ajax-loader.gif"></div>');
    $("#loading-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
    });
    
    $("#import-button").click(function() {
    	$("#loading-dialog").dialog("open");
    	return true;
    });
});
