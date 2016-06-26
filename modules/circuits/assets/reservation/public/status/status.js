var refreshInterval;

$(document).ready(function() {
	prepareRefreshSwitch();
	enableAutoRefresh();
    $.pjax.defaults.timeout = 5000;
});

function disableAutoRefresh() {
    clearInterval(refreshInterval);
}

function enableAutoRefresh(pjaxContainerId) {
    refreshInterval = setInterval(function() {
        refreshGrid(pjaxContainerId);
    }, 120000);
}

function prepareRefreshSwitch() {
	$("#auto-refresh-scheduled-switch").bootstrapSwitch();
    $("#auto-refresh-scheduled-switch").on('switchChange.bootstrapSwitch', function(event, state) {
        state ? enableAutoRefresh() : disableAutoRefresh();
    });

    $("#auto-refresh-finished-switch").bootstrapSwitch();
    $("#auto-refresh-finished-switch").on('switchChange.bootstrapSwitch', function(event, state) {
        state ? enableAutoRefresh() : disableAutoRefresh();
    });
}

function refreshPjax(id) {
    $.pjax.reload({
        container:'#' + id
    });
}

function refreshGrid(gridId) {
	$('#' + gridId).yiiGridView('applyFilter');
}
