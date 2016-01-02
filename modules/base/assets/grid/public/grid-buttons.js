$("#delete-grid-btn").click(function() {
    if($(':checkbox:checked').length > 0) { 
        $("#delete-grid-modal").modal("show");
    }
    else {
        $("#error-grid-modal").modal("show");
    }
});

$("#cancel-grid-btn").click(function() {
    $("#delete-grid-modal").modal("hide");
});

$("#confirm-grid-btn").click(function (){
    submitDeleteForm();
});

$("#close-grid-btn").click(function (){
    $("#error-grid-modal").modal("hide");
});

