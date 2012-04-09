var request = {};

(function() {
    var actionUrl = null;
    var response = null;
    var bandwidth = null;
    
    this.resizefn = function() {
        if ($('#res_mapCanvas'))
            $('#res_mapCanvas').css('width', $('#subtab-points').offset().left-4-$('#tabs-2').offset().left );
    };
    
    this.reply = function(response, availableBandwidth) {
        //var message = prompt();
        if (response=="accept"){
            $("#MessageImg").attr("src", baseUrl+"webroot/img/hand_good.png");
            $("#MessageLabel").html("Request will be accepted, please provide a message: ");
        }else{
            $("#MessageImg").attr("src", baseUrl+"webroot/img/hand_bad.png");
            $("#MessageLabel").html("Request will be rejected, please provide a message: ");
        }
        $("#Message").val('');
        $("#MessageBandwidth").html('Bandwidth available: '+availableBandwidth+' Mbps. <br/>Bandwidth requested: '+this.bandwidth+' Mbps.');
        this.response = response;
        $("#dialog-form").dialog("open");
    };

    this.setBandwidth = function(v){
        this.bandwidth = v;
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
                                response: request.response, 
                                message: message
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

$(function(){
   $(window).resize(request.resizefn);
   request.resizefn();
});