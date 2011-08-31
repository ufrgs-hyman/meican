function newURN(domain_id) {
    var urns = getURNsFromDomain(domain_id);
    
    if (urns) {
        fillURNLine(domain_id);
    } else {
        $('#loading' + domain_id).show();
        $.post("main.php?app=topology&controller=urns&action=ajax_get_topology", {
            domain_id: domain_id
        }, function(data) {
            $('#loading' + domain_id).hide();
            
            if (data) {
                // retornou dados, testa se vetor está vazio
                if (data.length != 0) {
                    setURNsOfDomain(domain_id, data);
                    var urns_temp = getURNsFromDomain(domain_id);
                    for (var i in urns_temp) {
                        fillURNLine(domain_id, urns_temp[i].id);
                    }
                } else {
                    // topologia atualizada
                    setFlash(str_no_newUrn);
                }
            } else {
                // deu erro
                setFlash(str_error_import, "error");
                //$('#urn_table tbody tr:last').after('<tr><td colspan="11">' + str_error_import + '</td></tr>');
            }
        }, "json");
    }
}

function setURNsOfDomain(domain_id, urns) {
    for (var i in domains) {
        if (domains[i].id == domain_id) {
            domains[i].topo_urns = urns;
            break;
        }
    }
}

function getURNsFromDomain(domain_id) {
    var urns = null;
    
    for (var i in domains) {
        if (domains[i].id == domain_id) {
            urns = domains[i].topo_urns;
            break;
        }
    }
    
    return urns;
}

function getNetworksFromDomain(domain_id) {
    var networks = null;
    
    for (var i in domains) {
        if (domains[i].id == domain_id) {
            networks = domains[i].networks;
            break;
        }
    }
    
    return networks;
}

function newURNLine(dom_id) {
    
    isManual = true;
    
    $('#urn_table' + dom_id + ' tbody tr:last').after('<tr id="newline' + pos + '"/>');

    var columns = '<td class="edit" colspan="3"><img class="edit" alt="clear" border="0" id="delete' + pos + '" src="layouts/img/clear.png"/></td>';
    columns += '<td><select id="network' + pos + '"/></td>';
    columns += '<td><select id="device' + pos + '" style="display:none"/></td>';
    columns += '<td><input type="text" size="3" id="port' + pos + '"/></td>';
    columns += '<td><input type="text" size="50" id="name' + pos + '"/></td>';
    columns += '<td><input type="text" size="10" id="vlan' + pos + '"/></td>';
    columns += '<td><input type="text" size="10" id="max_capacity' + pos + '"/></td>';
    columns += '<td><input type="text" size="10" id="min_capacity' + pos + '"/></td>';
    columns += '<td><input type="text" size="10" id="granularity' + pos + '"/></td>';
    $('#newline' + pos).append(columns);
    
    fillSelectBox("#network" + pos, getNetworksFromDomain(dom_id));
    $('#network' + pos).change(function() {
        changeNetworkURN(dom_id, this);
    });
    
    $("#delete" + pos).click(function() {
        var replaceId = this.id.replace(/delete/, "");
        validArray[replaceId] = false;
        newCont--;
        if (!newCont && !isEditingURN) {
            isManual = false;
            $('#save_button').hide();
            $('#cancel_button').hide();
            for (var i in domains) {
                $('#add_button' + domains[i].id).show();
            }
        }
        replaceId = "#" + this.id.replace(/delete/, "newline");
        $(replaceId).remove();
    });
    
    for (var i in domains) {
        $('#add_button' + domains[i].id).hide();
    }
    
    $('#save_button').show();
    $('#cancel_button').show();
    
    validArray[pos] = true;
    newCont++;
    pos++;
}

