function fillPorts(htmlId, portsArray, current_port) {
    clearSelectBox(htmlId);
    for (var i=0; i < portsArray.length; i++) {
        if ((portsArray[i].port_number == current_port) || (portsArray.length == 1))
            $(htmlId).append('<option selected="true" value="' + portsArray[i].port_number + '">' + portsArray[i].port_number + '</option>');
        else
            $(htmlId).append('<option value="' + portsArray[i].port_number + '">' + portsArray[i].port_number + '</option>');
    }
}

function getNetworks(where) {
    if (where == "src")
        return src_networks;
    else if (where == "dst")
        return dst_networks;
    else return null;
}

function getDevices(network_id, where) {
    var networks = getNetworks(where);
    var devices = null;

    for (var i=0; networks.length; i++) {
        if (networks[i].id == network_id) {
            devices = networks[i].devices;
            break;
        }
    }
    return devices;
}

function getPorts(network_id, device_id, where) {
    var devices = getDevices(network_id, where);
    var ports = null;

    for (var i=0; devices.length; i++) {
        if (devices[i].id == device_id) {
            ports = devices[i].ports;
            break;
        }
    }
    return ports;
}

function getUrnData(where) {
    var network_id = "#" + where + "_network";
    var device_id = "#" + where + "_device";
    var port_id = "#" + where + "_port";

    var ports = getPorts($(network_id).val(), $(device_id).val(), where);
    var urnData = null;

    for (var i=0; ports.length; i++) {
        if (ports[i].port_number == $(port_id).val()) {
            urnData = ports[i];
            break;
        }
    }
    return urnData;
}

function changeDomain(elem, where) {
    var network_id = "#" + elem.id.replace(/domain/, "network");
    var device_id = "#" + elem.id.replace(/domain/, "device");
    var port_id = "#" + elem.id.replace(/domain/, "port");
    var load_id = "#" + elem.id.replace(/domain/, "loading");

    $(port_id).slideUp();
    $(device_id).slideUp();
    $(network_id).slideUp();

    if (where == "src")
        src_networks = null;
    else if (where == "dst")
        dst_networks = null;

    clearVlanConf(where);
    clearSelectBox(network_id);

    if (elem.value != -1) {

        if ( (where == "dst") && (elem.value == $("#src_domain").val()) ) {
            dst_networks = src_networks;
            fillSelectBox(network_id, dst_networks);
            $(network_id).slideDown();
        } else if ( (where == "src") && (elem.value == $("#dst_domain").val()) ) {
            src_networks = dst_networks;
            fillSelectBox(network_id, src_networks);
            $(network_id).slideDown();
        } else {
            $(load_id).show();

            $.post("main.php?app=circuits&controller=flows&action=get_domain", {
                domain_id: elem.value
            }, function(data) {
                $(load_id).hide();

                if (data){

                    if (where == "src")
                        src_networks = data;
                    else if (where == "dst")
                        dst_networks = data;

                    fillSelectBox(network_id, data);
                    $(network_id).slideDown();
                } else setFlash('Cannot connect to domain', 'error');
            }, "json");
        }
    }
}

function changeNetwork(elem, where) {
    var device_id = "#" + elem.id.replace(/network/, "device");
    var port_id = "#" + elem.id.replace(/network/, "port");

    $(port_id).slideUp();
    $(device_id).slideUp();

    clearVlanConf(where);
    clearSelectBox(device_id);

    if (elem.value != -1) {
        var devices = getDevices(elem.value, where);

        fillSelectBox(device_id, devices);
        $(device_id).slideDown();
    }
}

function changeDevice(elem, where) {
    var network_id = "#" + elem.id.replace(/device/, "network");
    var port_id = "#" + elem.id.replace(/device/, "port");

    $(port_id).slideUp();

    clearVlanConf(where);
    clearSelectBox(port_id);

    if (elem.value != -1) {
        var ports = getPorts($(network_id).val(), elem.value, where);

        fillPorts(port_id, ports);

        if (ports.length == 1)
            setEndpointConf(where);

        $(port_id).slideDown();
    }
}

function changePort(elem, where) {
    clearVlanConf(where);
    if (elem.value != -1) {
        setEndpointConf(where);
    }
}

