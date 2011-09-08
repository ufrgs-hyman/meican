function updateSystemTime() {
    $.post("main.php?app=init&controller=info_box&action=get_time", function(data) {
        if (data)
            $("#system_time").html(data);
    }, "json");
}