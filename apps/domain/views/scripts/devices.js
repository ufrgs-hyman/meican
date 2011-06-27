function validateDeviceForm() {
    setFlash("");

    if ($("#dev_descr").val() == "") {
        setFlash(flash_nameReq);
        js_submit_form = false;
        return false;
    }

    if ($("#dev_ip").val() == "") {
        setFlash(flash_ipAddrReq);
        js_submit_form = false;
        return false;
    }

    if ($("#dev_network").val() == -1) {
        setFlash(flash_networkReq);
        js_submit_form = false;
        return false;
    }

    return true;
}