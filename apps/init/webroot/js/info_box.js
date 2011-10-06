function updateSystemTime() {
    $.post(baseUrl+"init/info_box/get_time", function(data) {
        if (data)
            $("#system_time").html(data);
    }, "json");
}