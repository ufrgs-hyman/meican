$(document).on('ready pjax:success', function() {
    $("#grid-changes").on("click",'img.apply-button',  function() {
        $("#loading-dialog").dialog("open");
        context = $(this);
        $.ajax({
            url: baseUrl+'/topology/change/apply',
            dataType: 'json',
            method: "GET",
            data: {
                id: $(this).parent().parent().parent().attr('data-key'),
            },
            success: function(response) {
                $($(context.parent().parent().parent().children()[0]).children()[0]).attr("disabled","disabled");
                context.parent().parent().parent().addClass("success");
                context.attr("disabled","disabled");
                context.removeClass("apply-button");
                context.unwrap();
                $("#loading-dialog").dialog("close");
            },
            error: function() {
                context.parent().parent().parent().addClass("error");
                context.addClass("apply-button");
                $("#loading-dialog").dialog("close");
            }
        });
    });
});

$(document).ready(function() {
    $("#loading-dialog").attr("title", "Loading");
    $("#loading-dialog").html("<br>Wait a moment...<br><br>" + 
            '<div style="text-align: center;"><img src="' + baseUrl + '/images/ajax-loader.gif"></div>');
    $("#loading-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
    });

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


