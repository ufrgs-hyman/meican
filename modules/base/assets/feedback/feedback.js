$(document).ready(function() {
    $('.feedback-btn').on('click', function() {
        $("#feedback-modal").modal("show");
        return false;
    });

    $('#feedback-modal').on('click', '.cancel-btn', function() {
        $("#feedback-modal").modal("hide");
        return false;
    });

    $('#feedback-modal').on('click', '.send-btn', function() {
        sendFeedback();
        return false;
    });

    $('#feedback-modal').on('hidden.bs.modal', function () {
        $('#feedback-form').yiiActiveForm('resetForm');
        $("#feedbackform-subject").val("");
        $('#feedbackform-message').val("");
    })
});

function sendFeedback(){
    $("#feedback-form").yiiActiveForm("validateAttribute", 'feedbackform-subject');
    $("#feedback-form").yiiActiveForm("validateAttribute", 'feedbackform-message');

    setTimeout(function() {
        if($("#feedback-modal").find(".has-error").length > 0) {
            console.log("tem erro");
            return false;
        } else {
            $.ajax({
                type: 'POST',
                url: baseUrl + "/home/support/send-feedback",
                data: $("#feedback-form").serialize(),
                success: function (data) {
                    if(data) {
                        $.notify({
                            title: '<strong>Thank you!</strong>',
                            message: 'Your feedback is very important.'
                        },{
                            type: 'success'
                        });
                    } else {
                        $.notify({
                            title: '<strong>Ops!</strong>',
                            message: 'Something is wrong. Try again later.'
                        },{
                            type: 'error'
                        });
                    }
                    $("#feedback-modal").modal("hide");
                },
                error: function () { 
                    $.notify({
                        title: '<strong>Ops!</strong>',
                        message: 'Something is wrong. Try again later.'
                    },{
                        type: 'error'
                    });
                    $("#feedback-modal").modal("hide");
                }
            });
        }
    }, 200);
}