function clearVlanConf(where) {
    var untagged_htmlId = "#" + where + "_vlanUntagged";
    var tagged_htmlId = "#" + where + "_vlanTagged";
    var text_htmlId = "#" + where + "_vlanText";
    var tip_htmlId = "#" + where + "_vlanTip";

    $(tip_htmlId).html("");
    $(text_htmlId).val("");
    $(text_htmlId).attr('disabled','disabled');

    $(untagged_htmlId).removeAttr('checked');
    $(untagged_htmlId).attr('disabled','disabled');

    $(tagged_htmlId).removeAttr('checked');
    $(tagged_htmlId).attr('disabled','disabled');

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

function setEndpointConf(where) {

    var untagged_htmlId = "#" + where + "_vlanUntagged";
    var tagged_htmlId = "#" + where + "_vlanTagged";
    var text_htmlId = "#" + where + "_vlanText";
    var tip_htmlId = "#" + where + "_vlanTip";

    var urnData = getUrnData(where);

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
        $(tagged_htmlId).removeAttr('disabled');

        if (vlan_min && vlan_max)
            $(tip_htmlId).html('Value: ' + vlan_min + ' - ' + vlan_max);
        else if (vlan_validValues) {
            $(tip_htmlId).html('Value: ' + vlan_validValues);
        }

        if (allowUntag) {
            // pode ser untagged também
            $(untagged_htmlId).removeAttr('disabled');
            $(untagged_htmlId).attr('checked','yes');
        } else {
            $(tagged_htmlId).attr('checked','yes');
            $(text_htmlId).removeAttr('disabled');
        }
    } else {
        // não pode ser tagged, significa que só pode ser untagged
        $(untagged_htmlId).removeAttr('disabled');
        $(untagged_htmlId).attr('checked','yes');
    }

    if (where == "src") {
        src_urn = urnData.urn_string;
        src_vlan_min = vlan_min;
        src_vlan_max = vlan_max;
        src_vlan_validValues = vlan_validValues;
    } else if (where == "dst") {
        dst_urn = urnData.urn_string;
        dst_vlan_min = vlan_min;
        dst_vlan_max = vlan_max;
        dst_vlan_validValues = vlan_validValues;
    }
}

function changeVlanType(elem, where) {
    var text_htmlId = "#" + where + "_vlanText";

    if (elem.value == "FALSE")
        $(text_htmlId).attr('disabled','disabled');
    else if (elem.value == "TRUE")
        $(text_htmlId).removeAttr('disabled');
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

function checkVLAN(where) {

    var vlan_value = null;
    var vlan_min = null;
    var vlan_max = null;
    var vlan_validValues = null;

    if (where == "src") {
        vlan_value = $("#src_vlanText").val();
        vlan_min = src_vlan_min;
        vlan_max = src_vlan_max;
        vlan_validValues = src_vlan_validValues;
    } else if (where == "dst") {
        vlan_value = $("#dst_vlanText").val();
        vlan_min = dst_vlan_min;
        vlan_max = dst_vlan_max;
        vlan_validValues = dst_vlan_validValues;
    } else return false;

    if (vlan_min && vlan_max) {
        if ((vlan_value >= vlan_min) && (vlan_value <= vlan_max))
            return true;
        else
            return false;
    } else if (vlan_validValues) {
        var temp = new Array();
        var valid = false;
        temp = vlan_validValues.split(",");
        for (var i=0; i < temp.length; i++) {
            if (vlan_value == temp[i])
                valid = true;
        }
        if (valid)
            return true;
        else
            return false;
    } else
        return false;
}

function saveFlow(flow_id) {
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
        flow_Array[3] = $("#src_domain").val(); // source domainId
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
        flow_Array[6] = $("#dst_domain").val(); // destination domainId
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

    $.post("main.php?app=circuits&controller=flows&action="+action, {
        flowData: flow_Array
    }, function(data) {
        loadHtml(data);
    });
}

function fillFlowConf() {
    var networks = getNetworks("src");
    fillSelectBox("#src_network", networks, src_network_id);
    $("#src_network").show();
    var devices = getDevices(src_network_id, "src");
    fillSelectBox("#src_device", devices, src_device_id);
    $("#src_device").show();
    var ports = getPorts(src_network_id, src_device_id, "src");
    fillPorts("#src_port", ports, src_port);
    $("#src_port").show();

    networks = getNetworks("dst");
    fillSelectBox("#dst_network", networks, dst_network_id);
    $("#dst_network").show();
    devices = getDevices(dst_network_id, "dst");
    fillSelectBox("#dst_device", devices, dst_device_id);
    $("#dst_device").show();
    ports = getPorts(dst_network_id, dst_device_id, "dst");
    fillPorts("#dst_port", ports, dst_port);
    $("#dst_port").show();

    setEndpointConf("src");
    setEndpointConf("dst");

    if (src_vlan != 0) {
        $("#src_vlanText").val(src_vlan);
        $("#src_vlanText").removeAttr('disabled');

        $("#src_vlanTagged").removeAttr('disabled');
        $("#src_vlanTagged").attr('checked','yes');
    } else {
        $("#src_vlanUntagged").removeAttr('disabled');
        $("#src_vlanUntagged").attr('checked','yes');

        $("#src_vlanText").attr('disabled','disabled');
    }

    if (dst_vlan != 0) {
        $("#dst_vlanText").val(dst_vlan);
        $("#dst_vlanText").removeAttr('disabled');

        $("#dst_vlanTagged").removeAttr('disabled');
        $("#dst_vlanTagged").attr('checked','yes');
    } else {
        $("#dst_vlanUntagged").removeAttr('disabled');
        $("#dst_vlanUntagged").attr('checked','yes');

        $("#dst_vlanText").attr('disabled','disabled');
    }
}