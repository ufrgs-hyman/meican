var currentTab = "t1";
var tab1_valid = false;
var tab2_valid = true;
var previousTab;

var srcSet = false;
var dstSet = false;
var edit_markersArray = new Array();
var edit_selectedMarkers = new Array();
var view_markersArray = new Array();
var edit_bounds = new Array();
var edit_lines = new Array();
var view_bounds = new Array();
var view_lines = new Array();

var waypoints = new Array();
var waypointsMarkers = new Array();

var src_networks = null;
var dst_networks = null;
var src_urn = null;
var dst_urn = null;
var path = new Array();

var counter = 0;


var edit_map;
var view_map;
var view_center;
var overlay;
var mapDiv;
var contextMenu;
var editMapHandler;

var useView = false;

/*function createTabs(){
    $(".cont_tab").hide();                              //esconde todo conteudo

    $("ul.tabs li").click(function() {
        if ($("#t1").attr("class") != "ui-state-disabled") {
            clearFlash();
            //            if (($(this).attr("id") == "t3") && ($("#t3").attr("class") == "ui-state-disabled confirm")){
            //                return false;
            //            }
            if (($(this).attr("id") == "t3") && !validateReservationForm()) {
                return false;
            }
            previousTab = currentTab;
            currentTab = $(this).attr("id");
            $("ul.tabs li").removeClass("active");          //remove qualquer classe “active”
            $(this).addClass("active");                     //Adiciona a classe “active” na aba selecionada
            $(".cont_tab").hide();                          //esconde o conteudo de todas as abas
            var activeTab = $(this).find("a").attr("href"); //encontra o atributo href para identificar a aba ativa e seu conteudo

            if (currentTab == "t3") {
                // preenche o conteudo da aba 3
                fillConfirmationTab();
            }

            $(activeTab).fadeIn();                          //Mostra o conteudo da aba ativa gradualmente

            if (currentTab == "t3") {
                google.maps.event.trigger(view_map, 'resize');
                view_setBounds(view_bounds);
            }

            if (currentTab == "t1")
                google.maps.event.trigger(edit_map, 'resize');
        }
        return false;
    });
}*/

/*function createSlider(){
    
    $('#slider').slider("max",band_min);
    
    $('#slider').slider({
        value:(band_max)/5,
        min: band_min,
        max: band_max,
        step: band_div,
        slide: function( event, ui ) {
            $("#bandwidth").val(ui.value);
            $( "#amount" ).html( ui.value + " Mbps");
        }
    });
    $('#slider').removeClass("ui-widget");
    $('#slider').addClass("ui-widget-slider");
    $( "#amount" ).html( $( "#slider" ).slider( "value" ) + " Mbps");

    $("#slider").slider( "option", "disabled", true );
    $("#amount_label").hide();        
    $("#amount").hide();
}*/
/*
function showSlider() {
    var i=0;
    var j=0;
    var k=0;
    var l=0;
    band_max = domains[i].networks[j].devices[k].ports[l].max_capacity / 10000000;
    band_max = 1000;
    band_min = domains[i].networks[j].devices[k].ports[l].min_capacity / 1000000;
    band_div = domains[i].networks[j].devices[k].ports[l].granularity / 1000000;
    
//    $("#slider").slider("option", {
//        "max": band_max,
//        "min": band_min,
//        "step": band_div,
//        "disabled": false
//    });
    //$('#bandwidth').attr("min", band_min).attr("max", band_max).attr("step", band_div).attr('disabled', false).trigger('click').removeClass("ui-state-disabled");
    //$('#bandwidth').SpinnerControl({min: band_min, max: band_max, step:band_div});

    //    $("#slider").slider("min", band_min);
    //    $("#slider").slider("step", band_div);
    //    $("#slider").slider( "option", "disabled", false );
    
//    $("#div-bandwidth").slideDown();
//    $("#amount_label").show();        
//    $("#amount").show();
//}

function nextTab(elem){
    var activeTab;
    switch (elem.id) {
        case "bn1": {
            clearFlash();
            $("ul.tabs li").removeClass("active");                      //Desativa aba ativada
            $("ul.tabs li:eq(1)").addClass("active").show();            //Exibe aba selecionada    
            $(".cont_tab").hide();                                      //Esconde o conteúdo de todas as abas
            activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");   //Identifica ABA ativa pelo href
            $(activeTab).fadeIn();                                      //Exibe somente o conteudo da aba ativa
            previousTab = currentTab;
            currentTab = "t2";
            break;
        }
        case "bn2": {
            // clicou em next e vai para aba 3
            if ((tab1_valid) && (tab2_valid)) {
                clearFlash();
                $("ul.tabs li").removeClass("active");    
                $("ul.tabs li:eq(2)").addClass("active").show();            
                $(".cont_tab").hide();
                activeTab = $("ul.tabs li:eq(2)").find("a").attr("href");

                // antes de mostrar a aba, copia conteudo dos campos
                fillConfirmationTab();

                $(activeTab).fadeIn();  
                previousTab = currentTab;
                currentTab = "t3";
                google.maps.event.trigger(view_map, 'resize');
                view_setBounds(view_bounds);
                
            }                
            break;
        }
        default: {
            break;
        }
    }
}

function prevTab(elem){
    var activeTab;
    switch (elem.id){
        case "bp2": {
            $("ul.tabs li").removeClass("active");
            $("ul.tabs li:eq(0)").addClass("active");            
            $(".cont_tab").hide();
            activeTab = $("ul.tabs li:eq(0)").find("a").attr("href");
            $(activeTab).fadeIn();
            previousTab = currentTab;
            currentTab = "t1";                 
            google.maps.event.trigger(edit_map, 'resize');
            edit_setBounds(lines);
            break;    
        }
        case "bp3": {
            $("ul.tabs li").removeClass("active");
            $("ul.tabs li:eq(1)").addClass("active");            
            $(".cont_tab").hide();
            activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");
            $(activeTab).fadeIn();
            previousTab = currentTab;
            currentTab = "t2";        
            break;    
        }     
    } 
}

function nameError(){
    setFlash(flash_nameReq);
    $("ul.tabs li").removeClass("active");
    $("ul.tabs li:eq(0)").addClass("active");            
    $(".cont_tab").hide();
    activeTab = $("ul.tabs li:eq(0)").find("a").attr("href");
    $(activeTab).fadeIn();
    previousTab = currentTab;
    currentTab = "t1"; 
    return false;
}*/

function timerError(error){
    if (error) {
        setFlash(flash_timerReq);
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(3)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(3)").find("a").attr("href");
        $(activeTab).fadeIn();
        previousTab = currentTab;
        currentTab = "t4"; 
        return false;    
    } else
        return true;
}

