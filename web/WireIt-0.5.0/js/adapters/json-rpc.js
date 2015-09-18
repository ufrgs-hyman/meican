/**
 * JsonRpc Adapter (using ajax)
 * @static 
 */
WireIt.WiringEditor.adapters.JsonRpc = {
	
	config: {
		url: '../../bpm/workflow/'
	},
	
	init: function() {
        YAHOO.util.Connect.setDefaultPostHeader('application/json');
    },
	
    saveWiring: function(val, callbacks) {
        this._sendJsonRpcRequest("save-workflow", val, callbacks);
    },
    
    updateWiring: function(id, val, callbacks) {
        this._sendJsonRpcRequestUpdate("update-workflow", id, val, callbacks);
    },
	
    deleteWiring: function(val, callbacks) {
        this._sendJsonRpcRequest("delete-wiring", val, callbacks);
    },
	
    listWirings: function(val, callbacks) {
        this._sendJsonRpcRequestLoad("load-workflow", val, callbacks);
    },
    
    readWirings: function(val, callbacks) {
        this._sendJsonRpcRequestView("view-workflow", val, callbacks);
    },
    
    //private method to send a json-rpc request for save and update using ajax
    _sendJsonRpcRequest: function(method, value, callbacks) {
    	var postData = YAHOO.lang.JSON.stringify({
    		"id":(this._requestId++),
    		"params":value,
    		"version":"json-rpc-2.0"});

    	$.ajax({
			type: "POST",
			url: this.config.url+method,
			data: "model=".concat(postData),
			cache: false,
			success: function(response) {
				r = YAHOO.lang.JSON.parse(response);
            	if(r['error']!=null){
            		window.parent.$("#message").html(r['error']);
            		window.parent.$("#dialog").dialog({
						buttons: [
							{
								text: "Ok",
							    click: function() {
							    	window.parent.$(this).dialog( "close" );
							    }
							},
						]
					});
            		window.parent.$("#dialog").dialog("open");
            	}
                callbacks.success.call(callbacks.scope, r.result);
                if(r['error']==null) window.top.location.href="../workflow/index";
			}
    	});

    },
    
    //private method to send a json-rpc request for save and update using ajax
    _sendJsonRpcRequestUpdate: function(method, id, value, callbacks) {
    	var postData = YAHOO.lang.JSON.stringify({
    		"id":(this._requestId++),
    		"params":value,
    		"version":"json-rpc-2.0"});
    	
    	$.ajax({
			type: "POST",
			url: this.config.url+method,
			data: "type=".concat("update").concat("&id=").concat(id).concat("&model=").concat(postData),
			cache: false,
			success: function(response) {
                r = YAHOO.lang.JSON.parse(response);
            	if(r['error']!=null){
            		window.parent.$("#message").html(r['error']);
            		window.parent.$("#dialog").dialog({
						buttons: [
							{
								text: "Ok",
							    click: function() {
							    	window.parent.$(this).dialog( "close" );
							    }
							},
						]
					});
            		window.parent.$("#dialog").dialog("open");
            	}
                callbacks.success.call(callbacks.scope, r.result);
                if(r['error']==null) window.top.location.href="../workflow/index";
			}
    	});
    },
    
  //private method to send a json-rpc request for view using ajax
    _sendJsonRpcRequestView: function(method, value, callbacks) {
    	$.ajax({
			type: "POST",
			url: this.config.url+method,
			data: "id=".concat(value),
			cache: false,
			success: function(response) {
				if(response==-1){
                	alert("Failure");
                	window.top.location.href="../workflow/index";
                }
                else callbacks.success.call(callbacks.scope, JSON.parse(response));
			}
    	});
    },

    //private method to send a json-rpc request for load using ajax
    _sendJsonRpcRequestLoad: function(method, value, callbacks) {
    	$.ajax({
			type: "POST",
			url: this.config.url+method,
			data: "id=".concat(value),
			cache: false,
			success: function(response) {
				if(response==-1){
                	alert("Failure");
                	window.top.location.href="../workflow/index";
                }
				else if(response==0){
                	alert("Only disabled Workflows can be edited.");
                	window.top.location.href="../workflow/index";
                }
                else callbacks.success.call(callbacks.scope, JSON.parse(response));
			}
    	});
    },
	
    _requestId: 1
};