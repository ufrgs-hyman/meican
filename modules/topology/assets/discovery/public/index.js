/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

var refreshInterval;

function disableRefresh() {
    clearInterval(refreshInterval);
}

function enableRefresh() {
    refreshPjax('rule-pjax');
    refreshInterval = setInterval(function() {
        refreshPjax('rule-pjax');
    }, 30000);
}

function refreshPjax(id) {
    $.pjax.defaults.timeout = 5000;
    $.pjax.reload({
        container:'#' + id
    });
}

function submitDeleteForm() {
    $("#rule-form").submit();
}

$(document).ready(function() {
    $("#rule-grid").on("click",'.start-discovery',  function() {
        enableRefresh();
        var context = $(this);
        MAlert.show('Discovery task started.', 'Wait a moment while we analyze the provider topology. When finished, you will be notified.', 'success');
        $.ajax({
            url: baseUrl+'/topology/discovery/execute',
            method: "GET",
            data: {
                rule: context.parent().parent().attr('data-key'),
            },
            success: function(response) {
                MAlert.show('Discovery task finished.', 'Your results are available in the following task.', 'success');
            },
            error: function(response) {
                MAlert.show('Discovery task failed.', 'Sorry, try again later.', 'danger');
            }
        });
        return false;
    });
});