function validateReservationForm() {
    if (!$('#res_name').val().length){
        setFlash(flash_nameReq);
        js_submit_form = false;
        $('#res_name').focus();
        return false;	
    }
    if (tab1_valid) {
        
        var hops = "";
        $.each($("#hops_line select"), function() {
            if (this.value != -1)
                hops += this.value + ";";
        });
        if (hops) {
            var path = src_urn + ";";
            path += hops;
            path += dst_urn;
            $("#path").val(path);
        } else {
            $("#path").val("");
        }

        if (tab2_valid) {
            return true;
        } else {
            if ($("#finalTime").val() < $("#initialtime").val()) {
                setFlash(flash_timerInvalid);
                js_submit_form = false;
                return false;
            } else if ($("#finalTime").val() == $("#initialtime").val()) {
                setFlash(flash_invalidDuration);
                js_submit_form = false;
                return false;
            } else {
                setFlash(flash_timerReq);
                js_submit_form = false;
                return false;
            }
        }
    } else {
        setFlash(flash_missingEndpoints);
        js_submit_form = false;
        return false;
    }
    return true;
}
/*
function changeName(elem){
    if (elem.value != "") {
        $("#t1").removeClass("ui-state-disabled");
        $("#t2").removeClass("ui-state-disabled");
        if ((tab1_valid) && (tab2_valid)) {
            $("#t3").removeClass("ui-state-disabled");
        }
        switch (currentTab) {
            case "t1": {
                $(".cont_tab:eq(0)").show(); 
                $("#t1").addClass("active");                          
                break;
            }
            case "t2": {
                $(".cont_tab:eq(1)").show();                        
                $("#t2").addClass("active");
                break;
            }
            case "t3": {
                $(".cont_tab:eq(2)").show();                       
                $("#t3").addClass("active");
                break;
            }
            default: {
                break;
            }
        }        
        
        $("#div-tabs").removeClass("inactive");
        $("#ul-tabs").removeClass("inactive");        
        
        google.maps.event.trigger(edit_map, 'resize');
        edit_setBounds(edit_bounds);        
        
        google.maps.event.trigger(view_map, 'resize');
        view_setBounds(view_bounds);
        
    } else if ((elem.value == "")) {
        $("#t1").addClass("ui-state-disabled");
        $("#t2").addClass("ui-state-disabled");
        if ((tab1_valid) && (tab2_valid)) {
            $("#t3").addClass("ui-state-disabled");        
        }
        switch (currentTab) {
            case "t1": {
                $(".cont_tab:eq(0)").hide();
                $("#t1").removeClass("active");
                break;
            }
            case "t2": {
                $(".cont_tab:eq(1)").hide();
                $("#t2").removeClass("active");
                break;
            }
            case "t3": {
                $(".cont_tab:eq(2)").hide();
                $("#t3").removeClass("active");
                break;
            }
            default: {
                break;
            }
        }    
        $("#div-tabs").addClass("inactive");
        $("#ul-tabs").addClass("inactive");        
    }
}*/

function validateTab1() {
    if ( (path.length == 2) && ($("#src_device").val() != -1) && ($("#dst_device").val() != -1) &&
        ($("#src_port").val() != -1) && ($("#dst_port").val() != -1) ) {
        
        tab1_valid = true;
        $.each($("#hops_line select"), function() {
            if (this.value == -1) {
                tab1_valid &= false;
            }
        });
        if (!tab1_valid)
            return;
        
        if ($("#showVlan_checkbox").attr("checked")) {

            if ($("#src_vlanUntagged").attr("checked"))
                tab1_valid = true;
            else if ($("#src_vlanTagged").attr("checked")) {
                if (checkVLAN("src"))
                    tab1_valid = true;
                else {
                    setFlash(flash_srcVlanInv, "warning");
                    tab1_valid = false;
                    return;
                }
            }
            //            else {
            //                setFlash(flash_srcVlanReq, "warning");
            //                tab1_valid = false;
            //                return;
            //            }
        
            if ($("#dst_vlanUntagged").attr("checked"))
                tab1_valid = true;
            else if ($("#dst_vlanTagged").attr("checked")) {
                if (checkVLAN("dst"))
                    tab1_valid = true;
                else {
                    setFlash(flash_dstVlanInv, "warning");
                    tab1_valid = false;
                    return;
                }
            }
            //            else {
            //                setFlash(flash_dstVlanReq, "warning");
            //                tab1_valid = false;
            //                return;
            //            }
            
            //            if (($('input[name="sourceVLANType"]:checked').val() == "TRUE") && ($("#src_vlanText").val() == "")) {
            //                tab1_valid = false;
            //            } else if (($('input[name="destVLANType"]:checked').val() == "TRUE") && ($("#dst_vlanText").val() == "")) {
            //                tab1_valid = false;
            //            } else {
            //                tab1_valid = true;
            //            }
        } else {
            tab1_valid = true;
        }
    } else {
        tab1_valid = false;
    }
    validateTab3();
}

function validateTab3() {
    if ((tab2_valid) && (tab1_valid)) {
        $("#t3").removeClass("ui-state-disabled");
        $("#bn2").removeClass("ui-state-disabled")
    }                
    else {
        
        $("#t3").addClass("ui-state-disabled");        
        $("#bn2").addClass("ui-state-disabled")
    }        
}

function cancelRecurrence(){
    if ($("#repeat_chkbox").attr("checked") && ($("#recurrence-edit").is(":visible"))) {
        $("#recurrence").hide(); 
    } else {
        $("#recurrence").hide(); 
        $("#repeat_chkbox").removeAttr("checked");
        $('#recurrence_summary').empty();
        $('#summary_input').val("");
        refreshSummary();
    }
}

function saveRecurrence(){

    $("#recurrence-flash").hide();
    $("#recurrence-warning").html("");
    
    var freq = $('input[name="freq"]:checked').val();

    if ($("#date_radio").attr("checked")) {
        var until = $("#untilDate").val();
    } else if ($("#recur_radio").attr("checked"))
        var count = $("#nr_occurr").val();
    else {
        $("#recurrence-flash").show();        
        $("#recurrence-warning").html(end_rule_string);        
        return;
    }

    var interval = $("#interval").val();

    var week_str = "";
    
    if (freq == "WEEKLY") {
        var byday = getCheckedDays();
        if (byday.length == 0) {
            $("#recurrence-flash").show();
            $("#recurrence-warning").html(select_day_string);                    
            return;
        }
        byday = byday.toString();
        var weekdays = ["#Sunday_desc","#Monday_desc","#Tuesday_desc","#Wednesday_desc","#Thursday_desc","#Friday_desc","#Saturday_desc"];

        for (var i in weekdays) {            
            if ($(weekdays[i]).html()){                      
                week_str += $(weekdays[i]).html() + " ";
            }
        }
    }
    
    
    var sum_desc = $("#short_desc").html() + " ";
    sum_desc += week_str;
    sum_desc += $("#until_desc").html();
    $("#recurrence_summary").html(sum_desc);
    $("#summary_input").val(sum_desc);
    $("#confirmation_summary").html(sum_desc);
    $("#summary").html("");
    $("#recurrence").hide();
    $("#recurrence-edit").show();
    
}
/*
$.fn.extend({
    slideRight: function() {
        return this.each(function() {
            $(this).animate({
                width: 'show'
            });
        });
    },
    slideLeft: function() {
        return this.each(function() {
            $(this).animate({
                width: 'hide'
            });
        });
    },
    slideToggleWidth: function() {
        return this.each(function() {
            var el = $(this);
            if (el.css('display') == 'none') {
                el.slideRight();                
            } else {
                el.slideLeft();
            }
        });
    }
});

function showVlanConf() {
    if ($("#showVlan_checkbox").attr("checked")) {
        $("#div_vlan").slideDown();
    }else {
        $("#div_vlan").slideUp();
    }
    validateTab1();
}*/

