function initIcheck() {
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue',
    });
}

$(document).on('ready pjax:success', function() {
    initIcheck();
});

initIcheck();
