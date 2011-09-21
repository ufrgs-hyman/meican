var validArray = new Array(); // var que contém quais posições estão válidas, das novas regras a serem adicionadas (se a linha foi excluída, então é inválida)
var newCont = 0;  // contagem das regras válidas a serem adicionadas, é o mesmo que a quantidade de posições válidas de validArray
var pos = 0;  // posição das novas regras a serem adicionadas

var editpos = 0;  // posição das regras que estão sendo editadas
var edit = false;  // var que informa se o usuário está ou não editando alguma regra

function rights_initVars() {
    validArray = null;
    validArray = new Array();
    newCont = 0;
    pos = 0;
    editpos = 0;
    edit = false;
}

function newACL() {
    validArray[pos] = 1;
    newCont++;

    $('#acc_table tr:last').before('<tr id="newline' + pos + '" class="tr1"/>');

    var columns = '<td align="center" colspan="3"><input type="button" id="delete' + pos + '" value="' + del + '"></td>';
    columns += '<td><select id="manager' + pos + '"/></td>';
    columns += '<td><input type="text" id="resource' + pos + '"/></td>';
    columns += '<td><select id="managed' + pos + '"/></td>';
    $('#newline' + pos).append(columns);

    $("#manager" + pos).append('<option value="-1"/>');
    for (var i=0; i < groups.length; i++) {
        $("#manager" + pos).append('<option value="' + groups[i].id + '">' + groups[i].name + '</option>');
    }

    //    $("#resource" + pos).append('<option value="-1"/>');
    //    for (var i=0; i < resources.id.length; i++) {
    //        $("#resource" + pos).append('<option value="' + i + '">' + resources.name[i] + '</option>');
    //    }

    $("#managed" + pos).append('<option value="-1"/>');
    for (i=0; i < groups.length; i++) {
        $("#managed" + pos).append('<option value="' + groups[i].id + '">' + groups[i].name + '</option>');
    }

    $("#delete" + pos).click(function() {
        var replaceId = this.id.replace(/delete/, "");
        validArray[replaceId] = 0;
        newCont--;
        if (!newCont && !edit) {
            $('#save_button').hide();
            $('#cancel_button').hide();
        }
        replaceId = "#" + this.id.replace(/delete/, "newline");
        $(replaceId).remove();
    });


    $('#save_button').show();
    $('#cancel_button').show();

    pos++;
}

function saveACL() {
    var rule_editArray = new Array();
    var rule_newArray = new Array();

    if (edit) {

        //VERIFICA SE TODOS OS CAMPOS ESTÃO PREENCHIDOS
        for (var i=0; i < editpos; i++)
            if (($('#newManagerSelect' + i).val() == -1) || ($('#newResourceInput'+i).val() == "") || ($('#newManagedSelect'+i).val() == -1)) {
                alert(fillMessage);
                return;
            }

        // preenche editArray
        for (i=0; i < editpos; i++) {
            rule_editArray[i] = new Array();

            rule_editArray[i][0] = $('#newManagerSelect' + i).parent().attr("id").replace(/manager_box/, ""); // id da regra
            rule_editArray[i][1] = $("#newManagerSelect"+i).val();
            rule_editArray[i][2] = $("#newResourceInput"+i).val();
            rule_editArray[i][3] = $("#newManagedSelect"+i).val();
        }
    }

    if (newCont) {
        // verifica se todos os select box estao preenchidos
        for (var i=0; i < pos; i++) {
            if ( (validArray[i]) && ( ($('#manager'+i).val() == -1) || ($('#resource'+i).val() == "") || ($('#managed'+i).val() == -1) ) ) {
                alert(fillMessage);
                return;
            }
        }

        // preenche newArray
        for (i=0; i < pos; i++) {
            if (validArray[i]) {
                rule_newArray[i] = new Array();

                rule_newArray[i][0] = $("#manager"+i).val();
                rule_newArray[i][1] = $("#resource"+i).val();
                rule_newArray[i][2] = $("#managed"+i).val();
            }
        }
    }

    // mostra mensagem de confirmação para o usuário
    if (confirm(confirmMessage)) {
        rights_initVars();
        $.post(baseUrl+"aaa/rights/update",
            {
                rule_newArray: rule_newArray,
                rule_editArray: rule_editArray
            },
            function(data) {
                $("#menu").load(baseUrl+"init/menu");//TODO: rever isso. é realmente necessário?
                loadHtml(data);
            }
        );
    }
}

function editar(ruleId) {

    $('#save_button').show();
    $('#cancel_button').show();

    edit = true;

    managerSel = $('#manager_box' + ruleId).html();
    resourceSel = $('#resource_box' + ruleId).html();
    managedSel = $('#managed_box' + ruleId).html();

    $('#manager_box' + ruleId).html('<select id="newManagerSelect' + editpos + '"/>');
    //$('#resource_box' + ruleId).html('<select id="newResourceSelect' + editpos + '"/>');
    $('#resource_box' + ruleId).html('<input id="newResourceInput' + editpos + '" type="text" value="' + resourceSel + '"');
    $('#managed_box' + ruleId).html('<select id="newManagedSelect' + editpos + '"/>');

    $('#newManagerSelect' + editpos).append('<option value="-1"/>');
    for (var i=0; i < groups.length; i++) {
        if (groups[i].name == managerSel)
            $("#newManagerSelect" + editpos).append('<option selected="true" value="' + groups[i].id + '">' + groups[i].name + '</option>');
        else
            $("#newManagerSelect" + editpos).append('<option value="' + groups[i].id + '">' + groups[i].name + '</option>');
    }

    //    for (var i=0; i < resources.id.length; i++) {
    //        if (resources.name[i] == resourceSel) {
    //            $("#newResourceSelect" + editpos).append('<option selected ="true" value="' + i + '">' + resources.name[i] + '</option>');
    //        }
    //
    //        else $("#newResourceSelect" + editpos).append('<option value="' + i + '">' + resources.name[i] + '</option>');
    //    }

    $('#newManagedSelect' + editpos).append('<option value="-1"/>');
    for (i=0; i < groups.length; i++) {
        if (groups[i].name == managedSel) {
            $("#newManagedSelect" + editpos).append('<option selected="true" value="' + groups[i].id + '">' + groups[i].name + '</option>');
        }
        else
            $("#newManagedSelect" + editpos).append('<option value="' + groups[i].id + '">' + groups[i].name + '</option>');
    }

    editpos++;
}

function deletar(ruleId) {
    if (confirm(str_delete_rule)) {
        $.post(baseUrl+"aaa/rights/singleDelete", {
            ruleId: ruleId
        }, function() {
            $("#menu").load(baseUrl+"init/menu");//TODO: rever isso. é realmente necessário?
            $('#line' + ruleId).remove();
        });
    } else return;
}