function genHex(domainId) {
    var firstColor = "3a5879";
    if (domainId == 0) {
        return firstColor;
    } else {
        var color = parseInt(firstColor,16);
        color += (domainId * parseInt("d19510", 16));
        if ((color == "eee") && (color == "eeeeee")) {
            color = "dddddd";
            color = color.toString(16);
        } else if (color > 0xFFFFFF) {
            color = color.toString(16);
            color = color.substring(1, color.length);
        } else {
            color = color.toString(16);
        }
        return color;            
    }
}
/*
function moreFields() {
    counter++;
    var newFields = document.getElementById('addHops').cloneNode(true);
    newFields.id = '';
    newFields.style.display = 'block';
    var newField = newFields.childNodes;
    for (var i=0;i<newField.length;i++) {
        var theId = newField[i].id;
        if (theId) {
            newField[i].id = theId + counter;
        }
    }
    var insertHere = document.getElementById('writeHops');
    insertHere.parentNode.insertBefore(newFields,insertHere);
    var selectId = "#selectHops" + counter;
    fillUrnBox(selectId, urn_string);
}

function lessFields(elem) {
    elem.parentNode.parentNode.removeChild(elem.parentNode);
    edit_mapPlaceDevice();
}*/

function fillUrnBox(htmlId, fillerArray, current_val) {
    clearSelectBox(htmlId);
    for (var i=0; i < fillerArray.length; i++) {
        if (fillerArray[i] == current_val)
            $(htmlId).append('<option selected="true" value="' + fillerArray[i] + '">' + fillerArray[i] + '</option>');
        else
            $(htmlId).append('<option value="' + fillerArray[i] + '">' + fillerArray[i] + '</option>');
    }
}

function fillConfirmationTab() {
    // preenche informacões dos endpoints
    $("#confirmation_src_domain").html($("#src_domain").html());
    $("#confirmation_src_network").html($("#src_network").html());
    $("#confirmation_src_device ").html($("#src_device option:selected").html());
    $("#confirmation_src_port").html($("#src_port option:selected").html());

    $("#confirmation_dst_domain").html($("#dst_domain").html());
    $("#confirmation_dst_network").html($("#dst_network").html());
    $("#confirmation_dst_device").html($("#dst_device option:selected").html());
    $("#confirmation_dst_port").html($("#dst_port option:selected").html());

    // preenche info das VLANs
    if ($("#showVlan_checkbox").attr("checked")) {

        if ($("#src_vlanUntagged").attr("checked"))
            $("#confirmation_src_vlan").html("Untagged");

        else if ($("#src_vlanTagged").attr("checked")) {
            if ($("#src_vlanText").val()) {
                $("#confirmation_src_vlan").html("Tagged: " + $("#src_vlanText").val());
            } else {
                $("#confirmation_src_vlan").html("Tagged: " + any_string);
            }
        }

        if ($("#dst_vlanUntagged").attr("checked"))
            $("#confirmation_dst_vlan").html("Untagged");
            
        else if ($("#dst_vlanTagged").attr("checked")) {
            if ($("#dst_vlanText").val()) {
                $("#confirmation_dst_vlan").html("Tagged: " + $("#dst_vlanText").val());
            } else {
                $("#confirmation_dst_vlan").html("Tagged: " + any_string);
            }
        }

    } else {
        $("#confirmation_src_vlan").html("Tagged: " + any_string);
        $("#confirmation_dst_vlan").html("Tagged: " + any_string);
    }

    // preenche info da banda
    var value = $("#bandwidth").val();
    $("#lb_bandwidth").html(value + " Mbps");

    // preenche informacões do timer
    $("#summary_input").val($("#confirmation_summary").html());
}


/*----------------------------------------------------------------------------*/
// INICIO DAS FUNÇÕES DO MAPcounterA                                                 //
//                                 
//
//  PREFIXO "edit_" INDICA USO DO SCRIPT NA TAB "Endpoints & Bandwidth"       //
//  PREFIXO "view_" INDICA USO DO SCRIPT NA TAB "Confirmation"                //
/*----------------------------------------------------------------------------*/

// EDIT FUNCTIONS

//inicializa mapa com redes marcadas para a definicao dos endpoints
function edit_initializeMap() {
    contextMenu.hide();
    
    for (var i in domains) {
        color = genHex(i);
        for (var j in domains[i].networks) {            
            if (domains[i].networks[j].latitude) {
                for (var k=0; k<i; k++){
                    for (var l in domains[k].networks) {
                        if (domains[k].networks[l].latitude) {
                            if ((domains[i].networks[j].latitude == domains[k].networks[l].latitude) &&
                                (domains[i].networks[j].longitude == domains[k].networks[l].longitude)) {
                                domains[i].networks[j].longitude -= -0.015;
                            }
                        }
                    }
                }
                var coord = new google.maps.LatLng(domains[i].networks[j].latitude, domains[i].networks[j].longitude);                
                edit_addMapMarker(coord, domains[i].id, domains[i].name, domains[i].networks[j].id, domains[i].networks[j].name, color);
                edit_bounds.push(coord);
            }
        }
    }
    toggleCluster(true, edit_markersArray);
    
    google.maps.event.addListener(edit_map, 'click', function() {
        contextMenu.hide();        
    }); 
    
    if ( !(dstSet) && !(srcSet)) {       
        edit_setBounds(edit_bounds);
    }
}

//adiciona marcadores de endpoints no mapa 
function edit_addMapMarker(coord, domain_id, domain_name, network_id, network_name, color) {
    for (i in edit_markersArray){
        var mark = edit_markersArray[i];
        if (
            (mark.domain_id == domain_id) &&
            (mark.id == network_id))
            return ;
    } //não re adiciona marcador
    var marker = new StyledMarker({
        domain_id: domain_id,
        domain_name: domain_name,
        id: network_id,
        label: network_name,
        position: coord,
        styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
            color:color
        }),
        map:edit_map
    });

    var clickFn = function() {
        
        contextMenu.find('a').click( function() {
            // fade out the menu
            contextMenu.fadeOut(75);

            // The link's href minus the #
            var action = $(this).attr('href').substr(1);
            switch ( action )
            {
                case 'fromHere':
                    edit_markerClick(coord, domain_id, domain_name, network_id, network_name, "src", function(){
                        edit_initializeMap()
                    });
                    break;
                case 'toHere':
                    edit_markerClick(coord, domain_id, domain_name, network_id, network_name, "dst", function(){
                        edit_initializeMap()
                    });
                    break;
            }
            return false;
        });
    
        var pos = overlay.getProjection().fromLatLngToContainerPixel(coord),
        x = pos.x,
        y = pos.y;
            
        // save the clicked location

        // adjust if clicked to close to the edge of the map
        if (x > mapDiv.width() - contextMenu.width())
            x -= contextMenu.width();
        
        if (y > mapDiv.height() - contextMenu.height())
            y -= contextMenu.height();

        // Set the location and fade in the context menu
        contextMenu.css({
            top: y,
            left: x
        }).fadeIn(100);         
    };
    google.maps.event.addListener(marker, "click", clickFn);
    google.maps.event.addListener(marker, 'rightclick', clickFn);    
    
    var infowindow = new google.maps.InfoWindow({
        content:    "<b>" + domain_string + "</b>: " + domain_name + "<br/>" +
            "<b>" + network_string + "</b>: " + network_name,
        disableAutoPan: true
    });

    google.maps.event.addListener(marker, "mouseover", function() {
        infowindow.open(edit_map, marker);
    });
  
    google.maps.event.addListener(marker, "mouseout", function() {
        infowindow.close(edit_map);
    });
    
    edit_markersArray.push(marker);
    marker.setMap(edit_map);
}

