$(document).ready(function() {
	prepareRefreshButton();
	disableAutoRefresh();
});

var refresher;

function prepareRefreshButton() {
	$("#refresh-button").click(function(){
		if ($("#refresh-button").val() == "true") {
			disableAutoRefresh();
		} else {
			enableAutoRefresh();
		}
	});
}

function disableAutoRefresh() {
	$("#loader-img").hide();

	$("#refresh-button").val('false');
	clearInterval(refresher);
	$("#refresh-button").text(tt("Enable auto refresh"));
}

function enableAutoRefresh() {
	$("#loader-img").show();
	$("#cancel-button").attr("disabled", 'disabled');

	updateGridView();
	$("#refresh-button").val('true');
	refresher = setInterval(updateGridView, 30000);
	$("#refresh-button").text(tt("Disable auto refresh"));
}

function updateGridView() {
	$.pjax.defaults.timeout = false;
	$('#grid').yiiGridView('applyFilter');
}