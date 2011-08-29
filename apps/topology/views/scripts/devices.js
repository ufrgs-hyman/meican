function validateDeviceForm() {
    if ($("#dev_descr").val() == "") {
        setFlash(flash_nameReq);
        js_submit_form = false;
        return false;
    }

//    if ($("#dev_ip").val() == "") {
//        setFlash(flash_ipAddrReq);
//        js_submit_form = false;
//        return false;
//    }

    if (($("#dev_domain").val() == -1) || ($("#dev_network").val() == -1)) {
        setFlash(flash_networkReq);
        js_submit_form = false;
        return false;
    }

    return true;
}

function dev_changeDomain(elem) {
    $("#dev_network").attr("disabled","disabled");
    
    if (elem.value != -1) {
        var networks = null;
        for (var i in domains) {
            if (domains[i].id == elem.value) {
                networks = domains[i].networks;
                break;
            }
        }
        fillSelectBox("#dev_network", networks);
        $("#dev_network").removeAttr("disabled");
        $("#dev_network").slideDown();
    }
}