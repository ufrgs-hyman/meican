(function($){
    $(function(){
        if ((typeof(domains) != "undefined") && (domains.length > 0)) {
            refreshStatus();
            js_function_interval = setInterval("refreshStatus()", 60000);
        }
    });
})(jQuery);