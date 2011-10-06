function clearSelectBox(htmlId) {
    $('#'+htmlId).html("");
    $('#'+htmlId).append('<option value="-1"></option>');
}

function updateUsers(domainSelected, userSelected){

    $.ajax({
        type: 'POST',
        url: baseUrl+'bpm/request/getUsers',
        data: {
            dom_ip : $(domainSelected).val()
        },
        success: function(data){

            clearSelectBox(userSelected);
            $('#'+userSelected).removeAttr('disabled');


            for (var user in data) {
                    $('#'+userSelected).append($('<option>' + data[user].usr_name +'</option>').attr("value", data[user].usr_id));
                }

            $('#'+userSelected).slideDown();

        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert('error'+textStatus);
        },
        dataType: 'json'
    });
}