//funcao que gerencia os "clicks" nos marcadores
function edit_markerClick(coord, domain_id, domain_name, network_id, network_name, where, callback_initializeMap){
    contextMenu.hide();     

    $("#"+where+"_domain").html(domain_name);
    $("#"+where+"_network").html(network_name);
    map_changeNetwork(where, network_id, domain_id);       
    var pathPoint = {
        domain_id: domain_id,
        domain_name: domain_name,
        network_id: network_id,
        network_name: network_name,
        position: coord,
        color: "eee"
    };        
    
    if (where == "src") {
        srcSet = true;
        path[0] = pathPoint;
    } else if (where == "dst") {
        dstSet = true;
        path[1] = pathPoint;
    }
    
    if (callback_initializeMap) {
        callback_initializeMap();
    }
    
    $.fn.mapEdit.prepareContextMenu();
    
    if ((srcSet) && !(dstSet)) {
        //        for (var i=0; i<edit_markersArray.length; i++) {
        //            if ((edit_markersArray[i].id == path[0].network_id) && (edit_markersArray[i].domain_id == path[0].domain_id)) {
        //                edit_markersArray[i].setMap(null);
        //            }
        //        }
        edit_clearSelectedMarkers();
        edit_addSelectedMarker(path[0].position, path[0].domain_id, path[0].domain_name, path[0].network_id, path[0].network_name, "src");
    } else if (!(srcSet) && (dstSet)){
        //        for (var i=0; i<edit_markersArray.length; i++) {
        //            if ((edit_markersArray[i].id == path[1].network_id) && (edit_markersArray[i].domain_id == path[1].domain_id)) {
        //                edit_markersArray[i].setMap(null);
        //            }
        //        }
        edit_clearSelectedMarkers();
        edit_addSelectedMarker(path[1].position, path[1].domain_id, path[1].domain_name, path[1].network_id, path[1].network_name, "dst");
    }
    
    if (path.length == 2) { 
        $("#showVlan_checkbox").removeAttr("disabled");
        edit_clearLines();
        edit_drawPath(new Array(path[0].position, path[1].position));
        $('#bandwidth').attr("min", band_min).attr("max", band_max).attr("step", band_div).trigger('click').spinner("enable").disabled(false);   
        $('#bandwidth_un').disabled(false);
        window.scroll(0, 650);
    } 
    
    if (tab2_valid) {
        $("#t3").removeClass("ui-state-disabled");
    } 
    validateTab1();     
}

function edit_addSelectedMarker(coord, domain_id, domain_name, network_id, network_name, where) {
    var color;
    
    if (where == "src") {
        color = "0000EE";
    } else if (where == "dst") {
        color = "FF0000";
    }
    
    var selectedMarker = new StyledMarker({
        domain_id: domain_id,
        domain_name: domain_name,
        id: network_id,
        label: network_name,
        position: coord,
        clickable: false,
        styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
            color:color
        }),
        map:edit_map
    });
    
    edit_selectedMarkers.push(selectedMarker);
    selectedMarker.setMap(edit_map);
}

function callback_markers() {
    if (srcSet) {
        edit_addSelectedMarker(path[0].position, path[0].domain_id, path[0].domain_name, path[0].network_id, path[0].network_name, "src");
    }
    if (dstSet) {
        edit_addSelectedMarker(path[1].position, path[1].domain_id, path[1].domain_name, path[1].network_id, path[1].network_name, "dst");
    }
    
    for (var i=0; i<edit_markersArray.length; i++) {
        if ( ((edit_markersArray[i].id == path[0].network_id) && (edit_markersArray[i].domain_id == path[0].domain_id)) ||
            ((edit_markersArray[i].id == path[1].network_id) && (edit_markersArray[i].domain_id == path[1].domain_id)) ) {
            edit_markersArray[i].setMap(null);
        } 
    }
}

// desenha uma linha entre dois endpoints selecionados
function edit_drawPath(flightPlanCoordinates) {

    edit_clearSelectedMarkers(function(){
        callback_markers();
    });    


    var line = new google.maps.Polyline({
        path: flightPlanCoordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });
    line.setMap(edit_map);
    edit_lines.push(line);
    toggleCluster(true,edit_markersArray);
    //toggleCluster(false, edit_selectedMarkers);
    if ( flightPlanCoordinates[0] != flightPlanCoordinates[1] ) {
        edit_setBounds(flightPlanCoordinates);  
    }
    
    if (useView) {
        view_clearAll();
        view_Circuit();
    }
}

// reseta o mapa ao estado original e desabilita o slider
function edit_clearAll(){
    srcSet = false;
    dstSet = false;
    $("#slider").slider( "option", "disabled", true );
    $("#bandwidth").spinner("disabled");
    $('#bandwidth_un').disabled();
    $("#amount_label").hide();
    $("#amount").hide();  
    $("#src_domain").empty();
    $("#dst_domain").empty();
    $("#src_network").empty();
    $("#dst_network").empty();
    
    if (path.length != 0) {
        $("#src_device").empty();
        $("#src_device").slideUp();
        $("#dst_device").empty();
        $("#dst_device").slideUp();
        $("#src_port").empty();
        $("#src_port").slideUp();
        $("#dst_port").empty();
        $("#dst_port").slideUp();
        for (var i=counter; i>0; i--) {
            alert(i);
            var removeHop = "#removeHop" + counter;
            if ($(removeHop)) {
                lessFields($(removeHop));
            }
        }
        counter = 0;
    }
    
    edit_clearLines();
    edit_clearSelectedMarkers();
    edit_clearMarkers();
    path = [];
    edit_clearTopologyMarkers();
    edit_setBounds(edit_bounds);    
    
    $.fn.mapEdit.prepareContextMenu();
    
    view_clearAll();    
    
    validateTab1();
    if (tab2_valid) {
        $("#t3").addClass("ui-state-disabled");
    }
    $("#showVlan_checkbox").attr("disabled", "disabled");
    edit_initializeMap();
}

//limpa as linhas do mapa de edicao
function edit_clearLines() {
    for (var i = 0; i < edit_lines.length; i++) {
        edit_lines[i].setMap(null);
    }  
}

//limpa os marcadores do mapa de edicao
function edit_clearMarkers() {
    for (var i=0; i< edit_markersArray.length; i++){
        edit_markersArray[i].setMap(null);
    }
}

function edit_clearSelectedMarkers(callback) {
    for (var i=0; i< edit_selectedMarkers.length; i++){
        edit_selectedMarkers[i].setMap(null);
    } 
    if (callback) {
        callback();
    }
}

// seta os limites do mapa para enquadrar os marcadores ou as rotas traçadas
function edit_setBounds(flightPlanCoordinates){
    polylineBounds = new google.maps.LatLngBounds();

    for (i=0; i<flightPlanCoordinates.length; i++) {
        polylineBounds.extend(flightPlanCoordinates[i]);
    }
    edit_map.fitBounds(polylineBounds);
    edit_map.setCenter(polylineBounds.getCenter());
}

function edit_resetZoom() {
    edit_setBounds(edit_bounds);
}

