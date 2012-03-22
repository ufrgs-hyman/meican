function buildIDC_URL(){
    if ($("#http").attr("checked")) {
        $("#idc_url").html("http://" + $("#oscars_ip").val() + ":8080/axis2/services/OSCARS");
    } else if ($("#https").attr("checked")) {
        $("#idc_url").html("https://" + $("#oscars_ip").val() + ":8443/axis2/services/OSCARS");
    }
    $("#input_idcUrl").val($("#idc_url").html());
}