function fillURNLine(dom_id, urn_id) {
    $('#urn_table' + dom_id + ' tbody tr:last').after('<tr id="newline' + pos + '"/>');

    var columns = '<td class="edit" colspan="3"><img class="edit" alt="clear" border="0" id="delete' + pos + '" src="layouts/img/clear.png"/></td>';
    columns += '<td><select id="network' + pos + '"/></td>';
    columns += '<td><select id="device' + pos + '" style="display:none"/></td>';
    columns += '<td id="port' + pos + '"/>';
    columns += '<td><select id="urn' + pos + '"/></td>';
    columns += '<td id="vlan' + pos + '"/>';
    columns += '<td id="max_capacity' + pos + '"/>';
    columns += '<td id="min_capacity' + pos + '"/>';
    columns += '<td id="granularity' + pos + '"/>';
    $('#newline' + pos).append(columns);

    fillSelectBox("#network" + pos, getNetworksFromDomain(dom_id));
    if (urn_id) {
        fillSelectBox("#urn" + pos, getURNsFromDomain(dom_id), urn_id);
        changeURN(dom_id, "#urn" + pos);
    } else
        fillSelectBox("#urn" + pos, getURNsFromDomain(dom_id));

    $('#network' + pos).change(function() {
        changeNetworkURN(dom_id, this);
    });
    
    $('#urn' + pos).change(function() {
        changeURN(dom_id, this);
    });

    $("#delete" + pos).click(function() {
        var replaceId = this.id.replace(/delete/, "");
        validArray[replaceId] = false;
        newCont--;
        if (!newCont && !isEditingURN) {
            $('#save_button').hide();
            $('#cancel_button').hide();
            for (var i in domains) {
                $('#add_man_button' + domains[i].id).show();
            }
        }
        replaceId = "#" + this.id.replace(/delete/, "newline");
        $(replaceId).remove();
    });

    for (var i in domains) {
        $('#add_man_button' + domains[i].id).hide();
    }
    $('#save_button').show();
    $('#cancel_button').show();
    
    validArray[pos] = true;
    newCont++;
    pos++;
}

function editURN(dom_id, urnId) {
    var old_net_id = $('#network_box' + urnId).attr("title");
    $('#network_box' + urnId).removeAttr("title");
    
    var old_dev_id = $('#device_box' + urnId).attr("title");
    $('#device_box' + urnId).removeAttr("title");
    
    var networks = getNetworksFromDomain(dom_id);
    var devices = null;
    for (var i in networks) {
        if (networks[i].id == old_net_id) {
            devices = networks[i].devices;
            break;
        }
    }

    $('#network_box' + urnId).empty();
    $('#device_box' + urnId).empty();

    $('#network_box' + urnId).html('<select id="edit_network' + editpos + '"/>');
    $('#device_box' + urnId).html('<select id="edit_device' + editpos + '"/>');

    fillSelectBox('#edit_network' + editpos, networks, old_net_id);
    fillSelectBox('#edit_device' + editpos, devices, old_dev_id);

    $('#edit_network' + editpos).change(function() {
        changeNetworkURN(dom_id, this);
    });
    
    $('#save_button').show();
    $('#cancel_button').show();

    isEditingURN = true;
    editpos++;
}

function getURN(domain_id, urn_id) {
    var urn = null;
    var urns = getURNsFromDomain(domain_id);

    for (var i in urns) {
        if (urns[i].id == urn_id) {
            urn = urns[i];
            break;
        }
    }
        
    return urn;
}

function changeURN(domain_id, urn_select) {
    var portId = "#" + $(urn_select).attr("id").replace(/urn/, "port");
    var vlanId = "#" + $(urn_select).attr("id").replace(/urn/, "vlan");
    var max_capacityId = "#" + $(urn_select).attr("id").replace(/urn/, "max_capacity");
    var min_capacityId = "#" + $(urn_select).attr("id").replace(/urn/, "min_capacity");
    var granularityId = "#" + $(urn_select).attr("id").replace(/urn/, "granularity");
    var html_networkId = "#" + $(urn_select).attr("id").replace(/urn/, "network");
    var html_deviceId = "#" + $(urn_select).attr("id").replace(/urn/, "device");
    
    $(html_networkId + ' option[value="-1"]').attr("selected", true);
    $(html_deviceId).slideUp();
    
    var urn = getURN(domain_id, $(urn_select).val());

    if (urn) {
        var networks = getNetworksFromDomain(domain_id);
        var dev_found = false;
        for (var i in networks) {
            for (var j in networks[i].devices) {
                if (urn.node_id == networks[i].devices[j].node_id) {
                    $(html_networkId + ' option[value="' + networks[i].id + '"]').attr("selected", true);
//                    $.each($(html_networkId + " option"), function() {
//                        if (this.value == networks[i].id) {
//                            $(this).attr("selected",true);
//                        }
//                    });
                
                    fillSelectBox(html_deviceId, networks[i].devices, networks[i].devices[j].id);
                    $(html_deviceId).slideDown();
                    dev_found = true;
                    break;
                }
            }
            if (dev_found)
                break;
        }
        
        $(vlanId).html(urn.vlan);
        $(max_capacityId).html(urn.max_capacity);
        $(min_capacityId).html(urn.min_capacity);
        $(granularityId).html(urn.granularity);
        $(portId).html(urn.port);
    } else {
        $(portId).empty();
        $(vlanId).empty();
        $(max_capacityId).empty();
        $(min_capacityId).empty();
        $(granularityId).empty();
    }
}

