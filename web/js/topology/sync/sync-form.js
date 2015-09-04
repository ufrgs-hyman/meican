$(document).ready(function() {
    $('#cron-freq').cron({
        initial: $("#syncform-freq").val() ? $("#syncform-freq").val() : "0 12 * * *",
        onChange: function() {
            $('#syncform-freq').val($(this).cron("value"));
        },
    });
});

$('#syncform-freq_enabled').click(function() {
    $("#cron-freq").toggle();
});

$("#syncform-type").on("change", function() {
    setVisibleDSOptions();
});

function setVisibleDSOptions() {
    if ($("#syncform-type").val() != 'NSI_DS_1_0') {
        $("#subscribed-row").hide();
    } else {
        $("#subscribed-row").show();
    }
}