function decodeUrn(urn) {
    
    var string_aux = "domain=";
    var domainTopology = urn.substring((urn.indexOf("domain=") + string_aux.length), urn.indexOf(":node="));    
    string_aux = ":node=";
    var deviceTopology = urn.substring((urn.indexOf(":node=") + string_aux.length), urn.indexOf(":port="));


    for (var i in domains) {
        if (domains[i].topology_id == domainTopology) {
            for (var j in domains[i].networks) {
                for (var k in domains[i].networks[j].devices) {                    
                    if (domains[i].networks[j].devices[k].topology_node_id == deviceTopology) {
                        var waypoint = ({
                            location: new google.maps.LatLng(domains[i].networks[j].latitude, domains[i].networks[j].longitude),
                            domain_id: domains[i].id,
                            domain_name: domains[i].name,
                            network_id: domains[i].networks[j].id, 
                            network_name: domains[i].networks[j].name, 
                            device_id: domains[i].networks[j].devices[k].id,
                            device_name: domains[i].networks[j].devices[k].name + " " + domains[i].networks[j].devices[k].model
                        });
                    }
                }
            }
        }
    }
    return waypoint;
}

function edit_mapPlaceDevice() {

    edit_clearLines();
    //edit_clearTopologyMarkers();

    for (i=1; i<=counter; i++) {
        var selectId = "#selectHops" + counter;
        if ($(selectId).val()) {
            if ($(selectId).val() != -1) {
                var waypoint = decodeUrn($(selectId).val())
                edit_addTopologyMarker(waypoint);
            }            
        }

    }
    edit_redrawPath();
}

function edit_addTopologyMarker(waypoint) {
    
    marker = new google.maps.Marker({
        id : waypoint.device_id,
        position: waypoint.location,
        
        map:edit_map
    });

    google.maps.event.addListener(marker, "mouseover", function(marker) {

        infowindow = new google.maps.InfoWindow({
            content: "<b>" + domain_string + "</b>: " + waypoint.domain_name + "<br/>" +
                "<b>" + network_string + "</b>: " + waypoint.network_name + "<br/>" +
                "<b>" + device_string + "</b>: " + waypoint.device_name,
            disableAutoPan: true
        });
        infowindow.open(edit_map, marker);
    });
  
    google.maps.event.addListener(marker, "mouseout", function() {
        infowindow.close(edit_map);
    });
  
    // Display and position the menu    
    waypointsMarkers.push(marker);
    marker.setMap(edit_map);
}

function edit_redrawPath() {

    for (var i=1; i<=counter; i++) {
        var selectId = "#selectHops" + counter;
        if ($(selectId).val()) {
            if ($(selectId).val() != -1) {
                var waypoint = decodeUrn($(selectId).val());
                waypoints.push(waypoint.location);
            }
        }
            
    }
    
    var flightPlanCoordinates = new Array();
    
    flightPlanCoordinates[0] = path[0].position;
    
    for(i=0; i<waypoints.length; i++) {
        flightPlanCoordinates[i+1] = waypoints[i];
    }
    
    var length = flightPlanCoordinates.length;
    
    flightPlanCoordinates[length] = path[1].position;

    var line = new google.maps.Polyline({
        path: flightPlanCoordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });
    line.setMap(edit_map);
    edit_lines.push(line);
    edit_setBounds(flightPlanCoordinates);  
    view_Circuit();    
}

function edit_clearTopologyMarkers() {
    if (waypointsMarkers.length > 0) {
        for (var i=0; i< waypointsMarkers.length; i++){
            waypointsMarkers[i].setMap(null);
        }
    }
}

function toggleCluster(toggle, arrayMarkers){
    //
    //if (toggle) {
    //        markerCluster = new MarkerClusterer(edit_map, arrayMarkers);      
    //        google.maps.event.addListener(markerCluster, 'clustermouseover',function(markerCluster) {
    //                var stringInfo = "<h4>&nbsp;&nbsp;" + cluster_information_string + "</h4>&nbsp;&nbsp;";
    //                stringInfo += " <b>" + networks_string + "</b>: <br>&nbsp;&nbsp;";
    //                clusterContent = markerCluster.getMarkers();
    //                selectedMarker = new StyledMarker({
    //                    domain_id: clusterContent[0].domain_id,
    //                    domain_name: clusterContent[0].domain_name,
    //                    id: clusterContent[0].network_id,
    //                    label: clusterContent[0].label,
    //                    position: clusterContent[0].position,
    //                    styleIcon: new StyledIcon(StyledIconTypes.MARKER,{
    //                        color:clusterContent[0].styleIcon.color
    //                    }),
    //                    map:edit_map
    //                });
    //                for (var i=0; i<clusterContent.length;i++){
    //                        stringInfo+= " " + clusterContent[i].label +"&nbsp;&nbsp;";
    //                        stringInfo+= " (" + clusterContent[i].domain_name +")<br>&nbsp;&nbsp;";
    //                }
    //
    //                selectedMarker.setMap(null);
    //                infowindow = new google.maps.InfoWindow({
    //                    content: stringInfo,
    //                    disableAutoPan: true
    //                });
    //                infowindow.open(edit_map, selectedMarker);
    //
    //        });
    //        google.maps.event.addListener(markerCluster, 'clustermouseout',function() {
    //                infowindow.close(edit_map);
    //        });
    //        google.maps.event.addListener(markerCluster, 'clusterclick',function() {
    //                if (infowindow) {
    //                    infowindow.close(edit_map);
    //                }
    //        });
    //    } else {
    //        markerCluster.clearMarkers(arrayMarkers);
    //    }
}

// VIEW FUNCTIONS

// alterna entre a visão simples e a visão avançada no mapa
function view_toggleTopology(){
    //    clearAll();
    //    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
    //        var aux = parseFloat(dst_lng_network);
    //        aux += 0.0005;
    //        dst_lng_network = aux.toString();
    //    }
    //    var coordinatesArray=[];
    //
    //    var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
    //    edit_addMarker(coord_src);
    //    bounds.push(coord_src);
    //
    //    var waypoint = new google.maps.LatLng(-18,-54);    
    //    bounds.push(waypoint);
    //
    //    var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
    //    edit_addMarker(coord_dst);
    //    bounds.push(coord_dst);
    //
    //    coordinatesArray.push(coord_src);
    //    coordinatesArray.push(waypoint);
    //    coordinatesArray.push(coord_dst);
    //
    //    if (topology) {
    //        topology = false;
    //        edit_addMarker(waypoint);
    //        drawTopology(coordinatesArray);
    //    } else {
    //        topology = true;
    //        edit_drawPath(coordinatesArray);
    //    }
    //    edit_setBounds(bounds);
}

// inicializa o mapa para visualizacao do circuito
function view_Circuit(){
    var coord_src = path[0].position;
    view_addMarker(coord_src, "src");
    view_bounds.push(coord_src);
    var coord_dst = path[1].position;
    view_addMarker(coord_dst, "dst");
    view_bounds.push(coord_dst);
    view_setBounds(view_bounds);
    var sourceDest = new Array();
    sourceDest.push(path[0].position);
    sourceDest.push(path[1].position);
    view_drawPath(sourceDest);
}

// adiciona marcadores no mapa para visualizacao do circuito
function view_addMarker(location, where) {
    var color;
    
    if (where == "src") {
        color = "0000EE";
    } else if (where == "dst") {
        color = "FF0000";
    }
    
    marker = new StyledMarker({
        position: location,
        styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
            color:color
        }),
        map:view_map
    });    

    view_markersArray.push(marker);
    marker.setMap(view_map);
}

// desenha linha entre endpoints para visualizacao do circuito
function view_drawPath(flightPlanCoordinates) {
    var line = new google.maps.Polyline({
        path: flightPlanCoordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });
    line.setMap(view_map);
    view_lines.push(line);
    view_setBounds(flightPlanCoordinates);        
}

