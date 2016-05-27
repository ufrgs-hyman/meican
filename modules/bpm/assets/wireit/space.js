// InputEx needs a correct path to this image
inputEx.spacerUrl = "lib/inputex/trunk/images/space.gif";
var editor

YAHOO.util.Event.onDOMReady( function() {
    editor = new WireIt.WiringEditor(workflowLanguage);

    // Open the infos panel
    editor.accordionView.openPanel(1);

});