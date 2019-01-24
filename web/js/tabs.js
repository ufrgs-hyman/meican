function setCookie(key, value) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

$(function() { 
    $('a[data-toggle=\"tab\"]').on('show.bs.tab', function (e) {
        let tab =  $(this).attr('href');
        console.log(tab);
        setCookie('lastTab', tab);
    });
});

$(document).on('pjax:success', function() {
   $('a[data-toggle=\"tab\"]').on('show.bs.tab', function (e) {
        let tab =  $(this).attr('href');
        console.log(tab);
        setCookie('lastTab', tab);
    });
});