function submitDeleteForm() {
    $("#sync-form").submit();
}

$(document).ready(function() {
    $("#loading-dialog").attr("title", "Loading");
    $("#loading-dialog").html("<br>Wait a moment...<br><br>" + 
            '<div style="text-align: center;"><img src="' + baseUrl + '/images/ajax-loader.gif"></div>');
    $("#loading-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
    });

    $("#grid-sync").on("click",'img.sync-button',  function() {
        $("#loading-dialog").dialog("open");
        context = $(this);
        $.ajax({
            url: baseUrl+'/topology/sync/execute',
            dataType: 'json',
            method: "GET",
            data: {
                id: context.parent().parent().parent().attr('data-key'),
            },
            success: function(response) {
                window.location.href = baseUrl + "/topology/change/pending?eventId=" + response;
            },
            error: function(response) {
                $("#loading-dialog").html('<br><div class="error">Error. Contact your administrator.</div><br>');
            }
        });
    });
});