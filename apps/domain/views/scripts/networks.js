function validateNetworkForm() {
    if (!$("#net_descr").val()) {
        setFlash(flash_nameReq);
        js_submit_form = false;
        return false;
    }

    if (!$("#net_lat").val() || !$("#net_lng").val()) {
        setFlash(flash_setLatLng);
        js_submit_form = false;
        return false;
    }

    if ($("#domain_select").val() == -1) {
        setFlash(flash_setDomain);
        js_submit_form = false;
        return false;
    }

    return true;
}