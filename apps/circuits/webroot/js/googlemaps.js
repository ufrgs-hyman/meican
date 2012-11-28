window.google = window.google || {};
google.maps = google.maps || {};

(function() {
    function getScript(src) {
        document.write('<' + 'script src="' + src + '"' +
            ' type="text/javascript"><' + '/script>');
    }
  
    var modules = google.maps.modules = {};
    google.maps.__gjsload__ = function(name, text) {
        modules[name] = text;
    };

    if (typeof(MapsAPIKey) != "undefined")
        getScript("//maps.googleapis.com/maps/api/js?v=3.9&key=" + MapsAPIKey + "&sensor=false");
})();