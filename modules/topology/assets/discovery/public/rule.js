$(document).ready(function() {
    $('#cron-widget').cron({
        initial: $("#discoveryruleform-freq").val() ? $("#discoveryruleform-freq").val() : "0 12 * * *",
        onChange: function() {
            $('#discoveryruleform-freq').val($(this).cron("value"));
        },
    });

    $("#connectionform-acceptrelease").on("switchChange.bootstrapSwitch", function(event, state) {
        validateEditForm();
    });
});