function changeNetworkURN(domain_id, network_select) {
    var deviceId = "#" + network_select.id.replace(/network/, "device");
    var network_id = network_select.value;
    var networks = getNetworksFromDomain(domain_id);
    
    $(deviceId).slideUp();

    if (network_id != -1) {
        var devices = null;
        for (var i in networks) {
            if (networks[i].id == network_id) {
                devices = networks[i].devices;
                break;
            }
        }
        
        if (devices.length > 0) {
            fillSelectBox(deviceId, devices);
        } else {
            $(deviceId).empty();
            $(deviceId).append('<option selected="true" value="-1">No device</option>');
        }
        $(deviceId).slideDown();
    }
}

function saveURN() {
    var urn_editArray = new Array();
    var urn_newArray = new Array();

    var i=0;
    
    if (isEditingURN) {

        //VERIFICA SE TODOS OS CAMPOS ESTÃO PREENCHIDOS
        for (i=0; i < editpos; i++)
            if (($('#edit_network' + i).val() == -1) || ($('#edit_device'+i).val() == -1)) {
                setFlash(fillMessage, "warning");
                return;
            }

        // preenche editArray
        for (i=0; i < editpos; i++) {
            urn_editArray[i] = new Array();

            urn_editArray[i][0] = $('#edit_network' + i).parent().attr("id").replace(/network_box/, ""); // id da URN
            urn_editArray[i][1] = $("#edit_network"+i).val(); // id da rede
            urn_editArray[i][2] = $("#edit_device"+i).val(); // id do device
        }
    }

    if (newCont) {
        // verifica se todos os select box estao preenchidos
        for (i=0; i < pos; i++) {
            if ( (validArray[i]) && ((!(isImporting || isManual) && ($('#urn'+i).val() == -1)) || ($('#network'+i).val() == -1) || ($('#device'+i).val() == -1)) ) {
                setFlash(fillMessage, "warning");
                return;
            }
        }

        // verifica se nenhum URN foi selecionado mais de uma vez
        for (i=0; i < pos; i++) {
            for (var j=0; j < pos; j++) {
                if (!(isImporting || isManual) && validArray[i] && (i != j) && ($("#urn"+i).val() == $("#urn"+j).val()) ) {
                    setFlash(duplicateMessage, "error");
                    return;
                }
            }
        }

        // preenche newArray
        var index = 0;
        for (i=0; i < pos; i++) {
            if (validArray[i]) {
                urn_newArray[index] = new Array();
                
                var urn = null;
                
                if (isImporting) {
                    // se estiver importando, puxa informações do vetor urns_to_import (variável vem do PHP)
                    urn = urns_to_import[i];
                } else if (isManual) {
                    // se estiver adicionando manualmente, puxa informações dos inputs (informado pelo usuário)
                    urn = new Object();
                    urn.port = $("#port"+i).val();
                    urn.vlan = $("#vlan"+i).val();
                    urn.name = $("#name"+i).val();
                    urn.max_capacity = $("#max_capacity"+i).val();
                    urn.min_capacity = $("#min_capacity"+i).val();
                    urn.granularity = $("#granularity"+i).val();
                } else {
                    // senão, puxa informações do vetor lido (variável carregada por ajax)
                    var dom_id = $('#network' + i).parent().parent().parent().parent().attr("id").replace(/urn_table/, ""); // id do domínio
                    urn = getURN(dom_id, $('#urn'+i).val());
                }

                urn_newArray[index][0] = $("#network"+i).val(); // id da rede
                urn_newArray[index][1] = $("#device"+i).val(); // id do device
                urn_newArray[index][2] = urn.port; // porta
                urn_newArray[index][3] = urn.vlan; // VLAN values
                urn_newArray[index][4] = urn.name; // string
                urn_newArray[index][5] = urn.max_capacity; // max capacity
                urn_newArray[index][6] = urn.min_capacity; // min capacity
                urn_newArray[index][7] = urn.granularity; // granularity
                index++;
            }
        }
    }

    // mostra mensagem de confirmação para o usuário
    if (confirm(confirmMessage)) {
        $.post("main.php?app=topology&controller=urns&action=update",
            {
                urn_newArray: urn_newArray,
                urn_editArray: urn_editArray
            },
            function(data) {
                loadHtml(data);
            }
        );
    }
}

function deleteURN(urnId) {
    if (confirm(str_delete_urn)) {
        $.post("main.php?app=topology&controller=urns&action=singleDelete", {
            urnId: urnId
        }, function(data) {
            if (data) {
                setFlash(str_urn_deleted, "success");
                $('#line' + urnId).remove();
            } else {
                setFlash(str_urn_not_deleted, "error");
            }
        }, "json");
    } else return;
}

function deleteURNLine(lineNr) {
    validArray[lineNr] = false;
    newCont--;
    $("#newline" + lineNr).remove();
}