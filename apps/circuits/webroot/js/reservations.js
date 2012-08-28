
function refreshStatus() {
    for (var i in domains) {
        $('.load' + domains[i]).show();
        
        $.ajax ({
            type: "POST",
            url: baseUrl+'circuits/reservations/refresh_status',
            data: {
                dom_id: domains[i]
            },
            dom_id: domains[i],
            dataType: "json",
            success: function(data) {
                $('.load' + this.dom_id).hide();
                
                if (data) {
                    if (data.length > 0) {
                        var status_id = null;

                        for (var j in data) {
                            status_id = '#status' + data[j].id;
                
                            if (data[j].status != $(status_id).html()) {
                                $(status_id).empty();
                                $(status_id).html(data[j].status);
                
                                checkStatus(data[j].id, data[j].original_status);
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
        case "PENDING":
            // pinta a linha de azul
            $('#line' + index).css( {
                'background' : '#46DFFF'
            });
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