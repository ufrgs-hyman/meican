var refreshInterval = {val: null};

$(document).ready(function() {
    $.pjax.defaults.timeout = 5000;
	prepareRefreshSwitch();
	enableInterval(refreshInterval, 'circuits-grid');
});

function disableInterval(interval) {
    clearInterval(interval.val);
}

function enableInterval(interval, gridId) {
    interval.val = setInterval(function() {
        refreshGrid(gridId);
    }, 120000);
}

function prepareRefreshSwitch() {
    $("#auto-refresh-switch").on('switchChange.bootstrapSwitch', function(event, state) {
        if(state) {
            refreshGrid('circuits-grid');
            enableInterval(refreshInterval, 'circuits-grid')
        } else
            disableInterval(refreshInterval);
    });
}

function refreshGrid(gridId) {
    console.log('refreshing grid ' + gridId);
	$('#' + gridId).yiiGridView('applyFilter');
}
