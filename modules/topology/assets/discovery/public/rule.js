$(document).ready(function() {
    $('#cron-widget').cron({
        initial: $("#syncform-freq").val() ? $("#syncform-freq").val() : "0 12 * * *",
        onChange: function() {
            $('#syncform-freq').val($(this).cron("value"));
        },
    });
});