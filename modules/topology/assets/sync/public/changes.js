$(document).on('ready pjax:success', function() {
    $("#refresh-overlay").hide();
    $("#change-grid").on("click",'img.apply-button',  function() {
        $("#refresh-overlay").show();
        context = $(this);
        $.ajax({
            url: baseUrl+'/topology/change/apply',
            dataType: 'json',
            method: "GET",
            data: {
                id: $(this).parent().parent().parent().attr('data-key'),
            },
            success: function(response) {
                $.pjax.reload({container:"#change-pjax"}); 
            },
            error: function() {
                $.pjax.reload({container:"#change-pjax"}); 
            }
        });
        return false;
    });
});

$(document).ready(function() {
    $("#apply-all").on("click", function() {
        $("#loading-dialog").dialog("open");
        applyAll($("#sync-event-id").text());
    });
});

function applyAll(eventId) {
    $.ajax({
        url: baseUrl+'/topology/change/apply-all',
        dataType: 'json',
        method: "GET",
        data: {
            eventId: eventId,
        },
        success: function(response) {
            window.location.href = baseUrl + "/topology/change/pending?eventId=" + eventId;
        },
        error: function(response) {
            console.log("error");
        }
    });
}


