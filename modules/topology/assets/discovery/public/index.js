/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

function submitDeleteForm() {
    $("#rule-form").submit();
}

$(document).ready(function() {
    $("#rule-grid").on("click",'.start-discovery',  function() {
        var context = $(this);
        $.ajax({
            url: baseUrl+'/topology/discovery/execute',
            method: "GET",
            data: {
                rule: context.parent().parent().attr('data-key'),
            },
            success: function(response) {
            },
            error: function(response) {
            }
        });
        return false;
    });
});