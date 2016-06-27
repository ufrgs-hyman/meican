var scheduledInterval = {val: null};
var finishedInterval = {val: null};

$(document).ready(function() {
    $.pjax.defaults.timeout = 5000;
	prepareRefreshSwitch();
	enableInterval(scheduledInterval, 'scheduled-grid');
    enableInterval(finishedInterval, 'finished-grid');
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
    $("#auto-refresh-scheduled-switch").on('switchChange.bootstrapSwitch', function(event, state) {
        if(state) {
            refreshGrid('scheduled-grid');
            enableInterval(scheduledInterval, 'scheduled-grid')
        } else
            disableInterval(scheduledInterval);
    });

    $("#auto-refresh-finished-switch").on('switchChange.bootstrapSwitch', function(event, state) {
        if(state) {
            refreshGrid('finished-grid');
            enableInterval(finishedInterval, 'finished-grid')
        } else
            disableInterval(finishedInterval);
    });
}

function refreshGrid(gridId) {
    console.log('refreshing grid ' + gridId);
	$('#' + gridId).yiiGridView('applyFilter');
}
