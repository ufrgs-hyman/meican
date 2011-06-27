function newURN() {
    if (urns) {
        fillURNLine();
    } else {
        $('#loading').show();
        $.post("main.php?app=domain&controller=urns&action=get_topology", function(data) {
            $('#loading').hide();
            
            if (data) {
                // retornou dados, testa se vetor está vazio
                if (data.length != 0) {
                    urns = data;
                    fillURNLine();
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

function fillURNLine() {
    $('#urn_table tbody tr:last').after('<tr id="newline' + pos + '"/>');

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

    fillSelectBox("#network" + pos, networks);
    fillSelectBox("#urn" + pos, urns);

    $('#network' + pos).change(function() {
        changeNetworkURN(this);
    });
    
    $('#urn' + pos).change(function() {
        changeURN(this);
    });

    $("#delete" + pos).click(function() {
        var replaceId = this.id.replace(/delete/, "");
        validArray[replaceId] = false;
        newCont--;
        if (!newCont && !isEditingURN) {
            $('#save_button').hide();
            $('#cancel_button').hide();
        }
        replaceId = "#" + this.id.replace(/delete/, "newline");
        $(replaceId).remove();
    });

    $('#save_button').show();
    $('#cancel_button').show();
    
    validArray[pos] = true;
    newCont++;
    pos++;
}

function editURN(urnId) {
    var old_net_id = $('#network_box' + urnId).attr("title");
    $('#network_box' + urnId).removeAttr("title");
    
    var old_dev_id = $('#device_box' + urnId).attr("title");
    $('#device_box' + urnId).removeAttr("title");
    
    var devices = null;
    for (var i=0; networks.length; i++) {
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
        changeNetworkURN(this);
    });
    
    $('#save_button').show();
    $('#cancel_button').show();

    isEditingURN = true;
    editpos++;
}

function getURN(urn_id) {
    var urn = null;

    for (var i=0; urns.length; i++) {
        if (urns[i].id == urn_id) {
            urn = urns[i];
            break;
        }
    }
        
    return urn;
}

function changeURN(urn_select) {
    var portId = "#" + urn_select.id.replace(/urn/, "port");
    var vlanId = "#" + urn_select.id.replace(/urn/, "vlan");
    var max_capacityId = "#" + urn_select.id.replace(/urn/, "max_capacity");
    var min_capacityId = "#" + urn_select.id.replace(/urn/, "min_capacity");
    var granularityId = "#" + urn_select.id.replace(/urn/, "granularity");
    
    var urn = getURN(urn_select.value);

    if (urn) {
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

function changeNetworkURN(network_select) {
    var deviceId = "#" + network_select.id.replace(/network/, "device");
    var network_id = network_select.value;
    
    $(deviceId).slideUp();

    if (network_id != -1) {
        var devices = null;
        for (var i=0; networks.length; i++) {
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
            if ( (validArray[i]) && ((!isImporting && ($('#urn'+i).val() == -1)) || ($('#network'+i).val() == -1) || ($('#device'+i).val() == -1)) ) {
                setFlash(fillMessage, "warning");
                return;
            }
        }

        // verifica se nenhum URN foi selecionado mais de uma vez
        for (i=0; i < pos; i++) {
            for (var j=0; j < pos; j++) {
                if (!isImporting && validArray[i] && (i != j) && ($("#urn"+i).val() == $("#urn"+j).val()) ) {
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
                    urn = urns[i];
                } else {
                    urn = getURN($('#urn'+i).val());
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
        $.post("main.php?app=domain&controller=urns&action=update",
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
        $.post("main.php?app=domain&controller=urns&action=singleDelete", {
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
    $("#line" + lineNr).remove();
}