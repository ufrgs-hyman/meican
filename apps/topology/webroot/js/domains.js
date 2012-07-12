function buildIDC_URL(){

    // Selecting HTTP/HTTPS after setting OSCARS version
    if ($("#http").attr("checked")) 
    {
        //$("#idc_url").html($("#dom_version option:selected").text());
              
        if (($("#dom_version option:selected").text()).indexOf("OSCARS 0.6") != -1)
        {
            $("#idc_url").html("http://" + $("#oscars_ip").val() + ":9001/OSCARS");
        }
        else
        {
            $("#idc_url").html("http://" + $("#oscars_ip").val() + ":8080/axis2/services/OSCARS");
        }
    } 
    else if ($("#https").attr("checked")) 
    {
        $("#idc_url").html("https://" + $("#oscars_ip").val() + ":8443/axis2/services/OSCARS");
    }
        
    // Change Version AFTER selecting HTTP/HTTPS
    if (($("#dom_version option:selected").text()).indexOf("OSCARS 0.6") != -1)
    {
        if ($("#http").attr("checked"))
        { 
            $("#idc_url").html("http://" + $("#oscars_ip").val() + ":9001/OSCARS");
        }
    }
    else
    {
        if ($("#http").attr("checked"))
        {
            $("#idc_url").html("http://" + $("#oscars_ip").val() + ":8080/axis2/services/OSCARS");
        }
        else if($("#https").attr("checked"))
        {
            $("#idc_url").html("https://" + $("#oscars_ip").val() + ":8443/axis2/services/OSCARS");
        }
        else    //Nothing selected
        {
            $("#idc_url").html("");
        }
    }
    
    $("#input_idcUrl").val($("#idc_url").html());
}
