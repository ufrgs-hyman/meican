/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

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
        $("#refresh-overlay").show();
        applyAll($("#task-id").attr("value"));
    });
});

function applyAll(taskId) {
    $.ajax({
        url: baseUrl+'/topology/change/apply-all',
        dataType: 'json',
        method: "GET",
        data: {
            task: taskId,
        },
        success: function(response) {
            $.pjax.reload({container:"#change-pjax"}); 
        },
        error: function(response) {
            $.pjax.reload({container:"#change-pjax"}); 
        }
    });
}


