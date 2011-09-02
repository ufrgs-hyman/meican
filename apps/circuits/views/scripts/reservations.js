function refreshStatus() {
    //$('.load').show();
    
    var count = 0;

        $.each($("tbody tr"), function() {
            count++;
        });
    
    for (var i in domains) {
        $.ajax ({
            type: "POST",
            url: "main.php?app=circuits&controller=reservations&action=refresh_status",
            data: {
                count: count,
                dom_id: domains[i]
            },
            dataType: "json",
            success: function(data) {
                //$('.load').hide();
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
                    location.href = 'main.php?app=init&controller=gui';
            }
        });
    }
}

function griRefreshStatus(res_id) {
    //$('.load').show();
    
    $.ajax ({
        type: "POST",
        url: "main.php?app=circuits&controller=reservations&action=gri_refresh_status",
        data: {
            res_id: res_id
        },
        dataType: "json",
        success: function(data) {
            //$('.load').hide();
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
                location.href = 'main.php?app=init&controller=gui';
        }
    });
}

function checkStatus(index, status) {
    switch (status) {
        case "FAILED":
            $('#line' + index).css( {
                'background' : '#f99b9b'
            });
            break;
        case "ACTIVE":
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
            break;
        default:
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
        $("#cancel_button").removeAttr("disabled");
        $("#cancel_button").removeAttr("style");
    } else {
        $("#cancel_button").attr("disabled","disabled");
        $("#cancel_button").css( {
            'opacity' : '0.4'
        });
    }
}

function changeName(elem) {
    $.post("main.php?app=circuits&controller=reservations&action=update_name", {
        name: elem.value
    });
}

function changeFlow(elem) {
    $.post("main.php?app=circuits&controller=reservations&action=update_flow", {
        flow: elem.value
    }, function() {
        $("#next_button").removeAttr("disabled");    
    });
}

function changeTimer(elem) {
    $.post("main.php?app=circuits&controller=reservations&action=update_timer", {
        timer: elem.value
    }, function() {
        $("#next_button").removeAttr("disabled");
    });
}