var request = {};

(function(startId) {
    var actionUrl = null;
    
	this.reply = function(response) {
        var message = prompt("Request will be marked as "+response+", please provide a message: ");
        if (message && message != "")
            $.navigate({
                type: "POST",
                url: this.actionUrl,
                data: {response: response, message: message}
            });
	};

	this.setActionUrl = function(url){
        this.actionUrl = url;
    }
}).call(request);