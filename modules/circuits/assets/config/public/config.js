function fillProviderSelect() {
    $('#configurationform-defaultcsurl').attr("disabled","disabled");
    $.ajax({
        url: baseUrl+'/topology/provider/get-all',
        dataType: 'json',
        data: {
            cols: 'nsa'
        },
        success: function(response){
            $("#configurationform-defaultprovidernsa").replaceWith(
              '<select class="form-control" id="configurationform-defaultprovidernsa" name="ConfigurationForm[defaultProviderNsa]" disabled>' +
              '<option value="">loading</option>' +
              '</select>');
            $('#configurationform-defaultprovidernsa').children().remove();
            $('#configurationform-defaultprovidernsa').append('<option value="">none</option>');
            for (var i = 0; i < response.length; i++) {
               $('#configurationform-defaultprovidernsa').append('<option value="' + response[i].nsa + '">' + response[i].nsa + '</option>');
            }
            $('#configurationform-defaultprovidernsa').attr("disabled",false);

            $('#configurationform-defaultprovidernsa').on('change', function() {
                fillCSSelect();
            });
        },
    });
}

function fillCSSelect() {
    if ($('#configurationform-defaultprovidernsa').val()) {
        $('#configurationform-defaultcsurl').attr("disabled","disabled");
        $('#configurationform-defaultcsurl').children().remove();
        $('#configurationform-defaultcsurl').append('<option value="">loading</option>');
        $.ajax({
            url: baseUrl+'/topology/service/get-cs-by-provider-nsa',
            dataType: 'json',
            data: {
                nsa: $('#configurationform-defaultprovidernsa').val(),
                cols: 'url'
            },
            success: function(response){
                currentCS = $("#configurationform-defaultcsurl").val();

                $("#configurationform-defaultcsurl").replaceWith(
                  '<select class="form-control" id="configurationform-defaultcsurl" name="ConfigurationForm[defaultCSUrl]" disabled>' +
                  '<option value="">loading</option>' +
                  '</select>');
                $('#configurationform-defaultcsurl').children().remove();
                $('#configurationform-defaultcsurl').append('<option value="">none</option>');
                for (var i = 0; i < response.length; i++) {
                   $('#configurationform-defaultcsurl').append('<option value="' + response[i].url + '">' + response[i].url + '</option>');
                   if (currentCS == response[i].url) {
                        $("#configurationform-defaultcsurl").val(currentCS);
                    }
                }
                $('#configurationform-defaultcsurl').attr("disabled",false);
            },
        });
    } else {
        $('#configurationform-defaultcsurl').attr("disabled","disabled");
        $('#configurationform-defaultcsurl').children().remove();
        $('#configurationform-defaultcsurl').append('<option value="">none</option>');
    }
}

$('#default-cs').on('click', function() {
    if (!($('#configurationform-defaultprovidernsa').prop("type") == "select-one")) {
        fillProviderSelect();
    } 
});