// deseha topologia para a visao avancada
function view_drawTopology(coordinatesArray){
    var flightPlanCoordinates = [];
    for (var i=0; i<coordinatesArray.length; i++){
        flightPlanCoordinates.push(coordinatesArray[i]);
    }
    var line = new google.maps.Polyline({
        path: flightPlanCoordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });

    line.setMap(view_map);
    view_lines.push(line);
}

// limpa as linhas do mapa da visualizacao
function view_clearLines(){
    for (var i = 0; i < view_lines.length; i++) {
        view_lines[i].setMap(null);
    }    
}

// limpa os marcadores do mapa de visualizacao
function view_clearMarkers(){
    var j = view_markersArray.length;
    
    for (var i=0; i < j; i++){
        view_markersArray[i].setMap(null);        
    }
    for (i=j; i>0; i--) {
        view_markersArray.pop();
    }
    
}

//reseta os limites originais do mapa
function view_clearBounds(){
    var j = view_bounds.length;
    for (var i=j; i>0; i--){
        view_bounds.pop();        
    }
    view_setBounds(view_bounds);    
}

// reseta o mapa ao estado original
function view_clearAll(){
    view_clearMarkers();
    view_clearLines();
    view_clearBounds();       
}

function view_setBounds(flightPlanCoordinates){
    polylineBounds = new google.maps.LatLngBounds();

    for (i=0; i<flightPlanCoordinates.length; i++) {
        polylineBounds.extend(flightPlanCoordinates[i]);
    }
    view_map.fitBounds(polylineBounds);
    view_map.setCenter(polylineBounds.getCenter());
}



/*----------------------------------------------------------------------------*/
// INICIO DAS FUNÇÕES AVANÇADAS DE FLUXO                                      //
//                                                                            //
//                                                                            //
//                                                                            //
/*----------------------------------------------------------------------------*/



function map_changeNetwork(where, network_id, domain_id) {
    var network = "#" + where + "_network";
    var device_id = "#" + where + "_device";
    var port_id = "#" + where + "_port";

    $(device_id).disabled(false);
    //$("#" + where + "_vlanTagged").disabled(false);

    map_clearVlanConf(where);
    clearSelectBox(device_id);

    if ($(network).html() != "") {
        var devices = [];
        for (var i=0; i<domains.length; i++){
            for (var j=0; j<domains[i].networks.length; j++){
                for (var k=0; k<domains[i].networks[j].devices.length; k++){
                    if ((domains[i].id == domain_id) && (domains[i].networks[j].id == network_id)) {
                        devices.push(domains[i].networks[j].devices[k]);
                    }
                }
            }
        }
        fillSelectBox(device_id, devices);
        $(device_id).slideDown(); //TODO: pq slideDown aqui?
    }
}

function map_changeDevice(where) {
    var domain_id = "#" + where + "_domain";
    var network_id = "#" + where + "_network";
    var device_id    = "#" + where + "_device";
    var port_id = "#" + where + "_port";

    $(port_id).disabled(false);
    //$(port_id).slideUp();
    map_clearVlanConf(where);
    clearSelectBox(port_id);
    
    if ($(device_id).val() != -1) {
        var ports = map_getPorts($(domain_id).html() ,$(network_id).html(), $(device_id).val(), where);
        map_fillPorts(port_id, ports);

        if (ports.length == 1) {
            map_setEndpointConf(where);
        }        
        $(port_id).slideDown();
    } else
        tab1_valid = false;
    validateTab3();
}

function map_getPorts(domain_id, network_id, device_id, where) {
    var devices = map_getDevices(domain_id, network_id);
    var ports = null;
    if (devices) {
        for (var i=0; i<devices.length; i++) {
            if (devices[i].id == device_id) {
                var confirmation_device = "#confirmation_" + where + "_device";                
                $(confirmation_device).html(devices[i].name);
                ports = devices[i].ports;
                break;
            }
        }
    }
    return ports;
}

function map_getDevices(domain_id, network_id) {
    var devices = null;
    for (var i=0; i<domains.length; i++) {
        if (domains[i].name == domain_id){
            for (var j=0; j<domains[i].networks.length; j++) {
                if (domains[i].networks[j].name == network_id) {
                    devices = domains[i].networks[j].devices;
                    break;
                }
            }
        }
    }
    return devices;
}

function map_changePort(where) {
    var port_id = "#" + where + "_port";
    map_clearVlanConf(where);
    if ($(port_id).val() != -1) {
        map_setEndpointConf(where);
    } else
        tab1_valid = false
    validateTab3();
}

function map_clearVlanConf(where) {
    // var untagged_htmlId = "#" + where + "_vlanUntagged";
    var tagged_htmlId = "#" + where + "_vlanTagged";
    var text_htmlId = "#" + where + "_vlanText";
    var tip_htmlId = "#" + where + "_vlanTip";

    $(tip_htmlId).html("");
    $(text_htmlId).attr('title', '').val("").disabled().next().html("").disabled();

    /*$(untagged_htmlId).removeAttr('checked');
    $(untagged_htmlId).attr('disabled','disabled');*/

    //$(tagged_htmlId).removeAttr('checked');
    $(tagged_htmlId).disabled();

    if (where == "src") {
        src_urn = null;
        src_vlan_min = null;
        src_vlan_max = null;
        src_vlan_validValues = null;
    } else if (where == "dst") {
        dst_urn = null;
        dst_vlan_min = null;
        dst_vlan_max = null;
        dst_vlan_validValues = null;
    }
}

function map_fillPorts(htmlId, portsArray, current_port) {
    clearSelectBox(htmlId);
    for (var i=0; i < portsArray.length; i++) {
        if ((portsArray[i].port_number == current_port) || (portsArray.length == 1))
            $(htmlId).append('<option selected="true" value="' + portsArray[i].port_number + '">' + portsArray[i].port_number + '</option>');
        else
            $(htmlId).append('<option value="' + portsArray[i].port_number + '">' + portsArray[i].port_number + '</option>');
    }
}

