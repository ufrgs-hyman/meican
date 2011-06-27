function nextTab(elem){
    var $tabs = $('#tab_containers').tabs();
    if (elem.id == "bn1") {
	$('bn1').click(function() {
   		$tabs.tabs('select', 1);
                return false;
	});


    } else if (elem.id == "bn2") {
        $("#tab3").click();
    } else if (elem.id == "bn3") {
        $("#tab4").click();
    } else if (elem.id == "bn4") {
        $("#tab5").click();
    } 
}

function previousTab(elem){
    if (elem.id == "bp2") {
        $("#tab1").click();
    } else if (elem.id == "bp3") {
        $("#tab2").click();
    } else if (elem.id == "bp4") {
        $("#tab3").click();
    } else if (elem.id == "bp5") {
        $("#tab4").click();
    } 
}