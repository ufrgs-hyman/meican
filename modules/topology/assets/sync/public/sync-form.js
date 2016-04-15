$(document).ready(function() {
    $('#cron-widget').cron({
        initial: $("#syncform-freq").val() ? $("#syncform-freq").val() : "0 12 * * *",
        onChange: function() {
            $('#syncform-freq').val($(this).cron("value"));
        },
    });
});

$('#cron-open-link').click(function() {
    $("#cron-dialog").dialog({
            title: I18N.t("Set recurrence"),
            width: 360,
            height: 200,
            modal: true,
            buttons: [{
                text: I18N.t("Save"),
                click: function() {
                    $(this).dialog('close');
                }},
                {
                text: I18N.t("Cancel") + " (ESC)",
                click: function() {
                    $(this).dialog('close');
                }
            }],
        });
});

$("#syncform-protocol").on("change", function() {
    setVisibleDSOptions();
});

function setVisibleDSOptions() {
    $("#subscribed-row").attr("disabled", $("#syncform-protocol").val() != 'NSI_DS_1_0');
    $("#syncform-subscribe_enabled").attr("disabled", $("#syncform-protocol").val() != 'NSI_DS_1_0');
}
