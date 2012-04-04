var request = {};

(function() {
    var actionUrl = null;
    var response = null;
    
    this.reply = function(response) {
        //var message = prompt();
        if (response=="accept"){
            $("#MessageImg").attr("src", baseUrl+"webroot/img/hand_good.png");
            $("#MessageLabel").html("Request will be accepted, please provide a message: ");
        }else{
            $("#MessageImg").attr("src", baseUrl+"webroot/img/hand_bad.png");
            $("#MessageLabel").html("Request will be rejected, please provide a message: ");
        }
        $("#Message").val('');
        this.response = response;
        $("#dialog-form").dialog("open");
    };

    this.setActionUrl = function(url){
        this.actionUrl = url;
        var request = this;
        
        $("#dialog-form").dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            buttons: {
                "Ok": function() {
                    var message = $("#Message").val();
                    if (message && message != "")
                        $.navigate({
                            type: "POST",
                            url: url,
                            data: {
                                response: request.actionUrl, 
                                message: request.response
                            }
                        });
                    $(this).dialog("close");
                },
                "Cancel": function() {
                    $(this).dialog("close");
                }
            }
        });
    }
}).call(request);