function map_setEndpointConf(where) {

    /*var untagged_htmlId = "#" + where + "_vlanUntagged";*/
    var tagged_htmlId = "#" + where + "_vlanTagged";
    var text_htmlId = "#" + where + "_vlanText";
    var tip_htmlId = "#" + where + "_vlanTip";

    var urnData = map_getUrnData(where);

    var temp = new Array();
    var virgula = urnData.vlan.search(",");
    var range = urnData.vlan.search("-");

    var allowTag = true;
    var allowUntag = true;

    var vlan_min = null;
    var vlan_max = null;
    var vlan_validValues = null;

    if (virgula != -1) {
        temp = urnData.vlan.split(",");
        if (range != -1) {
            // possui virgula e range. Ex: "0,3000-3500"
            if (temp[0] != 0)
                allowUntag = false;
            temp = temp[1].split("-");
            vlan_min = temp[0];
            vlan_max = temp[1];
            $(text_htmlId).val(vlan_min);
        } else {
            // possui virgula, mas nao possui range. Ex: "3000,3001,3002" ou "0,3000,3001,3002"
            if (temp[0] != 0) {
                allowUntag = false;
                vlan_validValues = urnData.vlan;
            } else
                vlan_validValues = urnData.vlan.substr(2);
        }
    } else {
        if (range != -1) {
            // nao possui virgula, mas possui range. Ex: "3000-3500"
            temp = urnData.vlan.split("-");
            vlan_min = temp[0];
            vlan_max = temp[1];
            allowUntag = false;
            $(text_htmlId).val(vlan_min);
        } else {
            // nao possui virgula nem range. Ex: "0" ou "3000"
            vlan_validValues = urnData.vlan;
            if (vlan_validValues == 0) {
                allowTag = false;
            } else {
                // um valor só para VLAN
                $(text_htmlId).val(vlan_validValues);
                allowUntag = false;
            }
        }
    }

    if (allowTag) {
        // pode ser tagged
        $(tagged_htmlId).disabled(false);

        if (vlan_min && vlan_max){
            $(text_htmlId).attr('title', vlan_min + ' - ' + vlan_max).next().html("("+vlan_min + ' - ' + vlan_max+")");
            //$(tip_htmlId).html(value_string + ': ' + vlan_min + ' - ' + vlan_max);
        } else if (vlan_validValues) {
            $(text_htmlId).attr('title', vlan_validValues).next().html("("+vlan_validValues+")");
            //$(tip_htmlId).html(value_string + ': ' + vlan_validValues);
        }

        if (allowUntag) { //TODO: verificar isso
            // pode ser untagged também
            /*$(untagged_htmlId).removeAttr('disabled');
            $(untagged_htmlId).attr('checked','yes');*/
        } else {
            $(tagged_htmlId).disabled();
            $(tagged_htmlId).attr('checked','yes');
            $(text_htmlId).disabled(false);
        }
    } else {
        // não pode ser tagged, significa que só pode ser untagged
        /*$(untagged_htmlId).removeAttr('disabled');
        $(untagged_htmlId).attr('checked','yes');*/
    }

    if (where == "src") {
        src_urn = urnData.urn_string;
        src_vlan_min = vlan_min;
        src_vlan_max = vlan_max;
        src_vlan_validValues = vlan_validValues;
        $("#src_urn").val(src_urn);
    } else if (where == "dst") {
        dst_urn = urnData.urn_string;
        dst_vlan_min = vlan_min;
        dst_vlan_max = vlan_max;
        dst_vlan_validValues = vlan_validValues;
        $("#dst_urn").val(dst_urn);
    }

    validateTab1();
}

function map_getUrnData(where) {
    var domain_id = "#" + where + "_domain";
    var network_id = "#" + where + "_network";
    var device_id = "#" + where + "_device";
    var port_id = "#" + where + "_port";

    var ports = map_getPorts($(domain_id).html(), $(network_id).html(), $(device_id).val());
    var urnData = null;

    for (var i=0; ports.length; i++) {
        if (ports[i].port_number == $(port_id).val()) {
            urnData = ports[i];
            break;
        }
    }
    return urnData;
}

function map_changeVlanType(elem, where) {
    var text_htmlId = "#" + where + "_vlanText";
    var vlan_htmlId = "#confirmation_" + where + "_vlan"
    if ((elem).is(':checked')){
        $(text_htmlId).disabled();
        $(vlan_htmlId).html("Tagged: " + $(text_htmlId).val());
    } else {
        $(text_htmlId).disabled(false);        
        $(vlan_htmlId).html("Untagged");
    }
    validateTab1();
}

function map_saveFlow(flow_id) {
    var flow_Array = new Array();

    var action = "";
    if (flow_id) {
        action = "update";
        flow_Array[0] = flow_id; // id do flow quando está editando
    } else {
        action = "add";
        flow_Array[0] = 0;
    }

    flow_Array[1] = $('#name').val(); // name

    if (!flow_Array[1]) {
        setFlash(flash_nameReq, "warning");
        return;
    }

    flow_Array[2] = validateBand($('#bandwidth').val()); // bandwidth
    if (!flow_Array[2]) {
        setFlash(flash_bandInv, "warning");
        return;
    }

    if (src_urn) {
        var src_domain_id;
        for (var i=0; i<domains.length; i++){
            if (domains[i].name == $("#src_domain").html()){
                src_domain_id = domains[i].id;
            }
        }

        flow_Array[3] = src_domain_id;// source domainId
        flow_Array[4] = src_urn; // source URN
    } else {
        setFlash(flash_sourceReq, "warning");
        return;
    }

    if ($("#src_vlanUntagged").attr("checked"))
        flow_Array[5] = 0;
    else if ($("#src_vlanTagged").attr("checked")) {
        if (checkVLAN("src"))
            flow_Array[5] = $('#src_vlanText').val(); // source VLAN
        else {
            setFlash(flash_srcVlanInv, "warning");
            return;
        }
    } else {
        setFlash(flash_srcVlanReq, "warning");
        return;
    }

    if (dst_urn) {
        var dst_domain_id;
        for (i=0; i<domains.length; i++){
            if (domains[i].name == $("#dst_domain").html()){
                dst_domain_id = domains[i].id;
            }
        }
        flow_Array[6] = dst_domain_id; // destination domainId
        flow_Array[7] = dst_urn; // destination URN
    } else {
        setFlash(flash_destReq, "warning");
        return;
    }

    if ($("#dst_vlanUntagged").attr("checked"))
        flow_Array[8] = 0;
    else if ($("#dst_vlanTagged").attr("checked")) {
        if (checkVLAN("dst"))
            flow_Array[8] = $('#dst_vlanText').val(); // destination VLAN
        else {
            setFlash(flash_dstVlanInv, "warning");
            return;
        }
    } else {
        setFlash(flash_dstVlanReq, "warning");
        return;
    }    

    $.redir('circuits/flows/'+action, {
        flowData: flow_Array
    });
}

function validateBand(band_value) {
    var band = band_value.replace(/ /g, "");
    if (band >= band_min && band <= band_max) {
        if (band % band_div == 0) {
            return band;
        } else
            return false;
    } else
        return false;
}


