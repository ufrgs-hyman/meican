function newACL() {
    if (aros && acos) {
        fillACLNewLine();
    } else {
        $('#loading').show();
        $.post(baseUrl+"aaa/acl/get_aros_acos", function(data) {
            $('#loading').hide();
            
            if (data) {
                // retornou dados, testa se vetor está vazio
                if (data.length != 0) {
                    aros = data.aros;
                    acos = data.acos;
                    fillACLNewLine();
                } else {
                    setFlash("str_error_match_objs","error");
                }
            } else {
                // deu erro
                setFlash("str_error_fetch_objs", "error");
            }
        }, "json");
    }
}

function fillACLNewLine() {
    $('#acl_table tbody tr:last').after('<tr id="newline' + pos + '"/>');

    var columns = '<td colspan="3"><img class="edit" alt="clear" border="0" id="delete' + pos + '" src="'+baseUrl+'webroot/img/clear.png"/></td>';
    
    columns += '<td><select id="aro_model_select' + pos + '"/></td>';
    columns += '<td><select id="aro_obj_select' + pos + '" style="display:none"/></td>';
    
    columns += '<td><select id="aco_model_select' + pos + '"/></td>';
    columns += '<td><select id="aco_obj_select' + pos + '" style="display:none"/></td>';
    
    columns += '<td><select id="model_select' + pos + '"/></td>';
    
    columns += '<td><select id="create_select' + pos + '"/></td>';
    columns += '<td><select id="read_select' + pos + '"/></td>';
    columns += '<td><select id="update_select' + pos + '"/></td>';
    columns += '<td><select id="delete_select' + pos + '"/></td>';
    
    $('#newline' + pos).append(columns);

    fillSelectBox("#aro_model_select" + pos, aros);
    fillSelectBox("#aco_model_select" + pos, acos);
    
    fillSelectBox("#model_select" + pos, acos);
    $("#model_select" + pos).append('<option value="all">' + str_all + '</option>');

    $('#aro_model_select' + pos).change(function() {
        changeAccessSelect(this, aros);
    });
    
    $('#aco_model_select' + pos).change(function() {
        changeAccessSelect(this, acos);
    });
    
    var crud_opt = new Array();
    
    crud_opt[0] = new Object();
    crud_opt[0].id = "deny";
    crud_opt[0].name = deny_desc_string;
    
    crud_opt[1] = new Object();
    crud_opt[1].id = "allow";
    crud_opt[1].name = allow_desc_string;
    
    fillSelectBox("#create_select" + pos, crud_opt, "deny");
    fillSelectBox("#read_select" + pos, crud_opt, "deny");
    fillSelectBox("#update_select" + pos, crud_opt, "deny");
    fillSelectBox("#delete_select" + pos, crud_opt, "deny");
    
    $("#delete" + pos).click(function() {
        var replaceId = this.id.replace(/delete/, "");
        validArray[replaceId] = false;
        newCont--;
        if (!newCont && !isEditingACL) {
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

function changeAccessSelect(acc_select, acc_array) {
    var html_id = "#" + acc_select.id.replace(/model/, "obj");
    var acc_id = acc_select.value;
    
    $(html_id).slideUp();

    if (acc_id != -1) {
        var objs = null;
        for (var i in acc_array) {
            if (acc_array[i].id == acc_id) {
                objs = acc_array[i].objs;
                break;
            }
        }
        
        if (objs.length > 0) {
            if (objs.length == 1)
                fillSelectBox(html_id, objs, objs[0].id);
            else
                fillSelectBox(html_id, objs);
        } else {
            $(html_id).empty();
            $(html_id).append('<option selected="true" value="-1">No object</option>');
        }
        $(html_id).slideDown();
    }
}

function saveACL() {
    var acl_editArray = new Array();
    var acl_newArray = new Array();

    var i=0;

    if (isEditingACL) {

        //VERIFICA SE TODOS OS CAMPOS ESTÃO PREENCHIDOS
        for (i=0; i < editpos; i++)
            if ( ($('#edit_aro_model'+i).val() == -1) || ($('#edit_aro_obj'+i).val() == -1) || ($('#edit_aco_model'+i).val() == -1) || ($('#edit_aco_obj'+i).val() == -1) || ($('#edit_model'+i).val() == -1) ) {
                setFlash(fillMessage, "warning");
                return;
            }

        // preenche editArray
        for (i=0; i < editpos; i++) {
            acl_editArray[i] = new Array();

            acl_editArray[i][0] = $('#edit_aro_model' + i).parent().attr("id").replace(/aro_model_box/, ""); // id da regra
            acl_editArray[i][1] = $("#edit_aro_model"+i).val(); // ARO model
            acl_editArray[i][2] = $("#edit_aro_obj"+i).val(); // ARO object id
            acl_editArray[i][3] = $("#edit_aco_model"+i).val(); // ACO model
            acl_editArray[i][4] = $("#edit_aco_obj"+i).val(); // ACO object id
            acl_editArray[i][5] = $("#edit_model"+i).val(); // model to give permission
            acl_editArray[i][6] = $("#edit_create"+i).val();
            acl_editArray[i][7] = $("#edit_read"+i).val();
            acl_editArray[i][8] = $("#edit_update"+i).val();
            acl_editArray[i][9] = $("#edit_delete"+i).val();
        }
    }

    if (newCont) {
        // verifica se todos os select box estao preenchidos
        for (i=0; i < pos; i++) {
            if ( (validArray[i]) && (($('#aro_model_select'+i).val() == -1) || ($('#aro_obj_select'+i).val() == -1) || ($('#aco_model_select'+i).val() == -1) || ($('#aco_obj_select'+i).val() == -1) || ($('#model_select'+i).val() == -1)) ) {
                setFlash(fillMessage, "warning");
                return;
            }
        }

        // preenche newArray
        var index = 0;
        for (i=0; i < pos; i++) {
            if (validArray[i]) {
                acl_newArray[index] = new Array();
                
                /**
                 * @todo : substituir Array por Object, como abaixo
                 *         nao esquecer de substituir tbm no PHP
                 */
                /*acl_newArray[index] = new Object();
                acl_newArray[index].aro_model = $("#aro_model_select"+i).val();
                acl_newArray[index].aro_obj = $("#aro_obj_select"+i).val();
                acl_newArray[index].aco_model = $("#aco_model_select"+i).val();*/

                acl_newArray[index][0] = $("#aro_model_select"+i).val(); // ARO model
                acl_newArray[index][1] = $("#aro_obj_select"+i).val(); // ARO object id
                acl_newArray[index][2] = $("#aco_model_select"+i).val(); // ACO model
                acl_newArray[index][3] = $("#aco_obj_select"+i).val(); // ACO object id
                acl_newArray[index][4] = $("#model_select"+i).val(); // model to give permission
                acl_newArray[index][5] = $("#create_select"+i).val();
                acl_newArray[index][6] = $("#read_select"+i).val();
                acl_newArray[index][7] = $("#update_select"+i).val();
                acl_newArray[index][8] = $("#delete_select"+i).val();
                index++;
            }
        }
    }

    // mostra mensagem de confirmação para o usuário
    if (confirm(confirmMessage)) {
        $.post(baseUrl+"aaa/acl/update",
            {
                acl_newArray: acl_newArray,
                acl_editArray: acl_editArray
            },
            function(data) {
                /**
                 * @todo : ver qual função chamar ao invés dessa
                 */
                loadHtml(data);
            }
        );
    }
}

function editACL(perm_id) {
    
    var old_aro_model = $('#aro_model_box' + perm_id).attr("itemid");
    $('#aro_model_box' + perm_id).removeAttr("itemid");
    
    var old_aro_obj = $('#aro_obj_box' + perm_id).attr("itemid");
    $('#aro_obj_box' + perm_id).removeAttr("itemid");
    
    var old_aco_model = $('#aco_model_box' + perm_id).attr("itemid");
    $('#aco_model_box' + perm_id).removeAttr("itemid");
    
    var old_aco_obj = $('#aco_obj_box' + perm_id).attr("itemid");
    $('#aco_obj_box' + perm_id).removeAttr("itemid");
    
    var old_model = $('#model_box' + perm_id).attr("itemid");
    $('#model_box' + perm_id).removeAttr("itemid");
    
    var old_create = $('#create_box' + perm_id).html();
    var old_read = $('#read_box' + perm_id).html();
    var old_update = $('#update_box' + perm_id).html();
    var old_delete = $('#delete_box' + perm_id).html();

    $('#aro_model_box' + perm_id).empty();
    $('#aro_obj_box' + perm_id).empty();
    $('#aco_model_box' + perm_id).empty();
    $('#aco_obj_box' + perm_id).empty();
    $('#model_box' + perm_id).empty();
    $('#create_box' + perm_id).empty();
    $('#read_box' + perm_id).empty();
    $('#update_box' + perm_id).empty();
    $('#delete_box' + perm_id).empty();

    $('#aro_model_box' + perm_id).html('<select id="edit_aro_model' + editpos + '"/>');
    $('#aro_obj_box' + perm_id).html('<select id="edit_aro_obj' + editpos + '"/>');
    
    $('#aco_model_box' + perm_id).html('<select id="edit_aco_model' + editpos + '"/>');
    $('#aco_obj_box' + perm_id).html('<select id="edit_aco_obj' + editpos + '"/>');
    
    $('#model_box' + perm_id).html('<select id="edit_model' + editpos + '"/>');
    
    $('#create_box' + perm_id).html('<select id="edit_create' + editpos + '"/>');
    $('#read_box' + perm_id).html('<select id="edit_read' + editpos + '"/>');
    $('#update_box' + perm_id).html('<select id="edit_update' + editpos + '"/>');
    $('#delete_box' + perm_id).html('<select id="edit_delete' + editpos + '"/>');
    
    fillSelectBox('#edit_aro_model' + editpos, aros, old_aro_model);
    var objs = null;
    for (var i=0; aros.length; i++) {
        if (aros[i].id == old_aro_model) {
            objs = aros[i].objs;
            break;
        }
    }
    fillSelectBox('#edit_aro_obj' + editpos, objs, old_aro_obj);
    
    fillSelectBox('#edit_aco_model' + editpos, acos, old_aco_model);
    objs = null;
    for (i=0; acos.length; i++) {
        if (acos[i].id == old_aco_model) {
            objs = acos[i].objs;
            break;
        }
    }
    fillSelectBox('#edit_aco_obj' + editpos, objs, old_aco_obj);
    
    fillSelectBox("#edit_model" + editpos, acos, old_model);
    if (old_model == str_all)
        $("#edit_model" + editpos).append('<option selected="true" value="all">' + str_all + '</option>');
    else
        $("#edit_model" + editpos).append('<option value="all">' + str_all + '</option>');
    
    $('#edit_aro_model' + editpos).change(function() {
        changeAccessSelect(this, aros);
    });
    
    $('#edit_aco_model' + editpos).change(function() {
        changeAccessSelect(this, acos);
    });
    
    var crud_opt = new Array();
    
    crud_opt[0] = new Object();
    crud_opt[0].id = "deny";
    crud_opt[0].name = deny_desc_string;
    
    crud_opt[1] = new Object();
    crud_opt[1].id = "allow";
    crud_opt[1].name = allow_desc_string;
    
    fillSelectBox("#edit_create" + editpos, crud_opt, old_create);
    fillSelectBox("#edit_read" + editpos, crud_opt, old_read);
    fillSelectBox("#edit_update" + editpos, crud_opt, old_update);
    fillSelectBox("#edit_delete" + editpos, crud_opt, old_delete);
    
    $('#save_button').show();
    $('#cancel_button').show();

    isEditingACL = true;
    editpos++;
}

function deleteACL(perm_id) {
    if (confirm(str_delete_acl)) {
        $.post(baseUrl+"aaa/acl/singleDelete", {
            perm_id: perm_id
        }, function(data) {
            if (data) {
                setFlash(str_acl_deleted, "success");
                $('#line' + perm_id).remove();
            } else {
                setFlash(str_acl_not_deleted, "error");
            }
        }, "json");
    } else return;
}