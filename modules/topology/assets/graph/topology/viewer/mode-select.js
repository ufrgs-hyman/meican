$("#viewer-type-select").selectmenu({
    select: function( event, ui ) {
        setViewerType(ui.item.value);
    }
});

function setViewerType(value) {
    console.log(value);
}
