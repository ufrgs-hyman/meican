//(function($){ 
//    
//var cancelCont = 0;
//
//var res_map = null;
//var res_center = null;
//var res_markersArray = [];
//var res_bounds = [];
//var res_lines = [];
//var res_myOptions = null;

function refreshStatus() {
    $('.load').show();
    
    for (var i in domains) {
        $.ajax ({
            type: "POST",
            url: baseUrl+'circuits/reservations/refresh_status',
            data: {
                dom_id: domains[i]
            },
            dataType: "json",
            success: function(data) {
                $('.load').hide();
                if (data) {
                    var status_id = null;

                    for (var i=0; i < data.length; i++) {
                        status_id = '#status' + data[i].id;
                
                        if (data[i].translate != $(status_id).html()) {
                            $(status_id).empty();
                            $(status_id).html(data[i].translate);
                
                            checkStatus(data[i].id, data[i].name);
                        }
                    }
                } else {
                    setFlash(str_error_refresh_status,"error");
                }
            },
            error: function(jqXHR) {
                if (jqXHR.status == 406)
                    location.href = baseUrl+'init/gui';
            }
        });
    }
}

function griRefreshStatus(res_id) {
    $('.load').show();
    
    $.ajax ({
        type: "POST",
        url: baseUrl+'circuits/reservations/gri_refresh_status',
        data: {
            res_id: res_id
        },
        dataType: "json",
        success: function(data) {
            $('.load').hide();
            if (data) {
                if (data.length != 0) {
                    var status_id = null;

                    for (var i=0; i < data.length; i++) {
                        status_id = '#status' + data[i].id;
                
                        if (data[i].translate != $(status_id).html()) {
                            $(status_id).empty();
                            $(status_id).html(data[i].translate);
                
                            checkStatus(data[i].id, data[i].name);
                        }
                    }
                }
            } else {
                setFlash(str_error_refresh_status,"error");
            }
        },
        error: function(jqXHR) {
            if (jqXHR.status == 406)
                location.href = baseUrl+'init/gui';
        }
    });
}

function checkStatus(index, status) {
    switch (status) {
        case "FAILED":
        case "UNKNOWN":
        case "NO_GRI":
            // pinta a linha de vermelho
            $('#line' + index).css( {
                'background' : '#f99b9b'
            });
            break;
        case "ACTIVE":
            // pinta a linha de verde
            $('#line' + index).css( {
                'background' : '#99ec99'
            });
            $('#cancel' + index).removeAttr("disabled");
            break;
        // do nothing in these cases
        case "FINISHED":
        case "CANCELLED":
        case "INTEARDOWN":
        case "INMODIFY":
            // remove cor da linha
            $('#line' + index).removeAttr('style');
            break;
        default:
            // remove cor da linha
            $('#line' + index).removeAttr('style');
            $('#cancel' + index).removeAttr("disabled");
            break;
    }
}

function disabelCancelButton(elemId) {
    if ($(elemId).attr("checked"))
        cancelCont++;
    else
        cancelCont--;

    if (cancelCont) {
        $("#cancel_button").button('enable');
    } else {
        $("#cancel_button").button('disable');
    }
}


//})(jQuery);