(function($){
    
    $.fn.mapView = {
        inicialize: function(){
            view_center = new google.maps.LatLng(-23.051931,-60.975511);
            var view_myOptions = {
                zoom: 5,
                zoomControl: false,
                center: view_center,
                streetViewControl: false,
                mapTypeControl: false,
                draggable: false,
                disableDoubleClickZoom: true,
                keyboardShortcuts: false,
                scrollwheel: false,
                backgroundColor: "white",
                mapTypeId: google.maps.MapTypeId.TERRAIN
            };
            view_map = new google.maps.Map(document.getElementById("view_map_canvas"), view_myOptions);


            google.maps.event.trigger(view_map, 'resize');
            view_map.setZoom( view_map.getZoom() );
        }
    }
    
    /*função para criar mapa de edição */
    $.fn.mapEdit = {
        inicialize: function(){ /*inicializa mapa */
        
            var edit_myOptions = {
                zoom: 5,
                center: new google.maps.LatLng(-23.051931,-60.975511),
                streetViewControl: false,
                navigationControlOptions: {
                    style: google.maps.NavigationControlStyle.ZOOM_PAN
                },
                backgroundColor: "white",
                //    mapTypeControl: false,
                mapTypeId: google.maps.MapTypeId.TERRAIN
            };
            edit_map = new google.maps.Map(document.getElementById("edit_map_canvas"), edit_myOptions);
            var RedefineZoomControl = function (map, div, home) {
                var controlDiv = div;

                controlDiv.style.padding = '5px';

                // Set CSS for the control border
                var goHomeUI = document.createElement('DIV');
                goHomeUI.title = 'Click to reset zoom';
                controlDiv.appendChild(goHomeUI);
  
                // Set CSS for the control interior
                var goHomeText = document.createElement('DIV');
                goHomeText.innerHTML = reset_zoom;
                goHomeUI.appendChild(goHomeText);
                $(goHomeText).addClass("zoom ui-button ui-widget ui-state-default ui-corner-all ui-widget-content").attr('style', "direction: ltr;overflow: hidden;text-align: center;position: relative;font-family: Arial, sans-serif;-webkit-user-select: none;font-size: 12px;line-height: 160%;padding: 0px 6px;border-radius: ;-webkit-box-shadow: rgba(0, 0, 0, 0.347656) 2px 2px 3px;box-shadow: rgba(0, 0, 0, 0.347656) 2px 2px 3px;min-width: 44px;color: black;border: 1px solid #A9BBDF;border-image: initial;padding-left: 6px;font-weight: normal;background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FEFEFE), to(#F3F3F3));background-osition: initial initial;background-repeat: initial initial;");
  
                google.maps.event.addDomListener(goHomeUI, 'click', edit_resetZoom);
            }
            var homeControlDiv = document.createElement('DIV');
            var homeControl = new RedefineZoomControl(edit_map, homeControlDiv);
            homeControlDiv.index = 1;
            edit_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
	
            google.maps.event.trigger(edit_map, 'resize');
            edit_map.setZoom( edit_map.getZoom() );
            infowindow = new google.maps.InfoWindow();
            google.maps.event.addListener(edit_map, 'zoom_changed', function() {
                if (infowindow) {
                    infowindow.close(edit_map);
                }
            });
	
            $.fn.mapEdit.prepareContextMenu();
            MyOverlay.prototype = new google.maps.OverlayView();
            MyOverlay.prototype.onAdd = function() { }
            MyOverlay.prototype.onRemove = function() { }
            MyOverlay.prototype.draw = function() { }
            //	MyOverlay.prototype.draw.setMap(edit_map);
            function MyOverlay(edit_map) {
                this.setMap(edit_map);
            }
            overlay = new MyOverlay(edit_map);
            mapDiv = $(edit_map.getDiv());
            edit_initializeMap();
            initializeTimer();
        },
    
        clearMapElements: function (elements) {
            for (i in elements)
                elements[i].setMap(null);
            return [];
        },
    
        prepareContextMenu: function(){
        
            contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
            contextMenu.append('<li><a href="#fromHere">' + from_here_string + '</a></li>');
            contextMenu.append('<li><a href="#toHere">' + to_here_string + '</a></li>');
            contextMenu.bind('contextmenu', function() {
                return false;
            });
            $(edit_map.getDiv()).append(contextMenu);
        },
    
    
        clearPoint: function (point) {
            var n = 0;
            if (point == "src") {
                if (!srcSet)
                    return false;
                srcSet = false;
                n=0;
            } else if (point == "dst") {
                if (!dstSet)
                    return false;
                dstSet = false;
                n=1;
            }
            $("#bandwidth").spinner('disable');
            $('#bandwidth_un').disabled();
            $("#"+point+"_domain,#"+point+"_network").empty();
            $("#"+point+"_device,#"+point+"_port").empty().disabled();//,#src_vlanTagged
            map_clearVlanConf(point);
            path.splice(n, 1);            
            edit_lines = this.clearMapElements(edit_lines);
            edit_selectedMarkers.pop().setMap(null);
            while (edit_markersArray.length > 0 )
                edit_markersArray.pop().setMap(null);
            waypointsMarkers = this.clearMapElements(waypointsMarkers);
            edit_setBounds(edit_bounds); 
            this.prepareContextMenu();
            edit_initializeMap();
        },
    
        clearSrc: function () {//limpa ponto de origem
            $.fn.mapEdit.clearPoint('src');
        },
    
        clearDst: function () {//limpa ponto de destino
            $.fn.mapEdit.clearPoint('dst');
        },
        clearAll: function (){
            if (path.length != 0) {
                console.debug('eeee');
                for (var i=counter; i>0; i--) {
                    alert(i);
                    var removeHop = "#removeHop" + counter;
                    if ($(removeHop)) {
                        lessFields($(removeHop));
                    }
                }
                counter = 0;
            }
    
            edit_clearSelectedMarkers();
            edit_clearMarkers();
            path = [];
            edit_clearTopologyMarkers();
            edit_setBounds(edit_bounds);    
    
            
            $.fn.mapEdit.prepareContextMenu();
    
            view_clearAll();    
    
            edit_initializeMap();
        }
    
    };
	
    /* **************** DOCUMENT READY !!!! ******************** */
    
    $(function(){
        var f = function(){
            var v = ($("#bandwidth").val()/band_max)*100;
            if (v>100 || v < 0)
                return ;
            var k = 2*(50-v);
		    
            $('#bandwidth_bar_inside').animate({
                width: v+'%'/*, 
                'background-color': 'rgb('+(Math.round(255*(100-(k<0?0:k))/100))+','+(Math.round(255*(100-(-k<0?0:-k))/100))+',0)'*/
            }, 100);       
        };
        $('#bandwidth').attr("min", band_min).attr("max", band_max).attr("step", band_div).numeric().spinner({
            spin: f, 
            stop: f
        }).spinner("disable").bind('spin', f).change(f).keyup(f).click(f).scroll(f);
        $('#bandwidth_un').disabled();
        /*  if (false){ //configura tabs?
            $('#tabs-res ul').show();
            $('#tabs-3').show();
            $('#tabs-res').tabs({
                select: function(event, ui){
                    clearFlash();
                    // antes de mostrar a aba, copia conteudo dos campos
                    fillConfirmationTab();
                    google.maps.event.trigger(view_map, 'resize');
                    view_setBounds(view_bounds);
                }
            });
        } else {*/
        $('#tabs-res ul').hide();
        $('#tabs-3').hide();
        /*$('#repeat_chkbox').button();*/
        /*$('#weekdays input[type=checkbox]').button();*/
    
        $('#src_clearpath').click($.fn.mapEdit.clearSrc);
        $('#dst_clearpath').click($.fn.mapEdit.clearDst);
        $("#bandwidth").spinner('disable');
        $('#bandwidth_un').disabled();
        $("#src_domain,#src_network,#dst_domain,#dst_network").empty();
        $("#src_device,#src_port,#dst_device,#dst_port").empty().disabled();
        $('#repeat_chkbox').click(function(){
            showRecurrenceBox();
        });
        $('.recurrence-table td input[type=checkbox]').click(function(){
            checkWeekDay(this.id);
        });
        $('#recur_radio,#nr_occurr,#untilDate').change(function(){
            setUntilType();
        });
        /* resize da janela muda tamanho do mapa */
        $(window).resize(function() {
            $('#edit_map_canvas').css('width', $('#tabs-2').outerWidth()-$($('#tabs-1 div.tab_subcontent')[1]).outerWidth()-12 );
            /*      $('.tab-overlay').each(function(n, item){
		  	$(item).css({'width': $(item).parent().width(), 'height': $(item).parent().height()});
		  });*/
        });
        /* quando digita nome, tira overlay */
        $('form#reservation_add').submit(function() {
            validateReservationForm();
        });
        $('#res_name').keyup(function(){
            if ($(this).val())
                $('.tab-overlay').fadeOut();
            else
                $('.tab-overlay').fadeIn();
        }).focus().keyup();
        $.fn.mapEdit.inicialize();
        $(window).trigger('resize');
    });
	$(window).load(function (){$(window).trigger('resize');});
	
	
})(jQuery);

